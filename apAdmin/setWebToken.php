<?php 

include_once 'common/object.php';
session_start();
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
    		$society_name=$bms_admin_data['society_name']; //society master data
    		$admin_mobile=$d->encryptDecrypt("decrypt",$bms_admin_data['admin_mobile']);
    		$admin_email=$d->encryptDecrypt("decrypt",$bms_admin_data['admin_email']); 
    		$admin_profile=$bms_admin_data['admin_profile'];
    		$socieaty_logo=$bms_admin_data['socieaty_logo']; //society master data
    		$society_id=$bms_admin_data['society_id'];
    		$admin_type=$bms_admin_data['admin_type'];
    		$plan_expire_date=$bms_admin_data['plan_expire_date'];
    		$complaint_category_id=$bms_admin_data['complaint_category_id'];
    		$role_id=$bms_admin_data['role_id'];
    	}
    }
  } catch (Exception $e) {
    
  }
}
extract(array_map("test_input" , $_POST));
date_default_timezone_set('Asia/Kolkata');
$currentTokenSafe = $d->escapeSqlString($_POST['currentToken'] ?? ($currentToken ?? ''));
$checkData = $d->selectArray("web_fcm_master","fcm_token='$currentTokenSafe'");
if (empty($checkData)) {
	$m->set_data('admin_id',$bms_admin_id);
	$m->set_data('fcm_token',$currentToken);

	$a = array(
	  'admin_id'=>$m->get_data('admin_id'),
	  'fcm_token'=>$m->get_data('fcm_token'),
	);
	$d->insert("web_fcm_master",$a);
}
?>