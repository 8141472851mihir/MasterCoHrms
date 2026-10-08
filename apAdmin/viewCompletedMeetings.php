<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
extract($_REQUEST);
    $query = $d->selectRow("sm.society_name,bam.admin_name,tbm.batch_name,bsm.date,tmm.training_module_name,bts.module_id,tps.training_participant_master_id,tps.participant_type,tps.participant_name,tps.participant_designation,tps.participant_contact,tps.no_of_participants,tps.remark,bts.training_status,bts.training_date,bts.module_remark,bts.company_id AS status_company_id,tam.society_id,tam.attend_type,tam.absent_present,tam.absent_reason,bsm.*, tmt.topic_name", " batch_slot_master as bsm
	LEFT JOIN training_attend_master AS tam ON tam.training_slot_id = bsm.slot_id
	LEFT JOIN society_master AS sm ON sm.society_id = tam.society_id
	LEFT JOIN bms_admin_master AS bam ON bam.admin_id = bsm.trainer_id
	LEFT JOIN training_batch_master AS tbm ON tbm.batch_id = bsm.batch_id
	LEFT JOIN batch_training_status AS bts ON bts.slot_id = bsm.slot_id AND bts.company_id=tam.society_id
	LEFT JOIN training_module_master AS tmm ON tmm.training_module_id = bts.module_id
    LEFT JOIN training_module_topics AS tmt ON tmt.topic_id = tmm.topic_id AND tmt.topic_type = '0'
	LEFT JOIN training_participant_master AS tps ON tps.slot_id = bsm.slot_id AND tps.company_id=tam.society_id", "bsm.slot_id = '$meeting_slot_id'");

$companies = [];

while ($row = mysqli_fetch_array($query)) {
    $companyId = $row['society_id'];
    $societyName = $row['society_name'];
    $module_id = $row['module_id'];
    $slotName = $row['slot_name'];
    $batchName = $row['batch_name'];
    $trainerName = $row['admin_name'];
    $reference_company_ids = $row['reference_company_ids'];
    $date = $row['date'];
    $fromTime = $row['from_time'];
    $toTime = $row['to_time'];
    $recordLink = $row['recording_link'];
    $trainerPass = $row['training_password'];
    $momAttachment = $row['mom_attachment'];
    

    if (!isset($companies[$companyId])) {
        $companies[$companyId] = [
            'name' => $societyName,
            'absent_present' => $row['absent_present'],
            'absent_reason' => $row['absent_reason'],
            'modules' => [],
            'participants' => [],
            'reference_company_ids' => $reference_company_ids,
        ];
    }
    $companies[$companyId]['modules'][$module_id] = [
        'module_id' => $row['module_id'],
        'training_module_name' => $row['training_module_name'],
        'topic_name' => isset($row['topic_name']) && $row['topic_name'] !== '' ? $row['topic_name'] : 'Other',
        'training_status' => $row['training_status'],
        'training_date' => $row['training_date'],
        'module_remark' => $row['module_remark'],
        'slot_id' => $row['slot_id'],
        'slot_name' => $row['slot_name'],
        'batch_id' => $row['batch_id'],
        'from_time' => $row['from_time'],
        'to_time' => $row['to_time'],
        'trainer_id' => $row['trainer_id'],
        'recording_link' => $row['recording_link'],
        'training_password' => $row['training_password'],
        'meeting_status' => $row['meeting_status'],
        'reference_company_ids' => $row['reference_company_ids'],
    ];
    if (!empty($row['training_participant_master_id'])) {
        $training_participant_master_id = $row['training_participant_master_id'];
        if (!isset($companies[$companyId]['participants'][$training_participant_master_id])) {
            $companies[$companyId]['participants'][$training_participant_master_id] = [
                'participant_type' => $row['participant_type'],
                'participant_name' => $row['participant_name'],
                'participant_designation' => $row['participant_designation'],
                'participant_contact' => $row['participant_contact'],
                'no_of_participants' => ($row['no_of_participants'] != "0") ? $row['no_of_participants'] : "",
                'remark' => $row['remark'],
                'attend_type' => $row['attend_type'],
                'absent_present' => $row['absent_present'],
                'absent_reason' => $row['absent_reason'],
            ];
        }
    }
}

// Prefetch shared batch modules once (used when a company has no module statuses)
$sharedBatchModules = [];
$batchModulesPrefetch = $d->selectRow(
    "bmm.module_ids, tmm.training_module_id, tmm.training_module_name, tmt.topic_name",
    "batch_module_master bmm 
    LEFT JOIN training_module_master tmm ON FIND_IN_SET(tmm.training_module_id, bmm.module_ids)
    LEFT JOIN training_module_topics tmt ON tmt.topic_id = tmm.topic_id AND tmt.topic_type = '0'",
    "bmm.batch_id = '{$batch_id}' AND bmm.day_number = '{$meeting_day}'",
    "ORDER BY tmm.training_module_id"
);
if ($batchModulesPrefetch && mysqli_num_rows($batchModulesPrefetch) > 0) {
    while ($modRow = mysqli_fetch_assoc($batchModulesPrefetch)) {
        if (!empty($modRow['training_module_id'])) {
            $sharedBatchModules[$modRow['training_module_id']] = $modRow;
        }
    }
}

// Collect all module IDs across companies for batched subtopic/participant loads
$allModuleIds = [];
$allCompanyIds = array_map('intval', array_keys($companies));
foreach ($companies as $cid => &$cdata) {
    if (!isset($cdata['modules']) || empty($cdata['modules'])) {
        foreach ($sharedBatchModules as $modRow) {
            $cdata['modules'][$modRow['training_module_id']] = [
                'module_id' => $modRow['training_module_id'],
                'training_module_name' => $modRow['training_module_name'],
                'topic_name' => isset($modRow['topic_name']) && $modRow['topic_name'] !== '' ? $modRow['topic_name'] : 'Other',
                'training_status' => null,
                'training_date' => null,
                'module_remark' => null,
                'slot_id' => $slot_id,
                'slot_name' => $slotName,
                'batch_id' => $batch_id,
                'from_time' => $fromTime,
                'to_time' => $toTime,
                'trainer_id' => $trainer_id,
                'recording_link' => $recordLink,
                'training_password' => $trainerPass,
                'meeting_status' => null,
                'reference_company_ids' => $reference_company_ids,
            ];
        }
    }
    if (!empty($cdata['modules'])) {
        foreach (array_keys($cdata['modules']) as $mid) {
            if ($mid) {
                $allModuleIds[] = (int)$mid;
            }
        }
    }
}
unset($cdata);
$allModuleIds = array_values(array_unique($allModuleIds));
$allModuleIdsIn = !empty($allModuleIds) ? implode(',', $allModuleIds) : '0';
$allCompanyIdsIn = !empty($allCompanyIds) ? implode(',', $allCompanyIds) : '0';

// Prefetch all subtopics + completion for this slot across companies/modules
$subtopicsByModule = []; // module_id => [subtopic rows without completion]
$completionByCompanySubtopic = []; // company_id => [subtopic_id => is_completed]
if (!empty($allModuleIds)) {
    $subQ = $d->selectRow(
        "tms.training_module_id, tms.subtopic_id, tms.subtopic_name, tms.subtopic_description, tms.display_order",
        "training_module_subtopics tms",
        "tms.training_module_id IN ($allModuleIdsIn) AND tms.subtopic_status = 1",
        "ORDER BY tms.training_module_id ASC, tms.display_order ASC"
    );
    while ($srow = mysqli_fetch_assoc($subQ)) {
        $mid = (int)$srow['training_module_id'];
        if (!isset($subtopicsByModule[$mid])) {
            $subtopicsByModule[$mid] = [];
        }
        $subtopicsByModule[$mid][] = $srow;
    }

    if (!empty($allCompanyIds)) {
        $compQ = $d->selectRow(
            "company_id, subtopic_id, is_completed",
            "training_meeting_subtopics",
            "slot_id = '" . $meeting_slot_id . "' AND company_id IN ($allCompanyIdsIn)"
        );
        while ($crow = mysqli_fetch_assoc($compQ)) {
            $completionByCompanySubtopic[(int)$crow['company_id']][(int)$crow['subtopic_id']] =
                ($crow['is_completed'] === null ? null : (string)$crow['is_completed']);
        }
    }
}

// Prefetch present participants for all companies on this meeting date
$presentByCompanyModule = []; // company_id => [module_id => [participant_id => name]]
$meetingDateSql = !empty($date) ? date('Y-m-d', strtotime($date)) : '';
if (!empty($allCompanyIds) && !empty($allModuleIds) && $meetingDateSql !== '') {
    $ppQ = $d->selectRow(
        "DISTINCT btsm.company_id, btsm.module_id, btsm.participant_id, tpt.participant_name",
        "batch_training_status_master btsm INNER JOIN training_participants_type tpt ON tpt.participants_type_id = btsm.participant_id",
        "btsm.company_id IN ($allCompanyIdsIn) AND btsm.module_id IN ($allModuleIdsIn) AND DATE(btsm.training_date) = '$meetingDateSql'"
    );
    if ($ppQ && mysqli_num_rows($ppQ) > 0) {
        while ($pp = mysqli_fetch_assoc($ppQ)) {
            $presentByCompanyModule[(int)$pp['company_id']][(int)$pp['module_id']][(int)$pp['participant_id']] = $pp['participant_name'];
        }
    }
}

foreach ($companies as $cid => &$cdata) {
    if (!isset($cdata['modules']) || empty($cdata['modules'])) {
        continue;
    }
    $companyCompletions = $completionByCompanySubtopic[(int)$cid] ?? [];
    foreach ($cdata['modules'] as $modId => &$modData) {
        $subtopics = [];
        $moduleSubs = $subtopicsByModule[(int)$modId] ?? [];
        foreach ($moduleSubs as $srow) {
            $sid = (int)$srow['subtopic_id'];
            $subtopics[] = [
                'subtopic_id' => $sid,
                'subtopic_name' => $srow['subtopic_name'],
                'subtopic_description' => isset($srow['subtopic_description']) ? trim($srow['subtopic_description']) : '',
                'is_completed' => array_key_exists($sid, $companyCompletions) ? $companyCompletions[$sid] : null
            ];
        }
        $modData['subtopics'] = $subtopics;
    }
    // Only count participants present on this company's modules (match original per-company IN filter)
    $presentParticipants = [];
    $companyPresent = $presentByCompanyModule[(int)$cid] ?? [];
    foreach (array_keys($cdata['modules']) as $modId) {
        foreach ($companyPresent[(int)$modId] ?? [] as $pid => $pname) {
            $presentParticipants[(int)$pid] = $pname;
        }
    }
    $cdata['present_participants'] = $presentParticipants;
}
unset($cdata, $modData);
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Manage Training Meeting</h4>
            </div>
            <div class="col-sm-3 text-right">
                <?php
                $backUrl = 'manageTrainingSlots';
                $queryParams = [];
                if (isset($_GET['company_filter']) && $_GET['company_filter'] != '') {
                    $queryParams[] ='company_filter=' . urlencode($_GET['company_filter']);
                }
                if (!empty($queryParams)) {
                    $backUrl .= '?' . implode('&', $queryParams);
                }
                ?>
                <a href="<?php echo $backUrl; ?>" class="btn btn-primary btn-sm">
                    <i class="fa fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <p><span class="font-weight-bold">Meeting ID: </span><?= $slotName; ?></p>
                                <p><span class="font-weight-bold">Batch Name: </span><?= $batchName; ?></p>
                                <p><span class="font-weight-bold">Trainer Name: </span><?= $trainerName; ?></p>
                                <p class="card-text"><span class="font-weight-bold">Training Date: </span> <?= date('l, d F Y', strtotime($date)); ?></p>
                                <p class="card-text">
                                    <span class="font-weight-bold">Training Time: </span>
                                    <?= date("h:i A", strtotime($fromTime)); ?>
                                    <span class="font-weight-bold"> To </span>
                                    <?= date("h:i A", strtotime($toTime)); ?>
                                </p>
                                <?php if (!empty($cityName)) { ?>
                                    <p class="card-text"><span class="font-weight-bold">City: </span> <?= $cityName; ?> </p>
                                <?php } ?>
                                <p class="card-text"><span class="font-weight-bold">Recording Link: </span>
                                    <?php if (!empty($recordLink)): ?>
                                        <a href="<?= $recordLink ?>" target="_blank"><?= $recordLink ?></a>
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip"
                                            data-placement="top" title="Copy"
                                            onclick="copyToClipboard('<?= addslashes($recordLink) ?>')">
                                            <i class="fa fa-copy"></i></button>
                                    <?php else: ?>
                                        <span class="text-muted">No Link Available</span>
                                        <!--  -->
                                    <?php endif; ?>
                                </p>
                                <p class="card-text"><span class="font-weight-bold">Recording Link Password:</span>
                                    <?php if (!empty($trainerPass)): ?>

                                        <a href="<?= $trainerPass ?>" target="_blank"><?= $trainerPass ?></a>
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip"
                                            data-placement="top" title="Copy"
                                            onclick="copyToClipboard('<?= addslashes($trainerPass) ?>')">
                                            <i class="fa fa-copy"></i>
                                        </button>

                                    <?php else: ?>
                                        <span class="text-muted">No Password Available</span>
                                    <?php endif; ?>
                                </p>
                                <p class="card-text"><span class="font-weight-bold">MOM Attachment:</span>
                                    <?php if (!empty($momAttachment)): ?>
                                        <a href="../img/training_meetings/<?= htmlspecialchars($momAttachment) ?>" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fa fa-download"></i> View MOM
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No MOM Available</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if (empty($companies)) { ?>
            <h4 class="page-title">No Company Found</h4>
        <?php
        } else {
        ?>
            <div class="row pt-2 pb-2">
                <div class="col-sm-9">
                    <h4 class="page-title">Company List</h4>
                </div>
            </div>
            <?php
            foreach ($companies as $company_id => $company_data) {
                if (!empty($company_id)) {
                    $reference_company_ids = isset($company_data['reference_company_ids']) ? $company_data['reference_company_ids'] : '';
                    $reference_ids = $reference_company_ids ? explode(',', $reference_company_ids) : [];

                    $company_name_display = $company_data['name'];
                    if (in_array($company_id, $reference_ids)) {
                        $company_name_display .= ' (Reference)';
                    }
            ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-3">
                                <div class="card-header font-weight-bold">Company Name: <?= $company_name_display; ?></div>
                                <?php
                                if ($company_data['absent_present'] != '0') {
                                ?>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <?php if (!empty($company_data['present_participants'])) { ?>
                                                <p class="font-weight-bold text-primary mt-1">Present Participants</p>
                                                <div class="mb-3">
                                                    <?php
                                                    $names = array_values($company_data['present_participants']);
                                                    echo !empty($names) ? implode(', ', $names) : '<span class="text-muted">None</span>';
                                                    ?>
                                                </div>
                                            <?php } ?>
                                            <p class="font-weight-bold text-primary mt-1">Module List</p>
                                            <table class="table table-bordered mt-3">
                                                <thead>
                                                    <tr>
                                                        <th style="width:40%">Module Name</th>
                                                        <th class="text-center" style="width:15%">Training Status</th>
                                                        <th class="text-center" style="width:20%">Subtopics</th>
                                                        <th style="width:25%">Remark</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Sort modules by topic then module name
                                                    $mods = array_values($company_data['modules']);
                                                    usort($mods, function($a, $b) {
                                                        $ta = isset($a['topic_name']) ? $a['topic_name'] : 'Other';
                                                        $tb = isset($b['topic_name']) ? $b['topic_name'] : 'Other';
                                                        $tc = strcasecmp($ta, $tb);
                                                        if ($tc !== 0) return $tc;
                                                        return strcasecmp($a['training_module_name'], $b['training_module_name']);
                                                    });
                                                    $currentTopicHeader = null;
                                                    foreach ($mods as $module) { 
                                                        $collapseId = 'subs_'.$company_id.'_'.$module['module_id'];
                                                        $topicHeader = isset($module['topic_name']) && $module['topic_name'] !== '' ? $module['topic_name'] : 'Other';
                                                        if ($currentTopicHeader !== $topicHeader) {
                                                            $currentTopicHeader = $topicHeader;
                                                            echo '<tr class="table-active"><td colspan="4"><strong>Topic: '.htmlspecialchars($currentTopicHeader).'</strong></td></tr>';
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <?= $module['training_module_name']; ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <?= ($module['training_status'] == '1') ? 'Completed' : (($module['training_status'] == '0') ? 'Pending' : (($module['training_status'] == '2') ? 'Not Applicable' : (($module['training_status'] == '3') ? 'Later On' : ''))); ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php if (!empty($module['subtopics'])) { ?>
                                                                    <button class="btn btn-sm btn-primary" type="button" data-toggle="collapse" data-target="#<?= $collapseId ?>" aria-expanded="false" aria-controls="<?= $collapseId ?>">
                                                                        View Subtopics (<?= count($module['subtopics']); ?>)
                                                                    </button>
                                                                <?php } else { ?>
                                                                    <span class="text-muted">None</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?= !empty($module['module_remark']) ? htmlspecialchars($module['module_remark']) : '<span class="text-muted">No remark</span>'; ?>
                                                            </td>
                                                        </tr>
                                                        <?php if (!empty($module['subtopics'])) { ?>
                                                        <tr class="collapse" id="<?= $collapseId ?>">
                                                            <td colspan="4">
                                                                <div class="p-2">
                                                                    <div class="list-group list-group-flush">
                                                                        <?php foreach ($module['subtopics'] as $sub) { 
                                                                            $badge = '<span class=\"badge badge-secondary\">-</span>';
                                                                            if ($sub['is_completed'] === '1') { $badge = '<span class=\"badge badge-success\">Completed</span>'; }
                                                                            elseif ($sub['is_completed'] === '0') { $badge = '<span class=\"badge badge-warning\">Pending</span>'; }
                                                                            elseif ($sub['is_completed'] === '2') { $badge = '<span class=\"badge badge-info\">Not Applicable</span>'; }
                                                                            elseif ($sub['is_completed'] === '3') { $badge = '<span class=\"badge badge-primary\">Later On</span>'; }
                                                                        ?>
                                                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                                <div>
                                                                                    <?= htmlspecialchars($sub['subtopic_name']); ?>
                                                                                    <?php if (!empty($sub['subtopic_description'])) { ?>
                                                                                        <i class="fa fa-info-circle text-info ml-2" data-toggle="tooltip" data-placement="top" title="<?= htmlspecialchars($sub['subtopic_description']); ?>"></i>
                                                                                    <?php } ?>
                                                                                </div>
                                                                                <div><?= $badge ?></div>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </tbody>
                                            </table>

                                            <?php if (!empty($company_data['participants'])) { ?>
                                                <p class="font-weight-bold text-primary mt-4">Participant Details</p>
                                                <div class="table-responsive mt-4">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Type</th>
                                                                <th>Name</th>
                                                                <th>Designation</th>
                                                                <th>Contact</th>
                                                                <th>No Of Participants</th>
                                                                <th>Remark</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($company_data['participants'] as $participant) { ?>
                                                                <tr>
                                                                    <td><?= $participant['participant_type']; ?></td>
                                                                    <td><?= $participant['participant_name']; ?></td>
                                                                    <td><?= $participant['participant_designation']; ?></td>
                                                                    <td><?= $participant['participant_contact']; ?></td>
                                                                    <td><?= $participant['no_of_participants']; ?></td>
                                                                    <td><?= $participant['remark']; ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } else {
                                ?>
                                    <div class="card-body">
                                        <div class=" px-3">
                                            <h5 class="mb-2">
                                                <i class="fa fa-exclamation-circle"></i> <strong>Absent</strong> for this session.
                                            </h5>
                                            <p>
                                                <strong>Absent Reason:</strong> <?= !empty($company_data['absent_reason']) ? $company_data['absent_reason'] : 'N/A'; ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php
                                } ?>
                            </div>
                        </div>
                    </div>
        <?php }
            }
        } ?>
    </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script>
    function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            document.execCommand("copy");
            document.body.removeChild(textArea);
        }
    }
</script>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
    $(document).on('shown.bs.collapse', function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
    $(document).on('click', '[data-toggle="collapse"]', function(){
        setTimeout(function(){ $('[data-toggle="tooltip"]').tooltip(); }, 200);
    });
</script>