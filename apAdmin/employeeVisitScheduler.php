<?php
if (!function_exists('implReportCountWorkingDays')) {
  function implReportCountWorkingDays($fromDate, $toDate)
  {
    $start = DateTime::createFromFormat('Y-m-d', $fromDate);
    $end = DateTime::createFromFormat('Y-m-d', $toDate);
    if (!$start || !$end) {
      return 0;
    }
    if ($end < $start) {
      return 0;
    }
    $end = clone $end;
    $end->modify('+1 day');
    $count = 0;
    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
    foreach ($period as $date) {
      if ((int)$date->format('w') !== 0) {
        $count++;
      }
    }
    return $count;
  }
}
if (!function_exists('implReportParseDateRange')) {
  function implReportParseDateRange($range, $defaultFrom, $defaultTo)
  {
    $fromDate = $defaultFrom;
    $toDate = $defaultTo;
    $range = trim((string)$range);
    if ($range !== '') {
      $rangeParts = explode(' - ', $range);
      if (count($rangeParts) === 2) {
        $fromTs = strtotime($rangeParts[0]);
        $toTs = strtotime($rangeParts[1]);
        if ($fromTs && $toTs) {
          $fromDate = date('Y-m-d', $fromTs);
          $toDate = date('Y-m-d', $toTs);
        }
      }
    } else {
      $range = date('F d, Y', strtotime($defaultFrom)) . ' - ' . date('F d, Y', strtotime($defaultTo));
    }
    return array($fromDate, $toDate, $range);
  }
}
if (!function_exists('implReportVisitDate')) {
  function implReportVisitDate($visit)
  {
    foreach (array('scheduled_date', 'schedule_date', 'visit_date', 'visit_start_date', 'start_date', 'date') as $field) {
      if (empty($visit[$field])) {
        continue;
      }
      $timestamp = strtotime($visit[$field]);
      if ($timestamp !== false) {
        return date('Y-m-d', $timestamp);
      }
    }
    return '';
  }
}

$visitSchedulerFilterKeys = array('scheduled_date_range', 'visited_by', 'slot', 'report_view', 'visit_purpose');
$visitSchedulerIsFilterRequest = false;
foreach ($visitSchedulerFilterKeys as $visitSchedulerFilterKey) {
  if (isset($_GET[$visitSchedulerFilterKey])) {
    $visitSchedulerIsFilterRequest = true;
    break;
  }
}
if (!$visitSchedulerIsFilterRequest) {
  $visitSchedulerLogFile = dirname(__DIR__) . '/img/employeeVisitSchedulerAccess.txt';
  $visitSchedulerAccessedBy = trim((string)($admin_name ?? ''));
  if ($visitSchedulerAccessedBy === '') {
    $visitSchedulerAccessedBy = 'Unknown';
  }
  $visitSchedulerAdminId = (int)($bms_admin_id ?? 0);
  $visitSchedulerLog = fopen($visitSchedulerLogFile, 'c+');
  if ($visitSchedulerLog) {
    flock($visitSchedulerLog, LOCK_EX);
    $visitSchedulerExisting = stream_get_contents($visitSchedulerLog);
    $visitSchedulerRecentAccess = false;
    if (is_string($visitSchedulerExisting) && $visitSchedulerExisting !== '') {
      $visitSchedulerPattern = '/Accessed At: ([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2})\r?\nAccessed By: .*\(Admin ID: ' . $visitSchedulerAdminId . '\)/';
      if (preg_match_all($visitSchedulerPattern, $visitSchedulerExisting, $visitSchedulerMatches) && !empty($visitSchedulerMatches[1])) {
        $visitSchedulerLastAt = strtotime(end($visitSchedulerMatches[1]));
        $visitSchedulerRecentAccess = ($visitSchedulerLastAt !== false && (time() - $visitSchedulerLastAt) < 120);
      }
    }
    if (!$visitSchedulerRecentAccess) {
      $visitSchedulerAccessCount = (!is_string($visitSchedulerExisting) || trim($visitSchedulerExisting) === '')
        ? 1
        : (substr_count($visitSchedulerExisting, 'Access Count:') + 1);
      $visitSchedulerEntry = 'Access Count: ' . $visitSchedulerAccessCount . PHP_EOL
        . 'Accessed At: ' . date('Y-m-d H:i:s') . PHP_EOL
        . 'Accessed By: ' . $visitSchedulerAccessedBy . ' (Admin ID: ' . $visitSchedulerAdminId . ')' . PHP_EOL
        . '--------------------------------' . PHP_EOL;
      fseek($visitSchedulerLog, 0, SEEK_END);
      fwrite($visitSchedulerLog, $visitSchedulerEntry);
      fflush($visitSchedulerLog);
    }
    flock($visitSchedulerLog, LOCK_UN);
    fclose($visitSchedulerLog);
  }
}

