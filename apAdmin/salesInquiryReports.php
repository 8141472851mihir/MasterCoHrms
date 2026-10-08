<?php
extract($_GET);
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-4">
        <h4 class="page-title">Company Not Found Salse Inquiry </h4>
      </div>
      <div class="col-sm-8">
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-12">
      <div class="card mx-2">
        <div class="card-body">
          <div class="table-responsive">
            <table id="salesInquiryReportsTable" class="table table-bordered">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Id</th>
                  <th>Name</th>
                  <th>Mobile</th>
                  <th>Company</th>
                  <th>Employees</th>
                  <th>Email</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Address</th>
                  <th>Date</th>
                  <th>Type</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="assets/js/jquery.min.js"></script>
<script>
$(document).ready(function() {

  reloadData();
});
function reloadData() {

  var reportTable = $('#salesInquiryReportsTable').DataTable({
    destroy: true, // Reinitialize safely
    lengthChange: false,
    buttons: ['copy', 'excel', 'pdf', 'csv', 'colvis'],
    select: true,
    bPaginate: false,
    processing: true,
    ajax: {
      url: "ajax/salesInquiryReportsTable.php",
      type: "POST",
      dataSrc: "",
      data: {
        csrf: "<?php echo $_SESSION['token']; ?>"
      },
    },
    columns: [
      { data: "sr_no" },
      { data: "id" },
      { data: "name" },
      { data: "mobile" },
      { data: "company" },
      { data: "employee" },
      { data: "email" },
      { data: "type" },
      { data: "status" },
      { data: "address" },
      { data: "date" },
      { data: "type" }
    ],
    initComplete: function () {

      // Add column search inputs in the footer
      $('#salesInquiryReportsTable tfoot th').each(function () {
        var $th = $(this);
        if (!$th.hasClass('selectAllNew')) {
          var title = $th.text();
          $th.html('<input class="form-control tableSearch" type="text" placeholder="Search ' + title + '" />');
        }
      });

      // Append buttons
      $('#salesInquiryReportsTable_wrapper').prepend(reportTable.buttons().container());

      // Bind search inputs
      reportTable.columns().every(function () {
        var that = this;
        $('input', this.footer()).on('keyup change', function () {
          if (that.search() !== this.value) {
            that.search(this.value).draw();
          }
        });
      });
    }
  });

}

</script>
