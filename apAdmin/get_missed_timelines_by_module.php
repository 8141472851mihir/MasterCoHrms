<?php

include_once 'common/object.php';
include_once 'timeline_functions.php';
$countryId = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId']) : 0;
$sId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
$cId = isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : 0;
$moduleFilterWhere = [];
$moduleFilterWhere[] = "sm.created_on_society_server = 1";
if (!empty($countryId)) {
    $moduleFilterWhere[] = "sm.country_id = '" . $countryId . "'";
}
if (!empty($sId)) {
    $moduleFilterWhere[] = "sm.state_id = '" . $sId . "'";
}
if (!empty($cId)) {
    $moduleFilterWhere[] = "sm.city_id = '" . $cId . "'";
}
$moduleWhereSql = implode(' AND ', $moduleFilterWhere);
$moduleId = isset($_GET['module_id']) ? $d->sanitizeReportFilterIdAsInt($_GET['module_id']) : 0;
if ($moduleId > 0) {
    $mRes = $d->selectRow("completion_days, training_module_name, module_priority", "training_module_master", "training_module_id = '$moduleId' AND module_type = 1 AND IFNULL(training_module_status,0)=0");
    if ($mRes && mysqli_num_rows($mRes) > 0) {
        $mRow = mysqli_fetch_assoc($mRes);
        $mdays = intval($mRow['completion_days']);
        $modulePriority = intval($mRow['module_priority']);
        
        if ($mdays <= 0) {
            echo '<tr><td colspan="5" class="text-center text-muted">This module has no completion days set.</td></tr>';
            exit;
        }
        
        $companiesQuery = $d->selectRow(
            "sm.society_id, sm.society_name, sm.created_date, sm.city_name, sm.secretary_mobile",
            "society_master sm",
            $moduleWhereSql
        );

        // Resolve assigned participant once (module-constant)
        $mpRes = $d->selectRow("tmt.participant_type AS pid", "training_module_master tmm LEFT JOIN training_module_topics tmt ON tmm.topic_id = tmt.topic_id AND tmt.topic_type = '0'", "tmm.training_module_id = '$moduleId'", "LIMIT 1");
        $assignedPid = 0;
        if ($mpRes && mysqli_num_rows($mpRes) > 0) {
            $assignedPid = intval(mysqli_fetch_assoc($mpRes)['pid']);
        }

        $overdueCompanies = [];
        $companies = [];
        $companyIds = [];
        while ($company = mysqli_fetch_assoc($companiesQuery)) {
            $companies[] = $company;
            $companyIds[] = (int)$company['society_id'];
        }

        // Batch status lookup for all companies
        $statusByCompany = [];
        $idsIn = !empty($companyIds) ? implode(',', array_map('intval', $companyIds)) : '0';
        if ($assignedPid > 0 && !empty($companyIds)) {
            $stQ = $d->selectRow(
                "company_id, training_status, training_date",
                "batch_training_status_master",
                "company_id IN ($idsIn) AND participant_id='$assignedPid' AND module_id='$moduleId'"
            );
            while ($stR = mysqli_fetch_assoc($stQ)) {
                $cid = (int)$stR['company_id'];
                // First row wins (matches LIMIT 1 semantics)
                if (!isset($statusByCompany[$cid])) {
                    $statusByCompany[$cid] = $stR;
                }
            }
        }

        // Prefetch module order + aggregate completion dates once (avoids per-company helper SQL)
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
            
            if ($assignedPid > 0) {
                // Check module status for the assigned participant (from map)
                $pStatus = null;
                if (isset($statusByCompany[$companyId])) {
                    $stR = $statusByCompany[$companyId];
                    $pStatus = isset($stR['training_status']) ? (int)$stR['training_status'] : null;
                }

                // Skip if Not Applicable (2) or Later On (3)
                if ($pStatus === 2 || $pStatus === 3) {
                    continue;
                }

                // Compute due date from prefetched maps (no per-company SQL)
                $dueDateInfo = calculateModuleDueDateFromMaps(
                    $companyId,
                    $moduleId,
                    $companyCreatedDate,
                    $allModulesOrdered,
                    $moduleIndexMap,
                    $allAggregateDates
                );

                // If Completed (1), include only if it was completed late
                if ($pStatus === 1) {
                    $completedAgg = $allAggregateDates[$companyId][$moduleId] ?? null;
                    if ($completedAgg && !empty($dueDateInfo['due_date']) && $completedAgg > $dueDateInfo['due_date']) {
                        $overdueCompanies[] = [
                            'society_name' => $company['society_name'],
                            'city_name' => $company['city_name'],
                            'created_date' => $company['created_date'],
                            'due_date' => $dueDateInfo['due_date'],
                            'days_over' => max(0, (int)((strtotime($completedAgg) - strtotime($dueDateInfo['due_date'])) / (60 * 60 * 24))),
                            'contact_mobile' => $company['secretary_mobile']
                        ];
                    }
                    continue;
                }

                // Otherwise, it's pending; include only if due date already passed
                if (!empty($dueDateInfo['is_overdue'])) {
                    $overdueCompanies[] = [
                        'society_name' => $company['society_name'],
                        'city_name' => $company['city_name'],
                        'created_date' => $company['created_date'],
                        'due_date' => $dueDateInfo['due_date'],
                        'days_over' => intval($dueDateInfo['days_overdue']),
                        'contact_mobile' => $company['secretary_mobile']
                    ];
                }
            }
        }
        
        usort($overdueCompanies, function($a, $b) {
            return $b['days_over'] - $a['days_over'];
        });
        
        if (!empty($overdueCompanies)) {
            foreach ($overdueCompanies as $r) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($r['society_name']) . '</td>';
                echo '<td>' . htmlspecialchars($r['city_name'] ?? '') . '</td>';
                echo '<td>' . ($r['created_date'] ? date('d-M-Y', strtotime($r['created_date'])) : 'N/A') . '</td>';
                echo '<td>' . ($r['due_date'] ? date('d-M-Y', strtotime($r['due_date'])) : 'N/A') . '</td>';
                echo '<td class="text-danger font-weight-bold">' . $r['days_over'] . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5" class="text-center text-muted">No companies have missed this timeline.</td></tr>';
        }
    } else {
        echo '<tr><td colspan="5" class="text-center text-muted">Invalid module.</td></tr>';
    }
    exit;
}