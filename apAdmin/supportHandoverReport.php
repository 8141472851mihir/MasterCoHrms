<?php
include_once 'common/object.php';
$countryId = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId'], 101) : 101;
$stateId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
$cityId = isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : 0;
$riseFilter = isset($_GET['rise_filter']) ? $_GET['rise_filter'] : 'yes';
$handoverStatus = isset($_GET['handover_status']) ? $_GET['handover_status'] : 'all';
$expiryFilter = isset($_GET['expiry_filter']) ? $_GET['expiry_filter'] : 'not_expired';
$handoverDateFilter = isset($_GET['support_handover_date_range']) ?  $_GET['support_handover_date_range'] : '';

$where = ["sm.created_on_society_server = 1"];

if ($countryId > 0) {
    $where[] = "sm.country_id = $countryId";
}

if ($stateId > 0) {
    $where[] = "sm.state_id = $stateId";
}

if ($cityId > 0) {
    $where[] = "sm.city_id = $cityId";
}

if ($riseFilter === "yes") {
    $where[] = "sm.from_rise_event = 1";
} elseif ($riseFilter === "no") {
    $where[] = "(sm.from_rise_event IS NULL OR sm.from_rise_event = 0)";
}

if ($handoverStatus === "pending") {
    $where[] = "(sm.support_handover IS NULL OR sm.support_handover = 0)";
} elseif ($handoverStatus === "completed") {
    $where[] = "sm.support_handover = 1";
}

if ($expiryFilter === "expired") {
    $where[] = "(sm.plan_expire_date IS NOT NULL AND sm.plan_expire_date < CURDATE())";
} elseif ($expiryFilter === "not_expired") {
    $where[] = "(sm.plan_expire_date IS NULL OR sm.plan_expire_date >= CURDATE())";
}

if (!empty($handoverDateFilter)) {
    $range = explode(' - ', $handoverDateFilter);
    if (count($range) == 2) {
        $from = date('Y-m-d', strtotime($range[0])) . ' 00:00:00';
        $to = date('Y-m-d', strtotime($range[1])) . ' 23:59:59';
        $where[] = "sm.support_handover_date BETWEEN '$from' AND '$to'";
    }
}

$whereSql = implode(' AND ', $where);

$handoverQuery = $d->selectRow(
    "sm.society_id,
     sm.society_name,
     sm.support_handover,
     sm.region_name,
     sm.support_handover_date,sm.implementation_name,
     sm.post_implementation_remark,
     sm.customer_expectation_remark,
     sm.support_handover_by,
     sm.from_rise_event,
     sm.created_date,
     sm.plan_expire_date,
     c.name as country_name,
     ci.name as city_name,
     bam.admin_name as handover_by_name",
    "society_master sm
     INNER JOIN countries c ON sm.country_id = c.country_id
     LEFT JOIN cities ci ON sm.city_id = ci.city_id
     LEFT JOIN bms_admin_master bam ON sm.support_handover_by = bam.admin_id",
    $whereSql,
    "ORDER BY sm.support_handover_date DESC, sm.society_id DESC"
);

$shortAppName = $d->short_app_name();

$handoverRows = [];
$societyIdsForFeedback = [];
$rowCount = 0;
while ($row = mysqli_fetch_assoc($handoverQuery)) {
    $handoverRows[] = $row;
    $societyIdsForFeedback[] = (int)$row['society_id'];
    $rowCount++;
    if ($rowCount >= 10000) {
        break;
    }
}

// Batch latest training feedback forms (avoid N+1 per company)
$feedbackBySociety = [];
if (!empty($societyIdsForFeedback)) {
    $societyIdsIn = implode(',', array_map('intval', $societyIdsForFeedback));
    $feedbackQuery = $d->selectRow(
        "form_id, society_id, submitted_date",
        "training_completion_form_master",
        "society_id IN ($societyIdsIn)",
        "ORDER BY submitted_date DESC"
    );
    while ($feedbackRow = mysqli_fetch_assoc($feedbackQuery)) {
        $sid = (int)$feedbackRow['society_id'];
        if (!isset($feedbackBySociety[$sid])) {
            $feedbackBySociety[$sid] = $feedbackRow;
        }
    }
}

