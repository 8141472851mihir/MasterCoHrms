<?php
/**
 * Server-side export: streams full society users report as CSV (Excel-compatible).
 * Uses same filters as the report table; fetches in chunks to avoid memory issues.
 */
include_once('../common/objectController.php');

$countryId = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId']) : 0;
$stateId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
$cityId = isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : 0;
$societyId = isset($_GET['society_id']) ? $d->sanitizeReportFilterIdAsInt($_GET['society_id']) : 0;

$conn = $d->dbCon();
$whereParts = [
  "sum.society_id = sm.society_id",
  "sm.country_id = c.country_id",
  "sm.state_id = s.state_id",
  "sm.city_id = ci.city_id",
];
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

$columns = "sum.society_user_id, sum.society_id, sum.society_user_name, sum.society_user_phone, sum.designation, sum.branch_name, sum.department_name, sum.created_date,
  sm.society_name, c.name AS country_name, s.name AS state_name, ci.name AS city_name";
$from = "society_users_master sum
  INNER JOIN society_master sm ON sum.society_id = sm.society_id
  LEFT JOIN countries c ON sm.country_id = c.country_id
  LEFT JOIN states s ON sm.state_id = s.state_id
  LEFT JOIN cities ci ON sm.city_id = ci.city_id";
$orderBy = "ORDER BY sum.society_user_id ASC";
$chunkSize = 5000;
$offset = 0;
$shortAppName = $d->short_app_name();

header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=Society_Users_Report_" . date('Y-m-d-His') . ".csv");
header("Pragma: no-cache");
header("Expires: 0");
$out = fopen('php://output', 'w');
fprintf($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

$header = [
  'Sr No', 'Company Id', 'Company Name', 'Country', 'State', 'City',
  'User Name', 'Phone', 'Designation', 'Branch', 'Department', 'Created Date',
];
fputcsv($out, $header);
$sn = 0;

do {
  $limitClause = "LIMIT " . (int)$offset . ", " . (int)$chunkSize;
  $result = $d->selectRow($columns, $from, $whereClause, $orderBy . " " . $limitClause);
  $rowsInChunk = 0;

  while ($row = mysqli_fetch_assoc($result)) {
    $name = !empty($row['society_user_name']) ? $d->encryptDecrypt('decrypt', $row['society_user_name']) : '';
    $phone = !empty($row['society_user_phone']) ? $d->encryptDecrypt('decrypt', $row['society_user_phone']) : '';
    $designation = !empty($row['designation']) ? $d->encryptDecrypt('decrypt', $row['designation']) : '';
    $branch = !empty($row['branch_name']) ? $d->encryptDecrypt('decrypt', $row['branch_name']) : '';
    $dept = !empty($row['department_name']) ? $d->encryptDecrypt('decrypt', $row['department_name']) : '';
    $createdDate = !empty($row['created_date']) && $row['created_date'] != '0000-00-00 00:00:00'
      ? date('d M Y, h:i A', strtotime($row['created_date']))
      : '';

    $csvRow = [
      ++$sn,
      $shortAppName . '_' . $row['society_id'],
      $row['society_name'] ?? '',
      $row['country_name'] ?? '',
      $row['state_name'] ?? '',
      $row['city_name'] ?? '',
      $name,
      $phone,
      $designation,
      $branch,
      $dept,
      $createdDate,
    ];
    fputcsv($out, $csvRow);
    $rowsInChunk++;
  }

  $offset += $chunkSize;
  if (function_exists('flush')) {
    flush();
  }
} while ($rowsInChunk === $chunkSize);

fclose($out);
exit;
