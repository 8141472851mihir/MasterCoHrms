<?php
 $admin_id = isset($_POST['admin_id']) ? $d->sanitizeActionIdAsInt($_POST['admin_id']) : 0;
 $userDetails = $d->selectArray("bms_admin_master","admin_id='$admin_id'");
 $countrykAry = array();
 $fcmkAry = array();
  $duCheck=$d->select("admin_country_master","bms_admin_id='$admin_id' ");
  while ($oldBlock=mysqli_fetch_array($duCheck)) {
      array_push($countrykAry , $oldBlock['country_id']);
  }
  $fuCheck=$d->select("admin_fcm_notification_master","bms_admin_id='$admin_id' ");
  while ($oldFcm=mysqli_fetch_array($fuCheck)) {
      array_push($fcmkAry , $oldFcm['fcm_notifications']);
  }

$is_developer = $userDetails['is_developer'];
$allowedPlatformRaw = !empty($userDetails['allowed_platforms']) ? $userDetails['allowed_platforms'] : ($userDetails['platform'] ?? '');
$allowedProductsRaw = $userDetails['allowed_products'] ?? '';
$developerPermissionsRaw = $userDetails['developer_permissions'] ?? '';
$platformData = ($allowedPlatformRaw !== '') ? array_values(array_filter(array_map('trim', explode(",", (string)$allowedPlatformRaw)), 'strlen')) : [];
$allowedProductsData = ($allowedProductsRaw !== '') ? array_values(array_filter(array_map('trim', explode(",", (string)$allowedProductsRaw)), 'strlen')) : [];
$developerPermissionsData = ($developerPermissionsRaw !== '') ? array_values(array_filter(array_map('trim', explode(",", (string)$developerPermissionsRaw)), 'strlen')) : [];