$defaultFrom = date('Y-m-d', strtotime('-29 days'));
$defaultTo = date('Y-m-d');
$reportView = isset($_GET['report_view']) ? (string)$_GET['report_view'] : 'combined';
if (!in_array($reportView, array('combined', 'day_wise'), true)) {
  $reportView = 'combined';
}
$isDayWise = ($reportView === 'day_wise');
$scheduledDateRange = isset($_GET['scheduled_date_range']) ? trim((string)$_GET['scheduled_date_range']) : '';
list($fromDate, $toDate, $scheduledDateRange) = implReportParseDateRange($scheduledDateRange, $defaultFrom, $defaultTo);

$visitedByRaw = array();
if (isset($_GET['visited_by'])) {
  $visitedByRaw = is_array($_GET['visited_by']) ? $_GET['visited_by'] : array($_GET['visited_by']);
}
$selectedVisitedBy = array();
foreach ($visitedByRaw as $visitedByItem) {
  $visitedByItem = trim((string)$visitedByItem);
  if ($visitedByItem !== '' && strtolower($visitedByItem) !== 'all') {
    $selectedVisitedBy[] = $d->sanitizeActionIdAsInt($visitedByItem);
  }
}
$allVisitedBySelected = empty($selectedVisitedBy);

$slot = isset($_GET['slot']) ? (int)$_GET['slot'] : 0;
if (!in_array($slot, array(0, 1, 2, 3), true)) {
  $slot = 0;
}

$selectedVisitPurposes = array();
if (isset($_GET['visit_purpose'])) {
  $rawPurposes = $_GET['visit_purpose'];
  if (!is_array($rawPurposes)) {
    $rawPurposes = array($rawPurposes);
  }
  foreach ($rawPurposes as $purposeItem) {
    $purposeItem = trim((string)$purposeItem);
    if ($purposeItem !== '' && strtolower($purposeItem) !== 'all') {
      $selectedVisitPurposes[] = $purposeItem;
    }
  }
}
$allVisitPurposesSelected = empty($selectedVisitPurposes);

$boundAdmins = array();
$boundUserIds = array();
$adminNameByUserId = array();
$boundQ = $d->selectRow(
  "admin_id, admin_name, user_id",
  "bms_admin_master",
  "active_status=0 AND user_id IS NOT NULL AND user_id != 0 AND user_id != ''",
  "ORDER BY admin_name ASC"
);
if ($boundQ && mysqli_num_rows($boundQ) > 0) {
  while ($adminRow = mysqli_fetch_array($boundQ)) {
    $userId = (int)$adminRow['user_id'];
    if ($userId <= 0) {
      continue;
    }
    $boundAdmins[] = $adminRow;
    $boundUserIds[] = $userId;
    $adminNameByUserId[(string)$userId] = $adminRow['admin_name'];
  }
}

$selectedUserIds = $boundUserIds;
$selectedAdmins = $boundAdmins;
if (!$allVisitedBySelected) {
  $selectedUserIds = array();
  $selectedAdmins = array();
  foreach ($boundAdmins as $adminRow) {
    if (in_array((int)$adminRow['admin_id'], $selectedVisitedBy, true)) {
      $selectedUserIds[] = (int)$adminRow['user_id'];
      $selectedAdmins[] = $adminRow;
    }
  }
}

