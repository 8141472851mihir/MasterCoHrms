<?php
include_once 'common/object.php';
$type = $_GET['type'] ?? '';
$cityId = isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : 0;
$stateId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
$cityId = $cityId > 0 ? $cityId : null;
$stateId = $stateId > 0 ? $stateId : null;
$title = $_GET['title'] ?? '';

$attendance_best = isset($_GET['attendanceBest']) ? (float)$_GET['attendanceBest'] : 85;
$attendance_average = isset($_GET['attendanceAvg']) ? (float)$_GET['attendanceAvg'] : 60;
$attendance_low = isset($_GET['attendanceLow']) ? (float)$_GET['attendanceLow'] : 35;

$payroll_best = isset($_GET['payrollBest']) ? (float)$_GET['payrollBest'] : 85;
$payroll_average = isset($_GET['payrollAvg']) ? (float)$_GET['payrollAvg'] : 60;
$payroll_low = isset($_GET['payrollLow']) ? (float)$_GET['payrollLow'] : 35;

$tracking_best = isset($_GET['trackingBest']) ? (float)$_GET['trackingBest'] : 85;
$tracking_average = isset($_GET['trackingAvg']) ? (float)$_GET['trackingAvg'] : 60;
$tracking_low = isset($_GET['trackingLow']) ? (float)$_GET['trackingLow'] : 35;

$workReport_best = isset($_GET['workReportBest']) ? (float)$_GET['workReportBest'] : 85;
$workReport_average = isset($_GET['workReportAvg']) ? (float)$_GET['workReportAvg'] : 60;
$workReport_low = isset($_GET['workReportLow']) ? (float)$_GET['workReportLow'] : 35;

$where = "sm.created_on_society_server = 1 AND sm.society_id != ''";

if (!empty($cityId)) {
    $where .= " AND sm.city_id = '$cityId'";
}
if (!empty($stateId)) {
    $where .= " AND sm.state_id = '$stateId'";
}

