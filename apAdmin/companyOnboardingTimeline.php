<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (isset($_REQUEST) && is_array($_REQUEST)) {
    extract($_REQUEST);
}
$company_id = isset($_GET['company_id']) ? $d->sanitizeReportFilterIdAsInt($_GET['company_id']) : 0;
$product_type = (isset($_GET['product_type']) && $_GET['product_type'] !== '') ? $_GET['product_type'] : 'hrms';
$trainingFeedbackSubmitted = false;
$trainingFeedbackQuery = $d->selectRow(
    "form_id, submitted_date",
    "training_completion_form_master",
    "society_id = '$company_id'",
    "ORDER BY submitted_date DESC LIMIT 1"
);
if ($trainingFeedbackQuery && mysqli_num_rows($trainingFeedbackQuery) > 0) {
    $trainingFeedbackSubmitted = true;
}

$trainingFormUrl = '';
if (!$trainingFeedbackSubmitted) {
    // Encrypt society_id for the training Feedback form
    $encrypted_society_id = $d->encryptDecrypt("encrypt", $company_id);
    // Construct full URL - trainingImplementationForm.php is in root directory
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    // Get base path (remove apAdmin from current script path)
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = str_replace('/apAdmin', '', $scriptPath);
    $basePath = rtrim($basePath, '/');
    if (empty($basePath)) {
        $basePath = '';
    }
    $trainingFormUrl = $protocol . "://" . $host . $basePath . "/trainingImplementationForm.php?c=" . urlencode($encrypted_society_id);
}
// Store filter parameters in session if they come from URL
if (isset($_GET['countryId']) || isset($_GET['sId']) || isset($_GET['cId']) || isset($_GET['training_type']) || isset($_GET['rise_filter']) || isset($_GET['product_type'])) {
    $_SESSION['companyOnboarding_filters'] = [
        'countryId' => isset($_GET['countryId']) && $_GET['countryId'] > 0 ? $_GET['countryId'] : (isset($_SESSION['companyOnboarding_filters']['countryId']) ? $_SESSION['companyOnboarding_filters']['countryId'] : 101),
        'sId' => isset($_GET['sId']) && $_GET['sId'] != '' ? $_GET['sId'] : (isset($_SESSION['companyOnboarding_filters']['sId']) ? $_SESSION['companyOnboarding_filters']['sId'] : ''),
        'cId' => isset($_GET['cId']) && $_GET['cId'] != '' ? $_GET['cId'] : (isset($_SESSION['companyOnboarding_filters']['cId']) ? $_SESSION['companyOnboarding_filters']['cId'] : ''),
        'training_type' => isset($_GET['training_type']) ? $_GET['training_type'] : (isset($_SESSION['companyOnboarding_filters']['training_type']) ? $_SESSION['companyOnboarding_filters']['training_type'] : '0'),
        'rise_filter' => isset($_GET['rise_filter']) ? $_GET['rise_filter'] : (isset($_SESSION['companyOnboarding_filters']['rise_filter']) ? $_SESSION['companyOnboarding_filters']['rise_filter'] : 'yes'),
        'product_type' => isset($_GET['product_type']) && $_GET['product_type'] !== '' ? $_GET['product_type'] : (isset($_SESSION['companyOnboarding_filters']['product_type']) ? $_SESSION['companyOnboarding_filters']['product_type'] : 'hrms')
    ];
}

if ($company_id <= 0) {
    $_SESSION['msg1'] = "Invalid Company ID";
    // Preserve filter parameters when redirecting
    $filters = [];
    $filterKeys = ['countryId', 'sId', 'cId', 'training_type', 'rise_filter', 'product_type'];
    foreach ($filterKeys as $key) {
        if (isset($_GET[$key]) && $_GET[$key] != '') {
            $filters[$key] = $_GET[$key];
        } elseif (isset($_SESSION['companyOnboarding_filters'][$key]) && $_SESSION['companyOnboarding_filters'][$key] != '') {
            $filters[$key] = $_SESSION['companyOnboarding_filters'][$key];
        }
    }
    if (!isset($filters['countryId']) || $filters['countryId'] <= 0) {
        $filters['countryId'] = 101;
    }
    $queryString = !empty($filters) ? '?' . http_build_query($filters) : '';
    $redirectUrl = 'companyOnboarding' . $queryString;
    echo "<script>window.location.replace('" . htmlspecialchars($redirectUrl, ENT_QUOTES) . "');</script>";
    exit();
}

$companyQuery = $d->selectRow(
    "sm.society_id, sm.society_name, sm.created_date, sm.is_welcome_email_send, 
     sm.welcome_email_send_date, sm.is_whatsapp_group_created, sm.whatsapp_group_created_date,
     sm.crm_created, sm.crm_created_date, sm.is_crm_welcome_email_send, sm.crm_welcome_email_send_date,
     sm.implementation_name as implementation_person",
    "society_master sm",
    "sm.society_id = '$company_id'"
);

if (!$companyQuery || mysqli_num_rows($companyQuery) == 0) {
    $_SESSION['msg1'] = "Company not found";
    // Preserve filter parameters when redirecting
    $filters = [];
    $filterKeys = ['countryId', 'sId', 'cId', 'training_type', 'rise_filter', 'product_type'];
    foreach ($filterKeys as $key) {
        if (isset($_GET[$key]) && $_GET[$key] != '') {
            $filters[$key] = $_GET[$key];
        } elseif (isset($_SESSION['companyOnboarding_filters'][$key]) && $_SESSION['companyOnboarding_filters'][$key] != '') {
            $filters[$key] = $_SESSION['companyOnboarding_filters'][$key];
        }
    }
    if (!isset($filters['countryId']) || $filters['countryId'] <= 0) {
        $filters['countryId'] = 101;
    }
    $queryString = !empty($filters) ? '?' . http_build_query($filters) : '';
    $redirectUrl = 'companyOnboarding' . $queryString;
    echo "<script>window.location.replace('" . htmlspecialchars($redirectUrl, ENT_QUOTES) . "');</script>";
    exit();
}

