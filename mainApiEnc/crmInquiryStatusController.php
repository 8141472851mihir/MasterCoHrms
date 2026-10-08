<?php 
// used on myco website to get the status of the inquiry deletion request
include_once 'lib.php';
$is_encrypted = 0;
try {
    if (isset($_POST) && !empty($_POST)) {
        extract($_POST);
        $compress = resolve_api_compress();
        $response = array();
        if ($_POST['crmInquiryStatus'] == "crmInquiryStatus") {
            if ((isset($user_id) && $user_id != "")) {
                $user_id = isset($user_id) ? $user_id : "";
                $crmInquiryStatusQry = $d->selectRow("crm_inquiry_status", "crm_inquiry_deletion_requests", "confirmation_code = '$user_id' OR user_id = '$user_id'");
                $statusMap=['0'=>'Pending','1'=>'In Progress','2'=>'Complete','3'=>'Rejected'];
                if (mysqli_num_rows($crmInquiryStatusQry) > 0) {
                    $crmInquiryStatus = mysqli_fetch_array($crmInquiryStatusQry);
                    $response["crmInquiryStatus"] = $statusMap[$crmInquiryStatus['crm_inquiry_status']];
                    $response["message"] = $xml->string->success . '';
                    $response["status"] = "200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                } else {
                    $response["message"] = $xml->string->no_data_found_web . '';
                    $response["status"] = "201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }
            } else {
                $response["message"] = $xml->string->please_provide_required_data . '';
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
} catch (Exception $e) {
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
}
echo $d->manage_encryption($is_encrypted, $response, $compress);
exit();
