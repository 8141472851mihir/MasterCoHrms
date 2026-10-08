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
        <h4 class="page-title">Resent Activities Analytics Report</h4>
        
      </div>
      <div class="col-sm-8 text-right">
         <?php  ?>
         <a data-toggle="modal" onclick="resentGetDataSociety()" data-target="#publishPostModal" class="open-AddBookDialog btn btn-secondary btn-sm" href="#"><i class="fa fa-database"></i> Get Bulk Data</a>
        <?php  ?>
      </div>
    </div>
    <!-- End Breadcrumb-->
     <div class="row pt-2 pb-2">
      <div class="col-lg-12">
        <form action="" method="get" accept-charset="utf-8">

          <div class="form-group row">
            <label for="country_id" class="col-sm-1 col-form-label"> Country <span class="required">*</span></label>
            <div class="col-sm-3">
              <select type="text" required="" id="country_id" onchange="getStates();" class="form-control single-select" name="countryId">
                <option value="">-- Select --</option>
                <?php 
                for ($ic=0; $ic <count($qcountries) ; $ic++) { 
                  if(in_array($qcountries[$ic]['country_id'], $cIdsArray)){
                  ?>
                  <option <?php if( isset($_GET['countryId']) && $qcountries[$ic]['country_id']==$_GET['countryId']) {echo "selected";} ?> value="<?php echo $qcountries[$ic]['country_id'];?>"><?php echo $qcountries[$ic]['name'];?></option>
                <?php } }?>
              </select>
            </div>
            <label for="state_id" class="col-sm-1 col-form-label"> State <span class="required">*</span></label>
            <div class="col-sm-3">
              <?php  if(isset($_GET['sId'])) {
                $countryIdFilter = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId']) : 0;
                $qstates=$d->selectSpArray("getState('$countryIdFilter')");
                
               ?>
                <select type="text" onchange="getCity();"  required="" class="form-control single-select" id="state_id" name="sId">
                  <?php
                   for ($is=0; $is <count($qstates) ; $is++) { 
                    ?>
                    <option <?php if( isset($_GET['sId']) && $qstates[$is]['state_id']==$_GET['sId']) {echo "selected";} ?> value="<?php echo $qstates[$is]['state_id'];?>"><?php echo $qstates[$is]['name'];?></option>
                  <?php }  ?>
                </select>
              <?php } else { ?>
                <select type="text"  onchange="getCity();"  required="" class="form-control single-select" id="state_id" name="sId">
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
        </form>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        
        <div class="card-body">
          <div class="table-responsive">
            <table id="reportTable" class="table table-bordered">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Company</th>
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
                  <th>City</th>
                  <th>Days</th>
                  <th>Address</th>
                  <th>Updated Date</th>
                </tr>
              </thead>
              <tfoot>
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
                  
                  $q=$d->select("society_resent_analytics_master,society_master","society_master.society_id=society_resent_analytics_master.society_id ","order by society_resent_analytics_master.resent_analytics_id $appendCountry $appendState $appendCity","");
                  while ($data=mysqli_fetch_array($q)) {
                    extract($data);
                ?>
                <tr>
                  <td><?php echo $i++; ?></td>
                  <td><?php echo $society_name; ?></td>
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
                  <td><?php echo $city_name; ?></td>
                  <td><?php echo $days; ?></td>
                  <td><?php echo $society_address; ?></td>
                  <td><?php echo $update_date ; ?></td>
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
        <form id="resentpublishSocetyDataFrm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
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