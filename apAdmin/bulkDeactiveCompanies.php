<?php
if (!isset($role_id) || (int)$role_id !== 1) {
    echo '<div class="content-wrapper"><div class="container-fluid"><div class="alert alert-danger mt-3">Access denied.</div></div></div>';
    return;
}

mysqli_query($con, "CREATE TABLE IF NOT EXISTS temp_company_deactive (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    url VARCHAR(500) NOT NULL,
    status TINYINT NOT NULL DEFAULT 0 COMMENT '0=pending,1=deleted,2=not_404,3=not_found,4=error,5=processing',
    society_id INT DEFAULT NULL,
    society_name VARCHAR(255) DEFAULT NULL,
    http_code VARCHAR(20) DEFAULT NULL,
    message VARCHAR(500) DEFAULT NULL,
    processed_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

function tempDeactiveStatusLabel($status)
{
    switch ((int)$status) {
        case 0:
            return '<span class="badge badge-warning">Pending</span>';
        case 1:
            return '<span class="badge badge-success">Deleted</span>';
        case 2:
            return '<span class="badge badge-danger">Not 404</span>';
        case 3:
            return '<span class="badge badge-secondary">Not found</span>';
        case 4:
            return '<span class="badge badge-dark">Error</span>';
        case 5:
            return '<span class="badge badge-info">Processing</span>';
        default:
            return '<span class="badge badge-light">Unknown</span>';
    }
}

$pendingCount = 0;
$deletedCount = 0;
$not404Count = 0;
$notFoundCount = 0;
$errorCount = 0;
$totalCount = 0;
$cq = $d->selectRow("status, COUNT(*) AS cnt", "temp_company_deactive", "1", "GROUP BY status");
if ($cq) {
    while ($cRow = mysqli_fetch_assoc($cq)) {
        $cnt = (int)$cRow['cnt'];
        $totalCount += $cnt;
        switch ((int)$cRow['status']) {
            case 0:
                $pendingCount = $cnt;
                break;
            case 1:
                $deletedCount = $cnt;
                break;
            case 2:
                $not404Count = $cnt;
                break;
            case 3:
                $notFoundCount = $cnt;
                break;
            case 4:
                $errorCount = $cnt;
                break;
        }
    }
}
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Bulk Deactive Companies (Temporary)</h4>
                <p class="mb-0 text-muted">Same as the Deactive button: check sub_domain returns 404, then delete the company from master. Processes 1 by 1.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <div class="card"><div class="card-body text-center"><small>Pending</small><h4 id="cntPending"><?php echo $pendingCount; ?></h4></div></div>
            </div>
            <div class="col-md-2">
                <div class="card"><div class="card-body text-center"><small>Deleted</small><h4 class="text-success" id="cntDeleted"><?php echo $deletedCount; ?></h4></div></div>
            </div>
            <div class="col-md-2">
                <div class="card"><div class="card-body text-center"><small>Not 404</small><h4 class="text-danger" id="cntNot404"><?php echo $not404Count; ?></h4></div></div>
            </div>
            <div class="col-md-2">
                <div class="card"><div class="card-body text-center"><small>Not found</small><h4 class="text-secondary" id="cntNotFound"><?php echo $notFoundCount; ?></h4></div></div>
            </div>
            <div class="col-md-2">
                <div class="card"><div class="card-body text-center"><small>Error</small><h4 id="cntError"><?php echo $errorCount; ?></h4></div></div>
            </div>
            <div class="col-md-2">
                <div class="card"><div class="card-body text-center"><small>Total</small><h4 id="cntTotal"><?php echo $totalCount; ?></h4></div></div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h6>Add sub_domain URLs</h6>
                        <p class="text-muted small">Paste one URL per line, or insert into table <code>temp_company_deactive</code> (<code>url</code>, <code>status=0</code>).</p>
                        <textarea id="urlList" class="form-control" rows="8" placeholder="https://company1.example.com/&#10;https://company2.example.com/"></textarea>
                        <button type="button" id="btnAddUrls" class="btn btn-secondary btn-sm mt-2"><i class="fa fa-plus"></i> Add URLs</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <h6>Run</h6>
                        <p class="mb-2"><strong>Current:</strong> <span id="currentUrl">-</span></p>
                        <button type="button" id="btnStart" class="btn btn-danger btn-sm"><i class="fa fa-play"></i> Start Deactive</button>
                        <button type="button" id="btnPause" class="btn btn-warning btn-sm" disabled><i class="fa fa-pause"></i> Pause</button>
                        <button type="button" id="btnResetFailed" class="btn btn-outline-secondary btn-sm">Reset failed to pending</button>
                        <div id="runMessage" class="mt-2"></div>
                        <div class="table-responsive mt-3" style="max-height: 280px; overflow-y: auto;">
                            <table class="table table-sm table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>URL</th>
                                        <th>Result</th>
                                    </tr>
                                </thead>
                                <tbody id="liveLog"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h6>Queue (latest 200)</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>URL</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>HTTP</th>
                                        <th>Message</th>
                                        <th>Processed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $listQ = $d->select("temp_company_deactive", "1", "ORDER BY id DESC LIMIT 200");
                                    if ($listQ && mysqli_num_rows($listQ) > 0) {
                                        while ($item = mysqli_fetch_assoc($listQ)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo htmlspecialchars($item['url']); ?></td>
                                                <td><?php echo htmlspecialchars($item['society_name']); ?></td>
                                                <td><?php echo tempDeactiveStatusLabel($item['status']); ?></td>
                                                <td><?php echo htmlspecialchars($item['http_code']); ?></td>
                                                <td><?php echo htmlspecialchars($item['message']); ?></td>
                                                <td><?php echo htmlspecialchars($item['processed_at']); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="7" class="text-center">No rows yet. Add URLs first.</td></tr>';
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

<script src="assets/js/jquery.min.js"></script>
<script>
(function() {
    var running = false;

    function updateCounts(c) {
        if (!c) return;
        $('#cntPending').text(c.pending || 0);
        $('#cntDeleted').text(c.deleted || 0);
        $('#cntNot404').text(c.not_404 || 0);
        $('#cntNotFound').text(c.not_found || 0);
        $('#cntError').text(c.error || 0);
        $('#cntTotal').text(c.total || 0);
    }

    function logRow(result) {
        if (!result) return;
        var cls = 'table-secondary';
        if (result.status === 1) cls = 'table-success';
        else if (result.status === 2) cls = 'table-danger';
        else if (result.status === 4) cls = 'table-warning';
        var name = result.society_name ? result.society_name : '-';
        var html = '<tr class="' + cls + '"><td>' + $('<div>').text(name).html() +
            '</td><td>' + $('<div>').text(result.url || '').html() +
            '</td><td>' + $('<div>').text((result.status_text || '') + ' - ' + (result.message || '')).html() +
            '</td></tr>';
        $('#liveLog').prepend(html);
    }

    function processNext() {
        if (!running) return;
        $('#currentUrl').text('Checking...');
        $.ajax({
            url: 'ajax/bulkDeactiveCompany.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'process_one' },
            success: function(res) {
                if (!res || !res.success) {
                    $('#runMessage').html('<span class="text-danger">' + (res && res.message ? res.message : 'Request failed') + '</span>');
                    stopRun();
                    return;
                }
                updateCounts(res.counts);
                if (res.result) {
                    $('#currentUrl').text(res.result.url || '-');
                    logRow(res.result);
                }
                if (res.done) {
                    $('#currentUrl').text('Finished');
                    $('#runMessage').html('<span class="text-success">All pending companies processed.</span>');
                    stopRun();
                    return;
                }
                setTimeout(processNext, 400);
            },
            error: function() {
                $('#runMessage').html('<span class="text-danger">AJAX error. Paused.</span>');
                stopRun();
            }
        });
    }

    function stopRun() {
        running = false;
        $('#btnStart').prop('disabled', false);
        $('#btnPause').prop('disabled', true);
    }

    $('#btnStart').on('click', function() {
        running = true;
        $('#btnStart').prop('disabled', true);
        $('#btnPause').prop('disabled', false);
        $('#runMessage').html('<span class="text-info">Running...</span>');
        processNext();
    });

    $('#btnPause').on('click', function() {
        $('#runMessage').html('<span class="text-warning">Paused. You can Start again to continue.</span>');
        stopRun();
    });

    $('#btnAddUrls').on('click', function() {
        var urls = $('#urlList').val();
        if (!urls.trim()) {
            alert('Paste at least one sub_domain URL');
            return;
        }
        $.ajax({
            url: 'ajax/bulkDeactiveCompany.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'add_urls', urls: urls },
            success: function(res) {
                if (res && res.success) {
                    updateCounts(res.counts);
                    $('#urlList').val('');
                    $('#runMessage').html('<span class="text-success">' + res.message + '</span>');
                    setTimeout(function() { location.reload(); }, 800);
                } else {
                    alert(res && res.message ? res.message : 'Could not add URLs');
                }
            }
        });
    });

    $('#btnResetFailed').on('click', function() {
        if (!confirm('Set Not 404 / Not found / Error rows back to pending?')) return;
        $.ajax({
            url: 'ajax/bulkDeactiveCompany.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'reset_failed' },
            success: function(res) {
                if (res && res.success) {
                    updateCounts(res.counts);
                    location.reload();
                }
            }
        });
    });
})();
</script>
