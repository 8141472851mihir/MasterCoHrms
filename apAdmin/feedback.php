<?php
extract(array_map("test_input", $_REQUEST));
error_reporting(0);
$moduleTypes = [
  0 => "Other",
  1 => "My Visits",
  2 => "Sales & Order",
  3 => "Work Report",
  4 => "Leave",
  5 => "Payroll",
  6 => "Attendance",
  7 => "Tasks",
  8 => "Tax Exemption",
  9 => "Performance Matrix",
  10 => "CRM",
  11 => "Tracking",
  12 => "Biometric Attendance",
  13 => "Face App",
  14 => "Loan",
  15 => "Expense",
  16 => "Advance Expense",
  17 => "Advance Salary",
  18 => "Holiday",
  19 => "Employee Document & Letter",
  20 => "App Not Opening",
  21 => "Log Issue",
  22 => "Management",
  23 => "Employee",
  24 => "Server Down",
  25 => "Server Timeout",
  26 => "Third Party Integration",
  27 => "Chat",
  28 => "Timeline",
  29 => "Assets",
  30 => "Site Management",
  31 => "Penalty",
];
$module_type_filter = $module_type_filter ?? "All";
$project_type_filter = $_GET['project_type_filter'] ?? 'all';
$allowedProjectTypeFilters = ['all', 'myco', 'smart_society', 'my_association'];
if ($project_type_filter === 'non_whitelabel') {
  $project_type_filter = 'myco';
}
if (!in_array($project_type_filter, $allowedProjectTypeFilters, true)) {
  $project_type_filter = 'all';
}
$support_person_filter = trim((string)($_GET['support_person_filter'] ?? ($support_person_filter ?? 'all')));
if ($support_person_filter === '' || strcasecmp($support_person_filter, 'All') === 0) {
  $support_person_filter = 'all';
}
$created_by_filter = trim((string)($_GET['created_by_filter'] ?? ($created_by_filter ?? 'all')));
if ($created_by_filter === '' || strcasecmp($created_by_filter, 'All') === 0) {
  $created_by_filter = 'all';
}
$createdByAdmins = [];
$createdByAdminQ = $d->select1("bms_admin_master", "admin_id, admin_name, active_status", "admin_id >= 1 AND admin_name IS NOT NULL AND admin_name != ''", "ORDER BY admin_name ASC");
if ($createdByAdminQ) {
  while ($adminRow = mysqli_fetch_assoc($createdByAdminQ)) {
    $adminId = (int)($adminRow['admin_id'] ?? 0);
    $adminName = trim((string)($adminRow['admin_name'] ?? ''));
    if ($adminId < 1 || $adminName === '') {
      continue;
    }
    $createdByAdmins[] = [
      'id' => $adminId,
      'name' => $adminName,
      'active_status' => $adminRow['active_status'] ?? '',
    ];
  }
}
$supportPersonNames = [];
$supportPersonQ = $d->select1("society_master", "support_name", "support_name IS NOT NULL AND support_name != ''", "ORDER BY support_name ASC");
if ($supportPersonQ) {
  while ($spRow = mysqli_fetch_assoc($supportPersonQ)) {
    $spName = trim((string)($spRow['support_name'] ?? ''));
    if ($spName === '' || strcasecmp($spName, 'all') === 0 || strcasecmp($spName, 'other') === 0) {
      continue;
    }
    $supportPersonNames[] = $spName;
  }
}
$feedbackFilterHiddens = $d->feedbackListHiddenInputs();
$feedbackFilterSuffix = $d->feedbackListQuerySuffix();
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row align-items-center pt-2 pb-2">
      <div class="col-12 col-md-6 col-xl-3 mb-2 mb-xl-0">
        <h4 class="page-title mb-0">App Feedback</h4>
      </div>
      <div class="col-12 col-md-6 col-xl-9 mb-2 mb-xl-0 text-md-right">
        <div class="btn-group btn-group-sm" role="group">
          <a href="javascript:void(0)" data-toggle="modal" data-target="#feedbackOverdueSettingsModal" class="btn btn-info" title="Settings"><i class="fa fa-gear"></i></a>
          <a href="javascript:void(0)" data-toggle="modal" data-target="#feedbackAdd" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
          <a href="javascript:void(0)" onclick="DeleteAll('deleteFeedback');" class="btn btn-danger"><i class="fa fa-trash-o"></i> Delete</a>
        </div>
      </div>
    </div>
    <div class="row pb-2">
      <div class="col-6 col-md-4 col-xl mb-2 mb-xl-0">
        <form action="" method="get" accept-charset="utf-8">
          <select type="text" required="" id="country_id" onchange="this.form.submit()" class="form-control single-select" name="Status">
            <option <?php if (isset($_GET['Status']) && $_GET['Status'] == 0) {
                      echo "selected";
                    } ?> value="0">All</option>
            <option <?php if (isset($_GET['Status']) && $_GET['Status'] == 1) {
                      echo "selected";
                    } ?> value="1">Pending</option>
            <option <?php if (isset($_GET['Status']) && $_GET['Status'] == 2) {
                      echo "selected";
                    } ?> value="2">Close by Developer</option>
            <option <?php if (isset($_GET['Status']) && $_GET['Status'] == 3) {
                      echo "selected";
                    } ?> value="3">Reject by Developer</option>
            <option <?php if (isset($_GET['Status']) && $_GET['Status'] == 4) {
                      echo "selected";
                    } ?> value="4">In Progress</option>
          </select>
          <input type="hidden" name="platform_filter" value="<?php echo $_GET['platform_filter']; ?>">
          <input type="hidden" name="module_type_filter" value="<?php echo $_GET['module_type_filter']; ?>">
          <input type="hidden" name="created_by_filter" value="<?php echo $_GET['created_by_filter']; ?>">
          <input type="hidden" name="project_type_filter" value="<?php echo htmlspecialchars($project_type_filter); ?>">
          <input type="hidden" name="support_person_filter" value="<?php echo htmlspecialchars($support_person_filter); ?>">
          <input type="hidden" name="tab" value="<?php echo $_GET['tab']; ?>">
        </form>
      </div>
      <div class="col-6 col-md-4 col-xl mb-2 mb-xl-0">
        <form action="" method="get" accept-charset="utf-8">
          <input type="hidden" name="Status" value="<?php echo $_GET['Status']; ?>">
          <input type="hidden" name="module_type_filter" value="<?php echo $_GET['module_type_filter']; ?>">
          <input type="hidden" name="created_by_filter" value="<?php echo $_GET['created_by_filter']; ?>">
          <input type="hidden" name="project_type_filter" value="<?php echo htmlspecialchars($project_type_filter); ?>">
          <input type="hidden" name="support_person_filter" value="<?php echo htmlspecialchars($support_person_filter); ?>">
          <input type="hidden" name="tab" value="<?php echo $_GET['tab']; ?>">
          <select type="text" required="" id="platform_filter" onchange="this.form.submit()" class="form-control single-select" name="platform_filter">
            <option <?php if (isset($_GET['platform_filter']) && $_GET['platform_filter'] == 0) {
                      echo "selected";
                    } ?> value="0">All</option>
            <option <?php if (isset($_GET['platform_filter']) && $_GET['platform_filter'] == 1) {
                      echo "selected";
                    } ?> value="1">Android</option>
            <option <?php if (isset($_GET['platform_filter']) && $_GET['platform_filter'] == 2) {
                      echo "selected";
                    } ?> value="2">iOS</option>
            <option <?php if (isset($_GET['platform_filter']) && $_GET['platform_filter'] == 3) {
                      echo "selected";
                    } ?> value="3">Web</option>
            <option <?php if (isset($_GET['platform_filter']) && $_GET['platform_filter'] == 5) {
                      echo "selected";
                    } ?> value="5">CRM</option>
            <option <?php if (isset($_GET['platform_filter']) && $_GET['platform_filter'] == 4) {
                      echo "selected";
                    } ?> value="4">Other</option>
          </select>
        </form>
      </div>
      <div class="col-6 col-md-4 col-xl mb-2 mb-xl-0">
        <form action="" method="get" accept-charset="utf-8">
          <input type="hidden" name="Status" value="<?php echo $_GET['Status']; ?>">
          <input type="hidden" name="platform_filter" value="<?php echo $_GET['platform_filter']; ?>">
          <input type="hidden" name="created_by_filter" value="<?php echo $_GET['created_by_filter']; ?>">
          <input type="hidden" name="project_type_filter" value="<?php echo htmlspecialchars($project_type_filter); ?>">
          <input type="hidden" name="support_person_filter" value="<?php echo htmlspecialchars($support_person_filter); ?>">
          <input type="hidden" name="tab" value="<?php echo $_GET['tab']; ?>">
          <select id="module_type_filter" onchange="this.form.submit()" class="form-control single-select" name="module_type_filter">
            <option <?php if (isset($_GET['module_type_filter']) && $_GET['module_type_filter'] == 0) {
                      echo "selected";
                    } ?> value="All">All</option>
            <?php foreach ($moduleTypes as $value => $label): ?>
              <option <?php echo ($value == $module_type_filter) ? "selected" : ""; ?> value="<?php echo $value; ?>"><?php echo $label; ?></option>
            <?php endforeach; ?>
          </select>
        </form>
      </div>
      <div class="col-6 col-md-4 col-xl mb-2 mb-xl-0">
        <form action="" method="get" accept-charset="utf-8">
          <input type="hidden" name="Status" value="<?php echo $_GET['Status']; ?>">
          <input type="hidden" name="module_type_filter" value="<?php echo $_GET['module_type_filter']; ?>">
          <input type="hidden" name="platform_filter" value="<?php echo $_GET['platform_filter']; ?>">
          <input type="hidden" name="project_type_filter" value="<?php echo htmlspecialchars($project_type_filter); ?>">
          <input type="hidden" name="support_person_filter" value="<?php echo htmlspecialchars($support_person_filter); ?>">
          <input type="hidden" name="tab" value="<?php echo $_GET['tab']; ?>">
          <select type="text" required="" id="" onchange="this.form.submit()" class="form-control single-select" name="created_by_filter">
            <option value="all" <?php echo $created_by_filter === 'all' ? 'selected' : ''; ?>>All</option>
            <option value="0" <?php echo $created_by_filter === '0' ? 'selected' : ''; ?>>Client</option>
            <option value="1" <?php echo $created_by_filter === '1' ? 'selected' : ''; ?>>Support</option>
            <?php foreach ($createdByAdmins as $createdByAdmin) { ?>
              <option value="<?php echo (int)$createdByAdmin['id']; ?>" <?php echo $created_by_filter === (string)$createdByAdmin['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($createdByAdmin['name']); ?><?php echo (string)$createdByAdmin['active_status'] !== '0' ? ' (Deactive)' : ''; ?></option>
            <?php } ?>
          </select>
        </form>
      </div>
      <div class="col-6 col-md-4 col-xl mb-2 mb-xl-0">
        <form action="" method="get" accept-charset="utf-8">
          <input type="hidden" name="Status" value="<?php echo $_GET['Status']; ?>">
          <input type="hidden" name="platform_filter" value="<?php echo $_GET['platform_filter']; ?>">
          <input type="hidden" name="module_type_filter" value="<?php echo $_GET['module_type_filter']; ?>">
          <input type="hidden" name="created_by_filter" value="<?php echo $_GET['created_by_filter']; ?>">
          <input type="hidden" name="support_person_filter" value="<?php echo htmlspecialchars($support_person_filter); ?>">
          <input type="hidden" name="tab" value="<?php echo $_GET['tab']; ?>">
          <select id="project_type_filter" onchange="this.form.submit()" class="form-control single-select" name="project_type_filter">
            <option value="all" <?php echo $project_type_filter === 'all' ? 'selected' : ''; ?>>All</option>
            <option value="myco" <?php echo $project_type_filter === 'myco' ? 'selected' : ''; ?>>Myco</option>
            <option value="smart_society" <?php echo $project_type_filter === 'smart_society' ? 'selected' : ''; ?>>Smart Society</option>
            <option value="my_association" <?php echo $project_type_filter === 'my_association' ? 'selected' : ''; ?>>My Association</option>
          </select>
        </form>
      </div>
      <div class="col-6 col-md-4 col-xl mb-2 mb-xl-0">
        <form action="" method="get" accept-charset="utf-8">
          <input type="hidden" name="Status" value="<?php echo $_GET['Status']; ?>">
          <input type="hidden" name="platform_filter" value="<?php echo $_GET['platform_filter']; ?>">
          <input type="hidden" name="module_type_filter" value="<?php echo $_GET['module_type_filter']; ?>">
          <input type="hidden" name="created_by_filter" value="<?php echo $_GET['created_by_filter']; ?>">
          <input type="hidden" name="project_type_filter" value="<?php echo htmlspecialchars($project_type_filter); ?>">
          <input type="hidden" name="tab" value="<?php echo $_GET['tab']; ?>">
          <select id="support_person_filter" onchange="this.form.submit()" class="form-control single-select" name="support_person_filter">
            <option value="all" <?php echo strcasecmp($support_person_filter, 'all') === 0 ? 'selected' : ''; ?>>All</option>
            <option value="other" <?php echo strcasecmp($support_person_filter, 'other') === 0 ? 'selected' : ''; ?>>Other</option>
            <?php foreach ($supportPersonNames as $spName) { ?>
              <option value="<?php echo htmlspecialchars($spName); ?>" <?php echo $support_person_filter === $spName ? 'selected' : ''; ?>><?php echo htmlspecialchars($spName); ?></option>
            <?php } ?>
          </select>
        </form>
      </div>
    </div>
    <!-- End Breadcrumb-->

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5>
              <?php
              $unreadCount =  $d->count_data_direct("feedback_id", "feedback_master", "society_id IS NOT NULL AND society_id > 0 AND feedback_status!='2' AND inquiry_type='0' AND read_by=0");
              if ($unreadCount > 0) { ?>
                <p style="color:#c82333; font-weight:500; font-size:16px; margin-bottom:10px;">
                  There are <strong><?= $unreadCount ?></strong> unread tickets awaiting review.
                </p>

              <?php } ?>
            </h5>
            <ul class="nav nav-tabs nav-tabs-info nav-justified">
              <?php
              error_reporting(0);
              extract($_REQUEST);
              $pending_table = "";
              $withDeveloper = "";
              $solved_table = "";
              $need_spec_table = "";
              if ($tab == 0) {
                $pending_table = "active";
              } else if ($tab == 1) {
                $withDeveloper = "active";
              } else if ($tab == 2) {
                $solved_table = "active";
              } else if ($tab == 3) {
                $need_spec_table = "active";
              } else if ($tab == 4) {
                $next_update_table = "active";
              } else {
                $pending_table = "active";
              }
              $where = "";
              if (isset($_GET['Status']) && $_GET['Status'] == 0) {
                $where .= "";
              } elseif (isset($_GET['Status']) && $_GET['Status'] == 1) {
                $where .= "AND feedback_master.feedback_status='0'";
              } elseif (isset($_GET['Status']) && $_GET['Status'] == 2) {
                $where .= "AND feedback_master.feedback_status='5'";
              } elseif (isset($_GET['Status']) && $_GET['Status'] == 3) {
                $where .= "AND feedback_master.feedback_status='6'";
              } elseif (isset($_GET['Status']) && $_GET['Status'] == 4) {
                $where .= "AND feedback_master.feedback_status='1'";
              }

              if (isset($_GET['platform_filter']) && $_GET['platform_filter'] == 0) {
                $where .= "";
              } elseif (isset($_GET['platform_filter']) && $_GET['platform_filter'] == 1) {
                $where .= "AND feedback_master.platform='1'";
              } elseif (isset($_GET['platform_filter']) && $_GET['platform_filter'] == 2) {
                $where .= "AND feedback_master.platform='2'";
              } elseif (isset($_GET['platform_filter']) && $_GET['platform_filter'] == 3) {
                $where .= "AND feedback_master.platform='3'";
              } elseif (isset($_GET['platform_filter']) && $_GET['platform_filter'] == 4) {
                $where .= "AND feedback_master.platform='0'";
              } elseif (isset($_GET['platform_filter']) && $_GET['platform_filter'] == 5) {
                $where .= "AND feedback_master.platform='4'";
              }

              if ($created_by_filter !== 'all' && ctype_digit($created_by_filter)) {
                $createdById = (int) $created_by_filter;
                $where .= " AND feedback_master.created_by='$createdById'";
              }

              if ($project_type_filter === 'myco') {
                $where .= " AND (COALESCE(feedback_master.is_whitelabel, 0) = 0 OR (COALESCE(feedback_master.is_whitelabel, 0) = 1 AND COALESCE(feedback_master.whitelabel_type, 0) = 0))";
              } elseif ($project_type_filter === 'smart_society') {
                $where .= " AND COALESCE(feedback_master.is_whitelabel, 0) = 1 AND COALESCE(feedback_master.whitelabel_type, 0) = 1";
              } elseif ($project_type_filter === 'my_association') {
                $where .= " AND COALESCE(feedback_master.is_whitelabel, 0) = 1 AND COALESCE(feedback_master.whitelabel_type, 0) = 2";
              }

              ?>

              <li class="nav-item">
                <a class="nav-link <?php echo $pending_table; ?>" data-toggle="tab" href="#pending_tab" data-tab-value="0"><i class="fa fa-spinner"></i> <span>With Support Team</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $need_spec_table; ?>" data-toggle="tab" href="#need_spec_table" data-tab-value="3"><i class="fa fa-clock-o"></i> <span>Need More Specification</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $next_update_table; ?>" data-toggle="tab" href="#next_update_table" data-tab-value="4"><i class="fa fa-step-forward"></i> <span>Resolved in next update</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $withDeveloper; ?>" data-toggle="tab" href="#withDeveloper_tab" data-tab-value="1"><i class="fa fa-user"></i> <span>With Developer</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $solved_table; ?>" data-toggle="tab" href="#solved_tab" data-tab-value="2"><i class="fa fa-check"></i> <span>Solved</span></a>
              </li>
            </ul>
            <div class="tab-content">
              <div id="pending_tab" class="tab-pane <?php echo $pending_table; ?> show">
                <div class="my_pending_data">
                </div>
                <div class="table-responsive">
                  <table id="pending_table_feedback" class="table table-bordered pending_table_feedback">
                    <thead>
                      <tr>
                        <th class="deleteTh">#</th>
                        <th>#</th>
                        <th>Action</th>
                        <th>Id</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Company Name</th>
                        <th>Support Person Name</th>
                        <th>Name</th>
                        <th>Platform</th>
                        <th>Issue Type</th>
                        <th>Module Type</th>
                        <th>Created Date</th>
                        <th>Created By</th>
                      </tr>
                    </thead>
                  </table>
                </div>
                <div id="otherContaint" class="d-none">
                  <hr class="my-5">
                  <h5>With Support Team Feedback Report</h5>
                  <table id="adminTable" class="table table-bordered mt-5">
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
              <?php if ($tab = 'need_spec_table') { ?>
                <div id="need_spec_table" class=" tab-pane <?php echo $need_spec_table; ?> fade show">
                  <div class="my_need_spec_data">
                  </div>
                  <div class="table-responsive">
                    <table id="need_spec_table_feedback" class="table table-bordered need_spec_table_feedback">
                      <thead>
                        <tr>
                          <th class="deleteTh">#</th>
                          <th>#</th>
                          <th>Action</th>
                          <th>Id</th>
                          <th>Subject</th>
                          <th>Platform</th>
                          <th>Status</th>
                          <th>Company Name</th>
                          <th>Support Person Name</th>
                          <th>Name</th>
                          <th>Created Date</th>
                          <th>Assign Date</th>
                          <th>Created By</th>
                          <th>Reopen Remarks</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                  <div id="otherContaint1" class="d-none">
                    <hr class="my-5">
                    <h5>With Support Team Feedback Report</h5>
                    <table id="adminTable1" class="table table-bordered mt-5">
                      <thead>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              <?php } ?>
              <?php if ($tab = 'next_update_table') { ?>
                <div id="next_update_table" class=" tab-pane <?php echo $next_update_table; ?> fade show">
                  <div class="my_next_update_data">
                  </div>
                  <div class="table-responsive">
                    <table id="next_update_table_feedback" class="table table-bordered next_update_table_feedback">
                      <thead>
                        <tr>
                          <th class="deleteTh">#</th>
                          <th>#</th>
                          <th>Action</th>
                          <th>Id</th>
                          <th>Subject</th>
                          <th>Platform</th>
                          <th>Status</th>
                          <th>Company Name</th>
                          <th>Support Person Name</th>
                          <th>Name</th>
                          <th>Created Date</th>
                          <th>Assign Date</th>
                          <th>Created By</th>
                          <th>Reopen Remarks</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              <?php } ?>
              <?php if ($tab = 'withDeveloper_tab') { ?>
                <div id="withDeveloper_tab" class=" tab-pane <?php echo $withDeveloper; ?> fade show">
                  <div class="my_withDeveloper_data">
                  </div>
                  <div class="table-responsive">
                    <table id="withDeveloper_table_feedback" class="table table-bordered withDeveloper_table_feedback">
                      <thead>
                        <tr>
                          <th class="deleteTh">#</th>
                          <th>#</th>
                          <th>Action</th>
                          <th>Id</th>
                          <th>Subject</th>
                          <th>Platform</th>
                          <th>Module Type</th>
                          <th>Status</th>
                          <th>Company Name</th>
                          <th>Support Person Name</th>
                          <th>Name</th>
                          <th>Created Date</th>
                          <th>Assign Date</th>
                          <th>Created By</th>
                          <th>Reopen Remarks</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              <?php } ?>
              <?php if ($tab = 'solved_tab') { ?>
                <div id="solved_tab" class=" tab-pane <?php echo $solved_table; ?> fade show">
                  <div class="table-responsive">
                    <table id="solved_table_feedback" class="table table-bordered solved_table_feedback">
                      <thead>
                        <tr>
                          <th class="deleteTh">#</th>
                          <th>#</th>
                          <th>Action</th>
                          <th>Id</th>
                          <th class="tableWidth">Company Name</th>
                          <th class="tableWidth">Support Person Name</th>
                          <th class="">Subject</th>
                          <th class="tableWidth">Name</th>
                          <th class="tableWidth">Platform</th>
                          <th class="tableWidth">Module Type</th>
                          <th class="tableWidth">Close Remarks</th>
                          <?php if ($role_id == 1) { ?>
                            <th class="tableWidth">Reply Message</th>
                          <?php } ?>
                          <th>Created By</th>
                          <th>Create Date Time</th>
                          <th>Close Date Time</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="closebyDeveloper">
    <div class="modal-dialog modal-xl">
      <div class="modal-content border-primary">
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white">Developer Reply Feedback</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="developerSetReplyForm">
          <form id="developerReplyFeedbackFrm" action="controller/feedbackController.php" method="post" enctype="multipart/form-data">
            <?php echo $feedbackFilterHiddens; ?>
            <input type="hidden" name="closebyDeveloper">
            <input type="hidden" id="developerSociety_id" name="society_id">
            <input type="hidden" id="developerFeedback_id" name="feedback_id">
            <input type="hidden" id="developerFeedback_created_by" name="feedback_created_by">
            <input type="hidden" id="csrf" name="csrf" value="<?php echo $csrf; ?>">
            <div class="form-group row">
              <label for="input-10" class="col-sm-2 col-form-label">Reply</label>
              <div class="col-sm-10">
                <textarea class="form-control" rows="6" cols="30" maxlength="2500" style="resize: vertical;" id="dev_reply" name="reply"></textarea>
              </div>
            </div>
            <div class="form-group row">
              <label for="input-10" class="col-sm-2 col-form-label">Attachment File</label>
              <div class="col-sm-10">
                <input class="form-control-file border" type="file" name="attachment">
              </div>
            </div>
            <div class="form-group row">
              <label for="input-10" class="col-sm-2 col-form-label">Send Mail</label>
              <div class="col-sm-10">
                <select class="form-control single-select" name="send_mail">
                  <option value="0">Client and Support Executive</option>
                  <option value="1">Support Executive</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="input-10" class="col-sm-2 col-form-label">Issue <span class="required">*</span></label>
              <div class="col-sm-10">
                <select class="form-control single-select" name="issue_type" required>
                  <option value="">---Select---</option>
                  <option value="1">Bug</option>
                  <option value="2">Configuration Issue</option>
                  <option value="3">Training Issue</option>
                  <option value="4">Issue Not Found</option>
                  <option value="5">Change Request by Client</option>
                  <option value="6">Device Specific Issue</option>
                  <option value="7">Data Delete Request</option>
                  <option value="8">Not an issue</option>
                  <option value="9">Internet Connectivity Issue</option>
                </select>
              </div>
            </div>
            <div class="form-footer text-center">
              <input type="hidden" name="previousURL" value="feedback">
              <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i>Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="rejectbyDeveloper">
    <div class="modal-dialog modal-lg">
      <div class="modal-content border-primary">
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white">Developer Reject Feedback</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="RejectByDeveloperFeedbackFrm" action="controller/feedbackController.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="society_id" name="society_id" value="<?php echo $society_id; ?>">
            <input type="hidden" id="developer_feedback_id" name="feedback_id">
            <input type="hidden" id="developer_feedback_email" name="feedback_email">
            <div class="form-group row">
              <label for="input-10" class="col-sm-4 col-form-label">Reason <span class="required">*</span></label>
              <div class="col-sm-8">
                <textarea placeholder="Please mention how to reject this query" class="form-control" rows="6" cols="30" maxlength="2500" style="resize: vertical;" required="" name="resion"></textarea>
              </div>
            </div>
            <div class="form-group row">
              <label for="input-10" class="col-sm-4 col-form-label">Attachment </label>
              <div class="col-sm-8">
                <input type="file" accept="image/*,.pdf" id="reject_attachment" name="reject_attachment" class="form-control">
              </div>
            </div>
            <div class="form-footer text-center">
              <?php echo $feedbackFilterHiddens; ?>
              <input type="hidden" name="rejectbyDeveloper" value="rejectbyDeveloper">
              <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="replyModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content border-primary">
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white">Reply Feedback</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="setReplyForm">
        </div>
      </div>
    </div>
  </div><!--End Modal -->

  <div class="modal fade" id="closeRemarksModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content border-primary">
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white">Closing Remarks</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="remarkFeedbackFrm" action="controller/feedbackController.php" method="POST">
            <input type="hidden" id="society_id" name="society_id" value="<?php echo $society_id; ?>">
            <input type="hidden" id="feedback_remark_id" name="feedback_id">
            <input type="hidden" id="feedback_remark_email" name="feedback_email">
            <div class="form-group row">
              <label for="input-10" class="col-sm-4 col-form-label">Close By <span class="required">*</span></label>
              <div class="col-sm-8">
                <select class="form-control single-select" required="" name="closing_by">
                  <option value="">-- Select --</option>
                  <option value="By Whatsapp">By Whatsapp</option>
                  <option value="By Call">By Call</option>
                  <option value="By Text Message">By Text Message</option>
                  <option value="By Mail">By Mail</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="input-10" class="col-sm-4 col-form-label">Enter Last Message <span class="required">*</span></label>
              <div class="col-sm-8">
                <textarea placeholder="Please mention how to this query resolved" class="form-control" rows="6" cols="30" maxlength="2500" style="resize: vertical;" required="" name="closing_remarks" id="closing_remarks"></textarea>
              </div>
            </div>
            <div class="form-footer text-center">
              <?php echo $feedbackFilterHiddens; ?>
              <input type="hidden" name="remarkFeedback" value="remarkFeedback">
              <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="closeGeneratedTicketsWarningModal" tabindex="-1" role="dialog" aria-labelledby="closeGeneratedTicketsWarningTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-warning">
        <div class="modal-header bg-warning">
          <h5 class="modal-title" id="closeGeneratedTicketsWarningTitle">Pending Action Required from your end</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>You have tickets that you generated and still need action from your end:</p>
          <ul>
            <li>Closed by Developer: <strong id="closeGeneratedTicketsCount">0</strong></li>
            <li>Rejected by Developer: <strong id="rejectedByDeveloperCount">0</strong></li>
          </ul>
          <p class="mb-0">Please close your generated tickets to maintain proper TAT and stay up to date with the client.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="reopenRemarksModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content border-primary">
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white">Reopen Remarks</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="reopenRemarkFeedbackFrm" action="controller/feedbackController.php" method="POST">
            <input type="hidden" id="society_id" name="society_id" value="<?php echo $society_id; ?>">
            <input type="hidden" id="reopen_feedback_remark_id" name="feedback_id">
            <input type="hidden" id="reopen_feedback_remark_date" name="reopen_date_time">
            <div class="form-group row">
              <label for="input-10" class="col-sm-4 col-form-label">Reopen Remark<span class="required">*</span></label>
              <div class="col-sm-8">
                <textarea placeholder="Please mention how to this query reopen" class="form-control" rows="6" cols="30" maxlength="2500" style="resize: vertical;" required="" name="reopen_remarks"></textarea>
              </div>
            </div>
            <div class="form-footer text-center">
              <?php echo $feedbackFilterHiddens; ?>
              <input type="hidden" name="reOpenQuery" value="reOpenQuery">
              <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="ContentModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content border-primary">
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white">Message</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-sm-12" id="content"> </div>
          </div>
        </div>
      </div>
    </div>
  </div><!--End Modal -->

  <div class="modal fade" id="feedbackModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content border-primary">
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white">Feedback</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="infoDiv">
        </div>
      </div>
    </div>
  </div><!--End Modal -->

  <div class="modal fade" id="feedbackAdd">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content border-primary">
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white">Add New Query</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="addFeedbackForm" action="controller/feedbackController.php" method="post" enctype="multipart/form-data">
            <div class="form-group row" id="sourceTypeRow">
              <label for="ticket_source_type" class="col-sm-2 col-form-label">Source <span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <select required="" id="ticket_source_type" class="form-control single-select" name="source_type">
                  <option value="0" selected="">Normal</option>
                  <option value="1">Whitelabel</option>
                </select>
              </div>

              <label for="whitelabel_type" class="col-sm-2 col-form-label" id="whitelabelTypeLabel" style="display:none;">Whitelabel Type</label>
              <div class="col-sm-4" id="whitelabelTypeRow" style="display:none;">
                <select id="whitelabel_type" class="form-control single-select" name="whitelabel_type" disabled="">
                  <option value="">-- Select --</option>
                  <option value="0">MyCo</option>
                  <option value="1">Smart Society</option>
                  <option value="2">My Association</option>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="platform" class="col-sm-2 col-form-label"> Platform <span class="required">*</span></label>
              <div class="col-sm-4">
                <select required="" id="platform" class="form-control single-select" name="platform">
                  <option value="">-- Select --</option>
                  <option value="1">Android</option>
                  <option value="2">iOS</option>
                  <option value="3">Web</option>
                  <option value="4">CRM</option>
                  <option value="0">Other</option>
                </select>
              </div>
              <label for="input-10" class="col-sm-2 col-form-label">Company <span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <select class="form-control" id="companyId" name="companyId" required="">
                  <option value="">-- Select --</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
            </div>
            <div class="form-group row">
              <label for="input-10" class="col-sm-2 col-form-label">Employee Name <span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <input required="" maxlength="30" id="name" type="text" name="name" class="form-control">
              </div>
              <label for="input-10" class="col-sm-2 col-form-label">Mobile Number <span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <input required="" maxlength="15" id="mobile" type="text" name="mobile" class="form-control onlyNumber">
              </div>
            </div>
            <div class="form-group row">
              <label for="input-10" class="col-sm-2 col-form-label">Email<span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <input maxlength="100" id="email" type="email" name="email" class="form-control">
              </div>
              <label for="input-10" class="col-sm-2 col-form-label">Subject <span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <input required="" maxlength="100" id="subject" type="text" name="subject" class="form-control">
              </div>
            </div>
            <div class="form-group row">
              <label for="input-10" class="col-sm-2 col-form-label">Image 1</label>
              <div class="col-sm-4">
                <input type="file" accept="image/*" maxlength="500" name="attachment" id="attachment" class="form-control">
              </div>
              <label for="input-10" class="col-sm-2 col-form-label">Image 2</label>
              <div class="col-sm-4">
                <input type="file" accept="image/*" maxlength="500" name="attachment_2" id="attachment_2" class="form-control">
              </div>
            </div>
            <div class="form-group row">
              <label for="input-11" class="col-sm-2 col-form-label">Video </label>
              <div class="col-sm-4">
                <input accept="video/*" id="video" type="file" name="video" class="form-control">
              </div>
              <label for="input-12" class="col-sm-2 col-form-label">Documents </label>
              <div class="col-sm-4">
                <input accept="file/*" id="document" type="file" name="document" class="form-control">
              </div>
            </div>
            <div class="form-group row">
              <label for="input-10" class="col-sm-2 col-form-label">Description <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <textarea maxlength="500" required="" name="feedback_msg" class="form-control"></textarea>
              </div>
            </div>
            <div class="form-footer text-center">
              <?php echo $feedbackFilterHiddens; ?>
              <input type="hidden" name="addFeedback" value="addFeedback">
              <button type="submit" name="" value="" class="btn  btn-success"><i class="fa fa-check-square-o"></i> Send</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Forward to Developer Modal -->
<div class="modal fade" id="forwardToDeveloperModal">
  <div class="modal-dialog ">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Forward to Developer</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="controller/feedbackController.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="feedback_id" id="forward_feedback_id">
          <input type="hidden" name="society_id" id="forward_society_id">
          <input type="hidden" name="forwardtoDeveloper">
          <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
          <?php echo $feedbackFilterHiddens; ?>
          <div class="form-group">
            <label for="module_type">Module Type <span class="text-danger">*</span></label>
            <select id="module_type" class="form-control single-select" name="module_type" required>
              <option value="">Select Module</option>
              <?php foreach ($moduleTypes as $value => $label): ?>
                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-footer text-center">
          <button type="submit" name="" value="" class="btn  btn-primary"><i class="fa fa-check-square-o"></i> Forward</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="feedbackOverdueSettingsModal">
  <div class="modal-dialog ">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Feedback Overdue Settings</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="controller/feedbackController.php" method="POST" id="feedbackOverdueSettingsForm">
        <div class="modal-body">
          <?php $selectSetting = $d->select("feedback_overdue_settings", "feedback_overdue_id=1");
          if (mysqli_num_rows($selectSetting) > 0) {
            $overdueData = mysqli_fetch_array($selectSetting);
          } else {
            $overdueData = [
              'feedback_overdue_id' => '',
              'developer_feedback_overdue_hours' => '',
              'support_feedback_overdue_after_close_hours' => '',
              'support_feedback_overdue_new_ticket_hours' => '',
            ];
          }
          ?>
          <input type="hidden" name="feedback_overdue_id" value="<?php echo $overdueData['feedback_overdue_id']; ?>">
          <input type="hidden" name="feedbackOverdueSettings">
          <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
          <?php echo $feedbackFilterHiddens; ?>
          <div class="form-group">
            <label for="developer_hours">Developer Feedback Overdue Hours <span class="text-danger">*</span></label>
            <input type="text" min="1" class="form-control onlyNumber" name="developer_feedback_overdue_hours" value="<?php echo $overdueData['developer_feedback_overdue_hours']; ?>" required>
          </div>

          <div class="form-group">
            <label for="support_close_hours">Support Feedback (After Developer Close) Overdue Hours <span class="text-danger">*</span></label>
            <input type="text" min="1" class="form-control onlyNumber" id="" name="support_feedback_overdue_after_close_hours" value="<?php echo $overdueData['support_feedback_overdue_after_close_hours']; ?>" required>
          </div>

          <div class="form-group">
            <label for="support_new_hours">Support Feedback (New Ticket) Overdue Hours <span class="text-danger">*</span></label>
            <input type="text" min="1" class="form-control onlyNumber" id="" name="support_feedback_overdue_new_ticket_hours" value="<?php echo $overdueData['support_feedback_overdue_new_ticket_hours']; ?>" required>
          </div>

        </div>
        <div class="form-footer text-center">
          <button type="submit" name="" value="" class="btn  btn-primary"><i class="fa fa-check-square-o"></i><?php echo $overdueData['feedback_overdue_id'] == 1 ? 'Update' : 'Save'; ?></button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
  function DeveloperReply(feedback_id, email, society_id, created_by) {
    $("#developerSociety_id").val(society_id);
    $("#developerFeedback_id").val(feedback_id);
    $("#developerFeedback_created_by").val(created_by);
    $("#developerEmail").val(email);
  }

  function RejectByDeveloper(feedback_id, email) {
    $('#developer_feedback_id').val(feedback_id);
    $('#developer_feedback_email').val(email);
  }

  function remarkFeedback(feedback_id, email, name) {
    $('#feedback_remark_id').val(feedback_id);
    $('#feedback_remark_email').val(email);

  }

  function reopenRemarkFeedback(feedback_id, date) {
    $('#reopen_feedback_remark_id').val(feedback_id);
    $('#reopen_feedback_remark_date').val(date);
  }

  function infoFeedback(feedback_id) {
    $.ajax({
      url: "viewFeedbackMessage.php",
      cache: false,
      type: "POST",
      data: {
        feedback_id: feedback_id
      },
      success: function(response) {
        $('#infoDiv').html(response);
      }
    });
  }
</script>
<script type="text/javascript">
  var statusLabels = {
    0: "Pending",
    1: "In Progress",
    3: "On Hold",
    4: "Rejected",
    5: "Closed by Developer",
    6: "Rejected by Developer",
    7: "Need More Specification",
    8: "Resolved in Next Update"
  };

  var statusColors = {
    0: "badge-warning",
    1: "badge-default",
    3: "badge-warning",
    4: "badge-danger",
    5: "badge-success",
    6: "badge-danger",
    7: "badge-danger",
    8: "badge-danger"
  };
  $(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';
    var pending_table_feedback = $('#pending_table_feedback').DataTable({
      lengthChange: false,
      responsive: true,
      autoWidth: false,
      searching: true,
      ordering: true,
      processing: true,
      destroy: true,
      language: {
        lengthMenu: "_MENU_",
        processing: "<img src='../img/ajax-loader.gif' width='30px' class='bg-transparent'>",
        emptyTable: "NO DATA FOUND !"
      },
      lengthMenu: [
        [10, 20, 30, 50],
        [10, 20, 30, 50]
      ],
      pageLength: 50,
      'ajax': {
        url: './allFeedbacks.php',
        type: "POST",
        data: function(d) {
          d.module_type_filter = "<?php echo $module_type_filter; ?>";
          d.where = "<?php echo $where; ?>";
          d.Status = "<?php echo $Status; ?>";
          d.platform_filter = "<?php echo $platform_filter; ?>";
          d.created_by_filter = "<?php echo htmlspecialchars($_GET['created_by_filter'] ?? '', ENT_QUOTES); ?>";
          d.project_type_filter = "<?php echo htmlspecialchars($project_type_filter, ENT_QUOTES); ?>";
          d.support_person_filter = <?php echo json_encode($support_person_filter); ?>;
          d.tab = "<?php echo htmlspecialchars($_GET['tab'] ?? '', ENT_QUOTES); ?>";
          d.notifications = "<?php echo $notifications; ?>";
          d.role_id = "<?php echo $role_id; ?>";
          d.csrf = "<?php echo $_SESSION["token"]; ?>";
          d.countryAppendQuerySociety = "<?php echo $countryAppendQuerySociety; ?>";
          d.get_pending_tab_feedback = "get_pending_tab_feedback";
        },
        dataSrc: function(response) {
          var count_data = response.count_data;
          $('.my_pending_data').empty();
          Object.values(count_data).forEach(function(item) {
            var statusText = statusLabels[item.feedback_status] || "Unknown";
            var badgeColor = statusColors[item.feedback_status] || "badge-secondary";
            var badge = $('<span>')
              .addClass('badge')
              .addClass(badgeColor)
              .html(statusText + ' (' + item.count + ')');
            $('.my_pending_data').append(badge).append(' ');
          });
          var adminData = response.admin_wise_data || {};
          var closedByDevCount = parseInt(response.logged_in_closed_by_dev_count || 0, 10);
          var rejectedByDevCount = parseInt(response.logged_in_rejected_by_dev_count || 0, 10);
          var loggedInAdminId = String(response.logged_in_admin_id || '');
          if (closedByDevCount > 0 || rejectedByDevCount > 0) {
            $('#closeGeneratedTicketsCount').text(closedByDevCount);
            $('#rejectedByDeveloperCount').text(rejectedByDevCount);
            $('#closeGeneratedTicketsWarningModal').modal('show');
          }
          if (adminData && Object.keys(adminData).length > 0) {
            $('#otherContaint').removeClass('d-none');
          }
          $('#adminTable tbody').empty();
          $('#adminTable thead').empty();
          var uniqueStatuses = new Set();
          Object.values(adminData).forEach(admin => {
            Object.keys(admin.status_count || {}).forEach(status => {
              uniqueStatuses.add(String(status));
            });
          });
          uniqueStatuses.add('5');
          uniqueStatuses.add('6');
          var statusArray = Array.from(uniqueStatuses).filter(function(status) {
            return status !== '5' && status !== '6';
          });
          statusArray.push('5');
          statusArray.push('6');
          var headerHTML = `<tr>
              <th>ID</th>
              <th>Name</th>
              <th>Status</th>`;
          statusArray.forEach(status => {
            var statusText = statusLabels[status] || "Unknown";
            headerHTML += `<th>${statusText}</th>`;
          });
          headerHTML += `</tr>`;
          $('#adminTable thead').append(headerHTML);
          var i = 1;
          Object.values(adminData).forEach(admin => {
            if (admin.id == '0') {
              admin.name = "Client";
            }
            var currentClosedByDev = parseInt((admin.status_count && admin.status_count[5]) || 0, 10);
            var currentRejectedByDev = parseInt((admin.status_count && admin.status_count[6]) || 0, 10);
            var isCurrentAdmin = String(admin.id) === loggedInAdminId;
            var rowClass = (isCurrentAdmin && (currentClosedByDev > 0 || currentRejectedByDev > 0)) ? ' class="table-warning"' : '';
            var adminStatusHtml = '-';
            if (admin.id != '0' && admin.active_status !== '' && admin.active_status !== null && typeof admin.active_status !== 'undefined') {
              adminStatusHtml = String(admin.active_status) === '0'
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Deactive</span>';
            }
            var rowHTML = `<tr${rowClass}>
                <td>${i++}</td>
                <td>${admin.name || ''}</td>
                <td>${adminStatusHtml}</td>`;
            statusArray.forEach(status => {
              var count = (admin.status_count && admin.status_count[status]) || 0;
              if ((String(status) === '5' || String(status) === '6') && count > 0) {
                rowHTML += `<td class="text-danger font-weight-bold">${count}</td>`;
              } else {
                rowHTML += `<td>${count}</td>`;
              }
            });
            rowHTML += `</tr>`;
            $('#adminTable tbody').append(rowHTML);
          });
          if ($.fn.DataTable.isDataTable('#adminTable')) {
            $('#adminTable').DataTable().destroy();
          }
          if (Object.keys(adminData).length > 0) {
            $('#adminTable').DataTable({
              pageLength: 50,
              lengthMenu: [[10, 20, 30, 50], [10, 20, 30, 50]]
            });
          }
          return response.data;
        },
        'error': function(xhr, error, thrown) {
          console.log('Error:', error, thrown);
          $('#pending_table_feedback').DataTable().clear().draw();
          $('#pending_table_feedback tbody').html('<tr><td colspan="12" class="text-center">No data found</td></tr>');
        }
      },
      columns: [{
          "data": "abc"
        },
        {
          "data": "sr_no"
        },
        {
          "data": "action"
        },
        {
          "data": "feedback_id"
        },
        {
          "data": "subject",
          "render": function(data, type, row) {
            var feedback_msg = row.feedback_msg;
            return '<span data-toggle="tooltip" title="' + feedback_msg + '">' + data + '</span>';
          }
        },
        {
          "data": "status"
        },
        {
          "data": "company_name"
        },
        {
          "data": "support_person_name"
        },
        {
          "data": "name"
        },
        {
          "data": "platform"
        },
        {
          "data": "issue_type"
        },
        {
          "data": "module_type"
        },
        {
          "data": "created_date"
        },
        {
          "data": "created_by"
        }
      ],
      createdRow: function(row, data, dataIndex) {
        $('td:eq(6),td:eq(9),td:eq(10)', row).addClass('tableWidth');
      },
      'error': function(settings, helpPage, message) {
        console.log("DataTables error:", message);
      }
    });
  });
  $(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';
    var need_spec_table_feedback = $('#need_spec_table_feedback').DataTable({
      lengthChange: false,
      responsive: true,
      autoWidth: false,
      searching: true,
      ordering: true,
      processing: true,
      destroy: true,
      language: {
        lengthMenu: "_MENU_",
        processing: "<img src='../img/ajax-loader.gif' width='30px' class='bg-transparent'>",
        emptyTable: "NO DATA FOUND !"
      },
      lengthMenu: [
        [10, 20, 30, 50],
        [10, 20, 30, 50]
      ],
      pageLength: 50,
      'ajax': {
        url: './allFeedbacks.php',
        type: "POST",
        data: function(d) {
          d.module_type_filter = "<?php echo $module_type_filter; ?>";
          d.where = "<?php echo $where; ?>";
          d.Status = "<?php echo $Status; ?>";
          d.platform_filter = "<?php echo $platform_filter; ?>";
          d.created_by_filter = "<?php echo htmlspecialchars($_GET['created_by_filter'] ?? '', ENT_QUOTES); ?>";
          d.project_type_filter = "<?php echo htmlspecialchars($project_type_filter, ENT_QUOTES); ?>";
          d.support_person_filter = <?php echo json_encode($support_person_filter); ?>;
          d.tab = "<?php echo htmlspecialchars($_GET['tab'] ?? '', ENT_QUOTES); ?>";
          d.notifications = "<?php echo $notifications; ?>";
          d.role_id = "<?php echo $role_id; ?>";
          d.csrf = "<?php echo $_SESSION["token"]; ?>";
          d.countryAppendQuerySociety = "<?php echo $countryAppendQuerySociety; ?>";
          d.need_spec_tab_feedback = "need_spec_tab_feedback";
        },
        dataSrc: function(response) {
          var count_data = response.count_data;
          $('.my_need_spec_data').empty();
          Object.values(count_data).forEach(function(item) {
            var statusText = statusLabels[item.feedback_status] || "Unknown";
            var badgeColor = statusColors[item.feedback_status] || "badge-secondary";

            var badge = $('<span>')
              .addClass('badge')
              .addClass(badgeColor)
              .html(statusText + ' (' + item.count + ')');

            $('.my_need_spec_data').append(badge).append(' ');
          });
          var adminData = response.admin_wise_data;
          if (adminData && Object.keys(adminData).length > 0 > 0) {
            $('#otherContaint1').removeClass('d-none');
          }
          $('#adminTable1 tbody').empty();
          $('#adminTable1 thead').empty();
          var uniqueStatuses = new Set();
          Object.values(adminData).forEach(admin => {
            Object.keys(admin.status_count).forEach(status => {
              uniqueStatuses.add(status);
            });
          });
          var statusArray = Array.from(uniqueStatuses);
          var headerHTML = `<tr>
              <th>ID</th>
              <th>Name</th>
              <th>Status</th>`;
          statusArray.forEach(status => {
            var statusText = statusLabels[status] || "Unknown";
            headerHTML += `<th>${statusText}</th>`;
          });
          headerHTML += `</tr>`;
          $('#adminTable1 thead').append(headerHTML);
          var i = 1;
          Object.values(adminData).forEach(admin => {
            if (admin.id == '0') {
              admin.name = "Client";
            }
            var adminStatusHtml = '-';
            if (admin.id != '0' && admin.active_status !== '' && admin.active_status !== null && typeof admin.active_status !== 'undefined') {
              adminStatusHtml = String(admin.active_status) === '0'
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Deactive</span>';
            }
            var rowHTML = `<tr>
                <td>${i++}</td>
                <td>${admin.name || ''}</td>
                <td>${adminStatusHtml}</td>`;
            statusArray.forEach(status => {
              var count = admin.status_count[status] || 0;
              var badgeColor = statusColors[status] || "badge-secondary";
              rowHTML += `<td>${count}</td>`;
            });
            rowHTML += `</tr>`;
            $('#adminTable1 tbody').append(rowHTML);
          });
          if ($.fn.DataTable.isDataTable('#adminTable1')) {
            $('#adminTable1').DataTable().destroy();
          }
          if (Object.keys(adminData).length > 0) {
            $('#adminTable1').DataTable();
          }
          return response.data;
        },
        'error': function(xhr, error, thrown) {
          console.log('Error:', error, thrown);
          $('#need_spec_tab_feedback').DataTable().clear().draw();
          $('#need_spec_tab_feedback tbody').html('<tr><td colspan="12" class="text-center">No data found</td></tr>');
        }
      },
      columns: [{
          "data": "#"
        },
        {
          "data": "sr_no"
        },
        {
          "data": "action"
        },
        {
          "data": "feedback_id"
        },
        {
          "data": "subject",
          "render": function(data, type, row) {
            var feedback_msg = row.feedback_msg;
            return '<span data-toggle="tooltip" title="' + feedback_msg + '">' + data + '</span>';
          }
        },
        {
          "data": "platform"
        },
        {
          "data": "status"
        },
        {
          "data": "company_name"
        },
        {
          "data": "support_person_name"
        },
        {
          "data": "name"
        },
        {
          "data": "created_date"
        },
        {
          "data": "assign_date"
        },
        {
          "data": "created_by"
        },
        {
          "data": "reopen_remarks"
        }
      ],
      createdRow: function(row, data, dataIndex) {
        $('td:eq(5),td:eq(7)', row).addClass('tableWidth');
      },
      'error': function(settings, helpPage, message) {
        console.log("DataTables error:", message);
      }
    });
  });
  $(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';
    var next_update_table_feedback = $('#next_update_table_feedback').DataTable({
      lengthChange: false,
      responsive: true,
      autoWidth: false,
      searching: true,
      ordering: true,
      processing: true,
      destroy: true,
      language: {
        lengthMenu: "_MENU_",
        processing: "<img src='../img/ajax-loader.gif' width='30px' class='bg-transparent'>",
        emptyTable: "NO DATA FOUND !"
      },
      lengthMenu: [
        [10, 20, 30, 50],
        [10, 20, 30, 50]
      ],
      pageLength: 50,
      'ajax': {
        url: './allFeedbacks.php',
        type: "POST",
        data: function(d) {
          d.module_type_filter = "<?php echo $module_type_filter; ?>";
          d.where = "<?php echo $where; ?>";
          d.Status = "<?php echo $Status; ?>";
          d.platform_filter = "<?php echo $platform_filter; ?>";
          d.created_by_filter = "<?php echo htmlspecialchars($_GET['created_by_filter'] ?? '', ENT_QUOTES); ?>";
          d.project_type_filter = "<?php echo htmlspecialchars($project_type_filter, ENT_QUOTES); ?>";
          d.support_person_filter = <?php echo json_encode($support_person_filter); ?>;
          d.tab = "<?php echo htmlspecialchars($_GET['tab'] ?? '', ENT_QUOTES); ?>";
          d.notifications = "<?php echo $notifications; ?>";
          d.role_id = "<?php echo $role_id; ?>";
          d.csrf = "<?php echo $_SESSION["token"]; ?>";
          d.countryAppendQuerySociety = "<?php echo $countryAppendQuerySociety; ?>";
          d.next_update_tab_feedback = "next_update_tab_feedback";
        },
        dataSrc: function(response) {
          var count_data = response.count_data;
          $('.my_next_update_data').empty();
          Object.values(count_data).forEach(function(item) {
            var statusText = statusLabels[item.feedback_status] || "Unknown";
            var badgeColor = statusColors[item.feedback_status] || "badge-secondary";

            var badge = $('<span>')
              .addClass('badge')
              .addClass(badgeColor)
              .html(statusText + ' (' + item.count + ')');

            $('.my_next_update_data').append(badge).append(' ');
          });
          return response.data;
        },
        'error': function(xhr, error, thrown) {
          console.log('Error:', error, thrown);
          $('#next_update_tab_feedback').DataTable().clear().draw();
          $('#next_update_tab_feedback tbody').html('<tr><td colspan="12" class="text-center">No data found</td></tr>');
        }
      },
      columns: [{
          "data": "#"
        },
        {
          "data": "sr_no"
        },
        {
          "data": "action"
        },
        {
          "data": "feedback_id"
        },
        {
          "data": "subject",
          "render": function(data, type, row) {
            var feedback_msg = row.feedback_msg;
            return '<span data-toggle="tooltip" title="' + feedback_msg + '">' + data + '</span>';
          }
        },
        {
          "data": "platform"
        },
        {
          "data": "status"
        },
        {
          "data": "company_name"
        },
        {
          "data": "support_person_name"
        },
        {
          "data": "name"
        },
        {
          "data": "created_date"
        },
        {
          "data": "assign_date"
        },
        {
          "data": "created_by"
        },
        {
          "data": "reopen_remarks"
        }
      ],
      createdRow: function(row, data, dataIndex) {
        $('td:eq(5),td:eq(7)', row).addClass('tableWidth');
      },
      'error': function(settings, helpPage, message) {
        console.log("DataTables error:", message);
      }
    });
  });
  $(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';
    var withDeveloper_table_feedback = $('#withDeveloper_table_feedback').DataTable({
      lengthChange: false,
      responsive: true,
      autoWidth: false,
      searching: true,
      ordering: true,
      processing: true,
      destroy: true,
      language: {
        lengthMenu: "_MENU_",
        processing: "<img src='../img/ajax-loader.gif' width='30px' class='bg-transparent'>",
        emptyTable: "NO DATA FOUND !"
      },
      lengthMenu: [
        [10, 20, 30, 50],
        [10, 20, 30, 50]
      ],
      pageLength: 50,
      'ajax': {
        url: './allFeedbacks.php',
        type: "POST",
        data: function(d) {
          d.module_type_filter = "<?php echo $module_type_filter; ?>";
          d.where = "<?php echo $where; ?>";
          d.Status = "<?php echo $Status; ?>";
          d.platform_filter = "<?php echo $platform_filter; ?>";
          d.created_by_filter = "<?php echo htmlspecialchars($_GET['created_by_filter'] ?? '', ENT_QUOTES); ?>";
          d.project_type_filter = "<?php echo htmlspecialchars($project_type_filter, ENT_QUOTES); ?>";
          d.support_person_filter = <?php echo json_encode($support_person_filter); ?>;
          d.tab = "<?php echo htmlspecialchars($_GET['tab'] ?? '', ENT_QUOTES); ?>";
          d.notifications = "<?php echo $notifications; ?>";
          d.role_id = "<?php echo $role_id; ?>";
          d.csrf = "<?php echo $_SESSION["token"]; ?>";
          d.countryAppendQuerySociety = "<?php echo $countryAppendQuerySociety; ?>";
          d.withDeveloper_tab_feedback = "withDeveloper_tab_feedback";
        },
        dataSrc: function(response) {
          var count_data = response.count_data;
          $('.my_withDeveloper_data').empty();
          Object.values(count_data).forEach(function(item) {
            var statusText = statusLabels[item.feedback_status] || "Unknown";
            var badgeColor = statusColors[item.feedback_status] || "badge-secondary";

            var badge = $('<span>')
              .addClass('badge')
              .addClass(badgeColor)
              .html(statusText + ' (' + item.count + ')');

            $('.my_withDeveloper_data').append(badge).append(' ');
          });
          return response.data;
        },
        'error': function(xhr, error, thrown) {
          console.log('Error:', error, thrown);
          $('#withDeveloper_tab_feedback').DataTable().clear().draw();
          $('#withDeveloper_tab_feedback tbody').html('<tr><td colspan="12" class="text-center">No data found</td></tr>');
        }
      },
      columns: [{
          "data": "#"
        },
        {
          "data": "sr_no"
        },
        {
          "data": "action"
        },
        {
          "data": "feedback_id"
        },
        {
          "data": "subject",
          "render": function(data, type, row) {
            var feedback_msg = row.feedback_msg;
            return '<span data-toggle="tooltip" title="' + feedback_msg + '">' + data + '</span>';
          }
        },
        {
          "data": "platform"
        },
        {
          "data": "module_type"
        },
        {
          "data": "status"
        },
        {
          "data": "company_name"
        },
        {
          "data": "support_person_name"
        },
        {
          "data": "name"
        },
        {
          "data": "created_date"
        },
        {
          "data": "assign_date"
        },
        {
          "data": "created_by"
        },
        {
          "data": "reopen_remarks"
        }
      ],
      createdRow: function(row, data, dataIndex) {
        $('td:eq(5),td:eq(7)', row).addClass('tableWidth');
      },
      'error': function(settings, helpPage, message) {
        console.log("DataTables error:", message);
      }
    });
  });
  $(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';
    var solvedTableInitialized = false;

    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');

    function initializeSolvedTable() {
      if (!solvedTableInitialized) {
        solvedTableInitialized = true;
        $('#solved_table_feedback').DataTable({
          lengthChange: false,
          responsive: true,
          autoWidth: false,
          searching: true,
          ordering: true,
          processing: true,
          destroy: true,
          language: {
            lengthMenu: "_MENU_",
            processing: "<img src='../img/ajax-loader.gif' width='30px' class='bg-transparent'>",
            emptyTable: "NO DATA FOUND !"
          },
          lengthMenu: [
            [10, 20, 30, 50],
            [10, 20, 30, 50]
          ],
          pageLength: 50,
          ajax: {
            url: './allFeedbacks.php',
            type: "POST",
            data: function(d) {
              d.module_type_filter = "<?php echo $module_type_filter; ?>";
              d.where = "<?php echo $where; ?>";
              d.Status = "<?php echo $Status; ?>";
              d.platform_filter = "<?php echo $platform_filter; ?>";
              d.created_by_filter = "<?php echo htmlspecialchars($_GET['created_by_filter'] ?? '', ENT_QUOTES); ?>";
              d.project_type_filter = "<?php echo htmlspecialchars($project_type_filter, ENT_QUOTES); ?>";
              d.support_person_filter = <?php echo json_encode($support_person_filter); ?>;
              d.tab = "<?php echo htmlspecialchars($_GET['tab'] ?? '', ENT_QUOTES); ?>";
              d.notifications = "<?php echo $notifications; ?>";
              d.role_id = "<?php echo $role_id; ?>";
              d.csrf = "<?php echo $_SESSION["token"]; ?>";
              d.countryAppendQuerySociety = "<?php echo $countryAppendQuerySociety; ?>";
              d.solved_tab_feedback = "solved_tab_feedback";
            },
            error: function(xhr, error, thrown) {
              console.log('Error:', error, thrown);
              $('#solved_table_feedback').DataTable().clear().draw();
              $('#solved_table_feedback tbody').html('<tr><td colspan="12" class="text-center">No data found</td></tr>');
            }
          },
          columns: [{
              "data": "abc"
            },
            {
              "data": "sr_no"
            },
            {
              "data": "action"
            },
            {
              "data": "feedback_id"
            },
            {
              "data": "company_name"
            },
            {
              "data": "support_person_name"
            },
            {
              "data": "subject",
              "render": function(data, type, row) {
                var feedback_msg = row.feedback_msg;
                return '<span data-toggle="tooltip" title="' + feedback_msg + '">' + data + '</span>';
              }
            },
            {
              "data": "name"
            },
            {
              "data": "platform"
            },
            {
              "data": "module_type"
            },
            {
              "data": "close_remarks"
            },
            <?php if ($role_id == 1) { ?> {
                "data": "client_reply_message"
              },
            <?php } ?> {
              "data": "created_by"
            },
            {
              "data": "created_date"
            },
            {
              "data": "feedback_solve_time"
            }
          ],
          createdRow: function(row, data, dataIndex) {
            <?php if ($role_id == 1) { ?>
              $('td:eq(4),td:eq(7),td:eq(8),td:eq(9),td:eq(10)', row).addClass('tableWidth');
            <?php } else { ?>
              $('td:eq(4),td:eq(7),td:eq(8),td:eq(9)', row).addClass('tableWidth');
            <?php } ?>
          },
          error: function(settings, helpPage, message) {
            console.log("DataTables error:", message);
          }
        });
      }
    }
    if (activeTab === '2') {
      initializeSolvedTable();
    }
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
      var target = $(e.target).attr("href");
      if (target === "#solved_tab") {
        initializeSolvedTable();
      }
    });
  });
