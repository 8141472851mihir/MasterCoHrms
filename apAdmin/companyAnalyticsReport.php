<?php
extract(array_map("test_input", $_REQUEST));
error_reporting(0);
$qcountries = $d->selectSpArray("getCountry");
$cIdsArray = explode(",", $countryids);
$sId = $d->sanitizeReportFilterIdAsInt($sId);
$countryId = $d->sanitizeReportFilterIdAsInt($countryId, 101);
$cId = $d->sanitizeReportFilterIdAsInt($cId);
$includeExpired = isset($_GET['includeExpired']) ? (int)$_GET['includeExpired'] : 0;
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-4">
                <h4 class="page-title">Company Analytics Report</h4>

            </div>
            <div class="col-sm-8 text-right">
                <?php if (isset($cId) && $cId > 0) {  ?>
                    <form action="companyAnalyticsGetData" method="POST">
                        <input type="hidden" name="country_id" value="<?php if (isset($_GET['countryId'])) {
                                                                            echo $_GET['countryId'];
                                                                        } ?>">
                        <input type="hidden" name="state_id" value="<?php if (isset($_GET['sId'])) {
                                                                        echo $_GET['sId'];
                                                                    } ?>">
                        <input type="hidden" name="city_id" value="<?php if (isset($_GET['cId'])) {
                                                                        echo $_GET['cId'];
                                                                    } ?>">
                        <button type="submit" name="publishPost" value="publishPost" class="open-AddBookDialog btn btn-secondary btn-sm"><i class="fa fa-database"></i> Get Bulk Data</button>
                    </form>
                    <!-- <a data-toggle="modal" onclick="getDataSociety()" data-target="#publishPostModal" class="open-AddBookDialog btn btn-secondary btn-sm" href="#"><i class="fa fa-database"></i> Get Bulk Data</a> -->
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
                                for ($ic = 0; $ic < count($qcountries); $ic++) {
                                    if (in_array($qcountries[$ic]['country_id'], $cIdsArray)) {
                                ?>
                                        <option <?php if (isset($_GET['countryId']) && $qcountries[$ic]['country_id'] == $_GET['countryId']) {
                                                    echo "selected";
                                                } ?> value="<?php echo $qcountries[$ic]['country_id']; ?>"><?php echo $qcountries[$ic]['name']; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        <label for="state_id" class="col-sm-1 col-form-label"> State <span class="required">*</span></label>
                        <div class="col-sm-3">
                            <?php if (isset($_GET['sId'])) {
                                $countryIdFilter = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId']) : 0;
                                $qstates = $d->selectSpArray("getState('$countryIdFilter')");

                            ?>
                                <select type="text" onchange="this.form.submit()" required="" class="form-control single-select" id="state_id" name="sId">
                                    <option value="">Select</option>
                                    <?php
                                    for ($is = 0; $is < count($qstates); $is++) {
                                    ?>
                                        <option <?php if (isset($_GET['sId']) && $qstates[$is]['state_id'] == $_GET['sId']) {
                                                    echo "selected";
                                                } ?> value="<?php echo $qstates[$is]['state_id']; ?>"><?php echo $qstates[$is]['name']; ?></option>
                                    <?php }  ?>
                                </select>
                            <?php } else { ?>
                                <select type="text" onchange="this.form.submit()" required="" class="form-control single-select" id="state_id" name="sId">
                                    <option value="">-- Select --</option>
                                </select>
                            <?php } ?>
                        </div>

                        <label for="input-101" class="col-sm-1 col-form-label"> City <span class="required">*</span></label>
                        <div class="col-sm-3">
                            <?php if (isset($_GET['cId'])) {
                                $sIdFilter = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
                                $qcities = $d->selectSpArray("getCity('$sIdFilter')");
                            ?>
                                <select onchange="this.form.submit()" type="text" required="" class="form-control single-select" id="city_id" name="cId">
                                    <option value="">Select</option>
                                    <?php
                                    for ($icity = 0; $icity < count($qcities); $icity++) {
                                    ?>
                                        <option <?php if (isset($_GET['cId']) && $qcities[$icity]['city_id'] == $_GET['cId']) {
                                                    echo "selected";
                                                } ?> value="<?php echo $qcities[$icity]['city_id']; ?>"><?php echo $qcities[$icity]['name']; ?></option>
                                    <?php }  ?>
                                </select>
                            <?php } else { ?>
                                <select onchange="this.form.submit()" type="text" required="" class="form-control single-select" name="cId" id="city_id">
                                    <option value="">-- Select --</option>
                                </select>
                            <?php } ?>
                        </div>

                    </div>
                    <div class="form-group row">
                        <label for="serverId" class="col-sm-1 col-form-label"> Server <span class="required">*</span></label>
                        <div class="col-sm-3">
                            <select type="text" required="" id="serverId" onchange="this.form.submit()" class="form-control single-select" name="serverId">
                                <option value="0">-- Select --</option>
                                <?php
                                $serverQry = $d->select("server_master", "server_active_status='0'");
                                while ($server_data = mysqli_fetch_assoc($serverQry)) {
                                ?>
                                    <option <?php if (isset($_GET['serverId']) && $server_data['server_id'] == $_GET['serverId']) {
                                                echo "selected";
                                            } ?> value="<?php echo $server_data['server_id']; ?>"><?php echo $server_data['server_name']; ?></option>
                                <?php  } ?>
                            </select>
                        </div>
                        <label for="includeExpired" class="col-sm-1 col-form-label"> Include Expired</label>
                        <div class="col-sm-3">
                            <select type="text" id="includeExpired" onchange="this.form.submit()" class="form-control single-select" name="includeExpired">
                                <option value="0" <?php if (!isset($_GET['includeExpired']) || (isset($_GET['includeExpired']) && $_GET['includeExpired'] == '0')) echo "selected"; ?>>No (Active Only)</option>
                                <option value="1" <?php if (isset($_GET['includeExpired']) && $_GET['includeExpired'] == '1') echo "selected"; ?>>Yes (Include Expired)</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="reportTable1" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Company</th>
                                    <th>Support Person Name</th>
                                    <th>Server</th>
                                    <th>Company Type</th>
                                    <th>From Rise Event</th>
                                    <th>Plan Status</th>
                                    <th>Expiry Date</th>
                                    <th>Company Creation Date</th>
                                    <th>DB Size</th>
                                    <th>Storage in MB</th>
                                    <th>Branchs</th>
                                    <th>employee registration limit</th>
                                    <th>Tracking Limit</th>
                                    <th>Active Tracking users</th>
                                    <th>Employees</th>
                                    <th>Login Employees</th>
                                    <th>iOS Users</th>
                                    <th>Android Users</th>

                                    <th>Admin view count</th>
                                    <th>Total Google Visit</th>
                                    <th>This Month Google Visit</th>
                                    <th>Pre Month Google Visit</th>
                                    <th>Total Attendance</th>
                                    <th>This Week Attendance</th>
                                    <th>This Month Attendance</th>
                                    <th>Prev. Month Attendance</th>
                                    <th>Total Work From Home</th>
                                    <th>Total Salary Generated</th>
                                    <th>Last Month Salary</th>
                                    <th>loan</th>
                                    <th>advance salary</th>
                                    <th>Total Leaves</th>
                                    <th>Total Work Report</th>
                                    <th>This Week Work Report</th>
                                    <th>This Month Work Report</th>
                                    <th>Prev. Month Work Report</th>
                                    <th>Total DAR Work Report</th>
                                    <th>This DAR Week Work Report</th>
                                    <th>This DAR Month Work Report</th>
                                    <th>Prev. DAR Month Work Report</th>
                                    <th>Total Assets</th>

                                    <th>complains</th>
                                    <th>Noticeboard</th>
                                    <th>Events</th>
                                    <th>Visitors</th>
                                    <th>Timeline</th>
                                    <th>Chat Message</th>
                                    <th>SOS Triger</th>
                                    <th>Polls</th>
                                    <th>blancesheet</th>
                                    <th>election</th>
                                    <th>document </th>
                                    <th>Lost & Found</th>
                                    <th>penalty </th>
                                    <th>Parcel On Gate</th>
                                    <th>my request </th>
                                    <th>survey</th>
                                    <th>discussion forum</th>
                                    <th>City</th>
                                    <th>Updated Date</th>
                                    <th>Address</th>
                                </tr>
                            </thead>
                            <tfoot class="bottom-footer">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th class="find-count"></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php
                                $i = 1;
                                $appendCity = "";
                                $appendCountry = "";
                                $appendState = "";
                                $appendServer = "";
                                $appendExpired = "";

                                if (isset($cId) && $cId > 0) {
                                    $appendCity = " AND society_master.city_id='$cId'";
                                }

                                if (isset($countryId) && $countryId > 0) {
                                    $appendCountry = " AND society_master.country_id='$countryId'";
                                }

                                if (isset($sId) && $sId > 0) {
                                    $appendState = " AND society_master.state_id='$sId'";
                                }
                                if (isset($serverId) && $serverId > 0) {
                                    $appendServer = " AND server_master.server_id='$serverId'";
                                }
                                if (isset($includeExpired) && $includeExpired == 0) {
                                    $appendExpired = " AND plan_expire_date >= CURDATE()";
                                }
                                $q = $d->selectRow("business_entity_master.name AS society_type_name,society_master.created_date as society_created_date, society_analytics_master.*,society_master.*, CASE WHEN plan_expire_date < CURDATE() THEN 'YES' ELSE 'NO' END AS is_expired, CASE WHEN IFNULL(society_master.from_rise_event,0) = 1 THEN 'Yes' ELSE 'No' END AS is_from_rise_event,server_master.server_name,server_master.server_ip", "society_analytics_master,society_master LEFT JOIN domain_master ON society_master.domain_id=domain_master.domain_id LEFT JOIN server_master ON server_master.server_id=domain_master.server_id LEFT JOIN business_entity_master ON society_master.industry_type=business_entity_master.b_id", "society_master.society_id=society_analytics_master.society_id AND society_master.is_demo_society=0 $appendCountry $appendState $appendCity $appendServer $appendExpired", "order by society_analytics_master.analytics_id", "");
                                while ($data = mysqli_fetch_array($q)) {
                                    extract($data);
                                ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $society_name; ?></td>
                                        <td><?php echo !empty($support_name) ? htmlspecialchars($support_name) : ''; ?></td>
                                        <td><?php echo $server_name . '-' . $server_ip; ?></td>
                                        <td><?php echo $society_type_name; ?></td>
                                        <td><?php echo isset($is_from_rise_event) ? $is_from_rise_event : (isset($from_rise_event) && $from_rise_event == 1 ? 'Yes' : 'No'); ?></td>
                                        <td>
                                            <?php
                                            // Keep status label aligned with SQL filter (CURDATE based).
                                            $expiryDate = date('Y-m-d', strtotime((string)$plan_expire_date));
                                            $todayDate = date('Y-m-d');
                                            echo ($expiryDate >= $todayDate) ? "Active" : "Expired";
                                            ?>
                                        </td>
                                        <td><?php echo $plan_expire_date; ?></td>
                                        <td><?php echo (($society_created_date != '') ? date('Y-m-d', strtotime($society_created_date)) : "");
                                            $society_created_date; ?></td>
                                        <td><?php echo $db_size; ?></td>
                                        <td><?php echo $img_storage; ?></td>
                                        <td><?php echo $no_of_blocks; ?></td>
                                        <td><?php echo $employee_registration_limit; ?></td>
                                        <td><?php echo $employee_tracking_limit; ?></td>
                                        <td><?php echo ($active_tracking_users != "") ? $active_tracking_users : '0'; ?></td>
                                        <td><?php echo $total_users; ?></td>
                                        <td><?php echo $total_login_android + $total_login_ios; ?></td>
                                        <td><?php echo $total_login_ios; ?></td>
                                        <td><?php echo $total_login_android; ?></td>
                                        <td><?php echo $admin_size; ?></td>
                                        <td><?php echo $totalGoogleVisit; ?></td>
                                        <td><?php echo $thisMonthGoogleVisit; ?></td>
                                        <td><?php echo $preMonthGoogleVisit; ?></td>
                                        <td><?php echo $total_attendace; ?></td>
                                        <td><?php echo $total_attendace_weekly; ?></td>
                                        <td><?php echo $total_attendace_this_month; ?></td>
                                        <td><?php echo $total_attendace_prev_month; ?></td>
                                        <td><?php echo $total_work_from_home; ?></td>
                                        <td><?php echo $total_salary_slip; ?></td>
                                        <td><?php echo $total_monthly_salary_slip; ?></td>
                                        <td><?php echo $total_loan; ?></td>
                                        <td><?php echo $advance_salary; ?></td>
                                        <td><?php echo $total_leaves; ?></td>
                                        <td><?php echo $total_work_report; ?></td>
                                        <td><?php echo $total_work_report_weekly; ?></td>
                                        <td><?php echo $total_work_report_this_month; ?></td>
                                        <td><?php echo $total_work_report_prev_month; ?></td>
                                        <td><?php echo $total_dar_work_report; ?></td>
                                        <td><?php echo $total_dar_work_report_weekly; ?></td>
                                        <td><?php echo $total_dar_work_report_this_month; ?></td>
                                        <td><?php echo $total_dar_work_report_prev_month; ?></td>
                                        <td><?php echo $total_assets; ?></td>

                                        <td><?php echo $total_complains; ?></td>
                                        <td><?php echo $total_notice_board; ?></td>
                                        <td><?php echo $total_events; ?></td>
                                        <td><?php echo $total_visitors; ?></td>
                                        <td><?php echo $total_timeline_post; ?></td>
                                        <td><?php echo (int)$total_chat_msg; ?></td>
                                        <td><?php echo $total_sos_triger; ?></td>
                                        <td><?php echo $total_polls; ?></td>
                                        <td><?php echo $total_blancesheet; ?></td>
                                        <td><?php echo $total_election; ?></td>
                                        <td><?php echo $total_document; ?></td>
                                        <td><?php echo $total_lost_found; ?></td>
                                        <td><?php echo $total_penalty; ?></td>
                                        <td><?php echo $total_parcel_on_gate; ?></td>
                                        <td><?php echo $total_my_request; ?></td>
                                        <td><?php echo $total_survey; ?></td>
                                        <td><?php echo $total_duscussion_foram; ?></td>
                                        <td><?php echo $city_name; ?></td>
                                        <td><?php echo $update_date; ?></td>
                                        <td><?php echo $society_address; ?></td>

                                    </tr>

                                <?php } ?>
                            </tbody>
                            <tfoot class="top-footer">
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
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="publishPostModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Get <?php echo $xml->string->society; ?> Data</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="publishSocetyDataFrm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="countryId" id="countryId" value="<?php echo $countryId; ?>">
                    <input type="hidden" name="sId" id="sId" value="<?php echo $sId; ?>">
                    <input type="hidden" name="cId" id="cId" value="<?php echo $cId; ?>">
                    <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" />
                    <input type="hidden" name="publishPost" value="publishPost" />
                    <div id="sosa_detail">
                    </div>
                    <div id="chkError" class=""></div>
                    <div class="form-footer text-center">
                        <button type="submit" name="publishPost" value="publishPost" class="btn btn-sm btn-success publishPost"><i class="fa fa-check-square-o"></i> Get Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>