<?php
extract(array_map("test_input" , $_REQUEST));
error_reporting(0);
$qcountries=$d->selectSpArray("getCountry");
$cIdsArray = explode(",", $countryids);
$countryId =  (isset($_GET['countryId']) && $_GET['countryId']>0) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId'], 101) : 101 ;
$sId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : (isset($sId) ? $d->sanitizeReportFilterIdAsInt($sId) : 0);
$cId = isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : (isset($cId) ? $d->sanitizeReportFilterIdAsInt($cId) : 0);

?>  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
          <h4 class="page-title">Recent Activities Analytics</h4>
        
        </div>
       <div class="col-sm-3 text-right">
        
         <!-- <?php // if (isset($countryId) && $countryId>0 ) {  ?> -->
          <form action="recentActivitiesGetData" method="POST">
           <input type="hidden" name="country_id" value="<?php if(isset($_GET['countryId'])){echo $_GET['countryId']; } ?>">
           <input type="hidden" name="state_id" value="<?php if(isset($_GET['sId'])){echo $_GET['sId'];} ?>">
           <input type="hidden" name="city_id" value="<?php if(isset($_GET['cId'])){echo $_GET['cId'];} ?>">
           <button type="submit" name="publishPost" value="publishPost" class="open-AddBookDialog btn btn-secondary btn-sm"><i class="fa fa-database"></i> Get Bulk Data</button>
         </form>
         <!-- <a data-toggle="modal" onclick="recentGetDataSociety()" data-target="#publishPostModal" class="open-AddBookDialog btn btn-secondary btn-sm" href="#"><i class="fa fa-database"></i> Get Bulk Data</a> -->
     </div>
     </div>
    <!-- End Breadcrumb-->
     <div class="row pt-2 pb-2">
      <div class="col-lg-12">
        <form action="" method="get" accept-charset="utf-8">

          <div class="form-group row">
            <label for="country_id" class="col-sm-1 col-form-label"> Country <span class="required">*</span></label>
            <div class="col-sm-3">
              <select type="text" required="" id="country_id" onchange="this.form.submit()"  class="form-control single-select" name="countryId">
                <option value="">-- Select  Country--</option>
                <?php 
                for ($ic=0; $ic <count($qcountries) ; $ic++) { 
                  ?>
                  <option <?php if( isset($countryId) && $qcountries[$ic]['country_id']==$countryId) {echo "selected";} ?> value="<?php echo $qcountries[$ic]['country_id'];?>"><?php echo $qcountries[$ic]['name'];?></option>
                <?php }?>
              </select>
            </div>
            <label for="state_id" class="col-sm-1 col-form-label"> State </label>
            <div class="col-sm-3">
              <?php  if(isset($countryId)) {
               ?>
                <select type="text" onchange="this.form.submit()"  required="" class="form-control single-select" id="state_id" name="sId">
                  <option value=""> All</option>
                  <?php
                   $qs=$d->select("states","country_id='$countryId'");
                  while ($sData=mysqli_fetch_array($qs)) {
                    ?>
                    <option <?php if( isset($_GET['sId']) && $sData['state_id']==$_GET['sId']) {echo "selected";} ?> value="<?php echo $sData['state_id'];?>"><?php echo $sData['name'];?></option>
                  <?php }  ?>
                </select>
              <?php } else { ?>
                <select type="text" onchange="getCity();"  required="" class="form-control single-select" id="state_id" name="sId">
                  <option value="">-- Select --</option>
                </select>
              <?php } ?>
            </div>

            <label for="input-101" class="col-sm-1 col-form-label"> City</label>
            <div class="col-sm-3">
              <?php  if(isset($_GET['cId']) && $sId>0) {
               
               ?>
                <select onchange="this.form.submit()" type="text"  required="" class="form-control single-select" id="city_id" name="cId">
                  <option value=""> All</option>
                  <?php
                  if (isset($sId) && $sId>0) {
                      $appendStateQueryFilter = "state_id='$sId'";
                    }

                  $qcity=$d->select("cities"," $appendStateQueryFilter");
                  while ($cityData=mysqli_fetch_array($qcity)) {
                    ?>
                    <option <?php if( isset($_GET['cId']) && $cityData['city_id']==$_GET['cId']) {echo "selected";} ?> value="<?php echo $cityData['city_id'];?>"><?php echo $cityData['name'];?></option>
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
     <?php if (isset($countryId) && $countryId>0) {  ?>

      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
            <div class="card-body">
              <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                    <tr>
                      <th>#</th>
                      <th><?php echo $xml->string->society; ?> Id</th>
                      <th><?php echo $xml->string->society; ?> Name</th>
                      <th>City</th>
                      <th>Mobile</th>
                      <th>Plan</th>
                      <th>Plan Expire</th>
                    </tr>
                </thead>
                <tbody>
                   <?php 
                      $i=1;
                      if (isset($sId) && $sId>0) {
                        $appendStateQuery = " AND state_id='$sId'";
                      }

                      if (isset($cId) && $cId>0) {
                        $appendCityQuery = " AND city_id='$cId'";
                      }

          
                    $q = $d->select("society_master","country_id='$countryId' $appendStateQuery $appendCityQuery ","order by society_id  DESC");
                      $societyRows = [];
                      $societyIds = [];
                      $planValues = [];
                      while ($data=mysqli_fetch_array($q)) {
                        $societyRows[] = $data;
                        $societyIds[] = (int)$data['society_id'];
                        $planValues[] = (int)$data['package_id'];
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

                      $planByValue = [];
                      $planValues = array_unique(array_map('intval', $planValues));
                      if (!empty($planValues)) {
                        $planValuesIn = implode(',', $planValues);
                        $qw2 = $d->select("manage_plan", "plan_value IN ($planValuesIn)");
                        while ($planRow = mysqli_fetch_array($qw2)) {
                          $planByValue[(int)$planRow['plan_value']] = $planRow;
                        }
                      }

                      foreach ($societyRows as $data) {
                        extract($data);
                        $data11 = $adminBySociety[(int)$society_id] ?? [];
                        $admin_password = $data11['admin_password'] ?? null;
                        $row = $planByValue[(int)$package_id] ?? null;
                       ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo ''.$d->short_app_name().'_'.$society_id; ?></td>
                        <td><?php echo $society_name; ?></td>
                        <td><?php echo $city_name; ?></td>
                        <td><?php echo $secretary_mobile; ?></td>
                        <td><?php 
                            if ($row) {
                              echo $row['plan_name'];
                            } 
                            if ($row && $row['plan_value']=="0") {
                              echo "(".$data['trial_days']." Days)";
                            }
                         ?></td>
                         <td><?php echo $plan_expire_date; ?></td>
                        
                        
                    </tr>
                  <?php } ?>
                </tbody>
                
            </table>
            </div>
            </div>
          </div>
        </div>
      </div><!-- End Row-->
       <?php } else {
        echo "Select Country";
      } ?>
    </div>
    <!-- End container-fluid-->
    
    </div><!--End content-wrapper-->
   <!--Start Back To Top Button-->

  

<div class="modal fade" id="editFloor">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white"><?php echo $xml->string->society; ?> Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="BlockResp">
          
      </div>
     
    </div>
  </div>
</div><!--End Modal -->

<script type="text/javascript">
  
function  getSocietyData1(society_id) {
    var csrf =$('input[name="csrf"]').val();
    $('#BlockResp').html("Please Wait..!");
  $.ajax({
        url: "controller/cronGetData.php",
        cache: false,
        type: "POST",
        data: {society_id : society_id,csrf:csrf},
        success: function(response){
            $('#BlockResp').html(response);
            
        }
     });
}
</script>


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
          <div id="sosa_detail"></div>
          <div id="chkError" class=""></div>
          <div class="form-footer text-center">
            <button type="submit" name="publishPost" value="publishPost" class="btn btn-sm btn-success publishPost"><i class="fa fa-check-square-o"></i> Get Data</button>
          </div>
        </form> 
      </div>
    </div>
  </div>
</div>


<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
 
  $(".common_slider_delete").click(function(){ 
    var elmId = $(this). attr("id");
    if(this.checked) {
      $('#'+elmId+'_hidden').prop('checked', true);
    } else {
      $('#'+elmId+'_hidden').prop('checked', false);
    }
  });
</script>

<script type="text/javascript">
  $('.publishPost').click(function(){
    $(this).prop('disabled', true);
    this.form.submit()
  });

</script>