<?php
include_once 'common/object.php';
$tokenwhere = "admin_id=1";
$getTokens = $d->getWebFcm("web_fcm_master","$tokenwhere");
$url = "https://fcm.googleapis.com/fcm/send";
// Server Key
$cloud_messaging_server_key  = $d->get_server_key();
// Headers
$request_headers = array(
    "Authorization:" . $cloud_messaging_server_key,
    "Content-Type: application/json"
);
// FCM Tokens
$registration_ids = $getTokens;
// Message
$message = array(
    "title" => "This is Title",
    "body" => "This is Body",
    "icon" => "",
    "click_action" => $base_url."apAdmin/feedback"
);
// Data
$fields = array(
    'registration_ids' => $registration_ids,
    'data' => $message,
);
/** CURL POST code */
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
$season_data = curl_exec($ch);

if (curl_errno($ch)) {
    print "Error: " . curl_error($ch);
    exit();
}
// Show me the result
curl_close($ch);
$json = json_decode($season_data, true);
echo "<pre>";
print_r($json);