$companyData = mysqli_fetch_assoc($companyQuery);
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-6">
                <h4 class="page-title">Company Onboarding Timeline</h4>
            </div>
            <div class="col-sm-6 text-right">
                <?php
                // Build back URL with filter parameters preserved
                // Get filter parameters from GET or session
                $filters = [];
                $filterKeys = ['countryId', 'sId', 'cId', 'training_type', 'rise_filter', 'product_type'];
                foreach ($filterKeys as $key) {
                    if (isset($_GET[$key]) && $_GET[$key] != '') {
                        $filters[$key] = $_GET[$key];
                    } elseif (isset($_SESSION['companyOnboarding_filters'][$key]) && $_SESSION['companyOnboarding_filters'][$key] != '') {
                        $filters[$key] = $_SESSION['companyOnboarding_filters'][$key];
                    }
                }
                // Ensure countryId is set (default to 101)
                if (!isset($filters['countryId']) || $filters['countryId'] <= 0) {
                    $filters['countryId'] = 101;
                }
                // Build query string
                $queryString = !empty($filters) ? '?' . http_build_query($filters) : '';
                $backUrl = 'companyOnboarding' . $queryString;
                ?>
                <a href="<?php echo htmlspecialchars($backUrl, ENT_QUOTES); ?>" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left mr-1"></i> Back to Onboarding
                </a>
            </div>
        </div>
        <?php
        // If CRM, render CRM timeline and exit
        if ($product_type === 'crm') {
            $crmCreated = isset($companyData['crm_created']) ? (int)$companyData['crm_created'] : 0;
            $crmCreatedDate = !empty($companyData['crm_created_date']) ? new DateTime($companyData['crm_created_date']) : null;
            $crmWelcomeSent = isset($companyData['is_crm_welcome_email_send']) ? (int)$companyData['is_crm_welcome_email_send'] : 0;
            $crmWelcomeDate = !empty($companyData['crm_welcome_email_send_date']) ? new DateTime($companyData['crm_welcome_email_send_date']) : null;

            // Fetch CRM modules/topics
            $modulesQuery = $d->selectRow(
                "tmm.training_module_id, tmm.training_module_name, tmm.topic_id, tmm.training_module_order, tmm.module_priority,
         tmt.topic_id, tmt.topic_name, tmt.completion_days as topic_completion_days",
                "training_module_master tmm
         LEFT JOIN training_module_topics tmt ON tmm.topic_id = tmt.topic_id AND tmt.topic_type = 1",
                "tmm.module_type = 2 AND tmm.training_module_status = 0",
                "ORDER BY COALESCE(tmt.topic_name, ''), COALESCE(tmm.training_module_order, tmm.module_priority) ASC, tmm.training_module_id ASC"
            );
            $modulesData = [];
            $moduleIds = [];
            if ($modulesQuery && mysqli_num_rows($modulesQuery) > 0) {
                while ($m = mysqli_fetch_assoc($modulesQuery)) {
                    $modulesData[] = $m;
                    $moduleIds[] = (int)$m['training_module_id'];
                }
            }
            // Subtopics
            $subtopicsByModule = [];
            if (!empty($moduleIds)) {
                $midCsv = implode(',', array_map('intval', $moduleIds));
                $subQ = $d->selectRow(
                    "training_module_id, subtopic_id, subtopic_name, subtopic_description, display_order, estimated_minutes",
                    "training_module_subtopics",
                    "training_module_id IN ($midCsv)",
                    "ORDER BY training_module_id ASC, display_order ASC, subtopic_id ASC"
                );
                if ($subQ && mysqli_num_rows($subQ) > 0) {
                    while ($s = mysqli_fetch_assoc($subQ)) {
                        $mid = (int)$s['training_module_id'];
                        if (!isset($subtopicsByModule[$mid])) $subtopicsByModule[$mid] = [];
                        $subtopicsByModule[$mid][] = $s;
                    }
                }
            }
            // Progress
            $progressMap = [];
            $progQ = $d->selectRow(
                "progress_id, module_id, subtopic_id, status, remark, completed_date",
                "crm_training_progress",
                "society_id = '$company_id'"
            );
            if ($progQ && mysqli_num_rows($progQ) > 0) {
                while ($p = mysqli_fetch_assoc($progQ)) {
                    $mid = (int)$p['module_id'];
                    $sid = isset($p['subtopic_id']) ? (int)$p['subtopic_id'] : 0;
                    $key = $mid . '_' . ($sid > 0 ? $sid : 'module');
                    $progressMap[$key] = [
                        'status' => isset($p['status']) ? (int)$p['status'] : 0,
                        'remark' => $p['remark'] ?? '',
                        'completed_date' => $p['completed_date'] ?? null
                    ];
                }
            }
            // Build topics
            $topics = [];
            foreach ($modulesData as $m) {
                $topicId = isset($m['topic_id']) ? (int)$m['topic_id'] : 0;
                $topicName = !empty($m['topic_name']) ? $m['topic_name'] : 'Ungrouped';
                $topicKey = $topicId . '_' . $topicName;
                if (!isset($topics[$topicKey])) {
                    $topics[$topicKey] = [
                        'topic_id' => $topicId,
                        'topic_name' => $topicName,
                        'topic_completion_days' => isset($m['topic_completion_days']) ? (int)$m['topic_completion_days'] : null,
                        'modules' => []
                    ];
                }
                $mid = (int)$m['training_module_id'];
                $topics[$topicKey]['modules'][] = [
                    'module_id' => $mid,
                    'module_name' => $m['training_module_name'],
                    'module_priority' => $m['module_priority'] ?? null,
                    'topic_id' => $topicId,
                    'topic_name' => $topicName,
                    'subtopics' => $subtopicsByModule[$mid] ?? []
                ];
            }
            $topics = array_values($topics);

            $deriveModuleStatus = function ($module) use ($progressMap) {
                if (empty($module['subtopics'])) {
                    $key = $module['module_id'] . '_module';
                    $mp = $progressMap[$key] ?? ['status' => 0];
                    return (int)($mp['status'] ?? 0);
                }
                $statuses = [];
                foreach ($module['subtopics'] as $st) {
                    $k = $module['module_id'] . '_' . $st['subtopic_id'];
                    $sp = $progressMap[$k] ?? ['status' => 0];
                    $statuses[] = (int)($sp['status'] ?? 0);
                }
                if (in_array(0, $statuses, true)) return 0;
                if (count($statuses) === 0) return 0;
                if (count(array_unique($statuses)) === 1) return $statuses[0];
                return 1;
            };
            $getModuleCompletedDate = function ($module) use ($progressMap) {
                $dates = [];
                if (!empty($module['subtopics'])) {
                    foreach ($module['subtopics'] as $st) {
                        $k = $module['module_id'] . '_' . $st['subtopic_id'];
                        $sp = $progressMap[$k] ?? [];
                        if (!empty($sp['completed_date'])) {
                            $d = strtotime($sp['completed_date']);
                            if ($d) $dates[] = $d;
                        }
                    }
                }
                if (empty($dates)) {
                    $k = $module['module_id'] . '_module';
                    $mp = $progressMap[$k] ?? [];
                    if (!empty($mp['completed_date'])) {
                        $d = strtotime($mp['completed_date']);
                        if ($d) $dates[] = $d;
                    }
                }
                if (empty($dates)) return null;
                return max($dates);
            };

            // Topic stats/dates and highlight
            $topicStats = [];
            $topicDates = [];
            foreach ($topics as $t) {
                $tot = count($t['modules']);
                $comp = 0;
                $start = 0;
                $moduleStartDates = [];
                $moduleCompleteDates = [];
                foreach ($t['modules'] as $m) {
                    $st = $deriveModuleStatus($m);
                    if ($st !== 0) $start++;
                    if (in_array($st, [1, 2, 3], true)) $comp++;
                    $mDone = $getModuleCompletedDate($m);
                    if ($mDone) {
                        $moduleCompleteDates[] = $mDone;
                        $moduleStartDates[] = $mDone; // no explicit start; use first completion as proxy
                    }
                }
                $topicStats[$t['topic_id']] = ['total' => $tot, 'completed' => $comp, 'started' => $start];
                $topicDates[$t['topic_id']] = [
                    'started_ts' => !empty($moduleStartDates) ? min($moduleStartDates) : null,
                    'completed_ts' => ($comp === $tot && $tot > 0 && !empty($moduleCompleteDates)) ? max($moduleCompleteDates) : null
                ];
            }
            $allTopicsCompleted = true;
            $firstPendingTopicId = null;
            foreach ($topics as $t) {
                $st = $topicStats[$t['topic_id']] ?? ['total' => 0, 'completed' => 0];
                if ($st['total'] > 0 && $st['completed'] < $st['total']) {
                    $allTopicsCompleted = false;
                    if ($firstPendingTopicId === null) $firstPendingTopicId = $t['topic_id'];
                }
            }
            $highlightId = (!$crmCreated) ? 'crm_created' : (!$crmWelcomeSent ? 'crm_welcome' : (!$allTopicsCompleted && $firstPendingTopicId ? 'topic_' . $firstPendingTopicId : 'handover'));

            // Handover - CRM specific columns
            $handoverQuery = $d->selectRow(
                "crm_support_handover, crm_support_handover_date, crm_post_implementation_remark, crm_customer_expectation_remark, crm_handover_by, crm_admin.admin_name as crm_handover_by_name",
                "society_master LEFT JOIN bms_admin_master crm_admin ON society_master.crm_handover_by = crm_admin.admin_id",
                "society_master.society_id = '$company_id'"
            );
            $handoverData = mysqli_fetch_assoc($handoverQuery);
            $handoverCompleted = !empty($handoverData['crm_support_handover']) && (int)$handoverData['crm_support_handover'] === 1;
            $handoverDate = !empty($handoverData['crm_support_handover_date']) ? new DateTime($handoverData['crm_support_handover_date']) : null;
            $crmPostImplementationRemark = $handoverData['crm_post_implementation_remark'] ?? '';
            $crmCustomerExpectationRemark = $handoverData['crm_customer_expectation_remark'] ?? '';
            $crmHandoverByName = $handoverData['crm_handover_by_name'] ?? '';
            
            // Check if all CRM steps are complete
            $allCrmStepsCompleted = $crmCreated && $crmWelcomeSent && $allTopicsCompleted;

        ?>

            <!-- Company Basic Information -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="fa fa-building mr-2"></i>Company Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong class="h6">Company Name:</strong><br>
                                    <span class="text-primary h5"><?php echo htmlspecialchars($companyData['society_name']); ?></span>
                                </div>
                                <div class="col-md-3">
                                    <strong class="h6">CRM Created Date:</strong><br>
                                    <span class="text-info h6"><?php echo $crmCreatedDate ? $crmCreatedDate->format('d M Y, h:i A') : '—'; ?></span>
                                </div>
                                <div class="col-md-3">
                                    <strong class="h6">CRM Welcome Email:</strong><br>
                                    <span class="<?php echo $crmWelcomeSent ? 'text-success' : 'text-muted'; ?> h6">
                                        <?php echo $crmWelcomeDate ? $crmWelcomeDate->format('d M Y, h:i A') : 'Not Sent'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="timeline" id="crmTimeline">
                                <?php
                                $createdMarkerClass = $crmCreated ? 'bg-success' : 'bg-secondary';
                                $welcomeDue = $crmCreatedDate ? (clone $crmCreatedDate)->add(new DateInterval('P1D')) : null;
                                $welcomeCompletedTs = $crmWelcomeDate ? $crmWelcomeDate->getTimestamp() : null;
                                $welcomeOverdue = (!$crmWelcomeSent && $welcomeDue && (new DateTime()) > $welcomeDue);
                                $welcomeCompletedLate = ($crmWelcomeSent && $welcomeDue && $welcomeCompletedTs && ($welcomeCompletedTs > $welcomeDue->getTimestamp()));
                                ?>
                                <div class="timeline-item <?php echo ($highlightId === 'crm_created' ? 'highlight' : ''); ?>">
                                    <div class="timeline-marker <?php echo $createdMarkerClass; ?>"><i class="fa fa-building"></i></div>
                                    <div class="timeline-content">
                                        <div class="timeline-body">
                                            <div class="timeline-header d-flex justify-content-between align-items-center">
                                                <span class="timeline-title font-weight-bold">CRM Created</span>
                                                <span class="badge badge-<?php echo $crmCreated ? 'success' : 'secondary'; ?>"><?php echo $crmCreated ? 'Completed' : 'Pending'; ?></span>
                                            </div>
                                            <p class="text-muted mb-0 mt-1"><?php echo $crmCreatedDate ? $crmCreatedDate->format('d M Y, h:i A') : '—'; ?></p>
                                        </div>
                                    </div>
                                </div>

                                <?php $welcomeMarkerClass = $crmWelcomeSent ? 'bg-success' : 'bg-warning'; ?>
                                <div class="timeline-item <?php echo ($highlightId === 'crm_welcome' ? 'highlight' : ''); ?>">
                                    <div class="timeline-marker <?php echo $welcomeMarkerClass; ?>"><i class="fa fa-envelope"></i></div>
                                    <div class="timeline-content">
                                        <div class="timeline-body">
                                            <div class="timeline-header d-flex justify-content-between align-items-center">
                                                <span class="timeline-title font-weight-bold">CRM Welcome Email</span>
                                                <span class="badge badge-<?php echo $crmWelcomeSent ? 'success' : 'secondary'; ?>"><?php echo $crmWelcomeSent ? 'Completed' : 'Pending'; ?></span>
                                            </div>
                                            <p class="text-muted mb-0 mt-1">
                                                <strong>Due:</strong> <?php echo $welcomeDue ? $welcomeDue->format('d M Y, h:i A') : '—'; ?>
                                                <?php if ($welcomeOverdue): ?>
                                                    <span class="badge badge-warning ml-2">Overdue</span>
                                                    <i class="fa fa-exclamation-circle text-danger ml-1" title="Overdue"></i>
                                                <?php endif; ?>
                                            </p>
                                            <p class="mb-0 <?php echo $crmWelcomeDate ? 'text-success' : 'text-muted'; ?>">
                                                <strong>Completed:</strong>
                                                <?php if ($crmWelcomeDate): ?>
                                                    <i class="fa fa-check-circle text-success mr-1"></i><?php echo $crmWelcomeDate->format('d M Y, h:i A'); ?>
                                                    <?php if ($welcomeCompletedLate): ?>
                                                        <i class="fa fa-exclamation-circle text-danger ml-1" title="Completed after due date"></i>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    —
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                $step = 3;
                                // CRM topic due chaining: base starts from welcome completion, else welcome due, else creation
                                $topicBaseForNext = $crmWelcomeDate ? clone $crmWelcomeDate : ($welcomeDue ? clone $welcomeDue : ($crmCreatedDate ? clone $crmCreatedDate : null));
                                // Track completed topics to check if previous topics are completed before marking overdue
                                $topicIndex = 0;
                                foreach ($topics as $t):
                                    $stat = $topicStats[$t['topic_id']] ?? ['total' => 0, 'completed' => 0, 'started' => 0];
                                    $tdates = $topicDates[$t['topic_id']] ?? ['started_ts'=>null,'completed_ts'=>null];
                                    $topicStatus = 'Not Started';
                                    if ($stat['total'] > 0) {
                                        if ($stat['completed'] >= $stat['total']) $topicStatus = 'Completed';
                                        else if ($stat['started'] > 0) $topicStatus = 'In Progress';
                                    }
                                    $badge = $topicStatus === 'Completed' ? 'success' : ($topicStatus === 'In Progress' ? 'info' : 'secondary');
                                    $highlight = ($highlightId === 'topic_' . $t['topic_id']);
                                    $topicMarkerClass = $topicStatus === 'Completed' ? 'bg-success' : ($topicStatus === 'In Progress' ? 'bg-info' : 'bg-secondary');
                                    // Start by: based on previous step completion/due date
                                    $topicStartByObj = $topicBaseForNext;
                                    
                                    // Topic scheduled due: based on previous topic completion if available, else previous due, else base
                                    $topicScheduledDueObj = ($topicBaseForNext && !empty($t['topic_completion_days'])) ? (clone $topicBaseForNext)->add(new DateInterval('P' . (int)$t['topic_completion_days'] . 'D')) : null;
                                    
                                    // If started, effective due date is based on actual start date + completion days
                                    $topicDueObj = $topicScheduledDueObj;
                                    if ($tdates['started_ts'] && !empty($t['topic_completion_days'])) {
                                        $topicStartedDate = new DateTime('@' . $tdates['started_ts']);
                                        $topicDueObj = (clone $topicStartedDate)->add(new DateInterval('P' . (int)$t['topic_completion_days'] . 'D'));
                                    }
                                    
                                    $topicDueTs = $topicDueObj ? $topicDueObj->getTimestamp() : null;
                                    $topicScheduledDueTs = $topicScheduledDueObj ? $topicScheduledDueObj->getTimestamp() : null;
                                    $topicStartByTs = $topicStartByObj ? $topicStartByObj->getTimestamp() : null;
                                    
                                    // Check if all previous topics are completed
                                    $allPreviousTopicsCompleted = true;
                                    if ($topicIndex > 0) {
                                        // Check all topics before current one
                                        for ($i = 0; $i < $topicIndex; $i++) {
                                            $prevTopicId = $topics[$i]['topic_id'];
                                            $prevStat = $topicStats[$prevTopicId] ?? ['total' => 0, 'completed' => 0];
                                            if ($prevStat['total'] > 0 && $prevStat['completed'] < $prevStat['total']) {
                                                $allPreviousTopicsCompleted = false;
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // Only mark as overdue if not completed, due date passed, AND all previous topics are completed
                                    $topicOverdue = ($topicStatus !== 'Completed' && $topicDueTs && time() > $topicDueTs && $allPreviousTopicsCompleted);
                                    $topicStartedLate = ($tdates['started_ts'] && $topicStartByTs && $tdates['started_ts'] > $topicStartByTs);
                                    $topicCompletedLate = ($topicStatus === 'Completed' && $topicDueTs && $tdates['completed_ts'] && $tdates['completed_ts'] > $topicDueTs);
                                ?>
                                    <div class="timeline-item <?php echo $highlight ? 'highlight' : ''; ?>">
                                        <div class="timeline-marker <?php echo $topicMarkerClass; ?>"><i class="fa fa-folder-open"></i></div>
                                        <div class="timeline-content">
                                            <div class="timeline-body">
                                                <div class="timeline-header d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="timeline-title font-weight-bold"><?php echo htmlspecialchars($t['topic_name'] ?: 'Ungrouped'); ?></span>
                                                        <div class="small text-muted">
                                                            Step <?php echo $step++; ?> • Modules: <?php echo $stat['completed'] . '/' . $stat['total']; ?>
                                                        </div>
                                                    </div>
                                                    <span class="badge badge-<?php echo $badge; ?>"><?php echo $topicStatus; ?></span>
                                                </div>
                                                <div class="text-muted small">
                                                    <strong>Start by:</strong> <?php echo $topicStartByObj ? $topicStartByObj->format('d M Y, h:i A') : '—'; ?>
                                                    <span class="text-muted"> • </span>
                                                    <strong>Started:</strong> <?php echo $tdates['started_ts'] ? date('d M Y, h:i A', $tdates['started_ts']) : '—'; ?>
                                                    <?php if ($topicStartedLate): ?>
                                                        <i class="fa fa-exclamation-circle text-danger ml-1" title="Started after planned date"></i>
                                                    <?php endif; ?>
                                                    <br>
                                                    <strong>Due:</strong> <?php echo $topicDueObj ? $topicDueObj->format('d M Y, h:i A') : '—'; ?>
                                                    <?php if ($topicOverdue): ?>
                                                        <span class="badge badge-warning ml-2">Overdue</span>
                                                        <i class="fa fa-exclamation-circle text-danger ml-1" title="Overdue"></i>
                                                    <?php endif; ?>
                                                    <span class="text-muted"> • </span>
                                                    <strong>Completed:</strong>
                                                    <?php if ($tdates['completed_ts']): ?>
                                                        <i class="fa fa-check-circle text-success mr-1"></i><?php echo date('d M Y, h:i A', $tdates['completed_ts']); ?>
                                                        <?php if ($topicCompletedLate): ?>
                                                            <i class="fa fa-exclamation-circle text-danger ml-1" title="Completed after due date"></i>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        —
                                                    <?php endif; ?>
                                                </div>
                                                <div class="mt-2">
                                                    <?php foreach ($t['modules'] as $m):
                                                        $mst = $deriveModuleStatus($m);
                                                        $mbadge = $mst === 1 ? 'success' : ($mst === 2 ? 'secondary' : ($mst === 3 ? 'info' : 'warning'));
                                                        $mCompletedTs = $getModuleCompletedDate($m);
                                                    ?>
                                                        <div class="mb-2 pl-2">
                                                            <div class="d-flex justify-content-between">
                                                                <div><strong><?php echo htmlspecialchars($m['module_name']); ?></strong></div>
                                                                <div><span class="badge badge-<?php echo $mbadge; ?>">
                                                                        <?php echo ($mst === 1 ? 'Completed' : ($mst === 2 ? 'NA' : ($mst === 3 ? 'Later On' : 'Pending'))); ?>
                                                                    </span></div>
                                                            </div>
                                                            <div class="text-muted small">
                                                                <?php if (!empty($m['subtopics'])): ?>
                                                                    <?php echo count($m['subtopics']); ?> subtopics
                                                                <?php endif; ?>
                                                                <?php if ($mCompletedTs): ?>
                                                                    • Completed: <?php echo date('d M Y, h:i A', $mCompletedTs); ?>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php if (!empty($m['subtopics'])): ?>
                                                                <ul class="mb-0 pl-3 text-muted small">
                                                                    <?php foreach ($m['subtopics'] as $st):
                                                                        $k = $m['module_id'] . '_' . $st['subtopic_id'];
                                                                        $sp = $progressMap[$k] ?? ['status' => 0];
                                                                        $sbadge = ($sp['status'] ?? 0) === 1 ? 'success' : (($sp['status'] ?? 0) === 2 ? 'secondary' : (($sp['status'] ?? 0) === 3 ? 'info' : 'warning'));
                                                                    ?>
                                                                        <li><span class="badge badge-<?php echo $sbadge; ?>"><?php echo ($sp['status'] ?? 0) === 1 ? 'Completed' : (($sp['status'] ?? 0) === 2 ? 'NA' : (($sp['status'] ?? 0) === 3 ? 'Later On' : 'Pending')); ?></span> <?php echo htmlspecialchars($st['subtopic_name']); ?></li>
                                                                    <?php endforeach; ?>
                                                                </ul>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                        // Advance base for next topic: use this topic completion if available, else its due, else keep
                                        if (!empty($tdates['completed_ts'])) {
                                            $topicBaseForNext = (new DateTime())->setTimestamp($tdates['completed_ts']);
                                        } elseif ($topicDueObj) {
                                            $topicBaseForNext = clone $topicDueObj;
                                        }
                                        // Increment topic index for next iteration
                                        $topicIndex++;
                                    ?>
                                <?php endforeach; ?>

                                <?php $handoverMarkerClass = $handoverCompleted ? 'bg-success' : 'bg-secondary'; ?>
                                <div class="timeline-item <?php echo ($highlightId === 'handover' ? 'highlight' : ''); ?>">
                                    <div class="timeline-marker <?php echo $handoverMarkerClass; ?>"><i class="fa fa-handshake-o"></i></div>
                                    <div class="timeline-content">
                                        <div class="timeline-body">
                                            <div class="timeline-header d-flex justify-content-between align-items-center">
                                                <span class="timeline-title font-weight-bold">Handover to Support Team</span>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-<?php echo $handoverCompleted ? 'success' : 'secondary'; ?> mr-2"><?php echo $handoverCompleted ? 'Completed' : 'Pending'; ?></span>
                                                    <?php if (!$handoverCompleted && $allCrmStepsCompleted): ?>
                                                        <?php if ($trainingFeedbackSubmitted): ?>
                                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#crmHandoverModal">
                                                                <i class="fa fa-handshake-o mr-1"></i> Handover Now
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" 
                                                                class="btn btn-info btn-sm" 
                                                                onclick='copyTrainingFormUrl(<?php echo json_encode($trainingFormUrl, JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode($company_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                                                                title="Copy Training Feedback Form URL">
                                                                <i class="fa fa-copy mr-1"></i> Copy Feedback Form URL
                                                            </button>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if ($handoverCompleted): ?>
                                                <p class="text-muted mb-2 mt-1">
                                                    <strong>Completed:</strong> <?php echo $handoverDate ? $handoverDate->format('d M Y, h:i A') : '—'; ?>
                                                    <?php if (!empty($crmHandoverByName)): ?>
                                                        <span class="text-muted"> • By: <?php echo htmlspecialchars($crmHandoverByName); ?></span>
                                                    <?php endif; ?>
                                                </p>
                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <strong class="h6">Post Implementation Remark:</strong>
                                                        <p class="text-muted"><?php echo htmlspecialchars($crmPostImplementationRemark ?: 'N/A'); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong class="h6">Customer Expectation Remark:</strong>
                                                        <p class="text-muted"><?php echo htmlspecialchars($crmCustomerExpectationRemark ?: 'N/A'); ?></p>
                                                    </div>
                                                </div>
                                            <?php elseif ($allCrmStepsCompleted): ?>
                                                <?php if ($trainingFeedbackSubmitted): ?>
                                                    <p class="text-muted mb-0 mt-1">All CRM onboarding steps are completed. Please handover the company to the support team.</p>
                                                <?php else: ?>
                                                    <p class="text-warning mb-1 mt-1"><strong>Training feedback form must be filled before handover.</strong></p>
                                                    <p class="text-muted mb-0">All CRM onboarding steps are completed. Please ensure the training feedback form is submitted before handing over the company to the support team.</p>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <p class="text-muted mb-0 mt-1">This step will be available once all CRM onboarding steps (CRM Created, Welcome Email, and all Topics) are completed.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if ($handoverCompleted): ?>
                                        <div class="timeline-complete-icon" title="Completed">
                                            <i class="fa fa-check-circle"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <!-- CRM Handover Modal -->
    <div class="modal fade" id="crmHandoverModal" tabindex="-1" role="dialog" aria-labelledby="crmHandoverModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="crmHandoverModalLabel">
                        <i class="fa fa-handshake-o mr-2"></i>Handover Company to Support Team (CRM)
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="crmHandoverForm">
                    <div class="modal-body">
                        <input type="hidden" name="company_id" value="<?php echo $company_id; ?>">
                        <input type="hidden" name="product_type" value="crm">
                        <div class="form-group">
                            <label for="crm_post_implementation_remark" class="font-weight-bold">Post Implementation Remark <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="crm_post_implementation_remark" name="crm_post_implementation_remark" rows="4" required placeholder="Enter post implementation remarks..."></textarea>
                            <small class="form-text text-muted">Describe the CRM implementation status and any important notes.</small>
                        </div>
                        <div class="form-group">
                            <label for="crm_customer_expectation_remark" class="font-weight-bold">Customer Expectation Remark <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="crm_customer_expectation_remark" name="crm_customer_expectation_remark" rows="4" required placeholder="Enter customer expectation remarks..."></textarea>
                            <small class="form-text text-muted">Describe customer expectations and any follow-up requirements for CRM.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-handshake-o mr-1"></i>Complete Handover
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // CRM Handover form submission
            $('#crmHandoverForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();
                
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');
                
                $.ajax({
                    url: 'controller/companyOnboardingController.php',
                    type: 'POST',
                    data: formData + '&action=crm_handover',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#crmHandoverModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + (response.message || 'Failed to complete handover'));
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error: Failed to complete handover. Please try again.');
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
    </body>

    </html>
<?php
        } else {
$setupModulesQuery = $d->selectRow(
    "tmm.training_module_id, tmm.training_module_name, tmm.session_day_id,
     sdm.session_day_name,
     mtsm.data_receive_status, mtsm.data_receive_date, 
     mtsm.onboarding_status, mtsm.onboarding_date,
     CASE 
         WHEN mtsm.data_receive_status = 1 THEN 'Completed'
         WHEN mtsm.data_receive_status = 2 THEN 'Not Applicable'
         WHEN mtsm.data_receive_status = 0 THEN 'In Progress'
         ELSE 'Not Started'
     END as data_receive_status_text,
     CASE 
         WHEN mtsm.onboarding_status = 1 THEN 'Completed'
         WHEN mtsm.onboarding_status = 2 THEN 'Not Applicable'
         WHEN mtsm.onboarding_status = 0 THEN 'In Progress'
         ELSE 'Not Started'
     END as onboarding_status_text",
    "training_module_master tmm
     LEFT JOIN session_day_master sdm ON tmm.session_day_id = sdm.session_day_id
     LEFT JOIN module_training_status_master mtsm ON tmm.training_module_id = mtsm.module_id AND mtsm.company_id = '$company_id'
     LEFT JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
    "tmm.module_type = 0 AND tmm.training_module_status = 0 AND tmpm.is_required = 1",
    "ORDER BY sdm.session_day_id ASC, tmm.training_module_id ASC"
);

$setupModules = [];
while ($row = mysqli_fetch_assoc($setupModulesQuery)) {
    $setupModules[] = $row;
}

$trainingTopicsQuery = $d->selectRow(
    "tmt.topic_id, tmt.topic_name, tmt.completion_days, tmt.next_start_days,
     tpt.participant_name,
     COUNT(DISTINCT tmm.training_module_id) as module_count,
     COUNT(DISTINCT CASE WHEN btsm.training_status IN (1,2,3) THEN btsm.module_id END) AS completed_count,
     COUNT(DISTINCT btsm.module_id) AS started_count,
     GROUP_CONCAT(tmm.training_module_name ORDER BY tmm.training_module_order ASC) as module_names,
     GROUP_CONCAT(CASE WHEN btsm.training_status IN (1,2,3) THEN tmm.training_module_name END) AS completed_module_names,
     MIN(btsm.training_date) AS topic_started_on,
     CASE 
         WHEN COUNT(DISTINCT CASE WHEN btsm.training_status IN (1,2,3) THEN btsm.module_id END) = COUNT(DISTINCT tmm.training_module_id) 
         AND COUNT(DISTINCT tmm.training_module_id) > 0
         THEN MAX(CASE WHEN btsm.training_status IN (1,2,3) THEN btsm.training_date END)
         ELSE NULL 
     END AS topic_completed_on",
    "training_module_topics tmt
     LEFT JOIN training_module_master tmm ON tmt.topic_id = tmm.topic_id AND tmm.module_type = 1 AND tmm.training_module_status = 0
     LEFT JOIN batch_training_status_master btsm ON btsm.company_id = '$company_id' AND btsm.module_id = tmm.training_module_id AND btsm.participant_id = tmt.participant_type
     LEFT JOIN training_participants_type tpt ON tmt.participant_type = tpt.participants_type_id",
    "tmt.topic_status = 1 AND tmt.topic_type = '0'",
    "GROUP BY tmt.topic_id ORDER BY tmt.topic_name ASC"
);

$trainingTopics = [];
while ($row = mysqli_fetch_assoc($trainingTopicsQuery)) {
    $trainingTopics[] = $row;
}

// Calculate timeline dates
$companyCreationDate = new DateTime($companyData['created_date']);
$currentDate = new DateTime();
?>

        <!-- Company Basic Information -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fa fa-building mr-2"></i>Company Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong class="h6">Company Name:</strong><br>
                                <span class="text-primary h5"><?php echo htmlspecialchars($companyData['society_name']); ?></span>
                            </div>
                            <div class="col-md-3">
                                <strong class="h6">Creation Date:</strong><br>
                                <span class="text-info h6"><?php echo ($companyData['created_date'] != "" && $companyData['created_date'] != null && $companyData['created_date'] != "0000-00-00 00:00:00") ? date('d M Y, h:i A', strtotime($companyData['created_date'])) : ''; ?></span>
                            </div>
                            <div class="col-md-3">
                                <strong class="h6">Implementation Person:</strong><br>
                                <span class="text-success h6"><?php echo htmlspecialchars($companyData['implementation_person'] ?? 'Not Assigned'); ?></span>
                            </div>
                            <div class="col-md-3">
                                <strong class="h6">Days Since Creation:</strong><br>
                                <span class="text-secondary h6"><?php $daysSinceCreation = $currentDate->diff($companyCreationDate)->days;
                                                                echo $daysSinceCreation; ?> days</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        // steps 1 & 2
        $step1Completed = (int)($companyData['is_welcome_email_send'] ?? 0) === 1;
        $step2Completed = (int)($companyData['is_whatsapp_group_created'] ?? 0) === 1;
        $step1CompletedOn = !empty($companyData['welcome_email_send_date']) ? new DateTime($companyData['welcome_email_send_date']) : null;
        $step2CompletedOn = !empty($companyData['whatsapp_group_created_date']) ? new DateTime($companyData['whatsapp_group_created_date']) : null;
        // Deadline for steps 1 & 2: within 1 day from company creation
        $step12DueDate = clone $companyCreationDate;
        $step12DueDate->add(new DateInterval('P2D'));
        $step1Late = (!$step1Completed && $currentDate > $step12DueDate);
        $step2Late = (!$step2Completed && $currentDate > $step12DueDate);
        // Delayed completions (completed after due date)
        if ($step1CompletedOn && $step2CompletedOn) {
            $step1or2completedDate = ($step1CompletedOn > $step2CompletedOn) ? $step1CompletedOn : $step2CompletedOn;
        } else {
            if ($step1CompletedOn) {
                $step1or2completedDate = $step1CompletedOn;
            } else if ($step2CompletedOn) {
                $step1or2completedDate = $step2CompletedOn;
            } else {
                $step1or2completedDate = $step12DueDate;
            }
        }
        $step1Delayed = ($step1CompletedOn && ($step1CompletedOn > $step12DueDate));
        $step2Delayed = ($step2CompletedOn && ($step2CompletedOn > $step12DueDate));
        $dataStartDate = clone $step1or2completedDate;
        $dataStartDate->add(new DateInterval('P5D'));
        $dataEndDate = clone $dataStartDate;
        $dataEndDate->add(new DateInterval('P0D'));

        $setupTotal = count($setupModules);
        $setupCompleted = 0;
        $setupInProgress = 0;
        $setupNotStarted = 0;
        foreach ($setupModules as $m) {
            if (!isset($m['onboarding_status']) || $m['onboarding_status'] === null || $m['onboarding_status'] === '') {
                $setupNotStarted++;
                continue;
            }
            $st = (int)$m['onboarding_status'];
            if ($st === 1 || $st === 2) {
                $setupCompleted++;
            } else if ($st === 0) { // 0 In Progress
                $setupInProgress++;
            } else {
                $setupNotStarted++;
            }
        }
        // Guard divide by zero
        $setupCompletedPct = $setupTotal > 0 ? round(($setupCompleted / $setupTotal) * 100) : 0;
        $setupInProgressPct = $setupTotal > 0 ? round(($setupInProgress / $setupTotal) * 100) : 0;
        $setupNotStartedPct = max(0, 100 - $setupCompletedPct - $setupInProgressPct);

        $setupLatestOnboarding = null;
        if ($setupTotal > 0 && $setupCompleted === $setupTotal) {
            foreach ($setupModules as $m) {
                if (!empty($m['onboarding_date']) && in_array((int)$m['onboarding_status'], [1, 2], true)) {
                    $ts = strtotime($m['onboarding_date']);
                    if ($ts) {
                        if ($setupLatestOnboarding === null || $ts > $setupLatestOnboarding) {
                            $setupLatestOnboarding = $ts;
                        }
                    }
                }
            }
        }

        $topicSchedules = [];
        $baseDate = null;
        if ($setupLatestOnboarding) {
            $baseDate = (new DateTime(date('Y-m-d', $setupLatestOnboarding)));
        } else {
            $baseDate = clone $dataEndDate;
        }
        $cursorStart = clone $baseDate;
        $cursorStart->add(new DateInterval('P5D'));
        $priorActualCompletedOn = null;
        $priorNextStartDays = 0;

        foreach ($trainingTopics as $idx => $topicRow) {
            if (!empty($priorActualCompletedOn)) {
                $cursorStart = new DateTime($priorActualCompletedOn);
                $nsd = max(0, (int)$priorNextStartDays);
                if ($nsd > 0) {
                    $cursorStart->add(new DateInterval('P' . $nsd . 'D'));
                }
            }

            $start = clone $cursorStart;
            $complete = clone $start;
            $compDays = max(0, (int)($topicRow['completion_days'] ?? 0));
            $complete->add(new DateInterval('P' . $compDays . 'D'));

            $topicId = (int)$topicRow['topic_id'];
            $topicSchedules[$topicId] = [
                'start' => clone $start,
                'complete' => clone $complete,
            ];

            $priorActualCompletedOn = !empty($topicRow['topic_completed_on']) ? $topicRow['topic_completed_on'] : null;
            $priorNextStartDays = (int)($topicRow['next_start_days'] ?? 0);

            if (empty($priorActualCompletedOn)) {
                $cursorStart = clone $complete;
                if ($priorNextStartDays > 0) {
                    $cursorStart->add(new DateInterval('P' . $priorNextStartDays . 'D'));
                }
            }
        }

        $setupNoneStarted = ($setupCompleted === 0 && $setupInProgress === 0);
        $setupStartLate = ($currentDate > $dataStartDate && $setupNoneStarted);
        $setupCompleteLate = ($currentDate > $dataEndDate && $setupCompleted < $setupTotal);
        $setupStartedOnTs = null;
        foreach ($setupModules as $m) {
            $drDate = !empty($m['data_receive_date']) ? strtotime($m['data_receive_date']) : null;
            $upDate = !empty($m['onboarding_date']) ? strtotime($m['onboarding_date']) : null;
            $candidate = null;
            if ($drDate) {
                $candidate = $drDate;
            }
            if ($upDate) {
                $candidate = $candidate ? min($candidate, $upDate) : $upDate;
            }
            if ($candidate) {
                if ($setupStartedOnTs === null || $candidate < $setupStartedOnTs) {
                    $setupStartedOnTs = $candidate;
                }
            }
        }
        $setupStartedOn = $setupStartedOnTs ? (new DateTime(date('Y-m-d H:i:s', $setupStartedOnTs))) : null;
        if ($setupStartedOn) {
            $dataEndDate = clone $setupStartedOn;
            $dataEndDate->add(new DateInterval('P0D'));
        }
        // Late flags for setup step based on actual dates
        $setupStartedLate = ($setupStartedOn && ($setupStartedOn > $dataStartDate));
        $setupCompletedLate = ($setupLatestOnboarding && ($setupLatestOnboarding > $dataEndDate->getTimestamp()));

        // Step statuses for 1 & 2


        // Setup (Step 3) overall status
        $setupStatus = 'Not Started';
        if ($setupTotal > 0 && $setupCompleted === $setupTotal) {
            $setupStatus = 'Completed';
        } elseif ($setupInProgress > 0 || $setupCompleted > 0) {
            $setupStatus = 'In Progress';
        }

        // Check if all topics are completed
        $allTopicsCompleted = true;
        $lastTopicCompletedDate = null;
        foreach ($trainingTopics as $topic) {
            $totalModulesForTopic = (int)($topic['module_count'] ?? 0);
            $completedModulesForTopic = (int)($topic['completed_count'] ?? 0);
            if ($totalModulesForTopic > 0 && $completedModulesForTopic < $totalModulesForTopic) {
                $allTopicsCompleted = false;
                break;
            }
            // Track latest topic completion date
            if (!empty($topic['topic_completed_on'])) {
                $topicCompletedTs = strtotime($topic['topic_completed_on']);
                if ($topicCompletedTs && ($lastTopicCompletedDate === null || $topicCompletedTs > $lastTopicCompletedDate)) {
                    $lastTopicCompletedDate = $topicCompletedTs;
                }
            }
        }

        // Check if all steps are completed (for handover eligibility)
        $allStepsCompleted = ($step1Completed && $step2Completed && $setupStatus === 'Completed' && $allTopicsCompleted);

        // Check if training feedback form is submitted
        
        // Generate training feedback form URL if not submitted
        

        // Get handover data if exists
        $handoverQuery = $d->selectRow(
            "support_handover, support_handover_date, post_implementation_remark, customer_expectation_remark, support_handover_by,bms_admin_master.admin_name as handover_by_name",
            "society_master LEFT JOIN bms_admin_master ON society_master.support_handover_by = bms_admin_master.admin_id",
            "society_master.society_id = '$company_id'"
        );
        $handoverData = mysqli_fetch_assoc($handoverQuery);
        $handoverCompleted = !empty($handoverData['support_handover']) && (int)$handoverData['support_handover'] === 1;
        $handoverDate = !empty($handoverData['support_handover_date']) ? new DateTime($handoverData['support_handover_date']) : null;
        $postImplementationRemark = $handoverData['post_implementation_remark'] ?? '';
        $customerExpectationRemark = $handoverData['customer_expectation_remark'] ?? '';
        $handoverBy = $handoverData['support_handover_by'] ?? '';
        $handoverByName = $handoverData['handover_by_name'] ?? '';

        // Calculate handover due date (3 days after all steps completed)
        $handoverDueDate = null;
        if ($allStepsCompleted && $lastTopicCompletedDate) {
            $handoverDueDate = new DateTime(date('Y-m-d', $lastTopicCompletedDate));
            $handoverDueDate->add(new DateInterval('P3D'));
        } elseif ($allStepsCompleted && $setupLatestOnboarding) {
            // Fallback to setup completion if no topic completion date
            $handoverDueDate = new DateTime(date('Y-m-d', $setupLatestOnboarding));
            $handoverDueDate->add(new DateInterval('P3D'));
        }

        // Check if handover is overdue (not completed and past due date)
        $handoverOverdue = false;
        $handoverOverdueDays = 0;
        if ($allStepsCompleted && !$handoverCompleted && $handoverDueDate) {
            $currentDateOnly = clone $currentDate;
            $currentDateOnly->setTime(0, 0, 0);
            $handoverDueDateOnly = clone $handoverDueDate;
            $handoverDueDateOnly->setTime(0, 0, 0);
            if ($currentDateOnly > $handoverDueDateOnly) {
                $handoverOverdue = true;
                $handoverOverdueDays = (int)$currentDateOnly->diff($handoverDueDateOnly)->days;
            }
        }
        
        // Check if handover was completed after due date (delayed completion)
        $handoverDelayed = false;
        if ($allStepsCompleted && $handoverCompleted && $handoverDate && $handoverDueDate) {
            $handoverDateOnly = clone $handoverDate;
            $handoverDateOnly->setTime(0, 0, 0);
            $handoverDueDateOnly = clone $handoverDueDate;
            $handoverDueDateOnly->setTime(0, 0, 0);
            $handoverDelayed = ($handoverDateOnly > $handoverDueDateOnly);
        }

        $highlightId = null; // one of: step1_welcome, step2_whatsapp, step3_setup, topic_item_<id>, handover
        if (!$step1Completed) {
            $highlightId = 'step1_welcome';
        } elseif (!$step2Completed) {
            $highlightId = 'step2_whatsapp';
        } elseif ($setupStatus !== 'Completed') {
            $highlightId = 'step3_setup';
        } else {
            foreach ($trainingTopics as $index => $topic) {
                $tid = (int)$topic['topic_id'];
                $totalModulesForTopic = (int)($topic['module_count'] ?? 0);
                $completedModulesForTopic = (int)($topic['completed_count'] ?? 0);
                if ($totalModulesForTopic === 0 || $completedModulesForTopic < $totalModulesForTopic) {
                    $highlightId = 'topic_item_' . $tid;
                    break;
                }
            }
            // If all topics completed but handover not done, highlight handover
            if ($highlightId === null && $allStepsCompleted && !$handoverCompleted) {
                $highlightId = 'handover';
            }
        }
        ?>

        <!-- Timeline -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="timeline" id="timelineRoot">
                            <div class="timeline-item<?php echo ($highlightId === 'step1_welcome' ? ' highlight' : ''); ?>" data-filter-text="welcome mail">
                                <div class="timeline-marker bg-primary"> <i class="fa fa-envelope"></i> </div>
                                <div class="timeline-content py-1">
                                    <div class="timeline-body">
                                        <div class="timeline-header d-flex align-items-center justify-content-between flex-wrap">
                                            <div class="d-flex align-items-center flex-wrap">
                                                <strong class="mr-2 h6">Welcome Email</strong>
                                                <small class="text-muted mr-3">Step 1</small>
                                            </div>
                                            <div class="d-flex align-items-center flex-wrap justify-content-end">
                                                <?php if ($step1Completed): ?>
                                                    <span class="badge badge-success mr-3">Sent</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning mr-3">Not Sent</span>
                                                <?php endif; ?>

                                                <span class="mx-3 <?php echo $step1Late ? 'text-danger font-weight-bold' : 'text-muted'; ?>">
                                                    Due: <?php echo $step12DueDate->format('d M Y'); ?>
                                                </span>

                                                <?php if ($step1CompletedOn): ?>
                                                    <span class="mx-2">Completed:</span>
                                                    <span class="text-success">
                                                        <?php echo $step1CompletedOn->format('d M Y, h:i A'); ?>
                                                    </span>
                                                    <?php if ($step1Delayed): ?>
                                                        <i class="fa fa-exclamation-circle text-danger ml-1" title="Completed after due date"></i>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($step1Completed): ?>
                                                <div class="timeline-complete-icon" title="Completed">
                                                    <i class="fa fa-check-circle"></i>
                                                </div>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="timeline-item<?php echo ($highlightId === 'step2_whatsapp' ? ' highlight' : ''); ?>" data-filter-text="whatsapp group">
                                <div class="timeline-marker bg-success">
                                    <i class="fa fa-whatsapp"></i>
                                </div>

                                <div class="timeline-content py-1">
                                    <div class="timeline-body">
                                        <div class="timeline-header d-flex align-items-center justify-content-between flex-wrap">

                                            <!-- Left: Step title -->
                                            <div class="d-flex align-items-center flex-wrap">
                                                <strong class="mr-2 h6">WhatsApp Group</strong>
                                                <small class="text-muted mr-3">Step 2</small>
                                            </div>

                                            <!-- Right: Status, Due, Completed -->
                                            <div class="d-flex align-items-center flex-wrap justify-content-end">
                                                <?php if ($step2Completed): ?>
                                                    <span class="badge badge-success mr-3">Created</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning mr-3">Not Created</span>
                                                <?php endif; ?>

                                                <span class="mx-3 <?php echo $step2Late ? 'text-danger font-weight-bold' : 'text-muted'; ?>">
                                                    Due: <?php echo $step12DueDate->format('d M Y'); ?>
                                                </span>

                                                <?php if ($step2CompletedOn): ?>
                                                    <span class="mr-2">Completed:</span>
                                                    <span class="text-success"><?php echo $step2CompletedOn->format('d M Y, h:i A'); ?></span>
                                                    <?php if ($step2Delayed): ?>
                                                        <i class="fa fa-exclamation-circle text-danger ml-1" title="Completed after due date"></i>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Completed icon -->
                                            <?php if ($step2Completed): ?>
                                                <div class="timeline-complete-icon" title="Completed">
                                                    <i class="fa fa-check-circle"></i>
                                                </div>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Step 3: Data Receive & Upload Status -->
                            <div class="timeline-item<?php echo ($highlightId === 'step3_setup' ? ' highlight' : ''); ?> " data-filter-text="data receive upload setup">
                                <div class="timeline-marker" style="border-color: #6c757d; background-color: #6c757d;">
                                    <i class="fa fa-database"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header d-flex align-items-center justify-content-between flex-wrap">
                                        <div class="d-flex align-items-center" role="button" data-toggle="collapse" data-target="#collapseSetupModules" aria-expanded="false" aria-controls="collapseSetupModules" style="cursor: pointer;">
                                            <h6 class="mb-1 mr-2">Data Collection & Setup</h6>
                                            <small class="text-muted">Step 3</small>
                                        </div>
                                        <div class="text-muted mt-1 mt-sm-0">
                                            <?php $setupStatusBadge = ($setupStatus === 'Completed' ? 'success' : ($setupStatus === 'In Progress' ? 'info' : 'secondary')); ?>
                                            <span class="badge badge-<?php echo $setupStatusBadge; ?> ml-2"><?php echo $setupStatus; ?></span>
                                            <span class="<?php echo $setupStartLate ? 'text-danger font-weight-bold' : ''; ?>">Start by: <?php echo $dataStartDate->format('d M Y'); ?></span>
                                            <?php if ($setupStartedOn): ?>
                                                <span class="text-muted"> • </span>
                                                <span>Started: <?php echo $setupStartedOn->format('d M Y'); ?><?php if ($setupStartedLate): ?><i class="fa fa-exclamation-circle text-danger ml-1" title="Started after planned date"></i><?php endif; ?></span>
                                            <?php endif; ?>
                                            <span class="text-muted"> • </span>
                                            <span class="<?php echo $setupCompleteLate ? 'text-danger font-weight-bold' : ''; ?>">Due: <?php echo $dataEndDate->format('d M Y'); ?></span>
                                            <?php if ($setupLatestOnboarding): ?>
                                                <span class="text-muted"> • </span>
                                                <span>Completed: <?php echo date('d M Y', $setupLatestOnboarding); ?><?php if ($setupCompletedLate): ?><i class="fa fa-exclamation-circle text-danger ml-1" title="Completed after due date"></i><?php endif; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="timeline-body">
                                        <?php if (!empty($setupModules)): ?>
                                            <div class="collapse" id="collapseSetupModules">
                                                <div class="mb-2">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <strong class="mr-2 h6">Overall Progress:</strong>
                                                        <span class="text-muted"><?php echo $setupCompleted; ?>/<?php echo $setupTotal; ?> completed</span>
                                                    </div>
                                                    <div class="progress" style="height: 10px;">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $setupCompletedPct; ?>%" aria-valuenow="<?php echo $setupCompletedPct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $setupInProgressPct; ?>%" aria-valuenow="<?php echo $setupInProgressPct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: <?php echo $setupNotStartedPct; ?>%" aria-valuenow="<?php echo $setupNotStartedPct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <div class="text-muted mt-1">
                                                        <span class="mr-3"><span class="badge badge-success progress_badge">&nbsp;</span> Completed (<?php echo $setupCompletedPct; ?>%)</span>
                                                        <span class="mr-3"><span class="badge badge-info">&nbsp;</span> In Progress (<?php echo $setupInProgressPct; ?>%)</span>
                                                        <span><span class="badge badge-secondary">&nbsp;</span> Not Started (<?php echo $setupNotStartedPct; ?>%)</span>
                                                    </div>
                                                </div>
                                                <div class="compact-scroll">
                                                    <div class="list-group list-group-flush">
                                                        <?php foreach ($setupModules as $module): ?>
                                                            <?php
                                                            $drClass = 'secondary';
                                                            switch ((int)($module['data_receive_status'])) {
                                                                case 1:
                                                                case 2:
                                                                    $drClass = 'success';
                                                                    break; // Completed or Not Applicable
                                                                case 0:
                                                                    $drClass = 'info';
                                                                    break; // In Progress
                                                                default:
                                                                    $drClass = 'secondary'; // Not Started/empty
                                                            }
                                                            $upClass = 'secondary';
                                                            switch ((int)($module['onboarding_status'])) {
                                                                case 1:
                                                                case 2:
                                                                    $upClass = 'success';
                                                                    break; // Completed or Not Applicable
                                                                case 0:
                                                                    $upClass = 'info';
                                                                    break; // In Progress
                                                                default:
                                                                    $upClass = 'secondary'; // Not Started/empty
                                                            }
                                                            ?>
                                                            <div class="list-group-item py-1 px-0">
                                                                <div class="d-flex align-items-center flex-wrap">
                                                                    <strong class="mr-2"><?php echo htmlspecialchars($module['training_module_name']); ?></strong>
                                                                    <span class="text-muted mr-3"><?php echo htmlspecialchars($module['session_day_name']); ?></span>
                                                                    <span class="mr-1">Data:</span>
                                                                    <span class="badge badge-<?php echo $drClass; ?> mr-3"><?php echo $module['data_receive_status_text']; ?></span>
                                                                    <span class="mr-1">Upload:</span>
                                                                    <span class="badge badge-<?php echo $upClass; ?> mr-3"><?php echo $module['onboarding_status_text']; ?></span>
                                                                    <span class="mr-1">Date:</span>
                                                                    <?php if ($module['onboarding_date']): ?>
                                                                        <span class="text-success"><?php echo date('d M Y', strtotime($module['onboarding_date'])); ?></span>
                                                                    <?php else: ?>
                                                                        <span class="text-muted">Not completed</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning">No setup modules found.</div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($setupStatus === 'Completed'): ?>
                                        <div class="timeline-complete-icon" title="Completed">
                                            <i class="fa fa-check-circle"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Step 4+: Training Module Topics -->
                            <?php if (!empty($trainingTopics)): ?>
                                <?php $priorTopicsCompleted = true;
                                foreach ($trainingTopics as $index => $topic): ?>
                                    <?php
                                    $topicNumber = $index + 4; // Starting from step 4
                                    $topicStartDate = clone $dataEndDate;
                                    $topicStartDate->add(new DateInterval('P5D')); // 5 days from data completion
                                    $topicEndDate = clone $topicStartDate;
                                    $topicEndDate->add(new DateInterval('P' . $topic['completion_days'] . 'D')); // Based on completion days
                                    $totalModulesForTopic = (int)($topic['module_count'] ?? 0);
                                    $completedModulesForTopic = (int)($topic['completed_count'] ?? 0);
                                    $startedModulesForTopic = (int)($topic['started_count'] ?? 0);
                                    $topicStatus = 'Not Started';
                                    if ($totalModulesForTopic > 0) {
                                        if ($completedModulesForTopic >= $totalModulesForTopic) {
                                            $topicStatus = 'Completed';
                                        } elseif ($startedModulesForTopic > 0) {
                                            $topicStatus = 'In Progress';
                                        }
                                    }
                                    $topicBadge = $topicStatus === 'Completed' ? 'success' : ($topicStatus === 'In Progress' ? 'info' : 'secondary');
                                    $topicFilter = strtolower(trim(($topic['topic_name'] ?? '') . ' ' . ($topic['participant_name'] ?? '')));
                                    ?>
                                    <div class="timeline-item<?php echo ($highlightId === 'topic_item_' . (int)$topic['topic_id'] ? ' highlight' : ''); ?>" data-filter-text="<?php echo htmlspecialchars($topicFilter); ?>">
                                        <div class="timeline-marker bg-warning">
                                            <i class="fa fa-graduation-cap"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="timeline-header">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                    <div class="mb-1">
                                                        <a data-toggle="collapse" href="#topic_<?php echo (int)$topic['topic_id']; ?>" role="button" aria-expanded="false" aria-controls="topic_<?php echo (int)$topic['topic_id']; ?>" class="text-dark">
                                                            <h6 class="mb-0 d-inline"><?php echo htmlspecialchars($topic['topic_name']); ?></h6>
                                                            <small class="text-muted ml-2">Step <?php echo $topicNumber; ?></small>
                                                        </a>
                                                    </div>
                                                    <div class="mb-1 d-flex align-items-center">
                                                        <span class="badge badge-<?php echo $topicBadge; ?>" title="Current Status"><?php echo $topicStatus; ?></span>
                                                        <?php
                                                        $tid = (int)$topic['topic_id'];
                                                        $schedStart = isset($topicSchedules[$tid]) ? $topicSchedules[$tid]['start'] : $topicStartDate;
                                                        $schedEnd = isset($topicSchedules[$tid]) ? $topicSchedules[$tid]['complete'] : $topicEndDate;
                                                        $topicStartLate = false;
                                                        $topicOverdueToStart = false;
                                                        $topicCompleteLate = false;
                                                        $topicStartedOn = !empty($topic['topic_started_on']) ? new DateTime($topic['topic_started_on']) : null;
                                                        $topicCompletedOn = !empty($topic['topic_completed_on']) ? new DateTime($topic['topic_completed_on']) : null;

                                                        // Normalize dates to start of day for accurate date-only comparison
                                                        $schedStartDateOnly = clone $schedStart;
                                                        $schedStartDateOnly->setTime(0, 0, 0);
                                                        $currentDateOnly = clone $currentDate;
                                                        $currentDateOnly->setTime(0, 0, 0);

                                                        // Check if topic is overdue to start (not started and past start date)
                                                        if (!$topicStartedOn && $currentDateOnly > $schedStartDateOnly) {
                                                            $topicOverdueToStart = true;
                                                        }

                                                        if ($topicStartedOn) {
                                                            $topicStartedOnDateOnly = clone $topicStartedOn;
                                                            $topicStartedOnDateOnly->setTime(0, 0, 0);
                                                            $topicStartLate = ($topicStartedOnDateOnly > $schedStartDateOnly);
                                                        }
                                                        // If started, adjust the "Complete by" to be based on actual start date
                                                        $effectiveSchedEnd = clone $schedEnd;
                                                        if ($topicStartedOn) {
                                                            $effectiveSchedEnd = clone $topicStartedOn;
                                                            $effectiveSchedEnd->add(new DateInterval('P' . ((int)$topic['completion_days']) . 'D'));
                                                        }
                                                        $effectiveSchedEndDateOnly = clone $effectiveSchedEnd;
                                                        $effectiveSchedEndDateOnly->setTime(0, 0, 0);
                                                        if ($topicCompletedOn) {
                                                            $topicCompletedOnDateOnly = clone $topicCompletedOn;
                                                            $topicCompletedOnDateOnly->setTime(0, 0, 0);
                                                            $topicCompleteLate = ($topicCompletedOnDateOnly > $effectiveSchedEndDateOnly);
                                                        } elseif ($topicStartedOn && $currentDateOnly > $effectiveSchedEndDateOnly) {
                                                            // Topic started but completion is overdue
                                                            $topicCompleteLate = true;
                                                        }
                                                        ?>
                                                        <?php if ($setupStatus === 'Completed' && $priorTopicsCompleted): ?>
                                                            <span class="ml-3">
                                                                <span class="<?php echo $topicOverdueToStart ? 'text-danger font-weight-bold' : ''; ?>">Start by: <?php echo $schedStart->format('d M Y'); ?><?php if ($topicOverdueToStart): ?><i class="fa fa-exclamation-circle text-danger ml-1" title="Overdue to start"></i><?php endif; ?></span>
                                                                <?php if ($topicStartedOn): ?>
                                                                    <span class="text-muted"> • </span>
                                                                    <span>Started: <?php echo $topicStartedOn->format('d M Y'); ?><?php if ($topicStartLate): ?><i class="fa fa-exclamation-circle text-danger ml-1" title="Started after planned date"></i><?php endif; ?></span>
                                                                <?php endif; ?>
                                                                <span class="text-muted"> • </span>
                                                                <span class="<?php echo $topicCompleteLate ? 'text-danger font-weight-bold' : 'text-muted'; ?>">Due: <?php echo $effectiveSchedEnd->format('d M Y'); ?></span>
                                                                <?php if ($topicCompletedOn): ?>
                                                                    <span class="text-muted"> • </span>
                                                                    <span>Completed: <?php echo $topicCompletedOn->format('d M Y'); ?><?php if ($topicCompleteLate): ?><i class="fa fa-exclamation-circle text-danger ml-1" title="Completed after due date"></i><?php endif; ?></span>
                                                                <?php endif; ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                            </div>
                                            <?php if ($topicStatus === 'Completed'): ?>
                                                <div class="timeline-complete-icon" title="Completed">
                                                    <i class="fa fa-check-circle"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="timeline-body collapse" id="topic_<?php echo (int)$topic['topic_id']; ?>">
                                                <div class="row mb-2">
                                                    <div class="col-md-6 px-3">
                                                        <strong class="h6">Participant Type:</strong>
                                                        <span class="text-info h6"><?php echo htmlspecialchars($topic['participant_name']); ?></span>
                                                    </div>
                                                    <div class="col-md-6 px-3">
                                                        <strong class="h6">Modules Count:</strong>
                                                        <span class="badge badge-primary"><?php echo $topic['module_count']; ?></span>
                                                    </div>
                                                </div>



                                                <?php if ($topic['module_names']): ?>
                                                    <div class="mb-2">
                                                        <strong class="h6">Training Modules:</strong>
                                                        <div class="mt-1 wrap-chips compact-scroll-sm">
                                                            <?php
                                                            $moduleNames = explode(',', $topic['module_names']);
                                                            $completedNamesRaw = $topic['completed_module_names'] ?? '';
                                                            $completedSet = [];
                                                            if (!empty($completedNamesRaw)) {
                                                                foreach (explode(',', $completedNamesRaw) as $cn) {
                                                                    $completedSet[trim($cn)] = true;
                                                                }
                                                            }
                                                            foreach ($moduleNames as $moduleName): ?>
                                                                <?php $mnTrim = trim($moduleName);
                                                                $isDone = !empty($completedSet[$mnTrim]); ?>
                                                                <span class="badge <?php echo $isDone ? 'badge-success' : 'badge-light'; ?> mr-1"><?php echo htmlspecialchars($mnTrim); ?></span>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="row">
                                                    <div class="col-md-4 px-3">
                                                        <strong class="h6">Duration:</strong>
                                                        <span class="text-primary"><?php echo $topic['completion_days']; ?> days</span>
                                                    </div>
                                                    <div class="col-md-4 px-3">
                                                        <strong class="h6">Next Start:</strong>
                                                        <span class="text-info"><?php echo $topic['next_start_days']; ?> days</span>
                                                    </div>
                                                    <div class="col-md-4 px-3">
                                                        <strong class="h6">Status:</strong>
                                                        <span class="badge badge-<?php echo $topicBadge; ?>"><?php echo $topicStatus; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    // After rendering topic, update gating for subsequent topics based on completion status
                                    $totalModulesForTopic = (int)($topic['module_count'] ?? 0);
                                    $completedModulesForTopic = (int)($topic['completed_count'] ?? 0);
                                    $priorTopicsCompleted = ($priorTopicsCompleted && ($totalModulesForTopic > 0 ? ($completedModulesForTopic >= $totalModulesForTopic) : true));
                                endforeach; ?>
                            <?php endif; ?>

                            <!-- Handover Step - Always visible -->
                            <?php
                            $handoverStepNumber = count($trainingTopics) + 4; // After all topics
                            if ($allStepsCompleted) {
                                $handoverStatusBadge = $handoverCompleted ? 'success' : ($handoverOverdue ? 'danger' : 'warning');
                                $handoverStatusText = $handoverCompleted ? 'Completed' : ($handoverOverdue ? 'Overdue' : 'Pending');
                            } else {
                                $handoverStatusBadge = 'secondary';
                                $handoverStatusText = 'Pending';
                            }
                            ?>
                            <div class="timeline-item<?php echo ($highlightId === 'handover' ? ' highlight' : ''); ?>" data-filter-text="handover support team">
                                <div class="timeline-marker bg-info">
                                    <i class="fa fa-handshake-o"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header d-flex align-items-center justify-content-between flex-wrap">
                                        <div class="d-flex align-items-center flex-wrap">
                                            <h6 class="mb-0 mr-2">Handover to Support Team</h6>
                                            <small class="text-muted">Step <?php echo $handoverStepNumber; ?></small>
                                        </div>
                                        <div class="d-flex align-items-center flex-wrap">
                                            <span class="badge badge-<?php echo $handoverStatusBadge; ?> mr-3"><?php echo $handoverStatusText; ?></span>
                                            <?php if ($allStepsCompleted && $handoverDueDate): ?>
                                                <span class="<?php echo $handoverOverdue ? 'text-danger font-weight-bold' : 'text-muted'; ?>">
                                                    Due: <?php echo $handoverDueDate->format('d M Y'); ?>
                                                    <?php if ($handoverOverdue): ?>
                                                        <i class="fa fa-exclamation-circle text-danger ml-1" title="Overdue by <?php echo $handoverOverdueDays; ?> days"></i>
                                                    <?php endif; ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($allStepsCompleted && $handoverDate): ?>
                                                <span class="text-muted ml-3"> • </span>
                                                <span class="text-success ml-2">Completed: <?php echo $handoverDate->format('d M Y, h:i A'); ?><?php if ($handoverDelayed): ?><i class="fa fa-exclamation-circle text-danger ml-1" title="Completed after due date"></i><?php endif; ?></span>
                                            <?php endif; ?>
                                            <?php if (!$handoverCompleted && $allStepsCompleted): ?>
                                                <?php if ($trainingFeedbackSubmitted): ?>
                                                    <button type="button" class="btn btn-primary btn-sm ml-3" data-toggle="modal" data-target="#handoverModal">
                                                        <i class="fa fa-handshake-o mr-1"></i> Handover Now
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" 
                                                        class="btn btn-info btn-sm ml-3" 
                                                        onclick='copyTrainingFormUrl(<?php echo json_encode($trainingFormUrl, JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode($company_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                                                        title="Copy Training Feedback Form URL">
                                                        <i class="fa fa-copy mr-1"></i> Copy Feedback Form URL
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="timeline-body">
                                        <?php if ($handoverCompleted): ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong class="h6">Post Implementation Remark:</strong>
                                                    <p class="text-muted"><?php echo htmlspecialchars($postImplementationRemark ?: 'N/A'); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong class="h6">Customer Expectation Remark:</strong>
                                                    <p class="text-muted"><?php echo htmlspecialchars($customerExpectationRemark ?: 'N/A'); ?></p>
                                                </div>
                                            </div>
                                            <?php if ($handoverByName): ?>
                                                <div class="mt-2">
                                                    <small class="text-muted">Handed over by: <?php echo htmlspecialchars($handoverByName); ?></small>
                                                </div>
                                            <?php endif; ?>
                                        <?php elseif ($allStepsCompleted): ?>
                                            <?php if ($trainingFeedbackSubmitted): ?>
                                                <p class="text-muted mb-0">All onboarding steps are completed. Please handover the company to the support team.</p>
                                            <?php else: ?>
                                                <p class="text-warning mb-2"><strong>Training feedback form must be filled before handover.</strong></p>
                                                <p class="text-muted mb-0">All onboarding steps are completed. Please ensure the training feedback form is submitted before handing over the company to the support team.</p>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <p class="text-muted mb-0">This step will be available once all onboarding steps (Welcome Email, WhatsApp Group, Setup, and Training Topics) are completed.</p>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($handoverCompleted): ?>
                                        <div class="timeline-complete-icon" title="Completed">
                                            <i class="fa fa-check-circle"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Handover Modal -->
<div class="modal fade" id="handoverModal" tabindex="-1" role="dialog" aria-labelledby="handoverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="handoverModalLabel">
                    <i class="fa fa-handshake-o mr-2"></i>Handover Company to Support Team
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="handoverForm">
                <div class="modal-body">
                    <input type="hidden" name="company_id" value="<?php echo $company_id; ?>">
                    <div class="form-group">
                        <label for="post_implementation_remark" class="font-weight-bold">Post Implementation Remark <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="post_implementation_remark" name="post_implementation_remark" rows="4" required placeholder="Enter post implementation remarks..."></textarea>
                        <small class="form-text text-muted">Describe the implementation status and any important notes.</small>
                    </div>
                    <div class="form-group">
                        <label for="customer_expectation_remark" class="font-weight-bold">Customer Expectation Remark <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="customer_expectation_remark" name="customer_expectation_remark" rows="4" required placeholder="Enter customer expectation remarks..."></textarea>
                        <small class="form-text text-muted">Describe customer expectations and any follow-up requirements.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-handshake-o mr-1"></i>Complete Handover
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>
<style>
    .timeline {
        position: relative;
        padding: 10px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 30px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #e9ecef;
        z-index: 0;
    }

    /* Green progress line - more prominent */
    .timeline::after {
        content: '';
        position: absolute;
        left: 30px;
        top: 0;
        width: 3px;
        height: var(--green-line-height, 0px);
        background: linear-gradient(to bottom, #28a745, #20c997);
        box-shadow: 0 0 8px rgba(40, 167, 69, 0.4);
        z-index: 1;
        transition: height 0.5s ease-in-out;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
        padding-left: 80px;
    }

    .timeline-marker {
        position: absolute;
        left: 20px;
        top: 0;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 10px;
        z-index: 2;
        border: 3px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .timeline-item:has(.timeline-header .fa-check-circle) .timeline-marker {
        background: #28a745 !important;
        border-color: #28a745 !important;
    }

    /* Completed items: ensure marker turns green (inside .timeline-header) */
    .timeline-item:has(.timeline-header .badge-success:not(.progress_badge)) .timeline-marker {
        background: #28a745 !important;
        border-color: #28a745 !important;
    }

    /* CRM timeline visual parity */
    #crmTimeline .timeline-card {
        border-left: 4px solid #17a2b8;
        padding: 12px 16px;
        border-radius: 6px;
    }
    #crmTimeline .timeline-item.highlight .timeline-card {
        border-left-color: #ffc107;
        box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.15);
    }
    #crmTimeline .timeline-title {
        font-weight: 600;
    }
    #crmTimeline .timeline-header .badge {
        font-size: 12px;
        padding: 4px 8px;
    }

    /* Current highlighted step - pulsing effect (inside .timeline-header only) */
    .timeline-item.highlight:has(.timeline-header) .timeline-marker {
        background: #ffc107 !important;
        border-color: #ffc107 !important;
        box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.4);
        animation: pulseHighlight 2s infinite;
    }

    .timeline-item:not(:has(.fa-check-circle)):not(:has(.badge-success)):not(.highlight) .timeline-marker {
        background: #6c757d !important;
        border-color: #6c757d;
    }

    .timeline-content {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        padding-right: 56px;
        /* reserve space for right-side complete icon */
        position: relative;
        transition: all 0.3s ease;
    }

    /* Large completed check icon positioned at right-middle inside content */
    .timeline-complete-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #28a745;
        z-index: 3;
    }

    .timeline-complete-icon .fa-check-circle {
        font-size: 26px;
    }

    .timeline-content::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 15px;
        width: 0;
        height: 0;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        border-right: 8px solid #dee2e6;
    }

    .timeline-content::after {
        content: '';
        position: absolute;
        left: -7px;
        top: 16px;
        width: 0;
        height: 0;
        border-top: 7px solid transparent;
        border-bottom: 7px solid transparent;
        border-right: 7px solid #f8f9fa;
    }

    /* Completed steps content styling */
    .timeline-item:has(.fa-check-circle) .timeline-content {
        background: #f8fff9;
        border-color: #d4edda;
    }

    .timeline-item:has(.fa-check-circle) .timeline-content::after {
        border-right-color: #f8fff9;
    }

    /* Current step highlighting */
    .timeline-item.highlight .timeline-content {
        background: #fff3cd;
        border-color: #ffc107;
        border-width: 2px;
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);
    }

    .timeline-item.highlight .timeline-content::after {
        border-right-color: #fff3cd;
    }

    .timeline-header h6 {
        color: #495057;
        font-weight: 600;
    }

    .timeline-body {
        margin-top: 8px;
    }

    .badge {
        font-size: 0.8em;
    }

    @keyframes pulseHighlight {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
            transform: scale(1);
        }
    }

    /* Progress indicator text */
    .progress-text {
        position: absolute;
        left: 15px;
        top: var(--green-line-height, 0px);
        transform: translateY(-50%);
        background: #28a745;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: bold;
        z-index: 3;
        white-space: nowrap;
    }

    .progress-text::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -4px;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 4px solid #28a745;
    }

    @media (max-width: 768px) {
        .timeline::before {
            left: 20px;
            width: 2px;
        }

        .timeline::after {
            left: 20px;
            width: 2px;
        }

        .timeline-item {
            padding-left: 60px;
        }

        .timeline-marker {
            left: 10px;
            width: 18px;
            height: 18px;
            font-size: 8px;
            border-width: 2px;
        }

        .timeline-content {
            padding: 12px 56px 12px 12px;
            /* reserve space for right-side complete icon */
        }

        .timeline-content::before {
            left: -6px;
            top: 12px;
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
            border-right: 6px solid #dee2e6;
        }

        .timeline-content::after {
            left: -5px;
            top: 13px;
            border-top: 5px solid transparent;
            border-bottom: 5px solid transparent;
            border-right: 5px solid #f8f9fa;
        }
    }