switch ($type) {
    case 'totalCompanies':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id
             LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'totalLastYearCompanies':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id 
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
             LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND YEAR(sm.created_date) = YEAR(CURDATE()) - 1 
             AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'renwedThisMonth':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id 
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
             INNER JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND t.is_renewal = 1 
             AND t.payment_status = 'success' 
             AND MONTH(t.transection_date) = MONTH(CURDATE()) 
             AND YEAR(t.transection_date) = YEAR(CURDATE()) 
             AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'renwedThisYear':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id 
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
             INNER JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND t.is_renewal = 1 
             AND t.payment_status = 'success' 
             AND YEAR(t.transection_date) = YEAR(CURDATE()) 
             AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'lostThisMonth':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id 
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
             LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND MONTH(sm.plan_expire_date) = MONTH(CURDATE()) 
             AND YEAR(sm.plan_expire_date) = YEAR(CURDATE()) 
             AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'lostThisYear':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id 
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
             LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND YEAR(sm.plan_expire_date) = YEAR(CURDATE()) 
             AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'cardRefundThisMonth':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date,bm.admin_id,bm.admin_name as refund_person_name",
            "society_master sm
                 LEFT JOIN cities c ON sm.city_id = c.city_id 
                 LEFT JOIN states s ON sm.state_id = s.state_id 
                 LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
                 LEFT JOIN transection_master t ON sm.society_id = t.society_id
                 LEFT JOIN bms_admin_master bm ON sm.refund_person = bm.admin_id",
            "$where AND sm.refund_status = '1' 
             AND MONTH(sm.created_date) = MONTH(CURDATE()) 
             AND YEAR(sm.created_date) = YEAR(CURDATE())",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'cardRefundThisYear':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
                 LEFT JOIN cities c ON sm.city_id = c.city_id 
                 LEFT JOIN states s ON sm.state_id = s.state_id 
                 LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
                 LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND sm.refund_status = '1' 
             AND YEAR(sm.created_date) = YEAR(CURDATE())",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'totalTrialThisMonth':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id 
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
             LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND sm.package_id = '0' 
             AND MONTH(sm.created_date) = MONTH(CURDATE()) 
             AND YEAR(sm.created_date) = YEAR(CURDATE())",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'totalTrialThisYear':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id 
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
             LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND sm.package_id = '0' 
             AND YEAR(sm.created_date) = YEAR(CURDATE())",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'trialToRenewThisMonth':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id 
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
             INNER JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND sm.package_id = 0 
             AND t.is_renewal = 1 
             AND MONTH(t.transection_date) = MONTH(CURDATE()) 
             AND YEAR(t.transection_date) = YEAR(CURDATE()) 
             AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'trialToRenewThisYear':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id 
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
             INNER JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND sm.package_id = 0 
             AND t.is_renewal = 1 
             AND YEAR(t.transection_date) = YEAR(CURDATE()) 
             AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'expiringThisMonth':

        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
     LEFT JOIN cities c ON sm.city_id = c.city_id 
     LEFT JOIN states s ON sm.state_id = s.state_id 
     LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
     LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND MONTH(sm.plan_expire_date) = MONTH(CURDATE()) 
     AND YEAR(sm.plan_expire_date) = YEAR(CURDATE()) 
     AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'expiringThisYear':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id 
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
             LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND YEAR(sm.plan_expire_date) = YEAR(CURDATE()) 
             AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'totalDoneCompanies':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id 
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
             LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND sm.training_status = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'totalPendingCompanies':

        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id 
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
             LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND IFNULL(sm.training_data, '') = ''  
             AND sm.created_on_society_server = 1 
             AND sm.training_status = 0",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'totalOngoingCompanies':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id 
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id 
             LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND IFNULL(sm.training_data, '') != ''  
             AND sm.created_on_society_server = 1 
             AND sm.training_status = 0",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'payrollUsingCompanies':

        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
     LEFT JOIN cities c ON sm.city_id = c.city_id 
     LEFT JOIN states s ON sm.state_id = s.state_id 
     INNER JOIN society_analytics_master a ON sm.society_id = a.society_id 
     LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND a.last_month_payroll_count > 0 
     AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    case 'attendanceBestUsage':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
                 LEFT JOIN cities c ON sm.city_id = c.city_id 
                 LEFT JOIN states s ON sm.state_id = s.state_id 
                 INNER JOIN society_analytics_master a ON sm.society_id = a.society_id 
                 LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND (
                        CASE 
                            WHEN sm.employee_registration_limit != 0 
                            THEN ROUND((a.current_month_attendance_count / (sm.employee_registration_limit * 26)) * 100, 2)
                            ELSE ROUND((a.current_month_attendance_count / (a.total_users * 26)) * 100, 2)
                        END) >= $attendance_best
                AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );

        break;

    case 'attendanceAvgUsage':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date
            ,a.last_month_attendance_count, a.second_last_month_attendance_count, a.current_month_attendance_count",
            "society_master sm
                 LEFT JOIN cities c ON sm.city_id = c.city_id 
                 LEFT JOIN states s ON sm.state_id = s.state_id 
                 INNER JOIN society_analytics_master a ON sm.society_id = a.society_id 
                 LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND (
            CASE 
                WHEN sm.employee_registration_limit != 0 
                THEN ROUND((a.current_month_attendance_count / (sm.employee_registration_limit * 26)) * 100, 2)
                ELSE ROUND((a.current_month_attendance_count / (a.total_users * 26)) * 100, 2)
            END
            ) >= $attendance_average 
            AND (
            CASE 
                WHEN sm.employee_registration_limit != 0 
                THEN ROUND((a.current_month_attendance_count / (sm.employee_registration_limit * 26)) * 100, 2)
                ELSE ROUND((a.current_month_attendance_count / (a.total_users * 26)) * 100, 2)
                END
            ) < $attendance_best 
            AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );

        break;

    case 'attendanceLowUsage':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date
            ,a.last_month_attendance_count, a.second_last_month_attendance_count, a.current_month_attendance_count",
            "society_master sm
                 LEFT JOIN cities c ON sm.city_id = c.city_id 
                 LEFT JOIN states s ON sm.state_id = s.state_id 
                 INNER JOIN society_analytics_master a ON sm.society_id = a.society_id 
                 LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND (
                        CASE 
                            WHEN sm.employee_registration_limit != 0 
                            THEN ROUND((a.current_month_attendance_count / (sm.employee_registration_limit * 26)) * 100, 2)
                            ELSE ROUND((a.current_month_attendance_count / (a.total_users * 26)) * 100, 2)
                        END) <= $attendance_low",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );

        break;

    case 'payrollBestUsage':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date
            ,a.last_month_payroll_count, a.second_last_month_payroll_count, a.current_month_payroll_count",
            "society_master sm
                 LEFT JOIN cities c ON sm.city_id = c.city_id 
                 LEFT JOIN states s ON sm.state_id = s.state_id 
                 INNER JOIN society_analytics_master a ON sm.society_id = a.society_id 
                 LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND (
                        CASE 
                            WHEN a.total_users != 0 
                            THEN ROUND((a.current_month_payroll_count / a.total_users) * 100, 2)
                            ELSE 0
                        END) >= $payroll_best 
                AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );

        break;

    case 'payrollAvgUsage':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date
                    ,a.last_month_payroll_count, a.second_last_month_payroll_count, a.current_month_payroll_count",
            "society_master sm
                 LEFT JOIN cities c ON sm.city_id = c.city_id 
                 LEFT JOIN states s ON sm.state_id = s.state_id 
                 INNER JOIN society_analytics_master a ON sm.society_id = a.society_id 
                 LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND (
            CASE 
                WHEN a.total_users != 0 
                THEN ROUND((a.current_month_payroll_count / a.total_users) * 100, 2)
                ELSE 0
            END
            ) >= $payroll_average 
            AND (
            CASE 
                WHEN a.total_users != 0 
                THEN ROUND((a.current_month_payroll_count / a.total_users) * 100, 2)
                ELSE 0
            END
            ) < $payroll_best 
            AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );

        break;

    case 'payrollLowUsage':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date
                    ,a.last_month_payroll_count, a.second_last_month_payroll_count, a.current_month_payroll_count",
            "society_master sm
                 LEFT JOIN cities c ON sm.city_id = c.city_id 
                 LEFT JOIN states s ON sm.state_id = s.state_id 
                 INNER JOIN society_analytics_master a ON sm.society_id = a.society_id 
                 LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND (
                        CASE 
                            WHEN a.total_users != 0 
                            THEN ROUND((a.current_month_payroll_count / a.total_users) * 100, 2)
                            ELSE 0
                        END) <= $payroll_low ",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );

        break;

    case 'trackingBestUsage':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date
            ,a.last_month_tracking_user_count, a.second_last_month_tracking_user_count, a.current_month_tracking_user_count",
            "society_master sm
                 LEFT JOIN cities c ON sm.city_id = c.city_id 
                 LEFT JOIN states s ON sm.state_id = s.state_id 
                 INNER JOIN society_analytics_master a ON sm.society_id = a.society_id 
                 LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND (
                        CASE 
                            WHEN sm.employee_tracking_limit != 0 
                            THEN ROUND((a.current_month_tracking_user_count / sm.employee_tracking_limit) * 100, 2)
                            ELSE 0
                        END) >= $tracking_best 
                AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );

        break;

    case 'trackingAvgUsage':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date
             ,a.last_month_tracking_user_count, a.second_last_month_tracking_user_count, a.current_month_tracking_user_count",
            "society_master sm
                 LEFT JOIN cities c ON sm.city_id = c.city_id 
                 LEFT JOIN states s ON sm.state_id = s.state_id 
                 INNER JOIN society_analytics_master a ON sm.society_id = a.society_id 
                 LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND (
            CASE 
                WHEN sm.employee_tracking_limit != 0 
                THEN ROUND((a.current_month_tracking_user_count / sm.employee_tracking_limit) * 100, 2)
                ELSE 0
            END
            ) >= $tracking_average 
            AND (
            CASE 
                WHEN sm.employee_tracking_limit != 0 
                THEN ROUND((a.current_month_tracking_user_count / sm.employee_tracking_limit) * 100, 2)
                ELSE 0
            END
            ) < $tracking_best 
                AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );

        break;

    case 'trackingLowUsage':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date
             ,a.last_month_tracking_user_count, a.second_last_month_tracking_user_count, a.current_month_tracking_user_count",
            "society_master sm
                 LEFT JOIN cities c ON sm.city_id = c.city_id 
                 LEFT JOIN states s ON sm.state_id = s.state_id 
                 INNER JOIN society_analytics_master a ON sm.society_id = a.society_id 
                 LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND (
                        CASE 
                            WHEN sm.employee_tracking_limit != 0 
                            THEN ROUND((a.current_month_tracking_user_count / sm.employee_tracking_limit) * 100, 2)
                            ELSE 0
                        END) <= $tracking_low ",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );

        break;

    case 'workReportBestUsage':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date
             ,a.last_month_work_report_count, a.second_last_month_work_report_count, a.current_month_work_report_count",
            "society_master sm
                 LEFT JOIN cities c ON sm.city_id = c.city_id 
                 LEFT JOIN states s ON sm.state_id = s.state_id 
                 INNER JOIN society_analytics_master a ON sm.society_id = a.society_id 
                 LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND (
                        CASE 
                            WHEN sm.employee_registration_limit != 0 
                            THEN ROUND((a.current_month_work_report_count / (sm.employee_registration_limit * 26)) * 100, 2)
                            ELSE ROUND((a.current_month_work_report_count / (a.total_users * 26)) * 100, 2)
                        END) >= $workReport_best 
                AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );

        break;

    case 'workReportAvgUsage':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date
             ,a.last_month_work_report_count, a.second_last_month_work_report_count, a.current_month_work_report_count",
            "society_master sm
                 LEFT JOIN cities c ON sm.city_id = c.city_id 
                 LEFT JOIN states s ON sm.state_id = s.state_id 
                 INNER JOIN society_analytics_master a ON sm.society_id = a.society_id 
                 LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND (
            CASE 
                WHEN sm.employee_registration_limit != 0 
                THEN ROUND((a.current_month_work_report_count / (sm.employee_registration_limit * 26)) * 100, 2)
                ELSE ROUND((a.current_month_work_report_count / (a.total_users * 26)) * 100, 2)
            END
                ) >= $workReport_average 
            AND (
            CASE 
                WHEN sm.employee_registration_limit != 0 
                THEN ROUND((a.current_month_work_report_count / (sm.employee_registration_limit * 26)) * 100, 2)
                ELSE ROUND((a.current_month_work_report_count / (a.total_users * 26)) * 100, 2)
                END
            ) < $workReport_best 
            AND sm.created_on_society_server = 1",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );

        break;

    case 'workReportLowUsage':
        $result = $d->selectRow(
            "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date
             ,a.last_month_work_report_count, a.second_last_month_work_report_count, a.current_month_work_report_count",
            "society_master sm
                 LEFT JOIN cities c ON sm.city_id = c.city_id 
                 LEFT JOIN states s ON sm.state_id = s.state_id 
                 INNER JOIN society_analytics_master a ON sm.society_id = a.society_id 
                 LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND (
                        CASE 
                            WHEN sm.employee_registration_limit != 0 
                            THEN ROUND((a.current_month_work_report_count / (sm.employee_registration_limit * 26)) * 100, 2)
                            ELSE ROUND((a.current_month_work_report_count / (a.total_users * 26)) * 100, 2)
                        END) <= $workReport_low",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );

        break;

    case 'trackingUsingCompanies':
        $result = $d->selectRow(
           "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id
             LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND employee_tracking_limit > 0",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;


    case 'trackingNotUsingCompanies':
        $result = $d->selectRow(
           "DISTINCT sm.society_id, sm.*, a.*, c.name as city, s.name as state, sm.created_date",
            "society_master sm
             LEFT JOIN cities c ON sm.city_id = c.city_id 
             LEFT JOIN states s ON sm.state_id = s.state_id
             LEFT JOIN society_analytics_master a ON sm.society_id = a.society_id
             LEFT JOIN transection_master t ON sm.society_id = t.society_id",
            "$where AND employee_tracking_limit = 0",
            "GROUP BY sm.society_id ORDER BY sm.society_id DESC"
        );
        break;

    default:
        break;
}

if (!empty($result)) {
    echo '<div class="card"><div class="card-body">';
    echo "<h5 class='mb-3 text-dark'>Company Details($title)</h5>";
    echo '<div class="table-responsive">';
    echo "<table id='engagementTable' class='table table-bordered'><thead><tr>
        <th>#</th>
        <th>Name</th>
        <th>City</th>
        <th>State</th>
        <th>Account Type</th>
        <th>Plan</th>
        <th>Created Date</th>
        <th>Plan Expire Date</th>
        <th>Trial Days</th>";

        if (in_array($type, ['cardRefundThisMonth', 'cardRefundThisYear'])) {
            echo "<th>Refund Description</th>
                  <th>Refund Amount</th>
                  <th>Refund Person Name</th>";
        }
        
        echo "<th>Employee Registration Limit</th>
        <th>Received Ticket Size</th>
        <th>Yearly Ticket Size</th>
        <th>Expected Team Size</th>
        <th>Training Status</th>";

    if (in_array($type, ['attendanceBestUsage', 'attendanceAvgUsage', 'attendanceLowUsage'])) {
        echo "<th>Previous Month Attendance Ratio</th>
              <th>Last Month Attendance Ratio</th>
              <th>Current Month Attendance Ratio</th>";
    }
    if (in_array($type, ['payrollBestUsage', 'payrollAvgUsage', 'payrollLowUsage'])) {
        echo "<th>Previous Month Payroll Ratio</th>
              <th>Last Month Payroll Ratio</th>
              <th>Current Month Payroll Ratio</th>";
    }
    if (in_array($type, ['trackingBestUsage', 'trackingAvgUsage', 'trackingLowUsage'])) {
        echo "<th>Previous Month Tracking Ratio</th>
              <th>Last Month Tracking Ratio</th>
              <th>Current Month Tracking Ratio</th>";
    }
    if (in_array($type, ['workReportBestUsage', 'workReportAvgUsage', 'workReportLowUsage'])) {
        echo "<th>Previous Month Work Report Ratio</th>
              <th>Last Month Work Report Ratio</th>
              <th>Current Month Work Report Ratio</th>";
    }

    echo "</tr></thead><tbody>";
    $i = 1;
    foreach ($result as $row) {
        // Compute ratios per row
        $stdAttendanceCount = (isset($row['employee_registration_limit']) && $row['employee_registration_limit'] != '0') ? ((int)$row['employee_registration_limit'] * 26) : ((isset($row['total_users']) ? (int)$row['total_users'] : 0) * 26);
        $current_month_attendance_ratio = ($stdAttendanceCount > 0 && isset($row['current_month_attendance_count'])) ? round(((float)$row['current_month_attendance_count'] / $stdAttendanceCount) * 100, 2) : 0;
        $last_month_attendance_ratio = ($stdAttendanceCount > 0 && isset($row['last_month_attendance_count'])) ? round(((float)$row['last_month_attendance_count'] / $stdAttendanceCount) * 100, 2) : 0;
        $second_last_month_attendance_ratio = ($stdAttendanceCount > 0 && isset($row['second_last_month_attendance_count'])) ? round(((float)$row['second_last_month_attendance_count'] / $stdAttendanceCount) * 100, 2) : 0;

        $stdPayrollCount = isset($row['total_users']) ? (int)$row['total_users'] : 0;
        $current_payroll_ratio = ($stdPayrollCount > 0 && isset($row['current_month_payroll_count'])) ? round(((float)$row['current_month_payroll_count'] / $stdPayrollCount) * 100, 2) : 0;
        $last_payroll_ratio = ($stdPayrollCount > 0 && isset($row['last_month_payroll_count'])) ? round(((float)$row['last_month_payroll_count'] / $stdPayrollCount) * 100, 2) : 0;
        $second_last_payroll_ratio = ($stdPayrollCount > 0 && isset($row['second_last_month_payroll_count'])) ? round(((float)$row['second_last_month_payroll_count'] / $stdPayrollCount) * 100, 2) : 0;

        $employee_tracking_limit = isset($row['employee_tracking_limit']) ? (int)$row['employee_tracking_limit'] : 0;
        $current_month_tracking_user_ratio = ($employee_tracking_limit > 0 && isset($row['current_month_tracking_user_count'])) ? round(((float)$row['current_month_tracking_user_count'] / $employee_tracking_limit) * 100, 2) : 0;
        $last_month_tracking_user_ratio = ($employee_tracking_limit > 0 && isset($row['last_month_tracking_user_count'])) ? round(((float)$row['last_month_tracking_user_count'] / $employee_tracking_limit) * 100, 2) : 0;
        $second_last_month_tracking_user_ratio = ($employee_tracking_limit > 0 && isset($row['second_last_month_tracking_user_count'])) ? round(((float)$row['second_last_month_tracking_user_count'] / $employee_tracking_limit) * 100, 2) : 0;

        $stdWorkReportCount = (isset($row['employee_registration_limit']) && $row['employee_registration_limit'] != '0') ? ((int)$row['employee_registration_limit'] * 26) : ((isset($row['total_users']) ? (int)$row['total_users'] : 0) * 26);
        $current_work_ratio = ($stdWorkReportCount > 0 && isset($row['current_month_work_report_count'])) ? round(((float)$row['current_month_work_report_count'] / $stdWorkReportCount) * 100, 2) : 0;
        $last_work_ratio = ($stdWorkReportCount > 0 && isset($row['last_month_work_report_count'])) ? round(((float)$row['last_month_work_report_count'] / $stdWorkReportCount) * 100, 2) : 0;
        $second_last_work_ratio = ($stdWorkReportCount > 0 && isset($row['second_last_month_work_report_count'])) ? round(((float)$row['second_last_month_work_report_count'] / $stdWorkReportCount) * 100, 2) : 0;
        echo "<tr>";
        echo "<td>" . $i++ . "</td>";
        echo "<td>" . $row['society_name'] . "</td>";
        echo "<td>" . $row['city'] . "</td>";
        echo "<td>" . $row['state'] . "</td>";
        echo "<td>" . ($row['account_type'] == 1 ? 'Key Account' : 'Normal Account') . "</td>";
        echo "<td>" . $row['plan'] ?? '' . "</td>";
        echo '<td>';
        $created = $row['created_date'] ?? '';
        if (!empty($created) && $created !== '0000-00-00') {
            echo date("d-M-Y", strtotime($created))           
                . '&nbsp;&nbsp;'
                . date("D, h:i A", strtotime($created));      
        }
        echo "</td>";

        echo '<td>';
        $expire = $row['plan_expire_date'] ?? '';
        if (!empty($expire) && $expire !== '0000-00-00') {
            echo date("d-M-Y", strtotime($expire));          
        }
        echo "</td>";

        echo "<td>" . $row['trial_days'] ?? '' . "</td>";

        if (in_array($type, ['cardRefundThisMonth', 'cardRefundThisYear'])) {
            echo "<td>" . $row['refund_description'] . "</td>";
            echo "<td>" . $row['refund_amount'] ?? '' . "</td>";
            echo "<td>" . $row['refund_person_name'] ?? '' . "</td>";
        }

        echo "<td>" . $row['employee_registration_limit'] . "</td>";
        echo "<td>" . $row['received_ticket_size'] ?? '' . "</td>";
        echo "<td>" . $row['yearly_ticket_size'] ?? '' . "</td>";
        echo "<td>" . $row['expected_team_size'] ?? '' . "</td>";
        $trainingStatusText = '';
        if (isset($row['training_status'])) {
            $trainingStatusText = ((string)$row['training_status'] === '1') ? 'Completed' : (((string)$row['training_status'] === '0') ? 'Pending' : (string)$row['training_status']);
        }
        echo "<td>" . $trainingStatusText . "</td>";

        if (in_array($type, ['attendanceBestUsage', 'attendanceAvgUsage', 'attendanceLowUsage'])) {
            echo "<td>" . $second_last_month_attendance_ratio . "</td>";
            echo "<td>" . $last_month_attendance_ratio . "</td>";
            echo "<td>" . $current_month_attendance_ratio . "</td>";
        }

        if (in_array($type, ['payrollBestUsage', 'payrollAvgUsage', 'payrollLowUsage'])) {
            echo "<td>" . $second_last_payroll_ratio . "</td>";
            echo "<td>" . $last_payroll_ratio . "</td>";
            echo "<td>" . $current_payroll_ratio . "</td>";
        }
        if (in_array($type, ['trackingBestUsage', 'trackingAvgUsage', 'trackingLowUsage'])) {
            echo "<td>" . $second_last_month_tracking_user_ratio . "</td>";
            echo "<td>" . $last_month_tracking_user_ratio . "</td>";
            echo "<td>" . $current_month_tracking_user_ratio . "</td>";
        }
        if (in_array($type, ['workReportBestUsage', 'workReportAvgUsage', 'workReportLowUsage'])) {
            echo "<td>" . $second_last_work_ratio . "</td>";
            echo "<td>" . $last_work_ratio . "</td>";
            echo "<td>" . $current_work_ratio . "</td>";
        }


        echo "</tr>";
    }

    echo "</tbody></table></div></div></div>";
}
?>