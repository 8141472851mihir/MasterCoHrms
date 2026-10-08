<?php


include_once 'common/object.php';
$slot_id = isset($_POST['slot_id']) ? intval($_POST['slot_id']) : 0;

if ($slot_id <= 0) {
    echo '<div class="alert alert-danger">Invalid slot ID.</div>';
    exit;
}

$slotQuery = $d->selectRow(
    "bsm.slot_name, bsm.date, bsm.from_time, bsm.to_time, bsm.company_id, bsm.reference_company_ids, bsm.city,
     bam.admin_name AS trainer_name, tbm.batch_name",
    "batch_slot_master bsm
     LEFT JOIN bms_admin_master bam ON bam.admin_id = bsm.trainer_id
     LEFT JOIN training_batch_master tbm ON tbm.batch_id = bsm.batch_id",
    "bsm.slot_id = '$slot_id'"
);

if (mysqli_num_rows($slotQuery) == 0) {
    echo '<div class="alert alert-danger">Slot not found.</div>';
    exit;
}

$slotData = mysqli_fetch_assoc($slotQuery);
$companyIds = !empty($slotData['company_id']) ? explode(',', $slotData['company_id']) : [];
$referenceIds = !empty($slotData['reference_company_ids']) ? explode(',', $slotData['reference_company_ids']) : [];
$companyIds = array_filter($companyIds);
$referenceIds = array_filter($referenceIds);

?>
<div class="card">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0">Slot Information</h6>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Slot Name:</strong> <?php echo htmlspecialchars($slotData['slot_name']); ?>
            </div>
            <div class="col-md-6">
                <strong>Batch Name:</strong> <?php echo htmlspecialchars($slotData['batch_name'] ?? 'N/A'); ?>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Date:</strong> <?php echo date('d M Y', strtotime($slotData['date'])); ?>
            </div>
            <div class="col-md-6">
                <strong>Time:</strong> <?php echo date('h:i A', strtotime($slotData['from_time'])) . ' - ' . date('h:i A', strtotime($slotData['to_time'])); ?>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Trainer:</strong> <?php echo htmlspecialchars($slotData['trainer_name'] ?? 'N/A'); ?>
            </div>
            <div class="col-md-6">
                <strong>City:</strong> <?php echo htmlspecialchars($slotData['city'] ?? '-'); ?>
            </div>
        </div>
    </div>
</div>

<?php if (empty($companyIds) && empty($referenceIds)): ?>
    <div class="alert alert-warning mt-3">No companies assigned to this slot.</div>
<?php else: ?>
    <div class="card mt-3">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">Companies List (<?php echo count($companyIds) + count($referenceIds); ?>)</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Company Name</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $index = 1;
                    if (!empty($companyIds)) {
                        $companyIdsStr = implode(',', $companyIds);
                        $companiesQuery = $d->selectRow(
                            "society_id, society_name",
                            "society_master",
                            "society_id IN ($companyIdsStr)"
                        );
                        while ($company = mysqli_fetch_assoc($companiesQuery)) {
                            echo '<tr>';
                            echo '<td>' . $index++ . '</td>';
                            echo '<td>' . htmlspecialchars($company['society_name']) . '</td>';
                            echo '<td><span class="badge badge-success">Main</span></td>';
                            echo '</tr>';
                        }
                    }
                    if (!empty($referenceIds)) {
                        $referenceIdsStr = implode(',', $referenceIds);
                        $refCompaniesQuery = $d->selectRow(
                            "society_id, society_name",
                            "society_master",
                            "society_id IN ($referenceIdsStr)"
                        );
                        while ($refCompany = mysqli_fetch_assoc($refCompaniesQuery)) {
                            echo '<tr>';
                            echo '<td>' . $index++ . '</td>';
                            echo '<td>' . htmlspecialchars($refCompany['society_name']) . '</td>';
                            echo '<td><span class="badge badge-info">Reference</span></td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

