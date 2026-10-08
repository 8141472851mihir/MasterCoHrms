<?php error_reporting(0);
extract($_REQUEST);
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-4">
        <h4 class="page-title mb-0">Training Feedback Report</h4>
      </div>
      <div class="col-sm-8">
        <form action="" method="get" accept-charset="utf-8" id="filterForm">
            <div class="container-fluid">
                <div class="form-row ">
                <div class="form-group col-md-6">
                    <input type="text" class="form-control auto-submit" id="submittedDateRange" name="submitted_date_range" autocomplete="off" readonly>
                </div>
                <div class="form-group col-md-6">
                    <select id="employee_filter" name="employee_name" class="form-control auto-submit single-select">
                    <?php
                    $employee_selected = (isset($_GET['employee_name']) && $_GET['employee_name'] != '') ? $_GET['employee_name'] : 'All';
                    $all_representatives_selected = ($employee_selected == 'All') ? 'selected' : '';
                    echo "<option value='All' $all_representatives_selected>All Representatives</option>";
                    $employees = $d->selectRow("admin_name", "bms_admin_master", "role_id != 1 AND active_status = '0'", "ORDER BY admin_name ASC");
                    while ($emp = mysqli_fetch_array($employees)) {
                        $selected = ($employee_selected == $emp['admin_name']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($emp['admin_name'], ENT_QUOTES) . "' $selected>" . htmlspecialchars($emp['admin_name']) . "</option>";
                    }
                    ?>
                    </select>
                </div>
                </div>
            </div>
        </form>
      </div>
    </div>
    <div class="row mt-2">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="reportTableReorderable" class="table table-bordered" data-use-server="1">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Company Name</th>
                    <th>CHL Representative</th>
                    <th>CHL Designation</th>
                    <th>Client Name</th>
                    <th>Client Designation</th>
                    <th>Client Mobile</th>
                    <th>Client Email</th>
                    <th>Completed Modules</th>
                    <th>Not Applicable Modules</th>
                    <th>Pending Modules</th>
                    <th>Total Modules</th>
                    <th>Product Knowledge</th>
                    <th>Communication</th>
                    <th>Attire & Behavior</th>
                    <th>Training Capabilities</th>
                    <th>Submitted Date</th>
                    <th>View Details</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th class="no-search-box"></th>
                    <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                  </tr>
                </tfoot>
                <tbody></tbody>
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
        <h5 class="modal-title text-white" id="viewFormDetailsModalLabel">Training Form Details</h5>
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
  var reportTable;
  $(function() {
    moment.locale('en');
    function initDateRangePicker(selector, defaultStart, defaultEnd) {
      $(selector).daterangepicker({
        startDate: defaultStart,
        endDate: defaultEnd,
        autoUpdateInput: false,
        locale: { format: 'MMMM DD, YYYY', applyLabel: "Apply", cancelLabel: "Cancel", daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'], monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'], firstDay: 0 },
        maxDate: new Date(),
        maxSpan: { days: 365 },
        ranges: { 'Last 30 Days': [moment().subtract(29, 'days'), moment()], 'This Month': [moment().startOf('month'), moment().endOf('month')], 'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')], 'Last 3 Months': [moment().subtract(2, 'month').startOf('month'), moment().endOf('month')] }
      });
      $(selector).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MMMM DD, YYYY') + ' - ' + picker.endDate.format('MMMM DD, YYYY'));
        $('#filterForm').submit();
      });
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
      return { start, end };
    }
    const submittedDateRangeStr = "<?php echo isset($_GET['submitted_date_range']) ? addslashes($_GET['submitted_date_range']) : ''; ?>";
    const submittedRange = parseDateRange(submittedDateRangeStr) || { start: moment().startOf('month'), end: moment().endOf('month') };
    if (submittedDateRangeStr) $('#submittedDateRange').val(submittedDateRangeStr);
    initDateRangePicker('#submittedDateRange', submittedRange.start, submittedRange.end);
    // Remove preventDefault - let form submit normally with GET
    $('.auto-submit').not('input[type=text]').on('change', function() {
      $('#filterForm').submit();
    });
    if ($.fn.DataTable.isDataTable('#reportTableReorderable')) {
      reportTable = $('#reportTableReorderable').DataTable();
    } else {
      reportTable = $('#reportTableReorderable').DataTable({
      processing: true,
      serverSide: true,
      stateSave: true,
      ajax: {
        url: 'ajax/trainingFeedbackReportTable.php',
        type: 'POST',
        data: function(d) {
          // Get filter values from URL parameters (GET)
          var urlParams = new URLSearchParams(window.location.search);
          d.submitted_date_range = urlParams.get('submitted_date_range') || '';
          d.employee_name = urlParams.get('employee_name') || 'All';
        }
      },
      colReorder: true,
      lengthChange: true,
      pageLength: 25,
      lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
      buttons: ['copy', 'excel', 'pdf', 'csv', 'colvis', { text: 'Select All Columns', action: function (e, dt, node, config) { dt.columns().every(function () { this.visible(true, false); }); dt.columns.adjust().draw(false); } }, { text: 'Deselect All Columns', action: function (e, dt, node, config) { dt.columns().every(function () { this.visible(false, false); }); dt.column(0).visible(true, false); dt.columns.adjust().draw(false); } }],
      select: true,
      dom: 'Blfrtip',
      columnDefs: [
        { orderable: false, targets: [17] },
        // Allow HTML rendering for star rating columns (12-15) - columns are 0-indexed
        { 
          targets: [12, 13, 14, 15],
          orderable: false,
          defaultContent: '<span class="text-muted">-</span>',
          render: function(data, type, row) {
            // Always return HTML string for display - DataTables will render it
            if (!data || data === '' || data === null) {
              return '<span class="text-muted">-</span>';
            }
            // Return the HTML string as-is - don't escape it
            return data;
          }
        }
      ],
      initComplete: function() {
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
      if (data.trainer_feedback.feedback_remark) {
        html += '<div class="col-12"><strong>Feedback Remark:</strong> ' + data.trainer_feedback.feedback_remark + '</div>';
      }
      
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
