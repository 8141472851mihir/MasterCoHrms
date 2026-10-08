<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-9 col-5">
                <h4 class="page-title">Manage Training Priority</h4>
            </div>
            <div class="col-sm-3 col-7">
                <div class="btn-group float-sm-right">
                    <a href="javascript:void(0);" data-toggle="modal" onclick="addForm()" data-target="#trainingPriorityModal" class="btn btn-primary btn-sm waves-effect waves-light">
                        <i class="fa fa-plus mr-1"></i> Add Training Priority
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
                                        <th>Priority Name</th>
                                        <th>Required</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $q = $d->selectRow("priority_id, priority_name, is_required", "training_module_priority_master", "1", "ORDER BY priority_id ASC");
                                    while ($data = mysqli_fetch_array($q)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo htmlspecialchars($data['priority_name']); ?></td>
                                            <td>
                                                <?php
                                                $posText = 'Required';
                                                $negText = 'Not Required';
                                                $buttonClass = ($data['is_required'] == "1") ? 'btn-success-new' : 'btn-danger';
                                                $buttonCondition = ($data['is_required'] == "1") ? 'Required' : 'Not Required';
                                                $status = ($data['is_required'] == "1") ? 'priorityStatusDeactive' : 'priorityStatusActive';
                                                $newStatus = ($data['is_required'] == "1") ? 'priorityStatusActive' : 'priorityStatusDeactive';
                                                $newStatusVal = ($data['is_required'] == "1") ? '1' : '0';
                                                $statusValue = ($data['is_required'] == "1") ? '0' : '1';
                                                ?>
                                                <input type="button"
                                                    class="btn btn-sm pl-1 pr-1 w-50 <?php echo $buttonClass ?>"
                                                    id="<?php echo 'priority_' . $data['priority_id']; ?>"
                                                    onclick="changeStatusNew('<?php echo $data['priority_id']; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'priority_' . $data['priority_id']; ?>','','','','<?php echo $negText; ?>','<?php echo $posText; ?>');"
                                                    data-size="small" value="<?php echo $buttonCondition ?>" />
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-primary" 
                                                onclick="editForm('<?php echo $data['priority_id']; ?>', '<?php echo htmlspecialchars($data['priority_name']); ?>', '<?php echo $data['is_required']; ?>')">
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

<div class="modal fade" id="trainingPriorityModal">
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-white">
                    <span id="modalTitle">Add Training Priority</span>
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="trainingPriorityForm" action="controller/manageTrainingPriorityController.php" method="post">
                    <input type="hidden" name="priority_id" id="priority_id">
                    <div class="form-group">
                        <label for="priority_name">Priority Name <span class="required">*</span></label>
                        <input type="text" autocomplete="off"  name="priority_name" id="priority_name" class="form-control" >
                    </div>
                    
                    <div class="form-footer text-center">
                        <button type="submit" name="saveTrainingPriority" class="btn btn-success">
                            <i class="fa fa-check-square-o"></i> <span id="submitButton">ADD</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function editForm(id, name, required) {
        $("#priority_id").val(id);
        $("#priority_name").val(name);
        $("#is_required").prop("checked", required == 1);
        $("#modalTitle").text("Update Training Priority");
        $("#submitButton").text("UPDATE");
        $("#trainingPriorityModal").modal("show");
    }

    function addForm() {
        $("#priority_id").val("");
        $("#priority_name").val("");
        $("#is_required").prop("checked", false);
        $("#modalTitle").text("Add Training Priority");
        $("#submitButton").text("ADD");
        $("#trainingPriorityModal").modal("show");
    }
</script>

