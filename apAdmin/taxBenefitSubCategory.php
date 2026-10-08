<?php
extract($_REQUEST);
?>
<?php
if (date('m') >= 4) {
  $currentYear = date('Y') . '-' . date('Y', strtotime('+1 year'));
  $previousYear = date('Y', strtotime('-1 year')) . '-' . date('Y');
  $nextYear = date('Y', strtotime('+1 year')) . '-' . date('Y', strtotime('+2 year'));
} else {
  $currentYear = date('Y', strtotime('-1 year')) . '-' . date('Y');
  $previousYear = date('Y', strtotime('-2 year')) . '-' . date('Y', strtotime('-1 year'));
  $nextYear = date('Y') . '-' . date('Y', strtotime('+1 year'));
}
$year = $d->sanitizeReportFilterFiscalYear(isset($_REQUEST['year']) ? $_REQUEST['year'] : '', $currentYear);

$yearArray = explode('-', $year);
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-3 col-md-5 col-4">
        <h4 class="page-title">Manage Tax Benefit Sub Category</h4>
      </div>

      <div class="col-sm-3 col-md-3 col-4">
        <form action="" method="get">
          <select name="year" class="form-control single-select" onchange="this.form.submit();">
            <option <?php echo ($year == $previousYear) ? 'selected' : ''; ?> value="<?php echo $previousYear ?>">
              <?php echo $previousYear ?></option>
            <option <?php echo ($year == $currentYear) ? 'selected' : ''; ?> value="<?php echo $currentYear ?>">
              <?php echo $currentYear ?></option>
            <option <?php echo ($year == $nextYear) ? 'selected' : ''; ?> value="<?php echo $nextYear ?>">
              <?php echo $nextYear ?></option>
          </select>
        </form>
      </div>
      <div class="col-sm-3 col-md-4 col-4">
        <div class="float-sm-right">
          <button onclick="openAddModal()" class="btn btn-sm btn-primary waves-effect waves-light m-1"><i
              class="fa fa-plus"></i> Add Tax Benefit Sub Category</button>
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
                    <th>Tax Benefit Sub Category Name</th>
                    <th>Tax Benefit Category Name</th>
                    <th>Tax Benefit Percent</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->selectRow("tax_benefit_sub_category.*, tax_benefit_category.tax_benefit_category_name,tax_benefit_category.tax_benefit_year", "tax_benefit_sub_category LEFT JOIN tax_benefit_category ON tax_benefit_sub_category.tax_benefit_category_id = tax_benefit_category.tax_benefit_category_id", "tax_benefit_category.tax_benefit_year='$year'", "ORDER BY tax_benefit_sub_category.tax_benefit_sub_category_id DESC");

                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?= $tax_benefit_sub_category_name ?></td>
                      <td><?= $tax_benefit_category_name ?></td>
                      <td><?= $benefit_percent ?></td>
                      <td>
                        <div class="row ml-1">
                          <button onclick="openEditModal(
                          '<?= $tax_benefit_sub_category_id ?>',
                          '<?= $tax_benefit_sub_category_name ?>',
                          '<?= $tax_benefit_category_id ?>',
                          '<?= $benefit_percent ?>'
                        )" class="btn btn-sm btn-primary waves-effect waves-light m-1">
                            <i class="fa fa-pencil"></i>
                          </button>

                          <form action="controller/taxBenefitSubCategoryController.php" method="post">
                            <input type="hidden" name="tax_benefit_sub_category_id"
                              value="<?= $tax_benefit_sub_category_id ?>">
                            <input type="hidden" name="tax_benefit_sub_category_name"
                              value="<?= $tax_benefit_sub_category_name ?>">
                            <input type="hidden" name="deleteTaxBenefitSubCategory" id="deleteTaxBenefitSubCategory"
                              value="deleteTaxBenefitSubCategory">
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

<div class="modal fade" id="taxBenefitModalSubCategory" tabindex="-1" aria-labelledby="taxBenefitModalSubCategoryLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title text-white" id="taxBenefitModalSubCategoryLabel">Tax Benefit Sub Category</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="taxBenefitSubCategoryForm" action="controller/taxBenefitSubCategoryController.php" method="post">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Tax Benefit Sub Category Name <span class="required">*</span></label>
                <input type="text" autocomplete="off" name="tax_benefit_sub_category_name"
                  id="tax_benefit_sub_category_name" class="form-control" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Tax Benefit Category Name <span class="required">*</span></label>
                <select name="tax_benefit_category_id" id="tax_benefit_category_id" class="form-control single-select"
                  required>
                  <option value="">Select</option>
                  <?php
                  $res = $d->select("tax_benefit_category", "");
                  while ($r = mysqli_fetch_array($res)) {
                    echo '<option value="' . $r['tax_benefit_category_id'] . '">' . $r['tax_benefit_category_name'] . '</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="benefit_percent">Tax Benefit Percent</label>
                <input type="text" autocomplete="off" name="benefit_percent" id="benefit_percent"
                  class="form-control onlyNumber">
              </div>
            </div>
          </div>

          <div class="form-footer text-center">
            <input type="hidden" name="tax_benefit_sub_category_id" id="tax_benefit_sub_category_id">
            <input type="hidden" id="taxBenefitSubCategoryActionType" name="actionType" value="">
            <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function openAddModal() {
    $('#taxBenefitSubCategoryForm')[0].reset();
    $('#tax_benefit_sub_category_id').val('');
    $('#taxBenefitSubCategoryActionType').attr('name', 'addTaxBenefitSubCategory').val('addTaxBenefitSubCategory');
    $('#taxBenefitModalSubCategoryLabel').text('Add Tax Benefit Sub Category');
    $('#taxBenefitModalSubCategory').modal('show');
  }

  function openEditModal(id, name, catId, benefitPercent) {
    $('#taxBenefitSubCategoryForm')[0].reset();
    $('#taxBenefitSubCategoryForm').find('.is-invalid').removeClass('is-invalid');
    $('#taxBenefitSubCategoryForm').find('.error').remove();
    $('#tax_benefit_sub_category_id').val(id);
    $('#tax_benefit_sub_category_name').val(name);
    $('#tax_benefit_category_id').val(catId).trigger('change');
    $('#benefit_percent').val(benefitPercent);
    $('#taxBenefitSubCategoryActionType').attr('name', 'editTaxBenefitSubCategory').val('editTaxBenefitSubCategory');
    $('#taxBenefitModalSubCategoryLabel').text('Edit Tax Benefit Sub Category');
    $('#taxBenefitModalSubCategory').modal('show');
  }

</script>