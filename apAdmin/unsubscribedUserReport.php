<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// Check if this is a CSV export request
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
  // Include necessary files for database connection and session
  session_start();
  include_once 'common/object.php';
  include_once 'common/checkLogin.php';

  // Check if user is logged in
  if (!isset($bms_admin_id)) {
    header("location:index.php?Login First");
    exit;
  }

  // Get date range filter
  $dateFilter = "";
  if (!empty($_GET['date_range'])) {
    $range = explode(' - ', $_GET['date_range']);
    if (count($range) == 2) {
      $from = date('Y-m-d', strtotime($range[0]));
      $toDate = date('Y-m-d', strtotime($range[1]));
      $dateFilter = " AND DATE(unsubscribed_on) BETWEEN '$from' AND '$toDate'";
    }
  }

  // CSV headers
  $contents = "No,Mobile Number,Unsubscribed Date,Unsubscribed Time,Days Since\n";

  // Get data
  $q = $d->selectRow(
    "user_mobile, unsubscribed_on",
    "whatsapp_unsubscribe_master",
    "1=1" . $dateFilter,
    "ORDER BY unsubscribed_on DESC"
  );

  $i = 1;
  while ($row = mysqli_fetch_array($q)) {
    $unsubscribedDate = $row['unsubscribed_on'];
    $daysSince = floor((time() - strtotime($unsubscribedDate)) / (60 * 60 * 24));

    // Format date and time
    $dateFormatted = date("d-M-Y", strtotime($unsubscribedDate));
    $timeFormatted = date("h:i A", strtotime($unsubscribedDate));

    $contents .= $i++ . ",";
    $contents .= $row['user_mobile'] . ",";
    $contents .= $dateFormatted . ",";
    $contents .= $timeFormatted . ",";
    $contents .= $daysSince . " days\n";
  }

  $contents = strip_tags($contents);

  // Set headers for CSV download
  header("Content-Type: text/csv");
  header("Content-Disposition: attachment; filename=Unsubscribed_Users_Report_" . date('Y-m-d-h-i') . ".csv");
  header("Pragma: no-cache");
  header("Expires: 0");

  echo $contents;
  exit;
}

// Get date range filter
$dateFilter = "";
if (!empty($_GET['date_range'])) {
  $range = explode(' - ', $_GET['date_range']);
  if (count($range) == 2) {
    $from = date('Y-m-d', strtotime($range[0]));
    $toDate = date('Y-m-d', strtotime($range[1]));
    $dateFilter = " AND DATE(unsubscribed_on) BETWEEN '$from' AND '$toDate'";
  }
}
$totalCount = 0;
$todayCount = 0;
$thisMonthCount = 0;
// Get total count
$totalCount = $d->count_data_direct("user_id", "whatsapp_unsubscribe_master", "1=1" . $dateFilter);

// Get today's count
$todayCount = $d->count_data_direct("user_id", "whatsapp_unsubscribe_master", "DATE(unsubscribed_on) = CURDATE()");

// Get this month's count
$thisMonthCount = $d->count_data_direct("user_id", "whatsapp_unsubscribe_master", "MONTH(unsubscribed_on) = MONTH(CURDATE()) AND YEAR(unsubscribed_on) = YEAR(CURDATE())");

