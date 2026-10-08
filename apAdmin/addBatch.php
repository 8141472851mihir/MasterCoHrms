<style>
    .training-day-card {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .training-day-card .card-header {
        font-size: 16px;
        font-weight: bold;
        color: #007bff;
    }

    .training-day-card select:focus,
    .training-day-card input:focus {
        border-color: #007bff;
        outline: none;
    }

    .topic-container {
        margin-top: 10px;
    }

    .btn-show-topic {
        margin-top: 5px;
        font-size: 12px;
        color: #007bff;
        cursor: pointer;
        text-decoration: underline;
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="addBatchForm" action="controller/batchController.php" method="post">
                            <?php
                            if (isset($_POST['editBatch'])) {
                                $btnName = "Update";
                                extract(array_map("test_input", $_POST));
                                $q = $d->select("training_batch_master", "batch_id='$batch_id'");
                                $data = mysqli_fetch_array($q);
                                $allowedParticipants = explode(",", $data['allowed_participants']);
                            } else {
                                $btnName = "Add";
                                $data = [];
                                $allowedParticipants = [];
                            }
                            $modulesQuery = $d->select("training_module_master", "1");
                            $modules = [];
                            while ($module = mysqli_fetch_array($modulesQuery)) {
                                $modules[] = $module;
                            }
                            ?>
                            <h4 class="form-header text-uppercase">
                                <i class="fa fa-file"></i>
                                <?php echo (isset($_POST['editBatch']) && !isset($_POST['copyBatch'])) ? 'Edit Batch' : 'Add Batch'; ?>
                            </h4>
                            <div class="form-group row">
                                <label for="batch_name" class="col-sm-2 col-form-label">Batch Name <span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <?php
                                    if (isset($_POST['editBatch']) && !isset($_POST['copyBatch'])) {
                                    ?>
                                        <input type="hidden" id="edit_batch_id" name="batch_id" value="<?php echo $data['batch_id']; ?>">
                                        <input type="text" autocomplete="off" maxlength="50" class="form-control" id="batch_name"
                                            name="batch_name" value="<?php echo $data['batch_name']; ?>" required="">
                                    <?php } else if (isset($_POST['copyBatch']) && $_POST['copyBatch'] == "copyBatch") {
                                        $data['batch_name'] = $data['batch_name'] . "_Copy"; ?>
                                        <input type="hidden" id="edit_batch_id" name="batch_id" value="<?php echo $data['batch_id']; ?>">
                                        <input type="text" autocomplete="off" maxlength="50" class="form-control" id="batch_name"
                                            name="batch_name" required="" value="<?php echo $data['batch_name']; ?>">
                                    <?php
                                    } else { ?>
                                        <input type="text" autocomplete="off" maxlength="50" class="form-control" id="batch_name"
                                            name="batch_name" required="" value="">
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="batch_type" class="col-sm-2 col-form-label">Batch Type <span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <select name="batch_type" class="form-control single-select" required>
                                        <option value="9" <?php echo isset($data['batch_type']) && $data['batch_type'] == 9 ? 'selected' : ($data['batch_type'] == '' ? 'selected' : ''); ?>>Any Day</option>
                                        <option value="2" <?php echo isset($data['batch_type']) && $data['batch_type'] == 2 ? 'selected' : ($data['batch_type'] == '' ? 'selected' : ''); ?>>Monday</option>
                                        <option value="3" <?php echo isset($data['batch_type']) && $data['batch_type'] == 3 ? 'selected' : ''; ?>>Tuesday</option>
                                        <option value="4" <?php echo isset($data['batch_type']) && $data['batch_type'] == 4 ? 'selected' : ''; ?>>Wednesday</option>
                                        <option value="5" <?php echo isset($data['batch_type']) && $data['batch_type'] == 5 ? 'selected' : ''; ?>>Thursday</option>
                                        <option value="6" <?php echo isset($data['batch_type']) && $data['batch_type'] == 6 ? 'selected' : ''; ?>>Friday</option>
                                        <option value="7" <?php echo isset($data['batch_type']) && $data['batch_type'] == 7 ? 'selected' : ''; ?>>Saturday</option>
                                        <option value="8" <?php echo isset($data['batch_type']) && $data['batch_type'] == 8 ? 'selected' : ''; ?>>Sunday</option>
                                        <option value="0" <?php echo isset($data['batch_type']) && $data['batch_type'] == 0 || !isset($data['batch_type']) ? 'selected' : ''; ?>>Monday to Friday</option>
                                        <option value="1" <?php echo isset($data['batch_type']) && $data['batch_type'] == 1 ? 'selected' : ''; ?>>Saturday & Sunday</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="participant_name" class="col-sm-2 col-form-label">Participant Name <span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <select id="participant_name" name="participant_name[]" class="form-control single-select" required>
                                        <?php
                                        $participantsQuery = $d->select("training_participants_type", "1");
                                        if (mysqli_num_rows($participantsQuery) > 0) {
                                            while ($participant = mysqli_fetch_array($participantsQuery)) {
                                                $isSelected = in_array($participant['participants_type_id'], $allowedParticipants) ? 'selected' : '';
                                                echo "<option value='" . $participant['participants_type_id'] . "' $isSelected>" . $participant['participant_name'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>No Participants Available</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="training_days" class="col-sm-2 col-form-label">Training Days <span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" min="1" max="30" maxlength="2" class="form-control onlyNumber"
                                        id="training_days" name="training_days"
                                        value="<?php echo isset($data['training_days']) ? $data['training_days'] : ''; ?>" required="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Topic Groups</label>
                                <div class="col-sm-12">
                                    <div id="topicGroupsContainer" class="" style="min-height: 60px;"></div>
                                </div>
                            </div>
                            <div id="trainingDaysContainer"></div>
                            <input type="hidden" id="dayTopicAssignments" name="day_topic_assignments" value="">
                            <input type="hidden" id="dayModulesAssignments" name="day_modules_assignments" value="">
                            <div class="form-footer text-center">
                                <?php if (isset($_POST['editBatch']) && !isset($_POST['copyBatch'])) { ?>
                                    <input name="editBatch" value="editBatch" id="editBatch" type="hidden">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> UPDATE</button>
                                <?php } else { ?>
                                    <input name="addBatch" value="addBatch" type="hidden">
                                    <button name="batchAdd" value="add Batch" type="submit" class="btn btn-success"><i
                                            class="fa fa-check-square-o"></i> ADD</button>
                                <?php } ?>
                                <button type="reset" class="btn btn-danger"><i class="fa fa-times"></i> CANCEL</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>