<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Manage Training Batches</h4>
            </div>
            <div class="col-sm-3">
                <div class="btn-group float-sm-right">
                    <a href="addBatch" class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add New</a>
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
                                        <th>Sr.No</th>
                                        <th>Batch Name</th>
                                        <th>Training Days</th>
                                        <th>Batch Type</th>
                                        <th>Participant Name</th>
                                        <th>Created Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $q = $d->selectRow(
                                        "tbm.batch_id, 
                        tbm.batch_name, 
                        tbm.training_days, 
                        tbm.batch_type, 
                        GROUP_CONCAT(DISTINCT tm.training_module_name ORDER BY tm.training_module_id) AS training_module_names, 
                        tbm.created_date, 
                        tbm.status, 
                        GROUP_CONCAT(DISTINCT pt.participant_name ORDER BY pt.participants_type_id) AS participant_names",
                                        "training_batch_master tbm
                          LEFT JOIN batch_module_master bmm ON tbm.batch_id = bmm.batch_id
                          LEFT JOIN training_module_master tm ON bmm.module_ids = tm.training_module_id
                          LEFT JOIN training_participants_type pt ON FIND_IN_SET(pt.participants_type_id, tbm.allowed_participants) > 0",
                                        "1",
                                        "GROUP BY tbm.batch_id
                         ORDER BY tbm.batch_id DESC"
                                    );
                                    while ($data = mysqli_fetch_array($q)) {
                                    ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo $data['batch_name']; ?>
                                            </td>
                                            <td><?php echo $data['training_days']; ?></td>
                                            <td>
                                                <?php
                                                echo ($data['batch_type'] == 0) ? "Monday To Friday" : (($data['batch_type'] == 1) ? "Saturday & Sunday" : (($data['batch_type'] == 2) ? "Monday" : (($data['batch_type'] == 3) ? "Tuesday" : (($data['batch_type'] == 4) ? "Wednesday" : (($data['batch_type'] == 5) ? "Thursday" : (($data['batch_type'] == 6) ? "Friday" : (($data['batch_type'] == 7) ? "Saturday" : (($data['batch_type'] == 9) ? "Any Day" : (($data['batch_type'] == 8) ? "Sunday" : " ")))))))));
                                                ?>
                                            </td>
                                            <td><?php echo $data['participant_names']; ?></td>
                                            <td><?php
                                                $training_date = date("d M Y H:i A", strtotime($data['created_date']));
                                                echo ($data['created_date'] != "" && $data['created_date'] != "0000-00-00 00:00:00") ? "$training_date" : ""; ?></td>
                                            <td>
                                                <?php
                                                $buttonClass = ($data['status'] == "0") ? 'btn-success-new' : 'btn-danger';
                                                $buttonCondition = ($data['status'] == "0") ? 'Active' : 'Deactive';
                                                $status = ($data['status'] == "0") ? 'batchStatusDeactive' : 'batchStatusActive';
                                                $newStatus = ($data['status'] == "0") ? 'batchStatusActive' : 'batchStatusDeactive';
                                                $newStatusVal = ($data['status'] == "0") ? '1' : '0';
                                                $statusValue = ($data['status'] == "0") ? '0' : '1';
                                                ?>
                                                <input type="button"
                                                    class="btn btn-sm pl-1 pr-1 <?php echo $buttonClass ?>"
                                                    id="<?php echo 'batch_' . $data['batch_id']; ?>"
                                                    onclick="changeStatusNew('<?php echo $data['batch_id']; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'batch_' . $data['batch_id']; ?>');"
                                                    data-size="small" value="<?php echo $buttonCondition ?>" />
                                            </td>
                                            <td>
                                                <form style="margin-right: 5px; float: left;" action="addBatch" method="post">
                                                    <input type="hidden" name="batch_id" value="<?php echo $data['batch_id']; ?>">
                                                    <button name="editBatch" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Edit Batch"> <i class="fa fa-pencil"></i></button>
                                                </form>
                                                <form style="margin-right: 5px; float: left;" action="addBatch" method="post">
                                                    <input type="hidden" name="batch_id" value="<?php echo $data['batch_id']; ?>">
                                                    <input type="hidden" name="copyBatch" value="copyBatch">
                                                    <button name="editBatch" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Copy Batch"> <i class="fa fa-copy"></i></button>
                                                </form>
                                                <!-- Copy Button -->
                                                <!--  <form style="margin-right: 5px; float: left;" action="addBatch" method="post">
                        <input type="hidden" name="batch_id" value="<?php echo $data['batch_id']; ?>">
                        <button name="copyBatch" class="btn btn-info btn-sm" data-toggle="tooltip" title="Copy Batch">
                          <i class="fa fa-copy"></i>
                        </button>
                      </form> -->
                                                <!-- <form class="deleteForm<?php echo $data['batch_id']; ?>" style='float: left;' action="controller/batchController.php" method="post">
                          <input type="hidden" name="batch_id_delete" value="<?php echo $data['batch_id']; ?>">
                          <button name="deleteBatch" type="button" class="btn btn-danger btn-sm" onclick="deleteData('<?php echo $data['batch_id']; ?>');" data-toggle="tooltip" title="Delete Batch"><i class="fa fa-trash-o"></i></button>
                        </form> -->
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- End Row-->
    </div>
    <!-- End container-fluid-->
</div><!--End content-wrapper-->