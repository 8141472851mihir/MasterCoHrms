<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include_once 'lib.php';
$compress = resolve_api_compress();
include_once '../apAdmin/timeline_functions.php';
$rawData =  json_decode(file_get_contents("php://input"), true);

$is_encrypted = 0;
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted, [
        "status" => "200",
        "data" => 'Invalid Body Data'
    ], $compress);
    exit();
}
try {
    if (isset($_GET) && !empty($_GET)) {
        $response = array();
        extract(array_map("test_input", $_GET));
        if (isset($type) && ($type == "stop" || $type == "unsubscribe")) {
            $logFile = '../img/whatsapp_unsubscribing_logs.txt';
            $logContent = '';
            $user_mobile = $rawData['mobile'];
            $waba_phone_number = $rawData['waba_phone_number'];
            $logContent .= "======== WhatsApp Unsubscribing Request at " . date("Y-m-d H:i:s") . " ========\n User Mobile: $user_mobile | Waba Phone Number: $waba_phone_number\n";
            //check if already unsubscribed
            $checkQ = $d->select("whatsapp_unsubscribe_master", "user_mobile = '$user_mobile' AND waba_phone_number = '$waba_phone_number'");
            if (mysqli_num_rows($checkQ) > 0) {
                $logContent .= "======== Already Unsubscribed ========\n";
                $message = "\n*ℹ️ Already Unsubscribed*\n";
                $message .= "\n";
                $message .= "You are *already unsubscribed* from receiving messages and reports.\n";
                $message .= "No further action is required.\n";
            } else {
                $logContent .= "======== Unsubscribing Process Started ========\n";
                $user_mobile_clean = preg_replace('/\D/', '', $user_mobile);
                $societyQry = $d->selectRow(
                    "whatsapp_access_master.society_id, whatsapp_access_master.user_full_name, whatsapp_access_master.country_code, whatsapp_access_master.mobile_number_only, society_master.society_name, society_master.sub_domain, society_master.api_key",
                    "whatsapp_access_master LEFT JOIN society_master ON whatsapp_access_master.society_id = society_master.society_id",
                    "whatsapp_access_master.user_mobile='$user_mobile_clean' AND whatsapp_access_master.society_id IS NOT NULL"
                );
                $societies = [];
                if(mysqli_num_rows($societyQry) > 0){
                    while ($societyData = mysqli_fetch_assoc($societyQry)) {
                        $society_id = $societyData['society_id'];
                        $mobile_number_only = $societyData['mobile_number_only'];
                        $sub_domain = $societyData['sub_domain'];
                        $api_key = $societyData['api_key'];
                        $user_full_name = $societyData['user_full_name'];
                        $country_code = $societyData['country_code'];
                        $target_url = $sub_domain . "residentApiNew/societyAnalytics.php";
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => $target_url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => array('removeWhatsappAlerts' => 'removeWhatsappAlerts', 'society_id' => $society_id, 'mobile_number_only' => $mobile_number_only),
                            CURLOPT_HTTPHEADER => array(
                                'key: ' . $api_key
                            ),
                        ));
                        $crul_response = curl_exec($curl);
                        curl_close($curl);
                        $json = json_decode($crul_response, true);

                        if ($json && isset($json['status']) && $json['status'] == 200) {
                            $logContent .= "SUCCESS: user_mobile: $mobile_number_only | society_id: $society_id | Name: $user_full_name | Country: $country_code | API Response: " . json_encode($json) . "\n";
                        } else {
                            $logContent .= "FAIL: user_mobile: $mobile_number_only | society_id: $society_id | Name: $user_full_name | Country: $country_code | API Response: " . json_encode($json) . "\n";
                        }
                    }
                }else{
                    $logContent .= "======== No Societies Found ========\n";
                }

                $insert_qry = $d->insert("whatsapp_unsubscribe_master", [
                    "user_mobile" => $user_mobile,
                    "waba_phone_number" => $waba_phone_number,
                    "unsubscribed_on" => date("Y-m-d H:i:s")
                ]);
                if ($insert_qry) {
                    $message  = "\n*✅ Unsubscription Successful*\n";
                    $message .= "\n";
                    $message .= "You have been *successfully unsubscribed* from receiving further messages and reports.\n";
                    $message .= "Thank you for being with us!\n";
                    $message .= "If you wish to receive updates again in the future, please type *SUBSCRIBE* or *START*.\n";
                } else {
                    $message  = "\n*⚠️ Unsubscription Failed*\n";
                    $message .= "\n";
                    $message .= "We were *unable to process your unsubscription request* at this time.\n";
                    $message .= "Please contact the *Admin Team* for further assistance.\n";
                    $message .= "Thank you for your understanding.\n";
                }
                $logContent .= "======== Unsubscribing Process Ended ========\n";
                file_put_contents($logFile, $logContent, FILE_APPEND | LOCK_EX);
            }
            $response["data"] = $message;
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit;
        } else if (isset($type) && ($type == "subscribe" || $type == "start")) {
            $user_mobile = $rawData['mobile'];
            $waba_phone_number = $rawData['waba_phone_number'];
            // Check if user is unsubscribed
            $checkQ = $d->select("whatsapp_unsubscribe_master", "user_mobile = '$user_mobile' AND waba_phone_number = '$waba_phone_number'");
            
            if (mysqli_num_rows($checkQ) == 0) {
                // User is already subscribed
                $message = "\n*ℹ️ Already Subscribed*\n";
                $message .= "\n";
                $message .= "You are *already subscribed* to receive messages and reports.\n";
                $message .= "No further action is required.\n";
            } else {
                // Remove user from unsubscribe table
                $delete_qry = $d->delete("whatsapp_unsubscribe_master", "user_mobile = '$user_mobile' AND waba_phone_number = '$waba_phone_number'");
                if ($delete_qry) {
                    $message  = "\n*✅ Subscription Successful*\n";
                    $message .= "\n";
                    $message .= "You have been *successfully resubscribed* to receive messages and reports.\n";
                    $message .= "Welcome back!\n";
                } else {
                    $message  = "\n*⚠️ Subscription Failed*\n";
                    $message .= "\n";
                    $message .= "We were *unable to process your subscription request* at this time.\n";
                    $message .= "Please contact the *Admin Team* for further assistance.\n";
                }
            }
            
            $response["data"] = $message;
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit;
        }
         else {
            $response["data"] = $xml->string->wrong_tag . '';
            $response["status"] = "200";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit;
        }
    } else {
        $response["data"] = $xml->string->invalid_method . '';
        $response["status"] = "200";
        echo $d->manage_encryption($is_encrypted, $response, $compress);
        exit;
    }
} catch (Exception $e) {
    $response['status'] = "500";
    $response["data"] = $e->getMessage();
}
echo $d->manage_encryption($is_encrypted, $response, $compress);
exit();
