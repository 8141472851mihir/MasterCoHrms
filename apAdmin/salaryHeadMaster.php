<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

extract($_REQUEST);
$countryId = (isset($_GET['countryId']) && $_GET['countryId'] > 0) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId'], 101) : 101;

$dragpanel = 0;
if (isset($_REQUEST['dragpanel']) && (int)$_REQUEST['dragpanel'] == 1) {
    $dragpanel = (int)$_REQUEST['dragpanel'];
}
$earningDataArr = array();
$deductionDataArr = array();
$nonCashBenefitDataArr = array();
$typeQry = $d->select("salary_earning_deduction_type_master", "earn_deduct_is_delete=0 AND country_id='$countryId' ORDER BY earning_deduction_order ASC ");
while ($typeData = mysqli_fetch_array($typeQry)) {
    $is_earning_deduction_used = 0;
    if ($typeData['earning_deduction_type'] == 0 && $typeData['special_allowance_for_balance_salary_figure'] == 1) {
        $is_earning_deduction_used = 1;
    }
    $typeData['is_earning_deduction_used'] = $is_earning_deduction_used;
    if ($typeData['earning_deduction_type'] == 0) { // Earning
        array_push($earningDataArr, $typeData);
    } else if ($typeData['earning_deduction_type'] == 1) { // Deduction
        array_push($deductionDataArr, $typeData);
    } else if ($typeData['earning_deduction_type'] == 2) { // Non-Cash benefit
        array_push($nonCashBenefitDataArr, $typeData);
    }
}
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-3">
                <h4 class="page-title">Salary Head Types</h4>
            </div>

            <div class="col-lg-4">
                <form action="" method="get" accept-charset="utf-8">
                    <?php if ($dragpanel == 1) { ?>
                        <input type="hidden" name="dragpanel" value="1">
                    <?php } ?>
                    <div class="form-group row mb-0">
                        <div class="col-sm-10">
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

            <div class="col-5">
                <div class="btn-group float-sm-right">
                    <a href="syncSlabs?sync=Salary" class="btn mr-1 btn-sm btn-danger waves-effect waves-light"><i
              class="fa fa-plus mr-1"></i>Sync Data</a>
                    <?php if ($dragpanel == 1) { ?>
                        <a href="salaryHeadMaster?countryId=<?= $countryId ?>" class="btn mr-1 btn-sm btn-danger waves-effect waves-light"><i class="fa fa-sort" aria-hidden="true"></i> Back</a>
                    <?php } else { ?>
                        <form action="./controller/earningDeductionControlller.php" method="post">
                            <input type="hidden" name="importSalaryHead" value="importSalaryHead">
                            <input type="hidden" name="country_id" value="<?= $countryId ?>">
                            <button type="submit" class="btn btn-sm btn-warning waves-effect waves-light">Import Salary Head</button>
                        </form>
                        <a href="salaryHeadMaster?dragpanel=1&countryId=<?= $countryId ?>" class="btn mr-1 btn-sm btn-primary waves-effect waves-light"><i class="fa fa-sort" aria-hidden="true"></i> Change order</a>

                        <a href="javascript:void(0)" onclick="EarningDeductionTypeData(); buttonSetting();" class="btn mr-1 btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add</a>
                    <?php } ?>
                    
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>sr no</th>
                                        <th>earning name</th>
                                        <th>action</th>
                                    </tr>
                                </thead>
                                <tbody id="showFilterDatas" class="<?php if ($dragpanel == 1) { ?> earning-sortable <?php } ?>">
                                    <?php
                                    $counter = 1;
                                    foreach ($earningDataArr as $data) {
                                    ?>
                                        <tr class="earning-sortable-item" data-id="<?php echo $data['salary_earning_deduction_id']; ?>">
                                            <td class="earning-serial-number"><?php echo $counter++; ?></td>
                                            <td><?php echo $data['earning_deduction_name']; ?>
                                                <?php if ($data['is_bonus_allowance'] == 1) {
                                                    echo ' (Bonus allowance)';
                                                } else if ($data['special_allowance_for_balance_salary_figure'] == 1) {
                                                    echo ' (Special allowance)';
                                                }  ?></td>
                                            <td align="left">
                                                <div class="d-flex align-items-center">
                                                    <?php
                                                    if ($dragpanel == 1) { ?>
                                                        <button class="btn mr-1 btn-sm btn-primary waves-effect waves-light earning-drag-btn"><i class="fa fa-sort" aria-hidden="true"></i> drag button</button>

                                                        <?php } else {

                          $buttonClass = ($data['salary_earning_deduction_status'] == "0") ? 'btn-success-new' : 'btn-danger';
                          $buttonCondition = ($data['salary_earning_deduction_status'] == "0") ? 'Active' : 'Deactive';
                          $posText = 'Active';
                          $negText = 'Deactive';
                          $status = ($data['salary_earning_deduction_status'] == "0") ? 'EarningDeductionStatusDeactive' : 'EarningDeductionStatusActive';
                          $newStatus = ($data['salary_earning_deduction_status'] == "0") ? 'EarningDeductionStatusActive' : 'EarningDeductionStatusDeactive';
                          $newStatusVal = ($data['salary_earning_deduction_status'] == "0") ? '1' : '0';
                          $statusValue = ($data['salary_earning_deduction_status'] == "0") ? '0' : '1';
                          ?>

                          <input type="button" class="btn btn-sm pl-1 pr-1 w-50 <?php echo $buttonClass ?>" id="<?php echo 'earning_deduction_id_' . $data['salary_earning_deduction_id']; ?>" onclick="changeStatusNew('<?php echo $data['salary_earning_deduction_id']; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'earning_deduction_id_' . $data['salary_earning_deduction_id']; ?>','','','./controller/earningDeductionControlller.php','<?php echo $negText; ?>','<?php echo $posText; ?>');" data-size="small" value="<?php echo $buttonCondition ?>" /> 
                                                        <button type="button" class="btn btn-sm btn-primary ml-1 mr-1" onclick="EarningDeductionTypeData(<?php echo $data['salary_earning_deduction_id']; ?>)"> <i class="fa fa-pencil"></i></button>
                                                    <?php 
                                                    } ?>
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
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>sr no</th>
                                        <th>deduction name</th>
                                        <th>action</th>
                                    </tr>
                                </thead>
                                <tbody id="showFilterData" class="<?php if ($dragpanel == 1) { ?> deduction-sortable <?php } ?>">
                                    <?php
                                    $counter = 1;
                                    foreach ($deductionDataArr as $data) {
                                    ?>
                                        <tr class="deduction-sortable-item" data-id="<?php echo $data['salary_earning_deduction_id']; ?>">
                                            <td class="deduction-serial-number"><?php echo $counter++; ?></td>
                                            <td><?php echo $data['earning_deduction_name']; ?>
                                                <?php if ($data['is_tds_head'] == 1) {
                                                    echo '(TDS Head)';
                                                } else if ($data['is_gratuity_head'] == 1) {
                                                    echo ' ("gratuity head")';
                                                } else if ($data['is_special_deduction_head'] == 1) {
                                                    echo ' (Special deduction)';
                                                }  ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php
                                                    if ($dragpanel == 1) { ?>
                                                        <button class="btn mr-1 btn-sm btn-primary waves-effect waves-light deduction-drag-btn"><i class="fa fa-sort" aria-hidden="true"></i> drag button</button>

                                                        <?php } else {
                          $buttonClass = ($data['salary_earning_deduction_status'] == "0") ? 'btn-success-new' : 'btn-danger';
                          $buttonCondition = ($data['salary_earning_deduction_status'] == "0") ? 'Active' : 'Deactive';
                          $posText = 'Active';
                          $negText = 'Deactive';
                          $status = ($data['salary_earning_deduction_status'] == "0") ? 'EarningDeductionStatusDeactive' : 'EarningDeductionStatusActive';
                          $newStatus = ($data['salary_earning_deduction_status'] == "0") ? 'EarningDeductionStatusActive' : 'EarningDeductionStatusDeactive';
                          $newStatusVal = ($data['salary_earning_deduction_status'] == "0") ? '1' : '0';
                          $statusValue = ($data['salary_earning_deduction_status'] == "0") ? '0' : '1';
                          ?>

                          <input type="button" class="btn btn-sm pl-1 pr-1 w-50 <?php echo $buttonClass ?>" id="<?php echo 'earning_deduction_id_' . $data['salary_earning_deduction_id']; ?>" onclick="changeStatusNew('<?php echo $data['salary_earning_deduction_id']; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'earning_deduction_id_' . $data['salary_earning_deduction_id']; ?>','','','./controller/earningDeductionControlller.php','<?php echo $negText; ?>','<?php echo $posText; ?>');" data-size="small" value="<?php echo $buttonCondition ?>" /> 
                                                        <button type="button" class="btn btn-sm btn-primary ml-1 mr-1" onclick="EarningDeductionTypeData(<?php echo $data['salary_earning_deduction_id']; ?>)"> <i class="fa fa-pencil"></i></button>
                                                    <?php 
                                                    } ?>
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
<div class="modal fade" id="EarningDeductionModal">
    <div class="modal-dialog ">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white"> Earning deduction type </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="billPayDiv" style="align-content: center;">
                <div class="card-body">
                    <form id="earningDeductionForm" action="controller/earningDeductionControlller.php" enctype="multipart/form-data" method="post">
                        <div class="form-group row">
                            <label for="country_id" class="col-sm-12 col-form-label">Country <span class="required">*</span></label>
                            <div class="col-lg-12 col-md-12">
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
                        </div>

                        <div class="form-group row">
                            <label for="earning_deduction_name" class="col-sm-12 col-form-label">Earning deduction name <span class="required">*</span></label>
                            <div class="col-lg-12 col-md-12" id="">
                                <input type="text" required="" name="earning_deduction_name" id="earning_deduction_name" class="form-control" maxlength="100">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="earning_deduction_type" class="col-sm-12 col-form-label">earning deduction type <span class="required">*</span></label>
                            <div class="col-lg-12 col-md-12" id="">
                                <select name="earning_deduction_type" id="earning_deduction_type" class="form-control single-select" onchange="earningDeductionType(this.value);">
                                    <option value="0">Earnings</option>
                                    <option value="1">Deductions</option>
                                    <!-- <option value="2">Non cash benefit</option> -->
                                </select>
                            </div>
                        </div>

                        <div class="form-group row allowance-type">
                            <label for="allowance_type" class="col-sm-12 col-form-label">Allowance type <span class="required">*</span></label>
                            <div class="col-lg-12 col-md-12" id="">
                                <select name="allowance_type" id="allowance_type" class="form-control single-select">
                                    <option value="0">None</option>
                                    <option value="1">Bonus allowance</option>
                                    <!-- <option value="2">special allowance</option> -->
                                </select>
                            </div>
                        </div>

                        <div class="form-group row deduction-head-type">
                            <label for="deduction_head_type" class="col-sm-12 col-form-label">Deduction head <span class="required">*</span></label>
                            <div class="col-lg-12 col-md-12" id="">
                                <select name="deduction_head_type" id="deduction_head_type" class="form-control single-select">
                                    <option value="0">none</option>
                                    <option value="1">TDS head </option>
                                    <option value="2">Gratuity head</option>
                                    <!-- <option value="3">Special deduction</option> -->
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="earning_deduction_description" class="col-sm-12 col-form-label">Description </label>
                            <div class="col-lg-12 col-md-12" id="">
                                <textarea name="earning_deduction_description" id="earning_deduction_description" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="form-footer text-center">
                            <input type="hidden" name="addEarnDeductType" value="addEarnDeductType">
                            <input type="hidden" id="earning_deduction_id" name="earning_deduction_id" value="">
                            <button id="updateEarnDeductType" name="addSiteBtn" type="submit" class="btn btn-success sbmitbtn hideupdate"><i class="fa fa-check-square-o"></i> Update</button>
                            <button name="addEarnDeductType" id="addEarnDeductType" type="submit" class="btn btn-success hideAdd"><i class="fa fa-check-square-o"></i> Add</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
<script src="assets/js/jquery.min.js"></script>

<script type="text/javascript">
    var filterCountryId = '<?= $countryId ?>';

    function popitup(url) {
        newwindow = window.open(url, 'name', 'height=800,width=900, location=0');
        if (window.focus) {
            newwindow.focus()
        }
        return false;
    }

    function earningDeductionType(val) {
        $('.allowance-type').addClass("d-none");
        $('.deduction-head-type').addClass("d-none");
        if (val == 1) {
            $('.deduction-head-type').removeClass("d-none");
        }
        if (val == 0) {
            $('.allowance-type').removeClass("d-none");
        }
    }

    //Drag

    function updatePostionEerningDeduction(order, earningdeduction) {

        var csrf = "<?php echo $_SESSION["token"]; ?>";

        $.ajax({
            url: "controller/earningDeductionControlller.php",
            method: "POST",
            data: {
                order: order,
                csrf: csrf,
                earningdeduction: earningdeduction,
                earningDeductionType: 'earningDeductionType'
            },
            dataType: 'json',
            cache: false,
            success: function(response) {
                if (response.status == '200') {
                    swal(response.message, {
                        icon: "success",
                        timer: 1000
                    });
                } else {
                    swal(response.message, {
                        icon: "error",
                    });
                }
            },
            error: function(xhr, status, error) {
                swal("Error updating order", {
                    icon: "error",
                });
            }
        });
    }
    $(function() {
        $(".earning-sortable").sortable({
            placeholder: "ui-state-highlight",
            handle: '.earning-drag-btn',
            cancel: '',
            update: function(event, ui) {

                var order = [];

                $(".earning-sortable-item").each(function(index) {
                    $(this).find('.earning-serial-number').text((index + 1));
                    order.push({
                        id: $(this).data("id"),
                        position: index + 1
                    });
                });

                updatePostionEerningDeduction(order, 1);

            }
        });

        $(".deduction-sortable").sortable({
            placeholder: "ui-state-highlight",
            handle: '.deduction-drag-btn',
            cancel: '',
            update: function(event, ui) {

                var csrf = "<?php echo $_SESSION["token"]; ?>";
                var order = [];

                $(".deduction-sortable-item").each(function(index) {
                    $(this).find('.deduction-serial-number').text((index + 1));
                    order.push({
                        id: $(this).data("id"),
                        position: index + 1
                    });
                });

                updatePostionEerningDeduction(order, 2);
            }
        });

        $(".non-cash-benefit-sortable").sortable({
            placeholder: "ui-state-highlight",
            handle: '.non-cash-benefit-drag-btn',
            cancel: '',
            update: function(event, ui) {

                var csrf = "<?php echo $_SESSION["token"]; ?>";
                var order = [];

                $(".non-cash-benefit-sortable-item").each(function(index) {
                    $(this).find('.non-cash-benefit-serial-number').text((index + 1));
                    order.push({
                        id: $(this).data("id"),
                        position: index + 1
                    });
                });

                updatePostionEerningDeduction(order, 3);
            }
        });
    });

    function EarningDeductionTypeData(id) {
        if (id != '' && id != undefined) {
            $.ajax({
                url: './controller/earningDeductionControlller.php',
                cache: false,
                type: 'POST',
                dataType: 'json', 
                data: {
                    action: 'getEarningDeductionById',
                    salary_earning_deduction_type_id: id,
                    csrf: csrf,
                },
                success: function(response) {
                    $('#EarningDeductionModal').modal();
                    $('#addEarnDeductType').hide();
                    $('#updateEarnDeductType').show();
                    $('#earning_deduction_id').val(response.salary_earning_deduction_type_master.salary_earning_deduction_id);
                    $('#earning_deduction_name').val(response.salary_earning_deduction_type_master.earning_deduction_name);
                    $('#earning_deduction_description').val(response.salary_earning_deduction_type_master.earning_deduction_description);
                    $('#earning_deduction_type').val(response.salary_earning_deduction_type_master.earning_deduction_type);
                    $('#earning_deduction_type').select2();
                    $('#country_id').val(response.salary_earning_deduction_type_master.country_id || filterCountryId).trigger('change');

                    $('.allowance-type').addClass('d-none');
                    $('.deduction-head-type').addClass('d-none');
                    if (response.salary_earning_deduction_type_master.earning_deduction_type == 1) {
                        $('.deduction-head-type').removeClass('d-none');
                        if (response.salary_earning_deduction_type_master.is_tds_head == 1) {
                            $('#deduction_head_type').val(1).select2();
                        } else if (response.salary_earning_deduction_type_master.is_gratuity_head == 1) {
                            $('#deduction_head_type').val(2).select2();
                        } else if (response.salary_earning_deduction_type_master.is_special_deduction_head == 1) {
                            $('#deduction_head_type').val(3).select2();
                        } else {
                            $('#deduction_head_type').val(0).select2();
                        }
                    } else if (response.salary_earning_deduction_type_master.earning_deduction_type == 0) {
                        $('.allowance-type').removeClass('d-none');
                        if (response.salary_earning_deduction_type_master.is_bonus_allowance == 1) {
                            $('#allowance_type').val(1).select2();
                        } else if (response.salary_earning_deduction_type_master.special_allowance_for_balance_salary_figure == 1) {
                            $('#allowance_type').val(2).select2();
                        } else {
                            $('#allowance_type').val(0).select2();
                        }
                    }
                },
            });
        } else {
            $('#earning_deduction_type').val(0).select2();
            $('#deduction_head_type').val(0).select2();
            $('#allowance_type').val(0).select2();
            $('#earning_deduction_id').val('');
            $('#country_id').val(filterCountryId || '101').trigger('change');
            $('.allowance-type').removeClass('d-none');
            $('.deduction-head-type').addClass('d-none');
            $('#EarningDeductionModal').modal();
        }
    }

    function buttonSetting() {
        $('.hideupdate').hide();
        $('.hideAdd').show();
    }
</script>
<style>
    .hideupdate {
        display: none;
    }
</style>
