<?php


include_once 'common/object.php';
$slotPattern = isset($_POST['slot_pattern']) ? trim($_POST['slot_pattern']) : '';

if (empty($slotPattern)) {
    echo '<div class="alert alert-danger">Invalid slot pattern.</div>';
    exit;
}

$today = date('Y-m-d');

// Get all slots matching this pattern (with or without meeting numbers)
// Pattern could be like "B1S1-Dec" or "B1S1-Dec-2"
// We need to match slots like "B1S1-Dec-M1", "B1S1-Dec-M2" for pattern "B1S1-Dec"
// Or "B1S1-Dec-M1-2", "B1S1-Dec-M2-2" for pattern "B1S1-Dec-2"

// Get all upcoming slots and filter by pattern in PHP
// This is more reliable than SQL LIKE patterns
$allSlotsQuery = $d->selectRow(
    "bsm.slot_id, bsm.slot_name, bsm.date, bsm.from_time, bsm.to_time, bsm.company_id, bsm.reference_company_ids, bsm.city, bsm.batch_id,
     bam.admin_name AS trainer_name, tbm.batch_name",
    "batch_slot_master bsm
     LEFT JOIN bms_admin_master bam ON bam.admin_id = bsm.trainer_id
     LEFT JOIN training_batch_master tbm ON tbm.batch_id = bsm.batch_id",
    "bsm.date >= '$today' AND bsm.year = YEAR(CURDATE())",
    "ORDER BY bsm.slot_name ASC, bsm.date ASC"
);

// Filter slots that match the pattern
$matchingSlots = [];
while ($slot = mysqli_fetch_assoc($allSlotsQuery)) {
    $slotName = $slot['slot_name'];
    $slotBasePattern = preg_replace('/-M\d+(-?\d*)$/', '$1', $slotName);
    if ($slotBasePattern === $slotPattern) {
        $matchingSlots[] = $slot;
    }
}

if (empty($matchingSlots)) {
    echo '<div class="alert alert-warning">No slots found for this pattern.</div>';
    exit;
}

$allSlots = [];
$allCompanyIds = [];
$slotsWithCompanies = 0;

foreach ($matchingSlots as $slot) {
    $slotCompanyIds = !empty($slot['company_id']) ? explode(',', $slot['company_id']) : [];
    $slotReferenceIds = !empty($slot['reference_company_ids']) ? explode(',', $slot['reference_company_ids']) : [];
    $slotCompanyIds = array_filter($slotCompanyIds);
    $slotReferenceIds = array_filter($slotReferenceIds);
    
    $hasCompanies = !empty($slotCompanyIds) || !empty($slotReferenceIds);
    if ($hasCompanies) {
        $slotsWithCompanies++;
        $allCompanyIds = array_merge($allCompanyIds, $slotCompanyIds, $slotReferenceIds);
    }
    
    // Format slot name for display
    $slotNameDisplay = $slot['slot_name'];
    
    $allSlots[] = [
        'slot_id' => $slot['slot_id'],
        'slot_name' => $slot['slot_name'],
        'slot_name_display' => $slotNameDisplay,
        'date' => $slot['date'],
        'from_time' => $slot['from_time'],
        'to_time' => $slot['to_time'],
        'trainer_name' => $slot['trainer_name'],
        'city' => $slot['city'],
        'batch_name' => $slot['batch_name'],
        'company_count' => count($slotCompanyIds) + count($slotReferenceIds),
        'has_companies' => $hasCompanies
    ];
}

$allCompanyIds = array_unique(array_filter($allCompanyIds));
$totalCompanies = count($allCompanyIds);

?>
<div class="card">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0">Slot Pattern Information</h6>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <strong>Slot Pattern:</strong> <?php echo htmlspecialchars($slotPattern); ?>
            </div>
            <div class="col-md-4">
                <strong>Total Slots:</strong> <span class="badge badge-primary"><?php echo count($allSlots); ?></span>
            </div>
            <div class="col-md-4">
                <strong>Slots with Companies:</strong> <span class="badge badge-success"><?php echo $slotsWithCompanies; ?></span>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Total Companies:</strong> <span class="badge badge-info"><?php echo $totalCompanies; ?></span>
            </div>
            <div class="col-md-6">
                <strong>Date Range:</strong> 
                <?php 
                if (!empty($allSlots)) {
                    $dates = array_column($allSlots, 'date');
                    $earliest = min($dates);
                    $latest = max($dates);
                    if ($earliest == $latest) {
                        echo date('d M Y', strtotime($earliest));
                        if ($earliest == $today) echo ' <span class="badge badge-warning">Today</span>';
                    } else {
                        echo date('d M Y', strtotime($earliest)) . ' to ' . date('d M Y', strtotime($latest));
                    }
                } else {
                    echo 'N/A';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php if (empty($allSlots)): ?>
    <div class="alert alert-warning mt-3">No slots found for this pattern.</div>
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
                            <th>Batch Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Trainer</th>
                            <th>City</th>
                            <th>Companies</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $index = 1;
                        foreach ($allSlots as $slot) {
                            $dateClass = ($slot['date'] == $today) ? 'text-warning font-weight-bold' : '';
                            echo '<tr class="' . ($slot['has_companies'] ? '' : 'table-secondary') . '">';
                            echo '<td>' . $index++ . '</td>';
                            echo '<td>' . htmlspecialchars($slot['slot_name_display']) . '</td>';
                            echo '<td>' . htmlspecialchars($slot['batch_name'] ?? 'N/A') . '</td>';
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
                            $slotNameForJS = htmlspecialchars($slot['slot_name_display'], ENT_QUOTES);
                            echo '<td><button type="button" class="btn btn-sm btn-info" onclick="showSlotCompanies(' . $slot['slot_id'] . ', \'' . $slotNameForJS . '\')">View Details</button></td>';
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
                <h6 class="mb-0">All Companies in Slot Group (<?php echo $totalCompanies; ?>)</h6>
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

