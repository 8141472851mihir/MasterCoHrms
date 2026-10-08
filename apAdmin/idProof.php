<?php
extract($_REQUEST);
$countryId = (isset($_GET['countryId']) && $_GET['countryId'] > 0) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId'], 101) : 101;
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-3">
        <h4 class="page-title">Manage ID Proof Types</h4>
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
                    <th>ID Proof Name</th>
                    <th>Country</th>
                    <th>Number Required</th>
                    <th>No of Pages</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->selectRow(
                    "id_proof_master.*,countries.name AS country_name",
                    "id_proof_master,countries",
                    "id_proof_master.country_id=countries.country_id AND id_proof_master.is_deleted = 0 AND id_proof_master.country_id='$countryId'",
                    "order by id_proof_master.id_proof_id DESC"
                  );

                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?= $id_proof_name ?></td>
                      <td><?= htmlspecialchars($country_name) ?></td>
                      <td><?= ($id_proof_number_required == 1) ? "Yes" : "No" ?></td>
                      <td><?= $id_proof_pages ?></td>
                      <td>
                        <?php
                        if (isset($dragpanel) && $dragpanel == 1) { ?>
                          <button class="btn mr-1 btn-sm btn-primary waves-effect waves-light earning-drag-btn">
                            <i class="fa fa-sort" aria-hidden="true"></i> drag button
                          </button>
                        <?php } else {
                          $buttonClass = ($active_status == "0") ? 'btn-success-new' : 'btn-danger';
                          $buttonCondition = ($active_status == "0") ? 'Active' : 'Deactive';
                          $posText = 'Active';
                          $negText = 'Deactive';
                          $status = ($active_status == "0") ? 'IdProofStatusDeactive' : 'IdProofStatusActive';
                          $newStatus = ($active_status == "0") ? 'IdProofStatusActive' : 'IdProofStatusDeactive';
                          $newStatusVal = ($active_status == "0") ? '1' : '0';
                          $statusValue = ($active_status == "0") ? '0' : '1';
                          ?>
                          <input type="button" class="btn btn-sm pl-1 pr-1 <?php echo $buttonClass ?>"
                            id="<?php echo 'id_proof_id_' . $id_proof_id; ?>"
                            onclick="changeStatusNew('<?php echo $id_proof_id; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'id_proof_id_' . $id_proof_id; ?>','','','./controller/statusController.php','<?php echo $negText; ?>','<?php echo $posText; ?>');"
                            data-size="small" value="<?php echo $buttonCondition ?>" />
                        <?php } ?>
                      </td>
                      <td>
                        <div class="row ml-1">
                          <button onclick="openEditModal(
                                          '<?= $id_proof_id ?>',
                                          '<?= htmlspecialchars($id_proof_name, ENT_QUOTES) ?>',
                                          '<?= htmlspecialchars($document_short_name, ENT_QUOTES) ?>',
                                          '<?= $id_proof_number_required ?>',
                                          '<?= $id_proof_pages ?>',
                                          '<?= $document_min_length ?>',
                                          '<?= $document_max_length ?>',
                                          '<?= $document_number_pattern ?>',
                                          '<?= $country_id ?>'
                          )" class="btn btn-sm btn-primary waves-effect waves-light m-1" type="button" title="Edit">
                            <i class="fa fa-pencil"></i>
                          </button>
                          <form action="controller/idProofController.php" method="post" style="display:inline;">
                            <input type="hidden" name="id_proof_id" value="<?= $id_proof_id ?>">
                            <input type="hidden" name="country_id" value="<?= $country_id ?>">
                            <input type="hidden" name="deleteIdProofType" value="deleteIdProofType">
                            <button type="submit" class="form-btn btn btn-sm btn-danger waves-effect waves-light m-1"
                              title="Delete">
                              <i class="fa fa-trash-o"></i>
                            </button>
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


