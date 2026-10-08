<?php error_reporting(0);
$bId = isset($_REQUEST['bId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['bId']) : 0;
$dId = isset($_REQUEST['dId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['dId']) : 0;
$uId = isset($_REQUEST['uId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['uId']) : 0;
$currentYear = date('Y');
$currentMonth = date('m');
$nextYear = date('Y', strtotime('+1 year'));
$onePreviousYear = date('Y', strtotime('-1 year'));
$twoPreviousYear = date('Y', strtotime('-2 year'));
$_GET['month_year'] = (isset($_REQUEST['month_year']) && $_REQUEST['month_year'] !== '')
    ? $d->sanitizeReportFilterMonthYear($_REQUEST['month_year'], date('Y-m'))
    : '';
$from = $d->sanitizeReportFilterDate(isset($_GET['from']) ? $_GET['from'] : '', '');
$toDate = $d->sanitizeReportFilterDate(isset($_GET['toDate']) ? $_GET['toDate'] : '', '');
function getThresholds($module)
{
  return [
    'best' => $_GET["{$module}_best"] ?? 85,
    'average' => $_GET["{$module}_average"] ?? 60,
    'low' => $_GET["{$module}_low"] ?? 35,
    'colors' => [
      'best' => $_GET["{$module}_best_color"] ?? '#28a745',
      'average' => $_GET["{$module}_average_color"] ?? '#ffc107',
      'low' => $_GET["{$module}_low_color"] ?? '#dc3545',
    ]
  ];
}
$attendance = getThresholds('attendance');
$payroll = getThresholds('payroll');
$tracking = getThresholds('tracking');
$work = getThresholds('work');
function getColorStyle($value, $threshold)
{
  if ($value >= $threshold['best']) {
    return "style='color: {$threshold['colors']['best']}; font-weight: bold;'";
  } elseif ($value >= $threshold['average']) {
    return "style='color: {$threshold['colors']['average']}; font-weight: bold;'";
  } else {
    return "style='color: {$threshold['colors']['low']}; font-weight: bold;'";
  }
}
function getCategory($value, $threshold)
{
  if ($value >= $threshold['best']) {
    return 0;
  } elseif ($value >= $threshold['average']) {
    return 1;
  } else {
    return 2;
  }
}
$export_type_attendance = $_GET['export_type_attendance'] ?? [0, 1, 2];
$export_type_payroll = $_GET['export_type_payroll'] ?? [0, 1, 2];
$export_type_tracking = $_GET['export_type_tracking'] ?? [0, 1, 2];
$export_type_work_report = $_GET['export_type_work_report'] ?? [0, 1, 2];
$export_month = $_GET['export_month'] ?? 0;

if (isset($_GET['dttd']) && !empty($_GET['dttd']) && $_GET['dttd'] != "0000-00-00 00:00:00") {
  $date_from_training_date = $_GET['dttd'];
} else {
  $date_from_training_date = date("Y-m-01");
}
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row align-items-end mb-4">
      <div class="col-sm-12 d-flex align-items-center mb-3">
        <h4 class="page-title mb-0">Company Engagement Report</h4>
      </div>
      <form action="" method="get" accept-charset="utf-8" id="filterForm">
        <div class="container-fluid">
            <div class="form-row align-items-end">

              <div class="form-group col-md-3">
                <label for="trainingDateRange">Training Completion Date</label>
                <input type="text" class="form-control auto-submit" id="trainingDateRange" name="Dttd"
                  autocomplete="off" readonly>
              </div>

              <div class="form-group col-md-3">
                <label for="training_status">Installation Status</label>
                <select id="training_status" name="training_status" class="form-control auto-submit single-select">
                  <option value="0" <?php echo (isset($_GET['training_status']) && $_GET['training_status'] == '0') ? 'selected' : ''; ?>>Pending</option>
                  <option value="1" <?php echo (isset($_GET['training_status']) && $_GET['training_status'] == '1') ? 'selected' : ''; ?>>Completed</option>
                  <option value="2" <?php echo (!isset($_GET['training_status']) || $_GET['training_status'] == '2') ? 'selected' : ''; ?>>All</option>
                </select>
              </div>

              <div class="form-group col-md-3">
                <label for="lastCallDateRange">Last Call Date</label>
                <input type="text" class="form-control auto-submit" id="lastCallDateRange" name="last_call_date"
                  autocomplete="off" readonly>
              </div>

              <div class="form-group col-md-3">
                <label for="followupDateRange">Next Follow-up Date</label>
                <input type="text" class="form-control auto-submit" id="followupDateRange" name="follow_up_date"
                  autocomplete="off" readonly>
              </div>
              <input type="hidden" name="attendance_best" value="<?php echo isset($_GET['attendance_best']) ? $_GET['attendance_best'] : 85 ?>">
              <input type="hidden" name="attendance_best_color" value="<?php echo isset($_GET['attendance_best_color']) ? $_GET['attendance_best_color'] : "#28a745" ?>">
              <input type="hidden" name="attendance_average" value="<?php echo isset($_GET['attendance_average']) ? $_GET['attendance_average'] : 60 ?>">
              <input type="hidden" name="attendance_average_color" value="<?php echo isset($_GET['attendance_average_color']) ? $_GET['attendance_average_color'] : "#ffc107" ?>">
              <input type="hidden" name="attendance_low" value="<?php echo isset($_GET['attendance_low']) ? $_GET['attendance_low'] : 35 ?>">
              <input type="hidden" name="attendance_low_color" value="<?php echo isset($_GET['attendance_low_color']) ? $_GET['attendance_low_color'] : "#dc3545" ?>">
              <?php
              if (isset($_GET['export_type_attendance']) && is_array($_GET['export_type_attendance'])) {
                foreach ($_GET['export_type_attendance'] as $val) {
                  echo '<input type="hidden" name="export_type_attendance[]" value="' . htmlspecialchars($val) . '">' . "\n";
                }
              } else {
              ?>
                <input type="hidden" name="export_type_attendance[]" value="0">
                <input type="hidden" name="export_type_attendance[]" value="1">
                <input type="hidden" name="export_type_attendance[]" value="2">
              <?php
              }
              ?>

              <input type="hidden" name="payroll_best" value="<?php echo isset($_GET['payroll_best']) ? $_GET['payroll_best'] : 85 ?>">
              <input type="hidden" name="payroll_best_color" value="<?php echo isset($_GET['payroll_best_color']) ? $_GET['payroll_best_color'] : "#28a745" ?>">
              <input type="hidden" name="payroll_average" value="<?php echo isset($_GET['payroll_average']) ? $_GET['payroll_average'] : 60 ?>">
              <input type="hidden" name="payroll_average_color" value="<?php echo isset($_GET['payroll_average_color']) ? $_GET['payroll_average_color'] : "#ffc107" ?>">
              <input type="hidden" name="payroll_low" value="<?php echo isset($_GET['payroll_low']) ? $_GET['payroll_low'] : 35 ?>">
              <input type="hidden" name="payroll_low_color" value="<?php echo isset($_GET['payroll_low_color']) ? $_GET['payroll_low_color'] : "#dc3545" ?>">
              <?php
              if (isset($_GET['export_type_payroll']) && is_array($_GET['export_type_payroll'])) {
                foreach ($_GET['export_type_payroll'] as $val) {
                  echo '<input type="hidden" name="export_type_payroll[]" value="' . htmlspecialchars($val) . '">' . "\n";
                }
              } else {
              ?>
                <input type="hidden" name="export_type_payroll[]" value="0">
                <input type="hidden" name="export_type_payroll[]" value="1">
                <input type="hidden" name="export_type_payroll[]" value="2">
              <?php
              }
              ?>

              <input type="hidden" name="tracking_best" value="<?php echo isset($_GET['tracking_best']) ? $_GET['tracking_best'] : 85 ?>">
              <input type="hidden" name="tracking_best_color" value="<?php echo isset($_GET['tracking_best_color']) ? $_GET['tracking_best_color'] : "#28a745" ?>">
              <input type="hidden" name="tracking_average" value="<?php echo isset($_GET['tracking_average']) ? $_GET['tracking_average'] : 60 ?>">
              <input type="hidden" name="tracking_average_color" value="<?php echo isset($_GET['tracking_average_color']) ? $_GET['tracking_average_color'] : "#ffc107" ?>">
              <input type="hidden" name="tracking_low" value="<?php echo isset($_GET['tracking_low']) ? $_GET['tracking_low'] : 35 ?>">
              <input type="hidden" name="tracking_low_color" value="<?php echo isset($_GET['tracking_low_color']) ? $_GET['tracking_low_color'] : "#dc3545" ?>">
              <?php
              if (isset($_GET['export_type_tracking']) && is_array($_GET['export_type_tracking'])) {
                foreach ($_GET['export_type_tracking'] as $val) {
                  echo '<input type="hidden" name="export_type_tracking[]" value="' . htmlspecialchars($val) . '">' . "\n";
                }
              } else {
              ?>
                <input type="hidden" name="export_type_tracking[]" value="0">
                <input type="hidden" name="export_type_tracking[]" value="1">
                <input type="hidden" name="export_type_tracking[]" value="2">
              <?php
              }
              ?>

              <input type="hidden" name="work_best" value="<?php echo isset($_GET['work_best']) ? $_GET['work_best'] : 85 ?>">
              <input type="hidden" name="work_best_color" value="<?php echo isset($_GET['work_best_color']) ? $_GET['work_best_color'] : "#28a745" ?>">
              <input type="hidden" name="work_average" value="<?php echo isset($_GET['work_average']) ? $_GET['work_average'] : 60 ?>">
              <input type="hidden" name="work_average_color" value="<?php echo isset($_GET['work_average_color']) ? $_GET['work_average_color'] : "#ffc107" ?>">
              <input type="hidden" name="work_low" value="<?php echo isset($_GET['work_low']) ? $_GET['work_low'] : 35 ?>">
              <input type="hidden" name="work_low_color" value="<?php echo isset($_GET['work_low_color']) ? $_GET['work_low_color'] : "#dc3545" ?>">
              <?php
              if (isset($_GET['export_type_work_report']) && is_array($_GET['export_type_work_report'])) {
                foreach ($_GET['export_type_work_report'] as $val) {
                  echo '<input type="hidden" name="export_type_work_report[]" value="' . htmlspecialchars($val) . '">' . "\n";
                }
              } else {
                ?>
                <input type="hidden" name="export_type_work_report[]" value="0">
                <input type="hidden" name="export_type_work_report[]" value="1">
                <input type="hidden" name="export_type_work_report[]" value="2">
                <?php
              }
              ?>
              <input type="hidden" name="export_month" value="<?php echo isset($_GET['export_month']) ? $_GET['export_month'] : 0 ?>">
            </div>
        </div>

        <div class="col-sm-9 d-none">
          <form action="" class="branchDeptFilter">
            <div class="row ">
              <div class="col-md-2 col-6 form-group">
                <input type="text" class="form-control" autocomplete="off" id="autoclose-datepickerFrom" name="from"
                  value="<?php echo htmlspecialchars($from); ?>">
              </div>
              <div class="col-md-2 col-6 form-group">
                <input type="text" class="form-control" autocomplete="off" id="autoclose-datepickerTo" name="toDate"
                  value="<?php echo htmlspecialchars($toDate); ?>">
              </div>
              <div class="col-md-3 form-group">
                <input class="btn btn-success btn-sm " type="submit" name="getReport" value="Get">
              </div>
            </div>
          </form>
        </div>
    </div>
    <?php
    $trainingDateFilter = '';
    $lastCallDateFilter = '';
    $followupDateFilter = '';

    $trainingStatusFilter = "";

    if (isset($_GET['training_status']) && ($_GET['training_status'] === '0' || $_GET['training_status'] == '1')) {
      $type = $_GET['training_status'];
      $trainingStatusFilter = " AND society_master.training_status = '$type'";
    } else if (isset($_GET['training_status']) || ($_GET['training_status'] === '2')) {
      $trainingStatusFilter = "";
    } else {
      $trainingStatusFilter = "";
    }

    if (isset($_GET['Dttd']) && !empty($_GET['Dttd']) && $_GET['Dttd'] != "0000-00-00 00:00:00") {
      $training_date_range = explode(" - ", $_GET['Dttd']);

      $date_from_training_date = date("Y-m-d", strtotime(trim($training_date_range[0])));
      $date_to_training_date = date("Y-m-d", strtotime(trim($training_date_range[1])));

      $trainingDateFilter = " AND DATE(society_master.training_completion_date) BETWEEN '$date_from_training_date' AND '$date_to_training_date'";
    } else {
      $trainingDateFilter = "";
    }


    if (isset($_GET['last_call_date']) && !empty($_GET['last_call_date'])) {
      $lastCallDateRange = $_GET['last_call_date'];
      $dates = explode(" - ", $lastCallDateRange);
      if (count($dates) == 2) {
        $date_from_call_date = date("Y-m-d", strtotime(trim($dates[0])));
        $date_to_call_date = date("Y-m-d", strtotime(trim($dates[1])));

        $lastCallDateFilter = " AND society_master.last_call_date BETWEEN '$date_from_call_date' AND '$date_to_call_date'";
      }
    } else {
      $lastCallDateFilter = "";
    }

    if (isset($_GET['follow_up_date']) && !empty($_GET['follow_up_date'])) {
      $follow_up_date = $_GET['follow_up_date'];

      $dates = explode(" - ", $follow_up_date);

      if (count($dates) === 2) {
        $date_from_followup_date = trim($dates[0]);
        $date_to_followup_date = trim($dates[1]);

        if (!empty($date_from_followup_date) && !empty($date_to_followup_date)) {
          $date_from_followup_date = date("Y-m-d", strtotime($date_from_followup_date));
          $date_to_followup_date = date("Y-m-d", strtotime($date_to_followup_date));

          $followupDateFilter = " AND society_master.follow_up_date BETWEEN '$date_from_followup_date' AND '$date_to_followup_date'";
        }
      }
    } else {
      $followupDateFilter = "";
    }
    ?>

    <div class="row mt-2">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <div class="btn-group float-sm-right">
                <button data-toggle="modal" data-target="#settingsAdd" class="btn btn-sm btn-primary">
                  <i class="fa fa-cog"></i>
                </button>

              </div>
              <table id="reportTableReorderable" class="table table-bordered" data-use-server="1">
                <thead>
                  <tr>
                    <!-- <th>User limit <i class="fa fa-info-circle text-muted ml-1 not-export" data-toggle="tooltip" data-placement="top" title=""></i></th> -->
                    <th>#</th>
                    <th>Id</th>
                    <th>engagement call exe.</th>
                    <th>support exe.</th>
                    <th>Installation Status</th>
                    <th>Implementation Completion Date</th>
                    <th>Implementation Executive</th>
                    <th>Implementation Executive Remarks</th>
                    <th>Company Name</th>
                    <th>Company Type</th>
                    <th>City</th>
                    <th>Campaign Region</th>
                    <th>Company Address</th>
                    <th>Rise Event</th>
                    <th>CRM</th>
                    <th>Lead Source</th>
                    <th>SPOC</th>
                    <th>SPOC Designation</th>
                    <th>SPOC Mobile</th>
                    <th>Leader Details</th>
                    <th>Reference</th>
                    <th>User limit</th>
                    <th>Tracking limit</th>
                    <th>Total Users</th>
                    <th>Logged in</th>
                    <th>Free / Paid</th>
                    <th>Subsciption Fees</th>
                    <th>Company Creation Date</th>
                    <th>Expiry Date</th>
                    <th>Plan Status</th>
                    <th>Last Call Date</th>
                    <th>Last Call Remarks</th>
                    <th>Next Followup Date</th>

                    <th>Total Attendance Count</th>
                    <th>Std Attendance Count</th>
                    <th>Attendance Ratio (<?= date("F") ?>) <i class="fa fa-info-circle text-muted ml-1" data-toggle="tooltip" data-placement="top" title="(Attendance Count for month ÷ Std Attendance Count) × 100. Std Attendance Count = (employee_registration_limit or total_users) × 26"></i></th>
                    <th>Attendance Count (<?= date("F") ?>)</th>
                    <th>Attendance Ratio (<?= date("F", strtotime("first day of last month")) ?>) <i class="fa fa-info-circle text-muted ml-1" data-toggle="tooltip" data-placement="top" title="(Attendance Count for month ÷ Std Attendance Count) × 100. Std Attendance Count = (employee_registration_limit or total_users) × 26"></i></th>
                    <th>Attendance Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Attendance Ratio (<?= date("F", strtotime("first day of -2 month")) ?>) <i class="fa fa-info-circle text-muted ml-1" data-toggle="tooltip" data-placement="top" title="(Attendance Count for month ÷ Std Attendance Count) × 100. Std Attendance Count = (employee_registration_limit or total_users) × 26"></i></th>
                    <th>Attendance Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>

                    <th>Std payroll Count</th>
                    <th>Payroll Ratio (<?= date("F") ?>) <i class="fa fa-info-circle text-muted ml-1" data-toggle="tooltip" data-placement="top" title="(Payroll Count for month ÷ Std Payroll Count) × 100. Std Payroll Count = total_users"></i></th>
                    <th>Payroll Count (<?= date("F") ?>)</th>
                    <th>Payroll Ratio (<?= date("F", strtotime("first day of last month")) ?>) <i class="fa fa-info-circle text-muted ml-1" data-toggle="tooltip" data-placement="top" title="(Payroll Count for month ÷ Std Payroll Count) × 100. Std Payroll Count = total_users"></i></th>
                    <th>Payroll Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Payroll Ratio (<?= date("F", strtotime("first day of -2 month")) ?>) <i class="fa fa-info-circle text-muted ml-1" data-toggle="tooltip" data-placement="top" title="(Payroll Count for month ÷ Std Payroll Count) × 100. Std Payroll Count = total_users"></i></th>
                    <th>Payroll Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>

                    <th>Std tracking users Count</th>
                    <th>Tracking Users Ratio (<?= date("F") ?>) <i class="fa fa-info-circle text-muted ml-1" data-toggle="tooltip" data-placement="top" title="(Tracking Users Count for month ÷ Tracking limit) × 100. Tracking limit = employee_tracking_limit"></i></th>
                    <th>Tracking Users Count (<?= date("F") ?>)</th>
                    <th>Tracking Users Ratio (<?= date("F", strtotime("first day of last month")) ?>) <i class="fa fa-info-circle text-muted ml-1" data-toggle="tooltip" data-placement="top" title="(Tracking Users Count for month ÷ Tracking limit) × 100. Tracking limit = employee_tracking_limit"></i></th>
                    <th>Tracking Users Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Tracking Users Ratio (<?= date("F", strtotime("first day of -2 month")) ?>) <i class="fa fa-info-circle text-muted ml-1" data-toggle="tooltip" data-placement="top" title="(Tracking Users Count for month ÷ Tracking limit) × 100. Tracking limit = employee_tracking_limit"></i></th>
                    <th>Tracking Users Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>
                    <th>Std work report Count</th>
                    <th>Work Report Ratio (<?= date("F") ?>) <i class="fa fa-info-circle text-muted ml-1" data-toggle="tooltip" data-placement="top" title="(Work Report Count for month ÷ Std Work Report Count) × 100. Std Work Report Count = (employee_registration_limit or total_users) × 26"></i></th>
                    <th>Work Report Count (<?= date("F") ?>)</th>
                    <th>Work Report Ratio (<?= date("F", strtotime("first day of last month")) ?>) <i class="fa fa-info-circle text-muted ml-1" data-toggle="tooltip" data-placement="top" title="(Work Report Count for month ÷ Std Work Report Count) × 100. Std Work Report Count = (employee_registration_limit or total_users) × 26"></i></th>
                    <th>Work Report Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Work Report Ratio (<?= date("F", strtotime("first day of -2 month")) ?>) <i class="fa fa-info-circle text-muted ml-1" data-toggle="tooltip" data-placement="top" title="(Work Report Count for month ÷ Std Work Report Count) × 100. Std Work Report Count = (employee_registration_limit or total_users) × 26"></i></th>
                    <th>Work Report Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>
                    <th>Std Payroll Ratio <i class="fa fa-info-circle text-muted ml-1" data-toggle="tooltip" data-placement="top" title="(Total Salary Slips ÷ Total Users) × 100"></i></th>
                    <th>Current Payroll Count</th>
                    <th>Tracking</th>
                    <th>Work Report</th>
                    <th>Order Count</th>
                    <th>Circular Usage</th>
                    <th>Assets</th>
                    <th>Expense Count</th>
                    <th>Expenses</th>
                    <th>Leave Count</th>
                    <th>Leave Management</th>
                    <th>Admin count</th>
                    <th>Admin View Access</th>
                    <th>Advances & Loan Payment</th>
                    <th>Tax Exemption</th>
                    <th>Task Management</th>
                    <th>Visitors</th>
                    <th>Bring Your Buddy</th>
                    <th>Chat Count</th>
                    <th>Timeline</th>
                    <th>Events</th>
                    <th>Gallery</th>
                    <th>Penalty</th>
                    <th>Vendors</th>
                    <th>Circular Count (<?= date("F") ?>)</th>
                    <th>Circular Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Circular Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>
                    <th>Expense Count (<?= date("F") ?>)</th>
                    <th>Expense Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Expense Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>
                    <th>total google visit count</th>
                    <th>Google Visit Count (<?= date("F") ?>)</th>
                    <th>Google Visit Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Google Visit Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>
                    <th>total documents count</th>
                    <th>Assigned Tasks Count (<?= date("F") ?>)</th>
                    <th>Assigned Tasks Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Assigned Tasks Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>
                    <th>Visitors Count (<?= date("F") ?>)</th>
                    <th>Visitors Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Visitors Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>
                    <th>Work From Home(WFH) Count (<?= date("F") ?>)</th>
                    <th>Work From Home(WFH) Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Work From Home(WFH) Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>
                    <th>total current opening count </th>
                    <th>Timeline Count (<?= date("F") ?>)</th>
                    <th>Timeline Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Timeline Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>
                    <th>Events Count (<?= date("F") ?>)</th>
                    <th>Events Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Events Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>
                    <th>Gallery Count (<?= date("F") ?>)</th>
                    <th>Gallery Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Gallery Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>
                    <th>Penalty Count (<?= date("F") ?>)</th>
                    <th>Penalty Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Penalty Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>
                    <th>total vendors registered count</th>
                    <th>Sales Order Count (<?= date("F") ?>)</th>
                    <th>Sales Order Count (<?= date("F", strtotime("first day of last month")) ?>)</th>
                    <th>Sales Order Count (<?= date("F", strtotime("first day of -2 month")) ?>)</th>

                    <th>Google Reviews</th>
                    <th>Testimonials</th>
                    <th>References Generated</th>
                    <th>Company URL</th>
                    <th>Last Sync Date</th>
                    <th>Refund Status</th>
                    <th>Refund Date</th>
                    <th>Refund Amount</th>
                    <th>Refund Coordinator</th>
                    <th>Refund Description</th>
                    <th>Feedback Form Submitted</th>
                    <th>Feedback Form Submitted Date</th>
                    <th>Handover</th>
                    <th>Handover Date</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th class="no-search-box"></th>
                    <th></th>
                    <th></th>
                    <th></th>

                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                </tfoot>
                <tbody>
                  <!-- Data will be loaded via AJAX server-side processing -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div><!-- End Row-->
  </div>
  <div class="modal fade" id="settingsAdd">
    <div class="modal-dialog modal-lg">
      <div class="modal-content border-primary">
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white">Setting Data</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <form id="settingsForm" method="GET">
            <div class="container">
              <div class="form-group row align-items-center">
                <label class="col-form-label col-sm-1">Month</label>

                <div class="col-sm-4">
                  <select class="form-control single-select" name="export_month">
                    <?php
                    $selectedTypes = isset($_GET['export_month']) ? (array) $_GET['export_month'] : ['0', '1', '2'];
                    ?>
                    <option value="0" <?php echo in_array('0', $selectedTypes) ? 'selected' : '' ?>><?= date("F") ?></option>
                    <option value="1" <?php echo in_array('1', $selectedTypes) ? 'selected' : '' ?>><?= date("F", strtotime("first day of last month")) ?></option>
                    <option value="2" <?php echo in_array('2', $selectedTypes) ? 'selected' : '' ?>><?= date("F", strtotime("first day of -2 month")) ?></option>
                  </select>
                </div>
              </div>
              <!-- Attendance -->
              <h6 class="mb-3">Attendance</h6>
              <div class="form-group row align-items-center">

                <!-- Best -->
                <label class="col-form-label col-sm-1">Best(↑)</label>
                <div class="col-sm-2">
                  <input type="text" name="attendance_best" class="form-control onlyNumber"
                    value="<?php echo isset($_GET['attendance_best']) ? $_GET['attendance_best'] : 85 ?>" min="0" max="100"
                    maxlength="3" required>
                </div>
                <div class="col-sm-1">
                  <input type="color" name="attendance_best_color" class="form-control"
                    value="<?php echo isset($_GET['attendance_best_color']) ? $_GET['attendance_best_color'] : '#28a745' ?>">
                </div>

                <!-- Average -->
                <label class="col-form-label col-sm-1">Avg</label>
                <div class="col-sm-2">
                  <input type="text" name="attendance_average" class="form-control onlyNumber"
                    value="<?php echo isset($_GET['attendance_average']) ? $_GET['attendance_average'] : 60 ?>" min="0"
                    max="100" maxlength="3" required>
                </div>
                <div class="col-sm-1">
                  <input type="color" name="attendance_average_color" class="form-control"
                    value="<?php echo isset($_GET['attendance_average_color']) ? $_GET['attendance_average_color'] : '#ffc107' ?>">
                </div>

                <!-- Low -->
                <label class="col-form-label col-sm-1">Low(↓)</label>
                <div class="col-sm-2">
                  <input type="text" name="attendance_low" class="form-control onlyNumber"
                    value="<?php echo isset($_GET['attendance_low']) ? $_GET['attendance_low'] : 35 ?>" min="0" max="100"
                    maxlength="3" required>
                </div>
                <div class="col-sm-1">
                  <input type="color" name="attendance_low_color" class="form-control"
                    value="<?php echo isset($_GET['attendance_low_color']) ? $_GET['attendance_low_color'] : '#dc3545' ?>">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Export —</label>
                <div class="col-sm-4">
                  <select class="form-control multiple-select" name="export_type_attendance[]" multiple="multiple">
                    <?php
                    $selectedTypes = isset($_GET['export_type_attendance']) ? (array) $_GET['export_type_attendance'] : ['0', '1', '2'];
                    ?>
                    <option value="0" <?php echo in_array('0', $selectedTypes) ? 'selected' : '' ?>>BEST</option>
                    <option value="1" <?php echo in_array('1', $selectedTypes) ? 'selected' : '' ?>>AVERAGE</option>
                    <option value="2" <?php echo in_array('2', $selectedTypes) ? 'selected' : '' ?>>LOW</option>
                  </select>
                </div>
              </div>

              <!-- Payroll -->
              <h6 class="mt-4 mb-3">Payroll</h6>
              <div class="form-group row align-items-center">
                <!-- Best -->
                <label class="col-form-label col-sm-1">Best(↑)</label>
                <div class="col-sm-2">
                  <input type="text" name="payroll_best" class="form-control onlyNumber"
                    value="<?php echo isset($_GET['payroll_best']) ? $_GET['payroll_best'] : 85 ?>" min="0" max="100"
                    maxlength="3" required>
                </div>
                <div class="col-sm-1">
                  <input type="color" name="payroll_best_color" class="form-control"
                    value="<?php echo isset($_GET['payroll_best_color']) ? $_GET['payroll_best_color'] : '#28a745' ?>">
                </div>

                <!-- Average -->
                <label class="col-form-label col-sm-1">Avg</label>
                <div class="col-sm-2">
                  <input type="text" name="payroll_average" class="form-control onlyNumber"
                    value="<?php echo isset($_GET['payroll_average']) ? $_GET['payroll_average'] : 60 ?>" min="0" max="100"
                    maxlength="3" required>
                </div>
                <div class="col-sm-1">
                  <input type="color" name="payroll_average_color" class="form-control"
                    value="<?php echo isset($_GET['payroll_average_color']) ? $_GET['payroll_average_color'] : '#ffc107' ?>">
                </div>

                <!-- Low -->
                <label class="col-form-label col-sm-1">Low(↓)</label>
                <div class="col-sm-2">
                  <input type="text" name="payroll_low" class="form-control onlyNumber"
                    value="<?php echo isset($_GET['payroll_low']) ? $_GET['payroll_low'] : 35 ?>" min="0" max="100"
                    maxlength="3" required>
                </div>
                <div class="col-sm-1">
                  <input type="color" name="payroll_low_color" class="form-control"
                    value="<?php echo isset($_GET['payroll_low_color']) ? $_GET['payroll_low_color'] : '#dc3545' ?>">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Export —</label>
                <div class="col-sm-4">
                  <select class="form-control multiple-select" name="export_type_payroll[]" multiple="multiple">
                    <?php
                    // Handle GET input safely as an array
                    $selectedTypes = isset($_GET['export_type_payroll']) ? (array) $_GET['export_type_payroll'] : ['0', '1', '2'];
                    ?>
                    <option value="0" <?php echo in_array('0', $selectedTypes) ? 'selected' : '' ?>>BEST</option>
                    <option value="1" <?php echo in_array('1', $selectedTypes) ? 'selected' : '' ?>>AVERAGE</option>
                    <option value="2" <?php echo in_array('2', $selectedTypes) ? 'selected' : '' ?>>LOW</option>
                  </select>
                </div>
              </div>

              <!-- Tracking -->
              <h6 class="mt-4 mb-3">Tracking</h6>
              <div class="form-group row align-items-center">
                <!-- Best -->
                <label class="col-form-label col-sm-1">Best(↑)</label>
                <div class="col-sm-2">
                  <input type="text" name="tracking_best" class="form-control onlyNumber"
                    value="<?php echo isset($_GET['tracking_best']) ? $_GET['tracking_best'] : 85 ?>" min="0" max="100"
                    maxlength="3" required>
                </div>
                <div class="col-sm-1">
                  <input type="color" name="tracking_best_color" class="form-control"
                    value="<?php echo isset($_GET['tracking_best_color']) ? $_GET['tracking_best_color'] : '#28a745' ?>">
                </div>

                <!-- Average -->
                <label class="col-form-label col-sm-1">Avg</label>
                <div class="col-sm-2">
                  <input type="text" name="tracking_average" class="form-control onlyNumber"
                    value="<?php echo isset($_GET['tracking_average']) ? $_GET['tracking_average'] : 60 ?>" min="0" max="100"
                    maxlength="3" required>
                </div>
                <div class="col-sm-1">
                  <input type="color" name="tracking_average_color" class="form-control"
                    value="<?php echo isset($_GET['tracking_average_color']) ? $_GET['tracking_average_color'] : '#ffc107' ?>">
                </div>

                <!-- Low -->
                <label class="col-form-label col-sm-1">Low(↓)</label>
                <div class="col-sm-2">
                  <input type="text" name="tracking_low" class="form-control onlyNumber"
                    value="<?php echo isset($_GET['tracking_low']) ? $_GET['tracking_low'] : 35 ?>" min="0" max="100"
                    maxlength="3" required>
                </div>
                <div class="col-sm-1">
                  <input type="color" name="tracking_low_color" class="form-control"
                    value="<?php echo isset($_GET['tracking_low_color']) ? $_GET['tracking_low_color'] : '#dc3545' ?>">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Export —</label>
                <div class="col-sm-4">
                  <select class="form-control multiple-select" name="export_type_tracking[]" multiple="multiple">
                    <?php
                    $selectedTypes = isset($_GET['export_type_tracking']) ? (array) $_GET['export_type_tracking'] : ['0', '1', '2'];
                    ?>
                    <option value="0" <?php echo in_array('0', $selectedTypes) ? 'selected' : '' ?>>BEST</option>
                    <option value="1" <?php echo in_array('1', $selectedTypes) ? 'selected' : '' ?>>AVERAGE</option>
                    <option value="2" <?php echo in_array('2', $selectedTypes) ? 'selected' : '' ?>>LOW</option>
                  </select>
                </div>
              </div>

              <!-- Work Report -->
              <h6 class="mt-4 mb-3">Work Report</h6>
              <div class="form-group row align-items-center">
                <!-- Best -->
                <label class="col-form-label col-sm-1">Best(↑)</label>
                <div class="col-sm-2">
                  <input type="text" name="work_best" class="form-control onlyNumber"
                    value="<?php echo isset($_GET['work_best']) ? $_GET['work_best'] : 85 ?>" min="0" max="100" maxlength="3"
                    required>
                </div>
                <div class="col-sm-1">
                  <input type="color" name="work_best_color" class="form-control"
                    value="<?php echo isset($_GET['work_best_color']) ? $_GET['work_best_color'] : '#28a745' ?>">
                </div>

                <!-- Average -->
                <label class="col-form-label col-sm-1">Avg</label>
                <div class="col-sm-2">
                  <input type="text" name="work_average" class="form-control onlyNumber"
                    value="<?php echo isset($_GET['work_average']) ? $_GET['work_average'] : 60 ?>" min="0" max="100"
                    maxlength="3" required>
                </div>
                <div class="col-sm-1">
                  <input type="color" name="work_average_color" class="form-control"
                    value="<?php echo isset($_GET['work_average_color']) ? $_GET['work_average_color'] : '#ffc107' ?>">
                </div>

                <!-- Low -->
                <label class="col-form-label col-sm-1">Low(↓)</label>
                <div class="col-sm-2">
                  <input type="text" name="work_low" class="form-control onlyNumber"
                    value="<?php echo isset($_GET['work_low']) ? $_GET['work_low'] : 35 ?>" min="0" max="100" maxlength="3"
                    required>
                </div>
                <div class="col-sm-1">
                  <input type="color" name="work_low_color" class="form-control"
                    value="<?php echo isset($_GET['work_low_color']) ? $_GET['work_low_color'] : '#dc3545' ?>">
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Export —</label>
                <div class="col-sm-4">
                  <select class="form-control multiple-select" name="export_type_work_report[]" multiple="multiple">
                    <?php
                    $selectedTypes = isset($_GET['export_type_work_report']) ? (array) $_GET['export_type_work_report'] : ['0', '1', '2'];
                    ?>
                    <option value="0" <?php echo in_array('0', $selectedTypes) ? 'selected' : '' ?>>BEST</option>
                    <option value="1" <?php echo in_array('1', $selectedTypes) ? 'selected' : '' ?>>AVERAGE</option>
                    <option value="2" <?php echo in_array('2', $selectedTypes) ? 'selected' : '' ?>>LOW</option>
                  </select>
                </div>
              </div>
              <!-- Submit -->
              <div class="form-footer text-center mt-4">
                <button type="submit" class="btn btn-primary">
                  <i class="fa fa-check-square-o"></i> Save
                </button>
              </div>
            </div>
            <input type="hidden" name="Dttd" value="<?php echo $_GET['Dttd'] ?? ''; ?>">
            <input type="hidden" name="training_status" value="<?php echo $_GET['training_status'] ?? '2'; ?>">
            <input type="hidden" name="last_call_date" value="<?php echo $_GET['last_call_date'] ?? ''; ?>">
            <input type="hidden" name="follow_up_date" value="<?php echo $_GET['follow_up_date'] ?? ''; ?>">
          </form>
        </div>

      </div>
    </div>
  </div>
  <div class="modal fade" id="changeEngagementNameModel">
    <div class="modal-dialog modal-md">
      <div class="modal-content border-primary">
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white">Change Engagement Call Executive Name</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="card-body">
            <form id="" method="POST" action="controller/reportController.php">
              <div class="row">
                <div class="col-md-12">
                  <label for="engagement_call_executive_name" class="col-form-label">Engagement Call Executive
                    Name</label>
                  <select name="engagement_call_executive_name" id="engagement_call_executive_name"
                    class="form-control single-select" required>
                    <option value="">-- Select --</option>
                    <?php
                    $trainers = $d->select("bms_admin_master", "(role_id != 1) AND active_status='0'");
                    while ($row2 = mysqli_fetch_assoc($trainers)) {
                    ?>
                      <option value="<?php echo $row2['admin_name']; ?>">
                        <?php echo $row2['admin_name']; ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-footer text-center mt-3">
                <input type="hidden" name="changeEngagementName" value="changeEngagementName">
                <input type="hidden" name="society_id" id="society_id_edit">
                <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check-square-o"></i>
                  Update</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="changeSupportNameModel">
  <div class="modal-dialog modal-md">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Change Support Executive Name</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="card-body">
          <form id="" method="POST" action="controller/reportController.php">
            <div class="row">
              <div class="col-md-12">
                <label for="support_name" class="col-form-label">Support Executive Name</label>
                <select name="support_name" id="support_name" class="form-control single-select" required>
                  <option value="">-- Select --</option>
                  <?php
                  $trainers = $d->select("bms_admin_master", "(role_id != 1) AND active_status='0'");
                  while ($row2 = mysqli_fetch_assoc($trainers)) {
                  ?>
                    <option value="<?php echo $row2['admin_name']; ?>">
                      <?php echo $row2['admin_name']; ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-footer text-center mt-3">
              <input type="hidden" name="changeSupportName" value="changeSupportName">
              <input type="hidden" name="society_id" id="society_id_edit_support">
              <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check-square-o"></i> Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<script src="assets/js/jquery.min.js"></script>
<style>
  /* Make blank editable spans discoverable and clickable */
  .editable-date,
  .editable-remark,
  .editable-followup-date,
  .editable-implementation-remark,
  .editable-spoc,
  .editable-spoc-designation,
  .editable-spoc-mobile {
    cursor: text;
    display: inline-block;
    min-width: 120px; /* ensure a click target even when empty */
  }
  .editable-remark:empty::before,
  .editable-date:empty::before,
  .editable-followup-date:empty::before,
  .editable-implementation-remark:empty::before,
  .editable-spoc:empty::before,
  .editable-spoc-designation:empty::before,
  .editable-spoc-mobile:empty::before {
    content: 'Click to edit';
    color: #6c757d;
    font-style: italic;
  }
  .edit-input { min-width: 160px; }
  .editable-remark-box, .editable-date-box, .editable-followup-date-box,
  .editable-implementation-remark-box, .editable-spoc-box,
  .editable-spoc-designation-box, .editable-spoc-mobile-box { cursor: text; }
  </style>

<script>
  function changeEngagementNameId(engagementName, societyID) {
    $("#engagement_call_executive_name").val(engagementName).trigger('change');
    $("#society_id_edit").val(societyID);
  }

  function changeSupportNameId(supportName, societyID) {
    $("#support_name").val(supportName).trigger('change');
    $("#society_id_edit_support").val(societyID);
  }

  var reportTable;

  $(function() {
    moment.locale('en');

    function initDateRangePicker(selector, defaultStart, defaultEnd) {
      $(selector).daterangepicker({
        startDate: defaultStart,
        endDate: defaultEnd,
        autoUpdateInput: false, // <-- Prevents automatic input update
        locale: {
          format: 'MMMM DD, YYYY',
          applyLabel: "Apply",
          cancelLabel: "Cancel",
          daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
          monthNames: [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
          ],
          firstDay: 0
        },
        maxDate: new Date(),
        maxSpan: {
          days: 31
        },
        ranges: {
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
      });

      // When Apply is clicked
      $(selector).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MMMM DD, YYYY') + ' - ' + picker.endDate.format('MMMM DD, YYYY'));
        if (reportTable) {
          reportTable.ajax.reload();
        } else {
          $('#filterForm').submit();
        }
      });

      // When Cancel is clicked
      $(selector).on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        if (reportTable) {
          reportTable.ajax.reload();
        } else {
          $('#filterForm').submit();
        }
      });
    }

    function parseDateRange(dateRangeStr) {
      if (!dateRangeStr) return null;
      const parts = dateRangeStr.split(' - ');
      if (parts.length !== 2) return null;
      const start = moment(parts[0], 'MMMM DD, YYYY');
      const end = moment(parts[1], 'MMMM DD, YYYY');
      if (!start.isValid() || !end.isValid()) return null;
      return {
        start,
        end
      };
    }

    const trainingDateRangeStr = "<?php echo isset($_GET['Dttd']) ? addslashes($_GET['Dttd']) : ''; ?>";
    const lastCallDateRangeStr = "<?php echo isset($_GET['last_call_date']) ? addslashes($_GET['last_call_date']) : ''; ?>";
    const followUpDateRangeStr = "<?php echo isset($_GET['follow_up_date']) ? addslashes($_GET['follow_up_date']) : ''; ?>";

    const trainingRange = parseDateRange(trainingDateRangeStr) || {
      start: moment().startOf('month'),
      end: moment().endOf('month')
    };
    const lastCallRange = parseDateRange(lastCallDateRangeStr) || {
      start: moment().startOf('month'),
      end: moment().endOf('month')
    };
    const followUpRange = parseDateRange(followUpDateRangeStr) || {
      start: moment().startOf('month'),
      end: moment().endOf('month')
    };

    if (trainingDateRangeStr) $('#trainingDateRange').val(trainingDateRangeStr);
    if (lastCallDateRangeStr) $('#lastCallDateRange').val(lastCallDateRangeStr);
    if (followUpDateRangeStr) $('#followupDateRange').val(followUpDateRangeStr);

    initDateRangePicker('#trainingDateRange', trainingRange.start, trainingRange.end);
    initDateRangePicker('#lastCallDateRange', lastCallRange.start, lastCallRange.end);
    initDateRangePicker('#followupDateRange', followUpRange.start, followUpRange.end);

    // Handle form submission to reload table via AJAX instead of page reload
    $('#filterForm').on('submit', function(e) {
      if (reportTable) {
        e.preventDefault();
        reportTable.ajax.reload();
        // Update URL without reloading page
        const url = new URL(window.location);
        const formData = new FormData(this);
        for (const [key, value] of formData.entries()) {
          if (value) {
            url.searchParams.set(key, value);
          } else {
            url.searchParams.delete(key);
          }
        }
        window.history.pushState({}, '', url);
        return false;
      }
    });

    $('.auto-submit').not('input[type=text]').on('change', function() {
      if (reportTable) {
        reportTable.ajax.reload();
      } else {
        $('#filterForm').submit();
      }
    });

    // Initialize DataTable with server-side processing (guard against re-init)
    if ($.fn.DataTable.isDataTable('#reportTableReorderable')) {
      reportTable = $('#reportTableReorderable').DataTable();
    } else {
      reportTable = $('#reportTableReorderable').DataTable({
      processing: true,
      serverSide: true,
      stateSave: true,
      ajax: {
        url: 'ajax/engagementReportTable.php',
        type: 'POST',
        data: function(d) {
          // Pull live values from the filter form
          var formArray = $('#filterForm').serializeArray();
          formArray.forEach(function(item) {
            // Preserve [] in the key so PHP parses arrays properly
            var key = item.name;
            if (key.endsWith('[]')) {
              if (!d[key]) d[key] = [];
              d[key].push(item.value);
            } else {
              d[key] = item.value;
            }
          });
          // Also merge settings form values (thresholds, colors, export types, export_month)
          if ($('#settingsForm').length) {
            var settingsArray = $('#settingsForm').serializeArray();
            var skipKeys = { 'Dttd':1, 'training_status':1, 'last_call_date':1, 'follow_up_date':1 };
            settingsArray.forEach(function(item) {
              var key = item.name;
              if (skipKeys[key]) return; // don't let modal overwrite live filters
              if (key.endsWith('[]')) {
                if (!d[key]) d[key] = [];
                d[key].push(item.value);
              } else {
                d[key] = item.value;
              }
            });
          }
          // Also pass column search values for server-side column filtering
          // (DataTables already includes d.columns[i][search][value])
        }
      },
      colReorder: true,
      lengthChange: true,
      pageLength: 25,
      lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
      buttons: [
        'copy', 'excel', 'pdf', 'csv', 'colvis',
        {
          text: 'Select All Columns',
          action: function (e, dt, node, config) {
            dt.columns().every(function () {
              this.visible(true, false);
            });
            dt.columns.adjust().draw(false);
          }
        },
        {
          text: 'Deselect All Columns',
          action: function (e, dt, node, config) {
            dt.columns().every(function () {
              this.visible(false, false);
            });
            dt.column(0).visible(true, false);
            dt.columns.adjust().draw(false);
          }
        }
      ],
      select: true,
      dom: 'Blfrtip',
      columnDefs: [
        { orderable: false, targets: [2, 3] } // Make action columns non-sortable
      ],
      initComplete: function() {
        // Add column search inputs in the footer
        this.api().columns().every(function () {
          var that = this;
          $('input[type="text"]', this.footer()).on('keyup change', function () {
            if (that.search() !== this.value) {
              that.search(this.value).draw();
            }
          });
        });
      }
    });

    $('#reportTableReorderable_wrapper').prepend(reportTable.buttons().container());

    // Ensure ColReorder is available; if missing, load and activate on this table
    if (!$.fn.dataTable || !$.fn.dataTable.ColReorder) {
      (function(){
        var css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = 'https://cdn.datatables.net/colreorder/1.7.0/css/colReorder.dataTables.min.css';
        document.head.appendChild(css);
        var js = document.createElement('script');
        js.src = 'https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js';
        js.onload = function(){
          if ($.fn.dataTable && $.fn.dataTable.ColReorder) {
            try { new $.fn.dataTable.ColReorder(reportTable); } catch(e) {}
          }
        };
        document.head.appendChild(js);
      })();
    }

    // Intercept settings form submit to update hidden fields and reload table without page refresh
    $('#settingsForm').on('submit', function(e) {
      e.preventDefault();
      var $filterForm = $('#filterForm');
      var settingsArray = $(this).serializeArray();
      settingsArray.forEach(function(item) {
        // Normalize array fields export_type_*[] so we set multiple hidden inputs
        if (item.name.endsWith('[]')) {
          var base = item.name.slice(0, -2);
          // Remove existing hidden inputs for this array
          $filterForm.find('input[name="' + base + '[]"]').remove();
          // Will be re-added below (we need all values, so rebuild after loop)
        }
      });
      // Rebuild arrays and scalars
      var grouped = {};
      settingsArray.forEach(function(item) {
        if (item.name.endsWith('[]')) {
          var base = item.name.slice(0, -2);
          if (!grouped[base]) grouped[base] = [];
          grouped[base].push(item.value);
        } else {
          var $existing = $filterForm.find('input[name="' + item.name + '"]');
          if ($existing.length) $existing.val(item.value); else $filterForm.append('<input type="hidden" name="' + item.name + '" value="' + item.value.replace(/"/g,'&quot;') + '">');
        }
      });
      Object.keys(grouped).forEach(function(base) {
        grouped[base].forEach(function(val) {
          $filterForm.append('<input type="hidden" name="' + base + '[]" value="' + String(val).replace(/"/g,'&quot;') + '">');
        });
      });
      // Close modal and reload data
      $('#settingsAdd').modal('hide');
      if (reportTable) {
        reportTable.ajax.reload();
      }
    });

  // Delegated inline editing handlers for direct update fields
  function showSuccessToast(message) {
    if (window.toastr && toastr.success) {
      toastr.success(message);
    } else if (window.Swal && Swal.fire) {
      Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: message, showConfirmButton: false, timer: 1500 });
    } else {
      console.log('Success:', message);
    }
  }

  function bindInlineEditor(spanSelector, inputType, fieldName) {
    $('body').on('click', spanSelector, function () {
      var $span = $(this);
      if ($span.find('.edit-input').length) return;
      var currentValue = $span.text().trim();
      var societyId = $span.data('id');
      var inputHtml = '';
      if (inputType === 'date') {
        // Expect YYYY-MM-DD in DB; try to coerce any string to YYYY-MM-DD
        var val = currentValue ? moment(currentValue, ['YYYY-MM-DD','MMMM D, YYYY','DD MMM YYYY']).format('YYYY-MM-DD') : '';
        inputHtml = '<input type="text" class="form-control form-control-sm edit-input autoclose-datepicker-date" value="' + (val === 'Invalid date' ? '' : val) + '" autocomplete="off">';
      } else if (inputType === 'tel') {
        inputHtml = '<input type="tel" class="form-control form-control-sm edit-input" value="' + currentValue.replace(/"/g,'&quot;') + '">';
      } else {
        inputHtml = '<input type="text" class="form-control form-control-sm edit-input" value="' + currentValue.replace(/"/g,'&quot;') + '">';
      }
      $span.html(inputHtml);
      var $input = $span.find('.edit-input');
      var focusAt = Date.now();
      function commit() {
        var newValue = $input.val();
        // Ignore accidental immediate blur right after focus
        if (Date.now() - focusAt < 150) { return; }
        // Allow blank to persist as NULL
        if (newValue === currentValue) { $span.text(newValue); return; }
        $.post('ajax/updateEngagementInline.php', { society_id: societyId, field: fieldName, value: newValue })
          .done(function (res) {
            if (res && res.ok) {
              $span.text(newValue);
              showSuccessToast('Updated successfully');
              if (reportTable) reportTable.ajax.reload(null, false);
            } else {
              $span.text(currentValue);
            }
          })
          .fail(function () { $span.text(currentValue); });
      }
      if (inputType === 'date') {
        // Initialize datepicker for date inputs
        $input.datepicker({
          autoclose: true,
          todayHighlight: true,
          format: 'yyyy-mm-dd',
          orientation: 'bottom auto',
          zIndexOffset: 9999
        }).datepicker('show').on('changeDate', function() {
          commit();
        });
        // Also commit on blur after a delay to handle when datepicker closes
        $input.on('blur', function() {
          setTimeout(function() {
            if (!$input.data('datepicker') || !$input.data('datepicker').picker) {
              commit();
            }
          }, 200);
        });
      } else {
        $input.focus();
        $input.on('blur', commit);
      }
      $input.on('keyup', function (e) { if (e.key === 'Enter') commit(); if (e.key === 'Escape') $span.text(currentValue); });
    });
  }

  bindInlineEditor('.editable-date', 'date', 'last_call_date');
  bindInlineEditor('.editable-followup-date', 'date', 'follow_up_date');
  bindInlineEditor('.editable-remark', 'text', 'last_call_remark');
  bindInlineEditor('.editable-implementation-remark', 'text', 'implementation_remark');
  bindInlineEditor('.editable-spoc', 'text', 'last_spoc');
  bindInlineEditor('.editable-spoc-designation', 'text', 'last_spoc_designation');
  bindInlineEditor('.editable-spoc-mobile', 'tel', 'last_spoc_mobile_number');

  // Make entire cell clickable to start editing even if span is blank
  function proxyBoxClick(boxSelector, spanSelector) {
    $('body').on('mousedown', boxSelector, function (e) {
      // Prevent row selection or other handlers from stealing focus
      e.preventDefault();
      e.stopPropagation();
      var $targetSpan = $(this).find(spanSelector);
      if ($targetSpan.length && !$targetSpan.find('.edit-input').length) {
        if (!$(e.target).is('input,select,textarea')) {
          // Defer to next tick so DOM is stable before focusing input
          setTimeout(function(){ $targetSpan.trigger('click'); }, 0);
        }
      }
    });
  }
  proxyBoxClick('.editable-remark-box', '.editable-remark');
  proxyBoxClick('.editable-date-box', '.editable-date');
  proxyBoxClick('.editable-followup-date-box', '.editable-followup-date');
  proxyBoxClick('.editable-implementation-remark-box', '.editable-implementation-remark');
  proxyBoxClick('.editable-spoc-box', '.editable-spoc');
  proxyBoxClick('.editable-spoc-designation-box', '.editable-spoc-designation');
  proxyBoxClick('.editable-spoc-mobile-box', '.editable-spoc-mobile');

  // Make spans focusable and allow Enter key to trigger editing
  var editableSelectors = '.editable-date, .editable-remark, .editable-followup-date, .editable-implementation-remark, .editable-spoc, .editable-spoc-designation, .editable-spoc-mobile';
  $(document).on('focus', editableSelectors, function() { /* noop to allow focus styling if needed */ });
  $(editableSelectors).attr('tabindex', 0);
  $('body').on('keydown', editableSelectors, function(e){ if (e.key === 'Enter') { e.preventDefault(); $(this).trigger('click'); } });
    }
  });
</script>