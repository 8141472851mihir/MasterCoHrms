<?php
extract($_REQUEST);

if (isset($meeting_slot_id) && $meeting_slot_id != '') {
    $q = $d->selectRow(
        "batch_slot_master.*, 
    bms_admin_master.admin_name AS trainer_name, 
    bms_admin_master.admin_id AS trainer_id, 
    training_batch_master.batch_name, 
    GROUP_CONCAT(training_participants_type.participant_name ORDER BY training_participants_type.participants_type_id) AS allowed_participant_names",
        "batch_slot_master 
    LEFT JOIN bms_admin_master ON bms_admin_master.admin_id = batch_slot_master.trainer_id 
    LEFT JOIN training_batch_master ON training_batch_master.batch_id = batch_slot_master.batch_id 
    LEFT JOIN training_participants_type ON FIND_IN_SET(training_participants_type.participants_type_id, training_batch_master.allowed_participants) AND training_participants_type.status = 0",
        "batch_slot_master.slot_id = '$meeting_slot_id'"
    );

    $data = mysqli_fetch_array($q);
    extract($data);
    $allowed_participants_name = $data['allowed_participant_names'];
    $batch_id = $data['batch_id'];
    $trainer_id = $data['trainer_id'];
    $meeting_day = $data['meeting_day'];
    $reference_company_ids = $data['reference_company_ids'];

    $company_ids = $data['company_id'];
    if (isset($company_ids) && !empty($company_ids)) {
        $company_ids = explode(',', $company_ids);
    } else {
        $company_ids = [];
    }
    if (isset($reference_company_ids) && !empty($reference_company_ids)) {
        $reference_company_ids = explode(',', $reference_company_ids);
    } else {
        $reference_company_ids = [];
    }
} else {
    echo '<script>window.location.href = "manageTrainingSlots";</script>';
    exit();
}
?>
<style>
    .readonly-style {
        background-color: #e9ecef !important;
        pointer-events: none;
        opacity: 1;
        color: #6c757d;
    }
</style>
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Manage Training Meeting</h4>
            </div>
            <div class="col-sm-3 text-right">
                <?php
                $backUrl = 'manageTrainingSlots';
                $queryParams = [];
                if (isset($_GET['company_filter']) && $_GET['company_filter'] != '') {
                    $queryParams[] ='company_filter=' . urlencode($_GET['company_filter']);
                }
                if (!empty($queryParams)) {
                    $backUrl .= '?' . implode('&', $queryParams);
                }
                ?>
                <a href="<?php echo $backUrl; ?>" class="btn btn-primary btn-sm">
                    <i class="fa fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <!-- Main Meeting Details -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <p class=""><span class="font-weight-bold">Meeting ID:
                                    </span><?php echo $data['slot_name']; ?></p>
                                <p class=""><span class="font-weight-bold">Batch Name:
                                    </span><?php echo $data['batch_name']; ?></p>
                                <p class=""><span class="font-weight-bold">Trainer Name:
                                    </span><?php echo $data['trainer_name']; ?></p>
                                <p class="card-text"><span class="font-weight-bold">Training Date: </span>
                                    <?php echo date('l, d F Y', strtotime($data['date'])); ?></p>
                                <p class="card-text">
                                    <span class="font-weight-bold">Training Time: </span>
                                    <?php echo date("h:i A", strtotime($data['from_time'])); ?>
                                    <span class="font-weight-bold"> To </span>
                                    <?php echo date("h:i A", strtotime($data['to_time'])); ?>
                                </p>

                                <?php
                                if ($data['city'] != "") {
                                ?>
                                    <p class="card-text"><span class="font-weight-bold">City: </span>
                                        <?php echo $data['city']; ?> </p>
                                    <p class="card-text"><span class="font-weight-bold">Allowed Participants: </span>
                                        <?php echo $allowed_participants_name; ?> </p>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="col-sm-6">
                                <div>
                                    <?php
                                    $q = $d->select("society_master", "society_status='0' AND society_name!=''AND created_on_society_server='1'", "ORDER BY society_id DESC");
                                    $companies = [];
                                    while ($row = mysqli_fetch_assoc($q)) {
                                        $companies[] = $row;
                                    }
                                    ?>

                                    <form action="updateTrainingSlot" method="post" autocomplete="off">
                                        <label for="society_id" class="col-form-label">Company Name</label>
                                        <select name="society_id[]" id="batchCompanySelect" multiple="multiple"
                                            class="form-control multiple-select" placeholder="company_name">
                                            <?php
                                            $q = $d->select("society_master", "society_status='0' AND society_name!=''AND created_on_society_server='1'", "order by society_id DESC");
                                            foreach ($companies as $company):
                                                $selected = in_array($company['society_id'], $company_ids) && !in_array($company['society_id'], $reference_company_ids) ? 'selected' : '';
                                            ?>
                                                <option value="<?= $company['society_id']; ?>" <?= $selected; ?>>
                                                    <?= strip_tags($company['society_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <label for="reference_company_ids" class="col-form-label">Reference Company
                                            Name</label>
                                        <select name="reference_company_ids[]" id="referenceCompanySelect"
                                            class="form-control multiple-select" placeholder="Select Reference Company"
                                            multiple="multiple">
                                            <?php foreach ($companies as $company): ?>
                                                <?php
                                                $selected = in_array($company['society_id'], $company_ids) && in_array($company['society_id'], $reference_company_ids) ? 'selected' : '';
                                                ?>
                                                <option value="<?= $company['society_id']; ?>" <?= $selected; ?>>
                                                    <?= strip_tags($company['society_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>

                                </div>
                            </div>
                        </div>

                        <form method="POST" action="controller/batchController.php" id="trainingMeetingForm" enctype="multipart/form-data">

                            <input type="hidden" id="slot_id" name="slot_id" value="<?php echo $slot_id; ?>">
                            <input type="hidden" id="meeting_day" name="meeting_day"
                                value="<?php echo $meeting_day; ?>">
                            <input type="hidden" id="batch_id" name="batch_id" value="<?php echo $batch_id; ?>">
                            <input type="hidden" id="allowed_participants" name="allowed_participants"
                                value="<?php echo $allowed_participants_name; ?>">
                            <input type="hidden" id="trainer_id" name="trainer_id" value="<?php echo $trainer_id; ?>">
                            <input type="hidden" id="startBatchMeeting" name="startBatchMeeting"
                                value="startBatchMeeting">
                            <input type="hidden" id="batchTrainingStatus" name="batchTrainingStatus"
                                value="batchTrainingStatus">
                            <div id="companyUIContainer" class="mt-4"></div>

                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <label for="recording_link" class="col-form-label">Recording Link</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" autocomplete="off" class="form-control" id="recording_link"
                                        name="recording_link" placeholder="Recording Link" value="" maxlength="150">
                                </div>
                            </div>

                            <div class="row align-items-center mt-3">
                                <div class="col-md-2">
                                    <label for="training_password" class="col-form-label">Training Password</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" autocomplete="off" class="form-control" id="training_password"
                                        name="training_password" placeholder="Training Password" value=""
                                        maxlength="150">
                                </div>
                            </div>

                            <div class="row align-items-center mt-3">
                                <div class="col-md-2">
                                    <label for="mom_attachment" class="col-form-label">MOM Attachment <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-10">
                                    <input type="file" class="form-control-file" id="mom_attachment" name="mom_attachment"
                                        accept=".jpg,.jpeg,.png" required>
                                    <small class="form-text text-muted">Upload meeting MOM screenshot (JPG, JPEG, PNG)</small>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-3" id="submitTrainingMeeting">
                                    <?php echo 'Submit'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>