</script>
<script>
  $("#document").change(function() {
    var val = $(this).val();
    switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
      case 'csv':
      case 'xlsx':
      case 'pdf':
      case 'docx':
        break;
      default:
        $(this).val('');
        swal({
          icon: "error",
          text: "Only formats are allowed : csv, xlsx, pdf, docx",
          timer: 4000
        });
        break;
    }
  });
  $("#attachment").change(function() {
    var val = $(this).val();
    switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
      case 'png':
      case 'jpeg':
      case 'jpg':
      case 'gif':
        break;
      default:
        $(this).val('');
        swal({
          icon: "error",
          text: "Only formats are allowed : png, jpeg, jpg, gif",
          timer: 4000
        });
        break;
    }
  });
  $("#attachment_2").change(function() {
    var val = $(this).val();
    switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
      case 'png':
      case 'jpeg':
      case 'jpg':
      case 'gif':
        break;
      default:
        $(this).val('');
        swal({
          icon: "error",
          text: "Only formats are allowed : png, jpeg, jpg, gif",
          timer: 4000
        });
        break;
    }
  });
  $("#video").change(function() {
    var val = $(this).val();
    switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
      case 'mp4':
      case 'webm':
      case 'avi':
      case 'wmv':
      case 'mov':
        break;
      default:
        $(this).val('');
        swal({
          icon: "error",
          text: "Only formats are allowed : mp4, avi, mov, webm, wmv",
          timer: 4000
        });
        break;
    }
  });

  // Toggle Whitelabel Type dropdown + reset Company dropdown
  $(document).ready(function () {
    function toggleWhitelabelType() {
      const src = $('#ticket_source_type').val();
      if (src === '1') {
        $('#whitelabelTypeLabel').show();
        $('#whitelabelTypeRow').show();
        $('#whitelabel_type').prop('disabled', false).prop('required', true);
      } else {
        $('#whitelabelTypeLabel').hide();
        $('#whitelabelTypeRow').hide();
        $('#whitelabel_type').prop('disabled', true).prop('required', false);
        $('#whitelabel_type').val('');
      }

      // Reset company selection whenever source changes
      const $company = $('#companyId');
      $company.val('').trigger('change');
    }

    toggleWhitelabelType();
    $('#ticket_source_type').on('change', toggleWhitelabelType);
    $('#whitelabel_type').on('change', function () {
      $('#companyId').val('').trigger('change');
    });

    $('#feedbackAdd').on('hide.bs.modal', function (e) {
      if ($(document.activeElement).closest('.select2-container, .select2-dropdown, .select2-search').length) {
        e.preventDefault();
        e.stopPropagation();
      }
    });
    $(document).on('mousedown', '#feedbackAdd .select2-container, #feedbackAdd .select2-dropdown', function (e) {
      e.stopPropagation();
    });
  });
  $(document).ready(function() {
    $('.nav-tabs .nav-link').on('click', function() {
      const tabValue = $(this).data('tab-value');
      const form = $('form');
      form.find('input[name="tab"]').val(tabValue);
    });
  });
