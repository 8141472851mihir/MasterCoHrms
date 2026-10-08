<?php
date_default_timezone_set('Asia/Kolkata');
$activePage = basename($_SERVER['PHP_SELF'], ".php");
include_once '../apAdmin/lib/dao.php';
include_once '../apAdmin/lib/model.php';
include_once '../apAdmin/lib/sms_api.php';
include_once '../apAdmin/fcm_file/admin_fcm.php';
include_once '../apAdmin/fcm_file/gaurd_fcm.php';
include_once '../apAdmin/fcm_file/resident_fcm.php';

$d = new dao();
$m = new model();
$sms_api = new sms_api();

$nAdmin = new firebase_admin();
$nResident = new firebase_resident();
$nGaurd = new firebase_gaurd();
$con=$d->dbCon();
$keydb = $m->api_key();
