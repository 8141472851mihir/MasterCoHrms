<?php
// CRM Inquiry Deletion Requests (Admin screen)
// Loads data via DataTables server-side AJAX and updates status via AJAX.
?>

<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row align-items-end mb-4">
      <div class="col-sm-4 d-flex align-items-center mb-3">
        <h4 class="page-title mb-0">CRM Inquiry Deletion Requests</h4>
      </div>
      <div class="col-md-3">
        <label for="crmInquiryStatusFilter">Status</label>
        <select id="crmInquiryStatusFilter" class="form-control single-select">
          <option value="all">All</option>
          <option value="0">Pending</option>
          <option value="1">In Progress</option>
          <option value="2">Complete</option>
          <option value="3">Rejected</option>
        </select>
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="crmInquiryDeletionRequestsTable" class="table table-bordered" data-use-server="1">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>User ID</th>
                    <th>Algorithm</th>
                    <th>Issued At</th>
                    <th>Expires At</th>
                    <th>Confirmation Code</th>
                    <th>Status</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div><!-- End Row-->
  </div>
</div>

<script src="assets/js/jquery.min.js"></script>
<script>
  var crmInquiryDeletionRequestsTable;

  function showToast(message, type) {
    if (window.toastr && toastr[type]) {
      toastr[type](message);
      return;
    }
    if (window.Swal && Swal.fire) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type,
        title: message,
        showConfirmButton: false,
        timer: 1500
      });
      return;
    }
    console.log('Toast:', type, message);
  }

  $(function() {
    function exportCellBody(data, row, column, node) {
      // Status cell contains a <select>; export only the selected option text.
      if (node && node.querySelector) {
        var selectEl = node.querySelector('select');
        if (selectEl) {
          if (selectEl.selectedIndex >= 0 && selectEl.options && selectEl.options[selectEl.selectedIndex]) {
            return selectEl.options[selectEl.selectedIndex].text;
          }
          return selectEl.value || '';
        }
      }
      // Fallback: parse HTML string (Buttons sometimes doesn't provide `node`).
      try {
        var $tmp = $('<div>').html(data);
        var $opt = $tmp.find('option[selected]').first();
        if ($opt && $opt.length) return $opt.text();
        var $sel2 = $tmp.find('select').first();
        var val2 = $sel2.find('option:selected').first().text();
        if (val2) return val2;
      } catch (e) {}

      // Strip HTML for safe exports.
      return $('<div>').html(data).text();
    }

    // Initialize DataTable with server-side processing
    if ($.fn.DataTable.isDataTable('#crmInquiryDeletionRequestsTable')) {
      crmInquiryDeletionRequestsTable = $('#crmInquiryDeletionRequestsTable').DataTable();
    } else {
      crmInquiryDeletionRequestsTable = $('#crmInquiryDeletionRequestsTable').DataTable({
        processing: true,
        serverSide: true,
        stateSave: false,
        ajax: {
          url: 'ajax/crmInquiryDeletionRequestsTable.php',
          type: 'POST',
          data: function(d) {
            d.status_filter = $('#crmInquiryStatusFilter').val() || 'all';
          }
        },
        colReorder: true,
        lengthChange: true,
        pageLength: 25,
        lengthMenu: [
          [25, 50, 100, 200, -1],
          [25, 50, 100, 200, "All"]
        ],
        buttons: [
          { extend: 'copy', exportOptions: { format: { body: exportCellBody } } },
          { extend: 'excel', exportOptions: { format: { body: exportCellBody } } },
          { extend: 'csv', exportOptions: { format: { body: exportCellBody } } },
          { extend: 'pdf', exportOptions: { format: { body: exportCellBody } } },
          'colvis'
        ],
        select: true,
        dom: 'Blfrtip',
        columnDefs: [{
            orderable: false,
            targets: [0]
          } // "#" column
        ]
      });
    }

    // Status update handler (delegated for dynamically rendered rows)
    $('body').on('change', '.crm-inquiry-status-select', function() {
      var $select = $(this);
      var rowId = $select.data('id');
      var newStatus = $select.val();
      var prevStatus = $select.data('current');

      if (!rowId) return;
      if (String(newStatus) === String(prevStatus)) return;

      $select.prop('disabled', true);

      $.post('ajax/updateCrmInquiryDeletionRequestStatus.php', {
        id: rowId,
        crm_inquiry_status: newStatus
      }).done(function(res) {
        if (!res || res.ok !== true) {
          $select.val(prevStatus);
          showToast((res && res.msg) ? res.msg : 'Failed to update status', 'error');
          $select.prop('disabled', false);
          return;
        }

        showToast(res.msg || 'Status updated successfully', 'success');
        $select.data('current', newStatus);
        if (crmInquiryDeletionRequestsTable) {
          crmInquiryDeletionRequestsTable.ajax.reload(null, false);
        }
      }).fail(function() {
        $select.val(prevStatus);
        showToast('Failed to update status', 'error');
        $select.prop('disabled', false);
      });
    });

    // Status filter change
    $('#crmInquiryStatusFilter').on('change', function() {
      if (crmInquiryDeletionRequestsTable) {
        crmInquiryDeletionRequestsTable.ajax.reload();
      }
    });
  });
</script>