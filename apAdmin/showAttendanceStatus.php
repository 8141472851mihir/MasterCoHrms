<?php
extract($_REQUEST);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
if (isset($training_schedule_master_id) && $training_schedule_master_id != '') {
  $q = $d->selectRow(
    "training_schedule_master.training_date, 
         training_schedule_master.host_id,
         training_schedule_master.training_link,
         training_schedule_master.society_id, 
         session_master.session_id, 
         session_master.session_name, 
         session_master.session_days,
         admin_table.admin_name AS host_name",
    "training_schedule_master
         LEFT JOIN session_master ON training_schedule_master.session_id = session_master.session_id
         LEFT JOIN bms_admin_master AS admin_table ON training_schedule_master.host_id = admin_table.admin_id",
    "training_schedule_master.training_schedule_master_id = '$training_schedule_master_id' 
         AND training_schedule_master.schedule_status = '0'"
  );

  $data = mysqli_fetch_assoc($q);

  if ($data) {
    extract($data);
  }
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
            <p class="card-text">Training Date: <?php echo $formattedDate; ?></p>
            <p class="card-text">Training Day: <?php echo $dayName; ?></p>
            <p class="card-text">Session Name: <?php echo $data['session_name']; ?></p>
            <p class="card-text">Recording Link:
              <a href="<?= $data['training_link'] ?>" target="_blank"><?= $data['training_link'] ?></a>
              <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Copy"
                onclick="copyToClipboard('<?= addslashes($row['training_link']) ?>')">
                <i class="fa fa-copy"></i>
              </button>
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card mb-3">
          <div class="card-body">
            <?php
            $selected_societies = explode(',', $data['society_id']);
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
                 training_status_master.onboarding_status,
                 training_attend_master.absent_present,
                 training_attend_master.absent_reason",
                "society_master 
                 INNER JOIN training_module_master 
                 LEFT JOIN training_status_master 
                 ON training_module_master.training_module_id = training_status_master.training_module_id 
                 AND training_status_master.session_id = '$session_id' 
                 AND training_status_master.society_id = society_master.society_id 
                 AND training_status_master.training_schedule_master_id = '$training_schedule_master_id'
                 LEFT JOIN module_training_status_master
                 ON training_module_master.training_module_id = module_training_status_master.module_id
                 AND module_training_status_master.company_id = society_master.society_id LEFT JOIN training_attend_master
                 ON training_attend_master.society_id = society_master.society_id AND training_attend_master.training_schedule_master_id = '$training_schedule_master_id'",
                "society_master.society_status='0' 
                 AND society_master.society_id IN ($selectedSocietiesIn) 
                 AND training_module_master.module_type='0'
                 AND FIND_IN_SET('$session_id', training_module_master.session_id) > 0
                 AND training_module_master.training_module_status='0'"
              );
              while ($societydata = mysqli_fetch_assoc($q2All)) {
                $sid = (int)$societydata['society_id'];
                $key = $societydata['training_module_id'];
                $companyDataBySociety[$sid][$key] = $societydata;
              }
            }

            foreach ($selected_societies as $selected_society_id) {
              echo '<input type="hidden" name="society_id[]" value="' . $selected_society_id . '">';
              
              $companyData = $companyDataBySociety[(int)$selected_society_id] ?? [];
              if (!empty($companyData)) {
                $companyName = reset($companyData)['society_name'];
            ?>

                <div class="mb-4">
                  <h5 class="card-title">Company Name: <?php echo htmlspecialchars($companyName); ?></h5>

                  <?php
                  $isEditMode = false;

                  foreach ($companyData as $module) {
                    $attendanceStatus = $module['absent_present'];
                    $absentReason = $module['absent_reason'];
                    $trainingStatusMasterId = $module['training_status_master_id'];

                    if (!empty($trainingStatusMasterId)) {
                      $isEditMode = true;
                    }
                  }

                  if ($attendanceStatus === '0') { ?>
                    <p class="text-danger"><strong>Absent Reason:</strong> <?php echo htmlspecialchars($absentReason); ?></p>
                  <?php } else { ?>
                    <div class="table-responsive">
                      <table class="table table-bordered mt-3">
                        <thead>
                          <tr>
                            <th>#</th>
                            <!-- <th class="text-center">Training Status</th> -->
                            <th class="text-center">Data Receive Status</th>
                            <th class="text-center">Data Upload Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $index = 1;
                          foreach ($companyData as $module) { ?>
                            <tr>
                              <td><?php echo $module['training_module_name'] ?></td>
                              <!-- <td class="text-center">
                                <?php
                                echo ($module['training_status'] == '1') ? 'Yes' : (($module['training_status'] == '0') ? 'No' : 'Not Applicable');
                                ?>
                              </td> -->

                              <td class="text-center">
                                <?php
                                echo ($module['data_receive_status'] == '1') ? 'Received' : (($module['data_receive_status'] == '0') ? 'Not Received' : 'Not Applicable');
                                ?>
                              </td>

                              <td class="text-center">
                                <?php
                                echo ($module['onboarding_status'] == '1') ? 'Completed' : (($module['onboarding_status'] == '0') ? 'Pending' : 'Not Applicable');
                                ?>
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  <?php } ?>
                </div>
                <hr>
            <?php
              }
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>