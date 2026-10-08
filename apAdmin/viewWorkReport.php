<?php
extract($_GET);
$query = $d->selectRow(
    "iwr.implementation_work_report_id,
     iwr.admin_id,
     bms.admin_name,
     iwr.report_date,
     iwr.report_type,
     iwr.company_ids,
     iwr.no_of_call,
     iwr.no_of_linedup,
     iwr.report_desc,
     iwr.added_date,
     GROUP_CONCAT(sm.society_name) AS company_names",
    "implementation_work_report iwr
     JOIN bms_admin_master bms ON iwr.admin_id = bms.admin_id
     LEFT JOIN society_master sm ON FIND_IN_SET(sm.society_id, iwr.company_ids)",
    "iwr.implementation_work_report_id = '$implementation_work_report_id'",
    "GROUP BY iwr.implementation_work_report_id"
);

if (!$query || mysqli_num_rows($query) == 0) {
    echo "<div class='alert alert-warning'>No report found.</div>";
    exit;
}

$row = mysqli_fetch_assoc($query);
$reportTypes = [
    0 => "Setup Training",
    1 => "Product Training"
];

$host_id = $row['admin_id'];
?>

<div class="content-wrapper">
    <div class="container-fluid">

        <!-- Implementation Report Section -->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Work Report</h4>
            </div>
            <?php
            $source = isset($_GET['source']) ? $_GET['source'] : 'implementWorkreport';
            $report_date = isset($_GET['report_date']) ? $_GET['report_date'] : date('Y-m-d');
            $active_tab = isset($_GET['active_tab']) ? $_GET['active_tab'] : 'setup';

            $back_url = $source . "?report_date={$report_date}&active_tab={$active_tab}";
            ?>


            <div class="col-sm-3 text-right">
                <a href="<?php echo $back_url; ?>" class="btn btn-primary btn-sm">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>


        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white"> Report Details</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <p><span class="font-weight-bold">Trainer Name: </span><?= $row['admin_name']; ?></p>
                                <p><span class="font-weight-bold">Report Date: </span>
                                    <?php echo date('l, d F Y', strtotime($row['report_date'])); ?>
                                </p>
                                <p><span class="font-weight-bold">Report Type:
                                    </span><?= $reportTypes[$row['report_type']] ?? 'N/A'; ?></p>
                                <p><span class="font-weight-bold">No. of Calls: </span><?= $row['no_of_call']; ?></p>
                                <p><span class="font-weight-bold">No. of Lined Up: </span><?= $row['no_of_linedup']; ?>
                                </p>
                                <p><span class="font-weight-bold">Total Companies:
                                    </span><?= count(explode(',', $row['company_ids'])); ?></p>
                                <p><span class="font-weight-bold">Company List: </span>
                                    <?= implode(', ', array_map('trim', explode(',', $row['company_names']))); ?>
                                </p>

                                <p><span class="font-weight-bold">Added Date: </span>
                                    <?php echo date('l, d F Y', strtotime($row['added_date'])); ?>
                                <p><span class="font-weight-bold">Report Description: </span><?= $row['report_desc']; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    $implementation_work_report_id = $_REQUEST['implementation_work_report_id'];
    $report = $d->selectRow("*", "implementation_work_report", "implementation_work_report_id = $implementation_work_report_id");
    $reportData = mysqli_fetch_assoc($report);

    $report_type = $reportData['report_type'];
    $report_date = $reportData['report_date'];
    $admin_id = $reportData['admin_id'];

    if ($report_type == 0) {
        // SETUP TRAINING REPORT
        $result = $d->selectRow(
            "tsm.setup_meeting_name, sm.society_name, tam.absent_present, tsm.start_time, tsm.end_time",
            "training_schedule_master tsm
         JOIN training_attend_master tam ON tsm.training_schedule_master_id = tam.training_schedule_master_id
         JOIN society_master sm ON tam.society_id = sm.society_id",
            "tsm.training_date = '$report_date' AND tsm.host_id = '$admin_id' AND tam.attend_type = 0",
            "ORDER BY tsm.setup_meeting_name, sm.society_name"
        );

        $cardData = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $meeting = $row['setup_meeting_name'];
            $status = ($row['absent_present'] == 1) ? 'Present' : 'Absent';

            if (!isset($cardData[$meeting])) {
                $cardData[$meeting] = [
                    'companies' => [],
                    'present' => 0,
                    'absent' => 0,
                    'from_time' => $row['start_time'],
                    'to_time' => $row['end_time']
                ];
            }

            if ($status === 'Present') {
                $cardData[$meeting]['present']++;
            } else {
                $cardData[$meeting]['absent']++;
            }

            $cardData[$meeting]['companies'][] = [
                'society_name' => $row['society_name'],
                'status' => $status
            ];
        }

        foreach ($cardData as $meeting => $data): ?>
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    Setup Meeting: <?= $meeting; ?>
                </div>
                <div class="card-body">
                    <p><strong>Total Company:</strong> <?= count($data['companies']); ?></p>
                    <p><strong>Total Present Company:</strong> <?= $data['present']; ?></p>
                    <p><strong>Total Absent Company:</strong> <?= $data['absent']; ?></p>
                    <p><strong>Meeting Time:</strong>
                        <?= date("g:i A", strtotime($data['from_time'])); ?>
                        TO
                        <?= date("g:i A", strtotime($data['to_time'])); ?>
                    </p>
                    <hr>
                    <ul>
                        <?php foreach ($data['companies'] as $society): ?>
                            <li>
                                <?= $society['society_name']; ?> -
                                <strong><?= $society['status']; ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach;

    } else {
        // PRODUCT TRAINING REPORT
        $result = $d->selectRow(
            "bsm.slot_name, sm.society_name, tam.absent_present, bsm.from_time, bsm.to_time",
            "batch_slot_master bsm
         JOIN training_attend_master tam ON bsm.slot_id = tam.training_slot_id
         JOIN society_master sm ON FIND_IN_SET(sm.society_id, tam.society_id)",
            "bsm.date = '$report_date' AND bsm.trainer_id = '$admin_id' AND tam.attend_type = 1",
            "ORDER BY bsm.slot_name, sm.society_name"
        );

        $cardData = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $slot = $row['slot_name'];
            $status = ($row['absent_present'] == 1) ? 'Present' : 'Absent';

            if (!isset($cardData[$slot])) {
                $cardData[$slot] = [
                    'companies' => [],
                    'present' => 0,
                    'absent' => 0,
                    'from_time' => $row['from_time'],
                    'to_time' => $row['to_time']
                ];
            }

            if ($status === 'Present') {
                $cardData[$slot]['present']++;
            } else {
                $cardData[$slot]['absent']++;
            }

            $cardData[$slot]['companies'][] = [
                'society_name' => $row['society_name'],
                'status' => $status
            ];
        }

        foreach ($cardData as $slot => $data): ?>
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    Slot: <?= $slot; ?>
                </div>
                <div class="card-body">
                    <p><strong>Total Company:</strong> <?= count($data['companies']); ?></p>
                    <p><strong>Total Present Company:</strong> <?= $data['present']; ?></p>
                    <p><strong>Total Absent Company:</strong> <?= $data['absent']; ?></p>
                    <p><strong>Meeting Time:</strong>
                        <?= date("g:i A", strtotime($data['from_time'])); ?>
                        TO
                        <?= date("g:i A", strtotime($data['to_time'])); ?>
                    </p>

                    <hr>
                    <ul>
                        <?php foreach ($data['companies'] as $society): ?>
                            <li>
                                <?= $society['society_name']; ?> -
                                <strong><?= $society['status']; ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach;
    }
    ?>