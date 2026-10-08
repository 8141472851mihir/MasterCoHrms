<?php
include_once('../common/objectController.php');
header('Content-Type: application/json');

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 25;
$searchValue = isset($_POST['search']['value']) ? $d->escapeSqlLike($_POST['search']['value']) : '';
$orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = $d->sanitizeDatatableOrderDir(isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'ASC');

$submittedDateFilter = '';
$employeeFilter = '';

// Get filter parameters from POST (DataTables AJAX) or GET (form submission)
$submitted_date_range_param = isset($_POST['submitted_date_range']) ? $_POST['submitted_date_range'] : (isset($_GET['submitted_date_range']) ? $_GET['submitted_date_range'] : '');
$employee_name_param = isset($_POST['employee_name']) ? $_POST['employee_name'] : (isset($_GET['employee_name']) ? $_GET['employee_name'] : '');

if (!empty($submitted_date_range_param) && $submitted_date_range_param != "0000-00-00 00:00:00") {
  $submitted_date_range = explode(" - ", $submitted_date_range_param);
  if (count($submitted_date_range) == 2) {
    $date_from_submitted = date("Y-m-d", strtotime(trim($submitted_date_range[0])));
    $date_to_submitted = date("Y-m-d", strtotime(trim($submitted_date_range[1])));
    $submittedDateFilter = " AND DATE(tcfm.submitted_date) BETWEEN '$date_from_submitted' AND '$date_to_submitted'";
  }
}

if (!empty($employee_name_param) && $employee_name_param != 'All') {
  $employee_name = $d->escapeSqlString($employee_name_param);
  $employeeFilter = " AND tcfm.employee_name = '$employee_name'";
}

$whereClause = "1=1" . $submittedDateFilter . $employeeFilter;

if (!empty($searchValue)) {
  $whereClause .= " AND (tcfm.company_name LIKE '%$searchValue%' 
    OR tcfm.employee_name LIKE '%$searchValue%' 
    OR tcfm.client_name LIKE '%$searchValue%' 
    OR sm.society_name LIKE '%$searchValue%'";
  
  // Only add trainer feedback columns to search if they exist
  if ($feedbackColumnsExist) {
    $whereClause .= "
    OR tcfm.trainer_feedback_product_knowledge LIKE '%$searchValue%'
    OR tcfm.trainer_feedback_communication LIKE '%$searchValue%'
    OR tcfm.trainer_feedback_attire_behavior LIKE '%$searchValue%'
    OR tcfm.trainer_feedback_training_capabilities LIKE '%$searchValue%'";
  }
  
  $whereClause .= ")";
}

$columnSearchMap = [
  1 => "tcfm.company_name",
  2 => "tcfm.employee_name",
  3 => "tcfm.employee_designation",
  4 => "tcfm.client_name",
  5 => "tcfm.client_designation",
  6 => "tcfm.client_mobile",
  7 => "tcfm.client_email",
  16 => "DATE(tcfm.submitted_date)",
];

if (isset($_POST['columns']) && is_array($_POST['columns'])) {
  foreach ($_POST['columns'] as $idx => $col) {
    if (!isset($col['search']['value']) || $col['search']['value'] === '') {
      continue;
    }
    $val = $col['search']['value'];
    if (isset($columnSearchMap[$idx])) {
      $sqlCol = $columnSearchMap[$idx];
      if (strpos($val, ' - ') !== false && stripos($sqlCol, 'date') !== false) {
        $parts = explode(' - ', $val);
        if (count($parts) === 2) {
          $from = date('Y-m-d', strtotime(trim($parts[0])));
          $to   = date('Y-m-d', strtotime(trim($parts[1])));
          $whereClause .= " AND $sqlCol BETWEEN '$from' AND '$to'";
        }
      } else {
        $valLower = $d->escapeSqlLike(strtolower($val));
        $whereClause .= " AND LOWER($sqlCol) LIKE '%$valLower%'";
      }
    }
  }
}

$orderBy = "tcfm.submitted_date DESC";
if ($orderColumn > 0) {
  $columnMap = [
    1 => 'tcfm.company_name',
    2 => 'tcfm.employee_name',
    3 => 'tcfm.employee_designation',
    4 => 'tcfm.client_name',
    16 => 'tcfm.submitted_date',
  ];
  if (isset($columnMap[$orderColumn])) {
    $orderBy = $columnMap[$orderColumn] . " " . $orderDir;
  }
}

