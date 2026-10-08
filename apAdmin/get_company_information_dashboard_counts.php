<?php
include_once 'common/object.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// General counts
$totaltrial = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND created_on_society_server = 1");
$totalimplementation = $d->count_data_direct("society_id", "society_master", "training_status = 1 AND created_on_society_server = 1");
$totalpending = $d->count_data_direct("society_id", "society_master", "training_status = 0 AND created_on_society_server = 1");
$currentmonth = $d->count_data_direct("society_id", "society_master", "MONTH(plan_expire_date) = MONTH(CURDATE()) AND YEAR(plan_expire_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$completionmonth = $d->count_data_direct("society_id", "society_master", "training_status = 1 AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$keyaccount = $d->count_data_direct("society_id", "society_master", "account_type != 0 AND created_on_society_server = 1");
$pendingkey = $d->count_data_direct("society_id", "society_master", "account_type != 0 AND training_status = 0 AND created_on_society_server = 1");
$completionkey = $d->count_data_direct("society_id", "society_master", "account_type != 0 AND training_status = 1 AND created_on_society_server = 1");

// New general counts
$totalcompanies = $d->count_data_direct("society_id", "society_master", "created_on_society_server = 1");
$thisyearcompanies = $d->count_data_direct("society_id", "society_master", "YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$thismonthcompanies = $d->count_data_direct("society_id", "society_master", "MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");


$trial_totalexpired = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND plan_expire_date < CURDATE() AND (last_renew_date IS NULL OR last_renew_date = '') AND created_on_society_server = 1");
$trial_thisyearexpired = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND YEAR(plan_expire_date) = YEAR(CURDATE()) AND plan_expire_date < CURDATE() AND (last_renew_date IS NULL OR last_renew_date = '') AND created_on_society_server = 1");
$trial_thismonthexpired = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND MONTH(plan_expire_date) = MONTH(CURDATE()) AND YEAR(plan_expire_date) = YEAR(CURDATE()) AND plan_expire_date < CURDATE() AND (last_renew_date IS NULL OR last_renew_date = '') AND created_on_society_server = 1");
// Implementation counts
$totalimpdone = $totalimplementation;
$thisyearimpdone = $d->count_data_direct("society_id", "society_master", "training_status = 1 AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$thismonthimpdone = $completionmonth;

$totalimpending = $totalpending;
$thisyearimpending = $d->count_data_direct("society_id", "society_master", "training_status = 0 AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$thismonthimpending = $d->count_data_direct("society_id", "society_master", "training_status = 0 AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");

// Key account counts
$thisyearkey = $d->count_data_direct("society_id", "society_master", "account_type != 0 AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$thismonthkey = $d->count_data_direct("society_id", "society_master", "account_type != 0 AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");

$thisyearkeydone = $d->count_data_direct("society_id", "society_master", "account_type != 0 AND training_status = 1 AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$thismonthkeydone = $d->count_data_direct("society_id", "society_master", "account_type != 0 AND training_status = 1 AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");

$thisyearkeypending = $d->count_data_direct("society_id", "society_master", "account_type != 0 AND training_status = 0 AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$thismonthkeypending = $d->count_data_direct("society_id", "society_master", "account_type != 0 AND training_status = 0 AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");

// Trial counts
$trial_totaltrial = $totaltrial;
$trial_thismonth = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$trial_thisyear = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$trial_totalimplementation = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND training_status = 1 AND created_on_society_server = 1");
$trial_totalpending = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND training_status = 0 AND created_on_society_server = 1");
$trial_planexpired_month = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND MONTH(plan_expire_date) = MONTH(CURDATE()) AND YEAR(plan_expire_date) = YEAR(CURDATE()) AND (last_renew_date IS NULL OR last_renew_date = '') AND created_on_society_server = 1");
$trial_completionmonth = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND training_status = 1 AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$trial_keyaccount = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND account_type != 0 AND created_on_society_server = 1");
$trial_pendingkey = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND account_type != 0 AND training_status = 0 AND created_on_society_server = 1");
$trial_completionkey = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND account_type != 0 AND training_status = 1 AND created_on_society_server = 1");

// New trial counts
$thisyeartrial = $trial_thisyear;
$thismonthtrial = $trial_thismonth;

$thisyeartrialdone = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND training_status = 1 AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$thismonthtrialdone = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND training_status = 1 AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");

$thisyeartrialpending = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND training_status = 0 AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$thismonthtrialpending = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND training_status = 0 AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");

$trial_thisyearkey = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND account_type != 0 AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$trial_thismonthkey = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND account_type != 0 AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");

$trial_thisyearkeydone = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND account_type != 0 AND training_status = 1 AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$trial_thismonthkeydone = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND account_type != 0 AND training_status = 1 AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");

$trial_thisyearkeypending = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND account_type != 0 AND training_status = 0 AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
$trial_thismonthkeypending = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND account_type != 0 AND training_status = 0 AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");


// Companies Dashboard
// $lastyeartotalcompanies = $d->count_data_direct("society_id", "society_master", "YEAR(created_date) = YEAR(CURDATE()) - 1 AND created_on_society_server = 1");
// $lastyearcompanies = $d->count_data_direct("society_id", "society_master", "YEAR(created_date) = YEAR(CURDATE()) - 1 AND created_on_society_server = 1");
// $lastmonthcompanies = $d->count_data_direct("society_id", "society_master", "MONTH(created_date) = MONTH(CURDATE()) - 1 AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");


// $renewedcompany = $d->count_data_direct("society_id", "society_master", "package_id != 0 AND last_renew_date IS NOT NULL AND created_on_society_server = 1");
// $thisyearrenewedcompanies = $d->count_data_direct("society_id", "society_master", "package_id != 0 AND last_renew_date IS NOT NULL AND YEAR(last_renew_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
// $thismonthrenewdcompanies = $d->count_data_direct("society_id", "society_master", "package_id != 0 AND last_renew_date IS NOT NULL AND MONTH(last_renew_date) = MONTH(CURDATE()) AND YEAR(last_renew_date) = YEAR(CURDATE()) AND created_on_society_server = 1");


// $lostcompanies = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND plan_expire_date < CURDATE() AND (last_renew_date IS NULL OR last_renew_date = '') AND created_on_society_server = 1");
// $thisyearlostcompanies = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND YEAR(plan_expire_date) = YEAR(CURDATE()) AND plan_expire_date < CURDATE() AND (last_renew_date IS NULL OR last_renew_date = '') AND created_on_society_server = 1");
// $thismonthlostcompanies = $d->count_data_direct("society_id", "society_master", "package_id = 0 AND MONTH(plan_expire_date) = MONTH(CURDATE()) AND YEAR(plan_expire_date) = YEAR(CURDATE()) AND plan_expire_date < CURDATE() AND (last_renew_date IS NULL OR last_renew_date = '') AND created_on_society_server = 1");


// $trialtopaidcompanies = $d->count_data_direct("society_id", "society_master", "package_id != 0 AND training_status = 1 AND created_on_society_server = 1");
// $thisyear_trail_to_paid_companies = $d->count_data_direct("society_id", "society_master", "package_id != 0 AND training_status = 1 AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");
// $thismonth_trail_to_paid_companies = $d->count_data_direct("society_id", "society_master", "package_id != 0 AND training_status = 1 AND MONTH(created_date) = MONTH(CURDATE()) AND YEAR(created_date) = YEAR(CURDATE()) AND created_on_society_server = 1");


// $expiredcompanies = $d->count_data_direct("society_id", "society_master", "plan_expire_date < CURDATE() AND created_on_society_server = 1");
// $thisyearexpiredcompanies = $d->count_data_direct("society_id", "society_master", "YEAR(plan_expire_date) = YEAR(CURDATE()) AND plan_expire_date < CURDATE() AND created_on_society_server = 1");
// $thismonthexpairedcompanies = $d->count_data_direct("society_id", "society_master", "MONTH(plan_expire_date) = MONTH(CURDATE()) AND YEAR(plan_expire_date) = YEAR(CURDATE()) AND plan_expire_date < CURDATE() AND created_on_society_server = 1");

// $payrollcompanies = $d->count_data_direct("society_id", "society_master sm LEFT JOIN society_resent_analytics_master sram ON sm.society_id = sram.society_id", "sram.salary_count > 0 AND sm.created_on_society_server = 1");

// $thisyearpayrollcompanies = $d->count_data_direct("society_id", "society_master sm LEFT JOIN society_resent_analytics_master sram 
// ON sm.society_id = sram.society_id", "sram.salary_count > 0 AND YEAR(sm.created_date) = YEAR(CURDATE()) AND sm.created_on_society_server = 1");

// $thismonthpayrollcompanies = $d->count_data_direct("society_id", "society_master sm LEFT JOIN society_resent_analytics_master sram 
// ON sm.society_id = sram.society_id", "sram.salary_count > 0 AND MONTH(sm.created_date) = MONTH(CURDATE()) AND YEAR(sm.created_date) = YEAR(CURDATE())  AND sm.created_on_society_server = 1");

echo json_encode([
    'totaltrial' => $totaltrial,
    'totalimplementation' => $totalimplementation,
    'totalpending' => $totalpending,
    'currentmonth' => $currentmonth,
    'completionmonth' => $completionmonth,
    'keyaccount' => $keyaccount,
    'pendingkey' => $pendingkey,
    'completionkey' => $completionkey,
    'trial_totaltrial' => $trial_totaltrial,
    'trial_thismonth' => $trial_thismonth,
    'trial_thisyear' => $trial_thisyear,
    'trial_totalimplementation' => $trial_totalimplementation,
    'trial_totalpending' => $trial_totalpending,
    'trial_planexpired_month' => $trial_planexpired_month,
    'trial_completionmonth' => $trial_completionmonth,
    'trial_keyaccount' => $trial_keyaccount,
    'trial_pendingkey' => $trial_pendingkey,
    'trial_completionkey' => $trial_completionkey,

    // New counts
    'totalcompanies' => $totalcompanies,
    'thisyearcompanies' => $thisyearcompanies,
    'thismonthcompanies' => $thismonthcompanies,
    'totalimpdone' => $totalimpdone,
    'thisyearimpdone' => $thisyearimpdone,
    'thismonthimpdone' => $thismonthimpdone,
    'totalimpending' => $totalimpending,
    'thisyearimpending' => $thisyearimpending,
    'thismonthimpending' => $thismonthimpending,
    'thisyearkey' => $thisyearkey,
    'thismonthkey' => $thismonthkey,
    'thisyearkeydone' => $thisyearkeydone,
    'thismonthkeydone' => $thismonthkeydone,
    'thisyearkeypending' => $thisyearkeypending,
    'thismonthkeypending' => $thismonthkeypending,
    'thisyeartrial' => $thisyeartrial,
    'thismonthtrial' => $thismonthtrial,
    'thisyeartrialdone' => $thisyeartrialdone,
    'thismonthtrialdone' => $thismonthtrialdone,
    'thisyeartrialpending' => $thisyeartrialpending,
    'thismonthtrialpending' => $thismonthtrialpending,
    'trial_thisyearkey' => $trial_thisyearkey,
    'trial_thismonthkey' => $trial_thismonthkey,
    'trial_thisyearkeydone' => $trial_thisyearkeydone,
    'trial_thismonthkeydone' => $trial_thismonthkeydone,
    'trial_thisyearkeypending' => $trial_thisyearkeypending,
    'trial_thismonthkeypending' => $trial_thismonthkeypending,
    'trial_totalexpired' => $trial_totalexpired,
    'trial_thisyearexpired' => $trial_thisyearexpired,
    'trial_thismonthexpired' => $trial_thismonthexpired,


    //company dashboard
    // 'lastyeartotalcompanies' => $lastyeartotalcompanies,
    // 'lastyearcompanies' => $lastyearcompanies,
    // 'lastmonthcompanies' => $lastmonthcompanies,

    // 'renewedcompany' => $renewedcompany,
    // 'thisyearrenewedcompanies' => $thisyearrenewedcompanies,
    // 'thismonthrenewdcompanies' => $thismonthrenewdcompanies,

    // 'lostcompanies' => $lostcompanies,
    // 'thisyearlostcompanies' => $thisyearlostcompanies,
    // 'thismonthlostcompanies' => $thismonthlostcompanies,

    // 'refundscompanies' => $refundscompanies,
    // 'thisyearrefundscompanies' => $thisyearrefundscompanies,
    // 'thismonthrefundscompanies' => $thismonthrefundscompanies,

    // 'trialtopaidcompanies' => $trialtopaidcompanies,
    // 'thisyear_trail_to_paid_companies' => $thisyear_trail_to_paid_companies,
    // 'thismonth_trail_to_paid_companies' => $thismonth_trail_to_paid_companies,

    // 'expiredcompanies' => $expiredcompanies,
    // 'thisyearexpiredcompanies' => $thisyearexpiredcompanies,
    // 'thismonthexpairedcompanies' => $thismonthexpairedcompanies,

    // 'payrollcompanies' => $payrollcompanies,
    // 'thisyearpayrollcompanies' => $thisyearpayrollcompanies,
    // 'thismonthpayrollcompanies' => $thismonthpayrollcompanies,

]);
