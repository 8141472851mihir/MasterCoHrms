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
$country_id = 101;
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->

        <div class="row pt-2 pb-2">
            <div class="col-sm-3 col-md-4 col-12">
                <h4 class="page-title">Tax Benefit Category</h4>
            </div>
            <div class="col-sm-3 col-md-4 col-12">
                <form action="" method="get">

                    <select name="year" class="form-control single-select" onchange="this.form.submit();">
                        <option <?php echo ($year == $previousYear) ? 'selected' : ''; ?>
                            value="<?php echo $previousYear ?>"><?php echo $previousYear ?></option>
                        <option <?php echo ($year == $currentYear) ? 'selected' : ''; ?>
                            value="<?php echo $currentYear ?>"><?php echo $currentYear ?></option>
                        <option <?php echo ($year == $nextYear) ? 'selected' : ''; ?> value="<?php echo $nextYear ?>">
                            <?php echo $nextYear ?>
                        </option>
                    </select>
                </form>
            </div>
            <div class="col-sm-3 col-md-4 col-12 mt-2">
                <div class="btn-group float-sm-right">
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#importCategoryModal"
                        onclick="importData();" class="btn mr-1 btn-sm btn-warning waves-effect waves-light"> Copy
                        Rules</a>
                    <a href="addDeductionRule?year=<?php echo $year; ?>"
                        class="btn mr-1 btn-sm btn-primary waves-effect waves-light"><i
                            class="fa fa-plus mr-1"></i>Add</a>

                    <a href="javascript:void(0);" onclick="DeleteAll('deleteTaxBenefitCategory');"
                        class="btn  btn-sm btn-danger pull-right"><i class="fa fa-trash-o fa-lg"></i>Delete</a>

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
                                        <th>Sr. No</th>
                                        <th>Action</th>
                                        <th>Status</th>
                                        <th>Name</th>
                                        <?php if ($country_id == '101') { ?>
                                            <th>Applicable For</th>
                                        <?php } ?>
                                        <th>Year</th>
                                        <th>Rules Type</th>
                                        <th>Amount Type</th>
                                        <th>Amount</th>
                                        <th>Tax Benefit Order</th>
                                        <th>Added By</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $categoryArray = array();
                                    $i = 1;
                                    $q = $d->selectRow("tax_benefit_category.*, (SELECT COUNT(*) FROM tax_benefit_sub_category WHERE tax_benefit_sub_category.tax_benefit_category_id = tax_benefit_category.tax_benefit_category_id) AS totalAssign", "tax_benefit_category", "tax_benefit_category.delete_status=0 AND tax_benefit_category.tax_benefit_year='$year'", "ORDER BY tax_benefit_category.tax_benefit_order ASC");

                                    $counter = 1;
                                    while ($data = mysqli_fetch_array($q)) {
                                        $categoryData = array(
                                            "tax_benefit_category_id" => $data['tax_benefit_category_id'],
                                            "tax_benefit_category_name" => $data['tax_benefit_category_name'],
                                            "applicable_for" => $data['applicable_for'],
                                        );
                                        array_push($categoryArray, $categoryData);

                                        ?>
                                        <tr>
                                            <td class="text-center">
                                                <?php
                                                if ($data['totalAssign'] == 0) {
                                                    ?>
                                                    <input type="hidden" name="id" id="id"
                                                        value="<?php echo $data['tax_benefit_category_id']; ?>">
                                                    <input type="checkbox" name="" class="multiDelteCheckbox"
                                                        value="<?php echo $data['tax_benefit_category_id']; ?>">
                                                <?php } ?>
                                            </td>
                                            <td><?php echo $counter++; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="addDeductionRule?id=<?php echo $data['tax_benefit_category_id']; ?>"
                                                        class="btn btn-sm btn-primary mr-2"> <i
                                                            class="fa fa-pencil"></i></a>

                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $buttonClass = ($data['active_status'] == "1") ? 'btn-success-new' : 'btn-danger';
                                                $buttonCondition = ($data['active_status'] == "1") ? 'Active' : 'Deactive';
                                                $status = ($data['active_status'] == "1") ? 'taxBenefitCatDeactive' : 'taxBenefitCatActive';
                                                $newStatus = ($data['active_status'] == "1") ? 'taxBenefitCatActive' : 'taxBenefitCatDeactive';
                                                $newStatusVal = ($data['active_status'] == "1") ? '0' : '1';
                                                $statusValue = ($data['active_status'] == "1") ? '1' : '0';
                                                ?>

                                                <input type="button" class="btn btn-sm <?php echo $buttonClass ?>"
                                                    id="<?php echo 'tax_benefit_category_' . $data['tax_benefit_category_id']; ?>"
                                                    onclick="changeStatusNew('<?php echo $data['tax_benefit_category_id']; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'tax_benefit_category_' . $data['tax_benefit_category_id']; ?>','','1');"
                                                    data-size="small" value="<?php echo $buttonCondition ?>" />
                                            </td>
                                            <td><?php echo $data['tax_benefit_category_name']; ?></td>
                                            <?php if ($country_id == '101') { ?>
                                                <td><?php switch ($data['applicable_for']) {
                                                    case 0:
                                                        echo 'New Regime';
                                                        break;
                                                    case 1:
                                                        echo 'Old Regime';
                                                        break;
                                                    case 2:
                                                        echo 'Old & New Regime';
                                                        break;
                                                }
                                                ;
                                                ?></td>
                                            <?php } ?>
                                            <td><?php echo $data['tax_benefit_year']; ?></td>
                                            <td><?php switch ($data['rule_type']) {
                                                case 0:
                                                    echo 'Flat';
                                                    break;
                                                case 1:
                                                    echo 'From Salary';
                                                    break;
                                                case 2:
                                                    echo 'Tax On Taxable Amount';
                                                    break;
                                                case 3:
                                                    echo 'Claim By Employee';
                                                    break;
                                                case 4:
                                                    echo 'Deduction On Taxable Amount';
                                                    break;

                                            }
                                            ; ?></td>
                                            <td><?php
                                            if ($data['rule_type'] == 0) {
                                                echo '';
                                            } else if ($data['rule_type'] == 1) {
                                                echo $data['amount_type'] == 0 ? 'Full Amount' : 'Formula';
                                            } else if ($data['rule_type'] == 2) {
                                                echo 'Formula';
                                            } else if ($data['rule_type'] == 3) {
                                                echo '';
                                            } else if ($data['rule_type'] == 4) {
                                                echo $data['amount_type'] == 2 ? 'Max Relief' : 'Relief Apply On';
                                            }
                                            ?></td>
                                            <td><?php
                                            if ($data['rule_type'] == 0 || $data['rule_type'] == 4) {
                                                echo number_format($data['amount'], 2);
                                            } else if (($data['rule_type'] == 1 && $data['amount_type']) || $data['rule_type'] == 3) {
                                                echo number_format($data['max_tax_benefit_amount'], 2);
                                            }
                                            ?></td>
                                            <td><?php echo $data['tax_benefit_order']; ?></td>
                                            <td><?php echo $data['created_by_name']; ?></td>

                                        </tr>
                                    <?php } ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- End Row-->

    </div>
    <!-- End container-fluid-->

