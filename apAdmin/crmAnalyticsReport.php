<?php
extract(array_map("test_input", $_REQUEST));
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// Get CRM list from society_crm_master
$crmList = $d->selectRow("society_crm_master.*", "society_crm_master", "1=1", "ORDER BY server ASC");
$selectedCrmId = isset($_GET['society_crm_id']) ? (int)$_GET['society_crm_id'] : 0;

// Get selected CRM details
$selectedCrm = null;
if ($selectedCrmId > 0) {
    $crmQuery = $d->selectRow("society_crm_master.*", "society_crm_master", "society_crm_id='$selectedCrmId'");
    if (mysqli_num_rows($crmQuery) > 0) {
        $selectedCrm = mysqli_fetch_array($crmQuery);
    }
}

// Handle date range from date range picker
$startDate = date('Y-m-01'); // Default start of current month
$endDate = date('Y-m-t');    // Default end of current month

// Set default date range display for current month
$defaultDateRange = date('F d, Y', strtotime($startDate)) . ' - ' . date('F d, Y', strtotime($endDate));

if (!empty($_GET['date_range'])) {
    $range = explode(' - ', $_GET['date_range']);
    if (count($range) == 2) {
        $startDate = date('Y-m-d', strtotime($range[0]));
        $endDate = date('Y-m-d', strtotime($range[1]));
    }
} else {
    // If no date range is provided, set the default current month range
    $_GET['date_range'] = $defaultDateRange;
}

// Function to make cURL request to CRM API
function getCrmAnalyticsData($startDate, $endDate, $crmUrl = null, $crmToken = null)
{
    // Default values if no CRM is selected
    $url = $crmUrl;
    $bearerToken = $crmToken;

    $data = array(
        'startDate' => $startDate,
        'endDate' => $endDate
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $bearerToken
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return array('error' => 'cURL Error: ' . $error);
    }

    if ($httpCode !== 200) {
        return array('error' => 'HTTP Error: ' . $httpCode);
    }

    $decodedResponse = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return array('error' => 'JSON Decode Error: ' . json_last_error_msg());
    }

    return $decodedResponse;
}

