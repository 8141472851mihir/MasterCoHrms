<?php

include_once 'common/object.php';
error_reporting(0);
include_once 'timeline_functions.php';

$base_url = $m->base_url();
extract(array_map("test_input", $_POST));

if (isset($_POST) && !empty($_POST)) {
    if (isset($getProductModule) && $getProductModule == 'getProductModule' && isset($society_id)) {
        $q = $d->selectRow(
            "training_module_master.training_module_name,
                batch_training_status_master.module_id,
                batch_training_status_master.participant_id,
                training_participants_type.participant_name,
                training_module_master.module_type,
                batch_training_status_master.training_status,
                batch_training_status_master.training_date,
                training_module_topics.topic_name",
            "training_module_master LEFT JOIN batch_training_status_master
                    ON training_module_master.training_module_id = batch_training_status_master.module_id
                LEFT JOIN training_participants_type
                    ON training_participants_type.participants_type_id = batch_training_status_master.participant_id
                LEFT JOIN training_module_topics ON training_module_topics.topic_id = training_module_master.topic_id AND training_module_topics.topic_type = '0'",
            "training_module_master.training_module_status = 0 AND batch_training_status_master.company_id = '$society_id' AND training_module_master.module_type='1'"
        );
        $modules = [];
        $participants = [];

        while ($row = mysqli_fetch_assoc($q)) {
            $module_name = $row['training_module_name'];
            $participant_name = $row['participant_name'];
            $topic_name = isset($row['topic_name']) && $row['topic_name'] !== '' ? $row['topic_name'] : 'Other';

            if ($participant_name) {
                $modules[$module_name][$participant_name] = [
                    'status' => $row['training_status'] ?? '',
                    'date'   => $row['training_date'] ?? ''
                ];
                $participants[$participant_name] = true;
            }
            $modules[$module_name]['topic_name'] = $topic_name;
        }
        // Fetch all modules once
        $q_modules = $d->selectRow(
            "training_module_master.training_module_id, training_module_name, training_module_master.training_module_order AS module_order, training_module_priority_master.priority_name, training_module_priority_master.priority_id, training_module_topics.topic_name, training_module_master.completion_days, training_module_topics.participant_type AS participant_id",
            "training_module_master LEFT JOIN training_module_priority_master ON training_module_priority_master.priority_id = training_module_master.module_priority 
             LEFT JOIN training_module_topics ON training_module_topics.topic_id = training_module_master.topic_id AND training_module_topics.topic_type = '0'",
            "training_module_master.training_module_status=0 AND training_module_master.module_type='1'",
            "ORDER BY COALESCE(training_module_master.training_module_order, training_module_master.module_priority) ASC, training_module_master.training_module_name ASC"
        );
        $moduleIdByName = [];
        $moduleIds = [];
        while ($row = mysqli_fetch_assoc($q_modules)) {
            $module_name = $row['training_module_name'];
            if (!isset($modules[$module_name])) {
                $modules[$module_name] = [];
            }
            $modules[$module_name]['priority'] = $row['priority_name'];
            $modules[$module_name]['priority_id'] = (int)$row['priority_id'];
            $modules[$module_name]['topic_name'] = isset($row['topic_name']) && $row['topic_name'] !== '' ? $row['topic_name'] : 'Other';
            $modules[$module_name]['module_id'] = (int)$row['training_module_id'];
            $modules[$module_name]['completion_days'] = isset($row['completion_days']) ? (int)$row['completion_days'] : null;
            $modules[$module_name]['participant_id'] = isset($row['participant_id']) ? (int)$row['participant_id'] : 0;
            $modules[$module_name]['module_order'] = isset($row['module_order']) ? (int)$row['module_order'] : null;
            $moduleIdByName[$module_name] = (int)$row['training_module_id'];
            $moduleIds[] = (int)$row['training_module_id'];
        }

        // Prefetch all subtopics for all modules in one query
        $subtopicsByModuleId = [];
        if (!empty($moduleIds)) {
            $moduleIdsCsv = implode(',', $moduleIds);
            $subtopicsQAll = $d->selectRow(
                "training_module_id, subtopic_id, subtopic_name, subtopic_description, display_order",
                "training_module_subtopics",
                "training_module_id IN ($moduleIdsCsv) AND subtopic_status = 1",
                "ORDER BY training_module_id ASC, display_order ASC"
            );
            while ($s = mysqli_fetch_assoc($subtopicsQAll)) {
                $mid = (int)$s['training_module_id'];
                if (!isset($subtopicsByModuleId[$mid])) {
                    $subtopicsByModuleId[$mid] = [];
                }
                $subtopicsByModuleId[$mid][] = [
                    'subtopic_id' => (int)$s['subtopic_id'],
                    'subtopic_name' => $s['subtopic_name'],
                    'subtopic_description' => isset($s['subtopic_description']) ? trim($s['subtopic_description']) : ''
                ];
            }
        }

        // Attach subtopics to modules map
        foreach ($modules as $module_name => &$dataRef) {
            $mid = isset($moduleIdByName[$module_name]) ? $moduleIdByName[$module_name] : 0;
            $dataRef['subtopics'] = $subtopicsByModuleId[$mid] ?? [];
        }
        unset($dataRef);

        $q_participants = $d->selectRow("participant_name", "training_participants_type", "participant_name IS NOT NULL");
        while ($row = mysqli_fetch_assoc($q_participants)) {
            $participant_name = $row['participant_name'];
            $participants[$participant_name] = true;
        }

        $activeParticipantIds = [];
        $participantIdToName = [];
        $activePartsPrefQ = $d->selectRow("participants_type_id, participant_name", "training_participants_type", "status = 0");
        while ($p = mysqli_fetch_assoc($activePartsPrefQ)) {
            $pid = (int)$p['participants_type_id'];
            $activeParticipantIds[] = $pid;
            $participantIdToName[$pid] = $p['participant_name'];
        }

        $prevModuleIdById = [];
        $orderOnceQ = $d->selectRow(
            "training_module_id",
            "training_module_master",
            "module_type = 1 AND training_module_status = 0",
            "ORDER BY COALESCE(training_module_order, module_priority) ASC"
        );
        $lastIdForPrev = null;
        while ($r = mysqli_fetch_assoc($orderOnceQ)) {
            $midOrdered = (int)$r['training_module_id'];
            if ($lastIdForPrev !== null) {
                $prevModuleIdById[$midOrdered] = $lastIdForPrev;
            }
            $lastIdForPrev = $midOrdered;
        }
        $companyCreatedDateGlobal = '';
        $socOnce = $d->selectRow("created_date", "society_master", "society_id='" . (int)$society_id . "'");
        if ($socOnce && mysqli_num_rows($socOnce) > 0) {
            $cdOnce = mysqli_fetch_assoc($socOnce);
            $companyCreatedDateGlobal = $cdOnce['created_date'];
        }

        $participant_names = array_keys($participants);
        $idStyle         = "min-width: 10px;";
        $moduleNameStyle = "min-width: 150px;";
        $statusColStyle  = "";
        $dateColStyle    = "";

        echo '<div style="width:100%; overflow-x:auto;">';

        echo '<table id="productTableHeader" class="table table-bordered" style="width:100%; border-collapse:collapse;">';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="' . $idStyle . '">Id</th>';
        echo '<th style="' . $moduleNameStyle . '">Module Name</th>';
        echo '<th style="min-width: 140px;">Participant</th>';
        echo '<th style="min-width: 80px;">Days</th>';
        echo '<th style="min-width: 140px;">Due</th>';
        echo '<th style="min-width: 120px;">Status</th>';
        echo '<th style="min-width: 160px;">Date</th>';
        echo '<th style="min-width: 160px;">Subtopics</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        $i = 1;
        // Sort modules by topic, then by explicit training_module_order (fallback to priority_id), then by name
        uksort($modules, function ($a, $b) use ($modules) {
            $ta = isset($modules[$a]['topic_name']) ? $modules[$a]['topic_name'] : 'Other';
            $tb = isset($modules[$b]['topic_name']) ? $modules[$b]['topic_name'] : 'Other';
            $tc = strcasecmp($ta, $tb);
            if ($tc !== 0) return $tc;
            $oa = isset($modules[$a]['module_order']) && $modules[$a]['module_order'] !== null ? (int)$modules[$a]['module_order'] : (isset($modules[$a]['priority_id']) ? (int)$modules[$a]['priority_id'] : PHP_INT_MAX);
            $ob = isset($modules[$b]['module_order']) && $modules[$b]['module_order'] !== null ? (int)$modules[$b]['module_order'] : (isset($modules[$b]['priority_id']) ? (int)$modules[$b]['priority_id'] : PHP_INT_MAX);
            if ($oa !== $ob) return ($oa < $ob) ? -1 : 1;
            return strcasecmp($a, $b);
        });

        $currentTopic = null;

        // Prefetch all subtopic statuses once (before module loop)
        $allSubtopicStatusMap = [];
        $allSubIds = [];
        foreach ($modules as $modName => $modData) {
            if (!empty($modData['subtopics'])) {
                foreach ($modData['subtopics'] as $s) {
                    $allSubIds[] = (int)$s['subtopic_id'];
                }
            }
        }
        $allSubIds = array_values(array_unique($allSubIds));
        if (!empty($allSubIds)) {
            $allSubCsv = implode(',', $allSubIds);
            $sidInt = (int)$society_id;
            $stQAll = $d->selectRow(
                "ts.subtopic_id, ts.participant_id, ts.subtopic_company_status, ts.completed_at, tpt.participant_name",
                "training_subtopic_company_status ts INNER JOIN training_participants_type tpt ON tpt.participants_type_id = ts.participant_id",
                "ts.company_id = '$sidInt' AND ts.subtopic_id IN ($allSubCsv)"
            );
            while ($st = mysqli_fetch_assoc($stQAll)) {
                $sid = (int)$st['subtopic_id'];
                $pname = $st['participant_name'];
                if (!isset($allSubtopicStatusMap[$sid])) {
                    $allSubtopicStatusMap[$sid] = [];
                }
                $allSubtopicStatusMap[$sid][$pname] = [
                    'status' => (string)$st['subtopic_company_status'],
                    'date' => $st['completed_at'] ?? ''
                ];
            }
        }

        foreach ($modules as $module_name => $participant_data) {
            $moduleId = isset($participant_data['module_id']) ? (int)$participant_data['module_id'] : 0;
            $collapseId = 'prod_subs_' . $society_id . '_' . $moduleId;
            $topicHeader = isset($participant_data['topic_name']) && $participant_data['topic_name'] !== '' ? $participant_data['topic_name'] : 'Other';
            if ($currentTopic !== $topicHeader) {
                $currentTopic = $topicHeader;
                echo '<tr class="table-active">';
                echo '<td colspan="8"><strong>Topic: ' . htmlspecialchars($currentTopic) . '</strong></td>';
                echo '</tr>';
            }
            echo '<tr>';
            echo '<td style="' . $idStyle . '">' . $i++ . '</td>';
            $priorityText = isset($participant_data['priority']) ? $participant_data['priority'] : '';
            echo '<td style="' . $moduleNameStyle . '">' . $module_name . ' (' . $priorityText . ')</td>';
            // Assigned participant, due and status for the assigned participant only
            $isOverdue = false;
            $isCompletedLate = false;
            $dueDisplay = '<span class="text-muted"></span>';
            $assignedParticipantName = '<span class="text-muted"></span>';
            $companyCreatedDate = $companyCreatedDateGlobal;
            if (!empty($companyCreatedDate)) {
                $moduleIdForDue = isset($participant_data['module_id']) ? (int)$participant_data['module_id'] : 0;
                $assignedPid = isset($participant_data['participant_id']) ? (int)$participant_data['participant_id'] : 0;
                if ($moduleIdForDue > 0 && $assignedPid > 0) {
                    $pname = isset($participantIdToName[$assignedPid]) ? $participantIdToName[$assignedPid] : null;
                    if ($pname) { $assignedParticipantName = htmlspecialchars($pname); }
                    $pStatus = $pname && isset($participant_data[$pname]['status']) ? (int)$participant_data[$pname]['status'] : null;
                    $dueInfo = calculateModuleDueDate($d, (int)$society_id, $assignedPid, $moduleIdForDue, $companyCreatedDate);
                    if (!empty($dueInfo['due_date'])) {
                        $today = date('Y-m-d');
                        $dueDateStr = date('d M Y', strtotime($dueInfo['due_date']));
                        $daysDiff = (int)round((strtotime($dueInfo['due_date']) - strtotime($today)) / (60*60*24));
                        if ($daysDiff < 0) {
                            $isOverdue = true;
                            $dueDisplay = $dueDateStr . ' <span class="badge badge-danger ml-1">' . abs($daysDiff) . ' days over</span>';
                        } else {
                            $dueDisplay = $dueDateStr . ' <span class="badge badge-secondary ml-1">' . $daysDiff . ' days left</span>';
                        }
                    }
                    if (!($pStatus === 1 || $pStatus === 2 || $pStatus === 3)) {
                        if (!empty($dueInfo['is_overdue'])) { $isOverdue = true; }
                    } else {
                        // Optional: still detect late completion using aggregate
                        $prevId = isset($prevModuleIdById[$moduleIdForDue]) ? $prevModuleIdById[$moduleIdForDue] : null;
                        $baseDate = substr($companyCreatedDate, 0, 10);
                        if ($prevId !== null) {
                            $aggPrev = getAggregateModuleDoneDate($d, (int)$society_id, $prevId);
                            if (!empty($aggPrev)) { $baseDate = $aggPrev; }
                        }
                        $currentAgg = getAggregateModuleDoneDate($d, (int)$society_id, $moduleIdForDue);
                        $cdaysLocal = isset($participant_data['completion_days']) ? (int)$participant_data['completion_days'] : null;
                        if ($baseDate && $currentAgg && $cdaysLocal !== null && $cdaysLocal > 0) {
                            $dueCalc = date('Y-m-d', strtotime($baseDate . ' +' . $cdaysLocal . ' days'));
                            if ($currentAgg > $dueCalc) { $isCompletedLate = true; }
                        }
                    }
                }
            }
            echo '<td>' . $assignedParticipantName . '</td>';
            echo '<td>' . (isset($participant_data['completion_days']) ? (int)$participant_data['completion_days'] : '-') . '</td>';
            echo '<td>' . $dueDisplay . '</td>';
            // Assigned participant status/date only
            $statusText = '<span class="text-muted"></span>';
            $displayDate = '';
            $assignedPidLocal = isset($participant_data['participant_id']) ? (int)$participant_data['participant_id'] : 0;
            $assignedPnameLocal = $assignedPidLocal && isset($participantIdToName[$assignedPidLocal]) ? $participantIdToName[$assignedPidLocal] : null;
            $raw_status_assigned = $assignedPnameLocal && isset($participant_data[$assignedPnameLocal]['status']) ? $participant_data[$assignedPnameLocal]['status'] : '';
            $date_assigned = $assignedPnameLocal && isset($participant_data[$assignedPnameLocal]['date']) ? $participant_data[$assignedPnameLocal]['date'] : '';
            if ($raw_status_assigned !== '') {
                if ($raw_status_assigned == '1') {
                    $statusText = '<span style="color: green; font-weight: bold;">Completed</span>';
                } elseif ($raw_status_assigned == '0') {
                    $statusText = '<span style="color: red; font-weight: bold;">Pending</span>';
                } elseif ($raw_status_assigned == '2') {
                    $statusText = '<span style="color: blue; font-weight: bold;">Not Applicable</span>';
                } elseif ($raw_status_assigned == '3') {
                    $statusText = '<span style="color: #6f42c1; font-weight: bold;">Later On</span>';
                }
            }
            if (!empty($date_assigned)) {
                $timestamp = strtotime($date_assigned);
                if ($timestamp && $timestamp > 0) {
                    $displayDate = date('D d M Y h:i A', $timestamp);
                }
            }
            echo '<td>' . $statusText . '</td>';
            echo '<td>' . (!empty($displayDate) ? $displayDate : '<span class="text-muted"></span>') . '</td>';
            echo '<td>';
            if (!empty($participant_data['subtopics'])) {
                echo '<button class="btn btn-sm btn-primary" type="button" data-toggle="collapse" data-target="#' . $collapseId . '" aria-expanded="false" aria-controls="' . $collapseId . '">View Subtopics (' . count($participant_data['subtopics']) . ')</button>';
            } else {
                echo '<span class="text-muted">None</span>';
            }
            echo '</td>';
            echo '</tr>';
            if (!empty($participant_data['subtopics'])) {
                $statusMap = $allSubtopicStatusMap;

                echo '<tr class="collapse" id="' . $collapseId . '">';
                echo '<td colspan="8">';
                echo '<div class="p-2">';
                echo '<div class="table-responsive">';
                echo '<table class="table table-sm table-striped mb-0">';
                echo '<thead><tr>';
                echo '<th style="width:10%;">ID</th>';
                echo '<th style="width:40%;">Subtopic</th>';
                echo '<th style="width:15%;">Participant</th>';
                echo '<th style="width:15%;">Status</th>';
                echo '<th style="width:20%;">Date</th>';
                echo '</tr></thead>';
                echo '<tbody>';
                $subtopicCounter = 1;
                $modulePriorityId = isset($participant_data['priority_id']) ? $participant_data['priority_id'] : $i;
                foreach ($participant_data['subtopics'] as $sub) {
                    $desc = !empty($sub['subtopic_description']) ? htmlspecialchars($sub['subtopic_description']) : '';
                    echo '<tr>';
                    echo '<td>' . $i - 1 . '.' . $subtopicCounter . '</td>';
                    echo '<td>' . htmlspecialchars($sub['subtopic_name']);
                    if (!empty($desc)) {
                        echo ' <i class="fa fa-info-circle text-info ml-2" data-toggle="tooltip" data-placement="top" title="' . $desc . '"></i>';
                    }
                    echo '</td>';
                    // Participant, Status, Date for assigned participant only
                    $assignedPidLocal = isset($participant_data['participant_id']) ? (int)$participant_data['participant_id'] : 0;
                    $assignedPnameLocal = $assignedPidLocal && isset($participantIdToName[$assignedPidLocal]) ? $participantIdToName[$assignedPidLocal] : '';
                    echo '<td>' . htmlspecialchars($assignedPnameLocal) . '</td>';
                    $entry = ($assignedPnameLocal !== '' && isset($statusMap[$sub['subtopic_id']][$assignedPnameLocal])) ? $statusMap[$sub['subtopic_id']][$assignedPnameLocal] : null;
                    $stVal = $entry ? $entry['status'] : '';
                    $dateRaw = $entry ? $entry['date'] : '';
                    $statusHtml = '<span class="badge badge-secondary">-</span>';
                    if ($stVal === '1') {
                        $statusHtml = '<span class="badge badge-success">Completed</span>';
                    } elseif ($stVal === '0') {
                        $statusHtml = '<span class="badge badge-warning">Pending</span>';
                    } elseif ($stVal === '2') {
                        $statusHtml = '<span class="badge badge-info">Not Applicable</span>';
                    } elseif ($stVal === '3') {
                        $statusHtml = '<span class="badge badge-primary">Later On</span>';
                    }
                    echo '<td>' . $statusHtml . '</td>';
                    $dateDisp = '';
                    if (!empty($dateRaw)) {
                        $ts = strtotime($dateRaw);
                        if ($ts && $ts > 0) {
                            $dateDisp = date('D, d M Y h:i A', $ts);
                        }
                    }
                    echo '<td>' . (!empty($dateDisp) ? $dateDisp : '<span class="text-muted"></span>') . '</td>';
                    echo '</tr>';
                    $subtopicCounter++;
                }
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
    }
}
