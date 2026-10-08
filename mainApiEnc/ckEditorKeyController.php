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
            if ($_POST['getCkEditorKey'] == "getCkEditorKey") {
                if (isset($company_id) && $company_id != "") {
                    $response["key"] = 'eyJhbGciOiJFUzI1NiJ9.eyJleHAiOjE4MDcxNDIzOTksImp0aSI6IjM1NGZlNjBmLTFmNWYtNDQyOC1iMDFkLTU4MzJhOTEyYjg1NSIsImxpY2Vuc2VkSG9zdHMiOlsiKi5teS1jb21wYW55LmFwcCIsIioubXktY28uYXBwIl0sInVzYWdlRW5kcG9pbnQiOiJodHRwczovL3Byb3h5LWV2ZW50LmNrZWRpdG9yLmNvbSIsImRpc3RyaWJ1dGlvbkNoYW5uZWwiOlsiY2xvdWQiLCJkcnVwYWwiXSwiZmVhdHVyZXMiOlsiRFJVUCIsIkUyUCIsIkUyVyJdLCJyZW1vdmVGZWF0dXJlcyI6WyJQQiIsIlJGIiwiU0NIIiwiVENQIiwiVEwiLCJUQ1IiLCJJUiIsIlNVQSIsIkI2NEEiLCJMUCIsIkhFIiwiUkVEIiwiUEZPIiwiV0MiLCJGQVIiLCJCS00iLCJGUEgiLCJNUkUiXSwidmMiOiIwYmYxYzA0MCJ9.X-rBaFUtK3qYX_96ffk0QlK0HO1e0Q6xlxNyhuMaaYOnesX_RApgk2i2KcWlHdNx7PRW8oymajJtEcSJAYluQA';
                    $response["message"] = 'Success';
                    $response["status"] = "200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }
                $response["message"] = 'Company Id Not Found';
                $response["status"] = "201";
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
