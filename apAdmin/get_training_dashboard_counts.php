<?php
include_once 'common/object.php';
include_once 'timeline_functions.php'; 

$cityId = isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : 0;
$stateId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
$cityId = $cityId > 0 ? $cityId : null;
$stateId = $stateId > 0 ? $stateId : null;
$durationType = $_GET['duration_type'] ?? null;
$riseFilter = $_GET['rise_filter'] ?? "yes";
$expiryFilter = $_GET['expiry_filter'] ?? 'not_expired';
$productType = $_GET['product_type'] ?? 'hrms'; // 'hrms' or 'crm'

$stateCityFilter = '';
$setupFilter = '';
$companyFilter = ''; 
$trainingFilter = '';
$riseFilterCondition = '';
$expiryFilterCondition = '';
$productTypeFilter = '';

if (!empty($stateId)) {
    $stateCityFilter .= " AND state_id = '$stateId'";
}
if (!empty($cityId)) {
    $stateCityFilter .= " AND city_id = '$cityId'";
}

// Myco Rise filter on companies
if (!isset($_GET['rise_filter']) || $riseFilter === "yes") {
    $riseFilterCondition = " AND IFNULL(from_rise_event,0) = 1";
} elseif ($riseFilter === "no") {
    $riseFilterCondition = " AND (from_rise_event IS NULL OR from_rise_event = 0)";
} else {
    $riseFilterCondition = "";
}

// Expiry filter on companies
if ($expiryFilter === "expired") {
    $expiryFilterCondition = " AND (plan_expire_date IS NOT NULL AND plan_expire_date < CURDATE())";
} elseif ($expiryFilter === "not_expired") {
    $expiryFilterCondition = " AND (plan_expire_date IS NULL OR plan_expire_date >= CURDATE())";
} else {
    $expiryFilterCondition = "";
}

// Product type filter on companies
if ($productType === 'crm') {
    $productTypeFilter = " AND IFNULL(crm_created,0) = 1";
} else {
    // For HRMS, we can optionally exclude CRM companies or include all
    // $productTypeFilter = " AND IFNULL(crm_created,0) = 0"; // Uncomment to exclude CRM companies from HRMS
}

// Duration Filters
if (!empty($durationType)) {
    if ($durationType == '1') {
        $setupFilter .= " AND DATE(training_date) = CURDATE()";
        $companyFilter .= " AND DATE(created_date) = CURDATE()";
        $trainingFilter .= " AND DATE(date) = CURDATE()";
    } elseif ($durationType == '2') {
        $setupFilter .= " AND MONTH(training_date) = MONTH(CURDATE()) AND YEAR(training_date) = YEAR(CURDATE())";
        $companyFilter .= " AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE())";
        $trainingFilter .= " AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
    } elseif ($durationType == '3') {
        $setupFilter .= " AND YEAR(training_date) = YEAR(CURDATE())";
        $companyFilter .= " AND YEAR(created_date) = YEAR(CURDATE())";
        $trainingFilter .= " AND YEAR(date) = YEAR(CURDATE())";
    }
}

$totalSetup = $d->count_data_direct("training_schedule_master_id", "training_schedule_master", "session_id!='' AND training_date IS NOT NULL $setupFilter");
$completedSetup = $d->count_data_direct("training_schedule_master_id", "training_schedule_master", "meeting_status=1 $setupFilter");
$pendingSetupSessions = $d->count_data_direct("training_schedule_master_id", "training_schedule_master", "meeting_status=0 $setupFilter");

$totalCompanies = $d->count_data_direct("society_id", "society_master", "created_on_society_server = 1" . $riseFilterCondition . $expiryFilterCondition . $productTypeFilter . (!empty($stateCityFilter) ? $stateCityFilter : "") . (!empty($companyFilter) ? $companyFilter : ""));
$totalDoneCompanies = $d->count_data_direct("society_id", "society_master", "created_on_society_server = 1 AND setup_training_status=1" . $riseFilterCondition . $expiryFilterCondition . (!empty($stateCityFilter) ? $stateCityFilter : "") . (!empty($companyFilter) ? $companyFilter : ""));
$totalPendingCompanies = $d->count_data_direct("society_id", "society_master", "created_on_society_server = 1 AND setup_training_status=0" . $riseFilterCondition . $expiryFilterCondition . (!empty($stateCityFilter) ? $stateCityFilter : "") . (!empty($companyFilter) ? $companyFilter : ""));

$crm = $d->count_data_direct("society_id", "society_master", "crm_created = 1 AND created_on_society_server = 1" . $riseFilterCondition . $expiryFilterCondition . (!empty($stateCityFilter) ? $stateCityFilter : "") . (!empty($companyFilter) ? $companyFilter : ""));
$notCrm = $d->count_data_direct("society_id", "society_master", "crm_created = 0 AND created_on_society_server = 1" . $riseFilterCondition . $expiryFilterCondition . (!empty($stateCityFilter) ? $stateCityFilter : "") . (!empty($companyFilter) ? $companyFilter : ""));

$keyAccounts = $d->count_data_direct("society_id", "society_master", "account_type = 1 AND created_on_society_server = 1" . $riseFilterCondition . $expiryFilterCondition . (!empty($stateCityFilter) ? $stateCityFilter : "") . (!empty($companyFilter) ? $companyFilter : ""));
$keyAccountsDone = $d->count_data_direct("society_id", "society_master", "account_type = 1 AND training_status = 1 AND created_on_society_server = 1" . $riseFilterCondition . $expiryFilterCondition . (!empty($stateCityFilter) ? $stateCityFilter : "") . (!empty($companyFilter) ? $companyFilter : ""));
$keyAccountsPending = $d->count_data_direct("society_id", "society_master", "account_type = 1 AND training_status = 0 AND created_on_society_server = 1" . $riseFilterCondition . $expiryFilterCondition . (!empty($stateCityFilter) ? $stateCityFilter : "") . (!empty($companyFilter) ? $companyFilter : ""));

$totalMeetings = $d->count_data_direct("slot_id", "batch_slot_master", "trainer_id!='' AND company_id!='' AND date IS NOT NULL  $trainingFilter");
$completedMeetings = $d->count_data_direct("slot_id", "batch_slot_master", "meeting_status=1  $trainingFilter");
$pendingMeetings = $d->count_data_direct("slot_id", "batch_slot_master", "meeting_status=0  $trainingFilter");

