<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Developer Notes</h4>
            </div>
            <div class="col-sm-3">
                <div class="btn-group float-sm-right">
                    <a href="addDevelopernotes" class="btn btn-sm btn-primary waves-effect waves-light"><i
                            class="fa fa-plus mr-1"></i> Add New</a>
                </div>
            </div>
        </div>
        <!-- End Breadcrumb-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Id</th>
                                        <th>Action</th>
                                        <th>Company Name</th>
                                        <th>Log File</th>
                                        <th>Integration Name</th>
                                        <th>Plan Expire</th>
                                        <th> Expire Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $q = $d->selectRow("DNM.*,SM.plan_expire_date,CASE WHEN SM.plan_expire_date < CURDATE() THEN 'YES' ELSE 'NO' END AS is_expired, SM.society_name,SM.sub_domain, SM.city_name, SM.society_id", "developer_note_master DNM, society_master SM", "DNM.company_selection = SM.society_id", "ORDER BY DNM.developer_note_id ASC");
                                    while ($data = mysqli_fetch_array($q)) {
                                        extract($data);
                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <form action="addDevelopernotes" method="POST" class="d-inline-block">
                                                        <input type="hidden" name="developer_note_id"
                                                            value="<?php echo $developer_note_id ?>">
                                                        <input type="hidden" name="edit_developer">
                                                        <button class="btn btn-sm btn-info"><i
                                                                class="fa fa-pencil"></i></button>
                                                    </form>
                                                </div>
                                                <div class="btn-group">
                                                    <form class="d-inline-block"
                                                        action="controller/developerNotesController.php" method="post">
                                                        <input type="hidden" name="developer_note_id_delete"
                                                            value="<?php echo $data['developer_note_id']; ?>">
                                                        <button name="deleteDeveloperNotes" type="button"
                                                            class="btn btn-danger btn-sm form-btn" data-toggle="tooltip"
                                                            title="Delete Package"><i class="fa fa-trash-o"></i></button>
                                                    </form>
                                                </div>
                                                <div class="btn-group">
                                                    <form action="viewDeveloperNotes" method="POST">
                                                        <input type="hidden" name="developer_note_id"
                                                            value="<?php echo $developer_note_id; ?>">
                                                        <button class="btn btn-sm btn-primary"><i
                                                                class="fa fa-eye"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                            <td><span style="display: none;"><?php echo $society_id; ?></span><?php echo ''.$d->short_app_name().'_'.$society_id; ?></td>
                                            <td><a href="<?php echo $sub_domain; ?>apAdmin/" target="_blank"><?php echo $society_name.'-'.$city_name; ?></a></td>
                                            <td><a href="<?php echo $sub_domain; ?>img/cronlogs.txt" target="_blank"><i class="fa fa-file"></i></a> </td>
                                            <td><?php echo $integration_name; ?></td>
                                            <td><?php echo $is_expired; ?></td>
                                            <td><?php echo $plan_expire_date; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->
    </div>
    <!-- end container-fluid  -->
</div>
<!-- end content-wrapper  -->