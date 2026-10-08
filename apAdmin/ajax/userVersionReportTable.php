<?php
include_once('../common/objectController.php');

header('Content-Type: application/json');

// DataTables server-side parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 50;
$searchValue = isset($_POST['search']['value']) ? $d->escapeSqlLike(trim($_POST['search']['value'])) : '';
$orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 2;
$orderDir = $d->sanitizeDatatableOrderDir(isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC');

// Filter parameters from POST or GET
$countryId = isset($_POST['countryId']) ? $d->sanitizeReportFilterIdAsInt($_POST['countryId']) : (isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId']) : 0);
$sId = isset($_POST['sId']) ? $d->sanitizeReportFilterIdAsInt($_POST['sId']) : (isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0);
$cId = isset($_POST['cId']) ? $d->sanitizeReportFilterIdAsInt($_POST['cId']) : (isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : 0);
$version_type = isset($_POST['version_type']) ? $_POST['version_type'] : (isset($_GET['version_type']) ? $_GET['version_type'] : '0');
$version_type = in_array((string)$version_type, ['0', '1'], true) ? (string)$version_type : '0';
$device_type = isset($_POST['device_type']) ? $_POST['device_type'] : (isset($_GET['device_type']) ? $_GET['device_type'] : 'All');
$version_from = isset($_POST['version_from']) ? trim($_POST['version_from']) : (isset($_GET['version_from']) ? trim($_GET['version_from']) : '');
$version_to = isset($_POST['version_to']) ? trim($_POST['version_to']) : (isset($_GET['version_to']) ? trim($_GET['version_to']) : '');

$appendCountry = $appendState = $appendCity = '';
if ($countryId > 0) {
  $appendCountry = " AND society_master.country_id='$countryId'";
}
if ($sId > 0) {
  $appendState = " AND society_master.state_id='$sId'";
}
if ($cId > 0) {
  $appendCity = " AND society_master.city_id='$cId'";
}

$append_version_type = " AND user_version_data_master.type='" . $d->escapeSqlString($version_type) . "'";
$append_device_type = '';
if ($device_type === 'android' || $device_type === 'ios') {
  $append_device_type = " AND user_version_data_master.device_type = '" . $d->escapeSqlString($device_type) . "'";
}

// Version range filters in SQL (best-effort string comparison)
$versionFromCond = '';
$versionToCond = '';
if (!empty($version_from)) {
  $vf = $d->escapeSqlString($version_from);
  if ($version_type === '1') {
    $versionFromCond = " AND user_version_data_master.app_version_os >= '$vf'";
  } else {
    $versionFromCond = " AND user_version_data_master.app_version_code >= '$vf'";
  }
}
if (!empty($version_to)) {
  $vt = $d->escapeSqlString($version_to);
  if ($version_type === '1') {
    $versionToCond = " AND user_version_data_master.app_version_os <= '$vt'";
  } else {
    $versionToCond = " AND user_version_data_master.app_version_code <= '$vt'";
  }
}

$whereBase = "users_count>='0' $appendCountry $appendState $appendCity $append_version_type $append_device_type $versionFromCond $versionToCond";

// Global search
if ($searchValue !== '') {
  $sv = $searchValue;
  $whereBase .= " AND (society_master.society_name LIKE '%$sv%' OR user_version_data_master.users_count LIKE '%$sv%' OR user_version_data_master.device_type LIKE '%$sv%' OR user_version_data_master.app_version_code LIKE '%$sv%' OR user_version_data_master.app_version_os LIKE '%$sv%' OR user_version_data_master.last_updated_date LIKE '%$sv%')";
}

// Per-column search (top-footer inputs)
$columnSearchMap = [
  0 => 'user_version_data_master.id',
  1 => 'society_master.society_name',
  2 => 'user_version_data_master.users_count',
  3 => 'user_version_data_master.device_type',
  4 => 'user_version_data_master.type',
  5 => 'user_version_data_master.app_version_code', // or app_version_os when type=1
  6 => 'user_version_data_master.last_updated_date',
];
if (isset($_POST['columns']) && is_array($_POST['columns'])) {
  foreach ($_POST['columns'] as $idx => $col) {
    if (!isset($col['search']['value']) || trim($col['search']['value']) === '') {
      continue;
    }
    $val = trim($col['search']['value']);
    $sqlCol = isset($columnSearchMap[$idx]) ? $columnSearchMap[$idx] : null;
    if ($sqlCol === null) {
      continue;
    }
    if ($idx === 5 && $version_type === '1') {
      $sqlCol = 'user_version_data_master.app_version_os';
    }
    $valEsc = $d->escapeSqlLike($val);
    $whereBase .= " AND " . $sqlCol . " LIKE '%$valEsc%'";
  }
}

// Order by column: 0=#, 1=Company, 2=users count, 3=device, 4=version type, 5=version col, 6=Updated Date
$orderBy = 'user_version_data_master.users_count DESC';
$columnMap = [
  0 => 'user_version_data_master.id',
  1 => 'society_master.society_name',
  2 => 'user_version_data_master.users_count',
  3 => 'user_version_data_master.device_type',
  4 => 'user_version_data_master.type',
  5 => 'user_version_data_master.app_version_code',
  6 => 'user_version_data_master.last_updated_date',
];
if (isset($columnMap[$orderColumn])) {
  $orderBy = $columnMap[$orderColumn] . ' ' . $orderDir;
}
if ($orderColumn === 5 && $version_type === '1') {
  $orderBy = 'user_version_data_master.app_version_os ' . $orderDir;
}

// Count total matching records (must use same JOIN as main query)
$countFromTable = "user_version_data_master LEFT JOIN society_master ON society_master.society_id=user_version_data_master.society_id";
$countResult = $d->selectRow("COUNT(*) AS total", $countFromTable, $whereBase);
$totalRecords = 0;
if ($countResult && $row = mysqli_fetch_assoc($countResult)) {
  $totalRecords = (int) $row['total'];
}
$recordsFiltered = $totalRecords;

// Main data query with LIMIT (-1 = "All" from DataTables)
$start = max(0, $start);
if ($length == -1 || $length < 0) {
  $length = $totalRecords; // All records
} else {
  $length = max(0, min($length, 500));
}

$selectCols = "society_master.society_name, user_version_data_master.*";
$fromTable = "user_version_data_master LEFT JOIN society_master ON society_master.society_id=user_version_data_master.society_id";
$result = $d->selectRow($selectCols, $fromTable, $whereBase, "ORDER BY $orderBy LIMIT $start, $length");

$data = [];
$rowNum = $start + 1;
$versionTypeLabel = ($version_type === '1') ? 'Mobile OS Version' : 'App Version';

while ($result && $row = mysqli_fetch_array($result)) {
  $society_name = isset($row['society_name']) ? $row['society_name'] : '';
  $users_count = isset($row['users_count']) ? $row['users_count'] : '';
  $device_type_val = isset($row['device_type']) ? $row['device_type'] : '';
  $type_val = isset($row['type']) ? $row['type'] : '0';
  $app_version_code = isset($row['app_version_code']) ? $row['app_version_code'] : '';
  $app_version_os = isset($row['app_version_os']) ? $row['app_version_os'] : '';
  $last_updated_date = isset($row['last_updated_date']) ? $row['last_updated_date'] : '';

  $typeLabel = ($type_val === '0' || $type_val === 0) ? 'App Version' : 'Mobile OS Version';
  $versionCol = ($version_type === '1') ? $app_version_os : $app_version_code;

  $data[] = [
    $rowNum++,
    htmlspecialchars($society_name),
    $users_count,
    $device_type_val,
    $typeLabel,
    $versionCol,
    $last_updated_date,
  ];
}

echo json_encode([
  'draw' => (int) $draw,
  'recordsTotal' => $totalRecords,
  'recordsFiltered' => $recordsFiltered,
  'data' => $data,
]);
