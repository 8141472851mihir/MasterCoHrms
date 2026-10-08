<?php
include_once('../common/objectController.php');
header('Content-Type: application/json');
// Get DataTables server-side processing parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 25;
$searchValue = isset($_POST['search']['value']) ? $d->escapeSqlLike($_POST['search']['value']) : '';
$orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = $d->sanitizeDatatableOrderDir(isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'ASC');
$orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

// Get filter parameters from POST (passed via DataTables ajax.data) or GET (fallback)
$trainingStatusFilter = "";
$training_status = isset($_POST['training_status']) ? $_POST['training_status'] : (isset($_GET['training_status']) ? $_GET['training_status'] : '2');
if ($training_status === '0' || $training_status == '1') {
  $trainingStatusFilter = " AND society_master.training_status = '$training_status'";
}

$trainingDateFilter = "";
$Dttd = isset($_POST['Dttd']) ? $_POST['Dttd'] : (isset($_GET['Dttd']) ? $_GET['Dttd'] : '');
if (!empty($Dttd) && $Dttd != "0000-00-00 00:00:00") {
  $training_date_range = explode(" - ", $Dttd);
  if (count($training_date_range) == 2) {
    $date_from_training_date = date("Y-m-d", strtotime(trim($training_date_range[0])));
    $date_to_training_date = date("Y-m-d", strtotime(trim($training_date_range[1])));
    $trainingDateFilter = " AND DATE(society_master.training_completion_date) BETWEEN '$date_from_training_date' AND '$date_to_training_date'";
  }
}

$lastCallDateFilter = "";
$last_call_date = isset($_POST['last_call_date']) ? $_POST['last_call_date'] : (isset($_GET['last_call_date']) ? $_GET['last_call_date'] : '');
if (!empty($last_call_date)) {
  $dates = explode(" - ", $last_call_date);
  if (count($dates) == 2) {
    $date_from_call_date = date("Y-m-d", strtotime(trim($dates[0])));
    $date_to_call_date = date("Y-m-d", strtotime(trim($dates[1])));
    $lastCallDateFilter = " AND DATE(society_master.last_call_date) BETWEEN '$date_from_call_date' AND '$date_to_call_date'";
  }
}

$followupDateFilter = "";
$follow_up_date = isset($_POST['follow_up_date']) ? $_POST['follow_up_date'] : (isset($_GET['follow_up_date']) ? $_GET['follow_up_date'] : '');
if (!empty($follow_up_date)) {
  $dates = explode(" - ", $follow_up_date);
  if (count($dates) === 2) {
    $date_from_followup_date = trim($dates[0]);
    $date_to_followup_date = trim($dates[1]);
    if (!empty($date_from_followup_date) && !empty($date_to_followup_date)) {
      $date_from_followup_date = date("Y-m-d", strtotime($date_from_followup_date));
      $date_to_followup_date = date("Y-m-d", strtotime($date_to_followup_date));
      $followupDateFilter = " AND DATE(society_master.follow_up_date) BETWEEN '$date_from_followup_date' AND '$date_to_followup_date'";
    }
  }
}

// Get threshold parameters
function getThresholds($module) {
  $best = isset($_POST["{$module}_best"]) ? $_POST["{$module}_best"] : (isset($_GET["{$module}_best"]) ? $_GET["{$module}_best"] : 85);
  $average = isset($_POST["{$module}_average"]) ? $_POST["{$module}_average"] : (isset($_GET["{$module}_average"]) ? $_GET["{$module}_average"] : 60);
  $low = isset($_POST["{$module}_low"]) ? $_POST["{$module}_low"] : (isset($_GET["{$module}_low"]) ? $_GET["{$module}_low"] : 35);
  $best_color = isset($_POST["{$module}_best_color"]) ? $_POST["{$module}_best_color"] : (isset($_GET["{$module}_best_color"]) ? $_GET["{$module}_best_color"] : '#28a745');
  $average_color = isset($_POST["{$module}_average_color"]) ? $_POST["{$module}_average_color"] : (isset($_GET["{$module}_average_color"]) ? $_GET["{$module}_average_color"] : '#ffc107');
  $low_color = isset($_POST["{$module}_low_color"]) ? $_POST["{$module}_low_color"] : (isset($_GET["{$module}_low_color"]) ? $_GET["{$module}_low_color"] : '#dc3545');
  
  return [
    'best' => $best,
    'average' => $average,
    'low' => $low,
    'colors' => [
      'best' => $best_color,
      'average' => $average_color,
      'low' => $low_color,
    ]
  ];
}

