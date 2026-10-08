<?php
include '../common/objectController.php';

if (isset($_GET['company_id']) && !empty($_GET['company_id'])) {
    $company_id = intval($_GET['company_id']);
    
    $q = $d->selectRow(
        "plan_expire_date",
        "society_master",
        "society_id = '$company_id'"
    );
    
    $data = mysqli_fetch_assoc($q);
    
    if ($data && !empty($data['plan_expire_date']) && $data['plan_expire_date'] != '0000-00-00') {
        $expiryDate = $data['plan_expire_date'];
        $formattedDate = date('d-M-Y', strtotime($expiryDate));
        
        echo json_encode([
            'success' => true,
            'plan_expiry_date' => $expiryDate,
            'formatted_date' => $formattedDate
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Plan expiry date not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Company ID is required'
    ]);
}
exit();