try {
  $countResult = $d->selectRow("COUNT(*) as total", "training_completion_form_master tcfm LEFT JOIN society_master sm ON tcfm.society_id = sm.society_id", "$whereClause");
  $totalRecords = 0;
  if ($countResult && mysqli_num_rows($countResult) > 0) {
    $dataCount = mysqli_fetch_assoc($countResult);
    $totalRecords = $dataCount['total'];
  }
  $filteredRecords = $totalRecords;

  $limitClause = '';
  if ($length > 0) {
    $limitClause = "LIMIT $start, $length";
  }

  // Build SELECT columns - include trainer feedback columns only if they exist
  $selectColumns = "tcfm.form_id, tcfm.society_id, tcfm.company_name, tcfm.employee_name, tcfm.employee_designation, 
    tcfm.client_name, tcfm.client_designation, tcfm.client_country_code, tcfm.client_mobile, tcfm.client_email,
    tcfm.completed_modules_count, tcfm.na_modules_count, tcfm.pending_modules_count, tcfm.total_modules_count,
    tcfm.submitted_date, tcfm.created_date, sm.society_name";
  
  if ($feedbackColumnsExist) {
    $selectColumns .= ", tcfm.trainer_feedback_product_knowledge, tcfm.trainer_feedback_communication, 
      tcfm.trainer_feedback_attire_behavior, tcfm.trainer_feedback_training_capabilities";
  }

  $result = $d->selectRow($selectColumns, "training_completion_form_master tcfm LEFT JOIN society_master sm ON tcfm.society_id = sm.society_id", "$whereClause", "ORDER BY $orderBy $limitClause");
} catch (Exception $e) {
  echo json_encode([
    'draw' => $draw,
    'recordsTotal' => 0,
    'recordsFiltered' => 0,
    'data' => [],
    'error' => 'Database error: ' . $e->getMessage()
  ]);
  exit;
}

// Function to render star rating (defined outside loop to avoid redeclaration)
function renderStarRating($rating) {
  if (!$rating || $rating < 1 || $rating > 5) {
    return '<span class="text-muted"></span>';
  }
  return ' <small class="text-muted">(' . $rating . '/5)</small>';
}

$data = [];
$rowIndex = $start + 1;

if ($result) {
  while ($row = mysqli_fetch_array($result)) {
  $company_name = htmlspecialchars($row['company_name'] ?: $row['society_name']);
  $employee_name = htmlspecialchars($row['employee_name']);
  $employee_designation = htmlspecialchars($row['employee_designation']);
  $client_name = htmlspecialchars($row['client_name']);
  $client_designation = htmlspecialchars($row['client_designation']);
  $client_mobile = htmlspecialchars($row['client_country_code'] . ' ' . $row['client_mobile']);
  $client_email = htmlspecialchars($row['client_email']);
  $completed_modules = (int)$row['completed_modules_count'];
  $na_modules = (int)$row['na_modules_count'];
  $pending_modules = (int)$row['pending_modules_count'];
  $total_modules = (int)$row['total_modules_count'];
  
  // Trainer Feedback Ratings (1-5 stars)
  $feedback_product_knowledge = isset($row['trainer_feedback_product_knowledge']) && $row['trainer_feedback_product_knowledge'] > 0 ? (int)$row['trainer_feedback_product_knowledge'] : null;
  $feedback_communication = isset($row['trainer_feedback_communication']) && $row['trainer_feedback_communication'] > 0 ? (int)$row['trainer_feedback_communication'] : null;
  $feedback_attire_behavior = isset($row['trainer_feedback_attire_behavior']) && $row['trainer_feedback_attire_behavior'] > 0 ? (int)$row['trainer_feedback_attire_behavior'] : null;
  $feedback_training_capabilities = isset($row['trainer_feedback_training_capabilities']) && $row['trainer_feedback_training_capabilities'] > 0 ? (int)$row['trainer_feedback_training_capabilities'] : null;
  
  $submitted_date = $row['submitted_date'];
  $submitted_date_formatted = !empty($submitted_date) ? date('d M Y, h:i A', strtotime($submitted_date)) : '';
  
  $form_id = (int)$row['form_id'];
  $view_details_btn = '<button type="button" class="btn btn-sm btn-primary" onclick="viewFormDetails(' . $form_id . ')"><i class="fa fa-eye"></i> View</button>';

  $rowData = [
    $rowIndex++,
    $company_name,
    $employee_name,
    $employee_designation,
    $client_name,
    $client_designation,
    $client_mobile,
    $client_email,
    $completed_modules,
    $na_modules,
    $pending_modules,
    $total_modules,
    renderStarRating($feedback_product_knowledge),
    renderStarRating($feedback_communication),
    renderStarRating($feedback_attire_behavior),
    renderStarRating($feedback_training_capabilities),
    $submitted_date_formatted,
    $view_details_btn
  ];

    $data[] = $rowData;
  }
}

echo json_encode([
  'draw' => $draw,
  'recordsTotal' => $totalRecords,
  'recordsFiltered' => $filteredRecords,
  'data' => $data
]);
?>
