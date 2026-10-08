<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include_once '../common/objectController.php';
header('Content-Type: application/json');

if (!isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$action = $_POST['action'];

if ($action === 'handover') {
    $companyId = isset($_POST['company_id']) ? intval($_POST['company_id']) : 0;
    $postImplementationRemark = isset($_POST['post_implementation_remark']) ? trim($_POST['post_implementation_remark']) : '';
    $customerExpectationRemark = isset($_POST['customer_expectation_remark']) ? trim($_POST['customer_expectation_remark']) : '';
    
    if ($companyId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid company ID']);
        exit();
    }
    
    if (empty($postImplementationRemark) || empty($customerExpectationRemark)) {
        echo json_encode(['success' => false, 'message' => 'Both remarks are required']);
        exit();
    }
    
    $handoverBy = isset($bms_admin_id) ? intval($bms_admin_id) : (isset($bms_admin_id) ? intval($bms_admin_id) : 0);
    
    $updateData = [
        'support_handover' => 1,
        'support_handover_date' => date('Y-m-d H:i:s'),
        'post_implementation_remark' => $postImplementationRemark,
        'customer_expectation_remark' => $customerExpectationRemark,
        'support_handover_by' => $handoverBy
    ];
    
    $result = $d->update('society_master', $updateData, "society_id = '$companyId'");
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Company successfully handed over to support team']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update handover data']);
    }
} elseif ($action === 'crm_handover') {
    $companyId = isset($_POST['company_id']) ? intval($_POST['company_id']) : 0;
    $crmPostImplementationRemark = isset($_POST['crm_post_implementation_remark']) ? trim($_POST['crm_post_implementation_remark']) : '';
    $crmCustomerExpectationRemark = isset($_POST['crm_customer_expectation_remark']) ? trim($_POST['crm_customer_expectation_remark']) : '';
    
    if ($companyId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid company ID']);
        exit();
    }
    
    if (empty($crmPostImplementationRemark) || empty($crmCustomerExpectationRemark)) {
        echo json_encode(['success' => false, 'message' => 'Both remarks are required']);
        exit();
    }
    
    $handoverBy = isset($bms_admin_id) ? intval($bms_admin_id) : 0;
    
    $updateData = [
        'crm_support_handover' => 1,
        'crm_support_handover_date' => date('Y-m-d H:i:s'),
        'crm_post_implementation_remark' => $crmPostImplementationRemark,
        'crm_customer_expectation_remark' => $crmCustomerExpectationRemark,
        'crm_handover_by' => $handoverBy
    ];
    
    $result = $d->update('society_master', $updateData, "society_id = '$companyId'");
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Company successfully handed over to support team (CRM)']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update handover data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>

