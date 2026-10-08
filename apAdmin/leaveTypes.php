<?php
extract($_REQUEST);
$countryId = (isset($_GET['countryId']) && $_GET['countryId'] > 0) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId'], 101) : 101;
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-3">
        <h4 class="page-title">Manage Leave Types</h4>
      </div>
      <div class="col-lg-6">
        <form action="" method="get" accept-charset="utf-8">
          <div class="form-group row">
            <div class="col-sm-6">
              <select id="filter_country_id" onchange="this.form.submit()" class="form-control single-select" name="countryId">
                <?php
                $qc = $d->select("countries", "flag=1", "ORDER BY name ASC");
                while ($cData = mysqli_fetch_array($qc)) {
                  $selected = ($cData['country_id'] == $countryId) ? 'selected' : '';
                  echo '<option ' . $selected . ' value="' . $cData['country_id'] . '">' . htmlspecialchars($cData['name']) . '</option>';
                }
                ?>
              </select>
            </div>
          </div>
        </form>
      </div>

      <div class="col-sm-3 col-md-3 col-6">
        <div class="float-sm-right">
          <a href="syncSlabs?sync=Leave" class="btn mr-1 btn-sm btn-danger waves-effect waves-light"><i
              class="fa fa-plus mr-1"></i>Sync Leaves</a>
          <button onclick="openAddModal()" class="btn btn-sm btn-primary waves-effect waves-light m-1"><i
              class="fa fa-plus"></i> Add</button>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Leave Type Name</th>
                    <th>Leave Type Short Name</th>
                    <th>Country</th>
                    <th>Applicable For</th>
                    <th>No. of Leaves</th>
                    <th>Sandwich Leave Applicable</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->selectRow("leave_types_master.*,countries.name AS country_name", "leave_types_master,countries", "leave_types_master.country_id=countries.country_id AND leave_types_master.country_id='$countryId'", "order by leave_types_master.leave_type_id DESC");

                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?= $leave_type_name ?></td>
                      <td><?= $leave_type_short_name ?></td>
                      <td><?= htmlspecialchars($country_name) ?></td>
                      <?php
                      if ($leave_for == 0) {
                        $applicable_for = "All";
                      } elseif ($leave_for == 1) {
                        $applicable_for = "Male Only";
                      } else if ($leave_for == 2) {
                        $applicable_for = "Female Only";
                      } else if ($leave_for == 3) {
                        $applicable_for = "Married Only";
                      } else if ($leave_for == 4) {
                        $applicable_for = "Un-Married Only";
                      } else if ($leave_for == 5) {
                        $applicable_for = "Married Female Only";
                      } else if ($leave_for == 6) {
                        $applicable_for = "Married Male Only";
                      } else {
                        $applicable_for = "-";
                      }
                      ?>
                      <td><?= $applicable_for ?></td>
                      <td><?= ($no_of_leaves != "") ? $no_of_leaves : "-" ?></td>
                      <td> <?php if($sandwich_leave_applicable==1) { echo 'Yes'; } else { echo 'No';} ?>  </td>
                      <td>

                        <?php if ($leave_type_status == 1) { ?>
                          <form action="controller/statusController.php" method="post">
                            <input type="hidden" name="leave_type_id" value="<?= $leave_type_id ?>">
                            <input type="hidden" name="leave_type_status" value="0">
                            <input type="hidden" name="leave_type_name" value="<?= $leave_type_name ?>">
                            <input type="hidden" name="Status" value="leaveTypeStatus">
                            <button type="submit" class="form-btn btn btn-sm btn-danger waves-effect waves-light m-1"
                              title="Update Status">Deactive</button>
                          </form>
                        <?php } elseif ($leave_type_status == 0) { ?>
                          <form action="controller/statusController.php" method="post">
                            <input type="hidden" name="leave_type_id" value="<?= $leave_type_id ?>">
                            <input type="hidden" name="leave_type_status" value="1">
                            <input type="hidden" name="leave_type_name" value="<?= $leave_type_name ?>">
                            <input type="hidden" name="Status" value="leaveTypeStatus">
                            <button style="background-color: green;" type="submit"
                              class="form-btn btn btn-sm btn-info waves-effect waves-light m-1"
                              title="Update Status">Active</button>
                          </form>
                        <?php } ?>
                      </td>
                      <td>
                        <div class="row ml-1">
                          <button
                            onclick="openEditModal('<?= $leave_type_id ?>','<?= $leave_type_name ?>','<?= $country_id ?>','<?= $leave_for ?>','<?= $no_of_leaves ?>','<?= $leave_apply_on_date ?>','<?= $leave_type_short_name ?>','<?= $sandwich_leave_applicable ?>')"
                            class="btn btn-sm btn-primary waves-effect waves-light m-1"><i
                              class="fa fa-pencil"></i></button>
                          <form action="controller/leaveController.php" method="post">
                            <input type="hidden" name="leave_type_id" value="<?= $leave_type_id ?>">
                            <input type="hidden" name="leave_type_name" value="<?= $leave_type_name ?>">
                            <input type="hidden" name="country_id" value="<?= $country_id ?>">
                            <input type="hidden" name="deleteLeaveType" id="deleteLeaveType" value="deleteLeaveType">
                            <button type="submit" class="form-btn btn btn-sm btn-danger waves-effect waves-light m-1"
                              title="Delete"> <i class="fa fa-trash-o"></i> </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="leaveTypeModal">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="leaveTypeModalTitle">Add Leave Type</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="leaveTypeForm" action="controller/leaveController.php" method="post">
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Country <span class="required">*</span></label>
            <div class="col-sm-8">
              <?php
              $countryq = $d->selectRow("country_id,name", "countries", "flag='1'", "Order By name ASC");
              ?>
              <select name="country_id" id="country_id" class="form-control single-select" required>
                <option value="">Select Country</option>
                <?php while ($countrydata = mysqli_fetch_array($countryq)) {
                  echo '<option value="' . $countrydata['country_id'] . '">' . $countrydata['name'] . '</option>';
                } ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Leave Type Name <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control" autocomplete="off" name="leave_type_name" id="leave_type_name"
                required minlength="2" maxlength="40">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Leave Type Short Name</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" autocomplete="off" name="leave_type_short_name" id="leave_type_short_name">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Applicable For <span class="required">*</span></label>
            <div class="col-sm-8">
              <select name="leave_for" id="leave_for" class="form-control single-select" required>
                <option value="">Select Applicable For</option>
                <option value="0">All</option>
                <option value="1">Male Only</option>
                <option value="2">Female Only</option>
                <option value="3">Married Only</option>
                <option value="4">Un-Married Only</option>
                <option value="5">Married Female Only</option>
                <option value="6">Married Male Only</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">No. of Leaves <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" class="form-control onlyNumber" autocomplete="off" name="no_of_leaves"
                id="no_of_leaves">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Sandwich Leave Applicable <span class="required">*</span></label>
            <div class="col-sm-8">
              <select name="sandwich_leave_applicable" id="sandwich_leave_applicable" class="form-control single-select" required>
                <option value="1">Yes</option>
                <option value="0">No</option>
              </select>
            </div>
          </div>

          <div class="form-group d-flex align-items-center">
            <label class="mb-1 font-weight-bold" style="width: 250px; margin-left: -10px;">
              Is Birthday/Anniversary Leave<span class="required">*</span>
            </label>

            <div class="form-check form-check-inline mb-0">
              <input class="form-check-input" type="radio" name="leave_apply_on_date" id="leaveNo" value="0" checked>
              <label class="form-check-label" for="leaveNo">No</label>
            </div>
            <div class="form-check form-check-inline mb-0 ms-3">
              <input class="form-check-input" type="radio" name="leave_apply_on_date" id="leaveYes" value="1">
              <label class="form-check-label" for="leaveYes">Yes</label>
            </div>
          </div>

          <div class="form-footer text-center">
            <input type="hidden" name="leave_type_id" id="leave_type_id">
            <input type="hidden" id="leaveActionType" name="" value="">

            <button type="submit" class="btn btn-primary" id="formSubmitBtn"><i class="fa fa-check-square-o"></i>
              Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
  var filterCountryId = '<?= $countryId ?>';

  function openAddModal() {
    $('#leaveTypeForm')[0].reset();
    $('#leave_type_id').val('');
    $('#leaveActionType').attr('name', 'addLeaveType').val('addLeaveType');
    $('#leaveTypeModalTitle').text('Add Leave Type');
    $('#formSubmitBtn').html('<i class="fa fa-check-square-o"></i> Save');
    $('#country_id').val(filterCountryId || '101').trigger('change');
    $('#leave_for').val('').trigger('change');
    $('#sandwich_leave_applicable').val('1').trigger('change');
    $('#leaveNo').prop('checked', true);
    $('#leave_type_short_name').val(''); // Reset short name
    $('#leaveTypeModal').modal('show');
  }

  function openEditModal(id, name, country_id, leave_for, leaves, leave_apply_on_date, short_name,sandwich_leave) {
    $('#leave_type_id').val(id);
    $('#leave_type_name').val(name);
    $('#country_id').val(country_id).trigger('change');
    $('#leave_for').val(leave_for).trigger('change');
    $('#sandwich_leave_applicable').val(sandwich_leave).trigger('change');
    $('#no_of_leaves').val(leaves);
    $('#leaveActionType').attr('name', 'editLeaveType').val('editLeaveType');
    $('#leaveTypeModalTitle').text('Update Leave Type');
    $('#formSubmitBtn').html('<i class="fa fa-check-square-o"></i> Update');
    if (leave_apply_on_date == '1') {
      $('#leaveYes').prop('checked', true);
    } else {
      $('#leaveNo').prop('checked', true);
    }
    $('#leave_type_short_name').val(short_name); // Set short name
    $('#leaveTypeModal').modal('show');
  }
</script>