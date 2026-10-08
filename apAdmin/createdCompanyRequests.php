<?php
extract(array_map("test_input", $_REQUEST));
error_reporting(0);

?>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Company Requests </h4>

            </div>
            <div class="col-sm-3">
                <div class="btn-group float-sm-right">
                    <a href="addCompany" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add</a>
                    <?php if ($role_id == 1) { ?>
                        <a href="javascript:void(0)" onclick="DeleteAll('deleteRequestSociety');" class="btn  btn-sm btn-danger pull-right"><i class="fa fa-trash-o fa-lg"></i> Delete </a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- End Breadcrumb-->

        <div class="row pt-2 pb-2">
            <div class="col-lg-12">
                <form action="" method="get" accept-charset="utf-8">

                    <div class="form-group row">
                        <label for="country_id" class="col-sm-1 col-form-label"> Country <span class="required">*</span></label>
                        <div class="col-sm-3">
                            <select type="text" required="" id="country_id" onchange="getStates();" class="form-control single-select" name="countryId">
                                <option value="">-- Select --</option>
                                <?php
                                $qc = $d->select("countries", "flag=1");
                                while ($cData = mysqli_fetch_array($qc)) {
                                ?>
                                    <option <?php if (isset($_GET['countryId']) && $cData['country_id'] == $_GET['countryId']) {
                                                echo "selected";
                                            } ?> value="<?php echo $cData['country_id']; ?>"><?php echo $cData['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <label for="state_id" class="col-sm-1 col-form-label"> State <span class="required">*</span></label>
                        <div class="col-sm-3">
                            <?php if (isset($_GET['sId'])) {
                            ?>
                                <select type="text" onchange="getCity();" required="" class="form-control single-select" id="state_id" name="sId">
                                    <?php
                                    $countryIdFilter = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId']) : 0;
                                    $qs = $d->select("states", "country_id=$countryIdFilter");
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

                        <label for="input-101" class="col-sm-1 col-form-label"> City <span class="required">*</span></label>
                        <div class="col-sm-3">
                            <?php if (isset($_GET['cId'])) {

                            ?>
                                <select onchange="this.form.submit()" type="text" required="" class="form-control single-select" id="city_id" name="cId">
                                    <?php
                                    $sIdFilter = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
                                    $qcity = $d->select("cities", "state_id=$sIdFilter");
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
        <?php
        $cityFilterActive = isset($countryId, $sId, $cId)
            && filter_var($countryId, FILTER_VALIDATE_INT)
            && filter_var($sId, FILTER_VALIDATE_INT)
            && filter_var($cId, FILTER_VALIDATE_INT);

        $requestWhere = "society_master_requests.request_society_create_status=1";
        $requestOrder = "order by society_master_requests.request_society_id DESC";

        if ($cityFilterActive) {
            $requestWhere .= " AND society_master_requests.request_city_id='$cId' $countryAppendQuerySocietySingleReq";
        } else {
            $requestOrder .= " LIMIT 10000";
        }

        $requestJoins = "society_master_requests
            LEFT JOIN bms_admin_master ON bms_admin_master.admin_id=society_master_requests.request_added_by
            LEFT JOIN society_master ON society_master.society_id=society_master_requests.society_id_added
            LEFT JOIN domain_master ON domain_master.domain_id=society_master.domain_id
            LEFT JOIN server_master ON server_master.server_id=domain_master.server_id";

        $requestFields = "society_master_requests.*, bms_admin_master.admin_name,
            society_master.society_id, society_master.support_name, server_ip, server_name";

        $q = $d->selectRow($requestFields, $requestJoins, $requestWhere, $requestOrder);
        ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="spTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>#</th>
                                        <th>Requested Company Id</th>
                                        <th>Company Id</th>
                                        <th>Company</th>
                                        <th>Server</th>
                                        <th>Requested Date</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                        <th>Requested By</th>
                                        <th>Support Person</th>
                                        <th>TAT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    while ($data = mysqli_fetch_array($q)) {
                                        extract($data);
                                        $displayRequestedDate = ($default_time_zone != "Asia/Kolkata")
                                            ? $d->change_timezone($requested_date, $default_time_zone, 'Y-m-d h:i A')
                                            : $requested_date;
                                        $displayCreatedDate = ($default_time_zone != "Asia/Kolkata")
                                            ? $d->change_timezone($created_date, $default_time_zone, 'Y-m-d h:i A')
                                            : $created_date;
                                        $dev_tat = "";
                                        if ($requested_date != '' && $created_date != '') {
                                            $dev_tat = (new DateTime($requested_date))->diff(new DateTime($created_date));
                                        }
                                    ?>
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="multiDelteCheckbox" value="<?php echo $data['request_society_id']; ?>">
                                            </td>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo 'R_' . $d->short_app_name() . '_' . $request_society_id; ?></td>
                                            <td><?php echo !empty($society_id) ? $d->short_app_name() . '_' . $society_id : ''; ?></td>
                                            <td><a href="javascript:void" data-toggle="modal" data-target="#buildingModal" onclick="getAllBuildingData(<?php echo $request_society_id; ?>)"><?php echo $request_society_name; ?></a></td>
                                            <td><?php echo $server_name . '-' . $server_ip; ?></td>
                                            <td><?php echo $displayRequestedDate; ?></td>
                                            <td><?php echo $displayCreatedDate; ?></td>
                                            <td>
                                                <form class="d-inline-block" method="POST" action="controller/societyRequestController.php">
                                                    <input type="hidden" name="mail_society" value="<?php echo $society_id_added; ?>">
                                                    <input type="hidden" name="request_society_name" value="<?php echo $request_society_name; ?>">
                                                    <input type="hidden" name="request_sub_domain" value="<?php echo $request_sub_domain; ?>">
                                                    <input type="hidden" name="request_added_by" value="<?php echo $request_added_by; ?>">
                                                    <input type="hidden" name="redirect" value="createdCompanyRequests">
                                                    <button type="submit" name="rejected" class="btn btn-sm btn-secondary" data-toggle="tooltip" title="Send Mail"><i class="fa fa-envelope"></i></button>
                                                </form>
                                            </td>
                                            <td><?php echo $admin_name; ?></td>
                                            <td>
                                                <?php echo $support_name; ?>
                                                <button data-toggle="modal" data-target="#changeSupportNameModel" title="Change Support Name?" class="btn text-warning btn-sm ml-2" onclick="changeSupportNameId('<?php echo htmlspecialchars($support_name, ENT_QUOTES); ?>','<?php echo $society_id; ?>')"><i class="fa fa-pencil"></i></button>
                                            </td>
                                            <td><?php echo ($dev_tat != "") ? $dev_tat->format('%d d %h h %i m') : ''; ?></td>
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

<div class="modal fade" id="buildingModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="buildingData">

            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="changeSupportNameModel">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Change Support Executive Name</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form id="" method="POST" action="controller/reportController.php">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="support_name" class="col-form-label">Support Executive Name</label>
                                <select name="support_name" id="support_name" class="form-control single-select" required>
                                    <option value="">-- Select --</option>
                                    <?php
                                    $trainers = $d->select("bms_admin_master", "(role_id != 1) AND active_status='0'");
                                    while ($row2 = mysqli_fetch_assoc($trainers)) {
                                    ?>
                                        <option value="<?php echo $row2['admin_name']; ?>">
                                            <?php echo $row2['admin_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-footer text-center mt-3">
                            <input type="hidden" name="redirect_location" value="createdCompanyRequests">
                            <input type="hidden" name="changeSupportName" value="changeSupportName">
                            <input type="hidden" name="society_id" id="society_id_edit_support">
                            <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check-square-o"></i> Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function changeSupportNameId(supportName, societyID) {
        console.log(supportName, societyID);
        $("#support_name").val(supportName).trigger('change');
        $("#society_id_edit_support").val(societyID);
    }

    function getAllBuildingData(request_society_id) {
        $.ajax({
            url: "getBuildingDetails.php",
            cache: false,
            type: "POST",
            data: {
                request_society_id: request_society_id
            },
            success: function(response) {
                $('#buildingData').html(response);
            }
        });
    }
</script>