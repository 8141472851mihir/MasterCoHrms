<?php
include '../common/objectController.php';

function renderCompanyDataHtml(array $json)
{
    if (!isset($json['status']) || $json['status'] != '200') {
        $message = isset($json['message']) ? htmlspecialchars($json['message']) : 'Unable to fetch company data.';
        return '<div class="alert alert-danger">' . $message . '</div>';
    }

    $rows = array(
        'Plan Expire Date' => $json['plan_expire_date'] ?? '',
        'Employees' => $json['totalOwners'] ?? 0,
        'Android Employees' => $json['androidUser'] ?? 0,
        'IOS Employees' => $json['iosUsers'] ?? 0,
        'Today Active Employees' => $json['todayActiveUser'] ?? 0,
        'Weekly Active Employees' => $json['weeklyActiveUser'] ?? 0,
        'Monthly Active Employees' => $json['monthActiveUser'] ?? 0,
        'Today Register Employees' => $json['todayRegisterUser'] ?? 0,
        'Pending Employees' => $json['pendingUsers'] ?? 0,
        'Timeline Feed' => $json['newsFeed'] ?? 0,
        'Timeline Feed Today' => $json['newsFeedToday'] ?? 0,
        'Total Attendance' => $json['totalAttendance'] ?? 0,
        'This Week Attendance' => $json['totalAttendanceWeekly'] ?? 0,
        'This Month Attendance' => $json['totalAttendanceThisMonth'] ?? 0,
        'Prev. Month Attendance' => $json['totalAttendancePrevMonth'] ?? 0,
        'Total Work From Home' => $json['totalWorkFromHome'] ?? 0,
        'Total Salary Generated' => $json['totalSalarySlip'] ?? 0,
        'Last Month Salary Generated' => $json['totalSalarySlipMonthly'] ?? 0,
        'Total Leaves' => $json['totalLeaves'] ?? 0,
        'Total Work Report' => $json['totalWorkReport'] ?? 0,
        'This Week Work Report' => $json['totalWorkReportWeek'] ?? 0,
        'This Month Work Report' => $json['totalWorkReportMonth'] ?? 0,
        'Prev. Month Work Report' => $json['totalWorkReportPrvMonth'] ?? 0,
        'Total DAR Work Report' => $json['totalDarWorkReport'] ?? 0,
        'This DAR Week Work Report' => $json['totalDarWorkReportWeek'] ?? 0,
        'This DAR Month Work Report' => $json['totalDarWorkReportMonth'] ?? 0,
        'Prev. DAR Month Work Report' => $json['totalDarWorkReportPrvMonth'] ?? 0,
        'Total Assets' => $json['totalAssets'] ?? 0,
        'Photo Storage' => ($json['photoStorageMb'] ?? 0) . ' MB',
    );

    $html = '<h4>' . htmlspecialchars($json['society_name'] ?? '') . '</h4>';
    $html .= '<table class="table table-bordered">';
    foreach ($rows as $label => $value) {
        $html .= '<tr><th>' . htmlspecialchars($label) . '</th><td>' . htmlspecialchars((string) $value) . '</td></tr>';
    }
    $html .= '</table>';

    return $html;
}

$q = $d->select('society_master', "society_id='$society_id'", 'order by society_id DESC');
$data = mysqli_fetch_array($q);
$baseUrl = $data['sub_domain'];
$society_id = $data['society_id'];

$post = array(
    'getData' => 'getData',
    'society_id' => $society_id,
);

$json = $d->callCompanyApiEnc($baseUrl, 'companyDataController.php', $post);


echo renderCompanyDataHtml(is_array($json) ? $json : array());
