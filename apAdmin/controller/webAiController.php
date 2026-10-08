<?php
include '../common/objectController.php';

if (!isset($_POST) || empty($_POST)) {
    $_SESSION['msg1'] = 'Invalid request.';
    header('location:../trackingLimit');
    exit();
}

extract(array_map('test_input', $_POST));

$society_id = isset($society_id) ? (int) $society_id : 0;
if ($society_id <= 0) {
    $_SESSION['msg1'] = 'Invalid company.';
    header('location:../trackingLimit');
    exit();
}

$societyQ = $d->selectRow(
    'society_id, society_name, city_name, sub_domain, ai_status, ai_credentials_id, web_ai_status, web_ai_credentials_id',
    'society_master',
    "society_id='$society_id'"
);
if (!$societyQ || mysqli_num_rows($societyQ) === 0) {
    $_SESSION['msg1'] = 'Company not found.';
    header('location:../trackingLimit');
    exit();
}
$society = mysqli_fetch_assoc($societyQ);
$society_base_url = $society['sub_domain'];
$company_label = trim(($society['society_name'] ?? '') . '-' . ($society['city_name'] ?? ''), '-');

function mobile_ai_resolve_credential($d, $credentialsId)
{
    $credentialsId = (int) $credentialsId;
    if ($credentialsId <= 0) {
        return null;
    }
    $q = $d->selectRow(
        'ai_credentials_id, ai_base_url, ai_model, debug_key, ai_status',
        'ai_credentials_master',
        "ai_credentials_id='$credentialsId' AND ai_status='1'"
    );
    if (!$q || mysqli_num_rows($q) === 0) {
        return null;
    }
    return mysqli_fetch_assoc($q);
}

function web_ai_resolve_credential($d, $credentialsId)
{
    $credentialsId = (int) $credentialsId;
    if ($credentialsId <= 0) {
        return null;
    }
    $q = $d->selectRow(
        'web_ai_credentials_id, credential_name, ai_base_url, credential_type, ai_status',
        'web_ai_credentials_master',
        "web_ai_credentials_id='$credentialsId' AND ai_status='1'"
    );
    if (!$q || mysqli_num_rows($q) === 0) {
        return null;
    }
    return mysqli_fetch_assoc($q);
}

function web_ai_sync_to_company($d, $societyBaseUrl, $societyId, $webAiStatus, $baseUrl, $urlMode = '')
{
    $post = array(
        'society_id' => (int) $societyId,
        'updateWebAiSetting' => 'updateWebAiSetting',
        'web_ai_status' => (int) $webAiStatus,
        'web_ai_base_url' => (string) $baseUrl,
    );
    if ($urlMode !== '') {
        $post['web_ai_url_mode'] = $urlMode;
    }
    return $d->callCompanyApiEnc($societyBaseUrl, 'buildingChangePlanController.php', $post);
}

// Mobile AI: Off (no credential needed)
if (isset($updateMobileAiStatus) && $updateMobileAiStatus === 'updateMobileAiStatus') {
    $ai_status = isset($ai_status) ? ((int) $ai_status === 1 ? 1 : 0) : 0;

    if ($ai_status === 1) {
        $_SESSION['msg1'] = 'To enable Mobile AI, select a credential.';
        header('location:../trackingLimit');
        exit();
    }

    $oldCredId = (int) ($society['ai_credentials_id'] ?? 0);
    $d->update(
        'society_master',
        array(
            'ai_status' => 0,
            'ai_credentials_id' => null,
        ),
        "society_id='$society_id'",
        1
    );
    $logMsg = "Mobile AI Disabled for $company_label" . ($oldCredId > 0 ? " (cleared credential ID $oldCredId)" : '');
    $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", $logMsg, 8);
    $_SESSION['msg'] = "Mobile AI Disabled for $company_label";
    header('location:../trackingLimit');
    exit();
}

// Mobile AI: On / update with credential select
if (isset($updateMobileAiSetting) && $updateMobileAiSetting === 'updateMobileAiSetting') {
    $ai_status = isset($ai_status) ? ((int) $ai_status === 1 ? 1 : 0) : 0;
    $ai_credentials_id = isset($ai_credentials_id) ? (int) $ai_credentials_id : 0;
    $oldCredId = (int) ($society['ai_credentials_id'] ?? 0);
    $oldStatus = (int) ($society['ai_status'] ?? 0);

    if ($ai_status === 1) {
        $cred = mobile_ai_resolve_credential($d, $ai_credentials_id);
        if ($cred === null) {
            $_SESSION['msg1'] = 'Please select a valid Mobile AI credential.';
            header('location:../trackingLimit');
            exit();
        }
        $d->update(
            'society_master',
            array(
                'ai_status' => 1,
                'ai_credentials_id' => (int) $cred['ai_credentials_id'],
            ),
            "society_id='$society_id'",
            1
        );
        $credLabel = trim(($cred['ai_model'] ?? '') . ' / ' . (((int) ($cred['debug_key'] ?? 0) === 1) ? 'Debug' : 'Live'));
        $credUrl = rtrim(trim((string) ($cred['ai_base_url'] ?? '')), '/');
        $prevNote = $oldCredId > 0 ? " (previous credential ID $oldCredId)" : '';
        $logMsg = "Mobile AI Enabled for $company_label. Credential: $credLabel (ID {$cred['ai_credentials_id']})$prevNote, URL: $credUrl";
        $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", $logMsg, 8);
        $_SESSION['msg'] = "Mobile AI Enabled for $company_label";
    } else {
        $d->update(
            'society_master',
            array(
                'ai_status' => 0,
                'ai_credentials_id' => null,
            ),
            "society_id='$society_id'",
            1
        );
        $logMsg = "Mobile AI Disabled for $company_label" . ($oldCredId > 0 ? " (cleared credential ID $oldCredId)" : '');
        $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", $logMsg, 8);
        $_SESSION['msg'] = "Mobile AI Disabled for $company_label";
    }

    header('location:../trackingLimit');
    exit();
}

