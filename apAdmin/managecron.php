<div class="content-wrapper">
    <div class="container-fluid">
      <form action="" method="get" >
      <div class="row pt-2 pb-2">
        <div class="col-sm-6">
        <h4 class="page-title">Cron Logs</h4>
        </div>
        <?php if (isset($_GET['runLog']) && $_GET['runLog']==1) { ?>
          <input type="hidden" name="runLog" value="1">
          <input type="hidden" name="cron_id" value="<?php echo $d->sanitizeReportFilterIdAsInt($_GET['cron_id'] ?? 0); ?>">
        <div class="col-sm-3">
          <?php
            $from = $d->sanitizeReportFilterDate(isset($_GET['from']) ? $_GET['from'] : '', date('Y-m-d'));
          ?>
          <input type="text" class="form-control" autocomplete="off" id="autoclose-datepickerFrom"  name="from" value="<?php echo htmlspecialchars($from); ?>">   
        </div>
        <div class="col-sm-3">
          <input  class="btn btn-success btn-sm submitBtn" type="submit" name="getReport" value="Get">
        </div>
        <?php } ?>
       </div>
     </form>
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <?php if (isset($_GET['runLog']) && $_GET['runLog']==1) {
                $today = date("Y-m-d");
                $cron_id = $d->sanitizeReportFilterIdAsInt($_GET['cron_id'] ?? 0);
               ?>
                <div class="table-responsive">
                  <table id="exampleReport" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>Cron ID</th>
                            <th>Cron Name</th>
                            <th>Company</th>
                            <th>Run Time</th>
                            <th>Status Code</th>
                            <th>Server</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php 


                        $i=1;
                        $q=$d->selectRow("crons_master.*,cron_error_logs.*,society_master.society_name,society_master.city_name,society_master.sub_domain,server_master.server_name,server_master.server_ip,domain_master.domain_name","society_master LEFT JOIN domain_master ON society_master.domain_id=domain_master.domain_id LEFT JOIN server_master ON server_master.server_id=domain_master.server_id,crons_master,cron_error_logs","society_master.society_id=cron_error_logs.society_id AND cron_error_logs.cron_id=crons_master.cron_id AND cron_error_logs.cron_id = '$cron_id' AND DATE_FORMAT(log_date,'%Y-%m-%d')='$from' ","ORDER BY cron_error_logs.error_id DESC LIMIT 1000");
                        while ($data=mysqli_fetch_array($q)) {
                      ?>
                      <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $data['cron_id']; ?></td>
                        <td><?php echo $data['cron_name']; ?></td>
                        <td>
                          <a href="<?php echo $data['sub_domain']; ?>apAdmin/" target="_blank"><?php echo $data['society_name'].'-'.$data['city_name']; ?></a>
                        </td>
                        <td><?php echo $data['log_date']; ?></td>
                        <td><?php echo $data['status_code']; ?></td>
                        <td><?php echo $data['server_ip']; ?></td>
                        
                      </tr>
                        <?php } ?>
                        
                    </tbody>
                    
                </table>
                </div>
             <?php  } else {?>

              <form id="signupForm" action="controller/cronController.php" method="post" >
                <?php 
                
                   if(isset($_GET['cron_id'])) {
                      extract(array_map("test_input" , $_GET));
                      $q=$d->select("crons_master","cron_id='$cron_id'");
                      $data=mysqli_fetch_array($q);
                      $cron_category = $data['cron_category'];

                      // find same category cron data 
                      $sameCategroyCrondCompanyIds= array();
                      $sq= $d->selectRow("crons_society_master.society_id","crons_master,crons_society_master","crons_master.cron_id=crons_society_master.cron_id AND crons_master.cron_category='$cron_category' AND crons_master.cron_category!='' AND crons_master.cron_id!='$cron_id'");
                      while($sameData=mysqli_fetch_array($sq)){
                        array_push($sameCategroyCrondCompanyIds, $sameData['society_id']);
                      }
                      // print_r($sameCategroyCrondCompanyIds);
                    }else{
                      $data = array();
                    }
                   ?>
                <h4 class="form-header text-uppercase">
                  <i class="fa fa-lock"></i>
                  Cron
                </h4>
                <div class="form-group row">
                  <label for="input-10" class="col-sm-2 col-form-label">Cron Name  <span class="required">*</span></label>
                  <div class="col-sm-4">
                    <input maxlength="60" type="text" name="cron_name" class="form-control" required value="<?=($data) ? $data['cron_name'] : ''?>">
                  </div>
                  <label for="input-11" class="col-sm-2 col-form-label"> Cron URL <span class="required">*</span></label>
                  <div class="col-sm-4" >
                    <input maxlength="200" type="text" name="cron_url" class="form-control" required value="<?=($data) ? $data['cron_url'] : ''?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="input-10" class="col-sm-2 col-form-label">Cron Tag  <span class="required">*</span></label>
                  <div class="col-sm-4">
                    <input maxlength="200" type="text" name="cron_tag" class="form-control" required value="<?=($data) ? $data['cron_tag'] : ''?>">
                  </div>
                  <label for="input-11" class="col-sm-2 col-form-label"> Cron Value <span class="required">*</span></label>
                  <div class="col-sm-4" >
                    <input maxlength="200" type="text" name="cron_value" class="form-control" required value="<?=($data) ? $data['cron_value'] : ''?>">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="input-11" class="col-sm-2 col-form-label"> Cron Category <span class="required">*</span></label>
                  <div class="col-sm-4" >
                    <input  <?php if(isset($_GET['cron_id'])) {  echo "disabled"; } ?> maxlength="200" type="text" name="cron_category" class="form-control" required value="<?=($data) ? $data['cron_category'] : ''?>">
                  </div>
                </div>
                
                    <input  id="run_per_company" type="hidden" maxlength="2" max="10" min="1" name="run_per_company"  required value="1">
                  
                <div class="form-group row">
                  
                  <label for="input-11" class="col-sm-3 col-form-label">Crons For <span class="required">*</span></label>
                  <div class="col-sm-9">
                    <div class="controls">
                      <div class="row">
                        <div class="col-sm-3">
                          <h6>
                              <div class="icheck-material-primary">
                                  <input type="checkbox" id="user-checkbox" class="chk_boxes" />
                                  <label for="user-checkbox">Check All</label>
                              </div>
                          </h6>
                        </div>
                        <?php if ($data) { ?>
                        <div class="col-sm-3">
                          <h6>
                              <div>
                                  <input type="checkbox" id="draggable-checkbox" checked />
                                  <label for="draggable-checkbox">Draggable</label>
                              </div>
                          </h6>
                        </div>
                        <?php } ?>
                      </div>

                        <ul id="sortable-societies" class="sortable-list">
                            <?php

                            $selected_societies = array();
                            $lastRunBySociety = array();
                            if ($data) {

                              $lastCronRunStartDate = array('log_time' => '00:00:00');
                              $lastCronRunStartDateQuery = $d->selectRow("TIME(log_date) as log_time","cron_error_logs","cron_id=$cron_id AND DATE(log_date) = CURDATE() - INTERVAL 1 DAY ORDER BY log_date ASC LIMIT 1");

                              if(mysqli_num_rows($lastCronRunStartDateQuery)>0){
                                $lastCronRunStartDate = mysqli_fetch_array($lastCronRunStartDateQuery);
                              } else {
                                $lastCronRunStartDateQuery = $d->selectRow("TIME(log_date) as log_time","cron_error_logs","DATE(log_date) = CURDATE() - INTERVAL 1 DAY ORDER BY log_date ASC LIMIT 1");
                                if(mysqli_num_rows($lastCronRunStartDateQuery)>0){
                                  $lastCronRunStartDate = mysqli_fetch_array($lastCronRunStartDateQuery);
                                }
                              }

                              $initialCronTime = $lastCronRunStartDate['log_time'];
                              $cronTime = new DateTime($initialCronTime);

                              $lastRunQ = $d->selectRow(
                                  "society_id, MAX(log_date) AS last_run_time, SUBSTRING_INDEX(GROUP_CONCAT(status_code ORDER BY error_id DESC), ',', 1) AS status_code",
                                  "cron_error_logs",
                                  "cron_id=$cron_id",
                                  "GROUP BY society_id"
                              );
                              while ($lastRunRow = mysqli_fetch_array($lastRunQ)) {
                                  $lastRunBySociety[$lastRunRow['society_id']] = array(
                                      'last_run_time' => $lastRunRow['last_run_time'],
                                      'status_code' => $lastRunRow['status_code'],
                                  );
                              }

                                $q2 = $d->select(
                                    "crons_society_master csm JOIN society_master sm ON csm.society_id = sm.society_id",
                                    "cron_id=$cron_id",
                                    "ORDER BY csm.display_order_by ASC"
                                );
                                while ($soc = mysqli_fetch_array($q2)) {
                                    $society_id = $soc['society_id'];
                                    $society_name = $soc['society_name'];
                                    $city_name = $soc['city_name'];
                                    $cron_user_count = $soc['cron_user_count'];
                                    $selected_societies[$society_id] = array(
                                        'society_name' => $society_name,
                                        'city_name' => $city_name,
                                        'cron_user_count' => $cron_user_count,
                                        'society_id' => $society_id,
                                    );
                                }
                            }

                            // remove same category comapny 
                            if (isset($sameCategroyCrondCompanyIds) && count($sameCategroyCrondCompanyIds)>0) {
                                $idsCompany = join("','",$sameCategroyCrondCompanyIds);
                                $appendSameCategoryQuery = "society_id NOT IN ('$idsCompany')";
                            } else {
                              $appendSameCategoryQuery = "";
                            }

                            $q1 = $d->selectRow("society_master.*, CASE WHEN plan_expire_date < CURDATE() THEN 'YES' ELSE 'NO' END AS is_expired","society_master", "$appendSameCategoryQuery", "ORDER BY society_id ASC");
                            $counter = 1;

                            foreach ($selected_societies as $society_id => $details) {

                              $timeIncrement = $details['cron_user_count'];
                              $updatedTime = $cronTime->format('H:i');

                              $lastRunLabel = 'Last Run: -';
                              $lastRunStyle = 'color:#888;';
                              if (isset($lastRunBySociety[$society_id])) {
                                  $lastRunTime = $lastRunBySociety[$society_id]['last_run_time'];
                                  $lastRunStatus = $lastRunBySociety[$society_id]['status_code'];
                                  $lastRunStyle = ((int)$lastRunStatus === 200) ? 'color:green;' : 'color:red;';
                                  $lastRunLabel = 'Last Run: ' . $lastRunTime . ' (' . $lastRunStatus . ')';
                              }

                            ?>
                                <li class="sortable-item" data-id="<?php echo $society_id; ?>" style="cursor:move; list-style: none;">
                                    <label style="margin-left: -30px;" class="custom-control custom-checkbox error_color">
                                        (<span class="item-number"><?php echo $counter++; ?></span>)
                                        <input checked type="checkbox" class="pagePrivilege" value="<?php echo $society_id; ?>" name="society_id[]">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">
                                            <b><?= '('.$d->short_app_name() . '_' .$details['society_id'].') '. $details['society_name'] . '(' . $details['city_name'] . ') - ( '.$updatedTime.' )'; ?></b>
                                            <span style="margin-left:6px; font-weight:600; <?php echo $lastRunStyle; ?>"><?php echo htmlspecialchars($lastRunLabel); ?></span>
                                        </span>
                                    </label>
                                </li>
                            <?php
                              if (isset($updatedTime) && $updatedTime != '') {
                                $cronTime = new DateTime($updatedTime);
                                $cronTime->add(new DateInterval('PT' . max(0, (int)$timeIncrement) . 'M'));
                              }
                            }
                          ?>
                        </ul>
                        <ul>
                          <?php
                            while ($dataMenu = mysqli_fetch_array($q1)) {

                                $society_id = $dataMenu['society_id'];
                                if (isset($selected_societies[$society_id])) {
                                    continue;
                                }
                                if($dataMenu['is_expired']!='YES') { 
                            ?>
                                <li style="cursor:move; list-style: none;" >
                                    <label style="margin-left: -30px;<?php if($dataMenu['is_expired']=='YES') { echo 'color:red'; } ?>" class="custom-control custom-checkbox error_color">
                                        (<span><?php echo $counter++; ?></span>)
                                        <input type="checkbox" class="pagePrivilege" value="<?php echo $society_id; ?>" name="society_id[]">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">
                                            <b><?= '('.$d->short_app_name() . '_' .$dataMenu['society_id'].') '. $dataMenu['society_name'] . '(' . $dataMenu['city_name'] . ')'; ?> </b>
                                        </span>
                                    </label>
                                </li>
                            <?php } } ?>
                        </ul>

                    </div>
                  </div>
                </div>
                
                <div class="form-footer text-center">
                  <input type="hidden" name="cron_id" value="<?=($data) ? $data['cron_id'] : ''?>">
                  <input type="hidden" name="manage_cron" value="manage_cron">
                  <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> <?= ($data) ? 'UPDATE' : 'ADD' ?></button>
                    <button  type="reset" class="btn btn-danger"><i class="fa fa-times"></i> CANCEL</button>
                </div>
              </form>

            <?php } ?>
            </div>
          </div>
        </div>
      </div><!--End Row-->

    </div>
    <!-- End container-fluid-->
    
    </div><!--End content-wrapper-->
  <!--select icon modal -->
    <script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">

  function updateItemNumbers() {
    $('#sortable-societies li').each(function(index) {
      $(this).find('.item-number').text(index + 1);
    });
  }

  $(function() {

    $('.chk_boxes').click(function() {

        $('.pagePrivilege').prop('checked', this.checked);

    });

    updateItemNumbers();

    $("#sortable-societies").sortable({
        update: function(event, ui) {
            
            var order = [];

            $(".ajax-loader").show();
            
            $(".sortable-item").each(function(index, element) {
                order.push({
                    id: $(this).data('id'),
                    cron_id: '<?php echo $cron_id; ?>',
                    position: index + 1
                });
            });

            updateItemNumbers();

            $.ajax({
              url: 'controller/cronController.php',
              type: 'POST',
              data: { csrf: '<?php echo $_SESSION["token"]; ?>', order: order, manage_cron_society_order: 'manage_cron_society_order' },
              success: function(response) {
                $(".ajax-loader").hide();
                var jsonResponse = JSON.parse(response);
                if(jsonResponse.status == 200) {
                  swal({
                    icon: "success",
                    text: jsonResponse.message,
                    timer: 4000
                  }).then(() => {
                    window.location.reload();
                  });
                }else{
                  swal({
                    icon: "error",
                    text: jsonResponse.message,
                    timer: 4000
                  }).then(() => {
                    window.location.reload();
                  });
                }
              }
            });
        }
    });
    $("#sortable-societies").disableSelection();

    $('#draggable-checkbox').change(function() {
        if (this.checked) {
          $("#sortable-societies").sortable("enable");
        } else {
            $("#sortable-societies").sortable("disable");
        }
    });

});

</script>