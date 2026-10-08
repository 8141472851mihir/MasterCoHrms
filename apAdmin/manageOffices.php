<?php $token = $_SESSION['token']; ?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Manage Offices</h4>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="welcome">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Manage Offices</li>
        </ol>
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <a href="#addOffice" data-toggle="modal" data-target="#addOffice" class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add New</a>
          <a href="#" onclick="DeleteAll('deleteOffice');" class="btn btn-danger btn-sm waves-effect waves-light"><i class="fa fa-trash-o fa-lg"></i> Delete </a>
        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th class="sn-th">#</th>
                    <th>Office Type</th>
                    <th>Contact No 1</th>
                    <th>Contact No 2</th>
                    <th>Email 1</th>
                    <th>Email 2</th>
                    <th>Address</th>
                    <th>Order No</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->select("offices_master");
                  while ($row = $q->fetch_assoc()) {
                  ?>
                    <tr>
                      <td class='text-center delete-th'>
                        <input type="checkbox" class="multiDelteCheckbox" value="<?php echo $row['office_id'] ?>">
                      </td>
                      <td><?php echo $i++; ?></td>
                      <td class="tableWidth"><?php echo $row['office_type']; ?></td>
                      <td class="tableWidth"><?php echo "+" . $row['country_code_one'] . " " . $row['office_contact_one']; ?></td>
                      <td class="tableWidth"><?php echo ($row['office_contact_two'] != 0) ? "+" . $row['country_code_two'] . " " . $row['office_contact_two'] : ""; ?></td>
                      <td class="tableWidth"><?php echo $row['office_email_one']; ?></td>
                      <td class="tableWidth"><?php echo $row['office_email_two']; ?></td>
                      <td class="tableWidth"><?php echo $row['office_address']; ?></td>
                      <td class="tableWidth"><?php echo $row['order_no']; ?></td>
                      <td class="tableWidth">
                        <label class="switch-custom">
                          <?php
                          if ($row['status'] == 1) { ?>
                            <input type="checkbox"  data-color="#15ca20" data-size="small" onchange="changeStatus('<?php echo $row['office_id']; ?>','officeDeactive','<?php echo $token; ?>');" checked />
                          <?php
                          } else {
                          ?>
                            <input type="checkbox"  data-color="#15ca20" data-size="small" onchange="changeStatus('<?php echo $row['office_id']; ?>','officeActive','<?php echo $token; ?>');" />
                          <?php } ?>
                          <span class="slider-custom round"></span>
                        </label>
                      </td>
                      <td>
                        <!-- <button name="editOffice" class="btn btn-sm btn-primary" onclick="editOffice(<?php echo $row['office_id'] ?>)"> <i class="fa fa-pencil"></i> </button> -->
                        <button name="editOffice" class="btn btn-sm btn-primary" onclick="editOffice(<?php echo $row['office_id']; ?>, '<?php echo $row['office_type']; ?>', '<?php echo $row['office_contact_one']; ?>', '<?php echo $row['office_contact_two']; ?>', '<?php echo $row['office_email_one']; ?>', '<?php echo $row['office_email_two']; ?>', '<?php echo $row['order_no']; ?>', '<?php echo $row['office_address']; ?>', '<?php echo $row['country_code_one']; ?>', '<?php echo $row['country_code_two']; ?>')"><i class="fa fa-pencil"></i></button>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th class="hideSearch">#</th>
                    <th class="hideSearch">#</th>
                    <th>Office Type</th>
                    <th>Contact No 1</th>
                    <th>Contact No 2</th>
                    <th>Email 1</th>
                    <th>Email 2</th>
                    <th>Address</th>
                    <th>Order No</th>
                    <th class="hideSearch">#</th>
                    <th class="hideSearch">#</th>
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

<div class="modal fade" id="addOffice">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Add Office</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container">
          <form id="addOfficeForm" action="controller/officeController.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="redirectURL" value="manageOffices">
            <div class="form-group row">
              <div class="col-sm-6 custom-col">
                <label for="office_type" class="col-form-label">Office Type <span class="required">*</span></label>
                <input type="text" maxlength="100" autocomplete="off" name="office_type" id="office_type" class="form-control" required>
              </div>
              <div class="col-sm-6 custom-col">
                <label for="office_contact_one" class="col-form-label">Contact 1 <span class="required">*</span></label>
                <div class="row">
                  <div class="col-sm-5 custom-col">
                    <select class="form-control single-select" required id="country_code_one" name="country_code_one">
                      <?php
                      $q = $d->select("country_code");
                      while ($row = $q->fetch_assoc()) {
                      ?>
                        <option value="<?php echo $row['country_code']; ?>" <?php if ($row['country_code'] == 91) {
                                                                              echo "selected";
                                                                            } ?>>+ <?php echo $row['country_code'] . " " . $row['country_name']; ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                  <div class="col-sm-7 custom-col">
                    <input type="text" autocomplete="off" name="office_contact_one" id="office_contact_one" class="form-control onlyNumber" required>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-6 custom-col">
                <label for="office_contact_two" class="col-form-label">Contact 2 </label>
                <div class="row">
                  <div class="col-sm-5 custom-col">
                    <select class="form-control single-select" id="country_code_two" name="country_code_two">
                      <option>--SELECT--</option>
                      <?php
                      $q = $d->select("country_code");
                      while ($row = $q->fetch_assoc()) {
                      ?>
                        <option value="<?php echo $row['country_code']; ?>" <?php if ($row['country_code'] == 91) {
                                                                              echo "selected";
                                                                            } ?>>+ <?php echo $row['country_code'] . " " . $row['country_name']; ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                  <div class="col-sm-7 custom-col">
                    <input type="text" autocomplete="off" name="office_contact_two" id="office_contact_two" class="form-control onlyNumber">
                  </div>
                </div>
              </div>
              <div class="col-sm-6 custom-col">
                <label for="office_email_one" class="col-form-label">Email 1 <span class="required">*</span></label>
                <input type="email" maxlength="250" autocomplete="off" name="office_email_one" id="office_email_one" class="form-control" required>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-6 custom-col">
                <label for="office_email_two" class="col-form-label">Email 2</label>
                <input type="email" maxlength="250" autocomplete="off" name="office_email_two" id="office_email_two" class="form-control">
              </div>
              <div class="col-sm-6 custom-col">
                <label for="office_address" class="col-form-label">Order No <span class="required">*</span></label>
                <input type="number" name="order_no" autocomplete="off" id="order_no" class="form-control" required>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-12 custom-col">
                <label for="office_address" class="col-form-label">Address <span class="required">*</span></label>
                <textarea name="office_address" autocomplete="off" maxlength="255" rows="2" id="office_address" class="form-control" required></textarea>
              </div>
            </div>
            <div class="form-footer text-center">
              <input type="hidden" name="csrf" value="<?php echo $token; ?>">
              <input type="hidden" name="addOffice" value="addOffice">
              <button type="submit" name="addOffice" class="btn btn-success"><i class="fa fa-check-square-o"></i> Add</button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="editOffice">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Edit Office Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container">
          <form id="editOfficeForm" action="controller/officeController.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="redirectURL" value="manageOffices">
            <div class="form-group row">
              <div class="col-sm-6 custom-col">
                <label for="office_type" class="col-form-label">Office Type <span class="required">*</span></label>
                <input type="text" maxlength="50" autocomplete="off" name="office_type" id="office_type_edit" class="form-control" required>
              </div>
              <div class="col-sm-6 custom-col">
                <label for="office_contact_one" class="col-form-label">Contact 1 <span class="required">*</span></label>
                <div class="row">
                  <div class="col-sm-5 custom-col">
                    <select class="form-control single-select" required id="country_code_one_edit" name="country_code_one">
                      <?php
                      $q = $d->select("country_code");
                      while ($row = $q->fetch_assoc()) {
                      ?>
                        <option value="<?php echo $row['country_code']; ?>">+ <?php echo $row['country_code'] . " " . $row['country_name']; ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                  <div class="col-sm-7 custom-col">
                    <input type="text" autocomplete="off" name="office_contact_one" id="office_contact_one_edit" class="form-control onlyNumber" required>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-6 custom-col">
                <label for="office_contact_two" class="col-form-label">Contact 2 </label>
                <div class="row">
                  <div class="col-sm-5 custom-col">
                    <select class="form-control single-select" id="country_code_two_edit" name="country_code_two">
                      <option>--SELECT--</option>
                      <?php
                      $q = $d->select("country_code");
                      while ($row = $q->fetch_assoc()) {
                      ?>
                        <option value="<?php echo $row['country_code']; ?>">+ <?php echo $row['country_code'] . " " . $row['country_name']; ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                  <div class="col-sm-7 custom-col">
                    <input type="text" autocomplete="off" name="office_contact_two" id="office_contact_two_edit" class="form-control onlyNumber">
                  </div>
                </div>
              </div>
              <div class="col-sm-6 custom-col">
                <label for="office_email_one" class="col-form-label">Email 1 <span class="required">*</span></label>
                <input type="email" maxlength="250" autocomplete="off" name="office_email_one" id="office_email_one_edit" class="form-control" required>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-6 custom-col">
                <label for="office_email_two" class="col-form-label">Email 2</label>
                <input type="email" maxlength="250" autocomplete="off" name="office_email_two" id="office_email_two_edit" class="form-control">
              </div>
              <div class="col-sm-6 custom-col">
                <label for="office_address" class="col-form-label">Order No <span class="required">*</span></label>
                <input type="number" name="order_no" autocomplete="off" id="order_no_edit" class="form-control" required>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-12 custom-col">
                <label for="office_address" class="col-form-label">Address <span class="required">*</span></label>
                <textarea name="office_address" maxlength="255" autocomplete="off" rows="3" id="office_address_edit" class="form-control" required></textarea>
              </div>
            </div>
            <div class="form-footer text-center">
              <input type="hidden" name="csrf" value="<?php echo $token; ?>">
              <input type="hidden" name="office_id" id="office_id">
              <input type="hidden" name="editOffice" value="editOffice">
              <button type="submit" name="editOffice" class="btn btn-success"><i class="fa fa-check-square-o"></i> Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  function editOffice(office_id, office_type, office_contact_one, office_contact_two, office_email_one, office_email_two, order_no, office_address, country_code_one, country_code_two) {
    document.getElementById("office_id").value = office_id;
    document.getElementById("office_type_edit").value = office_type;
    if (office_contact_one != "0") {
      document.getElementById("office_contact_one_edit").value = office_contact_one;
    }
    if (office_contact_two != "0") {
      document.getElementById("office_contact_two_edit").value = office_contact_two;
    }
    document.getElementById("office_email_one_edit").value = office_email_one;
    document.getElementById("office_email_two_edit").value = office_email_two;
    document.getElementById("order_no_edit").value = order_no;
    document.getElementById("office_address_edit").value = office_address;
    if (country_code_one != "" && country_code_one != "0" && office_contact_one != "0") {
      $('#country_code_one_edit').val(country_code_one).trigger('change');
    }
    if (country_code_two != "" && country_code_two != "0" && office_contact_two != "0") {
      $('#country_code_two_edit').val(country_code_two).trigger('change');
    }
    $('#editOffice').modal('show');
  }
</script>