$productImplementationDone = $d->count_data_direct("society_id", "society_master", "training_status = 1 AND created_on_society_server = 1" . $riseFilterCondition . $expiryFilterCondition . (!empty($stateCityFilter) ? $stateCityFilter : "") . (!empty($companyFilter) ? $companyFilter : ""));
$productImplementationPending = $d->count_data_direct("society_id", "society_master", "training_status = 0 AND training_percentage = 0 AND created_on_society_server = 1" . $riseFilterCondition . $expiryFilterCondition . (!empty($stateCityFilter) ? $stateCityFilter : "") . (!empty($companyFilter) ? $companyFilter : ""));
$productImplementationRunning = $d->count_data_direct("society_id", "society_master", "training_status = 0 AND training_percentage > 0 AND created_on_society_server = 1" . $riseFilterCondition . $expiryFilterCondition . (!empty($stateCityFilter) ? $stateCityFilter : "") . (!empty($companyFilter) ? $companyFilter : ""));

// Training modules count (module_type: 1 = HRMS, 2 = CRM)
$moduleTypeForTraining = ($productType === 'crm') ? 2 : 1;
$trainingresult = $d->select(
    'training_module_master tmm 
     JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id',
    "tmm.module_type = $moduleTypeForTraining AND tmm.training_module_status = 0 AND tmpm.is_required = 1"
);
$training_modules_count = mysqli_num_rows($trainingresult);

// Get active participant types
$activeParticipantTypes = [];
$participantIdByName = [];
$participantsResult = $d->select("training_participants_type", "status = 0");
while ($row = mysqli_fetch_assoc($participantsResult)) {
    $activeParticipantTypes[] = $row['participant_name'];
    $participantIdByName[$row['participant_name']] = isset($row['participants_type_id']) ? (int)$row['participants_type_id'] : 0;
}

// Map participant_id => [module_ids] (topic_type: '0' = HRMS, '1' = CRM)
$topicTypeForTraining = ($productType === 'crm') ? '1' : '0';
$participantModules = [];
$modsQ = $d->selectRow(
    "tmm.training_module_id, tmt.participant_type AS participant_id",
    "training_module_master tmm LEFT JOIN training_module_topics tmt ON tmm.topic_id = tmt.topic_id AND tmt.topic_type = '$topicTypeForTraining' LEFT JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
    "tmm.module_type = $moduleTypeForTraining AND tmm.training_module_status = 0",
    "ORDER BY COALESCE(tmm.training_module_order, tmm.module_priority) ASC"
);
while ($m = mysqli_fetch_assoc($modsQ)) {
    $pid = isset($m['participant_id']) ? (int)$m['participant_id'] : 0;
    $mid = (int)$m['training_module_id'];
    if ($pid > 0) {
        if (!isset($participantModules[$pid])) { $participantModules[$pid] = []; }
        $participantModules[$pid][] = $mid;
    }
}

// Required module counts per participant
$participantRequiredCounts = [];
foreach ($activeParticipantTypes as $name) { $participantRequiredCounts[$name] = 0; }
$requiredQ = $d->selectRow(
    "tmt.participant_type AS participant_id",
    "training_module_master tmm 
        LEFT JOIN training_module_topics tmt ON tmm.topic_id = tmt.topic_id AND tmt.topic_type = '0'
        LEFT JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
    "tmm.module_type = 1 AND tmm.training_module_status = 0 AND tmpm.is_required = 1"
);
while ($rm = mysqli_fetch_assoc($requiredQ)) {
    $pid = isset($rm['participant_id']) ? (int)$rm['participant_id'] : 0;
    if ($pid > 0) {
        $pname = array_search($pid, $participantIdByName, true);
        if ($pname !== false) {
            $participantRequiredCounts[$pname] = isset($participantRequiredCounts[$pname]) ? ($participantRequiredCounts[$pname] + 1) : 1;
        }
    }
}
$allCompaniesRequiredTotal = 0;
foreach ($participantRequiredCounts as $cnt) { $allCompaniesRequiredTotal += (int)$cnt; }

// ========== OPTIMIZATION: Fetch all companies once ==========
$companiesFilter = "created_on_society_server = 1" . $riseFilterCondition . $expiryFilterCondition . $productTypeFilter . (!empty($companyFilter) ? $companyFilter : "") . (!empty($stateCityFilter) ? $stateCityFilter : "");
$allCompanies = [];
$companiesQuery = $d->select("society_master", $companiesFilter, "");
while ($company = mysqli_fetch_assoc($companiesQuery)) {
    $allCompanies[(int)$company['society_id']] = $company;
}

// ========== OPTIMIZATION: Fetch city names in bulk (needed for company info) ==========
$cityIds = array_unique(array_column($allCompanies, 'city_id'));
$cityNameMap = [];
if (!empty($cityIds)) {
    $cityIdsStr = implode(',', array_map('intval', $cityIds));
    $cityQuery = $d->selectRow("city_id, name", "cities", "city_id IN ($cityIdsStr)");
    while ($city = mysqli_fetch_assoc($cityQuery)) {
        $cityNameMap[(int)$city['city_id']] = $city['name'];
    }
}

// ========== OPTIMIZATION: Fetch all training statuses in bulk ==========
$allTrainingStatuses = [];
if ($productType === 'crm') {
    // CRM: Use crm_training_progress table
    $trainingStatusQuery = $d->selectRow(
        "society_id as company_id, module_id, status as training_status",
        "crm_training_progress",
        "society_id IN (" . (!empty($allCompanies) ? implode(',', array_map('intval', array_keys($allCompanies))) : '0') . ")"
    );
    while ($ts = mysqli_fetch_assoc($trainingStatusQuery)) {
        $cid = (int)$ts['company_id'];
        $mid = (int)$ts['module_id'];
        if (!isset($allTrainingStatuses[$cid])) {
            $allTrainingStatuses[$cid] = [];
        }
        // For CRM, we use a dummy participant_id of 0 since CRM doesn't use participants
        if (!isset($allTrainingStatuses[$cid][0])) {
            $allTrainingStatuses[$cid][0] = [];
        }
        $allTrainingStatuses[$cid][0][$mid] = (int)$ts['training_status'];
    }
} else {
    // HRMS: Use batch_training_status_master table
    $trainingStatusQuery = $d->selectRow(
        "company_id, participant_id, module_id, training_status",
        "batch_training_status_master",
        "company_id IN (" . (!empty($allCompanies) ? implode(',', array_map('intval', array_keys($allCompanies))) : '0') . ")"
    );
    while ($ts = mysqli_fetch_assoc($trainingStatusQuery)) {
        $cid = (int)$ts['company_id'];
        $pid = (int)$ts['participant_id'];
        $mid = (int)$ts['module_id'];
        if (!isset($allTrainingStatuses[$cid])) {
            $allTrainingStatuses[$cid] = [];
        }
        if (!isset($allTrainingStatuses[$cid][$pid])) {
            $allTrainingStatuses[$cid][$pid] = [];
        }
        $allTrainingStatuses[$cid][$pid][$mid] = (int)$ts['training_status'];
    }
}

