<style>
    .report-desc-col {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<?php $activeTab = isset($_SESSION['active_tab']) ? $_SESSION['active_tab'] : '0'; ?>
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-8">
                <h4 class="page-title">White Label</h4>
            </div>
            <div class="col-md-4 row justify-content-end">
                <div class="btn-group px-1">
                    <a href="getWhiteLabelData" class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-database"></i> Get Bulk Data</a>
                </div>
                <div class="btn-group px-1">
                    <a href="addWhiteLabel" class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add New</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-info nav-justified" id="trainingTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($activeTab == 0) ? 'active' : ''; ?>" id="myco-tab" data-toggle="tab" href="#myco" role="tab"
                                    aria-controls="myco" aria-selected="<?php echo ($activeTab == 0) ? 'true' : 'false'; ?>">MyCo</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($activeTab == 1) ? 'active' : ''; ?>" id="smart_society-tab" data-toggle="tab" href="#smart_society" role="tab"
                                    aria-controls="smart_society" aria-selected="<?php echo ($activeTab == 1) ? 'true' : 'false'; ?>">Smart Society</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($activeTab == 2) ? 'active' : ''; ?>" id="association-tab" data-toggle="tab" href="#association" role="tab"
                                    aria-controls="association" aria-selected="<?php echo ($activeTab == 2) ? 'true' : 'false';  ?>">My Association</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="trainingTabsContent">
                            <!-- myco -->
                            <div class="tab-pane fade <?php echo ($activeTab == 0) ? 'show active' : ''; ?>" id="myco" role="tabpanel" aria-labelledby="myco-tab">
                                <div class="table-responsive">
                                    <table id="example" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Company ID</th>
                                                <th>Company Name</th>
                                                <th>City</th>
                                                <th>Company Base Url</th>
                                                <th>Company Address</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            $q = $d->select("society_master_white_label SMWL LEFT JOIN cities C ON SMWL.city_id=C.city_id", "SMWL.project_type=0", "ORDER BY SMWL.society_id ASC");
                                            while ($data = mysqli_fetch_array($q)) {
                                                extract($data);
                                            ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td><?php echo $master_company_id; ?></td>
                                                    <td><?php echo $society_name; ?></td>
                                                    <td><?php echo $name; ?></td>
                                                    <td><?php echo $sub_domain; ?></td>
                                                    <td><?php echo $society_address; ?></td>

                                                    <td>
                                                        <div class="btn-group">
                                                            <form action="addWhiteLabel" method="POST" class="d-inline-block">
                                                                <input type="hidden" name="society_id"
                                                                    value="<?php echo $society_id ?>">
                                                                <input type="hidden" name="edit_white_lable">
                                                                <button class="btn btn-sm btn-info"><i
                                                                        class="fa fa-pencil"></i></button>
                                                            </form>
                                                        </div>
                                                        <div class="btn-group">
                                                            <form class="d-inline-block"
                                                                action="controller/mycoWhitelabelController.php" method="post">
                                                                <input type="hidden" name="society_delete_id"
                                                                    value="<?php echo $data['society_id']; ?>">
                                                                <input type="hidden" name="project_type" value="<?php echo $data['project_type']; ?>">
                                                                <input type="hidden" name="deleteWhiteLable" value="deleteWhiteLable">
                                                                <button name="deleteMyCoWhiteLable" type="button"
                                                                    class="btn btn-danger btn-sm form-btn" data-toggle="tooltip"
                                                                    title="Delete White Label"><i class="fa fa-trash-o"></i></button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- smart society -->
                            <div class="tab-pane fade <?php echo ($activeTab == 1) ? 'show active' : ''; ?>" id="smart_society" role="tabpanel" aria-labelledby="smart_society-tab">
                                <div class="table-responsive">
                                    <table id="example" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Company ID</th>
                                                <th>Company Name</th>
                                                <th>City</th>
                                                <th>Company Base Url</th>
                                                <th>Company Address</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            $q = $d->select("society_master_white_label SMWL LEFT JOIN cities C ON SMWL.city_id=C.city_id", "SMWL.project_type=1", "ORDER BY SMWL.society_id ASC");

                                            while ($data = mysqli_fetch_array($q)) {
                                                extract($data);
                                            ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td><?php echo $master_company_id; ?></td>
                                                    <td><?php echo $society_name; ?></td>
                                                    <td><?php echo $name; ?></td>
                                                    <td><?php echo $sub_domain; ?></td>
                                                    <td><?php echo $society_address; ?></td>

                                                    <td>
                                                        <div class="btn-group">
                                                            <form action="addWhiteLabel" method="POST" class="d-inline-block">
                                                                <input type="hidden" name="society_id"
                                                                    value="<?php echo $society_id ?>">
                                                                <input type="hidden" name="edit_white_lable">
                                                                <button class="btn btn-sm btn-info"><i
                                                                        class="fa fa-pencil"></i></button>
                                                            </form>
                                                        </div>
                                                        <div class="btn-group">
                                                            <form class="d-inline-block"
                                                                action="controller/mycoWhitelabelController.php" method="post">
                                                                <input type="hidden" name="society_delete_id"
                                                                    value="<?php echo $data['society_id']; ?>">
                                                                <input type="hidden" name="project_type" value="<?php echo $data['project_type']; ?>">
                                                                <input type="hidden" name="deleteWhiteLable" value="deleteWhiteLable">
                                                                <button name="deleteMyCoWhiteLable" type="button"
                                                                    class="btn btn-danger btn-sm form-btn" data-toggle="tooltip"
                                                                    title="Delete White Label"><i class="fa fa-trash-o"></i></button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- my association -->
                            <div class="tab-pane fade <?php echo ($activeTab == 2) ? 'show active' : ''; ?>" id="association" role="tabpanel" aria-labelledby="association-tab">
                                <div class="table-responsive">
                                    <table id="example" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Company ID</th>
                                                <th>Company Name</th>
                                                <th>City</th>
                                                <th>Company Base Url</th>
                                                <th>Company Address</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            $q = $d->select("society_master_white_label SMWL LEFT JOIN cities C ON SMWL.city_id=C.city_id", "SMWL.project_type=2", "ORDER BY SMWL.society_id ASC");

                                            while ($data = mysqli_fetch_array($q)) {
                                                extract($data);
                                            ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td><?php echo $master_company_id; ?></td>
                                                    <td><?php echo $society_name; ?></td>
                                                    <td><?php echo $name; ?></td>
                                                    <td><?php echo $sub_domain; ?></td>
                                                    <td><?php echo $society_address; ?></td>

                                                    <td>
                                                        <div class="btn-group">
                                                            <form action="addWhiteLabel" method="POST" class="d-inline-block">
                                                                <input type="hidden" name="society_id"
                                                                    value="<?php echo $society_id ?>">
                                                                <input type="hidden" name="edit_white_lable">
                                                                <button class="btn btn-sm btn-info"><i
                                                                        class="fa fa-pencil"></i></button>
                                                            </form>
                                                        </div>
                                                        <div class="btn-group">
                                                            <form class="d-inline-block"
                                                                action="controller/mycoWhitelabelController.php" method="post">
                                                                <input type="hidden" name="society_delete_id" value="<?php echo $data['society_id']; ?>">
                                                                <input type="hidden" name="project_type" value="<?php echo $data['project_type']; ?>">
                                                                <input type="hidden" name="deleteWhiteLable" value="deleteWhiteLable">
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm form-btn" data-toggle="tooltip"
                                                                    title="Delete White Label"><i class="fa fa-trash-o"></i></button>
                                                            </form>
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
    </div>
</div>