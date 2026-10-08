<?php
include_once 'lib.php';
$_POST =  json_decode($d->manage_decryption("1", file_get_contents("php://input")), true);
$compress = resolve_api_compress();
// $_POST =  $d->manage_encryption("1", $_POST);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(201);
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
            $today  = date("Y-m-d");
            if ($_POST['getCard'] == "getCard" && filter_var($society_id, FILTER_VALIDATE_INT) == true) {


                $q = $d->select("visiting_card_master", "active_status=0 AND is_old_card=0", "ORDER BY card_id ASC LIMIT 15");

                if (mysqli_num_rows($q) > 0) {

                    $response["visit_card"] = array();

                    while ($data = mysqli_fetch_array($q)) {
                        $visit_card = array();
                        $visit_card["card_id"] = $data['card_id'];
                        $visit_card["card_bg"] = $base_url . 'img/visitcard/' . $data['card_empty'];
                        $visit_card["card_bg_back"] = $base_url . 'img/visitcard/' . $data['card_bg_back'];
                        $visit_card["card_empty"] = $base_url . 'img/visitcard/' . $data['card_bg'];
                        $visit_card["card_empty_back"] = $base_url . 'img/visitcard/' . $data['card_empty_back'];
                        if ($data['is_logo'] == 0) {
                            $visit_card["is_logo"] = false;
                        } else {
                            $visit_card["is_logo"] = true;
                        }

                        array_push($response["visit_card"], $visit_card);
                    }

                    $response["message"] = $xml->string->data_found . '';
                    $response["status"] = "200";
                    http_response_code(200);
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                } else {
                    $response["message"] = $xml->string->no_data_found_web . '';
                    $response["status"] = "201";
                    http_response_code(201);
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }
            } else if ($_POST['getCardNew'] == "getCardNew" && filter_var($society_id, FILTER_VALIDATE_INT) == true) {


                $q = $d->select("visiting_card_master", "(society_id=0 OR society_id='$society_id') AND  active_status=0 AND is_old_card=1", "ORDER BY card_id ASC LIMIT 15");



                if (mysqli_num_rows($q) > 0) {

                    $response["visit_card"] = array();

                    while ($data = mysqli_fetch_array($q)) {
                        $visit_card = array();
                        $visit_card["card_id"] = $data['card_id'];
                        $visit_card["is_qr_code"] = $data['is_qr_code'];
                        $visit_card["card_bg"] = $base_url . 'img/visitcard/' . $data['card_empty'];
                        $visit_card["card_bg_back"] = $base_url . 'img/visitcard/' . $data['card_bg_back'];
                        $visit_card["card_empty"] = $base_url . 'img/visitcard/' . $data['card_bg'];
                        $visit_card["card_empty_back"] = $base_url . 'img/visitcard/' . $data['card_empty_back'];
                        if ($data['is_logo'] == 0) {
                            $visit_card["is_logo"] = false;
                        } else {
                            $visit_card["is_logo"] = true;
                        }

                        array_push($response["visit_card"], $visit_card);
                    }

                    $response["message"] = $xml->string->data_found . '';
                    $response["status"] = "200";
                    http_response_code(200);
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                } else {
                    $response["message"] = $xml->string->no_data_found_web . '';
                    $response["status"] = "201";
                    http_response_code(201);
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }
            } else {
                $response["message"] = $xml->string->wrong_tag . '';
                $response["status"] = "201";
                http_response_code(201);
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            }
        } else {
            $response["message"] = $xml->string->wrong_api_key . '';
            $response["status"] = "201";
            http_response_code(201);
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit();
        }
    }
} catch (Exception $e) {
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
}
http_response_code(201);
echo $d->manage_encryption($is_encrypted, $response, $compress);
exit();
