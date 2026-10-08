<?php
extract($_POST);
error_reporting(0);
$sId = $d->sanitizeActionIds(isset($_GET['sId']) ? (array) $_GET['sId'] : []);
$dId = $d->sanitizeActionIds(isset($_GET['dId']) ? (array) $_GET['dId'] : []);
// Reset domain if no server is selected
if (empty($sId)) {
  $dId = [];
}
$getDataType = (isset($_GET['getDataType']) && $_GET['getDataType'] != '0') ? $_GET['getDataType'] : '3';
$includeExpired = isset($_GET['includeExpired']) ? (int)$_GET['includeExpired'] : 0;

$currentRoleId = (int) ($role_id ?? 0);
$currentAdminId = (int) ($bms_admin_id ?? 0);
$companyActions = array();
$actionsQ = $d->select("company_action_master", "", "ORDER BY action_id ASC");
if ($actionsQ) {
  while ($actionRow = mysqli_fetch_assoc($actionsQ)) {
    $accessRoles = trim((string) ($actionRow['access_by_role'] ?? ''));
    $accessUsers = trim((string) ($actionRow['access_by_user'] ?? ''));
    $hasAccess = false;
    if ($accessRoles === '' && $accessUsers === '') {
      $hasAccess = true;
    } else {
      $roleOk = false;
      $userOk = false;
      if ($accessRoles !== '') {
        $roleIds = array_filter(array_map('intval', explode(',', $accessRoles)));
        $roleOk = in_array($currentRoleId, $roleIds, true);
      }
      if ($accessUsers !== '') {
        $userIds = array_filter(array_map('intval', explode(',', $accessUsers)));
        $userOk = in_array($currentAdminId, $userIds, true);
      }
      if ($accessRoles !== '' && $accessUsers !== '') {
        $hasAccess = $roleOk || $userOk;
      } elseif ($accessRoles !== '') {
        $hasAccess = $roleOk;
      } else {
        $hasAccess = $userOk;
      }
    }
    if ($hasAccess) {
      $companyActions[] = $actionRow;
    }
  }
}
$allowedActionValues = array_map(function ($row) {
  return (string) $row['value'];
}, $companyActions);
if (!in_array((string) $getDataType, $allowedActionValues, true)) {
  $getDataType = !empty($allowedActionValues) ? $allowedActionValues[0] : '0';
}
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-12">
        <h4 class="page-title mb-3">Company Analytics Data</h4>
        <form action="" method="get" accept-charset="utf-8">
          <input type="hidden" name="getDataType" value="<?php echo isset($_GET['getDataType']) ? htmlspecialchars($_GET['getDataType']) : '3'; ?>">
          <div class="row mx-0">
            <div class="col-12 col-md-6 col-lg-3 mb-2">
              <select id="sId" class="form-control multiple-select" name="sId[]" multiple>
                <?php
                $qc = $d->select("server_master", "");
                while ($cData = mysqli_fetch_array($qc)) {
                  $selected = in_array((int)$cData['server_id'], $sId) ? "selected" : "";
                ?>
                  <option <?php echo $selected; ?> value="<?php echo $cData['server_id']; ?>"><?php echo $cData['server_name']; ?> (<?php echo $cData['server_ip']; ?>)</option>
                <?php } ?>
              </select>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-2">
              <select id="dId" name="dId[]" class="form-control multiple-select" multiple>
                <?php
                if (!empty($sId)) {
                  $serverIds = implode("','", array_map('intval', $sId));
                  $domain_data = $d->select("domain_master", "server_id IN ('$serverIds')");
                  while ($dData = mysqli_fetch_array($domain_data)) {
                    $selected = in_array((int)$dData['domain_id'], $dId) ? "selected" : "";
                    echo "<option value='{$dData['domain_id']}' {$selected}>{$dData['domain_name']}</option>";
                  }
                }
                ?>
              </select>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-2">
              <select id="includeExpired" name="includeExpired" class="form-control single-select">
                <option value="0" <?php if (!isset($_GET['includeExpired']) || (isset($_GET['includeExpired']) && $_GET['includeExpired'] == '0')) echo "selected"; ?>>No (Active Only)</option>
                <option value="1" <?php if (isset($_GET['includeExpired']) && $_GET['includeExpired'] == '1') echo "selected"; ?>>Yes (Include Expired)</option>
              </select>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-2 d-flex align-items-start">
              <button type="submit" class="btn btn-primary btn-sm">Apply filter</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <?php
            $today = date("Y-m-d");
            $appendSeverQuery = "";
            $appendDomainQuery = "";
            $appendExpired = "";

            if (!empty($sId)) {
              $serverIds = implode("','", array_map('intval', $sId));
              $appendSeverQuery = " AND domain_master.server_id IN ('$serverIds')";
            }
            if (!empty($dId)) {
              $domainIds = implode("','", array_map('intval', $dId));
              $appendDomainQuery = " AND domain_master.domain_id IN ('$domainIds')";
            }
            if (isset($includeExpired) && $includeExpired == 0) {
              $appendExpired = " AND society_master.plan_expire_date >= CURDATE()";
            }
            ?>
            <div class="row mx-0">
              <div class="col-12 col-lg-6">
                <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-3">
                  <div class="mb-2 mb-sm-0 mr-sm-3"><b>Not Sync Company</b></div>
                  <form method="get" class="flex-fill mb-0 w-100">
                    <?php
                    if (!empty($sId)) {
                      foreach ($sId as $sid) {
                        echo '<input type="hidden" name="sId[]" value="' . $sid . '">';
                      }
                    }
                    if (!empty($dId)) {
                      foreach ($dId as $did) {
                        echo '<input type="hidden" name="dId[]" value="' . $did . '">';
                      }
                    }
                    ?>
                    <?php if (isset($_GET['includeExpired'])) { ?>
                      <input type="hidden" name="includeExpired" value="<?php echo htmlspecialchars($_GET['includeExpired']); ?>">
                    <?php } ?>
                    <select id="getDataType"
                      name="getDataType"
                      class="form-control single-select"
                      required
                      onchange="this.form.submit()">

                      <option value="0">-- Select --</option>
                      <?php foreach ($companyActions as $actionRow) {
                        $optValue = (string) $actionRow['value'];
                        $optLabel = (string) $actionRow['action'];
                        $selected = ((string) ($getDataType ?? '') === $optValue) ? 'selected' : '';
                        ?>
                        <option value="<?php echo htmlspecialchars($optValue); ?>" <?php echo $selected; ?>>
                          <?php echo htmlspecialchars($optLabel); ?>
                        </option>
                      <?php } ?>
                    </select>
                  </form>
                </div>
              </div>
            </div>

                <form id="publishSocetyDataFrm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="countryId" id="countryId" value="<?php echo $country_id; ?>">
                  <input type="hidden" name="sId" id="sId" value="<?php echo $state_id; ?>">
                  <input type="hidden" name="cId" id="cId" value="<?php echo $city_id; ?>">
                  <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" />
                  <input type="hidden" id="getDataType" name="getDataType" value="<?php echo $getDataType ?? '3'; ?>" required>
                <?php
                $isAiBulk = in_array(($getDataType ?? ''), array('25', '26'), true);
                $listColClass = $isAiBulk ? 'col-12' : 'col-12 col-lg-6';
                ?>
                <div class="row mx-0">
                <div class="<?= $listColClass ?>">
                <?php
                $webAiCredentials = array();
                $webAiCredMap = array();
                if (($getDataType ?? '') == '25') {
                  $webCredQ = $d->select('web_ai_credentials_master', "ai_status='1'", 'ORDER BY credential_type ASC, web_ai_credentials_id ASC');
                  if ($webCredQ && mysqli_num_rows($webCredQ) > 0) {
                    while ($webCredRow = mysqli_fetch_assoc($webCredQ)) {
                      $webAiCredentials[] = $webCredRow;
                    }
                  }
                  $webCredAllQ = $d->selectRow('web_ai_credentials_id, credential_name, ai_base_url, credential_type', 'web_ai_credentials_master', '1=1');
                  if ($webCredAllQ && mysqli_num_rows($webCredAllQ) > 0) {
                    while ($webCredAllRow = mysqli_fetch_assoc($webCredAllQ)) {
                      $webAiCredMap[(int) $webCredAllRow['web_ai_credentials_id']] = $webCredAllRow;
                    }
                  }
                }
                $mobileAiCredentials = array();
                $mobileAiCredMap = array();
                if (($getDataType ?? '') == '26') {
                  $mobileCredQ = $d->select('ai_credentials_master', "ai_status='1'", 'ORDER BY debug_key ASC, ai_credentials_id ASC');
                  if ($mobileCredQ && mysqli_num_rows($mobileCredQ) > 0) {
                    while ($mobileCredRow = mysqli_fetch_assoc($mobileCredQ)) {
                      $mobileAiCredentials[] = $mobileCredRow;
                    }
                  }
                  $mobileCredAllQ = $d->selectRow('ai_credentials_id, ai_model, ai_base_url, debug_key', 'ai_credentials_master', '1=1');
                  if ($mobileCredAllQ && mysqli_num_rows($mobileCredAllQ) > 0) {
                    while ($mobileCredAllRow = mysqli_fetch_assoc($mobileCredAllQ)) {
                      $mobileAiCredMap[(int) $mobileCredAllRow['ai_credentials_id']] = $mobileCredAllRow;
                    }
                  }
                }
                $success_array = array();
                $failure_array = array();
                $custom_language_exist=array();
                $custom_language_companies='';
                if ($getDataType == '13') {
                  $post_log_master = $d->selectRow("DISTINCT society_id", "language_key_value_master_society", "");
                  while ($post_log_master_data = mysqli_fetch_array($post_log_master)) {
                    array_push($custom_language_exist, $post_log_master_data['society_id']);
                  }
                  $custom_language_companies = join("','", $custom_language_exist);
                  $appendLanguageCompanies = " AND society_master.society_id IN ('$custom_language_companies')";
                }
                if ($getDataType == '3') {
                  $post_log_master = $d->select("society_analytics_master", " update_date = '$today'   ");
                  while ($post_log_master_data = mysqli_fetch_array($post_log_master)) {
                    if ($post_log_master_data['status'] == '200') {
                      array_push($success_array, $post_log_master_data['society_id']);
                    } else {
                      array_push($failure_array, $post_log_master_data['society_id']);
                    }
                  }
                }
                $companySrId = 1;
                $ids = join("','", $success_array);
                $query = $d->selectRow("society_master.*,server_master.server_name,server_master.server_ip,domain_master.domain_name", "society_master,domain_master,server_master ", "society_master.domain_id=domain_master.domain_id AND server_master.server_id=domain_master.server_id  AND society_master.society_id NOT IN ('$ids') $appendLanguageCompanies $appendSeverQuery $appendDomainQuery $appendExpired", "order by domain_master.domain_name  asc");
                if (mysqli_num_rows($query) > 0) {
                ?>
                  <?php if (($getDataType ?? '') == '25') { ?>
                    <div class="row mx-0 mb-3">
                      <div class="form-group col-12 col-md-2 mb-3">
                        <label for="bulkWebAiStatus">Web AI Status</label>
                        <select name="web_ai_status" id="bulkWebAiStatus" class="form-control single-select" required>
                          <option value="0">Off</option>
                          <option value="1">On</option>
                        </select>
                      </div>
                      <div id="bulkWebAiUrlModeOffWrap" class="form-group col-12 col-md-4 mb-3">
                        <label for="bulkWebAiUrlModeOff">URL Action</label>
                        <select name="web_ai_url_mode_off" id="bulkWebAiUrlModeOff" class="form-control single-select">
                          <option value="keep">Keep existing URL</option>
                          <option value="clear">Clear URL</option>
                        </select>
                      </div>
                      <div id="bulkWebAiUrlModeOnWrap" class="form-group col-12 col-md-4 mb-3 d-none">
                        <label for="bulkWebAiUrlModeOn">URL Action</label>
                        <select name="web_ai_url_mode_on" id="bulkWebAiUrlModeOn" class="form-control single-select">
                          <option value="fill_blank">Keep existing, set if blank</option>
                          <option value="replace">Replace URL for all</option>
                        </select>
                      </div>
                      <div id="bulkWebAiCredentialWrap" class="form-group col-12 col-md-5 mb-3 d-none">
                        <label for="bulkWebAiCredentialsId">Web AI Credential</label>
                        <select name="web_ai_credentials_id" id="bulkWebAiCredentialsId" class="form-control single-select">
                          <option value="">Select credential</option>
                          <?php foreach ($webAiCredentials as $cred) { ?>
                            <option value="<?php echo (int) $cred['web_ai_credentials_id']; ?>">
                              <?php
                              echo htmlspecialchars($cred['credential_name']);
                              echo ((int) $cred['credential_type'] === 1) ? ' (Dev)' : ' (Live)';
                              echo ' — ' . htmlspecialchars($cred['ai_base_url']);
                              ?>
                            </option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                  <?php } ?>
                  <?php if (($getDataType ?? '') == '26') { ?>
                    <div class="row mx-0 mb-3">
                      <div class="form-group col-12 col-md-2 mb-3">
                        <label for="bulkMobileAiStatus">Mobile AI Status</label>
                        <select name="ai_status" id="bulkMobileAiStatus" class="form-control single-select" required>
                          <option value="0">Off</option>
                          <option value="1">On</option>
                        </select>
                      </div>
                      <div id="bulkMobileAiModeOffWrap" class="form-group col-12 col-md-4 mb-3">
                        <label for="bulkMobileAiModeOff">Credential Action</label>
                        <select name="mobile_ai_mode_off" id="bulkMobileAiModeOff" class="form-control single-select">
                          <option value="keep">Keep existing credential</option>
                          <option value="clear">Clear credential</option>
                        </select>
                      </div>
                      <div id="bulkMobileAiModeOnWrap" class="form-group col-12 col-md-4 mb-3 d-none">
                        <label for="bulkMobileAiModeOn">Credential Action</label>
                        <select name="mobile_ai_mode_on" id="bulkMobileAiModeOn" class="form-control single-select">
                          <option value="fill_blank">Keep existing, set if blank</option>
                          <option value="replace">Replace credential for all</option>
                        </select>
                      </div>
                      <div id="bulkMobileAiCredentialWrap" class="form-group col-12 col-md-5 mb-3 d-none">
                        <label for="bulkMobileAiCredentialsId">Mobile AI Credential</label>
                        <select name="ai_credentials_id" id="bulkMobileAiCredentialsId" class="form-control single-select">
                          <option value="">Select credential</option>
                          <?php foreach ($mobileAiCredentials as $cred) { ?>
                            <option value="<?php echo (int) $cred['ai_credentials_id']; ?>">
                              <?php
                              echo htmlspecialchars(($cred['ai_model'] ?? ('Credential #' . $cred['ai_credentials_id'])));
                              echo ((int) ($cred['debug_key'] ?? 0) === 1) ? ' (Debug)' : ' (Live)';
                              if (!empty($cred['ai_base_url'])) {
                                echo ' — ' . htmlspecialchars($cred['ai_base_url']);
                              }
                              ?>
                            </option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                  <?php } ?>
                  <div class="d-flex flex-wrap align-items-center mb-3 pl-1">
                    <div class="custom-control custom-checkbox mr-3 mb-2">
                      <input type="checkbox" class="custom-control-input chk_boxes" value="0" name="society_id[]" id="chkAllCompanies">
                      <label class="custom-control-label" for="chkAllCompanies">Check All</label>
                    </div>
                    <div class="custom-control custom-checkbox mr-3 mb-2">
                      <input type="checkbox" class="custom-control-input" value="0" id="chkAllFailedCompanies">
                      <label class="custom-control-label" for="chkAllFailedCompanies">Check All Failed</label>
                    </div>
                    <button type="submit" name="publishPost" value="publishPost" class="btn btn-sm btn-success publishPost mb-2"><i class="fa fa-check-square-o"></i> <?= ($getDataType ?? '') == '25' ? 'Update Web AI' : ((($getDataType ?? '') == '26') ? 'Update Mobile AI' : 'Get Data') ?></button>
                  </div>
                <?php } else { ?>
                  <br>
                  <span class="text-danger"><b>Data Sync Company </b></span>
                <?php }
                $formatAiUrlDisplay = function ($url) {
                  $url = rtrim(trim((string) $url), '/');
                  if ($url === '') {
                    return '<span class="text-muted">No URL</span>';
                  }
                  $display = $url;
                  if (strlen($display) > 45) {
                    $display = substr($display, 0, 42) . '...';
                  }
                  return '<span title="' . htmlspecialchars($url) . '">' . htmlspecialchars($display) . '</span>';
                };
                while ($society_master_data = mysqli_fetch_array($query)) {
                  $aiStatusHtml = '';
                  if (($getDataType ?? '') == '25') {
                    $isOn = ((int) ($society_master_data['web_ai_status'] ?? 0) === 1);
                    $credId = (int) ($society_master_data['web_ai_credentials_id'] ?? 0);
                    $credInfo = ($credId > 0 && isset($webAiCredMap[$credId])) ? $webAiCredMap[$credId] : null;
                    $credUrl = $credInfo ? trim((string) ($credInfo['ai_base_url'] ?? '')) : '';
                    $credName = $credInfo ? trim((string) ($credInfo['credential_name'] ?? '')) : '';
                    $statusCls = $isOn ? 'text-success' : 'text-danger';
                    $statusText = $isOn ? 'On' : 'Off';
                    $urlText = $formatAiUrlDisplay($credUrl);
                    $nameText = $credName !== '' ? ' (' . htmlspecialchars($credName) . ')' : '';
                    $aiStatusHtml = '<div class="small text-break mt-1 ' . $statusCls . '"><b>Web AI:</b> ' . $statusText
                      . ' <span class="text-secondary">| <b>URL:</b> ' . $urlText . $nameText . '</span></div>';
                  } elseif (($getDataType ?? '') == '26') {
                    $isOn = ((int) ($society_master_data['ai_status'] ?? 0) === 1);
                    $credId = (int) ($society_master_data['ai_credentials_id'] ?? 0);
                    $credInfo = ($credId > 0 && isset($mobileAiCredMap[$credId])) ? $mobileAiCredMap[$credId] : null;
                    $credUrl = $credInfo ? trim((string) ($credInfo['ai_base_url'] ?? '')) : '';
                    $credName = $credInfo ? trim((string) ($credInfo['ai_model'] ?? '')) : '';
                    if ($credInfo) {
                      $credName .= ((int) ($credInfo['debug_key'] ?? 0) === 1) ? ' (Debug)' : ' (Live)';
                    }
                    $statusCls = $isOn ? 'text-success' : 'text-danger';
                    $statusText = $isOn ? 'On' : 'Off';
                    $urlText = $formatAiUrlDisplay($credUrl);
                    $nameText = $credName !== '' ? ' (' . htmlspecialchars($credName) . ')' : '';
                    $aiStatusHtml = '<div class="small text-break mt-1 ' . $statusCls . '"><b>Mobile AI:</b> ' . $statusText
                      . ' <span class="text-secondary">| <b>URL:</b> ' . $urlText . $nameText . '</span></div>';
                  }
                ?>
                  <div class="custom-control custom-checkbox mb-2">
                    <input type="checkbox" class="custom-control-input pagePrivilege" value="<?php echo $society_master_data['society_id']; ?>" name="society_id[]" id="society_chk_<?php echo $society_master_data['society_id']; ?>">
                    <label class="custom-control-label text-break" for="society_chk_<?php echo $society_master_data['society_id']; ?>"><?php echo $companySrId++; ?>.
                      <?php echo $d->short_app_name() . '_' . $society_master_data['society_id']; ?>
                      <?php echo $society_master_data['society_name']; ?> (<?php echo $society_master_data['city_name']; ?>) <?php echo $society_master_data['server_ip']; ?>
                      <?php echo $aiStatusHtml; ?>
                      <span id="result_<?php echo $society_master_data['society_id']; ?>"></span>
                    </label>
                    <input type="hidden" id="val_<?php echo $society_master_data['society_id']; ?>" value="1" />
                  </div>
                <?php  } ?>
              </div>
              <?php if ($getDataType == '3') { ?>
                <div class="col-12 col-lg-6"> <b>Today Sync Data</b>
                  <?php
                  $queryPost = $d->selectRow("society_master.*,society_analytics_master.*,server_master.server_name,server_master.server_ip,domain_master.domain_name", "society_master,society_analytics_master,domain_master,server_master", "society_master.domain_id=domain_master.domain_id AND server_master.server_id=domain_master.server_id  AND society_analytics_master.update_date = '$today' and   society_analytics_master.status ='200' AND society_master.society_id=society_analytics_master.society_id   $appendSeverQuery $appendExpired");
                  $cnt = 1;
                  echo '(' . mysqli_num_rows($queryPost) . ')';
                  while ($society_master_data = mysqli_fetch_array($queryPost)) {
                  ?>
                    <div class="mb-1 text-break"><?php echo $cnt . '). ' . $society_master_data['society_name']; ?> (<?php echo $society_master_data['city_name']; ?>) <?php echo $society_master_data['server_ip']; ?>
                      <?php $cls = "";
                      if ($society_master_data['status'] == "200") {
                        $cls = "text-success";
                      } else {
                        $cls = "text-danger";
                      }
                      ?>
                      <span class="<?php echo $cls; ?>"> - <?php echo $society_master_data['last_updated_date']; ?></span>
                    </div>
                  <?php $cnt++;
                  } ?>
                </div>
              <?php } ?>
                </div>
                </form>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
  $(function() {
    $('.chk_boxes').click(function() {
      $('#chkAllFailedCompanies').prop('checked', false);
      $('.pagePrivilege').prop('checked', this.checked);
    });
    $('#chkAllFailedCompanies').click(function() {
      $('#chkAllCompanies').prop('checked', false);
      $('.pagePrivilege').prop('checked', false);
      if (this.checked) {
        $('.pagePrivilege.company-action-failed').prop('checked', true);
      }
    });
    // Update domain list when server selection changes
    $('#sId').on('change', function() {
      var serverIds = $(this).val();
      var $dId = $('#dId');
      if (!serverIds || serverIds.length === 0) {
        $dId.html('<option value="">-- Select Domain --</option>');
        return;
      }
      $.get('getDomainsByServers.php', {
        serverIds: serverIds.join(',')
      }, function(html) {
        $dId.html(html);
      });
    });

    function toggleBulkWebAiCredential() {
      var $status = $('#bulkWebAiStatus');
      var $credWrap = $('#bulkWebAiCredentialWrap');
      var $cred = $('#bulkWebAiCredentialsId');
      var $offWrap = $('#bulkWebAiUrlModeOffWrap');
      var $onWrap = $('#bulkWebAiUrlModeOnWrap');
      if (!$status.length) {
        return;
      }
      if ($status.val() === '1') {
        $offWrap.addClass('d-none');
        $onWrap.removeClass('d-none');
        $credWrap.removeClass('d-none');
        $cred.prop('required', true);
      } else {
        $onWrap.addClass('d-none');
        $offWrap.removeClass('d-none');
        $credWrap.addClass('d-none');
        $cred.prop('required', false).val('').trigger('change');
      }
    }
    function toggleBulkMobileAiCredential() {
      var $status = $('#bulkMobileAiStatus');
      var $credWrap = $('#bulkMobileAiCredentialWrap');
      var $cred = $('#bulkMobileAiCredentialsId');
      var $offWrap = $('#bulkMobileAiModeOffWrap');
      var $onWrap = $('#bulkMobileAiModeOnWrap');
      if (!$status.length) {
        return;
      }
      if ($status.val() === '1') {
        $offWrap.addClass('d-none');
        $onWrap.removeClass('d-none');
        $credWrap.removeClass('d-none');
        $cred.prop('required', true);
      } else {
        $onWrap.addClass('d-none');
        $offWrap.removeClass('d-none');
        $credWrap.addClass('d-none');
        $cred.prop('required', false).val('').trigger('change');
      }
    }
    $('#bulkWebAiStatus').on('change', toggleBulkWebAiCredential);
    toggleBulkWebAiCredential();
    $('#bulkMobileAiStatus').on('change', toggleBulkMobileAiCredential);
    toggleBulkMobileAiCredential();
  });
</script>