$visitPurposes = array();
$visits = array();
$apiError = '';

$purposeResponse = $d->callCompanyApiEnc($d->company_url(), 'getEmployeeVisitSchedulerController.php', array(
  'getVisitPurposes' => 'getVisitPurposes',
  'society_id' => $d->company_id(),
));
if (isset($purposeResponse['status']) && (string)$purposeResponse['status'] === '200' && isset($purposeResponse['visit_purposes']) && is_array($purposeResponse['visit_purposes'])) {
  $visitPurposes = $purposeResponse['visit_purposes'];
}

if (!empty($selectedUserIds)) {
  $visitResponse = $d->callCompanyApiEnc($d->company_url(), 'getEmployeeVisitSchedulerController.php', array(
    'getEmployeeVisits' => 'getEmployeeVisits',
    'society_id' => $d->company_id(),
    'from_date' => $fromDate,
    'to_date' => $toDate,
    'user_ids' => implode(',', $selectedUserIds),
    // Option 3 is a display mode for both individual slots; the API uses 0 for both.
    'slot' => ($slot === 3 ? 0 : $slot),
    'visit_purpose' => implode('||', $selectedVisitPurposes),
    'data_type' => 'datewise',
  ));

  if (!isset($visitResponse['status']) || (string)$visitResponse['status'] !== '200' || !isset($visitResponse['visits']) || !is_array($visitResponse['visits'])) {
    $apiError = isset($visitResponse['message']) ? $visitResponse['message'] : 'Failed to fetch visit data';
  } else {
    $visits = $visitResponse['visits'];
  }
}

$workingDays = implReportCountWorkingDays($fromDate, $toDate);
$slotLabels = array(
  0 => 'Full Day',
  1 => 'Slot 1 Timing (12 AM to 12:59 PM)',
  2 => 'Slot 2 Timing (1 PM to 11:59 PM)',
);

$purposeColumns = array();
if (!$allVisitPurposesSelected) {
  $purposeColumns = array_values(array_unique($selectedVisitPurposes));
} else {
  $seenPurposes = array();
  foreach ($visits as $visit) {
    $purposeName = trim((string)($visit['visit_purpose'] ?? ''));
    if ($purposeName !== '') {
      $seenPurposes[$purposeName] = true;
    }
  }
  foreach ($visitPurposes as $purpose) {
    $purposeName = isset($purpose['visit_purpose']) ? trim((string)$purpose['visit_purpose']) : '';
    if ($purposeName !== '') {
      $seenPurposes[$purposeName] = true;
    }
  }
  $purposeColumns = array_keys($seenPurposes);
}
$emptyPurposeStats = array('started' => 0, 'started_lt45' => 0);
$schedulerRows = array();

foreach ($visits as $visit) {
  $userId = isset($visit['user_id']) ? (string)$visit['user_id'] : '';
  $visitedByName = isset($adminNameByUserId[$userId]) ? $adminNameByUserId[$userId] : ($visit['visited_by'] ?? '');
  $purposeName = trim((string)($visit['visit_purpose'] ?? ''));
  $slotNo = (int)($visit['slot'] ?? 0);
  if ($slotNo !== 1 && $slotNo !== 2) {
    $slotNo = 1;
  }
  $isStarted = ((string)($visit['is_started'] ?? '0') === '1');
  $under45 = ((string)($visit['under_45'] ?? '0') === '1');
  $visitDate = implReportVisitDate($visit);
  $rowKey = $userId . '|' . $slotNo . ($isDayWise ? '|' . $visitDate : '');
  if (!isset($schedulerRows[$rowKey])) {
    $schedulerRows[$rowKey] = array(
      'user_id' => $userId,
      'visited_by' => $visitedByName,
      'slot' => $slotNo,
      'slot_label' => isset($slotLabels[$slotNo]) ? $slotLabels[$slotNo] : ('Slot ' . $slotNo),
      'visit_date' => $visitDate,
      'purposes' => array(),
      'scheduled_total' => 0,
      'started_total' => 0,
      'started_lt45_total' => 0,
    );
    foreach ($purposeColumns as $colPurpose) {
      $schedulerRows[$rowKey]['purposes'][$colPurpose] = $emptyPurposeStats;
    }
  }
  if ($purposeName !== '' && isset($schedulerRows[$rowKey]['purposes'][$purposeName])) {
    $schedulerRows[$rowKey]['scheduled_total']++;
    if ($isStarted) {
      $schedulerRows[$rowKey]['purposes'][$purposeName]['started']++;
      $schedulerRows[$rowKey]['started_total']++;
      if ($under45) {
        $schedulerRows[$rowKey]['purposes'][$purposeName]['started_lt45']++;
        $schedulerRows[$rowKey]['started_lt45_total']++;
      }
    }
  }
}

