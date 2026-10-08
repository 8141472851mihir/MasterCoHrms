<?php

include_once 'common/object.php';
error_reporting(0);
include_once 'timeline_functions.php';
$base_url = $m->base_url();
extract(array_map("test_input", $_POST));

$company_ids_post = $_POST['company_ids'] ?? [];
$reference_company_ids = $_POST['reference_company_ids'] ?? [];
$company_id = array_unique(array_merge($company_ids_post, $reference_company_ids));

$prefillData = [];
$existingSubtopicStatusByCompany = []; // company_id => [subtopic_id => status]
$societyMetaById = []; // company_id => society row fields
$modulesRowsShared = []; // shared module catalog for this batch/day
$statusByCompanyPidMid = []; // company_id => [pid => [mid => status]]
$dateByCompanyPidMid = [];
$subtopicsByModuleShared = [];

if (!empty($company_id)) {
  $companyIdsInt = array_values(array_unique(array_map('intval', $company_id)));
  $companyIdsIn = implode(',', $companyIdsInt);

  if (isset($allowed_Participants) && $allowed_Participants != '') {
    $allowedParticipants = explode(',', $allowed_Participants);

    // Check if only one participant type is allowed
    if (count($allowedParticipants) === 1) {
      $singleParticipantType = $allowedParticipants[0];

      // Get participant type ID
      $participantQuery = $d->selectRow("participants_type_id", "training_participants_type", "participant_name = '$singleParticipantType'");
      if ($participantQuery && mysqli_num_rows($participantQuery) > 0) {
        $participantRow = mysqli_fetch_assoc($participantQuery);
        $participantTypeId = $participantRow['participants_type_id'];

        // Batch prefill for all companies
        $prefillQuery = $d->selectRow(
          "company_id, module_id, training_status, training_date",
          "batch_training_status_master",
          "company_id IN ($companyIdsIn) AND participant_id = '$participantTypeId'"
        );
        foreach ($companyIdsInt as $companyId) {
          $prefillData[$companyId] = [];
        }
        if ($prefillQuery && mysqli_num_rows($prefillQuery) > 0) {
          while ($prefillRow = mysqli_fetch_assoc($prefillQuery)) {
            $cid = (int)$prefillRow['company_id'];
            $prefillData[$cid][$prefillRow['module_id']] = [
              'training_status' => $prefillRow['training_status'],
              'training_date' => $prefillRow['training_date']
            ];
          }
        }
      }
    }

    $participantNames = implode("','", $allowedParticipants);
    $query = $d->selectRow("participant_name, participants_type_id", "training_participants_type", "participant_name IN ('$participantNames')");
    $participantData = [];
    while ($row = mysqli_fetch_array($query)) {
      $participantData[$row['participant_name']] = $row['participants_type_id'];
    }
  }

  // Prefetch active participants once (for overdue logic across all participants)
  $activeParticipantIds = [];
  $activePartsPrefQ = $d->selectRow("participants_type_id", "training_participants_type", "status = 0");
  while ($p = mysqli_fetch_assoc($activePartsPrefQ)) {
    $activeParticipantIds[] = (int)$p['participants_type_id'];
  }

  // Batch society metadata
  $socQ = $d->selectRow(
    "sm.society_name, sm.society_id, sm.created_date AS society_created_date, smr.society_remark, smr.implementation_remark",
    "society_master sm LEFT JOIN society_master_requests smr ON smr.society_id_added = sm.society_id",
    "sm.society_id IN ($companyIdsIn)"
  );
  while ($sr = mysqli_fetch_assoc($socQ)) {
    $societyMetaById[(int)$sr['society_id']] = $sr;
  }

  // Shared modules for this batch/day (same for every company)
  $modQ = $d->selectRow(
    "bmm.batch_module_id, bmm.batch_id, bmm.module_ids, bmm.day_number,
     tmm.training_module_id, tmm.training_module_name, tmm.module_type, tmm.completion_days, tmm.estimated_minutes AS module_estimated_minutes,
     tmpm.priority_name, tmpm.is_required, tmpm.priority_id,
     tmt.topic_id, tmt.topic_name, tmt.participant_type AS module_participant_id",
    "batch_module_master bmm
     LEFT JOIN training_module_master tmm ON FIND_IN_SET(tmm.training_module_id, bmm.module_ids)
     LEFT JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id
     LEFT JOIN training_module_topics tmt ON tmt.topic_id = tmm.topic_id AND tmt.topic_type = '0'",
    "bmm.batch_id = '$batch_id' AND bmm.day_number = '$meeting_day'
     GROUP BY tmm.training_module_id, tmt.topic_id ORDER BY tmm.training_module_order,(tmt.topic_name IS NULL), tmt.topic_name ASC, tmpm.priority_id ASC, tmm.training_module_name ASC"
  );
  $moduleIdsShared = [];
  while ($mr = mysqli_fetch_assoc($modQ)) {
    $modulesRowsShared[] = $mr;
    if (!empty($mr['training_module_id'])) {
      $moduleIdsShared[] = (int)$mr['training_module_id'];
    }
  }
  $moduleIdsShared = array_values(array_unique($moduleIdsShared));

  // Prefetch subtopics once for shared modules
  if (!empty($moduleIdsShared)) {
    $midCsv = implode(',', $moduleIdsShared);
    $subQ = $d->selectRow(
      "training_module_id, subtopic_id, subtopic_name, subtopic_description, estimated_minutes, display_order",
      "training_module_subtopics",
      "training_module_id IN ($midCsv) AND subtopic_status = 1",
      "ORDER BY training_module_id ASC, display_order ASC"
    );
    while ($s = mysqli_fetch_assoc($subQ)) {
      $mid = (int)$s['training_module_id'];
      if (!isset($subtopicsByModuleShared[$mid])) {
        $subtopicsByModuleShared[$mid] = [];
      }
      $subtopicsByModuleShared[$mid][] = $s;
    }
  }

  // Batch subtopic company statuses
  if (isset($slot_id) && $slot_id != '') {
    $existingStatuses = $d->select("training_subtopic_company_status", "company_id IN ($companyIdsIn)");
    while ($row = mysqli_fetch_assoc($existingStatuses)) {
      $cid = (int)$row['company_id'];
      $existingSubtopicStatusByCompany[$cid][(int)$row['subtopic_id']] = (string)$row['subtopic_company_status'];
    }
  }

  // Batch training statuses for all companies
  $stAllQ = $d->selectRow(
    "company_id, participant_id, module_id, training_status, training_date",
    "batch_training_status_master",
    "company_id IN ($companyIdsIn)"
  );
  while ($sr = mysqli_fetch_assoc($stAllQ)) {
    $cid = (int)$sr['company_id'];
    $pid = (int)$sr['participant_id'];
    $mid = (int)$sr['module_id'];
    if (!isset($statusByCompanyPidMid[$cid])) {
      $statusByCompanyPidMid[$cid] = [];
      $dateByCompanyPidMid[$cid] = [];
    }
    if (!isset($statusByCompanyPidMid[$cid][$pid])) {
      $statusByCompanyPidMid[$cid][$pid] = [];
      $dateByCompanyPidMid[$cid][$pid] = [];
    }
    $statusByCompanyPidMid[$cid][$pid][$mid] = isset($sr['training_status']) ? (int)$sr['training_status'] : null;
    $dateByCompanyPidMid[$cid][$pid][$mid] = $sr['training_date'] ?? null;
  }

  foreach ($company_id as $selected_society_id) {
    $selected_society_id = intval($selected_society_id);
    $existingSubtopicStatusById = $existingSubtopicStatusByCompany[$selected_society_id] ?? [];
    $companyData = $societyMetaById[$selected_society_id] ?? null;
    if (!empty($companyData)) {
      $companyName = $companyData['society_name'];
      $companyID = $companyData['society_id'];
      $companyCreatedDate = isset($companyData['society_created_date']) ? substr($companyData['society_created_date'], 0, 10) : '';
      $companyRemark = isset($companyData['society_remark']) ? trim($companyData['society_remark']) : '';
      $implementationRemark = isset($companyData['implementation_remark']) ? trim($companyData['implementation_remark']) : '';

      $isReference = in_array($companyID, $reference_company_ids);
      $referenceText = $isReference ? ' (Reference)' : '';
?>
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Company Name: <?php echo htmlspecialchars($companyName) . $referenceText; ?></h5>
        <div class="btn-group btn-group-sm" role="group">
          <button type="button" class="btn btn-outline-success" onclick="markAllForCompany(<?php echo $companyID; ?>, 1)" title="Mark all modules and subtopics as Completed">
            <i class="fa fa-check"></i> All Completed
          </button>
          <button type="button" class="btn btn-outline-warning" onclick="markAllForCompany(<?php echo $companyID; ?>, 0)" title="Mark all modules and subtopics as Pending">
            <i class="fa fa-clock-o"></i> All Pending
          </button>
          <button type="button" class="btn btn-outline-info" onclick="markAllForCompany(<?php echo $companyID; ?>, 2)" title="Mark all modules and subtopics as Not Applicable">
            <i class="fa fa-ban"></i> All NA
          </button>
          <button type="button" class="btn btn-outline-secondary" onclick="markAllForCompany(<?php echo $companyID; ?>, 3)" title="Mark all modules and subtopics as Later On">
            <i class="fa fa-calendar"></i> All Later On
          </button>
        </div>
      </div>
      <?php if ($companyRemark !== '' || $implementationRemark !== ''): ?>
        <div class="mb-2" style="background:#f8f9fa;border:1px solid #e9ecef;border-radius:4px;padding:8px;">
          <?php if ($companyRemark !== ''): ?>
            <div><strong>Company Remark:</strong> <span><?php echo nl2br(htmlspecialchars($companyRemark)); ?></span></div>
          <?php endif; ?>
          <?php if ($implementationRemark !== ''): ?>
            <div><strong>Implementation Remark:</strong> <span><?php echo nl2br(htmlspecialchars($implementationRemark)); ?></span></div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <div class="mb-2">
        <input type="radio" name="attendance_<?php echo $selected_society_id; ?>" value="present"
          id="present_<?php echo $selected_society_id; ?>" onchange="toggleAttendance(this)" checked>
        <label for="present_<?php echo $selected_society_id; ?>">Present</label>

        <input type="radio" name="attendance_<?php echo $selected_society_id; ?>" value="absent"
          id="absent_<?php echo $selected_society_id; ?>" onchange="toggleAttendance(this)">
        <label for="absent_<?php echo $selected_society_id; ?>">Absent</label>
      </div>

      <div id="reasonBox_<?php echo $selected_society_id; ?>" class="mb-3 reason-box" style="display: none;">
        <textarea name="reason_<?php echo $selected_society_id; ?>" id="reasonText_<?php echo $selected_society_id; ?>" class="form-control"
          placeholder="Enter Absent Report"></textarea>
        <small id="reasonErr_<?php echo $selected_society_id; ?>" class="text-danger" style="display:none;">Remark is required when company is absent.</small>
      </div>

      <div id="presentTable_<?php echo $selected_society_id; ?>" class="table-responsive"
        style="display: block;">

        <div class="mb-3 d-flex flex-wrap">
          <label class="form-label mt-2 mr-2"><strong>Present Participants:</strong></label>
          <div class="form-check form-check-inline me-3">
            <input class="form-check-input" type="checkbox" id="checkAllParticipants_<?php echo $companyID; ?>" />
            <label class="form-check-label" for="checkAllParticipants_<?php echo $companyID; ?>">Check All</label>
          </div>

          <?php
          if (isset($allowed_Participants) && $allowed_Participants != '') {
            foreach ($allowedParticipants as $index => $participantName) {
              $participantName = htmlspecialchars($participantName);
              $participantId = isset($participantData[$participantName]) ? $participantData[$participantName] : '';

              $isChecked = '';
              if (count($allowedParticipants) === 1 && isset($prefillData[$companyID]) && !empty($prefillData[$companyID])) {
                $isChecked = 'checked';
              }
          ?>
              <div class="form-check form-check-inline me-3">
                <input class="form-check-input participant-checkbox" type="checkbox" id="participant_<?php echo $companyID . '_' . $participantId; ?>" name="present_participants[<?php echo $companyID; ?>][]" value="<?php echo $participantId; ?>" <?php echo $isChecked; ?>>
                <label class="form-check-label" for="participant_<?php echo $companyID . '_' . $participantId; ?>"><?php echo $participantName; ?></label>
              </div>
          <?php
            }
          }
          ?>
          <span class="text-danger ml-2 mt-1" id="participantError_<?php echo $companyID; ?>" style="display:none;">Select at least one participant</span>
        </div>

        <style>
          #presentTable_<?php echo $companyID; ?> .table-bordered {
            table-layout: auto;
          }
          #presentTable_<?php echo $companyID; ?> .table-bordered th:nth-child(1),
          #presentTable_<?php echo $companyID; ?> .table-bordered td:nth-child(1) {
            width: auto;
            min-width: 200px;
            max-width: 400px;
            word-break: keep-all;
            overflow-wrap: normal;
            white-space: normal;
          }
          #presentTable_<?php echo $companyID; ?> .table-bordered td:nth-child(1) > div {
            word-break: keep-all;
            overflow-wrap: normal;
          }
          #presentTable_<?php echo $companyID; ?> .table-bordered td:nth-child(1) > div > div {
            word-break: keep-all;
            overflow-wrap: normal;
          }
          #presentTable_<?php echo $companyID; ?> .table-bordered th:nth-child(2),
          #presentTable_<?php echo $companyID; ?> .table-bordered td:nth-child(2) {
            width: 35%;
            min-width: 250px;
          }
          #presentTable_<?php echo $companyID; ?> .table-bordered th:nth-child(3),
          #presentTable_<?php echo $companyID; ?> .table-bordered td:nth-child(3) {
            width: 40%;
            min-width: 300px;
          }
          #presentTable_<?php echo $companyID; ?> .table-bordered textarea[name^="module_remark"] {
            min-width: 100%;
            resize: vertical;
          }
        </style>
        <table class="table table-bordered">
          <input type="hidden" name="company_ids[]" value="<?php echo $companyID; ?>">
          <input type="hidden" name="reference_company_ids" value="<?php echo implode(',', $reference_company_ids); ?>">

          <tr>
            <th>Module Name</th>
            <th>Training Status</th>
            <th>Remark</th>
          </tr>
          <?php
          // Use shared prefetched module rows + statuses for this company
          $moduleIdsForCompany = $moduleIdsShared;
          $subtopicsByModule = $subtopicsByModuleShared;
          $statusByPidMid = $statusByCompanyPidMid[$companyID] ?? [];
          $dateByPidMid = $dateByCompanyPidMid[$companyID] ?? [];
          // Memoization caches to avoid repeated heavy helpers
          $dueCache = [];
          $lateCache = [];

          $currentTopic = null;
          $moduleOrder = [];
          $moduleIndex = 0;
          foreach ($modulesRowsShared as $module) {
            $moduleId = $module['training_module_id'];
            $moduleName = htmlspecialchars($module['training_module_name']);
            $priorityName = !empty($module['priority_name']) ? ' (' . htmlspecialchars($module['priority_name']) . ')' : '';
            $completionDays = isset($module['completion_days']) ? (int)$module['completion_days'] : null;
            $moduleType = isset($module['module_type']) ? (int)$module['module_type'] : 0;
            $moduleEstimatedMinutes = isset($module['module_estimated_minutes']) ? intval($module['module_estimated_minutes']) : 0;

            $isFirstModule = ($moduleIndex === 0);
            $moduleOrder[] = $moduleId;
            $rowTopic = !empty($module['topic_name']) ? $module['topic_name'] : 'Other';
            if ($currentTopic !== $rowTopic) {
              $currentTopic = $rowTopic;
              echo '<tr class="table-active">';
              echo '<td colspan="3"><strong>Topic: ' . htmlspecialchars($currentTopic) . '</strong>';
              echo '</td></tr>';
            }
            $isOverdue = false;
            $dueDateLabel = '';
            $isCompletedLate = false;
            $completedLateDueLabel = '';
            if ($moduleType === 1 && $completionDays && $companyCreatedDate) {
              $assignedPid = isset($module['module_participant_id']) ? (int)$module['module_participant_id'] : 0;
              if ($assignedPid > 0) {
                $pStatus = isset($statusByPidMid[$assignedPid][$moduleId]) ? (int)$statusByPidMid[$assignedPid][$moduleId] : null;
                if ($pStatus === 1 || $pStatus === 2 || $pStatus === 3) {
                  if (!isset($lateCache[$moduleId])) {
                    $lateCache[$moduleId] = isModuleCompletedLate($d, $companyID, $moduleId, $companyCreatedDate);
                  }
                  $lateInfo = $lateCache[$moduleId];
                  if ($lateInfo['completed_late']) {
                    $isOverdue = false;
                    $isCompletedLate = true;
                    $completedLateDueLabel = date('d-M-Y', strtotime($lateInfo['due_date']));
                  }
                } else {
                  $dueKey = $assignedPid . '_' . $moduleId;
                  if (!isset($dueCache[$dueKey])) {
                    $dueCache[$dueKey] = calculateModuleDueDate($d, $companyID, $assignedPid, $moduleId, $companyCreatedDate);
                  }
                  $info = $dueCache[$dueKey];
                  if (!empty($info['due_date']) && !empty($info['is_overdue'])) {
                    $isOverdue = true;
                    $dueDateLabel = date('d-M-Y', strtotime($info['due_date']));
                  }
                }
              }
            }
            $subtopicsForModule = isset($subtopicsByModule[$moduleId]) ? $subtopicsByModule[$moduleId] : [];
            $hasSubtopics = !empty($subtopicsForModule);
          ?>
            <tr id="moduleRow_<?php echo $companyID; ?>_<?php echo $moduleId; ?>"
              class="module-row"
              data-company-id="<?php echo $companyID; ?>"
              data-module-id="<?php echo $moduleId; ?>"
              data-module-index="<?php echo $moduleIndex; ?>"
              data-is-first="<?php echo $isFirstModule ? 'true' : 'false'; ?>"
              data-prefill-status="<?php echo isset($prefillData[$companyID][$moduleId]) ? htmlspecialchars($prefillData[$companyID][$moduleId]['training_status']) : ''; ?>"
              <?php echo $isOverdue ? " style=\"background:#fff5f5;\"" : ""; ?>>
              <td>
                <div class="d-flex align-items-center">
                  <span class="module-order-badge badge badge-info mr-2" style="min-width: 25px; text-align: center;">
                    <?php echo $moduleIndex + 1; ?>
                  </span>
                  <div>
                    <?php echo $moduleName . $priorityName; ?>
                    <?php if ($moduleEstimatedMinutes > 0): ?>
                      <span class="badge badge-secondary ml-1" title="Estimated time to complete"><?php echo $moduleEstimatedMinutes; ?> min</span>
                    <?php endif; ?>
                    <?php if ($isOverdue): ?>
                      <span class="badge badge-danger" title="Due by <?php echo $dueDateLabel; ?>">Overdue</span>
                    <?php elseif ($isCompletedLate): ?>
                      <span class="badge badge-warning" title="Due by <?php echo $completedLateDueLabel; ?>">Completed Late</span>
                    <?php endif; ?>
                    <?php if (!$isFirstModule): ?>
                      <div class="text-warning small mt-1">
                        <i class="fa fa-lock"></i> Complete previous modules first
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </td>
              <td>
                <?php 
                if ($hasSubtopics): ?>
                  <div class="mb-2">
                    <?php
                    $isBulkDisabled = !$isFirstModule;
                    $bulkDisabledAttr = $isBulkDisabled ? 'disabled' : '';
                    ?>
                    <input type="radio" class="ignore-validate" id="all_yes_<?php echo $companyID; ?>_<?php echo $moduleId; ?>" name="training_status_all[<?php echo $companyID; ?>][<?php echo $moduleId; ?>]" value="1" data-all-toggle="1"
                      onclick="setAllSubtopics(<?php echo $companyID; ?>, <?php echo $moduleId; ?>, 1)" <?php echo $bulkDisabledAttr; ?>>
                    <label for="all_yes_<?php echo $companyID; ?>_<?php echo $moduleId; ?>">All Completed</label>

                    <input type="radio" class="ignore-validate" id="all_no_<?php echo $companyID; ?>_<?php echo $moduleId; ?>" name="training_status_all[<?php echo $companyID; ?>][<?php echo $moduleId; ?>]" value="0" data-all-toggle="1"
                      onclick="setAllSubtopics(<?php echo $companyID; ?>, <?php echo $moduleId; ?>, 0)" <?php echo $bulkDisabledAttr; ?>>
                    <label for="all_no_<?php echo $companyID; ?>_<?php echo $moduleId; ?>">All Pending</label>

                    <input type="radio" class="ignore-validate" id="all_na_<?php echo $companyID; ?>_<?php echo $moduleId; ?>" name="training_status_all[<?php echo $companyID; ?>][<?php echo $moduleId; ?>]" value="2" data-all-toggle="1"
                      onclick="setAllSubtopics(<?php echo $companyID; ?>, <?php echo $moduleId; ?>, 2)" <?php echo $bulkDisabledAttr; ?>>
                    <label for="all_na_<?php echo $companyID; ?>_<?php echo $moduleId; ?>">All Not Applicable</label>

                    <input type="radio" class="ignore-validate" id="all_later_<?php echo $companyID; ?>_<?php echo $moduleId; ?>" name="training_status_all[<?php echo $companyID; ?>][<?php echo $moduleId; ?>]" value="3" data-all-toggle="1"
                      onclick="setAllSubtopics(<?php echo $companyID; ?>, <?php echo $moduleId; ?>, 3)" <?php echo $bulkDisabledAttr; ?>>
                    <label for="all_later_<?php echo $companyID; ?>_<?php echo $moduleId; ?>">All Later On</label>
                  </div>
                  <?php foreach ($subtopicsForModule as $subtopic): ?>
                    <div class="pl-4 mb-1">
                      <?php
                      $subtopicName = htmlspecialchars($subtopic['subtopic_name']);
                      $subMinutes = isset($subtopic['estimated_minutes']) ? intval($subtopic['estimated_minutes']) : 0;
                      echo $subtopicName;
                      if ($subMinutes > 0) {
                        echo ' <span class="badge badge-light" title="Estimated time">' . $subMinutes . ' min</span>';
                      }

                      ?>

                      <?php
                      $subId = (int)$subtopic['subtopic_id'];
                      $saved = isset($existingSubtopicStatusById[$subId]) ? $existingSubtopicStatusById[$subId] : null; // '0' | '1' | null
                      $prefillStatus = null;
                      if (isset($prefillData[$companyID][$moduleId])) {
                        $prefillStatus = $prefillData[$companyID][$moduleId]['training_status'];
                      }

                      $checkedYes = ($saved == '1') ? 'checked' : (($prefillStatus == '1') ? 'checked' : '');
                      $checkedNo = ($saved == '0') ? 'checked' : (($prefillStatus == '0') ? 'checked' : '');
                      $checkedNa = ($prefillStatus === '2') ? 'checked' : '';
                      $checkedLater = ($prefillStatus === '3') ? 'checked' : '';
                      $yesId = "yes_sub_{$slot_id}_{$companyID}_{$subId}";
                      $noId = "no_sub_{$slot_id}_{$companyID}_{$subId}";
                      $naId = "na_sub_{$slot_id}_{$companyID}_{$subId}";
                      $laterId = "later_sub_{$slot_id}_{$companyID}_{$subId}";
                      ?>
                      <?php
                      $isSubtopicDisabled = !$isFirstModule;
                      $subtopicDisabledAttr = $isSubtopicDisabled ? 'disabled' : '';
                      ?>
                      <input class="subtopic-radio company-<?php echo $companyID; ?> module-<?php echo $moduleId; ?>" type="radio" id="<?php echo $yesId; ?>"
                        name="training_status_subtopic[<?php echo $companyID; ?>][<?php echo $moduleId; ?>][<?php echo $subtopic['subtopic_id']; ?>]" value="1" <?php echo $checkedYes; ?> required <?php echo $subtopicDisabledAttr; ?>>
                      <label for="<?php echo $yesId; ?>">Completed</label>

                      <input class="subtopic-radio company-<?php echo $companyID; ?> module-<?php echo $moduleId; ?>" type="radio" id="<?php echo $noId; ?>"
                        name="training_status_subtopic[<?php echo $companyID; ?>][<?php echo $moduleId; ?>][<?php echo $subtopic['subtopic_id']; ?>]" value="0" <?php echo $checkedNo; ?> <?php echo $subtopicDisabledAttr; ?>>
                      <label for="<?php echo $noId; ?>">Pending</label>

                      <input class="subtopic-radio company-<?php echo $companyID; ?> module-<?php echo $moduleId; ?>" type="radio" id="<?php echo $naId; ?>"
                        name="training_status_subtopic[<?php echo $companyID; ?>][<?php echo $moduleId; ?>][<?php echo $subtopic['subtopic_id']; ?>]" value="2" <?php echo $checkedNa; ?> <?php echo $subtopicDisabledAttr; ?>>
                      <label for="<?php echo $naId; ?>">Not Applicable</label>

                      <input class="subtopic-radio company-<?php echo $companyID; ?> module-<?php echo $moduleId; ?>" type="radio" id="<?php echo $laterId; ?>"
                        name="training_status_subtopic[<?php echo $companyID; ?>][<?php echo $moduleId; ?>][<?php echo $subtopic['subtopic_id']; ?>]" value="3" <?php echo $checkedLater; ?> <?php echo $subtopicDisabledAttr; ?>>
                      <label for="<?php echo $laterId; ?>">Later On</label>
                      <label id="training_status_subtopic[<?php echo $companyID; ?>][<?php echo $moduleId; ?>][<?php echo $subtopic['subtopic_id']; ?>]-error" class="error" for="training_status_subtopic[<?php echo $companyID; ?>][<?php echo $moduleId; ?>][<?php echo $subtopic['subtopic_id']; ?>]" style="display:inline-block;margin-left:8px;"></label>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="mb-1">
                    <?php
                    $yesModId = "yes_mod_{$slot_id}_{$companyID}_{$moduleId}";
                    $noModId = "no_mod_{$slot_id}_{$companyID}_{$moduleId}";
                    $naModId = "na_mod_{$slot_id}_{$companyID}_{$moduleId}";
                    $laterModId = "later_mod_{$slot_id}_{$companyID}_{$moduleId}";

                    $prefillStatus = null;
                    if (isset($prefillData[$companyID][$moduleId])) {
                      $prefillStatus = $prefillData[$companyID][$moduleId]['training_status'];
                    }

                    $checkedYes = ($prefillStatus === '1') ? 'checked' : '';
                    $checkedNo = ($prefillStatus === '0') ? 'checked' : '';
                    $checkedNa = ($prefillStatus === '2') ? 'checked' : '';
                    $checkedLater = ($prefillStatus === '3') ? 'checked' : '';
                    ?>
                    <?php
                    $isModuleDisabled = !$isFirstModule;
                    $disabledAttr = $isModuleDisabled ? 'disabled' : '';
                    ?>
                    <input type="radio" id="<?php echo $yesModId; ?>"
                      name="training_status_module[<?php echo $companyID; ?>][<?php echo $moduleId; ?>]" value="1" required <?php echo $checkedYes; ?> <?php echo $disabledAttr; ?>>
                    <label for="<?php echo $yesModId; ?>">Completed</label>

                    <input type="radio" id="<?php echo $noModId; ?>"
                      name="training_status_module[<?php echo $companyID; ?>][<?php echo $moduleId; ?>]" value="0" <?php echo $checkedNo; ?> <?php echo $disabledAttr; ?>>
                    <label for="<?php echo $noModId; ?>">Pending</label>

                    <input type="radio" id="<?php echo $naModId; ?>"
                      name="training_status_module[<?php echo $companyID; ?>][<?php echo $moduleId; ?>]" value="2" <?php echo $checkedNa; ?> <?php echo $disabledAttr; ?>>
                    <label for="<?php echo $naModId; ?>">Not Applicable</label>

                    <input type="radio" id="<?php echo $laterModId; ?>"
                      name="training_status_module[<?php echo $companyID; ?>][<?php echo $moduleId; ?>]" value="3" <?php echo $checkedLater; ?> <?php echo $disabledAttr; ?>>
                    <label for="<?php echo $laterModId; ?>">Later On</label>
                    <label id="training_status_module[<?php echo $companyID; ?>][<?php echo $moduleId; ?>]-error" class="error" for="training_status_module[<?php echo $companyID; ?>][<?php echo $moduleId; ?>]" style="display:inline-block;margin-left:8px;"></label>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <textarea class="form-control" name="module_remark[<?php echo $companyID; ?>][<?php echo $moduleId; ?>]"
                  placeholder="Enter remark for this module" rows="4" maxlength="500" style="min-height: 80px;"></textarea>
              </td>
            </tr>
          <?php
            $moduleIndex++;
          } ?>
        </table>
        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-success btn-sm mt-2 mb-2"
            onclick="addParticipants(<?php echo $companyID; ?>,'<?php echo $allowed_Participants; ?>')">Add
            Participants</button>
        </div>
        <div>
          <table class="table table-bordered" id="addParticipants_<?php echo $companyID; ?>"></table>
        </div>
      </div>
      <hr>
