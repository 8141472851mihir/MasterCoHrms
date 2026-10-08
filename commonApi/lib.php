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

header('Access-Control-Allow-Origin: *');  //I have also tried the * wildcard and get the same response
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
$base_url=$m->base_url();
$keydb = $m->api_key();
if(isset($headers) && !empty($headers))
{
	extract(array_map("test_input", $headers));
}
$key = $_SERVER['HTTP_KEY'];  
// Server Key
$cloud_messaging_server_key = $d->get_server_key();
?>