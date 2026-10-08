<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-9 col-5">
                <h4 class="page-title">Manage Participants</h4>
            </div>
            <div class="col-sm-3 col-7">
                <div class="btn-group float-sm-right">
                    <a href="javascript:void(0);" data-toggle="modal" onclick="addForm()" data-target="#participantsModal" class="btn btn-primary btn-sm waves-effect waves-light">
                        <i class="fa fa-plus mr-1"></i> Add Participant
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
                                        <th>Participant Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $q = $d->selectRow("participants_type_id, participant_name, status", "training_participants_type", "", "ORDER BY participants_type_id DESC");
                                    if(mysqli_num_rows($q)>0){
                                    while ($data = mysqli_fetch_array($q)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo htmlspecialchars($data['participant_name']); ?></td>
                                            <td>
                                            <?php
                                                $buttonClass = ($data['status'] == "0") ? 'btn-success-new' : 'btn-danger';
                                                $buttonCondition = ($data['status'] == "0") ? 'Active' : 'Deactive';
                                                $status = ($data['status'] == "0") ? 'participantStatusDeactive' : 'participantStatusActive';
                                                $newStatus = ($data['status'] == "0") ? 'participantStatusActive' : 'participantStatusDeactive';
                                                $newStatusVal = ($data['status'] == "0") ? '1' : '0';
                                                $statusValue = ($data['status'] == "0") ? '0' : '1';
                                                ?>

                                                <input type="button" class="btn btn-sm pl-1 pr-1 w-50 <?php echo $buttonClass ?>" id="<?php echo 'participant_' . $data['participants_type_id']; ?>" onclick="changeStatusNew('<?php echo $data['participants_type_id']; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'participant_' . $data['participants_type_id']; ?>');" data-size="small" value="<?php echo $buttonCondition ?>" />
                                            </td>


                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-primary"  
                                                onclick="editForm('<?php echo $data['participants_type_id']; ?>', '<?php echo htmlspecialchars($data['participant_name']); ?>', '<?php echo $data['status']; ?>')">
                                                <i class="fa fa-pencil"></i> 
                                            </a>

                                        </td>
                                    </tr>
                                <?php } }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="participantsModal">
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-uppercase text-white">
                    <span id="modalTitle">Add Participant</span>
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="participantsForm" action="controller/manageParticipantsController.php" method="post">
                    <input type="hidden" name="participants_type_id" id="participants_type_id">
                    <div class="form-group">
                        <label for="participant_name">Participant Name <span class="required">*</span></label>
                        <input type="text" autocomplete="off"  name="participant_name" id="participant_name" class="form-control" required>
                    </div>
                    
                    <div class="form-footer text-center">
                        <button type="submit" name="saveParticipant"  class="btn btn-success">
                            <i class="fa fa-check-square-o"></i> <span id="submitButton">ADD</span>
                        </button>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function editForm(id, name, status) {
        $("#participants_type_id").val(id);
        $("#participant_name").val(name);
        $("#status").prop("checked", status == 1);
        $("#modalTitle").text("Edit Participant");
        $("#submitButton").text("EDIT");
        $("#participantsModal").modal("show");
    }

    function addForm() {
        $("#participants_type_id").val("");
        $("#participant_name").val("");
        $("#status").prop("checked", false);
        $("#modalTitle").text("Add Participant");
        $("#submitButton").text("ADD");
        $("#participantsModal").modal("show");
    }
</script>


