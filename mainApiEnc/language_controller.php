<?php
include_once 'lib.php';
$_POST =  json_decode($d->manage_decryption("1", file_get_contents("php://input")), true);
$compress = resolve_api_compress();
// $_POST =  $d->manage_encryption("1", $_POST);
// print_r($_POST);
// exit;
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted, [
        "status" => "201",
        "message" => "Invalid Data"
    ], $compress);
    exit();
}
try {
    if (isset($_POST) && !empty($_POST)) {
        if ($key == $keydb) {
            $response = array();
            extract(array_map("test_input", $_POST));
            $today  = date("Y-m-d");
            if ($_POST['getLanguage'] == "getLanguage") {

                if (!$d->rl_rate_limit_myco('getLanguage', 20, 120)) {
                    http_response_code(429);
                    $response["message"] = "Too many requests. Please try again later.";
                    $response["status"] = "429";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }

                $q = $d->select("language_master", "active_status=0 AND language_id!=5", "ORDER BY language_id ASC");

                if (mysqli_num_rows($q) > 0) {

                    $response["language"] = array();

                    while ($data = mysqli_fetch_array($q)) {
                        $language = array();
                        $language["language_id"] = $data['language_id'];
                        $language["language_name"] = $data['language_name'];
                        $language["language_name_1"] = $data['language_name_1'];
                        $language["language_file"] = $data['language_file'];
                        $language["continue_btn_name"] = $data['continue_btn_name'];
                        $language["language_direction"] = $data['language_direction'];
                        $language["language_code"] = $data['language_code'];
                        $language["language_icon"] = $base_url . 'img/language/' . $data['language_file'];
                        array_push($response["language"], $language);
                    }

                    $response["message"] = "Get Language Successfully!";
                    $response["status"] = "200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                } else {
                    $response["message"] = "No Language Available.";
                    $response["status"] = "201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }
            } else  if ($_POST['getLanguageAll'] == "getLanguageAll") {


                $q = $d->select("language_master", "", "ORDER BY language_id ASC");

                if (mysqli_num_rows($q) > 0) {

                    $response["language"] = array();

                    while ($data = mysqli_fetch_array($q)) {
                        $language = array();
                        $language["language_id"] = $data['language_id'];
                        $language["language_name"] = $data['language_name'];
                        $language["language_name_1"] = $data['language_name_1'];
                        $language["language_file"] = $data['language_file'];
                        $language["continue_btn_name"] = $data['continue_btn_name'];
                        $language["language_direction"] = $data['language_direction'];
                        $language["language_code"] = $data['language_code'];
                        $language["language_icon"] = $base_url . 'img/language/' . $data['language_file'];
                        array_push($response["language"], $language);
                    }

                    $response["message"] = "Get Language Successfully!";
                    $response["status"] = "200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                } else {
                    $response["message"] = "No Language Available.";
                    $response["status"] = "201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }
            } else if ($_POST['getLanguageNew'] == "getLanguageNew") {

                $response["language"] = array();

                // $q = $d->select("language_master", "active_status=0 AND is_english_language=1 AND country_id='$country_id'", " ORDER BY language_id DESC LIMIT 1");

                // if (mysqli_num_rows($q) == 0) {
                //     $q = $d->select("language_master", "active_status=0 AND is_english_language=1 AND country_id='101'", "LIMIT 1");
                // }

                // if (mysqli_num_rows($q) > 0) {

                //     while ($data = mysqli_fetch_array($q)) {
                //         $language = array();
                //         $language["language_id"] = $data['language_id'];
                //         $language["language_name"] = $data['language_name'];
                //         $language["language_name_1"] = $data['language_name_1'];
                //         $language["language_file"] = $data['language_file'];
                //         $language["continue_btn_name"] = $data['continue_btn_name'];
                //         $language["language_direction"] = $data['language_direction'];
                //         $language["language_code"] = $data['language_code'];
                //         $language["language_icon"] = $base_url . 'img/language/' . $data['language_file'];
                //         array_push($response["language"], $language);
                //     }

                //     $temp = true;
                // } else {
                //     $temp = false;
                // }

                $q1 = $d->select("language_master", "active_status=0", "ORDER BY language_id ASC");
                // $q1 = $d->select("language_master", "active_status=0 AND is_english_language=0 AND country_id=0 OR active_status=0 AND is_english_language=0 AND country_id='$country_id'", "ORDER BY language_id ASC");
                if (mysqli_num_rows($q1) > 0) {

                    while ($data1 = mysqli_fetch_array($q1)) {
                        $language = array();
                        $language["language_id"] = $data1['language_id'];
                        $language["language_name"] = $data1['language_name'];
                        $language["language_name_1"] = $data1['language_name_1'];
                        $language["language_file"] = $data1['language_file'];
                        $language["continue_btn_name"] = $data1['continue_btn_name'];
                        $language["language_direction"] = $data1['language_direction'];
                        $language["language_code"] = $data1['language_code'];
                        $language["language_icon"] = $base_url . 'img/language/' . $data1['language_file'];
                        array_push($response["language"], $language);
                    }

                    $temp1 = true;
                } else {
                    $temp1 = false;
                }

                if ($temp1 == true) {
                    $response["message"] = 'Get Language Successfully!';
                    $response["status"] = "200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                } else {
                    $response["message"] = 'No Language Found';
                    $response["status"] = "201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }
            } else if ($_POST['getLanguageValuesOld'] == "getLanguageValuesOld") {
                $is_encrypted = '0';
                if ($language_id == "" || $language_id == 0) {
                    $language_id = 1;
                }
                $arrayKeyValueSingle = array();
                $arrayKeyValue = array();
                $qc = $d->selectRow("language_key_value_master.value_name,language_key_master.language_key_id,language_key_master.key_name, language_key_master.key_type, language_key_value_master_society.value_name_society", "language_key_value_master, language_key_master Left join language_key_value_master_society on language_key_master.key_name=language_key_value_master_society.key_name AND language_key_value_master_society.language_id='$language_id' AND language_key_value_master_society.society_id='$society_id'", "language_key_value_master.language_id='$language_id' AND language_key_master.language_key_id=language_key_value_master.language_key_id");
                while ($oldData = mysqli_fetch_array($qc)) {
                    if ($oldData['key_type'] == 0) {
                        $arrayKeyValueSingle[$oldData['key_name']] = $oldData['value_name_society'] != '' ? $oldData['value_name_society'] : $oldData['value_name'];
                    } else {
                        if (array_key_exists($oldData['key_name'], $arrayKeyValue)) {
                            array_push($arrayKeyValue[$oldData['key_name']], $oldData['value_name']);
                        } else {
                            $arrayKeyValue[$oldData['key_name']] = array($oldData['value_name']);
                        }
                    }
                }
                $response = array_merge($arrayKeyValueSingle, $arrayKeyValue);
                $response["totalArray"] = count($arrayKeyValue);
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            } else if ($_POST['getLanguageValues'] == "getLanguageValues") {
                $is_encrypted = '0';

                if (!$d->rl_rate_limit_myco('getLanguageValues', 10, 60)) {
                    http_response_code(429);
                    $response["message"] = "Too many requests. Please try again later.";
                    $response["status"] = "429";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }

                if ($language_id == "" || $language_id == 0) {
                    $language_id = 1;
                }
                if ($society_id == "" || $society_id == 0) {
                    $society_id = 0;
                }
                $is_whitelabel = $d->is_whitelabel();
                if ($is_whitelabel == "false") {
                    $customValueCount=$d->count_data_direct("value_master_society_id","language_key_value_master_society","society_id='$society_id'");
                    if($customValueCount>0){
                        $s3Url = $d->getLanguageFileS3Url("company_{$society_id}_{$language_id}.json");
                    }else{
                        $s3Url = $d->getLanguageFileS3Url("{$language_id}.json");
                    }
                }else{
                    $s3Url = $d->getLanguageFileS3Url("{$language_id}.json");
                }
                $commonValues = array();
                if ($s3Url !== false) {
                    $commonJsonContent = @file_get_contents($s3Url);
                    if ($commonJsonContent !== false) {
                        $commonValues = json_decode($commonJsonContent, true);
                        if ($commonValues === null) {
                            $commonValues = array();
                        }
                    }
                }
                
                if (empty($commonValues)) {
                    $arrayKeyValueSingle = array();
                    $arrayKeyValue = array();
                    $qc = $d->selectRow(
                        "language_key_value_master.value_name,language_key_master.language_key_id,language_key_master.key_name, language_key_master.key_type, language_key_value_master_society.value_name_society",
                        "language_key_value_master, language_key_master Left join language_key_value_master_society on language_key_master.key_name=language_key_value_master_society.key_name AND language_key_value_master_society.language_id='$language_id' AND language_key_value_master_society.society_id='$society_id'",
                        "language_key_value_master.language_id='$language_id' AND language_key_master.language_key_id=language_key_value_master.language_key_id"
                    );
                    while ($oldData = mysqli_fetch_array($qc)) {
                        if ($oldData['key_type'] == 0) {
                            $arrayKeyValueSingle[$oldData['key_name']] = $oldData['value_name_society'] != '' ? $oldData['value_name_society'] : $oldData['value_name'];
                        } else {
                            if (array_key_exists($oldData['key_name'], $arrayKeyValue)) {
                                array_push($arrayKeyValue[$oldData['key_name']], $oldData['value_name']);
                            } else {
                                $arrayKeyValue[$oldData['key_name']] = array($oldData['value_name']);
                            }
                        }
                    }
                    $response = array_merge($arrayKeyValueSingle, $arrayKeyValue);
                    $response["totalArray"] = count($arrayKeyValue);
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }
                $response = array();
                $arrayCount = 0;
                foreach ($commonValues as $key => $value) {
                    if (is_array($value)) {
                        $arrayCount++;
                    }
                }
                $response = $commonValues;
                $response["totalArray"] = $arrayCount;
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            } else if ($_POST['checkLangugeModify'] == "checkLangugeModify"  && filter_var($language_id, FILTER_VALIDATE_INT) == true && filter_var($society_id, FILTER_VALIDATE_INT) == true) {

                $qc = $d->select("language_key_value_master_society", "language_id='$language_id' AND society_id='$society_id'");
                if (mysqli_num_rows($qc) > 0) {

                    $response["is_language_re_downlaod"] = true;
                } else {
                    $response["is_language_re_downlaod"] = false;
                }
                $response["message"] = "Success";
                $response["status"] = "200";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            } else {
                $response["message"] = "wrong tag";
                $response["status"] = "201";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            }
        } else {
            $response["message"] = "wrong api key";
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
