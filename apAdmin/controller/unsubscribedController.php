<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include '../common/objectController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $waba_phone_number = $_POST['waba_phone_number'] ?? '';
    
    if (empty($mobile)) {
        echo json_encode(['status' => 'error', 'message' => 'Mobile number is required']);
        exit;
    }
    
    switch ($action) {
        case 'resubscribe':
            // Remove user from unsubscribe table
            $deleteResult = $d->delete("whatsapp_unsubscribe_master", "user_mobile = '$mobile' AND waba_phone_number = '$waba_phone_number'");
            
            if ($deleteResult) {
                // Log the action
                $logData = [
                    'user_id' => $bms_admin_id,
                    'user_name' => $admin_name,
                    'log_name' => "Resubscribed user: $mobile From Waba Phone Number : $waba_phone_number",
                    'log_time' => date('Y-m-d H:i:s')
                ];
                $d->insert("log_master", $logData);
                
                echo json_encode(['status' => 'success', 'message' => 'User resubscribed successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to resubscribe user']);
            }
            break;
            
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
