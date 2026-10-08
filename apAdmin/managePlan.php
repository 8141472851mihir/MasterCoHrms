<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Manage Plan</h4>
            </div>
            <div class="col-sm-3 col-7">
                <div class="btn-group float-sm-right">
                    <a href="javascript:void(0);" onclick="addPlanForm()" data-toggle="modal" data-target="#addPlan" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus mr-1"></i> Add Plan
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Plan Name</th>
                                        <th>No. of Month</th>
                                        <th>Plan Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $q = $d->select("manage_plan", "", "ORDER BY plan_id DESC");
                                    while ($data = mysqli_fetch_array($q)) {
                                    ?>
                                        <tr>
                                            <td><?= $i++; ?></td>
                                            <td><?= $data['plan_name']; ?></td>
                                            <td><?= $data['plan_value']; ?></td>
                                            <td>
                                                <?php
                                                $buttonClass = ($data['status'] == "0") ? 'btn-success-new' : 'btn-danger';
                                                $buttonCondition = ($data['status'] == "0") ? 'Active' : 'Deactive';
                                                $status = ($data['status'] == "0") ? 'planStatusDeactive' : 'planStatusActive';
                                                $newStatus = ($data['status'] == "0") ? 'planStatusActive' : 'planStatusDeactive';
                                                $newStatusVal = ($data['status'] == "0") ? '1' : '0';
                                                $statusValue = ($data['status'] == "0") ? '0' : '1';
                                                ?>

                                                <input type="button" class="btn btn-sm pl-1 pr-1 w-50 <?php echo $buttonClass ?>" id="<?php echo 'plan_button_' . $data['plan_id']; ?>" onclick="changeStatusNew('<?php echo $data['plan_id']; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'plan_button_' . $data['plan_id']; ?>');" data-size="small" value="<?php echo $buttonCondition ?>" />
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-primary" onclick="editPlanForm('<?= $data['plan_id']; ?>', '<?= $data['plan_name']; ?>', '<?= $data['plan_value']; ?>')">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
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
<!-- Modal for Add/Edit Plan -->
<div class="modal fade" id="addPlan">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-white" id="plan">Add Plan</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addPlanForm" action="controller/planController.php" method="POST">
                    <div class="container mt-4">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Plan Name<span class="required">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" autocomplete="off" name="plan_name" id="plan_name" class="form-control" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">No. of Month<span class="required">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" autocomplete="off" name="plan_value" id="plan_value" class="form-control onlyNumber" >
                            </div>
                        </div>
                        <div class="form-footer text-center mt-4">
                            <input type="hidden" name="planModule" id="planModule" value="planModule">
                            <input type="hidden" name="editId" id="editId" value="">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-check-square-o"></i> <span id="submitButton">Submit</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function addPlanForm() {
        $("#plan").text("Add Plan");
        $("#planModule").val("planModule");
        $("#plan_name").val("");
        $("#plan_value").val("");
        $("#editId").val("");
        $("#submitButton").text("Add");
        $("#addPlan").modal("show");
    }
    function editPlanForm(id, planName, planValue) {
        $("#plan").text("Update Plan");
        $("#planModule").val("planModuleEdit");
        $("#plan_name").val(planName);
        $("#plan_value").val(planValue);
        $("#editId").val(id);
        $("#submitButton").text("Update");
        $("#addPlan").modal("show");
    }
</script>