// ========== OPTIMIZATION: Fetch all setup module statuses in bulk ==========
$allSetupStatuses = [];
$setupStatusQuery = $d->selectRow(
    "mtsm.company_id, mtsm.module_id, mtsm.onboarding_status",
    "training_module_master tmm
     LEFT JOIN module_training_status_master mtsm ON tmm.training_module_id = mtsm.module_id
     LEFT JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
    "tmm.module_type = 0 AND tmm.training_module_status = 0 AND tmpm.is_required = 1 AND mtsm.company_id IN (" . (!empty($allCompanies) ? implode(',', array_map('intval', array_keys($allCompanies))) : '0') . ")"
);
while ($ss = mysqli_fetch_assoc($setupStatusQuery)) {
    $cid = (int)$ss['company_id'];
    if (!isset($allSetupStatuses[$cid])) {
        $allSetupStatuses[$cid] = [];
    }
    $allSetupStatuses[$cid][] = (int)($ss['onboarding_status'] ?? -1);
}

// ========== OPTIMIZATION: Fetch all topic completion stats in bulk ==========
$allTopicStats = [];
if ($productType === 'crm') {
    // CRM: Use crm_training_progress table
    $topicStatsQuery = $d->selectRow(
        "tmt.topic_id, ctp.society_id as company_id,
         COUNT(DISTINCT tmm.training_module_id) as total_modules,
         COUNT(DISTINCT CASE WHEN ctp.status IN (1,2,3) THEN ctp.module_id END) as completed_modules,
         COUNT(DISTINCT ctp.module_id) as started_modules",
        "training_module_topics tmt
         LEFT JOIN training_module_master tmm ON tmt.topic_id = tmm.topic_id AND tmm.module_type = 2 AND tmm.training_module_status = 0
         LEFT JOIN crm_training_progress ctp ON ctp.module_id = tmm.training_module_id
         LEFT JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
        "tmt.topic_status = 1 AND tmt.topic_type = '1' AND tmpm.is_required = 1 AND ctp.society_id IN (" . (!empty($allCompanies) ? implode(',', array_map('intval', array_keys($allCompanies))) : '0') . ")",
        "GROUP BY tmt.topic_id, ctp.society_id"
    );
} else {
    // HRMS: Use batch_training_status_master table
    $topicStatsQuery = $d->selectRow(
        "tmt.topic_id, btsm.company_id,
         COUNT(DISTINCT tmm.training_module_id) as total_modules,
         COUNT(DISTINCT CASE WHEN btsm.training_status IN (1,2,3) THEN btsm.module_id END) as completed_modules,
         COUNT(DISTINCT btsm.module_id) as started_modules",
        "training_module_topics tmt
         LEFT JOIN training_module_master tmm ON tmt.topic_id = tmm.topic_id AND tmm.module_type = 1 AND tmm.training_module_status = 0
         LEFT JOIN batch_training_status_master btsm ON btsm.module_id = tmm.training_module_id AND btsm.participant_id = tmt.participant_type
         LEFT JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
        "tmt.topic_status = 1 AND tmt.topic_type = '0' AND tmpm.is_required = 1 AND btsm.company_id IN (" . (!empty($allCompanies) ? implode(',', array_map('intval', array_keys($allCompanies))) : '0') . ")",
        "GROUP BY tmt.topic_id, btsm.company_id"
    );
}
while ($ts = mysqli_fetch_assoc($topicStatsQuery)) {
    $topicId = (int)$ts['topic_id'];
    $cid = (int)$ts['company_id'];
    if (!isset($allTopicStats[$cid])) {
        $allTopicStats[$cid] = [];
    }
    $allTopicStats[$cid][$topicId] = [
        'total_modules' => (int)$ts['total_modules'],
        'completed_modules' => (int)$ts['completed_modules'],
        'started_modules' => (int)$ts['started_modules']
    ];
}

// Get session names
$sessionNames = [];
$sessionResult = $d->select("session_day_master", "session_day_status=0");
while ($row = mysqli_fetch_assoc($sessionResult)) {
    $sessionNames[] = $row['session_day_name'];
}

// Get all product training topics (topic_type: '0' = HRMS, '1' = CRM)
$topicsResult = $d->select("training_module_topics", "topic_status = 1 AND topic_type = '$topicTypeForTraining'");
$topicStages = [];
while ($topic = mysqli_fetch_assoc($topicsResult)) {
    $topicId = $topic['topic_id'];
    $topicName = $topic['topic_name'];
    $topicStages[$topicId] = $topicName;
}

// ========== OPTIMIZATION: Pre-fetch all modules ordered (used by due date calculations) ==========
$allModulesOrdered = [];
$allModulesQuery = $d->selectRow(
    "training_module_id, module_priority, completion_days, training_module_order",
    "training_module_master",
    "module_type = $moduleTypeForTraining AND training_module_status = 0",
    "ORDER BY COALESCE(training_module_order, module_priority) ASC"
);
while ($row = mysqli_fetch_assoc($allModulesQuery)) {
    $allModulesOrdered[] = $row;
}
$moduleIndexMap = [];
foreach ($allModulesOrdered as $index => $mod) {
    $moduleIndexMap[(int)$mod['training_module_id']] = $index;
}

// ========== OPTIMIZATION: Pre-fetch active participant count ==========
$activeParticipantCount = 0;
$activeCntQ = $d->selectRow("COUNT(*) AS total", "training_participants_type", "status = 0");
if ($activeCntQ && mysqli_num_rows($activeCntQ) > 0) {
    $activeCntRow = mysqli_fetch_assoc($activeCntQ);
    $activeParticipantCount = (int)$activeCntRow['total'];
}

// ========== OPTIMIZATION: Pre-fetch all aggregate module completion dates in bulk ==========
$allAggregateDates = [];
if (!empty($allCompanies) && $activeParticipantCount > 0) {
    $companyIds = array_map('intval', array_keys($allCompanies));
    $companyIdsStr = implode(',', $companyIds);
    $moduleIds = array_map(function($m) { return (int)$m['training_module_id']; }, $allModulesOrdered);
    if (!empty($moduleIds)) {
        $moduleIdsStr = implode(',', $moduleIds);
        // Bulk query for aggregate completion dates
        $aggQuery = $d->selectRow(
            "company_id, module_id, COUNT(DISTINCT participant_id) AS done_cnt, MAX(training_date) AS latest_date",
            "batch_training_status_master",
            "company_id IN ($companyIdsStr) AND module_id IN ($moduleIdsStr) AND training_status IN (1,2,3)",
            "GROUP BY company_id, module_id"
        );
        while ($agg = mysqli_fetch_assoc($aggQuery)) {
            $cid = (int)$agg['company_id'];
            $mid = (int)$agg['module_id'];
            $doneCnt = (int)$agg['done_cnt'];
            if ($doneCnt >= $activeParticipantCount) {
                $latest = isset($agg['latest_date']) ? substr($agg['latest_date'], 0, 10) : null;
                if ($latest) {
                    if (!isset($allAggregateDates[$cid])) {
                        $allAggregateDates[$cid] = [];
                    }
                    $allAggregateDates[$cid][$mid] = $latest;
                }
            }
        }
    }
}

