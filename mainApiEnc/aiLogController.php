<?php
include_once 'lib.php';
$_POST = json_decode($d->manage_decryption("1", file_get_contents("php://input")), true);
$compress = resolve_api_compress();
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted, [
        "status" => "201",
        "message" => $xml->string->invalid_request . ""
    ], $compress);
    exit();
}
try {
    if (isset($_POST) && !empty($_POST)) {
        if ($key == $keydb) {
            $response = array();
            extract(array_map("test_input", $_POST));

            if (isset($addAIError) && $addAIError == "addAIError") {
                if (!isset($company_id) || filter_var($company_id, FILTER_VALIDATE_INT) != true) {
                    $response["message"] = $xml->string->please_provide_required_data . "";
                    $response["status"] = "201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }

                if ((!isset($chat_history) || trim($chat_history) == "") && (!isset($log_history) || trim($log_history) == "")) {
                    $response["message"] = $xml->string->please_provide_required_data . "";
                    $response["status"] = "201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }

                $log_from = (isset($log_from) && $log_from !== "") ? $log_from : "0";
                if (!in_array($log_from, ["0", "1"], true)) {
                    $response["message"] = $xml->string->invalid_request . "";
                    $response["status"] = "201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }

                $user_id = (isset($user_id) && filter_var($user_id, FILTER_VALIDATE_INT) == true) ? $user_id : "0";
                $user_mobile = isset($user_mobile) ? $user_mobile : "";
                $user_name = isset($user_name) ? $user_name : "";
                $chat_history = isset($chat_history) ? $chat_history : "";
                $log_history = isset($log_history) ? $log_history : "";
                $created_date = date("Y-m-d H:i:s");

                $m->set_data('company_id', $company_id);
                $m->set_data('user_id', $user_id);
                $m->set_data('user_mobile', $user_mobile);
                $m->set_data('user_name', $user_name);
                $m->set_data('chat_history', $chat_history);
                $m->set_data('log_history', $log_history);
                $m->set_data('log_from', $log_from);
                $m->set_data('status', "0");
                $m->set_data('is_deleted', "0");
                $m->set_data('created_date', $created_date);

                $a = array(
                    'company_id' => $m->get_data('company_id'),
                    'user_id' => $m->get_data('user_id'),
                    'user_mobile' => $m->get_data('user_mobile'),
                    'user_name' => $m->get_data('user_name'),
                    'chat_history' => $m->get_data('chat_history'),
                    'log_history' => $m->get_data('log_history'),
                    'log_from' => $m->get_data('log_from'),
                    'status' => $m->get_data('status'),
                    'is_deleted' => $m->get_data('is_deleted'),
                    'created_date' => $m->get_data('created_date'),
                );

                $q = $d->insert("ai_error_log_master", $a);
                if ($q == true) {
                    $response["ai_error_log_id"] = $con->insert_id . "";
                    $response["message"] = $xml->string->thank_you_for_your_feedback . "";
                    $response["status"] = "200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }

                $response["message"] = $xml->string->something_wrong . "";
                $response["status"] = "201";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            }

            $response["message"] = $xml->string->wrong_tag . "";
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit();
        }

        $response["message"] = $xml->string->wrong_api_key . "";
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