$boundUserId = isset($userDetails['user_id']) ? $userDetails['user_id'] : '';
$mycoEmployees = [];
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
        <h4 class="page-title">Edit User</h4>
      </div>
    </div>
    <!-- End Breadcrumb-->
    
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="addUserValidation" action="controller/userController.php" method="post" >
              <input type="hidden" name="admin_id" value="<?php echo $userDetails['admin_id'] ?>">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Name <span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" maxlength="50" required="" name="admin_name" value="<?php echo $userDetails['admin_name'] ?>">
                </div>
                <label class="col-sm-2 col-form-label">Role <span class="required">*</span></label>
                <div class="col-sm-4">
                  <select class="form-control single-select" name="role_id">
                    <option value=""></option>
                    <?php 
                      if($global_role_id==12 || $global_role_id==14) {
                        $query = $d->select("role_master","role_id!=1 AND role_id IN ('12','14')");
                      } else if($global_role_id==1) {
                        $query = $d->select("role_master","");
                      }else{
                        $query = $d->select("role_master","role_id!=1");
                      }
                      while ($roleData = mysqli_fetch_array($query)) { ?>
                        <option <?php if($userDetails['role_id']==$roleData['role_id']){echo "selected";} ?> value="<?php echo $roleData['role_id'] ?>"><?php echo $roleData['role_name'] ?></option>
                    <?php  } ?>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Mobile <span class="required">*</span></label>
                <div class="col-lg-2">
                   <input type="hidden" value="<?php echo $userDetails['country_code']; ?>" id="country_code_get" name="">
                    <select name="country_code" class="form-control single-select" id="country_code" required="">
                      <?php include 'country_code_option_list.php'; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                  <input type="text" maxlength="13" minlength="8" class="form-control" name="admin_mobile"  required="" value="<?php echo $d->encryptDecrypt("decrypt",$userDetails['admin_mobile']) ?>">
                </div>
                <label class="col-sm-2 col-form-label">Email <span class="required">*</span></label>
                <div class="col-sm-4" >
                  <input type="email" maxlength="100" class="form-control" name="admin_email" required="" value="<?php echo $d->encryptDecrypt("decrypt",$userDetails['admin_email']); ?>">
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
                      <option  <?php if(in_array($cData['country_id'], $countrykAry)){ echo 'selected'; } ?>  value="<?php echo $cData['country_id'];?>"><?php echo $cData['name'];?> </option>
                      <?php }?>
                    </select>
                </div>
                <label class="col-sm-2 col-form-label">FCM Notifications</label>
                <div class="col-sm-4" >
                  <select  type="text" class="form-control multiple-select" name="fcm_notifications[]" multiple="multiple">
                    <option value="">-- Select--</option>
                    <option value="1" <?php if(in_array(1, $fcmkAry)){ echo 'selected'; } ?>>App Support</option>
                    <option value="2" <?php if(in_array(2, $fcmkAry)){ echo 'selected'; } ?>>Web Feedback</option>
                    <option value="3" <?php if(in_array(3, $fcmkAry)){ echo 'selected'; } ?>>Company Request</option>
                    <option value="5" <?php if(in_array(5, $fcmkAry)){ echo 'selected'; } ?>>Sales Inquiry</option>
                    <option value="6" <?php if(in_array(6, $fcmkAry)){ echo 'selected'; } ?>>Forward to developer</option>
                    <option value="7" <?php if(in_array(7, $fcmkAry)){ echo 'selected'; } ?>>Close by developer</option>
                    <option value="8" <?php if(in_array(8, $fcmkAry)){ echo 'selected'; } ?>>Server Notification</option>
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
                      <option <?php if($ldata['language_id']==$userDetails['primary_language_id']) { echo 'selected';} ?> value="<?php echo $ldata['language_id'];?>"><?php echo $ldata['language_name'].'-'.$ldata['language_name_1'];?></option>
                    <?php } ?>

                  </select>
                </div>
                <label for="financial_year_start" class="col-sm-2 col-form-label">Timezone <?php echo $xml->string->default_timezone; ?> <span class="required">*</span></label>
                <div class="col-sm-4">
                  <select class="form-control single-select" name="default_time_zone" required="">
                    <option>-- Select --</option>
                    <?php $qt = $d->select("timezoneMaster","");
                    while ($timeData=mysqli_fetch_array($qt)) { ?>
                      <option <?php if($userDetails['default_time_zone']==$timeData['timezone_name']) { echo 'selected'; } ?> value="<?php echo $timeData['timezone_name'];?>"><?php echo $timeData['timezone_name'];?>-<?php echo $timeData['timezone_category'];?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Is Developer <span class="required">*</span></label>
                <div class="col-sm-4" >
                   <select  type="text" required="" id="is_developer" class="form-control single-select" name="is_developer">
                    <option value="">-- Select--</option>
                    <option value="1" <?php if($is_developer == 1){ echo "selected"; } ?>>Yes</option>
                    <option value="0" <?php if($is_developer == 0){ echo "selected"; } ?>>No</option>
                  </select>
                </div>
                <label for="allowed_products" class="col-sm-2 col-form-label" id="allowed_products_label" style="<?php if($is_developer == 1){ echo 'display: block'; }else{ echo 'display: none'; } ?>">Allowed Products</label>
                <div class="col-sm-4 allowed-products-wrap" style="<?php if($is_developer == 1){ echo 'display: block'; }else{ echo 'display: none'; } ?>">
                  <select class="form-control multiple-select" id="allowed_products" name="allowed_products[]" multiple="multiple">
                    <?php
                    $developerProducts = array(
                      '1' => 'MyCo HRMS',
                      '2' => 'MyCo CRM',
                      '3' => 'Smart Society',
                      '4' => 'My Association',
                      '5' => 'Other',
                      '6' => 'MyCo White Label',
                    );
                    foreach ($developerProducts as $productId => $productName) { ?>
                      <option value="<?php echo htmlspecialchars($productId); ?>" <?php if(in_array((string)$productId, $allowedProductsData) || in_array($productName, $allowedProductsData)){echo "selected";} ?>><?php echo htmlspecialchars($productName); ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label for="allowed_platforms" class="col-sm-2 col-form-label" id="allowed_platforms_label" style="<?php if($is_developer == 1){ echo 'display: block'; }else{ echo 'display: none'; } ?>">Allowed Platforms</label>
                <div class="col-sm-4 allowed-platforms-wrap" style="<?php if($is_developer == 1){ echo 'display: block'; }else{ echo 'display: none'; } ?>">
                  <select class="form-control multiple-select" id="allowed_platforms" name="allowed_platforms[]" multiple="multiple">
                    <option value="0" <?php if(in_array('0',$platformData)){echo "selected";}?>>Backend/Api</option>
                    <option value="1" <?php if(in_array('1',$platformData)){echo "selected";}?>>Frontend/web</option>
                    <option value="2" <?php if(in_array('2',$platformData)){echo "selected";}?>>App</option>
                    <option value="4" <?php if(in_array('4',$platformData)){echo "selected";}?>>QA</option>
                  </select>
                </div>
                <label for="developer_permissions" class="col-sm-2 col-form-label" id="developer_permissions_label" style="<?php if($is_developer == 1){ echo 'display: block'; }else{ echo 'display: none'; } ?>">Developer Permissions</label>
                <div class="col-sm-4 developer-permissions-wrap" style="<?php if($is_developer == 1){ echo 'display: block'; }else{ echo 'display: none'; } ?>">
                  <select class="form-control multiple-select" id="developer_permissions" name="developer_permissions[]" multiple="multiple">
                    <option value="0" <?php if(in_array('0',$developerPermissionsData, true)){echo "selected";}?>>Change Patch Status &amp; Date</option>
                    <option value="1" <?php if(in_array('1',$developerPermissionsData, true)){echo "selected";}?>>Change Developer</option>
                    <option value="2" <?php if(in_array('2',$developerPermissionsData, true)){echo "selected";}?>>Change Development Status</option>
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
                      $selected = ((string)$boundUserId !== '' && (string)$boundUserId === (string)$empUserId) ? 'selected' : '';
                    ?>
                      <option value="<?php echo htmlspecialchars($empUserId); ?>"
                        data-user-full-name="<?php echo htmlspecialchars($empName); ?>"
                        data-branch-name="<?php echo htmlspecialchars($empBranch); ?>"
                        data-user-designation="<?php echo htmlspecialchars($empDesignation); ?>"
                        data-department-name="<?php echo htmlspecialchars($empDept); ?>"
                        <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($optionLabel); ?>
                      </option>
                    <?php } ?>
                  </select>
                  <input type="hidden" name="user_full_name" id="bind_user_full_name" value="<?php echo htmlspecialchars($userDetails['user_full_name'] ?? ''); ?>">
                  <input type="hidden" name="branch_name" id="bind_branch_name" value="<?php echo htmlspecialchars($userDetails['branch_name'] ?? ''); ?>">
                  <input type="hidden" name="user_designation" id="bind_user_designation" value="<?php echo htmlspecialchars($userDetails['user_designation'] ?? ''); ?>">
                  <input type="hidden" name="department_name" id="bind_department_name" value="<?php echo htmlspecialchars($userDetails['department_name'] ?? ''); ?>">
                </div>
              </div>
              <div class="form-footer text-center">
                <input type="hidden" name="editUser">
                <button value="add Page" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> Update</button>
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
  function toggleDeveloperFields() {
    var isDeveloper = $('#is_developer').val();
    var show = (isDeveloper == '1');
    $('#allowed_products_label, #allowed_platforms_label, #developer_permissions_label').toggle(show);
    $('.allowed-products-wrap, .allowed-platforms-wrap, .developer-permissions-wrap').toggle(show);
    $('#allowed_products, #allowed_platforms, #developer_permissions').prop('required', false);
    if (!show) {
      $('#allowed_products, #allowed_platforms, #developer_permissions').val(null).trigger('change');
    }
  }

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

  $("#is_developer").change(toggleDeveloperFields);
  toggleDeveloperFields();
  $('#myco_user_bind').on('change', function () {
    syncMycoUserBindFields();
    if ($(this).length && typeof $(this).valid === 'function') {
      $(this).valid();
    }
  });
  syncMycoUserBindFields();
});
</script>
