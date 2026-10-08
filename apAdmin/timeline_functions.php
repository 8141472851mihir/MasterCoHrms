<?php
/**
 * In-memory due-date calc using prefetched module order + aggregate completion maps.
 * Mirrors calculateModuleDueDate() without per-company SQL.
 */
function calculateModuleDueDateFromMaps($companyId, $moduleId, $companyCreatedDate, $allModulesOrdered, $moduleIndexMap, $allAggregateDates)
{
    $moduleIndex = isset($moduleIndexMap[(int)$moduleId]) ? $moduleIndexMap[(int)$moduleId] : -1;
    if ($moduleIndex === -1) {
        return ['due_date' => null, 'is_overdue' => false, 'days_overdue' => 0];
    }
    $module = $allModulesOrdered[$moduleIndex];
    $completionDays = intval($module['completion_days']);
    if ($completionDays <= 0) {
        return ['due_date' => null, 'is_overdue' => false, 'days_overdue' => 0];
    }
    $baseDate = $companyCreatedDate ? substr($companyCreatedDate, 0, 10) : null;
    if ($moduleIndex > 0) {
        $previousModuleId = (int)$allModulesOrdered[$moduleIndex - 1]['training_module_id'];
        if (isset($allAggregateDates[(int)$companyId][$previousModuleId])) {
            $baseDate = $allAggregateDates[(int)$companyId][$previousModuleId];
        } else {
            return ['due_date' => null, 'is_overdue' => false, 'days_overdue' => 0];
        }
    }
    if (!$baseDate) {
        return ['due_date' => null, 'is_overdue' => false, 'days_overdue' => 0];
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

function getModuleOverdueCount($d, $moduleId, $stateCityFilter = '')
{
    $overdueCount = 0;
    // Fetch completion days and the assigned participant for this module (via topic)
    $moduleQuery = $d->selectRow(
        "tmm.completion_days, tmt.participant_type AS participant_id",
        "training_module_master tmm LEFT JOIN training_module_topics tmt ON tmm.topic_id = tmt.topic_id AND tmt.topic_type = '0'",
        "tmm.training_module_id = '$moduleId' AND tmm.module_type = 1 AND tmm.training_module_status = 0"
    );
    if (!$moduleQuery || mysqli_num_rows($moduleQuery) == 0) {
        return 0;
    }
    $moduleData = mysqli_fetch_assoc($moduleQuery);
    $completionDays = intval($moduleData['completion_days']);
    $assignedPid = isset($moduleData['participant_id']) ? intval($moduleData['participant_id']) : 0;
    if ($completionDays <= 0 || $assignedPid <= 0) {
        return 0;
    }

    $companiesQuery = $d->selectRow(
        "society_id, created_date",
        "society_master",
        "created_on_society_server = 1" . (!empty($stateCityFilter) ? $stateCityFilter : "")
    );
    $companies = [];
    $companyIds = [];
    while ($company = mysqli_fetch_assoc($companiesQuery)) {
        $companies[] = $company;
        $companyIds[] = (int)$company['society_id'];
    }

    // Batch status lookup for assigned participant + module
    $statusByCompany = [];
    $idsIn = !empty($companyIds) ? implode(',', array_map('intval', $companyIds)) : '0';
    if (!empty($companyIds)) {
        $stQ = $d->selectRow(
            "company_id, training_status",
            "batch_training_status_master",
            "company_id IN ($idsIn) AND participant_id='$assignedPid' AND module_id='$moduleId'"
        );
        while ($stR = mysqli_fetch_assoc($stQ)) {
            $cid = (int)$stR['company_id'];
            if (!isset($statusByCompany[$cid])) {
                $statusByCompany[$cid] = isset($stR['training_status']) ? (int)$stR['training_status'] : null;
            }
        }
    }

    // Prefetch module order + aggregate completion dates once
    $allModulesOrdered = [];
    $moduleIndexMap = [];
    $allModQ = $d->selectRow(
        "training_module_id, module_priority, completion_days, training_module_order",
        "training_module_master",
        "module_type = 1 AND training_module_status = 0",
        "ORDER BY COALESCE(training_module_order, module_priority) ASC"
    );
    while ($row = mysqli_fetch_assoc($allModQ)) {
        $moduleIndexMap[(int)$row['training_module_id']] = count($allModulesOrdered);
        $allModulesOrdered[] = $row;
    }
    $activeParticipantCount = 0;
    $activeCntQ = $d->selectRow("COUNT(*) AS total", "training_participants_type", "status = 0");
    if ($activeCntQ && mysqli_num_rows($activeCntQ) > 0) {
        $activeParticipantCount = (int)mysqli_fetch_assoc($activeCntQ)['total'];
    }
    $allAggregateDates = [];
    if (!empty($companyIds) && $activeParticipantCount > 0 && !empty($allModulesOrdered)) {
        $moduleIdsAll = array_map(function ($m) { return (int)$m['training_module_id']; }, $allModulesOrdered);
        $moduleIdsStr = implode(',', $moduleIdsAll);
        $aggQuery = $d->selectRow(
            "company_id, module_id, COUNT(DISTINCT participant_id) AS done_cnt, MAX(training_date) AS latest_date",
            "batch_training_status_master",
            "company_id IN ($idsIn) AND module_id IN ($moduleIdsStr) AND training_status IN (1,2,3)",
            "GROUP BY company_id, module_id"
        );
        while ($agg = mysqli_fetch_assoc($aggQuery)) {
            if ((int)$agg['done_cnt'] >= $activeParticipantCount && !empty($agg['latest_date'])) {
                $allAggregateDates[(int)$agg['company_id']][(int)$agg['module_id']] = substr($agg['latest_date'], 0, 10);
            }
        }
    }

    foreach ($companies as $company) {
        $companyId = (int)$company['society_id'];
        $companyCreatedDate = $company['created_date'];

        // Check this module's status for its assigned participant only (from map)
        $pStatus = $statusByCompany[$companyId] ?? null;

        // Completed(1) / Not Applicable(2) / Later On(3) are not overdue
        if ($pStatus === 1 || $pStatus === 2 || $pStatus === 3) {
            continue;
        }

        $dueDateInfo = calculateModuleDueDateFromMaps(
            $companyId,
            $moduleId,
            $companyCreatedDate,
            $allModulesOrdered,
            $moduleIndexMap,
            $allAggregateDates
        );
        if (!empty($dueDateInfo['due_date']) && !empty($dueDateInfo['is_overdue'])) {
            $overdueCount++;
        }
    }

    return $overdueCount;
}
function getModuleCompletionDates($d, $companyId, $participantId)
{
    $completionDates = [];
    $query = $d->selectRow(
        "btsm.module_id, btsm.training_date",
        "batch_training_status_master btsm 
         INNER JOIN training_module_master tmm ON btsm.module_id = tmm.training_module_id",
        "btsm.company_id = '$companyId' 
         AND btsm.participant_id = '$participantId' 
         AND btsm.training_status = 1 
         AND tmm.module_type = 1 
         AND tmm.training_module_status = 0",
        "ORDER BY tmm.module_priority ASC, btsm.training_date ASC"
    );
    while ($row = mysqli_fetch_assoc($query)) {
        $completionDates[$row['module_id']] = $row['training_date'];
    }
    return $completionDates;
}

function getModuleLaterOnStatus($d, $companyId, $participantId)
{
    $laterOnModules = [];
    $query = $d->selectRow(
        "btsm.module_id",
        "batch_training_status_master btsm 
         INNER JOIN training_module_master tmm ON btsm.module_id = tmm.training_module_id",
        "btsm.company_id = '$companyId' 
         AND btsm.participant_id = '$participantId' 
         AND btsm.training_status = 3 
         AND tmm.module_type = 1 
         AND tmm.training_module_status = 0"
    );
    while ($row = mysqli_fetch_assoc($query)) {
        $laterOnModules[] = $row['module_id'];
    }
    return $laterOnModules;
}

function getModuleNotApplicableStatus($d, $companyId, $participantId)
{
    $notApplicableModules = [];
    $query = $d->selectRow(
        "btsm.module_id",
        "batch_training_status_master btsm 
         INNER JOIN training_module_master tmm ON btsm.module_id = tmm.training_module_id",
        "btsm.company_id = '$companyId' 
         AND btsm.participant_id = '$participantId' 
         AND btsm.training_status = 2 
         AND tmm.module_type = 1 
         AND tmm.training_module_status = 0"
    );
    while ($row = mysqli_fetch_assoc($query)) {
        $notApplicableModules[] = $row['module_id'];
    }
    return $notApplicableModules;
}

function getAggregateModuleDoneDate($d, $companyId, $moduleId)
{
    // Base date should be available only when ALL active participants are Completed/NA/Later for this module
    // 1) Count active participants
    $activeQ = $d->selectRow("COUNT(*) AS total", "training_participants_type", "status = 0");
    $activeCnt = 0;
    if ($activeQ && mysqli_num_rows($activeQ) > 0) {
        $r = mysqli_fetch_assoc($activeQ);
        $activeCnt = (int)$r['total'];
    }
    if ($activeCnt === 0) {
        return null;
    }

    // 2) Among those participants, count how many have status in (1,2,3) for this module and get MAX date
    $doneQ = $d->selectRow(
        "COUNT(DISTINCT participant_id) AS done_cnt, MAX(training_date) AS latest_date",
        "batch_training_status_master",
        "company_id='" . $companyId . "' AND module_id='" . $moduleId . "' AND training_status IN (1,2,3)"
    );
    if (!$doneQ || mysqli_num_rows($doneQ) === 0) {
        return null;
    }
    $doneRow = mysqli_fetch_assoc($doneQ);
    $doneCnt = (int)$doneRow['done_cnt'];
    $latest = isset($doneRow['latest_date']) ? $doneRow['latest_date'] : null;

    if ($doneCnt < $activeCnt) {
        return null;
    }
    if (empty($latest)) {
        return null;
    }
    return substr($latest, 0, 10);
}

// Returns the date when a specific participant completed/NA/Later a given module
function getParticipantModuleDoneDate($d, $companyId, $participantId, $moduleId)
{
    $q = $d->selectRow(
        "MAX(training_date) AS latest_date",
        "batch_training_status_master",
        "company_id='" . $companyId . "' AND module_id='" . $moduleId . "' AND participant_id='" . $participantId . "' AND training_status IN (1,2,3)",
        "LIMIT 1"
    );
    if (!$q || mysqli_num_rows($q) === 0) {
        return null;
    }
    $row = mysqli_fetch_assoc($q);
    $latest = isset($row['latest_date']) ? $row['latest_date'] : null;
    return $latest ? substr($latest, 0, 10) : null;
}

function getPreviousModuleId($d, $moduleId)
{
    $q = $d->selectRow(
        "training_module_id",
        "training_module_master",
        "module_type = 1 AND training_module_status = 0",
        "ORDER BY COALESCE(training_module_order, module_priority) ASC"
    );
    if (!$q) {
        return null;
    }
    $ids = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $ids[] = (int)$r['training_module_id'];
    }
    $idx = array_search((int)$moduleId, $ids, true);
    if ($idx === false || $idx === 0) {
        return null;
    }
    return $ids[$idx - 1];
}

function isModuleCompletedLate($d, $companyId, $moduleId, $companyCreatedDate)
{
    $modQ = $d->selectRow("completion_days", "training_module_master", "training_module_id='" . $moduleId . "' AND module_type=1 AND training_module_status=0", "LIMIT 1");
    if (!$modQ || mysqli_num_rows($modQ) === 0) {
        return ['completed_late' => false, 'due_date' => null, 'completed_date' => null];
    }
    $modRow = mysqli_fetch_assoc($modQ);
    $cdays = isset($modRow['completion_days']) ? (int)$modRow['completion_days'] : 0;
    if ($cdays <= 0) {
        return ['completed_late' => false, 'due_date' => null, 'completed_date' => null];
    }
    // Check if module is fully done by all participants
    $activeQ = $d->selectRow("COUNT(*) AS total", "training_participants_type", "status=0");
    $activeCnt = 0;
    if ($activeQ && mysqli_num_rows($activeQ) > 0) {
        $activeCnt = (int)mysqli_fetch_assoc($activeQ)['total'];
    }
    if ($activeCnt === 0) {
        return ['completed_late' => false, 'due_date' => null, 'completed_date' => null];
    }
    $doneQ = $d->selectRow("COUNT(DISTINCT participant_id) AS done_cnt, MAX(training_date) AS latest_date", "batch_training_status_master", "company_id='" . $companyId . "' AND module_id='" . $moduleId . "' AND training_status IN (1,2,3)");
    if (!$doneQ || mysqli_num_rows($doneQ) === 0) {
        return ['completed_late' => false, 'due_date' => null, 'completed_date' => null];
    }
    $doneRow = mysqli_fetch_assoc($doneQ);
    if ((int)$doneRow['done_cnt'] < $activeCnt) {
        return ['completed_late' => false, 'due_date' => null, 'completed_date' => null];
    }
    $completedAgg = substr($doneRow['latest_date'], 0, 10);
    // Base date is previous module aggregate date (all participants) or company created date for first module
    $prevId = getPreviousModuleId($d, (int)$moduleId);
    $baseDate = $companyCreatedDate ? substr($companyCreatedDate, 0, 10) : null;
    if ($prevId !== null) {
        $aggPrev = getAggregateModuleDoneDate($d, $companyId, $prevId);
        if (!$aggPrev) {
            return ['completed_late' => false, 'due_date' => null, 'completed_date' => null];
        }
        $baseDate = $aggPrev;
    }
    if (!$baseDate || !$completedAgg) {
        return ['completed_late' => false, 'due_date' => null, 'completed_date' => null];
    }
    $due = date('Y-m-d', strtotime($baseDate . ' +' . $cdays . ' days'));
    $completedLate = ($completedAgg > $due);
    return ['completed_late' => $completedLate, 'due_date' => $due, 'completed_date' => $completedAgg];
}

function hasAnyProductTrainingRecord($d, $companyId, $participantId)
{
    $q = $d->selectRow(
        "COUNT(*) AS cnt",
        "batch_training_status_master btsm INNER JOIN training_module_master tmm ON btsm.module_id = tmm.training_module_id",
        "btsm.company_id = '" . $companyId . "' AND btsm.participant_id = '" . $participantId . "' AND tmm.module_type = 1 AND tmm.training_module_status = 0"
    );
    if ($q && mysqli_num_rows($q) > 0) {
        $r = mysqli_fetch_assoc($q);
        return intval($r['cnt']) > 0;
    }
    return false;
}

function calculateModuleDueDate($d, $companyId, $participantId, $moduleId, $companyCreatedDate)
{
    $moduleQuery = $d->selectRow(
        "completion_days, module_priority",
        "training_module_master",
        "training_module_id = '$moduleId' AND module_type = 1 AND training_module_status = 0"
    );
    if (!$moduleQuery || mysqli_num_rows($moduleQuery) == 0) {
        return ['due_date' => null, 'is_overdue' => false];
    }
    $moduleData = mysqli_fetch_assoc($moduleQuery);
    $completionDays = intval($moduleData['completion_days']);
    if ($completionDays <= 0) {
        return ['due_date' => null, 'is_overdue' => false];
    }
    $allModulesQuery = $d->selectRow(
        "training_module_id, module_priority, completion_days, training_module_order",
        "training_module_master",
        "module_type = 1 AND training_module_status = 0",
        "ORDER BY COALESCE(training_module_order, module_priority) ASC"
    );
    $allModules = [];
    while ($row = mysqli_fetch_assoc($allModulesQuery)) {
        $allModules[] = $row;
    }
    $currentModuleIndex = -1;
    foreach ($allModules as $index => $module) {
        if ($module['training_module_id'] == $moduleId) {
            $currentModuleIndex = $index;
            break;
        }
    }
    if ($currentModuleIndex === -1) {
        return ['due_date' => null, 'is_overdue' => false];
    }
    $baseDate = $companyCreatedDate;
    if ($currentModuleIndex > 0) {
        $previousModule = $allModules[$currentModuleIndex - 1];
        $previousModuleId = $previousModule['training_module_id'];
        // Use aggregate completion date of the previous module across all active participants
        $aggPrevDone = getAggregateModuleDoneDate($d, $companyId, $previousModuleId);
        if (empty($aggPrevDone)) {
            return ['due_date' => null, 'is_overdue' => false];
        }
        $baseDate = $aggPrevDone;
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
