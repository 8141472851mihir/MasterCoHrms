<?php
include_once 'lib.php';
$is_encrypted = 0;
if (isset($_POST) && !empty($_POST)) {
    $response = array();
    extract(array_map("test_input", $_POST));
    $compress = resolve_api_compress();
    if ($key != $keydb) {
        $response["message"] = "Wrong Api Key";
        $response["status"] = "201";
        echo $d->manage_encryption($is_encrypted, $response, $compress);
        exit();
    }
    if (isset($getSoceietyData)) {
        $today = date("Y-m-d");
        $success_array = array();
        $ids = join("','", $success_array);
        $society_master_qry = $d->select("society_master", " society_id  ='$society_id' AND society_id NOT IN ('$ids') ");
        if (mysqli_num_rows($society_master_qry) > 0) {
            while ($society_master_data = mysqli_fetch_array($society_master_qry)) {
                $society_id = $society_master_data['society_id'];
                $keydb = $society_master_data['api_key'];
                $target_url = $society_master_data['sub_domain'] . "residentApiNew/societyAnalytics.php";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $target_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "buildingDetails=buildingDetails&society_id=$society_id&language_id=1");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'key: ' . $keydb
                ));
                $result = curl_exec($ch);
                curl_close($ch);
                $json = json_decode($result, true);
                if(isset($json["message"]) && isset($json["status"]) && $json["status"]==200){
                    $result2 = $json["message"];
                    $result3 = $json["status"];
                    require_once "../apAdmin/companyDataCommon.php";
                }
                if ((!isset($result2) || $result2== "") && (!isset($result3) || $result3 != "200")) {
                    $response["message"] = "- No Response:201";
                    $response["status"] = "201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                } else {
                    $json["message"] = ($json["message"] != "") ? $json["message"] : "Success";
                    $response["message"] = $json["message"] . ':' . $json["status"] . '';
                    $response["status"] = "200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }
            }
        } else {
            $response["message"] = "Society not found";
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit();
        }
    } else {
        $response["message"] = $xml->string->wrong_tag . '';
        $response["status"] = "201";
        echo $d->manage_encryption($is_encrypted, $response, $compress);
        exit();
    }
}
