<?php
include_once('../common/objectController.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'msg' => 'Method not allowed']);
  exit;
}

$society_id = isset($_POST['society_id']) ? $d->sanitizeActionIdAsInt($_POST['society_id']) : 0;
$field = isset($_POST['field']) ? trim($_POST['field']) : '';
$value = isset($_POST['value']) ? trim($_POST['value']) : '';

if ($society_id <= 0 || $field === '') {
  http_response_code(400);
  echo json_encode(['ok' => false, 'msg' => 'Invalid payload']);
  exit;
}

// Allowed field -> column mapping (table: society_master)
$allowed = [
  'last_call_date' => 'last_call_date',
  'last_call_remark' => 'last_call_remark',
  'follow_up_date' => 'follow_up_date',
  'implementation_remark' => 'implementation_remark',
  'last_spoc' => 'last_spoc',
  'last_spoc_designation' => 'last_spoc_designation',
  'last_spoc_mobile_number' => 'last_spoc_mobile_number',
];

if (!isset($allowed[$field])) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'msg' => 'Field not allowed']);
  exit;
}

$column = $allowed[$field];

// Normalize dates to Y-m-d when editing date fields
if (in_array($field, ['last_call_date', 'follow_up_date'], true)) {
  if ($value === '') {
    $value = null;
  } else {
    $ts = strtotime($value);
    if ($ts !== false) {
      $value = date('Y-m-d', $ts);
    }
  }
}

$safeValue = isset($value) ? mysqli_real_escape_string($con, $value) : null;
$societyId = intval($society_id);

if ($safeValue === null) {
  $sql = "UPDATE society_master SET $column = NULL WHERE society_id = $societyId LIMIT 1";
} else {
  $sql = "UPDATE society_master SET $column = '$safeValue' WHERE society_id = $societyId LIMIT 1";
}

$ok = mysqli_query($con, $sql);
if (!$ok) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'msg' => 'DB error']);
  exit;
}

echo json_encode(['ok' => true]);
?>


