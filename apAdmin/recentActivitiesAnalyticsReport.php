<?php
extract(array_map("test_input" , $_REQUEST));
error_reporting(0);
$qcountries=$d->selectSpArray("getCountry");
$cIdsArray = explode(",", $countryids);
$sId = $d->sanitizeReportFilterIdAsInt($sId);
$countryId = $d->sanitizeReportFilterIdAsInt($countryId, 101);
$cId = $d->sanitizeReportFilterIdAsInt($cId);
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-4">
        <h4 class="page-title">Recent Activities Analytics Report</h4>
      </div>
      <div class="col-sm-8 text-right">
        <?php if(isset($cId) && $cId>0){ ?>
          <form action="recentActivitiesGetData" method="POST">
            <input type="hidden" name="country_id" value="<?php if(isset($_GET['countryId'])){echo $_GET['countryId']; } ?>">
            <input type="hidden" name="state_id" value="<?php if(isset($_GET['sId'])){echo $_GET['sId'];} ?>">
            <input type="hidden" name="city_id" value="<?php if(isset($_GET['cId'])){echo $_GET['cId'];} ?>">
            <button type="submit" name="publishPost" value="publishPost" class="open-AddBookDialog btn btn-secondary btn-sm"><i class="fa fa-database"></i> Get Bulk Data</button>
          </form>
          <?php
        } ?>
      </div>
    </div>
    <!-- End Breadcrumb-->
     <div class="row pt-2 pb-2">
      <div class="col-lg-12">
        <form action="" method="get" accept-charset="utf-8">
          <div class="form-group row">
            <label for="country_id" class="col-sm-1 col-form-label"> Country <span class="required">*</span></label>
            <div class="col-sm-3">
              <select type="text" required="" id="country_id" onchange="this.form.submit()" class="form-control single-select" name="countryId">
                <option value="">-- Select --</option>
                <?php 
                for($ic=0;$ic<count($qcountries);$ic++){
                  if(in_array($qcountries[$ic]['country_id'], $cIdsArray)){
                    ?>
                    <option <?php if( isset($_GET['countryId']) && $qcountries[$ic]['country_id']==$_GET['countryId']) {echo "selected";} ?> value="<?php echo $qcountries[$ic]['country_id'];?>"><?php echo $qcountries[$ic]['name'];?></option>
                    <?php
                  }
                }?>
              </select>
            </div>
            <label for="state_id" class="col-sm-1 col-form-label"> State <span class="required">*</span></label>
            <div class="col-sm-3">
              <?php if(isset($_GET['sId'])){
                $countryIdFilter = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId']) : 0;
                $qstates=$d->selectSpArray("getState('$countryIdFilter')");
                ?>
                <select type="text" onchange="this.form.submit()"  required="" class="form-control single-select" id="state_id" name="sId">
                  <option value="">Select</option>
                  <?php
                  for ($is=0; $is <count($qstates) ; $is++) { 
                    ?>
                    <option <?php if( isset($_GET['sId']) && $qstates[$is]['state_id']==$_GET['sId']) {echo "selected";} ?> value="<?php echo $qstates[$is]['state_id'];?>"><?php echo $qstates[$is]['name'];?></option>
                    <?php
                  } ?>
                </select>
              <?php }else{ ?>
                <select type="text"  onchange="this.form.submit()" required="" class="form-control single-select" id="state_id" name="sId">
                  <option value="">-- Select --</option>
                </select>
              <?php } ?>
            </div>
            <label for="input-101" class="col-sm-1 col-form-label"> City <span class="required">*</span></label>
            <div class="col-sm-3">
              <?php  if(isset($_GET['cId'])) {
                $sIdFilter = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
                $qcities=$d->selectSpArray("getCity('$sIdFilter')");
                ?>
                <select onchange="this.form.submit()" type="text"  required="" class="form-control single-select" id="city_id" name="cId">
                  <option value="">Select</option>
                  <?php
                  for ($icity=0; $icity <count($qcities) ; $icity++) { 
                    ?>
                    <option <?php if( isset($_GET['cId']) && $qcities[$icity]['city_id']==$_GET['cId']) {echo "selected";} ?> value="<?php echo $qcities[$icity]['city_id'];?>"><?php echo $qcities[$icity]['name'];?></option>
                  <?php }  ?>
                </select>
              <?php } else { ?>
                <select  onchange="this.form.submit()" type="text" required="" class="form-control single-select" name="cId" id="city_id">
                  <option value="">-- Select --</option>

                </select>
              <?php } ?>
            </div>
           
          </div>
           <div class="form-group row">
            <label for="serverId" class="col-sm-1 col-form-label"> Server <span class="required">*</span></label>
            <div class="col-sm-3">
              <select type="text" required="" id="serverId" onchange="this.form.submit()" class="form-control single-select" name="serverId">
                <option value="0">-- Select --</option>
                <?php 
                $serverQry=$d->select("server_master","server_active_status='0'");
                while($server_data=mysqli_fetch_assoc($serverQry)){
                  ?>
                  <option <?php if( isset($_GET['serverId']) && $server_data['server_id']==$_GET['serverId']) {echo "selected";} ?> value="<?php echo $server_data['server_id'];?>"><?php echo $server_data['server_name'];?></option>
                <?php  }?>
              </select>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table id="reportTable1" class="table table-bordered">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Company</th>
                  <th>City</th>
                  <th>Days</th>
                  <th>Attendance Count</th>
                  <th>Leave Count</th>
                  <th>Tracking Count</th>
                  <th>Salary Count</th>
                  <th>Assets Count</th>
                  <th>Expenses Count</th>
                  <th>Work Report Count</th>
                  <th>Task Count</th>
                  <th>Order Count</th>
                  <th>Visit Count</th>
                  <th>Circular Count</th>
                  <th>Document Count</th>
                  <th>Discussion Count</th>
                  <th>Status</th>
                  <th>Message</th>
                  <th>Address</th>
                  <th>Updated Date</th>
                </tr>
              </thead>
              <tfoot class="bottom-footer">
                <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th class="find-count"></th>
                  <th class="find-count"></th>
                  <th class="find-count"></th>
                  <th class="find-count"></th>
                  <th class="find-count"></th>
                  <th class="find-count"></th>
                  <th class="find-count"></th>
                  <th class="find-count"></th>
                  <th class="find-count"></th>
                  <th class="find-count"></th>
                  <th class="find-count"></th>
                  <th class="find-count"></th>
                  <th class="find-count"></th>
                  <th class="find-count"></th>
                  <th class="find-count"></th>
                  <th></th>
                  <th></th>
                  <th></th>
                </tr>
              </tfoot>
              <tbody>
                <?php
                  $i=1; 
                  if (isset($cId) && $cId>0) {
                    $appendCity = " AND society_master.city_id='$cId'";
                  }
                  if (isset($countryId) && $countryId>0) {
                    $appendCountry = " AND society_master.country_id='$countryId'";
                  }
                  if (isset($sId) && $sId>0) {
                    $appendState = " AND society_master.state_id='$sId'";
                  }
                  if (isset($serverId) && $serverId>0) {
                    $appendServer = " AND server_master.server_id='$serverId'";
                  }
                  $q=$d->select("society_resent_analytics_master,society_master LEFT JOIN domain_master ON society_master.domain_id=domain_master.domain_id LEFT JOIN server_master ON server_master.server_id=domain_master.server_id","society_master.society_id=society_resent_analytics_master.society_id $appendCountry $appendState $appendCity $appendServer","order by society_resent_analytics_master.resent_analytics_id");
                  // $q=$d->select("society_resent_analytics_master,society_master","society_master.society_id=society_resent_analytics_master.society_id $appendCity","order by society_resent_analytics_master.resent_analytics_id $appendCountry $appendState $appendCity","");
                  while ($data=mysqli_fetch_array($q)) {
                    extract($data);
                ?>
                <tr>
                  <td><?php echo $i++; ?></td>
                  <td><?php echo $society_name; ?></td>
                  <td><?php echo $city_name; ?></td>
                  <td><?php echo $days; ?></td>
                  <td><?php echo $attendance_count; ?></td>
                  <td><?php echo $leave_count; ?></td>
                  <td><?php echo $tracking_count; ?></td>
                  <td><?php echo $salary_count; ?></td>
                  <td><?php echo $assets_count; ?></td>
                  <td><?php echo $expenses_count; ?></td>
                  <td><?php echo $work_report_count; ?></td>
                  <td><?php echo $task_count; ?></td>
                  <td><?php echo $order_count; ?></td>
                  <td><?php echo $visit_count; ?></td>
                  <td><?php echo $circular_count; ?></td>
                  <td><?php echo $document_count; ?></td>
                  <td><?php echo $discussion_count; ?></td>
                  <td><?php echo $status; ?></td>
                  <td><?php echo $message; ?></td>
                  <td><?php echo $society_address; ?></td>
                  <td><?php echo $update_date ; ?></td>
                </tr>
                <?php } ?> 
              </tbody>
              <tfoot  class="top-footer">
                <tr>
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
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="publishPostModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Get <?php echo $xml->string->society; ?> Data</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="recentpublishSocetyDataFrm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
          <input type="hidden" name="countryId" id="countryId" value="<?php echo $countryId;?>">
          <input type="hidden" name="sId" id="sId" value="<?php echo $sId;?>">
          <input type="hidden" name="cId" id="cId" value="<?php echo $cId;?>">
          <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" />
          <label class="col-sm-2 col-form-label">Days <span class="required">*</span></label>
          <div class="col-sm-4">
            <input type="text" autocomplete="off" maxlength="2" min="1" max="90" class="form-control" name="days" value="30" required="">
          </div>
          <div id="sosa_detail">
          </div>
          <div id="chkError" class=""></div>
          <div class="form-footer text-center">
            <button type="submit" name="publishPost" value="publishPost" class="btn btn-sm btn-success publishPost"><i class="fa fa-check-square-o"></i> Get Data</button>
          </div>
        </form> 
      </div>
    </div>
  </div>
</div>