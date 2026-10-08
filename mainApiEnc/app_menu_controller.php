<?php
include_once 'lib.php';
$_POST =  json_decode($d->manage_decryption("1", file_get_contents("php://input")), true);
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
            if ($_POST['getAppMenuGridWithCategory'] == "getAppMenuGridWithCategory"  && filter_var($society_id, FILTER_VALIDATE_INT) == true) {
                $gridWidth = '3';
                $response["is_update_show"] = true;
                $response["grid_width"] = $gridWidth;
                $response["ai_base_url"] = "";
                $response["ai_username"] = "";
                $response["ai_password"] = "";
                $response["ai_model"] = "";
                $response["ai_api_key"] = "";
                $response["ai_terms_conditions"] = "";
                $response["ai_llm_model"] = "";
                $response["agent_token"] = "";
                if ($device == 'android') {
                    $menuAppendQuery = " AND resident_app_menu_society.menu_status='0' AND resident_app_menu_society.menu_status_android='0'";
                } else  if ($device == 'ios') {
                    $menuAppendQuery = " AND resident_app_menu_society.menu_status='0' AND resident_app_menu_society.menu_status_ios='0'";
                } else {
                    $menuAppendQuery = " AND resident_app_menu_society.menu_status='0'";
                }

                $orderBy = "ORDER BY CASE  WHEN resident_app_menu.menu_type = 1 THEN 1  WHEN resident_app_menu.menu_type = 2 THEN 2  WHEN resident_app_menu_society.alternate_sequence > 0 THEN 3  ELSE 4 END,resident_app_menu_society.alternate_sequence ASC,resident_app_menu_society.menu_sequence ASC,resident_app_menu_society.app_menu_id ASC";

                $singleQry = $d->selectRow("resident_app_menu.*,resident_app_menu_society.*", "resident_app_menu,resident_app_menu_society", "resident_app_menu.menu_status=0  AND resident_app_menu_society.app_menu_id=resident_app_menu.app_menu_id  AND resident_app_menu.parent_menu_id=0 AND resident_app_menu_society.society_id='$society_id' AND resident_app_menu.menu_type IN (0, 1, 2) $menuAppendQuery", "$orderBy");

                if (mysqli_num_rows($singleQry) > 0) {
                    $response["appmenu"] = array();
                    $response["appmenu_big"] = array();
                    $response["appmenu_home"] = array();
                    $dashboardCount = 0;
                    while ($data_app = mysqli_fetch_array($singleQry)) {
                        $data_app = array_map("html_entity_decode", $data_app);
                        $menuId = $data_app["app_menu_id"];
                        $menuType = $data_app["menu_type"];
                        $alternateSequence = $data_app["alternate_sequence"];
                        $appmenu = array();
                        $appmenu["app_menu_id"] = $data_app["app_menu_id"];
                        $appmenu["menu_category_id"] = $data_app["menu_category_id"];
                        $appmenu["menu_title"] = $data_app["menu_title"];
                        $appmenu["menu_language_key"] = $data_app['language_key_name'];
                        $appmenu["page_link"] = $data_app['page_link'] ?? '';
                        $appmenu["menu_title_search"] = $data_app["menu_title"];
                        $appmenu["menu_click"] = $data_app["menu_click"];
                        $appmenu["ios_menu_click"] = $data_app["ios_menu_click"];
                        $appmenu["menu_icon"] = $bucket_url . "icons/" . $data_app["menu_icon"];
                        $appmenu["menu_icon_new"] = "";
                        $appmenu["no_data_image"] = (!empty($data_app["no_data_image"])) ? $bucket_url . "icons/" . $data_app["no_data_image"] : "";
                        $appmenu["menu_sequence"] = $data_app["menu_sequence"];
                        $appmenu["tutorial_video"] = "";
                        $appmenu["is_new"] = ($data_app["is_new"] == 1) ? true : false;
                        $appmenu["appmenu_sub"] = array();
                        if ($menuId == 37 || $menuId == 41) {
                            array_push($response["appmenu_big"], $appmenu);
                        } else if ($menuType == '2' || $menuId == 1 || $menuId == 2) {
                            array_push($response["appmenu_home"], $appmenu);
                            $dashboardCount++;
                        } else if ($alternateSequence > 0) {
                            if (($dashboardCount % $gridWidth) != 0 || $dashboardCount == 0) {
                                array_push($response["appmenu_home"], $appmenu);
                                $dashboardCount++;
                            } else {
                                array_push($response["appmenu"], $appmenu);
                            }
                        } else if ($menuType == '0') {
                            if (($dashboardCount % $gridWidth) != 0) {
                                array_push($response["appmenu_home"], $appmenu);
                                $dashboardCount++;
                            } else {
                                array_push($response["appmenu"], $appmenu);
                            }
                        }
                    }
                }

                $response["chat_video"] = "";
                $response["timeline_video"] = "";
                $response["setting_video"] = "";
                $response["homepage_video"] = "";

                $response["accessKeyAws"] = "";
                $response["secretKeyAws"] = "";

                $qnotification = $d->select("app_common_slider_master, app_slider_master", "app_common_slider_master.app_slider_id=app_slider_master.app_slider_id AND app_common_slider_master.society_id='$society_id' AND app_common_slider_master.status=0", "order by RAND()");

                $response["slider"] = array();

                if (mysqli_num_rows($qnotification) > 0) {

                    while ($data_notification = mysqli_fetch_array($qnotification)) {

                        $slider = array();

                        $slider["app_slider_id"] = $data_notification['app_slider_id'];
                        $slider["society_id"] = $data_notification['society_id'];
                        $slider["slider_image_name"] = $base_url . "img/sliders/" . $data_notification['slider_image_name'];
                        $slider["youtube_url"] = ($data_notification['youtube_url'] != '' || $data_notification['youtube_url'] != null) ? $data_notification['youtube_url'] : "";
                        $slider["slider_status"] = $data_notification['slider_status'];
                        $slider["page_url"] = $data_notification['page_url'] . '';
                        if ($data_notification['page_mobile'] != 0) {
                            $slider["page_mobile"] = $data_notification['page_mobile'] . '';
                        } else {
                            $slider["page_mobile"] = '';
                        }
                        $slider["date_view"] = "";
                        $slider["about_offer"] = $data_notification['about_offer'] . '';

                        array_push($response["slider"], $slider);
                    }
                } else {

                    $qdefailt = $d->select("app_common_slider_master, app_slider_master", "app_common_slider_master.app_slider_id=app_slider_master.app_slider_id AND app_common_slider_master.default_flag='1' AND app_common_slider_master.status=0", "order by RAND()");

                    while ($data_notification = mysqli_fetch_array($qdefailt)) {

                        $slider = array();

                        $slider["app_slider_id"] = $data_notification['app_slider_id'];
                        $slider["society_id"] = $data_notification['society_id'];
                        $slider["slider_image_name"] = $base_url . "img/sliders/" . $data_notification['slider_image_name'];
                        $slider["youtube_url"] = $data_notification['youtube_url'];
                        $slider["slider_status"] = $data_notification['slider_status'];
                        $slider["page_url"] = $data_notification['page_url'] . '';
                        $slider["page_mobile"] = $data_notification['page_mobile'] . '';
                        array_push($response["slider"], $slider);
                    }
                }
                $app_signin = $_POST['app_signin'] ?? '';
                $app_signin_key = $d->app_signin_key();
                if ($app_signin_key == $app_signin && $app_signin_key != false) {
                    $debug_condition = "0";
                } else {
                    $debug_condition = "1";
                }
                $qb = $d->selectRow("sub_domain,maintainance_active_status,distance_get_type,distance_get_url,app_url_ios,app_url_android,splash_colour,splash_image,is_firebase_otp,is_firebase_chat,visible_in_search_list,society_master.ai_status, CASE WHEN society_master.ai_status = 1 THEN COALESCE(final_ai.ai_base_url, '') ELSE '' END AS ai_base_url, CASE WHEN society_master.ai_status = 1 THEN COALESCE(final_ai.ai_username, '') ELSE '' END AS ai_username, CASE WHEN society_master.ai_status = 1 THEN COALESCE(final_ai.ai_password, '') ELSE '' END AS ai_password, CASE WHEN society_master.ai_status = 1 THEN COALESCE(final_ai.ai_model, '') ELSE '' END AS ai_model, CASE WHEN society_master.ai_status = 1 THEN COALESCE(final_ai.ai_api_key, '') ELSE '' END AS ai_api_key, CASE WHEN society_master.ai_status = 1 THEN COALESCE(final_ai.ai_terms_conditions, '') ELSE '' END AS ai_terms_conditions, CASE WHEN society_master.ai_status = 1 THEN COALESCE(final_ai.ai_llm_model, '') ELSE '' END AS ai_llm_model, CASE WHEN society_master.ai_status = 1 THEN COALESCE(final_ai.agent_token, '') ELSE '' END AS agent_token ", " society_master LEFT JOIN domain_master ON domain_master.domain_id = society_master.domain_id LEFT JOIN ai_credentials_master final_ai ON final_ai.ai_credentials_id = (SELECT ai_credentials_id FROM ai_credentials_master WHERE ai_status = 1 AND (('$debug_condition' = '1' AND debug_key = 1) OR ('$debug_condition' = '0' AND ai_credentials_id = COALESCE(society_master.ai_credentials_id,(SELECT ai_credentials_id FROM ai_credentials_master WHERE ai_status = 1 AND debug_key = 0 ORDER BY ai_credentials_id LIMIT 1)))) ORDER BY ai_credentials_id LIMIT 1) ", " society_master.society_id='$society_id' ");

                $bData = mysqli_fetch_array($qb);
                $response["ai_base_url"] = $bData['ai_base_url'];
                $app_version = $_POST['app_version'] ?? '0';
                if (isset($app_version) && version_compare((string) $app_version, '341', '<')) {
                    $response["ai_base_url"] = "";
                }
                $response["ai_username"] = $bData['ai_username'];
                $response["ai_password"] = $bData['ai_password'];
                $response["ai_model"] = $bData['ai_model'];
                $response["ai_api_key"] = $bData['ai_api_key'];
                $response["ai_terms_conditions"] = $bData['ai_terms_conditions'];
                $response["ai_llm_model"] = $bData['ai_llm_model'];
                $response["agent_token"] = $bData['agent_token'];
                $maintainance_active_status = $bData['maintainance_active_status'];

                $app_url_ios = $bData['app_url_ios'];
                $app_url_android = $bData['app_url_android'];

                if ($bData['visible_in_search_list'] == 1) {
                    $society_name = $data_society['society_name'];
                    $response["share_app_content"] = "Hello \nI am referring you Mobile Application named $society_name, a Company Management Mobile Application (Platform). \n$society_name is a ‘Company Management’ mobile application that provides Company Management Services vide a comprehensive service providers management system that provides various maintenance services to\n•    Employee Directory\n•    Connect and Work together\n•    Service Provider\n•    Facilities & Resources\n•    Share Events, Polls & Circulars by way of technology platform to ensure safety, security, convenience, ease and leisure to its users.\n\nAndroid App: $app_url_android\niOS App: $app_url_ios";
                } else {
                    $response["share_app_content"] = "Hello \nI am referring you Mobile Application named " . $d->app_name() . ", a Company Management Mobile Application (Platform). \n" . $d->app_name() . " is a ‘Company Management’ mobile application that provides Company Management Services vide a comprehensive service providers management system that provides various maintenance services to\n•    Employee Directory\n•    Connect and Work together\n•    Attendance Management\n•    Leave Management\n•    Payroll Management\n•    Share Events, Polls & Circulars by way of technology platform to ensure safety, security, convenience, ease and leisure to its users.\n\nAndroid App: $app_url_android\niOS App: $app_url_ios";
                }

                if ($bData['distance_get_type'] == 1) {
                    $response["my_distance"] = "https://api.distancematrix.ai/maps/api/distancematrix/json?key=" . $d->distance_matrix_key();
                } else {
                    $response["my_distance"] = $bData['distance_get_url'];
                }

                if ($bData['distance_get_type'] == 1) {
                    $response["distance_get_type"] = $bData['distance_get_type'];
                } else {
                    $response["distance_get_type"] = $bData['distance_get_type'];
                }

                if ($bData['is_firebase_otp'] == 1) {
                    $response["is_firebase"] = true;
                } else {
                    $response["is_firebase"] = false;
                }

                if ($bData['is_firebase_chat'] == 1) {
                    $response["chat_firebase"] = true;
                } else {
                    $response["chat_firebase"] = false;
                }

                if ($bData['splash_colour'] != "" && $bData['splash_image'] != "") {
                    $response["sp_bg_colour_code"] = $bData['splash_colour'];
                    $response["sp_bg_url"] = $base_url . "img/society_requests/" . $bData['splash_image'];
                } else {
                    $response["sp_bg_colour_code"] = "";
                    $response["sp_bg_url"] = "";
                }

                $is_upcomming_maintenance = false;
                $is_under_maintenance = false;
                if ($maintainance_active_status == 1) {
                    $appendMaintaceQuery = " OR is_festival=1";
                    $is_upcomming_maintenance = true;
                }

                $today = date("Y-m-d");
                $qa = $d->select("festival_master", "festival_date='$today' $appendMaintaceQuery");
                $advData = mysqli_fetch_array($qa);
                if ($advData > 0) {
                    if ($advData['festival_view_status'] == 0) {
                        $festival_view_status = '1';
                    } else {
                        $festival_view_status = '0';
                    }
                    if ($advData['festival_active_status'] == 0) {
                        $festival_active_status = '1';
                    } else {
                        $festival_active_status = '0';
                    }
                    $specifiedDate = $advData['festival_time'];
                    $currentTime = date('Y-m-d H:i:s');
                    $is_under_maintenance = ($specifiedDate != "" && $specifiedDate != "0000-00-00 00:00:00" && strtotime($currentTime) > strtotime($specifiedDate)) ? true : false;
                    $response["is_upcomming_maintenance"] = $is_upcomming_maintenance;
                    $response["is_under_maintenance"] = $is_under_maintenance;
                    $response["festival_name"] = $advData['festival_name'];
                    $response["festival_number"] = $advData['festival_number'];
                    $response["festival_video"] = $advData['festival_video'];
                    $response["festival_url"] = $advData['festival_url'];
                    $response["festival_date"] = $advData['festival_date'];
                    $response["view_status"] = $festival_view_status . '';
                    $response["active_status"] = $festival_active_status;
                    $response["advertisement_url"] = $base_url . "img/festival/" . $advData['festival_image'];
                } else {
                    $response["is_upcomming_maintenance"] = $is_upcomming_maintenance;
                    $response["is_under_maintenance"] = $is_under_maintenance;
                    $response["festival_name"] = '';
                    $response["festival_number"] = '';
                    $response["festival_video"] = '';
                    $response["festival_url"] = '';
                    $response["festival_date"] = '';
                    $response["view_status"] = '0';
                    $response["active_status"] = '0';
                    $response["advertisement_url"] = "";
                }
                $response["menu_category"] = array();
                $menu_category_master = $d->select("menu_category_master", "menu_category_status='0'");
                while ($category_master_data = mysqli_fetch_array($menu_category_master)) {
                    $menu_category = array();
                    $menu_category["menu_category_id"] = $category_master_data['menu_category_id'];
                    $menu_category["menu_category_name"] = $category_master_data['menu_category_name'];
                    $menu_category["menu_category_key"] = $category_master_data['menu_category_key'];
                    if ($category_master_data['menu_category_icon'] != "") {
                        $menu_category["menu_category_icon"] = $bucket_url . "icons/" . $category_master_data['menu_category_icon'];
                    } else {
                        $menu_category["menu_category_icon"] = "";
                    }
                    array_push($response["menu_category"], $menu_category);
                }
                $response["base_url"] = $bData['sub_domain'];
                $response["message"] = $xml->string->success . '';
                $response["status"] = "200";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            } else {
                $response["message"] = $xml->string->wrong_tag . '';
                $response["status"] = "201";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            }
        } else {

            $response["message"] = $xml->string->wrong_api_key . '';
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit();
        }
    }
} catch (Exception $e) {
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
}
echo $d->manage_encryption($is_encrypted, $response, $compress);
exit();