// Fetch data if CRM is selected and date filters are applied
$analyticsData = null;
$error = null;
if ($selectedCrmId > 0 && (!empty($_GET['date_range']) || (!empty($startDate) && !empty($endDate)))) {
    // Use selected CRM URL and token
    $crmUrl = null;
    $crmToken = null;
    
    if ($selectedCrm) {
        $crmUrl = rtrim($selectedCrm['url'], '/') . '/api/v1/tenant-activity-counts';
        $crmToken = $selectedCrm['token'];
    }
    
    $result = getCrmAnalyticsData($startDate, $endDate, $crmUrl, $crmToken);
    if (isset($result['error'])) {
        $error = $result['error'];
    } else {
        $analyticsData = $result;
    }
} elseif ($selectedCrmId > 0 && empty($_GET['date_range'])) {
    // CRM selected but no date range
    $error = "Please select a date range to generate the report.";
} elseif ($selectedCrmId == 0 && !empty($_GET['date_range'])) {
    // Date range selected but no CRM
    $error = "Please select a CRM to generate the report.";
}
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row">
            <div class="col-sm-3 pt-2">
                <h4 class="page-title">CRM Analytics Report</h4>
            </div>

        <div class="col-sm-9">
            <form action="" method="get" accept-charset="utf-8" class="branchDeptFilter" id="crmReportForm">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <select class="form-control single-select" name="society_crm_id" onchange="this.form.submit()">
                            <option value="">-- Select CRM --</option>
                            <?php
                            if (mysqli_num_rows($crmList) > 0) {
                                mysqli_data_seek($crmList, 0); // Reset pointer
                                while ($crm = mysqli_fetch_array($crmList)) {
                                    $selected = ($selectedCrmId == $crm['society_crm_id']) ? 'selected' : '';
                                    echo '<option value="' . $crm['society_crm_id'] . '" ' . $selected . '>' . htmlspecialchars($crm['server']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 col-12 form-group">
                        <input type="text" class="form-control" id="crmDateRange" name="date_range" placeholder="Select date range" autocomplete="off" readonly
                            value="<?php echo isset($_GET['date_range']) ? $_GET['date_range'] : ''; ?>">
                    </div>
                </div>
            </form>
        </div>
        </div>

        <!-- Results Section -->
        <?php if ($error) { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-danger p-2">
                        <i class="fa fa-exclamation-triangle"></i> Error: <?php echo htmlspecialchars($error); ?>
                    </div>
                </div>
            </div>
        <?php } elseif ($analyticsData) { ?>
            <!-- Data Table -->
            <div class="row mt-2">
                <div class="col-lg-12">
                    <div class="card">
                        <?php if ($selectedCrm) { ?>
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    CRM Analytics Data - <?php echo htmlspecialchars($selectedCrm['server']); ?>
                                    <small class="text-muted">(<?php echo htmlspecialchars($selectedCrm['url']); ?>)</small>
                                </h6>
                            </div>
                        <?php } ?>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="reportTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Company ID</th>
                                            <th>Company Name</th>
                                            <th>Range Lead Count</th>
                                            <th>Total Lead Count</th>
                                            <th>Range Meeting Count</th>
                                            <th>Total Meeting Count</th>
                                            <th>Range Schedule Call Count</th>
                                            <th>Total Schedule Call Count</th>
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
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                        if (isset($analyticsData['data']) && is_array($analyticsData['data'])) {
                                            $sno = 1;
                                            foreach ($analyticsData['data'] as $company) { ?>
                                                <tr>
                                                    <td><?php echo $sno++; ?></td>
                                                    <td><?php echo htmlspecialchars($company['id'] ?? 'N/A'); ?></td>
                                                    <td><?php echo htmlspecialchars($company['companyName'] ?? 'N/A'); ?></td>
                                                    <td><?php echo number_format($company['RangeLeadCount'] ?? 0); ?></td>
                                                    <td><?php echo number_format($company['TotalLeadCount'] ?? 0); ?></td>
                                                    <td><?php echo number_format($company['RangeMeetingCount'] ?? 0); ?></td>
                                                    <td><?php echo number_format($company['TotalMeetingCount'] ?? 0); ?></td>
                                                    <td><?php echo number_format($company['RangeScheduleCallCount'] ?? 0); ?></td>
                                                    <td><?php echo number_format($company['TotalScheduleCallCount'] ?? 0); ?></td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <td colspan="9" class="text-center">No data available</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php } else { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-info p-2">
                        <i class="fa fa-info-circle"></i> Please select a CRM and choose date range to view CRM analytics data.
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script>
    $(function() {
        moment.locale('en');

        function initDateRangePicker(selector, defaultStart, defaultEnd) {
            $(selector).daterangepicker({
                startDate: defaultStart,
                endDate: defaultEnd,
                autoUpdateInput: true,
                locale: {
                    format: 'MMMM DD, YYYY',
                    applyLabel: "Apply",
                    cancelLabel: "Cancel",
                    daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                    monthNames: moment.months(),
                    firstDay: 0
                },
                maxDate: moment(),
                maxSpan: {
                    days: 400
                },
                ranges: {
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $(selector).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MMMM DD, YYYY') + ' - ' + picker.endDate.format('MMMM DD, YYYY'));
                // Auto-submit form when date range is applied
                $('#crmReportForm').submit();
            });

            $(selector).on('cancel.daterangepicker', function() {
                $(this).val('');
            });
        }

        const rangeStr = "<?php echo isset($_GET['date_range']) ? addslashes($_GET['date_range']) : addslashes($defaultDateRange); ?>";
        const parts = rangeStr.split(' - ');
        const defaultStart = parts[0] ? moment(parts[0], 'MMMM DD, YYYY') : moment().startOf('month');
        const defaultEnd = parts[1] ? moment(parts[1], 'MMMM DD, YYYY') : moment().endOf('month');

        initDateRangePicker('#crmDateRange', defaultStart, defaultEnd);
    });
</script>