<?php
    }
  }
}
?>

<script>
  function setAllSubtopics(companyID, moduleId, value) {
    const row = document.getElementById(`moduleRow_${companyID}_${moduleId}`);
    if (!row) return;
    const selector = `input.subtopic-radio.company-${companyID}.module-${moduleId}[value="${value}"]`;
    const radios = row.querySelectorAll(selector);
    radios.forEach(radio => {
      radio.checked = true;
    });

    if (radios.length > 0) {
      validateModuleOrder(radios[0]);
    }
  }

  function markAllForCompany(companyID, statusValue) {
    const moduleRows = Array.from(document.querySelectorAll(`tr.module-row[data-company-id="${companyID}"]`));
    if (moduleRows.length === 0) {
      alert('No modules found for this company.');
      return;
    }
    const statusNames = {
      1: 'Completed',
      0: 'Pending',
      2: 'Not Applicable',
      3: 'Later On'
    };
    if (!confirm(`Are you sure you want to mark all modules and subtopics as "${statusNames[statusValue]}" for this company?`)) {
      return;
    }
    window._bulkUpdateInProgress = true;
    moduleRows.sort((a, b) => {
      const indexA = parseInt(a.dataset.moduleIndex) || 0;
      const indexB = parseInt(b.dataset.moduleIndex) || 0;
      return indexA - indexB;
    });
      let processedCount = 0;
      const changedElements = [];
      moduleRows.forEach((moduleRow) => {
        const moduleId = moduleRow.dataset.moduleId;
        const subtopicRadios = moduleRow.querySelectorAll(`input.subtopic-radio.company-${companyID}.module-${moduleId}`);
        if (subtopicRadios.length > 0) {
          // Module has subtopics - mark all subtopics (including disabled ones)
          const subtopicSelector = `input.subtopic-radio.company-${companyID}.module-${moduleId}[value="${statusValue}"]`;
          const targetRadios = moduleRow.querySelectorAll(subtopicSelector);
          if (targetRadios.length > 0) {
            targetRadios.forEach(radio => {
              // Temporarily enable if disabled, mark as checked, then restore disabled state
              const wasDisabled = radio.disabled;
              if (wasDisabled) {
                radio.disabled = false;
              }
              radio.checked = true;
              if (wasDisabled) {
                radio.disabled = true;
              }
              changedElements.push(radio);
              processedCount++;
            });
          }
        } else {
          // Module has no subtopics - mark module status directly (including disabled ones)
          const moduleRadioSelector = `input[name="training_status_module[${companyID}][${moduleId}]"][value="${statusValue}"]`;
          const moduleRadio = moduleRow.querySelector(moduleRadioSelector);
          if (moduleRadio) {
            // Temporarily enable if disabled, mark as checked, then restore disabled state
            const wasDisabled = moduleRadio.disabled;
            if (wasDisabled) {
              moduleRadio.disabled = false;
            }
            moduleRadio.checked = true;
            if (wasDisabled) {
              moduleRadio.disabled = true;
            }
            changedElements.push(moduleRadio);
            processedCount++;
          }
        }
      });

    // Clear the bulk update flag
    window._bulkUpdateInProgress = false;

    // Now validate only once, starting from the first changed element
    if (changedElements.length > 0 && typeof validateModuleOrder === 'function') {
      // Validate starting from the first changed element
      validateModuleOrder(changedElements[0]);
    }

    // Scroll to first module for visual feedback
    if (moduleRows.length > 0) {
      moduleRows[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }

  $(document).on('change', '.subtopic-radio', function() {
    // Skip validation during bulk updates
    if (window._bulkUpdateInProgress) {
      return;
    }
    
    const companyID = this.name.match(/\[(\d+)\]/)[1];

    const moduleClass = this.className.match(/module-(\d+)/);
    const moduleId = moduleClass ? moduleClass[1] : null;

    if (moduleId) {
      const row = document.getElementById(`moduleRow_${companyID}_${moduleId}`);

      if (row) {
        const bulkRadios = row.querySelectorAll('[data-all-toggle="1"]');
        bulkRadios.forEach(radio => radio.checked = false);
      }
    }

    validateModuleOrder(this);
  });

  $(document).on('change', 'input[name^="training_status_module"]', function() {
    // Skip validation during bulk updates
    if (window._bulkUpdateInProgress) {
      return;
    }
    
    validateModuleOrder(this);
  });

  function validateModuleOrder(changedElement) {
    const companyID = changedElement.name.match(/\[(\d+)\]/)[1];
    const moduleRow = changedElement.closest('tr[data-company-id="' + companyID + '"]');

    if (!moduleRow) return;

    const moduleIndex = parseInt(moduleRow.dataset.moduleIndex);
    const isFirstModule = moduleRow.dataset.isFirst === 'true';

    if (isFirstModule) {
      enableModule(moduleRow);
    } else {
      const previousModulesCompleted = checkPreviousModulesCompleted(companyID, moduleIndex);

      if (previousModulesCompleted) {
        enableModule(moduleRow);
      } else {
        disableModule(moduleRow);
      }
    }

    revalidateSubsequentModules(companyID, moduleIndex);
  }

  function revalidateSubsequentModules(companyID, changedModuleIndex) {
    // Skip revalidation during bulk updates to prevent recursive calls
    if (window._bulkUpdateInProgress) {
      return;
    }
    
    const allModules = document.querySelectorAll(`tr[data-company-id="${companyID}"].module-row`);

    allModules.forEach(moduleRow => {
      const moduleIndex = parseInt(moduleRow.dataset.moduleIndex);
      if (moduleIndex > changedModuleIndex) {
        const isFirstModule = moduleRow.dataset.isFirst === 'true';
        if (isFirstModule) {
          enableModule(moduleRow);
        } else {
          const previousModulesCompleted = checkPreviousModulesCompleted(companyID, moduleIndex);

          if (previousModulesCompleted) {
            enableModule(moduleRow);
          } else {
            disableModule(moduleRow);
          }
        }
      }
    });
  }

  function checkPreviousModulesCompleted(companyID, currentModuleIndex) {
    for (let i = 0; i < currentModuleIndex; i++) {
      const moduleRow = document.querySelector(`tr[data-company-id="${companyID}"][data-module-index="${i}"]`);
      if (!moduleRow) continue;

      const moduleStatus = getModuleStatus(moduleRow);
      if (moduleStatus !== 1 && moduleStatus !== 2 && moduleStatus !== 3) {
        return false;
      }
    }
    return true;
  }

  function getModuleStatus(moduleRow) {
    const companyID = moduleRow.dataset.companyId;
    const moduleId = moduleRow.dataset.moduleId;
    const prefill = (moduleRow.dataset.prefillStatus || '').trim();

    const moduleRadio = moduleRow.querySelector(`input[name="training_status_module[${companyID}][${moduleId}]"]:checked`);
    if (moduleRadio) {
      return parseInt(moduleRadio.value);
    }

    const allSubtopicInputs = moduleRow.querySelectorAll(`input[name^="training_status_subtopic[${companyID}][${moduleId}]"]`);
    if (allSubtopicInputs.length > 0) {
      const subtopicIds = new Set();
      allSubtopicInputs.forEach(inp => {
        const match = inp.name.match(/\]\[(\d+)\]$/);
        if (match) {
          subtopicIds.add(match[1]);
        }
      });
      const totalSubtopics = subtopicIds.size;

      const checkedSubtopicRadios = moduleRow.querySelectorAll(`input[name^="training_status_subtopic[${companyID}][${moduleId}]"]:checked`);
      if (checkedSubtopicRadios.length < totalSubtopics) {
        // If nothing is selected yet but prefill says completed/NA/Later, consider completed for unlocking
        if (checkedSubtopicRadios.length === 0 && (prefill === '1' || prefill === '2' || prefill === '3')) {
          return parseInt(prefill);
        }
        return 0;
      }
      for (let radio of checkedSubtopicRadios) {
        if (parseInt(radio.value) === 0) {
          return 0;
        }
      }
      return 1;
    }

    // Fall back to prefill status (from previous meeting) if available
    if (prefill === '1' || prefill === '2' || prefill === '3') {
      return parseInt(prefill);
    }

    return 0; // Pending
  }

  function enableModule(moduleRow) {
    const inputs = moduleRow.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
      input.disabled = false;
    });
    const warning = moduleRow.querySelector('.text-warning');
    if (warning) {
      warning.style.display = 'none';
    }
    moduleRow.style.opacity = '1';

    const moduleOrderBadge = moduleRow.querySelector('.module-order-badge');
    if (moduleOrderBadge) {
      moduleOrderBadge.classList.remove('badge-secondary');
      moduleOrderBadge.classList.add('badge-success');
    }
  }

  function disableModule(moduleRow) {
    const inputs = moduleRow.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
      if (input.type === 'radio' || input.type === 'checkbox') {
        input.checked = false;
      } else if (input.tagName === 'TEXTAREA' || input.tagName === 'SELECT' || input.type === 'text' || input.type === 'number' || input.type === 'tel') {
        input.value = '';
      }
      input.disabled = true;
    });
    const warning = moduleRow.querySelector('.text-warning');
    if (warning) {
      warning.style.display = 'block';
    }
    moduleRow.style.opacity = '0.6';
    const moduleOrderBadge = moduleRow.querySelector('.module-order-badge');
    if (moduleOrderBadge) {
      moduleOrderBadge.classList.remove('badge-success');
      moduleOrderBadge.classList.add('badge-secondary');
    }
  }

  function runInitialValidation() {
    try {
      const companies = new Set();
      const allModuleRows = document.querySelectorAll('.module-row');

      allModuleRows.forEach(row => {
        companies.add(row.dataset.companyId);
      });
      companies.forEach(companyID => {
        const companyModules = document.querySelectorAll(`tr[data-company-id="${companyID}"].module-row`);

        companyModules.forEach(moduleRow => {
          const moduleIndex = parseInt(moduleRow.dataset.moduleIndex);
          const isFirstModule = moduleRow.dataset.isFirst === 'true';

          if (isFirstModule) {
            enableModule(moduleRow);
          } else {
            const previousModulesCompleted = checkPreviousModulesCompleted(companyID, moduleIndex);

            if (previousModulesCompleted) {
              enableModule(moduleRow);
            } else {
              disableModule(moduleRow);
            }
          }
        });
      });
    } catch (e) {}
  }

  document.addEventListener('DOMContentLoaded', runInitialValidation);
  // Also run immediately for AJAX-inserted content where DOMContentLoaded already fired
  runInitialValidation();

  $(document).on('input', '.onlyNum', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
  });

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name^="training_status_all["]').forEach(function(radio) {
      radio.required = false;
    });
  });

  (function enforceParticipantsRequirement() {
    try {
      const submitBtn = parent.document.getElementById('submitTrainingMeeting');
      if (!submitBtn) return;
      submitBtn.addEventListener('click', function(e) {
        const companies = Array.from(document.querySelectorAll('[id^="presentTable_"]'));
        for (const table of companies) {
          const companyID = table.id.split('_')[1];
          const presentRadio = document.getElementById('present_' + companyID);
          if (presentRadio && !presentRadio.checked) {
            const errSkip = document.getElementById(`participantError_${companyID}`);
            if (errSkip) {
              errSkip.style.display = 'none';
            }
            continue;
          }

          const anyChecked = !!document.querySelector(`#presentTable_${companyID} .participant-checkbox:checked`);
          const err = document.getElementById(`participantError_${companyID}`);
          if (!anyChecked) {
            e.preventDefault();
            if (err) {
              err.style.display = 'inline';
            }
            table.scrollIntoView({
              behavior: 'smooth',
              block: 'center'
            });
            return false;
          } else if (err) {
            err.style.display = 'none';
          }
        }
      }, false);
    } catch (ex) {}
  })();

  function addParticipants(companyID, allowed) {
    var table = document.getElementById('addParticipants_' + companyID);
    if (table.rows.length === 0) {
      var headingRow = document.createElement('tr');
      headingRow.className = 'participant-input-row heading-row';
      headingRow.style.display = 'table-row';
      headingRow.innerHTML = `
      <th>Type</th>
      <th>Name</th>
      <th>Designation</th>
      <th>Contact</th>
      <th>No Of Participant</th>
      <th>Remark</th>
      <th>Action</th>
          `;
      table.appendChild(headingRow);
    }
    var allowedTypes = allowed.split(',');
    var inputRow = document.createElement('tr');
    inputRow.className = 'participant-input-row input-row';
    inputRow.style.display = 'table-row';
    inputRow.innerHTML = `
    <td>
      <select class="form-control" required="" name="participant_Type[${companyID}][]" onchange="toggleFields(this, ${companyID})">
        <option class="form-control" value="">--Select Type--</option>
        ${allowedTypes.map(function (type) {
          return `<option value="${type.trim()}">${type.trim()}</option>`;
          }).join('')}
      </select>
    </td>
    <td><input type="text" class="form-control" autocomplete="off" name="participant_Name[${companyID}][]" maxlength="100" required="" placeholder="Name"></td>
    <td><input type="text" class="form-control" autocomplete="off" name="participant_Designation[${companyID}][]" maxlength="100" placeholder="Designation"></td>
    <td><input type="text" class="form-control onlyNum" autocomplete="off" maxlength="15" minlength="8" name="participant_Contact[${companyID}][]" required="" placeholder="Contact"></td>
    <td><input type="text" class="form-control onlyNum" autocomplete="off" name="no_of_participant[${companyID}][]" maxlength="3" minlength="1" placeholder="No. of Participant" required=""></td>
    <td><input type="text" class="form-control" autocomplete="off" name="Remark[${companyID}][]" maxlength="200" placeholder="Remark"></td>
    <td><button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)"><i class="fa fa-trash"></i></button></td>
        `;
    table.appendChild(inputRow);
    toggleFields(inputRow.querySelector('select'), companyID);
  }

  function toggleFields(selectElement, companyID) {
    var row = selectElement.closest('tr');
    var type = selectElement.value;
    var nameField = row.querySelector('input[name="participant_Name[' + companyID + '][]"]');
    var designationField = row.querySelector('input[name="participant_Designation[' + companyID + '][]"]');
    var contactField = row.querySelector('input[name="participant_Contact[' + companyID + '][]"]');
    var noOfField = row.querySelector('input[name="no_of_participant[' + companyID + '][]"]');
    var remarkField = row.querySelector('input[name="Remark[' + companyID + '][]"]');

    if (type === 'Team') {
      noOfField.readOnly = false;
      noOfField.required = true;
      noOfField.classList.remove('readonly-style');

      nameField.readOnly = false;
      nameField.required = true;
      nameField.classList.remove('readonly-style');

      remarkField.readOnly = false;
      remarkField.required = false;
      remarkField.classList.remove('readonly-style');

      designationField.readOnly = true;
      designationField.required = false;
      designationField.classList.add('readonly-style');
      designationField.value = ''; // clear

      contactField.readOnly = true;
      contactField.required = false;
      contactField.classList.add('readonly-style');
      contactField.value = ''; // clear

    } else {
      noOfField.readOnly = true;
      noOfField.required = false;
      noOfField.classList.add('readonly-style');
      noOfField.value = ''; // clear

      nameField.readOnly = false;
      nameField.required = true;
      nameField.classList.remove('readonly-style');

      remarkField.readOnly = false;
      remarkField.required = false;
      remarkField.classList.remove('readonly-style');

      designationField.readOnly = false;
      designationField.required = true;
      designationField.classList.remove('readonly-style');

      contactField.readOnly = false;
      contactField.required = true;
      contactField.classList.remove('readonly-style');
    }


  }

  function deleteRow(button) {
    var row = button.parentNode.parentNode;
    row.parentNode.removeChild(row);
  }

  function toggleAttendance(radio) {
    const societyId = radio.name.split('_')[1];
    const presentTable = document.getElementById('presentTable_' + societyId);
    const reasonBox = document.getElementById('reasonBox_' + societyId);
    const reasonText = document.getElementById('reasonText_' + societyId);
    const reasonErr = document.getElementById('reasonErr_' + societyId);

    if (radio.value === "absent") {
      reasonBox.style.display = 'block';
      presentTable.style.display = 'none';
      if (reasonErr) {
        reasonErr.style.display = 'none';
      }
      try {
        const form = parent.$ && parent.$('#trainingMeetingForm');
        if (form && reasonText) {
          parent.$(reasonText).rules('add', {
            required: true,
            messages: {
              required: 'Remark is required when company is absent.'
            }
          });
        }
      } catch (ex) {}
      if (presentTable) {
        const inputs = presentTable.querySelectorAll('input, select, textarea');
        inputs.forEach(el => {
          if (el.type === 'radio' || el.type === 'checkbox') {
            el.checked = false;
          } else if (el.tagName === 'TEXTAREA' || el.tagName === 'SELECT' || el.type === 'text' || el.type === 'number' || el.type === 'tel') {
            el.value = '';
          }
          el.disabled = true;
        });
      }
    } else {
      reasonBox.style.display = 'none';
      presentTable.style.display = 'block';
      if (reasonErr) {
        reasonErr.style.display = 'none';
      }
      try {
        if (parent.$ && reasonText) {
          parent.$(reasonText).rules && parent.$(reasonText).rules('remove', 'required');
        }
      } catch (ex) {}
      if (presentTable) {
        const inputs = presentTable.querySelectorAll('input, select, textarea');
        inputs.forEach(el => {
          el.disabled = false;
        });
      }
    }
  }

  (function initValidateHooks() {
    try {
      if (!(parent.$ && parent.$.fn && parent.$.fn.validate)) return;
      const $form = parent.$('#trainingMeetingForm');
      if (!$form.length) return;
      if (!$form.data('validator')) {
        $form.validate({
          ignore: ':hidden, .ignore-validate'
        });
      } else {
        // Extend ignore to include our custom class in case validator already exists
        try {
          const validator = $form.data('validator');
          if (validator && validator.settings) {
            const currentIgnore = validator.settings.ignore || '';
            if (!/\.ignore-validate/.test(currentIgnore)) {
              validator.settings.ignore = currentIgnore ? (currentIgnore + ', .ignore-validate') : '.ignore-validate';
            }
          }
        } catch (e) {}
      }
      document.querySelectorAll('textarea[id^="reasonText_"]').forEach(function(el) {
        el.addEventListener('input', function() {
          const sid = this.id.split('_')[1];
          const err = document.getElementById('reasonErr_' + sid);
          if (err) {
            err.style.display = 'none';
          }
        });
      });
    } catch (ex) {}
  })();
  document.querySelectorAll('[id^="checkAllParticipants_"]').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
      const companyID = this.id.split('_')[1];
      const checkboxes = document.querySelectorAll(`#presentTable_${companyID} .participant-checkbox`);
      checkboxes.forEach(function(participantCheckbox) {
        participantCheckbox.checked = checkbox.checked;
      });
    });
  });
</script>