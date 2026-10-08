<?php
include_once 'lib.php';

$logFile = dirname(__DIR__) . '/img/autoCreateSocietySubdomainCronLogs.log';
$log = function ($msg) use ($logFile) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $msg . PHP_EOL, FILE_APPEND | LOCK_EX);
};

$log('Cron Start');
$ongoing_patch=$d->count_data_direct("festival_id","festival_master","is_festival='1' AND ongoing_patch=1");
if($ongoing_patch>0){
    $log('Ongoing patch found, skipping cron');
    exit;
}
// Get first pending company (created_on_society_server=0, oldest first - order by society_id ASC)
$q = $d->selectRow('society_id', 'society_master', 'created_on_society_server=0', 'ORDER BY society_id ASC LIMIT 1');

if (mysqli_num_rows($q) == 0) {
    $log('No pending companies found');
    exit;
}

$row = mysqli_fetch_array($q);
$society_id = $row['society_id'];
$log("Processing society_id: $society_id (simulating createSoceitySubdomain button)");

// Simulate POST data for createSoceitySubdomain (same as the Create button on pendingCompanies page)
$_POST['society_id'] = $society_id;
$_POST['createSoceitySubdomain'] = 'createSoceitySubdomain';

// Use cron bootstrap (no session/login required) instead of objectController
define('CRON_CREATE_SOCIETY_MODE', true);

// Include and execute the controller - it handles subdomain creation and will redirect/exit on completion
include dirname(__DIR__) . '/apAdmin/controller/createSocietyAutoController.php';

$log('Cron End (controller may have redirected/exited)');
