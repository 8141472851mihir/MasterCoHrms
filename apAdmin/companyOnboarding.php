<?php
extract($_REQUEST);
$countryId = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId'], 101) : (isset($countryId) ? $d->sanitizeReportFilterIdAsInt($countryId, 101) : 101);
$sId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : (isset($sId) ? $d->sanitizeReportFilterIdAsInt($sId) : 0);
$cId = isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : (isset($cId) ? $d->sanitizeReportFilterIdAsInt($cId) : 0);
$sId = $sId > 0 ? $sId : '';
$cId = $cId > 0 ? $cId : '';

// Persist onboarding filters so redirects (emails, status updates, etc.) keep context
$defaultFilters = [
    'countryId' => 101,
    'sId' => '',
    'cId' => '',
    'training_type' => '0',
    'rise_filter' => 'yes',
    'product_type' => 'hrms'
];
$filters = [];
foreach ($defaultFilters as $key => $default) {
    $value = $default;
    if (isset($_GET[$key]) && $_GET[$key] !== '') {
        $value = $_GET[$key];
    } elseif (isset($_SESSION['companyOnboarding_filters'][$key])) {
        $value = $_SESSION['companyOnboarding_filters'][$key];
    }

    if ($key === 'countryId') {
        $value = (intval($value) > 0) ? intval($value) : $default;
    } elseif ($key === 'product_type') {
        $value = ($value === 'crm' || $value === 'hrms') ? $value : $default;
    } elseif ($key === 'training_type') {
        $value = in_array((string)$value, ['0', '1', '2'], true) ? (string)$value : '0';
    } elseif ($value === null) {
        $value = $default;
    }

    $filters[$key] = $value;
}
$_SESSION['companyOnboarding_filters'] = $filters;

$countryId = $filters['countryId'];
// Myco Rise filter: default to "yes"
$rise_filter = $filters['rise_filter'];
// Product type filter: default to "hrms" (0 = HRMS, 1 = CRM)
$product_type = $filters['product_type'];

