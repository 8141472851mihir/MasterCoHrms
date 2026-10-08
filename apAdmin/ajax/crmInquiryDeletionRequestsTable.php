<?php
include_once('../common/objectController.php');
/** @var mysqli $con */
header('Content-Type: application/json');

// DataTables server-side processing parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 25;

$searchValue = isset($_POST['search']['value']) ? $d->escapeSqlLike(trim($_POST['search']['value'])) : '';
$statusFilter = isset($_POST['status_filter']) ? trim((string)$_POST['status_filter']) : 'all';

$orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 1;
$orderDir = $d->sanitizeDatatableOrderDir(isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC');
$orderDir = (strtolower($orderDir) === 'asc') ? 'ASC' : 'DESC';

$statusMap = [
  0 => 'Pending',
  1 => 'In Progress',
  2 => 'Complete',
  3 => 'Rejected'
];

function formatUnixTs($ts)
{
  if ($ts === null || $ts === '') return '';
  if (!is_numeric($ts)) return '';
  $ts = (int)$ts;
  if ($ts <= 0) return '';
  return date('d F Y, h:i A', $ts);
}

// Total records
$totalRecords = 0;
$totalRes = mysqli_query($con, "SELECT COUNT(*) AS total FROM crm_inquiry_deletion_requests");
if ($totalRes) {
  $row = mysqli_fetch_assoc($totalRes);
  $totalRecords = intval($row['total'] ?? 0);
}

// Filtering
$whereClause = "1=1";
if ($statusFilter !== '' && $statusFilter !== 'all') {
  $statusFilterInt = intval($statusFilter);
  if (in_array($statusFilterInt, [0, 1, 2, 3], true)) {
    $whereClause .= " AND crm_inquiry_status = '$statusFilterInt'";
  }
}
if ($searchValue !== '') {
  $sv = mysqli_real_escape_string($con, $searchValue);
  $whereClause .= " AND (
    user_id LIKE '%$sv%' OR
    algorithm LIKE '%$sv%' OR
    confirmation_code LIKE '%$sv%' OR
    CAST(id AS CHAR) LIKE '%$sv%' OR
    CAST(crm_inquiry_status AS CHAR) LIKE '%$sv%'
  )";
}

$filteredRecords = 0;
$filteredRes = $d->selectRow("COUNT(*) AS total","crm_inquiry_deletion_requests", "$whereClause");
if (mysqli_num_rows($filteredRes)>0) {
  $filteredRecords = mysqli_fetch_assoc($filteredRes)['total'];
}

// Ordering
$orderMap = [
  1 => 'id',
  2 => 'algorithm',
  3 => 'issued_at_unix',
  4 => 'expires_at_unix',
  5 => 'confirmation_code',
  6 => 'crm_inquiry_status',
  7 => 'created_at'
];
$orderBy = $orderMap[$orderColumn] ?? 'created_at';

$limitClause = '';
if ($length > 0) {
  $limitClause = " LIMIT " . intval($start) . ", " . intval($length);
}
$result= $d->selectRow("id, user_id, algorithm, issued_at_unix, expires_at_unix, confirmation_code, crm_inquiry_status, created_at","crm_inquiry_deletion_requests", "$whereClause", "ORDER BY $orderBy $orderDir $limitClause");


$data = [];
$rowIndex = $start + 1;

while ($row = mysqli_fetch_assoc($result)) {
  $id = intval($row['id']);
  $userId = $row['user_id'] ?? '';
  $algorithm = $row['algorithm'] ?? '';
  $confirmationCode = $row['confirmation_code'] ?? '';
  $crmInquiryStatus = isset($row['crm_inquiry_status']) ? intval($row['crm_inquiry_status']) : 0;
  $createdAt = $row['created_at'] ?? '';

  $issuedAt = formatUnixTs($row['issued_at_unix'] ?? '');
  $expiresAt = formatUnixTs($row['expires_at_unix'] ?? '');

  $createdAtFormatted = '';
  if (!empty($createdAt) && $createdAt !== '0000-00-00 00:00:00') {
    $createdAtFormatted = date('d F Y, h:i A', strtotime($createdAt));
  }

  $optionsHtml = '';
  foreach ($statusMap as $val => $label) {
    $selected = ($val === $crmInquiryStatus) ? 'selected' : '';
    $optionsHtml .= '<option value="' . htmlspecialchars((string)$val, ENT_QUOTES) . '" ' . $selected . '>' . htmlspecialchars($label, ENT_QUOTES) . '</option>';
  }

  $statusDropdown = '<select class="form-control form-control-sm crm-inquiry-status-select" ' .
    'data-id="' . htmlspecialchars((string)$id, ENT_QUOTES) . '" ' .
    'data-current="' . htmlspecialchars((string)$crmInquiryStatus, ENT_QUOTES) . '" ' .
    'style="min-width:140px">' . $optionsHtml . '</select>';

  $data[] = [
    $rowIndex++,
    htmlspecialchars((string)$userId, ENT_QUOTES),
    htmlspecialchars((string)$algorithm, ENT_QUOTES),
    htmlspecialchars((string)$issuedAt, ENT_QUOTES),
    htmlspecialchars((string)$expiresAt, ENT_QUOTES),
    htmlspecialchars((string)$confirmationCode, ENT_QUOTES),
    $statusDropdown,
    htmlspecialchars((string)$createdAtFormatted, ENT_QUOTES)
  ];
}

echo json_encode([
  'draw' => $draw,
  'recordsTotal' => $totalRecords,
  'recordsFiltered' => $filteredRecords,
  'data' => $data
]);
