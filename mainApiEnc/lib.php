<?php
session_start();
date_default_timezone_set('Asia/kolkata');
$activePage = basename($_SERVER['PHP_SELF'], ".php");
include_once '../apAdmin/lib/dao.php';
include_once '../apAdmin/lib/model.php';
include_once '../apAdmin/fcm_file/admin_fcm.php';
include_once '../apAdmin/fcm_file/gaurd_fcm.php';
include_once '../apAdmin/fcm_file/resident_fcm.php';
$d = new dao();
$m = new model();
$nAdmin = new firebase_admin();
$nResident = new firebase_resident();
$nGaurd = new firebase_gaurd();
$con=$d->dbCon();
// header('Access-Control-Allow-Origin: *');  //I have also tried the * wildcard and get the same response
// header("Access-Control-Allow-Credentials: true");
// header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
// header('Access-Control-Max-Age: 1000');
// header('content-type: application/json; charset=utf-8');
// header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('content-type: application/json; charset=utf-8');
header("Access-Control-Allow-Headers: Content-Type, Authorization, Key, compress");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_METHOD'] != 'OPTIONS') {
    http_response_code(405);
    echo json_encode([
        "status" => 405,
        "message" => "Invalid request method."
    ]);
    exit;
}
$base_url=$m->base_url();
$keydb = $m->api_key();
$enc_key = $d->get_encrypt_key();
$enc_iv = $d->get_encrypt_iv();

$is_encrypted=1; 

$key = isset($_SERVER['HTTP_KEY']) ? $_SERVER['HTTP_KEY'] : "";
$language_id = isset($_SERVER['HTTP_LANGUAGEID']) ? $_SERVER['HTTP_LANGUAGEID'] : "";
$HTTP_COMPRESS = isset($_SERVER['HTTP_COMPRESS']) ? $_SERVER['HTTP_COMPRESS'] : "";
$compress = $HTTP_COMPRESS;

/**
 * Prefer header compress; if blank, use body compress (after decrypt when encrypted).
 */
function resolve_api_compress()
{
	global $HTTP_COMPRESS;
	if (isset($HTTP_COMPRESS) && $HTTP_COMPRESS != '') {
		$val = $HTTP_COMPRESS;
	} elseif (isset($_POST['compress']) && $_POST['compress'] != '') {
		$val = $_POST['compress'];
	} else {
		$val = '0';
	}
	unset($_POST['compress']);
	return $val;
}

if($language_id=="") {
	$language_id = 1;
}
if ($language_id != "") {
	$xml = $d->loadLanguageXmlFromS3($language_id,"xml");
	if ($xml === false) {
		$d->createLanguageFiles($language_id, $base_url, '', 'both');
		$xml = $d->loadLanguageXmlFromS3($language_id, "xml");
	}
}

// Get bucket URL for other file references
$bucket_url = $d->getBucketUrl($base_url);