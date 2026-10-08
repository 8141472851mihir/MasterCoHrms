<?php
extract($_REQUEST);
// error_reporting(0);
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
?>

<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Master Login Auth Management</h4>
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
        </div>
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
                    <th class='deleteTh'>#</th>
                    <th>Mobile</th>
                    <th>Password</th>
                    <th>Last Updated Date</th>
                    <th>Updated By</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $q=$d->select("master_user_auth_master","","LIMIT 1");
                    $i = 1;
                    $row=mysqli_fetch_array($q);
                    $masterAuth = $row['auth_passs'];
                  ?>
                    <tr>

                      <td><?php echo $i++; ?></td>
                      <td><?php echo '9687271071'; ?></td>
                      <td><?php echo $row['auth_passs'] ?></td>
                      <td><?php 
                         if ($default_time_zone!="Asia/Kolkata") {
                              echo $d->change_timezone($row['updated_date'],$default_time_zone,'d M Y h:i A');
                          } else {
                        echo $row['updated_date']; 
                      } ?></td>
                      <td><?php 
                        $qad=$d->select("bms_admin_master","admin_id='$row[updated_by]'");
                            $adminData=mysqli_fetch_array($qad);
                           echo $adminData['admin_name'];
                        ?></td>
                      <td>
                        <?php 
                          echo $changedSociety=  $d->count_data_direct("auth_log_id","auth_log_master","auth_password='$row[auth_passs]' AND auth_log_master.status=200"); ;
                          echo "/";
                        echo $totalSociety = $d->count_data_direct("society_id","society_master",""); 
                        if ($changedSociety<$totalSociety) {
                         ?>
                           <form id="postForm" action="masterAuthAll" method="POST" style="display:none;">
                              <input type="hidden" name="masterAuthPassword" class="masterAuthPassword">
                              <input type="hidden" name="country_id" class="country_id">
                              <input type="hidden" name="state_id" class="state_id">
                              <input type="hidden" name="city_id" class="city_id">
                              <input type="hidden" name="token" class="token">
                            </form>
                            <a onclick="submitPostForm('<?php echo $row['auth_passs'] ; ?>','<?php echo $_SESSION["token"] ; ?>','<?php echo $_GET['countryId'] ; ?>','<?php echo $_GET['sId'] ; ?>','<?php echo $_GET['cId'] ; ?>')" data-backdrop="static" data-keyboard="false"  data-toggle="modal" data-target="#publishPostModal" class="open-AddBookDialog btn btn-secondary btn-sm" href="#"><i class="fa fa-bullhorn"></i> Change In Remaining </a>
                        <?php } ?>
                      </td>
                      <td>
                        
                          <a href="javascript:void(0)" data-toggle="modal" data-target="#changePassword" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                        
                      </td>
                    </tr>
                </tbody>

              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row pt-2 pb-2">
      <div class="col-lg-12">
        <form action="" method="get" accept-charset="utf-8">

          <div class="form-group row">
            <label for="country_id" class="col-sm-1 col-form-label"> Country <span class="required">*</span></label>
            <div class="col-sm-3">
              <select type="text" required="" id="country_id" onchange="this.form.submit()"  class="form-control single-select country_id_getSociety" name="countryId">
                <option value="">-- Select --</option>
                <?php 
                $qc=$d->select("countries","flag=1");
                  while ($cData=mysqli_fetch_array($qc)) {
                  ?>
                  <option <?php if( isset($_GET['countryId']) && $cData['country_id']==$_GET['countryId']) {echo "selected";} ?> value="<?php echo $cData['country_id'];?>"><?php echo $cData['name'];?></option>
                <?php }?>
              </select>
            </div>
            <label for="state_id" class="col-sm-1 col-form-label"> State <span class="required">*</span></label>
            <div class="col-sm-3">
              <?php  if(isset($_GET['sId'])) {
               ?>
                <select type="text" onchange="this.form.submit()" required="" class="form-control single-select state_id_getSociety" id="state_id" name="sId">
                  <?php
                   $countryIdFilter = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId']) : 0;
                   $qs=$d->select("states","country_id=$countryIdFilter");
                  while ($sData=mysqli_fetch_array($qs)) { 
                    ?>
                    <option <?php if( isset($_GET['sId']) && $sData['state_id']==$_GET['sId']) {echo "selected";} ?> value="<?php echo $sData['state_id'];?>"><?php echo $sData['name'];?></option>
                  <?php }  ?>
                </select>
              <?php } else { ?>
                <select type="text"  onchange="this.form.submit()" required="" class="form-control single-select" id="state_id" name="sId">
                  <option value="">-- Select --</option>
                </select>
              <?php } ?>
            </div>

            <label for="input-101" class="col-sm-1 col-form-label"> City </label>
            <div class="col-sm-2">
              <?php  if(isset($_GET['cId']) && $_GET['cId']>0) {
              
               ?>
                <select  onchange="this.form.submit()" type="text"  class="form-control single-select city_id_getSociety" id="city_id" name="cId">
                  <option value="">-- Select City --</option>
                  <?php
                    $sIdFilter = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
                    $qcity=$d->select("cities","state_id=$sIdFilter");
                  while ($cityData=mysqli_fetch_array($qcity)) {
                    ?>
                    <option <?php if( isset($_GET['cId']) && $cityData['city_id']==$_GET['cId']) {echo "selected";} ?> value="<?php echo $cityData['city_id'];?>"><?php echo $cityData['name'];?></option>
                  <?php }  ?>
                </select>
              <?php } else { ?>
                <select onchange="this.form.submit()" type="text" required="" class="form-control single-select" name="cId" id="city_id">
                  <option value="">-- Select --</option>

                </select>
              <?php } ?>
            </div>
           <!--  <div for="input-101" class="col-sm-1">
              <button class="btn btn-success" type="submit">Filter</button>
            </div> -->
          </div>
        </form>
      </div>
    </div>

    <?php if (isset($countryId) ) {  ?>

      <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <ul class="nav nav-tabs nav-tabs-info nav-justified">
            <li class="nav-item">
              <a class="nav-link active" data-toggle="tab" href="#tabe-13"> Success</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link  show" data-toggle="tab" href="#tabe-14">  Pending</span></a>
            </li>
            
          </ul>
          <div class="tab-content">
            <div id="tabe-13" class="container-fluid tab-pane active show">
              <div class="table-responsive">
                <table id="checkAllwithAllDelete" class="table table-bordered">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>City</th>
                      <th>Url</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                      $i=1;
                      $alreadySyc = array();
                      if (isset($cId) && $cId>0) {
                        $appendCityQuery = " AND society_master.city_id='$cId'";
                      }

                      if (isset($sId) && $sId>0) {
                        $appendStateQuery = " AND society_master.state_id='$sId'";
                      }
                      $q=$d->select("society_master,auth_log_master","auth_log_master.society_id=society_master.society_id AND auth_log_master.auth_password='$masterAuth' AND society_master.country_id='$countryId' AND auth_log_master.status=200 $appendStateQuery $appendCityQuery","ORDER BY society_master.society_id DESC");
                      while ($data=mysqli_fetch_array($q)) {
                        array_push($alreadySyc, $data['society_id']);
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td> <a onclick="checkMasterPassword('<?php echo $data['society_id']; ?>','<?php echo $data['sub_domain']; ?>','<?php echo $masterAuth; ?>')"  class="btn btn-secondary btn-sm" href="javascript:void(0)"><i class="fa fa-refresh"></i> </a>  <?php echo $data['society_name']; ?></td>
                      <td><?php echo $data['city_name']; ?></td>
                      <td><?php echo $data['sub_domain']; ?></td>
                      <td><?php echo $data['created_at']; ?></td>
                      
                    </tr>
                    <?php } ?> 
                  </tbody>
                </table>
              </div>
            </div>

            <div id="tabe-14" class="container-fluid tab-pane ">
              <?php 
                      $i=1;
                       $ids = join("','",$alreadySyc);
                      $q11=$d->select("society_master","country_id='$countryId' AND society_id NOT IN ('$ids') ","ORDER BY society_id DESC");

                if (mysqli_num_rows($q11)>0) {
                   ?>
              <div class="text-right mb-1">
                  <form id="postForm" action="masterAuthAll" method="POST" style="display:none;">
                    <input type="hidden" name="masterAuthPassword" class="masterAuthPassword">
                    <input type="hidden" name="country_id" class="country_id">
                    <input type="hidden" name="state_id" class="state_id">
                    <input type="hidden" name="city_id" class="city_id">
                    <input type="hidden" name="token" class="token">
                  </form>
                  <a onclick="submitPostForm('<?php echo $row['auth_passs'] ; ?>','<?php echo $_SESSION["token"] ; ?>','<?php echo $_GET['countryId'] ; ?>','<?php echo $_GET['sId'] ; ?>','<?php echo $_GET['cId'] ; ?>')" data-backdrop="static" data-keyboard="false"  data-toggle="modal" data-target="#publishPostModal" class="open-AddBookDialog btn btn-secondary btn-sm" href="#"><i class="fa fa-bullhorn"></i> Change In Remaining </a>
              </div>
            <?php } ?>
              <div class="table-responsive">
                <table id="default-datatable" class="table table-bordered">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>City</th>
                      <th>Url</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      while ($data=mysqli_fetch_array($q11)) {
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo $data['society_name']; ?></td>
                      <td><?php echo $data['city_name']; ?></td>
                      <td><?php echo $data['sub_domain']; ?></td>
                      
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
</div>



    <?php } ?>

  </div>
</div>

<div class="modal fade" id="changePassword">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Change Password</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form id="chngProfileFrm" action="controller/profileController.php" method="post">
             <input type="hidden" name="cPassword"  value="<?php echo $masterAuth ; ?>">
             <div class="form-group row">
              <label class="col-lg-3 col-form-label form-control-label">Password <span class="required">*</span></label>
              <div class="col-lg-9">
               <?php //IS_577   id="password"?> 
               <input class="form-control" required="" type="password" name="password" id="password" value="">
             </div>
           </div>
           <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Confirm password <span class="required">*</span></label>
            <div class="col-lg-9">
              <?php //IS_577   id="password2"?> 
              <input class="form-control" required="" name="password2" id="password2" type="password" value="">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label"></label>
            <div class="col-lg-9">
              <input type="hidden" value="masterpasswordChange" name="masterpasswordChange">
              <input type="submit" name="" class="btn btn-primary" value="Change Password">
            </div>
          </div>
        </form>
      </div>
     
    </div>
  </div>
</div>
<script>
    function submitPostForm(masterAuthPassword, token, country_id, state_id, city_id) {
        document.getElementsByClassName('masterAuthPassword')[0].value = masterAuthPassword;
        document.getElementsByClassName('token')[0].value = token;
        document.getElementsByClassName('country_id')[0].value = country_id;
        document.getElementsByClassName('state_id')[0].value = state_id;
        document.getElementsByClassName('city_id')[0].value = city_id;
        document.getElementById('postForm').submit();
    }
</script>