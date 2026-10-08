<?php
include_once('../common/objectController.php');
header('Content-Type: application/json; charset=utf-8');

if (!isset($role_id) || (int)$role_id !== 1) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Access denied',
    ));
    exit;
}

function ensureTempCompanyDeactiveTable($con)
{
    mysqli_query($con, "CREATE TABLE IF NOT EXISTS temp_company_deactive (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        url VARCHAR(500) NOT NULL,
        status TINYINT NOT NULL DEFAULT 0 COMMENT '0=pending,1=deleted,2=not_404,3=not_found,4=error,5=processing',
        society_id INT DEFAULT NULL,
        society_name VARCHAR(255) DEFAULT NULL,
        http_code VARCHAR(20) DEFAULT NULL,
        message VARCHAR(500) DEFAULT NULL,
        processed_at DATETIME DEFAULT NULL,
        PRIMARY KEY (id),
        KEY status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function getTempDeactiveCounts($d)
{
    $counts = array(
        'pending' => 0,
        'deleted' => 0,
        'not_404' => 0,
        'not_found' => 0,
        'error' => 0,
        'processing' => 0,
        'total' => 0,
    );

    $q = $d->selectRow("status, COUNT(*) AS cnt", "temp_company_deactive", "1", "GROUP BY status");
    if ($q) {
        while ($row = mysqli_fetch_assoc($q)) {
            $cnt = (int)$row['cnt'];
            $counts['total'] += $cnt;
            switch ((int)$row['status']) {
                case 0:
                    $counts['pending'] = $cnt;
                    break;
                case 1:
                    $counts['deleted'] = $cnt;
                    break;
                case 2:
                    $counts['not_404'] = $cnt;
                    break;
                case 3:
                    $counts['not_found'] = $cnt;
                    break;
                case 4:
                    $counts['error'] = $cnt;
                    break;
                case 5:
                    $counts['processing'] = $cnt;
                    break;
            }
        }
    }

    return $counts;
}

function findSocietyBySubDomain($d, $url)
{
    $url = trim($url);
    $urlNorm = rtrim($url, '/');
    $urlEsc = $d->escapeSqlString($url);
    $urlNormEsc = $d->escapeSqlString($urlNorm);

    $sq = $d->select(
        "society_master",
        "sub_domain='$urlEsc' OR TRIM(TRAILING '/' FROM sub_domain)='$urlNormEsc'",
        "LIMIT 1"
    );
    if ($sq && mysqli_num_rows($sq) > 0) {
        return mysqli_fetch_assoc($sq);
    }
    return false;
}

function checkCompanyUrlIs404($serverUrl, $societyIdDelete)
{
    $serverUrl = rtrim(trim($serverUrl), '/');
    $societyIdDelete = (int)$societyIdDelete;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $serverUrl . "/apAdmin/index.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "society_id=$societyIdDelete&checkUrl=checkUrl");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'key: ' . EnvLoader::get('API_KEY')
    ));
    curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    return array(
        'code' => $code,
        'error' => $curlError,
    );
}

