<?php
session_start();
// Include necessary files
include_once 'apAdmin/lib/dao.php';
include_once 'apAdmin/lib/model.php';

// Initialize DAO and Model
$d = new dao();
$m = new model();

// Initialize variables
$employee_name = ''; // CHL Representative / Implementation Person
$employee_designation = 'Implementaion Executive';
$company_name = '';
$client_name = '';
$client_designation = '';
$client_mobile = '';
$client_email = '';
$society_id = 0;

// Handle OTP actions
if (isset($_POST['action'])) {
    header('Content-Type: application/json');

    if ($_POST['action'] == 'send_otp') {
        // Get encrypted society_id from POST or GET
        $encrypted_society_id = isset($_POST['c']) ? $_POST['c'] : (isset($_GET['c']) ? $_GET['c'] : '');
        if (empty($encrypted_society_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            exit;
        }

        try {
            $soc_id = $d->encryptDecrypt("decrypt", $encrypted_society_id);
            $soc_id = (int)$soc_id;

            if ($soc_id > 0) {
                // Get client mobile and email from form POST data
                $client_country_code = isset($_POST['client_country_code']) ? trim($_POST['client_country_code']) : '';
                $client_mobile = isset($_POST['client_mobile']) ? trim($_POST['client_mobile']) : '';
                $client_email = isset($_POST['client_email']) ? trim($_POST['client_email']) : '';

                // Combine country code and mobile number
                $full_mobile = '';
                if (!empty($client_country_code) && !empty($client_mobile)) {
                    $full_mobile = $client_country_code . $client_mobile;
                } elseif (!empty($client_mobile)) {
                    $full_mobile = $client_mobile;
                }

                if (empty($full_mobile) && empty($client_email)) {
                    echo json_encode(['status' => 'error', 'message' => 'Please provide mobile number or email address']);
                    exit;
                }

                // Generate 6-digit OTP
                $digits = 6;
                $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);

                // Store OTP in session
                $_SESSION['training_form_otp_' . $soc_id] = $otp;
                $_SESSION['training_form_otp_time_' . $soc_id] = time();

                $sent_to = [];

                // Send OTP via email if email is provided
                if (!empty($client_email)) {
                    $subject = "Training & Implementation Completion Form - OTP Verification";
                    $message = "Your OTP for Training & Implementation Completion Form verification is: <strong>$otp</strong><br><br>This OTP is valid for 10 minutes.";

                    $to = $client_email;
                    $cc = [];
                    $bcc = [];
                    $attachments = null;
                    include 'apAdmin/mail.php';
                    $sent_to[] = $client_email;
                }

                // Send OTP via SMS if mobile is provided
                if (!empty($full_mobile) && method_exists($d, 'send_otp')) {
                    $d->send_otp($full_mobile, $otp);
                    $sent_to[] = $full_mobile;
                }

                if (!empty($sent_to)) {
                    $sent_message = 'OTP sent successfully to ' . implode(' and ', $sent_to);
                    echo json_encode(['status' => 'success', 'message' => $sent_message]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP. Please check your mobile number and email address.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid society ID']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($_POST['action'] == 'verify_otp') {
        $encrypted_society_id = isset($_POST['c']) ? $_POST['c'] : (isset($_GET['c']) ? $_GET['c'] : '');
        $entered_otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';

        if (empty($encrypted_society_id) || empty($entered_otp)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            exit;
        }

        try {
            $soc_id = $d->encryptDecrypt("decrypt", $encrypted_society_id);
            $soc_id = (int)$soc_id;

            $stored_otp = isset($_SESSION['training_form_otp_' . $soc_id]) ? $_SESSION['training_form_otp_' . $soc_id] : '';
            $otp_time = isset($_SESSION['training_form_otp_time_' . $soc_id]) ? $_SESSION['training_form_otp_time_' . $soc_id] : 0;

            // Check if OTP expired (10 minutes)
            if (time() - $otp_time > 600) {
                unset($_SESSION['training_form_otp_' . $soc_id]);
                unset($_SESSION['training_form_otp_time_' . $soc_id]);
                echo json_encode(['status' => 'error', 'message' => 'OTP has expired. Please request a new one.']);
                exit;
            }

            if ($stored_otp == $entered_otp) {
                // OTP verified - mark as verified
                $_SESSION['training_form_verified_' . $soc_id] = true;
                $_SESSION['training_form_verified_time_' . $soc_id] = time();

                // Clear OTP from session
                unset($_SESSION['training_form_otp_' . $soc_id]);
                unset($_SESSION['training_form_otp_time_' . $soc_id]);

                echo json_encode(['status' => 'success', 'message' => 'OTP verified successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid OTP. Please try again.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    // Handle form submission after OTP verification
    if ($_POST['action'] == 'submit_form') {
        $encrypted_society_id = isset($_POST['c']) ? $_POST['c'] : '';
        if (empty($encrypted_society_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            exit;
        }

        try {
            $soc_id = $d->encryptDecrypt("decrypt", $encrypted_society_id);
            $soc_id = (int)$soc_id;

            // Check if already submitted
            $existingForm = $d->selectRow(
                "form_id",
                "training_completion_form_master",
                "society_id = '$soc_id'"
            );
            if ($existingForm && mysqli_num_rows($existingForm) > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Form already submitted for this company.']);
                exit;
            }

            // Check OTP verification
            if (!isset($_SESSION['training_form_verified_' . $soc_id]) || $_SESSION['training_form_verified_' . $soc_id] !== true) {
                echo json_encode(['status' => 'error', 'message' => 'OTP verification required']);
                exit;
            }

            // Get form data
            $company_name = isset($_POST['company_name']) ? trim($_POST['company_name']) : '';
            $employee_name = isset($_POST['employee_name']) ? trim($_POST['employee_name']) : '';
            $employee_designation = isset($_POST['employee_designation']) ? trim($_POST['employee_designation']) : '';
            $client_name = isset($_POST['client_name']) ? trim($_POST['client_name']) : '';
            $client_designation = isset($_POST['client_designation']) ? trim($_POST['client_designation']) : '';
            $client_country_code = isset($_POST['client_country_code']) ? trim($_POST['client_country_code']) : '';
            $client_mobile = isset($_POST['client_mobile']) ? trim($_POST['client_mobile']) : '';
            $client_email = isset($_POST['client_email']) ? trim($_POST['client_email']) : '';
            $declaration_agreed = isset($_POST['agree']) && $_POST['agree'] == 'on' ? 1 : 0;

            // Trainers Feedback (1-5 stars per criterion, stored in 4 columns)
            $feedback_product_knowledge = isset($_POST['trainer_feedback_product_knowledge']) ? max(1, min(5, (int)$_POST['trainer_feedback_product_knowledge'])) : null;
            $feedback_communication = isset($_POST['trainer_feedback_communication']) ? max(1, min(5, (int)$_POST['trainer_feedback_communication'])) : null;
            $feedback_attire_behavior = isset($_POST['trainer_feedback_attire_behavior']) ? max(1, min(5, (int)$_POST['trainer_feedback_attire_behavior'])) : null;
            $feedback_training_capabilities = isset($_POST['trainer_feedback_training_capabilities']) ? max(1, min(5, (int)$_POST['trainer_feedback_training_capabilities'])) : null;
            $feedback_remark  =  isset($_POST['feedback_remark']) ? trim($_POST['feedback_remark']) : '';
            // Get participants data in proper JSON format
            $participants_data = [];
            $processed_participant_ids = []; // Track processed IDs to avoid duplicates
            foreach ($_POST as $key => $value) {
                // Only process keys that start with 'participant_status_' and don't end with '_id' or '_name'
                // Also skip empty values and validate the value
                if (
                    strpos($key, 'participant_status_') === 0 &&
                    !preg_match('/_(id|name)$/', $key) &&
                    !empty(trim($value)) &&
                    in_array(strtolower(trim($value)), ['yes', 'no', 'na'])
                ) {
                    $participantId = (int)str_replace('participant_status_', '', $key);

                    // Skip if we've already processed this participant ID
                    if (in_array($participantId, $processed_participant_ids)) {
                        continue;
                    }

                    // Fetch participant name from database
                    $participantQuery = $d->selectRow("participants_type_id, participant_name", "training_participants_type", "participants_type_id = '$participantId'");
                    if ($participantQuery && mysqli_num_rows($participantQuery) > 0) {
                        $participantRow = mysqli_fetch_assoc($participantQuery);
                        $participantValue = ucfirst(strtolower(trim($value)));
                        // Only add if we have a valid value (Yes, No, or Na)
                        if (in_array(strtolower($participantValue), ['yes', 'no', 'na'])) {
                            $participants_data[] = [
                                'participant_id' => (int)$participantRow['participants_type_id'],
                                'participant_name' => $participantRow['participant_name'],
                                'participant_value' => $participantValue
                            ];
                            $processed_participant_ids[] = $participantId;
                        }
                    }
                }
            }

            // Get modules data in proper JSON format
            $modules_data = [];
            $completed_count = 0;
            $na_count = 0;
            $pending_count = 0;
            $total_count = 0;
            $processed_module_ids = []; // Track processed IDs to avoid duplicates

            foreach ($_POST as $key => $value) {
                // Only process keys that start with 'module_status_' and don't end with '_id' or '_name'
                // Also skip empty values
                if (
                    strpos($key, 'module_status_') === 0 &&
                    !preg_match('/_(id|name)$/', $key) &&
                    !empty(trim($value)) &&
                    in_array(strtolower(trim($value)), ['completed', 'na', 'pending'])
                ) {
                    $moduleId = (int)str_replace('module_status_', '', $key);

                    // Skip if we've already processed this module ID
                    if (in_array($moduleId, $processed_module_ids)) {
                        continue;
                    }

                    // Fetch module name from database
                    $moduleQuery = $d->selectRow("training_module_id, training_module_name", "training_module_master", "training_module_id = '$moduleId'");
                    if ($moduleQuery && mysqli_num_rows($moduleQuery) > 0) {
                        $moduleRow = mysqli_fetch_assoc($moduleQuery);
                        $statusValue = '';
                        if ($value == 'completed') {
                            $statusValue = 'Completed';
                            $completed_count++;
                        } elseif ($value == 'na') {
                            $statusValue = 'Not Applicable';
                            $na_count++;
                        } elseif ($value == 'pending') {
                            $statusValue = 'Pending';
                            $pending_count++;
                        }

                        // Only add if we have a valid status value
                        if (!empty($statusValue)) {
                            $modules_data[] = [
                                'module_id' => (int)$moduleRow['training_module_id'],
                                'module_name' => $moduleRow['training_module_name'],
                                'module_value' => $statusValue
                            ];
                            $total_count++;
                            $processed_module_ids[] = $moduleId;
                        }
                    }
                }
            }

            // Insert into database
            // Get current date/time in IST
            $ist_timezone = new DateTimeZone('Asia/Kolkata');
            $current_time = new DateTime('now', $ist_timezone);
            $current_datetime = $current_time->format('Y-m-d H:i:s');

            $insert_data = array(
                'society_id' => $soc_id,
                'company_name' => $company_name,
                'employee_name' => $employee_name,
                'employee_designation' => $employee_designation,
                'client_name' => $client_name,
                'client_designation' => $client_designation,
                'client_country_code' => $client_country_code,
                'client_mobile' => $client_mobile,
                'client_email' => $client_email,
                'participants_data' => json_encode($participants_data),
                'modules_data' => json_encode($modules_data),
                'completed_modules_count' => $completed_count,
                'na_modules_count' => $na_count,
                'pending_modules_count' => $pending_count,
                'total_modules_count' => $total_count,
                'trainer_feedback_product_knowledge' => $feedback_product_knowledge,
                'trainer_feedback_communication' => $feedback_communication,
                'trainer_feedback_attire_behavior' => $feedback_attire_behavior,
                'trainer_feedback_training_capabilities' => $feedback_training_capabilities,
                'feedback_remark' => $feedback_remark,
                'declaration_agreed' => $declaration_agreed,
                'otp_verified' => 1,
                'submitted_date' => $current_datetime,
                'created_date' => $current_datetime
            );

            $d->insert("training_completion_form_master", $insert_data);

            // Set session variable to show success message
            $_SESSION['training_form_submitted_success'] = true;

            echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully!']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
}
// Get encrypted society_id from URL parameter
$valid_company_id = false;
$company_id_error = false;

if (isset($_GET['c']) && !empty($_GET['c'])) {
    $encrypted_society_id = $_GET['c'];
    try {
        // Decrypt the society_id
        $society_id = $d->encryptDecrypt("decrypt", $encrypted_society_id);
        $society_id = (int)$society_id;
        if ($society_id > 0) {
            $valid_company_id = true;
            // Fetch society details - get all static client details from society_master
            $societyQuery = $d->selectRow(
                "society_master.implementation_name, 
                 society_master.society_name,
                 society_master.company_full_name,
                 states.name as state_name, 
                 cities.name as city_name",
                "society_master 
                 LEFT JOIN states ON states.state_id = society_master.state_id
                 LEFT JOIN cities ON cities.city_id = society_master.city_id",
                "society_master.society_id = '$society_id'"
            );
            if (mysqli_num_rows($societyQuery) > 0) {
                $societyData = mysqli_fetch_array($societyQuery);

                // Populate CHL Representative / Implementation Person details
                $employee_name = isset($societyData['implementation_name']) && !empty($societyData['implementation_name'])
                    ? htmlspecialchars($societyData['implementation_name'])
                    : '';

                // Populate company name from society_master
                $company_name = '';
                if (isset($societyData['company_full_name']) && !empty($societyData['company_full_name'])) {
                    $company_name = htmlspecialchars($societyData['company_full_name']);
                } elseif (isset($societyData['society_name']) && !empty($societyData['society_name'])) {
                    $company_name = htmlspecialchars($societyData['society_name']);
                }

                // Populate client details from society_master (static details)
                // Client Name from secretary_name
                $client_name = isset($societyData['secretary_name']) && !empty($societyData['secretary_name'])
                    ? htmlspecialchars($societyData['secretary_name'])
                    : '';

                // Designation from secretary_designation (if exists)
                $client_designation = isset($societyData['secretary_designation']) && !empty($societyData['secretary_designation'])
                    ? htmlspecialchars($societyData['secretary_designation'])
                    : '';

                // Mobile Number from secretary_mobile (decrypt)
                $client_mobile = '';
                if (isset($societyData['secretary_mobile']) && !empty($societyData['secretary_mobile'])) {
                    try {
                        $client_mobile = htmlspecialchars($d->encryptDecrypt("decrypt", $societyData['secretary_mobile']));
                    } catch (Exception $e) {
                        $client_mobile = '';
                    }
                }

                // Email Address from secretary_email (decrypt)
                $client_email = '';
                if (isset($societyData['secretary_email']) && !empty($societyData['secretary_email'])) {
                    $client_email = $societyData['secretary_email'] ?? "";
                }
                $society_id = $society_id; // Store for later use
            } else {
                $company_id_error = true;
            }
        } else {
            $company_id_error = true;
        }
    } catch (Exception $e) {
        // Handle error
        $company_id_error = true;
        error_log("Error fetching society details: " . $e->getMessage());
    }
} else {
    $company_id_error = true;
}

// Check if form already submitted for this company
$form_already_submitted = false;
$submitted_form_data = null;
if (isset($_GET['c']) && !empty($_GET['c'])) {
    try {
        $check_soc_id = $d->encryptDecrypt("decrypt", $_GET['c']);
        $check_soc_id = (int)$check_soc_id;
        if ($check_soc_id > 0) {
            // Check if form already submitted
            $existingForm = $d->selectRow(
                "form_id, submitted_date, client_name, company_name",
                "training_completion_form_master",
                "society_id = '$check_soc_id'"
            );
            if ($existingForm && mysqli_num_rows($existingForm) > 0) {
                $form_already_submitted = true;
                $submitted_form_data = mysqli_fetch_assoc($existingForm);
            }
        }
    } catch (Exception $e) {
        // Ignore
    }
}

// Check if OTP is verified
$otp_verified = false;
if (isset($_GET['c']) && !empty($_GET['c'])) {
    try {
        $check_soc_id = $d->encryptDecrypt("decrypt", $_GET['c']);
        $check_soc_id = (int)$check_soc_id;
        if ($check_soc_id > 0) {
            $otp_verified = isset($_SESSION['training_form_verified_' . $check_soc_id])
                && $_SESSION['training_form_verified_' . $check_soc_id] === true;
        }
    } catch (Exception $e) {
        // Ignore
    }
}

// Fetch modules from training_module_master (training_module_status=0, module_type=1) grouped by topic/visit
$modulesByVisit = [];
$visitIndex = 0;
$modulesQuery = $d->selectRow(
    "tmm.training_module_id, tmm.training_module_name, tmm.training_module_order, tmm.module_priority,
     tmt.topic_id, tmt.topic_name",
    "training_module_master tmm
     LEFT JOIN training_module_topics tmt ON tmt.topic_id = tmm.topic_id AND tmt.topic_type = '0'
     LEFT JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
    "tmm.training_module_status = 0 AND tmm.module_type = 1",
    "ORDER BY (tmt.topic_name IS NULL), tmt.topic_name ASC, tmpm.priority_id ASC, COALESCE(tmm.training_module_order, 999) ASC, tmm.training_module_name ASC"
);
if ($modulesQuery && mysqli_num_rows($modulesQuery) > 0) {
    $currentTopicKey = null;
    while ($row = mysqli_fetch_array($modulesQuery)) {
        $topicName = isset($row['topic_name']) && $row['topic_name'] !== '' ? $row['topic_name'] : 'Other';
        $topicKey = (string)$row['topic_id'] . '|' . $topicName;
        if (!isset($modulesByVisit[$topicKey])) {
            $visitIndex++;
            $modulesByVisit[$topicKey] = [
                'visit_label' => 'Visit ' . $visitIndex,
                'visit_key' => 'visit_' . $visitIndex,
                'topic_name' => $topicName,
                'topic_id' => $row['topic_id'],
                'modules' => []
            ];
        }
        $modulesByVisit[$topicKey]['modules'][] = [
            'module_id' => $row['training_module_id'],
            'module_name' => $row['training_module_name']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training & Implementation Completion Form - CHL-MyCo</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="apAdmin/assets/plugins/select2/css/select2.min.css" rel="stylesheet" />

    <style>
        /* Modern Web Design Styles */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }

        .form-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header-section {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 40px 30px;
            margin-bottom: 0;
        }

        .header-section .header-logo {
            max-height: 70px;
            height: auto;
            width: auto;
            margin-bottom: 20px;
            display: inline-block;
            vertical-align: middle;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
        }

        .company-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .company-address {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .form-title {
            font-size: 22px;
            font-weight: 600;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .content-section {
            padding: 40px;
        }

        .section-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 3px solid #3498db;
            display: inline-block;
            width: 100%;
        }

        .form-group-row {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 14px;
            display: block;
        }

        .read-only-field {
            padding: 12px 15px;
            background: white;
            border-radius: 8px;
            min-height: 45px;
            color: #212529;
            font-size: 15px;
            border: 2px solid #e9ecef;
            display: flex;
            align-items: center;
        }

        .read-only-field.empty {
            color: #6c757d;
            font-style: italic;
        }

        .form-control {
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.2s;
            background: white;
        }

        .form-control:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-control::placeholder {
            color: #95a5a6;
        }

        /* Select2 styling to match form-control */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 45px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0;
            background: white;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 45px;
            padding-left: 15px;
            padding-right: 20px;
            font-size: 15px;
            color: #212529;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 43px;
            right: 10px;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3498db;
        }

        .select2-dropdown {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: #2c3e50;
            color: white;
            border-radius: 12px 12px 0 0;
            border-bottom: none;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        .modal-title {
            font-weight: 600;
        }

        #modal-otp-input {
            font-weight: bold;
        }

        /* Resend OTP Timer Styles */
        #resend-timer-container {
            font-size: 13px;
        }

        #resend-timer {
            font-weight: bold;
            color: #e74c3c;
        }

        #modal-resend-otp-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .table {
            margin-top: 20px;
            margin-bottom: 20px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table th {
            font-weight: 600;
            padding: 15px;
            border: none;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table td ul {
            margin: 0;
            padding-left: 20px;
        }

        .table td li {
            margin-bottom: 8px;
            color: #495057;
        }

        .declaration-box {
            background: #ecf0f1;
            color: #2c3e50;
            padding: 25px;
            border-radius: 12px;
            margin: 20px 0;
            border: 2px solid #bdc3c7;
        }

        .declaration-text {
            font-weight: 600;
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .checkbox-container {
            margin-top: 15px;
        }

        .checkbox-container input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
        }

        .checkbox-container label {
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .module-status-radio {
            margin-right: 15px;
        }

        .module-status-radio input[type="radio"] {
            margin-right: 5px;
            cursor: pointer;
        }

        .module-status-radio label {
            cursor: pointer;
            font-size: 14px;
        }

        /* Participants Section Styles */
        .participants-section {
            padding: 10px 0;
        }

        .participant-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            margin-bottom: 12px;
        }

        .participant-label {
            font-size: 15px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0;
            flex-shrink: 0;
        }

        .participant-options {
            display: flex;
            gap: 20px;
            flex-wrap: nowrap;
            align-items: center;
        }

        .participant-radio {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            font-size: 14px;
            color: #495057;
            margin: 0;
            white-space: nowrap;
        }

        .participant-radio-input {
            margin-right: 6px;
            cursor: pointer;
            width: 18px;
            height: 18px;
            accent-color: #3498db;
        }

        .participant-radio:hover {
            color: #3498db;
        }

        @media (max-width: 768px) {
            .participant-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .participant-label {
                margin-bottom: 10px;
            }

            .participant-options {
                width: 100%;
                justify-content: flex-start;
            }
        }

        .visit-actions {
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            padding: 8px;
        }

        .visit-actions .btn {
            font-size: 11px;
            padding: 6px 10px;
            border-radius: 4px;
            font-weight: 400;
            transition: background-color 0.15s ease;
            border: 1px solid #dee2e6;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            flex: 1;
            min-width: 0;
            background: white;
        }

        .visit-actions .btn:hover {
            background: #f8f9fa;
        }

        .visit-actions .btn-success {
            color: #27ae60;
            border-color: #27ae60;
        }

        .visit-actions .btn-success:hover {
            background: #d4edda;
            border-color: #27ae60;
        }

        .visit-actions .btn-warning {
            color: #f39c12;
            border-color: #f39c12;
        }

        .visit-actions .btn-warning:hover {
            background: #fff3cd;
            border-color: #f39c12;
        }

        .visit-actions .btn-secondary {
            color: #6c757d;
            border-color: #6c757d;
        }

        .visit-actions .btn-secondary:hover {
            background: #e9ecef;
            border-color: #6c757d;
        }

        /* Trainers Feedback star rating */
        .trainer-feedback-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .trainer-feedback-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .trainer-feedback-label {
            font-size: 15px;
            font-weight: 600;
            color: #495057;
            margin: 0;
            flex-shrink: 0;
        }

        .star-rating {
            display: inline-flex;
            gap: 4px;
            align-items: center;
        }

        .star-rating .star {
            cursor: pointer;
            color: #dee2e6;
            font-size: 24px;
            transition: color 0.15s ease;
        }

        .star-rating .star:hover {
            color: #f39c12;
        }

        .star-rating .star.filled i {
            color: #f39c12;
        }

        .otp-section {
            padding: 30px;
            background: #ecf0f1;
            border-radius: 12px;
            color: #2c3e50;
            border: 2px solid #bdc3c7;
        }

        #otp-input {
            font-size: 20px;
            text-align: center;
            letter-spacing: 8px;
            font-weight: bold;
            border-radius: 8px;
            border: 2px solid #95a5a6;
            background: white;
            color: #2c3e50;
            padding: 12px;
        }

        #otp-input::placeholder {
            color: #95a5a6;
        }

        #otp-input:focus {
            background: white;
            border-color: #3498db;
            outline: none;
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.2s;
            border: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-success {
            background: #27ae60;
            color: white;
        }

        .btn-warning {
            background: #f39c12;
            color: white;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px 20px;
        }

        .module-status-group {
            background: white;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            border-left: 4px solid #3498db;
        }

        .module-status-group.required {
            border-left-color: #e74c3c;
        }

        .module-status-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }

        .module-status-group>label::after {
            content: " *";
            color: #e74c3c;
        }

        .module-status-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .content-section {
                padding: 20px;
            }

            .visit-actions {
                flex-direction: column;
            }

            .visit-actions .btn {
                width: 100%;
            }

            .module-status-options {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="form-container">
        <!-- Header Section -->
        <div class="header-section">
            <?php if (file_exists(__DIR__ . '/img/logo.png')): ?>
                <img src="img/logo.png" alt="Logo" class="header-logo">
            <?php endif; ?>
            <div class="company-name">Communities Heritage Limited</div>
            <div class="company-address">A-Block, 5th Floor, WTT, World Trade Tower,Sarkhej Gandhinagar Hwy, Makarba, Ahmedabad, Gujarat 380051</div>
            <div class="form-title">Training & Implementation Completion Acknowledgement</div>
        </div>

        <?php
        // Check if company ID is missing or invalid
        if ($company_id_error || !$valid_company_id): ?>
            <div class="content-section">
                <div class="section-card">
                    <div class="alert alert-danger text-center" style="padding: 50px; font-size: 18px; background: #f8d7da; border: 2px solid #dc3545; border-radius: 12px;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 64px; color: #dc3545; margin-bottom: 20px;"></i>
                        <h3 style="color: #dc3545; margin-bottom: 15px;"><strong>Invalid Access</strong></h3>
                        <!-- <p style="font-size: 16px; margin-bottom: 10px; color: #721c24;">Company ID is missing or invalid.</p> -->
                        <p style="font-size: 14px; color: #6c757d; margin-bottom: 0;">Please access this form using a valid company link provided by CHL-MyCo.</p>
                    </div>
                </div>
            </div>
            <?php else:
            // Check if this is a successful submission (using session variable)
            $show_success_message = isset($_SESSION['training_form_submitted_success']) && $_SESSION['training_form_submitted_success'] === true;

            // Clear the session variable after checking (so it only shows once)
            if ($show_success_message) {
                unset($_SESSION['training_form_submitted_success']);
            }

            if ($form_already_submitted): ?>
                <div class="content-section">
                    <?php if ($show_success_message): ?>
                        <!-- Show only success message when session variable is set -->
                        <div class="section-card">
                            <div class="alert alert-success text-center" style="padding: 50px; font-size: 18px; background: #d4edda; border: 2px solid #27ae60; border-radius: 12px;">
                                <i class="fas fa-check-circle" style="font-size: 64px; color: #27ae60; margin-bottom: 20px;"></i>
                                <h3 style="color: #27ae60; margin-bottom: 15px;"><strong>Form Successfully Submitted!</strong></h3>
                                <p style="font-size: 16px; margin-bottom: 10px; color: #155724;">Your form has been submitted successfully.</p>
                                <?php if ($submitted_form_data):
                                    // Format date - assume stored date is in IST, format directly
                                    $submitted_date = $submitted_form_data['submitted_date'];
                                    try {
                                        // Parse the date and format it
                                        $date_obj = new DateTime($submitted_date);
                                        $formatted_date = $date_obj->format('d M Y, h:i A');
                                    } catch (Exception $e) {
                                        // Fallback
                                        $formatted_date = date('d M Y, h:i A', strtotime($submitted_date));
                                    }
                                ?>
                                    <p style="font-size: 14px; color: #6c757d; margin-bottom: 5px;"><strong>Submitted Date:</strong> <?php echo $formatted_date; ?></p>
                                    <p style="font-size: 14px; color: #6c757d; margin-bottom: 5px;"><strong>Client Name:</strong> <?php echo htmlspecialchars($submitted_form_data['client_name']); ?></p>
                                    <p style="font-size: 14px; color: #6c757d; margin-bottom: 0;"><strong>Company:</strong> <?php echo htmlspecialchars($submitted_form_data['company_name']); ?></p>
                                <?php endif; ?>
                                <p style="font-size: 14px; color: #6c757d; margin-top: 20px; margin-bottom: 0;">Thank you for completing the Training & Implementation Completion Form.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Show "already submitted" message on subsequent visits -->
                        <div class="section-card">
                            <div class="alert alert-warning text-center" style="padding: 40px; font-size: 18px;">
                                <i class="fas fa-check-circle" style="font-size: 48px; color: #f39c12; margin-bottom: 20px;"></i>
                                <h4><strong>Form Already Submitted</strong></h4>
                                <p class="mb-2">This training completion form has already been submitted for this company.</p>
                                <?php if ($submitted_form_data):
                                    // Format date - assume stored date is in IST, format directly
                                    $submitted_date = $submitted_form_data['submitted_date'];
                                    try {
                                        // Parse the date and format it
                                        $date_obj = new DateTime($submitted_date);
                                        $formatted_date = $date_obj->format('d M Y, h:i A');
                                    } catch (Exception $e) {
                                        // Fallback
                                        $formatted_date = date('d M Y, h:i A', strtotime($submitted_date));
                                    }
                                ?>
                                    <p class="mb-1"><strong>Submitted Date:</strong> <?php echo $formatted_date; ?></p>
                                    <p class="mb-1"><strong>Client Name:</strong> <?php echo htmlspecialchars($submitted_form_data['client_name']); ?></p>
                                    <p class="mb-0"><strong>Company:</strong> <?php echo htmlspecialchars($submitted_form_data['company_name']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <form id="trainingForm" method="POST" action="" onsubmit="return false;">
                    <div class="content-section" id="form-content-section">

                        <!-- Section 1: CHL Representative Details -->
                        <div class="section-card">
                            <h5 class="section-title">Section 1: CHL Representative Details</h5>
                            <div class="row form-group-row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Employee Name:</label>
                                    <div class="read-only-field employee-name-field <?php echo empty($employee_name) ? 'empty' : ''; ?>" data-field="employee_name">
                                        <?php echo !empty($employee_name) ? htmlspecialchars($employee_name) : 'Not assigned'; ?>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Designation:</label>
                                    <div class="read-only-field employee-designation-field <?php echo empty($employee_designation) ? 'empty' : ''; ?>" data-field="employee_designation">
                                        <?php echo !empty($employee_designation) ? htmlspecialchars($employee_designation) : ''; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Client Details -->
                        <div class="section-card">
                            <h5 class="section-title">Section 2: Client Details</h5>
                            <div class="row form-group-row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Company Name:</label>
                                    <div class="read-only-field company-name-field <?php echo empty($company_name) ? 'empty' : ''; ?>" data-field="company_name">
                                        <?php echo !empty($company_name) ? htmlspecialchars($company_name) : '—'; ?>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Client Name: <span style="color: #e74c3c;">*</span></label>
                                    <input type="text" class="form-control" name="client_name" id="client_name" value="" placeholder="Enter client name" required="" minlength="3" pattern="[A-Za-z\s]+" title="Client name must be at least 3 characters and contain only letters and spaces">
                                    <small class="form-text text-muted" id="client-name-help">Minimum 3 characters, letters and spaces only</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Designation: <span style="color: #e74c3c;">*</span></label>
                                    <input type="text" class="form-control" name="client_designation" value="" placeholder="Enter designation" required="">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mobile Number: <span style="color: #e74c3c;">*</span></label>
                                    <div class="row">
                                        <div class="col-4">
                                            <select name="client_country_code" class="form-control single-select" id="client_country_code" required="">
                                                <?php include 'apAdmin/country_code_option_list.php'; ?>
                                            </select>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control onlyNumber" name="client_mobile" id="client_mobile" value="" placeholder="Enter mobile number" maxlength="13" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address: <span style="color: #e74c3c;">*</span></label>
                                    <input type="email" class="form-control" name="client_email" value="" placeholder="Enter email address" required="">
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Participants -->
                        <div class="section-card">
                            <h5 class="section-title">Section 3: Participants</h5>
                            <div class="alert alert-info mb-3" style="background: #e7f3ff; border-left: 4px solid #3498db; border-radius: 6px;">
                                <strong><i class="fas fa-info-circle"></i> Instructions:</strong>
                                <p class="mb-0 mt-2" style="font-size: 14px;">
                                    Please select the participation status for each participant type:<br>
                                    <strong>Yes</strong> - Participant attended the training<br>
                                    <strong>No</strong> - Participant did not attend the training<br>
                                    <strong>NA</strong> - Not Applicable (this participant type is not relevant for this training)
                                </p>
                            </div>
                            <div class="participants-section">
                                <?php
                                // Fetch participants types from training_participants_type table
                                $participants = [];
                                $participantQuery = $d->selectRow("participants_type_id, participant_name", "training_participants_type", "1", "ORDER BY participants_type_id ASC");
                                if ($participantQuery && mysqli_num_rows($participantQuery) > 0) {
                                    while ($row = mysqli_fetch_assoc($participantQuery)) {
                                        $participants[] = [
                                            'participant_id' => (int)$row['participants_type_id'],
                                            'participant_name' => htmlspecialchars($row['participant_name'])
                                        ];
                                    }
                                }

                                // Display each participant with radio buttons
                                if (!empty($participants)) {
                                    foreach ($participants as $index => $participant) {
                                        $participantId = $participant['participant_id'];
                                        $participantName = $participant['participant_name'];
                                        $radioName = 'participant_status_' . $participantId;
                                ?>
                                        <div class="participant-item mb-1">
                                            <label class="participant-label">
                                                <strong><?php echo $participantName; ?>:</strong>
                                            </label>
                                            <div class="participant-options">
                                                <label class="participant-radio">
                                                    <input type="radio" name="<?php echo $radioName; ?>" value="yes" class="participant-radio-input" data-participant-id="<?php echo $participantId; ?>" data-participant-name="<?php echo htmlspecialchars($participantName, ENT_QUOTES); ?>" required> Yes
                                                </label>
                                                <label class="participant-radio">
                                                    <input type="radio" name="<?php echo $radioName; ?>" value="no" class="participant-radio-input" data-participant-id="<?php echo $participantId; ?>" data-participant-name="<?php echo htmlspecialchars($participantName, ENT_QUOTES); ?>" required> No
                                                </label>
                                                <label class="participant-radio">
                                                    <input type="radio" name="<?php echo $radioName; ?>" value="na" class="participant-radio-input" data-participant-id="<?php echo $participantId; ?>" data-participant-name="<?php echo htmlspecialchars($participantName, ENT_QUOTES); ?>" required> NA
                                                </label>
                                            </div>
                                        </div>
                                <?php
                                    }
                                } else {
                                    echo '<p class="text-muted">No participants found</p>';
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Section 4: Modules Covered -->
                        <div class="section-card">
                            <h5 class="section-title">Section 4: Modules Covered (Visits Table)</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">Visit</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($modulesByVisit)) {
                                        foreach ($modulesByVisit as $visitData) {
                                            $visitKey = $visitData['visit_key'];
                                            echo '<tr>';
                                            echo '<td><strong style="font-size: 16px; color: #2c3e50;">' . htmlspecialchars($visitData['visit_label']) . '</strong></td>';

                                            echo '<td>';
                                            echo '<div class="visit-actions">';
                                            echo '<button type="button" class="btn btn-success" onclick="setAllStatus(\'' . $visitKey . '\', \'completed\')" title="Mark all modules as Completed">';
                                            echo '<i class="fas fa-check-circle"></i> All Completed';
                                            echo '</button>';
                                            echo '<button type="button" class="btn btn-warning" onclick="setAllStatus(\'' . $visitKey . '\', \'na\')" title="Mark all modules as Not Applicable">';
                                            echo '<i class="fas fa-ban"></i> All NA';
                                            echo '</button>';
                                            echo '<button type="button" class="btn btn-secondary" onclick="setAllStatus(\'' . $visitKey . '\', \'pending\')" title="Mark all modules as Pending">';
                                            echo '<i class="fas fa-clock"></i> All Pending';
                                            echo '</button>';
                                            echo '</div>';
                                            foreach ($visitData['modules'] as $module) {
                                                $moduleId = $module['module_id'];
                                                $moduleName = $module['module_name'];
                                                $moduleNameSafe = htmlspecialchars($moduleName);
                                                $radioName = 'module_status_' . $moduleId;
                                                echo '<div class="module-status-group required">';
                                                echo '<label><strong>' . $moduleNameSafe . '</strong></label>';
                                                echo '<div class="module-status-options">';
                                                echo '<label class="module-status-radio"><input type="radio" name="' . $radioName . '" value="completed" data-visit="' . $visitKey . '" data-module-id="' . $moduleId . '" data-module-name="' . htmlspecialchars($moduleName, ENT_QUOTES) . '" class="module-status-radio-input" required> Completed</label>';
                                                echo '<label class="module-status-radio"><input type="radio" name="' . $radioName . '" value="na" data-visit="' . $visitKey . '" data-module-id="' . $moduleId . '" data-module-name="' . htmlspecialchars($moduleName, ENT_QUOTES) . '" class="module-status-radio-input" required> Not Applicable</label>';
                                                echo '<label class="module-status-radio"><input type="radio" name="' . $radioName . '" value="pending" data-visit="' . $visitKey . '" data-module-id="' . $moduleId . '" data-module-name="' . htmlspecialchars($moduleName, ENT_QUOTES) . '" class="module-status-radio-input" required> Pending</label>';
                                                echo '</div>';
                                                echo '</div>';
                                            }
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="3" class="text-muted">No modules configured. Please add modules in Training Module Master (training_module_status=0, module_type=1).</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Trainers Feedback (before Section 5: Declaration) -->
                        <div class="section-card">
                            <h5 class="section-title">Trainers Feedback</h5>
                            <p class="text-muted mb-4">Please rate the trainer on the following criteria (1 to 5 stars). <span style="color: #e74c3c;">*</span></p>
                            <div class="trainer-feedback-list">
                                <div class="trainer-feedback-row">
                                    <label class="trainer-feedback-label">Product Knowledge <span style="color: #e74c3c;">*</span></label>
                                    <div class="star-rating" data-input-name="trainer_feedback_product_knowledge">
                                        <input type="hidden" name="trainer_feedback_product_knowledge" value="" required>
                                        <span class="star" data-value="1" title="1 star"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="2" title="2 stars"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="3" title="3 stars"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="4" title="4 stars"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="5" title="5 stars"><i class="far fa-star"></i></span>
                                    </div>
                                </div>
                                <div class="trainer-feedback-row">
                                    <label class="trainer-feedback-label">Communication <span style="color: #e74c3c;">*</span></label>
                                    <div class="star-rating" data-input-name="trainer_feedback_communication">
                                        <input type="hidden" name="trainer_feedback_communication" value="" required>
                                        <span class="star" data-value="1" title="1 star"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="2" title="2 stars"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="3" title="3 stars"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="4" title="4 stars"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="5" title="5 stars"><i class="far fa-star"></i></span>
                                    </div>
                                </div>
                                <div class="trainer-feedback-row">
                                    <label class="trainer-feedback-label">Attire &amp; Behavior <span style="color: #e74c3c;">*</span></label>
                                    <div class="star-rating" data-input-name="trainer_feedback_attire_behavior">
                                        <input type="hidden" name="trainer_feedback_attire_behavior" value="" required>
                                        <span class="star" data-value="1" title="1 star"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="2" title="2 stars"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="3" title="3 stars"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="4" title="4 stars"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="5" title="5 stars"><i class="far fa-star"></i></span>
                                    </div>
                                </div>
                                <div class="trainer-feedback-row">
                                    <label class="trainer-feedback-label">Training Capabilities <span style="color: #e74c3c;">*</span></label>
                                    <div class="star-rating" data-input-name="trainer_feedback_training_capabilities">
                                        <input type="hidden" name="trainer_feedback_training_capabilities" value="" required>
                                        <span class="star" data-value="1" title="1 star"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="2" title="2 stars"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="3" title="3 stars"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="4" title="4 stars"><i class="far fa-star"></i></span>
                                        <span class="star" data-value="5" title="5 stars"><i class="far fa-star"></i></span>
                                    </div>
                                </div>
                                <div class="trainer-feedback-row">
                                    <label class="trainer-feedback-label">Feedback Remark </label>
                                    <div class="col-sm-9">
                                        <textarea class="col-sm-9 form-control" name="feedback_remark" id=""></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 5: Declaration -->
                        <div class="section-card">
                            <h5 class="section-title">Section 5: Declaration</h5>
                            <div class="declaration-box">
                                <div class="declaration-text">
                                    I hereby acknowledge that the implementation and training part is complete from CHL-MyCo's end and we are ready to roll-out live with the same.
                                </div>
                                <div class="checkbox-container">
                                    <label>
                                        <input type="checkbox" name="agree" required=""> Agree <span style="color: #e74c3c;">*</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="section-card">
                            <div class="text-center">
                                <button type="button" class="btn btn-success btn-lg" id="submit-form-btn">
                                    <i class="fas fa-check-circle"></i> Submit Form
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; // End of form_already_submitted check 
            ?>
        <?php endif; // End of valid_company_id check 
        ?>
    </div>

    <!-- OTP Verification Modal -->
    <div class="modal fade" id="otpVerificationModal" tabindex="-1" aria-labelledby="otpVerificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="otpVerificationModalLabel">OTP Verification Required</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Please verify your identity by entering the OTP sent to your registered email/mobile.</p>

                    <!-- Client Mobile Number Display (Reference) -->
                    <div class="alert alert-info mb-3" id="client-mobile-display">
                        <strong>OTP will be sent to:</strong> <span id="display-mobile-number">—</span>
                    </div>

                    <!-- Send OTP Section -->
                    <div id="modal-otp-send-section">
                        <button type="button" class="btn btn-primary w-100" id="modal-send-otp-btn" onclick="sendOTPFromModal()">
                            <i class="fas fa-paper-plane"></i> Send OTP
                        </button>
                        <div id="modal-otp-message" class="mt-3"></div>
                    </div>

                    <!-- Verify OTP Section (Hidden initially) -->
                    <div id="modal-otp-verify-section" style="display: none;">
                        <div class="form-group mb-3">
                            <label class="form-label">Enter OTP:</label>
                            <input type="text" class="form-control" id="modal-otp-input" name="modal_otp" maxlength="6" placeholder="Enter 6-digit OTP" style="text-align: center; font-size: 20px; letter-spacing: 8px;">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success flex-fill" id="modal-verify-otp-btn" onclick="verifyOTPFromModal()">
                                <i class="fas fa-check"></i> Verify OTP
                            </button>
                            <button type="button" class="btn btn-secondary flex-fill" id="modal-resend-otp-btn" onclick="resendOTPFromModal()" disabled>
                                <i class="fas fa-redo"></i> Resend OTP
                            </button>
                        </div>
                        <div id="resend-timer-container" class="text-center mt-2" style="display: none;">
                            <small class="text-muted">Resend OTP available in <span id="resend-timer">60</span> seconds</small>
                        </div>
                        <div id="modal-otp-verify-message" class="mt-3"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="apAdmin/assets/plugins/select2/js/select2.min.js"></script>
    <script>
        // Store OTP verification status
        var otpVerified = <?php echo (!empty($otp_verified)) ? 'true' : 'false'; ?>;

        // Get encrypted society_id from URL
        function getEncryptedSocietyId() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('c') || '';
        }

        // Send OTP
        function sendOTP() {
            const encryptedId = getEncryptedSocietyId();
            if (!encryptedId) {
                alert('Invalid request. Please refresh the page.');
                return;
            }

            $('#send-otp-btn').prop('disabled', true).text('Sending...');
            $('#otp-message').html('');

            $.ajax({
                url: 'trainingImplementationForm.php',
                type: 'POST',
                data: {
                    action: 'send_otp',
                    c: encryptedId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#otp-message').html('<div class="alert alert-success">' + response.message + '</div>');
                        $('#otp-send-section').hide();
                        $('#otp-verify-section').show();
                    } else {
                        $('#otp-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                        $('#send-otp-btn').prop('disabled', false).text('Send OTP');
                    }
                },
                error: function() {
                    $('#otp-message').html('<div class="alert alert-danger">Error sending OTP. Please try again.</div>');
                    $('#send-otp-btn').prop('disabled', false).text('Send OTP');
                }
            });
        }

        // Verify OTP
        function verifyOTP() {
            const encryptedId = getEncryptedSocietyId();
            const otp = $('#otp-input').val().trim();

            if (!otp || otp.length !== 6) {
                $('#otp-verify-message').html('<div class="alert alert-danger">Please enter a valid 6-digit OTP.</div>');
                return;
            }

            $('#verify-otp-btn').prop('disabled', true).text('Verifying...');
            $('#otp-verify-message').html('');

            $.ajax({
                url: 'trainingImplementationForm.php',
                type: 'POST',
                data: {
                    action: 'verify_otp',
                    c: encryptedId,
                    otp: otp
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        otpVerified = true;
                        $('#otp-verify-message').html('<div class="alert alert-success">' + response.message + '</div>');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        $('#otp-verify-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                        $('#verify-otp-btn').prop('disabled', false).text('Verify OTP');
                        $('#otp-input').val('');
                    }
                },
                error: function() {
                    $('#otp-verify-message').html('<div class="alert alert-danger">Error verifying OTP. Please try again.</div>');
                    $('#verify-otp-btn').prop('disabled', false).text('Verify OTP');
                }
            });
        }

        // Resend OTP
        function resendOTP() {
            $('#otp-verify-section').hide();
            $('#otp-send-section').show();
            $('#otp-input').val('');
            $('#otp-message').html('');
            sendOTP();
        }

        // Set all modules in a visit to the same status
        function setAllStatus(visitKey, status) {
            // Find all radio buttons for modules in this visit
            $('input.module-status-radio-input[data-visit="' + visitKey + '"]').each(function() {
                if ($(this).val() === status) {
                    $(this).prop('checked', true);
                }
            });
        }

        // Get all module statuses (for form submission)
        function getAllModuleStatuses() {
            var statuses = {};
            $('input.module-status-radio-input:checked').each(function() {
                var moduleId = $(this).data('module-id');
                var status = $(this).val();
                statuses[moduleId] = status;
            });
            return statuses;
        }

        // Allow Enter key to verify OTP and restrict to numbers only
        $(document).ready(function() {
            $('#otp-input').on('keypress', function(e) {
                // Only allow numbers
                if (e.which < 48 || e.which > 57) {
                    e.preventDefault();
                    return false;
                }
                // Allow Enter key to verify
                if (e.which === 13) {
                    verifyOTP();
                }
            });

            // Prevent paste of non-numeric content
            $('#otp-input').on('paste', function(e) {
                e.preventDefault();
                var paste = (e.originalEvent || e).clipboardData.getData('text');
                var numbers = paste.replace(/\D/g, '');
                $(this).val(numbers.substring(0, 6));
            });

            // Handle onlyNumber class for numeric-only input
            $(document).on('keydown', '.onlyNumber', function(e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                    // Allow: Ctrl+A, Command+A
                    (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) &&
                    (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });

            // Handle Client Name - no numbers allowed
            $('#client_name').on('keypress', function(e) {
                // Allow: letters, space, backspace, delete, tab, etc.
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 32]) !== -1 ||
                    (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    return;
                }
                // Allow only letters (A-Z, a-z)
                if ((e.shiftKey && e.keyCode >= 65 && e.keyCode <= 90) ||
                    (!e.shiftKey && e.keyCode >= 65 && e.keyCode <= 90) ||
                    (e.keyCode >= 97 && e.keyCode <= 122)) {
                    return;
                }
                // Block numbers and other characters
                e.preventDefault();
            });

            // Prevent paste of numbers in Client Name
            $('#client_name').on('paste', function(e) {
                e.preventDefault();
                var paste = (e.originalEvent || e).clipboardData.getData('text');
                // Remove numbers and keep only letters and spaces
                var cleaned = paste.replace(/[0-9]/g, '').replace(/[^A-Za-z\s]/g, '');
                var currentValue = $(this).val();
                $(this).val(currentValue + cleaned);
                // Trigger validation check after paste
                checkClientNameValidation();
            });

            // Function to check and show/hide help text based on validation
            function checkClientNameValidation() {
                var clientNameField = $('#client_name');
                var clientName = (clientNameField.val() || '').trim();
                var helpText = $('#client-name-help');

                // Check if field is valid: at least 3 characters, no numbers, matches pattern
                if (clientName.length >= 3 && !/\d/.test(clientName) && clientNameField.length && clientNameField[0].checkValidity()) {
                    helpText.hide();
                } else {
                    helpText.show();
                }
            }

            // Check validation on input and blur
            $('#client_name').on('input blur', function() {
                checkClientNameValidation();
            });

            // Hide help text initially if field is empty (will show when user starts typing)
            $(document).ready(function() {
                checkClientNameValidation();
            });

            // Initialize Select2 for single-select class
            $('.single-select').select2({
                placeholder: "-- Select --"
            });

            // Trainers Feedback: star rating click handler
            $('.star-rating .star').on('click', function() {
                var $star = $(this);
                var $rating = $star.closest('.star-rating');
                var value = parseInt($star.data('value'), 10);
                var $input = $rating.find('input[type="hidden"]');
                $input.val(value);
                $rating.find('.star').each(function() {
                    var $s = $(this);
                    if (parseInt($s.data('value'), 10) <= value) {
                        $s.addClass('filled').removeClass('active');
                        $s.find('i').removeClass('far').addClass('fas');
                    } else {
                        $s.removeClass('filled active');
                        $s.find('i').removeClass('fas').addClass('far');
                    }
                });
            });

            // Handle submit button click - validate form first, then open modal
            $('#submit-form-btn').on('click', function(e) {
                e.preventDefault();

                // Validate all required fields
                var isValid = true;
                var firstInvalidField = null;

                // Check Client Name
                var clientNameField = $('#client_name');
                var clientName = (clientNameField.val() || '').trim();
                if (!clientName || clientName.length < 3 || /\d/.test(clientName) || !clientNameField.length || !clientNameField[0].checkValidity()) {
                    isValid = false;
                    firstInvalidField = clientNameField;
                }

                // Check Designation
                if (!($('#trainingForm input[name="client_designation"]').val() || '').trim()) {
                    isValid = false;
                    if (!firstInvalidField) {
                        firstInvalidField = $('#trainingForm input[name="client_designation"]');
                    }
                }
                // Check Country Code
                if (!$('#client_country_code').val() || $('#client_country_code').val() === '') {
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = $('#client_country_code');
                }

                // Check Mobile Number
                if (!($('#client_mobile').val() || '').trim()) {
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = $('#client_mobile');
                }

                // Check Email
                var emailField = $('#trainingForm input[name="client_email"]');
                if (!(emailField.val() || '').trim() || !emailField.length || !emailField[0].checkValidity()) {
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = emailField;
                }

                // Check all module statuses are selected
                var moduleGroups = {};
                $('input.module-status-radio-input').each(function() {
                    var radioName = $(this).attr('name');
                    if (!moduleGroups[radioName]) {
                        moduleGroups[radioName] = true;
                        if (!$('input[name="' + radioName + '"]:checked').length) {
                            isValid = false;
                            if (!firstInvalidField) firstInvalidField = $(this).closest('.module-status-group');
                        }
                    }
                });

                // Check all participant statuses are selected
                var participantGroups = {};
                $('input.participant-radio-input').each(function() {
                    var radioName = $(this).attr('name');
                    if (!participantGroups[radioName]) {
                        participantGroups[radioName] = true;
                        if (!$('input[name="' + radioName + '"]:checked').length) {
                            isValid = false;
                            if (!firstInvalidField) firstInvalidField = $(this).closest('.participant-item');
                        }
                    }
                });

                // Check Trainers Feedback (all 4 star ratings required)
                var feedbackNames = ['trainer_feedback_product_knowledge', 'trainer_feedback_communication', 'trainer_feedback_attire_behavior', 'trainer_feedback_training_capabilities'];
                feedbackNames.forEach(function(name) {
                    var val = $('input[name="' + name + '"]').val();
                    if (!val || val < 1 || val > 5) {
                        isValid = false;
                        if (!firstInvalidField) firstInvalidField = $('input[name="' + name + '"]').closest('.trainer-feedback-row');
                    }
                });

                // Check Declaration checkbox
                if (!$('#trainingForm input[name="agree"]').is(':checked')) {
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = $('#trainingForm input[name="agree"]');
                }

                if (!isValid) {
                    if (firstInvalidField) {
                        $('html, body').animate({
                            scrollTop: firstInvalidField.offset().top - 100
                        }, 500);
                        // Focus on the field if it's an input/select, otherwise focus on first input within the container
                        if (firstInvalidField.is('input, select, textarea')) {
                            firstInvalidField.focus();
                        } else {
                            // For containers like module-status-group, find the first radio button
                            var firstRadio = firstInvalidField.find('input[type="radio"]').first();
                            if (firstRadio.length) {
                                firstRadio.focus();
                            }
                        }
                    }
                    return false;
                }

                // If all fields are valid, open modal
                $('#otpVerificationModal').modal('show');
            });

            // Handle modal opening - show client mobile number
            $('#otpVerificationModal').on('show.bs.modal', function() {
                // Get mobile number from form input
                var clientMobile = $('#client_mobile').val();
                var countryCode = $('#client_country_code').val();
                var fullMobile = '';

                if (countryCode && countryCode !== '') {
                    fullMobile = countryCode + ' ' + (clientMobile || '');
                } else {
                    fullMobile = clientMobile || '—';
                }

                $('#display-mobile-number').text(fullMobile || '—');

                // Reset modal state - show send OTP section first
                $('#modal-otp-send-section').show();
                $('#modal-otp-verify-section').hide();
                $('#modal-otp-input').val('');
                $('#modal-otp-message').html('');
                $('#modal-otp-verify-message').html('');
                $('#modal-send-otp-btn').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Send OTP');

                // Reset resend timer
                if (resendTimerInterval) {
                    clearInterval(resendTimerInterval);
                }
                $('#modal-resend-otp-btn').prop('disabled', true);
                $('#resend-timer-container').hide();
                resendTimerSeconds = 60;
            });

            // Timer variables for resend OTP
            var resendTimerInterval = null;
            var resendTimerSeconds = 60;

            // Function to start resend timer
            function startResendTimer() {
                // Clear any existing timer
                if (resendTimerInterval) {
                    clearInterval(resendTimerInterval);
                }

                // Disable resend button and show timer
                $('#modal-resend-otp-btn').prop('disabled', true);
                $('#resend-timer-container').show();
                resendTimerSeconds = 60;
                $('#resend-timer').text(resendTimerSeconds);

                // Start countdown
                resendTimerInterval = setInterval(function() {
                    resendTimerSeconds--;
                    $('#resend-timer').text(resendTimerSeconds);

                    if (resendTimerSeconds <= 0) {
                        clearInterval(resendTimerInterval);
                        $('#modal-resend-otp-btn').prop('disabled', false);
                        $('#resend-timer-container').hide();
                    }
                }, 1000);
            }

            // Send OTP from Modal
            window.sendOTPFromModal = function() {
                const encryptedId = getEncryptedSocietyId();
                if (!encryptedId) {
                    $('#modal-otp-message').html('<div class="alert alert-danger">Invalid request. Please refresh the page.</div>');
                    return;
                }

                // Get client mobile and email from form
                var clientCountryCode = $('#client_country_code').val() || '';
                var clientMobile = $('#client_mobile').val().trim() || '';
                var clientEmail = $('input[name="client_email"]').val().trim() || '';

                if (!clientMobile && !clientEmail) {
                    $('#modal-otp-message').html('<div class="alert alert-danger">Please enter mobile number or email address in Section 2 before sending OTP.</div>');
                    return;
                }

                $('#modal-send-otp-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
                $('#modal-otp-message').html('');

                $.ajax({
                    url: 'trainingImplementationForm.php',
                    type: 'POST',
                    data: {
                        action: 'send_otp',
                        c: encryptedId,
                        client_country_code: clientCountryCode,
                        client_mobile: clientMobile,
                        client_email: clientEmail
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#modal-otp-message').html('<div class="alert alert-success">' + response.message + '</div>');
                            $('#modal-otp-send-section').hide();
                            $('#modal-otp-verify-section').show();
                            $('#modal-otp-input').focus();

                            // Start resend timer
                            startResendTimer();
                        } else {
                            $('#modal-otp-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                            $('#modal-send-otp-btn').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Send OTP');
                        }
                    },
                    error: function() {
                        $('#modal-otp-message').html('<div class="alert alert-danger">Error sending OTP. Please try again.</div>');
                        $('#modal-send-otp-btn').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Send OTP');
                    }
                });
            };

            // Verify OTP from Modal
            window.verifyOTPFromModal = function() {
                const encryptedId = getEncryptedSocietyId();
                if (!encryptedId) {
                    $('#modal-otp-verify-message').html('<div class="alert alert-danger">Invalid request. Please refresh the page.</div>');
                    return;
                }

                var otp = $('#modal-otp-input').val().trim();
                if (!otp || otp.length !== 6) {
                    $('#modal-otp-verify-message').html('<div class="alert alert-danger">Please enter a valid 6-digit OTP.</div>');
                    return;
                }

                $('#modal-verify-otp-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Verifying...');
                $('#modal-otp-verify-message').html('');

                $.ajax({
                    url: 'trainingImplementationForm.php',
                    type: 'POST',
                    data: {
                        action: 'verify_otp',
                        c: encryptedId,
                        otp: otp
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            otpVerified = true;
                            $('#modal-otp-verify-message').html('<div class="alert alert-success">' + response.message + '</div>');

                            // Submit form data via AJAX
                            submitFormData();
                        } else {
                            $('#modal-otp-verify-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                            $('#modal-verify-otp-btn').prop('disabled', false).html('<i class="fas fa-check"></i> Verify OTP');
                            $('#modal-otp-input').val('');
                        }
                    },
                    error: function() {
                        $('#modal-otp-verify-message').html('<div class="alert alert-danger">Error verifying OTP. Please try again.</div>');
                        $('#modal-verify-otp-btn').prop('disabled', false).html('<i class="fas fa-check"></i> Verify OTP');
                    }
                });
            };

            // Resend OTP from Modal
            window.resendOTPFromModal = function() {
                // Check if timer is still running
                if (resendTimerSeconds > 0) {
                    return;
                }

                // Clear timer if running
                if (resendTimerInterval) {
                    clearInterval(resendTimerInterval);
                }

                // Reset and show send section
                $('#modal-otp-verify-section').hide();
                $('#modal-otp-send-section').show();
                $('#modal-otp-input').val('');
                $('#modal-otp-message').html('');
                $('#modal-otp-verify-message').html('');
                $('#resend-timer-container').hide();

                // Send OTP (which will start the timer again)
                sendOTPFromModal();
            };

            // Handle Enter key in modal OTP input
            $('#modal-otp-input').on('keypress', function(e) {
                // Only allow numbers
                if (e.which < 48 || e.which > 57) {
                    e.preventDefault();
                    return false;
                }
                // Allow Enter key to verify
                if (e.which === 13) {
                    verifyOTPFromModal();
                }
            });

            // Prevent paste of non-numeric content in modal OTP input
            $('#modal-otp-input').on('paste', function(e) {
                e.preventDefault();
                var paste = (e.originalEvent || e).clipboardData.getData('text');
                var numbers = paste.replace(/\D/g, '');
                $(this).val(numbers.substring(0, 6));
            });

            // Function to submit form data after OTP verification
            function submitFormData() {
                const encryptedId = getEncryptedSocietyId();
                if (!encryptedId) {
                    alert('Invalid request. Please refresh the page.');
                    return;
                }

                // Collect all form data
                var formData = {
                    action: 'submit_form',
                    c: encryptedId,
                    company_name: $('.company-name-field').text().trim(),
                    employee_name: $('.employee-name-field').text().trim(),
                    employee_designation: $('.employee-designation-field').text().trim(),
                    client_name: $('input[name="client_name"]').val(),
                    client_designation: $('input[name="client_designation"]').val(),
                    client_country_code: $('#client_country_code').val(),
                    client_mobile: $('#client_mobile').val(),
                    client_email: $('input[name="client_email"]').val(),
                    trainer_feedback_product_knowledge: $('input[name="trainer_feedback_product_knowledge"]').val(),
                    trainer_feedback_communication: $('input[name="trainer_feedback_communication"]').val(),
                    trainer_feedback_attire_behavior: $('input[name="trainer_feedback_attire_behavior"]').val(),
                    trainer_feedback_training_capabilities: $('input[name="trainer_feedback_training_capabilities"]').val(),
                    feedback_remark:$('textarea[name="feedback_remark"]').val(),
                    agree: $('input[name="agree"]').is(':checked') ? 'on' : ''
                };

                // Collect participant data (only status values, IDs and names are fetched from DB)
                $('input.participant-radio-input:checked').each(function() {
                    var name = $(this).attr('name');
                    formData[name] = $(this).val();
                });

                // Collect module data (only status values, IDs and names are fetched from DB)
                $('input.module-status-radio-input:checked').each(function() {
                    var name = $(this).attr('name');
                    formData[name] = $(this).val();
                });

                // Submit via AJAX
                $.ajax({
                    url: 'trainingImplementationForm.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Close modal immediately
                            $('#otpVerificationModal').modal('hide');

                            // Get current URL with encrypted ID
                            const encryptedId = getEncryptedSocietyId();
                            if (!encryptedId) {
                                alert('Invalid request. Please refresh the page.');
                                return;
                            }

                            // Redirect to same page (session variable will handle success message)
                            const currentUrl = window.location.pathname;
                            const redirectUrl = currentUrl + '?c=' + encodeURIComponent(encryptedId);

                            // Show success message immediately before redirect
                            var successHtml = '<div class="content-section">' +
                                '<div class="section-card" id="success-message">' +
                                '<div class="alert alert-success text-center" style="padding: 50px; font-size: 18px; background: #d4edda; border: 2px solid #27ae60; border-radius: 12px;">' +
                                '<i class="fas fa-check-circle" style="font-size: 64px; color: #27ae60; margin-bottom: 20px;"></i>' +
                                '<h3 style="color: #27ae60; margin-bottom: 15px;"><strong>Form Successfully Submitted!</strong></h3>' +
                                '<p style="font-size: 16px; margin-bottom: 10px; color: #155724;">' + response.message + '</p>' +
                                '<p style="font-size: 14px; color: #6c757d; margin-bottom: 0;">Thank you for completing the Training & Implementation Completion Form.</p>' +
                                '</div>' +
                                '</div>' +
                                '</div>';

                            // Hide the form and show success message
                            $('#trainingForm').hide();

                            // Replace content section with success message
                            $('#form-content-section').replaceWith(successHtml);

                            // Scroll to top immediately
                            window.scrollTo(0, 0);

                            // Redirect after 2 seconds (session variable will show success message on reload)
                            setTimeout(function() {
                                window.location.href = redirectUrl;
                            }, 2000);
                        } else {
                            $('#modal-otp-verify-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                            $('#modal-verify-otp-btn').prop('disabled', false).html('<i class="fas fa-check"></i> Verify OTP');
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMsg = 'Error submitting form. Please try again.';
                        try {
                            var errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.message) {
                                errorMsg = errorResponse.message;
                            }
                        } catch (e) {
                            // Use default error message
                        }
                        $('#modal-otp-verify-message').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                        $('#modal-verify-otp-btn').prop('disabled', false).html('<i class="fas fa-check"></i> Verify OTP');
                    }
                });
            }

            // Prevent form submission - only allow after OTP verification via modal
            $('#trainingForm').on('submit', function(e) {
                e.preventDefault();
                // Form submission is handled via AJAX in submitFormData() function
                // This prevents default form submission
                return false;
            });
        });
    </script>
</body>

</html>