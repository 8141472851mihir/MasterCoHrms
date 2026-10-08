<?php
extract(array_map("test_input", $_REQUEST));
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$startDate = '';
$endDate = '';

if (isset($_GET['date_range_filter'])) {
  if (!empty($_GET['date_range_filter'])) {
    $range = explode(' - ', $_GET['date_range_filter']);
    if (count($range) === 2) {
      $startDate = date('Y-m-d', strtotime($range[0]));
      $endDate = date('Y-m-d', strtotime($range[1]));
    }
  }
} else {
  $startDate = date('Y-m-d', strtotime('-29 days'));
  $endDate = date('Y-m-d');
}
?>
<style>
  td {
    vertical-align: middle;
  }

  td strong {
    font-weight: 600;
  }

  .table-striped tbody tr:nth-of-type(odd) {
    background-color: #f8f9fa;
  }

  #reportTable td.desc-cell {
    white-space: normal !important;
    max-width: 360px;
    min-width: 200px;
    vertical-align: top;
  }

  #reportTable td.desc-cell .desc-text {
    display: block;
    max-width: 360px;
    max-height: 7.5em;
    overflow-x: hidden;
    overflow-y: auto;
    white-space: pre-wrap;
    word-wrap: break-word;
    overflow-wrap: anywhere;
    line-height: 1.4;
  }

  .chart-box {
    position: relative;
    width: 100%;
  }

  .chart-box.chart-doughnut {
    height: 320px;
  }

  .chart-box.chart-bar {
    height: 420px;
  }

  .chart-box.chart-module {
    height: 640px;
  }

  .chart-box.chart-line {
    height: 380px;
  }

  .chart-box canvas {
    width: 100% !important;
    height: 100% !important;
  }

  .today-status-toggle {
    cursor: pointer;
    user-select: none;
  }

  .today-status-toggle .toggle-icon {
    transition: transform 0.2s ease;
  }

  .today-status-toggle[aria-expanded="true"] .toggle-icon {
    transform: rotate(180deg);
  }

  .avg-stat-box {
    border-radius: 6px;
    text-align: center;
    padding: 16px 8px;
    min-height: 110px;
  }

  .avg-stat-box h3 {
    margin: 6px 0 0;
    font-weight: 700;
  }

  .avg-stat-box small {
    display: block;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }

  .avg-stat-days { background: #eef4ff; color: #1a4089; }
  .avg-stat-tickets { background: #e8f8f5; color: #1e8449; }
  .avg-stat-forwarded { background: #f4ecf7; color: #6c3483; }
  .avg-stat-ticket-avg { background: #fff6e5; color: #b9770e; }
  .avg-stat-forwarded-avg { background: #e8f6f3; color: #0e6655; }
  .avg-stat-bug-avg { background: #fdecea; color: #c0392b; }
  .avg-stat-open { background: #fef9e7; color: #7d6608; }
  .avg-stat-mix { background: #fdebd0; color: #af601a; }
  .avg-stat-tat { background: #eaf2f8; color: #1a5276; }
  .avg-stat-dev-tat { background: #f5eef8; color: #5b2c6f; }
  .avg-stat-fwd-tat { background: #e8f8f5; color: #0e6655; }
  .avg-stat-close-tat { background: #fdebd0; color: #af601a; }
  .aging-fresh { background: #e8f8f5; color: #1e8449; }
  .aging-ok { background: #fef9e7; color: #b7950b; }
  .aging-warn { background: #fdebd0; color: #d35400; }
  .aging-late { background: #fdecea; color: #c0392b; }
  .mix-bug { background: #fdecea; color: #c0392b; }
  .mix-train { background: #eaf2f8; color: #1a5276; }
  .mix-not { background: #eaecee; color: #566573; }
  .mix-change { background: #fff6e5; color: #b9770e; }
  .mix-other { background: #f4ecf7; color: #6c3483; }
  .exec-section-title {
    font-size: 14px;
    font-weight: 700;
    margin: 8px 0 12px;
  }
  .chart-box.chart-mix {
    height: 280px;
  }
  .chart-box.chart-company {
    height: 360px;
  }
</style>

<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-lg-12">
        <h4 class="page-title">App Support Report</h4>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <?php
    $issueTypes = [
      0 => 'All',
      1 => 'Bug',
      2 => 'Configuration Issue',
      3 => 'Training Issue',
      4 => 'Issue Not Found',
      5 => 'Change Request by Client',
      6 => 'Device Specific Issue',
      7 => 'Data Delete Request',
      8 => 'Not an issue',
      9 => 'Internet Connectivity Issue'
    ];
    $selectedType = isset($_GET['filter_type']) ? $_GET['filter_type'] : 0;

    $moduleTypes = [
      'closed_module_my_visits' => ['id' => 1, 'label' => 'My Visits', 'emoji' => '📍'],
      'closed_module_sales_order' => ['id' => 2, 'label' => 'Sales & Order', 'emoji' => '🛒'],
      'closed_module_work_report' => ['id' => 3, 'label' => 'Work Report', 'emoji' => '📑'],
      'closed_module_leave' => ['id' => 4, 'label' => 'Leave', 'emoji' => '🌴'],
      'closed_module_payroll' => ['id' => 5, 'label' => 'Payroll', 'emoji' => '💰'],
      'closed_module_attendance' => ['id' => 6, 'label' => 'Attendance', 'emoji' => '⏱️'],
      'closed_module_tasks' => ['id' => 7, 'label' => 'Tasks', 'emoji' => '✅'],
      'closed_module_tax_exemption' => ['id' => 8, 'label' => 'Tax Exemption', 'emoji' => '🧾'],
      'closed_module_performance_matrix' => ['id' => 9, 'label' => 'Performance Matrix', 'emoji' => '📊'],
      'closed_module_crm' => ['id' => 10, 'label' => 'CRM', 'emoji' => '🤝'],
      'closed_module_tracking' => ['id' => 11, 'label' => 'Tracking', 'emoji' => '🛰️'],
      'closed_biometric_attendance' => ['id' => 12, 'label' => 'Biometric Attendance', 'emoji' => '🆔'],
      'closed_face_app' => ['id' => 13, 'label' => 'Face App', 'emoji' => '🧑‍💻'],
      'closed_loan' => ['id' => 14, 'label' => 'Loan', 'emoji' => '🏦'],
      'closed_expense' => ['id' => 15, 'label' => 'Expense', 'emoji' => '💸'],
      'closed_advance_expense' => ['id' => 16, 'label' => 'Advance Expense', 'emoji' => '📤'],
      'closed_advance_salary' => ['id' => 17, 'label' => 'Advance Salary', 'emoji' => '💵'],
      'closed_holiday' => ['id' => 18, 'label' => 'Holiday', 'emoji' => '✈️'],
      'closed_employee_document_letter' => ['id' => 19, 'label' => 'Employee Document & Letter', 'emoji' => '📂'],
      'closed_app_not_opening' => ['id' => 20, 'label' => 'App Not Opening', 'emoji' => '📵'],
      'closed_log_issue' => ['id' => 21, 'label' => 'Log Issue', 'emoji' => '📋'],
      'closed_management' => ['id' => 22, 'label' => 'Management', 'emoji' => '👔'],
      'closed_employee' => ['id' => 23, 'label' => 'Employee', 'emoji' => '👤'],
      'closed_server_down' => ['id' => 24, 'label' => 'Server Down', 'emoji' => '🔌'],
      'closed_server_timeout' => ['id' => 25, 'label' => 'Server Timeout', 'emoji' => '⏳'],
      'closed_third_party_integration' => ['id' => 26, 'label' => 'Third Party Integration', 'emoji' => '🔗'],
      'closed_chat' => ['id' => 27, 'label' => 'Chat', 'emoji' => '💬'],
      'closed_timeline' => ['id' => 28, 'label' => 'Timeline', 'emoji' => '🕒'],
      'closed_assets' => ['id' => 29, 'label' => 'Assets', 'emoji' => '🧰'],
      'closed_site_management' => ['id' => 30, 'label' => 'Site Management', 'emoji' => '🏗️'],
      'closed_penalty' => ['id' => 31, 'label' => 'Penalty', 'emoji' => '⚖️'],
      'closed_module_other' => ['id' => 0, 'label' => 'Other', 'emoji' => '📦']
    ];
    $moduleTypeLabels = [];
    foreach ($moduleTypes as $mod) {
      $moduleTypeLabels[(int)$mod['id']] = $mod['label'];
    }
    $platformFilterOptions = [
      0 => 'All',
      1 => 'Android',
      2 => 'iOS',
      3 => 'Web',
      5 => 'CRM',
      4 => 'Other',
    ];
    $selectedPlatform = isset($_GET['platform_filter']) ? $_GET['platform_filter'] : 0;
    $dateRangeValue = isset($_GET['date_range_filter'])
      ? htmlspecialchars($_GET['date_range_filter'])
      : date('F d, Y', strtotime('-29 days')) . ' - ' . date('F d, Y');
    ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="filterForm" method="get" accept-charset="utf-8" class="mb-3">
              <div class="row mx-0">
                <div class="col-md-3 col-sm-6 mb-2 pl-0 pr-2">
                  <select type="text" required="" id="" onchange="this.form.submit()" class="form-control single-select"
                    name="Status">
                    <option <?php if (isset($_GET['Status']) && $_GET['Status'] == 0) {
                              echo "selected";
                            } ?> value="0">All
                    </option>
                    <option <?php if (isset($_GET['Status']) && $_GET['Status'] == 1) {
                              echo "selected";
                            } ?> value="1">Pending
                    </option>
                    <option <?php if (isset($_GET['Status']) && $_GET['Status'] == 2) {
                              echo "selected";
                            } ?> value="2">Close by
                      Developer</option>
                    <option <?php if (isset($_GET['Status']) && $_GET['Status'] == 3) {
                              echo "selected";
                            } ?> value="3">Reject by
                      Developer</option>
                    <option <?php if (isset($_GET['Status']) && $_GET['Status'] == 4) {
                              echo "selected";
                            } ?> value="4">Closed
                    </option>
                    <option <?php if (isset($_GET['Status']) && $_GET['Status'] == 5) {
                              echo "selected";
                            } ?> value="5">Open
                    </option>
                  </select>
                </div>
                <div class="col-md-3 col-sm-6 mb-2 px-2">
                  <select required onchange="this.form.submit()" class="form-control single-select" name="filter_type">
                    <?php foreach ($issueTypes as $key => $label): ?>
                      <option value="<?= $key ?>" <?= ($selectedType == $key) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-3 col-sm-6 mb-2 px-2">
                  <select required onchange="this.form.submit()" class="form-control single-select" name="platform_filter" id="platform_filter">
                    <?php foreach ($platformFilterOptions as $key => $label): ?>
                      <option value="<?= $key ?>" <?= ((string)$selectedPlatform === (string)$key) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-3 col-sm-6 mb-2 pr-0 pl-2">
                  <input type="text" class="form-control auto-submit" id="date_range_filter" name="date_range_filter"
                    autocomplete="off" readonly value="<?= $dateRangeValue ?>">
                </div>
              </div>
            </form>
          </div>
        </div>
        <?php ob_start(); ?>
        <div class="card mt-3">
          <div class="card-body">
            <div class="tab-content">
              <div class="table-responsive">
                <table id="reportTable" class="table table-bordered">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Ticket Id</th>
                      <th>Company Name</th>
                      <th>Name</th>
                      <th>Mobile No</th>
                      <th>Platform</th>
                      <th>Status</th>
                      <th>Title</th>
                      <th>Description</th>
                      <th>Issue Type</th>
                      <th>Module Type</th>
                      <th>Created Date</th>
                      <th>Created By</th>
                      <th>Ticket TAT</th>
                      <th>Ticket TAT Min.</th>
                      <th>Created to Dev. Forward TAT</th>
                      <th>Created to Dev. Forward TAT Min.</th>
                      <th>Developer TAT</th>
                      <th>Developer TAT Min.</th>
                      <th>Dev Closed to Resolve TAT</th>
                      <th>Dev Closed to Resolve TAT Min.</th>
                      <th>Developer Forward Date</th>
                      <th>Developer Closed Date</th>
                      <th>Resolve Date</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th class="no-search-box"></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </tfoot>
                  <tbody>
                    <?php
                    $issueTypeCounts = array_fill_keys(array_keys($issueTypes), 0);
                    $openCount = 0;
                    $pendingCount = 0;
                    $rejectedCount = 0;
                    $withDeveloperCount = 0;
                    $solvedCount = 0;
                    $closedByDeveloperCount = 0;
                    $ticketRowCount = 0;
                    $platformCounts = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0);
                    $bugPlatformCounts = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0);
                    $bugModuleCounts = array();
                    $monthTicketCounts = array();
                    $monthBugCounts = array();
                    $dayTicketCounts = array();
                    $dayBugCounts = array();
                    $agingBuckets = array('0-2' => 0, '3-7' => 0, '8-15' => 0, '15+' => 0);
                    $devAgingBuckets = array('0-2' => 0, '3-7' => 0, '8-15' => 0, '15+' => 0);
                    $openTicketCount = 0;
                    $devAgingCount = 0;
                    $ticketTatSumMin = 0;
                    $ticketTatCount = 0;
                    $devTatSumMin = 0;
                    $devTatCount = 0;
                    $forwardTatSumMin = 0;
                    $forwardTatCount = 0;
                    $supportCloseTatSumMin = 0;
                    $supportCloseTatCount = 0;
                    $companyStats = array();
                    foreach ($moduleTypes as $mod) {
                      $bugModuleCounts[(int)$mod['id']] = 0;
                    }
                    $i = 1;
                    $where = "";
                    if (isset($_GET['Status']) && $_GET['Status'] == 0) {
                      $where = "";
                    } else if (isset($_GET['Status']) && $_GET['Status'] == 1) {
                      $where = "AND feedback_master.feedback_status='0'";
                    } else if (isset($_GET['Status']) && $_GET['Status'] == 2) {
                      $where = "AND feedback_master.feedback_status='5'";
                    } else if (isset($_GET['Status']) && $_GET['Status'] == 3) {
                      $where = "AND feedback_master.feedback_status='6'";
                    } else if (isset($_GET['Status']) && $_GET['Status'] == 4) {
                      $where = "AND feedback_master.feedback_status='2'";
                    } else if (isset($_GET['Status']) && $_GET['Status'] == 5) {
                      $where = "AND feedback_master.feedback_status!='2' AND feedback_master.with_developer=0";
                    }
                    if (isset($_GET['filter_type']) && $_GET['filter_type'] != 0) {
                      $where .= " AND issueType='$filter_type'";
                    }
                    $platform_filter = isset($_GET['platform_filter']) ? (int)$_GET['platform_filter'] : 0;
                    if ($platform_filter == 1) {
                      $where .= " AND feedback_master.platform='1'";
                    } else if ($platform_filter == 2) {
                      $where .= " AND feedback_master.platform='2'";
                    } else if ($platform_filter == 3) {
                      $where .= " AND feedback_master.platform='3'";
                    } else if ($platform_filter == 4) {
                      $where .= " AND feedback_master.platform='0'";
                    } else if ($platform_filter == 5) {
                      $where .= " AND feedback_master.platform='4'";
                    }
                    if (!empty($startDate) && !empty($endDate)) {
                      $where .= " AND STR_TO_DATE(feedback_master.feedback_date_time, '%d-%m-%Y %H:%i') BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
                    }

                    $q = $d->selectRow("bms_admin_master.*,society_master.*,feedback_master.*,bms_admin_master.platform as admin_platform", "feedback_master LEFT JOIN bms_admin_master ON feedback_master.created_by=bms_admin_master.admin_id,society_master", "society_master.society_id=feedback_master.society_id AND feedback_master.inquiry_type=0 $countryAppendQuerySociety $where", "ORDER BY feedback_id DESC");

                    while ($data = mysqli_fetch_array($q)) {
                      extract($data);
                      $ticketRowCount++;
                      if ($feedback_status == 6) {
                        $rejectedCount++;
                      } elseif ($feedback_status == 2) {
                        $solvedCount++;
                      } elseif ($feedback_status == 5) {
                        $closedByDeveloperCount++;
                      }

                      // Same rules as feedback.php tabs (allFeedbacks.php)
                      // With Support Team: status != 2 AND with_developer = 0
                      // With Developer: status != 2,7,8 AND with_developer = 1
                      $ticketStatus = (int)$feedback_status;
                      $withDevFlag = (int)$with_developer;
                      if ($ticketStatus === 0) {
                        $pendingCount++;
                      }
                      if ($ticketStatus !== 2 && $withDevFlag === 0) {
                        $openCount++;
                      }
                      if ($ticketStatus !== 2 && $ticketStatus !== 7 && $ticketStatus !== 8 && $withDevFlag === 1) {
                        $withDeveloperCount++;
                      }

                      if ($issueType !== '' && $issueType !== null && $issueType != 0 && isset($issueTypeCounts[$issueType])) {
                        $issueTypeCounts[$issueType]++;
                      }

                      $platKey = isset($platform) ? (int)$platform : 0;
                      if (!isset($platformCounts[$platKey])) {
                        $platKey = 0;
                      }
                      $platformCounts[$platKey]++;

                      if ((int)$issueType === 1) {
                        $bugPlatKey = isset($platform) ? (int)$platform : 0;
                        if (!isset($bugPlatformCounts[$bugPlatKey])) {
                          $bugPlatKey = 0;
                        }
                        $bugPlatformCounts[$bugPlatKey]++;

                        $modKey = isset($module_type) ? (int)$module_type : 0;
                        if (!isset($bugModuleCounts[$modKey])) {
                          $modKey = 0;
                        }
                        $bugModuleCounts[$modKey]++;
                      }

                      $ticketDt = DateTime::createFromFormat('d-m-Y H:i', $feedback_date_time);
                      if (!$ticketDt) {
                        $ticketDt = DateTime::createFromFormat('d-m-Y H:i:s', $feedback_date_time);
                      }
                      if (!$ticketDt && !empty($feedback_date_time)) {
                        $tmpTs = strtotime($feedback_date_time);
                        if ($tmpTs) {
                          $ticketDt = new DateTime();
                          $ticketDt->setTimestamp($tmpTs);
                        }
                      }
                      if ($ticketDt) {
                        $monthKey = $ticketDt->format('Y-m');
                        $dayKey = $ticketDt->format('Y-m-d');
                        if (!isset($monthTicketCounts[$monthKey])) {
                          $monthTicketCounts[$monthKey] = 0;
                          $monthBugCounts[$monthKey] = 0;
                        }
                        if (!isset($dayTicketCounts[$dayKey])) {
                          $dayTicketCounts[$dayKey] = 0;
                          $dayBugCounts[$dayKey] = 0;
                        }
                        $monthTicketCounts[$monthKey]++;
                        $dayTicketCounts[$dayKey]++;
                        if ((int)$issueType === 1) {
                          $monthBugCounts[$monthKey]++;
                          $dayBugCounts[$dayKey]++;
                        }
                      }

                      $isOpenTicket = $withDevFlag === 0 && !in_array($ticketStatus, array(2, 5, 6), true);
                      if ($isOpenTicket) {
                        $openTicketCount++;
                        $ageDays = 0;
                        if ($ticketDt) {
                          $ageDays = (int)floor((time() - $ticketDt->getTimestamp()) / 86400);
                          if ($ageDays < 0) {
                            $ageDays = 0;
                          }
                        }
                        if ($ageDays <= 2) {
                          $agingBuckets['0-2']++;
                        } elseif ($ageDays <= 7) {
                          $agingBuckets['3-7']++;
                        } elseif ($ageDays <= 15) {
                          $agingBuckets['8-15']++;
                        } else {
                          $agingBuckets['15+']++;
                        }
                      }

                      if ($feedback_date_time != '' && $feedback_solve_time != '') {
                        $tatStart = strtotime($feedback_date_time);
                        $tatEnd = strtotime($feedback_solve_time);
                        if ($tatStart && $tatEnd && $tatEnd > $tatStart) {
                          $ticketTatSumMin += abs($tatEnd - $tatStart) / 60;
                          $ticketTatCount++;
                        }
                      }
                      if ($feedback_date_time != '' && $develeoper_assign_time != '') {
                        $fwdStart = strtotime($feedback_date_time);
                        $fwdEnd = strtotime($develeoper_assign_time);
                        if ($fwdStart && $fwdEnd && $fwdEnd > $fwdStart) {
                          $forwardTatSumMin += abs($fwdEnd - $fwdStart) / 60;
                          $forwardTatCount++;
                        }
                      }
                      if ($develeoper_assign_time != '' && $developer_solve_time != '') {
                        $devStart = strtotime($develeoper_assign_time);
                        $devEnd = strtotime($developer_solve_time);
                        if ($devStart && $devEnd && $devEnd > $devStart) {
                          $devTatSumMin += abs($devEnd - $devStart) / 60;
                          $devTatCount++;
                          $devAgeDays = (int)floor(($devEnd - $devStart) / 86400);
                          $devAgingCount++;
                          if ($devAgeDays <= 2) {
                            $devAgingBuckets['0-2']++;
                          } elseif ($devAgeDays <= 7) {
                            $devAgingBuckets['3-7']++;
                          } elseif ($devAgeDays <= 15) {
                            $devAgingBuckets['8-15']++;
                          } else {
                            $devAgingBuckets['15+']++;
                          }
                        }
                      }
                      if ($developer_solve_time != '' && $feedback_solve_time != '') {
                        $closeStart = strtotime($developer_solve_time);
                        $closeEnd = strtotime($feedback_solve_time);
                        if ($closeStart && $closeEnd && $closeEnd > $closeStart) {
                          $supportCloseTatSumMin += abs($closeEnd - $closeStart) / 60;
                          $supportCloseTatCount++;
                        }
                      }

                      $companyKey = isset($society_id) ? (int)$society_id : 0;
                      $companyLabel = trim((isset($society_name) ? $society_name : '') . '-' . (isset($city_name) ? $city_name : ''), '-');
                      if ($companyLabel === '') {
                        $companyLabel = 'Unknown';
                      }
                      if (!isset($companyStats[$companyKey])) {
                        $companyStats[$companyKey] = array('name' => $companyLabel, 'tickets' => 0, 'bugs' => 0);
                      }
                      $companyStats[$companyKey]['tickets']++;
                      if ((int)$issueType === 1) {
                        $companyStats[$companyKey]['bugs']++;
                      }
                    ?>
                      <tr>
                        <td><?php echo $i++; ?></td>
                        <td>#TKT<?php echo $data['feedback_id']; ?>&nbsp;&nbsp;<?php if ($read_by == 0) { ?><span
                            class="badge badge-warning">Unread</span><?php } ?></td>
                        <td><?php echo $society_name . '-' . $city_name; ?></td>
                        <td class="tableWidth"><?php echo $name; ?></td>
                        <td class="tableWidth"><?php echo $mobile; ?></td>
                        <td class="tableWidth">
                          <?php if ($platform == 0) {
                            $platform_name = "Other";
                          } else if ($platform == 1) {
                            $platform_name = "Android";
                          } else if ($platform == 2) {
                            $platform_name = "iOS";
                          } else if ($platform == 3) {
                            $platform_name = "Web";
                          } else if ($platform == 4) {
                            $platform_name = "CRM";
                          } else {
                            $platform_name = "Other";
                          }
                          echo $platform_name;
                          ?>
                        </td>
                        <td>
                          <?php
                          if ($feedback_status == 0) {
                            echo "<span class='badge badge-warning'>Pending</span>";
                          } else if ($feedback_status == 1) {
                            echo "<span class='badge badge-default'>In Progress</span>";
                          } else if ($feedback_status == 3) {
                            echo "<span class='badge badge-warning'>On Hold</span>";
                          } else if ($feedback_status == 4) {
                            echo "<span class='badge badge-danger'>Rejected</span>";
                          } else if ($feedback_status == 5) {
                            echo "<span class='badge badge-success'>Closed by Developer</span>";
                            $todayDateTime = date('Y-m-d H:i:s');
                            $developerSolveDateTime = date('Y-m-d H:i:s', strtotime("+48 hours", strtotime($developer_solve_time)));
                          } else if ($feedback_status == 2) {
                            echo "<span class='badge badge-success'>Closed</span>";
                          } else if ($feedback_status == 6) {
                            echo "<span class='badge badge-danger'>Rejected by Developer</span>";
                          } else if ($feedback_status == 7) {
                            echo "<span class='badge badge-danger'>Need More Specification</span>";
                          } else if ($feedback_status == 8) {
                            echo "<span class='badge badge-danger'>Resolve in next update</span>";
                          }
                          ?>

                        </td>
                        <td><?php echo $subject; ?></td>
                        <td class="desc-cell">
                          <div class="desc-text"><?php echo htmlspecialchars((string)$feedback_msg); ?></div>
                        </td>
                        <td class="tableWidth">
                          <?php if ($issueType == 0) {
                            $IssueType = "-";
                          } else if ($issueType == 1) {
                            $IssueType = "Bug";
                          } else if ($issueType == 2) {
                            $IssueType = "Configuration Issue";
                          } else if ($issueType == 3) {
                            $IssueType = "Training Issue";
                          } else if ($issueType == 4) {
                            $IssueType = "Issue Not Found";
                          } else if ($issueType == 5) {
                            $IssueType = "Change Request by Client";
                          } else if ($issueType == 6) {
                            $IssueType = "Device Specific Issue";
                          } else if ($issueType == 7) {
                            $IssueType = "Data Delete Request";
                          } else if ($issueType == 8) {
                            $IssueType = "Not an issue";
                          } else if ($issueType == 9) {
                            $IssueType = "Internet Connectivity Issue";
                          }
                          echo $IssueType;
                          ?>
                        </td>
                        <td class="tableWidth">
                          <?php
                          $modId = isset($module_type) && $module_type !== '' ? (int)$module_type : -1;
                          echo isset($moduleTypeLabels[$modId]) ? htmlspecialchars($moduleTypeLabels[$modId]) : '-';
                          ?>
                        </td>
                        <td><?php echo $feedback_date_time; ?></td>
                        <td>
                          <?php if ($created_by > 0) {
                            echo $admin_name;
                          } else {
                            echo $d->app_name() . " App";
                          }
                          ?>
                        </td>
                        <td>
                          <?php
                          $feed_tat = "";
                          if ($feedback_date_time != '' && $feedback_solve_time != '') {
                            $time1 = new DateTime($feedback_date_time);
                            $time2 = new DateTime($feedback_solve_time);
                            $feed_tat = $time1->diff($time2);
                          }
                          if ($feed_tat != "") {
                            echo $feed_tat->format('%a d %h h %i m');
                          }
                          ?>
                        </td>
                        <td>
                          <?php if ($feed_tat != "") {
                            $to_time1 = strtotime($feedback_date_time);
                            $from_time1 = strtotime($feedback_solve_time);
                            echo round(abs($to_time1 - $from_time1) / 60, 0);
                          } ?>
                        </td>
                        <td>
                          <?php
                          $fwd_tat = "";
                          if ($feedback_date_time != '' && $develeoper_assign_time != '') {
                            $time1 = new DateTime($feedback_date_time);
                            $time2 = new DateTime($develeoper_assign_time);
                            $fwd_tat = $time1->diff($time2);
                          }
                          if ($fwd_tat != "") {
                            echo $fwd_tat->format('%a d %h h %i m');
                          }
                          ?>
                        </td>
                        <td>
                          <?php if ($fwd_tat != "") {
                            echo round(abs(strtotime($feedback_date_time) - strtotime($develeoper_assign_time)) / 60, 0);
                          } ?>
                        </td>
                        <td>
                          <?php
                          $dev_tat = "";
                          if ($develeoper_assign_time != '' && $developer_solve_time != '') {
                            $time1 = new DateTime($develeoper_assign_time);
                            $time2 = new DateTime($developer_solve_time);
                            $dev_tat = $time1->diff($time2);
                          }
                          if ($dev_tat != "") {
                            echo $dev_tat->format('%a d %h h %i m');
                          }
                          ?>
                        </td>
                        <td>
                          <?php if ($dev_tat != "") {
                            $to_time = strtotime($develeoper_assign_time);
                            $from_time = strtotime($developer_solve_time);
                            echo round(abs($to_time - $from_time) / 60, 0);
                          } ?>
                        </td>
                        <td>
                          <?php
                          $close_tat = "";
                          if ($developer_solve_time != '' && $feedback_solve_time != '') {
                            $time1 = new DateTime($developer_solve_time);
                            $time2 = new DateTime($feedback_solve_time);
                            $close_tat = $time1->diff($time2);
                          }
                          if ($close_tat != "") {
                            echo $close_tat->format('%a d %h h %i m');
                          }
                          ?>
                        </td>
                        <td>
                          <?php if ($close_tat != "") {
                            echo round(abs(strtotime($developer_solve_time) - strtotime($feedback_solve_time)) / 60, 0);
                          } ?>
                        </td>
                        <td><?php echo $develeoper_assign_time; ?></td>
                        <td><?php echo $developer_solve_time; ?></td>
                        <td><?php echo $feedback_solve_time; ?></td>
                      </tr>

                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <?php
        $tableHtml = ob_get_clean();
        $totalClosed = array_sum($issueTypeCounts);
        $totalAll = (int)$ticketRowCount;
        $totalForwarded = $withDeveloperCount + $totalClosed;
        $totalTickets = (int)$ticketRowCount;
        $totalBugs = isset($issueTypeCounts[1]) ? (int)$issueTypeCounts[1] : 0;
        $forwardedPercent = $totalTickets > 0 ? (int)round(($totalForwarded / $totalTickets) * 100) : 0;

        // Live queue counts — same rules as App Feedback (no date range)
        // Pending tab: Status=1 + tab=0 → status=0 AND with_developer=0
        // Close by Developer tab: Status=2 + tab=0 → status=5 AND with_developer=0
        $liveQueueWhere = "feedback_master.society_id IS NOT NULL AND feedback_master.society_id > 0 AND feedback_master.inquiry_type='0' AND feedback_master.with_developer=0";
        if ($platform_filter == 1) {
          $liveQueueWhere .= " AND feedback_master.platform='1'";
        } else if ($platform_filter == 2) {
          $liveQueueWhere .= " AND feedback_master.platform='2'";
        } else if ($platform_filter == 3) {
          $liveQueueWhere .= " AND feedback_master.platform='3'";
        } else if ($platform_filter == 4) {
          $liveQueueWhere .= " AND feedback_master.platform='0'";
        } else if ($platform_filter == 5) {
          $liveQueueWhere .= " AND feedback_master.platform='4'";
        }
        if (isset($_GET['filter_type']) && $_GET['filter_type'] != 0) {
          $liveQueueWhere .= " AND feedback_master.issueType='$filter_type'";
        }
        $pendingCount = (int)$d->count_data_direct("feedback_id", "feedback_master", "$liveQueueWhere AND feedback_master.feedback_status='0'");
        $closedByDevNotSupportCount = (int)$d->count_data_direct("feedback_id", "feedback_master", "$liveQueueWhere AND feedback_master.feedback_status='5'");

        $filterDays = 0;
        $hasDateFilter = !empty($startDate) && !empty($endDate);
        if ($hasDateFilter) {
          $startTs = strtotime($startDate);
          $endTs = strtotime($endDate);
          if ($startTs && $endTs && $endTs >= $startTs) {
            $filterDays = (int)floor(($endTs - $startTs) / 86400) + 1;
          }
        }
        if ($hasDateFilter && $filterDays < 1) {
          $filterDays = 1;
        }
        $avgTicketsPerDay = ($hasDateFilter && $filterDays > 0) ? round($totalTickets / $filterDays, 1) : '—';
        $avgForwardedPerDay = ($hasDateFilter && $filterDays > 0) ? round($totalForwarded / $filterDays, 1) : '—';
        $avgBugsPerDay = ($hasDateFilter && $filterDays > 0) ? round($totalBugs / $filterDays, 1) : '—';

        $formatAvgTat = function ($minutes) {
          if ($minutes === null || $minutes <= 0) {
            return '—';
          }
          if ($minutes >= 1440) {
            return round($minutes / 1440, 1) . ' d';
          }
          if ($minutes >= 60) {
            return round($minutes / 60, 1) . ' h';
          }
          return round($minutes) . ' m';
        };
        $avgTicketTatMin = $ticketTatCount > 0 ? ($ticketTatSumMin / $ticketTatCount) : null;
        $avgDevTatMin = $devTatCount > 0 ? ($devTatSumMin / $devTatCount) : null;
        $avgForwardTatMin = $forwardTatCount > 0 ? ($forwardTatSumMin / $forwardTatCount) : null;
        $avgSupportCloseTatMin = $supportCloseTatCount > 0 ? ($supportCloseTatSumMin / $supportCloseTatCount) : null;
        $avgTicketTatText = $formatAvgTat($avgTicketTatMin);
        $avgDevTatText = $formatAvgTat($avgDevTatMin);
        $avgForwardTatText = $formatAvgTat($avgForwardTatMin);
        $avgSupportCloseTatText = $formatAvgTat($avgSupportCloseTatMin);

        $mixBugs = isset($issueTypeCounts[1]) ? (int)$issueTypeCounts[1] : 0;
        $mixTrainConfig = (int)$issueTypeCounts[2] + (int)$issueTypeCounts[3];
        $mixNotIssue = (int)$issueTypeCounts[4] + (int)$issueTypeCounts[8];
        $mixChange = (int)$issueTypeCounts[5] + (int)$issueTypeCounts[7];
        $mixOther = (int)$issueTypeCounts[6] + (int)$issueTypeCounts[9];
        $mixClassified = $mixBugs + $mixTrainConfig + $mixNotIssue + $mixChange + $mixOther;
        $mixUnclassified = max(0, $totalTickets - $mixClassified);
        $mixOtherAll = $mixOther + $mixUnclassified;
        $bugPercentAll = $totalTickets > 0 ? (int)round(($mixBugs / $totalTickets) * 100) : 0;
        $mixPct = function ($n, $d) {
          return $d > 0 ? (int)round(($n / $d) * 100) : 0;
        };

        $topCompanies = $companyStats;
        usort($topCompanies, function ($a, $b) {
          if ($a['tickets'] == $b['tickets']) {
            return $b['bugs'] - $a['bugs'];
          }
          return $b['tickets'] - $a['tickets'];
        });
        $topCompanies = array_slice($topCompanies, 0, 10);
        $topCompanyLabels = array();
        $topCompanyTickets = array();
        $topCompanyBugs = array();
        foreach ($topCompanies as $row) {
          $topCompanyLabels[] = $row['name'];
          $topCompanyTickets[] = (int)$row['tickets'];
          $topCompanyBugs[] = (int)$row['bugs'];
        }
        $chartMixLabels = array('Bug', 'Training / Config', 'Not an issue', 'Change Request', 'Other');
        $chartMixCounts = array($mixBugs, $mixTrainConfig, $mixNotIssue, $mixChange, $mixOtherAll);

        $mergedIssueLabels = array(
          1 => 'Bug',
          2 => 'Configuration Issue',
          3 => 'Training Issue',
          4 => 'Issue Not Found / Not an issue',
          5 => 'Change Request / Data Delete Request',
          6 => 'Device Specific Issue',
          9 => 'Internet Connectivity Issue',
        );
        $mergedIssueCounts = array(
          1 => (int)$issueTypeCounts[1],
          2 => (int)$issueTypeCounts[2],
          3 => (int)$issueTypeCounts[3],
          4 => (int)$issueTypeCounts[4] + (int)$issueTypeCounts[8],
          5 => (int)$issueTypeCounts[5] + (int)$issueTypeCounts[7],
          6 => (int)$issueTypeCounts[6],
          9 => (int)$issueTypeCounts[9],
        );

        $chartIssueLabels = array();
        $chartIssueCounts = array();
        foreach ($mergedIssueLabels as $key => $label) {
          $chartIssueLabels[] = $label;
          $chartIssueCounts[] = $mergedIssueCounts[$key];
        }

        $chartModuleLabels = array();
        $chartModuleCounts = array();
        foreach ($moduleTypes as $mod) {
          $chartModuleLabels[] = $mod['label'];
          $chartModuleCounts[] = isset($bugModuleCounts[$mod['id']]) ? (int)$bugModuleCounts[$mod['id']] : 0;
        }

        $chartPlatformLabels = array('App', 'Web', 'CRM', 'Other');
        $chartPlatformCounts = array(
          (int)$platformCounts[1] + (int)$platformCounts[2],
          (int)$platformCounts[3],
          (int)$platformCounts[4],
          (int)$platformCounts[0],
        );
        $chartBugPlatformCounts = array(
          (int)$bugPlatformCounts[1] + (int)$bugPlatformCounts[2],
          (int)$bugPlatformCounts[3],
          (int)$bugPlatformCounts[4],
          (int)$bugPlatformCounts[0],
        );

        $trendLabels = array();
        $trendTicketCounts = array();
        $trendBugCounts = array();
        $isSingleMonth = $hasDateFilter && date('Y-m', strtotime($startDate)) === date('Y-m', strtotime($endDate));
        $trendTitle = 'Month Wise Tickets Created vs Bugs';

        if ($isSingleMonth) {
          $trendTitle = 'Daily Tickets Created vs Bugs (' . date('F Y', strtotime($startDate)) . ')';
          $cursor = new DateTime($startDate);
          $endDt = new DateTime($endDate);
          while ($cursor <= $endDt) {
            $key = $cursor->format('Y-m-d');
            $trendLabels[] = $cursor->format('d M');
            $trendTicketCounts[] = isset($dayTicketCounts[$key]) ? (int)$dayTicketCounts[$key] : 0;
            $trendBugCounts[] = isset($dayBugCounts[$key]) ? (int)$dayBugCounts[$key] : 0;
            $cursor->modify('+1 day');
          }
        } else {
          $cursor = null;
          $endMonth = null;
          if ($hasDateFilter) {
            $cursor = new DateTime(date('Y-m-01', strtotime($startDate)));
            $endMonth = new DateTime(date('Y-m-01', strtotime($endDate)));
          } else {
            $allMonths = array_keys($monthTicketCounts);
            sort($allMonths);
            if (!empty($allMonths)) {
              $cursor = new DateTime($allMonths[0] . '-01');
              $endMonth = new DateTime($allMonths[count($allMonths) - 1] . '-01');
            }
          }
          if ($cursor && $endMonth) {
            while ($cursor <= $endMonth) {
              $key = $cursor->format('Y-m');
              $trendLabels[] = $cursor->format('M Y');
              $trendTicketCounts[] = isset($monthTicketCounts[$key]) ? (int)$monthTicketCounts[$key] : 0;
              $trendBugCounts[] = isset($monthBugCounts[$key]) ? (int)$monthBugCounts[$key] : 0;
              $cursor->modify('+1 month');
            }
          }
        }
        $selectedDateText = '';
        if (isset($_GET['date_range_filter'])) {
          if ($_GET['date_range_filter'] !== '') {
            $selectedDateText = htmlspecialchars($_GET['date_range_filter']);
          } else {
            $selectedDateText = 'All Time';
          }
        } else {
          $selectedDateText = date('F d, Y', strtotime('-29 days')) . ' - ' . date('F d, Y');
        }
        ?>
        <div class="card mt-3">
          <div class="card-body">
            <div class="mb-3 d-flex flex-wrap align-items-center justify-content-between">
              <div>
                <strong>Executive Snapshot</strong>
                <span class="text-muted"> — management view of the filtered range</span>
              </div>
              <div>
                <strong>Date Range:</strong> <?= $selectedDateText ?>
                <?php if (!empty($startDate) && !empty($endDate)) { ?>
                  <span class="text-muted">(<?= (int)$filterDays ?> days)</span>
                <?php } ?>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-2">
                <div class="avg-stat-box avg-stat-days">
                  <small>Filter Days</small>
                  <h3><?= !empty($startDate) && !empty($endDate) ? (int)$filterDays : 'All' ?></h3>
                </div>
              </div>
              <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-2">
                <div class="avg-stat-box avg-stat-tickets">
                  <small>Total Tickets</small>
                  <h3><?= (int)$totalTickets ?></h3>
                </div>
              </div>
              <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-2">
                <div class="avg-stat-box avg-stat-forwarded">
                  <small>Forwarded to Developer</small>
                  <h3><?= (int)$totalForwarded ?> <span style="font-size: 16px; font-weight: 600;">(<?= (int)$forwardedPercent ?>%)</span></h3>
                </div>
              </div>
              <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-2">
                <div class="avg-stat-box avg-stat-open">
                  <small>Open Tickets</small>
                  <h3><?= (int)$openTicketCount ?></h3>
                </div>
              </div>
              <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-2">
                <div class="avg-stat-box avg-stat-mix">
                  <small>Bug Mix</small>
                  <h3><?= (int)$bugPercentAll ?>%</h3>
                </div>
              </div>
              <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-2">
                <div class="avg-stat-box avg-stat-tat">
                  <small>Avg Ticket TAT</small>
                  <h3><?= htmlspecialchars($avgTicketTatText) ?></h3>
                </div>
              </div>
              <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-2">
                <div class="avg-stat-box avg-stat-fwd-tat">
                  <small>Avg Created to Dev. Forward TAT</small>
                  <h3><?= htmlspecialchars($avgForwardTatText) ?></h3>
                </div>
              </div>
              <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-2">
                <div class="avg-stat-box avg-stat-dev-tat">
                  <small>Avg Developer TAT</small>
                  <h3><?= htmlspecialchars($avgDevTatText) ?></h3>
                </div>
              </div>
              <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-2">
                <div class="avg-stat-box avg-stat-close-tat">
                  <small>Avg Dev Closed to Resolve TAT</small>
                  <h3><?= htmlspecialchars($avgSupportCloseTatText) ?></h3>
                </div>
              </div>
              <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-2">
                <div class="avg-stat-box avg-stat-ticket-avg">
                  <small>Avg Tickets / Day</small>
                  <h3><?= $avgTicketsPerDay ?></h3>
                </div>
              </div>
              <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-2">
                <div class="avg-stat-box avg-stat-forwarded-avg">
                  <small>Avg Forwarded / Day</small>
                  <h3><?= $avgForwardedPerDay ?></h3>
                </div>
              </div>
              <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-2">
                <div class="avg-stat-box avg-stat-bug-avg">
                  <small>Avg Bugs / Day</small>
                  <h3><?= $avgBugsPerDay ?></h3>
                </div>
              </div>
            </div>

            <div class="exec-section-title">Open Ticket Aging (<?= (int)$openTicketCount ?>)</div>
            <div class="row mb-3">
              <div class="col-6 col-md-3 mb-2">
                <div class="avg-stat-box aging-fresh">
                  <small>0–2 Days</small>
                  <h3><?= (int)$agingBuckets['0-2'] ?> <span style="font-size:16px;font-weight:600;">(<?= $mixPct($agingBuckets['0-2'], $openTicketCount) ?>%)</span></h3>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-2">
                <div class="avg-stat-box aging-ok">
                  <small>3–7 Days</small>
                  <h3><?= (int)$agingBuckets['3-7'] ?> <span style="font-size:16px;font-weight:600;">(<?= $mixPct($agingBuckets['3-7'], $openTicketCount) ?>%)</span></h3>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-2">
                <div class="avg-stat-box aging-warn">
                  <small>8–15 Days</small>
                  <h3><?= (int)$agingBuckets['8-15'] ?> <span style="font-size:16px;font-weight:600;">(<?= $mixPct($agingBuckets['8-15'], $openTicketCount) ?>%)</span></h3>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-2">
                <div class="avg-stat-box aging-late">
                  <small>15+ Days</small>
                  <h3><?= (int)$agingBuckets['15+'] ?> <span style="font-size:16px;font-weight:600;">(<?= $mixPct($agingBuckets['15+'], $openTicketCount) ?>%)</span></h3>
                </div>
              </div>
            </div>

            <div class="exec-section-title">Developer Ticket Aging (<?= (int)$devAgingCount ?>) <span class="text-muted" style="font-weight:500;font-size:12px;">— assign to developer closed</span></div>
            <div class="row mb-3">
              <div class="col-6 col-md-3 mb-2">
                <div class="avg-stat-box aging-fresh">
                  <small>0–2 Days</small>
                  <h3><?= (int)$devAgingBuckets['0-2'] ?> <span style="font-size:16px;font-weight:600;">(<?= $mixPct($devAgingBuckets['0-2'], $devAgingCount) ?>%)</span></h3>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-2">
                <div class="avg-stat-box aging-ok">
                  <small>3–7 Days</small>
                  <h3><?= (int)$devAgingBuckets['3-7'] ?> <span style="font-size:16px;font-weight:600;">(<?= $mixPct($devAgingBuckets['3-7'], $devAgingCount) ?>%)</span></h3>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-2">
                <div class="avg-stat-box aging-warn">
                  <small>8–15 Days</small>
                  <h3><?= (int)$devAgingBuckets['8-15'] ?> <span style="font-size:16px;font-weight:600;">(<?= $mixPct($devAgingBuckets['8-15'], $devAgingCount) ?>%)</span></h3>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-2">
                <div class="avg-stat-box aging-late">
                  <small>15+ Days</small>
                  <h3><?= (int)$devAgingBuckets['15+'] ?> <span style="font-size:16px;font-weight:600;">(<?= $mixPct($devAgingBuckets['15+'], $devAgingCount) ?>%)</span></h3>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-lg-7">
                <div class="exec-section-title">Ticket Mix — Bug vs Others (<?= (int)$totalTickets ?>)</div>
                <div class="row">
                  <div class="col-6 col-md mb-2">
                    <div class="avg-stat-box mix-bug">
                      <small>Bugs</small>
                      <h3><?= (int)$mixBugs ?> <span style="font-size:16px;font-weight:600;">(<?= $mixPct($mixBugs, $totalTickets) ?>%)</span></h3>
                    </div>
                  </div>
                  <div class="col-6 col-md mb-2">
                    <div class="avg-stat-box mix-train">
                      <small>Training / Config</small>
                      <h3><?= (int)$mixTrainConfig ?> <span style="font-size:16px;font-weight:600;">(<?= $mixPct($mixTrainConfig, $totalTickets) ?>%)</span></h3>
                    </div>
                  </div>
                  <div class="col-6 col-md mb-2">
                    <div class="avg-stat-box mix-not">
                      <small>Not an Issue</small>
                      <h3><?= (int)$mixNotIssue ?> <span style="font-size:16px;font-weight:600;">(<?= $mixPct($mixNotIssue, $totalTickets) ?>%)</span></h3>
                    </div>
                  </div>
                  <div class="col-6 col-md mb-2">
                    <div class="avg-stat-box mix-change">
                      <small>Change Request</small>
                      <h3><?= (int)$mixChange ?> <span style="font-size:16px;font-weight:600;">(<?= $mixPct($mixChange, $totalTickets) ?>%)</span></h3>
                    </div>
                  </div>
                  <div class="col-6 col-md mb-2">
                    <div class="avg-stat-box mix-other">
                      <small>Other</small>
                      <h3><?= (int)$mixOtherAll ?> <span style="font-size:16px;font-weight:600;">(<?= $mixPct($mixOtherAll, $totalTickets) ?>%)</span></h3>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-5">
                <div class="exec-section-title">Ticket Mix</div>
                <div class="chart-box chart-mix">
                  <canvas id="ticketMixChart"></canvas>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="card mb-3">
                  <div class="card-body">
                    <h6 class="mb-3">Total Forwarded to Developer (<?= (int)$totalForwarded ?>)</h6>
                    <div class="chart-box chart-doughnut">
                      <canvas id="forwardedOverviewChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card mb-3">
                  <div class="card-body">
                    <h6 class="mb-3">Platform Wise (<?= (int)$totalTickets ?>)</h6>
                    <div class="chart-box chart-doughnut">
                      <canvas id="platformChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card mb-3">
                  <div class="card-body">
                    <h6 class="mb-3">Platform Wise Bugs (<?= (int)$totalBugs ?>)</h6>
                    <div class="chart-box chart-doughnut">
                      <canvas id="platformBugChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="exec-section-title">Top Companies by Tickets</div>
            <div class="row">
              <div class="col-lg-7">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered mb-3">
                    <thead class="bg-primary">
                      <tr>
                        <th class="text-white" style="width: 8%">#</th>
                        <th class="text-white">Company</th>
                        <th class="text-white" style="width: 14%">Tickets</th>
                        <th class="text-white" style="width: 14%">Bugs</th>
                        <th class="text-white" style="width: 14%">Bug %</th>
                        <th class="text-white" style="width: 14%">Share</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($topCompanies)) { ?>
                        <tr>
                          <td colspan="6" class="text-center text-muted">No company data</td>
                        </tr>
                      <?php } else {
                        $rank = 1;
                        foreach ($topCompanies as $row) {
                          $companyTickets = (int)$row['tickets'];
                          $companyBugs = (int)$row['bugs'];
                          $companyBugPct = $mixPct($companyBugs, $companyTickets);
                          ?>
                          <tr>
                            <td><?= $rank++ ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= $companyTickets ?></td>
                            <td><?= $companyBugs ?></td>
                            <td><?= $companyBugPct ?>%</td>
                            <td><?= $mixPct($companyTickets, $totalTickets) ?>%</td>
                          </tr>
                        <?php }
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="col-lg-5">
                <div class="chart-box chart-company">
                  <canvas id="topCompanyChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card mt-3">
          <div class="card-body">
            <div class="mb-3">
              <strong>Graphical View</strong>
              <span class="text-muted"> — same filters &amp; date range as the report above</span>
            </div>
            <div class="row">
              <div class="col-lg-12">
                <div class="card mb-3">
                  <div class="card-body">
                    <h6 class="mb-3">Closed by Developer — Issue Types (<?= (int)$totalClosed ?>)</h6>
                    <div class="chart-box chart-bar">
                      <canvas id="issueTypeChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="card mb-3">
                  <div class="card-body">
                    <h6 class="mb-3">Bug Report — Module Wise (<?= (int)$totalBugs ?>)</h6>
                    <div class="chart-box chart-module">
                      <canvas id="bugModuleChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="card mb-0">
                  <div class="card-body">
                    <h6 class="mb-3"><?= htmlspecialchars($trendTitle) ?></h6>
                    <div class="chart-box chart-line">
                      <canvas id="trendLineChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card mt-3">
          <div class="card-body">
            <table class="table table-striped table-bordered">
              <thead class="bg-primary">
                <tr>
                  <th class="text-white" style="width: 70%">Title</th>
                  <th class="text-white" style="width: 30%">Count</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>All</strong></td>
                  <td><strong><?= $totalAll ?></strong></td>
                </tr>
                <tr>
                  <td><strong>Total Open Tickets with Support Team</strong></td>
                  <td><strong><?= $openCount ?></strong></td>
                </tr>
                <tr>
                  <td><strong>Pending Tickets Not Touch with Support Team</strong></td>
                  <td><strong><?= (int)$pendingCount ?></strong></td>
                </tr>
                <tr>
                  <td><strong>Open Tickets with Support Team (Closed by Developer)</strong></td>
                  <td><strong><?= (int)$closedByDevNotSupportCount ?></strong></td>
                </tr>
                <tr>
                  <td><strong>Direct Closed by Support Team</strong></td>
                  <td><strong><?= ($solvedCount + $closedByDeveloperCount) - $totalClosed; ?></strong></td>
                </tr>

                <tr>
                  <td><strong>Rejected By Developer</strong></td>
                  <td><strong><?= $rejectedCount ?></strong></td>
                </tr>

                <tr class="table-light">
                  <td><strong>Closed By Support Team (Closed by Developer)</strong></td>
                  <td><strong><?= $solvedCount ?></strong></td>
                </tr>
                <tr class="table-light">
                  <td><strong>Total Forwareded to Developer</strong></td>
                  <td><strong><?= $withDeveloperCount + $totalClosed; ?></strong></td>
                </tr>
                <tr class="table-light">
                  <td><strong>Open With Developer</strong></td>
                  <td><strong><?= $withDeveloperCount ?></strong></td>
                </tr>
                <tr class="table-light">
                  <td><strong>Total Closed by Developer</strong></td>
                  <td><strong><?= $totalClosed ?></strong></td>
                </tr>

                <?php foreach ($mergedIssueLabels as $key => $label): ?>
                  <tr>
                    <td style="padding-left: 2rem;">↳ <?= htmlspecialchars($label) ?></td>
                    <td><?= $mergedIssueCounts[$key] ?></td>
                  </tr>
                <?php endforeach; ?>

              </tbody>
            </table>
          </div>
        </div>
        <?php
        $issueTypesNew = [
          'closed_rejected' => ['id' => 0, 'label' => 'Rejected', 'emoji' => '⛔'],
          'closed_bug' => ['id' => 1, 'label' => 'Bug', 'emoji' => '🐞'],
          'closed_configuration_issue' => ['id' => 2, 'label' => 'Configuration Issue', 'emoji' => '⚙️'],
          'closed_training_issue' => ['id' => 3, 'label' => 'Training Issue', 'emoji' => '🧑‍🏫'],
          'closed_issue_not_found' => ['id' => 4, 'label' => 'Issue Not Found', 'emoji' => '❌'],
          'closed_client_change_request' => ['id' => 5, 'label' => 'Change Request by Client', 'emoji' => '🔁'],
          'closed_device_issue' => ['id' => 6, 'label' => 'Device Specific Issue', 'emoji' => '📱'],
          'closed_data_delete_request' => ['id' => 7, 'label' => 'Data Delete Request', 'emoji' => '🗑️'],
          'closed_not_an_issue' => ['id' => 8, 'label' => 'Not an issue', 'emoji' => '🚫'],
          'closed_internet_connectivity_issue' => ['id' => 9, 'label' => 'Internet Connectivity Issue', 'emoji' => '🌐']
        ];

        $current_date = date('Y-m-d');
        $selectFields = [];
        $selectFields[] = "SUM(CASE WHEN (feedback_status IN ('0','1','3','7','8') AND isTicket != 0) THEN 1 ELSE 0 END) AS today_open_with_developer";
        $selectFields[] = "SUM(CASE WHEN (feedback_status = '8' AND isTicket != 0 AND with_developer = 1) THEN 1 ELSE 0 END) AS today_resolved_next_update";
        $selectFields[] = "SUM(CASE WHEN (feedback_status = '7' AND isTicket != 0 AND with_developer = 1) THEN 1 ELSE 0 END) AS today_need_more_spec";
        $selectFields[] = "SUM(CASE WHEN DATE(develeoper_assign_time) = '$current_date' THEN 1 ELSE 0 END) AS today_generated_ticket";
        $selectFields[] = "SUM(CASE WHEN DATE(developer_solve_time) = '$current_date'  THEN 1 ELSE 0 END) AS today_closed_ticket";
        foreach ($issueTypesNew as $field => $data) {
          $selectFields[] = "SUM(CASE WHEN DATE(developer_solve_time) = '$current_date'  AND issueType = {$data['id']} THEN 1 ELSE 0 END) AS $field";
        }
        foreach ($moduleTypes as $field => $data) {
          $selectFields[] = "SUM(CASE WHEN DATE(developer_solve_time) = '$current_date' AND issueType = 1 AND module_type = {$data['id']} THEN 1 ELSE 0 END) AS $field";
        }
        $sqlFields = implode(",\n", $selectFields);
        $todaysCountQry = $d->selectRow(
          $sqlFields,
          "feedback_master",
          "feedback_master.society_id != 0"
        );
        $todaysCountData = mysqli_fetch_array($todaysCountQry);
        $todayOpen = $todaysCountData['today_open_with_developer'] ?? 0;
        $todayResolvedNext = $todaysCountData['today_resolved_next_update'] ?? 0;
        $todayNeedMoreSpec = $todaysCountData['today_need_more_spec'] ?? 0;
        $todayAssigned = $todaysCountData['today_generated_ticket'] ?? 0;
        $todayClosed = $todaysCountData['today_closed_ticket'] ?? 0;

        $copyText = "🚦 *Ticket Summary (" . date('d-M-Y') . ")*\n";
        $copyText .= " 🟠 Open with Developer: $todayOpen\n";
        $copyText .= " 🧩 Resolved in Next Update: $todayResolvedNext\n";
        $copyText .= " 🧾 Need More Specification: $todayNeedMoreSpec\n";
        $copyText .= " 🆕 Assigned Today: $todayAssigned\n";
        $copyText .= " ✅ Closed Today: $todayClosed\n\n";
        $copyText .= " 📂 *Closed Ticket Breakdown:*\n";

        foreach ($issueTypesNew as $field => $data) {
          $count = $todaysCountData[$field] ?? 0;
          $copyText .= " {$data['emoji']} {$data['label']}: $count\n";
        }
        $copyText .= "\n🔍 *Bug Report – Module Wise:*\n";
        foreach ($moduleTypes as $field => $data) {
          $count = $todaysCountData[$field] ?? 0;
          $copyText .= " {$data['emoji']} {$data['label']}: $count\n";
        }
        ?>
        <div class="card mt-3">
          <div class="card-header today-status-toggle d-flex align-items-center justify-content-between"
            data-toggle="collapse" data-target="#todayTicketStatus" aria-expanded="false" aria-controls="todayTicketStatus">
            <strong>🚦 Ticket Status (Today)</strong>
            <div class="d-flex align-items-center">
              <button type="button" id="copySummaryBtn" class="btn btn-sm btn-primary mr-2">📋 Copy Summary</button>
              <i class="fa fa-chevron-down toggle-icon"></i>
              <textarea id="summaryText" style="display:none;"><?php echo trim($copyText); ?></textarea>
            </div>
          </div>
          <div id="todayTicketStatus" class="collapse">
            <div class="card-body">
            <table class="table table-striped table-bordered">
              <thead class="bg-primary">
                <tr>
                  <th class="text-white" style="width: 70%">Title</th>
                  <th class="text-white" style="width: 30%">Count</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>Today Open With Developer</strong></td>
                  <td><strong><?= $todaysCountData['today_open_with_developer'] ?></strong></td>
                </tr>
                <tr>
                  <td><strong>Resolved in next update</strong></td>
                  <td><strong><?= (int)($todaysCountData['today_resolved_next_update'] ?? 0) ?></strong></td>
                </tr>
                <tr>
                  <td><strong>Need More Specification</strong></td>
                  <td><strong><?= (int)($todaysCountData['today_need_more_spec'] ?? 0) ?></strong></td>
                </tr>
                <tr>
                  <td><strong>Today Generated Ticket</strong></td>
                  <td><strong><?= $todaysCountData['today_generated_ticket']; ?></strong></td>
                </tr>

                <tr>
                  <td><strong>Today Closed Ticket</strong></td>
                  <td><strong><?= $todaysCountData['today_closed_ticket'] ?></strong></td>
                </tr>
                <?php
                foreach ($issueTypesNew as $field => $data): ?>
                  <tr>
                    <td style="padding-left: 2rem;">↳ <?= htmlspecialchars($data['label']) ?></td>
                    <td><?= $todaysCountData[$field] ?? 0 ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>

              <tr>
                <td colspan="2"><strong>🔍 Bug Report – Module Wise:</strong></td>
              </tr>
              <?php foreach ($moduleTypes as $field => $data): ?>
                <tr>
                  <td style="padding-left: 2rem;">↳ <?= htmlspecialchars($data['label']) ?></td>
                  <td><?= $todaysCountData[$field] ?? 0 ?></td>
                </tr>
              <?php endforeach; ?>
            </table>
            </div>
          </div>
        </div>
        <?php echo $tableHtml; ?>
      </div>
    </div>
  </div>
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/plugins/Chart.js/Chart.min.js"></script>
  <script>
    $(document).ready(function() {
      moment.locale('en');

      function initDateRangePicker() {
        const urlParams = new URLSearchParams(window.location.search);
        const dateRangeValue = $('#date_range_filter').val();
        let startDate, endDate;

        if (dateRangeValue) {
          const dates = dateRangeValue.split(' - ');
          startDate = moment(dates[0], 'MMMM DD, YYYY');
          endDate = moment(dates[1], 'MMMM DD, YYYY');
        } else {
          startDate = moment().subtract(29, 'days');
          endDate = moment();
        }

        $('#date_range_filter').daterangepicker({
          startDate: startDate,
          endDate: endDate,
          opens: 'right',
          locale: {
            format: 'MMMM DD, YYYY',
            applyLabel: 'Apply',
            cancelLabel: 'Clear',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: [
              'January', 'February', 'March', 'April', 'May', 'June',
              'July', 'August', 'September', 'October', 'November', 'December'
            ],
            firstDay: 1
          },
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          alwaysShowCalendars: true,
          autoUpdateInput: false,
          maxDate: moment()
        });

        if (!urlParams.has('date_range_filter') && !dateRangeValue) {
          $('#date_range_filter').val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
        }
        $('#date_range_filter').on('apply.daterangepicker', function(ev, picker) {
          $(this).val(picker.startDate.format('MMMM DD, YYYY') + ' - ' + picker.endDate.format('MMMM DD, YYYY'));
          $('#filterForm').submit();
        });

        $('#date_range_filter').on('cancel.daterangepicker', function() {
          $(this).val('');
          $('#filterForm').submit();
        });
      }

      initDateRangePicker();
    });
  </script>
  <script>
    document.getElementById('copySummaryBtn').addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const textArea = document.getElementById('summaryText');
      textArea.style.display = 'block';
      textArea.select();
      document.execCommand('copy');
      textArea.style.display = 'none';
      Lobibox.notify('success', {
        pauseDelayOnHover: true,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        icon: 'fa fa-check-circle',
        msg: "Summary has been copied to clipboard."
      });
    });
  </script>
  <script>
    $(function() {
      if (typeof Chart === 'undefined') {
        return;
      }

      var forwardedData = {
        labels: ['Open With Developer', 'Total Closed by Developer'],
        values: [<?php echo (int)$withDeveloperCount; ?>, <?php echo (int)$totalClosed; ?>],
        colors: ['#f39c12', '#27ae60']
      };
      var issueTypeData = {
        labels: <?php echo json_encode($chartIssueLabels); ?>,
        values: <?php echo json_encode($chartIssueCounts); ?>,
        colors: ['#e74c3c', '#3498db', '#9b59b6', '#7f8c8d', '#f39c12', '#1abc9c', '#16a085']
      };
      var platformData = {
        labels: <?php echo json_encode($chartPlatformLabels); ?>,
        values: <?php echo json_encode($chartPlatformCounts); ?>,
        colors: ['#3ddc84', '#3498db', '#9b59b6', '#95a5a6']
      };
      var platformBugData = {
        labels: <?php echo json_encode($chartPlatformLabels); ?>,
        values: <?php echo json_encode($chartBugPlatformCounts); ?>,
        colors: ['#3ddc84', '#3498db', '#9b59b6', '#95a5a6']
      };
      var ticketMixData = {
        labels: <?php echo json_encode($chartMixLabels); ?>,
        values: <?php echo json_encode($chartMixCounts); ?>,
        colors: ['#e74c3c', '#3498db', '#7f8c8d', '#f39c12', '#9b59b6']
      };
      var topCompanyData = {
        labels: <?php echo json_encode($topCompanyLabels); ?>,
        tickets: <?php echo json_encode($topCompanyTickets); ?>,
        bugs: <?php echo json_encode($topCompanyBugs); ?>
      };
      var bugModuleData = {
        labels: <?php echo json_encode($chartModuleLabels); ?>,
        values: <?php echo json_encode($chartModuleCounts); ?>,
        colors: '#5e72e4'
      };
      var trendLineData = {
        labels: <?php echo json_encode($trendLabels); ?>,
        tickets: <?php echo json_encode($trendTicketCounts); ?>,
        bugs: <?php echo json_encode($trendBugCounts); ?>
      };

      function sumValues(values) {
        var total = 0;
        for (var i = 0; i < values.length; i++) {
          total += Number(values[i]) || 0;
        }
        return total;
      }

      function percentOf(value, total) {
        if (!total) {
          return 0;
        }
        return Math.round((Number(value) / total) * 1000) / 10;
      }

      function formatPercent(value, total) {
        var pct = percentOf(value, total);
        if (pct === 0 && Number(value) > 0) {
          return '<0.1';
        }
        if (Math.abs(pct - Math.round(pct)) < 0.05) {
          return String(Math.round(pct));
        }
        return pct.toFixed(1);
      }

      function formatCountPercent(value, total) {
        return value + ' (' + formatPercent(value, total) + '%)';
      }

      function tooltipCountPercent(tooltipItem, data) {
        var idx = tooltipItem.index;
        var label = data.labels[idx] || '';
        var values = data.datasets[tooltipItem.datasetIndex].data;
        var value = values[idx];
        return label + ': ' + formatCountPercent(value, sumValues(values));
      }

      function legendWithCountPercent(chart) {
        var data = chart.data;
        if (!data.labels || !data.datasets.length) {
          return [];
        }
        var values = data.datasets[0].data;
        var total = sumValues(values);
        return data.labels.map(function(label, i) {
          return {
            text: label + ' — ' + formatCountPercent(values[i], total),
            fillStyle: data.datasets[0].backgroundColor[i],
            hidden: false,
            index: i
          };
        });
      }

      function renderDoughnut(canvasId, chartData) {
        var el = document.getElementById(canvasId);
        if (!el) return;
        new Chart(el.getContext('2d'), {
          type: 'doughnut',
          data: {
            labels: chartData.labels,
            datasets: [{
              data: chartData.values,
              backgroundColor: chartData.colors
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
              position: 'right',
              labels: {
                boxWidth: 16,
                fontSize: 13,
                padding: 14,
                generateLabels: legendWithCountPercent
              }
            },
            tooltips: {
              callbacks: { label: tooltipCountPercent }
            }
          },
          plugins: [{
            afterDatasetsDraw: function(chart) {
              var ctx = chart.ctx;
              chart.data.datasets.forEach(function(dataset, i) {
                var meta = chart.getDatasetMeta(i);
                if (!meta || meta.hidden) {
                  return;
                }
                var total = sumValues(dataset.data);
                meta.data.forEach(function(element, index) {
                  var value = Number(dataset.data[index]) || 0;
                  var percent = percentOf(value, total);
                  if (!value || percent < 4) {
                    return;
                  }
                  var pos = element.tooltipPosition();
                  ctx.fillStyle = '#fff';
                  ctx.strokeStyle = '#222';
                  ctx.lineWidth = 3;
                  ctx.font = 'bold 12px sans-serif';
                  ctx.textAlign = 'center';
                  ctx.textBaseline = 'middle';
                  var text = formatCountPercent(value, total);
                  ctx.strokeText(text, pos.x, pos.y);
                  ctx.fillText(text, pos.x, pos.y);
                });
              });
            }
          }]
        });
      }

      function renderHorizontalBar(canvasId, chartData, barColor) {
        var el = document.getElementById(canvasId);
        if (!el) return;
        new Chart(el.getContext('2d'), {
          type: 'horizontalBar',
          data: {
            labels: chartData.labels,
            datasets: [{
              label: 'Tickets',
              data: chartData.values,
              backgroundColor: barColor || chartData.colors
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            tooltips: {
              callbacks: { label: tooltipCountPercent }
            },
            layout: {
              padding: { left: 8, right: 70, top: 8, bottom: 8 }
            },
            scales: {
              xAxes: [{
                ticks: {
                  beginAtZero: true,
                  fontSize: 12
                }
              }],
              yAxes: [{
                barPercentage: 0.7,
                categoryPercentage: 0.8,
                ticks: {
                  autoSkip: false,
                  fontSize: 12,
                  fontColor: '#333'
                },
                gridLines: { display: false }
              }]
            }
          },
          plugins: [{
            afterDatasetsDraw: function(chart) {
              var ctx = chart.ctx;
              chart.data.datasets.forEach(function(dataset, i) {
                var meta = chart.getDatasetMeta(i);
                if (!meta || meta.hidden) {
                  return;
                }
                var total = sumValues(dataset.data);
                meta.data.forEach(function(bar, index) {
                  var value = Number(dataset.data[index]) || 0;
                  if (!value) {
                    return;
                  }
                  ctx.fillStyle = '#222';
                  ctx.font = '12px sans-serif';
                  ctx.textBaseline = 'middle';
                  ctx.textAlign = 'left';
                  ctx.fillText(formatCountPercent(value, total), bar._model.x + 8, bar._model.y);
                });
              });
            }
          }]
        });
      }

      renderDoughnut('forwardedOverviewChart', forwardedData);
      renderDoughnut('platformChart', platformData);
      renderDoughnut('platformBugChart', platformBugData);
      renderDoughnut('ticketMixChart', ticketMixData);
      renderHorizontalBar('issueTypeChart', issueTypeData, issueTypeData.colors);
      renderHorizontalBar('bugModuleChart', bugModuleData, bugModuleData.colors);

      var topCompanyEl = document.getElementById('topCompanyChart');
      if (topCompanyEl && topCompanyData.labels.length) {
        new Chart(topCompanyEl.getContext('2d'), {
          type: 'horizontalBar',
          data: {
            labels: topCompanyData.labels,
            datasets: [{
              label: 'Tickets',
              data: topCompanyData.tickets,
              backgroundColor: '#3498db'
            }, {
              label: 'Bugs',
              data: topCompanyData.bugs,
              backgroundColor: '#e74c3c'
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
              position: 'bottom',
              labels: { boxWidth: 14, fontSize: 12, padding: 12 }
            },
            scales: {
              xAxes: [{
                ticks: { beginAtZero: true, fontSize: 11 },
                gridLines: { display: true }
              }],
              yAxes: [{
                ticks: { fontSize: 11 },
                gridLines: { display: false }
              }]
            }
          }
        });
      }

      var trendEl = document.getElementById('trendLineChart');
      if (trendEl && trendLineData.labels.length) {
        new Chart(trendEl.getContext('2d'), {
          type: 'line',
          data: {
            labels: trendLineData.labels,
            datasets: [{
              label: 'Tickets Created',
              data: trendLineData.tickets,
              borderColor: '#3498db',
              backgroundColor: 'rgba(52, 152, 219, 0.12)',
              pointBackgroundColor: '#3498db',
              pointRadius: 4,
              fill: false,
              lineTension: 0.25
            }, {
              label: 'Bugs',
              data: trendLineData.bugs,
              borderColor: '#e74c3c',
              backgroundColor: 'rgba(231, 76, 60, 0.12)',
              pointBackgroundColor: '#e74c3c',
              pointRadius: 4,
              fill: false,
              lineTension: 0.25
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
              position: 'bottom',
              labels: { boxWidth: 16, fontSize: 13, padding: 14 }
            },
            tooltips: {
              mode: 'index',
              intersect: false
            },
            scales: {
              xAxes: [{
                ticks: {
                  autoSkip: true,
                  maxRotation: 45,
                  minRotation: 0,
                  fontSize: 11
                },
                gridLines: { display: false }
              }],
              yAxes: [{
                ticks: {
                  beginAtZero: true,
                  fontSize: 12
                }
              }]
            }
          }
        });
      }
    });
  </script>