// ========== OPTIMIZED due date calculation function (in-memory, no DB queries) ==========
function calculateModuleDueDateOptimized($companyId, $participantId, $moduleId, $companyCreatedDate, $allModulesOrdered, $moduleIndexMap, $allAggregateDates) {
    $moduleIndex = isset($moduleIndexMap[$moduleId]) ? $moduleIndexMap[$moduleId] : -1;
    if ($moduleIndex === -1) {
        return ['due_date' => null, 'is_overdue' => false];
    }
    
    $module = $allModulesOrdered[$moduleIndex];
    $completionDays = intval($module['completion_days']);
    if ($completionDays <= 0) {
        return ['due_date' => null, 'is_overdue' => false];
    }
    
    $baseDate = $companyCreatedDate ? substr($companyCreatedDate, 0, 10) : null;
    if ($moduleIndex > 0) {
        $previousModule = $allModulesOrdered[$moduleIndex - 1];
        $previousModuleId = (int)$previousModule['training_module_id'];
        if (isset($allAggregateDates[$companyId][$previousModuleId])) {
            $baseDate = $allAggregateDates[$companyId][$previousModuleId];
        } else {
            return ['due_date' => null, 'is_overdue' => false];
        }
    }
    
    if (!$baseDate) {
        return ['due_date' => null, 'is_overdue' => false];
    }
    
    $dueDate = date('Y-m-d', strtotime($baseDate . ' +' . $completionDays . ' days'));
    $today = date('Y-m-d');
    $isOverdue = $dueDate < $today;
    return [
        'due_date' => $dueDate,
        'is_overdue' => $isOverdue,
        'days_overdue' => $isOverdue ? (int)((strtotime($today) - strtotime($dueDate)) / (60 * 60 * 24)) : 0
    ];
}

// Initialize stats arrays
$sessionStats = [];
$sessionStats["All Companies"] = ['running' => 0, 'pending' => 0, 'finished' => 0];
foreach ($activeParticipantTypes as $type) {
    $sessionStats[$type] = ['running' => 0, 'pending' => 0, 'finished' => 0];
}

$setupSessionStats = [];
$setupSessionStats["All Companies"] = ['running' => 0, 'pending' => 0, 'finished' => 0];
foreach ($sessionNames as $sessionName) {
    $setupSessionStats[$sessionName] = ['running' => 0, 'pending' => 0, 'finished' => 0];
}

$implementationStatus = [];
$implementationStatus["Full Implementation (All Steps)"] = ['running' => 0, 'completed' => 0, 'pending' => 0, 'companies' => ['running' => [], 'completed' => [], 'pending' => []]];
$implementationStatus["Welcome Email"] = ['running' => 0, 'completed' => 0, 'pending' => 0, 'companies' => ['running' => [], 'completed' => [], 'pending' => []]];
if ($productType === 'hrms') {
    $implementationStatus["WhatsApp Group"] = ['running' => 0, 'completed' => 0, 'pending' => 0, 'companies' => ['running' => [], 'completed' => [], 'pending' => []]];
    $implementationStatus["Data Collection & Setup"] = ['running' => 0, 'completed' => 0, 'pending' => 0, 'companies' => ['running' => [], 'completed' => [], 'pending' => []]];
}
foreach ($topicStages as $topicId => $topicName) {
    $implementationStatus[$topicName] = ['running' => 0, 'completed' => 0, 'pending' => 0, 'companies' => ['running' => [], 'completed' => [], 'pending' => []]];
}
$implementationStatus["Handover to Support Team"] = ['running' => 0, 'completed' => 0, 'pending' => 0, 'companies' => ['running' => [], 'completed' => [], 'pending' => []]];

