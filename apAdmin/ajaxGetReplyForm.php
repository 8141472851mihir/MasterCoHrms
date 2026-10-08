<?php 

include_once 'common/object.php';
error_reporting(0);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$base_url = $m->base_url();
if(isset($_COOKIE['master_token'])){ 
  $token=$_COOKIE['master_token'] ?? null;
  $key = $d->get_encrypt_key(); 
  try {
    $decoded = JWT::decode($token, $key, ['HS256']);
    if (time() < $decoded->exp) {
      $bms_admin_id=$decoded->adminId;
        $bms_admin_id=$d->encryptDecrypt("decrypt","$bms_admin_id");
      $bms_admin_qry=$d->selectRow("bms_admin_master.*,role_master.*,society_master.*","bms_admin_master LEFT JOIN role_master ON bms_admin_master.role_id=role_master.role_id LEFT JOIN society_master ON society_master.society_id=bms_admin_master.society_id","admin_id='$bms_admin_id' and active_status='0'");
      if (mysqli_num_rows($bms_admin_qry)>0) {
        $bms_admin_data=mysqli_fetch_array($bms_admin_qry);
        $default_time_zone=$bms_admin_data['default_time_zone'];
        $admin_name=$bms_admin_data['admin_name'];
        $society_name=$bms_admin_data['society_name'];
        $secretary_mobile=$bms_admin_data['secretary_mobile'];
        $secretary_email=$bms_admin_data['secretary_email'];
        $admin_profile=$bms_admin_data['admin_profile'];
        $socieaty_logo=$bms_admin_data['socieaty_logo'];
        $society_id=$bms_admin_data['society_id'];
        $admin_type=$bms_admin_data['admin_type'];
        $plan_expire_date=$bms_admin_data['plan_expire_date'];
        $complaint_category_id=$bms_admin_data['complaint_category_id'];
        $role_id=$bms_admin_data['role_id'];
        $is_developer=$bms_admin_data['is_developer'];
      }
    }
  } catch (Exception $e) {
    
  }
}
extract(array_map("test_input" , $_POST));
$data = $d->selectArray("feedback_master LEFT JOIN feedback_log_master flm  ON feedback_master.feedback_id = flm.feedback_id","feedback_master.feedback_id='$feedback_id'","ORDER BY flm.feedback_log_date DESC LIMIT 1");
?>
<form id="replyFeedbackFrm" action="controller/feedbackController.php" method="post" enctype="multipart/form-data">
  <input type="hidden" id="society_id" name="society_id" value="<?php echo $data['society_id'];?>">
  <input type="hidden" id="feedback_id" name="feedback_id" value="<?php echo $feedback_id;?>">
  <input type="hidden" id="feedback_added_by" name="feedback_added_by" value="<?=$data['created_by']?>">
  <input type="hidden" id="feedback_email" name="feedback_email" value="<?php echo $data['email'];?>">
  <input type="hidden" id="csrf" name="csrf" value="<?php echo $csrf;?>">
  <?php if($data['feedback_status'] < 5 || $data['feedback_status'] > 6){ ?>
    <div class="form-group row">
      <label for="input-10" class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
      <div class="col-sm-10">
        <select name="feedback_status" class="form-control" required="">
          <option value="">--SELECT--</option>
          <?php if($data['feedback_status']==0){ ?>
            <!-- <option value="0">Open</option> -->
            <option value="1">In Progress</option>
            <option value="3">On Hold</option>
            <option value="7">Need More Specification</option>
            <option value="8">Resolve in Next Update</option>
          <?php } else if($data['feedback_status']==1){ ?>
            <option value="1">In Progress</option>
            <option value="3">On Hold</option>
            <option value="7">Need More Specification</option>
            <option value="8">Resolve in Next Update</option>
          <?php } else if($data['feedback_status']==3){ ?>
            <option value="0">Open</option>
          <?php } else if($data['feedback_status']==7){ ?>
            <option value="1">In Progress</option>
            <option value="3">On Hold</option>
            <?php
            if($is_developer=="1" || $role_id=='1'){
            ?>
            <option value="7">Need More Specification</option>
            <?php
            } 
            ?>
            <option value="8">Resolve in Next Update</option>
            <!-- <option value="4">Rejected</option> -->
          <?php }else if($data['feedback_status']==8){ ?>
            <option value="1">In Progress</option>
            <option value="3">On Hold</option>
            <option value="7">Need More Specification</option>
            <option value="8">Resolve in Next Update</option>
            <!-- <option value="4">Rejected</option> -->
          <?php } ?>
          <?php if($data['feedback_status']==4){ ?>
            <option value="0">Open</option>
          <?php } ?>
        </select>
      </div>
    </div>
  <?php } ?>
  <div class="form-group row">
    <label for="input-10"  class="col-sm-2 col-form-label">Reply </label>
    <div class="col-sm-10">
      <textarea class="form-control" rows="6" cols="30" maxlength="2500" style="resize: vertical;" name="reply" id="reply"></textarea>
    </div>
  </div>
  <div class="form-group row">
    <label for="input-10" class="col-sm-2 col-form-label">Attachment File</label>
    <div class="col-sm-10">
      <input class="form-control-file border" type="file" name="attachment">
    </div>
  </div>
  <div class="form-group row">
      <label for="hideToUser" class="mx-1 col-form-label"> Hide for User</label> 
      <input class="border" type="checkbox" name="hideToUser" id="hideToUser">
  </div>
  <div class="form-footer text-center">
    <input type="hidden" name="previousURL" value="<?php echo htmlspecialchars($previousURL ?? '', ENT_QUOTES); ?>">
    <?php echo $d->feedbackListHiddenInputs(); ?>
    <input type="hidden" name="replyFeedback" value="replyFeedback">
    <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Reply</button>
  </div>
</form>
