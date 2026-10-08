<?php

include_once 'common/object.php';
session_start();

extract(array_map("test_input", $_POST));

if (!isset($society_id) || $society_id === '') {
  echo '<tr><td colspan="4" class="text-center text-muted">Select a company to view sliders</td></tr>';
  exit;
}

$society_id = intval($society_id);
$sliders = [];

// 1. Default sliders (slider_type=1, shown to all companies)
$defaultQ = $d->select("app_slider_master", "slider_type=1 AND slider_status=0", "ORDER BY app_slider_id ASC");
while ($row = mysqli_fetch_assoc($defaultQ)) {
  $row['source'] = 'Default';
  $sliders[] = $row;
}

// 2. Company-assigned sliders (from app_common_slider_master)
$companyQ = $d->selectRow(
  "asm.app_slider_id, asm.slider_image_name, asm.slider_type",
  "app_slider_master asm, app_common_slider_master acsm",
  "asm.app_slider_id = acsm.app_slider_id AND acsm.society_id = '$society_id' AND acsm.status = 0",
  "ORDER BY asm.app_slider_id ASC"
);
while ($row = mysqli_fetch_assoc($companyQ)) {
  $row['source'] = 'Company';
  $sliders[] = $row;
}

$imgBase = '../../img/sliders/';
$i = 1;
if (count($sliders) > 0) {
  foreach ($sliders as $s) {
    $imgSrc = $imgBase . ($s['slider_image_name'] ?? '');
    $badgeClass = $s['source'] === 'Default' ? 'badge-info' : 'badge-primary';
    ?>
    <tr>
      <td><?= $i++ ?></td>
      <td><a href="<?= $imgSrc ?>" data-fancybox="company-sliders"><img src="<?= $imgSrc ?>" width="80" height="45" class="img-thumbnail" onerror="this.src='../../img/dummy-image.jpg'"></a></td>
      <td><?= htmlspecialchars($s['slider_image_name'] ?? '') ?></td>
      <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($s['source']) ?></span></td>
    </tr>
    <?php
  }
} else {
  echo '<tr><td colspan="4" class="text-center text-muted">No sliders found for this company</td></tr>';
}
