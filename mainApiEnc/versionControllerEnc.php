<?php
include_once 'lib.php';
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
$_POST =  json_decode($d->manage_decryption("1", file_get_contents("php://input")),true);
$compress = resolve_api_compress();
// $_POST =  $d->manage_encryption("1", $_POST);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted,[
        "status" => "201",
        "message" => $xml->string->invalid_request.""
    ], $compress);
    exit();
}
try {
    if (isset($_POST) && !empty($_POST)) {
        $response = array();
        extract(array_map("test_input", $_POST));
        if ($_POST['getVersion'] == "getVersion") {
            // call from SP
            if (!isset($society_id) || $society_id == '' || $society_id == '0') {
                $society_id = 0;
            }

            if (!isset($mobile_app) || $mobile_app == '') {
                $mobile_app = 1;
            }

            // Hard rate limit for getVersion (prevents API abuse)
            if (!$d->rl_rate_limit_myco('getVersion', 5, 50)) {
                http_response_code(429);
                echo $d->manage_encryption($is_encrypted, [
                    "status" => "429",
                    "message" => "Too many requests. Please try again later."
                ], $compress);
                exit();
            }

            if (isset($_POST['saveAnalytics'])) {
                if (!empty($_POST['saveAnalytics']) && $_POST['saveAnalytics'] != null && $_POST['saveAnalytics'] != "null") {
                    $jsonobj = $_POST['saveAnalytics'];
                    $json = json_decode($jsonobj, true);
                    if (count($json) > 0) {
                        for ($i = 0; $i < count($json); $i++) {
                            $societyId = $json[$i]["societyId"];
                            $userId = $json[$i]["userId"];
                            $menuId = $json[$i]["menuId"];
                            $menuName = $json[$i]["menuName"];
                            $userName = $json[$i]["userName"];
                            $userMobile = $json[$i]["userMobile"];
                            $userDesignation = $json[$i]["userDesignation"];
                            $clickTime = $json[$i]["clickTime"];
                            $m->set_data('societyId', $societyId);
                            $m->set_data('userId', $userId);
                            $m->set_data('menuId', $menuId);
                            $m->set_data('menuName', $menuName);
                            $m->set_data('userName', $userName);
                            $m->set_data('userMobile', $userMobile);
                            $m->set_data('userDesignation', $userDesignation);
                            $m->set_data('clickTime', $clickTime);
                            $a1 = array(
                                'societyId' => $m->get_data('societyId'),
                                'userId' => $m->get_data('userId'),
                                'menuId' => $m->get_data('menuId'),
                                'menuName' => $m->get_data('menuName'),
                                'userName' => $m->get_data('userName'),
                                'userMobile' => $m->get_data('userMobile'),
                                'userDesignation' => $m->get_data('userDesignation'),
                                'clickTime' => $m->get_data('clickTime'),
                            );
                            $q = $d->insert("app_menu_master", $a1);
                        }
                    }
                }
            }

            $data = $d->selectSpArray("getVersion($version_app,$mobile_app,$society_id)");
            if (count($data) > 0) {
                $response["version_id"] = $data[0]['version_id'];
                $response["version_app"] = $data[0]['version_app'];
                $response["version_code"] = $data[0]['version_code'];
                $response["version_name"] = $data[0]['version_code'];
                $response["version_name_view"] = $data[0]['version_name_view'];
                $response["language_version"] = $data[0]['language_version'];
                $response["face_sdk_key"] = $data[0]['face_sdk_key'];
                $response["back_banner"] = $base_url . "img/" . $data[0]['back_banner'];
                $response["chat_video"] = "";
                $response["timeline_video"] = "";
                if($version_app==4 && $base_url!="") {
                    $queryData = $d->selectRow("powered_by_logo","society_master_white_label","sub_domain='$base_url'");
                    if(mysqli_num_rows($queryData)>0) {
                        $queryData = mysqli_fetch_array($queryData);
                        $response["powered_by_logo"] = ($queryData['powered_by_logo']!="") ? $m->base_url() . "/img/whitelabel/" . $queryData['powered_by_logo'] : "";
                    } else {
                        $response["powered_by_logo"] = "";
                    }
                } else {
                    $response["powered_by_logo"] = "";
                }

                $app_url_ios = $d->ios_url();
                $app_url_android = $d->android_url();
                if ($mobile_app == 1) {
                    $response["app_url"] = $app_url_android;
                } else {
                    $response["app_url"] = $app_url_ios;
                }
                $response["share_app_content"] = "Hello \nI am referring you Mobile Application named ".$d->app_name().", a Company Management Mobile Application (Platform). \n".$d->app_name()." is a ‘Company Management’ mobile application that provides Company Management Services vide a comprehensive service providers management system that provides various maintenance services to\n•    Employee Directory\n•    Connect and Work together\n•    Attendance Management\n•    Leve Management\n•    Payroll Management\n•    Share Events, Polls & Circulars by way of technology platform to ensure safety, security, convenience, ease and leisure to its users.\n\nAndroid App: $app_url_android\niOS App: $app_url_ios";

                $response["message"] = $xml->string->data_found."";
                $response["status"] = "200";
                echo $d->manage_encryption($is_encrypted,$response, $compress);
                exit();

            } else {

                $response["message"] = $xml->string->no_data_found_web.'';
                $response["status"] = "201";
                echo $d->manage_encryption($is_encrypted,$response, $compress);
                exit();

            }
        } else {
            $response["message"] = $xml->string->wrong_tag.'';
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted,$response, $compress);
            exit();

        }
    }
} catch (Exception $e) {
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
}
echo $d->manage_encryption($is_encrypted,$response, $compress);
exit();