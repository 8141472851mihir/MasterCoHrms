<?php
$today = date('Y-m-d');
$monthStart = date('Y-m-01');
$startDate = $monthStart;
$endDate = $today;
$rawRange = trim((string)($_GET['date_range_filter'] ?? ''));
if ($rawRange !== '') {
    $rangeParts = explode(' - ', $rawRange, 2);
    if (count($rangeParts) === 2) {
        $parsedStart = strtotime(trim($rangeParts[0]));
        $parsedEnd = strtotime(trim($rangeParts[1]));
        if ($parsedStart && $parsedEnd) {
            $startDate = date('Y-m-d', $parsedStart);
            $endDate = date('Y-m-d', $parsedEnd);
        }
    }
}
$startDate = $d->sanitizeReportFilterDate($startDate, $monthStart);
$endDate = $d->sanitizeReportFilterDate($endDate, $today);
if ($startDate > $today) {
    $startDate = $today;
}
if ($endDate > $today) {
    $endDate = $today;
}
if ($startDate > $endDate) {
    list($startDate, $endDate) = array($endDate, $startDate);
}
$dateRangeLabel = date('F d, Y', strtotime($startDate)) . ' - ' . date('F d, Y', strtotime($endDate));
$rangeStart = $d->escapeSqlString($startDate) . ' 00:00:00';
$rangeEnd = $d->escapeSqlString($endDate) . ' 23:59:59';

$countryIds = array();
foreach ((array)($countryAryAccess ?? array()) as $countryId) {
    $countryId = (int)$countryId;
    if ($countryId > 0) {
        $countryIds[$countryId] = $countryId;
    }
}
$countryIds = implode(',', $countryIds);
$companyCountrySql = $countryAppendQuerySociety ?? '';
$requestCountrySql = $countryAppendQuerySocietySingleReq ?? '';
$ticketCountrySql = '';
if ($countryIds !== '') {
    $ticketCountrySql = " AND (
        (COALESCE(fm.is_whitelabel, 0) = 0 AND EXISTS (
            SELECT 1 FROM society_master country_company
            WHERE country_company.society_id = fm.society_id AND country_company.country_id IN ($countryIds)
        ))
        OR (COALESCE(fm.is_whitelabel, 0) = 1 AND EXISTS (
            SELECT 1 FROM society_master_white_label country_whitelabel
            WHERE country_whitelabel.society_id = fm.society_id
            AND country_whitelabel.project_type = fm.whitelabel_type
            AND country_whitelabel.country_id IN ($countryIds)
        ))
    )";
}

$employees = array();
$employeeQuery = $d->selectRow(
    'admin_id, admin_name',
    'bms_admin_master',
    "active_status='0' AND admin_name IS NOT NULL AND TRIM(admin_name) != ''",
    'ORDER BY admin_name ASC'
);
if ($employeeQuery) {
    while ($employee = mysqli_fetch_assoc($employeeQuery)) {
        $employees[] = $employee;
    }
}

$assignedByName = array();
$assignedQuery = $d->selectRow(
    'TRIM(support_name) AS support_name, COUNT(*) AS cnt',
    'society_master',
    "society_status='0' AND TRIM(support_name) != ''" . $companyCountrySql,
    'GROUP BY TRIM(support_name)'
);
if ($assignedQuery) {
    while ($countRow = mysqli_fetch_assoc($assignedQuery)) {
        $nameKey = strtolower(trim((string)$countRow['support_name']));
        if ($nameKey !== '') {
            $assignedByName[$nameKey] = (int)$countRow['cnt'];
        }
    }
}

