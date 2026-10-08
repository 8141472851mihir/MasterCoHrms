<?php
error_reporting(0);
$sId = (isset($_GET['sId']) && $_GET['sId'] != 'all') ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
$sId = $sId > 0 ? $sId : '';
$maintenance = (isset($_GET['maintenance']) && $_GET['maintenance'] != 'all') ? $d->sanitizeReportFilterIdAsInt($_GET['maintenance']) : 0;
$maintenance = $maintenance > 0 ? $maintenance : '';
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-2">
        <h4 class="page-title">Manage Domain</h4>

      </div>
      <div class="col-sm-3">
        <form action="" method="get" accept-charset="utf-8">
          <input type="hidden" name="maintenance" value="<?php echo $_GET['maintenance']; ?>">
          <select type="text" required="" id="sId" onchange="this.form.submit()" class="form-control single-select"
            name="sId">
            <option value="all">-- All --</option>
            <?php
            $qc = $d->select("server_master", "");
            while ($cData = mysqli_fetch_array($qc)) {
            ?>
              <option <?php if (isset($sId) && $cData['server_id'] == $sId) {
                        echo "selected";
                      } ?> value="<?php echo $cData['server_id']; ?>"><?php echo $cData['server_name']; ?>
                (<?php echo $cData['server_ip']; ?>)</option>
            <?php } ?>
          </select>
        </form>
      </div>
      <div class="col-sm-3">
        <form action="" method="get" accept-charset="utf-8">
          <input type="hidden" name="sId" value="<?php echo $_GET['sId']; ?>">
          <select type="text" required="" id="maintenance" onchange="this.form.submit()"
            class="form-control single-select" name="maintenance">
            <option value="all">-- All Domains --</option>
            <option <?php if (isset($_GET['maintenance']) && $_GET['maintenance'] == '1') {
                      echo "selected";
                    } ?> value="1">Domain under maintenance</option>
            <option <?php if (isset($_GET['maintenance']) && $_GET['maintenance'] == '2') {
                      echo "selected";
                    } ?> value="2">Domain not in maintenance</option>
          </select>
        </form>
      </div>
      <div class="col-sm-4">
        <a id="addDomainBtn" href="#" data-toggle="modal" data-target="#addDomainModel"
          class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add Domain</a>
        <?php if ($role_id == 1) { ?>
          <a href="javascript:void(0)" id="startMaintainance" onclick="maintenanceAll();"
            class="btn  btn-sm btn-danger">Schedule Maintenance </a>
        <?php }
        if ($role_id == 1) { ?>
          <a href="javascript:void(0)" id="endMaintainance" onclick="endMaintenance();"
            class="btn  btn-sm btn-warning ">End Maintenance </a>
        <?php } ?>
        <?php
        $ongoing_patch = $d->count_data_direct("festival_id", "festival_master", "is_festival='1' AND ongoing_patch=1");
        $show_note = ($ongoing_patch == 0) ? "Start Patch " : "End Patch ";
        ?>
        <a href="javascript:void(0)" id="ongoingPatch" onclick="ongoingPatch('<?php echo $ongoing_patch; ?>');"
          class="btn  btn-sm btn-danger "><?php echo $show_note; ?></a>
      </div>
    </div>
    <?php
    $companyArray = array();
    $companyArrayExpire = array();
    $qcompany = $d->selectRow(
      "domain_id, CASE WHEN STR_TO_DATE(TRIM(plan_expire_date), '%Y-%m-%d') < CURDATE() THEN 'YES' ELSE 'NO' END AS is_expired",
      "society_master",
      "domain_id!=0"
    );
    while ($companyCountData = mysqli_fetch_assoc($qcompany)) {
      $domain_id = $companyCountData['domain_id'];
      if (!isset($companyArray[$domain_id])) {
        $companyArray[$domain_id] = 0;
        $companyArrayExpire[$domain_id] = 0;
      }
      $companyArray[$domain_id]++;
      if ($companyCountData['is_expired'] == 'YES') {
        $companyArrayExpire[$domain_id]++;
      }
    }
    $maintenance_date_time = $d->selectRow("festival_time", "festival_master", "is_festival='1'");
    if (mysqli_num_rows($maintenance_date_time)) {
      $maintenance_data = mysqli_fetch_array($maintenance_date_time);
      $maintenance_time = $maintenance_data['festival_time'];
      if ($maintenance_time != '' && $maintenance_time != '0000-00-00 00:00:00') {
        $maintenance_datetime = new DateTime($maintenance_time);
        $current_datetime = new DateTime();
        if ($maintenance_datetime < $current_datetime) {
          $message = "Ongoing Maintenance";
        } else {
          $message = "Upcoming Maintenance";
        }
        $show_note = "";
      } else {
        $show_note = "d-none";
      }
    }
    ?>
    <div class="col-sm-12 text-danger float-right <?php echo $show_note; ?>">
      <p>Note: <?php echo $message; ?> :- <?php echo $maintenance_time; ?> </p>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="reportTable" class="table table-bordered">
                <thead>
                  <tr>
                    <th class="maintenance_td">#</th>
                    <th>#</th>
                    <th>Action</th>
                    <th>Domain Name</th>
                    <th>Server Name</th>
                    <th>Company Count</th>
                    <th>Crm Count</th>
                    <th>Expire Company</th>
                    <th>Total Login Users</th>
                    <th>Tracking Users</th>
                    <th>Total Active Tracking Users</th>
                    <th>Remote Db</th>
                    <th>Maintainance</th>
                    <th>Remote Ip</th>
                    <th>Domain Remark</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th class="selectAllNew maintenance_td">
                      <input type="checkbox" class="selectAllMaintenanceCheckbox" />
                    </th>
                    <th class="no-search-box"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                </tfoot>
                <tbody>
                  <?php
                  $appendSeverQuery = '';
                  if (isset($sId) && $sId > 0 && $sId != 'all') {
                    $appendSeverQuery .= " AND dm.server_id='$sId'";
                  }
                  if (isset($maintenance) && $maintenance > 0 && $maintenance != 'all') {
                    $maintenance_value = ($maintenance == '1') ? '1' : '0';
                    $appendSeverQuery .= " AND dm.maintainance_active_status='$maintenance_value'";
                  }
                  $i = 1;
                  $q = $d->selectRow(
                    "dm.*, sm.server_name, sm.server_ip,
    COUNT(DISTINCT s.society_id) AS company_count,
    SUM(CASE WHEN s.crm_created = 1 THEN 1 ELSE 0 END) AS crm_count,
    SUM(CASE WHEN STR_TO_DATE(TRIM(s.plan_expire_date), '%Y-%m-%d') < CURDATE() THEN 1 ELSE 0 END) AS expire_company_count,
    SUM(sa.total_login_user) AS total_login_user,
    SUM(sa.active_tracking_users) AS active_tracking_users,
    SUM(sa.last_month_tracking_user_count) AS last_month_tracking_user_count",
                    "domain_master AS dm
    LEFT JOIN server_master AS sm ON sm.server_id = dm.server_id
    LEFT JOIN society_master AS s ON s.domain_id = dm.domain_id
    LEFT JOIN society_analytics_master AS sa ON sa.society_id = s.society_id",
                    "1 $appendSeverQuery GROUP BY dm.domain_id"
                  );
                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);

                  ?>
                    <tr>
                      <td class='text-center maintenance_td'>
                        <input type="checkbox" class="multiMaintenanceCheckbox" value="<?php echo $domain_id; ?>">
                      </td>
                      <td><?php echo $i++; ?></td>
                      <td>
                        <button type="button" data-toggle="modal" data-target="#addDomainModel" class="btn btn-info btn-sm"
                          onclick="editDomain(<?php echo $domain_id ?>,'<?php echo $domain_name ?>','<?php echo $domain_remark ?>','<?php echo $server_id ?>','1','<?php echo $is_remote_db ?>','<?php echo $remote_ip ?>')"><i
                            class="fa fa-pencil"></i></button>
                        <label class="switch-custom">
                          <?php if ($domain_active_status == 0) { ?>
                            <input type="checkbox" data-color="#15ca20" data-size="small"
                              onchange="changeStatus('<?php echo $domain_id; ?>','domainDeactive');" checked />
                          <?php } else { ?>
                            <input type="checkbox" data-color="#15ca20" data-size="small"
                              onchange="changeStatus('<?php echo $domain_id; ?>','domainActive');" />
                          <?php } ?>
                          <span class="slider-custom round"></span>

                        </label>
                      </td>
                      <td><?php echo $domain_name; ?></td>
                      <!-- <td><?php echo "UPDATE `society_master` SET `domain_id`='$domain_id' WHERE `sub_domain` LIKE '%$domain_name%';"; ?></td> -->
                      <td class="tableWidth"><?php echo $server_name; ?> (<?php echo $server_ip; ?>)</td>
                      <td class="tableWidth"><?php if (array_key_exists($domain_id, $companyArray)) {
                                                echo $companyArray[$domain_id];
                                              } ?></td>
                      <td><?php
                          if ($crm_count == 0) {
                            echo "";
                          } else {
                            echo $crm_count;
                          } ?>
                      </td>
                      <td><?php if (array_key_exists($domain_id, $companyArrayExpire)) {
                            echo $companyArrayExpire[$domain_id];
                          } ?></td>
                      <td class="tableWidth"><?php echo $total_login_user; ?></td>
                      <td class="tableWidth"><?php echo $active_tracking_users; ?></td>
                      <td class="tableWidth"><?php echo $last_month_tracking_user_count; ?></td>
                      <td class="tableWidth">
                        <?php echo ($is_remote_db == 1) ? "YES" : "NO"; ?>
                      </td>
                      <td class="tableWidth">
                        <?php echo ($maintainance_active_status == 1) ? "YES" : "NO"; ?>
                      </td>
                      <td class="tableWidth"><?php echo $remote_ip; ?></td>
                      <td class="tableWidth"><?php echo $domain_remark; ?></td>
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
<div>
  <?php
  $maintenance_count = $d->count_data_direct("domain_id", "domain_master", "maintainance_active_status='1'");
  ?>
  <span class="d-none maintainance_active_count"><?php echo $maintenance_count; ?></span>
