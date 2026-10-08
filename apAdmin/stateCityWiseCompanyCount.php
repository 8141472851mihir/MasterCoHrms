<?php
extract($_REQUEST);
$countryId = (isset($_GET['countryId']) && $_GET['countryId'] > 0) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId'], 101) : 101;
$stateId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
$stateId = $stateId > 0 ? $stateId : '';

$where = "1";
if (!empty($stateId)) {
    $where .= " AND s.state_id = '$stateId'";
} elseif (!empty($countryId)) {
    $where .= " AND st.country_id = '$countryId'";
}
?>

<div class="content-wrapper">
    <div class="container-fluid">

        <div class="row pt-2 pb-2 align-items-center">
            <div class="col-md-4 col-sm-12">
                <h4 class="page-title mb-3">Company Report(State/City)</h4>
            </div>

            <div class="col-md-8 col-sm-12">
                <form action="" method="get" accept-charset="utf-8">
                    <div class="form-row justify-content-end mb-3">
                        <div class="col-md-4">
                            <select required id="country_id" onchange="this.form.submit()"
                                class="form-control single-select" name="countryId">
                                <option value="">-- Select Country --</option>
                                <?php
                                $qc = $d->select("countries", "flag=1");
                                while ($cData = mysqli_fetch_array($qc)) {
                                ?>
                                    <option value="<?= $cData['country_id'] ?>" <?= ($cData['country_id'] == $countryId) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cData['name']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="position-relative">
                                <select onchange="this.form.submit()" class="form-control single-select pr-5" id="state_id"
                                    name="sId" style="padding-right: 2.5rem !important;">
                                    <option value="">-- All States --</option>
                                    <?php
                                    if (!empty($countryId)) {
                                        $qs = $d->select("states", "country_id='$countryId'");
                                        while ($sData = mysqli_fetch_array($qs)) {
                                    ?>
                                            <option value="<?= $sData['state_id'] ?>" <?= ($sData['state_id'] == $stateId) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($sData['name']) ?>
                                            </option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <?php if (!empty($stateId)): ?>
                                    <a href="?countryId=<?= $countryId ?>" 
                                       class="position-absolute btn btn-link text-danger p-0"
                                       style="right: 10px; top: 50%; transform: translateY(-50%); z-index: 5;"
                                       title="Clear selection">
                                        <i class="fa fa-times-circle fa-lg"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="reportTable1" class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <?php if (empty($stateId)): ?>
                                            <th>State</th>
                                        <?php else: ?>
                                            <th>City</th>
                                        <?php endif; ?>
                                        <th class="text-end">Company Count</th>
                                    </tr>
                                </thead>
                                <tfoot class="bottom-footer">
                                    <tr>
                                        <th class="no-search-box"></th>
                                        <?php if (empty($stateId)): ?>
                                            <th></th>
                                        <?php else: ?>
                                            <th></th>
                                        <?php endif; ?>
                                        <th class="find-count"></th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    
                                    if (empty($stateId)) {
                                        // Show states only when no specific state is selected
                                        $reportResult = $d->selectRow(
                                            "st.name as state, COUNT(*) as count",
                                            "society_master s 
                                             JOIN states st ON st.state_id = s.state_id",
                                            "$where",
                                            "GROUP BY s.state_id ORDER BY st.name"
                                        );
                                        
                                        while ($row = mysqli_fetch_assoc($reportResult)) {
                                    ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td><?= htmlspecialchars($row['state']) ?></td>
                                                <td class="text-end"><?= number_format($row['count']) ?></td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        // Show cities when a specific state is selected
                                        $reportResult = $d->selectRow(
                                            "c.name as city, COUNT(*) as count",
                                            "society_master s 
                                             JOIN cities c ON c.city_id = s.city_id",
                                            "$where",
                                            "GROUP BY s.city_id ORDER BY c.name"
                                        );
                                        
                                        while ($row = mysqli_fetch_assoc($reportResult)) {
                                    ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td><?= htmlspecialchars($row['city']) ?></td>
                                                <td class="text-end"><?= number_format($row['count']) ?></td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>