// ========== OPTIMIZATION: Single loop through all companies ==========
foreach ($allCompanies as $companyId => $company) {
    $companyId = (int)$companyId;
    
    // === Session Stats (Product Training) ===
    $companyStatus = [];
    if ($productType === 'crm') {
        // CRM: No participant-based sessions, use a single "All Companies" status
        $doneByPidMid = isset($allTrainingStatuses[$companyId]) ? $allTrainingStatuses[$companyId] : [];
        $allModules = [];
        foreach ($participantModules as $mods) {
            $allModules = array_merge($allModules, $mods);
        }
        $allModules = array_unique($allModules);
        $totalModules = count($allModules);
        $completedCount = 0;
        if (isset($doneByPidMid[0])) {
            foreach ($allModules as $mid) {
                if (isset($doneByPidMid[0][$mid]) && in_array($doneByPidMid[0][$mid], [1,2,3])) {
                    $completedCount++;
                }
            }
        }
        if ($totalModules === 0) {
            $companyStatus["All Companies"] = 'pending';
        } elseif ($completedCount === 0) {
            $companyStatus["All Companies"] = 'pending';
        } elseif ($completedCount < $totalModules) {
            $companyStatus["All Companies"] = 'running';
        } else {
            $companyStatus["All Companies"] = 'finished';
        }
    } else {
        // HRMS: Participant-based sessions
        foreach ($activeParticipantTypes as $type) { $companyStatus[$type] = 'pending'; }
        
        $doneByPidMid = isset($allTrainingStatuses[$companyId]) ? $allTrainingStatuses[$companyId] : [];
        
        foreach ($activeParticipantTypes as $participantName) {
            $pid = isset($participantIdByName[$participantName]) ? (int)$participantIdByName[$participantName] : 0;
            if ($pid <= 0) { continue; }
            $mods = isset($participantModules[$pid]) ? $participantModules[$pid] : [];
            $totalModulesForPid = count($mods);
            if ($totalModulesForPid === 0) {
                $companyStatus[$participantName] = 'pending';
                continue;
            }
            $completedCount = 0;
            foreach ($mods as $mid) {
                if (isset($doneByPidMid[$pid]) && isset($doneByPidMid[$pid][$mid]) && in_array($doneByPidMid[$pid][$mid], [1,2,3])) {
                    $completedCount++;
                }
            }
            if ($completedCount === 0) {
                $companyStatus[$participantName] = 'pending';
            } elseif ($completedCount < $totalModulesForPid) {
                $companyStatus[$participantName] = 'running';
            } else {
                $companyStatus[$participantName] = 'finished';
            }
        }
    }
    
    foreach ($companyStatus as $participantName => $status) {
        $sessionStats[$participantName][$status]++;
    }
    
    $statuses = array_values($companyStatus);
    $uniqueStatuses = array_unique($statuses);
    if (count($uniqueStatuses) === 1) {
        $status = $uniqueStatuses[0];
        $sessionStats["All Companies"][$status]++;
    } else {
        $sessionStats["All Companies"]['running']++;
    }
    
    // === Setup Session Stats ===
    $setupData = json_decode($company['setup_data'], true);
    $companySetupStatus = [];
    
    foreach ($sessionNames as $sessionName) {
        $status = 'pending';
        if (is_array($setupData)) {
            foreach ($setupData as $entry) {
                if ($entry['session_day_name'] == $sessionName) {
                    $moduleCount = $entry['module_count'] ?? 0;
                    $trainingCompleted = $entry['training_completed'] ?? 0;
                    $dataReceiveCompleted = $entry['data_receive_completed'] ?? 0;
                    $onboardingCompleted = $entry['onboarding_completed'] ?? 0;
                    
                    if ($moduleCount > 0 && $trainingCompleted == 0 && $dataReceiveCompleted == 0 && $onboardingCompleted == 0) {
                        $status = 'pending';
                    } elseif ($moduleCount > 0 && ($moduleCount > $trainingCompleted || $moduleCount > $dataReceiveCompleted || $moduleCount > $onboardingCompleted)) {
                        $status = 'running';
                    } elseif ($moduleCount > 0 && $moduleCount <= $trainingCompleted && $moduleCount <= $dataReceiveCompleted && $moduleCount <= $onboardingCompleted) {
                        $status = 'finished';
                    }
                    break;
                }
            }
        }
        $companySetupStatus[$sessionName] = $status;
        $setupSessionStats[$sessionName][$status]++;
    }
    
    $uniqueSetupStatuses = array_unique(array_values($companySetupStatus));
    if (count($uniqueSetupStatuses) === 1) {
        $status = $uniqueSetupStatuses[0];
        $setupSessionStats["All Companies"][$status]++;
    } else {
        $setupSessionStats["All Companies"]['running']++;
    }
    
    // === Implementation Status ===
    // Prepare company info for tracking
    $cityId = isset($company['city_id']) ? (int)$company['city_id'] : 0;
    $companyInfo = [
        'company_id' => $d->short_app_name() . "_" . $companyId,
        'company_name' => $company['society_name'] ?? '',
        'city_name' => $cityNameMap[$cityId] ?? ''
    ];
    
    if ($productType === 'crm') {
        // CRM Welcome Email: Due within 1 day from CRM creation
        $welcomeEmailCompleted = (int)($company['is_crm_welcome_email_send'] ?? 0) === 1;
        $companyCreatedDate = !empty($company['crm_created_date']) ? $company['crm_created_date'] : null;
        $welcomeEmailOverdue = false;
        if (!$welcomeEmailCompleted && $companyCreatedDate) {
            $createdDate = new DateTime($companyCreatedDate);
            $dueDate = clone $createdDate;
            $dueDate->add(new DateInterval('P1D')); // 1 day from CRM creation
            $now = new DateTime();
            $welcomeEmailOverdue = $now > $dueDate;
        }
        $whatsappCompleted = false; // CRM doesn't have WhatsApp Group step
        $whatsappOverdue = false;
    } else {
        // HRMS Welcome Email: Due within 2 days from company creation
        $welcomeEmailCompleted = (int)($company['is_welcome_email_send'] ?? 0) === 1;
        $companyCreatedDate = !empty($company['created_date']) ? $company['created_date'] : null;
        $welcomeEmailOverdue = false;
        if (!$welcomeEmailCompleted && $companyCreatedDate) {
            $createdDate = new DateTime($companyCreatedDate);
            $dueDate = clone $createdDate;
            $dueDate->add(new DateInterval('P2D')); // 2 days from creation
            $now = new DateTime();
            $welcomeEmailOverdue = $now > $dueDate;
        }
    }
    
    if ($welcomeEmailCompleted) {
        $implementationStatus["Welcome Email"]['completed']++;
        $implementationStatus["Welcome Email"]['companies']['completed'][] = $companyInfo;
    } elseif ($welcomeEmailOverdue) {
        // Only count as pending if overdue and not completed
        $implementationStatus["Welcome Email"]['pending']++;
        $implementationStatus["Welcome Email"]['companies']['pending'][] = $companyInfo;
    } else {
        // Pending but not overdue = Running
        $implementationStatus["Welcome Email"]['running']++;
        $implementationStatus["Welcome Email"]['companies']['running'][] = $companyInfo;
    }
    
    // Initialize setup variables
    $setupTotal = 0;
    $setupCompleted = 0;
    $setupInProgress = 0;
    
    if ($productType === 'crm') {
        // CRM doesn't have WhatsApp Group or Setup steps
        // Skip these for CRM
    } else {
        // HRMS WhatsApp Group: Due within 2 days from company creation
        $whatsappCompleted = (int)($company['is_whatsapp_group_created'] ?? 0) === 1;
        $whatsappOverdue = false;
        if (!$whatsappCompleted && $companyCreatedDate) {
            $createdDate = new DateTime($companyCreatedDate);
            $dueDate = clone $createdDate;
            $dueDate->add(new DateInterval('P2D')); // 2 days from creation
            $now = new DateTime();
            $whatsappOverdue = $now > $dueDate;
        }
        
        if ($whatsappCompleted) {
            $implementationStatus["WhatsApp Group"]['completed']++;
            $implementationStatus["WhatsApp Group"]['companies']['completed'][] = $companyInfo;
        } elseif ($whatsappOverdue) {
            // Only count as pending if overdue and not completed
            $implementationStatus["WhatsApp Group"]['pending']++;
            $implementationStatus["WhatsApp Group"]['companies']['pending'][] = $companyInfo;
        } else {
            // Pending but not overdue = Running
            $implementationStatus["WhatsApp Group"]['running']++;
            $implementationStatus["WhatsApp Group"]['companies']['running'][] = $companyInfo;
        }
        
        // Data Collection & Setup (HRMS only)
        $setupStatuses = isset($allSetupStatuses[$companyId]) ? $allSetupStatuses[$companyId] : [];
        $setupTotal = count($setupStatuses);
        $setupCompleted = 0;
        $setupInProgress = 0;
        foreach ($setupStatuses as $status) {
            if ($status === 1 || $status === 2) {
                $setupCompleted++;
            } elseif ($status === 0) {
                $setupInProgress++;
            }
        }
        
        if ($setupTotal > 0) {
            if ($setupCompleted === $setupTotal) {
                $implementationStatus["Data Collection & Setup"]['completed']++;
                $implementationStatus["Data Collection & Setup"]['companies']['completed'][] = $companyInfo;
            } elseif ($setupInProgress > 0 || $setupCompleted > 0) {
                $implementationStatus["Data Collection & Setup"]['running']++;
                $implementationStatus["Data Collection & Setup"]['companies']['running'][] = $companyInfo;
            } else {
                $implementationStatus["Data Collection & Setup"]['pending']++;
                $implementationStatus["Data Collection & Setup"]['companies']['pending'][] = $companyInfo;
            }
        }
    }
    
    // Product Training Topics
    $companyTopicStats = isset($allTopicStats[$companyId]) ? $allTopicStats[$companyId] : [];
    foreach ($topicStages as $topicId => $topicName) {
        if (isset($companyTopicStats[$topicId])) {
            $stats = $companyTopicStats[$topicId];
            $totalModules = $stats['total_modules'];
            $completedModules = $stats['completed_modules'];
            $startedModules = $stats['started_modules'];
            
            if ($totalModules > 0) {
                if ($completedModules >= $totalModules) {
                    $implementationStatus[$topicName]['completed']++;
                    $implementationStatus[$topicName]['companies']['completed'][] = $companyInfo;
                } elseif ($startedModules > 0) {
                    $implementationStatus[$topicName]['running']++;
                    $implementationStatus[$topicName]['companies']['running'][] = $companyInfo;
                } else {
                    $implementationStatus[$topicName]['pending']++;
                    $implementationStatus[$topicName]['companies']['pending'][] = $companyInfo;
                }
            }
        }
    }
    
    // Full Implementation Status
    // Running: At least one step started and others not completed
    // Completed: All processes completed
    // Pending: All modules pending (nothing started)
    $allCompleted = true;
    $hasRunning = false;
    $allPending = true;
    
    // Check Welcome Email
    // If completed OR running (pending but not overdue) = started
    $welcomeEmailStarted = $welcomeEmailCompleted || (!$welcomeEmailCompleted && !$welcomeEmailOverdue);
    if (!$welcomeEmailCompleted) {
        $allCompleted = false;
        if ($welcomeEmailStarted) {
            // Pending but not overdue = running (started)
            $hasRunning = true;
            $allPending = false;
        } else {
            // Overdue and not completed = pending
            $allPending = $allPending && true;
        }
    } else {
        $allPending = false; // At least one completed
        $hasRunning = true; // At least one started/completed
    }
    
    if ($productType === 'crm') {
        // CRM: No WhatsApp Group or Setup steps to check
    } else {
        // HRMS: Check WhatsApp Group
        // If completed OR running (pending but not overdue) = started
        $whatsappStarted = $whatsappCompleted || (!$whatsappCompleted && !$whatsappOverdue);
        if (!$whatsappCompleted) {
            $allCompleted = false;
            if ($whatsappStarted) {
                // Pending but not overdue = running (started)
                $hasRunning = true;
                $allPending = false;
            } else {
                // Overdue and not completed = pending
                $allPending = $allPending && true;
            }
        } else {
            $allPending = false; // At least one completed
            $hasRunning = true; // At least one started/completed
        }
        
        // HRMS: Check Setup
        if ($setupTotal > 0) {
            if ($setupCompleted < $setupTotal) {
                $allCompleted = false;
                if ($setupInProgress > 0 || $setupCompleted > 0) {
                    $hasRunning = true; // At least one started
                    $allPending = false;
                } else {
                    $allPending = $allPending && true; // All pending
                }
            } else {
                $hasRunning = true; // At least one completed
                $allPending = false;
            }
        }
    }
    
    // Check Product Training Topics
    foreach ($topicStages as $topicId => $topicName) {
        if (isset($companyTopicStats[$topicId])) {
            $stats = $companyTopicStats[$topicId];
            $totalModules = $stats['total_modules'];
            $completedModules = $stats['completed_modules'];
            $startedModules = $stats['started_modules'];
            
            if ($totalModules > 0) {
                if ($completedModules < $totalModules) {
                    $allCompleted = false;
                    if ($startedModules > 0) {
                        $hasRunning = true; // At least one started
                        $allPending = false;
                    } else {
                        $allPending = $allPending && true; // All pending
                    }
                } else {
                    $hasRunning = true; // At least one completed
                    $allPending = false;
                }
            }
        }
    }
    
    if ($allCompleted) {
        $implementationStatus["Full Implementation (All Steps)"]['completed']++;
        $implementationStatus["Full Implementation (All Steps)"]['companies']['completed'][] = $companyInfo;
    } elseif ($hasRunning && !$allPending) {
        // At least one step started but not all completed = Running
        $implementationStatus["Full Implementation (All Steps)"]['running']++;
        $implementationStatus["Full Implementation (All Steps)"]['companies']['running'][] = $companyInfo;
    } else {
        // All modules pending = Pending
        $implementationStatus["Full Implementation (All Steps)"]['pending']++;
        $implementationStatus["Full Implementation (All Steps)"]['companies']['pending'][] = $companyInfo;
    }
    
    // Handover to Support Team Status
    // Only eligible if all steps are completed
    if ($allCompleted) {
        if ($productType === 'crm') {
            $handoverCompleted = !empty($company['crm_support_handover']) && (int)$company['crm_support_handover'] === 1;
        } else {
            $handoverCompleted = !empty($company['support_handover']) && (int)$company['support_handover'] === 1;
        }
        if ($handoverCompleted) {
            $implementationStatus["Handover to Support Team"]['completed']++;
            $implementationStatus["Handover to Support Team"]['companies']['completed'][] = $companyInfo;
        } else {
            $implementationStatus["Handover to Support Team"]['pending']++;
            $implementationStatus["Handover to Support Team"]['companies']['pending'][] = $companyInfo;
        }
    } else {
        // Not eligible yet - all steps not completed
        $implementationStatus["Handover to Support Team"]['pending']++;
        $implementationStatus["Handover to Support Team"]['companies']['pending'][] = $companyInfo;
    }
}