function deleteCompanyFromMaster($d, $societyIdDelete)
{
    $societyIdDelete = (int)$societyIdDelete;
    if ($societyIdDelete <= 0) {
        return false;
    }
    $d->delete("app_common_slider_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("auth_log_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("feedback_master", "society_id='$societyIdDelete' AND society_id!=0 AND is_whitelabel='0'");
    $d->delete("resident_app_menu_society", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("slider_post_log_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("post_log_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("society_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("society_analytics_master", "society_id='$societyIdDelete' AND society_id!=0");

    $d->delete("admin_notification", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("crm_training_progress", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("cron_error_logs", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("crons_society_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete( "feedback_log_master", "feedback_id IS NOT NULL AND NOT EXISTS ( SELECT 1 FROM feedback_master fm WHERE fm.feedback_id = feedback_log_master.feedback_id AND fm.society_id='$societyIdDelete' AND fm.society_id!=0)");
    $d->delete("kycapi_companyprice_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("language_key_value_master_society", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("society_resent_analytics_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("society_users_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("timeline_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("training_attend_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("training_completion_form_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("training_schedule_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("training_status_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("training_visit_master", "society_id='$societyIdDelete' AND society_id!=0");
    $d->delete("whatsapp_access_master", "society_id='$societyIdDelete' AND society_id!=0");
    return true;
}

function updateTempDeactiveRow($d, $id, $status, $message, $httpCode = '', $societyId = null, $societyName = null)
{
    $data = array(
        'status' => (string)$status,
        'message' => $message,
        'http_code' => (string)$httpCode,
        'processed_at' => date('Y-m-d H:i:s'),
    );
    if ($societyId !== null) {
        $data['society_id'] = (string)$societyId;
    }
    if ($societyName !== null) {
        $data['society_name'] = $societyName;
    }
    $id = (int)$id;
    $d->update("temp_company_deactive", $data, "id='$id'");
}

ensureTempCompanyDeactiveTable($con);

$action = isset($_POST['action']) ? $_POST['action'] : 'counts';

if ($action === 'add_urls') {
    $urlsText = isset($_POST['urls']) ? $_POST['urls'] : '';
    $lines = preg_split('/\r\n|\r|\n/', $urlsText);
    $added = 0;
    $skipped = 0;

    foreach ($lines as $line) {
        $url = trim($line);
        if ($url === '') {
            continue;
        }
        $urlEsc = $d->escapeSqlString($url);
        $exists = $d->selectRow("id", "temp_company_deactive", "url='$urlEsc' AND status IN (0,5)", "LIMIT 1");
        if ($exists && mysqli_num_rows($exists) > 0) {
            $skipped++;
            continue;
        }
        $ok = $d->insert("temp_company_deactive", array(
            'url' => $url,
            'status' => '0',
        ));
        if ($ok) {
            $added++;
        }
    }

    echo json_encode(array(
        'success' => true,
        'added' => $added,
        'skipped' => $skipped,
        'counts' => getTempDeactiveCounts($d),
        'message' => "$added URL(s) added" . ($skipped > 0 ? ", $skipped already pending" : ''),
    ));
    exit;
}

if ($action === 'reset_failed') {
    $d->update("temp_company_deactive", array('status' => '0', 'message' => '', 'http_code' => '', 'processed_at' => null), "status IN (2,3,4,5)", 1);
    echo json_encode(array(
        'success' => true,
        'counts' => getTempDeactiveCounts($d),
        'message' => 'Failed / skipped rows set back to pending',
    ));
    exit;
}

if ($action === 'counts') {
    echo json_encode(array(
        'success' => true,
        'counts' => getTempDeactiveCounts($d),
        'done' => false,
    ));
    exit;
}

if ($action === 'process_one') {
    $rowQ = $d->select("temp_company_deactive", "status='0'", "ORDER BY id ASC LIMIT 1");
    if (!$rowQ || mysqli_num_rows($rowQ) == 0) {
        echo json_encode(array(
            'success' => true,
            'done' => true,
            'counts' => getTempDeactiveCounts($d),
            'message' => 'No pending companies left',
        ));
        exit;
    }

    $row = mysqli_fetch_assoc($rowQ);
    $id = (int)$row['id'];
    $url = trim($row['url']);

    $d->update("temp_company_deactive", array('status' => '5'), "id='$id' AND status='0'");
    if ((int)$con->affected_rows < 1) {
        echo json_encode(array(
            'success' => true,
            'done' => false,
            'skipped' => true,
            'counts' => getTempDeactiveCounts($d),
            'message' => 'Row already picked by another process',
        ));
        exit;
    }

    $socData = findSocietyBySubDomain($d, $url);
    if (!$socData) {
        updateTempDeactiveRow($d, $id, 3, 'Company not found for this sub_domain');
        echo json_encode(array(
            'success' => true,
            'done' => false,
            'result' => array(
                'id' => $id,
                'url' => $url,
                'status' => 3,
                'status_text' => 'Not found',
                'message' => 'Company not found for this sub_domain',
            ),
            'counts' => getTempDeactiveCounts($d),
        ));
        exit;
    }

    $societyIdDelete = $d->sanitizeActionIdAsInt($socData['society_id']);
    $sName = $socData['society_name'];
    $serverUrl = $socData['sub_domain'];

    $check = checkCompanyUrlIs404($serverUrl, $societyIdDelete);
    $code = $check['code'];

    if ($code === 0) {
        $msg = $check['error'] !== '' ? $check['error'] : 'Could not connect to company URL';
        updateTempDeactiveRow($d, $id, 4, $msg, '0', $societyIdDelete, $sName);
        echo json_encode(array(
            'success' => true,
            'done' => false,
            'result' => array(
                'id' => $id,
                'url' => $url,
                'society_id' => $societyIdDelete,
                'society_name' => $sName,
                'http_code' => 0,
                'status' => 4,
                'status_text' => 'Error',
                'message' => $msg,
            ),
            'counts' => getTempDeactiveCounts($d),
        ));
        exit;
    }

    if ($code !== 404) {
        $msg = "Please Delete Server Code & Database First (HTTP $code)";
        updateTempDeactiveRow($d, $id, 2, $msg, (string)$code, $societyIdDelete, $sName);
        echo json_encode(array(
            'success' => true,
            'done' => false,
            'result' => array(
                'id' => $id,
                'url' => $url,
                'society_id' => $societyIdDelete,
                'society_name' => $sName,
                'http_code' => $code,
                'status' => 2,
                'status_text' => 'Not 404',
                'message' => $msg,
            ),
            'counts' => getTempDeactiveCounts($d),
        ));
        exit;
    }

    deleteCompanyFromMaster($d, $societyIdDelete);
    $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "$sName  Company Deleted.");
    updateTempDeactiveRow($d, $id, 1, 'Company deleted', '404', $societyIdDelete, $sName);

    echo json_encode(array(
        'success' => true,
        'done' => false,
        'result' => array(
            'id' => $id,
            'url' => $url,
            'society_id' => $societyIdDelete,
            'society_name' => $sName,
            'http_code' => 404,
            'status' => 1,
            'status_text' => 'Deleted',
            'message' => 'Company deleted',
        ),
        'counts' => getTempDeactiveCounts($d),
    ));
    exit;
}

echo json_encode(array(
    'success' => false,
    'message' => 'Invalid action',
));