$attendance = getThresholds('attendance');
$payroll = getThresholds('payroll');
$tracking = getThresholds('tracking');
$work = getThresholds('work');
$export_month = isset($_POST['export_month']) ? $_POST['export_month'] : (isset($_GET['export_month']) ? $_GET['export_month'] : 0);

// Handle export types - can come as array from POST/GET or single value
$export_type_attendance = [];
$source = isset($_POST['export_type_attendance']) ? $_POST['export_type_attendance'] : (isset($_GET['export_type_attendance']) ? $_GET['export_type_attendance'] : null);
if ($source !== null) {
  $export_type_attendance = is_array($source) ? $source : [$source];
} else {
  $export_type_attendance = [0, 1, 2];
}

$export_type_payroll = [];
$source = isset($_POST['export_type_payroll']) ? $_POST['export_type_payroll'] : (isset($_GET['export_type_payroll']) ? $_GET['export_type_payroll'] : null);
if ($source !== null) {
  $export_type_payroll = is_array($source) ? $source : [$source];
} else {
  $export_type_payroll = [0, 1, 2];
}

$export_type_tracking = [];
$source = isset($_POST['export_type_tracking']) ? $_POST['export_type_tracking'] : (isset($_GET['export_type_tracking']) ? $_GET['export_type_tracking'] : null);
if ($source !== null) {
  $export_type_tracking = is_array($source) ? $source : [$source];
} else {
  $export_type_tracking = [0, 1, 2];
}

$export_type_work_report = [];
$source = isset($_POST['export_type_work_report']) ? $_POST['export_type_work_report'] : (isset($_GET['export_type_work_report']) ? $_GET['export_type_work_report'] : null);
if ($source !== null) {
  $export_type_work_report = is_array($source) ? $source : [$source];
} else {
  $export_type_work_report = [0, 1, 2];
}

// Precompute integer filters once (instead of every row)
$export_type_attendance_int = array_map('intval', $export_type_attendance);
$export_type_payroll_int = array_map('intval', $export_type_payroll);
$export_type_tracking_int = array_map('intval', $export_type_tracking);
$export_type_work_report_int = array_map('intval', $export_type_work_report);

// Static map for lead source labels
$leadSources = [
  1 => 'Meta',
  2 => 'Inbound',
  3 => 'Walk IN',
  4 => 'Cold Data',
  5 => 'BA / Director Reference',
  6 => 'BNI Reference',
  7 => 'Event - Exhibitor',
  8 => 'Event - Exhibitor ( Exhibitor Cards )',
  9 => 'Event - Exhibitor ( Visitor Cards )',
  10 => 'Event - Industry Specific',
  11 => 'Event - Networking',
  12 => 'Existing Client',
  13 => 'Nikseam BPO',
  14 => 'Old Lead ( Any Source)',
  15 => 'Personal Reference',
  16 => 'Reference from demo client',
  17 => 'Reference from existing client',
  18 => 'Reference from Implementation team',
  19 => 'Rise',
  20 => 'Tech Imply',
  21 => 'Tech Jockey',
  22 => 'Website / Landing Page',
  23 => 'Website / Landing Page / ChatBot /MyCo App',
  24 => 'Whatsapp Bulkshoot'
];