// Custom Group Stats
$customGroupStats = null;
if (isset($_GET['customGroupTypes']) && is_array($_GET['customGroupTypes'])) {
    $selectedTypes = $_GET['customGroupTypes'];
    $customGroupStats = ['running' => 0, 'pending' => 0, 'finished' => 0];
    
    foreach ($allCompanies as $companyId => $company) {
        $companyId = (int)$companyId;
        $companyGroupStatus = [];
        
        $doneByPidMid = isset($allTrainingStatuses[$companyId]) ? $allTrainingStatuses[$companyId] : [];
        
        foreach ($selectedTypes as $participantName) {
            $pid = isset($participantIdByName[$participantName]) ? (int)$participantIdByName[$participantName] : 0;
            if ($pid <= 0) { $companyGroupStatus[$participantName] = 'pending'; continue; }
            $mods = isset($participantModules[$pid]) ? $participantModules[$pid] : [];
            $totalModulesForPid = count($mods);
            if ($totalModulesForPid === 0) { $companyGroupStatus[$participantName] = 'pending'; continue; }
            $completedCount = 0;
            foreach ($mods as $mid) {
                if (isset($doneByPidMid[$pid]) && isset($doneByPidMid[$pid][$mid]) && in_array($doneByPidMid[$pid][$mid], [1,2,3])) {
                    $completedCount++;
                }
            }
            if ($completedCount === 0) {
                $companyGroupStatus[$participantName] = 'pending';
            } elseif ($completedCount < $totalModulesForPid) {
                $companyGroupStatus[$participantName] = 'running';
            } else {
                $companyGroupStatus[$participantName] = 'finished';
            }
        }
        
        $statuses = array_values($companyGroupStatus);
        $uniqueStatuses = array_unique($statuses);
        if (count($uniqueStatuses) === 1) {
            $status = $uniqueStatuses[0];
            $customGroupStats[$status]++;
        } else {
            $customGroupStats['running']++;
        }
    }
}

