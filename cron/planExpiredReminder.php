<?php
include_once 'lib.php';
$logFile = '../img/remindercronlogs.txt';
$logStart = "Email Plan Expiry Reminder Cron Start " . date("Y-m-d h:i:s A") . PHP_EOL;
file_put_contents($logFile, $logStart, FILE_APPEND | LOCK_EX);

$generatedDate = date('Y-m-d');
$daysAfterExpiry = isset($_GET['days_after_expiry']) ? (int)trim($_GET['days_after_expiry']) : 90;
$reminderDays = isset($_GET['reminder_days']) ? (int)trim($_GET['reminder_days']) : 5;
$societies = $d->selectRow("society_id,plan_expire_date,secretary_name, secretary_email, secretary_mobile, society_name, city_name, country_code", "society_master", "society_status = 0  AND STR_TO_DATE(plan_expire_date, '%Y-%m-%d') =  DATE_ADD(CURRENT_DATE(), INTERVAL -$reminderDays DAY)");

if (mysqli_num_rows($societies) > 0) {
    while ($society = mysqli_fetch_array($societies)) {
        $expired_date = date("dS F Y", strtotime($society['plan_expire_date']));
        $givenDate = date("dS F Y", strtotime("+$daysAfterExpiry days", strtotime($society['plan_expire_date'])));
        $to = $society['secretary_email'];
        // $bcc = "bhavesh@chplgroup.org";
        $society_name = $society['society_name'] . ', ' . $society['city_name'];
        $secretary_name = $society['secretary_name'];
        $fullSocietyId = $d->short_app_name() . '_' . $society['society_id'];
        $societyName = $society_name;
        $remainingDays = $daysAfterExpiry - $reminderDays;
        if ($remainingDays <= 0) {
            $remainingTimeMessage = "Your data is scheduled for permanent deletion <b>today</b>.";
        } elseif ($remainingDays == 1) {
            $remainingTimeMessage = "Your data is scheduled for permanent deletion <b>tomorrow</b>.";
        } elseif ($remainingDays <= 10) {
            $remainingTimeMessage = "Your data is scheduled for permanent deletion in <b>$remainingDays days</b>.";
        } else {
            $remainingTimeMessage = "Your data is scheduled for permanent deletion on <b>$givenDate</b>.";
        }
        if ($remainingDays <= 0) {
            $stepsDeadlineMessage = "please take the necessary steps <b>immediately</b>";
        } elseif ($remainingDays == 1) {
            $stepsDeadlineMessage = "please take the necessary steps by <b>tomorrow</b>";
        } elseif ($remainingDays <= 10) {
            $stepsDeadlineMessage = "please take the necessary steps within <b>$remainingDays days</b>";
        } else {
            $stepsDeadlineMessage = "please take the necessary steps before <b>$givenDate</b>";
        }
        $subject = $d->app_name() . " : Final Reminder: Data Deletion Scheduled After Subscription Expiry";
        include '../apAdmin/mail/expiredReminder.php';
        include '../apAdmin/mail.php';
        $logEntry = "Reminder sent to $to for $societyName on " . date("Y-m-d h:i:s A") . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
} else {
    file_put_contents($logFile, "No societies found for reminder.\n", FILE_APPEND | LOCK_EX);
}

$logEnd = "Email Plan Expiry Reminder Cron End " . date("Y-m-d h:i:s A") . PHP_EOL;
file_put_contents($logFile, $logEnd, FILE_APPEND | LOCK_EX);
