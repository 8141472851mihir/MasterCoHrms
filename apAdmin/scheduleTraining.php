<?php
extract($_REQUEST);
if (isset($editScheduleTraining) && $editScheduleTraining == 'editScheduleTraining' && isset($training_schedule_master_id) && $training_schedule_master_id != '') {
  $q = $d->selectRow(
    "society_id,training_date,
        session_id,host_id,training_link,start_time,end_time",
    "training_schedule_master",
    "training_schedule_master.training_schedule_master_id='$training_schedule_master_id' AND schedule_status ='0'"
  );
  $data = mysqli_fetch_assoc($q);
  extract($data);
  $modules =$d->selectRow("training_module_master.training_module_id,training_module_master.training_module_name",
  "training_module_master LEFT JOIN session_master ON session_master.session_id=training_module_master.session_id","1");
}
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Schedule Setup</h4>
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <a href="manageTraining" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="scheduleTrainingValidation" action="controller/trainingController.php" method="post">
              <div class="form-group row">
                <div class="col-md-4">
                  <label for="society_id" class="col-form-label">Company Name</label>
                  <select name="society_id[]" class="form-control multiple-select" placeholder="company_name"
                    multiple="multiple">
                    <?php
                    $selected_societies = explode(',', $society_id);
                    $q = $d->select(
                      "society_master", 
                      "society_status='0' AND society_name != '' AND (created_on_society_server = 1)",
                      "ORDER BY society_id DESC"
                  );
                  
                    while ($data = mysqli_fetch_array($q)) {
                      $selected = in_array($data['society_id'], $selected_societies) ? 'selected' : '';
                      ?>
                      <option value="<?php echo $data['society_id']; ?>" <?php echo $selected; ?>>
                        <?php echo $data['society_name']; ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="host_name" class="col-form-label">Trainer Name <span class="required">*</span></label>
                  <select name="host_name" class="form-control single-select" required>
                    <?php
                    echo "<option value='' selected disabled>-- Select Admin --</option>";
                    $result = $d->select("bms_admin_master", "(bms_admin_master.role_id!=1) AND active_status='0'");
                    while ($row = mysqli_fetch_array($result)) {
                      $selected = ($row['admin_name'] == $host_name) ? 'selected' : '';
                      ?>
                      <option value="<?php echo $row['admin_id']; ?>" <?php echo $selected; ?>>
                        <?php echo $row['admin_name']; ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="training_date" class="col-form-label">Training Date <span
                      class="text-danger">*</span></label>
                  <input type="text" class="form-control autoclose-datepicker" name="training_date" id="training_date"
                    placeholder="Training Date" readonly required value="<?php echo $training_date; ?>">
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-4">
                  <label for="session_id" class="col-form-label">Session <span class="required">*</span></label>
                  <select class="form-control single-select" name="session_id" id="session_id" required
                    onchange="setSessionTimes();getModule()">
                    <option value="">-- Select --</option>
                    <?php
                    $qt = $d->select("session_master", "session_status='0'");
                    while ($Data = mysqli_fetch_array($qt)) {
                      $session_day_id = isset($Data['session_day_id']) ? $Data['session_day_id'] : 'N/A'; 
                      $selected = ($Data['session_id'] == $session_id) ? 'selected' : '';
                      ?>
                      <option value="<?php echo $Data['session_id']; ?>"
                        data-session-day-id="<?php echo $session_day_id; ?>"
                        data-start-time="<?php echo $Data['start_time']; ?>"
                        data-end-time="<?php echo $Data['end_time']; ?>"
                        data-session-name="<?php echo $Data['session_name']; ?>"
                        <?php echo $selected; ?>>
                        <?php echo $Data['session_name'] . '(' . $Data['session_days'] . ' - ' . date("h:i A", strtotime($Data['start_time'])) . ' - ' . date("h:i A", strtotime($Data['end_time'])) . ')'; ?>
                      </option>
                    <?php } ?>
                  </select>

                </div>
                <input type="hidden" name="session_day_id" id="session_day_id">
                <input type="hidden" name="start_time" id="start_time">
                <input type="hidden" name="end_time" id="end_time">
              </div>

              <div class="form-group row">
                <div class="col-md-4">
                  <label>Covered Modules-</label>
                  <input type="hidden" class="form-control form-control-sm" id="training_module_name" name="training_module_name" readonly="">
                  <span id="module_label"></span>
                </div>
              </div>

              <div class="form-footer text-center">
                <input type="hidden" name="scheduleTraining" value="scheduleTraining">
                <?php if ($training_schedule_master_id != "") { ?>
                  <input type="hidden" name="training_schedule_master_id"
                    value="<?php echo $training_schedule_master_id; ?>">
                  <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i>UPDATE</button>
                <?php } else { ?>
                  <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i>ADD</button>
                <?php } ?>
                
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="assets/js/jquery.min.js"></script>

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

  function getModule() {
    const sessionId = document.getElementById('session_id').value;
    if (sessionId) {
      $.ajax({
      url: "controller/trainingController.php",
      cache: false,
      type: "POST",
      data: {getModule:'getModule',session_id:sessionId,csrf:csrf},
      success: function(response){
        let modules = JSON.parse(response);
        console.log(modules);
    document.getElementById('module_label').innerText = modules;
      },
      error: function(xhr, status, error) {
          console.error("Error fetching modules: ", error);
      }
    });
    }
}
</script>