$ticketCreatedOn = "COALESCE(
    STR_TO_DATE(fm.feedback_date_time, '%d-%m-%Y %H:%i:%s'),
    STR_TO_DATE(fm.feedback_date_time, '%d-%m-%Y %H:%i')
)";
$adminCounts = array(
    'companies_created' => array(),
    'crm_created' => array(),
    'wa_groups' => array(),
    'renewals' => array(),
    'tickets_raised' => array(),
    'tickets_closed' => array(),
);
$adminCountQueries = array(
    'companies_created' => array($d->selectRow(
        'request_added_by, COUNT(*) AS cnt',
        'society_master_requests',
        "request_society_create_status='1'
            AND society_id_added > 0
            AND request_added_by > 0
            AND created_date BETWEEN '$rangeStart' AND '$rangeEnd'" . $requestCountrySql,
        'GROUP BY request_added_by'
    ), 'request_added_by'),
    'crm_created' => array($d->selectRow(
        'crm_request_master.crm_request_created_by_id, COUNT(*) AS cnt',
        'crm_request_master INNER JOIN society_master ON society_master.society_id = crm_request_master.society_id',
        "crm_request_master.crm_request_created_by_id > 0
            AND crm_request_master.crm_request_created_date BETWEEN '$rangeStart' AND '$rangeEnd'" . $companyCountrySql,
        'GROUP BY crm_request_master.crm_request_created_by_id'
    ), 'crm_request_created_by_id'),
    'wa_groups' => array($d->selectRow(
        'log_master.user_id, COUNT(DISTINCT log_master.society_id) AS cnt',
        'log_master INNER JOIN society_master ON society_master.society_id = log_master.society_id',
        "log_master.user_id > 0
            AND log_master.log_name LIKE 'Whatsapp Group Created for society id %'
            AND DATE(log_master.log_time) BETWEEN '$startDate' AND '$endDate'" . $companyCountrySql,
        'GROUP BY log_master.user_id'
    ), 'user_id'),
    'renewals' => array($d->selectRow(
        'transection_master.plan_change_by_id, COUNT(*) AS cnt',
        'transection_master INNER JOIN society_master ON society_master.society_id = transection_master.society_id',
        "transection_master.plan_change_by_id > 0
            AND transection_master.is_renewal IN ('1', '3', '4')
            AND LOWER(transection_master.payment_status) = 'success'
            AND transection_master.transection_date BETWEEN '$rangeStart' AND '$rangeEnd'" . $companyCountrySql,
        'GROUP BY transection_master.plan_change_by_id'
    ), 'plan_change_by_id'),
    'tickets_raised' => array($d->selectRow(
        'fm.created_by, COUNT(*) AS cnt',
        'feedback_master fm',
        "fm.inquiry_type='0'
            AND fm.created_by > 0
            AND $ticketCreatedOn BETWEEN '$rangeStart' AND '$rangeEnd'" . $ticketCountrySql,
        'GROUP BY fm.created_by'
    ), 'created_by'),
    'tickets_closed' => array($d->selectRow(
        'fl.feedback_added_by, COUNT(DISTINCT fm.feedback_id) AS cnt',
        "feedback_master fm
            INNER JOIN feedback_log_master fl ON fl.feedback_id = fm.feedback_id
            AND fl.feedback_log LIKE 'Ticket Resolved%'",
        "fm.inquiry_type='0'
            AND fm.feedback_status='2'
            AND fl.feedback_added_by > 0
            AND fm.feedback_solve_time BETWEEN '$rangeStart' AND '$rangeEnd'" . $ticketCountrySql,
        'GROUP BY fl.feedback_added_by'
    ), 'feedback_added_by'),
);
foreach ($adminCountQueries as $countName => $countQuery) {
    if (!$countQuery[0]) {
        continue;
    }
    while ($countRow = mysqli_fetch_assoc($countQuery[0])) {
        $countAdminId = (int)($countRow[$countQuery[1]] ?? 0);
        if ($countAdminId > 0) {
            $adminCounts[$countName][(string)$countAdminId] = (int)$countRow['cnt'];
        }
    }
}

