<?php
include_once 'lib.php';
$txt = "Attachments Delete Cron Start " . date("Y-m-d h:i:s A");
$myfile = file_put_contents('../img/attachmentscronlogs.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
$societyQry = $d->selectRow("society_name,secretary_mobile,sub_domain,society_id,society_settings", "society_master", "society_status = 0 AND society_id=2");

if (mysqli_num_rows($societyQry) > 0) {
    while ($societyData = mysqli_fetch_assoc($societyQry)) {
        $society_name = $societyData['society_name'];
        $mobile = $societyData['secretary_mobile'];
        $society_id = $societyData['society_id'];
        $sub_domain = $societyData['sub_domain'];
        $society_settings_json = $societyData['society_settings'];
        $society_settings = json_decode($society_settings_json, true);

        $face_attendance_delete_days = $society_settings['face_attendance_delete_days'] ?? '';
        $work_report_delete_days = $society_settings['work_report_delete_days'] ?? '';
        $visit_attachment_delete_days = $society_settings['visit_attachment_delete_days'] ?? '';
        $expense_attachment_delete_days = $society_settings['expense_attachment_delete_days'] ?? '';
        $chat_attachment_delete_days = $society_settings['chat_attachment_delete_days'] ?? '';
        $task_attachment_delete_days = $society_settings['task_attachment_delete_days'] ?? '';
        $circular_attachment_delete_days = $society_settings['circular_attachment_delete_days'] ?? '';
        $discussion_attachment_delete_days = $society_settings['discussion_attachment_delete_days'] ?? '';
        $meeting_attachment_delete_days = $society_settings['meeting_attachment_delete_days'] ?? '';
        $tracking_attachment_delete_months = $society_settings['tracking_attachment_delete_months'] ?? '';
        $success_message = deleteFiles($face_attendance_delete_days, "deleteAttendance", $sub_domain, $society_id, $keydb);
        // attendance master
        $success_message = deleteFiles($work_report_delete_days, "deleteWorkReport", $sub_domain, $society_id, $keydb);
        // work report master and work report employee master
        $success_message = deleteFiles($visit_attachment_delete_days, "deleteVisitEnd", $sub_domain, $society_id, $keydb);
        // retailer daily visit master
        $success_message = deleteFiles($expense_attachment_delete_days, "deleteExpense", $sub_domain, $society_id, $keydb);
        // // user expenses
        $success_message = deleteFiles($chat_attachment_delete_days, "deleteChat", $sub_domain, $society_id, $keydb);
        // chat master(pending)
        $success_message = deleteFiles($task_attachment_delete_days, "deleteTask", $sub_domain, $society_id, $keydb);
        // task master & task timeline 
        $success_message = deleteFiles($circular_attachment_delete_days, "deleteCircular", $sub_domain, $society_id, $keydb);
        // notice board master
        $success_message = deleteFiles($discussion_attachment_delete_days, "deleteDiscussion", $sub_domain, $society_id, $keydb);
        // discussion master
        $success_message = deleteFiles($meeting_attachment_delete_days, "deleteMeeting", $sub_domain, $society_id, $keydb);
        // meeting master & meeting doc master
        $success_message = deleteFiles($tracking_attachment_delete_months, "deleteTracking", $sub_domain, $society_id, $keydb);
        // deleteTracking jsons
        $txt = "Attachments Deleted $society_name ($mobile) on " . date("Y-m-d h:i:s A") . " " . $result;
        $myfile = file_put_contents('../img/attachmentscronlogs.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

$txt = "Attachments Delete Cron End " . date("Y-m-d h:i:s A");
$myfile = file_put_contents('../img/attachmentscronlogs.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);

function deleteFiles($days, $deleteAction, $subdomain, $society_id, $keydb)
{
    $target_url = $subdomain . "residentApiNew/societyAnalytics.php";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $target_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    // use deleteOldAttachments if want to delete without sending email
    curl_setopt($ch, CURLOPT_POSTFIELDS, "createAndSendAttachmentBackup=createAndSendAttachmentBackup&society_id=$society_id&language_id=1&deleteAction=$deleteAction&timePeriod=$days");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'key: ' . $keydb
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($result, true);
    $result2 = $json["message"];
    return $result2;
}