</div>

<?php
$yearArray = explode('-', $year);

$nextYear = date('Y', strtotime($yearArray[1] . '-01-01')) . '-' . date('Y', strtotime($yearArray[1] . '-01-01' . ' +1 year'));
$nextYear1 = date('Y', strtotime($yearArray[1] . '-01-01' . ' +1 year')) . '-' . date('Y', strtotime($yearArray[1] . '-01-01' . ' +2 year'));
$nextYear2 = date('Y', strtotime($yearArray[1] . '-01-01' . ' +2 year')) . '-' . date('Y', strtotime($yearArray[1] . '-01-01' . ' +3 year'));
?>

<div class="modal fade" id="importCategoryModal">
    <div class="modal-dialog ">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Copy Tax Benefit Category</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="align-content: center;">
                <div class="card-body">

                    <form id="importCategoryForm" action="controller/taxBenefitController.php"
                        enctype="multipart/form-data" method="post">
                        <div class="form-group row">
                            <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Year<span
                                    class="required">*</span></label>
                            <div class="col-lg-12 col-md-12" id="">
                                <select name="year" id="year" class="form-control single-select">
                                    <option value="<?php echo $nextYear; ?>" selected><?php echo $nextYear; ?></option>
                                    <option value="<?php echo $nextYear1; ?>"><?php echo $nextYear1; ?></option>
                                    <option value="<?php echo $nextYear2; ?>"><?php echo $nextYear2; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Tax Benefit Category<span
                                    class="required">*</span></label>
                            <div class="col-lg-12 col-md-12">
                                <select type="text" class="form-control multiple-select" multiple
                                    name="tax_benefit_category_id[]" id="tax_benefit_category_id">
                                    <?php
                                    foreach ($categoryArray as $val) {
                                        if ($val['applicable_for'] == '0') {
                                            $regime = 'New Regime';
                                        } else if ($val['applicable_for'] == '0') {
                                            $regime = 'Old Regime';
                                        } else {
                                            $regime = 'Old & New Regime';
                                        }
                                        $category = $val['tax_benefit_category_name'] . " ($regime)";
                                        ?>
                                        <option selected value="<?php echo $val['tax_benefit_category_id']; ?>">
                                            <?php echo $category; ?>
                                        </option>
                                    <?php } ?>

                                </select>
                            </div>
                        </div>
                        <div class="form-footer text-center">
                            <input type="hidden" name="importTaxBenefitCategory" value="importTaxBenefitCategory">
                            <button type="submit" class="btn btn-success hideAdd"><i
                                    class="fa fa-check-square-o"></i>SUBMIT</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    function importData() {

        $("#year").val("<?php echo $nextYear; ?>").trigger("change");
        $("#tax_benefit_category_id  option").prop("selected", "selected");
        $("#tax_benefit_category_id").trigger("change");

    }
</script>