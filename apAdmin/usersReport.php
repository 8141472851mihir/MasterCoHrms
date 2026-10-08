<?php
include_once 'common/object.php';

$countryId = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId']) : 0;
$stateId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
$cityId = isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : 0;
$societyId = isset($_GET['society_id']) ? $d->sanitizeReportFilterIdAsInt($_GET['society_id']) : 0;
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-12">
        <h4 class="page-title">Users Report</h4>
      </div>
    </div>

    <form action="" method="get" accept-charset="utf-8" id="filterForm">
      <div class="row align-items-end mb-3">
        <div class="form-group col-md-2">
          <label for="country_id" class="col-form-label">Country</label>
          <select id="country_id" class="form-control single-select" name="countryId" onchange="this.form.submit()">
            <option value="" <?php echo ($countryId === 0) ? 'selected' : ''; ?>>All</option>
            <?php
            $qc = $d->select("countries", "flag=1", "ORDER BY name ASC");
            while ($cData = mysqli_fetch_assoc($qc)) {
              $selected = ($countryId > 0 && $cData['country_id'] == $countryId) ? "selected" : "";
              echo "<option value='" . (int)$cData['country_id'] . "' $selected>" . htmlspecialchars($cData['name']) . "</option>";
            }
            ?>
          </select>
        </div>
        <div class="form-group col-md-2">
          <label for="state_id" class="col-form-label">State</label>
          <select class="form-control single-select" id="state_id" name="sId" onchange="this.form.submit()">
            <option value="" <?php echo ($stateId === 0) ? 'selected' : ''; ?>>All</option>
            <?php if ($countryId > 0) {
              $qs = $d->select("states", "country_id=$countryId", "ORDER BY name ASC");
              while ($sData = mysqli_fetch_assoc($qs)) {
                $selected = ($stateId > 0 && $sData['state_id'] == $stateId) ? "selected" : "";
                echo "<option value='" . (int)$sData['state_id'] . "' $selected>" . htmlspecialchars($sData['name']) . "</option>";
              }
            } ?>
          </select>
        </div>
        <div class="form-group col-md-2">
          <label for="city_id" class="col-form-label">City</label>
          <select class="form-control single-select" id="city_id" name="cId" onchange="this.form.submit()">
            <option value="" <?php echo ($cityId === 0) ? 'selected' : ''; ?>>All</option>
            <?php if ($stateId > 0) {
              $qcity = $d->select("cities", "state_id=$stateId", "ORDER BY name ASC");
              while ($cityData = mysqli_fetch_assoc($qcity)) {
                $selected = ($cityId > 0 && $cityData['city_id'] == $cityId) ? "selected" : "";
                echo "<option value='" . (int)$cityData['city_id'] . "' $selected>" . htmlspecialchars($cityData['name']) . "</option>";
              }
            } ?>
          </select>
        </div>
        <div class="form-group col-md-3">
          <label for="society_id" class="col-form-label">Company</label>
          <select class="form-control single-select" id="society_id" name="society_id" onchange="this.form.submit()">
            <option value="" <?php echo ($societyId === 0) ? 'selected' : ''; ?>>All</option>
            <?php
            $socWhere = "1=1";
            if ($countryId > 0) $socWhere .= " AND country_id='$countryId'";
            if ($stateId > 0) $socWhere .= " AND state_id='$stateId'";
            if ($cityId > 0) $socWhere .= " AND city_id='$cityId'";
            $qsoc = $d->select("society_master", $socWhere, "ORDER BY society_name ASC");
            while ($socData = mysqli_fetch_assoc($qsoc)) {
              $selected = ($societyId > 0 && $socData['society_id'] == $societyId) ? "selected" : "";
              echo "<option value='" . (int)$socData['society_id'] . "' $selected>" . htmlspecialchars($socData['society_name']) . "</option>";
            }
            ?>
          </select>
        </div>
      </div>
    </form>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
            <a href="#" id="btnExportFullReport" class="btn btn-success btn-sm ml-1 float-right" title="Download all users (respects current filters)">
            <i class="fa fa-file-excel-o"></i> Download full report
          </a>
              <table id="usersReportTable" class="table table-bordered" data-use-server="1">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Company Id</th>
                    <th>Company Name</th>
                    <th>Country</th>
                    <th>State</th>
                    <th>City</th>
                    <th>User Name</th>
                    <th>Phone</th>
                    <th>Designation</th>
                    <th>Branch</th>
                    <th>Department</th>
                    <th>Created Date</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Data loaded via AJAX -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script>
$(document).ready(function() {
  var $table = $('#usersReportTable');
  if (!$table.length) return;
  if ($.fn.DataTable.isDataTable('#usersReportTable')) {
    $table.DataTable().destroy();
    $table.find('tbody').empty();
  }
  var reportTable = $table.DataTable({
    processing: true,
    serverSide: true,
    stateSave: true,
    ajax: {
      url: 'ajax/usersReportTable.php',
      type: 'POST',
      data: function(d) {
        var formArray = $('#filterForm').serializeArray();
        formArray.forEach(function(item) {
          d[item.name] = item.value;
        });
      }
    },
    order: [[0, 'asc']],
    lengthChange: true,
    pageLength: 25,
    lengthMenu: [[25, 50, 100, 200, 500, 1000], [25, 50, 100, 200, 500, 1000]],
    buttons: ['copy', 'excel', 'pdf', 'csv', 'colvis'],
    dom: 'Blfrtip',
    columnDefs: [
      { orderable: false, targets: 0 }
    ]
  });
  if (reportTable.buttons && $('#usersReportTable_wrapper').length) {
    $('#usersReportTable_wrapper').prepend(reportTable.buttons().container());
  }

  $('#btnExportFullReport').on('click', function(e) {
    e.preventDefault();
    var params = $('#filterForm').serialize();
    var url = 'ajax/usersReportExport.php' + (params ? '?' + params : '');
    window.location.href = url;
  });
});
</script>