?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-6">
        <h4 class="page-title">Unsubscribed Users Report</h4>
      </div>
      <div class="col-sm-6">
        <div class="btn-group float-sm-right">
          <button type="button" class="btn btn-success btn-sm" onclick="exportToCSV()">
            <i class="fa fa-download"></i> Export CSV
          </button>
        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->

    <!-- Statistics Cards -->
    <div class="row mb-3">
      <div class="col-lg-4 col-md-6">
        <div class="card bg-primary text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h4 class="mb-0 text-white"><?php echo number_format($totalCount); ?></h4>
                <p class="mb-0">Total Unsubscribed</p>
              </div>
              <div class="align-self-center">
                <i class="fa fa-users fa-2x"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card bg-warning text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h4 class="mb-0 text-white"><?php echo number_format($todayCount); ?></h4>
                <p class="mb-0">Today</p>
              </div>
              <div class="align-self-center">
                <i class="fa fa-calendar-o fa-2x"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="card bg-info text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h4 class="mb-0 text-white"><?php echo number_format($thisMonthCount); ?></h4>
                <p class="mb-0">This Month</p>
              </div>
              <div class="align-self-center">
                <i class="fa fa-calendar fa-2x"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-3">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form action="" method="get" class="row">
              <div class="col-md-4">
                <label>Date Range:</label>
                <input placeholder="Select a date range to filter the report" type="text" class="form-control" id="dateRange" name="date_range"
                  autocomplete="off" readonly>
              </div>
              <div class="col-md-4">
                <label>&nbsp;</label>
                <div>
                  <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                  <a href="unsubscribedUserReport" class="btn btn-outline-warning"><i class="fa fa-times"></i> Clear</a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Table -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="unsubscribedTable" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Mobile Number</th>
                    <th>Unsubscribed From (WABA Phone number)</th>
                    <th>Unsubscribed Date</th>
                    <th>Unsubscribed Time</th>
                    <th>Days Since</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->selectRow(
                    "user_mobile, unsubscribed_on, waba_phone_number",
                    "whatsapp_unsubscribe_master",
                    "1=1" . $dateFilter,
                    "ORDER BY unsubscribed_on DESC"
                  );

                  while ($data = mysqli_fetch_array($q)) {
                    $unsubscribedDate = $data['unsubscribed_on'];
                    $daysSince = floor((time() - strtotime($unsubscribedDate)) / (60 * 60 * 24));

                    // Format date and time
                    $dateFormatted = date("d M Y", strtotime($unsubscribedDate));
                    $timeFormatted = date("h:i A", strtotime($unsubscribedDate));
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td>
                        <span><?php echo "+" . $data['user_mobile']; ?></span>
                      </td>
                      <td>
                        <span><?php echo "+" . $data['waba_phone_number']; ?></span>
                      </td>
                      <td><?php echo $dateFormatted; ?></td>
                      <td><?php echo $timeFormatted; ?></td>
                      <td>
                        <?php if ($daysSince == 0): ?>
                          <span>Today</span>
                        <?php elseif ($daysSince == 1): ?>
                          <span>Yesterday</span>
                        <?php else: ?>
                          <span><?php echo $daysSince; ?> days</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <button class="btn btn-sm btn-success" onclick="resubscribeUser('<?php echo $data['user_mobile']; ?>','<?php echo $data['waba_phone_number']; ?>')" title="Resubscribe">
                          <i class="fa fa-check"></i> Resubscribe
                        </button>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="6" class="text-center">
                      Total Records: <strong><?php echo number_format($totalCount); ?></strong>
                    </th>
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

<!-- Resubscribe Confirmation Modal -->
<div class="modal fade" id="resubscribeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Resubscribe User</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to resubscribe this user?</p>
        <p><strong>Mobile:</strong> <span id="resubscribeMobile"></span></p>
        <p><strong>Resubscribe To:</strong> <span id="resubscribeWabaMobile"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="confirmResubscribe">Yes, Resubscribe</button>
      </div>
    </div>
  </div>
</div>

<script src="assets/js/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    // Initialize DataTable
    $('#unsubscribedTable').DataTable({
      "pageLength": 25,
      "order": [
        [2, "desc"]
      ],
      "columnDefs": [{
        "orderable": false,
        "targets": [5]
      }]
    });

  });

  // Resubscribe user function
  function resubscribeUser(mobile, wabaPhoneNumber) {
    $('#resubscribeMobile').text(mobile);
    $('#resubscribeWabaMobile').text(wabaPhoneNumber);
    $('#resubscribeModal').modal('show');

    $('#confirmResubscribe').off('click').on('click', function() {
      $.ajax({
        url: 'controller/unsubscribedController.php',
        type: 'POST',
        data: {
          action: 'resubscribe',
          mobile: mobile,
          waba_phone_number: wabaPhoneNumber
        },
        success: function(response) {
          var result = JSON.parse(response);
          if (result.status === 'success') {
            swal({
              title: "Success!",
              icon: "success",
              text: "User resubscribed successfully!",
              // type: "success",
              timer: 3000
            });
            setTimeout(() => {
              location.reload();
            }, 2000);
          } else {
            swal({
              title: "Error!",
              icon: "error",
              text: result.message,
              // type: "error",
              timer: 4000
            });
          }
        },
        error: function() {
          swal({
            title: "Error!",
            icon: "error",
            text: "An error occurred while processing the request.",
            // type: "error",
            timer: 4000
          });
        }
      });
      $('#resubscribeModal').modal('hide');
    });
  }

  // Export to CSV function
  function exportToCSV() {
    var dateRange = $('#dateRange').val();
    var url = 'unsubscribedUserReport.php?export=csv';
    if (dateRange) {
      url += '&date_range=' + encodeURIComponent(dateRange);
    }
    window.open(url, '_blank');
  }
</script>

<script>
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
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
      });

      // When Apply is clicked
      $(selector).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MMMM DD, YYYY') + ' - ' + picker.endDate.format('MMMM DD, YYYY'));
        $('#filterForm').submit();
      });

      // When Cancel is clicked
      $(selector).on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('#filterForm').submit();
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

    const dateRangeStr = "<?php echo isset($_GET['date_range']) ? addslashes($_GET['date_range']) : ''; ?>";

    const dateRange = parseDateRange(dateRangeStr) || {
      start: moment().startOf('month'),
      end: moment().endOf('month')
    };

    if (dateRangeStr) $('#dateRange').val(dateRangeStr);

    initDateRangePicker('#dateRange', dateRange.start, dateRange.end);

  });
</script>