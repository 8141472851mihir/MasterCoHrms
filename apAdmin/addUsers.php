<?php 
    $aq=$d->select("bms_admin_master,role_master","role_master.role_id=bms_admin_master.role_id AND bms_admin_master.admin_id='$bms_admin_id'");
    $adminData= mysqli_fetch_array($aq);
    $roleId=   $adminData['role_id'];
    $mycoEmployees = array();
    $empResponse = $d->callCompanyApiEnc($d->company_url(), 'getAllEmployeesController.php', array(
      'getAllEmployees' => 'getAllEmployees',
      'society_id' => $d->company_id(),
    ));
    if (isset($empResponse['employees']) && is_array($empResponse['employees'])) {
      $mycoEmployees = $empResponse['employees'];
    }
    ?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Add User</h4>
      </div>
    </div>
    <!-- End Breadcrumb-->

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="addUserValidation" action="controller/userController.php" method="post" >

              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Name <span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" maxlength="50" required="" name="admin_name">
                </div>
                <label class="col-sm-2 col-form-label">Role <span class="required">*</span></label>
                <div class="col-sm-4">
                  <select class="form-control single-select" name="role_id">
                    <option value=""></option>
                    <?php 
                    if($global_role_id==12 || $global_role_id==14) {
                        $query = $d->select("role_master","role_id!=1 AND role_id IN ('12','14','26')");
                    } else {
                        $query = $d->select("role_master","role_id!=1");
                    }
                      while ($roleData = mysqli_fetch_array($query)) { ?>
                        <option value="<?php echo $roleData['role_id'] ?>"><?php echo $roleData['role_name'] ?></option>
                    <?php  } ?>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Mobile <span class="required">*</span></label>
                <div class="col-lg-2">
                    <select name="country_code" class="form-control single-select" id="country_code" required="">
                      <?php include 'country_code_option_list.php'; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                  <input type="text" maxlength="13" minlength="8" class="form-control" name="admin_mobile"  required="">
                </div>
                <label class="col-sm-2 col-form-label">Email <span class="required">*</span></label>
                <div class="col-sm-4" >
                  <input type="email" maxlength="100" class="form-control" name="admin_email" required="">
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Countries Access <span class="required">*</span></label>
                <div class="col-sm-4" >
                  <select  type="text" required="" class="form-control multiple-select" name="country_id[]" multiple="multiple">
                    <option value="">-- Select--</option>
                    <?php 
                    $qc=$d->select("countries","flag=1");
                      while ($cData=mysqli_fetch_array($qc)) {
                     ?>
                      <option  value="<?php echo $cData['country_id'];?>"><?php echo $cData['name'];?> </option>
                      <?php }?>
                    </select>
                </div>
                <label class="col-sm-2 col-form-label">FCM Notifications</label>
                <div class="col-sm-4" >
                  <select  type="text" class="form-control multiple-select" name="fcm_notifications[]" multiple="multiple">
                    <option value="">-- Select--</option>
                    <option value="1">App Support</option>
                    <option value="2">Web Feedback</option>
                    <option value="3">Company Request</option>
                    <!-- <option value="4">Feedback By Admin</option> -->
                    <option value="5">Sales Inquiry</option>
                    <option value="6">Forward to developer</option>
                    <option value="7">Close by developer</option>
                    <option value="8">Server Notification</option>
                  </select>
                </div>
                
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Primary Language <span class="required">*</span></label>
                <div class="col-sm-4" >
                   <select  type="text" required="" id="primary_language_id" class="form-control single-select" name="primary_language_id" >
                    <option value="">-- Select--</option>
                    <?php     
                    $ql=$d->select("language_master","active_status=0 ","");
                     while($ldata=mysqli_fetch_array($ql)) { ?>
                      <option value="<?php echo $ldata['language_id'];?>"><?php echo $ldata['language_name'].'-'.$ldata['language_name_1'];?></option>
                    <?php } ?>

                  </select>
                </div>
                <label for="financial_year_start" class="col-sm-2 col-form-label">Timezone <span class="required">*</span></label>
                <div class="col-sm-4">
                  <select class="form-control single-select" name="default_time_zone" required="">
                    <option value="">-- Select --</option>
                    <?php $qt = $d->select("timezoneMaster","");
                    while ($timeData=mysqli_fetch_array($qt)) { ?>
                      <option <?php if($default_time_zone==$timeData['timezone_name']) { echo 'selected'; } ?> value="<?php echo $timeData['timezone_name'];?>"><?php echo $timeData['timezone_name'];?>-<?php echo $timeData['timezone_category'];?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Is Developer <span class="required">*</span></label>
                <div class="col-sm-4" >
                   <select  type="text" required="" id="is_developer" class="form-control single-select" name="is_developer">
                    <option value="">-- Select--</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Bind MyCo User</label>
                <div class="col-sm-4">
                  <select class="form-control single-select" id="myco_user_bind" name="user_id">
                    <option value="">-- Select MyCo User (Optional) --</option>
                    <?php foreach ($mycoEmployees as $emp) {
                      $empUserId = isset($emp['user_id']) ? $emp['user_id'] : '';
                      $empName = isset($emp['user_full_name']) ? $emp['user_full_name'] : '';
                      $empDept = isset($emp['department_name']) ? $emp['department_name'] : '';
                      $empBranch = isset($emp['branch_name']) ? $emp['branch_name'] : '';
                      $empDesignation = isset($emp['user_designation']) ? $emp['user_designation'] : '';
                      $labelParts = array_filter(array($empDept, $empBranch));
                      $optionLabel = $empName;
                      if (!empty($labelParts)) {
                        $optionLabel .= ' (' . implode(' - ', $labelParts) . ')';
                      }
                    ?>
                      <option value="<?php echo htmlspecialchars($empUserId); ?>"
                        data-user-full-name="<?php echo htmlspecialchars($empName); ?>"
                        data-branch-name="<?php echo htmlspecialchars($empBranch); ?>"
                        data-user-designation="<?php echo htmlspecialchars($empDesignation); ?>"
                        data-department-name="<?php echo htmlspecialchars($empDept); ?>">
                        <?php echo htmlspecialchars($optionLabel); ?>
                      </option>
                    <?php } ?>
                  </select>
                  <input type="hidden" name="user_full_name" id="bind_user_full_name">
                  <input type="hidden" name="branch_name" id="bind_branch_name">
                  <input type="hidden" name="user_designation" id="bind_user_designation">
                  <input type="hidden" name="department_name" id="bind_department_name">
                </div>
              </div>
              
              <div class="form-footer text-center">
                <input type="hidden" name="addUser">
                <button value="add Page" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> ADD</button>
                <button  type="reset" class="btn btn-danger"><i class="fa fa-times"></i> CANCEL</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script>
$(document).ready(function(){
  function syncMycoUserBindFields() {
    var $opt = $('#myco_user_bind option:selected');
    if (!$opt.val()) {
      $('#bind_user_full_name, #bind_branch_name, #bind_user_designation, #bind_department_name').val('');
      return;
    }
    $('#bind_user_full_name').val($opt.data('user-full-name') || '');
    $('#bind_branch_name').val($opt.data('branch-name') || '');
    $('#bind_user_designation').val($opt.data('user-designation') || '');
    $('#bind_department_name').val($opt.data('department-name') || '');
  }

  $('#myco_user_bind').on('change', syncMycoUserBindFields);
  syncMycoUserBindFields();
});
</script>
