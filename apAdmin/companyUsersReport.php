<?php
extract(array_map("test_input", $_REQUEST));
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-9 col-5">
                <h4 class="page-title">Company User Report</h4>
            </div>
            <div class="col-sm-3">
                <form action="" method="get" accept-charset="utf-8">
                    <select type="text" required="" id="tracking_registration_user" class="form-control single-select"
                        name="tracking_registration_user" onchange="this.form.submit()">
                        <option value="0" <?php echo (isset($_GET['tracking_registration_user']) && $_GET['tracking_registration_user'] == '0') ? 'selected' : ''; ?>>Tracking User</option>
                        <option value="1" <?php echo (!isset($_GET['tracking_registration_user']) || $_GET['tracking_registration_user'] == '1') ? 'selected' : ''; ?>>Registration User</option>
                    </select>
                </form>
            </div>
        </div>

        <?php
        $filter = "";
        if (isset($_GET['tracking_registration_user']) && $_GET['tracking_registration_user'] == '0') {
            $filter = "society_analytics_master.active_tracking_users > society_master.employee_tracking_limit";
        } else {
            $filter = "society_analytics_master.total_users > society_master.employee_registration_limit";
        }

        ?>
        <?php
        if (isset($_GET['tracking_registration_user']) && $_GET['tracking_registration_user'] == '0') {
            echo '<div class="alert alert-dark mt-2 px-2" role="alert">
            <strong>Note:</strong> Showing companies where <strong>Active Tracking Users</strong> exceed <strong>Employee Tracking Limit</strong>.
          </div>';
        } else {
            echo '<div class="alert alert-dark mt-2 px-2" role="alert">
            <strong>Note:</strong> Showing companies where <strong>Total Users</strong> exceed <strong>Employee Registration Limit</strong>.
          </div>';
        }
        ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="reportTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Company ID</th>
                                        <th>Name</th>
                                        <th>Total Users</th>
                                        <th>Employee Registration Limit</th>
                                        <th>Current Tracking Users</th>
                                        <th>Employee Tracking Limit</th>
                                        <th>City</th>
                                        <th>Email</th>
                                        <th>Phone No</th>
                                        <th>Plan Expire Days</th>
                                        <th>Implementation Person Name</th>
                                        <th>Account Type</th>
                                        <th>Created Date</th>
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
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>

                                <tbody>
                                    <?php
                                    $i = 1;
                                    $q = $d->selectRow("society_master.*,society_analytics_master.*", "society_master LEFT JOIN society_analytics_master ON society_master.society_id=society_analytics_master.society_id", "$filter", "ORDER BY society_master.society_id DESC");
                                    while ($data = mysqli_fetch_array($q)) {
                                        extract($data);
                                    ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo '' . $d->short_app_name() . '_' . $society_id; ?></td>
                                            <td><?php echo $society_name; ?></td>
                                            <td><?php echo $total_users; ?></td>
                                            <td><?php echo $employee_registration_limit; ?></td>
                                            <td><?php echo $active_tracking_users; ?></td>
                                            <td><?php echo $employee_tracking_limit; ?></td>
                                            <td><?php echo $city_name; ?></td>
                                            <td><?php echo $secretary_email; ?></td>
                                            <td><?php echo $secretary_mobile; ?></td>
                                            <td><?php echo $plan_expire_date; ?></td>
                                            <td><?php echo $implementation_name; ?></td>
                                            <td><?php echo ($account_type) == 0 ? 'Normal' : 'Key'; ?></td>
                                            <?php
                                            if ($created_date != "") {
                                                $display_date = date('d-M-Y h:i A', strtotime($created_date));
                                            } else {
                                                $display_date = "-";
                                            }
                                            ?>
                                            <td><?php echo $display_date; ?></td>
                                        </tr>
                                    <?php
                                    } // end while loop
                                    ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>