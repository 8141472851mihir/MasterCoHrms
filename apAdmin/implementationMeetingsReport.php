<?php
include_once 'common/object.php';
$countryId          = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId']) : 0;
$stateId            = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
$cityId             = isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : 0;
$implementationName = isset($_GET['implementation_name']) ? trim($_GET['implementation_name']) : '';
$riseFilter         = isset($_GET['rise_filter']) ? $_GET['rise_filter'] : '';

$conn = $d->dbCon();

$where = ["sm.created_on_society_server = 1"];

if ($countryId > 0) {
  $where[] = "sm.country_id = '$countryId'";
}
if ($stateId > 0) {
  $where[] = "sm.state_id = '$stateId'";
}
if ($cityId > 0) {
  $where[] = "sm.city_id = '$cityId'";
}
if ($implementationName !== '' && $implementationName !== 'All') {
  $safeImpl = mysqli_real_escape_string($conn, $implementationName);
  $where[] = "sm.implementation_name LIKE '%$safeImpl%'";
}

// Apply Myco Rise filter (from_rise_event)
if ($riseFilter === '' || $riseFilter === 'yes') {
  // Default / "Yes" → only Myco Rise companies (treat NULL as 0)
  $where[] = "IFNULL(sm.from_rise_event,0) = 1";
} elseif ($riseFilter === 'no') {
  // "No" → companies from before Myco Rise
  $where[] = "(sm.from_rise_event IS NULL OR sm.from_rise_event = 0)";
} elseif ($riseFilter === 'all') {
  // "All" → no additional filter
}

$whereSql = implode(' AND ', $where);

$companyQuery = $d->selectRow(
  "sm.society_id,
   sm.society_name,
   sm.implementation_name,
   sm.created_date,
   sm.training_completion_date,
   sm.support_handover_date,
   c.name  AS country_name,
   s.name  AS state_name,
   ci.name AS city_name",
  "society_master sm
   INNER JOIN countries c ON sm.country_id = c.country_id
   LEFT JOIN states s ON sm.state_id = s.state_id
   LEFT JOIN cities ci ON sm.city_id = ci.city_id",
  $whereSql,
  "ORDER BY sm.society_id DESC"
);

$shortAppName = $d->short_app_name();

$companies = [];
while ($row = mysqli_fetch_assoc($companyQuery)) {
  $cid = (int)$row['society_id'];
  $companies[$cid] = $row;
}

$trainingSummary = [];
$topicMeetings = [];
$allTopics = [];
$setupSummary = [];
$feedbackFormDates = [];

