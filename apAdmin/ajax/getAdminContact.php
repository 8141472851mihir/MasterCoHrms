<?php
include '../common/objectController.php';

if(isset($_POST['admin_name']) && $_POST['admin_name'] != '') {
    $admin_name = $d->escapeSqlString($_POST['admin_name']);
    
    $supportPersonQry = $d->selectRow("admin_id,admin_mobile,country_code", "bms_admin_master", "admin_name='$admin_name'");
    
    if(mysqli_num_rows($supportPersonQry) > 0) {
        $supportPersonData = mysqli_fetch_assoc($supportPersonQry);
        $admin_mobile = $supportPersonData['admin_mobile'];
        $admin_mobile = $d->encryptDecrypt("decrypt", $admin_mobile);
        $country_code = $supportPersonData['country_code'];
        
        $response = array(
            'success' => true,
            'mobile' => $admin_mobile,
            'country_code' => $country_code
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Admin not found'
        );
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    $response = array(
        'success' => false,
        'message' => 'Admin name is required'
    );
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>