// Optimized missed timeline modules - use bulk query
$missedTimelineModules = [];
$modulesQuery = $d->selectRow(
    "tmm.training_module_id, tmm.training_module_name, tmm.completion_days, tmm.module_priority, tmm.training_module_order, tmt.participant_type AS participant_id",
    "training_module_master tmm LEFT JOIN training_module_topics tmt ON tmm.topic_id = tmt.topic_id AND tmt.topic_type = '0'",
    "tmm.module_type = 1 AND tmm.training_module_status = 0 AND tmm.completion_days > 0",
    "ORDER BY COALESCE(tmm.training_module_order, tmm.module_priority) ASC"
);

if ($modulesQuery && mysqli_num_rows($modulesQuery) > 0) {
    $modulesData = [];
    $moduleParticipantMap = [];
    while ($module = mysqli_fetch_assoc($modulesQuery)) {
        $moduleId = (int)$module['training_module_id'];
        $modulesData[$moduleId] = $module;
        $moduleParticipantMap[$moduleId] = isset($module['participant_id']) ? (int)$module['participant_id'] : 0;
    }
    
    if (!empty($modulesData) && !empty($allCompanies)) {
        $overdueCounts = [];
        foreach ($modulesData as $moduleId => $module) {
            $overdueCount = 0;
            $completionDays = intval($module['completion_days']);
            $assignedPid = $moduleParticipantMap[$moduleId];
            
            if ($completionDays > 0 && $assignedPid > 0) {
                // Check each company for this module
                foreach ($allCompanies as $cid => $comp) {
                    $cid = (int)$cid;
                    $status = isset($allTrainingStatuses[$cid][$assignedPid][$moduleId]) ? $allTrainingStatuses[$cid][$assignedPid][$moduleId] : null;
                    if (!in_array($status, [1,2,3])) {
                        $dueInfo = calculateModuleDueDateOptimized($cid, $assignedPid, $moduleId, $comp['created_date'], $allModulesOrdered, $moduleIndexMap, $allAggregateDates);
                        if (!empty($dueInfo['is_overdue'])) {
                            $overdueCount++;
                        }
                    }
                }
            }
            $overdueCounts[$moduleId] = $overdueCount;
        }
        
        foreach ($modulesData as $moduleId => $module) {
            $missedTimelineModules[] = [
                'module_id' => $moduleId,
                'module_name' => $module['training_module_name'],
                'completion_days' => intval($module['completion_days']),
                'module_priority' => intval($module['module_priority']),
                'overdue_count' => isset($overdueCounts[$moduleId]) ? $overdueCounts[$moduleId] : 0
            ];
        }
    }
}

usort($missedTimelineModules, function($a, $b) {
    if ($a['overdue_count'] == $b['overdue_count']) {
        return $a['module_priority'] - $b['module_priority'];
    }
    return $b['overdue_count'] - $a['overdue_count'];
});

// Optimized company overdues - reuse module data
$companyOverdues = [];
// Build modules array with names and participant IDs from pre-fetched data
$modules = [];
$moduleNameMap = [];
$moduleParticipantIdMap = [];
// Fetch module names and participant IDs in one query
$modulesWithDetailsQuery = $d->selectRow(
    "tmm.training_module_id, tmm.training_module_name, tmm.completion_days, tmt.participant_type AS participant_id",
    "training_module_master tmm LEFT JOIN training_module_topics tmt ON tmm.topic_id = tmt.topic_id AND tmt.topic_type = '0'",
    "tmm.module_type = 1 AND tmm.training_module_status = 0 AND tmm.completion_days > 0",
    "ORDER BY COALESCE(tmm.training_module_order, tmm.module_priority) ASC"
);
if ($modulesWithDetailsQuery && mysqli_num_rows($modulesWithDetailsQuery) > 0) {
    while ($m = mysqli_fetch_assoc($modulesWithDetailsQuery)) {
        $modules[] = $m;
        $mid = (int)$m['training_module_id'];
        $moduleNameMap[$mid] = $m['training_module_name'];
        $moduleParticipantIdMap[$mid] = isset($m['participant_id']) ? (int)$m['participant_id'] : 0;
    }
}

// City names already fetched above, no need to fetch again

foreach ($allCompanies as $companyId => $company) {
    $companyId = (int)$companyId;
    $overdueModules = [];
    $companyCreatedDate = $company['created_date'];
    $cityId = (int)$company['city_id'];
    
    $statusByMid = [];
    if (isset($allTrainingStatuses[$companyId])) {
        foreach ($allTrainingStatuses[$companyId] as $pid => $mods) {
            foreach ($mods as $mid => $status) {
                $statusByMid[$mid] = $status;
            }
        }
    }
    
    $dueCache = [];
    foreach ($modules as $mod) {
        $moduleId = intval($mod['training_module_id']);
        $moduleName = $mod['training_module_name'];
        $completionDays = intval($mod['completion_days']);
        $assignedPid = isset($mod['participant_id']) ? intval($mod['participant_id']) : 0;
        if ($completionDays <= 0 || $assignedPid <= 0) continue;
        
        $pStatus = isset($statusByMid[$moduleId]) ? intval($statusByMid[$moduleId]) : null;
        if ($pStatus === 1 || $pStatus === 2 || $pStatus === 3) { continue; }
        
        $key = $assignedPid.'_'.$moduleId;
        if (!isset($dueCache[$key])) {
            $dueCache[$key] = calculateModuleDueDateOptimized($companyId, $assignedPid, $moduleId, $companyCreatedDate, $allModulesOrdered, $moduleIndexMap, $allAggregateDates);
        }
        $dueInfo = $dueCache[$key];
        if (!empty($dueInfo['is_overdue'])) {
            $days = isset($dueInfo['days_overdue']) ? intval($dueInfo['days_overdue']) : 0;
            $overdueModules[] = [
                'module_id' => $moduleId,
                'module_name' => $moduleName,
                'days_overdue' => $days
            ];
        }
    }
    
    if (!empty($overdueModules)) {
        usort($overdueModules, function($a, $b){ return $b['days_overdue'] - $a['days_overdue']; });
        $companyOverdues[] = [
            'company_id' => $companyId,
            'company_name' => $company['society_name'],
            'city_name' => $cityNameMap[$cityId] ?? '',
            'overdue_count' => count($overdueModules),
            'modules' => $overdueModules
        ];
    }
}

