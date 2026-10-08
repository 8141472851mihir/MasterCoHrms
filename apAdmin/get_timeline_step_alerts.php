<?php
include_once 'common/object.php';

header('Content-Type: application/json');

try {
    $countryId = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId']) : 0;
    $stateId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
    $cityId = isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : 0;
    $riseFilter = isset($_GET['rise_filter']) ? $_GET['rise_filter'] : "yes";
    $productType = isset($_GET['product_type']) ? $_GET['product_type'] : 'hrms'; // 'hrms' or 'crm'

    $where = ["created_on_society_server = 1"];
    if ($countryId > 0) {
        $where[] = "country_id = '" . $countryId . "'";
    }
    if ($stateId > 0) {
        $where[] = "state_id = '" . $stateId . "'";
    }
    if ($cityId > 0) {
        $where[] = "city_id = '" . $cityId . "'";
    }
    // Apply Myco Rise filter: yes (default), no, all
    if (!isset($_GET['rise_filter']) || $riseFilter === "yes") {
        $where[] = "IFNULL(from_rise_event,0) = 1";
    } elseif ($riseFilter === "no") {
        $where[] = "(from_rise_event IS NULL OR from_rise_event = 0)";
    } // 'all' => no additional filter
    $where[] = "IFNULL(is_not_responding,0) = 0";
    
    // Filter by product type
    if ($productType === 'crm') {
        $where[] = "IFNULL(crm_created,0) = 1";
    } else {
        // For HRMS, exclude CRM companies or include all non-CRM
        // $where[] = "IFNULL(crm_created,0) = 0"; // Uncomment if you want to exclude CRM companies from HRMS alerts
    }
    
    $whereSql = implode(' AND ', $where);

    // Select different fields based on product type (include handover in initial select)
    if ($productType === 'crm') {
        $companiesQ = $d->selectRow(
            "society_id, society_name, city_name, crm_created_date as created_date, is_crm_welcome_email_send as is_welcome_email_send, crm_welcome_email_send_date as welcome_email_send_date, crm_support_handover, crm_support_handover_date",
            "society_master",
            $whereSql,
            "ORDER BY society_id DESC"
        );
    } else {
        $companiesQ = $d->selectRow(
            "society_id, society_name, city_name, created_date, is_welcome_email_send, welcome_email_send_date, is_whatsapp_group_created, whatsapp_group_created_date, support_handover, support_handover_date",
            "society_master",
            $whereSql,
            "ORDER BY society_id DESC"
        );
    }

    $now = new DateTime();
    $nowDateOnly = clone $now;
    $nowDateOnly->setTime(0, 0, 0);
    $results = [];

    // Materialize companies once so we can batch-load related data
    $companies = [];
    $companyIds = [];
    while ($row = mysqli_fetch_assoc($companiesQ)) {
        $cid = (int)$row['society_id'];
        $companies[] = $row;
        $companyIds[] = $cid;
    }
    $companyIdsIn = !empty($companyIds) ? implode(',', array_map('intval', $companyIds)) : '0';

    // Preload total required setup modules (for completion check)
    $requiredSetupRes = $d->selectRow(
        "COUNT(DISTINCT tmm.training_module_id) AS total_required",
        "training_module_master tmm JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
        "tmm.module_type = 0 AND IFNULL(tmm.training_module_status,0)=0 AND tmpm.is_required = 1"
    );
    $totalRequiredSetup = 0;
    if ($requiredSetupRes && mysqli_num_rows($requiredSetupRes) > 0) {
        $totalRequiredSetup = (int)mysqli_fetch_assoc($requiredSetupRes)['total_required'];
    }

    // ---- Prefetch CRM catalog + progress ----
    $crmTopics = [];
    $crmModulesByTopic = [];
    $crmProgressBySociety = [];
    if ($productType === 'crm') {
        $crmTopicsQ = $d->selectRow(
            "tmt.topic_id, tmt.topic_name, tmt.completion_days as topic_completion_days",
            "training_module_topics tmt",
            "tmt.topic_status = 1 AND tmt.topic_type = '1'",
            "ORDER BY tmt.topic_id ASC"
        );
        while ($ct = mysqli_fetch_assoc($crmTopicsQ)) {
            $crmTopics[] = $ct;
        }

        $crmModulesQ = $d->selectRow(
            "tmm.training_module_id, tmm.topic_id",
            "training_module_master tmm",
            "tmm.module_type = 2 AND tmm.training_module_status = 0"
        );
        while ($cm = mysqli_fetch_assoc($crmModulesQ)) {
            $topicId = (int)$cm['topic_id'];
            if (!isset($crmModulesByTopic[$topicId])) {
                $crmModulesByTopic[$topicId] = [];
            }
            $crmModulesByTopic[$topicId][] = (int)$cm['training_module_id'];
        }

        if (!empty($companyIds)) {
            $crmProgressQ = $d->selectRow(
                "society_id, module_id, status, completed_date",
                "crm_training_progress",
                "society_id IN ($companyIdsIn)"
            );
            while ($cp = mysqli_fetch_assoc($crmProgressQ)) {
                $sid = (int)$cp['society_id'];
                $mid = (int)$cp['module_id'];
                if (!isset($crmProgressBySociety[$sid])) {
                    $crmProgressBySociety[$sid] = [];
                }
                // Keep first matching completed-style row; allow multiple rows per module
                if (!isset($crmProgressBySociety[$sid][$mid])) {
                    $crmProgressBySociety[$sid][$mid] = [];
                }
                $crmProgressBySociety[$sid][$mid][] = $cp;
            }
        }
    }

    // ---- Prefetch HRMS setup statuses + product-training topic catalog/status ----
    $mtsmByCompany = [];
    $hrmsTopics = [];
    $hrmsModulesByTopic = []; // topic_id => [ ['module_id'=>, 'participant_id'=>], ... ]
    $btsmByCompany = []; // company_id => [ module_id => [ participant_id => rows ] ]
    if ($productType !== 'crm' && !empty($companyIds)) {
        $mtsmQ = $d->selectRow(
            "mtsm.company_id, mtsm.data_receive_status, mtsm.data_receive_date, mtsm.onboarding_status, mtsm.onboarding_date",
            "module_training_status_master mtsm JOIN training_module_master tmm ON tmm.training_module_id = mtsm.module_id JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
            "mtsm.company_id IN ($companyIdsIn) AND tmm.module_type = 0 AND IFNULL(tmm.training_module_status,0)=0 AND tmpm.is_required = 1"
        );
        while ($r = mysqli_fetch_assoc($mtsmQ)) {
            $sid = (int)$r['company_id'];
            if (!isset($mtsmByCompany[$sid])) {
                $mtsmByCompany[$sid] = [];
            }
            $mtsmByCompany[$sid][] = $r;
        }

        $topicsQ = $d->selectRow(
            "tmt.topic_id, tmt.topic_name, tmt.completion_days, tmt.next_start_days, tmt.participant_type, tpt.participant_name",
            "training_module_topics tmt LEFT JOIN training_participants_type tpt ON tmt.participant_type = tpt.participants_type_id",
            "tmt.topic_status = 1 AND tmt.topic_type = '0'",
            "ORDER BY tmt.topic_name ASC"
        );
        while ($t = mysqli_fetch_assoc($topicsQ)) {
            $hrmsTopics[] = $t;
        }

        $hrmsModQ = $d->selectRow(
            "tmm.training_module_id, tmm.topic_id",
            "training_module_master tmm",
            "tmm.module_type = 1 AND tmm.training_module_status = 0"
        );
        while ($hm = mysqli_fetch_assoc($hrmsModQ)) {
            $tid = (int)$hm['topic_id'];
            if (!isset($hrmsModulesByTopic[$tid])) {
                $hrmsModulesByTopic[$tid] = [];
            }
            $hrmsModulesByTopic[$tid][] = (int)$hm['training_module_id'];
        }

        $btsmQ = $d->selectRow(
            "company_id, module_id, participant_id, training_status, training_date",
            "batch_training_status_master",
            "company_id IN ($companyIdsIn)"
        );
        while ($b = mysqli_fetch_assoc($btsmQ)) {
            $sid = (int)$b['company_id'];
            $mid = (int)$b['module_id'];
            $pid = (int)$b['participant_id'];
            if (!isset($btsmByCompany[$sid])) {
                $btsmByCompany[$sid] = [];
            }
            if (!isset($btsmByCompany[$sid][$mid])) {
                $btsmByCompany[$sid][$mid] = [];
            }
            if (!isset($btsmByCompany[$sid][$mid][$pid])) {
                $btsmByCompany[$sid][$mid][$pid] = [];
            }
            $btsmByCompany[$sid][$mid][$pid][] = $b;
        }
    }

    foreach ($companies as $row) {
        $companyId = (int)$row['society_id'];
        $created = !empty($row['created_date']) ? new DateTime($row['created_date']) : null;
        if (!$created) {
            continue;
        }

        // Handle CRM vs HRMS differently
        if ($productType === 'crm') {
            // CRM delay calculation
            // Step 1: Welcome Email - due within 1 day from CRM creation
            $crmWelcomeDue = (clone $created)->add(new DateInterval('P1D'));
            $crmWelcomeDone = ((int)($row['is_welcome_email_send'] ?? 0)) === 1;
            $crmWelcomeDoneAt = !empty($row['welcome_email_send_date']) ? new DateTime($row['welcome_email_send_date']) : null;
            
            $crmWelcomeLate = (!$crmWelcomeDone && $now > $crmWelcomeDue);
            $crmWelcomeLateDays = 0;
            if ($crmWelcomeLate) {
                $crmWelcomeLateDays = (int)floor(($now->getTimestamp() - $crmWelcomeDue->getTimestamp()) / (60 * 60 * 24));
                if ($crmWelcomeLateDays < 0) {
                    $crmWelcomeLateDays = 0;
                }
            }
            
            // CRM Topics delay calculation
            $crmTopicIssues = [];
            $crmHandoverOverdue = false;
            $crmHandoverOverdueDays = 0;
            $allCrmStepsCompleted = false;
            
            // Base date for topics: welcome completion date or welcome due date or creation date
            $crmTopicBase = $crmWelcomeDoneAt ? clone $crmWelcomeDoneAt : ($crmWelcomeDue ? clone $crmWelcomeDue : clone $created);
            
            // Get CRM topic progress from prefetched maps
            $crmTopicStats = [];
            $crmTopicDates = [];
            $societyProgress = $crmProgressBySociety[$companyId] ?? [];
            foreach ($crmTopics as $ct) {
                $topicId = (int)$ct['topic_id'];
                $moduleIds = $crmModulesByTopic[$topicId] ?? [];
                $totalModules = count($moduleIds);
                $completedModules = 0;
                $moduleCompleteDates = [];
                
                foreach ($moduleIds as $moduleId) {
                    $moduleCompleted = false;
                    $moduleCompletedDate = null;
                    $progressRows = $societyProgress[$moduleId] ?? [];
                    foreach ($progressRows as $cp) {
                        $status = (int)($cp['status'] ?? 0);
                        if (in_array($status, [1, 2, 3], true)) { // Completed, NA, Later On
                            $moduleCompleted = true;
                            if (!empty($cp['completed_date'])) {
                                $moduleCompletedDate = strtotime($cp['completed_date']);
                            }
                        }
                    }
                    
                    if ($moduleCompleted) {
                        $completedModules++;
                        if ($moduleCompletedDate) {
                            $moduleCompleteDates[] = $moduleCompletedDate;
                        }
                    }
                }
                
                $crmTopicStats[$topicId] = [
                    'total' => $totalModules,
                    'completed' => $completedModules
                ];
                
                $crmTopicDates[$topicId] = [
                    'completed_ts' => ($completedModules === $totalModules && $totalModules > 0 && !empty($moduleCompleteDates)) ? max($moduleCompleteDates) : null
                ];
            }
            
            // Calculate topic delays (only if previous topics are completed)
            $topicIndex = 0;
            foreach ($crmTopics as $ct) {
                $topicId = (int)$ct['topic_id'];
                $topicStats = $crmTopicStats[$topicId] ?? ['total' => 0, 'completed' => 0];
                $topicDates = $crmTopicDates[$topicId] ?? ['completed_ts' => null];
                
                $topicStatus = 'Not Started';
                if ($topicStats['total'] > 0) {
                    if ($topicStats['completed'] >= $topicStats['total']) {
                        $topicStatus = 'Completed';
                    }
                }
                
                // Check if all previous topics are completed
                $allPreviousCompleted = true;
                if ($topicIndex > 0) {
                    for ($i = 0; $i < $topicIndex; $i++) {
                        $prevTopicId = (int)$crmTopics[$i]['topic_id'];
                        $prevStats = $crmTopicStats[$prevTopicId] ?? ['total' => 0, 'completed' => 0];
                        if ($prevStats['total'] > 0 && $prevStats['completed'] < $prevStats['total']) {
                            $allPreviousCompleted = false;
                            break;
                        }
                    }
                }
                
                // Calculate due date
                $topicDueDate = null;
                if ($topicIndex === 0) {
                    // First topic: based on welcome completion or due
                    $topicDueDate = clone $crmTopicBase;
                    if (!empty($ct['topic_completion_days'])) {
                        $topicDueDate->add(new DateInterval('P' . (int)$ct['topic_completion_days'] . 'D'));
                    }
                } else {
                    // Subsequent topics: based on previous topic completion
                    $prevTopicId = (int)$crmTopics[$topicIndex - 1]['topic_id'];
                    $prevTopicDates = $crmTopicDates[$prevTopicId] ?? ['completed_ts' => null];
                    if (!empty($prevTopicDates['completed_ts'])) {
                        $prevCompletedDate = new DateTime('@' . $prevTopicDates['completed_ts']);
                        $topicDueDate = clone $prevCompletedDate;
                        if (!empty($ct['topic_completion_days'])) {
                            $topicDueDate->add(new DateInterval('P' . (int)$ct['topic_completion_days'] . 'D'));
                        }
                    }
                }
                
                // Check if topic is overdue (only if previous topics are completed)
                if ($topicStatus !== 'Completed' && $topicDueDate && $allPreviousCompleted) {
                    $topicDueDateOnly = clone $topicDueDate;
                    $topicDueDateOnly->setTime(0, 0, 0);
                    if ($nowDateOnly > $topicDueDateOnly && empty($crmTopicIssues)) {
                        $days = (int)$nowDateOnly->diff($topicDueDateOnly)->days;
                        if ($days < 0) {
                            $days = 0;
                        }
                        $crmTopicIssues[] = [
                            'topic_id' => $topicId,
                            'topic_name' => $ct['topic_name'] ?? '',
                            'type' => 'overdue',
                            'days' => $days
                        ];
                    }
                }
                
                $topicIndex++;
            }
            
            // Check if all CRM topics are completed
            $allCrmTopicsCompleted = true;
            $lastCrmTopicCompletedDate = null;
            foreach ($crmTopics as $ct) {
                $topicId = (int)$ct['topic_id'];
                $topicStats = $crmTopicStats[$topicId] ?? ['total' => 0, 'completed' => 0];
                if ($topicStats['total'] > 0 && $topicStats['completed'] < $topicStats['total']) {
                    $allCrmTopicsCompleted = false;
                }
                $topicDates = $crmTopicDates[$topicId] ?? ['completed_ts' => null];
                if (!empty($topicDates['completed_ts']) && ($lastCrmTopicCompletedDate === null || $topicDates['completed_ts'] > $lastCrmTopicCompletedDate)) {
                    $lastCrmTopicCompletedDate = $topicDates['completed_ts'];
                }
            }
            
            // Check handover (from initial select)
            $allCrmStepsCompleted = $crmWelcomeDone && $allCrmTopicsCompleted;
            $crmHandoverCompleted = !empty($row['crm_support_handover']) && (int)$row['crm_support_handover'] === 1;
            
            if ($allCrmStepsCompleted && !$crmHandoverCompleted) {
                $crmHandoverDueDate = null;
                if ($lastCrmTopicCompletedDate) {
                    $crmHandoverDueDate = new DateTime(date('Y-m-d', $lastCrmTopicCompletedDate));
                    $crmHandoverDueDate->add(new DateInterval('P3D'));
                } elseif ($crmWelcomeDoneAt) {
                    $crmHandoverDueDate = clone $crmWelcomeDoneAt;
                    $crmHandoverDueDate->add(new DateInterval('P3D'));
                }
                
                if ($crmHandoverDueDate) {
                    $crmHandoverDueDateOnly = clone $crmHandoverDueDate;
                    $crmHandoverDueDateOnly->setTime(0, 0, 0);
                    if ($nowDateOnly > $crmHandoverDueDateOnly) {
                        $crmHandoverOverdue = true;
                        $crmHandoverOverdueDays = (int)$nowDateOnly->diff($crmHandoverDueDateOnly)->days;
                        if ($crmHandoverOverdueDays < 0) {
                            $crmHandoverOverdueDays = 0;
                        }
                    }
                }
            }
            
            // Include in results if any CRM delay
            if ($crmWelcomeLate || count($crmTopicIssues) > 0 || $crmHandoverOverdue) {
                $results[] = [
                    'company_id' => $d->short_app_name() . "_" . $companyId,
                    'company_name' => $row['society_name'] ?? '',
                    'city_name' => $row['city_name'] ?? '',
                    'step1_late' => $crmWelcomeLate ? 1 : 0,
                    'step1_late_days' => $crmWelcomeLateDays,
                    'step2_late' => 0,
                    'step2_late_days' => 0,
                    'setup_start_late' => 0,
                    'setup_not_started_days_overdue' => 0,
                    'setup_incomplete_late' => 0,
                    'setup_incomplete_days_overdue' => 0,
                    'topics_overdue' => $crmTopicIssues,
                    'topics_overdue_count' => count($crmTopicIssues),
                    'handover_overdue' => $crmHandoverOverdue ? 1 : 0,
                    'handover_overdue_days' => $crmHandoverOverdueDays
                ];
            }
            
            continue; // Skip HRMS processing for CRM companies
        }

        // HRMS delay calculation (existing logic)
        // Steps 1 & 2: due in 1 day from creation
        $step12Due = (clone $created)->add(new DateInterval('P2D'));
        $step1Done = ((int)($row['is_welcome_email_send'] ?? 0)) === 1;
        $step2Done = ((int)($row['is_whatsapp_group_created'] ?? 0)) === 1;
        $welDoneAt = !empty($row['welcome_email_send_date']) ? new DateTime($row['welcome_email_send_date']) : null;
        $waDoneAt = !empty($row['whatsapp_group_created_date']) ? new DateTime($row['whatsapp_group_created_date']) : null;

        // Late only if currently pending past due (no historical late-completion alerts)
        $step1Late = (!$step1Done && $now > $step12Due);
        $step2Late = (!$step2Done && $now > $step12Due);

        $step1LateDays = 0;
        if ($step1Late) {
            $step1LateDays = (int)floor(($now->getTimestamp() - $step12Due->getTimestamp()) / (60 * 60 * 24));
            if ($step1LateDays < 0) {
                $step1LateDays = 0;
            }
        }
        $step2LateDays = 0;
        if ($step2Late) {
            $step2LateDays = (int)floor(($now->getTimestamp() - $step12Due->getTimestamp()) / (60 * 60 * 24));
            if ($step2LateDays < 0) {
                $step2LateDays = 0;
            }
        }

        // Setup window baseline: start due at +10d, complete due at +20d from creation
        if ($welDoneAt && $waDoneAt) {
            $step1or2completedDate = ($welDoneAt > $waDoneAt) ? $welDoneAt : $waDoneAt;
        } else {
            if ($welDoneAt) {
                $step1or2completedDate = $welDoneAt;
            } else if ($waDoneAt) {
                $step1or2completedDate = $waDoneAt;
            } else {
                $step1or2completedDate = $step12Due;
            }
        }
        $setupStartDue = (clone $step1or2completedDate)->add(new DateInterval('P5D'));
        $setupCompleteDue = (clone $setupStartDue)->add(new DateInterval('P0D'));

        // Determine actual setup start (first data_receive_date or onboarding_date)
        $firstSetupDate = null;
        $lastSetupCompletedDate = null;
        $setupCompletedCount = 0;
        $latestSetupCompletedTs = null;

        // Use prefetched module training rows for this company (setup only)
        $companyMtsm = $mtsmByCompany[$companyId] ?? [];
        foreach ($companyMtsm as $r) {
            $drStatus = isset($r['data_receive_status']) ? (int)$r['data_receive_status'] : null;
            $upStatus = isset($r['onboarding_status']) ? (int)$r['onboarding_status'] : null;
            $drDate = !empty($r['data_receive_date']) ? new DateTime($r['data_receive_date']) : null;
            $upDate = !empty($r['onboarding_date']) ? new DateTime($r['onboarding_date']) : null;

            // First start date observed
            if ($drDate) {
                $firstSetupDate = $firstSetupDate ? min($firstSetupDate, $drDate) : $drDate;
            }
            if ($upDate) {
                $firstSetupDate = $firstSetupDate ? min($firstSetupDate, $upDate) : $upDate;
            }

            // Completed means onboarding_status in (1,2) or data_receive_status in (1,2) for each module
            $isCompleted = false;
            if ($upStatus === 1 || $upStatus === 2) {
                $isCompleted = true;
            }
            if (!$isCompleted && ($drStatus === 1 || $drStatus === 2)) {
                $isCompleted = true;
            }
            if ($isCompleted) {
                $setupCompletedCount++;
                if ($upStatus === 1 && $upDate) {
                    $lastSetupCompletedDate = $lastSetupCompletedDate ? max($lastSetupCompletedDate, $upDate) : $upDate;
                } elseif ($drStatus === 1 && $drDate) {
                    $lastSetupCompletedDate = $lastSetupCompletedDate ? max($lastSetupCompletedDate, $drDate) : $drDate;
                }
            }

            // Latest setup completed for topic baseline (onboarding_status IN 1,2 with date)
            if (($upStatus === 1 || $upStatus === 2) && !empty($r['onboarding_date'])) {
                $dt = strtotime($r['onboarding_date']);
                if ($dt && ($latestSetupCompletedTs === null || $dt > $latestSetupCompletedTs)) {
                    $latestSetupCompletedTs = $dt;
                }
            }
        }

        // Setup delay policy:
        // - Only calculate setup delays if BOTH step 1 and step 2 are completed
        // - If either step 1 or step 2 is late/incomplete, setup cannot start, so no setup delay should be shown
        $setupStartLate = false;
        $setupNotStartedDays = 0;
        $setupIncompleteLate = false;
        $setupIncompleteDays = 0;

        // Only calculate setup delays if both steps 1 & 2 are completed
        if ($step1Done && $step2Done) {
            // Normalize dates to start of day for accurate date-only comparison
            $setupStartDueDateOnly = clone $setupStartDue;
            $setupStartDueDateOnly->setTime(0, 0, 0);
            $setupCompleteDueDateOnly = clone $setupCompleteDue;
            $setupCompleteDueDateOnly->setTime(0, 0, 0);

            // - If not started and past start due -> flag start late (with days overdue)
            // - Only consider late if current date is AFTER the due date (not on the same day)
            $setupStartLate = (!$firstSetupDate && $nowDateOnly > $setupStartDueDateOnly);
            if ($setupStartLate) {
                // Calculate days overdue based on date difference (not datetime)
                $setupNotStartedDays = (int)$nowDateOnly->diff($setupStartDueDateOnly)->days;
                if ($setupNotStartedDays < 0) {
                    $setupNotStartedDays = 0;
                }
            }

            // Check if all required setup modules completed
            $isSetupCompleted = ($totalRequiredSetup > 0 && $setupCompletedCount >= $totalRequiredSetup);
            // Only consider incomplete late if current date is AFTER the completion due date
            if (!$setupStartLate && $firstSetupDate && !$isSetupCompleted && $nowDateOnly > $setupCompleteDueDateOnly) {
                $setupIncompleteLate = true;
                // Calculate days overdue based on date difference (not datetime)
                $setupIncompleteDays = (int)$nowDateOnly->diff($setupCompleteDueDateOnly)->days;
                if ($setupIncompleteDays < 0) {
                    $setupIncompleteDays = 0;
                }
            }
        } else {
            // Steps 1 & 2 not both completed, so setup cannot start yet - no setup delay calculation
            $isSetupCompleted = false;
        }

        // Step 4+: Product training topics overdue calculation (from prefetched maps)
        $topicIssues = [];
        $allTopicsCompleted = true;
        $lastTopicCompletedDate = null;
        if ($isSetupCompleted) {
            $dataStartDue = (clone $step1or2completedDate)->add(new DateInterval('P5D'));
            $dataStartDue->setTime(0, 0, 0); // Normalize to start of day
            $dataEndDue = (clone $dataStartDue)->add(new DateInterval('P0D'));
            $dataEndDue->setTime(0, 0, 0); // Normalize to start of day
            $baseDate = $latestSetupCompletedTs ? (new DateTime(date('Y-m-d', $latestSetupCompletedTs))) : $dataEndDue;
            // Normalize baseDate to start of day
            $baseDate->setTime(0, 0, 0);

            $companyBtsm = $btsmByCompany[$companyId] ?? [];

            // Build per-topic aggregates matching original SQL semantics
            $topicAggregates = [];
            foreach ($hrmsTopics as $tMeta) {
                $topicId = (int)$tMeta['topic_id'];
                // Match SQL: btsm.participant_id = tmt.participant_type (NULL never matches)
                $rawParticipantType = $tMeta['participant_type'] ?? null;
                $participantId = ($rawParticipantType === null || $rawParticipantType === '') ? null : (int)$rawParticipantType;
                $moduleIds = $hrmsModulesByTopic[$topicId] ?? [];
                $moduleCount = count($moduleIds);
                $completedModuleIds = [];
                $startedModuleIds = [];
                $topicStartedOn = null;
                $topicCompletedDates = [];

                if ($participantId !== null) {
                    foreach ($moduleIds as $moduleId) {
                        $pidRows = $companyBtsm[$moduleId][$participantId] ?? [];
                        foreach ($pidRows as $bRow) {
                            $startedModuleIds[$moduleId] = true;
                            $tDate = !empty($bRow['training_date']) ? $bRow['training_date'] : null;
                            if ($tDate && ($topicStartedOn === null || $tDate < $topicStartedOn)) {
                                $topicStartedOn = $tDate;
                            }
                            $tStatus = (int)($bRow['training_status'] ?? 0);
                            if (in_array($tStatus, [1, 2, 3], true)) {
                                $completedModuleIds[$moduleId] = true;
                                if ($tDate) {
                                    $topicCompletedDates[] = $tDate;
                                }
                            }
                        }
                    }
                }

                $completedCount = count($completedModuleIds);
                $startedCount = count($startedModuleIds);
                $topicCompletedOn = ($completedCount === $moduleCount && $moduleCount > 0 && !empty($topicCompletedDates))
                    ? max($topicCompletedDates)
                    : null;

                $topicAggregates[$topicId] = [
                    'topic_id' => $topicId,
                    'topic_name' => $tMeta['topic_name'] ?? '',
                    'completion_days' => $tMeta['completion_days'] ?? 0,
                    'next_start_days' => $tMeta['next_start_days'] ?? 0,
                    'participant_name' => $tMeta['participant_name'] ?? '',
                    'module_count' => $moduleCount,
                    'completed_count' => $completedCount,
                    'started_count' => $startedCount,
                    'topic_started_on' => $topicStartedOn,
                    'topic_completed_on' => $topicCompletedOn,
                ];
            }

            $cursorStart = clone $baseDate;
            $cursorStart->add(new DateInterval('P5D'));
            $cursorStart->setTime(0, 0, 0); // Ensure normalized to start of day
            $priorActualCompletedOn = null;
            $priorNextStartDays = 0;
            $addedTopicIssue = false;
            foreach ($hrmsTopics as $tMeta) {
                $t = $topicAggregates[(int)$tMeta['topic_id']];
                if (!empty($priorActualCompletedOn)) {
                    $cursorStart = new DateTime($priorActualCompletedOn);
                    $cursorStart->setTime(0, 0, 0); // Normalize to start of day
                    $nsd = max(0, (int)($priorNextStartDays ?? 0));
                    if ($nsd > 0) {
                        $cursorStart->add(new DateInterval('P' . $nsd . 'D'));
                    }
                }

                $schedStart = clone $cursorStart;
                $schedStart->setTime(0, 0, 0); // Ensure normalized to start of day
                $compDays = max(0, (int)($t['completion_days'] ?? 0));
                $schedEnd = clone $schedStart;
                $schedEnd->add(new DateInterval('P' . $compDays . 'D'));

                $startedOn = !empty($t['topic_started_on']) ? new DateTime($t['topic_started_on']) : null;
                if ($startedOn) {
                    $startedOn->setTime(0, 0, 0);
                } // Normalize to start of day
                $completedOn = !empty($t['topic_completed_on']) ? new DateTime($t['topic_completed_on']) : null;
                if ($completedOn) {
                    $completedOn->setTime(0, 0, 0);
                } // Normalize to start of day

                $effectiveEnd = clone $schedEnd;
                if ($startedOn) {
                    $effectiveEnd = clone $startedOn;
                    $effectiveEnd->add(new DateInterval('P' . $compDays . 'D'));
                }

                // Normalize dates to start of day for accurate date-only comparison
                $schedStartDateOnly = clone $schedStart;
                $schedStartDateOnly->setTime(0, 0, 0);
                $effectiveEndDateOnly = clone $effectiveEnd;
                $effectiveEndDateOnly->setTime(0, 0, 0);

                // If overdue and not completed -> create issue
                // Only consider late if current date is AFTER the due date (not on the same day)
                if (!$completedOn && !$addedTopicIssue) {
                    if (!$startedOn && $nowDateOnly > $schedStartDateOnly) {
                        // Calculate days overdue based on date difference (not datetime)
                        $days = (int)$nowDateOnly->diff($schedStartDateOnly)->days;
                        if ($days < 0) {
                            $days = 0;
                        }
                        $topicIssues[] = [
                            'topic_id' => (int)$t['topic_id'],
                            'topic_name' => $t['topic_name'] ?? '',
                            'participant' => $t['participant_name'] ?? '',
                            'type' => 'start',
                            'days' => $days
                        ];
                        $addedTopicIssue = true;
                    } elseif ($startedOn && $nowDateOnly > $effectiveEndDateOnly) {
                        // Topic started but completion is overdue
                        // Calculate days overdue based on date difference (not datetime)
                        $days = (int)$nowDateOnly->diff($effectiveEndDateOnly)->days;
                        if ($days < 0) {
                            $days = 0;
                        }
                        $topicIssues[] = [
                            'topic_id' => (int)$t['topic_id'],
                            'topic_name' => $t['topic_name'] ?? '',
                            'participant' => $t['participant_name'] ?? '',
                            'type' => 'complete',
                            'days' => $days
                        ];
                        $addedTopicIssue = true;
                    }
                }

                if ($addedTopicIssue) {
                    break;
                }

                $priorActualCompletedOn = $t['topic_completed_on'] ?? null;
                $priorNextStartDays = (int)($t['next_start_days'] ?? 0);
                if (empty($priorActualCompletedOn)) {
                    $cursorStart = clone $schedEnd;
                    if ($priorNextStartDays > 0) {
                        $cursorStart->add(new DateInterval('P' . $priorNextStartDays . 'D'));
                    }
                }
            }

            // Check if all topics are completed (for handover eligibility) — reuse aggregates
            foreach ($topicAggregates as $tc) {
                $totalModulesForTopic = (int)($tc['module_count'] ?? 0);
                $completedModulesForTopic = (int)($tc['completed_count'] ?? 0);
                if ($totalModulesForTopic > 0 && $completedModulesForTopic < $totalModulesForTopic) {
                    $allTopicsCompleted = false;
                    break;
                }
                if (!empty($tc['topic_completed_on'])) {
                    $topicCompletedTs = strtotime($tc['topic_completed_on']);
                    if ($topicCompletedTs && ($lastTopicCompletedDate === null || $topicCompletedTs > $lastTopicCompletedDate)) {
                        $lastTopicCompletedDate = $topicCompletedTs;
                    }
                }
            }
        }

        // Check if all steps are completed (for handover)
        $allStepsCompleted = ($step1Done && $step2Done && $isSetupCompleted && $allTopicsCompleted);

        // Get handover data from initial select
        $handoverCompleted = !empty($row['support_handover']) && (int)$row['support_handover'] === 1;

        // Calculate handover due date and check if overdue
        $handoverOverdue = false;
        $handoverOverdueDays = 0;
        if ($allStepsCompleted && !$handoverCompleted) {
            // Calculate handover due date (3 days after all steps completed)
            $handoverDueDate = null;
            if ($lastTopicCompletedDate) {
                $handoverDueDate = new DateTime(date('Y-m-d', $lastTopicCompletedDate));
                $handoverDueDate->add(new DateInterval('P3D'));
            } elseif ($latestSetupCompletedTs) {
                $handoverDueDate = new DateTime(date('Y-m-d', $latestSetupCompletedTs));
                $handoverDueDate->add(new DateInterval('P3D'));
            }

            if ($handoverDueDate) {
                $handoverDueDateOnly = clone $handoverDueDate;
                $handoverDueDateOnly->setTime(0, 0, 0);
                if ($nowDateOnly > $handoverDueDateOnly) {
                    $handoverOverdue = true;
                    $handoverOverdueDays = (int)$nowDateOnly->diff($handoverDueDateOnly)->days;
                    if ($handoverOverdueDays < 0) {
                        $handoverOverdueDays = 0;
                    }
                }
            }
        }

        // If any step is currently late, include in results
        if ($step1Late || $step2Late || (!$isSetupCompleted && ($setupStartLate || $setupIncompleteLate)) || count($topicIssues) > 0 || $handoverOverdue) {
            $results[] = [
                'company_id' => $d->short_app_name() . "_" . $companyId,
                'company_name' => $row['society_name'] ?? '',
                'city_name' => $row['city_name'] ?? '',
                'step1_late' => $step1Late ? 1 : 0,
                'step1_late_days' => $step1LateDays,
                'step2_late' => $step2Late ? 1 : 0,
                'step2_late_days' => $step2LateDays,
                'setup_start_late' => $setupStartLate ? 1 : 0,
                'setup_not_started_days_overdue' => $setupNotStartedDays,
                'setup_incomplete_late' => $setupIncompleteLate ? 1 : 0,
                'setup_incomplete_days_overdue' => $setupIncompleteDays,
                'topics_overdue' => $topicIssues,
                'topics_overdue_count' => count($topicIssues),
                'handover_overdue' => $handoverOverdue ? 1 : 0,
                'handover_overdue_days' => $handoverOverdueDays
            ];
        }
    }

    echo json_encode([
        'count' => count($results),
        'companies' => $results
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => true]);
}
