<?php


include_once 'common/object.php';
$batch_id = isset($_POST['batch_id']) ? intval($_POST['batch_id']) : 0;

if ($batch_id <= 0) {
    echo '<div class="alert alert-danger">Invalid batch ID.</div>';
    exit;
}

$today = date('Y-m-d');

$batchQuery = $d->selectRow(
    "batch_id, batch_name, training_days, batch_type",
    "training_batch_master",
    "batch_id = '$batch_id'"
);

if (mysqli_num_rows($batchQuery) == 0) {
    echo '<div class="alert alert-danger">Batch not found.</div>';
    exit;
}

$batchData = mysqli_fetch_assoc($batchQuery);

// Get all slots for this batch (upcoming and today)
$slotsQuery = $d->selectRow(
    "bsm.slot_id, bsm.slot_name, bsm.date, bsm.from_time, bsm.to_time, bsm.company_id, bsm.reference_company_ids, bsm.city,
     bam.admin_name AS trainer_name",
    "batch_slot_master bsm
     LEFT JOIN bms_admin_master bam ON bam.admin_id = bsm.trainer_id",
    "bsm.batch_id = '$batch_id' AND bsm.date >= '$today' AND bsm.year = YEAR(CURDATE())",
    "ORDER BY bsm.date ASC, bsm.from_time ASC"
);

$allSlots = [];
$allCompanyIds = [];
$slotsWithCompanies = 0;

while ($slot = mysqli_fetch_assoc($slotsQuery)) {
    $slotCompanyIds = !empty($slot['company_id']) ? explode(',', $slot['company_id']) : [];
    $slotReferenceIds = !empty($slot['reference_company_ids']) ? explode(',', $slot['reference_company_ids']) : [];
    $slotCompanyIds = array_filter($slotCompanyIds);
    $slotReferenceIds = array_filter($slotReferenceIds);

    $hasCompanies = !empty($slotCompanyIds) || !empty($slotReferenceIds);
    if ($hasCompanies) {
        $slotsWithCompanies++;
        $allCompanyIds = array_merge($allCompanyIds, $slotCompanyIds, $slotReferenceIds);
    }

    $allSlots[] = [
        'slot_id' => $slot['slot_id'],
        'slot_name' => $slot['slot_name'],
        'date' => $slot['date'],
        'from_time' => $slot['from_time'],
        'to_time' => $slot['to_time'],
        'trainer_name' => $slot['trainer_name'],
        'city' => $slot['city'],
        'company_count' => count($slotCompanyIds) + count($slotReferenceIds),
        'has_companies' => $hasCompanies
    ];
}

$allCompanyIds = array_unique(array_filter($allCompanyIds));
$totalCompanies = count($allCompanyIds);

?>
<div class="card">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0">Batch Information</h6>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <strong>Batch Name:</strong> <?php echo htmlspecialchars($batchData['batch_name']); ?> (ID: <?php echo $batchData['batch_id']; ?>)
            </div>
            <div class="col-md-4">
                <strong>Training Days:</strong> <?php echo $batchData['training_days']; ?>
            </div>
            <div class="col-md-4">
                <strong>Batch Type:</strong>
                <?php
                $batchTypeValue = isset($batchData['batch_type']) ? (int)$batchData['batch_type'] : -1;
                $batchTypeName = ($batchTypeValue == 0) ? "Monday To Friday" : (($batchTypeValue == 1) ? "Saturday & Sunday" : (($batchTypeValue == 2) ? "Monday" : (($batchTypeValue == 3) ? "Tuesday" : (($batchTypeValue == 4) ? "Wednesday" : (($batchTypeValue == 5) ? "Thursday" : (($batchTypeValue == 6) ? "Friday" : (($batchTypeValue == 7) ? "Saturday" : (($batchTypeValue == 9) ? "Any Day" : (($batchTypeValue == 8) ? "Sunday" : "N/A")))))))));
                echo htmlspecialchars($batchTypeName);
                ?>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <strong>Total Slots:</strong> <span class="badge badge-primary"><?php echo count($allSlots); ?></span>
            </div>
            <div class="col-md-4">
                <strong>Slots with Companies:</strong> <span class="badge badge-success"><?php echo $slotsWithCompanies; ?></span>
            </div>
            <div class="col-md-4">
                <strong>Total Companies:</strong> <span class="badge badge-info"><?php echo $totalCompanies; ?></span>
            </div>
        </div>
    </div>
</div>

<?php if (empty($allSlots)): ?>
    <div class="alert alert-warning mt-3">No upcoming slots found for this batch.</div>
<?php else: ?>
    <div class="card mt-3">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">Slots List (<?php echo count($allSlots); ?>)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Slot Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Trainer</th>
                            <th>City</th>
                            <th>Companies</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $index = 1;
                        foreach ($allSlots as $slot) {
                            $dateClass = ($slot['date'] == $today) ? 'text-warning font-weight-bold' : '';
                            echo '<tr class="' . ($slot['has_companies'] ? '' : 'table-secondary') . '">';
                            echo '<td>' . $index++ . '</td>';
                            echo '<td>' . htmlspecialchars($slot['slot_name']) . '</td>';
                            echo '<td class="' . $dateClass . '">';
                            echo date('d M Y', strtotime($slot['date']));
                            if ($slot['date'] == $today) echo ' <span class="badge badge-warning">Today</span>';
                            echo '</td>';
                            echo '<td>' . date('h:i A', strtotime($slot['from_time'])) . ' - ' . date('h:i A', strtotime($slot['to_time'])) . '</td>';
                            echo '<td>' . htmlspecialchars($slot['trainer_name'] ?? 'N/A') . '</td>';
                            echo '<td>' . htmlspecialchars($slot['city'] ?? '-') . '</td>';
                            if ($slot['has_companies']) {
                                echo '<td><span class="badge badge-success">' . $slot['company_count'] . ' Company(s)</span></td>';
                            } else {
                                echo '<td><span class="badge badge-secondary">No Companies</span></td>';
                            }
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($totalCompanies > 0): ?>
        <div class="card mt-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">All Companies in Batch (<?php echo $totalCompanies; ?>)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Company Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($allCompanyIds)) {
                                $companyIdsStr = implode(',', $allCompanyIds);
                                $companiesQuery = $d->selectRow(
                                    "society_id, society_name",
                                    "society_master",
                                    "society_id IN ($companyIdsStr)",
                                    "ORDER BY society_name ASC"
                                );
                                $compIndex = 1;
                                while ($company = mysqli_fetch_assoc($companiesQuery)) {
                                    echo '<tr>';
                                    echo '<td>' . $compIndex++ . '</td>';
                                    echo '<td>' . htmlspecialchars($company['society_name']) . '</td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>