usort($companyOverdues, function($a, $b){
    if ($a['overdue_count'] == $b['overdue_count']) {
        return strcmp($a['company_name'], $b['company_name']);
    }
    return $b['overdue_count'] - $a['overdue_count'];
});

// Optimized employee overdues
$employeeOverdues = [];
foreach ($allCompanies as $companyId => $company) {
    $companyId = (int)$companyId;
    $overdueModules = [];
    $companyCreatedDate = $company['created_date'];
    $cityId = (int)$company['city_id'];
    $employeeName = trim((string)($company['implementation_name'] ?? ''));
    if ($employeeName === '') { $employeeName = 'Unassigned'; }
    
    $statusByMid = [];
    if (isset($allTrainingStatuses[$companyId])) {
        foreach ($allTrainingStatuses[$companyId] as $pid => $mods) {
            foreach ($mods as $mid => $status) {
                $statusByMid[$mid] = $status;
            }
        }
    }
    
    $dueCache = [];
    foreach ($modules as $mod) {
        $moduleId = intval($mod['training_module_id']);
        $completionDays = intval($mod['completion_days']);
        $assignedPid = isset($mod['participant_id']) ? intval($mod['participant_id']) : 0;
        if ($completionDays <= 0 || $assignedPid <= 0) continue;
        
        $pStatus = isset($statusByMid[$moduleId]) ? intval($statusByMid[$moduleId]) : null;
        if ($pStatus === 1 || $pStatus === 2 || $pStatus === 3) { continue; }
        
        $key = $assignedPid.'_'.$moduleId;
        if (!isset($dueCache[$key])) {
            $dueCache[$key] = calculateModuleDueDateOptimized($companyId, $assignedPid, $moduleId, $companyCreatedDate, $allModulesOrdered, $moduleIndexMap, $allAggregateDates);
        }
        $dueInfo = $dueCache[$key];
        if (!empty($dueInfo['is_overdue'])) {
            $days = isset($dueInfo['days_overdue']) ? intval($dueInfo['days_overdue']) : 0;
            $overdueModules[] = [
                'module_id' => $moduleId,
                'days_overdue' => $days
            ];
        }
    }
    
    if (!empty($overdueModules)) {
        if (!isset($employeeOverdues[$employeeName])) {
            $employeeOverdues[$employeeName] = [
                'employee_name' => $employeeName,
                'overdue_count' => 0,
                'companies' => []
            ];
        }
        $employeeOverdues[$employeeName]['overdue_count']++;
        $employeeOverdues[$employeeName]['companies'][] = [
            'company_id' => $companyId,
            'company_name' => $company['society_name'],
            'city_name' => $cityNameMap[$cityId] ?? '',
            'overdue_modules' => count($overdueModules)
        ];
    }
}

$employeeOverduesResult = array_values($employeeOverdues);
usort($employeeOverduesResult, function($a, $b){
    if ($a['overdue_count'] == $b['overdue_count']) {
        return strcmp($a['employee_name'], $b['employee_name']);
    }
    return $b['overdue_count'] - $a['overdue_count'];
});

// Not Responding Companies
$notRespondingCount = $d->count_data_direct("society_id", "society_master", "created_on_society_server = 1 AND IFNULL(is_not_responding,0) = 1" . $riseFilterCondition . $expiryFilterCondition . (!empty($stateCityFilter) ? $stateCityFilter : "") . (!empty($companyFilter) ? $companyFilter : ""));

$notRespondingCompanies = [];
$notRespondingQuery = $d->selectRow(
    "society_id, society_name, city_id, created_date, sales_closure_date, implementation_name, is_welcome_email_send, is_whatsapp_group_created, setup_training_status, training_status",
    "society_master",
    "created_on_society_server = 1 AND IFNULL(is_not_responding,0) = 1" . $riseFilterCondition . $expiryFilterCondition . (!empty($stateCityFilter) ? $stateCityFilter : "") . (!empty($companyFilter) ? $companyFilter : ""),
    "ORDER BY society_id DESC"
);

while ($nrCompany = mysqli_fetch_assoc($notRespondingQuery)) {
    $cityId = (int)$nrCompany['city_id'];
    $notRespondingCompanies[] = [
        'company_id' => (int)$nrCompany['society_id'],
        'company_name' => $nrCompany['society_name'],
        'city_name' => $cityNameMap[$cityId] ?? '',
        'created_date' => $nrCompany['created_date'] ?? '',
        'sales_closure_date' => $nrCompany['sales_closure_date'] ?? '',
        'implementation_name' => $nrCompany['implementation_name'] ?? 'Unassigned',
        'welcome_email_sent' => (int)($nrCompany['is_welcome_email_send'] ?? 0) === 1,
        'whatsapp_created' => (int)($nrCompany['is_whatsapp_group_created'] ?? 0) === 1,
        'setup_status' => (int)($nrCompany['setup_training_status'] ?? 0),
        'training_status' => (int)($nrCompany['training_status'] ?? 0)
    ];
}

echo json_encode([
    'totalSetup' => $totalSetup,
    'completedSetup' => $completedSetup,
    'pendingSetupSessions' => $pendingSetupSessions,
    'totalDoneCompanies' => $totalDoneCompanies,
    'totalPendingCompanies' => $totalPendingCompanies,
    'totalMeetings' => $totalMeetings,
    'completedMeetings' => $completedMeetings,
    'pendingMeetings' => $pendingMeetings,
    'totalCompanies' => $totalCompanies,
    'productImplementationDone' => $productImplementationDone,
    'productImplementationPending' => $productImplementationPending,
    'productImplementationRunning' => $productImplementationRunning,
    'sessionStats' => $sessionStats,
    'requiredModuleCounts' => [
        'perParticipant' => $participantRequiredCounts,
        'allCompanies' => $allCompaniesRequiredTotal
    ],
    'setupSessionStats' => $setupSessionStats,
    'customGroupStats' => $customGroupStats,
    'keyAccounts' => $keyAccounts,
    'keyAccountsDone' => $keyAccountsDone,
    'keyAccountsPending' => $keyAccountsPending,
    'notCrm' => $notCrm,
    'crm' => $crm,
    'implementationStatus' => $implementationStatus,
    'missedTimelineModules' => $missedTimelineModules,
    'companyOverdues' => $companyOverdues,
    'employeeOverdues' => $employeeOverduesResult,
    'notRespondingCount' => $notRespondingCount,
    'notRespondingCompanies' => $notRespondingCompanies,
]);
