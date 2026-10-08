<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
extract($_REQUEST);
if (isset($training_schedule_master_id) && $training_schedule_master_id != '') {
  $q = $d->selectRow(
    "training_schedule_master.training_date, 
     training_schedule_master.host_id,
     training_schedule_master.society_id, 
    training_schedule_master.training_schedule_master_id, 
     session_master.session_id, 
     session_master.session_name, 
     session_master.session_days,
     admin_table.admin_name AS host_name,
     training_schedule_master.training_link, 
     training_schedule_master.training_password",
    "training_schedule_master
     LEFT JOIN session_master ON training_schedule_master.session_id = session_master.session_id
     LEFT JOIN bms_admin_master AS admin_table ON training_schedule_master.host_id = admin_table.admin_id",
    "training_schedule_master.training_schedule_master_id = '$training_schedule_master_id' 
     AND training_schedule_master.schedule_status = '0'"
  );

  $data = mysqli_fetch_array($q);

  extract($data);
}
?>

<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Meeting Updates</h4>
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <a href="manageTraining" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
      </div>
    </div>

    <!-- Main Meeting Details -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card mb-3">
          <div class="card-body">
            <?php
            $rawDate = $data['training_date'];
            $formattedDate = date('d M Y', strtotime($rawDate));
            $dayName = date('l', strtotime($rawDate));
            ?>
            <h5 class="card-title">Trainer Name: <?php echo $data['host_name']; ?></h5>
            <p class='card-text'>Training Date: <?php echo $formattedDate; ?></p>
            <p class="card-text">Training Day: <?php echo $dayName; ?></p>
            <p class="card-text">Session Name: <?php echo $session_name; ?></p>

            <form action="controller/attendanceStatusController.php" id="comapanyAdd" method="POST">
              <input type="hidden" name="setupSocietyAdd" value="setupSocietyAdd">
              <input type="hidden" name="training_schedule_master_id"
                value="<?php echo $training_schedule_master_id; ?>">
              <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">

              <label for="society_id" class="col-form-label">Company Name <span class="required">*</span></label>
              <select name="society_id[]" class="form-control multiple-select" id="attendanceCompanySelect"
                multiple="multiple" required>
                <?php
                $imploded_societies = $society_id;
                $selected_societies = explode(',', $society_id);
                $q = $d->select("society_master", "society_status='0' AND society_name!='' AND created_on_society_server='1'", "ORDER BY society_id DESC");
                while ($data = mysqli_fetch_array($q)) {
                  $selected = in_array($data['society_id'], $selected_societies) ? 'selected' : '';
                ?>
                  <option value="<?php echo $data['society_id']; ?>" <?php echo $selected; ?>>
                    <?php echo $data['society_name']; ?>
                  </option>
                <?php } ?>
              </select>
              <button type="submit" id="changeButton" class="btn btn-success btn-sm mt-2" style="display: none;">
                <i class="fa fa-check-square-o"></i> CHANGE COMPANY
              </button>
            </form>

          </div>
        </div>
      </div>
    </div>

    <form method="POST" action="controller/attendanceStatusController.php" id="attendanceForm">
      <input type="hidden" name="setupAttendanceAdd" value="setupAttendanceAdd">

      <input type="hidden" name="training_schedule_master_id" value="<?php echo $training_schedule_master_id; ?>">
      <input type="hidden" name="session_id" value="<?php echo $session_id; ?>">
      <input type="hidden" name="csrf" value="csrf">
      <div class="row">
        <div class="col-lg-12">
          <div class="card mb-3">
            <div class="card-body">
              <?php
              $selected_societies = explode(',', $society_id);
              $selectedSocietyIdsInt = array_values(array_filter(array_map('intval', $selected_societies)));
              $selectedSocietiesIn = !empty($selectedSocietyIdsInt) ? implode(',', $selectedSocietyIdsInt) : '0';

              // One query for all selected societies instead of per-society
              $companyDataBySociety = [];
              if (!empty($selectedSocietyIdsInt)) {
                $q2All = $d->selectRow(
                  "society_master.society_name, 
                   society_master.society_id, 
                   training_module_master.training_module_id, 
                   training_module_master.training_module_name,
                  training_module_master.module_type, 
                   module_training_status_master.module_training_status_id,
                   training_status_master.training_status_master_id,
                   training_status_master.training_status,
                   training_status_master.data_receive_status,
                   training_status_master.onboarding_status",
                  "society_master 
                   INNER JOIN training_module_master 
                   LEFT JOIN training_status_master 
                   ON training_module_master.training_module_id = training_status_master.training_module_id 
                   AND training_status_master.session_id = '$session_id' 
                   AND training_status_master.society_id = society_master.society_id 
                   AND training_status_master.training_schedule_master_id = '$training_schedule_master_id'
                   LEFT JOIN module_training_status_master
                   ON training_module_master.training_module_id = module_training_status_master.module_id
                   AND module_training_status_master.company_id = society_master.society_id",
                  "society_master.society_status='0' 
                   AND society_master.society_id IN ($selectedSocietiesIn) 
                   AND training_module_master.module_type='0'
                   AND FIND_IN_SET('$session_id', training_module_master.session_id) > 0
                   AND training_module_master.training_module_status='0'"
                );
                while ($societydata = mysqli_fetch_array($q2All)) {
                  $sid = (int)$societydata['society_id'];
                  $companyDataBySociety[$sid][] = $societydata;
                }
              }

              foreach ($selected_societies as $selected_society_id) {

                echo '<input type="hidden" name="society_id[]" value="' . $selected_society_id . '">';

                $companyData = $companyDataBySociety[(int)$selected_society_id] ?? [];
                // print_r($companyData[]);exit;
                if (!empty($companyData)) {
                  $companyName = $companyData[0]['society_name'];
              ?>

                  <div class="mb-4">
                    <h5 class="card-title">Company Name: <?php echo $companyName; ?></h5>

                    <div class="mb-2">
                      <input type="radio" name="attendance_<?php echo $selected_society_id; ?>" value="present"
                        id="present_<?php echo $selected_society_id; ?>" onchange="toggleAttendance(this)" checked>
                      <label for="present_<?php echo $selected_society_id; ?>">Present</label>

                      <input type="radio" name="attendance_<?php echo $selected_society_id; ?>" value="absent"
                        id="absent_<?php echo $selected_society_id; ?>" onchange="toggleAttendance(this)">
                      <label for="absent_<?php echo $selected_society_id; ?>">Absent</label>
                    </div>

                    <div id="reasonBox_<?php echo $selected_society_id; ?>" class="mb-3 reason-box" style="display: none;">
                      <textarea name="reason_<?php echo $selected_society_id; ?>" id="reasonBox_P<?php echo $selected_society_id; ?>" class="form-control"
                        placeholder="Enter Absent Report"></textarea>
                    </div>

                    <div id="presentTable_<?php echo $selected_society_id; ?>" class="table-responsive"
                      style="display: block;">
                      <table class="table table-bordered mt-3">
                        <thead class="">
                          <tr>
                            <th rowspan="2" class="text-center align-middle">#</th>
                            <!-- <th colspan="3" class="text-center">Training Status</th> -->
                            <th colspan="3" class="text-center">Data Receive Status</th>
                            <th colspan="3" class="text-center">Data Upload Status</th>
                          </tr>
                          <tr>
                            <!-- <th>Yes</th>
                            <th>No</th>
                            <th>N/A</th> -->
                            <th>Received</th>
                            <th>Pending</th>
                            <th>N/A</th>
                            <th>Completed</th>
                            <th>Pending</th>
                            <th>N/A</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $isEditMode = false;
                          foreach ($companyData as $index => $module) {
                            $module_training_status_id = $module['module_training_status_id'];
                            $moduleId = $module['training_module_id'];
                            $training_status_master_id = $module['training_status_master_id'];
                            $training_status_old = $module['training_status'];
                            $data_receive_status_old = $module['data_receive_status'];
                            $onboarding_status_old = $module['onboarding_status'];

                            if (!empty($training_status_master_id)) {
                              $isEditMode = true;
                            }
                          ?>
                            <tr>
                              <td>
                                <div class="training_status">
                                  <?php echo $module['training_module_name']; ?>
                                  <input type="hidden" name="old_id_<?php echo $moduleId; ?>"
                                    value="<?php echo $training_status_master_id; ?>">
                                  <input type="hidden"
                                    name="training_module_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>"
                                    value="<?php echo $module_training_status_id; ?>">
                                  <input type="hidden" name="company_module_ids[<?php echo $selected_society_id; ?>][]"
                                    value="<?php echo $moduleId; ?>">
                                </div>
                              </td>

                              <?php
                              $disableTraining = in_array($training_status_old, ["1", "2"]) ? 'disabled' : '';
                              $disableData = in_array($data_receive_status_old, ["1", "2"]) ? 'disabled' : '';
                              $disableOnboarding = in_array($onboarding_status_old, ["1", "2"]) ? 'disabled' : '';
                              ?>

                              <!-- <td>
                                <input type="radio"
                                  id="user_training_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>_1"
                                  name="user_training_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>"
                                  value="1" <?php echo ($training_status_master_id != '' && $training_status_old == "1") ? "checked" : ""; ?> <?php echo $disableTraining; ?>
                                  onclick="checkNA('<?php echo $moduleId; ?>', '<?php echo $selected_society_id; ?>')">
                              </td>
                              <td>
                                <input type="radio"
                                  id="user_training_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>_0"
                                  name="user_training_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>"
                                  value="0" <?php echo ($training_status_master_id != '' && $training_status_old == "0") ? "checked" : ""; ?> <?php echo $disableTraining; ?>
                                  onclick="checkNA('<?php echo $moduleId; ?>', '<?php echo $selected_society_id; ?>')">
                              </td>
                              <td>
                                <input type="radio"
                                  id="user_training_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>_2"
                                  name="user_training_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>"
                                  value="2" <?php echo ($training_status_master_id != '' && $training_status_old == "2") ? "checked" : ""; ?> <?php echo $disableTraining; ?>
                                  onclick="checkNA('<?php echo $moduleId; ?>', '<?php echo $selected_society_id; ?>')">
                              </td> -->

                              <td>
                                <input type="radio"
                                  id="user_data_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>_1"
                                  name="user_data_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>"
                                  value="1" <?php echo ($training_status_master_id != '' && $data_receive_status_old == "1") ? "checked" : ""; ?> <?php echo $disableData; ?>>
                              </td>
                              <td>
                                <input type="radio"
                                  id="user_data_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>_0"
                                  name="user_data_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>"
                                  value="0" <?php echo ($training_status_master_id != '' && $data_receive_status_old == "0") ? "checked" : ""; ?> <?php echo $disableData; ?>>
                              </td>
                              <td>
                                <input type="radio"
                                  id="user_data_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>_2"
                                  name="user_data_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>"
                                  value="2" <?php echo ($training_status_master_id != '' && $data_receive_status_old == "2") ? "checked" : ""; ?> <?php echo $disableData; ?>>
                              </td>

                              <td>
                                <input type="radio"
                                  id="user_onboarding_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>_1"
                                  name="user_onboarding_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>"
                                  value="1" <?php echo ($training_status_master_id != '' && $onboarding_status_old == "1") ? "checked" : ""; ?> <?php echo $disableOnboarding; ?>>
                              </td>
                              <td>
                                <input type="radio"
                                  id="user_onboarding_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>_0"
                                  name="user_onboarding_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>"
                                  value="0" <?php echo ($training_status_master_id != '' && $onboarding_status_old == "0") ? "checked" : ""; ?> <?php echo $disableOnboarding; ?>>
                              </td>
                              <td>
                                <input type="radio"
                                  id="user_onboarding_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>_2"
                                  name="user_onboarding_status_<?php echo $selected_society_id; ?>_<?php echo $moduleId; ?>"
                                  value="2" <?php echo ($training_status_master_id != '' && $onboarding_status_old == "2") ? "checked" : ""; ?> <?php echo $disableOnboarding; ?>>
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div class="text-right mt-3">
                    <a href="javascript:void(0);" onclick="openScheduleMeetingModal(<?= $selected_society_id ?>, <?= $host_id ?>)"
                      class="btn btn-sm btn-primary">
                      Schedule Meeting
                    </a>
                  </div>
                  <div id="next_meeting_<?= $selected_society_id ?>">
                    <input type="hidden" name="next_meeting_session_id_[<?php echo $selected_society_id ?>]" value="">
                    <input type="hidden" name="next_meeting_id_[<?php echo $selected_society_id ?>]" value="">
                    <input type="hidden" name="next_meeting_session_date_[<?php echo $selected_society_id ?>]" value="">
                    <span id="next_meeting_view_society_id_<?php echo $selected_society_id; ?>"></span>
                    <span id="next_meeting_view_session_id_<?php echo $selected_society_id; ?>"></span>
                  </div>

                  <hr>
              <?php
                }
              }
              ?>
              <div class="col-form-label">
                <label for="training_link">Recording Link</label>
                <input type="text" class="form-control" id="training_link" autocomplete="off" name="training_link"
                  maxlength="50" placeholder="Recording Link"
                  value="<?php echo isset($training_link) ? $training_link : ""; ?>">
              </div>

              <div class="col-form-label">
                <label for="training_password">Training password</label>
                <input type="text" class="form-control" id="training_password" autocomplete="off"
                  name="training_password" maxlength="150" placeholder="Training password"
                  value="<?php echo isset($training_password) ? $training_password : ""; ?>">
              </div>
              <div class="text-right mt-3">
                <a href="javascript:void(0);" onclick="openScheduleMeetingModal('<?= $imploded_societies ?>', <?= $host_id ?>,'1')"
                  class="btn btn-sm btn-primary">
                  Schedule Next Meeting
                </a>
              </div>
              <div class="text-center mb-3">
                <button type="submit" class="btn btn-success mt-3">
                  <?php echo $isEditMode ? 'UPDATE' : 'Submit'; ?>
                </button>
              </div>
            </div>

          </div>
        </div>
    </form>
  </div>
</div>
<div class="modal fade" id="scheduleMeeting">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h4 class="modal-title text-white">
          <span id="modalTitle">Schedule Meeting</span>
        </h4>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="scheduleMeetingValidation" action="controller/trainingController.php" method="post">
          <div class="society_div d-none">
            <label for="reschedule_meeting_companies" class="col-form-label">Company Name <span class="required">*</span></label>

            <select name="reschedule_meeting_companies[]" class="form-control multiple-select d-none" id="reschedule_meeting_companies" multiple="multiple" required>
              <?php
              $in_clause = implode(',', $selected_societies);
              $q = $d->select("society_master", "society_status='0' AND society_name!='' AND created_on_society_server='1' AND society_id IN ('$in_clause')", "ORDER BY society_id DESC");
              while ($data = mysqli_fetch_array($q)) {
                $selected = in_array($data['society_id'], $selected_societies) ? 'selected' : '';
              ?>
                <option value="<?php echo $data['society_id']; ?>" <?php echo $selected; ?>>
                  <?php echo $data['society_name']; ?>
                </option>
              <?php } ?>
            </select>
          </div>
          <input type="hidden" id="is_multiple" name="is_multiple" value="0">
          <input type="hidden" id="society_id" name="society_id">
          <input type="hidden" id="host_id" name="host_id">
          <div class="form-group row px-2">
            <div class="col-md-6">
              <label for="training_date" class="col-form-label">Training Date <span class="text-danger">*</span></label>
              <input type="text" class="form-control autoclose-datepicker" name="training_date" id="training_date"
                placeholder="Training Date" readonly required value="" onchange="getMeetingName()">
            </div>
            <div class="col-md-6">
              <label for="session_id" class="col-form-label">Session <span class="required">*</span></label>
              <select class="form-control single-select" name="session_id" id="session_id" required
                onchange="setSessionTimes();getMeetingName();">
                <option value="">-- Select --</option>
                <?php
                $qt = $d->select("session_master", "session_status='0'");
                while ($Data = mysqli_fetch_array($qt)) {
                  $session_day_id = isset($Data['session_day_id']) ? $Data['session_day_id'] : 'N/A';
                ?>
                  <option value="<?php echo $Data['session_id']; ?>" data-session-day-id="<?php echo $session_day_id; ?>"
                    data-start-time="<?php echo $Data['start_time']; ?>" data-end-time="<?php echo $Data['end_time']; ?>"
                    data-session-name="<?php echo $Data['session_name']; ?>">
                    <?php echo $Data['session_name'] . '(' . $Data['session_days'] . ' - ' . date("h:i A", strtotime($Data['start_time'])) . ' - ' . date("h:i A", strtotime($Data['end_time'])) . ')'; ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <input type="hidden" name="session_day_id" id="session_day_id">
            <input type="hidden" name="start_time" id="start_time">
            <input type="hidden" name="end_time" id="end_time">
          </div>
          <input type="hidden" id="training_schedule_master_id" name="training_schedule_master_id">

          <div class="form-group">
            <div class="col-md-12">
              <label class="font-weight-bold">Meeting Names:</label>
              <input type="hidden" id="meeting_name" name="meeting_name">
              <div class="row justify-content-center mx-2">
                <div class="col-12 row justify-content-center" id="meeting_list"></div>
                <div class="col-md-6 mb-3 d-none" id="add_new_meeting_div">
                  <label class="btn btn-outline-primary w-100 text-center p-3">
                    <input type="radio" name="selected_meeting" value="new_meeting" class="mr-2">
                    <span>Add New Meeting</span>
                  </label>
                </div>
              </div>
              <div class="text-center mt-3">
                <input type="hidden" name="scheduleNextMeeting" id="scheduleNextMeeting" value="scheduleNextMeeting">
                <button type="submit" id="submitMeeting" class="btn btn-success">Submit</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function setSessionTimes() {
    const sessionSelect = document.getElementById('session_id');
    const selectedOption = sessionSelect.options[sessionSelect.selectedIndex];

    if (selectedOption) {
      document.getElementById("session_day_id").value = selectedOption.getAttribute("data-session-day-id");
      const startTime = selectedOption.getAttribute('data-start-time');
      const endTime = selectedOption.getAttribute('data-end-time');

      document.getElementById('start_time').value = startTime || '';
      document.getElementById('end_time').value = endTime || '';
    }
  }

  function toggleAttendance(radio) {
    const societyId = radio.name.split('_')[1];
    const presentTable = document.getElementById('presentTable_' + societyId);
    const reasonBox = document.getElementById('reasonBox_' + societyId);

    if (radio.value === "absent") {
      reasonBox.style.display = 'block';
      presentTable.style.display = 'none';
    } else {
      reasonBox.style.display = 'none';
      presentTable.style.display = 'block';
    }
  }

  function checkNA(moduleId, societyId) {
    const trainingNA = document.querySelector(`#user_training_status_${societyId}_${moduleId}_2`).checked;
    const dataStatus = document.querySelectorAll(`[id^="user_data_status_${societyId}_${moduleId}"]`);
    const onboardingStatus = document.querySelectorAll(`[id^="user_onboarding_status_${societyId}_${moduleId}"]`);

    if (trainingNA) {
      dataStatus.forEach(el => {
        if (el.value === "2") {
          el.checked = true;
        }
        el.disabled = el.value !== "2";
      });

      onboardingStatus.forEach(el => {
        if (el.value === "2") {
          el.checked = true;
        }
        el.disabled = el.value !== "2";
      });
    } else {
      dataStatus.forEach(el => el.disabled = false);
      onboardingStatus.forEach(el => el.disabled = false);
    }
  }

  function openScheduleMeetingModal(society_id, host_id, type = '0') {
    $("#scheduleMeeting").modal("show");

    if (type == '0') {

      $("#is_multiple").val('0');
      $(".society_div").addClass('d-none');
      $("#add_new_meeting_div").addClass('d-none');
      $("#society_id").val(society_id);
      
    } else {
      $("#is_multiple").val('1');
      $("#add_new_meeting_div").removeClass('d-none');
      $(".society_div").removeClass('d-none');
    }
    $("#host_id").val(host_id);
  }

  function getMeetingName() {
    const sessionId = document.getElementById('session_id').value;
    const trainingDate = document.getElementById('training_date').value;
    const society_id = document.getElementById('society_id').value;

    if (sessionId && trainingDate) {
      $.ajax({
        url: "controller/trainingController.php",
        type: "POST",
        data: {
          action: "fetchMeeting",
          session_id: sessionId,
          trainingDate: trainingDate,
          society_id: society_id,
          csrf: csrf
        },
        success: function(response) {
          let data = JSON.parse(response);
          let meetingList = document.getElementById('meeting_list');
          meetingList.innerHTML = "";
          let meetingsContent = "";
          data.forEach((item, index) => {
            let radioId = `meeting_${index}`;
            let isDisabled = item.flag === "1" ? "disabled" : "";
            let labelClass = item.flag === "1" ? "disabled text-muted" : "";

            let meetingItem = `
                        <div class="col-md-6 mb-3">
                            <label class="btn btn-outline-primary w-100 text-center p-3 ${labelClass}">
                                <input type="radio" id="${radioId}" name="selected_meeting" 
                                       value="${item.meeting_name}" data-id="${item.training_schedule_master_id}" 
                                       class="mr-2" ${isDisabled}>
                                ${item.meeting_name} - Host: ${item.host_name}
                            </label>
                        </div>`;
            meetingsContent += meetingItem;
          });

          meetingList.innerHTML = meetingsContent;
          $("input[name='selected_meeting']").change(function() {
            $("#meeting_name").val($(this).val());
            $("#training_schedule_master_id").val($(this).data("id"));
            $("#submitMeeting").prop("disabled", false);
          });
        },
        error: function() {
          // console.error("Error fetching meetings.");
        }
      });
    }
  }
</script>