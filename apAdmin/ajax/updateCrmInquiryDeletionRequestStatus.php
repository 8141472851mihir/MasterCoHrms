<?php
include_once('../common/objectController.php');
/** @var mysqli $con */
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'msg' => 'Method not allowed']);
  exit;
}

$id = isset($_POST['id']) ? $d->sanitizeActionIdAsInt($_POST['id']) : 0;
$crmInquiryStatus = isset($_POST['crm_inquiry_status']) ? $d->sanitizeReportFilterIdAsInt($_POST['crm_inquiry_status']) : null;

if ($id <= 0 || $crmInquiryStatus === null) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'msg' => 'Invalid payload']);
  exit;
}

$allowed = [0, 1, 2, 3];
if (!in_array($crmInquiryStatus, $allowed, true)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'msg' => 'Invalid status']);
  exit;
}

$crmInquiryStatusEsc = mysqli_real_escape_string($con, (string)$crmInquiryStatus);

$statusMap = [
  0 => 'Pending',
  1 => 'In Progress',
  2 => 'Complete',
  3 => 'Rejected'
];

// Fetch previous status for proper logging
$oldStatus = null;
$oldUserId = '';
$oldConfirmationCode = '';
$oldRes = $d->selectRow("crm_inquiry_status, user_id, confirmation_code","crm_inquiry_deletion_requests", "id = '$id'");
if (mysqli_num_rows($oldRes)>0) {
  $oldRow = mysqli_fetch_assoc($oldRes);
  $oldStatus = isset($oldRow['crm_inquiry_status']) ? intval($oldRow['crm_inquiry_status']) : null;
  $oldUserId = isset($oldRow['user_id']) ? (string)$oldRow['user_id'] : '';
  $oldConfirmationCode = isset($oldRow['confirmation_code']) ? (string)$oldRow['confirmation_code'] : '';
}

$ok = $d->update("crm_inquiry_deletion_requests", ["crm_inquiry_status" => $crmInquiryStatusEsc], "id = '$id'");

if (!$ok) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'msg' => 'DB error']);
  exit;
}

// Insert log for status change (admin side)
$societyForLog = '0';
$adminIdForLog = isset($bms_admin_id) ? (string)$bms_admin_id : '0';
$adminNameForLog = isset($created_by) ? (string)$created_by : 'Admin';
$fromLabel = ($oldStatus !== null && isset($statusMap[$oldStatus])) ? $statusMap[$oldStatus] : (string)($oldStatus ?? '');
$toLabel = isset($statusMap[$crmInquiryStatus]) ? $statusMap[$crmInquiryStatus] : (string)$crmInquiryStatus;
$logMessage = "CRM Inquiry Deletion Request Status Updated (User: {$oldUserId}, Confirmation: {$oldConfirmationCode}) ({$fromLabel} -> {$toLabel})";

// Best-effort logging; don't break API if logging fails
try {
  if (isset($d) && method_exists($d, 'insert_log_specific')) {
    // log_type = 7 => CRM Inquiry Deletion Requests (see apAdmin/spLogs.php)
    $d->insert_log_specific($societyForLog, $adminIdForLog, $adminNameForLog, $logMessage, 7);
  }
} catch (Exception $e) {
  // ignore
}

echo json_encode(['ok' => true, 'msg' => 'Updated successfully']);
exit;