if (!empty($companies)) {
  $companyIds = array_keys($companies);
  $companyIdsInt = array_map('intval', $companyIds);
  $companyIdsStr = implode(',', $companyIdsInt);

  // Per-company training feedback form submitted dates (latest submission per company)
  $feedbackQuery = $d->selectRow(
    "tcfm.society_id,
     MAX(tcfm.submitted_date) AS feedback_submitted_date",
    "training_completion_form_master tcfm",
    "tcfm.society_id IN ($companyIdsStr) AND tcfm.submitted_date IS NOT NULL AND tcfm.submitted_date != '0000-00-00 00:00:00'",
    "GROUP BY tcfm.society_id"
  );
  while ($row = mysqli_fetch_assoc($feedbackQuery)) {
    $cid = (int)$row['society_id'];
    $feedbackFormDates[$cid] = $row['feedback_submitted_date'];
  }

  // Per-company setup start and end dates
  // Setup start = MIN(data_receive_date, onboarding_date) from required setup modules
  // Setup end = MAX(onboarding_date or data_receive_date) when modules are completed (status 1 or 2)
  $setupQuery = $d->selectRow(
    "mtsm.company_id,
     MIN(CASE 
       WHEN NULLIF(mtsm.data_receive_date, '0000-00-00 00:00:00') IS NOT NULL
         AND NULLIF(mtsm.onboarding_date, '0000-00-00 00:00:00') IS NULL
       THEN NULLIF(mtsm.data_receive_date, '0000-00-00 00:00:00')
       WHEN NULLIF(mtsm.onboarding_date, '0000-00-00 00:00:00') IS NOT NULL
         AND NULLIF(mtsm.data_receive_date, '0000-00-00 00:00:00') IS NULL
       THEN NULLIF(mtsm.onboarding_date, '0000-00-00 00:00:00')
       WHEN NULLIF(mtsm.data_receive_date, '0000-00-00 00:00:00') IS NOT NULL
         AND NULLIF(mtsm.onboarding_date, '0000-00-00 00:00:00') IS NOT NULL
       THEN LEAST(NULLIF(mtsm.data_receive_date, '0000-00-00 00:00:00'), NULLIF(mtsm.onboarding_date, '0000-00-00 00:00:00'))
       ELSE NULL
     END) AS setup_start_date,
     MAX(CASE 
       WHEN mtsm.onboarding_status IN (1,2) AND NULLIF(mtsm.onboarding_date, '0000-00-00 00:00:00') IS NOT NULL
       THEN NULLIF(mtsm.onboarding_date, '0000-00-00 00:00:00')
       WHEN mtsm.data_receive_status IN (1,2) AND NULLIF(mtsm.data_receive_date, '0000-00-00 00:00:00') IS NOT NULL
       THEN NULLIF(mtsm.data_receive_date, '0000-00-00 00:00:00')
       ELSE NULL
     END) AS setup_end_date",
    "module_training_status_master mtsm
     INNER JOIN training_module_master tmm ON tmm.training_module_id = mtsm.module_id
     INNER JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
    "mtsm.company_id IN ($companyIdsStr) 
     AND tmm.module_type = 0 
     AND IFNULL(tmm.training_module_status,0) = 0 
     AND tmpm.is_required = 1",
    "GROUP BY mtsm.company_id"
  );
  while ($row = mysqli_fetch_assoc($setupQuery)) {
    $cid = (int)$row['company_id'];
    $setupSummary[$cid] = [
      'setup_start_date' => $row['setup_start_date'],
      'setup_end_date' => $row['setup_end_date']
    ];
  }

  // Per-company training start date and total distinct meeting dates
  $summaryQuery = $d->selectRow(
    "company_id,
     MIN(training_date) AS training_start_date,
     COUNT(DISTINCT DATE(training_date)) AS total_meetings",
    "batch_training_status_master",
    "company_id IN ($companyIdsStr) AND training_status IN (1,2,3)",
    "GROUP BY company_id"
  );
  while ($row = mysqli_fetch_assoc($summaryQuery)) {
    $trainingSummary[(int)$row['company_id']] = $row;
  }

  // Per-company, per-topic meeting counts (distinct training dates)
  $topicQuery = $d->selectRow(
    "btsm.company_id,
     COALESCE(tmt.topic_id, 0) AS topic_id,
     COALESCE(tmt.topic_name, 'Other') AS topic_name,
     COUNT(DISTINCT DATE(btsm.training_date)) AS topic_meetings",
    "batch_training_status_master btsm
     INNER JOIN training_module_master tmm
       ON tmm.training_module_id = btsm.module_id
       AND tmm.module_type = 1
       AND tmm.training_module_status = 0
     LEFT JOIN training_module_topics tmt
       ON tmt.topic_id = tmm.topic_id
       AND tmt.topic_type = '0'",
    "btsm.company_id IN ($companyIdsStr) AND btsm.training_status IN (1,2,3)",
    "GROUP BY btsm.company_id, topic_id, topic_name"
  );
  while ($row = mysqli_fetch_assoc($topicQuery)) {
    $cid = (int)$row['company_id'];
    $tName = $row['topic_name'];
    if (!isset($topicMeetings[$cid])) {
      $topicMeetings[$cid] = [];
    }
    $topicMeetings[$cid][$tName] = (int)$row['topic_meetings'];
    if (!isset($allTopics[$tName])) {
      $allTopics[$tName] = true;
    }
  }
  // Normalize topic list as ordered array
  if (!empty($allTopics)) {
    $topicNames = array_keys($allTopics);
    sort($topicNames, SORT_NATURAL | SORT_FLAG_CASE);
    $allTopics = $topicNames;
  }
}
?>

