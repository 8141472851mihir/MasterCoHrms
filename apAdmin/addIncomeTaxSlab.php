<?php
$tax_slab_type = isset($_GET['st']) ? $d->sanitizeReportFilterIdAsInt($_GET['st']) : 0;
if (date('m') >= 4) {
    $currentYear = date('Y') . '-' . date('Y', strtotime('+1 year'));
    $previousYear = date('Y', strtotime('-1 year')) . '-' . date('Y');
    $nextYear = date('Y', strtotime('+1 year')) . '-' . date('Y', strtotime('+2 year'));
} else {
    $currentYear = date('Y', strtotime('-1 year')) . '-' . date('Y');
    $previousYear = date('Y', strtotime('-2 year')) . '-' . date('Y', strtotime('-1 year'));
    $nextYear = date('Y') . '-' . date('Y', strtotime('+1 year'));
}
$tax_slab_year = $d->sanitizeReportFilterFiscalYear(isset($_REQUEST['year']) ? $_REQUEST['year'] : '', $currentYear);
$editIncomeTaxSlab = false;
extract($_POST);
$tax_slab_id = $d->sanitizeActionIdAsInt($tax_slab_id ?? 0);
if ($tax_slab_id > 0) {
    $q = $d->selectRow("tax_slab_master.*", "tax_slab_master", "tax_slab_id = '$tax_slab_id'");
    if (mysqli_num_rows($q) > 0) {
        $editIncomeTaxSlab = true;
        $data = mysqli_fetch_array($q);
        extract($data);
    }
}
$country_id = 101;
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-12">
                <h4 class="page-title">Add Income Tax Slab</h4>
            </div>
        </div>
        <!-- End Breadcrumb-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="addIncomeTaxSlabForm" action="controller/IncomeTaxSlabController.php" enctype="multipart/form-data" method="post">
                            <div class="row">
                            <?php if($country_id == '101'){ ?>
                                <div class="col-md-4">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="tax_slab_range_start" class="col-lg-12 col-md-12 col-form-label">Financial Year <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12 col-12" id="">
                                            <select name="tax_slab_year" id="tax_slab_year" class="form-control single-select">
                                                <option <?php echo ($tax_slab_year == $previousYear) ? 'selected' : ''; ?> value="<?php echo $previousYear ?>"><?php echo $previousYear ?></option>
                                                <option <?php echo ($tax_slab_year == $currentYear) ? 'selected' : ''; ?> value="<?php echo $currentYear ?>"><?php echo $currentYear ?></option>
                                                <option <?php echo ($tax_slab_year == $nextYear) ? 'selected' : ''; ?> value="<?php echo $nextYear ?>"><?php echo $nextYear ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="col-md-4">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="tax_slab_range_start" class="col-lg-12 col-md-12 col-form-label">Start Amount Range <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12 col-12" id="">
                                            <input type="text" name="tax_slab_range_start" id="tax_slab_range_start" class="form-control onlyNumber" min="0" value="<?php echo $editIncomeTaxSlab ? $tax_slab_range_start : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="tax_slab_range_end" class="col-lg-12 col-md-12 col-form-label">End Amount Range <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12 col-12" id="">
                                            <input type="text" name="tax_slab_range_end" id="tax_slab_range_end" class="form-control onlyNumber" min="0" value="<?php echo $editIncomeTaxSlab ? $tax_slab_range_end : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="tax_slab_percentage" class="col-lg-12 col-md-12 col-form-label">Percentage <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12 col-12" id="">
                                            <input type="text" name="tax_slab_percentage" id="tax_slab_percentage" class="form-control onlyNumber" min="0" max="99" maxlength="2" value="<?php echo $editIncomeTaxSlab ? $tax_slab_percentage : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                <?php if($country_id=='101'){ ?>
                                <div class="col-md-4">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="tax_slab_age_group" class="col-lg-12 col-md-12 col-form-label">Type <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12 col-12" id="">
                                            <div class="form-check mr-1">
                                                <input class="form-check-input mr-1" type="radio" name="tax_slab_type" id="tax_slab_type_new" value="0" <?php echo $tax_slab_type == 0 ? 'checked' : ''; ?> onchange="slabTypeChanges(this.value);">
                                                <span class="checkmark"></span>
                                                <label class="form-check-label" for="tax_slab_type_new">New Tax Regime</label>
                                            </div>
                                            <div class="form-check mr-1">
                                                <input class="form-check-input mr-1" type="radio" name="tax_slab_type" id="tax_slab_type_old" value="1" <?php echo $tax_slab_type == 1 ? 'checked' : ''; ?> onchange="slabTypeChanges(this.value);">
                                                <span class="checkmark"></span>
                                                <label class="form-check-label" for="tax_slab_type_old">Old Tax Regime</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="col-md-4 ageGroupContainer <?php echo $tax_slab_type == 0 ? 'd-none' : ''; ?>">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="tax_slab_age_group" class="col-lg-12 col-md-12 col-form-label">Age Group <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12 col-12" id="">
                                            <select name="tax_slab_age_group" id="tax_slab_age_group" class="form-control single-select">
                                                <option value="">Select</option>
                                                <option <?php echo $editIncomeTaxSlab && $tax_slab_age_group == 1 ? 'selected' : ''; ?> value="1">Below 60 Years</option>
                                                <option <?php echo $editIncomeTaxSlab && $tax_slab_age_group == 2 ? 'selected' : ''; ?> value="2">60 to 80 Years</option>
                                                <option <?php echo $editIncomeTaxSlab && $tax_slab_age_group == 3 ? 'selected' : ''; ?> value="3">Above 80 Years</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="tax_slab_remark" class="col-lg-12 col-md-12 col-form-label">Remarks</label>
                                        <div class="col-lg-12 col-md-12 col-12" id="">
                                            <textarea id="tax_slab_remark" name="tax_slab_remark" class="form-control"><?php echo $editIncomeTaxSlab ? $tax_slab_remark : ''; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-footer text-center">
                                <input type="hidden" name="incomeTaxSlab" value="incomeTaxSlab">
                                <input type="hidden" name="tax_slab_id" value="<?php echo $editIncomeTaxSlab ? $tax_slab_id : ''; ?>">
                                <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> SUBMIT</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div><!--End Row-->
    </div>
    <!-- End container-fluid-->
</div><!--End content-wrapper-->
<script src="assets/js/jquery.min.js"></script>

<script>
    function slabTypeChanges(value) {
        $('.ageGroupContainer').addClass('d-none');
        if (value == 1) {
            $('.ageGroupContainer').removeClass('d-none');
        }
    }
</script>