</script>
<script>
  let ticketClosingReasons = [];
  let ticketReplySuggestions = [];

  fetch('ajaxGetSuggestions.php', {
      method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
      if (data.status) {
        ticketClosingReasons = data.data.ticketClosingReasons;
        ticketReplySuggestions = data.data.ticketReplySuggestions;

        $(document).ready(function() {
          $('#reply').autocomplete({
            source: ticketReplySuggestions,
            minLength: 0,
            autoFocus: true
          });

          $('#dev_reply').autocomplete({
            source: ticketClosingReasons,
            minLength: 0,
            autoFocus: true
          });
        });
      } else {
        console.error("Failed to fetch suggestions:", data.message);
      }
    })
    .catch(error => {
      console.error("Error loading the suggestions:", error);
    });

  function replyFeedback(feedback_id, email) {
    $.ajax({
        url: 'ajaxGetReplyForm.php',
        type: 'POST',
        data: {
          feedback_id: feedback_id,
          email: email,
          previousURL: "feedback<?php echo $feedbackFilterSuffix; ?>",
          csrf: csrf
        }
      })
      .done(function(response) {
        $('#setReplyForm').html(response);

        $('#reply').autocomplete({
          source: ticketReplySuggestions,
          minLength: 0,
          autoFocus: true
        });
      });
  }

  document.addEventListener('click', function(e) {
    if (e.target.closest('.deleteButton')) {
      const button = e.target.closest('.deleteButton');
      const feedbackId = button.getAttribute('data-feedback-id');
      deleteFeedback(feedbackId);
    }
  });

  function deleteFeedback(feedback_id) {
    swal({
      title: "Are you sure?",
      text: "This action will permanently delete the feedback.",
      icon: "warning",
      buttons: ['Cancel', 'Yes, delete it!'],
      dangerMode: true,
    }).then((willDelete) => {
      if (willDelete) {
        document.getElementById(`delete-form-${feedback_id}`).submit();
      }
    });
  }
</script>

<script>
  function setForwardData(feedback_id, society_id) {
    document.getElementById('forward_feedback_id').value = feedback_id;
    document.getElementById('forward_society_id').value = society_id;
  }
</script>