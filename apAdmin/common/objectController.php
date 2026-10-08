<?php 
session_start();

date_default_timezone_set('Asia/Kolkata');
$url=$_SESSION['url'] ; 
$activePage = basename($_SERVER['PHP_SELF'], ".php");
include_once '../lib/dao.php';
include_once '../lib/model.php';
include_once '../lib/jwt.php';
include_once '../fcm_file/admin_fcm.php';
include_once '../fcm_file/gaurd_fcm.php';
include_once '../fcm_file/resident_fcm.php';
$d = new dao();
$m = new model();
$nAdmin = new firebase_admin();
$nResident = new firebase_resident();
$nGaurd = new firebase_gaurd();
$con=$d->dbCon();
if(isset($_COOKIE['master_token'])){ 
  $token=$_COOKIE['master_token'] ?? null;
  $key = $d->get_encrypt_key(); 
  try {
    $decoded = JWT::decode($token, $key, ['HS256']);
    if (time() < $decoded->exp) {
    	$bms_admin_id=$decoded->adminId;
        $bms_admin_id=$d->encryptDecrypt("decrypt","$bms_admin_id");
    	$bms_admin_qry=$d->selectRow("bms_admin_master.*,role_master.*,society_master.*","bms_admin_master LEFT JOIN role_master ON bms_admin_master.role_id=role_master.role_id LEFT JOIN society_master ON society_master.society_id=bms_admin_master.society_id","admin_id='$bms_admin_id' AND active_status='0'");
    	if (mysqli_num_rows($bms_admin_qry)>0) {
    		$bms_admin_data=mysqli_fetch_array($bms_admin_qry);
    		$default_time_zone=$bms_admin_data['default_time_zone'];
            $admin_name=$bms_admin_data['admin_name'];
    		$society_name=$bms_admin_data['society_name']; //society master data
    		$admin_mobile=$d->encryptDecrypt("decrypt",$bms_admin_data['admin_mobile']); //society master data
    		$admin_email=$d->encryptDecrypt("decrypt",$bms_admin_data['admin_email']); //society master data
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

// Enforce master login for all controllers / ajax that include this file
if (!isset($bms_admin_id) || $bms_admin_id === '' || $bms_admin_id === null) {
	http_response_code(401);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode([
		'success' => false,
		'status' => '401',
		'message' => 'Login First',
	]);
	exit;
}

$created_by=$admin_name;
$updated_by=$admin_name;
$society_id=$society_id;
extract($_POST);

$language_id = $_COOKIE['language_id'];
$xml = $d->loadLanguageXmlFromS3($language_id, "xml");
if ($xml === false && $language_id != "") {
	$d->createLanguageFiles($language_id, $base_url, '', 'both');
	$xml = $d->loadLanguageXmlFromS3($language_id, "xml");
}
$societyLngName = ($xml !== false && isset($xml->string->society)) ? $xml->string->society : '';

$base_url=$m->base_url();
$keydb = $m->api_key();

/* if(isset($_REQUEST) && !empty($_REQUEST) && $_REQUEST["csrf"] != $_SESSION["token"] || $_REQUEST["csrf"]==''){
	$_SESSION['msg1']="Token Mismatch";
	header('location:'.$url);
	exit();
} */
?>