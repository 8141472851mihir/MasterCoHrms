<?php

include_once 'common/object.php';
// error_reporting(0);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


$response = ['status' => 'ok', 'message' => '', 'conflictDates' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batch_id = $_POST['batch_id'] ?? '';
    $selectedDates = $_POST['selectedDates'] ?? '';

    if (!empty($batch_id) && !empty($selectedDates)) {
        $response['status'] = 'ok';
        $response['message'] = "Slot available.";
    } else {
        $response['status'] = 'error';
        $response['message'] = "Invalid batch or date.";
    }
} else {
    $response['status'] = 'error';
    $response['message'] = "Invalid request.";
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