<div class="modal fade" id="idProofModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="idProofModalTitle">Add ID Proof Type</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="idProofForm" action="controller/idProofController.php" method="post">

          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Country <span class="required">*</span></label>
              <?php
              $countryq = $d->selectRow("country_id,name", "countries", "flag='1'", "ORDER BY name ASC");
              ?>
              <select name="country_id" id="country_id" class="form-control single-select" required>
                <option value="">Select Country</option>
                <?php while ($countrydata = mysqli_fetch_array($countryq)) {
                  echo '<option value="' . $countrydata['country_id'] . '">' . htmlspecialchars($countrydata['name']) . '</option>';
                } ?>
              </select>
            </div>
            <div class="form-group col-md-4">
              <label>ID Proof Name <span class="required">*</span></label>
              <input type="text" class="form-control" autocomplete="off" name="id_proof_name" id="id_proof_name"
                required minlength="2" maxlength="40">
            </div>
            <div class="form-group col-md-4">
              <label>Document Short Name </label>
              <input type="text" class="form-control" autocomplete="off" name="document_short_name"
                id="document_short_name" minlength="2" maxlength="40">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Number Required <span class="required">*</span></label>
              <select name="id_proof_number_required" id="id_proof_number_required"
                onchange="documentfieldsshow(this.value)" class="form-control" required>
                <option value="1">Yes</option>
                <option value="0" selected>No</option>
              </select>
            </div>
            <div class="form-group col-md-4">
              <label>No of Pages <span class="required">*</span></label>
              <select name="id_proof_pages" id="id_proof_pages" class="form-control" required>
                <option value="1" selected>1</option>
                <option value="2">2</option>
              </select>
            </div>
          </div>

          <div class="form-row" id="documentExtraFields" style="display: none;">
            <div class="form-group col-md-4">
              <label>Document Max Length <span class="required">*</span></label>
              <input type="text" class="form-control onlyNumber" autocomplete="off" oninput="checkwithmaxvalues()"
                name="document_max_length" id="document_max_length" placeholder="Max Length">
            </div>
            <div class="form-group col-md-4">
              <label>Document Min Length <span class="required">*</span></label>
              <input type="text" class="form-control onlyNumber" autocomplete="off" oninput="checkwithmaxvalue()"
                name="document_min_length" id="document_min_length" placeholder="Min Length">
            </div>
            <div class="form-group col-md-4">
              <label>Document Number Pattern <span class="required">*</span></label>
              <select class="form-control" name="document_number_pattern" id="document_number_pattern">
                <option value="0">None</option>
                <option value="1">Numbers Only</option>
                <option value="2">Alphabets Only</option>
                <option value="3">Alphanumeric</option>
              </select>
            </div>
          </div>

          <div class="form-footer text-center">
            <input type="hidden" name="id_proof_id" id="id_proof_id">
            <input type="hidden" id="idProofActionType" name="" value="">
            <button type="submit" class="btn btn-primary" id="idProofFormSubmitBtn">
              <i class="fa fa-check-square-o"></i> Save
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>



<script>
  var filterCountryId = '<?= $countryId ?>';

  function openAddModal() {
    $('#idProofForm')[0].reset();
    $('#id_proof_id').val('');
    $('#idProofActionType').attr('name', 'addIdProofType').val('addIdProofType');
    $('#idProofModalTitle').text('Add ID Proof Type');
    $('#idProofFormSubmitBtn').html('<i class="fa fa-check-square-o"></i> Save');
    $('#country_id').val(filterCountryId || '101').trigger('change');
    documentfieldsshow(0);
    $('#idProofModal').modal('show');
  }

  function openEditModal(id_proof_id, id_proof_name, document_short_name, id_proof_number_required, id_proof_pages, document_min_length, document_max_length, document_number_pattern, country_id) {
    $('#idProofForm')[0].reset();
    $('#id_proof_id').val(id_proof_id);
    $('#id_proof_name').val(id_proof_name);
    $('#document_short_name').val(document_short_name);
    $('#id_proof_number_required').val(id_proof_number_required);
    $('#id_proof_pages').val(id_proof_pages);
    $('#document_min_length').val(document_min_length);
    $('#document_max_length').val(document_max_length);
    $('#document_number_pattern').val(document_number_pattern);
    $('#country_id').val(country_id).trigger('change');
    $('#idProofActionType').attr('name', 'editIdProofType').val('editIdProofType');
    $('#idProofModalTitle').text('Update ID Proof Type');
    $('#idProofFormSubmitBtn').html('<i class="fa fa-check-square-o"></i> Update');
    documentfieldsshow(id_proof_number_required, document_min_length, document_max_length, document_number_pattern);
    $('#idProofModal').modal('show');
  }


  function documentfieldsshow(val) {
    if (val == "1") {
      $('#documentExtraFields').show();
    } else {
      $('#documentExtraFields').hide();
      $('#document_min_length').val('');
      $('#document_max_length').val('');
      $('#document_number_pattern').val(0);
    }
  }



  const document_min_length = document.getElementById('document_min_length');

  document_min_length.addEventListener('paste', function (e) {
    e.preventDefault();
    swal("Pasting is not allowed!", {
      icon: "error",
      timer: 2000,
    });

  });
  const document_max_length = document.getElementById('document_max_length');
  document_max_length.addEventListener('paste', function (e) {
    e.preventDefault();
    swal("Pasting is not allowed!", {
      icon: "error",
      timer: 2000,
    });
  });
  function checkwithmaxvalue() {
    var document_max_length = $('#document_max_length').val();
    var document_min_length = $('#document_min_length').val();
    if (parseFloat(document_min_length) > parseFloat(document_max_length)) {
      swal("Document Minimum Length not greater than Document maximum length.", {
        icon: "error",
        timer: 3000,
      });
      $('#document_min_length').val('');
    }
  }
  function checkwithmaxvalues() {
    $('#document_min_length').val('');
  }


</script>
