<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-12">
                <h4 class="page-title">Company Deleted Report</h4>
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
                                        <th>Company Name</th>
                                        <th>Company Type</th>
                                        <th>Secretary Name</th>
                                        <th>Secretary Mobile</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $q = $d->select("society_master_requests smr", "smr.request_society_id NOT IN (SELECT sm.society_id FROM society_master sm) AND smr.request_society_create_status=1");

                                    while ($data = mysqli_fetch_array($q)) {
                                        extract($data);
                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo $request_society_name; ?></td>
                                            <td><?php if ($society_type == '0') {
                                                echo "Residential";
                                            } else {
                                                echo "Commercial";
                                            } ?></td>
                                            <td><?php echo $request_secretary_name; ?></td>
                                            <td><?php echo $request_secretary_mobile; ?></td>
                                            <td><?php echo $created_date; ?></td>
                                        </tr>
                                        <?php
                                    }
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