$combinedRows = array();
$slotsToShow = ($slot === 3) ? array(1, 2) : array($slot);
$datesToShow = array('');
if ($isDayWise) {
  $datesToShow = array();
  $periodEnd = new DateTime($toDate);
  $periodEnd->modify('+1 day');
  foreach (new DatePeriod(new DateTime($fromDate), new DateInterval('P1D'), $periodEnd) as $reportDate) {
    $datesToShow[] = $reportDate->format('Y-m-d');
  }
}
foreach ($selectedAdmins as $adminRow) {
  $uid = (string)(int)$adminRow['user_id'];
  $employeeName = $adminRow['admin_name'];
  foreach ($slotsToShow as $slotNo) {
    foreach ($datesToShow as $reportDate) {
    if ($slotNo === 0) {
      $slotRow = array(
        'slot_label' => $slotLabels[0],
        'purposes' => array(),
        'scheduled_total' => 0,
        'started_total' => 0,
        'started_lt45_total' => 0,
      );
      foreach ($purposeColumns as $purposeName) {
        $slotRow['purposes'][$purposeName] = $emptyPurposeStats;
      }
      foreach (array(1, 2) as $sourceSlot) {
        $sourceKey = $uid . '|' . $sourceSlot . ($isDayWise ? '|' . $reportDate : '');
        if (!isset($schedulerRows[$sourceKey])) {
          continue;
        }
        $sourceRow = $schedulerRows[$sourceKey];
        $slotRow['scheduled_total'] += (int)$sourceRow['scheduled_total'];
        $slotRow['started_total'] += (int)$sourceRow['started_total'];
        $slotRow['started_lt45_total'] += (int)$sourceRow['started_lt45_total'];
        foreach ($purposeColumns as $purposeName) {
          $slotRow['purposes'][$purposeName]['started'] += (int)$sourceRow['purposes'][$purposeName]['started'];
          $slotRow['purposes'][$purposeName]['started_lt45'] += (int)$sourceRow['purposes'][$purposeName]['started_lt45'];
        }
      }
    } else {
      $rowKey = $uid . '|' . $slotNo . ($isDayWise ? '|' . $reportDate : '');
      if (isset($schedulerRows[$rowKey])) {
        $slotRow = $schedulerRows[$rowKey];
      } else {
        $slotRow = array(
          'slot_label' => isset($slotLabels[$slotNo]) ? $slotLabels[$slotNo] : ('Slot ' . $slotNo),
          'purposes' => array(),
          'scheduled_total' => 0,
          'started_total' => 0,
          'started_lt45_total' => 0,
        );
        foreach ($purposeColumns as $purposeName) {
          $slotRow['purposes'][$purposeName] = $emptyPurposeStats;
        }
      }
    }
    $combinedRows[] = array(
      'visited_by' => $employeeName,
      'slot_label' => $slotRow['slot_label'],
      'visit_date' => $isDayWise ? $reportDate : '',
      'purposes' => $slotRow['purposes'],
      'scheduled_total' => (int)$slotRow['scheduled_total'],
      'grand_total_lt45' => (int)$slotRow['started_lt45_total'],
      'grand_total' => (int)$slotRow['started_total'],
    );
    }
  }
}