// Web AI: Off (no credential needed)
if (isset($updateWebAiStatus) && $updateWebAiStatus === 'updateWebAiStatus') {
    $web_ai_status = isset($web_ai_status) ? ((int) $web_ai_status === 1 ? 1 : 0) : 0;

    if ($web_ai_status === 1) {
        $_SESSION['msg1'] = 'To enable Web AI, select a credential.';
        header('location:../trackingLimit');
        exit();
    }

    $oldCredId = (int) ($society['web_ai_credentials_id'] ?? 0);
    $sync = web_ai_sync_to_company($d, $society_base_url, $society_id, 0, '', 'clear');
    if (!is_array($sync) || (string) ($sync['status'] ?? '') !== '200') {
        $_SESSION['msg1'] = 'Unable to sync Web AI setting to company server.';
        header('location:../trackingLimit');
        exit();
    }

    $d->update(
        'society_master',
        array(
            'web_ai_status' => 0,
            'web_ai_credentials_id' => null,
        ),
        "society_id='$society_id'",
        1
    );
    $logMsg = "Web AI Disabled for $company_label (cleared URL/credential" . ($oldCredId > 0 ? ", credential ID $oldCredId" : '') . ')';
    $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", $logMsg, 8);
    $_SESSION['msg'] = "Web AI Disabled for $company_label";
    header('location:../trackingLimit');
    exit();
}

// Web AI: On / update with credential select
if (isset($updateWebAiSetting) && $updateWebAiSetting === 'updateWebAiSetting') {
    $web_ai_status = isset($web_ai_status) ? ((int) $web_ai_status === 1 ? 1 : 0) : 0;
    $web_ai_credentials_id = isset($web_ai_credentials_id) ? (int) $web_ai_credentials_id : 0;
    $oldCredId = (int) ($society['web_ai_credentials_id'] ?? 0);

    if ($web_ai_status === 1) {
        $cred = web_ai_resolve_credential($d, $web_ai_credentials_id);
        if ($cred === null) {
            $_SESSION['msg1'] = 'Please select a valid Web AI credential (Live or Dev).';
            header('location:../trackingLimit');
            exit();
        }
        $baseUrl = rtrim($cred['ai_base_url'], '/') . '/';

        $sync = web_ai_sync_to_company($d, $society_base_url, $society_id, 1, $baseUrl, 'replace');
        if (!is_array($sync) || (string) ($sync['status'] ?? '') !== '200') {
            $_SESSION['msg1'] = 'Unable to sync Web AI setting to company server.';
            header('location:../trackingLimit');
            exit();
        }

        $d->update(
            'society_master',
            array(
                'web_ai_status' => 1,
                'web_ai_credentials_id' => (int) $cred['web_ai_credentials_id'],
            ),
            "society_id='$society_id'",
            1
        );
        $credLabel = $cred['credential_name'];
        $prevNote = $oldCredId > 0 ? " (previous credential ID $oldCredId)" : '';
        $logMsg = "Web AI Enabled for $company_label. Credential: $credLabel (ID {$cred['web_ai_credentials_id']})$prevNote, URL: " . rtrim($baseUrl, '/');
        $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", $logMsg, 8);
        $_SESSION['msg'] = "Web AI Enabled for $company_label";
    } else {
        $sync = web_ai_sync_to_company($d, $society_base_url, $society_id, 0, '', 'clear');
        if (!is_array($sync) || (string) ($sync['status'] ?? '') !== '200') {
            $_SESSION['msg1'] = 'Unable to sync Web AI setting to company server.';
            header('location:../trackingLimit');
            exit();
        }

        $d->update(
            'society_master',
            array(
                'web_ai_status' => 0,
                'web_ai_credentials_id' => null,
            ),
            "society_id='$society_id'",
            1
        );
        $logMsg = "Web AI Disabled for $company_label (cleared URL/credential" . ($oldCredId > 0 ? ", credential ID $oldCredId" : '') . ')';
        $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", $logMsg, 8);
        $_SESSION['msg'] = "Web AI Disabled for $company_label";
    }

    header('location:../trackingLimit');
    exit();
}

$_SESSION['msg1'] = 'Invalid action.';
header('location:../trackingLimit');
exit();