function getCategory($value, $threshold) {
  if ($value >= $threshold['best']) {
    return 0;
  } elseif ($value >= $threshold['average']) {
    return 1;
  } else {
    return 2;
  }
}

function getColorStyle($value, $threshold) {
  if ($value >= $threshold['best']) {
    return "style='color: {$threshold['colors']['best']}; font-weight: bold;'";
  } elseif ($value >= $threshold['average']) {
    return "style='color: {$threshold['colors']['average']}; font-weight: bold;'";
  } else {
    return "style='color: {$threshold['colors']['low']}; font-weight: bold;'";
  }
}

// Build WHERE clause
$whereClause = "society_master.society_id=society_analytics_master.society_id 
  $trainingStatusFilter $trainingDateFilter $lastCallDateFilter $followupDateFilter";

// Add global search condition
if (!empty($searchValue)) {
  $whereClause .= " AND (society_master.society_name LIKE '%$searchValue%' 
    OR society_master.society_id LIKE '%$searchValue%' 
    OR business_entity_master.name LIKE '%$searchValue%' 
    OR society_master.city_name LIKE '%$searchValue%' 
    OR society_master.support_name LIKE '%$searchValue%' 
    OR society_master.engagement_call_executive_name LIKE '%$searchValue%')";
}

// Add per-column search conditions (footer inputs)
if (isset($_POST['columns']) && is_array($_POST['columns'])) {
  // Map DataTable column index => SQL column/expression
  $columnSearchMap = [
    1  => "society_master.society_id",
    2  => "society_master.engagement_call_executive_name",
    3  => "society_master.support_name",
    6  => "society_master.implementation_name",
    7  => "society_master.implementation_remark",
    8  => "society_master.society_name",
    10 => "society_master.city_name",
    21 => "DATE(society_master.created_date)",
    24 => "society_master.last_call_date",
    26 => "society_master.follow_up_date",
    127 => "society_master.refund_status",
    128 => "DATE(society_master.refund_date)",
    129 => "society_master.refund_amount",
    130 => "bms_admin_master.admin_name",
    131 => "society_master.refund_description",
  ];
  foreach ($_POST['columns'] as $idx => $col) {
    if (!isset($col['search']['value']) || $col['search']['value'] === '') {
      continue;
    }
    $val = $col['search']['value'];
    if (isset($columnSearchMap[$idx])) {
      $sqlCol = $columnSearchMap[$idx];
      // If date-like filter contains ' - ' range, handle BETWEEN
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

// Build ORDER BY clause
$orderBy = "society_analytics_master.analytics_id";
if ($orderColumn > 0) {
  // Map column index to actual column name (simplified - you may need to adjust)
  $columnMap = [
    1 => 'society_analytics_master.analytics_id',
    2 => 'society_master.engagement_call_executive_name',
    8 => 'society_master.society_name',
  ];
  if (isset($columnMap[$orderColumn])) {
    $orderBy = $columnMap[$orderColumn] . " " . $orderDir;
  }
}

$countResult = $d->selectRow("COUNT(DISTINCT society_master.society_id) as total", "society_analytics_master, society_master LEFT JOIN business_entity_master ON society_master.industry_type=business_entity_master.b_id","$whereClause");
$totalRecords=0;
if (mysqli_num_rows($countResult) > 0) {
  $dataCount = mysqli_fetch_assoc($countResult);
  $totalRecords = $dataCount['total'];
}
// Count total records
// Count filtered records (with search)
$filteredRecords = $totalRecords; // Same as total if no search

// Main query with pagination
$limitClause = '';
if ($length > 0) {
  $limitClause = "LIMIT $start, $length";
}

          $result = $d->selectRow(
          "business_entity_master.name AS society_type_name, 
          society_analytics_master.*,
          server_master.server_name,
          server_master.server_ip,
          society_resent_analytics_master.*, 
          society_analytics_master.last_updated_date, 
          IFNULL(trans_total.society_total, 0) AS society_total,
          society_master.*,
          society_master.created_date as society_created_date, 
          CASE WHEN plan_expire_date < CURDATE() THEN 'YES' ELSE 'NO' END AS is_expired,
          bms_admin_master.admin_name AS refund_coordinator_name,
          feedback_master.feedback_society_id AS feedback_form_id,
          training_completion_form_master.feedback_submitted_date",

          "society_analytics_master, society_master
          LEFT JOIN domain_master 
          ON society_master.domain_id = domain_master.domain_id 
          LEFT JOIN server_master 
          ON server_master.server_id = domain_master.server_id 
          LEFT JOIN society_resent_analytics_master 
          ON society_master.society_id = society_resent_analytics_master.society_id 
          LEFT JOIN (
              SELECT society_id, 
              SUM(transection_amount) AS society_total 
              FROM transection_master 
              GROUP BY society_id
          ) AS trans_total 
          ON society_master.society_id = trans_total.society_id
          LEFT JOIN business_entity_master 
          ON society_master.industry_type = business_entity_master.b_id
          LEFT JOIN bms_admin_master 
          ON bms_admin_master.admin_id = society_master.refund_person
          LEFT JOIN (
              SELECT DISTINCT society_id AS feedback_society_id
              FROM feedback_master
          ) AS feedback_master
          ON feedback_master.feedback_society_id = society_master.society_id
          LEFT JOIN (
              SELECT 
                  society_id,
                  MAX(submitted_date) AS feedback_submitted_date
              FROM training_completion_form_master
              GROUP BY society_id
          ) AS training_completion_form_master
          ON training_completion_form_master.society_id = society_master.society_id",
          "$whereClause",
          "ORDER BY $orderBy $limitClause"
          );

$data = [];
$rowIndex = $start + 1;
$todayDate = date('Y-m-d');

while ($row = mysqli_fetch_assoc($result)) {
  extract($row);
  
  // Calculate ratios
  // $stdAttendanceCount = ($employee_registration_limit != '0') ? ($employee_registration_limit * 26) : ($total_users * 26);
  $stdAttendanceCount = ($total_users * 26);
  $current_month_attendance_ratio = ($stdAttendanceCount != 0 ? round(($current_month_attendance_count / $stdAttendanceCount) * 100, 2) : 0);
  $last_month_attendance_ratio = ($stdAttendanceCount != 0 ? round(($last_month_attendance_count / $stdAttendanceCount) * 100, 2) : 0);
  $second_last_month_attendance_ratio = ($stdAttendanceCount != 0 ? round(($second_last_month_attendance_count / $stdAttendanceCount) * 100, 2) : 0);

  $stdPayrollCount = $total_users;
  $current_payroll_ratio = ($stdPayrollCount != 0 ? round(($current_month_payroll_count / $stdPayrollCount) * 100, 2) : 0);
  $last_payroll_ratio = ($stdPayrollCount != 0 ? round(($last_month_payroll_count / $stdPayrollCount) * 100, 2) : 0);
  $second_last_payroll_ratio = ($stdPayrollCount != 0 ? round(($second_last_month_payroll_count / $stdPayrollCount) * 100, 2) : 0);

  $current_month_tracking_user_ratio = ($employee_tracking_limit != 0 ? round(($current_month_tracking_user_count / $employee_tracking_limit) * 100, 2) : 0);
  $last_month_tracking_user_ratio = ($employee_tracking_limit != 0 ? round(($last_month_tracking_user_count / $employee_tracking_limit) * 100, 2) : 0);
  $second_last_month_tracking_user_ratio = ($employee_tracking_limit != 0 ? round(($second_last_month_tracking_user_count / $employee_tracking_limit) * 100, 2) : 0);

  $stdWorkReportCount = $total_users * 26;
  $current_work_ratio = ($stdWorkReportCount != 0 ? round(($current_month_work_report_count / $stdWorkReportCount) * 100, 2) : 0);
  $last_work_ratio = ($stdWorkReportCount != 0 ? round(($last_month_work_report_count / $stdWorkReportCount) * 100, 2) : 0);
  $second_last_work_ratio = ($stdWorkReportCount != 0 ? round(($second_last_month_work_report_count / $stdWorkReportCount) * 100, 2) : 0);

  // Determine categories based on export_month
  if($export_month=='0'){
    $cat_attendance = getCategory($current_month_attendance_ratio, $attendance);
    $cat_payroll = getCategory($current_payroll_ratio, $payroll);
    $cat_tracking = getCategory($current_month_tracking_user_ratio, $tracking);
    $cat_work = getCategory($current_work_ratio, $work);
  } elseif($export_month=='1'){
    $cat_attendance = getCategory($last_month_attendance_ratio, $attendance);
    $cat_payroll = getCategory($last_payroll_ratio, $payroll);
    $cat_tracking = getCategory($last_month_tracking_user_ratio, $tracking);
    $cat_work = getCategory($last_work_ratio, $work);
  } else {   
    $cat_attendance = getCategory($second_last_month_attendance_ratio, $attendance);
    $cat_payroll = getCategory($second_last_payroll_ratio, $payroll);
    $cat_tracking = getCategory($second_last_month_tracking_user_ratio, $tracking);
    $cat_work = getCategory($second_last_work_ratio, $work);
  }

  // Filter by export types (convert to int for comparison)
  $cat_attendance = (int)$cat_attendance;
  $cat_payroll = (int)$cat_payroll;
  $cat_tracking = (int)$cat_tracking;
  $cat_work = (int)$cat_work;
  
  if (!in_array($cat_attendance, $export_type_attendance_int, true) ||
      !in_array($cat_payroll, $export_type_payroll_int, true) ||
      !in_array($cat_tracking, $export_type_tracking_int, true) ||
      !in_array($cat_work, $export_type_work_report_int, true)) {
    continue; // Skip this row
  }
  $leadSource = $lead_sources ?? $requests_lead_sources ?? '';
  // Build row data
  $rowData = [
    $rowIndex++,
    '' . $d->short_app_name() . '_' . $society_id,
    $engagement_call_executive_name . 
      '<button data-toggle="modal" data-target="#changeEngagementNameModel" title="Change Engagement Executive?" class="btn text-warning btn-sm ml-2" onclick="changeEngagementNameId(\'' . htmlspecialchars($engagement_call_executive_name, ENT_QUOTES) . '\',\'' . $society_id . '\')"><i class="fa fa-pencil"></i></button>',
    $support_name . 
      '<button data-toggle="modal" data-target="#changeSupportNameModel" title="Change Support Name?" class="btn text-warning btn-sm ml-2" onclick="changeSupportNameId(\'' . htmlspecialchars($support_name, ENT_QUOTES) . '\',\'' . $society_id . '\')"><i class="fa fa-pencil"></i></button>',
    ($training_status == '1') ? 'Completed' : 'Pending',
    (!empty($training_completion_date) && $training_status == '1') ? date('d F Y, h:i A', strtotime($training_completion_date)) : '',
    $implementation_name,
    '<span class="editable-implementation-remark" data-id="' . $society_id . '">' . htmlspecialchars($implementation_remark) . '</span>',
    $society_name,
    $society_type_name,
    $city_name,
    $region_name,
    htmlspecialchars($society_address ?? ''),
    (isset($from_rise_event) && $from_rise_event == '1') ? 'Yes' : 'No',
    (isset($crm_created) && $crm_created == '1') ? 'Yes' : 'No',
    $leadSources[$leadSource] ?? '',
    '<span class="editable-spoc" data-id="' . $society_id . '" data-default="' . htmlspecialchars($secretary_name) . '">' . (!empty($last_spoc) ? htmlspecialchars($last_spoc) : htmlspecialchars($secretary_name)) . '</span>',
    '<span class="editable-spoc-designation" data-id="' . $society_id . '">' . (!empty($last_spoc_designation) ? htmlspecialchars($last_spoc_designation) : '') . '</span>',
    '<span class="editable-spoc-mobile" data-id="' . $society_id . '" data-default="' . htmlspecialchars($country_code . " " . $secretary_mobile) . '">' . (!empty($last_spoc_mobile_number) ? htmlspecialchars($last_spoc_mobile_number) : htmlspecialchars($country_code . " " . $secretary_mobile)) . '</span>',
    '',
    '',
    $employee_registration_limit,
    $employee_tracking_limit,
    $total_users,
    $total_login_user,
    ($package_id == '' || $package_id == '0') ? 'Free' : 'Paid',
    $society_total,
    (($society_created_date != '') ? date('Y-m-d', strtotime($society_created_date)) : ''),
    $plan_expire_date,
    (!empty($plan_expire_date) && $plan_expire_date >= $todayDate) ? 'Active' : 'Expired',
    '<span class="editable-date" data-id="' . $society_id . '">' . $last_call_date . '</span>',
    '<span class="editable-remark" data-id="' . $society_id . '">' . htmlspecialchars($last_call_remark) . '</span>',
    '<span class="editable-followup-date" data-id="' . $society_id . '">' . $follow_up_date . '</span>',
    $total_attendace,
    $stdAttendanceCount,
    '<span ' . getColorStyle($current_month_attendance_ratio, $attendance) . '>' . $current_month_attendance_ratio . '</span>',
    $current_month_attendance_count,
    '<span ' . getColorStyle($last_month_attendance_ratio, $attendance) . '>' . $last_month_attendance_ratio . '</span>',
    $last_month_attendance_count,
    '<span ' . getColorStyle($second_last_month_attendance_ratio, $attendance) . '>' . $second_last_month_attendance_ratio . '</span>',
    $second_last_month_attendance_count,
    $stdPayrollCount,
    '<span ' . getColorStyle($current_payroll_ratio, $payroll) . '>' . $current_payroll_ratio . '</span>',
    $current_month_payroll_count,
    '<span ' . getColorStyle($last_payroll_ratio, $payroll) . '>' . $last_payroll_ratio . '</span>',
    $last_month_payroll_count,
    '<span ' . getColorStyle($second_last_payroll_ratio, $payroll) . '>' . $second_last_payroll_ratio . '</span>',
    $second_last_month_payroll_count,
    $employee_tracking_limit,
    '<span ' . getColorStyle($current_month_tracking_user_ratio, $tracking) . '>' . $current_month_tracking_user_ratio . '</span>',
    $current_month_tracking_user_count,
    '<span ' . getColorStyle($last_month_tracking_user_ratio, $tracking) . '>' . $last_month_tracking_user_ratio . '</span>',
    $last_month_tracking_user_count,
    '<span ' . getColorStyle($second_last_month_tracking_user_ratio, $tracking) . '>' . $second_last_month_tracking_user_ratio . '</span>',
    $second_last_month_tracking_user_count,
    $stdWorkReportCount,
    '<span ' . getColorStyle($current_work_ratio, $work) . '>' . $current_work_ratio . '</span>',
    $current_month_work_report_count,
    '<span ' . getColorStyle($last_work_ratio, $work) . '>' . $last_work_ratio . '</span>',
    $last_month_work_report_count,
    '<span ' . getColorStyle($second_last_work_ratio, $work) . '>' . $second_last_work_ratio . '</span>',
    $second_last_month_work_report_count,
    ($total_users != '0') ? (round((($total_salary_slip * 100) / $total_users), 2)) : '0',
    $total_salary_slip,
    $active_tracking_users,
    $total_work_report,
    $order_count,
    $circular_count,
    $total_assets,
    $expenses_count,
    '',
    $total_leaves,
    '',
    $admin_size,
    $admin_view_access,
    $total_loan + $advance_salary,
    '',
    $task_count,
    $total_visitors,
    '',
    (int) $total_chat_msg,
    $total_timeline_post,
    $total_events,
    '',
    $total_penalty,
    '',
    $current_month_circular_count,
    $last_month_circular_count,
    $second_last_month_circular_count,
    $current_month_expense_count,
    $last_month_expense_count,
    $second_last_month_expense_count,
    $totalGoogleVisit,
    $thisMonthGoogleVisit,
    $preMonthGoogleVisit,
    $second_last_month_google_visit_count,
    $total_document,
    $current_month_task_count,
    $last_month_task_count,
    $second_last_month_task_count,
    $current_month_visitor_count,
    $last_month_visitor_count,
    $second_last_month_visitor_count,
    $current_month_wfh_count,
    $last_month_wfh_count,
    $second_last_month_wfh_count,
    $current_opening_count,
    $current_month_timeline_count,
    $last_month_timeline_count,
    $second_last_month_timeline_count,
    $current_month_event_count,
    $last_month_event_count,
    $second_last_month_event_count,
    $current_month_gallery_count,
    $last_month_gallery_count,
    $second_last_month_gallery_count,
    $current_month_penalty_count,
    $last_month_penalty_count,
    $second_last_month_penalty_count,
    $total_registered_vendors,
    $current_month_sales_order_count,
    $last_month_sales_order_count,
    $second_last_month_sales_order_count,
    '',
    '',
    '',
    $sub_domain,
    (!empty($last_updated_date) && $last_updated_date != '0000-00-00 00:00:00') ? date('d F Y, h:i A', strtotime($last_updated_date)) : '',
    (isset($refund_status) && $refund_status == '1') ? 'Yes' : 'No',
    (!empty($refund_date) && $refund_date != '0000-00-00 00:00:00') ? date('d F Y, h:i A', strtotime($refund_date)) : '',
    !empty($refund_amount) ? htmlspecialchars($refund_amount) : '',
    !empty($refund_coordinator_name) ? htmlspecialchars($refund_coordinator_name) : '',
    !empty($refund_description) ? htmlspecialchars($refund_description) : '',
    (!empty($feedback_form_id) ? 'Yes' : 'No'),
    (!empty($feedback_submitted_date) &&
    $feedback_submitted_date != '0000-00-00 00:00:00')
    ? date('d-m-Y h:i A', strtotime($feedback_submitted_date))
    : '-',
    (isset($support_handover) && $support_handover == '1') ? 'Yes' : 'No',
   (!empty($support_handover_date) &&
    $support_handover_date != '0000-00-00 00:00:00')
    ? date('d-m-Y h:i A', strtotime($support_handover_date))
    : '-',
    ];

  $data[] = $rowData;
}

// Note: Due to export filters being applied in PHP after query execution,
// recordsFiltered will be approximate. For exact count with export filters,
// we would need to apply the same filter logic to the count query.
// For performance, we'll use recordsTotal as recordsFiltered when export filters are active.
$hasExportFilters = (count($export_type_attendance) < 3 || count($export_type_payroll) < 3 || 
                     count($export_type_tracking) < 3 || count($export_type_work_report) < 3);

if ($hasExportFilters) {
  // When export filters are active, we can't get exact count without running full query
  // Use a reasonable approximation
  $filteredRecords = $totalRecords; // Will be adjusted based on actual returned data
} else {
  $filteredRecords = $totalRecords;
}

// Response structure for DataTables
echo json_encode([
  'draw' => $draw,
  'recordsTotal' => $totalRecords,
  'recordsFiltered' => $filteredRecords,
  'data' => $data
]);
?>

