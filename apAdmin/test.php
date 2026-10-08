<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include_once './lib/dao.php';
include_once './lib/model.php';
// include_once './lib/jwt.php';
// include_once './fcm_file/admin_fcm.php';
// include_once './fcm_file/gaurd_fcm.php';
// include_once './fcm_file/resident_fcm.php';
$d = new dao();
$m = new model();
$bms_admin_master_data_q = $d->selectRow("admin_id, country_code, admin_mobile", "bms_admin_master");

while($bms_admin_master_data = mysqli_fetch_array($bms_admin_master_data_q)){
    $display_admin_mobile = $bms_admin_master_data['country_code'].$d->encryptDecrypt("decrypt",$bms_admin_master_data['admin_mobile']);
    $a = array('display_admin_mobile' => $d->encryptDecrypt("encrypt",$display_admin_mobile));
    $update = $d->update("bms_admin_master", $a, "admin_id = '".$bms_admin_master_data['admin_id']."' ");
}

if($update){
    echo "Display Admin Mobile Updated";
} else {
    echo "Something Wents Wrong";
}