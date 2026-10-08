<?php
$batches = $d->selectRow("training_batch_master.*,GROUP_CONCAT(DISTINCT pt.participant_name ORDER BY pt.participants_type_id) AS participant_names", "training_batch_master LEFT JOIN training_participants_type pt ON (training_batch_master.allowed_participants IS NOT NULL AND training_batch_master.allowed_participants <> '' AND FIND_IN_SET(pt.participants_type_id, training_batch_master.allowed_participants) )", "training_batch_master.status=0", "GROUP BY training_batch_master.batch_id");
$trainers = $d->select("bms_admin_master", "(role_id != 1) AND active_status='0'", "");
$meetings = $d->select("batch_meetings_master", "");
$participantTypes = [];
$participants = $d->select("training_participants_type", "");
while ($participant = mysqli_fetch_assoc($participants)) {
    $participantTypes[$participant['participants_type_id']] = $participant['participant_name'];
}

$societies = $d->selectRow("society_master.society_name,society_master.society_id", "society_master", "society_status='0' AND society_name!='' AND created_on_society_server='1'", "ORDER BY society_id DESC");
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Add Batch Slot</h4>
            </div>
            <div class="col-sm-3">
                <div class="btn-group float-sm-right">
                    <a href="manageTrainingSlots" class="btn btn-primary btn-sm">
                        <i class="fa fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="batchSlotForm" action="controller/addBatchSlotController.php" method="post">
                            <input type="hidden" name="selected_dates" id="selected_dates">

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label>Batch Name <span class="required">*</span></label>
                                    <select name="batch_id" id="batch_id" class="form-control single-select">
                                        <option value="">-- Select Batch --</option>
                                        <?php while ($row = mysqli_fetch_assoc($batches)) { ?>
                                            <option value="<?php echo htmlspecialchars($row['batch_id']); ?>"
                                                data-days="<?php echo htmlspecialchars($row['training_days']); ?>"
                                                data-batch-type="<?php echo htmlspecialchars($row['batch_type']); ?>"
                                                data-allowed-participants="<?php echo htmlspecialchars($row['participant_names']); ?>">
                                                <?php echo htmlspecialchars($row['batch_name']); ?>
                                            </option>

                                        <?php } ?>
                                    </select>
                                </div>


                                <div class="col-md-4">
                                    <label>Date <span class="required">*</span></label>
                                    <input type="text" class="form-control datepicker" name="start_date" id="start_date" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label>City </label>
                                    <input type="text" autocomplete="off" class="form-control" name="city">
                                </div>


                            </div>



                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label>Number of Days: <span id="training_days_label">-</span></label>
                                    <input type="hidden" class="form-control form-control-sm" id="training_days" name="training_days" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label>Batch Type: <span id="batch_type_label">-</span></label>
                                    <input type="hidden" class="form-control" id="batch_type" name="batch_type" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label>Allowed Participants:<span id="allowed_participants_label">-</span></label>
                                    <input type="hidden" class="form-control" id="allowed_participants" name="allowed_participants" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="society_id" class="col-form-label">Company Name </label>
                                    <select name="society_id[]" id="society_id" class="form-control multiple-select" multiple="multiple">
                                        <?php while ($data = mysqli_fetch_array($societies)) { ?>
                                            <option value="<?php echo $data['society_id']; ?>">
                                                <?php echo htmlspecialchars($data['society_name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <?php mysqli_data_seek($societies, 0); ?>
                                <div class="col-md-4">
                                    <label for="reference_society_id" class="col-form-label">Reference Company Name </label>
                                    <select name="reference_society_id[]" id="reference_society_id" class="form-control multiple-select" multiple="multiple">
                                        <?php while ($data = mysqli_fetch_array($societies)) { ?>
                                            <option value="<?php echo $data['society_id']; ?>">
                                                <?php echo htmlspecialchars($data['society_name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Meeting Slots</label>
                                <div id="meetingSlots" class="p-1">
                                    <?php
                                    $trainersArray = [];
                                    while ($trainer = mysqli_fetch_assoc($trainers)) {
                                        $trainersArray[] = $trainer;
                                    }
                                    $i = 1;
                                    $j = 0;
                                    while ($row = mysqli_fetch_assoc($meetings)) {
                                    ?>
                                        <div class="row mb-2 align-items-center g-2">
                                            <div class="col-md-2">
                                                <p class="form-control-static small-box">
                                                    <?php echo htmlspecialchars($row['meeting_name']); ?>
                                                </p>
                                                <input type="hidden" class="form-control" name="meeting_name[]"
                                                    value="<?php echo htmlspecialchars($row['meeting_name']); ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control session-time-picker" name="meeting_from_time[]"
                                                    value="<?php echo htmlspecialchars($row['meeting_from_time']); ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control session-time-picker" name="meeting_to_time[]"
                                                    value="<?php echo htmlspecialchars($row['meeting_to_time']); ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <select name="meeting_trainer_id[<?php echo $j++; ?>]" id="meeting_trainer_id_<?php echo $i++; ?>" class="form-control single-select">
                                                    <option value="">-- Select Trainer --</option>
                                                    <?php foreach ($trainersArray as $trainer) { ?>
                                                        <option value="<?php echo htmlspecialchars($trainer['admin_id']); ?>">
                                                            <?php echo htmlspecialchars($trainer['admin_name']); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">Training Schedule</div>
                                        <div class="card-body" id="scheduleCard">
                                        </div>
                                        <div class="card-body" id="scheduleCardError">
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">Holidays </div>
                                        <div class="card-body" id="removedDaysCard"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-footer text-center">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="datePickerModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select a Date</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="text" id="customDatePicker" class="form-control">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmAddDate">Add Date</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>