<?php
extract($_REQUEST);
// error_reporting(0);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
$countryId =  (isset($_GET['countryId']) && $_GET['countryId'] > 0) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId'], 101) : 101;
$sId =  (isset($_GET['sId']) && $_GET['sId'] > 0) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
$cId =  (isset($_GET['cId']) && $_GET['cId'] > 0) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : 0;
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Company</h4>
            </div>
            <div class="col-sm-3 text-right">
                <?php if (isset($countryId) && $countryId > 0) {  ?>
                    <form action="companyCrmSyncData" method="POST">
                        <input type="hidden" name="country_id" value="<?php if (isset($_GET['countryId'])) {
                                                                            echo $_GET['countryId'];
                                                                        } ?>">
                        <input type="hidden" name="state_id" value="<?php if (isset($_GET['sId'])) {
                                                                        echo $_GET['sId'];
                                                                    } ?>">
                        <input type="hidden" name="city_id" value="<?php if (isset($_GET['cId'])) {
                                                                        echo $_GET['cId'];
                                                                    } ?>">
                        <button type="submit" name="publishPost" value="publishPost" class="open-AddBookDialog btn btn-secondary btn-sm"><i class="fa fa-database"></i> Sync CRM Data</button>
                    </form>
                <?php } ?>
            </div>
        </div>
        <!-- End Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-lg-12">
                <form action="" method="get" accept-charset="utf-8">
                    <div class="form-group row">

                        <label for="country_id" class="col-sm-1 col-form-label"> Country <span class="required">*</span></label>
                        <div class="col-sm-3">
                            <select type="text" required="" id="country_id" onchange="this.form.submit()" class="form-control single-select" name="countryId">
                                <option value="">-- Select --</option>
                                <?php
                                $qc = $d->select("countries", "flag=1");
                                while ($cData = mysqli_fetch_array($qc)) {
                                ?>
                                    <option <?php if (isset($countryId) && $cData['country_id'] == $countryId) {
                                                echo "selected";
                                            } ?> value="<?php echo $cData['country_id']; ?>"><?php echo $cData['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <label for="state_id" class="col-sm-1 col-form-label"> State </label>
                        <div class="col-sm-3">
                            <?php if (isset($countryId)) {
                            ?>
                                <select type="text"  onchange="document.getElementById('city_id').value=''; this.form.submit();" required="" class="form-control single-select" id="state_id" name="sId">
                                    <option value=""> All</option>
                                    <?php
                                    $qs = $d->select("states", "country_id='$countryId'");
                                    while ($sData = mysqli_fetch_array($qs)) {
                                    ?>
                                        <option <?php if (isset($_GET['sId']) && $sData['state_id'] == $_GET['sId']) {
                                                    echo "selected";
                                                } ?> value="<?php echo $sData['state_id']; ?>"><?php echo $sData['name']; ?></option>
                                    <?php }  ?>
                                </select>
                            <?php } else { ?>
                                <select type="text" onchange="getCity();" required="" class="form-control single-select" id="state_id" name="sId">
                                    <option value="">-- Select --</option>
                                </select>
                            <?php } ?>
                        </div>

                        <label for="input-101" class="col-sm-1 col-form-label"> City</label>
                        <div class="col-sm-3">
                            <?php if (isset($_GET['cId']) && $sId > 0) {

                            ?>
                                <select onchange="this.form.submit()" type="text" required="" class="form-control single-select" id="city_id" name="cId">
                                    <option value=""> All</option>
                                    <?php
                                    if (isset($sId) && $sId > 0) {
                                        $appendStateQueryFilter = "state_id='$sId'";
                                    }

                                    $qcity = $d->select("cities", " $appendStateQueryFilter");
                                    while ($cityData = mysqli_fetch_array($qcity)) {
                                    ?>
                                        <option <?php if (isset($_GET['cId']) && $cityData['city_id'] == $_GET['cId']) {
                                                    echo "selected";
                                                } ?> value="<?php echo $cityData['city_id']; ?>"><?php echo $cityData['name']; ?></option>
                                    <?php }  ?>
                                </select>
                            <?php } else { ?>
                                <select onchange="this.form.submit()" type="text" required="" class="form-control single-select" name="cId" id="city_id">
                                    <option value="">-- Select --</option>

                                </select>
                            <?php } ?>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        <?php if (isset($countryId) && filter_var($countryId, FILTER_VALIDATE_INT) == true) {  ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="viewCompaniesTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Id</th>
                                            <th>Company</th>
                                            <th>Company Code</th>
                                            <th>City</th>
                                            <th>App Menu</th>
                                            <th>Banners</th>
                                            <th>Splash</th>
                                            <th>Rise Event</th>
                                             <th>Settings</th>
                                            <?php if ($role_id == 1) { ?>
                                                <th>Institute</th>
                                                <th>CRM</th>
                                                <th>Server Url</th>
                                                <th>Delete</th>
                                                <th>Company Details</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                  
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else {
            echo "Select Country";
        } ?>
    </div>
</div>
<div class="modal fade" id="splashModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Manage Company Splash</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="addUserDiv">

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function getAllBuildingData(request_society_id) {
        $.ajax({
            url: "getBuildingDetails.php",
            cache: false,
            type: "POST",
            data: {
                setting_society_id: request_society_id
            },
            success: function(response) {
                $('#settingsForm').html(response);
            }
        });
    }
</script>

<div class="modal fade" id="settingModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Company Settings</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="settingsForm" action="controller/buildingController.php" method="post">

                </form>
            </div>
        </div>
    </div>
</div>
<!-- // Mukesh end  5-6-24 -->

<div class="modal fade" id="addInstituteModel">
    <div class="modal-dialog ">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Add Institute</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="row ">
                        <div class="col-md-12">
                            <form id="addInstituteForm" action="controller/buildingController.php" enctype="multipart/form-data" method="post">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="institutePassword" class="col-sm-12 col-form-label"> Institute Password <span class="text-danger">*</span></label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <input type="text" class="form-control" id="institutePassword" name="institutePassword" value="" placeholder="Enter Institute Password"></input>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="academicYearTitle" class="col-sm-12 col-form-label"> Academic Year Title <span class="text-danger">*</span></label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <input type="text" class="form-control" id="academicYearTitle" name="academicYearTitle" value="" placeholder="Enter Academic Year Title"></input>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="academicYearCode" class="col-sm-12 col-form-label"> Academic Year Code <span class="text-danger">*</span></label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <input type="text" class="form-control" id="academicYearCode" name="academicYearCode" value="" placeholder="Enter Academic Year Code"></input>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="academicYearStartDate" class="col-sm-12 col-form-label"> Academic Year Start Date <span class="text-danger">*</span></label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <input type="text" class="form-control" id="academicYearStartDate" name="academicYearStartDate" value="" placeholder="Academic Year Start Date" readonly></input>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="academicYearEndDate" class="col-sm-12 col-form-label"> Academic Year End Date <span class="text-danger">*</span></label>
                                        <div class="col-lg-12 col-md-12" id="">
                                            <input type="text" class="form-control" id="academicYearEndDate" name="academicYearEndDate" value="" placeholder="Academic Year End Date" readonly></input>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-footer text-center">

                                    <input type="hidden" name="companyId" value="" id="instituteCompanyId">
                                    <input type="hidden" name="addInstitute" value="addInstitute">
                                    <button type="submit" class="btn btn-sm btn-success" id=""><i class="fa fa-check-square-o"></i> Add </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="createCrm">
    <div class="modal-dialog ">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Create CRM</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="row ">
                        <div class="col-md-12">
                            <form id="createCrmForm" action="controller/buildingController.php" method="post">
                                <div class="form-group align-items-center">
                                    <div class="form-group">
                                        <label for="input-14">Plan <span class="required">*</span></label>
                                        <select required name="crm_package_id" class="form-control single-select check" id="input-14">
                                            <option value="">-- Select Plan --</option>
                                            <?php
                                            $planQuery = $d->select("manage_plan", "status = 0");
                                            $selectedPlanId = isset($existingPlanId) ? $existingPlanId : '';

                                            if (mysqli_num_rows($planQuery) > 0) {
                                                while ($plan = mysqli_fetch_array($planQuery)) {
                                                    $isSelected = ($plan['plan_value'] == $selectedPlanId) ? 'selected' : '';
                                                    echo "<option value='" . $plan['plan_value'] . "' $isSelected>" . htmlspecialchars($plan['plan_name']) . "</option>";
                                                }
                                            } else {
                                                echo "<option value=''>No Plans Available</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group plan_expire_date_div">
                                        <label for="plan_expire_date" class="plan_expire_date">CRM Plan Expire Date <span
                                                class="required">*</span></label>
                                        <input type="text" readonly maxlength="120" value="" required
                                            class="form-control facility_datepicker" name="crm_plan_expiring_date" id="plan_expire_date">
                                    </div>
                                    <div class="form-group" id="TrialDiv2">
                                        <label id="TrialDiv1" for="trlDays">Trial Days <span class="required">*</span></label>
                                        <input type="text" min="0" max="100" maxlength="3" class="form-control" autocomplete="off"
                                            name="crm_trial_days" id="trlDays" value="">
                                    </div>
                                    <label for="crm_limit" class="mr-2 mb-0" style="min-width: 100;">CRM Limit</label>
                                    <input type="text" autocomplete="off" class="form-control onlyNumber" name="crm_limit" id="crm_limit"
                                        placeholder="Enter CRM Limit" required>
                                    <input type="hidden" id="society_id" name="society_id">
                                </div>
                                <select type="text" required="" id="society_crm_id" class="form-control single-select"
                                    name="society_crm_id">
                                </select>
                                <input type="hidden" name="companyId" class="companyId">
                                <input type="hidden" name="companyName" id="companyName">
                                <input type="hidden" name="subDomain" id="subDomain">
                                <input type="hidden" name="createCRM" value="createCRM">
                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-sm btn-primary waves-effect waves-light m-1" title="Create CRM">
                                        Create CRM <i class="fa fa-users"></i> </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script>
    var table;
    $(document).ready(function() {

        table = $('#viewCompaniesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'ajax/viewCompaniesTable.php',
                type: 'POST',
                data: function(d) {
                    d.countryId = <?php echo $countryId; ?>;
                    d.sId = <?php echo isset($sId) ? (int)$sId : 0; ?>;
                    d.cId = <?php echo isset($cId) ? (int)$cId : 0; ?>;
                }
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'company_id_display' },
                { data: 'company_name' },
                { data: 'society_code' },
                { data: 'city_name' },
                { data: 'app_menu' },
                { data: 'banners' },
                { data: 'splash' },
                { data: 'rise_event' },
                { data: 'settings' }
                <?php if ($role_id == 1) { ?>,
                { data: 'institute' },
                { data: 'crm' },
                { data: 'server_url' },
                { data: 'delete_company' },
                { data: 'company_details' }
                <?php } ?>
            ],
            columnDefs: [{
                targets: <?php echo ($role_id == 1) ? '[5,6,7,8,9,10,11,12,13,14]' : '[5,6,7,8,9]'; ?>,
                orderable: false,
                searchable: false
            }],
            order: [
                [1, 'desc']
            ],
            pageLength: 10
        });
        
        function formatDate(date) {
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            return date.getFullYear() + "-" + month + "-" + day;
        }

        function updateTrialExpireDate() {
            const trialDays = parseInt($('#trlDays').val());
            if (!isNaN(trialDays) && trialDays > 0) {
                const now = new Date();
                now.setDate(now.getDate() + trialDays - 1);
                let expireDate = formatDate(now);
                $('#plan_expire_date').val(expireDate);
                $('#crm_trial_days_date').val(expireDate);
            } else {
                $('#plan_expire_date').val('');
                $('#crm_trial_days_date').val('');
            }
        }


        function toggleFields(packageId) {
            if (packageId === '0') {
                $('#TrialDiv1, #TrialDiv2').show();
                $('.plan_expire_date_div, .plan_expire_date').hide();
                $('#plan_expire_date').val('');
            } else if (packageId !== '' && !isNaN(packageId)) {
                $('#TrialDiv1, #TrialDiv2').hide();
                $('.plan_expire_date_div, .plan_expire_date').show();
                $('#trlDays').val('');
                let dataTemp = parseInt(packageId);
                if (!isNaN(dataTemp)) {
                    let now = new Date();
                    let nextMonth = new Date(now.setMonth(now.getMonth() + dataTemp));
                    // $('#plan_expire_date').val(formatDate(nextMonth));
                    $('#plan_expire_date').datepicker('setDate', nextMonth).datepicker('setStartDate', formatDate(new Date()));
                } else {
                    $('#plan_expire_date').val('');
                }
            } else {
                $('#TrialDiv1, #TrialDiv2').hide();
                $('.plan_expire_date_div, .plan_expire_date').show();
                $('#trlDays').val('');
                $('#plan_expire_date').val('');
            }
        }

        $('.plan_expire_date_div, .plan_expire_date').show();

        $('.check').change(function() {
            toggleFields($(this).val());
        });

        $('#trlDays').on('input', function() {
            if ($('.check').val() === '0') {
                updateTrialExpireDate();
            }
        });

        toggleFields($('.check').val());
    });
</script>