<?php
include_once('../common/objectController.php');
header('Content-Type: application/json');
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 25;
$searchValue = isset($_POST['search']['value']) ? $d->escapeSqlLike(trim($_POST['search']['value'])) : '';
$orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = $d->sanitizeDatatableOrderDir($_POST['order'][0]['dir'] ?? 'ASC');

$countryId = isset($_POST['countryId']) ? $d->sanitizeReportFilterIdAsInt($_POST['countryId']) : (isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId']) : 0);
$stateId = isset($_POST['sId']) ? $d->sanitizeReportFilterIdAsInt($_POST['sId']) : (isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0);
$cityId = isset($_POST['cId']) ? $d->sanitizeReportFilterIdAsInt($_POST['cId']) : (isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : 0);
$societyId = isset($_POST['society_id']) ? $d->sanitizeReportFilterIdAsInt($_POST['society_id']) : (isset($_GET['society_id']) ? $d->sanitizeReportFilterIdAsInt($_GET['society_id']) : 0);

$conn = $d->dbCon();
$searchEsc = $conn->real_escape_string($searchValue);

$whereParts = ["sum.society_id = sm.society_id", "sm.country_id = c.country_id", "sm.state_id = s.state_id", "sm.city_id = ci.city_id"];
if ($countryId > 0) {
  $whereParts[] = "sm.country_id = '" . $conn->real_escape_string((string)$countryId) . "'";
}
if ($stateId > 0) {
  $whereParts[] = "sm.state_id = '" . $conn->real_escape_string((string)$stateId) . "'";
}
if ($cityId > 0) {
  $whereParts[] = "sm.city_id = '" . $conn->real_escape_string((string)$cityId) . "'";
}
if ($societyId > 0) {
  $whereParts[] = "sum.society_id = '" . $conn->real_escape_string((string)$societyId) . "'";
}
$whereClause = implode(' AND ', $whereParts);

// Global search: need PHP filtering to search encrypted columns
$hasGlobalSearch = ($searchValue !== '');
$needsPhpFiltering = $hasGlobalSearch;

$columns = "sum.society_user_id, sum.society_id, sum.society_user_name, sum.society_user_phone, sum.designation, sum.branch_name, sum.department_name, sum.created_date,
  sm.society_name, c.name AS country_name, s.name AS state_name, ci.name AS city_name";
$from = "society_users_master sum
  INNER JOIN society_master sm ON sum.society_id = sm.society_id
  LEFT JOIN countries c ON sm.country_id = c.country_id
  LEFT JOIN states s ON sm.state_id = s.state_id
  LEFT JOIN cities ci ON sm.city_id = ci.city_id";

$orderBy = "sum.society_user_id ASC";
$orderMap = [
  0 => 'sum.society_user_id',
  1 => 'sum.society_id',
  2 => 'sm.society_name',
  3 => 'c.name',
  4 => 's.name',
  5 => 'ci.name',
  6 => 'sum.society_user_name',
  7 => 'sum.society_user_phone',
  8 => 'sum.designation',
  9 => 'sum.branch_name',
  10 => 'sum.department_name',
  11 => 'sum.created_date',
];
if (isset($orderMap[$orderColumn])) {
  $orderBy = $orderMap[$orderColumn] . " " . $orderDir;
}

$totalWhere = implode(' AND ', array_slice($whereParts, 0, 4));
$totalCountResult = $d->selectRow("COUNT(sum.society_user_id) AS total", $from, $totalWhere);
$totalRecords = 0;
if ($totalCountResult && mysqli_num_rows($totalCountResult) > 0) {
  $row = mysqli_fetch_assoc($totalCountResult);
  $totalRecords = (int)$row['total'];
}

$shortAppName = $d->short_app_name();

if ($needsPhpFiltering) {
  // Filter by encrypted columns and/or global search: fetch in chunks, decrypt in PHP, match and paginate
  $chunkSize = 3000;
  $offset = 0;
  $matchCount = 0;
  $pageRows = [];
  $needCount = $start + $length;

  do {
    $limitClause = "LIMIT " . (int)$offset . ", " . (int)$chunkSize;
    $result = $d->selectRow($columns, $from, $whereClause, "ORDER BY $orderBy $limitClause");
    $chunkRows = 0;
    while ($row = mysqli_fetch_assoc($result)) {
      $chunkRows++; 
      $name = !empty($row['society_user_name']) ? $d->encryptDecrypt('decrypt', $row['society_user_name']) : '';
      $phone = !empty($row['society_user_phone']) ? $d->encryptDecrypt('decrypt', $row['society_user_phone']) : '';
      $designation = !empty($row['designation']) ? $d->encryptDecrypt('decrypt', $row['designation']) : '';
      $branch = !empty($row['branch_name']) ? $d->encryptDecrypt('decrypt', $row['branch_name']) : '';
      $dept = !empty($row['department_name']) ? $d->encryptDecrypt('decrypt', $row['department_name']) : '';

      // Check global search against all columns (encrypted and non-encrypted)
      if ($hasGlobalSearch) {
        $globalMatch = false;
        // Check non-encrypted columns
        if (stripos($row['society_name'] ?? '', $searchValue) !== false) $globalMatch = true;
        if (!$globalMatch && stripos($row['society_id'] ?? '', $searchValue) !== false) $globalMatch = true;
        if (!$globalMatch && stripos($row['country_name'] ?? '', $searchValue) !== false) $globalMatch = true;
        if (!$globalMatch && stripos($row['state_name'] ?? '', $searchValue) !== false) $globalMatch = true;
        if (!$globalMatch && stripos($row['city_name'] ?? '', $searchValue) !== false) $globalMatch = true;
        // Check encrypted columns
        if (!$globalMatch && stripos($name, $searchValue) !== false) $globalMatch = true;
        if (!$globalMatch && stripos($phone, $searchValue) !== false) $globalMatch = true;
        if (!$globalMatch && stripos($designation, $searchValue) !== false) $globalMatch = true;
        if (!$globalMatch && stripos($branch, $searchValue) !== false) $globalMatch = true;
        if (!$globalMatch && stripos($dept, $searchValue) !== false) $globalMatch = true;
        if (!$globalMatch) continue;
      }

      $matchCount++;
      if ($matchCount > $start && $matchCount <= $needCount) {
        $createdDate = !empty($row['created_date']) && $row['created_date'] != '0000-00-00 00:00:00'
          ? date('d M Y, h:i A', strtotime($row['created_date']))
          : '';
        $pageRows[] = [
          $matchCount,
          $shortAppName . '_' . $row['society_id'],
          htmlspecialchars($row['society_name'] ?? ''),
          htmlspecialchars($row['country_name'] ?? ''),
          htmlspecialchars($row['state_name'] ?? ''),
          htmlspecialchars($row['city_name'] ?? ''),
          htmlspecialchars($name),
          htmlspecialchars($phone),
          htmlspecialchars($designation),
          htmlspecialchars($branch),
          htmlspecialchars($dept),
          $createdDate,
        ];
      }
    }
    $offset += $chunkSize;
  } while ($chunkRows === $chunkSize);

  $filteredRecords = $matchCount;
  $data = $pageRows;
} else {
  // No global search - simple SQL query
  $countResult = $d->selectRow("COUNT(sum.society_user_id) AS total", $from, $whereClause);
  $filteredRecords = 0;
  if ($countResult && mysqli_num_rows($countResult) > 0) {
    $row = mysqli_fetch_assoc($countResult);
    $filteredRecords = (int)$row['total'];
  }
  $limitClause = ($length > 0) ? "LIMIT " . (int)$start . ", " . (int)$length : "";
  $result = $d->selectRow($columns, $from, $whereClause, "ORDER BY $orderBy $limitClause");
  $data = [];
  $rowIndex = $start + 1;
  while ($row = mysqli_fetch_assoc($result)) {
    $name = !empty($row['society_user_name']) ? $d->encryptDecrypt('decrypt', $row['society_user_name']) : '';
    $phone = !empty($row['society_user_phone']) ? $d->encryptDecrypt('decrypt', $row['society_user_phone']) : '';
    $designation = !empty($row['designation']) ? $d->encryptDecrypt('decrypt', $row['designation']) : '';
    $branch = !empty($row['branch_name']) ? $d->encryptDecrypt('decrypt', $row['branch_name']) : '';
    $dept = !empty($row['department_name']) ? $d->encryptDecrypt('decrypt', $row['department_name']) : '';
    $createdDate = !empty($row['created_date']) && $row['created_date'] != '0000-00-00 00:00:00'
      ? date('d M Y, h:i A', strtotime($row['created_date']))
      : '';
    $data[] = [
      $rowIndex++,
      $shortAppName . '_' . $row['society_id'],
      htmlspecialchars($row['society_name'] ?? ''),
      htmlspecialchars($row['country_name'] ?? ''),
      htmlspecialchars($row['state_name'] ?? ''),
      htmlspecialchars($row['city_name'] ?? ''),
      htmlspecialchars($name),
      htmlspecialchars($phone),
      htmlspecialchars($designation),
      htmlspecialchars($branch),
      htmlspecialchars($dept),
      $createdDate,
    ];
  }
}

echo json_encode([
  'draw' => $draw,
  'recordsTotal' => $totalRecords,
  'recordsFiltered' => $filteredRecords,
  'data' => $data,
]);
