<?php
// error_reporting(0);
// session_start();
include_once 'common/object.php';

$serverIds = [];
if (!empty($_GET['serverIds'])) {
  $ids = $d->sanitizeReportFilterIds($_GET['serverIds']);
  $serverIds = $ids !== '' ? array_map('intval', explode(',', $ids)) : [];
} elseif (!empty($_GET['sId'])) {
  $serverIds = $d->sanitizeActionIds((array) $_GET['sId']);
}

if (empty($serverIds)) {
  echo '<option value="">-- Select Domain --</option>';
  exit;
}

$serverIdsStr = implode("','", $serverIds);
$domain_data = $d->select("domain_master", "server_id IN ('$serverIdsStr')");
while ($dData = mysqli_fetch_array($domain_data)) {
  $domain_id = (int) $dData['domain_id'];
  $domain_name = htmlspecialchars($dData['domain_name']);
  echo "<option value=\"{$domain_id}\">{$domain_name}</option>";
}
