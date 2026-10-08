<?php

include_once 'lib.php';


$logFile = '../img/supportReplyNotificationLogs.txt';

$logStart = PHP_EOL . PHP_EOL .
    "===== Support Reply Notification Cron Started : " . date("Y-m-d h:i:s A") . " =====" . PHP_EOL;

file_put_contents($logFile, $logStart, FILE_APPEND | LOCK_EX);

$q = $d->selectRow(
    "feedback_master.feedback_id,feedback_master.society_id,feedback_master.mobile,feedback_master.subject,feedback_master.feedback_status,feedback_master.feedback_msg,feedback_master.created_by,feedback_master.with_developer,feedback_master.isTicket,feedback_master.society_id AS ticket_society_id,feedback_log_master.feedback_log,feedback_log_master.feedback_added_by,feedback_log_master.feedback_log_date,bms_admin_master.admin_id,bms_admin_master.admin_name,society_master.sub_domain",
    "feedback_master
        LEFT JOIN feedback_log_master
        ON feedback_log_master.feedback_log_id = (
            SELECT feedback_log_id
            FROM feedback_log_master
            WHERE feedback_log_master.feedback_id = feedback_master.feedback_id
            AND feedback_log_master.log_added_type = 0
            ORDER BY feedback_log_id DESC
            LIMIT 1
        )
        LEFT JOIN bms_admin_master ON feedback_log_master.feedback_added_by = bms_admin_master.admin_id
        LEFT JOIN society_master ON feedback_master.society_id = society_master.society_id",
    "feedback_master.isTicket = 1
        AND feedback_master.with_developer = 1
        AND feedback_master.created_by = 0
        AND society_master.society_status = 0
        AND feedback_log_master.feedback_log_date IS NOT NULL
        AND TIMESTAMPDIFF(HOUR,feedback_log_master.feedback_log_date,NOW()) >= 24",

    "ORDER BY feedback_master.feedback_id DESC"

);

if (mysqli_num_rows($q) > 0) {

    while ($query_data = mysqli_fetch_assoc($q)) {
        $feedback_id = $query_data['feedback_id'];
        $society_id = $query_data['ticket_society_id'];
        $admin_id = $query_data['admin_id'];
        $feedback_created_by = $query_data['admin_id'];
        $feedback_added_by = $query_data['feedback_added_by'];
        $user_mobile = $query_data['mobile'];
        $hit_url = $query_data['sub_domain'];

        // $todayEntry = $d->count_data_direct( "feedback_log_id", "feedback_log_master","feedback_id = '$feedback_id' AND log_added_type = 0 AND DATE(feedback_log_date) = CURDATE() AND feedback_log LIKE '%currently under review%'");

        // if($todayEntry == 0){

        // $q = $d->update("feedback_master", array('feedback_status' => '1'), "feedback_id='$feedback_id'");

        $reply = "We are still working on the reported issue and will update you as soon as possible.";

        $a1 = array(
            'feedback_id' => $feedback_id,
            'society_id' => $society_id,
            'feedback_log' => $reply,
            'feedback_log_status' => '1',
            'feedback_msg_status' => '1',
            'feedback_log_attachment' => '',
            'feedback_log_date' => date('Y-m-d H:i:s'),
            'feedback_added_by' => $feedback_added_by,
            'log_added_type' => '0',
            'hide_to_user' => '0'
        );

        $ticket_id = $feedback_id;
        $reply_message = $reply;
        $feedback_status = $feedback_status;
        $user_mobile_no = $user_mobile;
        include '../feedbackFcmCurl.php';

        $insertReply = $d->insert("feedback_log_master", $a1);

        $d->insert_log_specific("$society_id", "$admin_id", "$created_by", "Feedback #TKT$feedback_id Reply", "6");

        $notiAry = array(
            'admin_id' => $feedback_created_by,
            'society_id' => $society_id,
            'notification_tittle' => "Reply for Feedback - #TKT$feedback_id",
            'notification_description' => $reply,
            'notifiaction_date' => date('Y-m-d H:i'),
            'admin_click_action' => 'feedbackTimeline?id=' . $feedback_id,
        );

        $d->insert("admin_notification", $notiAry);

        $noti_title = "Developer Team Update - Ticket #TKT$feedback_id";
        $noti_description = $reply;

        $post = array(
            'send_notification' => 'send_notification_ticket',
            'title' => $noti_title,
            'description' => $noti_description,
            'society_id' => $society_id,
            'mobile_no' => $user_mobile,
            'feedback_id' => $feedback_id,
        );
        $d->callCompanyApiEnc($hit_url, 'sendNotificationCurlController.php', $post);


        $has_permission = $d->count_data_direct("fcm_id", "admin_fcm_notification_master", "bms_admin_id='$feedback_created_by' AND fcm_notifications='1' AND active_status='0'");
        if ($feedback_created_by > 0 && $has_permission > 0) {
            $noti_title = "" . $d->app_name() . " #TKT$feedback_id new reply";
            $noti_description = $reply;
            $click_action = "feedbackTimeline?id=$feedback_id";
            $getTokens = $d->getWebFcm("web_fcm_master", "admin_id='$feedback_created_by'");
            $nAdmin->sendAdminNotification($getTokens, $noti_title, $noti_description, $click_action);
        }

        if ($insertReply) {
            $logEntry = "SUCCESS | Ticket #TKT$feedback_id | Auto Developer Progress Reply Added Successfully | Time : " . date("Y-m-d h:i:s A") . PHP_EOL;
        } else {
            $logEntry = "FAILED | Ticket #TKT$feedback_id | Failed To Add Auto Developer Progress Reply | Time : " . date("Y-m-d h:i:s A") . PHP_EOL;
        }

        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        // } else {
        //     $logEntry = "SKIPPED | Ticket #TKT$feedback_id | Already Updated Today | Time : " . date("Y-m-d h:i:s A") . PHP_EOL;
        //     file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        // }

    }
} else {
    $logEntry = "INFO | No Developer Tickets Found | Time : " . date("Y-m-d h:i:s A") . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

$logEnd = "===== Support Reply Notification Cron Ended : " . date("Y-m-d h:i:s A") . " =====" . PHP_EOL;

file_put_contents($logFile, $logEnd, FILE_APPEND | LOCK_EX);
