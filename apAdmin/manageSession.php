<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Manage Session</h4>
      </div>
      <div class="col-sm-3 col-7">
        <div class="btn-group float-sm-right">
          <a href="javaScript:void();" data-toggle="modal" onclick="addSessionForm()" data-target="#addSession" class="btn btn-primary btn-sm waves-effect waves-light" title="Add Session"> <i class="fa fa-plus mr-1"></i> Add Session</a>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>Sr.No</th>
                    <th>Session Name</th>
                    <th>Session Days</th>
                    <th>Session Day Name</th>
                    <th>Start Time</th>
                    <th>End time</th>
                    <th>Session Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->select(
                    "session_master 
                     LEFT JOIN session_day_master ON session_master.session_day_id = session_day_master.session_day_id AND session_day_master.session_day_status='0'",
                    "session_master.delete_status=0",
                    "ORDER BY session_master.session_id DESC"
                  );
                  while ($data = mysqli_fetch_array($q)) {
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td>
                        <?php echo $data['session_name']; ?>

                      </td>
                      <td>
                        <?php
                        echo $data['session_days'];
                        ?>
                      </td>
                      <td><?php echo $data['session_day_name']; ?></td>
                      <td><?php echo !empty($data['start_time']) ? date("h:i A", strtotime($data['start_time'])) : ''; ?></td>
                      <td><?php echo !empty($data['end_time']) ? date("h:i A", strtotime($data['end_time'])) : ''; ?></td>
                      <td>
                        <?php
                        $buttonClass = ($data['session_status'] == "0") ? 'btn-success-new' : 'btn-danger';
                        $buttonCondition = ($data['session_status'] == "0") ? 'Active' : 'Deactive';
                        $status = ($data['session_status'] == "0") ? 'sessionStatusDeactive' : 'sessionStatusActive';
                        $newStatus = ($data['session_status'] == "0") ? 'sessionStatusActive' : 'sessionStatusDeactive';
                        $newStatusVal = ($data['session_status'] == "0") ? '1' : '0';
                        $statusValue = ($data['session_status'] == "0") ? '0' : '1';
                        ?>

                        <input type="button" class="btn btn-sm pl-1 pr-1 w-50 <?php echo $buttonClass ?>" id="<?php echo 'session_button_' . $data['session_id']; ?>" onclick="changeStatusNew('<?php echo $data['session_id']; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'session_button_' . $data['session_id']; ?>');" data-size="small" value="<?php echo $buttonCondition ?>" />

                      </td>
                      <td>
                        <a href="javaScript:void(0);" class="btn btn-sm btn-primary m-2" title="Add Session" onclick="editSessionForm('<?php echo $data['session_id']; ?>','<?php echo $data['session_name']; ?>','<?php echo $data['session_days']; ?>','<?php echo date('h:i A', strtotime($data['start_time'])); ?>',
       '<?php echo date('h:i A', strtotime($data['end_time'])); ?>','<?php echo $data['session_day_id']; ?>')"><i class="fa fa-pencil"></i></a>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<div class="modal fade" id="addSession">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h4 class="modal-title text-uppercase text-white" id="session">
        </h4>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addSessionForm" action="controller/sessionController.php" method="POST">
          <div class="container mt-4">
            <form action="" method="POST">
              <div class="form-group row">
                <label for="session_name" class="col-sm-3 col-form-label">Session Name <span class="required">*</span></label>
                <div class="col-sm-9">
                  <input type="hidden" name="session_id" id="session_id" value="<?php echo isset($data['session_id']) ? $data['session_id'] : ''; ?>">
                  <input type="text" autocomplete="off" maxlength="40" class="form-control" id="session_name" name="session_name" value="<?php echo isset($data['session_name']) ? $data['session_name'] : ''; ?>" required>
                </div>
              </div>
              <div class="form-group row">
                <label for="session_days" class="col-sm-3 col-form-label">Session Day <span class="required">*</span></label>
                <div class="col-sm-9">
                  <select name="session_days" id="session_days" class="form-control single-select" required>
                    <option value="" selected disabled>-- SELECT --</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                  </select>
                </div>
              </div>

              <div class="form-group row">
                <label for="session_day" class="col-sm-3 col-form-label">Session Day Name</label>
                <div class="col-sm-9">
                  <select class="form-control single-select" name="session_day_id" id="session_day_id" required>
                    <option value="">-- Select --</option>
                    <?php
                    $qt = $d->select("session_day_master", "session_day_status='0'");
                    while ($Data = mysqli_fetch_array($qt)) {
                      $selected = ($Data['session_day_id'] == $session_day_id) ? 'selected' : '';
                    ?>
                      <option value="<?php echo $Data['session_day_id']; ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($Data['session_day_name']); ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
              </div>


              <div class="form-group row">
                <label for="start_time" class="col-sm-3 col-form-label">Start Time <span class="required">*</span></label>
                <div class="col-sm-4">
                <input type="text" class="form-control time-picker-session" required id="start_time" name="start_time" value="<?php echo isset($data['start_time']) ? $data['start_time'] : ''; ?>">
                </div>
                <label for="end_time" class="col-sm-1 col-form-label text-center">End Time <span class="required">*</span></label>
                <div class="col-sm-4">
                <input type="text" class="form-control time-picker-session" required id="end_time" name="end_time" value="<?php echo isset($data['end_time']) ? $data['end_time'] : ''; ?>">
                </div>
              </div>
              <div class="form-footer text-center mt-4">
                <input type="hidden" name="sessionModule" id="sessionModule" value="sessionModule">
                <input type="hidden" name="editId" id="editId" value="">
                <button type="submit" class="btn btn-success">
                  <i class="fa fa-check-square-o"></i>
                  <span id="submitButton">Submit</span>
                </button>
              </div>
            </form>
          </div>
      </div>
    </div>
  </div>