<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Implementation Training Meetings Report</h4>
      </div>
    </div>

    <div class="row p-2 pb-2">
      <div class="col-lg-12">
        <form action="" method="get" accept-charset="utf-8">
          <div class="form-group row">
            <label for="country_id" class="col-sm-1 mb-2 col-form-label">Country <span class="required">*</span></label>
            <div class="col-sm-2 mb-2">
              <select required id="country_id" class="form-control single-select" name="countryId" onchange="this.form.submit()">
                <option value="">-- Select --</option>
                <?php
                $qc = $d->select("countries", "flag=1", "ORDER BY name ASC");
                while ($cData = mysqli_fetch_assoc($qc)) {
                  $selected = ($cData['country_id'] == $countryId) ? "selected" : "";
                  echo "<option value='{$cData['country_id']}' $selected>" . htmlspecialchars($cData['name']) . "</option>";
                }
                ?>
              </select>
            </div>

            <label for="state_id" class="col-sm-1 mb-2 col-form-label">State</label>
            <div class="col-sm-2 mb-2">
              <?php if ($countryId > 0) { ?>
                <select class="form-control single-select" id="state_id" name="sId" onchange="this.form.submit()">
                  <option value="">All</option>
                  <?php
                  $qs = $d->select("states", "country_id=$countryId", "ORDER BY name ASC");
                  while ($sData = mysqli_fetch_assoc($qs)) {
                    $selected = ($stateId > 0 && $sData['state_id'] == $stateId) ? "selected" : "";
                    echo "<option value='{$sData['state_id']}' $selected>" . htmlspecialchars($sData['name']) . "</option>";
                  }
                  ?>
                </select>
              <?php } else { ?>
                <select class="form-control single-select" id="state_id" name="sId">
                  <option value="">-- Select Country First --</option>
                </select>
              <?php } ?>
            </div>

            <label for="city_id" class="col-sm-1 mb-2 col-form-label">City</label>
            <div class="col-sm-2 mb-2">
              <?php if ($stateId > 0) { ?>
                <select class="form-control single-select" id="city_id" name="cId" onchange="this.form.submit()">
                  <option value="">All</option>
                  <?php
                  $qcity = $d->select("cities", "state_id=$stateId", "ORDER BY name ASC");
                  while ($cityData = mysqli_fetch_assoc($qcity)) {
                    $selected = ($cityId > 0 && $cityData['city_id'] == $cityId) ? "selected" : "";
                    echo "<option value='{$cityData['city_id']}' $selected>" . htmlspecialchars($cityData['name']) . "</option>";
                  }
                  ?>
                </select>
              <?php } else { ?>
                <select class="form-control single-select" id="city_id" name="cId">
                  <option value="">-- Select State First --</option>
                </select>
              <?php } ?>
            </div>

            <label for="implementation_name" class="col-sm-1 mb-2 col-form-label">Implementation</label>
            <div class="col-sm-2 mb-2">
              <select id="implementation_name" name="implementation_name" class="form-control single-select" onchange="this.form.submit()">
                <option value="All">All</option>
                <?php
                $implResult = $d->select("bms_admin_master", "", "ORDER BY admin_name ASC");
                while ($impl = mysqli_fetch_assoc($implResult)) {
                  $name = $impl['admin_name'];
                  $selected = ($implementationName !== '' && $implementationName === $name) ? 'selected' : '';
                  echo "<option value='" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($name) . "</option>";
                }
                ?>
              </select>
            </div>
            
            <label for="rise_filter" class="col-sm-1 mb-2 col-form-label">Myco Rise</label>
            <div class="col-sm-2 mb-2">
              <select id="rise_filter" name="rise_filter" class="form-control single-select" onchange="this.form.submit()">
                <option value="yes" <?php echo ($riseFilter === '' || $riseFilter === 'yes') ? 'selected' : ''; ?>>Yes</option>
                <option value="no" <?php echo ($riseFilter === 'no') ? 'selected' : ''; ?>>No</option>
                <option value="all" <?php echo ($riseFilter === 'all') ? 'selected' : ''; ?>>All</option>
              </select>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="row pt-2 pb-2">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="implementationMeetingsReport" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Company ID</th>
                    <th>Company Name</th>
                    <th>Country</th>
                    <th>State</th>
                    <th>City</th>
                    <th>Implementation Name</th>
                    <th>Company Creation Date</th>
                    <th>Setup Start Date</th>
                    <th>Setup End Date</th>
                    <th>Days of Setup Completion</th>
                    <th>Training Start Date</th>
                    <th>Training Completion Date</th>
                    <th>Days Between Start &amp; Completion</th>
                    <th>Total Meetings (All Topics)</th>
                    <?php if (!empty($allTopics)) { ?>
                      <?php foreach ($allTopics as $topicHeader) { ?>
                        <th><?php echo htmlspecialchars($topicHeader); ?> Meetings</th>
                      <?php } ?>
                    <?php } ?>
                    <th>Training Feedback Form Submitted Date</th>
                    <th>Handover Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (!empty($companies)) {
                    $i = 1;
                    foreach ($companies as $cid => $row) {
                      $companyIdDisplay = $shortAppName . '_' . $cid;

                      $createdDateFormatted = '';
                      if (!empty($row['created_date']) && $row['created_date'] !== '0000-00-00 00:00:00') {
                        $createdDateFormatted = date('d M Y, h:i A', strtotime($row['created_date']));
                      }

                      // Setup dates
                      $setupStartRaw = isset($setupSummary[$cid]['setup_start_date']) ? $setupSummary[$cid]['setup_start_date'] : null;
                      $setupStartFormatted = '';
                      if (!empty($setupStartRaw) && $setupStartRaw !== '0000-00-00 00:00:00') {
                        $setupStartFormatted = date('d M Y, h:i A', strtotime($setupStartRaw));
                      }

                      $setupEndRaw = isset($setupSummary[$cid]['setup_end_date']) ? $setupSummary[$cid]['setup_end_date'] : null;
                      $setupEndFormatted = '';
                      if (!empty($setupEndRaw) && $setupEndRaw !== '0000-00-00 00:00:00') {
                        $setupEndFormatted = date('d M Y, h:i A', strtotime($setupEndRaw));
                      }

                      $setupDaysBetween = '';
                      if (!empty($setupStartRaw) && !empty($setupEndRaw) && $setupStartRaw !== '0000-00-00 00:00:00' && $setupEndRaw !== '0000-00-00 00:00:00') {
                        try {
                          $setupStartDateObj = new DateTime($setupStartRaw);
                          $setupEndDateObj   = new DateTime($setupEndRaw);
                          $setupDiff = $setupEndDateObj->diff($setupStartDateObj);
                          // Inclusive day count: same-day start/end should be counted as 1 day
                          $setupDaysBetween = $setupDiff->days + 1;
                        } catch (Exception $e) {
                          $setupDaysBetween = '';
                        }
                      }

                      $trainingStartRaw = isset($trainingSummary[$cid]['training_start_date']) ? $trainingSummary[$cid]['training_start_date'] : null;
                      $trainingStartFormatted = '';
                      if (!empty($trainingStartRaw) && $trainingStartRaw !== '0000-00-00 00:00:00') {
                        $trainingStartFormatted = date('d M Y, h:i A', strtotime($trainingStartRaw));
                      }

                      $trainingEndRaw = isset($row['training_completion_date']) ? $row['training_completion_date'] : null;
                      $trainingEndFormatted = '';
                      if (!empty($trainingEndRaw) && $trainingEndRaw !== '0000-00-00 00:00:00') {
                        $trainingEndFormatted = date('d M Y, h:i A', strtotime($trainingEndRaw));
                      }

                      $daysBetween = '';
                      if (!empty($trainingStartRaw) && !empty($trainingEndRaw) && $trainingStartRaw !== '0000-00-00 00:00:00' && $trainingEndRaw !== '0000-00-00 00:00:00') {
                        try {
                          $startDateObj = new DateTime($trainingStartRaw);
                          $endDateObj   = new DateTime($trainingEndRaw);
                          $diff = $endDateObj->diff($startDateObj);
                          // Inclusive day count: same-day start/end should be counted as 1 day
                          $daysBetween = $diff->days + 1;
                        } catch (Exception $e) {
                          $daysBetween = '';
                        }
                      }

                      $totalMeetings = isset($trainingSummary[$cid]['total_meetings']) ? (int)$trainingSummary[$cid]['total_meetings'] : 0;

                      // Training feedback form submitted date
                      $feedbackSubmittedRaw = isset($feedbackFormDates[$cid]) ? $feedbackFormDates[$cid] : null;
                      $feedbackSubmittedFormatted = '';
                      if (!empty($feedbackSubmittedRaw) && $feedbackSubmittedRaw !== '0000-00-00 00:00:00') {
                        $feedbackSubmittedFormatted = date('d M Y, h:i A', strtotime($feedbackSubmittedRaw));
                      }

                      // Handover date
                      $handoverDateRaw = isset($row['support_handover_date']) ? $row['support_handover_date'] : null;
                      $handoverDateFormatted = '';
                      if (!empty($handoverDateRaw) && $handoverDateRaw !== '0000-00-00 00:00:00') {
                        $handoverDateFormatted = date('d M Y, h:i A', strtotime($handoverDateRaw));
                      }
                      ?>
                      <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($companyIdDisplay); ?></td>
                        <td><?php echo htmlspecialchars($row['society_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['country_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['state_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['city_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['implementation_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($createdDateFormatted); ?></td>
                        <td><?php echo htmlspecialchars($setupStartFormatted); ?></td>
                        <td><?php echo htmlspecialchars($setupEndFormatted); ?></td>
                        <td><?php echo ($setupDaysBetween !== '' ? (int)$setupDaysBetween : ''); ?></td>
                        <td><?php echo htmlspecialchars($trainingStartFormatted); ?></td>
                        <td><?php echo htmlspecialchars($trainingEndFormatted); ?></td>
                        <td><?php echo ($daysBetween !== '' ? (int)$daysBetween : ''); ?></td>
                        <td><?php echo (int)$totalMeetings; ?></td>
                        <?php if (!empty($allTopics)) { ?>
                          <?php foreach ($allTopics as $topicHeader) {
                            $count = 0;
                            if (isset($topicMeetings[$cid][$topicHeader])) {
                              $count = (int)$topicMeetings[$cid][$topicHeader];
                            }
                            ?>
                            <td><?php echo $count; ?></td>
                          <?php } ?>
                        <?php } ?>
                        <td><?php echo htmlspecialchars($feedbackSubmittedFormatted); ?></td>
                        <td><?php echo htmlspecialchars($handoverDateFormatted); ?></td>
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
<script src="assets/js/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    if ($('#implementationMeetingsReport').length && $('#implementationMeetingsReport tbody tr').length > 0) {
      $('#implementationMeetingsReport').DataTable({
        pageLength: 25,
        lengthMenu: [
          [25, 50, 100, 200, -1],
          [25, 50, 100, 200, "All"]
        ],
        order: [[13, "desc"]], // Sort by days between start & completion
        stateSave: true,
        processing: false,
        dom: 'Bfrtip',
        buttons: [
          {
            extend: 'copy',
            exportOptions: {
              columns: ':visible',
              format: {
                body: function(data, row, column, node) {
                  var exportData = $(node).attr('data-export');
                  if (typeof exportData !== 'undefined' && exportData !== false) {
                    return exportData;
                  }
                  return $(data).text ? $(data).text() : data;
                }
              }
            }
          },
          {
            extend: 'excel',
            exportOptions: {
              columns: ':visible',
              format: {
                body: function(data, row, column, node) {
                  var exportData = $(node).attr('data-export');
                  if (typeof exportData !== 'undefined' && exportData !== false) {
                    return exportData;
                  }
                  return $(data).text ? $(data).text() : data;
                }
              }
            }
          },
          'pdf',
          'csv',
          'colvis'
        ]
      });
    }
  });
</script>

