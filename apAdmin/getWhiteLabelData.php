<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$selectedAnalytics = isset($_GET['analytics']) ? $_GET['analytics'] : '0';
$curr_date = date('Y-m-d');
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-3">
                <h4 class="page-title">White Label Analytics Data</h4>
            </div>
            <div class="col-lg-9">
                <form action="" class="row" method="get" accept-charset="utf-8">
                    <div class="col-lg-5 float-left">
                        <select type="text" required="" id="analytics" onchange="this.form.submit();" class="form-control single-select" name="analytics">
                            <option value="" <?php if ($selectedAnalytics === '') echo 'selected'; ?>>-- Select --</option>
                            <option value="0" <?php if ($selectedAnalytics === '0') echo 'selected'; ?> selected>MyCo</option>
                            <option value="1" <?php if ($selectedAnalytics === '1') echo 'selected'; ?>>Smart Society</option>
                            <option value="2" <?php if ($selectedAnalytics === '2') echo 'selected'; ?>>My Association</option>
                        </select>
                    </div>
                    <!-- DateTime Picker -->
                    <div class="col-lg-5">
                        <?php $selectedDate = isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : date('Y-m-d'); ?>
                        <input type="text" class="form-control datepicker" name="start_date" id="selectedDate" value="<?php echo $selectedDate; ?>" required>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="sendWhiteLabelDatafetch" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" />
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <div class="col-lg-12 d-flex">
                                        <div class="col-lg-6 ">
                                            <b>Not Sync Company</b>
                                        </div>
                                    </div>
                                    <?php
                                    $companySrId = 1;
                                    if ($selectedAnalytics === '0'){
                                        $query = $d->selectRow(" wl.society_id, wl.master_company_id, wl.society_name, wl.sub_domain, wl.project_type, MAX(wla.created_at) AS created_date", "society_master_white_label wl LEFT JOIN society_whitelable_analytics_master wla ON wl.society_id = wla.society_id", " wl.project_type = '$selectedAnalytics' AND wl.society_id NOT IN (SELECT society_id FROM society_whitelable_analytics_master WHERE fetch_date = '$selectedDate')", "GROUP BY wl.society_id ORDER BY wl.society_id;");
                                    }else{
                                        $query = $d->selectRow(" wl.society_id, wl.master_company_id, wl.society_name, wl.sub_domain, wl.project_type, MAX(wla.created_at) AS created_date", "society_master_white_label wl LEFT JOIN white_label_analytics_master wla ON wl.society_id = wla.society_id", " wl.project_type = '$selectedAnalytics' AND wl.society_id NOT IN (SELECT society_id FROM white_label_analytics_master WHERE fetch_date = '$selectedDate')", "GROUP BY wl.society_id ORDER BY wl.society_id;");
                                    }

                                    if (mysqli_num_rows($query) > 0) {
                                    ?>
                                        <label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
                                            <input type="checkbox" class="chk_boxes" value="0" name="society_id[]">
                                            <span class="custom-control-description">Check All</span>
                                            <button type="submit" name="publishPost" value="publishPost" class="btn btn-sm btn-success publishPost"><i class="fa fa-check-square-o"></i> Get Data</button>
                                        </label>

                                    <?php } else { ?>
                                        <br>
                                        <span class="text-danger"><b>Data Sync Company </b></span>
                                    <?php }
                                    while ($society_master_white_label_data = mysqli_fetch_array($query)) {
                                    ?>
                                        <label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
                                            <input type="hidden" name="society_id" value="<?php echo $society_master_white_label_data["society_id"]; ?>" />
                                            <input type="checkbox" class="pagePrivilege"
                                                value="<?php echo $society_master_white_label_data['master_company_id']; ?>"
                                                data-name="<?php echo htmlspecialchars($society_master_white_label_data['society_name']); ?>"
                                                data-domain="<?php echo htmlspecialchars($society_master_white_label_data['sub_domain']); ?>"
                                                name="society_id[]">
                                            <span class="custom-control-description"><?php echo $companySrId++; ?>.
                                                <?php echo $society_master_white_label_data['master_company_id']; ?> <?php echo $society_master_white_label_data['society_name']; ?> (<?php echo $society_master_white_label_data['sub_domain']; ?> )
                                            </span>
                                            <span id="result_<?php echo $society_master_white_label_data['society_id']; ?>"></span>
                                            <input type="hidden" id="val_<?php echo $society_master_white_label_data['society_id']; ?>" value="1" />
                                        </label>
                                    <?php  } ?>
                                </div>
                                <div class="col-sm-6"> <b>Today Sync Data</b>
                                    <?php
                                    if ($selectedAnalytics == '0'){
                                        $queryPost = $d->selectRow("DISTINCT wl.society_id, wl.master_company_id, wl.society_name, wl.sub_domain, wl.project_type, wla.created_at", "society_master_white_label wl INNER JOIN society_whitelable_analytics_master wla ON wl.society_id = wla.society_id", " wl.project_type = '$selectedAnalytics' AND (wla.fetch_date = '$selectedDate')");
                                    }else{
                                        $queryPost = $d->selectRow("DISTINCT wl.society_id, wl.master_company_id, wl.society_name, wl.sub_domain, wl.project_type, wla.created_at", "society_master_white_label wl INNER JOIN white_label_analytics_master wla ON wl.society_id = wla.society_id", " wl.project_type = '$selectedAnalytics' AND (wla.fetch_date = '$selectedDate')");

                                    }
                                    $cnt = 1;
                                    echo '(' . mysqli_num_rows($queryPost) . ')';
                                    while ($white_label_data = mysqli_fetch_array($queryPost)) {
                                    ?>
                                        <label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
                                            <span class="custom-control-description"><?php echo $cnt . '). ' . $white_label_data['master_company_id']; ?> (<?php echo $white_label_data['society_name']; ?>) <?php echo $white_label_data['sub_domain']; ?></span>
                                            <?php $cls = "";

                                            ?>
                                            <span class="text-success"> <?php echo $white_label_data['created_at']; ?></span>
                                        </label>
                                    <?php $cnt++;
                                    } ?>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {


        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            endDate: new Date(),
            autoclose: true,
            todayHighlight: true
        }).on('changeDate', function(e) {
            $(this).closest('form').submit();
        });

        $('.chk_boxes').click(function() {
            $('.pagePrivilege').prop('checked', this.checked);
        });
    });
</script>