$handoverData = [];
foreach ($handoverRows as $row) {
    $row['handover_completed'] = !empty($row['support_handover']) && (int)$row['support_handover'] === 1;
    $row['status_badge'] = $row['handover_completed'] ? 'success' : 'warning';
    $row['status_text'] = $row['handover_completed'] ? 'Completed' : 'Pending';

    $row['handover_date_formatted'] = !empty($row['support_handover_date'])
        ? date('d M Y, h:i A', strtotime($row['support_handover_date']))
        : '';

    $row['created_date_formatted'] = !empty($row['created_date'])
        ? date('d M Y', strtotime($row['created_date']))
        : '';

    $row['expiry_date_formatted'] = !empty($row['plan_expire_date'])
        ? date('d M Y', strtotime($row['plan_expire_date']))
        : '';

    $row['is_expired'] = !empty($row['plan_expire_date']) && strtotime($row['plan_expire_date']) < time();

    $row['rise_event_badge'] = !empty($row['from_rise_event']) && (int)$row['from_rise_event'] === 1
        ? '<span class="badge badge-info">Myco Rise</span>'
        : '<span class="badge badge-secondary">Before Myco Rise</span>';

    $row['post_impl_remark_short'] = !empty($row['post_implementation_remark'])
        ? (strlen($row['post_implementation_remark']) > 50
            ? htmlspecialchars(substr($row['post_implementation_remark'], 0, 50)) . '...'
            : htmlspecialchars($row['post_implementation_remark']))
        : '';

    $row['cust_exp_remark_short'] = !empty($row['customer_expectation_remark'])
        ? (strlen($row['customer_expectation_remark']) > 50
            ? htmlspecialchars(substr($row['customer_expectation_remark'], 0, 50)) . '...'
            : htmlspecialchars($row['customer_expectation_remark']))
        : '';

    $row['company_id_display'] = $shortAppName . '_' . $row['society_id'];

    $row['feedback_form_id'] = null;
    $row['feedback_submitted'] = false;
    if (isset($feedbackBySociety[(int)$row['society_id']])) {
        $feedbackRow = $feedbackBySociety[(int)$row['society_id']];
        $row['feedback_form_id'] = (int)$feedbackRow['form_id'];
        $row['feedback_submitted'] = true;
    }

    $handoverData[] = $row;
}
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Support Handover Report</h4>
            </div>
        </div>

        <div class="row pt-2 pb-2">
            <div class="col-lg-12">
                <form action="" method="get" accept-charset="utf-8">
                    <div class="form-group row">
                        <label for="country_id" class="col-sm-1 mb-2 col-form-label">Country <span class="required">*</span></label>
                        <div class="col-sm-2 mb-2">
                            <select required id="country_id" class="form-control single-select" name="countryId" onchange="this.form.submit()">
                                <option value="">-- Select --</option>
                                <?php
                                // Cache countries query result
                                $qc = $d->select("countries", "flag=1", "ORDER BY name ASC");
                                while ($cData = mysqli_fetch_assoc($qc)) {
                                    $selected = ($cData['country_id'] == $countryId) ? "selected" : "";
                                    echo "<option value='{$cData['country_id']}' $selected>" . htmlspecialchars($cData['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <label for="state_id" class="col-sm-1 mb-2 col-form-label">State</label>
                        <div class="col-sm-2 mb-2">
                            <?php if ($countryId > 0) { ?>
                                <select class="form-control single-select" id="state_id" name="sId" onchange="this.form.submit()">
                                    <option value="">All</option>
                                    <?php
                                    $qs = $d->select("states", "country_id=$countryId", "ORDER BY name ASC");
                                    while ($sData = mysqli_fetch_assoc($qs)) {
                                        $selected = ($stateId > 0 && $sData['state_id'] == $stateId) ? "selected" : "";
                                        echo "<option value='{$sData['state_id']}' $selected>" . htmlspecialchars($sData['name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            <?php } else { ?>
                                <select class="form-control single-select" id="state_id" name="sId">
                                    <option value="">-- Select Country First --</option>
                                </select>
                            <?php } ?>
                        </div>

                        <label for="city_id" class="col-sm-1 mb-2 col-form-label">City</label>
                        <div class="col-sm-2 mb-2">
                            <?php if ($stateId > 0) { ?>
                                <select class="form-control single-select" id="city_id" name="cId" onchange="this.form.submit()">
                                    <option value="">All</option>
                                    <?php
                                    $qcity = $d->select("cities", "state_id=$stateId", "ORDER BY name ASC");
                                    while ($cityData = mysqli_fetch_assoc($qcity)) {
                                        $selected = ($cityId > 0 && $cityData['city_id'] == $cityId) ? "selected" : "";
                                        echo "<option value='{$cityData['city_id']}' $selected>" . htmlspecialchars($cityData['name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            <?php } else { ?>
                                <select class="form-control single-select" id="city_id" name="cId">
                                    <option value="">-- Select State First --</option>
                                </select>
                            <?php } ?>
                        </div>

                        <label for="rise_filter" class="col-sm-1 mb-2 col-form-label">Rise Event</label>
                        <div class="col-sm-2 mb-2">
                            <select id="rise_filter" name="rise_filter" class="form-control single-select" onchange="this.form.submit()">
                                <option value="yes" <?php echo ($riseFilter === 'yes' || !isset($_GET['rise_filter'])) ? 'selected' : ''; ?>>Myco Rise</option>
                                <option value="no" <?php echo ($riseFilter === 'no') ? 'selected' : ''; ?>>Before Myco Rise</option>
                                <option value="all" <?php echo ($riseFilter === 'all') ? 'selected' : ''; ?>>All</option>
                            </select>
                        </div>

                        <label for="handover_status" class="col-sm-1 mb-2 col-form-label">Status</label>
                        <div class="col-sm-2 mb-2">
                            <select id="handover_status" name="handover_status" class="form-control single-select" onchange="this.form.submit()">
                                <option value="all" <?php echo ($handoverStatus === 'all') ? 'selected' : ''; ?>>All</option>
                                <option value="pending" <?php echo ($handoverStatus === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="completed" <?php echo ($handoverStatus === 'completed') ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>

                        <label for="expiry_filter" class="col-sm-1 mb-2 col-form-label">Expiry</label>
                        <div class="col-sm-2 mb-2">
                            <select id="expiry_filter" name="expiry_filter" class="form-control single-select" onchange="this.form.submit()">
                                <option value="not_expired" <?php echo ($expiryFilter === 'not_expired' || !isset($_GET['expiry_filter'])) ? 'selected' : ''; ?>>Not Expired</option>
                                <option value="expired" <?php echo ($expiryFilter === 'expired') ? 'selected' : ''; ?>>Expired</option>
                                <option value="all" <?php echo ($expiryFilter === 'all') ? 'selected' : ''; ?>>All</option>
                            </select>
                        </div>

                        <label for="expiry_filter" class="col-sm-1 mb-2 col-form-label">Handover Date</label>
                        <div class="col-sm-2 mb-2">
                            <input type="text" class="form-control jsDateRangePicker" name="support_handover_date_range" data-drp-auto-submit="true" data-drp-show-ranges="true" data-drp-max-date="today" readonly value="<?php echo isset($_GET['support_handover_date_range']) ?  $_GET['support_handover_date_range'] : ''; ?>">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="supportHandoverReport" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Company ID</th>
                                        <th>Company Name</th>
                                        <th>Country</th>
                                        <th>City</th>
                                        <th>Campaign Region</th>
                                        <th>Rise Event</th>
                                        <th>Handover Status</th>
                                        <th>Handover Date</th>
                                        <th>Handover By</th>
                                        <th>Implementation Name</th>
                                        <th>Post Implementation Remark</th>
                                        <th>Customer Expectation Remark</th>
                                        <th>Plan Expiry Date</th>
                                        <th>Company Created Date</th>
                                        <th>View Feedback</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($rowCount > 0) {
                                        $i = 1;
                                        foreach ($handoverData as $row) {
                                    ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo htmlspecialchars($row['company_id_display']); ?></td>
                                                <td><?php echo htmlspecialchars($row['society_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['country_name'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($row['city_name'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($row['region_name'] ?? ''); ?></td>
                                                <td data-export="<?php echo htmlspecialchars(($row['from_rise_event'] == 1) ? 'Myco Rise' : 'Before Myco Rise'); ?>">
                                                    <?php echo $row['rise_event_badge']; ?>
                                                </td>
                                                <td data-export="<?php echo htmlspecialchars($row['status_text']); ?>">
                                                    <span class="badge badge-<?php echo $row['status_badge']; ?>">
                                                        <?php echo $row['status_text']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $row['handover_date_formatted']; ?></td>
                                                <td><?php echo htmlspecialchars($row['handover_by_name'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($row['implementation_name'] ?? ''); ?></td>
                                                <td data-export="<?php echo htmlspecialchars($row['post_implementation_remark'] ?? ''); ?>">
                                                    <?php
                                                    if ($row['post_impl_remark_short'] !== '') {
                                                        echo '<span title="' . htmlspecialchars($row['post_implementation_remark']) . '">';
                                                        echo $row['post_impl_remark_short'];
                                                        echo '</span>';
                                                    } else {
                                                        echo '';
                                                    }
                                                    ?>
                                                </td>
                                                <td data-export="<?php echo htmlspecialchars($row['customer_expectation_remark'] ?? ''); ?>">
                                                    <?php
                                                    if ($row['cust_exp_remark_short'] !== '') {
                                                        echo '<span title="' . htmlspecialchars($row['customer_expectation_remark']) . '">';
                                                        echo $row['cust_exp_remark_short'];
                                                        echo '</span>';
                                                    } else {
                                                        echo '';
                                                    }
                                                    ?>
                                                </td>
                                                <td data-export="<?php echo htmlspecialchars($row['expiry_date_formatted']); ?>">
                                                    <?php
                                                    if (!empty($row['expiry_date_formatted'])) {
                                                        $expiryBadge = $row['is_expired'] ? 'danger' : 'success';
                                                        $expiryText = $row['is_expired'] ? 'Expired' : 'Active';
                                                        echo '<span class="badge badge-' . $expiryBadge . '">' . $expiryText . '</span><br>';
                                                        echo $row['expiry_date_formatted'];
                                                    } else {
                                                        echo '<span class="badge badge-secondary">N/A</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo $row['created_date_formatted']; ?></td>
                                                <td>
                                                    <?php if ($row['feedback_submitted'] && $row['feedback_form_id']): ?>
                                                        <button type="button" 
                                                            class="btn btn-sm btn-info" 
                                                            onclick="viewFormDetails(<?php echo $row['feedback_form_id']; ?>)"
                                                            title="View Training Feedback Form">
                                                            <i class="fa fa-eye"></i> View Feedback
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Not Submitted</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Form Details Modal -->
<div class="modal fade" id="viewFormDetailsModal" tabindex="-1" aria-labelledby="viewFormDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="viewFormDetailsModalLabel">Training Feedback Form Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="formDetailsContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading form details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/jquery.min.js"></script>
<script>
    $(document).ready(function() {       
        // Initialize DataTable for better performance with client-side pagination
        if ($('#supportHandoverReport').length && $('#supportHandoverReport tbody tr').length > 0) {
            $('#supportHandoverReport').DataTable({
                "pageLength": 25,
                "lengthMenu": [
                    [25, 50, 100, 200, -1],
                    [25, 50, 100, 200, "All"]
                ],
                "order": [
                    [7, "desc"]
                ], // Sort by handover date descending
                "columnDefs": [{
                        "type": "date",
                        "targets": [7, 12, 13]
                    }, // Enable date sorting
                    {
                        "orderable": false,
                        "targets": [14] // View Feedback column
                    }
                ],
                "stateSave": true, // Save table state
                "processing": false,
                "dom": 'Bfrtip',
                "buttons": [{
                        extend: 'copy',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    // Use data-export attribute if available (contains full original data)
                                    var exportData = $(node).attr('data-export');
                                    if (exportData !== undefined && exportData !== null && exportData !== '') {
                                        return exportData;
                                    }
                                    // Get text content from the node directly (safer than parsing data)
                                    var textContent = $(node).text();
                                    // If node text is empty, use data as-is (might be a number or plain string)
                                    return textContent || data || '';
                                }
                            }
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    // Use data-export attribute if available (contains full original data)
                                    var exportData = $(node).attr('data-export');
                                    if (exportData !== undefined && exportData !== null && exportData !== '') {
                                        return exportData;
                                    }
                                    // Get text content from the node directly (safer than parsing data)
                                    var textContent = $(node).text();
                                    // If node text is empty, use data as-is (might be a number or plain string)
                                    return textContent || data || '';
                                }
                            }
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    // Use data-export attribute if available (contains full original data)
                                    var exportData = $(node).attr('data-export');
                                    if (exportData !== undefined && exportData !== null && exportData !== '') {
                                        return exportData;
                                    }
                                    // Get text content from the node directly (safer than parsing data)
                                    var textContent = $(node).text();
                                    // If node text is empty, use data as-is (might be a number or plain string)
                                    return textContent || data || '';
                                }
                            }
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    // Use data-export attribute if available (contains full original data)
                                    var exportData = $(node).attr('data-export');
                                    if (exportData !== undefined && exportData !== null && exportData !== '') {
                                        return exportData;
                                    }
                                    // Get text content from the node directly (safer than parsing data)
                                    var textContent = $(node).text();
                                    // If node text is empty, use data as-is (might be a number or plain string)
                                    return textContent || data || '';
                                }
                            }
                        }
                    },
                    'colvis'
                ],
                "language": {
                    "processing": "Loading data...",
                    "emptyTable": "No handover data available"
                }
            });
        }
    });

    // Function to view form details in modal
    function viewFormDetails(formId) {
        $('#viewFormDetailsModal').modal('show');
        $('#formDetailsContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2">Loading form details...</p></div>');
        
        $.ajax({
            url: 'ajax/getTrainingFormDetails.php',
            type: 'POST',
            data: { form_id: formId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displayFormDetails(response.data);
                } else {
                    $('#formDetailsContent').html('<div class="alert alert-danger">' + (response.message || 'Error loading form details') + '</div>');
                }
            },
            error: function() {
                $('#formDetailsContent').html('<div class="alert alert-danger">Error loading form details. Please try again.</div>');
            }
        });
    }

    // Function to display form details in modal
    function displayFormDetails(data) {
        var html = '<div class="container-fluid">';
        
        // Header Section
        html += '<div class="row mb-3"><div class="col-12"><h5 class="text-primary"><strong>Training & Implementation Completion Form</strong></h5></div></div>';
        
        // Section 1: CHL Representative Details
        html += '<div class="card mb-3"><div class="card-header bg-light"><h6 class="mb-0"><strong>Section 1: CHL Representative Details</strong></h6></div><div class="card-body">';
        html += '<div class="row"><div class="col-md-6"><strong>Employee Name:</strong> ' + (data.employee_name || '-') + '</div>';
        html += '<div class="col-md-6"><strong>Designation:</strong> ' + (data.employee_designation || '-') + '</div></div></div></div>';
        
        // Section 2: Client Details
        html += '<div class="card mb-3"><div class="card-header bg-light"><h6 class="mb-0"><strong>Section 2: Client Details</strong></h6></div><div class="card-body">';
        html += '<div class="row"><div class="col-md-6"><strong>Company Name:</strong> ' + (data.company_name || '-') + '</div>';
        html += '<div class="col-md-6"><strong>Client Name:</strong> ' + (data.client_name || '-') + '</div></div>';
        html += '<div class="row mt-2"><div class="col-md-6"><strong>Client Designation:</strong> ' + (data.client_designation || '-') + '</div>';
        html += '<div class="col-md-6"><strong>Client Mobile:</strong> ' + (data.client_country_code || '') + ' ' + (data.client_mobile || '-') + '</div></div>';
        html += '<div class="row mt-2"><div class="col-md-6"><strong>Client Email:</strong> ' + (data.client_email || '-') + '</div></div></div></div>';
        
        // Section 3: Participants
        if (data.participants_data && data.participants_data.length > 0) {
            html += '<div class="card mb-3"><div class="card-header bg-light"><h6 class="mb-0"><strong>Section 3: Participants</strong></h6></div><div class="card-body">';
            html += '<table class="table table-bordered table-sm"><thead><tr><th>Participant</th><th>Status</th></tr></thead><tbody>';
            data.participants_data.forEach(function(participant) {
                var statusBadge = '';
                if (participant.participant_value === 'Yes') {
                    statusBadge = '<span class="badge badge-success">Yes</span>';
                } else if (participant.participant_value === 'No') {
                    statusBadge = '<span class="badge badge-danger">No</span>';
                } else {
                    statusBadge = '<span class="badge badge-secondary">NA</span>';
                }
                html += '<tr><td>' + (participant.participant_name || '-') + '</td><td>' + statusBadge + '</td></tr>';
            });
            html += '</tbody></table></div></div>';
        }
        
        // Section 4: Modules Covered
        if (data.modules_data && data.modules_data.length > 0) {
            html += '<div class="card mb-3"><div class="card-header bg-light"><h6 class="mb-0"><strong>Section 4: Modules Covered</strong></h6></div><div class="card-body">';
            html += '<table class="table table-bordered table-sm"><thead><tr><th>Module Name</th><th>Status</th></tr></thead><tbody>';
            data.modules_data.forEach(function(module) {
                var statusBadge = '';
                if (module.module_value === 'Completed') {
                    statusBadge = '<span class="badge badge-success">Completed</span>';
                } else if (module.module_value === 'Not Applicable') {
                    statusBadge = '<span class="badge badge-warning">Not Applicable</span>';
                } else if (module.module_value === 'Pending') {
                    statusBadge = '<span class="badge badge-secondary">Pending</span>';
                } else {
                    statusBadge = '<span class="badge badge-light">-</span>';
                }
                html += '<tr><td>' + (module.module_name || '-') + '</td><td>' + statusBadge + '</td></tr>';
            });
            html += '</tbody></table>';
            html += '<div class="mt-3"><strong>Summary:</strong> ';
            html += '<span class="badge badge-success">Completed: ' + (data.completed_modules_count || 0) + '</span> ';
            html += '<span class="badge badge-warning">Not Applicable: ' + (data.na_modules_count || 0) + '</span> ';
            html += '<span class="badge badge-secondary">Pending: ' + (data.pending_modules_count || 0) + '</span> ';
            html += '<span class="badge badge-info">Total: ' + (data.total_modules_count || 0) + '</span>';
            html += '</div></div></div>';
        }
        
        // Trainers Feedback Section
        if (data.trainer_feedback && (data.trainer_feedback.product_knowledge || data.trainer_feedback.communication || data.trainer_feedback.attire_behavior || data.trainer_feedback.training_capabilities)) {
            html += '<div class="card mb-3"><div class="card-header bg-light"><h6 class="mb-0"><strong>Trainers Feedback</strong></h6></div><div class="card-body">';
            html += '<div class="row">';
            
            function renderStars(rating) {
                if (!rating || rating < 1 || rating > 5) return '<span class="text-muted">-</span>';
                var starsHtml = '';
                for (var i = 1; i <= 5; i++) {
                    if (i <= rating) {
                        starsHtml += '<i class="fa fa-star text-warning"></i>';
                    } else {
                        starsHtml += '<i class="fa fa-star-o text-muted"></i>';
                    }
                }
                return starsHtml + ' <span class="ml-2">(' + rating + '/5)</span>';
            }
            
            html += '<div class="col-md-6 mb-3"><strong>Product Knowledge:</strong><br>' + renderStars(data.trainer_feedback.product_knowledge) + '</div>';
            html += '<div class="col-md-6 mb-3"><strong>Communication:</strong><br>' + renderStars(data.trainer_feedback.communication) + '</div>';
            html += '<div class="col-md-6 mb-3"><strong>Attire & Behavior:</strong><br>' + renderStars(data.trainer_feedback.attire_behavior) + '</div>';
            html += '<div class="col-md-6 mb-3"><strong>Training Capabilities:</strong><br>' + renderStars(data.trainer_feedback.training_capabilities) + '</div>';
            
            html += '</div></div></div>';
        }
        
        // Section 5: Declaration
        html += '<div class="card mb-3"><div class="card-header bg-light"><h6 class="mb-0"><strong>Section 5: Declaration</strong></h6></div><div class="card-body">';
        html += '<p><strong>Declaration Agreed:</strong> ' + (data.declaration_agreed == 1 ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>') + '</p>';
        html += '<p><strong>OTP Verified:</strong> ' + (data.otp_verified == 1 ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>') + '</p></div></div>';
        
        // Submission Info
        html += '<div class="card mb-3"><div class="card-header bg-light"><h6 class="mb-0"><strong>Submission Information</strong></h6></div><div class="card-body">';
        html += '<div class="row"><div class="col-md-6"><strong>Submitted Date:</strong> ' + (data.submitted_date_formatted || '-') + '</div>';
        html += '<div class="col-md-6"><strong>Created Date:</strong> ' + (data.created_date_formatted || '-') + '</div></div></div></div>';
        
        html += '</div>';
        $('#formDetailsContent').html(html);
    }
</script>