// Helper to reuse hidden inputs on forms that redirect back to onboarding
function renderCompanyOnboardingFilterInputs($filters)
{
    $html = '';
    foreach ($filters as $key => $value) {
        $html .= '<input type="hidden" name="' . htmlspecialchars($key, ENT_QUOTES) . '" value="' . htmlspecialchars($value, ENT_QUOTES) . '">';
    }
    return $html;
}
$filterHiddenInputs = renderCompanyOnboardingFilterInputs($filters);
?>
<?php
$result = $d->select(
    'training_module_master tmm 
                 JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id',
    "tmm.module_type = 0 AND tmm.training_module_status = 0 AND tmpm.is_required = 1"
);
$module_count = mysqli_num_rows($result) ?>
<?php
$trainingresult = $d->select(
    'training_module_master tmm 
                JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id',
    "tmm.module_type = 1 AND tmm.training_module_status = 0 AND tmpm.is_required = 1"
);
$training_count = mysqli_num_rows($trainingresult);
// Precompute per-participant total required training modules
$participantModuleCounts = [];
$pmcRes = $d->selectRow(
    "tmt.participant_type AS pid, COUNT(DISTINCT tmm.training_module_id) AS cnt",
    "training_module_master tmm 
     LEFT JOIN training_module_topics tmt ON tmm.topic_id = tmt.topic_id AND tmt.topic_type = '0'
     JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
    "tmm.module_type = 1 
     AND tmm.training_module_status = 0 
     AND tmpm.is_required = 1
     AND (tmt.topic_status = 1 OR tmt.topic_status IS NULL)",
    "GROUP BY tmt.participant_type"
);
if ($pmcRes && mysqli_num_rows($pmcRes) > 0) {
    while ($row = mysqli_fetch_assoc($pmcRes)) {
        $pid = isset($row['pid']) ? intval($row['pid']) : 0;
        $participantModuleCounts[$pid] = isset($row['cnt']) ? intval($row['cnt']) : 0;
    }
}
?>
<?php
$closureDateResult = $d->selectRow(
    "closure_date_setting.*",
    "closure_date_setting",
    "",
    ""
);
$closureDateData = mysqli_fetch_assoc($closureDateResult);
$closureDateCount = mysqli_num_rows($closureDateResult);
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Company Onboarding</h4>
            </div>
            <div class="col-sm-3 d-flex justify-content-end align-items-center">
                <button type="button" class="btn btn-danger btn-sm mr-2 d-none pulse-animation" id="step-delay-alert-btn" data-toggle="modal" data-target="#stepDelayAlertModal" title="Delay Alert">
                    <i class="fa fa-bell"></i>
                    <span class="badge badge-light ml-1" id="step-delay-alert-count">0</span>
                </button>
                <!-- <button type="button" class="btn btn-primary btn-sm" id="product-closure-date-btn" data-toggle="modal"
                    data-target="#closure-date-modal"><i class="fa fa-cogs"></i></button> -->
            </div>
        </div>

        <div class="row pt-2 pb-2">
            <div class="col-lg-12 px-4">
                <form action="" method="get" accept-charset="utf-8">

                    <div class="form-group row">
                        <label for="country_id" class="col-sm-1 col-form-label"> Country <span
                                class="required">*</span></label>
                        <div class="col-sm-3">
                            <select type="text" required="" id="country_id" onchange="this.form.submit()"
                                class="form-control single-select" name="countryId">
                                <option value="">-- Select --</option>
                                <?php
                                $qc = $d->select("countries", "flag=1");
                                while ($cData = mysqli_fetch_array($qc)) {
                                ?>
                                    <option <?php if (isset($countryId) && $cData['country_id'] == $countryId) {
                                                echo "selected";
                                            } ?> value="<?php echo $cData['country_id']; ?>">
                                        <?php echo $cData['name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <label for="state_id" class="col-sm-1 col-form-label"> State </label>
                        <div class="col-sm-3">
                            <?php if (isset($countryId)) {
                            ?>
                                <select type="text" onchange="this.form.submit()" required=""
                                    class="form-control single-select" id="state_id" name="sId">
                                    <option value=""> All</option>
                                    <?php
                                    $qs = $d->select("states", "country_id='$countryId'");
                                    while ($sData = mysqli_fetch_array($qs)) {
                                    ?>
                                        <option <?php if (isset($_GET['sId']) && $sData['state_id'] == $_GET['sId']) {
                                                    echo "selected";
                                                } ?> value="<?php echo $sData['state_id']; ?>">
                                            <?php echo $sData['name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            <?php } else { ?>
                                <select type="text" onchange="getCity();" required="" class="form-control single-select"
                                    id="state_id" name="sId">
                                    <option value="">-- Select --</option>
                                </select>
                            <?php } ?>
                        </div>

                        <label for="input-101" class="col-sm-1 col-form-label"> City</label>
                        <div class="col-sm-3">
                            <?php if (isset($_GET['cId']) && $sId > 0) {

                            ?>
                                <select onchange="this.form.submit()" type="text" required=""
                                    class="form-control single-select" id="city_id" name="cId">
                                    <option value=""> All</option>
                                    <?php
                                    if (isset($sId) && $sId > 0) {
                                        $appendStateQueryFilter = "state_id='$sId'";
                                    }

                                    $qcity = $d->select("cities", " $appendStateQueryFilter");
                                    while ($cityData = mysqli_fetch_array($qcity)) {
                                    ?>
                                        <option <?php if (isset($_GET['cId']) && $cityData['city_id'] == $_GET['cId']) {
                                                    echo "selected";
                                                } ?> value="<?php echo $cityData['city_id']; ?>">
                                            <?php echo $cityData['name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            <?php } else { ?>
                                <select onchange="this.form.submit()" type="text" required=""
                                    class="form-control single-select" name="cId" id="city_id">
                                    <option value="">-- Select --</option>

                                </select>
                            <?php } ?>
                        </div>

                        <label for="product_type" class="col-sm-1 col-form-label mt-3"> Product </label>
                        <div class="col-sm-3 mt-3">
                            <select type="text" required="" id="product_type" onchange="toggleTrainingTypeFilter(); this.form.submit();"
                                class="form-control single-select" name="product_type">
                                <option value="hrms" <?php echo (!isset($_GET['product_type']) || $_GET['product_type'] === "hrms") ? 'selected' : ''; ?>>HRMS</option>
                                <option value="crm" <?php echo (isset($_GET['product_type']) && $_GET['product_type'] === 'crm') ? 'selected' : ''; ?>>CRM</option>
                            </select>
                        </div>

                        <label for="training_type" class="col-sm-1 col-form-label mt-3" id="training_type_label"> Training type </label>
                        <div class="col-sm-3 mt-3" id="training_type_div">
                            <select type="text" required="" id="training_type" onchange="this.form.submit()"
                                class="form-control single-select" name="training_type">
                                <option value="0" <?php echo (!isset($_GET['training_type']) || $_GET['training_type'] == '0') ? "selected" : ""; ?>>All</option>
                                <option value="1" <?php echo (isset($_GET['training_type']) && $_GET['training_type'] == '1') ? "selected" : ""; ?>>Setup Training</option>
                                <option value="2" <?php echo (isset($_GET['training_type']) && $_GET['training_type'] == '2') ? "selected" : ""; ?>>Product Training</option>
                            </select>
                            <!-- Hidden input to ensure training_type is submitted when CRM is selected -->
                            <input type="hidden" id="training_type_hidden" name="training_type" value="0">
                        </div>

                        <label for="rise_filter" class="col-sm-1 col-form-label mt-3"> Myco Rise </label>
                        <div class="col-sm-3 mt-3">
                            <select id="rise_filter" name="rise_filter" class="form-control single-select" onchange="this.form.submit()">
                                <option value="yes" <?php echo (!isset($_GET['rise_filter']) || $_GET['rise_filter'] === "yes") ? 'selected' : ''; ?>>Myco Rise</option>
                                <option value="no" <?php echo (isset($_GET['rise_filter']) && $_GET['rise_filter'] === 'no') ? 'selected' : ''; ?>>Before Myco Rise</option>
                                <option value="all" <?php echo (isset($_GET['rise_filter']) && $_GET['rise_filter'] === 'all') ? 'selected' : ''; ?>>All</option>
                            </select>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        <?php if (isset($countryId) && filter_var($countryId, FILTER_VALIDATE_INT) == true) { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Id</th>
                                            <th>Company</th>
                                            <th>City</th>
                                            <th>Implementation Person</th>
                                            <th>Support Person Name</th>
                                            <th>Sales Closure Date</th>
                                            <th>Company Created Date</th>
                                            <?php
                                            if (isset($_GET['product_type']) && $_GET['product_type'] == 'crm') {
                                                echo '<th>CRM Created Date</th>';
                                            }
                                            ?>
                                            <th><?php echo ($is_crm ?? false) ? 'CRM Welcome Email' : 'Welcome Email'; ?></th>
                                            <th>Whatsapp Group</th>
                                            <th>Responding Status</th>
                                            <th>Training Feedback Form</th>
                                            <?php 
                                            $is_crm = isset($_GET['product_type']) && $_GET['product_type'] 
                                            == 'crm';
                                            if ($is_crm) { ?>
                                                <th>CRM Process</th>
                                            <?php } ?>
                                            <?php
                                            // Get training_type, default to '0' (All) for CRM
                                            $training_type = isset($_GET['training_type']) ? $_GET['training_type'] : '0';
                                            if ($is_crm) {
                                                $training_type = '0'; // CRM always shows all columns
                                            }
                                            if ($training_type != 2 && !$is_crm) {
                                            ?>
                                                <th>Setup Status</th>
                                                <!-- <th>Setup Training Status</th> -->
                                                <th>Setup Data Receive Status</th>
                                                <th>Setup Data Upload Status</th>
                                                <?php
                                                $sessionList = [];
                                                $session_module_count = array();
                                                $sessionQuery = $d->select("session_day_master", "session_day_status=0");
                                                while ($session_type = mysqli_fetch_array($sessionQuery)) {
                                                    $sessionList[] = $session_type;
                                                    $session_module_count[(int)$session_type['session_day_id']] = 0;
                                                }
                                                // One grouped count instead of per-session query
                                                $sessionCountQ = $d->selectRow(
                                                    "tmm.session_day_id, COUNT(DISTINCT tmm.training_module_id) AS cnt",
                                                    "training_module_master tmm JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
                                                    "tmm.module_type = 0 AND tmm.training_module_status = 0 AND tmpm.is_required = 1",
                                                    "GROUP BY tmm.session_day_id"
                                                );
                                                while ($sc = mysqli_fetch_assoc($sessionCountQ)) {
                                                    $session_module_count[(int)$sc['session_day_id']] = (int)$sc['cnt'];
                                                }
                                                foreach ($sessionList as $session_type) {
                                                    /* echo "<th>{$session_type['session_day_name']} training status</th>"; */
                                                    echo "<th>{$session_type['session_day_name']} data received status</th>";
                                                    echo "<th>{$session_type['session_day_name']} data upload status</th>";
                                                }
                                                ?>
                                            <?php
                                            }
                                            ?>
                                            <?php
                                            if ($training_type != 1 && !$is_crm) {
                                            ?>
                                                <th>Product Training </th>
                                                <th>Training Status</th>
                                                <?php
                                                $participantList = [];
                                                $participantQuery = $d->select("training_participants_type", "status=0");
                                                while ($participant_type = mysqli_fetch_array($participantQuery)) {
                                                    $participantList[] = $participant_type;
                                                    echo "<th>{$participant_type['participant_name']} Training</th>";
                                                }
                                                ?>
                                            <?php
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $is_crm = isset($_GET['product_type']) && $_GET['product_type'] === 'crm';
                                        $training_type = isset($_GET['training_type']) ? $_GET['training_type'] : '0';
                                        if ($is_crm) {
                                            $training_type = '0'; 
                                        }

                                        if (isset($sId) && $sId > 0) {
                                            $appendStateQuery = " AND state_id='$sId'";
                                        }

                                        if (isset($cId) && $cId > 0) {
                                            $appendCityQuery = " AND city_id='$cId'";
                                        }

                                        // Apply Myco Rise filter
                                        $appendRiseQuery = "";
                                        if (!isset($_GET['rise_filter']) || $_GET['rise_filter'] === "yes") {
                                            // Show only Myco Rise companies (treat null as 0)
                                            $appendRiseQuery = " AND IFNULL(from_rise_event,0) = 1";
                                        } elseif (isset($_GET['rise_filter']) && $_GET['rise_filter'] === 'no') {
                                            // Show companies from before Myco Rise
                                            $appendRiseQuery = " AND (from_rise_event IS NULL OR from_rise_event = 0)";
                                        } else {
                                            // 'all' → no additional filter
                                            $appendRiseQuery = "";
                                        }

                                        $appendProductQuery = "";
                                        if (isset($_GET['product_type'])) {
                                            if ($_GET['product_type'] == 'crm') {
                                                $appendProductQuery = " AND IFNULL(crm_created,0) = 1";
                                            }
                                        }

                                        $q = $d->selectRow(
                                            "society_master.*,transection_master.payment_mode,transection_master.transection_amount",
                                            "society_master LEFT JOIN transection_master ON transection_master.society_id=society_master.society_id",
                                            "society_master.created_on_society_server = 1 AND country_id='$countryId' $appendStateQuery $appendCityQuery $appendRiseQuery $appendProductQuery",
                                            "GROUP BY society_master.society_id order by society_id  DESC"
                                        );
                                        // Materialize rows + batch-load latest training forms (avoid N+1 per row)
                                        $societyRows = [];
                                        $societyIdsForForms = [];
                                        while ($data = mysqli_fetch_array($q)) {
                                            $societyRows[] = $data;
                                            $societyIdsForForms[] = (int)$data['society_id'];
                                        }
                                        $formBySociety = [];
                                        if (!empty($societyIdsForForms)) {
                                            $societyIdsIn = implode(',', array_map('intval', $societyIdsForForms));
                                            $formsQ = $d->selectRow(
                                                "form_id, society_id, submitted_date",
                                                "training_completion_form_master",
                                                "society_id IN ($societyIdsIn)",
                                                "ORDER BY submitted_date DESC"
                                            );
                                            while ($formRow = mysqli_fetch_assoc($formsQ)) {
                                                $sid = (int)$formRow['society_id'];
                                                // First row wins (latest by submitted_date DESC)
                                                if (!isset($formBySociety[$sid])) {
                                                    $formBySociety[$sid] = $formRow;
                                                }
                                            }
                                        }
                                        foreach ($societyRows as $data) {
                                            extract($data);
                                        ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td>
                                                    <span style="display: none;"><?php echo $society_id; ?></span>
                                                    <?php echo '' . $d->short_app_name() . '_' . $society_id; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="<?php echo $sub_domain; ?>apAdmin/" target="_blank"><?php echo $society_name; ?></a>
                                                        <?php
                                                        $timelineTitle = ($is_crm=='0')?"HRMS Timeline":"CRM Timeline";
                                                        $timelineQuery = http_build_query(array_merge(['company_id' => $society_id], $filters));
                                                        $timelineUrl = 'companyOnboardingTimeline?' . $timelineQuery;
                                                        ?>
                                                        <a href="<?php echo htmlspecialchars($timelineUrl, ENT_QUOTES); ?>" class="btn btn-sm btn-outline-info ml-2" title="<?php echo $timelineTitle; ?>">
                                                            <i class="fa fa-clock-o"></i> <?php echo $timelineTitle; ?>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td><?php echo $city_name; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span id="implementation_display_<?php echo $society_id; ?>" class="implementation-display mr-2">
                                                            <?php echo !empty($implementation_name) ? htmlspecialchars($implementation_name) : ''; ?>
                                                        </span>
                                                        <button type="button" class="btn text-warning btn-sm ml-2"
                                                            onclick='openImplementationEditModal(<?php echo json_encode($society_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode(isset($implementation_name) ? $implementation_name : '', JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                                                            title="Edit Implementation Person">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                    </div>
                                                    <small id="implementation_update_status_<?php echo $society_id; ?>" class="text-success d-none">Updated</small>
                                                </td>
                                                <td><?php echo !empty($support_name) ? htmlspecialchars($support_name) : ''; ?></td>
                                                <td>
                                                    <?php echo ($sales_closure_date != "") ? date("D, d-M-Y", strtotime($sales_closure_date)) : ""; ?>
                                                </td>
                                                <td>
                                                    <?php echo ($created_date != "") ? date("D, d-M-Y", strtotime($created_date)) : ""; ?>
                                                </td>
                                                <?php
                                                if (isset($_GET['product_type']) && $_GET['product_type'] == 'crm') {
                                                    ?>
                                                    <td>
                                                        <?php echo (isset($crm_created_date) && $crm_created_date != "") ? date("D, d-M-Y", strtotime($crm_created_date)) : ""; ?>
                                                    </td>
                                                    <?php
                                                }
                                                ?>
                                                    <td>
                                                        <?php
                                                        // For CRM, use CRM-specific welcome email flags; otherwise use default
                                                        $welcomeFlag = ($is_crm) ? (isset($is_crm_welcome_email_send) ? (int)$is_crm_welcome_email_send : 0) : (isset($is_welcome_email_send) ? (int)$is_welcome_email_send : 0);
                                                        $welcomeDate = ($is_crm) ? ($crm_welcome_email_send_date ?? '') : ($welcome_email_send_date ?? '');
                                                        $welcomeTitle = $is_crm ? 'Send CRM Welcome Email' : 'Send Welcome Email';
                                                        $welcomeLabel = $is_crm ? 'CRM Welcome Email' : 'Welcome Email';
                                                        if ($welcomeFlag === 0) { ?>
                                                            <button data-toggle="modal" data-target="#emailTypeModel"
                                                                title="<?php echo $welcomeTitle; ?>" class="btn btn-info btn-sm"
                                                                onclick='setSocietyid(<?php echo json_encode($society_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode('sendEmail', JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode(isset($payment_mode) ? $payment_mode : '', JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode(isset($transection_amount) ? $transection_amount : '', JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode(isset($secretary_email) ? $secretary_email : '', JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode('', JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode($is_crm ? 'crm' : 'hrms', JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                                                <?php echo $welcomeLabel; ?>
                                                            </button>
                                                        <?php } else {
                                                            echo ($welcomeDate != "") ? date("d-M-Y", strtotime($welcomeDate)) . " <br> " . date("D, h:i A", strtotime($welcomeDate)) : '';
                                                        } ?>
                                                    </td>
                                                <td>
                                                    <?php
                                                    $waGroupLink = isset($whatsapp_group_link) ? trim((string)$whatsapp_group_link) : '';
                                                    $waGroupLinkDisplay = $waGroupLink !== ''
                                                        ? (strlen($waGroupLink) > 28 ? substr($waGroupLink, 0, 28) . '...' : $waGroupLink)
                                                        : 'Add join link';
                                                    if ($is_whatsapp_group_created == 0) { ?>
                                                        <form method="POST" action="controller/createSocietyAutoController.php" class="d-inline-block">
                                                            <?php echo $filterHiddenInputs; ?>
                                                            <input type="hidden" name="is_whatsapp_group_created"
                                                                value="is_whatsapp_group_created">
                                                            <input type="hidden" name="society_id"
                                                                value="<?php echo $society_id; ?>">
                                                            <button type="submit" class="form-btn btn btn-success btn-sm"
                                                                title="WhatsApp Group Created?">

                                                                <i class="fa fa-whatsapp" aria-hidden="true"></i>

                                                            </button>
                                                        </form>
                                                    <?php } else {
                                                        echo date("d-M-Y", strtotime($whatsapp_group_created_date)) . " <br> " . date("D, h:i A", strtotime($whatsapp_group_created_date));
                                                    } ?>
                                                    <div class="mt-1 d-flex align-items-center flex-wrap" style="gap:6px;">
                                                        <small class="text-muted">Join:</small>
                                                        <span id="wa_group_link_display_<?php echo (int)$society_id; ?>"
                                                            class="text-primary"
                                                            style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:inline-block;vertical-align:middle;"
                                                            title="<?php echo htmlspecialchars($waGroupLink, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <?php echo htmlspecialchars($waGroupLinkDisplay, ENT_QUOTES, 'UTF-8'); ?>
                                                        </span>
                                                        <a href="javascript:void(0);"
                                                            title="Edit WhatsApp Join Link"
                                                            onclick='openWhatsappGroupLinkModal(<?php echo json_encode((int)$society_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode(isset($society_name) ? $society_name : '', JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                                            <i class="fa fa-pencil text-warning"></i>
                                                        </a>
                                                        <a href="javascript:void(0);"
                                                            id="wa_group_link_copy_<?php echo (int)$society_id; ?>"
                                                            title="Copy Join Link"
                                                            style="<?php echo $waGroupLink === '' ? 'display:none;' : ''; ?>"
                                                            onclick='copyWhatsappGroupLink(<?php echo json_encode((int)$society_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                                            <i class="fa fa-copy text-info"></i>
                                                        </a>
                                                        <input type="hidden" id="wa_group_link_value_<?php echo (int)$society_id; ?>" value="<?php echo htmlspecialchars($waGroupLink, ENT_QUOTES, 'UTF-8'); ?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $is_not_responding = isset($data['is_not_responding']) ? (int)$data['is_not_responding'] : 0;
                                                    $buttonClass = ($is_not_responding == 0) ? 'btn-success-new' : 'btn-danger';
                                                    $buttonCondition = ($is_not_responding == 0) ? 'Responding' : 'Not Responding';
                                                    $status = ($is_not_responding == 0) ? 'respondingStatusDeactive' : 'respondingStatusActive';
                                                    $newStatus = ($is_not_responding == 0) ? 'respondingStatusActive' : 'respondingStatusDeactive';
                                                    $newStatusVal = ($is_not_responding == 0) ? '1' : '0';
                                                    $statusValue = ($is_not_responding == 0) ? '0' : '1';
                                                    $posText = 'Responding';
                                                    $negText = 'Not Responding';
                                                    ?>
                                                    <input type="button" 
                                                        class="btn btn-sm pl-1 pr-1 <?php echo $buttonClass; ?>" 
                                                        id="<?php echo 'responding_btn_' . $society_id; ?>"
                                                        onclick='changeStatusNew(<?php echo json_encode($society_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode($status, JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode($newStatus, JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode($statusValue, JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode($newStatusVal, JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode('responding_btn_' . $society_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode('', JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode('0', JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode('./controller/statusController.php', JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode($negText, JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode($posText, JSON_HEX_APOS | JSON_HEX_QUOT); ?>);'
                                                        data-size="small" 
                                                        value="<?php echo htmlspecialchars($buttonCondition, ENT_QUOTES, 'UTF-8'); ?>" />
                                                </td>
                                                <td>
                                                    <?php
                                                    // Check if training form has been submitted (from prefetched map)
                                                    $form_submitted = false;
                                                    $form_id = 0;
                                                    $submitted_date_formatted = '';
                                                    
                                                    if (isset($formBySociety[(int)$society_id])) {
                                                        $form_row = $formBySociety[(int)$society_id];
                                                        $form_submitted = true;
                                                        $form_id = (int)$form_row['form_id'];
                                                        $submitted_date = $form_row['submitted_date'];
                                                        $submitted_date_formatted = !empty($submitted_date) ? date('d M Y, h:i A', strtotime($submitted_date)) : '';
                                                    }
                                                    
                                                    if ($form_submitted) {
                                                        // Show submitted date with view icon
                                                        ?>
                                                        <span class="text-muted"><?php echo htmlspecialchars($submitted_date_formatted); ?></span>
                                                        <a href="javascript:void(0);" onclick="viewFormDetails(<?php echo $form_id; ?>);" class="ml-2" title="View Training Form Details">
                                                            <i class="fa fa-eye text-primary"></i>
                                                        </a>
                                                        <?php
                                                    } else {
                                                        // Show copy URL button
                                                        // Encrypt society_id for the training Feedback form
                                                        $encrypted_society_id = $d->encryptDecrypt("encrypt", $society_id);
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
                                                        $training_form_url = $protocol . "://" . $host . $basePath . "/trainingImplementationForm.php?c=" . urlencode($encrypted_society_id);
                                                        ?>
                                                        <button type="button" 
                                                            class="btn btn-sm btn-primary" 
                                                            onclick='copyTrainingFormUrl(<?php echo json_encode($training_form_url, JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode($society_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                                                            title="Copy Training Feedback Form">
                                                            <i class="fa fa-copy"></i> Copy URL
                                                        </button>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <?php if ($is_crm) { ?>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary"
                                                            onclick='openCrmProcessModal(<?php echo json_encode($society_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode($society_name, JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode(isset($secretary_email) ? $secretary_email : '', JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode(isset($payment_mode) ? $payment_mode : '', JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode(isset($transection_amount) ? $transection_amount : '', JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                                            CRM Process
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-info ml-1"
                                                            onclick='downloadCrmProcessDetails(<?php echo json_encode($society_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode($society_name, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                                                            title="View/Download CRM Process Details">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    </td>
                                                <?php } ?>

                                                <?php
                                                if ($training_type != 2 && !$is_crm) {
                                                ?>
                                                    <td>
                                                        <a href="javascript:void" data-toggle="modal"
                                                            data-target="#updateStatusModal"
                                                            onclick='fetchSocietyName(<?php echo json_encode($society_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode(isset($society_name) ? $society_name : '', JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                                                            class="btn btn-sm btn-info waves-effect waves-light m-1"
                                                            title="Update Status">Update Status</a>
                                                        <a href="javascript:void(0);" data-toggle="modal"
                                                            data-target="#SetupTrainingStatusModal"
                                                            onclick='fetchTrainingModules(<?php echo json_encode($society_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode(isset($society_name) ? $society_name : '', JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                                                            class="btn btn-sm btn-success waves-effect waves-light m-1"
                                                            title="Setup Training Status"><i class="fa fa-eye"></i></a>
                                                        <a href="javascript:void(0);"
                                                            onclick='openScheduleMeetingModal(<?php echo json_encode($society_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode($bms_admin_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                                                            class="btn btn-sm btn-primary">
                                                            Schedule Setup
                                                        </a>
                                                    </td>



                                                    <!-- <td>
                                                        <?php if ($data['setup_training_status'] != '1') {
                                                            echo "<span class='text-danger'>Pending</span>";
                                                        } else {
                                                            echo "<span class='text-success'>Completed</span>";
                                                        } ?>

                                                        <?= !empty($data['setup_training_percentage']) ? $data['setup_training_percentage'] . '/' : '0/' ?>

                                                        <?= $module_count ?>
                                                    </td> -->



                                                    <td>
                                                        <?php if ($data['setup_data_receive_status'] != '1') {
                                                            echo "<span class='text-danger'>Pending</span>";
                                                        } else {
                                                            echo "<span class='text-success'>Completed</span>";
                                                        } ?>
                                                        <?= !empty($data['setup_data_receive_percentage']) ? $data['setup_data_receive_percentage'] . '/' : '0/' ?>
                                                        <?= $module_count ?>
                                                    </td>

                                                    <td>
                                                        <?php if ($data['setup_onboarding_status'] != '1') {
                                                            echo "<span class='text-danger'>Pending</span>";
                                                        } else {
                                                            echo "<span class='text-success'>Completed</span>";
                                                        } ?>
                                                        <?= !empty($data['setup_onboarding_percentage']) ? $data['setup_onboarding_percentage'] . '/' : '0/' ?>
                                                        <?= $module_count ?>
                                                    </td>


                                                    <?php
                                                    $setup_data = json_decode($data['setup_data'], true) ?? [];

                                                    foreach ($sessionList as $session_type) {
                                                        $sessionId = $session_type['session_day_id'];
                                                        $found = false;

                                                        if (!empty($setup_data)) {
                                                            foreach ($setup_data as $session) {
                                                                if ($session['session_day_id'] == $sessionId) {
                                                                    $training_completed_color = ($session['training_completed'] >= $session_module_count[$sessionId]) ? 'green' : 'red';
                                                                    $data_receive_completed_color = ($session['data_receive_completed'] >= $session_module_count[$sessionId]) ? 'green' : 'red';
                                                                    $onboarding_completed_color = ($session['onboarding_completed'] >= $session_module_count[$sessionId]) ? 'green' : 'red';

                                                                    /* echo "<td style='color: $training_completed_color;'>{$session['training_completed']}/{$session_module_count[$sessionId]}</td>"; */
                                                                    echo "<td style='color: $data_receive_completed_color;'>{$session['data_receive_completed']}/{$session_module_count[$sessionId]}</td>";
                                                                    echo "<td style='color: $onboarding_completed_color;'>{$session['onboarding_completed']}/{$session_module_count[$sessionId]}</td>";

                                                                    $found = true;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        if (!$found) {
                                                            // echo "<td style='color: red;'>0/{$session_module_count[$sessionId]}</td>";
                                                            echo "<td style='color: red;'>0/{$session_module_count[$sessionId]}</td>";
                                                            echo "<td style='color: red;'>0/{$session_module_count[$sessionId]}</td>";
                                                        }
                                                    }
                                                    ?>
                                                <?php
                                                }
                                                ?>
                                                <?php
                                                if ($training_type != 1 && !$is_crm) {
                                                ?>
                                                    <td>
                                                        <a href="javascript:void(0);" data-toggle="modal"
                                                            data-target="#ProductTrainingStatusModal"
                                                            onclick='fetchProductTrainingModules(<?php echo json_encode($society_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>,<?php echo json_encode(isset($society_name) ? $society_name : '', JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                                                            class="btn btn-sm btn-success waves-effect waves-light m-1"
                                                            title="Product Training Status"><i class="fa fa-eye"></i></a>
                                                        <a href="javascript:void(0);"
                                                            onclick='openBatchMeetingModal(<?php echo json_encode($society_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode($bms_admin_id, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                                                            class="btn btn-sm btn-primary">
                                                            Schedule Batch Meeting
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <?php if ($data['training_status'] != '1') {
                                                            echo "<span class='text-danger'>Pending</span>";
                                                        } else {
                                                            echo "<span class='text-success'>Completed</span>";
                                                        } ?>
                                                        <?= !empty($data['training_percentage']) ? $data['training_percentage'] . '/' : '0/' ?>
                                                        <?= $training_count ?>
                                                    </td>
                                                    <?php
                                                    $training_data = json_decode($data['training_data'], true) ?? [];

                                                    foreach ($participantList as $participant_type) {
                                                        $participantId = $participant_type['participants_type_id'];
                                                        $found = false;

                                                        if (!empty($training_data)) {
                                                            foreach ($training_data as $participant) {
                                                                if ($participant['participant_id'] == $participantId) {
                                                                    $pid = intval($participantId);
                                                                    $ptotal = isset($participantModuleCounts[$pid]) ? $participantModuleCounts[$pid] : 0;
                                                                    $color = ($ptotal == 0) ? 'green' : (($participant['completed_percentage'] >= $ptotal) ? 'green' : 'red');
                                                                    echo "<td style='color: $color;'>{$participant['completed_percentage']}/{$ptotal}</td>";
                                                                    $found = true;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        if (!$found) {
                                                            $pid = intval($participant_type['participants_type_id']);
                                                            $ptotal = isset($participantModuleCounts[$pid]) ? $participantModuleCounts[$pid] : 0;
                                                            $color = ($ptotal == 0) ? 'green' : 'red';
                                                            echo "<td style='color: $color;'>0/{$ptotal}</td>";
                                                        }
                                                    }
                                                    ?>
                                                <?php
                                                }
                                                ?>

                                            </tr>
                                        <?php } ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else {
            echo "Select Country";
        } ?>

    </div>
</div>

<div class="modal fade" id="updateStatusModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Update Setup Status</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="training_setup" action="controller/attendanceStatusController.php" method="post">
                    <?php echo $filterHiddenInputs; ?>

                    <div>
                        <label for="society_name" class="col-form-label">Company Name :<span id="company_name_span"
                                class="px-2"></span></label>
                        <div class="mb-4">
                            <input type="hidden" id="society_name" name="society_name" class="form-control" value="">
                            <input type="hidden" id="society_id" name="society_id" class="form-control" value="">
                            <input type="hidden" id="updateTrainingStatus" name="updateTrainingStatus"
                                class="form-control" value="updateTrainingStatus">
                        </div>
                    </div>

                    <div>
                        <label for="executive_name" class="col-form-label">Executive Name <span
                                class="required">*</span></label>
                        <select name="executive_name" id="executive_name" class="form-control single-select" required>
                            <option value="">--Select Executive Name--</option>

                            <?php
                            $executiveQuery = $d->selectRow("admin_name, admin_id", "bms_admin_master", "role_id!=1");
                            while ($admin_data = mysqli_fetch_array($executiveQuery)) {
                                $selected = ($admin_data['admin_name'] == $executive_name) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($admin_data['admin_id']) . "' $selected>" . htmlspecialchars($admin_data['admin_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center align-middle">#</th>
                                <th colspan="3" class="text-center">Data Receive Status</th>
                                <th colspan="3" class="text-center">Data Upload Status</th>
                            </tr>
                            <tr>
                                <th>Received</th>
                                <th>Pending</th>
                                <th>N/A</th>
                                <th>Completed</th>
                                <th>In Progress</th>
                                <th>N/A</th>
                            </tr>
                        </thead>
                        <tbody id="statusTableContent">

                        </tbody>
                    </table>

                    <div class="text-center mb-3">
                        <button type="submit" class="btn btn-success mt-3">
                            <?php echo $isEditMode ? 'Update' : 'Submit'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Step Delay Alerts Modal -->
<div class="modal fade" id="stepDelayAlertModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">Delay Alert</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <div class="row mb-2 px-5">
                        <div class="col-md-4 mb-2">
                            <input type="text" id="stepDelaySearch" class="form-control" placeholder="Search by company name or ID">
                        </div>
                        <div class="col-md-4 mb-2">
                            <select id="stepDelayIssueFilter" class="form-control">
                                <option value="">All Issues</option>
                                <option value="welcome">Welcome Email late</option>
                                <option value="whatsapp">WhatsApp Group late</option>
                                <option value="setup_not_started">Setup not started</option>
                                <option value="setup_incomplete">Setup incomplete</option>
                                <option value="topic">Product Training late</option>
                                <option value="handover">Handover overdue</option>
                            </select>
                        </div>
                        <!-- <div class="col-md-4 mb-2 d-flex align-items-center">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="stepDelayOnlyOverdue">
                                <label class="custom-control-label" for="stepDelayOnlyOverdue">Only overdue setup</label>
                            </div>
                        </div> -->
                    </div>
                    <table class="table table-bordered table-sm mb-0" id="stepDelayTable">
                        <thead>
                            <tr>
                                <th style="width:10%">Company ID</th>
                                <th style="width:35%">Company</th>
                                <th>City</th>
                                <th>Issues</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="settingModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Company Settings</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="settingsForm" action="controller/buildingController.php" method="post">
                    <?php echo $filterHiddenInputs; ?>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- // Mukesh end  5-6-24 -->

<!-- added by jainit 03-02-25 -->
<div class="modal fade" id="isOnboardModel">
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Onboarding Process</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form id="isOnboardingForm" method="POST"
                                action="controller/createSocietyAutoController.php">
                                <?php echo $filterHiddenInputs; ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="onBoardedDate" class="col-form-label">Onboarding Date <span
                                                class="text-danger">*</span></label>
                                        <div class="col-12">
                                            <input type="text" class="form-control" id="autoclose-datepickerFrom"
                                                name="data_onboarding_date" placeholder="Onboarding Date" readonly
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="status" class="col-form-label">Status</label>
                                        <select name="is_data_onboarding" id="is_data_onboarding" class="form-control"
                                            required>
                                            <option value="">---Select----</option>
                                            <option value="1">Pending</option>
                                            <option value="2">Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-footer text-center mt-3">
                                    <input type="hidden" name="society_id_onboarding" id="society_id_onboarding">
                                    <input type="hidden" name="is_data_onboarding_completed"
                                        value="is_data_onboarding_completed">
                                    <button type="submit" class="btn btn-sm btn-success"><i
                                            class="fa fa-check-square-o"></i> Approve </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<style>
    /* CRM Process Timeline Styling - Similar to companyOnboardingTimeline */
    .crm-process-list {
        position: relative;
        padding: 10px 0;
    }

    .crm-day-card {
        position: relative;
        margin-bottom: 30px;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .crm-day-card .card-body {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        position: relative;
        transition: all 0.3s ease;
    }

    .crm-day-card.day-completed .card-body {
        background: #f8fff9;
        border-color: #d4edda;
    }

    .crm-day-card.day-overdue {
        border-color: #dc3545;
    }

    .crm-day-card.day-overdue .card-body {
        background-color: #fff5f5;
        border-color: #dc3545;
    }

    .crm-day-complete-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #28a745;
        z-index: 3;
    }

    .crm-day-complete-icon .fa-check-circle {
        font-size: 28px;
    }

    .day-step {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        padding-right: 56px;
        margin-bottom: 15px;
        position: relative;
        transition: all 0.3s ease;
    }

    .day-step.step-completed {
        background: #f8fff9;
        border-color: #d4edda;
    }

    .crm-step-complete-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #28a745;
        z-index: 3;
    }

    .crm-step-complete-icon .fa-check-circle {
        font-size: 26px;
    }

    .day-step::before {
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

    .day-step::after {
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

    .day-step.step-completed::after {
        border-right-color: #f8fff9;
    }

    .crm-day-card .card-body {
        padding-right: 60px;
    }

    .tagify {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.4rem;
        min-height: 45px;
        overflow: visible !important;
    }

    .tagify__input {
        min-width: 120px !important;
    }

    .form-check-inline {
        margin-top: 10px;
    }

    /* Step Delay Alert Button Styling */
    .pulse-animation {
        animation: pulse 2s infinite;
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        border: 2px solid #dc3545;
        font-weight: bold;
        position: relative;
        overflow: hidden;
    }

    .pulse-animation::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        animation: shimmer 3s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }

        70% {
            transform: scale(1.05);
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }

        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }

    @keyframes shimmer {
        0% {
            left: -100%;
        }

        100% {
            left: 100%;
        }
    }

    /* Enhanced styling when button is visible */
    #step-delay-alert-btn:not(.d-none) {
        background: linear-gradient(45deg, #dc3545, #c82333);
        border: 2px solid #fff;
        color: white;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    #step-delay-alert-btn:not(.d-none):hover {
        background: linear-gradient(45deg, #c82333, #bd2130);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    #step-delay-alert-btn .fa-bell {
        animation: bell-shake 1s infinite;
    }

    @keyframes bell-shake {

        0%,
        100% {
            transform: rotate(0deg);
        }

        25% {
            transform: rotate(-10deg);
        }

        75% {
            transform: rotate(10deg);
        }
    }

    /* Toast Notification Styles */
    .copy-url-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        font-size: 14px;
        font-weight: 500;
        min-width: 300px;
        max-width: 400px;
        opacity: 0;
        transform: translateX(400px);
        transition: all 0.3s ease-in-out;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .copy-url-toast.show {
        opacity: 1;
        transform: translateX(0);
    }

    .copy-url-toast-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .copy-url-toast-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .copy-url-toast i {
        font-size: 18px;
    }

    .copy-url-toast-success i {
        color: #28a745;
    }

    .copy-url-toast-error i {
        color: #dc3545;
    }

    @media (max-width: 768px) {
        .copy-url-toast {
            right: 10px;
            left: 10px;
            min-width: auto;
            max-width: none;
            transform: translateY(-100px);
        }

        .copy-url-toast.show {
            transform: translateY(0);
        }
    }
</style>



<!-- email type model -->
<div class="modal fade" id="emailTypeModel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Email Type</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form id="emailTypeForm" method="POST" action="controller/createSocietyAutoController.php"
                                enctype="multipart/form-data">
                                <?php echo $filterHiddenInputs; ?>
                                <div class="row">
                                    <p class="col-md-12">
                                        <strong>Receiver Email:</strong>
                                        <span id="secretary_email"
                                            style="text-transform: lowercase; font-weight: bold;"></span>
                                    </p>

                                    <div class="col-md-12 mb-3">
                                        <label class="col-form-label">CC Email</label>
                                        <input name="cc_emails" id="cc_emails" class="form-control"
                                            placeholder="Type email and press Enter or click out" />
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="emailType" value="1"
                                                id="emailTypeWithInvoice" checked required>
                                            <label class="form-check-label" for="emailTypeWithInvoice">With
                                                Invoice</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="emailType" value="2"
                                                id="emailTypeWithoutInvoice">
                                            <label class="form-check-label" for="emailTypeWithoutInvoice">Without
                                                Invoice</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3 pay_div">
                                        <label class="col-form-label">Received Amount</label>
                                        <input type="text" name="receivedamount" id="receivedamount"
                                            class="form-control onlyNumber">
                                    </div>

                                    <div class="col-md-12 mb-3 pay_div">
                                        <label class="col-form-label">Payment method</label>
                                        <select name="pay_mode" id="pay_mode" class="form-control">
                                            <option value="1">Online Bank Transfer</option>
                                            <option value="2">Cheque</option>
                                            <option value="3">UPI</option>
                                            <option value="4">Cash</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="col-form-label">Payment Attachment</label>
                                        <input type="file" name="payment_attachment[]" id="payment_attachment"
                                            class="form-control" multiple>
                                    </div>
                                </div>

                                <div class="form-footer text-center mt-3">
                                    <input type="hidden" name="society_id_sendemail" id="society_id_sendemail">
                                    <input type="hidden" name="welcome_template_id" id="welcome_template_id" value="">
                                    <input type="hidden" name="welcome_product_type" id="welcome_product_type" value="">
                                    <input type="hidden" name="is_welcome_email" value="is_welcome_email">
                                    <button type="submit" class="btn btn-sm btn-success"><i
                                            class="fa fa-check-square-o"></i> Send </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- training Detail model -->
<div class="modal fade" id="trainingModel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Training Process</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="trainingVisitData">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="SetupTrainingStatusModal">
    <div class="modal-dialog modal-lg" style="max-width: 90%; width: auto;">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Setup Status</h5>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-success mr-2" onclick="exportSetupStatus()" title="Export to Excel">
                        <i class="fa fa-file-excel-o"></i> Excel
                    </button>
                    <button type="button" class="btn btn-sm btn-danger mr-2" onclick="exportSetupStatusPDF()" title="Export to PDF">
                        <i class="fa fa-file-pdf-o"></i> PDF
                    </button>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <form id="setup_training_status" action="controller/attendanceStatusController.php" method="post">
                    <?php echo $filterHiddenInputs; ?>
                    <table class="table table-bordered mt-3">
                        <thead>
                        </thead>
                        <tbody id="setupStatus">
                            <!-- Table rows will be inserted here by AJAX -->
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="scheduleMeeting">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-white">
                    <span id="modalTitle">Schedule Meeting</span>
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="scheduleMeetingValidation" action="controller/trainingController.php" method="post">
                    <?php echo $filterHiddenInputs; ?>

                    <input type="hidden" id="society_id" name="society_id">
                    <input type="hidden" id="bms_admin_id" name="bms_admin_id">

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="training_date" class="col-form-label">Training Date <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control autoclose-datepicker" name="training_date"
                                id="training_date" placeholder="Training Date" readonly required value=""
                                onchange="getMeetingName()">
                        </div>

                        <div class="col-md-6">
                            <label for="session_id" class="col-form-label">Session <span
                                    class="required">*</span></label>
                            <select class="form-control single-select" name="session_id" id="session_id" required
                                onchange="setSessionTimes();getMeetingName();">
                                <option value="">-- Select --</option>
                                <?php
                                $qt = $d->select("session_master", "session_status='0'");
                                while ($Data = mysqli_fetch_array($qt)) {
                                    $session_day_id = isset($Data['session_day_id']) ? $Data['session_day_id'] : 'N/A';
                                ?>
                                    <option value="<?php echo $Data['session_id']; ?>"
                                        data-session-day-id="<?php echo $session_day_id; ?>"
                                        data-start-time="<?php echo $Data['start_time']; ?>"
                                        data-end-time="<?php echo $Data['end_time']; ?>"
                                        data-session-name="<?php echo $Data['session_name']; ?>">
                                        <?php echo $Data['session_name'] . '(' . $Data['session_days'] . ' - ' . date("h:i A", strtotime($Data['start_time'])) . ' - ' . date("h:i A", strtotime($Data['end_time'])) . ')'; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <input type="hidden" name="session_day_id" id="session_day_id">
                        <input type="hidden" name="start_time" id="start_time">
                        <input type="hidden" name="end_time" id="end_time">
                    </div>
                    <input type="hidden" id="training_schedule_master_id" name="training_schedule_master_id">

                    <div class="form-group">
                        <div class="col-md-12">
                            <label class="font-weight-bold">Meeting Names:</label>
                            <input type="hidden" id="meeting_name" name="meeting_name">
                            <div class="row justify-content-center mx-2">
                                <div class="col-12 row justify-content-center" id="meeting_list"></div>
                                <div class="col-md-6 mb-3">
                                    <label class="btn btn-outline-primary w-100 text-center p-3">
                                        <input type="radio" name="selected_meeting" value="new_meeting" class="mr-2">
                                        <span>Add New Meeting</span>
                                    </label>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <button type="submit" id="submitMeeting" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="scheduleBatch">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-white">
                    <span id="modalTitle">Schedule Batch Meeting</span>
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="scheduleBatchValidation" action="controller/trainingController.php" method="post">
                    <?php echo $filterHiddenInputs; ?>
                    <input type="hidden" id="batch_society_id" name="society_id">
                    <input type="hidden" id="assignBatchMeeting" name="action" value="assignBatchMeeting">
                    <input type="hidden" id="batch_bms_admin_id" name="bms_admin_id">

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="batch_training_date" class="col-form-label">Training Date <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control autoclose-datepicker" name="batch_training_date"
                                id="batch_training_date" placeholder="Training Date" readonly required value=""
                                onchange="getBatchMeetings()">
                        </div>
                        <div class="col-md-6">
                            <label for="company_type" class="col-form-label">Company Type<span
                                    class="text-danger">*</span></label>
                            <select class="form-control single-select" name="company_type" id="company_type" required>
                                <option value="">-- Select --</option>
                                <option value="0" selected>Product Training Team
                                </option>
                                <option value="1">Engagement Team
                                </option>
                            </select>
                        </div>
                    </div>

                    <?php
                    $batches = $d->selectRow(
                        "batch_slot_master.batch_id, batch_slot_master.slot_id,batch_slot_master.start_date, training_batch_master.batch_name",
                        "batch_slot_master 
                        LEFT JOIN training_batch_master ON batch_slot_master.batch_id = training_batch_master.batch_id",
                        ""
                    );

                    $seen = [];

                    $uniqueBatches = [];
                    while ($row = mysqli_fetch_assoc($batches)) {
                        $new = $row['batch_name'] . '|' . $row['start_date'];
                        if (!in_array($new, $seen)) {
                            $seen[] = $new;
                            $uniqueBatches[] = $row;
                        }
                    }
                    ?>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="meeting_type" class="col-form-label">Meeting Type<span
                                    class="text-danger">*</span></label>
                            <select class="form-control single-select" name="meeting_type" id="meeting_type" required
                                onchange="getBatchMeetings()">
                                <option value="">-- Select --</option>
                                <option value="0">Batch Wise
                                </option>
                                <option value="1">Slot Wise
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 d-none" id="fetchBatchNames">
                            <label>Batch Name</label>
                            <select name="slot_id[]" id="slot_id" class="form-control multiple-select"
                                multiple="multiple">
                                <option value="">-- Select Batch --</option>
                                <?php foreach ($uniqueBatches as $row) { ?>
                                    <option value="<?php echo htmlspecialchars($row['slot_id']); ?>"
                                        data-start-date="<?php echo htmlspecialchars($row['start_date']); ?>">
                                        <?php echo htmlspecialchars($row['batch_name'] . ' (' . $row['start_date'] . ')'); ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <div class="text-left mt-3">
                                <button type="submit" id="submitBatchMeeting" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group d-none " id="meetingAvailableDiv">
                        <div class="col-md-12">
                            <label class="font-weight-bold">Available batch Meetings:</label>
                            <div class="col-12 row justify-content-center" id="batch_meeting_list"></div>
                            <div class="text-center mt-3">
                                <button type="submit" id="submitBatchMeeting" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ProductTrainingStatusModal">
    <div class="modal-dialog modal-lg" style="max-width: 90%; width: auto;">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Product Training Status - <span id="productCompanyName"></span></h5>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-success mr-2" onclick="exportProductTrainingStatus('excel')" title="Export to Excel">
                        <i class="fa fa-file-excel-o"></i> Excel
                    </button>
                    <button type="button" class="btn btn-sm btn-danger mr-2" onclick="exportProductTrainingStatus('pdf')" title="Export to PDF">
                        <i class="fa fa-file-pdf-o"></i> PDF
                    </button>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <div class="modal-body">
                <div class="row px-5">
                    <div class="table-responsive">
                        <div id="productStatus"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Modal for Product Closure Date -->
<div class="modal fade" id="closure-date-modal" tabindex="-1" role="dialog" aria-labelledby="closureDateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="closureDateModalLabel">Closure Dates</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="closure_date" action="controller/trainingController.php" method="post">
                    <?php echo $filterHiddenInputs; ?>

                    <!-- Product Closure Dates Card -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Product Training Timeline</h5>
                        </div>
                        <div class="card-body">

                            <div class="form-group row">
                                <label for="hr_training_days_from_closure" class="col-sm-8 col-form-label">Closure to
                                    Implementation Done (HR Only):<span class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" name="hr_training_days_from_closure"
                                        id="hr_training_days_from_closure" class="form-control onlyNumber"
                                        value="<?php echo isset($closureDateData['hr_training_days_from_closure']) ? $closureDateData['hr_training_days_from_closure'] : ''; ?>"
                                        minlength="1" maxlength="3">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="hr_training_days_from_first_meeting" class="col-sm-8 col-form-label">Session
                                    1 to All Sessions Done (HR Only):<span class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" name="hr_training_days_from_first_meeting"
                                        id="hr_training_days_from_first_meeting" class="form-control onlyNumber"
                                        value="<?php echo isset($closureDateData['hr_training_days_from_first_meeting']) ? $closureDateData['hr_training_days_from_first_meeting'] : ''; ?>"
                                        minlength="1" maxlength="3">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="first_hr_training_days_from_closure" class="col-sm-8 col-form-label">Closure
                                    to First Product Training (HR Only):<span class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" name="first_hr_training_days_from_closure"
                                        id="first_hr_training_days_from_closure" class="form-control onlyNumber"
                                        value="<?php echo isset($closureDateData['first_hr_training_days_from_closure']) ? $closureDateData['first_hr_training_days_from_closure'] : ''; ?>"
                                        minlength="1" maxlength="3">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="owner_team_leader_training_days_from_closure"
                                    class="col-sm-8 col-form-label">Closure to Owner/Team/Leader Training:<span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off"
                                        name="owner_team_leader_training_days_from_closure"
                                        id="owner_team_leader_training_days_from_closure"
                                        class="form-control onlyNumber"
                                        value="<?php echo isset($closureDateData['owner_team_leader_training_days_from_closure']) ? $closureDateData['owner_team_leader_training_days_from_closure'] : ''; ?>"
                                        minlength="1" maxlength="3">
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Setup Closure Dates Card -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Setup Training Timeline</h5>
                        </div>
                        <div class="card-body">

                            <div class="form-group row">
                                <label for="session1_training_days_from_closure" class="col-sm-8 col-form-label">Closure
                                    to Setup Session 1 Training Done:<span class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" name="session1_training_days_from_closure"
                                        id="session1_training_days_from_closure" class="form-control onlyNumber"
                                        value="<?php echo isset($closureDateData['session1_training_days_from_closure']) ? $closureDateData['session1_training_days_from_closure'] : ''; ?>"
                                        minlength="1" maxlength="3">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="session1_data_receive_days_from_training"
                                    class="col-sm-8 col-form-label">Setup Session 1 Training Completion to Data
                                    Receive:<span class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off"
                                        name="session1_data_receive_days_from_training"
                                        id="session1_data_receive_days_from_training" class="form-control onlyNumber"
                                        value="<?php echo isset($closureDateData['session1_data_receive_days_from_training']) ? $closureDateData['session1_data_receive_days_from_training'] : ''; ?>"
                                        minlength="1" maxlength="3">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="session1_data_upload_days_from_receive"
                                    class="col-sm-8 col-form-label">Setup Session 1 Data Receive to Data Upload:<span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" name="session1_data_upload_days_from_receive"
                                        id="session1_data_upload_days_from_receive" class="form-control onlyNumber"
                                        value="<?php echo isset($closureDateData['session1_data_upload_days_from_receive']) ? $closureDateData['session1_data_upload_days_from_receive'] : ''; ?>"
                                        minlength="1" maxlength="3">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="session1_completion_days_from_closure"
                                    class="col-sm-8 col-form-label">Closure to Setup Session 1 Completion:<span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" name="session1_completion_days_from_closure"
                                        id="session1_completion_days_from_closure" class="form-control onlyNumber"
                                        value="<?php echo isset($closureDateData['session1_completion_days_from_closure']) ? $closureDateData['session1_completion_days_from_closure'] : ''; ?>"
                                        minlength="1" maxlength="3">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="session2_data_receive_days_from_training"
                                    class="col-sm-8 col-form-label">Setup Session 2 Training to Data Receive:<span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off"
                                        name="session2_data_receive_days_from_training"
                                        id="session2_data_receive_days_from_training" class="form-control onlyNumber"
                                        value="<?php echo isset($closureDateData['session2_data_receive_days_from_training']) ? $closureDateData['session2_data_receive_days_from_training'] : ''; ?>"
                                        minlength="1" maxlength="3">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="session2_data_upload_days_from_receive"
                                    class="col-sm-8 col-form-label">Setup Session 2 Data Receive to Data Upload:<span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" name="session2_data_upload_days_from_receive"
                                        id="session2_data_upload_days_from_receive" class="form-control onlyNumber"
                                        value="<?php echo isset($closureDateData['session2_data_upload_days_from_receive']) ? $closureDateData['session2_data_upload_days_from_receive'] : ''; ?>"
                                        minlength="1" maxlength="3">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="session2_data_upload_days_from_closure"
                                    class="col-sm-8 col-form-label">Closure to Setup Session 2 Data Upload:<span
                                        class="required">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" autocomplete="off" name="session2_data_upload_days_from_closure"
                                        id="session2_data_upload_days_from_closure" class="form-control onlyNumber"
                                        value="<?php echo isset($closureDateData['session2_data_upload_days_from_closure']) ? $closureDateData['session2_data_upload_days_from_closure'] : ''; ?>"
                                        minlength="1" maxlength="3">
                                </div>
                            </div>

                        </div>
                    </div>

                    <input type="hidden" name="closureDate" value="closureDate">
                    <div class="text-center mb-3">
                        <button type="submit" class="btn btn-success mt-3" id="closureDate">Update Date</button>
                    </div>


                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="whatsappGroupLinkModal" tabindex="-1" role="dialog" aria-labelledby="whatsappGroupLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="whatsappGroupLinkModalLabel">WhatsApp Group Join Link</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-2 text-muted" id="whatsappGroupLinkCompanyName"></p>
                <div class="form-group mb-0">
                    <label for="whatsapp_group_link_input">Joining Link</label>
                    <div class="input-group">
                        <input type="url" class="form-control" id="whatsapp_group_link_input" placeholder="https://chat.whatsapp.com/..." maxlength="255" autocomplete="off">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-info" id="whatsappGroupLinkCopyBtn" title="Copy Link" onclick="copyWhatsappGroupLinkFromModal()">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">Paste the WhatsApp group invite/join link. Leave blank to clear.</small>
                </div>
                <input type="hidden" id="whatsapp_group_link_society_id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveWhatsappGroupLink()">
                    <i class="fa fa-check"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="implementationEditModal">
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="implementationEditModalLabel">Edit Implementation Person</h5>
                <button type="button" class="close text-white" data-dismiss="modal"
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="implementationEditForm">
                    <div class="form-group">
                        <label for="implementation_person_select" class="col-form-label">Implementation Person <span class="text-danger">*</span></label>
                        <select id="implementation_person_select" class="form-control single-select" required>
                            <option value="">-- Select Implementation Person --</option>
                            <?php
                            $result = $d->select("bms_admin_master", "bms_admin_master.role_id!=1");
                            while ($row = mysqli_fetch_array($result)) {
                                $adminNameEsc = htmlspecialchars($row['admin_name']);
                                echo "<option value='{$adminNameEsc}'>$adminNameEsc</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <input type="hidden" id="edit_society_id" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateImplementationPerson()">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- CRM Process Modal -->
<div class="modal fade" id="crmProcessModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="crmProcessLabel">CRM Process - <span id="crmProcessCompanyName"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="crmProcessAlert" class="alert d-none" role="alert"></div>
                <div id="crmProcessLoader" class="text-center py-3 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="crmProcessList" class="crm-process-list"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveAllCrmTrainingProgress()">
                    <i class="fa fa-save mr-1"></i>Save All
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CRM Process Details Modal -->
<div class="modal fade" id="crmProcessDetailsModal">
    <div class="modal-dialog modal-xl" style="max-width: 95%; width: auto;">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">CRM Process Details - <span id="crmProcessDetailsCompanyName"></span></h5>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-success mr-2" onclick="exportCrmProcessExcel()" title="Export to Excel">
                        <i class="fa fa-file-excel-o"></i> Excel
                    </button>
                    <button type="button" class="btn btn-sm btn-danger mr-2" onclick="exportCrmProcessPDF()" title="Export to PDF">
                        <i class="fa fa-file-pdf-o"></i> PDF
                    </button>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div id="crmProcessDetailsLoader" class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="crmProcessDetailsContent" class="table-responsive" style="display: none;">
                    <!-- Details will be rendered here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script>
    const input = document.querySelector('#cc_emails');
    const tagify = new Tagify(input, {
        enforceWhitelist: false,
        pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        dropdown: {
            enabled: 0
        },
        delimiters: ",",
        keepInvalidTags: true
    });
    tagify.on('add', resizeTagBox);
    tagify.on('remove', resizeTagBox);

    function resizeTagBox() {
        const wrapper = tagify.DOM.scope;
        wrapper.style.height = 'auto';
        wrapper.style.height = wrapper.scrollHeight + 'px';
    }

    resizeTagBox();

    // ---------------- CRM Process ----------------
    let crmProcessCurrentSociety = null;
    let crmProcessSteps = [];
    let crmProcessDayDescriptions = {}; // Store day descriptions
    let crmProcessDayInfo = {}; // Store day info (due dates, completed dates, delays)
    let crmProcessSecretaryEmail = '';
    let crmProcessPayMode = '';
    let crmProcessAmount = '';
    let crmProcessCompanyNameText = '';
    const CRM_MULTI_DELIM = '||';

    function parseIntegrationValues(val) {
        if (!val) return [];
        if (val.includes(CRM_MULTI_DELIM)) {
            return val.split(CRM_MULTI_DELIM).map(v => v.trim()).filter(Boolean);
        }
        return val.split(',').map(v => v.trim()).filter(Boolean);
    }


    function setCrmProcessAlert(type, msg) {
        const box = $('#crmProcessAlert');
        if (!msg) {
            box.addClass('d-none').removeClass('alert-success alert-danger').text('');
            return;
        }
        box.removeClass('d-none').removeClass('alert-success alert-danger').addClass('alert-' + type).text(msg);
    }

    function openCrmProcessModal(societyId, companyName, secretaryEmail = '', payMode = '', amount = '') {
        crmProcessCurrentSociety = societyId;
        crmProcessCompanyNameText = companyName || '';
        crmProcessSecretaryEmail = secretaryEmail || '';
        crmProcessPayMode = payMode || '';
        crmProcessAmount = amount || '';
        $('#crmProcessCompanyName').text(companyName || '');
        setCrmProcessAlert(null, '');
        $('#crmProcessLoader').removeClass('d-none');
        $('#crmProcessList').html('<div class="text-center text-muted">Loading...</div>');
        $('#crmProcessModal').modal('show');

        $.post('controller/crmProcessController.php', {
            action: 'get_crm_training_modules',
            society_id: societyId
        }, function(resp) {
            $('#crmProcessLoader').addClass('d-none');
            if (resp && resp.success) {
                // Ensure progress map is properly formatted
                const progressMap = resp.progress || {};
                // Convert progress map keys to ensure proper matching
                const normalizedProgress = {};
                Object.keys(progressMap).forEach(key => {
                    const item = progressMap[key];
                    normalizedProgress[key] = {
                        status: parseInt(item.status) || 0,
                        remark: (item.remark !== undefined && item.remark !== null) ? String(item.remark) : ''
                    };
                });
                // Debug: log progress map to console
                console.log('Raw Progress Map from server:', progressMap);
                console.log('Normalized Progress Map:', normalizedProgress);
                console.log('Topics:', resp.topics);
                renderCrmTrainingModules(resp.topics || [], normalizedProgress);
            } else {
                const errorMsg = resp && resp.message ? resp.message : 'Failed to load CRM training modules';
                $('#crmProcessList').html('<div class="alert alert-danger"><strong>Error:</strong> ' + escapeHtml(errorMsg) + '<br><small>Please ensure the crm_training_progress table exists in the database.</small></div>');
            }
        }, 'json').fail(function(xhr, status, error) {
            $('#crmProcessLoader').addClass('d-none');
            let errorMsg = 'Error loading CRM training modules';
            if (xhr.responseText) {
                try {
                    const resp = JSON.parse(xhr.responseText);
                    if (resp.message) errorMsg = resp.message;
                } catch (e) {
                    errorMsg = xhr.responseText.substring(0, 200);
                }
            }
            $('#crmProcessList').html('<div class="alert alert-danger"><strong>Error:</strong> ' + escapeHtml(errorMsg) + '</div>');
        });
    }

    function renderCrmTrainingModules(topics, progressMap) {
        const container = $('#crmProcessList');

        if (!topics || topics.length === 0) {
            container.html('<div class="text-center text-muted">No CRM training modules found</div>');
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-bordered">';
        html += '<thead>';
        html += '<tr>';
        html += '<th rowspan="2" class="text-center align-middle" style="width: 5%;">#</th>';
        html += '<th rowspan="2" class="text-center align-middle" style="width: 25%;">Module / Sub-topic Name</th>';
        html += '<th colspan="4" class="text-center">Status</th>';
        html += '<th rowspan="2" class="text-center align-middle" style="width: 30%;">Remark</th>';
        html += '</tr>';
        html += '<tr>';
        html += '<th class="text-center" style="width: 10%;">Pending</th>';
        html += '<th class="text-center" style="width: 10%;">Completed</th>';
        html += '<th class="text-center" style="width: 10%;">NA</th>';
        html += '<th class="text-center" style="width: 10%;">Later On</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';

        let rowNumber = 1;

        topics.forEach(topic => {
            const topicName = topic.topic_name || 'Ungrouped';
            const topicCompletionDays = topic.topic_completion_days !== null && topic.topic_completion_days !== undefined ? topic.topic_completion_days : null;
            const topicCompletionDaysText = topicCompletionDays !== null ? ` <span class="badge badge-secondary ml-2">${topicCompletionDays} day${topicCompletionDays !== 1 ? 's' : ''}</span>` : '';

            // Topic header row
            html += `<tr class="table-info"><td colspan="7" class="font-weight-bold"><i class="fa fa-folder-open mr-2"></i>${escapeHtml(topicName)}${topicCompletionDaysText}</td></tr>`;

            topic.modules.forEach(module => {
                const moduleId = module.module_id;
                const moduleKey = moduleId + '_module';
                const moduleProgress = progressMap[moduleKey] || {
                    status: 0,
                    remark: ''
                };
                const currentStatus = deriveModuleStatus(module, progressMap);
                const currentRemark = (moduleProgress.remark !== undefined && moduleProgress.remark !== null) ? moduleProgress.remark : '';

                // Debug log for this module
                console.log(`Module ${moduleId}: key=${moduleKey}, progress=`, moduleProgress, 'status=', currentStatus, 'remark=', currentRemark);

                // Module row
                const moduleCompletionDays = module.module_completion_days !== null && module.module_completion_days !== undefined ? module.module_completion_days : null;
                const moduleCompletionDaysText = moduleCompletionDays !== null ? ` <span class="badge badge-info ml-2">${moduleCompletionDays} day${moduleCompletionDays !== 1 ? 's' : ''}</span>` : '';
                const modulePriority = module.module_priority !== null && module.module_priority !== undefined ? module.module_priority : null;
                const modulePriorityText = modulePriority !== null ? ` <span class="badge badge-warning ml-2">${escapeHtml(modulePriority)}</span>` : '';

                const hasSubtopics = module.subtopics && module.subtopics.length > 0;
                html += `<tr data-module-id="${moduleId}" data-subtopic-id="">`;
                html += `<td class="text-center">${rowNumber++}</td>`;
                html += `<td class="font-weight-bold" style="color: black;">${escapeHtml(module.module_name)}${moduleCompletionDaysText}${modulePriorityText}</td>`;
                if (hasSubtopics) {
                    html += `<td class="text-center"><label class="mr-2 mb-0"><input type="checkbox" class="crm-bulk-status" data-module-id="${moduleId}" value="0" onclick="setModuleSubtopicsStatusFromCheckbox(${moduleId}, 0, this)"> All Pending</label></td>`;
                    html += `<td class="text-center"><label class="mb-0"><input type="checkbox" class="crm-bulk-status" data-module-id="${moduleId}" value="1" onclick="setModuleSubtopicsStatusFromCheckbox(${moduleId}, 1, this)"> All Completed</label></td>`;
                    html += `<td class="text-center"><label class="mr-2 mb-0"><input type="checkbox" class="crm-bulk-status" data-module-id="${moduleId}" value="2" onclick="setModuleSubtopicsStatusFromCheckbox(${moduleId}, 2, this)"> All NA</label></td>`;
                    html += `<td class="text-center"><label class="mb-0"><input type="checkbox" class="crm-bulk-status" data-module-id="${moduleId}" value="3" onclick="setModuleSubtopicsStatusFromCheckbox(${moduleId}, 3, this)"> All Later On</label></td>`;
                } else {
                    html += `<td class="text-center"><input type="radio" name="crm_status_${moduleId}_module" value="0" class="crm-module-status" data-module-id="${moduleId}" data-subtopic-id="" ${currentStatus === 0 ? 'checked' : ''}></td>`;
                    html += `<td class="text-center"><input type="radio" name="crm_status_${moduleId}_module" value="1" class="crm-module-status" data-module-id="${moduleId}" data-subtopic-id="" ${currentStatus === 1 ? 'checked' : ''}></td>`;
                    html += `<td class="text-center"><input type="radio" name="crm_status_${moduleId}_module" value="2" class="crm-module-status" data-module-id="${moduleId}" data-subtopic-id="" ${currentStatus === 2 ? 'checked' : ''}></td>`;
                    html += `<td class="text-center"><input type="radio" name="crm_status_${moduleId}_module" value="3" class="crm-module-status" data-module-id="${moduleId}" data-subtopic-id="" ${currentStatus === 3 ? 'checked' : ''}></td>`;
                }
                html += `<td><textarea class="form-control form-control-sm crm-module-remark" data-module-id="${moduleId}" data-subtopic-id="" rows="1" placeholder="Enter remark...">${escapeHtml(currentRemark)}</textarea></td>`;
                html += `</tr>`;

                // Add subtopics if any
                if (module.subtopics && module.subtopics.length > 0) {
                    module.subtopics.forEach(subtopic => {
                        const subtopicId = subtopic.subtopic_id;
                        const subtopicKey = moduleId + '_' + subtopicId;
                        const subtopicProgress = progressMap[subtopicKey] || {
                            status: 0,
                            remark: ''
                        };
                        const currentSubtopicStatus = parseInt(subtopicProgress.status) || 0;
                        const currentSubtopicRemark = (subtopicProgress.remark !== undefined && subtopicProgress.remark !== null) ? subtopicProgress.remark : '';

                        // Debug log for this subtopic
                        console.log(`Subtopic ${subtopicId} of Module ${moduleId}: key=${subtopicKey}, progress=`, subtopicProgress, 'status=', currentSubtopicStatus, 'remark=', currentSubtopicRemark);
                        const estimatedMinutes = subtopic.estimated_minutes !== null && subtopic.estimated_minutes !== undefined ? subtopic.estimated_minutes : null;
                        const estimatedTimeBadge = estimatedMinutes !== null ? ` <span class="badge badge-success ml-2">${estimatedMinutes} min</span>` : '';
                        const subtopicDisplayOrder = subtopic.display_order !== null && subtopic.display_order !== undefined ? subtopic.display_order : null;
                        const subtopicPriorityText = subtopicDisplayOrder !== null ? ` <span class="badge badge-warning ml-2">#${subtopicDisplayOrder}</span>` : '';
                        const description = subtopic.subtopic_description ? escapeHtml(subtopic.subtopic_description) : '';
                        const infoIconHtml = description ? `<i class="fa fa-info-circle ml-2 text-info" data-toggle="tooltip" data-placement="top" title="${description}" style="cursor: pointer;"></i>` : '';

                        html += `<tr data-module-id="${moduleId}" data-subtopic-id="${subtopicId}" class="table-light">`;
                        html += `<td class="text-center">${rowNumber++}</td>`;
                        html += `<td style="color: black; padding-left: 30px;"><i class="fa fa-arrow-right mr-2 text-muted"></i>${escapeHtml(subtopic.subtopic_name)}${estimatedTimeBadge}${infoIconHtml}</td>`;
                        html += `<td class="text-center"><input type="radio" name="crm_status_${moduleId}_${subtopicId}" value="0" class="crm-subtopic-status" data-module-id="${moduleId}" data-subtopic-id="${subtopicId}" ${currentSubtopicStatus === 0 ? 'checked' : ''}></td>`;
                        html += `<td class="text-center"><input type="radio" name="crm_status_${moduleId}_${subtopicId}" value="1" class="crm-subtopic-status" data-module-id="${moduleId}" data-subtopic-id="${subtopicId}" ${currentSubtopicStatus === 1 ? 'checked' : ''}></td>`;
                        html += `<td class="text-center"><input type="radio" name="crm_status_${moduleId}_${subtopicId}" value="2" class="crm-subtopic-status" data-module-id="${moduleId}" data-subtopic-id="${subtopicId}" ${currentSubtopicStatus === 2 ? 'checked' : ''}></td>`;
                        html += `<td class="text-center"><input type="radio" name="crm_status_${moduleId}_${subtopicId}" value="3" class="crm-subtopic-status" data-module-id="${moduleId}" data-subtopic-id="${subtopicId}" ${currentSubtopicStatus === 3 ? 'checked' : ''}></td>`;
                        html += `<td><textarea class="form-control form-control-sm crm-subtopic-remark" data-module-id="${moduleId}" data-subtopic-id="${subtopicId}" rows="1" placeholder="Enter remark...">${escapeHtml(currentSubtopicRemark)}</textarea></td>`;
                        html += `</tr>`;
                    });
                }
            });
        });

        html += '</tbody></table></div>';
        container.html(html);

        // Initialize tooltips for info icons
        container.find('[data-toggle="tooltip"]').tooltip();
    }

    // Bulk set all subtopic statuses for a module (values: 0=pending,1=completed,2=na,3=later on)
    function setModuleSubtopicsStatus(moduleId, value) {
        const radios = $(`tr[data-module-id="${moduleId}"][data-subtopic-id!=""] input[type="radio"][value="${value}"]`);
        radios.each(function() {
            $(this).prop('checked', true);
        });
    }

    // Checkbox handler: apply status and uncheck sibling bulk checkboxes
    function setModuleSubtopicsStatusFromCheckbox(moduleId, value, checkboxEl) {
        const $cb = $(checkboxEl);
        const groupSelector = `.crm-bulk-status[data-module-id="${moduleId}"]`;
        // Uncheck other bulk checkboxes in this module group
        $(groupSelector).not($cb).prop('checked', false);
        if ($cb.is(':checked')) {
            setModuleSubtopicsStatus(moduleId, value);
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }

    function saveCrmTrainingProgress(moduleId, subtopicId) {
        if (!crmProcessCurrentSociety) {
            setCrmProcessAlert('danger', 'No company selected.');
            return;
        }

        let statusSelector, remarkSelector;
        if (subtopicId === null) {
            statusSelector = `input[name="crm_status_${moduleId}_module"]:checked`;
            remarkSelector = `.crm-module-remark[data-module-id="${moduleId}"]`;
        } else {
            statusSelector = `input[name="crm_status_${moduleId}_${subtopicId}"]:checked`;
            remarkSelector = `.crm-subtopic-remark[data-module-id="${moduleId}"][data-subtopic-id="${subtopicId}"]`;
        }

        const status = parseInt($(statusSelector).val() || '0', 10);
        const remark = $(remarkSelector).val() || '';

        $('#crmProcessLoader').removeClass('d-none');
        setCrmProcessAlert(null, '');

        $.post('controller/crmProcessController.php', {
            action: 'save_crm_training_progress',
            society_id: crmProcessCurrentSociety,
            module_id: moduleId,
            subtopic_id: subtopicId !== null ? subtopicId : '',
            status: status,
            remark: remark
        }, function(resp) {
            $('#crmProcessLoader').addClass('d-none');
            if (resp && resp.success) {
                setCrmProcessAlert('success', resp.message || 'Progress saved successfully');
                // Refresh the modal to show updated data
                setTimeout(() => {
                    openCrmProcessModal(crmProcessCurrentSociety, crmProcessCompanyNameText, crmProcessSecretaryEmail, crmProcessPayMode, crmProcessAmount);
                }, 1000);
            } else {
                setCrmProcessAlert('danger', resp && resp.message ? resp.message : 'Failed to save progress');
            }
        }, 'json').fail(function() {
            $('#crmProcessLoader').addClass('d-none');
            setCrmProcessAlert('danger', 'Error while saving progress');
        });
    }

    function saveAllCrmTrainingProgress() {
        if (!crmProcessCurrentSociety) {
            setCrmProcessAlert('danger', 'No company selected.');
            return;
        }

        const modules = [];
        const subtopics = [];

        // Collect all subtopic progress and map per module
        const subtopicsByModule = {};
        $('tr[data-module-id][data-subtopic-id]').not('[data-subtopic-id=""]').each(function() {
            const moduleId = parseInt($(this).data('module-id'));
            const subtopicId = parseInt($(this).data('subtopic-id'));
            const checkedRadio = $(this).find(`input[name="crm_status_${moduleId}_${subtopicId}"]:checked`);
            const status = checkedRadio.length > 0 ? parseInt(checkedRadio.val() || '0', 10) : 0;
            const remark = $(this).find('.crm-subtopic-remark').val() || '';
            subtopics.push({
                moduleId,
                subtopicId,
                status,
                remark
            });
            if (!subtopicsByModule[moduleId]) subtopicsByModule[moduleId] = [];
            subtopicsByModule[moduleId].push(status);
        });

        // Collect module progress, deriving status from subtopics when present
        $('tr[data-module-id][data-subtopic-id=""]').each(function() {
            const moduleId = parseInt($(this).data('module-id'));
            const remark = $(this).find('.crm-module-remark').val() || '';
            const moduleSubStatuses = subtopicsByModule[moduleId] || [];
            let status = 0;
            if (moduleSubStatuses.length > 0) {
                const hasPending = moduleSubStatuses.some(s => s === 0 || isNaN(s));
                if (hasPending) {
                    status = 0;
                } else {
                    const allCompleted = moduleSubStatuses.every(s => s === 1);
                    const allNa = moduleSubStatuses.every(s => s === 2);
                    const allLater = moduleSubStatuses.every(s => s === 3);
                    if (allCompleted) status = 1;
                    else if (allNa) status = 2;
                    else if (allLater) status = 3;
                    else status = 1; // mixed non-pending counts as completed
                }
            } else {
                const checkedRadio = $(this).find(`input[name="crm_status_${moduleId}_module"]:checked`);
                status = checkedRadio.length > 0 ? parseInt(checkedRadio.val() || '0', 10) : 0;
            }
            modules.push({
                moduleId,
                status,
                remark
            });
        });

        if (modules.length === 0 && subtopics.length === 0) {
            setCrmProcessAlert('warning', 'No data to save.');
            return;
        }

        $('#crmProcessLoader').removeClass('d-none');
        setCrmProcessAlert(null, '');

        const requests = [];

        // Save all modules
        modules.forEach(item => {
            requests.push($.post('controller/crmProcessController.php', {
                action: 'save_crm_training_progress',
                society_id: crmProcessCurrentSociety,
                module_id: item.moduleId,
                subtopic_id: '',
                status: item.status,
                remark: item.remark
            }, null, 'json'));
        });

        // Save all subtopics
        subtopics.forEach(item => {
            requests.push($.post('controller/crmProcessController.php', {
                action: 'save_crm_training_progress',
                society_id: crmProcessCurrentSociety,
                module_id: item.moduleId,
                subtopic_id: item.subtopicId,
                status: item.status,
                remark: item.remark
            }, null, 'json'));
        });

        $.when.apply($, requests).done(function() {
            $('#crmProcessLoader').addClass('d-none');
            setCrmProcessAlert('success', 'All progress saved successfully');
            // Refresh the modal to show updated data
            setTimeout(() => {
                openCrmProcessModal(crmProcessCurrentSociety, crmProcessCompanyNameText, crmProcessSecretaryEmail, crmProcessPayMode, crmProcessAmount);
            }, 1000);
        }).fail(function() {
            $('#crmProcessLoader').addClass('d-none');
            setCrmProcessAlert('danger', 'Error while saving progress');
        });
    }

    let crmProcessDetailsData = null;
    let crmProcessDetailsCompanyName = '';

    function downloadCrmProcessDetails(societyId, companyName) {
        if (!societyId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Invalid company ID',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        crmProcessDetailsCompanyName = companyName || '';
        $('#crmProcessDetailsCompanyName').text(companyName || '');
        $('#crmProcessDetailsModal').modal('show');
        $('#crmProcessDetailsLoader').show();
        $('#crmProcessDetailsContent').hide();

        $.post('controller/crmProcessController.php', {
            action: 'get_crm_training_modules',
            society_id: societyId
        }, function(resp) {
            $('#crmProcessDetailsLoader').hide();
            if (resp && resp.success) {
                crmProcessDetailsData = resp;
                renderCrmProcessDetails(resp.topics || [], resp.progress || {});
                $('#crmProcessDetailsContent').show();
            } else {
                $('#crmProcessDetailsContent').html('<div class="alert alert-danger">Failed to load CRM process data</div>').show();
            }
        }, 'json').fail(function() {
            $('#crmProcessDetailsLoader').hide();
            $('#crmProcessDetailsContent').html('<div class="alert alert-danger">Error loading CRM process data</div>').show();
        });
    }

    function formatRemarkWithTooltip(remark, maxLength = 50) {
        if (!remark || remark.trim() === '') {
            return '<span class="text-muted">-</span>';
        }
        const escapedRemark = escapeHtml(remark);
        if (remark.length <= maxLength) {
            return escapedRemark;
        }
        const truncated = remark.substring(0, maxLength) + '...';
        return '<span data-toggle="tooltip" data-placement="top" title="' + escapedRemark + '" style="cursor: pointer;">' + escapeHtml(truncated) + '</span>';
    }

    // Derive module status from its subtopics (if any)
    function deriveModuleStatus(module, progressMap) {
        if (!module.subtopics || module.subtopics.length === 0) {
            const moduleKey = module.module_id + '_module';
            const mp = progressMap[moduleKey] || { status: 0 };
            return parseInt(mp.status) || 0;
        }
        const statuses = module.subtopics.map(st => {
            const key = module.module_id + '_' + st.subtopic_id;
            const sp = progressMap[key] || { status: 0 };
            return parseInt(sp.status) || 0;
        });
        if (statuses.some(s => s === 0)) return 0;
        if (statuses.every(s => s === 1)) return 1;
        if (statuses.every(s => s === 2)) return 2;
        if (statuses.every(s => s === 3)) return 3;
        return 1; // mixed non-pending counts as completed
    }

    // Get module completed date: max completed_date of subtopics if any, else module's own completed_date
    function getModuleCompletedDate(module, progressMap) {
        let dates = [];
        if (module.subtopics && module.subtopics.length > 0) {
            module.subtopics.forEach(st => {
                const key = module.module_id + '_' + st.subtopic_id;
                const sp = progressMap[key] || {};
                if (sp.completed_date) {
                    const d = new Date(sp.completed_date);
                    if (!isNaN(d)) dates.push(d);
                }
            });
        }
        if (dates.length === 0) {
            const moduleKey = module.module_id + '_module';
            const mp = progressMap[moduleKey] || {};
            if (mp.completed_date) {
                const d = new Date(mp.completed_date);
                if (!isNaN(d)) dates.push(d);
            }
        }
        if (dates.length === 0) return null;
        const maxDate = new Date(Math.max.apply(null, dates));
        return maxDate;
    }

    function renderCrmProcessDetails(topics, progressMap) {
        const container = $('#crmProcessDetailsContent');

        if (!topics || topics.length === 0) {
            container.html('<div class="text-center text-muted">No CRM training modules found</div>');
            return;
        }

        let html = '<table class="table table-bordered table-striped">';
        html += '<thead class="thead-light">';
        html += '<tr>';
        html += '<th style="width: 5%;">#</th>';
        html += '<th style="width: 30%;">Module / Subtopic</th>';
        html += '<th style="width: 10%;">Priority</th>';
        html += '<th style="width: 10%;">Days/Min</th>';
        html += '<th style="width: 10%;">Status</th>';
        html += '<th style="width: 25%;">Remark</th>';
        html += '<th style="width: 10%;">Completed Date</th>';
        html += '</tr>';
        html += '</thead><tbody>';

        let rowNumber = 1;
        topics.forEach(topic => {
            const topicName = topic.topic_name || 'Ungrouped';
            const topicCompletionDays = topic.topic_completion_days !== null && topic.topic_completion_days !== undefined ? topic.topic_completion_days : null;
            const topicCompletionDaysText = topicCompletionDays !== null ? ` (${topicCompletionDays} days)` : '';

            html += `<tr class="table-info"><td colspan="7" class="font-weight-bold"><i class="fa fa-folder-open mr-2"></i>${escapeHtml(topicName)}${topicCompletionDaysText}</td></tr>`;

            topic.modules.forEach(module => {
                const moduleId = module.module_id;
                const moduleKey = moduleId + '_module';
                const moduleProgress = progressMap[moduleKey] || {
                    status: 0,
                    remark: '',
                    completed_date: null
                };
                const currentStatus = deriveModuleStatus(module, progressMap);
                const currentRemark = (moduleProgress.remark !== undefined && moduleProgress.remark !== null) ? moduleProgress.remark : '';
                const completedDateObj = currentStatus === 1 ? getModuleCompletedDate(module, progressMap) : null;
                const completedDate = completedDateObj ? completedDateObj.toLocaleString() : '';

                const moduleCompletionDays = module.module_completion_days !== null && module.module_completion_days !== undefined ? module.module_completion_days : null;
                const moduleCompletionDaysText = moduleCompletionDays !== null ? `${moduleCompletionDays} days` : '';
                const modulePriority = module.module_priority !== null && module.module_priority !== undefined ? module.module_priority : '';

                const statusText = currentStatus === 1 ? 'Completed' : (currentStatus === 2 ? 'NA' : (currentStatus === 3 ? 'Later On' : 'Pending'));
                const statusClass = currentStatus === 1 ? 'badge-success' : (currentStatus === 2 ? 'badge-secondary' : (currentStatus === 3 ? 'badge-info' : 'badge-warning'));

                html += '<tr>';
                html += '<td class="text-center">' + rowNumber++ + '</td>';
                html += '<td class="font-weight-bold">' + escapeHtml(module.module_name) + '</td>';
                html += '<td>' + escapeHtml(modulePriority) + '</td>';
                html += '<td>' + moduleCompletionDaysText + '</td>';
                html += '<td><span class="badge ' + statusClass + '">' + statusText + '</span></td>';
                html += '<td>' + formatRemarkWithTooltip(currentRemark) + '</td>';
                html += '<td>' + escapeHtml(completedDate) + '</td>';
                html += '</tr>';

                if (module.subtopics && module.subtopics.length > 0) {
                    module.subtopics.forEach(subtopic => {
                        const subtopicId = subtopic.subtopic_id;
                        const subtopicKey = moduleId + '_' + subtopicId;
                        const subtopicProgress = progressMap[subtopicKey] || {
                            status: 0,
                            remark: '',
                            completed_date: null
                        };
                        const currentSubtopicStatus = parseInt(subtopicProgress.status) || 0;
                        const currentSubtopicRemark = (subtopicProgress.remark !== undefined && subtopicProgress.remark !== null) ? subtopicProgress.remark : '';
                        const subtopicCompletedDate = subtopicProgress.completed_date ? new Date(subtopicProgress.completed_date).toLocaleString() : '';

                        const estimatedMinutes = subtopic.estimated_minutes !== null && subtopic.estimated_minutes !== undefined ? subtopic.estimated_minutes : null;
                        const estimatedTimeText = estimatedMinutes !== null ? `${estimatedMinutes} min` : '';
                        const subtopicDisplayOrder = subtopic.display_order !== null && subtopic.display_order !== undefined ? subtopic.display_order : '';

                        const subtopicStatusText = currentSubtopicStatus === 1 ? 'Completed' : (currentSubtopicStatus === 2 ? 'NA' : (currentSubtopicStatus === 3 ? 'Later On' : 'Pending'));
                        const subtopicStatusClass = currentSubtopicStatus === 1 ? 'badge-success' : (currentSubtopicStatus === 2 ? 'badge-secondary' : (currentSubtopicStatus === 3 ? 'badge-info' : 'badge-warning'));

                        html += '<tr class="table-light">';
                        html += '<td class="text-center">' + rowNumber++ + '</td>';
                        html += '<td style="padding-left: 30px;"><i class="fa fa-arrow-right mr-2 text-muted"></i>' + escapeHtml(subtopic.subtopic_name) + '</td>';
                        html += '<td>#' + subtopicDisplayOrder + '</td>';
                        html += '<td>' + estimatedTimeText + '</td>';
                        html += '<td><span class="badge ' + subtopicStatusClass + '">' + subtopicStatusText + '</span></td>';
                        html += '<td>' + formatRemarkWithTooltip(currentSubtopicRemark) + '</td>';
                        html += '<td>' + escapeHtml(subtopicCompletedDate) + '</td>';
                        html += '</tr>';
                    });
                }
            });
        });

        html += '</tbody></table>';
        container.html(html);

        // Initialize tooltips for truncated remarks
        container.find('[data-toggle="tooltip"]').tooltip();
    }

    function exportCrmProcessPDF() {
        if (!crmProcessDetailsData) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No data available to export. Please open the details modal first.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        const topics = crmProcessDetailsData.topics || [];
        const progressMap = crmProcessDetailsData.progress || {};
        const companyName = crmProcessDetailsCompanyName || 'Company';

        let pdfContent = '<!DOCTYPE html><html><head><meta charset="utf-8">';
        pdfContent += '<title>CRM Process Details - ' + (companyName || 'Company') + '</title>';
        pdfContent += '<style>';
        pdfContent += 'body { font-family: Arial, sans-serif; margin: 20px; }';
        pdfContent += '.header { text-align: center; margin-bottom: 20px; }';
        pdfContent += '.company-name { font-size: 18px; font-weight: bold; color: #333; }';
        pdfContent += '.generated-date { font-size: 12px; color: #666; margin-top: 5px; }';
        pdfContent += 'table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
        pdfContent += 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
        pdfContent += 'th { background-color: #f2f2f2; font-weight: bold; }';
        pdfContent += '.topic-header { background-color: #e8f4f8; font-weight: bold; }';
        pdfContent += '.subtopic-row { background-color: #f9f9f9; }';
        pdfContent += '.subtopic-name { padding-left: 20px; }';
        pdfContent += '.module-name { font-weight: bold; }';
        pdfContent += '.status-pending { color: #ffc107; font-weight: bold; }';
        pdfContent += '.status-completed { color: #28a745; font-weight: bold; }';
        pdfContent += '.status-na { color: #6c757d; font-style: italic; }';
        pdfContent += '.status-later { color: #6f42c1; font-weight: bold; }';
        pdfContent += '@media print { body { margin: 0; } .no-print { display: none; } }';
        pdfContent += '</style></head><body>';

        pdfContent += '<div class="header">';
        pdfContent += '<div class="company-name">CRM Process Details - ' + escapeHtml(companyName) + '</div>';
        pdfContent += '<div class="generated-date">Generated on: ' + new Date().toLocaleString() + '</div>';
        pdfContent += '</div>';

        pdfContent += '<table><thead><tr>';
        pdfContent += '<th>#</th><th>Module / Subtopic</th><th>Priority</th><th>Days/Min</th><th>Status</th><th>Remark</th><th>Completed Date</th>';
        pdfContent += '</tr></thead><tbody>';

        let rowNumber = 1;
        topics.forEach(topic => {
            const topicName = topic.topic_name || 'Ungrouped';
            const topicCompletionDays = topic.topic_completion_days !== null && topic.topic_completion_days !== undefined ? topic.topic_completion_days : null;
            const topicCompletionDaysText = topicCompletionDays !== null ? ` (${topicCompletionDays} days)` : '';

            pdfContent += '<tr class="topic-header"><td colspan="7"><strong>Topic: ' + escapeHtml(topicName) + topicCompletionDaysText + '</strong></td></tr>';

            topic.modules.forEach(module => {
                const moduleId = module.module_id;
                const moduleKey = moduleId + '_module';
                const moduleProgress = progressMap[moduleKey] || {
                    status: 0,
                    remark: '',
                    completed_date: null
                };
                const currentStatus = deriveModuleStatus(module, progressMap);
                const currentRemark = (moduleProgress.remark !== undefined && moduleProgress.remark !== null) ? moduleProgress.remark : '';
                const completedDateObj = currentStatus === 1 ? getModuleCompletedDate(module, progressMap) : null;
                const completedDate = completedDateObj ? completedDateObj.toLocaleString() : '';

                const moduleCompletionDays = module.module_completion_days !== null && module.module_completion_days !== undefined ? module.module_completion_days : null;
                const moduleCompletionDaysText = moduleCompletionDays !== null ? `${moduleCompletionDays} days` : '';
                const modulePriority = module.module_priority !== null && module.module_priority !== undefined ? module.module_priority : '';

                const statusText = currentStatus === 1 ? 'Completed' : (currentStatus === 2 ? 'NA' : (currentStatus === 3 ? 'Later On' : 'Pending'));
                const statusClass = currentStatus === 1 ? 'status-completed' : (currentStatus === 2 ? 'status-na' : (currentStatus === 3 ? 'status-later' : 'status-pending'));

                pdfContent += '<tr class="module-name">';
                pdfContent += '<td>' + rowNumber++ + '</td>';
                pdfContent += '<td><strong>' + escapeHtml(module.module_name) + '</strong></td>';
                pdfContent += '<td>' + escapeHtml(modulePriority) + '</td>';
                pdfContent += '<td>' + moduleCompletionDaysText + '</td>';
                pdfContent += '<td class="' + statusClass + '">' + statusText + '</td>';
                pdfContent += '<td>' + escapeHtml(currentRemark) + '</td>';
                pdfContent += '<td>' + escapeHtml(completedDate) + '</td>';
                pdfContent += '</tr>';

                if (module.subtopics && module.subtopics.length > 0) {
                    module.subtopics.forEach(subtopic => {
                        const subtopicId = subtopic.subtopic_id;
                        const subtopicKey = moduleId + '_' + subtopicId;
                        const subtopicProgress = progressMap[subtopicKey] || {
                            status: 0,
                            remark: '',
                            completed_date: null
                        };
                        const currentSubtopicStatus = parseInt(subtopicProgress.status) || 0;
                        const currentSubtopicRemark = (subtopicProgress.remark !== undefined && subtopicProgress.remark !== null) ? subtopicProgress.remark : '';
                        const subtopicCompletedDate = subtopicProgress.completed_date ? new Date(subtopicProgress.completed_date).toLocaleString() : '';

                        const estimatedMinutes = subtopic.estimated_minutes !== null && subtopic.estimated_minutes !== undefined ? subtopic.estimated_minutes : null;
                        const estimatedTimeText = estimatedMinutes !== null ? `${estimatedMinutes} min` : '';
                        const subtopicDisplayOrder = subtopic.display_order !== null && subtopic.display_order !== undefined ? subtopic.display_order : '';

                        const subtopicStatusText = currentSubtopicStatus === 1 ? 'Completed' : (currentSubtopicStatus === 2 ? 'NA' : (currentSubtopicStatus === 3 ? 'Later On' : 'Pending'));
                        const subtopicStatusClass = currentSubtopicStatus === 1 ? 'status-completed' : (currentSubtopicStatus === 2 ? 'status-na' : (currentSubtopicStatus === 3 ? 'status-later' : 'status-pending'));

                        pdfContent += '<tr class="subtopic-row subtopic-name">';
                        pdfContent += '<td>' + rowNumber++ + '</td>';
                        pdfContent += '<td>  └─ ' + escapeHtml(subtopic.subtopic_name) + '</td>';
                        pdfContent += '<td>#' + subtopicDisplayOrder + '</td>';
                        pdfContent += '<td>' + estimatedTimeText + '</td>';
                        pdfContent += '<td class="' + subtopicStatusClass + '">' + subtopicStatusText + '</td>';
                        pdfContent += '<td>' + escapeHtml(currentSubtopicRemark) + '</td>';
                        pdfContent += '<td>' + escapeHtml(subtopicCompletedDate) + '</td>';
                        pdfContent += '</tr>';
                    });
                }
            });
        });

        pdfContent += '</tbody></table>';
        pdfContent += '<script>window.onload=function(){window.print();setTimeout(function(){window.close();if(window.opener){window.opener.focus();}},800);}<' + '/script>';
        pdfContent += '</body></html>';
        
        // Open preview (like setup/product status PDF preview)
        const printWindow = window.open('', '_blank');
        printWindow.document.open();
        printWindow.document.write(pdfContent);
        printWindow.document.close();
        printWindow.focus();
    }

    function exportCrmProcessExcel() {
        if (!crmProcessDetailsData) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No data available to export. Please open the details modal first.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        const topics = crmProcessDetailsData.topics || [];
        const progressMap = crmProcessDetailsData.progress || {};
        const companyName = crmProcessDetailsCompanyName || 'Company';
        const fileName = `CRM_Process_${companyName.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().split('T')[0]}`;

        let excelContent = '<table border="1">';
        excelContent += '<tr><th colspan="7" style="background-color:#4CAF50;color:#fff;font-weight:bold;text-align:center;padding:10px;">CRM Process Details - ' + escapeHtml(companyName) + '</th></tr>';
        excelContent += '<tr><th colspan="7" style="background-color:#f0f0f0;text-align:center;padding:5px;">Generated on: ' + new Date().toLocaleString() + '</th></tr>';
        excelContent += '<tr></tr>';
        excelContent += '<tr style="background-color:#e8f4f8;font-weight:bold;">';
        excelContent += '<th>#</th><th>Module / Subtopic</th><th>Priority</th><th>Days/Min</th><th>Status</th><th>Remark</th><th>Completed Date</th>';
        excelContent += '</tr>';

        let rowNumber = 1;
        topics.forEach(topic => {
            const topicName = topic.topic_name || 'Ungrouped';
            const topicCompletionDays = topic.topic_completion_days !== null && topic.topic_completion_days !== undefined ? topic.topic_completion_days : null;
            const topicCompletionDaysText = topicCompletionDays !== null ? ` (${topicCompletionDays} days)` : '';

            excelContent += '<tr style="background-color:#e8f4f8;font-weight:bold;"><td colspan="7">Topic: ' + escapeHtml(topicName) + topicCompletionDaysText + '</td></tr>';

            topic.modules.forEach(module => {
                const moduleId = module.module_id;
                const moduleKey = moduleId + '_module';
                const moduleProgress = progressMap[moduleKey] || {
                    status: 0,
                    remark: '',
                    completed_date: null
                };
                    const currentStatus = deriveModuleStatus(module, progressMap);
                const currentRemark = (moduleProgress.remark !== undefined && moduleProgress.remark !== null) ? moduleProgress.remark : '';
                    const completedDateObj = currentStatus === 1 ? getModuleCompletedDate(module, progressMap) : null;
                    const completedDate = completedDateObj ? completedDateObj.toLocaleString() : '';

                const moduleCompletionDays = module.module_completion_days !== null && module.module_completion_days !== undefined ? module.module_completion_days : null;
                const moduleCompletionDaysText = moduleCompletionDays !== null ? `${moduleCompletionDays} days` : '';
                const modulePriority = module.module_priority !== null && module.module_priority !== undefined ? module.module_priority : '';

                const statusText = currentStatus === 1 ? 'Completed' : (currentStatus === 2 ? 'NA' : (currentStatus === 3 ? 'Later On' : 'Pending'));

                excelContent += '<tr>';
                excelContent += '<td>' + rowNumber++ + '</td>';
                excelContent += '<td><strong>' + escapeHtml(module.module_name) + '</strong></td>';
                excelContent += '<td>' + escapeHtml(modulePriority) + '</td>';
                excelContent += '<td>' + moduleCompletionDaysText + '</td>';
                excelContent += '<td>' + statusText + '</td>';
                excelContent += '<td>' + escapeHtml(currentRemark) + '</td>';
                excelContent += '<td>' + escapeHtml(completedDate) + '</td>';
                excelContent += '</tr>';

                if (module.subtopics && module.subtopics.length > 0) {
                    module.subtopics.forEach(subtopic => {
                        const subtopicId = subtopic.subtopic_id;
                        const subtopicKey = moduleId + '_' + subtopicId;
                        const subtopicProgress = progressMap[subtopicKey] || {
                            status: 0,
                            remark: '',
                            completed_date: null
                        };
                        const currentSubtopicStatus = parseInt(subtopicProgress.status) || 0;
                        const currentSubtopicRemark = (subtopicProgress.remark !== undefined && subtopicProgress.remark !== null) ? subtopicProgress.remark : '';
                        const subtopicCompletedDate = subtopicProgress.completed_date ? new Date(subtopicProgress.completed_date).toLocaleString() : '';

                        const estimatedMinutes = subtopic.estimated_minutes !== null && subtopic.estimated_minutes !== undefined ? subtopic.estimated_minutes : null;
                        const estimatedTimeText = estimatedMinutes !== null ? `${estimatedMinutes} min` : '';
                        const subtopicDisplayOrder = subtopic.display_order !== null && subtopic.display_order !== undefined ? subtopic.display_order : '';

                        const subtopicStatusText = currentSubtopicStatus === 1 ? 'Completed' : (currentSubtopicStatus === 2 ? 'NA' : (currentSubtopicStatus === 3 ? 'Later On' : 'Pending'));

                        excelContent += '<tr style="background-color:#f9f9f9;">';
                        excelContent += '<td>' + rowNumber++ + '</td>';
                        excelContent += '<td>  └─ ' + escapeHtml(subtopic.subtopic_name) + '</td>';
                        excelContent += '<td>#' + subtopicDisplayOrder + '</td>';
                        excelContent += '<td>' + estimatedTimeText + '</td>';
                        excelContent += '<td>' + subtopicStatusText + '</td>';
                        excelContent += '<td>' + escapeHtml(currentSubtopicRemark) + '</td>';
                        excelContent += '<td>' + escapeHtml(subtopicCompletedDate) + '</td>';
                        excelContent += '</tr>';
                    });
                }
            });
        });

        excelContent += '</table>';

        const blob = new Blob([excelContent], {
            type: 'application/vnd.ms-excel'
        });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    // Clean up when modal is closed
    $(document).on('hidden.bs.modal', '#crmProcessDetailsModal', function() {
        crmProcessDetailsData = null;
        crmProcessDetailsCompanyName = '';
    });

    function renderCrmProcessRows() {
        const container = $('#crmProcessList');
        if (!crmProcessSteps || crmProcessSteps.length === 0) {
            container.html('<div class="text-center text-muted">No steps found</div>');
            return;
        }

        const options = [{
                v: 0,
                l: 'PENDING'
            },
            {
                v: 1,
                l: 'COMPLETED'
            },
            {
                v: 2,
                l: 'NA'
            }
        ];

        // Group by day_offset
        const groups = {};
        crmProcessSteps.forEach(step => {
            const day = parseInt(step.day_offset ?? 0, 10);
            if (!groups[day]) groups[day] = [];
            groups[day].push(step);
        });

        // Sort groups by day
        const dayKeys = Object.keys(groups).map(d => parseInt(d, 10)).sort((a, b) => a - b);

        let html = '';
        dayKeys.forEach(day => {
            const dayDesc = crmProcessDayDescriptions[day] || '';
            const title = day > 0 ? `Day ${day}${dayDesc ? ' ' + dayDesc : ''}` : '';
            let stepRows = '';
            let dayCompletedCount = 0;
            let dayTotalCount = 0;

            groups[day].forEach(step => {
                const config = step.config || {};
                const statusInt = parseInt(step.status ?? 0, 10);
                const isStepCompleted = statusInt === 1 || statusInt === 2; // Completed or NA
                
                // Count steps for day completion (exclude show_only and button steps that are read-only)
                if (config.show_only !== 'true' && !step.show_only && config.button !== 'true' && !step.button) {
                    dayTotalCount++;
                    if (isStepCompleted) { // Includes both Completed (1) and NA (2)
                        dayCompletedCount++;
                    }
                }

                // Step 1: show_only mode
                if (config.show_only === 'true' || step.show_only) {
                    const showVal = step.show_on_value || '-';
                    const hasValue = showVal && showVal !== '-';
                    stepRows += `
                        <div class="day-step pb-2 mb-2 border-bottom position-relative ${hasValue ? 'step-completed' : ''}" data-day="${day}" data-step-row="${step.key}" data-step-type="show_only" data-skip-bulk="1">
                            ${hasValue ? '<div class="crm-step-complete-icon"><i class="fa fa-check-circle"></i></div>' : ''}
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="font-weight-bold">${step.label}</div>
                                <div class="text-right">
                                    <div class="small text-muted">${step.show_on_label || 'Created Date'}</div>
                                    <div class="font-weight-bold text-success">${showVal}</div>
                                </div>
                            </div>
                        </div>
                    `;
                    return;
                }

                // Step 2: welcome email button
                if (config.button === 'true' || step.button) {
                    if (step.show_only) {
                        const sentDate = step.show_on_value || '-';
                        const hasSentDate = sentDate && sentDate !== '-';
                        stepRows += `
                            <div class="day-step pb-2 mb-2 border-bottom position-relative ${hasSentDate ? 'step-completed' : ''}" data-day="${day}" data-step-row="${step.key}" data-step-type="button" data-skip-bulk="1">
                                ${hasSentDate ? '<div class="crm-step-complete-icon"><i class="fa fa-check-circle"></i></div>' : ''}
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="font-weight-bold">${step.label}</div>
                                    <div class="text-right">
                                        <div class="small text-muted">${step.show_on_label || 'Welcome Email Sent Date'}</div>
                                        <div class="font-weight-bold text-success">${sentDate}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        stepRows += `
                            <div class="day-step pb-2 mb-2 border-bottom" data-day="${day}" data-step-row="${step.key}" data-step-type="button" data-skip-bulk="1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="font-weight-bold">${step.label}</div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="openWelcomeEmailFromCrm()">Send Welcome Email</button>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    return;
                }

                // Step 19: support handover with mark_completed
                if (config.mark_completed === 'true' || step.mark_completed) {
                    const isCompleted = statusInt === 1 || statusInt === 2; // Completed or NA
                    const completedDate = step.completed_at || '';
                    stepRows += `
                        <div class="day-step pb-3 mb-3 border-bottom position-relative ${isCompleted ? 'step-completed' : ''}" data-day="${day}" data-step-row="${step.key}" data-step-type="handover">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="font-weight-bold">${step.label}</div>
                                <div>
                                    ${isCompleted ? `
                                        <span class="badge badge-success">Completed</span>
                                        ${completedDate !== '-' ? `<div class="small text-muted mt-1">${completedDate}${step.updated_by_name ? ` by ${step.updated_by_name}` : ''}</div>` : ''}
                                    ` : `
                                        <button type="button" class="btn btn-sm btn-success" onclick="markSupportHandover('${step.key}')">Mark Completed</button>
                                    `}
                                </div>
                            </div>
                            ${isCompleted ? `
                                <div class="mt-2">
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold">Post Implementation Remark:</label>
                                        <div class="border p-2 bg-light">${step.post_implementation_remark || '-'}</div>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold">Customer Expectation Remark:</label>
                                        <div class="border p-2 bg-light">${step.customer_expectation_remark || '-'}</div>
                                    </div>
                                </div>
                            ` : `
                                <div id="handover_fields_${step.key}" class="d-none">
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold">Post Implementation Remark <span class="text-danger">*</span></label>
                                        <textarea class="form-control form-control-sm crm-handover-post" rows="2" placeholder="Enter post implementation remark"></textarea>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold">Customer Expectation Remark <span class="text-danger">*</span></label>
                                        <textarea class="form-control form-control-sm crm-handover-customer" rows="2" placeholder="Enter customer expectation remark"></textarea>
                                    </div>
                                </div>
                            `}
                        </div>
                    `;
                    return;
                }

                // Step 3: status dropdown + remark (no value)
                if (step.key === 'step_3_requirement_gathering') {
                    const statusOptions = options.map(o => `<option value="${o.v}" ${statusInt === o.v ? 'selected' : ''}>${o.l}</option>`).join('');
                    const dateInfo = step.completed_at || '';
                    const dateClass = (statusInt === 1 || statusInt === 2) ? 'text-success' : 'text-muted';
                    const dateLabelPrefix = statusInt === 1 ? 'Completed: ' : 'Date: ';
                    stepRows += `
                        <div class="day-step pb-3 mb-3 border-bottom position-relative ${isStepCompleted ? 'step-completed' : ''}" data-day="${day}" data-step-row="${step.key}" data-step-type="status_remark">
                            ${isStepCompleted ? '<div class="crm-step-complete-icon"><i class="fa fa-check-circle"></i></div>' : ''}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="font-weight-bold">${step.label}</div>
                                <div style="width: 170px;">
                                    <select class="form-control form-control-sm single-select crm-step-status" data-step="${step.key}">
                                        ${statusOptions}
                                    </select>
                                </div>
                            </div>
                            ${dateInfo !== '' ? `
                                <div class="small ${dateClass} mb-2">
                                    ${dateLabelPrefix}${dateInfo}${step.updated_by_name ? ` by ${step.updated_by_name}` : ''}
                                </div>
                            ` : ''}
                            <div class="form-group mb-2">
                                <textarea class="form-control form-control-sm crm-step-remark" data-step="${step.key}" rows="2" placeholder="Remark">${step.remark || ''}</textarea>
                            </div>
                        </div>
                    `;
                    return;
                }

                // Step 4: multiselect with custom values
                if (config.multiselect === 'true') {
                    const multiselectOptions = config.multiselect_options || [];
                    const selectedValues = parseIntegrationValues(step.value);
                    const statusDate = step.completed_at || '';
                    const dateClass = (statusInt === 1 || statusInt === 2) ? 'text-success' : 'text-muted';
                    const dateLabelPrefix = statusInt === 1 ? 'Completed: ' : 'Date: ';
                    const customValues = selectedValues.filter(v => !multiselectOptions.includes(v));
                    // For multiselect, show check icon if there are selected values OR if status is Completed/NA
                    const hasSelectedValues = selectedValues.length > 0;
                    const showCheckIcon = isStepCompleted || hasSelectedValues;
                    const showCompletedStyle = isStepCompleted || hasSelectedValues;
                    
                    let multiselectHtml = '<div class="form-group mb-2"><label class="small font-weight-bold">Integration Types:</label>';
                    multiselectOptions.forEach((opt, idx) => {
                        const checked = selectedValues.includes(opt) ? 'checked' : '';
                        const checkboxId = `crm_checkbox_${step.key}_${idx}_${opt.replace(/[^a-zA-Z0-9]/g, '_')}`;
                        multiselectHtml += `
                            <div class="form-check">
                                <input class="form-check-input crm-multiselect-option" type="checkbox" id="${checkboxId}" value="${opt}" data-step="${step.key}" ${checked}>
                                <label class="form-check-label" for="${checkboxId}" style="cursor: pointer;">${opt}</label>
                            </div>
                        `;
                    });
                    customValues.forEach((opt, idx) => {
                        const checkboxId = `crm_checkbox_${step.key}_custom_${idx}_${opt.replace(/[^a-zA-Z0-9]/g, '_')}`;
                        multiselectHtml += `
                            <div class="form-check">
                                <input class="form-check-input crm-multiselect-option" type="checkbox" id="${checkboxId}" value="${opt}" data-step="${step.key}" checked>
                                <label class="form-check-label" for="${checkboxId}" style="cursor: pointer;">${opt}</label>
                            </div>
                        `;
                    });
                    if (config.multiselect_custom_value === 'true') {
                        multiselectHtml += `
                            <div class="mt-2">
                                <input type="text" class="form-control form-control-sm crm-multiselect-custom" data-step="${step.key}" placeholder="Add custom integration type and press Enter">
                            </div>
                        `;
                    }
                    multiselectHtml += '</div>';
                    
                    stepRows += `
                        <div class="day-step pb-3 mb-3 border-bottom position-relative ${showCompletedStyle ? 'step-completed' : ''}" data-day="${day}" data-step-row="${step.key}" data-step-type="multiselect">
                            ${showCheckIcon ? '<div class="crm-step-complete-icon"><i class="fa fa-check-circle"></i></div>' : ''}
                            <div class="font-weight-bold mb-2">${step.label}</div>
                            ${statusDate !== '' ? `
                                <div class="small ${dateClass} mb-2">
                                    ${dateLabelPrefix}${statusDate}${step.updated_by_name ? ` by ${step.updated_by_name}` : ''}
                                </div>
                            ` : ''}
                            ${multiselectHtml}
                            <div id="multiselect_selected_${step.key}" class="mb-2">
                                ${selectedValues.length > 0 ? `<div class="small text-muted">Selected: ${selectedValues.join(', ')}</div>` : ''}
                            </div>
                        </div>
                    `;
                    return;
                }

                // Step 5: date picker
                if (config.date === 'true') {
                    const statusDate = step.completed_at || '';
                    const dateClass = (statusInt === 1 || statusInt === 2) ? 'text-success' : 'text-muted';
                    const dateLabelPrefix = statusInt === 1 ? 'Completed: ' : 'Date: ';
                    stepRows += `
                        <div class="day-step pb-3 mb-3 border-bottom position-relative ${isStepCompleted ? 'step-completed' : ''}" data-day="${day}" data-step-row="${step.key}" data-step-type="date">
                            ${isStepCompleted ? '<div class="crm-step-complete-icon"><i class="fa fa-check-circle"></i></div>' : ''}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="font-weight-bold">${step.label}</div>
                                <div>
                                    ${statusDate !== '' ? `
                                        ${statusInt === 1 ? `<span class="badge badge-success">Completed</span>` : ''}
                                        <div class="small ${dateClass} mt-1">${dateLabelPrefix}${statusDate}${step.updated_by_name ? ` by ${step.updated_by_name}` : ''}</div>
                                    ` : ''}
                                </div>
                            </div>
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold">Meeting Date:</label>
                                <input type="text" class="form-control form-control-sm crm-step-date" data-step="${step.key}" value="${step.value || ''}" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                    `;
                    return;
                }

                // Steps 6-18: status dropdown only
                if (config.status === 'true') {
                    const statusOptions = options.map(o => `<option value="${o.v}" ${statusInt === o.v ? 'selected' : ''}>${o.l}</option>`).join('');
                    const statusDate = step.completed_at || '';
                    const dateClass = (statusInt === 1 || statusInt === 2) ? 'text-success' : 'text-muted';
                    const dateLabelPrefix = statusInt === 1 ? 'Completed: ' : 'Date: ';
                    stepRows += `
                        <div class="day-step pb-3 mb-3 border-bottom position-relative ${isStepCompleted ? 'step-completed' : ''}" data-day="${day}" data-step-row="${step.key}" data-step-type="status_only">
                            ${isStepCompleted ? '<div class="crm-step-complete-icon"><i class="fa fa-check-circle"></i></div>' : ''}
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="font-weight-bold">${step.label}</div>
                                    ${statusDate !== '' ? `
                                        <div class="small ${dateClass} mt-1">
                                            ${dateLabelPrefix}${statusDate}${step.updated_by_name ? ` by ${step.updated_by_name}` : ''}
                                        </div>
                                    ` : ''}
                                </div>
                                <div style="width: 170px;">
                                    <select class="form-control form-control-sm single-select crm-step-status" data-step="${step.key}">
                                        ${statusOptions}
                                    </select>
                                </div>
                            </div>
                        </div>
                    `;
                    return;
                }
            });

            const isDayCompleted = dayTotalCount > 0 && dayCompletedCount === dayTotalCount;
            const dayInfo = crmProcessDayInfo[day] || {};
            const isOverdue = dayInfo.is_overdue || false;
            const isDelayed = dayInfo.is_delayed || false;
            const dueDateFormatted = dayInfo.due_date_formatted || '';
            const completedDateFormatted = dayInfo.completed_date_formatted || '';
            const dayCardClass = isDayCompleted ? 'day-completed' : (isOverdue ? 'day-overdue' : '');
            
            html += `
                <div class="card crm-day-card mb-3 ${dayCardClass}" data-day="${day}">
                        <div class="card-body p-3 position-relative">
                        ${isDayCompleted && title ? '<div class="crm-day-complete-icon"><i class="fa fa-check-circle"></i></div>' : ''}
                        ${title ? `
                            <div class="font-weight-bold mb-2 ${isDayCompleted ? 'text-success' : (isOverdue ? 'text-danger' : '')}">${title}</div>
                            ${dueDateFormatted ? `
                                <div class="mb-2">
                                    <span class="small ${isOverdue ? 'text-danger font-weight-bold' : 'text-muted'}">
                                        Due: ${dueDateFormatted}
                                        ${isOverdue ? '<i class="fa fa-exclamation-circle text-danger ml-1" title="Overdue"></i>' : ''}
                                    </span>
                                    ${completedDateFormatted ? `
                                        <span class="small text-muted ml-3">•</span>
                                        <span class="small ${isDelayed ? 'text-danger' : 'text-success'} ml-2">
                                            Completed: ${completedDateFormatted}
                                            ${isDelayed ? '<i class="fa fa-exclamation-circle text-danger ml-1" title="Completed after due date"></i>' : ''}
                                        </span>
                                    ` : ''}
                                </div>
                            ` : ''}
                        ` : ''}
                        ${stepRows || '<div class="text-muted">No steps</div>'}
                        ${day !== 0 && day !== 1 ? `
                        <div class="text-right mt-3">
                            <button type="button" class="btn btn-sm btn-primary" onclick="saveCrmDay(${day})">Save Day</button>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });

        container.html(html);

        // Init datepicker for meeting date inputs (Step 5)
        container.find('.crm-step-date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd-mm-yyyy'
        });

        // Handle multiselect custom value input
        container.find('.crm-multiselect-custom').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                const stepKey = $(this).data('step');
                const customVal = $(this).val().trim();
                if (customVal) {
                    const checkboxId = `crm_checkbox_${stepKey}_custom_${Date.now()}_${customVal.replace(/[^a-zA-Z0-9]/g, '_')}`;
                    const checkbox = $(`<div class="form-check">
                        <input class="form-check-input crm-multiselect-option" type="checkbox" id="${checkboxId}" value="${customVal}" data-step="${stepKey}" checked>
                        <label class="form-check-label" for="${checkboxId}" style="cursor: pointer;">${customVal}</label>
                    </div>`);
                    $(this).closest('.form-group').find('.crm-multiselect-option').last().closest('.form-check').after(checkbox);
                    $(this).val('');
                }
            }
        });
    }

    function saveCrmStep(stepKey) {
        if (!crmProcessCurrentSociety) {
            setCrmProcessAlert('danger', 'No company selected.');
            return;
        }
        const status = parseInt($(`.crm-step-status[data-step="${stepKey}"]`).val() || '0', 10);
        const value = $(`.crm-step-value[data-step="${stepKey}"]`).val() || $(`.crm-step-date[data-step="${stepKey}"]`).val() || '';
        const remark = $(`.crm-step-remark[data-step="${stepKey}"]`).val() || '';
        $('#crmProcessLoader').removeClass('d-none');
        setCrmProcessAlert(null, '');

        $.post('controller/crmProcessController.php', {
            action: 'update_step',
            society_id: crmProcessCurrentSociety,
            step_key: stepKey,
            status: status,
            value: value,
            remark: remark
        }, function(resp) {
            $('#crmProcessLoader').addClass('d-none');
            if (resp && resp.success) {
                setCrmProcessAlert('success', 'Step updated');
                // Refresh to show updated timestamps
                openCrmProcessModal(crmProcessCurrentSociety, $('#crmProcessCompanyName').text());
            } else {
                setCrmProcessAlert('danger', resp && resp.message ? resp.message : 'Failed to update step');
            }
        }, 'json').fail(function() {
            $('#crmProcessLoader').addClass('d-none');
            setCrmProcessAlert('danger', 'Error while updating step');
        });
    }

    function saveCrmMultiselect(stepKey) {
        if (!crmProcessCurrentSociety) {
            setCrmProcessAlert('danger', 'No company selected.');
            return;
        }
        const selected = [];
        $(`.crm-multiselect-option[data-step="${stepKey}"]:checked`).each(function() {
            selected.push($(this).val());
        });
        const value = selected.join(CRM_MULTI_DELIM);
        $('#crmProcessLoader').removeClass('d-none');
        setCrmProcessAlert(null, '');

        $.post('controller/crmProcessController.php', {
            action: 'update_step',
            society_id: crmProcessCurrentSociety,
            step_key: stepKey,
            status: selected.length > 0 ? 1 : 0,
            value: value,
            remark: ''
        }, function(resp) {
            $('#crmProcessLoader').addClass('d-none');
            if (resp && resp.success) {
                setCrmProcessAlert('success', 'Integration types saved');
                openCrmProcessModal(crmProcessCurrentSociety, $('#crmProcessCompanyName').text());
            } else {
                setCrmProcessAlert('danger', resp && resp.message ? resp.message : 'Failed to save');
            }
        }, 'json').fail(function() {
            $('#crmProcessLoader').addClass('d-none');
            setCrmProcessAlert('danger', 'Error while saving');
        });
    }

    function markSupportHandover(stepKey) {
        $(`#handover_fields_${stepKey}`).removeClass('d-none');
    }

    function saveSupportHandover(stepKey) {
        if (!crmProcessCurrentSociety) {
            setCrmProcessAlert('danger', 'No company selected.');
            return;
        }
        const postRemark = $(`.crm-handover-post`).val().trim();
        const customerRemark = $(`.crm-handover-customer`).val().trim();
        
        if (!postRemark || !customerRemark) {
            setCrmProcessAlert('danger', 'Both remarks are required');
            return;
        }

        $('#crmProcessLoader').removeClass('d-none');
        setCrmProcessAlert(null, '');

        $.post('controller/crmProcessController.php', {
            action: 'update_step',
            society_id: crmProcessCurrentSociety,
            step_key: stepKey,
            status: 1,
            value: '',
            remark: postRemark + '|||' + customerRemark // Temporary separator, will be split in controller
        }, function(resp) {
            $('#crmProcessLoader').addClass('d-none');
            if (resp && resp.success) {
                setCrmProcessAlert('success', 'Support handover completed');
                openCrmProcessModal(crmProcessCurrentSociety, $('#crmProcessCompanyName').text());
            } else {
                setCrmProcessAlert('danger', resp && resp.message ? resp.message : 'Failed to save');
            }
        }, 'json').fail(function() {
            $('#crmProcessLoader').addClass('d-none');
            setCrmProcessAlert('danger', 'Error while saving');
        });
    }

    function saveCrmDay(day) {
        if (!crmProcessCurrentSociety) {
            setCrmProcessAlert('danger', 'No company selected.');
            return;
        }
        const cards = $(`.crm-day-card[data-day="${day}"] [data-step-row]`);
        if (!cards.length) {
            setCrmProcessAlert('danger', 'No steps to save for this day.');
            return;
        }
        const requests = [];
        $('#crmProcessLoader').removeClass('d-none');
        setCrmProcessAlert(null, '');

        let hasError = false;

        cards.each(function() {
            const $card = $(this);
            if ($card.data('skip-bulk') == 1) return;
            const stepKey = $card.data('step-row');
            const stepType = $card.data('step-type');
            const statusEl = $card.find('.crm-step-status');
            const dateEl = $card.find('.crm-step-date');
            const valueEl = $card.find('.crm-step-value');
            const remarkEl = $card.find('.crm-step-remark');
            const multiselectEls = $card.find('.crm-multiselect-option');
            const postRemarkEl = $card.find('.crm-handover-post');
            const customerRemarkEl = $card.find('.crm-handover-customer');

            let status = statusEl.length ? parseInt(statusEl.val() || '0', 10) : 0;
            let value = '';
            if (multiselectEls.length) {
                const selected = [];
                multiselectEls.filter(':checked').each(function() {
                    selected.push($(this).val());
                });
                value = selected.join(CRM_MULTI_DELIM);
            } else if (dateEl.length) {
                value = dateEl.val() || '';
            } else if (valueEl.length) {
                value = valueEl.val() || '';
            }
            let remark = remarkEl.length ? remarkEl.val() || '' : '';

            // Special handling for support handover (step 19)
            if (stepType === 'handover') {
                const postRemark = postRemarkEl.length ? postRemarkEl.val().trim() : '';
                const customerRemark = customerRemarkEl.length ? customerRemarkEl.val().trim() : '';
                if (!postRemark || !customerRemark) {
                    hasError = true;
                    return false; // break each
                }
                remark = `${postRemark}|||${customerRemark}`;
                status = 1; // mark completed when saving handover
            }

            requests.push($.post('controller/crmProcessController.php', {
                action: 'update_step',
                society_id: crmProcessCurrentSociety,
                step_key: stepKey,
                status: status,
                value: value,
                remark: remark
            }, null, 'json'));
        });

        if (hasError) {
            $('#crmProcessLoader').addClass('d-none');
            setCrmProcessAlert('danger', 'Please fill both remarks for Support Handover.');
            return;
        }

        if (!requests.length) {
            $('#crmProcessLoader').addClass('d-none');
            setCrmProcessAlert('danger', 'No savable steps for this day.');
            return;
        }

        $.when.apply($, requests).done(function() {
            $('#crmProcessLoader').addClass('d-none');
            setCrmProcessAlert('success', 'Day saved');
            openCrmProcessModal(crmProcessCurrentSociety, crmProcessCompanyNameText, crmProcessSecretaryEmail, crmProcessPayMode, crmProcessAmount);
        }).fail(function() {
            $('#crmProcessLoader').addClass('d-none');
            setCrmProcessAlert('danger', 'Error while saving day');
        });
    }

    function syncWelcomeTemplateByProduct() {
        var productType = $('#welcome_product_type').val() || 'hrms';
        if (productType === 'crm') {
            var selectedType = $('input[name=\"emailType\"]:checked').val();
            var templateId = selectedType === '2' ? 4 : 3; // 3 => With Invoice, 4 => Without Invoice
            $('#welcome_template_id').val(templateId);
        } else {
            $('#welcome_template_id').val('');
        }
    }

    $('input[name=\"emailType\"]').on('change', function() {
        syncWelcomeTemplateByProduct();
    });

    function openWelcomeEmailFromCrm() {
        if (!crmProcessCurrentSociety) {
            setCrmProcessAlert('danger', 'No company selected.');
            return;
        }
        var defaultTemplateId = $('input[name=\"emailType\"]:checked').val() === '2' ? 4 : 3;
        setSocietyid(crmProcessCurrentSociety, 'sendEmail', crmProcessPayMode, crmProcessAmount, crmProcessSecretaryEmail, defaultTemplateId, 'crm');
        syncWelcomeTemplateByProduct();
        $('#emailTypeModel').modal('show');
    }

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const value = input.value.trim();
            if (value !== '') {
                tagify.addTags([value]);
                tagify.removeAllTags();
                input.value = '';
            }
        }
    });

    input.addEventListener('blur', function() {
        const value = input.value.trim();
        if (value !== '') {
            tagify.addTags([value]);
            input.value = '';
        }
    });

    input.addEventListener('click', function() {
        const value = input.value.trim();
        if (value !== '') {
            tagify.addTags([value]);
            input.value = '';
        }
    });

    // Toggle Training Type filter based on Product Type selection
    function toggleTrainingTypeFilter() {
        const productType = document.getElementById('product_type') ? document.getElementById('product_type').value : 'hrms';
        const trainingTypeLabel = document.getElementById('training_type_label');
        const trainingTypeDiv = document.getElementById('training_type_div');
        const trainingTypeSelect = document.getElementById('training_type');
        const trainingTypeHidden = document.getElementById('training_type_hidden');
        
        if (productType === 'crm') {
            // Hide Training Type filter for CRM
            if (trainingTypeLabel) trainingTypeLabel.style.display = 'none';
            if (trainingTypeDiv) trainingTypeDiv.style.display = 'none';
            // Use hidden input for CRM (training_type = 0)
            if (trainingTypeSelect) {
                trainingTypeSelect.name = '';
                trainingTypeSelect.style.display = 'none';
            }
            if (trainingTypeHidden) {
                trainingTypeHidden.name = 'training_type';
                trainingTypeHidden.value = '0';
            }
        } else {
            // Show Training Type filter for HRMS
            if (trainingTypeLabel) trainingTypeLabel.style.display = '';
            if (trainingTypeDiv) trainingTypeDiv.style.display = '';
            // Use visible select for HRMS
            if (trainingTypeSelect) {
                trainingTypeSelect.name = 'training_type';
                trainingTypeSelect.style.display = '';
            }
            if (trainingTypeHidden) {
                trainingTypeHidden.name = '';
            }
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleTrainingTypeFilter();
    });
</script>

<script>
    // Helper function to escape HTML entities
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
    
    // Helper function to escape JavaScript string values
    function escapeJsString(str) {
        if (!str) return '';
        return String(str).replace(/\\/g, '\\\\')
                          .replace(/"/g, '\\"')
                          .replace(/'/g, "\\'")
                          .replace(/\n/g, '\\n')
                          .replace(/\r/g, '\\r')
                          .replace(/\t/g, '\\t');
    }

    function setSessionTimes() {
        const sessionSelect = document.getElementById('session_id');
        const selectedOption = sessionSelect.options[sessionSelect.selectedIndex];

        if (selectedOption) {
            document.getElementById("session_day_id").value = selectedOption.getAttribute("data-session-day-id");
            const startTime = selectedOption.getAttribute('data-start-time');
            const endTime = selectedOption.getAttribute('data-end-time');

            document.getElementById('start_time').value = startTime || '';
            document.getElementById('end_time').value = endTime || '';
        }
    }

    // Step delays alert: fetch and render
    function loadStepDelayAlerts() {
        var stateId = document.getElementById('state_id') ? document.getElementById('state_id').value : '';
        var cityId = document.getElementById('city_id') ? document.getElementById('city_id').value : '';
        var countryId = document.getElementById('country_id') ? document.getElementById('country_id').value : '';
        var riseFilter = document.getElementById('rise_filter') ? document.getElementById('rise_filter').value : '';
        var productType = document.getElementById('product_type') ? document.getElementById('product_type').value : 'hrms';
        $.ajax({
            url: 'get_timeline_step_alerts.php',
            method: 'GET',
            dataType: 'json',
            data: {
                countryId: countryId,
                sId: stateId,
                cId: cityId,
                rise_filter: riseFilter,
                product_type: productType
            },
            success: function(resp) {
                var list = (resp && resp.companies) ? resp.companies : [];
                if (list.length > 0) {
                    $('#step-delay-alert-count').text(list.length);
                    $('#step-delay-alert-btn').removeClass('d-none');
                } else {
                    $('#step-delay-alert-btn').addClass('d-none');
                }
                window._stepDelayCompanies = list;
                if ($('#stepDelayAlertModal').hasClass('show')) {
                    renderStepDelayTable();
                }
            },
            error: function() {
                $('#step-delay-alert-btn').addClass('d-none');
                window._stepDelayCompanies = [];
            }
        });
    }

    function renderStepDelayTable() {
        var tbody = $('#stepDelayTable tbody');
        tbody.empty();
        var list = window._stepDelayCompanies || [];

        // Apply filters
        var searchVal = ($('#stepDelaySearch').val() || '').toLowerCase().trim();
        var issueFilter = $('#stepDelayIssueFilter').val() || '';
        var onlyOverdue = $('#stepDelayOnlyOverdue').is(':checked');

        var filtered = list.filter(function(c) {
            var cid = (c.company_id || '').toString();
            var cname = (c.company_name || '').toString();
            var matchesSearch = true;
            if (searchVal) {
                matchesSearch = (cname.toLowerCase().indexOf(searchVal) !== -1) || cid.toLowerCase().indexOf(searchVal) !== -1 || (cid.indexOf(searchVal) !== -1);
            }

            var matchesIssue = true;
            if (issueFilter === 'welcome') matchesIssue = parseInt(c.step1_late, 10) === 1;
            else if (issueFilter === 'whatsapp') matchesIssue = parseInt(c.step2_late, 10) === 1;
            else if (issueFilter === 'setup_not_started') matchesIssue = (parseInt(c.setup_start_late, 10) === 1);
            else if (issueFilter === 'setup_incomplete') matchesIssue = parseInt(c.setup_incomplete_late, 10) === 1;
            else if (issueFilter === 'topic') matchesIssue = (c.topics_overdue_count && parseInt(c.topics_overdue_count, 10) > 0);
            else if (issueFilter === 'handover') matchesIssue = (parseInt(c.handover_overdue, 10) === 1);

            var matchesOverdue = true;
            if (onlyOverdue) {
                matchesOverdue = (parseInt(c.setup_start_late, 10) === 1) || (parseInt(c.setup_incomplete_late, 10) === 1);
            }

            return matchesSearch && matchesIssue && matchesOverdue;
        });

        if (filtered.length === 0) {
            tbody.append('<tr><td class="text-muted" colspan="4">No Delay Alert.</td></tr>');
            return;
        }
        filtered.forEach(function(c) {
            var issues = [];
            var productType = document.getElementById('product_type') ? document.getElementById('product_type').value : 'hrms';
            
            if (parseInt(c.step1_late, 10) === 1) {
                var dWel = parseInt(c.step1_late_days, 10) || 0;
                if (productType === 'crm') {
                    issues.push('CRM Welcome Email late' + (dWel ? ' (' + dWel + ' days)' : ''));
                } else {
                    issues.push('Welcome Email late' + (dWel ? ' (' + dWel + ' days)' : ''));
                }
            }
            if (parseInt(c.step2_late, 10) === 1) {
                var dWa = parseInt(c.step2_late_days, 10) || 0;
                issues.push('WhatsApp Group late' + (dWa ? ' (' + dWa + ' days)' : ''));
            }
            if (parseInt(c.setup_start_late, 10) === 1) {
                var d = parseInt(c.setup_not_started_days_overdue, 10) || 0;
                issues.push('Setup not started' + (d > 0 ? ' (' + d + ' days late)' : ''));
            } else if (parseInt(c.setup_incomplete_late, 10) === 1) {
                var d2 = parseInt(c.setup_incomplete_days_overdue, 10) || 0;
                issues.push('Setup incomplete' + (d2 > 0 ? ' (' + d2 + ' days late)' : ''));
            }
            // Append topic issues if any
            if (Array.isArray(c.topics_overdue)) {
                c.topics_overdue.forEach(function(t) {
                    var tname = t.topic_name || '';
                    var tpart = t.participant ? (' - ' + t.participant) : '';
                    var tdays = parseInt(t.days, 10) || 0;
                    if (t.type === 'start') {
                        issues.push('Topic ' + tname + tpart + ' not started (' + tdays + ' days late)');
                    } else if (t.type === 'complete') {
                        issues.push('Topic ' + tname + tpart + ' incomplete (' + tdays + ' days late)');
                    } else if (t.type === 'overdue') {
                        issues.push('Topic ' + tname + ' overdue (' + tdays + ' days late)');
                    }
                });
            }
            // Append handover overdue if any
            if (parseInt(c.handover_overdue, 10) === 1) {
                var hdays = parseInt(c.handover_overdue_days, 10) || 0;
                if (productType === 'crm') {
                    issues.push('CRM Handover overdue (' + hdays + ' days late)');
                } else {
                    issues.push('Handover overdue (' + hdays + ' days late)');
                }
            }
            var safeName = $('<div>').text(c.company_name || '').html();
            var safeCity = $('<div>').text(c.city_name || '').html();
            var companyId = (c.company_id || '');
            var tr = $('<tr></tr>');
            tr.append('<td>' + companyId + '</td>');
            tr.append('<td>' + safeName + '</td>');
            tr.append('<td>' + safeCity + '</td>');
            tr.append('<td>' + issues.join(', ') + '</td>');
            tbody.append(tr);
        });
    }

    $(document).on('click', '#step-delay-alert-btn', function() {
        renderStepDelayTable();
    });

    $(document).ready(function() {
        loadStepDelayAlerts();
        $(document).on('input change', '#stepDelaySearch, #stepDelayIssueFilter, #stepDelayOnlyOverdue', function() {
            renderStepDelayTable();
        });
    });

    function openScheduleMeetingModal(society_id, bms_admin_id) {
        $("#scheduleMeeting").modal("show");
        $("#society_id").val(society_id);
        $("#bms_admin_id").val(bms_admin_id);
    }

    function openBatchMeetingModal(society_id, bms_admin_id) {
        $("#batch_training_date").val('');
        $("#batch_meeting_list").html('');
        $("#meetingAvailableDiv").addClass('d-none');
        $("#batch_society_id").val(society_id);
        $("#batch_bms_admin_id").val(bms_admin_id);
        $("#scheduleBatch").modal("show");
    }

    function getMeetingName() {
        const sessionId = document.getElementById('session_id').value;
        const trainingDate = document.getElementById('training_date').value;
        const society_id = document.getElementById('society_id').value;

        if (sessionId && trainingDate) {
            $.ajax({
                url: "controller/trainingController.php",
                type: "POST",
                data: {
                    action: "fetchMeeting",
                    session_id: sessionId,
                    trainingDate: trainingDate,
                    society_id: society_id,
                    csrf: csrf
                },
                success: function(response) {
                    let data = JSON.parse(response);
                    let meetingList = document.getElementById('meeting_list');
                    meetingList.innerHTML = "";
                    let meetingsContent = "";

                    data.forEach((item, index) => {
                        let radioId = `meeting_${index}`;
                        let isDisabled = item.flag === "1" ? "disabled" : "";
                        let labelClass = item.flag === "1" ? "disabled text-muted" : "";
                        let meetingNameEsc = escapeHtml(item.meeting_name || '');
                        let hostNameEsc = escapeHtml(item.host_name || '');
                        let meetingNameJsEsc = escapeJsString(item.meeting_name || '');

                        let meetingItem = `
                        <div class="col-md-6 mb-3">
                            <label class="btn btn-outline-primary w-100 text-center p-3 ${labelClass}">
                                <input type="radio" id="${radioId}" name="selected_meeting" 
                                       value="${meetingNameJsEsc}" data-id="${item.training_schedule_master_id}" 
                                       class="mr-2" ${isDisabled}>
                                ${meetingNameEsc} - Host: ${hostNameEsc}
                            </label>
                        </div>`;
                        meetingsContent += meetingItem;
                    });

                    meetingList.innerHTML = meetingsContent;

                    $("input[name='selected_meeting']").change(function() {
                        $("#meeting_name").val($(this).val());
                        $("#training_schedule_master_id").val($(this).data("id"));
                        $("#submitMeeting").prop("disabled", false);
                    });
                },
                error: function() {}
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        window.getMeetingName = getMeetingName;
    });

    function getBatchMeetings() {
        const trainingDate = document.getElementById('batch_training_date').value;
        const society_id = document.getElementById('batch_society_id').value;
        const meetingType = document.getElementById('meeting_type').value;

        const batchNamesDiv = document.getElementById('fetchBatchNames');
        const meetingAvailableDiv = document.getElementById('meetingAvailableDiv');
        const meetingList = document.getElementById('batch_meeting_list');

        batchNamesDiv.classList.add('d-none');
        meetingAvailableDiv.classList.add('d-none');
        meetingList.innerHTML = "";

        if (meetingType === "0") {
            batchNamesDiv.classList.remove('d-none');
            return;
        }

        if (trainingDate && meetingType === "1") {
            $.ajax({
                url: "controller/trainingController.php",
                type: "POST",
                data: {
                    action: "fetchBatchMeeting",
                    trainingDate: trainingDate,
                    society_id: society_id,
                    csrf: csrf
                },
                success: function(response) {
                    let data = JSON.parse(response);
                    let meetingsContent = "";

                    data.forEach((item, index) => {
                        let radioId = `meeting_${index}`;
                        let isChecked = item.flag === "1" ? "checked" : "";
                        let isDisabled = item.flag === "1" ? "disabled" : "";
                        let style = item.flag === "1" ? 'color: #6c757d; cursor: not-allowed;' : '';
                        let meetingNameEsc = escapeHtml(item.meeting_name || '');
                        let hostNameEsc = escapeHtml(item.host_name || '');
                        let meetingDateEsc = escapeHtml(item.meeting_date || '');

                        meetingsContent += `
                    <div class="col-md-3 mb-3">
                        <label class="btn btn-outline-primary w-100 text-center p-2" style="${style}">
                            <input type="checkbox" id="${radioId}" name="selected_batch_meeting[]" 
                                value="${item.slot_id}" class="mr-2" ${isChecked} ${isDisabled}>
                            ${meetingNameEsc} <br>Host: ${hostNameEsc} <br>${meetingDateEsc}
                        </label>
                    </div>`;
                    });

                    meetingList.innerHTML = meetingsContent;

                    if (data.length > 0) {
                        meetingAvailableDiv.classList.remove('d-none');
                    }
                },
                error: function() {
                    console.error("Error fetching meetings.");
                }
            });
        }
    }

    function getAllBuildingData(request_society_id) {
        $.ajax({
            url: "getBuildingDetails.php",
            cache: false,
            type: "POST",
            data: {
                setting_society_id: request_society_id
            },
            success: function(response) {
                $('#settingsForm').html(response);
            }
        });
    }

    function setSocietyid(id, type, pay_mode = null, pay_amount = null, secretary_email = '', template_id = '', product_type = 'hrms') {
        var society_id = id;
        if (type == 'sendEmail') {
            if (pay_mode == "") {
                pay_mode = 1;
            }
            if (pay_amount == "") {
                pay_amount = 0;
            }
            $('#receivedamount').val(pay_amount);
            $('#pay_mode').val(pay_mode);
            $('#society_id_sendemail').val(society_id);
            $('#secretary_email').text(secretary_email);
            $('#welcome_product_type').val(product_type || '');
            $('#welcome_template_id').val(template_id || '');
            if (product_type === 'crm') {
                syncWelcomeTemplateByProduct();
            }
        } else if (type == 'onBoarding') {
            if (pay_mode != "") {
                $("#autoclose-datepickerFrom").val(pay_mode);
            }
            if (pay_amount != "") {
                $("#is_data_onboarding").val(pay_amount);
            }
            $('#society_id_onboarding').val(society_id);
        }
    }

    function fetchSocietyName(id, name) {
        $("#society_name").val(name);
        $("#company_name_span").text(name);
        $("#society_id").val(id);

        $.ajax({
            type: 'POST',
            url: './controller/attendanceStatusController.php',
            data: {
                getSetupStatus: "getSetupStatus",
                company_id: id,
                csrf: csrf
            },
            success: function(data) {
                $('#statusTableContent').html(data);
                // console.log(data);
            },
            error: function(error) {
                // console.log("Error fetching data:", error);
            }
        });
    }

    function fetchTrainingModules(id, companyName = '') {
        // Store for export title
        window.currentSetupCompanyId = id;
        window.currentSetupCompanyName = companyName || '';
        $.ajax({
            url: './ajaxGetSetupModuleName.php',
            method: 'POST',
            data: {
                getSetupModule: "getSetupModule",
                society_id: id,
                csrf: csrf
            },
            success: function(response) {
                // console.log(response);
                $('#setupStatus').html(response);
                // cache for export
                try {
                    const setupExport = {
                        htmlContent: response,
                        companyName: window.currentSetupCompanyName || '',
                        timestamp: new Date().toISOString()
                    };
                    localStorage.setItem('setupStatusData', JSON.stringify(setupExport));
                } catch (e) {}

            },
            error: function(xhr, status, error) {
                // console.error('Error fetching data: ', error);
                $('#setupStatus').html('<tr><td colspan="3">Error fetching data.</td></tr>');
            }
        });
    }

    function fetchProductTrainingModules(id, companyName = '') {
        if (companyName) {
            document.getElementById('productCompanyName').innerText = companyName;
        } else {
            document.getElementById('productCompanyName').innerText = '';
        }
        // Store company info for export
        window.currentCompanyId = id;
        window.currentCompanyName = companyName;

        $.ajax({
            url: 'ajaxGetProduct.php',
            method: 'POST',
            data: {
                getProductModule: "getProductModule",
                society_id: id,
                csrf: csrf
            },
            success: function(response) {
                $('#productStatus').html(response);
                storeTrainingDataInLocalStorage(id, companyName, response);
            },
            error: function(xhr, status, error) {
                $('#productStatus').html('<tr><td colspan="3">Error fetching data.</td></tr>');
            }
        });
    }

    function storeTrainingDataInLocalStorage(companyId, companyName, htmlResponse) {
        const trainingData = {
            companyId: companyId,
            companyName: companyName,
            htmlContent: htmlResponse,
            timestamp: new Date().toISOString(),
            exportData: extractTrainingDataFromHTML(htmlResponse)
        };

        localStorage.setItem('productTrainingData', JSON.stringify(trainingData));
    }

    function extractTrainingDataFromHTML(htmlContent) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlContent;

        const data = {
            modules: []
        };
        const ordered = [];

        // Find all main table rows (excluding collapsed subtopic rows)
        const mainRows = tempDiv.querySelectorAll('tbody tr:not(.collapse)');
        let currentTopic = '';
        let currentModule = null;

        mainRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (!cells || cells.length === 0) return;

            // Check if this is a topic header row
            if (cells.length === 1 || cells[0].hasAttribute('colspan')) {
                const topicText = cells[0].textContent.trim();
                if (topicText.startsWith('Topic:')) {
                    currentTopic = topicText.replace('Topic:', '').trim();
                }
                return;
            }

            // Check if this is a main module row (has 8+ cells)
            if (cells.length >= 8) {
                const module = {
                    id: cells[0].textContent.trim(),
                    name: cells[1].textContent.trim(),
                    participant: cells[2].textContent.trim(),
                    days: cells[3].textContent.trim(),
                    due: cells[4].textContent.trim(),
                    status: cells[5].textContent.trim(),
                    date: cells[6].textContent.trim(),
                    topic: currentTopic,
                    isSubtopic: false
                };
                ordered.push(module);
                currentModule = module;

                // Now look for subtopics in the collapsed section
                const collapseId = row.querySelector('button[data-target]')?.getAttribute('data-target')?.replace('#', '');
                if (collapseId) {
                    const collapseRow = tempDiv.querySelector(`tr.collapse#${collapseId}`);
                    if (collapseRow) {
                        const subtopicRows = collapseRow.querySelectorAll('table.table-sm tbody tr');
                        subtopicRows.forEach(subRow => {
                            const subCells = subRow.querySelectorAll('td');
                            if (subCells.length >= 5) {
                                const subtopic = {
                                    id: subCells[0].textContent.trim(),
                                    name: subCells[1].textContent.trim(),
                                    participant: subCells[2].textContent.trim(),
                                    days: '',
                                    due: '',
                                    status: subCells[3].textContent.trim(),
                                    date: subCells[4].textContent.trim(),
                                    topic: currentTopic,
                                    isSubtopic: true
                                };
                                ordered.push(subtopic);
                            }
                        });
                    }
                }
            }
        });

        data.modules = ordered;
        return data;
    }

    function exportProductTrainingStatus(format) {
        // Get data from localStorage
        const storedData = localStorage.getItem('productTrainingData');
        if (!storedData) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No training data available for export. Please refresh the modal.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        const trainingData = JSON.parse(storedData);
        const companyName = trainingData.companyName || 'Company';
        const fileName = `Product_Training_Status_${companyName.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().split('T')[0]}`;

        if (format === 'excel') {
            exportToExcel(trainingData, fileName);
        } else if (format === 'pdf') {
            exportToPDF(trainingData, fileName);
        }
    }

    function exportToExcel(trainingData, fileName) {
        // Create Excel content from localStorage data
        let excelContent = '<table border="1" style="border-collapse: collapse; width: 100%;">';
        excelContent += '<tr><th colspan="7" style="background-color: #4CAF50; color: white; font-weight: bold; text-align: center; padding: 12px; font-size: 14px;">Product Training Status - ' + trainingData.companyName + '</th></tr>';
        excelContent += '<tr><th colspan="7" style="background-color: #f0f0f0; text-align: center; padding: 8px; font-size: 12px;">Generated on: ' + new Date().toLocaleString() + '</th></tr>';
        excelContent += '<tr></tr>';
        // Headers
        excelContent += '<tr style="background-color: #e0e0e0; font-weight: bold;">';
        excelContent += '<th style="padding: 8px; border: 1px solid #ccc;">Id</th>';
        excelContent += '<th style="padding: 8px; border: 1px solid #ccc;">Module / Subtopic</th>';
        excelContent += '<th style="padding: 8px; border: 1px solid #ccc;">Participant</th>';
        excelContent += '<th style="padding: 8px; border: 1px solid #ccc;">Days</th>';
        excelContent += '<th style="padding: 8px; border: 1px solid #ccc;">Due</th>';
        excelContent += '<th style="padding: 8px; border: 1px solid #ccc;">Status</th>';
        excelContent += '<th style="padding: 8px; border: 1px solid #ccc;">Date</th>';
        excelContent += '</tr>';

        // Rows grouped by topic
        let lastTopic = '';
        (trainingData.exportData.modules || []).forEach(m => {
            if (m.topic && m.topic !== lastTopic) {
                excelContent += '<tr><th colspan="7" style="text-align:left;background:#e8f4f8;padding: 10px;border: 1px solid #ccc;font-size: 13px;font-weight: bold;">Topic: ' + m.topic.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</th></tr>';
                lastTopic = m.topic;
            }

            // Determine row styling based on whether it's a subtopic
            let rowStyle = '';
            let cellStyle = 'padding: 6px; border: 1px solid #ccc;';
            let moduleNameStyle = cellStyle;

            if (m.isSubtopic) {
                rowStyle = 'background-color: #f9f9f9;';
                moduleNameStyle += 'padding-left: 20px; font-style: italic; color: #666;';
            } else {
                rowStyle = 'background-color: #ffffff;';
                moduleNameStyle += 'font-weight: bold;';
            }

            excelContent += '<tr style="' + rowStyle + '">';
            excelContent += '<td style="' + cellStyle + '">' + (m.id || '') + '</td>';
            excelContent += '<td style="' + moduleNameStyle + '">' + (m.isSubtopic ? '  └─ ' : '') + (m.name || '') + '</td>';
            excelContent += '<td style="' + cellStyle + '">' + (m.participant || '') + '</td>';
            excelContent += '<td style="' + cellStyle + '">' + (m.isSubtopic ? '' : (m.days || '')) + '</td>';
            excelContent += '<td style="' + cellStyle + '">' + (m.isSubtopic ? '' : (m.due || '')) + '</td>';

            // Status styling based on status value
            let statusStyle = cellStyle;
            let statusText = m.status || '';
            if (statusText.toLowerCase() === 'completed') {
                statusStyle += 'color: #28a745; font-weight: bold;';
            } else if (statusText.toLowerCase() === 'pending') {
                statusStyle += 'color: #ffc107; font-weight: bold;';
            } else if (statusText.toLowerCase() === 'not applicable') {
                statusStyle += 'color: #6c757d; font-style: italic;';
            } else if (statusText.toLowerCase() === 'later on') {
                statusStyle += 'color: #6f42c1; font-weight: bold;';
            }

            excelContent += '<td style="' + statusStyle + '">' + statusText + '</td>';
            excelContent += '<td style="' + cellStyle + '">' + (m.date || '') + '</td>';
            excelContent += '</tr>';
        });

        excelContent += '</table>';

        // Create and download Excel file
        const blob = new Blob([excelContent], {
            type: 'application/vnd.ms-excel'
        });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    function exportToPDF(trainingData, fileName) {
        // Create PDF content from localStorage data
        let pdfContent = '<!DOCTYPE html><html><head><meta charset="utf-8">';
        pdfContent += '<title>Product Training Status - ' + trainingData.companyName + '</title>';
        pdfContent += '<style>';
        pdfContent += 'body { font-family: Arial, sans-serif; margin: 20px; }';
        pdfContent += '.header { text-align: center; margin-bottom: 20px; }';
        pdfContent += '.company-name { font-size: 18px; font-weight: bold; color: #333; }';
        pdfContent += '.generated-date { font-size: 12px; color: #666; margin-top: 5px; }';
        pdfContent += 'table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
        pdfContent += 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
        pdfContent += 'th { background-color: #f2f2f2; font-weight: bold; }';
        pdfContent += '.topic-header { background-color: #e8f4f8; font-weight: bold; }';
        pdfContent += '.subtopic-row { background-color: #f9f9f9; }';
        pdfContent += '.subtopic-name { padding-left: 20px; font-style: italic; color: #666; }';
        pdfContent += '.module-name { font-weight: bold; }';
        pdfContent += '.overdue { color: #d32f2f; font-weight: bold; }';
        pdfContent += '.completed { color: #28a745; font-weight: bold; }';
        pdfContent += '.pending { color: #ffc107; font-weight: bold; }';
        pdfContent += '.not-applicable { color: #6c757d; font-style: italic; }';
        pdfContent += '.later-on { color: #6f42c1; font-weight: bold; }';
        pdfContent += '@media print { body { margin: 0; } .no-print { display: none; } }';
        pdfContent += '</style></head><body>';

        pdfContent += '<div class="header">';
        pdfContent += '<div class="company-name">Product Training Status - ' + trainingData.companyName + '</div>';
        pdfContent += '<div class="generated-date">Generated on: ' + new Date().toLocaleString() + '</div>';
        pdfContent += '</div>';

        pdfContent += '<table><thead><tr>';
        pdfContent += '<th>Id</th><th>Module / Subtopic</th><th>Participant</th><th>Days</th><th>Due</th><th>Status</th><th>Date</th>';
        pdfContent += '</tr></thead><tbody>';

        let lastTopic = '';
        (trainingData.exportData.modules || []).forEach(m => {
            if (m.topic && m.topic !== lastTopic) {
                pdfContent += '<tr class="topic-header"><td colspan="7">Topic: ' + m.topic.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</td></tr>';
                lastTopic = m.topic;
            }

            let statusClass = '';
            let statusText = m.status || '';
            if (statusText.toLowerCase() === 'completed') statusClass = 'completed';
            else if (statusText.toLowerCase() === 'pending') statusClass = 'pending';
            else if (statusText.toLowerCase() === 'not applicable') statusClass = 'not-applicable';
            else if (statusText.toLowerCase() === 'later on') statusClass = 'later-on';

            let rowClass = m.isSubtopic ? 'subtopic-row' : '';
            let nameClass = m.isSubtopic ? 'subtopic-name' : 'module-name';
            let namePrefix = m.isSubtopic ? '  └─ ' : '';

            pdfContent += '<tr class="' + rowClass + '">';
            pdfContent += '<td>' + (m.id || '') + '</td>';
            pdfContent += '<td class="' + nameClass + '">' + namePrefix + (m.name || '') + '</td>';
            pdfContent += '<td>' + (m.participant || '') + '</td>';
            pdfContent += '<td>' + (m.isSubtopic ? '' : (m.days || '')) + '</td>';
            pdfContent += '<td>' + (m.isSubtopic ? '' : (m.due || '')) + '</td>';
            pdfContent += '<td class="' + statusClass + '">' + statusText + '</td>';
            pdfContent += '<td>' + (m.date || '') + '</td>';
            pdfContent += '</tr>';
        });

        pdfContent += '</tbody></table>';
        pdfContent += '<script>';
        pdfContent += 'window.onload = function() {';
        pdfContent += 'window.print();';
        pdfContent += 'setTimeout(function() {';
        pdfContent += 'window.close();';
        pdfContent += 'if (window.opener) { window.opener.focus(); }';
        pdfContent += '}, 2000);';
        pdfContent += '};';
        pdfContent += '<' + '/script>';
        pdfContent += '</body></html>';

        // Open PDF in new window
        const newWindow = window.open('', '_blank');
        newWindow.document.write(pdfContent);
        newWindow.document.close();
    }

    // Clean up localStorage when modal is closed
    $(document).on('hidden.bs.modal', '#ProductTrainingStatusModal', function() {
        localStorage.removeItem('productTrainingData');
    });

    function exportSetupStatus() {
        const stored = localStorage.getItem('setupStatusData');
        const company = window.currentSetupCompanyName || '';
        const title = 'Setup Status - ' + (company || 'Company');
        const generated = new Date().toLocaleString();

        // Build table content from current DOM if available, else from storage
        let bodyHtml = document.getElementById('setupStatus') ? document.getElementById('setupStatus').innerHTML : '';
        if (!bodyHtml && stored) {
            try {
                const obj = JSON.parse(stored);
                bodyHtml = obj.htmlContent || '';
            } catch (e) {}
        }

        if (!bodyHtml) {
            Swal && Swal.fire ? Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No setup status available to export.'
            }) : alert('No setup status available to export.');
            return;
        }

        let excelContent = '<table border="1">';
        excelContent += '<tr><th colspan="7" style="background-color:#4CAF50;color:#fff;font-weight:bold;text-align:center;padding:10px;">' + title + '</th></tr>';
        excelContent += '<tr><th colspan="7" style="background-color:#f0f0f0;text-align:center;padding:5px;">Generated on: ' + generated + '</th></tr>';
        excelContent += '<tr></tr>';
        // Wrap the tbody rows into a table for Excel
        excelContent += bodyHtml;
        excelContent += '</table>';

        const blob = new Blob([excelContent], {
            type: 'application/vnd.ms-excel'
        });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        const safeName = (company || 'Company').replace(/[^a-zA-Z0-9]/g, '_');
        a.download = 'Setup_Status_' + safeName + '_' + new Date().toISOString().split('T')[0] + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    function exportSetupStatusPDF() {
        const stored = localStorage.getItem('setupStatusData');
        const company = window.currentSetupCompanyName || '';
        const title = 'Setup Status - ' + (company || 'Company');
        const generated = new Date().toLocaleString();

        let bodyHtml = '';
        const container = document.getElementById('setupStatus');
        if (container && container.innerHTML.trim() !== '') {
            bodyHtml = container.innerHTML;
        } else if (stored) {
            try {
                bodyHtml = JSON.parse(stored).htmlContent || '';
            } catch (e) {}
        }
        if (!bodyHtml) {
            Swal && Swal.fire ? Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No setup status available to export.'
            }) : alert('No setup status available to export.');
            return;
        }

        let pdfContent = '<!DOCTYPE html><html><head><meta charset="utf-8">';
        pdfContent += '<title>' + title + '</title>';
        pdfContent += '<style>body{font-family:Arial,sans-serif;margin:20px;} .header{text-align:center;margin-bottom:20px;}';
        pdfContent += '.company-name{font-size:18px;font-weight:bold;color:#333;} .generated-date{font-size:12px;color:#666;margin-top:5px;}';
        pdfContent += 'table{width:100%;border-collapse:collapse;margin-top:20px;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#f2f2f2;font-weight:bold;}';
        pdfContent += '.badge{display:inline-block;padding:2px 6px;border-radius:4px;background:#e0e0e0;font-size:12px;} .text-muted{color:#777;}';
        pdfContent += '@media print{body{margin:0;} .no-print{display:none;}}';
        pdfContent += '</style></head><body>';
        pdfContent += '<div class="header"><div class="company-name">' + title + '</div><div class="generated-date">Generated on: ' + generated + '</div></div>';
        pdfContent += '<table><tbody>' + bodyHtml + '</tbody></table>';
        pdfContent += '<script>window.onload=function(){window.print();setTimeout(function(){window.close();if(window.opener){window.opener.focus();}},800);}<' + '/script>';
        pdfContent += '</body></html>';

        const w = window.open('', '_blank');
        w.document.write(pdfContent);
        w.document.close();
    }

    // Clean up localStorage when modal closed
    $(document).on('hidden.bs.modal', '#SetupTrainingStatusModal', function() {
        localStorage.removeItem('setupStatusData');
    });

    // WhatsApp Group Join Link
    function openWhatsappGroupLinkModal(societyId, companyName) {
        var currentLink = ($('#wa_group_link_value_' + societyId).val() || '').trim();
        $('#whatsapp_group_link_society_id').val(societyId);
        $('#whatsapp_group_link_input').val(currentLink);
        $('#whatsappGroupLinkCompanyName').text(companyName ? ('Company: ' + companyName) : '');
        $('#whatsappGroupLinkModal').modal('show');
        setTimeout(function() {
            $('#whatsapp_group_link_input').focus().select();
        }, 300);
    }

    function copyTextToClipboard(text, successMessage) {
        if (!text) {
            showToast('No join link to copy', 'error');
            return;
        }
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function() {
                showToast(successMessage || 'Join link copied!', 'success');
            }).catch(function() {
                fallbackCopyText(text, successMessage);
            });
        } else {
            fallbackCopyText(text, successMessage);
        }
    }

    function fallbackCopyText(text, successMessage) {
        var tempInput = document.createElement('textarea');
        tempInput.value = text;
        tempInput.style.position = 'fixed';
        tempInput.style.left = '-9999px';
        document.body.appendChild(tempInput);
        tempInput.select();
        try {
            document.execCommand('copy');
            showToast(successMessage || 'Join link copied!', 'success');
        } catch (e) {
            showToast('Failed to copy link. Please try again.', 'error');
        }
        document.body.removeChild(tempInput);
    }

    function copyWhatsappGroupLink(societyId) {
        var link = ($('#wa_group_link_value_' + societyId).val() || '').trim();
        copyTextToClipboard(link, 'WhatsApp join link copied!');
    }

    function copyWhatsappGroupLinkFromModal() {
        var link = ($('#whatsapp_group_link_input').val() || '').trim();
        copyTextToClipboard(link, 'WhatsApp join link copied!');
    }

    function saveWhatsappGroupLink() {
        var societyId = $('#whatsapp_group_link_society_id').val();
        var link = ($('#whatsapp_group_link_input').val() || '').trim();
        if (!societyId) {
            showToast('Invalid company selected', 'error');
            return;
        }
        if (link !== '' && !/^https?:\/\/.+/i.test(link)) {
            showToast('Please enter a valid URL (http/https)', 'error');
            return;
        }
        $.ajax({
            url: 'controller/createSocietyAutoController.php',
            type: 'POST',
            data: {
                updateWhatsappGroupLink: 'updateWhatsappGroupLink',
                society_id: societyId,
                whatsapp_group_link: link,
                csrf: (typeof csrf !== 'undefined') ? csrf : ''
            },
            success: function(resp) {
                if ((resp + '').trim() == '1') {
                    var display = link !== ''
                        ? (link.length > 28 ? link.substring(0, 28) + '...' : link)
                        : 'Add join link';
                    $('#wa_group_link_display_' + societyId)
                        .text(display)
                        .attr('title', link);
                    $('#wa_group_link_value_' + societyId).val(link);
                    if (link !== '') {
                        $('#wa_group_link_copy_' + societyId).show();
                    } else {
                        $('#wa_group_link_copy_' + societyId).hide();
                    }
                    $('#whatsappGroupLinkModal').modal('hide');
                    showToast('WhatsApp join link saved!', 'success');
                } else {
                    showToast('Failed to save join link', 'error');
                }
            },
            error: function() {
                showToast('Error while saving join link', 'error');
            }
        });
    }

    // Implementation Name Update Functionality
    function openImplementationEditModal(societyId, currentImplementationName) {
        $('#edit_society_id').val(societyId);
        $("#implementation_person_select").val(currentImplementationName).trigger('change');
        $('#implementationEditModal').modal('show');
    }

    function updateImplementationPerson() {
        var societyId = $('#edit_society_id').val();
        var newValue = $('#implementation_person_select').val();
        var statusElement = $('#implementation_update_status_' + societyId);
        var displayElement = $('#implementation_display_' + societyId);

        if (!newValue || !societyId) {
            alert('Please select an implementation person');
            return;
        }

        $.ajax({
            url: 'controller/reportController.php',
            type: 'POST',
            data: {
                society_id: societyId,
                implementation_name: newValue,
                changeImplementationName: 'changeImplementationName',
                csrf: (typeof csrf !== 'undefined') ? csrf : ''
            },
            success: function(resp) {
                if ((resp + '').trim() == '1') {
                    displayElement.text(newValue);
                    statusElement.removeClass('d-none');
                    setTimeout(function() {
                        statusElement.addClass('d-none');
                    }, 2000);
                    $('#implementationEditModal').modal('hide');
                } else {
                    // alert('Failed to update Implementation Name');
                }
            },
            error: function() {
                // alert('Error while updating Implementation Name');
            }
        });
    }

    // Function to show toast notification
    function showToast(message, type = 'success') {
        // Remove existing toast if any
        const existingToast = document.getElementById('copy-url-toast');
        if (existingToast) {
            existingToast.remove();
        }
        
        // Create toast element
        const toast = document.createElement('div');
        toast.id = 'copy-url-toast';
        toast.className = 'copy-url-toast copy-url-toast-' + type;
        toast.innerHTML = '<i class="fa fa-check-circle"></i> ' + message;
        
        // Add to body
        document.body.appendChild(toast);
        
        // Show toast with animation
        setTimeout(function() {
            toast.classList.add('show');
        }, 10);
        
        // Hide and remove toast after 3 seconds
        setTimeout(function() {
            toast.classList.remove('show');
            setTimeout(function() {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
    
    // Function to copy training form URL to clipboard
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
    
    // Fallback copy function using execCommand
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
    
    // Function to view training form details
    function viewFormDetails(formId) {
        $('#viewFormDetailsModal').modal('show');
        $('#formDetailsContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2">Loading form details...</p></div>');
        
        $.ajax({
            url: 'ajax/getTrainingFormDetails.php',
            type: 'POST',
            data: { form_id: formId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displayFormDetails(response.data);
                } else {
                    $('#formDetailsContent').html('<div class="alert alert-danger">' + (response.message || 'Error loading form details') + '</div>');
                }
            },
            error: function() {
                $('#formDetailsContent').html('<div class="alert alert-danger">Error loading form details. Please try again.</div>');
            }
        });
    }
    
    // Function to display form details in modal
    function displayFormDetails(data) {
        var html = '<div class="container-fluid">';
        
        // Header Section
        html += '<div class="row mb-3"><div class="col-12"><h5 class="text-primary"><strong>Training & Implementation Completion Form</strong></h5></div></div>';
        
        // Section 1: CHL Representative Details
        html += '<div class="card mb-3"><div class="card-header bg-light"><h6 class="mb-0"><strong>Section 1: CHL Representative Details</strong></h6></div><div class="card-body">';
        html += '<div class="row"><div class="col-md-6"><strong>Employee Name:</strong> ' + (data.employee_name || '-') + '</div>';
        html += '<div class="col-md-6"><strong>Designation:</strong> ' + (data.employee_designation || '-') + '</div></div></div></div>';
        
        // Section 2: Client Details
        html += '<div class="card mb-3"><div class="card-header bg-light"><h6 class="mb-0"><strong>Section 2: Client Details</strong></h6></div><div class="card-body">';
        html += '<div class="row"><div class="col-md-6"><strong>Company Name:</strong> ' + (data.company_name || '-') + '</div>';
        html += '<div class="col-md-6"><strong>Client Name:</strong> ' + (data.client_name || '-') + '</div></div>';
        html += '<div class="row mt-2"><div class="col-md-6"><strong>Client Designation:</strong> ' + (data.client_designation || '-') + '</div>';
        html += '<div class="col-md-6"><strong>Client Mobile:</strong> ' + (data.client_country_code || '') + ' ' + (data.client_mobile || '-') + '</div></div>';
        html += '<div class="row mt-2"><div class="col-md-6"><strong>Client Email:</strong> ' + (data.client_email || '-') + '</div></div></div></div>';
        
        // Section 3: Participants
        if (data.participants_data && data.participants_data.length > 0) {
            html += '<div class="card mb-3"><div class="card-header bg-light"><h6 class="mb-0"><strong>Section 3: Participants</strong></h6></div><div class="card-body">';
            html += '<table class="table table-bordered table-sm"><thead><tr><th>Participant</th><th>Status</th></tr></thead><tbody>';
            data.participants_data.forEach(function(participant) {
                var statusBadge = '';
                if (participant.participant_value === 'Yes') {
                    statusBadge = '<span class="badge badge-success">Yes</span>';
                } else if (participant.participant_value === 'No') {
                    statusBadge = '<span class="badge badge-danger">No</span>';
                } else {
                    statusBadge = '<span class="badge badge-secondary">NA</span>';
                }
                html += '<tr><td>' + (participant.participant_name || '-') + '</td><td>' + statusBadge + '</td></tr>';
            });
            html += '</tbody></table></div></div>';
        }
        
        // Section 4: Modules Covered
        if (data.modules_data && data.modules_data.length > 0) {
            html += '<div class="card mb-3"><div class="card-header bg-light"><h6 class="mb-0"><strong>Section 4: Modules Covered</strong></h6></div><div class="card-body">';
            html += '<table class="table table-bordered table-sm"><thead><tr><th>Module Name</th><th>Status</th></tr></thead><tbody>';
            data.modules_data.forEach(function(module) {
                var statusBadge = '';
                if (module.module_value === 'Completed') {
                    statusBadge = '<span class="badge badge-success">Completed</span>';
                } else if (module.module_value === 'Not Applicable') {
                    statusBadge = '<span class="badge badge-warning">Not Applicable</span>';
                } else if (module.module_value === 'Pending') {
                    statusBadge = '<span class="badge badge-secondary">Pending</span>';
                } else {
                    statusBadge = '<span class="badge badge-light">-</span>';
                }
                html += '<tr><td>' + (module.module_name || '-') + '</td><td>' + statusBadge + '</td></tr>';
            });
            html += '</tbody></table>';
            html += '<div class="mt-3"><strong>Summary:</strong> ';
            html += '<span class="badge badge-success">Completed: ' + (data.completed_modules_count || 0) + '</span> ';
            html += '<span class="badge badge-warning">Not Applicable: ' + (data.na_modules_count || 0) + '</span> ';
            html += '<span class="badge badge-secondary">Pending: ' + (data.pending_modules_count || 0) + '</span> ';
            html += '<span class="badge badge-info">Total: ' + (data.total_modules_count || 0) + '</span>';
            html += '</div></div></div>';
        }

        // Trainers Feedback Section
        if (data.trainer_feedback && (data.trainer_feedback.product_knowledge || data.trainer_feedback.communication || data.trainer_feedback.attire_behavior || data.trainer_feedback.training_capabilities)) {
        html += '<div class="card mb-3"><div class="card-header bg-light"><h6 class="mb-0"><strong>Trainers Feedback</strong></h6></div><div class="card-body">';
        html += '<div class="row">';
        
        function renderStars(rating) {
            if (!rating || rating < 1 || rating > 5) return '<span class="text-muted">-</span>';
            var starsHtml = '';
            for (var i = 1; i <= 5; i++) {
            if (i <= rating) {
                starsHtml += '<i class="fa fa-star text-warning"></i>';
            } else {
                starsHtml += '<i class="fa fa-star-o text-muted"></i>';
            }
            }
            return starsHtml + ' <span class="ml-2">(' + rating + '/5)</span>';
        }
        
        html += '<div class="col-md-6 mb-3"><strong>Product Knowledge:</strong><br>' + renderStars(data.trainer_feedback.product_knowledge) + '</div>';
        html += '<div class="col-md-6 mb-3"><strong>Communication:</strong><br>' + renderStars(data.trainer_feedback.communication) + '</div>';
        html += '<div class="col-md-6 mb-3"><strong>Attire & Behavior:</strong><br>' + renderStars(data.trainer_feedback.attire_behavior) + '</div>';
        html += '<div class="col-md-6 mb-3"><strong>Training Capabilities:</strong><br>' + renderStars(data.trainer_feedback.training_capabilities) + '</div>';
        if (data.trainer_feedback.feedback_remark) {
            html += '<div class="col-12"><strong>Feedback Remark:</strong> ' + data.trainer_feedback.feedback_remark + '</div>';
        }
        
        html += '</div></div></div>';
        }
        
        // Section 5: Declaration
        html += '<div class="card mb-3"><div class="card-header bg-light"><h6 class="mb-0"><strong>Section 5: Declaration</strong></h6></div><div class="card-body">';
        html += '<p><strong>Declaration Agreed:</strong> ' + (data.declaration_agreed == 1 ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>') + '</p>';
        html += '<p><strong>OTP Verified:</strong> ' + (data.otp_verified == 1 ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>') + '</p></div></div>';
        
        // Submission Info
        html += '<div class="card mb-3"><div class="card-header bg-light"><h6 class="mb-0"><strong>Submission Information</strong></h6></div><div class="card-body">';
        html += '<div class="row"><div class="col-md-6"><strong>Submitted Date:</strong> ' + (data.submitted_date_formatted || '-') + '</div>';
        html += '<div class="col-md-6"><strong>Created Date:</strong> ' + (data.created_date_formatted || '-') + '</div></div></div></div>';
        
        html += '</div>';
        $('#formDetailsContent').html(html);
    }
</script>

<!-- View Form Details Modal -->
<div class="modal fade" id="viewFormDetailsModal" tabindex="-1" aria-labelledby="viewFormDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="viewFormDetailsModalLabel">Training Form Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="formDetailsContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading form details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>