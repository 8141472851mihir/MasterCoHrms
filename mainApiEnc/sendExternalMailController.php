<?php 
// this api is used on Chl Website
include_once 'lib.php';
$_POST = json_decode($d->manage_decryption("1", file_get_contents("php://input")), true);
$compress = resolve_api_compress();
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted, [
        "status" => "201",
        "message" => "Invalid Request"
    ], $compress);
    exit();
}
if (isset($_POST) && !empty($_POST)) {
    $response = array();
    extract(array_map("test_input", $_POST));
    if ($key != $keydb) {
        $response["message"] = "Wrong Api Key";
        $response["status"] = "201";
        echo $d->manage_encryption($is_encrypted, $response, $compress);
        exit;
    }
    if (isset($send_email) && $send_email == "send_email") {
        if (isset($email) && $email != "" && isset($subject) && $subject != "" && isset($message) && $message != "") {
            $to = $email ?? "";
            $subject = $subject ?? "";
            $message = $message ?? "";
            include_once '../apAdmin/mail.php';
            $response["message"] = "Email sent successfully.";
            $response["status"] = "200";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
        } else {
            $response["message"] = "Invalid Request";
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
        }
    } else {
        $response["message"] = 'Wrong Tag';
        $response["status"] = "201";
        echo $d->manage_encryption($is_encrypted, $response, $compress);
        exit;
    }
} else {
    $response["message"] = 'Invalid Request';
    $response["status"] = "201";
    echo $d->manage_encryption($is_encrypted, $response, $compress);
    exit;
}
