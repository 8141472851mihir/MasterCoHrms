<?php
include_once 'lib.php';
$_POST = json_decode($d->manage_decryption("1", file_get_contents("php://input")), true);
$compress = resolve_api_compress();
// $_POST =  $d->manage_encryption("1", $_POST);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted, [
        "status" => "201",
        "message" => $xml->string->invalid_request . ''
    ], $compress);
    exit();
}

try {
    if (isset($_POST) && !empty($_POST)) {
        if ($key == $keydb) {
            $response = array();
            extract(array_map("test_input", $_POST));

            if (isset($latestPatchListing) && $latestPatchListing == "latestPatchListing") {
                $platformFlag = strtolower(trim((string)($platform ?? '')));
                if ($platformFlag === 'app') {
                    $platformFlag = '0';
                } elseif ($platformFlag === 'web') {
                    $platformFlag = '1';
                }

                // 0 = App (android/ios/flutter), 1 = Web (web/api)
                if ($platformFlag === '0') {
                    $platformSql = "(FIND_IN_SET('android', REPLACE(IFNULL(pd.platforms,''), ' ', '')) > 0
                        OR FIND_IN_SET('ios', REPLACE(IFNULL(pd.platforms,''), ' ', '')) > 0
                        OR FIND_IN_SET('flutter', REPLACE(IFNULL(pd.platforms,''), ' ', '')) > 0)";
                } elseif ($platformFlag === '1') {
                    $platformSql = "(FIND_IN_SET('web', REPLACE(IFNULL(pd.platforms,''), ' ', '')) > 0
                        OR FIND_IN_SET('api', REPLACE(IFNULL(pd.platforms,''), ' ', '')) > 0)";
                } else {
                    $response["message"] = $xml->string->please_provide_required_data . '';
                    $response["status"] = "201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit;
                }

                // recent_days=4 → icon/badge (last 4 patch days). 0 → all show_in_patch rows for platform.
                $recentDays = isset($recent_days) ? intval($recent_days) : 0;
                $patchStatusSql = "AND LOWER(TRIM(IFNULL(qp.patch_status,''))) = 'yes'";
                $dateSql = '';
                $fromDate = '';
                $toDate = date('Y-m-d');
                if ($recentDays > 0) {
                    $fromDate = date('Y-m-d', strtotime('-' . $recentDays . ' days'));
                    $dateSql = "AND qp.patch_date > '1000-01-01'
                    AND qp.patch_date >= '$fromDate'
                    AND qp.patch_date <= '$toDate'";
                    $platformWhere = $platformSql;
                } else {
                    $platformWhere = "($platformSql OR IFNULL(pd.platforms,'') = '')";
                }

                // Requirements / quotation patch tables removed from master.
                $response['patches'] = [];
                $response['platform'] = $platformFlag;
                $response['recent_days'] = (string)$recentDays;
                if ($recentDays > 0) {
                    $response['from_date'] = $fromDate;
                    $response['to_date'] = $toDate;
                }
                $response['message'] = $xml->string->no_data_found_web . '';
                $response['status'] = '200';
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit;
            }

            $response["message"] = $xml->string->wrong_tag . '';
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit;
        }

        $response["message"] = $xml->string->wrong_api_key . '';
        $response["status"] = "201";
        echo $d->manage_encryption($is_encrypted, $response, $compress);
        exit;
    }
} catch (Exception $e) {
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
}
echo $d->manage_encryption($is_encrypted, $response, $compress);
exit();
