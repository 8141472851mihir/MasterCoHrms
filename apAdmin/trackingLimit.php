<?php
$mobileAiCredentials = array();
$mobileCredQ = $d->select('ai_credentials_master', "ai_status='1'", 'ORDER BY debug_key ASC, ai_credentials_id ASC');
if ($mobileCredQ && mysqli_num_rows($mobileCredQ) > 0) {
  while ($mobileCredRow = mysqli_fetch_assoc($mobileCredQ)) {
    $mobileAiCredentials[] = $mobileCredRow;
  }
}
$webAiCredentials = array();
$webCredQ = $d->select('web_ai_credentials_master', "ai_status='1'", 'ORDER BY credential_type ASC, web_ai_credentials_id ASC');
if ($webCredQ && mysqli_num_rows($webCredQ) > 0) {
  while ($webCredRow = mysqli_fetch_assoc($webCredQ)) {
    $webAiCredentials[] = $webCredRow;
  }
}
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Manage Company Settings</h4>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Id</th>
                    <th>Company Name</th>
                    <th>City</th>
                    <th>Mobile</th>
                    <th>Per Emp Price</th>
                    <th>Tracking Limit</th>
                    <th>Registration Limit</th>
                    <th>CRM Limit</th>
                    <th>Tracking Status</th>
                    <th>Mobile AI</th>
                    <th>Web AI</th>
                    <?php
                    if ($global_role_id == '1') {
                      echo "<th>Start visit with otp</th>";
                    }
                    ?>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->select("society_master", "society_id!=0 $countryAppendQuerySocietySingle", "order by plan_expire_date ASC");
                  $societyRows = [];
                  $societyIds = [];
                  while ($data = mysqli_fetch_array($q)) {
                    $societyRows[] = $data;
                    $societyIds[] = (int)$data['society_id'];
                  }

                  $adminBySociety = [];
                  if (!empty($societyIds)) {
                    $societyIdsIn = implode(',', array_map('intval', $societyIds));
                    $qq = $d->select("bms_admin_master", "society_id IN ($societyIdsIn)", "ORDER BY admin_id ASC");
                    while ($adm = mysqli_fetch_array($qq)) {
                      $sid = (int)$adm['society_id'];
                      if (!isset($adminBySociety[$sid])) {
                        $adminBySociety[$sid] = $adm;
                      }
                    }
                  }

                  foreach ($societyRows as $data) {
                    extract($data);
                    $data11 = $adminBySociety[(int)$society_id] ?? [];
                    $admin_password = $data11['admin_password'] ?? null;
                    $mobileAiOn = ((int) ($ai_status ?? 0) === 1);
                    $webAiOn = ((int) ($web_ai_status ?? 0) === 1);
                    $mobileCredId = (int) ($ai_credentials_id ?? 0);
                    $webCredId = (int) ($web_ai_credentials_id ?? 0);
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo '' . $d->short_app_name() . '_' . $society_id; ?></td>
                      <td><?= $society_name ?></td>
                      <td><?php echo $city_name; ?></td>
                      <td><?php echo $secretary_mobile; ?></td>
                      <td class="editable-price-box">
                        <span class="editable-price" data-id="<?= $society_id ?>">
                          <?php echo $per_employee_price; ?>
                        </span>
                      </td>
                      <td><?php echo $employee_tracking_limit; ?></td>
                      <td><?php echo ($employee_registration_limit == 0) ? 'No Limit' : $employee_registration_limit; ?></td>
                      <td><?php echo $crm_limit; ?></td>
                      <td>
                        <?php
                        if ($tracking_status == "1") {
                        ?>
                          <form action="controller/statusController.php" method="post">
                            <input type="hidden" name="society_id" value="<?= $society_id ?>">
                            <input type="hidden" name="status" value="tracking_status">
                            <input type="hidden" name="value" value="0">
                            <button style="background-color: green;" type="submit" class="form-btn btn btn-sm btn-primary waves-effect waves-light m-1" title="Update Status">Active</button>
                          </form>
                        <?php } else { ?>
                          <form action="controller/statusController.php" method="post">
                            <input type="hidden" name="society_id" value="<?= $society_id ?>">
                            <input type="hidden" name="status" value="tracking_status">
                            <input type="hidden" name="value" value="1">
                            <button type="submit" class="form-btn btn btn-sm btn-danger waves-effect waves-light m-1" title="Update Status">Deactive</button>
                          </form>
                        <?php } ?>
                      </td>
                      <td>
                        <?php if ($mobileAiOn) { ?>
                          <div class="d-inline-flex align-items-center">
                            <form action="controller/webAiController.php" method="post" class="m-0 mr-1" id="mobileAiOffForm_<?= $society_id ?>">
                              <input type="hidden" name="society_id" value="<?= $society_id ?>">
                              <input type="hidden" name="updateMobileAiStatus" value="updateMobileAiStatus">
                              <input type="hidden" name="ai_status" value="0">
                              <button type="button" class="btn btn-sm btn-success" title="Turn off Mobile AI" onclick="confirmTurnOffAi('mobile', <?= (int) $society_id ?>)">On</button>
                            </form>
                            <button
                              type="button"
                              class="btn btn-sm btn-outline-secondary"
                              title="Change Mobile AI credential"
                              data-toggle="modal"
                              data-target="#updateMobileAiModal"
                              onclick="fillMobileAiModal('<?= $society_id ?>', '1', '<?= $mobileCredId ?>')">
                              <i class="fa fa-cog"></i>
                            </button>
                          </div>
                        <?php } else { ?>
                          <button
                            type="button"
                            class="btn btn-sm btn-danger"
                            title="Turn on Mobile AI"
                            data-toggle="modal"
                            data-target="#updateMobileAiModal"
                            onclick="fillMobileAiModal('<?= $society_id ?>', '1', '<?= $mobileCredId ?>')">
                            Off
                          </button>
                        <?php } ?>
                      </td>
                      <td>
                        <?php if ($webAiOn) { ?>
                          <div class="d-inline-flex align-items-center">
                            <form action="controller/webAiController.php" method="post" class="m-0 mr-1" id="webAiOffForm_<?= $society_id ?>">
                              <input type="hidden" name="society_id" value="<?= $society_id ?>">
                              <input type="hidden" name="updateWebAiStatus" value="updateWebAiStatus">
                              <input type="hidden" name="web_ai_status" value="0">
                              <button type="button" class="btn btn-sm btn-success" title="Turn off Web AI" onclick="confirmTurnOffAi('web', <?= (int) $society_id ?>)">On</button>
                            </form>
                            <button
                              type="button"
                              class="btn btn-sm btn-outline-secondary"
                              title="Change Web AI credential"
                              data-toggle="modal"
                              data-target="#updateWebAiModal"
                              onclick="fillWebAiModal('<?= $society_id ?>', '1', '<?= $webCredId ?>')">
                              <i class="fa fa-cog"></i>
                            </button>
                          </div>
                        <?php } else { ?>
                          <button
                            type="button"
                            class="btn btn-sm btn-danger"
                            title="Turn on Web AI"
                            data-toggle="modal"
                            data-target="#updateWebAiModal"
                            onclick="fillWebAiModal('<?= $society_id ?>', '1', '<?= $webCredId ?>')">
                            Off
                          </button>
                        <?php } ?>
                      </td>
                      <?php
                      if ($global_role_id == '1') {
                      ?><td>
                          <?php
                          $buttonClass = ($data['start_visit_with_otp'] == "1") ? 'btn-success-new' : 'btn-danger';
                          $buttonCondition = ($data['start_visit_with_otp'] == "1") ? 'Yes' : 'No';
                          $posText = 'Yes';
                          $negText = 'No';
                          $status = ($data['start_visit_with_otp'] == "1") ? 'startVisitWithOtpDeactive' : 'startVisitWithOtpActive';
                          $newStatus = ($data['start_visit_with_otp'] == "1") ? 'startVisitWithOtpActive' : 'startVisitWithOtpDeactive';
                          $newStatusVal = ($data['start_visit_with_otp'] == "1") ? '1' : '0';
                          $statusValue = ($data['start_visit_with_otp'] == "1") ? '0' : '1';
                          ?>

                          <input type="button" class="btn btn-sm pl-1 pr-1 w-50 <?php echo $buttonClass ?>" id="<?php echo 'company_' . $data['society_id']; ?>" onclick="changeStatusNew('<?php echo $data['society_id']; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'company_' . $data['society_id']; ?>','','','./controller/TrackingLimitController.php','<?php echo $negText; ?>','<?php echo $posText; ?>');" data-size="small" value="<?php echo $buttonCondition ?>" />

                        </td>

                      <?php
                      }
                      ?>
                      <td>
                        <button data-toggle="modal" data-target="#updatePlan" onclick="updateEmployeeLimit('<?= $sub_domain ?>','<?= $society_id ?>','<?= $employee_tracking_limit ?>','<?= $employee_registration_limit ?>','<?= $crm_limit ?>','<?= $package_id ?>','<?= $company_name ?>','<?= $crm_created ?>')" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i> Update Limit</button>
                        <?php
                        if ($global_role_id == '1') {
                        ?>
                          <?php
                          $society_settings_json = $data['society_settings'];
                          $society_settings = json_decode($society_settings_json, true);

                          $face_attendance_delete_days = $society_settings['face_attendance_delete_days'] ?? '';
                          $work_report_delete_days = $society_settings['work_report_delete_days'] ?? '';
                          $visit_attachment_delete_days = $society_settings['visit_attachment_delete_days'] ?? '';
                          $expense_attachment_delete_days = $society_settings['expense_attachment_delete_days'] ?? '';
                          $chat_attachment_delete_days = $society_settings['chat_attachment_delete_days'] ?? '';
                          $task_attachment_delete_days = $society_settings['task_attachment_delete_days'] ?? '';
                          $circular_attachment_delete_days = $society_settings['circular_attachment_delete_days'] ?? '';
                          $discussion_attachment_delete_days = $society_settings['discussion_attachment_delete_days'] ?? '';
                          $meeting_attachment_delete_days = $society_settings['meeting_attachment_delete_days'] ?? '';
                          $tracking_attachment_delete_months = $society_settings['tracking_attachment_delete_months'] ?? '';
                          ?>
                          <button data-toggle="modal" data-target="#updateCompanySettings" onclick="updateCompanySettings('<?= $sub_domain ?>','<?= $society_id ?>','<?= $package_id ?>','<?= $company_name ?>','<?= $face_attendance_delete_days ?>','<?= $work_report_delete_days ?>','<?= $visit_attachment_delete_days ?>','<?= $expense_attachment_delete_days ?>','<?= $chat_attachment_delete_days ?>','<?= $task_attachment_delete_days ?>','<?= $circular_attachment_delete_days ?>','<?= $discussion_attachment_delete_days ?>','<?= $meeting_attachment_delete_days ?>','<?= $tracking_attachment_delete_months ?>','<?= $attendance_map_type ?>','<?= $visit_map_type ?>','<?= $clear_local_save_attendance_data_face_app ?>')" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i> Company Settings</button>
                        <?php
                        }
                        ?>
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

<div class="modal fade" id="updatePlan">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Update Tracking Limit</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="TrackingEmployeeLimits" action="controller/TrackingLimitController.php" method="post" enctype="multipart/form-data" novalidate>
          <input type="hidden" name="update" value="update">
          <input type="hidden" id="society_id" name="society_id">
          <input type="hidden" id="base_url" name="society_base_url">
          <input type="hidden" id="package_id" name="package_id">
          <input type="hidden" id="society_name" name="society_name">
          <div id="trackingLimitNotice" class="alert alert-danger py-2 mb-3" style="display:none;" role="alert">
            Change at least one of tracking limit, registration limit, or CRM limit.
          </div>
          <div class="form-group row py-0 my-2">
            <label for="employee_tracking_limit" class="col-sm-4 col-form-label">Employee Tracking Limit <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control onlyNumber" id="employee_tracking_limit" name="employee_tracking_limit">
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="employee_registration_limit" class="col-sm-4 col-form-label">Employee Registration Limit <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control onlyNumber" id="employee_registration_limit" name="employee_registration_limit">
            </div>
          </div>
          <div class="form-group row py-0 my-2 Crmlimit">
            <label for="crm_limit" class="col-sm-4 col-form-label">CRM Limit <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" maxlength="6" class="form-control onlyNumber" id="crm_limit" name="crm_limit">
            </div>
          </div>
          <div class="form-group row py-0 my-2" id="paymentReceivedRow" style="display:none;">
            <label class="col-sm-4 col-form-label">Is payment received for limit change? <span class="required">*</span></label>
            <div class="col-sm-8 pt-2">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="payment_received" id="payment_received_yes" value="1">
                <label class="form-check-label" for="payment_received_yes">Yes</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="payment_received" id="payment_received_no" value="0">
                <label class="form-check-label" for="payment_received_no">No</label>
              </div>
            </div>
          </div>
          <div id="trackingPaymentFields">
          <div class="form-group row py-0 my-2">
            <label for="tracking_amount_received" class="col-sm-4 col-form-label">Received Amount <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" maxlength="15" class="form-control onlyNumber" id="tracking_amount_received" name="amountReceived">
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="tracking_payment_mode" class="col-sm-4 col-form-label">Payment Mode <span class="required">*</span></label>
            <div class="col-sm-8">
              <select name="payment_mode" id="tracking_payment_mode" class="form-control">
                <option value="">-- Select --</option>
                <option value="1">Online Bank Transfer</option>
                <option value="2">Cheque</option>
                <option value="3">UPI</option>
                <option value="4">Cash</option>
              </select>
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="tracking_payment_attachment" class="col-sm-4 col-form-label">Payment Attachment <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="file" accept="image/*,.pdf" id="tracking_payment_attachment" name="payment_attachment" class="form-control">
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="tracking_received_by" class="col-sm-4 col-form-label">Received By <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" maxlength="100" id="tracking_received_by" name="received_by" class="form-control">
            </div>
          </div>
          </div>
          <div class="form-footer text-center">
            <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="updateCompanySettings">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Update Company Settings</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="updateCompanySettingsForm" action="controller/TrackingLimitController.php" method="post">
          <input type="hidden" id="update_society_id" name="society_id">
          <input type="hidden" id="update_base_url" name="society_base_url">
          <input type="hidden" id="update_society_name" name="society_name">
          <div class="form-group row py-0 my-2">
            <label for="attendance_map_type" class="col-sm-4 col-form-label">Attendance map type <span class="required">*</span></label>
            <div class="col-sm-8">
              <select name="attendance_map_type" id="attendance_map_type" class="form-control single-select">
                <option value="0">Out of range visible with button</option>
                <option value="1">No map visible</option>
                <option value="2">Always visible</option>
                <option value="3">Out of range visible</option>
              </select>
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="visit_map_type" class="col-sm-4 col-form-label">visit map type <span class="required">*</span></label>
            <div class="col-sm-8">
              <select name="visit_map_type" id="visit_map_type" class="form-control single-select">
                <option value="0">Out of range visible with button</option>
                <option value="1">No map visible</option>
                <option value="2">Always visible</option>
                <option value="3">Out of range visible</option>
              </select>
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="clear_local_save_attendance_data_face_app" class="col-sm-9 col-form-label">Clear local save attendance data face app <span class="required">*</span></label>
            <div class="col-sm-3">
              <input type="text" autocomplete="off" class="form-control onlyNumber" maxlength="4" required id="clear_local_save_attendance_data_face_app" name="clear_local_save_attendance_data_face_app">
            </div>
          </div>

          <div class="form-group row py-0 my-2">
            <label for="tracking_attachment_delete_months" class="col-sm-9 col-form-label">tracking attachment delete months <span class="required">*</span></label>
            <div class="col-sm-3">
              <input type="text" autocomplete="off" class="form-control onlyNumber" maxlength="4" required id="tracking_attachment_delete_months" name="tracking_attachment_delete_months">
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="face_attendance_delete_days" class="col-sm-9 col-form-label">Face attendance attachment delete days <span class="required">*</span></label>
            <div class="col-sm-3">
              <input type="text" autocomplete="off" class="form-control onlyNumber" maxlength="4" required id="face_attendance_delete_days" name="face_attendance_delete_days">
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="work_report_delete_days" class="col-sm-9 col-form-label">work report attachment delete days <span class="required">*</span></label>
            <div class="col-sm-3">
              <input type="text" autocomplete="off" class="form-control onlyNumber" maxlength="4" required id="work_report_delete_days" name="work_report_delete_days">
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="visit_attachment_delete_days" class="col-sm-9 col-form-label">visit end attachment delete days <span class="required">*</span></label>
            <div class="col-sm-3">
              <input type="text" autocomplete="off" class="form-control onlyNumber" maxlength="4" required id="visit_attachment_delete_days" name="visit_attachment_delete_days">
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="expense_attachment_delete_days" class="col-sm-9 col-form-label">expense attachment delete days <span class="required">*</span></label>
            <div class="col-sm-3">
              <input type="text" autocomplete="off" class="form-control onlyNumber" maxlength="4" required id="expense_attachment_delete_days" name="expense_attachment_delete_days">
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="chat_attachment_delete_days" class="col-sm-9 col-form-label">chat attachment delete days <span class="required">*</span></label>
            <div class="col-sm-3">
              <input type="text" autocomplete="off" class="form-control onlyNumber" maxlength="4" required id="chat_attachment_delete_days" name="chat_attachment_delete_days">
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="task_attachment_delete_days" class="col-sm-9 col-form-label">task attachment delete days <span class="required">*</span></label>
            <div class="col-sm-3">
              <input type="text" autocomplete="off" class="form-control onlyNumber" maxlength="4" required id="task_attachment_delete_days" name="task_attachment_delete_days">
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="circular_attachment_delete_days" class="col-sm-9 col-form-label">circular attachment delete days <span class="required">*</span></label>
            <div class="col-sm-3">
              <input type="text" autocomplete="off" class="form-control onlyNumber" maxlength="4" required id="circular_attachment_delete_days" name="circular_attachment_delete_days">
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="discussion_attachment_delete_days" class="col-sm-9 col-form-label">discussion attachment delete days <span class="required">*</span></label>
            <div class="col-sm-3">
              <input type="text" autocomplete="off" class="form-control onlyNumber" maxlength="4" required id="discussion_attachment_delete_days" name="discussion_attachment_delete_days">
            </div>
          </div>
          <div class="form-group row py-0 my-2">
            <label for="meeting_attachment_delete_days" class="col-sm-9 col-form-label">meeting attachment delete days <span class="required">*</span></label>
            <div class="col-sm-3">
              <input type="text" autocomplete="off" class="form-control onlyNumber" maxlength="4" required id="meeting_attachment_delete_days" name="meeting_attachment_delete_days">
            </div>
          </div>
          <div class="form-footer text-center">
            <input type="hidden" name="updateCompanySettings" value="updateCompanySettings" />
            <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="updateMobileAiModal">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Mobile AI Settings</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="controller/webAiController.php" method="post">
        <div class="modal-body">
          <input type="hidden" name="updateMobileAiSetting" value="updateMobileAiSetting">
          <input type="hidden" name="society_id" id="mobileAiSocietyId" value="">
          <input type="hidden" name="ai_status" id="mobileAiStatus" value="1">
          <div class="form-group">
            <label>Mobile AI Credential <span class="text-danger">*</span></label>
            <select name="ai_credentials_id" id="mobileAiCredentialsId" class="form-control single-select" required>
              <option value="">Select credential</option>
              <?php foreach ($mobileAiCredentials as $cred) { ?>
                <option value="<?php echo (int) $cred['ai_credentials_id']; ?>">
                  <?php
                  echo htmlspecialchars(($cred['ai_model'] ?? ('Credential #' . $cred['ai_credentials_id'])));
                  echo ((int) ($cred['debug_key'] ?? 0) === 1) ? ' (Debug)' : ' (Live)';
                  if (!empty($cred['ai_base_url'])) {
                    echo ' — ' . htmlspecialchars($cred['ai_base_url']);
                  }
                  ?>
                </option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Enable Mobile AI</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="updateWebAiModal">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Web AI Settings</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="controller/webAiController.php" method="post">
        <div class="modal-body">
          <input type="hidden" name="updateWebAiSetting" value="updateWebAiSetting">
          <input type="hidden" name="society_id" id="webAiSocietyId" value="">
          <input type="hidden" name="web_ai_status" id="webAiStatus" value="1">
          <div class="form-group">
            <label>Web AI Credential <span class="text-danger">*</span></label>
            <select name="web_ai_credentials_id" id="webAiCredentialsId" class="form-control single-select" required>
              <option value="">Select credential</option>
              <?php foreach ($webAiCredentials as $cred) { ?>
                <option value="<?php echo (int) $cred['web_ai_credentials_id']; ?>">
                  <?php
                  echo htmlspecialchars($cred['credential_name']);
                  echo ((int) $cred['credential_type'] === 1) ? ' (Dev)' : ' (Live)';
                  echo ' — ' . htmlspecialchars($cred['ai_base_url']);
                  ?>
                </option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Enable Web AI</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script type="text/javascript">
  function updateEmployeeLimit(base_url, society_id, employee_tracking_limit, employee_registration_limit, crm_limit, package_id, society_name,crm_created) {
    $('#society_id').val(society_id);
    $('#base_url').val(base_url);
    $('#employee_tracking_limit').val(employee_tracking_limit);
    $('#employee_registration_limit').val(employee_registration_limit);
    $('#crm_limit').val(crm_limit);
    $('#package_id').val(package_id);
    $('#society_name').val(society_name);
    if(crm_created==1){
      $(".Crmlimit").show();
    }else{
      $(".Crmlimit").hide();
    }
    $('#tracking_amount_received').val('');
    $('#tracking_payment_mode').val('');
    $('#tracking_payment_attachment').val('');
    $('#tracking_received_by').val('');
    $('input[name="payment_received"]').prop('checked', false);
    refreshTrackingPaymentOption();
    var limitForm = $('#TrackingEmployeeLimits');
    limitForm.data('originalLimits', {
      tracking: employee_tracking_limit,
      registration: employee_registration_limit,
      crm: crm_limit
    });
    $('#trackingLimitNotice').hide();
    if (limitForm.data('validator')) {
      limitForm.validate().resetForm();
    }
  }

  function trackingLimitsUnchanged() {
    var original = $('#TrackingEmployeeLimits').data('originalLimits');
    if (!original) {
      return false;
    }
    var same = function (current, previous) {
      return Number(current) === Number(previous);
    };
    return same($('#employee_tracking_limit').val(), original.tracking)
      && same($('#employee_registration_limit').val(), original.registration)
      && same($('#crm_limit').val(), original.crm);
  }

  function showTrackingPaymentFields(show) {
    if (show) {
      $('#trackingPaymentFields').show();
      return;
    }
    $('#trackingPaymentFields').hide();
    $('#trackingPaymentFields').find('label.error').remove();
    $('#trackingPaymentFields').find('.error').removeClass('error');
  }

  function refreshTrackingPaymentOption() {
    if (trackingLimitsUnchanged()) {
      $('#payment_received_yes, #payment_received_no').prop('checked', false);
      $('#paymentReceivedRow').hide();
      showTrackingPaymentFields(false);
      return;
    }
    $('#paymentReceivedRow').css('display', 'flex');
    showTrackingPaymentFields($('#payment_received_yes').is(':checked'));
  }

  document.addEventListener('DOMContentLoaded', function () {
    $('#employee_tracking_limit, #employee_registration_limit, #crm_limit').on('input', function () {
      if (!trackingLimitsUnchanged()) {
        $('#trackingLimitNotice').hide();
      }
      refreshTrackingPaymentOption();
    });
    $('#payment_received_yes, #payment_received_no').on('change', function () {
      var paymentReceived = $('#payment_received_yes').is(':checked');
      window.setTimeout(function () {
        $('#paymentReceivedRow').css('display', 'flex');
        showTrackingPaymentFields(paymentReceived);
      }, 0);
    });

    $('#TrackingEmployeeLimits').validate({
      errorPlacement: function (error, element) {
        error.addClass('d-block mt-1 mb-0');
        if (element.attr('name') === 'payment_received') {
          error.appendTo(element.closest('.col-sm-8'));
          return;
        }
        error.insertAfter(element);
      },
      invalidHandler: function () {
        $('#trackingLimitNotice').toggle(trackingLimitsUnchanged());
      },
      rules: {
        employee_tracking_limit: {
          required: true,
          digits: true
        },
        employee_registration_limit: {
          required: true,
          digits: true
        },
        crm_limit: {
          required: true,
          digits: true
        },
        payment_received: {
          required: true
        },
        amountReceived: {
          required: true,
          number: true,
          min: 0
        },
        payment_mode: {
          required: true
        },
        payment_attachment: {
          required: true,
          extension: 'jpg|jpeg|png|pdf'
        },
        received_by: {
          required: true,
          noSpace: true,
          maxlength: 100
        }
      },
      messages: {
        employee_tracking_limit: {
          required: 'Please enter the employee tracking limit.',
          digits: 'Please enter a valid tracking limit.'
        },
        employee_registration_limit: {
          required: 'Please enter the employee registration limit.',
          digits: 'Please enter a valid registration limit.'
        },
        crm_limit: {
          required: 'Please enter the CRM limit.',
          digits: 'Please enter a valid CRM limit.'
        },
        payment_received: {
          required: 'Please select whether payment was received.'
        },
        amountReceived: {
          required: 'Please enter the received amount.',
          number: 'Please enter a valid amount.',
          min: 'Please enter a valid amount.'
        },
        payment_mode: {
          required: 'Please select a payment mode.'
        },
        payment_attachment: {
          required: 'Please upload a payment attachment.',
          extension: 'Allowed files: jpg, jpeg, png, pdf.'
        },
        received_by: {
          required: 'Please enter who received the amount.',
          noSpace: 'Please enter who received the amount.',
          maxlength: 'Received by must not exceed 100 characters.'
        }
      },
      submitHandler: function (form) {
        if (trackingLimitsUnchanged()) {
          $('#trackingLimitNotice').show();
          return false;
        }
        $('#trackingLimitNotice').hide();
        $(form).find(':input[type="submit"]').prop('disabled', true);
        form.submit();
      }
    });
  });

  function updateCompanySettings(base_url, society_id, package_id, society_name, face_attendance_delete_days, work_report_delete_days, visit_attachment_delete_days, expense_attachment_delete_days, chat_attachment_delete_days, task_attachment_delete_days, circular_attachment_delete_days, discussion_attachment_delete_days, meeting_attachment_delete_days, tracking_attachment_delete_months, attendance_map_type, visit_map_type, clear_local_save_attendance_data_face_app) {
    $('#update_society_id').val(society_id);
    $('#update_base_url').val(base_url);
    $('#attendance_map_type').val(attendance_map_type).trigger("change");
    $('#visit_map_type').val(visit_map_type).trigger("change");
    $('#face_attendance_delete_days').val(face_attendance_delete_days);
    $('#work_report_delete_days').val(work_report_delete_days);
    $('#visit_attachment_delete_days').val(visit_attachment_delete_days);
    $('#expense_attachment_delete_days').val(expense_attachment_delete_days);
    $('#chat_attachment_delete_days').val(chat_attachment_delete_days);
    $('#task_attachment_delete_days').val(task_attachment_delete_days);
    $('#circular_attachment_delete_days').val(circular_attachment_delete_days);
    $('#discussion_attachment_delete_days').val(discussion_attachment_delete_days);
    $('#meeting_attachment_delete_days').val(meeting_attachment_delete_days);
    $('#tracking_attachment_delete_months').val(tracking_attachment_delete_months);
    $('#clear_local_save_attendance_data_face_app').val(clear_local_save_attendance_data_face_app);
    $('#update_society_name').val(society_name);
  }

  function fillMobileAiModal(societyId, aiStatus, credentialsId) {
    document.getElementById('mobileAiSocietyId').value = societyId;
    document.getElementById('mobileAiStatus').value = aiStatus || '1';
    $('#mobileAiCredentialsId').val(credentialsId || '').trigger('change');
  }

  function fillWebAiModal(societyId, webAiStatus, credentialsId) {
    document.getElementById('webAiSocietyId').value = societyId;
    document.getElementById('webAiStatus').value = webAiStatus || '1';
    $('#webAiCredentialsId').val(credentialsId || '').trigger('change');
  }

  function confirmTurnOffAi(type, societyId) {
    var isWeb = type === 'web';
    var label = isWeb ? 'Web AI' : 'Mobile AI';
    var formId = (isWeb ? 'webAiOffForm_' : 'mobileAiOffForm_') + societyId;

    swal({
      title: 'Are you sure?',
      text: 'Do you want to turn off ' + label + ' for this company?',
      icon: 'warning',
      buttons: ['Cancel', 'Yes, turn off'],
      dangerMode: true,
    }).then(function(willTurnOff) {
      if (willTurnOff) {
        var form = document.getElementById(formId);
        if (form) {
          form.submit();
        }
      }
    });
  }
</script>