</div>
<div class="modal fade" id="addDomainModel">
  <div class="modal-dialog modal-md">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white"><span id="modeshow">Add</span> Domain</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="domainForm" action="controller/domainController.php" method="post">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Domain Name <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" maxlength="100" class="form-control" name="domain_name"
                id="domain_name_edit" required="">
            </div>
          </div>
          <input type="hidden" name="server_id_hidden" id="server_id_hidden">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Server Name <span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <select class="form-control single-select" id="server_id_edit" name="server_id" required>
                <option value="">--- Select Server ---</option>
                <?php
                $qc = $d->select("server_master", "server_active_status=0");
                while ($cData = mysqli_fetch_array($qc)) {
                ?>
                  <option <?php if ($server_id_edit == $cData['server_id']) {
                            echo "selected";
                          } ?> value="<?php echo $cData['server_id']; ?>">
                    <?php echo $cData['server_name']; ?> (<?php echo $cData['server_ip']; ?>)
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="remote_db" class="col-sm-4 col-form-label">Remote DB<span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <select class="form-control" id="remote_db" name="remote_db" required>
                <option value="0">No</option>
                <option value="1">Yes</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="remote_ip" class="col-sm-4 col-form-label">Remote IP <span class="required">*</span></label>
            <div class="col-sm-8">
              <textarea class="form-control" id="remote_ip" name="remote_ip"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Domain Remark </label>
            <div class="col-sm-8">
              <textarea class="form-control" id="domain_remark_edit" name="domain_remark" maxlength="300"></textarea>
            </div>
          </div>
          <div class="form-group row" id="subDomainAdded">
            <div class="col-sm-12">
              <label for="addedInCloudflare" class="mx-1 col-form-label"> Subdomain Added in Cloudflare <span
                  class="text-danger">*</span></label>
              <input class="border" type="checkbox" name="addedInCloudflare" id="addedInCloudflare">
            </div>
            <div class="col-sm-12">
              <label for="addedInFirebase" class="mx-1 col-form-label"> Subdomain Added in Firebase <span
                  class="text-danger">*</span></label>
              <input class="border" type="checkbox" name="addedInFirebase" id="addedInFirebase">
            </div>
          </div>
          <div class="form-footer text-center">
            <input type="hidden" name="domain_id" id="domain_id_edit">
            <button name="addDomain" id="submitButton" type="submit" class="btn btn-success"><i
                class="fa fa-check-square-o"></i><span id="submitText">Add</span></button>
            <button type="reset" class="btn btn-danger"><i class="fa fa-times"></i> CANCEL</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="startMaintenance">
  <div class="modal-dialog modal-md">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white"><span id="modeshow"></span> Schedule Maintenance</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="startMaintenanceForm" action="controller/domainController.php" method="post">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Maintenance Schedule <span
                class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" name="maintenanceDateTime" id="maintenanceDateTime" class="form-control" value=""
                required="">
            </div>
          </div>
          <div class="form-footer text-center">
            <input type="hidden" name="domain_ids" id="domain_ids">
            <input type="hidden" name="scheduleMaintenance" value="scheduleMaintenance" id="scheduleMaintenance">
            <button id="submitButton" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i><span
                id="submitText">Add</span></button>
            <button type="reset" class="btn btn-danger"><i class="fa fa-times"></i> CANCEL</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#maintenanceDateTime').bootstrapMaterialDatePicker({
      date: false,
      autoclose: true,
      todayHighlight: true,
      format: 'HH:mm',
    });
    var maintenanceCount = parseInt($('.maintainance_active_count').text().trim());
    var sId = <?php echo json_encode($sId); ?>;
    if ((maintenanceCount > 0) || !(sId != "All" && sId != "" && sId > 0)) {
      $('.maintenance_td').hide();
    } else {
      $('.maintenance_td').show();
    }
    if ((maintenanceCount > 0)) {
      $('#startMaintainance').hide();
      $('#endMaintainance').show();
    } else {
      $('#startMaintainance').show();
      $('#endMaintainance').hide();
    }
  });
</script>
<script>
  let $remoteDbSelect = $("#remote_db");
  let $remoteIpField = $("#remote_ip").closest(".form-group");

  function toggleRemoteIpField() {
    if ($remoteDbSelect.val() === "1") {
      $remoteIpField.show();
    } else {
      $remoteIpField.hide();
    }
  }
  

  
  
  $(document).ready(function() {
    $remoteDbSelect.on("change", toggleRemoteIpField);
    toggleRemoteIpField();
    
    $('#addDomainBtn').on('click', function() {
      $('#domainForm')[0].reset();
      $('#modeshow').text('Add');
      $('#submitText').text('Add');
    });
  });
</script>