$purposeColspanTotal = count($purposeColumns) * 3;
$totalColCount = 3 + $purposeColspanTotal + 2 + ($isDayWise ? 1 : 0);
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
    <h4 class="page-title">Employee Visit Scheduler</h4>
      </div>
    </div>

    <form method="get" accept-charset="utf-8">
      <div class="row align-items-end">
        <div class="form-group col-md-2">
          <label>Scheduled Date</label>
          <input type="text" class="form-control jsDateRangePicker" name="scheduled_date_range" data-drp-show-ranges="true" data-drp-max-date="today" readonly value="<?php echo htmlspecialchars($scheduledDateRange); ?>">
        </div>
        <div class="form-group col-md-2">
          <label>Visited By / Employees</label>
          <select class="form-control multiple-select" id="visitedByFilter" name="visited_by[]" multiple="multiple">
            <option value="all" <?php echo $allVisitedBySelected ? 'selected' : ''; ?>>All</option>
            <?php foreach ($boundAdmins as $adminRow) { ?>
              <option value="<?php echo (int)$adminRow['admin_id']; ?>" <?php echo in_array((int)$adminRow['admin_id'], $selectedVisitedBy, true) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($adminRow['admin_name']); ?>
              </option>
            <?php } ?>
          </select>
        </div>
        <div class="form-group col-md-2">
          <label>Slot</label>
          <select class="form-control single-select" name="slot">
            <option value="0" <?php echo ($slot === 0) ? 'selected' : ''; ?>>Full Day</option>
            <option value="1" <?php echo ($slot === 1) ? 'selected' : ''; ?>>Slot 1 Timing (12 AM to 12:59 PM)</option>
            <option value="2" <?php echo ($slot === 2) ? 'selected' : ''; ?>>Slot 2 Timing (1 PM to 11:59 PM)</option>
            <option value="3" <?php echo ($slot === 3) ? 'selected' : ''; ?>>Slot 1 and Slot 2</option>
          </select>
        </div>
        <div class="form-group col-md-2">
          <label>Report View</label>
          <select class="form-control single-select" name="report_view">
            <option value="combined" <?php echo ($reportView === 'combined') ? 'selected' : ''; ?>>Combined</option>
            <option value="day_wise" <?php echo ($reportView === 'day_wise') ? 'selected' : ''; ?>>Day Wise</option>
          </select>
        </div>
        <div class="form-group col-md-3">
          <label>Visit Purpose</label>
          <select class="form-control multiple-select" id="visitPurposeFilter" name="visit_purpose[]" multiple="multiple">
            <option value="all" <?php echo $allVisitPurposesSelected ? 'selected' : ''; ?>>All</option>
            <?php foreach ($visitPurposes as $purpose) {
              $purposeName = isset($purpose['visit_purpose']) ? $purpose['visit_purpose'] : '';
              if ($purposeName === '') {
                continue;
              }
            ?>
              <option value="<?php echo htmlspecialchars($purposeName); ?>" <?php echo in_array($purposeName, $selectedVisitPurposes, true) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($purposeName); ?>
              </option>
            <?php } ?>
          </select>
        </div>
        <div class="form-group col-md-1">
          <button type="submit" class="btn btn-success btn-sm">Get Data</button>
        </div>
      </div>
    </form>

    <?php if ($apiError !== '') { ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($apiError); ?></div>
    <?php } ?>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <p class="mb-2">
              <strong>Date Range:</strong> <?php echo date('d-m-Y', strtotime($fromDate)); ?> to <?php echo date('d-m-Y', strtotime($toDate)); ?>
              &nbsp;|&nbsp; <strong>Working Days (Mon–Sat):</strong> <?php echo (int)$workingDays; ?>
            </p>
            <div class="table-responsive">
              <table id="employeeVisitReportTable" class="table table-bordered table-sm">
                <thead>
                  <?php if (empty($purposeColumns)) { ?>
                    <tr>
                      <th>Visited By</th>
                      <?php if ($isDayWise) { ?>
                        <th>Scheduled Date</th>
                      <?php } ?>
                      <th>Slot</th>
                      <th class="text-center">Scheduled Count</th>
                      <th class="text-center">Grand Total &lt;45 Min</th>
                      <th class="text-center">Visit Total Count</th>
                    </tr>
                  <?php } else { ?>
                    <tr>
                      <th rowspan="2">Visited By</th>
                      <?php if ($isDayWise) { ?>
                        <th rowspan="2">Scheduled Date</th>
                      <?php } ?>
                      <th rowspan="2">Slot</th>
                      <th rowspan="2" class="text-center">Scheduled Count</th>
                      <?php foreach ($purposeColumns as $purposeName) {
                        $colspan = 3;
                      ?>
                        <th colspan="<?php echo $colspan; ?>" class="text-center"><?php echo htmlspecialchars($purposeName); ?></th>
                      <?php } ?>
                      <th rowspan="2" class="text-center">Grand Total &lt;45 Min</th>
                      <th rowspan="2" class="text-center">Visit Total Count</th>
                    </tr>
                    <tr>
                      <?php foreach ($purposeColumns as $purposeName) { ?>
                        <th>&lt;45 Min</th>
                        <th>Visit Count (Start Date)</th>
                        <th>Average Count</th>
                      <?php } ?>
                    </tr>
                  <?php } ?>
                </thead>
                <tbody>
                  <?php if (empty($combinedRows)) { ?>
                    <tr>
                      <td colspan="<?php echo (int)$totalColCount; ?>" class="text-center">No scheduled visit data found.</td>
                    </tr>
                  <?php } else {
                    foreach ($combinedRows as $row) {
                  ?>
                    <tr>
                      <td><?php echo htmlspecialchars($row['visited_by']); ?></td>
                      <?php if ($isDayWise) { ?>
                        <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($row['visit_date']))); ?></td>
                      <?php } ?>
                      <td><?php echo htmlspecialchars($row['slot_label']); ?></td>
                      <td><?php echo (int)$row['scheduled_total']; ?></td>
                      <?php foreach ($purposeColumns as $purposeName) {
                        $stats = isset($row['purposes'][$purposeName]) ? $row['purposes'][$purposeName] : $emptyPurposeStats;
                        ?>
                          <td><?php echo (int)$stats['started_lt45']; ?></td>
                          <td><?php echo (int)$stats['started']; ?></td>
                          <td><?php echo $workingDays > 0 ? number_format($stats['started'] / $workingDays, 1) : '0.0'; ?></td>
                        <?php
                      } ?>
                      <td><?php echo (int)$row['grand_total_lt45']; ?></td>
                      <td><?php echo $row['grand_total']; ?></td>
                    </tr>
                  <?php }
                  } ?>
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
$(document).ready(function () {
  function bindAllMulti($el) {
    $el.on('change', function () {
      var values = $(this).val() || [];
      if (values.indexOf('all') !== -1 && values.length > 1) {
        var last = values[values.length - 1];
        if (last === 'all') {
          $(this).val(['all']).trigger('change.select2');
        } else {
          $(this).val(values.filter(function (v) { return v !== 'all'; })).trigger('change.select2');
        }
      }
      if (values.length === 0) {
        $(this).val(['all']).trigger('change.select2');
      }
    });
  }
  bindAllMulti($('#visitPurposeFilter'));
  bindAllMulti($('#visitedByFilter'));

  <?php if (!empty($combinedRows)) { ?>
  if ($.fn.DataTable && $('#employeeVisitReportTable').length) {
    $('#employeeVisitReportTable').DataTable({
      lengthChange: true,
      pageLength: 50,
      lengthMenu: [[25, 50, 100, 200, 500], [25, 50, 100, 200, 500]],
      buttons: ['copy', 'excel', 'pdf', 'csv', 'colvis'],
      dom: 'Blfrtip',
      ordering: true,
      scrollX: true,
      order: [[0, 'asc']]
    });
  }
  <?php } ?>
});
</script>