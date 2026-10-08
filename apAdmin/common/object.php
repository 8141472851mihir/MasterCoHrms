<?php 
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
$url=$_SESSION['url'] = $_SERVER['REQUEST_URI']; 
$activePage = basename($_SERVER['PHP_SELF'], ".php");
include_once 'lib/dao.php';
include_once 'lib/model.php';
include_once 'lib/jwt.php';
$d = new dao();
$m = new model();
$con=$d->dbCon();
$base_url=$m->base_url();
$keydb = $m->api_key();

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
    		$admin_mobile=$d->encryptDecrypt("decrypt",$bms_admin_data['admin_mobile']); //society master data //not used
    		$admin_email=$d->encryptDecrypt("decrypt",$bms_admin_data['admin_email']); //society master data
    		$admin_profile=$bms_admin_data['admin_profile'];
    		$socieaty_logo=$bms_admin_data['socieaty_logo']; //society master data
    		$society_id=$bms_admin_data['society_id'];
    		$admin_type=$bms_admin_data['admin_type'];
    		$plan_expire_date=$bms_admin_data['plan_expire_date'];
    		$complaint_category_id=$bms_admin_data['complaint_category_id'];
    		$role_id=$bms_admin_data['role_id'];
    		$global_role_id=$bms_admin_data['role_id'];
    	}
    }
  } catch (Exception $e) {
    
  }
}
$bucket_configuration_qry= $d->selectRow("bucket_configuration.master_bucket_url","bucket_configuration","1","LIMIT 1");
if(mysqli_num_rows($bucket_configuration_qry)>0){
	$bucket_configuration_data=mysqli_fetch_array($bucket_configuration_qry);
	$bucket_url=$bucket_configuration_data['master_bucket_url'];
}else{
	$bucket_url=$base_url . "img/";
}

// Public pages that may load object.php without a session
$__masterPublicPages = ['index.php', 'forgot.php', 'resetPassword.php', 'accountLock.php'];
if (empty($MASTER_AUTH_SKIP) && !in_array(basename($_SERVER['PHP_SELF'] ?? ''), $__masterPublicPages, true)) {
	if (!isset($bms_admin_id) || $bms_admin_id === '' || $bms_admin_id === null) {
		$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
		$baseName = basename($scriptName);
		$forceJson = !empty($MASTER_AUTH_JSON)
			|| stripos($scriptName, '/ajax/') !== false
			|| stripos($scriptName, '/controller/') !== false
			|| (bool) preg_match('/Ajax\.php$/i', $baseName)
			|| (bool) preg_match('/^(get_|ajax)/i', $baseName)
			|| (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
			|| (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

		if ($forceJson) {
			http_response_code(401);
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode([
				'success' => false,
				'status' => '401',
				'message' => 'Login First',
			]);
			exit;
		}

		$_SESSION['msg1'] = 'Login First.';
		header('Location: ' . rtrim($base_url, '/') . '/apAdmin/index.php?LoginFirst');
		exit;
	}
}
 ?>