</style>
<script src="./assets/js/jquery.min.js"></script>
<script>
    // Function to show toast notification (must be global)
    function showToast(message, type) {
        type = type || 'info';
        const toast = document.createElement('div');
        toast.className = 'toast-notification toast-' + type;
        toast.textContent = message;
        toast.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 12px 20px; background: ' + 
            (type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8') + 
            '; color: white; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); opacity: 0; transition: opacity 0.3s;';
        document.body.appendChild(toast);
        
        // Trigger fade in
        setTimeout(function() {
            toast.style.opacity = '1';
        }, 10);
        
        // Hide and remove toast after 3 seconds
        setTimeout(function() {
            toast.style.opacity = '0';
            setTimeout(function() {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
    
    // Fallback copy function using execCommand (must be global)
    function fallbackCopy(url) {
        const tempInput = document.createElement('input');
        tempInput.value = url;
        tempInput.style.position = 'fixed';
        tempInput.style.left = '-9999px';
        document.body.appendChild(tempInput);
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices
        
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                // Show success toast
                showToast('Training form URL copied to clipboard!', 'success');
            } else {
                showToast('Failed to copy URL. Please try again.', 'error');
            }
        } catch (err) {
            showToast('Failed to copy URL. Please try again.', 'error');
        }
        
        document.body.removeChild(tempInput);
    }
    
    // Function to copy training form URL to clipboard (must be global)
    function copyTrainingFormUrl(url, societyId) {
        // Use modern Clipboard API if available
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(function() {
                // Show success toast
                showToast('Training form URL copied to clipboard!', 'success');
            }).catch(function(err) {
                // Fallback to execCommand
                fallbackCopy(url);
            });
        } else {
            // Fallback for older browsers
            fallbackCopy(url);
        }
    }

    $(document).ready(function() {
        console.log('Company Onboarding Timeline loaded for Company ID: <?php echo $company_id; ?>');

        // Handover form submission
        $('#handoverForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var submitBtn = $(this).find('button[type="submit"]');
            var originalText = submitBtn.html();
            
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');
            
            $.ajax({
                url: 'controller/companyOnboardingController.php',
                type: 'POST',
                data: formData + '&action=handover',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#handoverModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to complete handover'));
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error: Failed to complete handover. Please try again.');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        function updateGreenLine() {
            const $timeline = $('.timeline');
            const $timelineItems = $('.timeline-item');
            let $lastCompletedStep = null;
            let $currentStep = null;

            $timelineItems.each(function() {
                const $item = $(this);

                // Check if this step is completed
                const hasSuccessBadge = $item.find('.badge-success').length > 0;
                const hasCheckIcon = $item.find('.fa-check-circle').length > 0;
                const isCompleted = hasSuccessBadge || hasCheckIcon;

                if (isCompleted) {
                    $lastCompletedStep = $item;
                }

                // Check if this is the current highlighted step
                if ($item.hasClass('highlight')) {
                    $currentStep = $item;
                }
            });

            // Determine where to draw the green line to
            let $targetStep = $currentStep || $lastCompletedStep;

            if ($targetStep && $targetStep.length > 0) {
                const timelineTop = $timeline.offset().top;
                const $marker = $targetStep.find('.timeline-marker');
                const markerCenter = $marker.offset().top + ($marker.outerHeight() / 2);
                const greenLineHeight = markerCenter - timelineTop;

                $timeline.css('--green-line-height', Math.max(0, greenLineHeight) + 'px');

                // Add progress indicator
                $('.progress-text').remove();
                if ($lastCompletedStep) {
                    // $timeline.append('<div class="progress-text">Current Progress</div>');
                    $('.progress-text').css('top', greenLineHeight + 'px');
                }
            } else {
                // If no completed or current steps, hide the green line
                $timeline.css('--green-line-height', '0px');
                $('.progress-text').remove();
            }
        }

        // Update green line on page load
        setTimeout(updateGreenLine, 100);

        // Update green line when window is resized or when collapses are toggled
        $(window).on('resize', updateGreenLine);

        $('.collapse').on('shown.bs.collapse hidden.bs.collapse', function() {
            setTimeout(updateGreenLine, 300);
        });

        // Expand / Collapse all
        $('#btnExpandAll').on('click', function() {
            $('.timeline-body.collapse').collapse('show');
            $('#collapseSetupModules').collapse('show');
        });

        $('#btnCollapseAll').on('click', function() {
            $('.timeline-body.collapse').collapse('hide');
            $('#collapseSetupModules').collapse('hide');
        });

        // Quick Search
        $('#timelineSearch').on('input', function() {
            const q = $(this).val().toLowerCase().trim();
            if (!q) {
                $('#timelineRoot .timeline-item').show();
                updateGreenLine();
                return;
            }
            $('#timelineRoot .timeline-item').each(function() {
                const t = ($(this).data('filter-text') || '').toString().toLowerCase();
                $(this).toggle(t.indexOf(q) !== -1);
            });
            updateGreenLine();
        });
    });
</script>