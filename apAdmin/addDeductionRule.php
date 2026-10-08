<?php
$tax_benefit_category_id = isset($_GET['id']) ? $d->sanitizeReportFilterIdAsInt($_GET['id']) : 0;
if ($tax_benefit_category_id > 0) {
    $q = $d->select("tax_benefit_category", "tax_benefit_category_id='$tax_benefit_category_id'");
    $data = mysqli_fetch_array($q);
    extract($data);
}

if (date('m') >= 4) {
    $previousYear = date('Y', strtotime('-1 year')) . '-' . date('Y');
    $currentYear = date('Y') . '-' . date('Y', strtotime('+1 year'));
    $nextYear = date('Y', strtotime('+1 year')) . '-' . date('Y', strtotime('+2 year'));
} else {
    $previousYear = date('Y', strtotime('-2 year')) . '-' . date('Y', strtotime('-1 year'));
    $currentYear = date('Y', strtotime('-1 year')) . '-' . date('Y');
    $nextYear = date('Y') . '-' . date('Y', strtotime('+1 year'));
}


$formulaArray = array();
if (isset($rule_type) && ($rule_type == 1 || $rule_type == 2 || $rule_type == 3)) {
    $formulaArray = json_decode($formula_json, TRUE);
    // echo "<pre>";print_R($formulaArray);
}
$country_id = 101;
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Add/Edit Tax Rules<?php echo $_GET['year']; ?></h4>
            </div>
            <div class="col-sm-3">
            </div>
        </div>
        <!-- End Breadcrumb-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="addDeductionRuleFrom" action="controller/taxBenefitController.php" method="post">

                            <div class="row">
                                <div class="col-md-3 col-sm-3">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Tax Benefit
                                            Category<span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12 col-12">
                                            <input autocomplete="off" id="tax_benefit_category_name"
                                                class="form-control" type="text"
                                                value="<?php if (isset($tax_benefit_category_name)) {
                                                    echo $tax_benefit_category_name;
                                                } ?>"
                                                name="tax_benefit_category_name" maxlength="250">
                                        </div>
                                    </div>
                                </div>
                                <?php if ($country_id == '101') { ?>
                                    <div class="col-md-3 col-sm-3">
                                        <div class="form-group row w-100 mx-0">
                                            <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Applicable
                                                For<span class="required">*</span></label>
                                            <div class="col-lg-12 col-md-12">
                                                <select id="applicable_for" class="form-control single-select"
                                                    name="applicable_for">
                                                    <option value="">--Select--</option>
                                                    <option <?php if (isset($applicable_for) && $applicable_for == 0) {
                                                        echo "selected";
                                                    } ?> value="0"> New Regime</option>
                                                    <option <?php if (isset($applicable_for) && $applicable_for == 1) {
                                                        echo "selected";
                                                    } ?> value="1"> Old Regime</option>
                                                    <option <?php if (isset($applicable_for) && $applicable_for == 2) {
                                                        echo "selected";
                                                    } ?> value="2"> Old & New Regime</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="col-md-3 col-sm-3">
                                    <div class="form-group row w-100 mx-0">
                                        <?php
                                        if ($_GET['year'] != "") {
                                            $year = $_REQUEST['year'];
                                        } else {
                                            $year = isset($data['tax_benefit_year']) && $data['tax_benefit_year'] != '' ? $data['tax_benefit_year'] : $currentYear;
                                        }
                                        ?>
                                        <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Tax Benefit
                                            Year<span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12">
                                            <select id="tax_benefit_year" class="form-control single-select"
                                                name="tax_benefit_year">
                                                <option <?php echo $year == $previousYear ? 'selected' : ''; ?>
                                                    value="<?php echo $previousYear ?>"><?php echo $previousYear ?>
                                                </option>
                                                <option <?php echo ($year == $currentYear) || ($year == '') ? 'selected' : ''; ?> value="<?php echo $currentYear ?>"><?php echo $currentYear ?>
                                                </option>
                                                <option <?php echo $year == $nextYear ? 'selected' : ''; ?>
                                                    value="<?php echo $nextYear ?>"><?php echo $nextYear ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Tax Benefit
                                            Order<span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12">
                                            <input type="text" id="tax_benefit_order" class="form-control onlyNumber"
                                                name="tax_benefit_order"
                                                value="<?php if (isset($tax_benefit_order)) {
                                                    echo $tax_benefit_order;
                                                } ?>">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Rule Type<span
                                                class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12">
                                            <select id="rule_type" class="form-control single-select" name="rule_type"
                                                onchange="ruleTypeChange(this.value);">
                                                <option value="">--select--</option>
                                                <option <?php if (isset($rule_type) && $rule_type == 3) {
                                                    echo "selected";
                                                } ?> value="3"> Claim By Employee </option>
                                                <option <?php if (isset($rule_type) && $rule_type == 0) {
                                                    echo "selected";
                                                } ?> value="0"> Flat</option>
                                                <option <?php if (isset($rule_type) && $rule_type == 1) {
                                                    echo "selected";
                                                } ?> value="1"> From Salary</option>
                                                <option <?php if (isset($rule_type) && $rule_type == 2) {
                                                    echo "selected";
                                                } ?> value="2">Tax On Taxable Amount </option>
                                                <option <?php if (isset($rule_type) && $rule_type == 4) {
                                                    echo "selected";
                                                } ?> value="4">Deduction On Taxable Amount</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="col-md-3 col-sm-3 amount_type <?php echo isset($rule_type) && $rule_type != 0 && $rule_type != 3 ? '' : 'd-none'; ?>">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Amount
                                            Type<span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" id="amount_type" class="form-control single-select"
                                                name="amount_type" onchange="amountTypeChange(this.value);">
                                                <option value="">--select--</option>
                                                <?php if (isset($rule_type) && $rule_type == '1') { ?>
                                                    <option <?php if (isset($amount_type) && $amount_type == 0) {
                                                        echo "selected";
                                                    } ?> value="0">Full Amount </option>
                                                <?php }
                                                if (isset($rule_type) && ($rule_type == '1' || $rule_type == '2')) { ?>
                                                    <option <?php if (isset($amount_type) && $amount_type == 1) {
                                                        echo "selected";
                                                    } ?> value="1">Formula</option>
                                                <?php }
                                                if (isset($rule_type) && $rule_type == '4') { ?>
                                                    <option <?php if (isset($amount_type) && $amount_type == 2) {
                                                        echo "selected";
                                                    } ?> value="2"> Max Relief </option>
                                                    <option <?php if (isset($amount_type) && $amount_type == 3) {
                                                        echo "selected";
                                                    } ?> value="3"> Relief Apply On </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="col-md-3 col-sm-3 max-amount <?php echo isset($rule_type) && ($rule_type == 3 || ($rule_type == 1 && $amount_type == 1)) ? '' : 'd-none'; ?>">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Max Amount
                                            <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12 col-12">
                                            <input autocomplete="off" id="max_tax_benefit_amount"
                                                class="form-control onlyNumber" type="text"
                                                value="<?php if (isset($max_tax_benefit_amount)) {
                                                    echo $max_tax_benefit_amount;
                                                } ?>"
                                                name="max_tax_benefit_amount" maxlength="250">
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="col-md-3 col-sm-3 max-amount <?php echo isset($rule_type) && $rule_type == 3 ? '' : 'd-none'; ?>">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Head </label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" class="form-control multiple-select-tax_deduct" multiple
                                                name="pf_deduction_head[]" id="pf_deduction_head">
                                                <?php

                                                $deductionTypeQ = $d->select("salary_earning_deduction_type_master", "salary_earning_deduction_status='0' AND earn_deduct_is_delete='0' AND earning_deduction_type=1 AND country_id='$country_id'", "ORDER BY salary_earning_deduction_id ASC");

                                                while ($deductionTypeData = mysqli_fetch_array($deductionTypeQ)) {
                                                    $type = $deductionTypeData['earning_deduction_type'] == 0 ? 'Earning' : 'Deduction';
                                                    ?>
                                                    <option
                                                        value="<?php echo $deductionTypeData['salary_earning_deduction_id']; ?>">
                                                        <?php echo $deductionTypeData['earning_deduction_name'] . ' (' . $type . ')'; ?>
                                                    </option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="col-md-3 col-sm-3 amount <?php echo isset($rule_type) && ($rule_type == 0 || $rule_type == 4) ? '' : 'd-none'; ?>">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Amount<span
                                                class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12 col-12">
                                            <input autocomplete="off" class="form-control" type="text"
                                                value="<?php if (isset($amount)) {
                                                    echo $amount;
                                                } ?>" name="amount"
                                                id="amount">
                                        </div>
                                    </div>
                                </div>

                                <!-- For Formula Type -->
                                <div
                                    class="col-md-3 col-sm-3 formula-type <?php echo isset($rule_type) && $rule_type == 2 ? '' : 'd-none'; ?>">
                                    <div class="form-group row w-100 mx-0">
                                        <label for="input-10" class="col-lg-12 col-md-12 col-form-label">Formula Type
                                            <span class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" id="formula_type" class="form-control single-select"
                                                name="formula_type" onchange="formulaTypeChange(this.value);">
                                                <option value="">--select--</option>
                                                <option <?php if (isset($formulaArray['formula_type']) && $formulaArray['formula_type'] == 0) {
                                                    echo "selected";
                                                } ?> value="0">
                                                    Fixed</option>
                                                <option <?php if (isset($formulaArray['formula_type']) && $formulaArray['formula_type'] == 1) {
                                                    echo "selected";
                                                } ?> value="1">
                                                    Slab</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- //For Formula Type -->

                                <!-- For Full Amount -->
                                <div
                                    class="col-md-3 col-sm-3 deduction-head <?php echo isset($rule_type) && $rule_type == 1 && isset($amount_type) && $amount_type == 0 ? '' : 'd-none'; ?>">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Head <span
                                                class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" class="form-control multiple-select-tax_deduct" multiple
                                                name="deduction_head[]" id="deduction_head">
                                                <?php

                                                $deductionTypeQ = $d->select("salary_earning_deduction_type_master", "salary_earning_deduction_status='0' AND earn_deduct_is_delete='0' AND country_id='$country_id'", "ORDER BY salary_earning_deduction_id ASC");

                                                while ($deductionTypeData = mysqli_fetch_array($deductionTypeQ)) {
                                                    $type = $deductionTypeData['earning_deduction_type'] == 0 ? 'Earning' : 'Deduction';
                                                    ?>
                                                    <option
                                                        value="<?php echo $deductionTypeData['salary_earning_deduction_id']; ?>">
                                                        <?php echo $deductionTypeData['earning_deduction_name'] . ' (' . $type . ')'; ?>
                                                    </option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- //For Full Amount -->
                            </div>



                            <!-- For Surcharge Slab -->

                            <div
                                class="row slab-formula <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 2 && $amount_type == 1 && isset($formulaArray['formula_type']) && $formulaArray['formula_type'] == 1 ? '' : 'd-none'; ?>">
                                <div class="col-md-3 col-sm-3 "><i class="text-warning"> Add Slab: </i></div>
                            </div>

                            <?php
                            $key = 0;
                            if (isset($rule_type) && isset($amount_type) && $rule_type == 2 && $amount_type == 1 && isset($formulaArray['formula_type']) && $formulaArray['formula_type'] == 1 && isset($formulaArray['value']) && count($formulaArray['value']) > 0) {
                                ?>
                                <div class="col-md-12 col-sm-12">
                                    <?php
                                    for ($i = 0; $i < count($formulaArray['value']); $i++) {
                                        ?>

                                        <div class="row <?php if ($i == 0) {
                                            echo 'slab-formula';
                                        } else {
                                            echo 'tempDiv';
                                        } ?>">
                                            <div class="col-md-2 col-sm-2 mt-2">
                                                <div class="form-group row w-100 mx-0  ">
                                                    <div class="col-lg-2 col-md-2 col-sm-0"></div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <i class="text-primary"> Value</i>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4"> = </div>
                                                </div>

                                            </div>

                                            <div class="col-md-3 col-sm-3 ">
                                                <div class="form-group row w-100 mx-0">
                                                    <div class="col-lg-12 col-md-12 col-12">
                                                        <input autocomplete="off" placeholder="Min"
                                                            class="onlyNumber form-control slab-input" type="text"
                                                            value="<?php echo isset($formulaArray['value'][$i]['min']) ? $formulaArray['value'][$i]['min'] : ''; ?>"
                                                            id="min_<?php echo $i; ?>" name="min[<?php echo $i; ?>]" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-3 ">
                                                <div class="form-group row w-100 mx-0">
                                                    <div class="col-lg-12 col-md-12 col-12">
                                                        <input autocomplete="off" placeholder="Max"
                                                            class="onlyNumber form-control slab-input" type="text"
                                                            value="<?php echo isset($formulaArray['value'][$i]['max']) ? $formulaArray['value'][$i]['max'] : ''; ?>"
                                                            id="max_<?php echo $i; ?>" name="max[<?php echo $i; ?>]" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-3 ">
                                                <div class="form-group row w-100 mx-0">
                                                    <div class="col-lg-12 col-md-12 col-12">
                                                        <input autocomplete="off" placeholder="Value"
                                                            class="onlyNumber form-control slab-input" type="text"
                                                            value="<?php echo isset($formulaArray['value'][$i]['value']) ? $formulaArray['value'][$i]['value'] : ''; ?>"
                                                            id="value_<?php echo $i; ?>" name="slab_value[<?php echo $i; ?>]" />
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if ($i == '0') { ?>
                                                <div class="col-md-1 col-sm-1 ">
                                                    <button type="button" class="btn btn-sm btn-info mb-2 addSlab"><i
                                                            class="fa fa-plus"></i></button>
                                                </div>
                                            <?php } else { ?>
                                                <div class="col-md-1 col-sm-1 ">
                                                    <button type="button" class="btn btn-sm btn-danger mb-2 removeSlab"><i
                                                            class="fa fa-minus"></i></button>
                                                </div>
                                            <?php } ?>

                                        </div>

                                        <?php
                                        $key++;
                                    }
                                    ?>
                                </div>
                                <?php
                            } else { ?>

                                <div
                                    class="col-md-12 col-sm-12 slab-formula <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 2 && $amount_type == 1 && isset($formulaArray['formula_type']) && $formulaArray['formula_type'] == 1 ? '' : 'd-none'; ?>">
                                    <div class="row ">
                                        <div class="col-md-2 col-sm-2 mt-2">
                                            <div class="form-group row w-100 mx-0  ">
                                                <div class="col-lg-2 col-md-2 col-sm-0"></div>
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <i class="text-primary"> Value</i>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4"> = </div>
                                            </div>

                                        </div>

                                        <div class="col-md-3 col-sm-3 ">
                                            <div class="form-group row w-100 mx-0">
                                                <div class="col-lg-12 col-md-12 col-12">
                                                    <input autocomplete="off" placeholder="Min"
                                                        class="onlyNumber form-control" type="text"
                                                        value="<?php //echo isset($formulaArray['value'])?$formulaArray['value']:''; ?>"
                                                        id="min_0" name="min[0]" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-3 ">
                                            <div class="form-group row w-100 mx-0">
                                                <div class="col-lg-12 col-md-12 col-12">
                                                    <input autocomplete="off" placeholder="Max"
                                                        class="onlyNumber form-control" type="text"
                                                        value="<?php //echo isset($formulaArray['value'])?$formulaArray['value']:''; ?>"
                                                        id="max_0" name="max[0]" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-3 ">
                                            <div class="form-group row w-100 mx-0">
                                                <div class="col-lg-12 col-md-12 col-12">
                                                    <input autocomplete="off" placeholder="Value"
                                                        class="onlyNumber form-control" type="text"
                                                        value="<?php //echo isset($formulaArray['value'])?$formulaArray['value']:''; ?>"
                                                        id="value_0" name="slab_value[0]" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1 col-sm-1 ">
                                            <button type="button" class="btn btn-sm btn-info mb-2 addSlab"><i
                                                    class="fa fa-plus"></i></button>
                                        </div>

                                    </div>
                                </div>
                            <?php } ?>

                            <div class="col-md-12 col-sm-12 slab-formula <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 2 && $amount_type == 1 && isset($formulaArray['formula_type']) && $formulaArray['formula_type'] == 1 ? '' : 'd-none'; ?>"
                                id="slabData"></div>

                            <div
                                class="row slab-formula <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 2 && $amount_type == 1 && isset($formulaArray['formula_type']) && $formulaArray['formula_type'] == 1 ? '' : 'd-none'; ?>">
                                <div class="col-md-3 col-sm-3 "><i class="text-warning"> Formula : </i></div>
                            </div>

                            <div
                                class="row slab-formula <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 2 && $amount_type == 1 && isset($formulaArray['formula_type']) && $formulaArray['formula_type'] == 1 ? '' : 'd-none'; ?>">


                                <div class="col-md-2 col-sm-2 mt-2">
                                    <label class="col-lg-12 col-md-12 col-form-label">&nbsp; </label>
                                    <div class="form-group row w-100 mx-0  ">
                                        <div class="col-lg-2 col-md-2 col-sm-0"></div>
                                        <div class="col-lg-10 col-md-10 col-sm-12">
                                            <i class="text-primary">Taxable Amount</i>

                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-2 col-sm-2 mt-2 ">
                                    <label class="col-lg-12 col-md-12 col-form-label">&nbsp; </label>
                                    <div class="form-group row w-100 mx-0">
                                        <div class="col-lg-4 col-md-4 col-sm-4"> = </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8">Total</div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-2 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Formula Modular 1 <span
                                                class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" class="form-control single-select"
                                                id="slab_formula_modular_1" name="slab_formula_modular_1"
                                                onchange="formulaModularChange(this.value,0);">
                                                <option value="">--select--</option>
                                                <option <?php echo isset($formulaArray['formula_modular_1']) && $formulaArray['formula_modular_1'] == '/' ? 'selected' : ''; ?>> /
                                                </option>
                                                <option <?php echo isset($formulaArray['formula_modular_1']) && $formulaArray['formula_modular_1'] == '*' ? 'selected' : ''; ?>> *
                                                </option>
                                                <option <?php echo isset($formulaArray['formula_modular_1']) && $formulaArray['formula_modular_1'] == '+' ? 'selected' : ''; ?>> +
                                                </option>
                                                <option <?php echo isset($formulaArray['formula_modular_1']) && $formulaArray['formula_modular_1'] == '-' ? 'selected' : ''; ?>> -
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2 col-sm-2  mt-2">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">&nbsp; </label>
                                        <div class="col-lg-4 col-md-2 col-sm-0"></div>
                                        <div class="col-lg-8 col-md-10 col-sm-12">
                                            <i class="text-primary">Value</i>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3 formula-modular-2">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Formula Modular 2 </label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" class="form-control single-select formula_modular_2_0"
                                                name="slab_formula_modular_2" id="slab_formula_modular_2">
                                                <option value="">--Select--</option>
                                                <option <?php echo isset($formulaArray['formula_modular_2']) && $formulaArray['formula_modular_2'] == '%' ? 'selected' : ''; ?>> %</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>



                            </div>

                            <!-- // For Surcharge -->

                            <!-- For Taxable Amount -->

                            <div
                                class="row from-deduction <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 2 && $amount_type == 1 && isset($formulaArray['formula_type']) && $formulaArray['formula_type'] == 0 ? '' : 'd-none'; ?>">

                                <div class="col-md-3 col-sm-3">
                                    <div class="form-group row w-100 mx-0  mt-2">
                                        <label class="col-lg-12 col-md-12 col-form-label">&nbsp;</label>
                                        <div class="col-lg-4 col-md-2 col-sm-0"></div>
                                        <div class="col-lg-8 col-md-10 col-sm-12">
                                            <i class="text-primary">Taxable Amount</i>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-1 col-sm-1 mt-2">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">&nbsp;</label>
                                        <div class="col-lg-4 col-md-4 col-sm-4"> = </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8"> Total </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Formula Modular 1 <span
                                                class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" class="form-control single-select formula_modular_1"
                                                id="formula_modular_1" name="formula_modular_1"
                                                onchange="formulaModularChange(this.value,1);">
                                                <option value="">--Select--</option>
                                                <option <?php echo isset($formulaArray['formula_modular_1']) && $formulaArray['formula_modular_1'] == '/' ? 'selected' : ''; ?>> /
                                                </option>
                                                <option <?php echo isset($formulaArray['formula_modular_1']) && $formulaArray['formula_modular_1'] == '*' ? 'selected' : ''; ?>> *
                                                </option>
                                                <option <?php echo isset($formulaArray['formula_modular_1']) && $formulaArray['formula_modular_1'] == '+' ? 'selected' : ''; ?>> +
                                                </option>
                                                <option <?php echo isset($formulaArray['formula_modular_1']) && $formulaArray['formula_modular_1'] == '-' ? 'selected' : ''; ?>> -
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Value <span
                                                class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12 col-12">
                                            <input autocomplete="off" placeholder="Value"
                                                class="onlyNumber form-control" type="text"
                                                value="<?php echo isset($formulaArray['value']) ? $formulaArray['value'] : ''; ?>"
                                                id="value" name="value" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3 formula-modular-2 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Formula Modular 2 </label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" class="form-control single-select formula_modular_2_1"
                                                name="formula_modular_2" id="formula_modular_2">
                                                <option value="">--Select--</option>
                                                <option <?php echo isset($formulaArray['formula_modular_2']) && $formulaArray['formula_modular_2'] == '%' ? 'selected' : ''; ?>> %</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- //For Taxable Amount -->
                            <!-- For HRA Cases -->

                            <div
                                class="row formula-case <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 1 && $amount_type == 1 ? '' : 'd-none'; ?>">
                                <label for="input-10" class="col-lg-12 col-md-12 col-form-label text-warning">Which Ever
                                    is Less : </label>
                            </div>

                            <div
                                class="row formula-case <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 1 && $amount_type == 1 ? '' : 'd-none'; ?>">
                                <label for="input-10" class="col-lg-12 col-md-12 col-form-label text-info">Case - 1
                                    <span class="text-danger">(Actual Rent Paid Minus Calculated Head)</span></label>
                            </div>

                            <div
                                class="row formula-case <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 1 && $amount_type == 1 ? '' : 'd-none'; ?>">

                                <div class="col-md-3 col-sm-3 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Head <span
                                                class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" class="form-control multiple-select-tax_deduct" multiple
                                                name="deduction_head_1[]" id="deduction_head_01">
                                                <?php

                                                $deductionTypeQ = $d->select("salary_earning_deduction_type_master", "salary_earning_deduction_status='0' AND earn_deduct_is_delete='0' AND country_id='$country_id'", "ORDER BY salary_earning_deduction_id ASC");

                                                while ($deductionTypeData = mysqli_fetch_array($deductionTypeQ)) {
                                                    $type = $deductionTypeData['earning_deduction_type'] == 0 ? 'Earning' : 'Deduction';
                                                    ?>
                                                    <option
                                                        value="<?php echo $deductionTypeData['salary_earning_deduction_id']; ?>">
                                                        <?php echo $deductionTypeData['earning_deduction_name'] . ' (' . $type . ')'; ?>
                                                    </option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 col-sm-1 mt-2 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">&nbsp;</label>
                                        <div class="col-lg-4 col-md-4 col-sm-4"> = </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8">Total</div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-2 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Formula Modular 1 <span
                                                class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" class="form-control single-select formula_modular_1"
                                                id="formula_modular_11" name="formula_modular_11"
                                                onchange="formulaModularChange(this.value,2);">
                                                <option value="">--Select--</option>
                                                <option <?php echo isset($formulaArray['case_1']['formula_modular_1']) && $formulaArray['case_1']['formula_modular_1'] == '/' ? 'selected' : ''; ?>>
                                                    / </option>
                                                <option <?php echo isset($formulaArray['case_1']['formula_modular_1']) && $formulaArray['case_1']['formula_modular_1'] == '*' ? 'selected' : ''; ?>>
                                                    * </option>
                                                <option <?php echo isset($formulaArray['case_1']['formula_modular_1']) && $formulaArray['case_1']['formula_modular_1'] == '+' ? 'selected' : ''; ?>>
                                                    + </option>
                                                <option <?php echo isset($formulaArray['case_1']['formula_modular_1']) && $formulaArray['case_1']['formula_modular_1'] == '-' ? 'selected' : ''; ?>>
                                                    - </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Value <span
                                                class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12 col-12">
                                            <input autocomplete="off" placeholder="Value	"
                                                class="onlyNumber form-control" type="text"
                                                value="<?php echo isset($formulaArray['case_1']['value']) ? $formulaArray['case_1']['value'] : ''; ?>"
                                                id="value_1" name="value_1" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3 formula-modular-2">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Formula Modular 2 </label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" class="form-control single-select formula_modular_2_2"
                                                name="formula_modular_21" id="formula_modular_21">
                                                <option value="">--Select--</option>
                                                <option <?php echo isset($formulaArray['case_1']['formula_modular_2']) && $formulaArray['case_1']['formula_modular_2'] == '%' ? 'selected' : ''; ?>>
                                                    %</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="row formula-case <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 1 && $amount_type == 1 ? '' : 'd-none'; ?>">
                                <label for="input-10" class="col-lg-12 col-md-12 col-form-label text-info">Case - 2
                                    <span class="text-danger"> (Calculated Head)</span></label>
                            </div>
                            <div
                                class="row formula-case <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 1 && $amount_type == 1 ? '' : 'd-none'; ?>">
                                <div class="col-md-3 col-sm-3 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Value <span
                                                class="text-success">(Non-Metro city)</span> <span
                                                class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12 col-12">
                                            <input autocomplete="off" placeholder="Value"
                                                class="onlyNumber form-control" type="text"
                                                value="<?php echo isset($formulaArray['case_2']['value']) ? $formulaArray['case_2']['value'] : ''; ?>"
                                                id="value_2" name="value_2" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Value <span
                                                class="text-success">(Metro city) </span> <span
                                                class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12 col-12">
                                            <input autocomplete="off" placeholder="Value"
                                                class="onlyNumber form-control" type="text"
                                                value="<?php echo isset($formulaArray['case_2']['value_1']) ? $formulaArray['case_2']['value_1'] : ''; ?>"
                                                id="value_3" name="value_3" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="row formula-case <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 1 && $amount_type == 1 ? '' : 'd-none'; ?>">
                                <div class="col-md-3 col-sm-3 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Head <span
                                                class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" class="form-control multiple-select-tax_deduct" multiple
                                                name="deduction_head_2[]" id="deduction_head_02">
                                                <?php
                                                $deductionTypeQ = $d->select("salary_earning_deduction_type_master", "salary_earning_deduction_status='0' AND earn_deduct_is_delete='0' AND country_id='$country_id'", "ORDER BY salary_earning_deduction_id ASC");

                                                while ($deductionTypeData = mysqli_fetch_array($deductionTypeQ)) {
                                                    $type = $deductionTypeData['earning_deduction_type'] == 0 ? 'Earning' : 'Deduction';
                                                    ?>
                                                    <option
                                                        value="<?php echo $deductionTypeData['salary_earning_deduction_id']; ?>">
                                                        <?php echo $deductionTypeData['earning_deduction_name'] . ' (' . $type . ')'; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-1 col-sm-1 mt-2 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">&nbsp;</label>
                                        <div class="col-lg-4 col-md-4 col-sm-4"> = </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8">Total </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-2 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Formula Modular 1 <span
                                                class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" class="form-control single-select formula_modular_1"
                                                id="formula_modular_12" name="formula_modular_12"
                                                onchange="formulaModularChange(this.value,3);">
                                                <option value="">--Select--</option>
                                                <option <?php echo isset($formulaArray['case_2']['formula_modular_1']) && $formulaArray['case_2']['formula_modular_1'] == '/' ? 'selected' : ''; ?>>
                                                    / </option>
                                                <option <?php echo isset($formulaArray['case_2']['formula_modular_1']) && $formulaArray['case_2']['formula_modular_1'] == '*' ? 'selected' : ''; ?>>
                                                    * </option>
                                                <option <?php echo isset($formulaArray['case_2']['formula_modular_1']) && $formulaArray['case_2']['formula_modular_1'] == '+' ? 'selected' : ''; ?>>
                                                    + </option>
                                                <option <?php echo isset($formulaArray['case_2']['formula_modular_1']) && $formulaArray['case_2']['formula_modular_1'] == '-' ? 'selected' : ''; ?>>
                                                    - </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">&nbsp; </label>
                                        <div class="col-lg-4 col-md-2 col-sm-0"></div>
                                        <div class="col-lg-8 col-md-10 col-sm-12">
                                            <i class="text-primary"> Value</i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3 formula-modular-2">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Formula Modular 2 </label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" class="form-control single-select formula_modular_2_3"
                                                name="formula_modular_22" id="formula_modular_22">
                                                <option value="">--Select--</option>
                                                <option <?php echo isset($formulaArray['case_2']['formula_modular_2']) && $formulaArray['case_2']['formula_modular_2'] == '%' ? 'selected' : ''; ?>>
                                                    %</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="row formula-case <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 1 && $amount_type == 1 ? '' : 'd-none'; ?>">
                                <label for="input-10" class="col-lg-12 col-md-12 col-form-label text-info">Case - 3
                                    <span class="text-danger">(Actual Head Received From The Employer)</span></label>
                            </div>
                            <div
                                class="row formula-case <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 1 && $amount_type == 1 ? '' : 'd-none'; ?>">
                                <div class="col-md-3 col-sm-3 ">
                                    <div class="form-group row w-100 mx-0">
                                        <label class="col-lg-12 col-md-12 col-form-label">Head <span
                                                class="required">*</span></label>
                                        <div class="col-lg-12 col-md-12">
                                            <select type="text" class="form-control multiple-select-tax_deduct" multiple
                                                name="deduction_head_3[]" id="deduction_head_03">
                                                <?php

                                                $deductionTypeQ = $d->select("salary_earning_deduction_type_master", "salary_earning_deduction_status='0' AND earn_deduct_is_delete='0' AND country_id='$country_id'", "ORDER BY salary_earning_deduction_id ASC");

                                                while ($deductionTypeData = mysqli_fetch_array($deductionTypeQ)) {
                                                    $type = $deductionTypeData['earning_deduction_type'] == 0 ? 'Earning' : 'Deduction';
                                                    ?>
                                                    <option
                                                        value="<?php echo $deductionTypeData['salary_earning_deduction_id']; ?>">
                                                        <?php echo $deductionTypeData['earning_deduction_name'] . ' (' . $type . ')'; ?>
                                                    </option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- // For HRA Cases -->
                            <!-- For Marginal Relief -->
                            <div
                                class="row marginal-relief <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 4 && $amount_type == 3 ? '' : 'd-none'; ?>">

                                <div class="col-md-3 col-sm-3">
                                    <div class="form-group row w-100 mx-0  mt-2">
                                        <div class="col-lg-4 col-md-2 col-sm-0"></div>
                                        <div class="col-lg-8 col-md-10 col-sm-12">
                                            <i class="text-primary">Actual Tax Liability</i>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-3 col-sm-3 mt-2">
                                    <div class="form-group row w-100 mx-0">
                                        <div class="col-lg-4 col-md-4 col-sm-4"> = </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8">Taxable Amount</div>
                                    </div>
                                </div>
                                <div class="col-md-1 col-sm-1 mt-2">
                                    <div class="form-group row w-100 mx-0">
                                        <div class="col-lg-4 col-md-4 col-sm-4"> - </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3 mt-2">
                                    <div class="form-group row w-100 mx-0">
                                        <div class="col-lg-8 col-md-8 col-sm-8 text-danger">Amount</div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="row marginal-relief <?php echo isset($rule_type) && isset($amount_type) && $rule_type == 4 && $amount_type == 3 ? '' : 'd-none'; ?>">

                                <div class="col-md-3 col-sm-3">
                                    <div class="form-group row w-100 mx-0  mt-2">
                                        <div class="col-lg-4 col-md-2 col-sm-0"></div>
                                        <div class="col-lg-8 col-md-10 col-sm-12">
                                            <i class="text-success">Marginal Relief</i>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-3 col-sm-3 mt-2">
                                    <div class="form-group row w-100 mx-0">
                                        <div class="col-lg-4 col-md-4 col-sm-4"> = </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8">Calculated Tax</div>
                                    </div>
                                </div>
                                <div class="col-md-1 col-sm-1 mt-2">
                                    <div class="form-group row w-100 mx-0">
                                        <div class="col-lg-4 col-md-4 col-sm-4"> - </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3 mt-2">
                                    <div class="form-group row w-100 mx-0">
                                        <div class="col-lg-8 col-md-8 col-sm-8 text-danger">Actual Tax Liability</div>
                                    </div>
                                </div>
                            </div>
                            <!-- // For Marginal Relief -->


                            <div class="form-footer text-center">

                                <button type="submit" class="btn btn-primary saveBtn"><i
                                        class="fa fa-check-square-o"></i>
                                    <?php if (isset($data['tax_benefit_category_id']) && $data['tax_benefit_category_id'] > 0) {
                                        echo "Update";
                                    } else {
                                        echo "Add";
                                    } ?></button>
                                <input type="hidden" name="tax_benefit_category_id"
                                    value="<?php echo $tax_benefit_category_id; ?>">
                                <input type="hidden" name="addDeductionRule" value="addDeductionRule">
                                <button type="button" class="btn btn-danger" onclick="resetAddDeductionRuleFrom();"><i
                                        class="fa fa-check-square-o"></i>RESET</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--End Row-->

    </div>
    <!-- End container-fluid-->

</div>
<!--End content-wrapper-->

<script src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/select2/js/select2.min.js"></script>

<script type="text/javascript">
    <?php if (isset($formulaArray['case_1']['head_id'])) { ?>
        //case-1
        var idsData1 = "<?php echo $formulaArray['case_1']['head_id']; ?>";
        idsData1 = idsData1.split(",");

        $("#deduction_head_01").val(idsData1).trigger("change");

        //case-2
        var idsData2 = "<?php echo $formulaArray['case_2']['head_id']; ?>";
        idsData2 = idsData2.split(",");

        $("#deduction_head_02").val(idsData2).trigger("change");

        //case-3
        var idsData3 = "<?php echo $formulaArray['case_3']['head_id']; ?>";
        idsData3 = idsData3.split(",");

        $("#deduction_head_03").val(idsData3).trigger("change");

    <?php } else if (isset($formulaArray['head_id']) && $formulaArray['head_id'] != '') { ?>


            var idsData = "<?php echo $formulaArray['head_id']; ?>";
            idsData = idsData.split(",");

            $(".multiple-select-tax_deduct").val(idsData).trigger("change");


    <?php } ?>

    function ruleTypeChange(val) {
        $("#amount_type option").remove();
        $("#max_tax_benefit_amount").val("");
        $("#amount_type").val("").trigger("change");
        $("#formula_type").val("").trigger("change");
        $('.multiple-select-tax_deduct').select2({
            placeholder: 'Deduct From Salary',
            allowClear: true,
        });
        $(".deduction-head").addClass("d-none");
        $(".max-amount").addClass("d-none");
        $(".from-deduction").addClass("d-none");
        $(".formula-case").addClass("d-none");
        $(".amount").addClass("d-none");
        $(".amount_type").addClass("d-none");
        $(".formula-type").addClass("d-none");
        $(".slab-formula").addClass("d-none");
        $(".marginal-relief").addClass("d-none");

        if (val == '0') {     // Flat
            $(".amount").removeClass("d-none");
        } else if (val == '1' || val == '2') {     // From Salary, Tax On Taxable Amount

            $(".amount_type").removeClass("d-none");
            if (val == '1') {
                $('#amount_type').select2();
                $('#amount_type').append('<option value="">-- Select --</option><option value="0">Full Amount</option><option value="1" > Formula </option>');
            } else if (val == '2') {
                $('#amount_type').select2();
                $('#amount_type').append('<option value="">-- Select --</option><option value="1" > Formula </option>');
            }
        } else if (val == '3') {
            $(".max-amount").removeClass("d-none");
        } else if (val == '4') {
            $(".amount_type").removeClass("d-none");
            $('#amount_type').select2();
            $('#amount_type').append('<option value="">-- Select --</option><option value="2"> 	Max Relief</option><option value="3" > Relief Apply On </option>');
        }
    }

    function amountTypeChange(val) {
        var rule_type = $("#rule_type").val();

        $(".multiple-select-tax_deduct").val('').trigger("change");
        $("#formula_modular_1").val("").trigger("change");
        $("#formula_modular_2").val("").trigger("change");
        $("#formula_type").val("").trigger("change");
        $("#value").val("");

        $(".amount").addClass("d-none");
        $(".max-amount").addClass("d-none");
        $(".formula-case").addClass("d-none");
        $(".from-deduction").addClass("d-none");
        $(".deduction-head").addClass("d-none");
        $(".formula-type").addClass("d-none");
        $(".slab-formula").addClass("d-none");
        $(".marginal-relief").addClass("d-none");

        if (val == '0') {     // Full Amount
            $(".deduction-head").removeClass("d-none");
            $('.multiple-select-tax_deduct').select2({
                placeholder: "Deduct From Salary",
                allowClear: true
            });
        } else if (val == '1') {     // Formula
            if (rule_type == '1') {
                $('.multiple-select-tax_deduct').select2({
                    placeholder: "Deduct From Salary",
                    allowClear: true
                });
                $(".max-amount").removeClass("d-none");
                $(".formula-case").removeClass("d-none");
            } else if (rule_type = "2") {
                $(".formula-type").removeClass("d-none");
            }
        } else if (val == '2') {
            $(".amount").removeClass("d-none");
        } else if (val == '3') {
            $(".amount").removeClass("d-none");
            $(".marginal-relief").removeClass("d-none");
        }
    }

    function formulaTypeChange(val) {
        var rule_type = $("#rule_type").val();
        var amount_type = $("#amount_type").val();

        $(".multiple-select-tax_deduct").val('').trigger("change");
        $("#formula_modular_1").val("").trigger("change");
        $("#formula_modular_2").val("").trigger("change");
        $("#value").val("");

        $(".amount").addClass("d-none");
        $(".max-amount").addClass("d-none");
        $(".formula-case").addClass("d-none");
        $(".from-deduction").addClass("d-none");
        $(".deduction-head").addClass("d-none");
        $(".slab-formula").addClass("d-none");
        $(".marginal-relief").addClass("d-none");

        $(".tempDiv").remove();
        $(".slab-input").val("");
        $("#slab_formula_modular_1").val("").trigger("change");
        $("#slab_formula_modular_2").val("").trigger("change");
        key = 1;
        if (val == '0') {     // Fixed
            $(".from-deduction").removeClass("d-none");
        } else if (val == '1') {     // Slab
            $(".slab-formula").removeClass("d-none");
            addSlabValidationRules();
        }
    }


    function formulaModularChange(val = '', num) {
        $(".formula_modular_2_" + num).val("").trigger("change");
        if (val == '' || val == '*') {
            $(".formula_modular_2_" + num).parent().parent().parent().removeClass("d-none");
        } else {
            $(".formula_modular_2_" + num).parent().parent().parent().addClass("d-none");
        }
    }

    $(".formula_modular_1").on('change', function () {

    });

    function resetAddDeductionRuleFrom() {
        $("#tax_benefit_category_name").val("");
        $("#tax_benefit_year").val("");
        $("#tax_benefit_order").val("");
        $("#value").val("");
        $("#value_1").val("");
        $("#value_2").val("");
        $("#value_3").val("");
        $("#max_tax_benefit_amount").val("");
        $("#applicable_for").val("").trigger("change");
        $("#rule_type").val("").trigger("change");
        $("#amount_type").val("").trigger("change");
        $("#formula_modular_1").val("").trigger("change");
        $("#formula_modular_11").val("").trigger("change");
        $("#formula_modular_12").val("").trigger("change");
        $("#formula_modular_2").val("").trigger("change");
        $("#formula_modular_21").val("").trigger("change");
        $("#formula_modular_22").val("").trigger("change");
        $(".multiple-select-tax_deduct").val("").trigger("change");
        $(".tempDiv").remove();
        $(".slab-input").val("");
        $("#slab_formula_modular_1").val("").trigger("change");
        $("#slab_formula_modular_2").val("").trigger("change");



        $(".amount").addClass("d-none");
        $(".deduction-head").addClass("d-none");
        $(".formula-case").addClass("d-none");
        $(".from-deduction").addClass("d-none");
        $(".marginal-relief").addClass("d-none");

    }

    var key = <?php echo $key; ?>;
    $(document).on('click', '.addSlab', function () {
        if (key < 5) {
            var main_content = `<div class="row tempDiv">
                <div class="col-md-2 col-sm-2 ">
                    <div class="form-group row w-100 mx-0  ">
                        <div class="col-lg-2 col-md-2 col-sm-0" ></div>
                        <div class="col-lg-6 col-md-6 col-sm-6" >
                            <i class="text-primary"> Value</i>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4"> = </div>
                    </div>
                    
                </div>
                
                <div class="col-md-3 col-sm-3 ">
                    <div class="form-group row w-100 mx-0">
                        <div class="col-lg-12 col-md-12 col-12">
                            <input autocomplete="off" placeholder="Min" class="onlyNumber form-control slab-input" type="text" id="min_`+ key + `" name="min[` + key + `]" />
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3 ">
                    <div class="form-group row w-100 mx-0">
                        <div class="col-lg-12 col-md-12 col-12">
                            <input autocomplete="off" placeholder="Max" class="onlyNumber form-control slab-input" type="text" id="max_`+ key + `" name="max[` + key + `]" />
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3 ">
                    <div class="form-group row w-100 mx-0">
                        <div class="col-lg-12 col-md-12 col-12">
                            <input autocomplete="off" placeholder="Value" class="onlyNumber form-control slab-input" type="text" id="slab_value_`+ key + `" name="slab_value[` + key + `]" />
                        </div>
                    </div>
                </div>
                <div class="col-md-1 col-sm-1 ">
                    <button type="button" class="btn btn-sm btn-danger mb-2 removeSlab"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            `;

            $('#slabData').append(main_content);
            key++;



        }
        addSlabValidationRules();
    });

    $(document).on('click', '.removeSlab', function () {
        if (key > 1) {
            $(this).parent().parent().remove();
            key--;
            if (key < 5) {
                // $('.addExtraShiftHourSlab').prop("disabled", false);
            }
        }
        addSlabValidationRules();
    });

    function addSlabValidationRules() {
        var validator = $("#addDeductionRuleFrom").validate();
        $('#addDeductionRuleFrom').valid();
        $("[name^=min]").each(function () {
            $(this).rules("add", {
                required: true,
                messages: {
                    required: "Please Enter Min Value",
                }
            })
        });
        $("[name^=max]").each(function () {
            $(this).rules("add", {
                required: true,
                messages: {
                    required: "Please Enter Max Value",
                }
            })
        });
        $("[name^=slab_value]").each(function () {
            $(this).rules("add", {
                required: true,
                messages: {
                    required: "Please Enter Value",
                }
            })
        });
    }
    $(document).ready(function() {
    $('.multiple-select-tax_deduct').select2({
        placeholder: "Deduct From Salary",
        allowClear: true,
        width: '100%'
    });
});
$(document).on('change', '#amount_type, #rule_type', function() {
    setTimeout(function() {
        $('.multiple-select-tax_deduct').select2({
            placeholder: "Deduct From Salary",
            allowClear: true,
            width: '100%'
        });
    }, 100);
});
</script>