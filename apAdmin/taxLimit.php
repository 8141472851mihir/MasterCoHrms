<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Manage Tax Limit</h4>
      </div>
      <div class="col-sm-3 col-md-3 col-6">
        <div class="float-sm-right">
          <button onclick="openAddModal()" class="btn btn-sm btn-primary waves-effect waves-light m-1"><i
              class="fa fa-plus"></i> Add Tax Limit</button>
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
                    <th>Tax Year</th>
                    <th>New Regime Amount</th>
                    <th>Old Regime Amount</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->selectRow("tax_limit_master.*", "tax_limit_master", "", "ORDER BY tax_limit_master.tax_limit_id DESC");

                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?= $tax_year ?></td>
                      <td><?= $new_regime_amount ?></td>
                      <td><?= $old_regime_amount ?></td>
                      <td>
                        <div class="row ml-1">
                          <button onclick="openEditModal(
                          '<?= $tax_limit_id ?>',
                          '<?= $tax_year ?>',
                          '<?= $new_regime_amount ?>',
                          '<?= $old_regime_amount ?>'
                        )" class="btn btn-sm btn-primary waves-effect waves-light m-1">
                            <i class="fa fa-pencil"></i>
                          </button>

                          <form action="controller/taxLimitController.php" method="post">
                            <input type="hidden" name="tax_limit_id" value="<?= $tax_limit_id ?>">
                            <input type="hidden" name="deleteTaxLimit" id="deleteTaxLimit" value="deleteTaxLimit">
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

<?php
$currentMonth = date('n');
$currentYearStart = ($currentMonth >= 4) ? date('Y') : date('Y', strtotime('-1 year'));

$yearOptions = [];
for ($i = 0; $i < 3; $i++) {
  $startYear = (int) $currentYearStart + $i;
  $endYear = $startYear + 1;
  $yearOptions[] = $startYear . '-' . $endYear;
}
?>

<div class="modal fade" id="taxLimitModalShow" tabindex="-1" aria-labelledby="taxLimitModel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title text-white" id="taxLimitModel">Tax Limit</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="taxLimitForm" action="controller/taxLimitController.php" method="post">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Tax Year<span class="required">*</span></label>
                <select name="tax_year" id="tax_year" class="form-control single-select" required>
                  <?php foreach ($yearOptions as $option): ?>
                    <option value="<?= $option ?>"><?= $option ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>New Regime Amount <span class="required">*</span></label>
                <input type="text" autocomplete="off" name="new_regime_amount" id="new_regime_amount"
                  class="form-control onlyNumber" minlength="2" maxlength="30" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="old_regime_amount">Old Regime Amount <span class="required">*</span></label>
                <input type="text" autocomplete="off" minlength="2" maxlength="30" name="old_regime_amount" id="old_regime_amount"
                  class="form-control onlyNumber" required>
              </div>
            </div>
          </div>

          <div class="form-footer text-center">
            <input type="hidden" name="tax_limit_id" id="tax_limit_id">
            <input type="hidden" id="taxLimitActionType" name="actionType" value="">
            <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function openAddModal() {
    $('#taxLimitForm')[0].reset();
    $('#tax_limit_id').val('');
    $('#taxLimitActionType').attr('name', 'addTaxLimit').val('addTaxLimit');
    $('#taxLimitModel').text('Add Tax Limit');
    $('#taxLimitModalShow').modal('show');
  }

function openEditModal(id, year, newRegimeAmount, oldRegimeAmount) {
  $('#taxLimitForm')[0].reset();
  $('#taxLimitForm').find('.is-invalid').removeClass('is-invalid');
  $('#taxLimitForm').find('.error').remove();

  $('#tax_limit_id').val(id);
  $('#new_regime_amount').val(newRegimeAmount);
  $('#old_regime_amount').val(oldRegimeAmount);
  $('#taxLimitActionType').attr('name', 'editTaxLimit').val('editTaxLimit');
  $('#taxLimitModel').text('Edit Tax Limit');

  const $yearSelect = $('#tax_year');
  const trimmedYear = year.trim();
  if ($yearSelect.find(`option[value="${trimmedYear}"]`).length === 0) {
    $yearSelect.append(`<option value="${trimmedYear}">${trimmedYear}</option>`);
  }
  $yearSelect.val(trimmedYear).trigger('change');

  $('#taxLimitModalShow').modal('show');
}

</script>