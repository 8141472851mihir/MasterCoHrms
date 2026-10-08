<?php error_reporting(0);
$bId = isset($_REQUEST['bId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['bId']) : 0;
$dId = isset($_REQUEST['dId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['dId']) : 0;
$uId = isset($_REQUEST['uId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['uId']) : 0;
$currentYear = date('Y');
$currentMonth = date('m');
$nextYear = date('Y', strtotime('+1 year'));
$onePreviousYear = date('Y', strtotime('-1 year'));
$twoPreviousYear = date('Y', strtotime('-2 year'));
$_GET['month_year'] = (isset($_REQUEST['month_year']) && $_REQUEST['month_year'] !== '')
    ? $d->sanitizeReportFilterMonthYear($_REQUEST['month_year'], date('Y-m'))
    : '';
$from = $d->sanitizeReportFilterDate(isset($_GET['from']) ? $_GET['from'] : '', '');
$toDate = $d->sanitizeReportFilterDate(isset($_GET['toDate']) ? $_GET['toDate'] : '', '');

?>
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row ">
            <div class="col-sm-3">
                <h4 class="page-title">CRM Plan Expire</h4>
            </div>
            <div class="col-sm-9">
                <form action="" class="branchDeptFilter">
                    <div class="row ">
                        <div class="col-md-2 col-6 form-group">
                            <input type="text" class="form-control" autocomplete="off" id="autoclose-datepickerFrom"
                                name="from" value="<?php echo htmlspecialchars($from); ?>">
                        </div>
                        <div class="col-md-2 col-6 form-group">
                            <input type="text" class="form-control" autocomplete="off" id="autoclose-datepickerTo"
                                name="toDate" value="<?php echo htmlspecialchars($toDate); ?>">
                        </div>
                        <div class="col-md-3 form-group">
                            <input class="btn btn-success btn-sm " type="submit" name="getReport" value="Get">
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- End Breadcrumb-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="reportTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Id</th>
                                        <th>Company Name</th>
                                        <th>City</th>
                                        <th>Rise Event</th>
                                        <th>Campaign Region</th>
                                        <th>Mobile</th>
                                        <th>CRM Create Date</th>
                                        <th>Plan</th>
                                        <th>Days Left</th>
                                        <th>Plan Expire</th>
                                        <th>Crm Limit</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th class="no-search-box"></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    if (isset($from) && isset($from) && $toDate != '' && $toDate) {
                                        $where1 = " AND crm_plan_expiring_date BETWEEN '$from' AND '$toDate'";
                                    } else {
                                        $where1 = "";
                                    }

                                    $q = $d->selectRow(
                                        "society_master.*,manage_plan.plan_value,manage_plan.plan_name,server_master.server_name,server_master.server_ip,domain_master.domain_name,cities.name,cities.city_id",
                                        "society_master LEFT JOIN manage_plan ON manage_plan.plan_value = society_master.crm_package_id LEFT JOIN domain_master ON society_master.domain_id=domain_master.domain_id LEFT JOIN server_master ON server_master.server_id=domain_master.server_id LEFT JOIN cities ON society_master.city_id=cities.city_id",
                                        "society_id!=0 AND crm_created=1 $where1",
                                        "order by crm_plan_expiring_date ASC"
                                    );
                                    while ($data = mysqli_fetch_array($q)) {
                                        extract($data);

                                    ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo '' . $d->short_app_name() . '_' . $society_id; ?></td>
                                            <td><?php echo $society_name; ?></td>
                                            <td><?php echo $name; ?></td>
                                            <td><?php echo ($from_rise_event == '1') ? 'Yes' : 'No'; ?></td>
                                            <td><?php echo $region_name; ?></td>
                                            <td><?php echo $secretary_mobile; ?></td>
                                            <td><?php echo $crm_created_date; ?></td>
                                            <td>
                                                <?php
                                                if ($crm_created == 0) {
                                                    echo "<span class='text-danger'>CRM Not Created</span>";
                                                } else {
                                                    if ($plan_value == 0) {
                                                        if ($crm_plan_expiring_date != '') {
                                                            echo "Custom Plan";
                                                        } else {
                                                            echo "";
                                                        }
                                                    } else {
                                                        echo $plan_name;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($crm_created != 0) {
                                                    if ($crm_plan_expiring_date != '') {
                                                        $now = time();
                                                        $your_date = strtotime($crm_plan_expiring_date);
                                                        $datediff = $your_date - $now;
                                                        echo ($datediff > 0) ? round($datediff / (60 * 60 * 24)) : "Expired";
                                                    } else {
                                                        echo "";
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($crm_created != 0) {
                                                    if ($default_time_zone != "Asia/Kolkata") {
                                                        echo $d->change_timezone($crm_plan_expiring_date, $default_time_zone, 'Y-m-d');
                                                    } else {
                                                        echo $crm_plan_expiring_date;
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $crm_limit; ?></td>
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



<div class="modal fade" id="updatePlan">
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Update Plan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="planUpdateValidation" action="controller/planController.php" method="post">
                    <input type="hidden" id="society_id" name="society_id">
                    <input type="hidden" id="base_url" name="society_base_url">
                    <input type="hidden" id="package_id" name="package_id">
                    <input type="hidden" id="society_name" name="society_name">
                    <div class="form-group row">
                        <label for="input-10" class="col-sm-4 col-form-label">Date</label>
                        <div class="col-sm-8">
                            <input required="" type="text" autocomplete="off" class="form-control"
                                id="autoclose-datepicker" name="update_plan">
                        </div>
                    </div>
                    <div class="form-footer text-center">
                        <button type="submit" name="update" value="update" class="btn btn-primary"><i
                                class="fa fa-check-square-o"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function updateSocPlan(base_url, society_id, crm_plan_expiring_date, package_id, society_name) {
        $('#society_id').val(society_id);
        $('#base_url').val(base_url);
        $('#autoclose-datepicker').val(crm_plan_expiring_date);
        $('#package_id').val(package_id);
        $('#society_name').val(society_name);
    }
</script>


<div class="modal fade" id="editFloor">
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Company Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="BlockResp">

            </div>

        </div>
    </div>
</div><!--End Modal -->

<script type="text/javascript">
    function getSocietyData1(society_id) {
        var csrf = $('input[name="csrf"]').val();
        $('#BlockResp').html("Please Wait..!");
        $.ajax({
            url: "controller/cronGetData.php",
            cache: false,
            type: "POST",
            data: {
                society_id: society_id,
                csrf: csrf
            },
            success: function(response) {
                $('#BlockResp').html(response);

            }
        });
    }
</script>


<script src="assets/js/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        $('.check').change(function() {
            var data = $(this).val();
            if (data != '') {

                var now = new Date();
                var dataTemp = parseInt(data);
                // Add one month to the current date
                var next_month = new Date(now.setMonth(now.getMonth() + dataTemp));

                // Manual date formatting
                var day = ("0" + next_month.getDate()).slice(-2);
                var month = ("0" + (next_month.getMonth() + 1)).slice(-2);
                var next_month_string = next_month.getFullYear() + "-" + (month) + "-" + (day);

                $('#plan_expire_date').val(next_month_string);

            }
        });

        function toggleFields(packageId) {
            if (packageId == '0') {
                $('#TrialDiv1, #TrialDiv2').show();
                $('.plan_expire_date, .plan_expire_date_div').hide();
            } else {
                $('#TrialDiv1, #TrialDiv2').hide();
                $('.plan_expire_date, .plan_expire_date_div').show();
            }
        }
        var initialPackageId = $('.check').val();
        toggleFields(initialPackageId);

        $('.check').change(function() {
            var selectedPackageId = $(this).val();
            toggleFields(selectedPackageId);
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {

        $('.check').change(function() {
            var data = $(this).val();
            if (data != '') {

                var now = new Date();
                var dataTemp = parseInt(data);
                // Add one month to the current date
                var next_month = new Date(now.setMonth(now.getMonth() + dataTemp));

                // Manual date formatting
                var day = ("0" + next_month.getDate()).slice(-2);
                var month = ("0" + (next_month.getMonth() + 1)).slice(-2);
                var next_month_string = next_month.getFullYear() + "-" + (month) + "-" + (day);

                $('#crm_plan_expiring_date').val(next_month_string);

            }
        });

    });

    function setRefundSociety(societyId) {
        document.getElementById('refund_society_id').value = societyId;
    }
</script>