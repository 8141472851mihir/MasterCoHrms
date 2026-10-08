<?php
include_once 'lib.php';
$compress = resolve_api_compress();
// $_POST =  json_decode($d->manage_decryption("1", file_get_contents("php://input")), true);
// $_POST =  $d->manage_encryption("1", $_POST);
$is_encrypted = 0;
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted, [
        "status" => "201",
        "message" => $xml->string->invalid_request . ''
    ], $compress);
    exit();
}
try {
    if (isset($_POST) && !empty($_POST)) {
        $response = array();

        extract(array_map("test_input", $_POST));
        if ($key == $keydb) {
            if ($_POST['getCrmList'] == "getCrmList") {
                $filer = "";
                if (isset($society_crm_id) && $society_crm_id != "") {
                    $filer = " AND society_master.society_crm_id ='$society_crm_id' ";
                }
                $crmListData = $d->selectRow("domain_master.domain_name,society_master.sub_domain,server_master.server_ip,domain_master.domain_name", "society_master LEFT JOIN domain_master ON domain_master.domain_id = society_master.domain_id  LEFT JOIN server_master ON server_master.server_id = domain_master.server_id", "society_master.crm_created  ='1' $filer", "");

                $response["crmData"] = array();
                if (mysqli_num_rows($crmListData) > 0) {
                    while ($data_crm = mysqli_fetch_array($crmListData)) {
                        $crmData = array();
                        $server_ip = $data_crm['server_ip'];
                        $sub_domain = $data_crm['sub_domain'];
                        $new_domain_name = str_replace("https://", "", $sub_domain);
                        $new_domain_name = rtrim($new_domain_name, '/');
                        $crm_url = "ubuntu@" . $server_ip . ":/var/www/html/mycompany/" . $new_domain_name . "/crm/";
                        $crmData["crm_url"] = $crm_url;
                        array_push($response["crmData"], $crmData);
                    }
                }
                $response["message"] = 'Success';
                $response["status"] = "200";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            } else if ($_POST['getCrmMasterList'] == "getCrmMasterList") {
                $crmMasterData = $d->selectRow("society_crm_id, url, server, visible_create_request, backendid", "society_crm_master", "");

                $response["crmMasterData"] = array();
                if (mysqli_num_rows($crmMasterData) > 0) {
                    while ($data_crm_master = mysqli_fetch_array($crmMasterData)) {
                        $crmMaster = array();
                        $crmMaster["society_crm_id"] = $data_crm_master['society_crm_id'];
                        $crmMaster["url"] = $data_crm_master['url'];
                        $crmMaster["server"] = $data_crm_master['server'];
                        $crmMaster["visible_create_request"] = $data_crm_master['visible_create_request'];
                        $crmMaster["backendid"] = $data_crm_master['backendid'];
                        array_push($response["crmMasterData"], $crmMaster);
                    }
                }
                $response["message"] = 'Success';
                $response["status"] = "200";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
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
