<?php
include_once('../common/objectController.php');
header('Content-Type: application/json');

if (!isset($_POST['form_id']) || empty($_POST['form_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Form ID is required']);
    exit;
}

$form_id = (int)$_POST['form_id'];

$result = $d->selectRow(
    "tcfm.*, sm.society_name",
    "training_completion_form_master tcfm 
     LEFT JOIN society_master sm ON tcfm.society_id = sm.society_id",
    "tcfm.form_id = '$form_id'"
);

if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Form not found']);
    exit;
}

$row = mysqli_fetch_assoc($result);

// Parse JSON data
$participants_data = [];
if (!empty($row['participants_data'])) {
    $participants_data = json_decode($row['participants_data'], true);
    if (!is_array($participants_data)) {
        $participants_data = [];
    }
}

$modules_data = [];
if (!empty($row['modules_data'])) {
    $modules_data = json_decode($row['modules_data'], true);
    if (!is_array($modules_data)) {
        $modules_data = [];
    }
}

// Trainer feedback: from 4 columns (or legacy JSON if present)
$trainer_feedback = [];
if (isset($row['trainer_feedback_product_knowledge']) || isset($row['trainer_feedback_communication']) || isset($row['trainer_feedback_attire_behavior']) || isset($row['trainer_feedback_training_capabilities'])) {
    $trainer_feedback = [
        'product_knowledge' => isset($row['trainer_feedback_product_knowledge']) ? (int)$row['trainer_feedback_product_knowledge'] : null,
        'communication' => isset($row['trainer_feedback_communication']) ? (int)$row['trainer_feedback_communication'] : null,
        'attire_behavior' => isset($row['trainer_feedback_attire_behavior']) ? (int)$row['trainer_feedback_attire_behavior'] : null,
        'training_capabilities' => isset($row['trainer_feedback_training_capabilities']) ? (int)$row['trainer_feedback_training_capabilities'] : null,
        'feedback_remark' => isset($row['feedback_remark']) ? $row['feedback_remark'] : ''
    ];
} elseif (!empty($row['trainer_feedback'])) {
    $trainer_feedback = json_decode($row['trainer_feedback'], true);
    if (!is_array($trainer_feedback)) {
        $trainer_feedback = [];
    }
}

// Format dates
$submitted_date_formatted = !empty($row['submitted_date']) ? date('d M Y, h:i A', strtotime($row['submitted_date'])) : '';
$created_date_formatted = !empty($row['created_date']) ? date('d M Y, h:i A', strtotime($row['created_date'])) : '';

$response = [
    'status' => 'success',
    'data' => [
        'form_id' => $row['form_id'],
        'society_id' => $row['society_id'],
        'company_name' => $row['company_name'] ?: $row['society_name'],
        'employee_name' => $row['employee_name'],
        'employee_designation' => $row['employee_designation'],
        'client_name' => $row['client_name'],
        'client_designation' => $row['client_designation'],
        'client_country_code' => $row['client_country_code'],
        'client_mobile' => $row['client_mobile'],
        'client_email' => $row['client_email'],
        'participants_data' => $participants_data,
        'modules_data' => $modules_data,
        'completed_modules_count' => (int)$row['completed_modules_count'],
        'na_modules_count' => (int)$row['na_modules_count'],
        'pending_modules_count' => (int)$row['pending_modules_count'],
        'total_modules_count' => (int)$row['total_modules_count'],
        'trainer_feedback' => $trainer_feedback,
        'declaration_agreed' => (int)$row['declaration_agreed'],
        'otp_verified' => (int)$row['otp_verified'],
        'submitted_date' => $row['submitted_date'],
        'submitted_date_formatted' => $submitted_date_formatted,
        'created_date' => $row['created_date'],
        'created_date_formatted' => $created_date_formatted
    ]
];

echo json_encode($response);
?>
