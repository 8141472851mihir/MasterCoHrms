<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include_once 'lib/dao.php';
$d = new dao();
$response = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $getData = file_get_contents('php://input');
    if (!$getData) {
        $response['message'] = "No data available or error while getting data";
        $response['status'] = 201;
        echo json_encode($response);
        exit();
    }
    $data = json_decode($getData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $response['message'] = "Invalid JSON";
        $response['status'] = 201;
        echo json_encode($response);
        exit();
    }
    if (isset($data['alerts']) && is_array($data['alerts'])) {
        // Hoist FCM token lookup once (same for all alerts)
        $tokenResult = $d->selectRow("DISTINCT web_fcm_master.fcm_token", "admin_fcm_notification_master JOIN web_fcm_master ON web_fcm_master.admin_id = admin_fcm_notification_master.bms_admin_id", "admin_fcm_notification_master.fcm_notifications = 8 AND admin_fcm_notification_master.active_status='0'" );
        $getTokens = [];
        if (mysqli_num_rows($tokenResult) > 0) {
            while ($row = mysqli_fetch_array($tokenResult)) {
                $getTokens[] = $row['fcm_token'];
            }
        }
        require_once './fcm_file/admin_fcm.php';
        $firebase = new firebase_admin();

        foreach ($data['alerts'] as $alert) {
            $alertName = isset($alert['labels']['alertname']) ? htmlspecialchars($alert['labels']['alertname'], ENT_QUOTES, 'UTF-8') : 'Not Available';
            $grafanaFolder = isset($alert['labels']['grafana_folder']) ? htmlspecialchars($alert['labels']['grafana_folder'], ENT_QUOTES, 'UTF-8') : 'Not Available';
            $startsAt = isset($alert['startsAt']) ? $alert['startsAt'] : 'Not Available';
            $status = isset($alert['status']) ? $alert['status'] : 'Not Available';
            $valueString = isset($alert['valueString']) ? $alert['valueString'] : 'Not Available';
            $summary = isset($alert['annotations']['summary']) ? $alert['annotations']['summary'] : 'Not Available';
            $status = $status === 'firing' ? 'Alert' : 'Resolved';

            // Convert UTC to IST
            $utcDateTime = new DateTime($startsAt, new DateTimeZone('UTC'));
            $utcDateTime->setTimezone(new DateTimeZone('Asia/Kolkata'));
            $storeISTtoDB = $utcDateTime->format('Y-m-d H:i:s');
            $startsAtIST = $utcDateTime->format('M-d,Y H:i');

            // Extract numeric value from valueString
            $pattern = "/var='A'\s+labels={}\s+value=([0-9\.]+)/";
            preg_match($pattern, $valueString, $matches);
            $value = isset($matches[1]) ? round((float)$matches[1], 2) : 'Not Available';

            // Logging message
            $message = "Alertname: $alertName\n";
            $message .= "Value: $value\n";
            $message .= "StartsAt: $startsAtIST IST\n";
            $message .= "Status: $status\n";
            $message .= "Grafana Folder: $grafanaFolder\n";
            $message .= "Summary: $summary\n";
            $message .= str_repeat('-', 50) . "\n";

            $fcm_status_type = ['Resolved' => 0, 'Alert' => 1];

            $responseData = [
                'fcm_alert_name' => $alertName,
                'fcm_value' => $value,
                'fcm_date' => $storeISTtoDB,
                'fcm_status_type' => $fcm_status_type[$status],
                'fcm_grafana_folder' => $grafanaFolder,
                'fcm_summary' => $summary
            ];

            $webHookFile = '../img/webhook.txt';
            if (file_put_contents($webHookFile, $message, FILE_APPEND)) {

                $storeNotification = $d->insert('grafana_fcm_master', $responseData);

                if (!$storeNotification) {
                    $response['notification'] = "Error to store notification";
                    $response['status'] = 201;
                    echo json_encode($response);
                    exit();
                }
                $title = $status . ": " . $alertName;
                $noti_description = $grafanaFolder . ($status === 'Alert'
                    ? " Alert: $value% \n"
                    : " dropped to $value% \n") . $startsAtIST . " IST";

                $click_action = "";
                $image = "";

                // Send Notification
                $firebase->sendAdminNotification($getTokens, $title, $noti_description, $click_action, $image);

                $response['webHook_message'] = "WebHook Data Added successfully";
                $response['notification_message'] = $message['message'] ?? "Notification sent";
                $response['notification_status'] = $message['status'] ?? 200;
            } else {
                $response['message'] = "Error While Adding WebHook Data";
                $response['status'] = 201;
            }
        }
    } else {
        $response['message'] = "No alerts array found in the data";
        $response['status'] = 201;
    }
} else {
    $response['message'] = "Only POST method allowed";
    $response['status'] = 201;
}

echo json_encode($response);
