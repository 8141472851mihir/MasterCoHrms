<?php
include_once 'lib.php';
$txt = "Whats App Plan Exrpire Reminder Cron Start " . date("Y-m-d h:i:s A");
$myfile = file_put_contents('../img/remindercronlogs.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
$generatedDate = date('Y-m-d');
$whatsAppConfigQuery = $d->select("whatsapp_configuration", "");
$whatsAppConfigData = mysqli_fetch_array($whatsAppConfigQuery);
$configuration_id = $whatsAppConfigData['configuration_id'];
$configuration_password = $whatsAppConfigData['configuration_password'];


$societyQry = $d->selectRow("society_id,plan_expire_date,secretary_name, secretary_mobile,secretary_email, society_name, city_name, country_code", "society_master", "society_status = 0 AND (TIMESTAMPDIFF(DAY, '$generatedDate', plan_expire_date) = 1 OR TIMESTAMPDIFF(DAY, '$generatedDate', plan_expire_date) = 5 OR TIMESTAMPDIFF(DAY, '$generatedDate', plan_expire_date) = 15) ");

if (mysqli_num_rows($societyQry) > 0) {
    while ($societyData = mysqli_fetch_assoc($societyQry)) {

        $header = "Subscription Reminder";
        $plan_expire_date = date("dS F Y", strtotime($societyData['plan_expire_date']));
        $society_name = $societyData['society_name'] . ', ' . $societyData['city_name'];
        $society_name_new = $societyData['society_name'];
        $mobile = $societyData['country_code'] . $societyData['secretary_mobile'];
        $secretary_mobile = $societyData['secretary_mobile'];
        $country_code = $societyData['country_code'];


        $message = "Hi there! 👋\nJust a friendly reminder that your subscription to " . $d->app_name() . " is expiring on *$plan_expire_date*. Don't miss out on uninterrupted access to all the amazing features and updates! Renew your subscription today to keep enjoying seamless functionality.\nThanks for being part of " . $d->app_name() . " community!";
        $email_message = $message;
        $mobile = urlencode($mobile);
        $header = urlencode($header);
        $message = urlencode($message);

        // $sms = file_get_contents("https://media.smsgupshup.com/GatewayAPI/rest?userid=$configuration_id&password=$configuration_password&send_to=$mobile&v=1.1&format=json&msg_type=TEXT&method=SENDMESSAGE&msg=$message&isTemplate=true&header=$header");

        // $result = send_whatsapp($mobile, $plan_expire_date);
        $result = $sms_api->sendWhatsAppMessage($secretary_mobile, "54", "1156918396251781", [$plan_expire_date], [], $d, "$country_code");
        $today = new DateTime();
        $expiry_date = new DateTime($plan_expire_date);
        $interval = $today->diff($expiry_date);
        $days_remaining = $interval->days;
        $days_remaining = $days_remaining + 1;

        if ($days_remaining > 10) {
            $expiry_text = "set to expire on <b>$plan_expire_date</b>";
        } elseif ($days_remaining == 1) {
            $expiry_text = "expiring <b>tomorrow</b>";
        } elseif ($days_remaining == 0) {
            $expiry_text = "expiring <b>today</b>";
        } else {
            $expiry_text = "expiring in <b>$days_remaining days</b>";
        }
        $to = $societyData['secretary_email'];
        $secretary_name = $societyData['secretary_name'];
        $societyName = $society_name;
        $fullSocietyId = $d->short_app_name() . '_' . $societyData['society_id'];
        $subject = "Gentle Reminder: Your Subscription Is Due for Renewal";
        include '../apAdmin/mail/expiryAlert.php';
        include '../apAdmin/mail.php';
        $txt = "Subscription Reminder Send to $society_name ($mobile) on " . date("Y-m-d h:i:s A") . " " . $result;

        $myfile = file_put_contents('../img/remindercronlogs.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

$txt = "Whats App Plan Exrpire Reminder Cron End " . date("Y-m-d h:i:s A");
$myfile = file_put_contents('../img/remindercronlogs.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);

function send_whatsapp($mobileno, $plan_expire_date)
{
    $accessToken = "EAAJli8MaKs4BOxIHFkiPYMfsiMpQdOnCscZBPEj2EVs7NALMUIJFZA9KvqF0rA2kMZCqNsn2PHjJmAvvpKZAQAU5FM2Aeo4uXu2KzglyqxNdfZCCYftVWm15si0m9VlmF8bEPC11bcfuNuXsTdessmwjnJA1uwwhpesAAHfFSau5Pcox8qatWpvpspj16Q6tcRwZDZD";
    $phoneNumberID = "459191870620488"; // Get this from the WhatsApp Business settings
    $recipientPhone = $mobileno; // Format: "country_code+phone_number"
    // $recipientPhone = "+919737564998" ; // Format: "country_code+phone_number"
    $templatename = "myco_subscription_renewal";
    //$invoiceNumber="inv-0025/24-25";


    // Prepare the API URL
    $url = "https://graph.facebook.com/v21.0/$phoneNumberID/messages";

    // Prepare the message payload
    $data = [
        "messaging_product" => "whatsapp",
        "recipient_type" => "individual",
        "to" => $recipientPhone,
        "type" => "template",
        "template" => [
            "name" => $templatename,
            "language" => ["code" => "en"],
            "components" => [
                [
                    'type' => 'body',
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => $plan_expire_date,
                        ]

                    ],
                ],
            ],
        ],
    ];

    // Initialize CURL for API request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute CURL request
    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpStatus === 200) {
        // echo "Message sent successfully: $response";
    } else {
        // echo "Failed to send message. Status: $httpStatus, Response: $response";
    }
    return $response;
}
