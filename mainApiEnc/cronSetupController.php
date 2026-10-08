<?php
include_once 'lib.php';
$is_encrypted = 0;
try {
    if (isset($_POST) && !empty($_POST)) {
        $response = array();
        extract(array_map("test_input", $_POST));
        $compress = resolve_api_compress();
        if ($key == $keydb) {
            if (isset($addCompanyToCron) && $addCompanyToCron == "addCompanyToCron") {
                if (isset($company_id) && $company_id != "") {
                    if (isset($cron_category) && $cron_category != "") {
                        $flag = isset($flag) ? $flag : 0;
                        if (isset($flag) && $flag == 1) {
                            $result = $d->addCompanyToCronCategory($company_id, $cron_category, array(
                                'cron_url' => isset($cron_url) ? $cron_url : '',
                                'cron_tag' => isset($cron_tag) ? $cron_tag : '',
                                'cron_value' => isset($cron_value) ? $cron_value : '',
                                'curl_script' => 'cron/curl1.php',
                                'send_mail' => true,
                            ));
                            $response["message"] = $result['message'];
                            $response["status"] = $result['status'];
                            echo $d->manage_encryption($is_encrypted, $response, $compress);
                            exit();
                        } else {
                            $result = $d->removeCompanyFromCronCategory($company_id, $cron_category);
                            $response["message"] = $result['message'];
                            $response["status"] = $result['status'];
                            echo $d->manage_encryption($is_encrypted, $response, $compress);
                            exit();
                        }
                    } else {
                        $response["message"] = 'Cron category is mandatory';
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    }
                } else {
                    $response["message"] = 'Company Id Not Found';
                    $response["status"] = "201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }
            } else {
                $response["message"] = "Wrong Tag   ";
                $response["status"] = "201";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            }
        } else {
            $response["message"] = "Wrong Api Key";
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit();
        }
    } else {
        $response["message"] = "Invalid Request Method";
        $response["status"] = "201";
        echo $d->manage_encryption($is_encrypted, $response, $compress);
        exit();
    }
} catch (Exception $e) {
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
}
echo $d->manage_encryption($is_encrypted, $response, $compress);
exit();
