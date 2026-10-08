
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-3">
        <h4 class="page-title">Manage Setup</h4>
      </div>

      <div class="col-sm-3">
        <form action="" method="get" accept-charset="utf-8">
          <input type="hidden" name="session_name" value="<?php echo $_GET['session_name']; ?>">
          <select type="text" required="" id="host_name" onchange="this.form.submit()"
            class="form-control single-select" name="host_name">
            <option value="all">-- All --</option>
            <?php
            $result = $d->select("bms_admin_master", "bms_admin_master.role_id!=1 AND bms_admin_master.active_status='0'");
            while ($row = mysqli_fetch_array($result)) {
              $selected = (isset($_GET['host_name']) && $_GET['host_name'] == $row['admin_id']) ? 'selected' : '';
              echo "<option value=\"{$row['admin_id']}\" $selected>{$row['admin_name']}</option>";
            } ?>
          </select>
        </form>
      </div>

      <div class="col-sm-3">
        <form action="" method="get" accept-charset="utf-8">
          <input type="hidden" name="host_name" value="<?php echo $_GET['host_name']; ?>">
          <select type="text" required="" id="session_name" onchange="this.form.submit()"
            class="form-control single-select" name="session_name">
            <option value="all">-- All --</option>
            <?php
            $qt = $d->select("session_master", "session_status='0'");
            while ($Data = mysqli_fetch_array($qt)) {
              $selected = (isset($_GET['session_name']) && $_GET['session_name'] == $Data['session_id']) ? 'selected' : '';
              echo "<option value=\"{$Data['session_id']}\" $selected>";
              echo $Data['session_name'] . '(' . $Data['session_days'] . ' - ' . date("h:i A", strtotime($Data['start_time'])) . ' - ' . date("h:i A", strtotime($Data['end_time'])) . ')';
              echo "</option>";
            }
            ?>
          </select>
        </form>
      </div>

      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <a href="scheduleTraining" class="btn btn-primary btn-sm"><i class="fa fa-plus mr-1"></i>Add Schedule</a>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <ul class="nav nav-tabs nav-tabs-info nav-justified" id="trainingTabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="today-tab" data-toggle="tab" href="#today" role="tab" aria-controls="today"
                aria-selected="true">Today</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="upcoming-tab" data-toggle="tab" href="#upcoming" role="tab"
                aria-controls="upcoming" aria-selected="false">Upcoming</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="previous-tab" data-toggle="tab" href="#previous" role="tab"
                aria-controls="previous" aria-selected="false">Previous</a>
            </li>
          </ul>

          <?php
          $where = "";
          if (isset($_GET["session_name"]) && $_GET["session_name"] != "" && $_GET["session_name"] != "all") {
            $session_name = $d->sanitizeReportFilterIdAsInt($_GET["session_name"]);
            $where = " AND ts.session_id='$session_name'";
          }
          if (isset($_GET["host_name"]) && $_GET["host_name"] != "" && $_GET["host_name"] != "all") {
            $host_name = $d->sanitizeReportFilterIdAsInt($_GET["host_name"]);
            $where .= " AND ts.host_id='$host_name'";
          }
          ?>

          <div class="tab-content" id="trainingTabsContent">
            <div class="tab-pane fade show active" id="today" role="tabpanel" aria-labelledby="today-tab">
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example" class="table table-bordered">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Action</th>
                        <th>Training Status</th>
                        <th>Setup Meeting Name</th>
                        <th>Training Date</th>
                        <th>Training Day</th>
                        <th>Training Time</th>
                        <th>Trainer Name</th>
                        <th>Total Company</th>
                        <th>Session</th>
                        <th>Scheduled By</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $today = date('Y-m-d');
                      $q_today = $d->selectRow(
                        "ts.training_date, 
                     ts.training_link, 
                     ts.start_time,ts.end_time,
                     ts.society_id,ts.meeting_status,
                     ts.training_schedule_master_id,ts.setup_meeting_name,
                     ts.host_id,
                     sm.session_name,
                     admin_table.admin_name AS scheduled_by, 
                     host_table.admin_name AS host_name",
                        "training_schedule_master AS ts
                     LEFT JOIN session_master AS sm ON ts.session_id = sm.session_id
                     LEFT JOIN bms_admin_master AS admin_table ON ts.created_by = admin_table.admin_id
                     LEFT JOIN bms_admin_master AS host_table ON ts.host_id = host_table.admin_id",
                        "ts.schedule_status = '0' AND ts.training_date = '$today' $where",
                        "ORDER BY ts.training_schedule_master_id DESC"
                      );

                      $i = 1;
                      while ($row = mysqli_fetch_array($q_today)) {
                        $formattedDate = date('d M Y', strtotime($row['training_date']));
                        $dayName = date('l', strtotime($row['training_date']));
                        $start_time = isset($row['start_time']) ? date("h:i A", strtotime($row['start_time'])) : '';
                        $end_time = isset($row['end_time']) ? date("h:i A", strtotime($row['end_time'])) : '';

                        if ($start_time && $end_time) {
                          $time = "$start_time - $end_time";
                        } elseif ($start_time) {
                          $time = "$start_time - N/A";
                        } elseif ($end_time) {
                          $time = "N/A - $end_time";
                        } else {
                          $time = "Time not available";
                        }

                        $societies = explode(',', $row['society_id']);
                        $societies = array_filter($societies);
                        $totalCompanies = count($societies);

                        ?>
                        <tr>
                          <td><?= $i++ ?></td>
                          <td>

                            <form method="GET" action="manageAttendanceStatus">
                              <input type="hidden" name="training_schedule_master_id"
                                value="<?= $row['training_schedule_master_id'] ?>">
                              <?php if ($row['meeting_status'] != '1') { ?>
                                <button class="btn btn-sm btn-primary">update meeting</button>
                              <?php } else { ?>
                                <button class="btn btn-sm btn-primary">view meeting</button>
                              <?php } ?>
                            </form>
                          </td>
                          <td><?php if ($row['meeting_status'] != '1') {
                            echo "<span class='text-danger'>Pending</span>";
                          } else {
                            echo "<span class='text-success'>Completed</span>";
                          } ?></td>
                          <td><?= $row['setup_meeting_name'] ?></td>
                          <td><?= $formattedDate ?></td>
                          <td><?= $dayName ?></td>
                          <td><?= $time ?></td>
                          <td><?= $row['host_name'] ?>
                          <?php if ($row['meeting_status'] == '0') { ?>
                              <button data-toggle="modal" data-target="#changeTrainerModel" title="Change Trainer?"
                                class="btn text-warning btn-sm ml-2"
                                onclick="changeTrainerid('<?php echo $row['host_id']; ?>','<?php echo $row['training_schedule_master_id']; ?>')">
                                <i class="fa fa-pencil"></i>
                              </button>
                            <?php } ?>
                            </td>
                          <td><?= $totalCompanies ?></td>
                          <td><?= $row['session_name'] ?></td>
                          <td><?= $row['scheduled_by'] ?></td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Upcoming Tab -->
            <div class="tab-pane fade" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example" class="table table-bordered">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Setup Meeting Name</th>
                        <th>Training Date</th>
                        <th>Training Day</th>
                        <th>Training Time</th>
                        <th>Trainer Name</th>
                        <th>Total Company</th>
                        <th>Session</th>
                        <th>Scheduled By</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $q_upcoming = $d->selectRow(
                        "ts.training_date, 
                     ts.training_link,ts.meeting_status,
                     ts.society_id,ts.setup_meeting_name,
                      ts.start_time,ts.end_time,
                     ts.training_schedule_master_id, 
                     ts.host_id,
                     sm.session_name,
                     admin_table.admin_name AS scheduled_by, 
                     host_table.admin_name AS host_name",
                        "training_schedule_master AS ts
                     LEFT JOIN session_master AS sm ON ts.session_id = sm.session_id
                     LEFT JOIN bms_admin_master AS admin_table ON ts.created_by = admin_table.admin_id
                     LEFT JOIN bms_admin_master AS host_table ON ts.host_id = host_table.admin_id",
                        "ts.schedule_status = '0' AND ts.training_date > '$today' $where",
                        "ORDER BY ts.training_schedule_master_id DESC"
                      );
                      
                      $i = 1;
                      while ($row = mysqli_fetch_array($q_upcoming)) {
                        $formattedDate = date('d M Y', strtotime($row['training_date']));
                        $dayName = date('l', strtotime($row['training_date']));
                        $start_time = isset($row['start_time']) ? date("h:i A", strtotime($row['start_time'])) : '';
                        $end_time = isset($row['end_time']) ? date("h:i A", strtotime($row['end_time'])) : '';

                        if ($start_time && $end_time) {
                          $time = "$start_time - $end_time";
                        } elseif ($start_time) {
                          $time = "$start_time - N/A";
                        } elseif ($end_time) {
                          $time = "N/A - $end_time";
                        } else {
                          $time = "Time not available";
                        }

                        $societies = explode(',', $row['society_id']);
                        $societies = array_filter($societies);
                        $totalCompanies = count($societies);
                        ?>
                        <tr>
                          <td><?= $i++ ?></td>
                          <td><?= $row['setup_meeting_name'] ?></td>
                          <td><?= $formattedDate ?></td>
                          <td><?= $dayName ?></td>
                          <td><?= $time ?></td>
                          <td><?= $row['host_name'] ?>
                            <?php if ($row['meeting_status'] == '0') { ?>
                              <button data-toggle="modal" data-target="#changeTrainerModel" title="Change Trainer?"
                                class="btn text-warning btn-sm ml-2"
                                onclick="changeTrainerid('<?php echo $row['host_id']; ?>','<?php echo $row['training_schedule_master_id']; ?>')">
                                <i class="fa fa-pencil"></i>
                              </button>
                            <?php } ?>
                          </td>
                          <td><?= $totalCompanies ?></td>
                          <td><?= $row['session_name'] ?></td>
                          <td><?= $row['scheduled_by'] ?></td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Previous Tab -->
            <div class="tab-pane fade" id="previous" role="tabpanel" aria-labelledby="previous-tab">
              <div class="card-body">
                <div class="table-responsive">
                  <table id="example" class="table table-bordered">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Action</th>
                        <th>Training Status</th>
                        <th>Setup Meeting Name</th>
                        <th>Training Date</th>
                        <th>Training Day</th>
                        <th>Training Time</th>
                        <th>Trainer Name</th>
                        <th>Total Company</th>
                        <th>Recording Link</th>
                        <th>Recording Link Paswword</th>
                        <th>Session</th>
                        <th>Scheduled By</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $q_previous = $d->selectRow(
                        "ts.training_date, 
                     ts.training_link, 
                     ts.society_id,ts.setup_meeting_name,
                      ts.start_time,ts.end_time,
                     ts.training_schedule_master_id,
                     ts.training_password, 
                     ts.meeting_status, 
                     sm.session_name,
                     admin_table.admin_name AS scheduled_by, 
                     host_table.admin_name AS host_name",
                        "training_schedule_master AS ts
                     LEFT JOIN session_master AS sm ON ts.session_id = sm.session_id
                     LEFT JOIN bms_admin_master AS admin_table ON ts.created_by = admin_table.admin_id
                     LEFT JOIN bms_admin_master AS host_table ON ts.host_id = host_table.admin_id",
                        "ts.schedule_status = '0' AND ts.training_date < '$today' $where",
                        "ORDER BY ts.training_schedule_master_id DESC"
                      );

                      $i = 1;
                      while ($row = mysqli_fetch_array($q_previous)) {
                        $formattedDate = date('d M Y', strtotime($row['training_date']));
                        $dayName = date('l', strtotime($row['training_date']));
                        $start_time = isset($row['start_time']) ? date("h:i A", strtotime($row['start_time'])) : '';
                        $end_time = isset($row['end_time']) ? date("h:i A", strtotime($row['end_time'])) : '';

                        if ($start_time && $end_time) {
                          $time = "$start_time - $end_time";
                        } elseif ($start_time) {
                          $time = "$start_time - N/A";
                        } elseif ($end_time) {
                          $time = "N/A - $end_time";
                        } else {
                          $time = "Time not available";
                        }
                        $societies = explode(',', $row['society_id']);
                        $societies = array_filter($societies);
                        $totalCompanies = count($societies);
                        ?>
                        <tr>
                          <td><?= $i++ ?></td>
                          <td class="d-flex">
                            <form class="px-2" method="GET" action="manageAttendanceStatus">
                              <input type="hidden" name="training_schedule_master_id"
                                value="<?= $row['training_schedule_master_id'] ?>">
                              <?php if ($row['meeting_status'] != '1') { ?>
                                <button class="btn btn-sm btn-primary">update meeting</button>
                              <?php } else { ?>
                                <button class="btn btn-sm btn-primary">view meeting</button>
                              <?php } ?>
                            </form>
                            <form method="POST" action="showAttendanceStatus">
                              <input type="hidden" name="training_schedule_master_id"
                                value="<?= $row['training_schedule_master_id'] ?>">
                              <button class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></button>
                            </form>
                          </td>
                          <td><?php if ($row['meeting_status'] != '1') {
                            echo "<span class='text-danger'>Pending</span>";
                          } else {
                            echo "<span class='text-success'>Completed</span>";
                          } ?></td>
                          <td><?= $row['setup_meeting_name'] ?></td>
                          <td><?= $formattedDate ?></td>
                          <td><?= $dayName ?></td>
                          <td><?= $time ?></td>
                          <td><?= $row['host_name'] ?></td>
                          <td><?= $totalCompanies ?></td>
                          <td>
                            <?php if (!empty($row['training_link'])): ?>
                              <a href="<?= $row['training_link'] ?>" target="_blank"><?= $row['training_link'] ?></a>
                              <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip"
                                data-placement="top" title="Copy"
                                onclick="copyToClipboard('<?= addslashes($row['training_link']) ?>')">
                                <i class="fa fa-copy"></i>
                              </button>
                            <?php else: ?>
                              <span class="text-muted">No Link Available</span>
                              <!--  -->
                            <?php endif; ?>
                          </td>

                          <td>
                            <?php if (!empty($row['training_password'])): ?>
                              <span><?= htmlspecialchars($row['training_password']) ?></span>
                              <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip"
                                data-placement="top" title="Copy"
                                onclick="copyToClipboard('<?= addslashes($row['training_password']) ?>')">
                                <i class="fa fa-copy"></i>
                              </button>

                            <?php else: ?>
                              <span class="text-muted">No Password Available</span>
                            <?php endif; ?>
                          </td>

                          <td><?= $row['session_name'] ?></td>
                          <td><?= $row['scheduled_by'] ?></td>
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


    <div class="modal fade" id="changeTrainerModel">
      <div class="modal-dialog modal-md">
        <div class="modal-content border-primary">
          <div class="modal-header bg-primary">
            <h5 class="modal-title text-white">Change Trainer</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="card-body">
              <form id="" method="POST" action="controller/trainingController.php">
                <div class="row">
                  <div class="col-md-12">
                    <label for="host_id" class="col-form-label">Trainer</label>
                    <select name="host_id" id="host_id" class="form-control single-select" required>
                      <option value="">-- Select Trainer --</option>
                      <?php
                      $trainers = $d->select("bms_admin_master", "role_id != 1 AND active_status='0'");
                      while ($row2 = mysqli_fetch_assoc($trainers)) {
                        ?>
                        <option value="<?php echo htmlspecialchars($row2['admin_id']); ?>">
                          <?php echo htmlspecialchars($row2['admin_name']); ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="form-footer text-center mt-3">
                  <input type="hidden" name="changeScheduleTrainers" value="changeScheduleTrainers">
                  <input type="hidden" name="training_schedule_master_id" id="training_schedule_master_id_edit">
                  <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check-square-o"></i>
                    Update</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>

function changeTrainerid(id, trainingScheduleMasterId) {
    $("#host_id").val(id).trigger('change');
    $("#training_schedule_master_id_edit").val(trainingScheduleMasterId);
  }

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