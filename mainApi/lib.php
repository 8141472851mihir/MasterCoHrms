<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
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
// Browsers reject Access-Control-Allow-Origin: * together with Allow-Credentials: true.
// Custom header "key" must be listed or preflight from sites like my-co.app fails (Postman does not enforce CORS).
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Headers: Content-Type, Authorization, Key, key, Content-Range, Content-Disposition, Content-Description');

if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$base_url=$m->base_url();

$enc_key = $d->get_encrypt_key();
$enc_iv = $d->get_encrypt_iv();

?>