$columns = array(
    'companies_assigned' => 'Companies Assigned',
    'companies_created' => 'HRMS Created',
    'crm_created' => 'CRM Created',
    'wa_groups' => 'WA Groups Created',
    'renewals' => 'Extension/Renewals',
    'tickets_raised' => 'Tickets Raised',
    'tickets_closed' => 'Tickets Closed',
);
$reportRows = array();
$totals = array_fill_keys(array_keys($columns), 0);
foreach ($employees as $employee) {
    $adminId = (string)(int)$employee['admin_id'];
    $employeeName = strtolower(trim((string)$employee['admin_name']));
    $row = array(
        'employee_name' => $employee['admin_name'],
        'companies_assigned' => isset($assignedByName[$employeeName]) ? $assignedByName[$employeeName] : 0,
    );
    foreach ($adminCounts as $countName => $counts) {
        $row[$countName] = isset($counts[$adminId]) ? $counts[$adminId] : 0;
    }
    foreach ($columns as $column => $label) {
        $totals[$column] += $row[$column];
    }
    $reportRows[] = $row;
}
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-4">
        <h4 class="page-title">Executive Centric Support Report</h4>
      </div>
      <div class="col-lg-4 col-md-6">
        <form id="executiveReportFilter" action="" method="get">
          <input type="text" class="form-control" id="date_range_filter" name="date_range_filter" autocomplete="off" readonly value="<?php echo htmlspecialchars($dateRangeLabel, ENT_QUOTES, 'UTF-8'); ?>">
        </form>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="executiveSupportReport" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Employee</th>
                    <?php foreach ($columns as $label) { ?>
                      <th><?php echo $label; ?></th>
                    <?php } ?>
                  </tr>
                </thead>
                <tbody>
                  <?php $serial = 1; ?>
                  <?php foreach ($reportRows as $row) { ?>
                    <tr>
                      <td><?php echo $serial++; ?></td>
                      <td><?php echo htmlspecialchars($row['employee_name']); ?></td>
                      <?php foreach ($columns as $column => $label) { ?>
                        <td><?php echo (int)$row[$column]; ?></td>
                      <?php } ?>
                    </tr>
                  <?php } ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Total</th>
                    <?php foreach ($columns as $column => $label) { ?>
                      <th><?php echo (int)$totals[$column]; ?></th>
                    <?php } ?>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  window.addEventListener('load', function () {
    var $ = window.jQuery;
    if (!$) {
      return;
    }

    if ($.fn.daterangepicker && window.moment) {
      var $input = $('#date_range_filter');
      var dates = ($input.val() || '').split(' - ');
      var startDate = moment().startOf('month');
      var endDate = moment();
      if (dates.length === 2) {
        var parsedStart = moment(dates[0], 'MMMM DD, YYYY');
        var parsedEnd = moment(dates[1], 'MMMM DD, YYYY');
        if (parsedStart.isValid()) {
          startDate = parsedStart;
        }
        if (parsedEnd.isValid()) {
          endDate = parsedEnd;
        }
      }
      if (startDate.isAfter(moment(), 'day')) {
        startDate = moment();
      }
      if (endDate.isAfter(moment(), 'day')) {
        endDate = moment();
      }

      $input.daterangepicker({
        startDate: startDate,
        endDate: endDate,
        maxDate: moment(),
        opens: 'left',
        autoUpdateInput: false,
        alwaysShowCalendars: true,
        locale: {
          format: 'MMMM DD, YYYY',
          applyLabel: 'Apply',
          cancelLabel: 'Cancel'
        },
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'This Month': [moment().startOf('month'), moment()],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()]
        }
      });
      $input.on('apply.daterangepicker', function (event, picker) {
        var label = picker.startDate.format('MMMM DD, YYYY') + ' - ' + picker.endDate.format('MMMM DD, YYYY');
        $(this).val(label);
        $('#executiveReportFilter').trigger('submit');
      });
    }

    if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#executiveSupportReport')) {
      $('#executiveSupportReport').DataTable({
        pageLength: 50,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, 'All']],
        order: [[1, 'asc']],
        columnDefs: [
          { orderable: false, targets: 0 },
          { type: 'num', targets: [2, 3, 4, 5, 6, 7, 8] }
        ],
        dom: 'Blfrtip',
        buttons: [
          { extend: 'csv', exportOptions: { columns: ':visible' } },
          { extend: 'excelHtml5', exportOptions: { columns: ':visible' } },
          { extend: 'pdfHtml5', orientation: 'landscape', pageSize: 'A4', exportOptions: { columns: ':visible' } },
          'colvis'
        ]
      });
    }
  });
</script>
