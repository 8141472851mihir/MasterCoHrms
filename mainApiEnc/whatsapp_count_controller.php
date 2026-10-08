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
        if (isset($type) && $type == "expiring_companies") {
            $user_mobile = $rawData['mobile'];
            $enc_user_mobile = $d->encryptDecrypt("encrypt", $user_mobile);
            $getUserDataQ = $d->selectRow(
                "rm.menu_id, bms.admin_id",
                "bms_admin_master as bms LEFT JOIN role_master as rm on rm.role_id = bms.role_id",
                "bms.display_admin_mobile = '$enc_user_mobile' "
            );
            if (mysqli_num_rows($getUserDataQ) == 0) {
                $response["data"] = "Your mobile number is not registered in MyCo Master.";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit;
            } else {
                $UserData = mysqli_fetch_array($getUserDataQ);
                $admin_id = $UserData['admin_id'];
                $menuAccessString = $UserData['menu_id'];
                $menuAccessArray = explode(",", $menuAccessString);

                // check if user has plan expire menu access or not
                if (in_array('264', $menuAccessArray)) {
                    // has permission then send expiring companies data

                    // check country access
                    $countryAryAccess = array();
                    $cCheck = $d->select("admin_country_master", "bms_admin_id='$admin_id'");
                    $countryAryAccess = [];
                    while ($accessCountry = mysqli_fetch_assoc($cCheck)) {
                        $countryAryAccess[] = $accessCountry['country_id'];
                    }
                    if (!empty($countryAryAccess)) {
                        $countryids = implode("','", $countryAryAccess);
                        $countryAppendQuerySocietySingle = " AND country_id IN ('$countryids')";
                        $countryAppendQuerySocietySingleReq = " AND request_country_id IN ('$countryids')";
                        $countryAppendQuerySociety = " AND society_master.country_id IN ('$countryids')";
                    } else {
                        $countryAppendQuerySocietySingle = "";
                        $countryAppendQuerySocietySingleReq = "";
                        $countryAppendQuerySociety = "";
                        $countryids = "";
                    }

                    $expiring_companies_data_q = $d->selectRow(
                        "society_master.society_id, society_master.plan_expire_date,
                        society_master.society_name, society_master.country_code, society_master.secretary_mobile",
                        "society_master",
                        "society_id!=0 $countryAppendQuerySocietySingle",
                        "order by plan_expire_date ASC"
                    );

                    if (mysqli_num_rows($expiring_companies_data_q) == 0) {
                        $response["data"] = "No companies were found.";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit;
                    } else {
                        $today = date("Y-m-d");
                        $yesterday = date("Y-m-d", strtotime("-1 day"));
                        $last7daysStart = date("Y-m-d", strtotime("-7 days"));
                        $tomorrow = date("Y-m-d", strtotime("+1 day"));
                        $upcoming7daysEnd = date("Y-m-d", strtotime("+7 days"));

                        // Initialize
                        $todayList = $yesterdayList = $last7daysList = $tomorrowList = $upcoming7daysList = [];

                        while ($expiring_companies_data = mysqli_fetch_array($expiring_companies_data_q)) {
                            $expireDate = $expiring_companies_data['plan_expire_date'];
                            $company = [
                                "society_id" => $expiring_companies_data['society_id'],
                                "society_name" => $expiring_companies_data['society_name'],
                                "country_code" => $expiring_companies_data['country_code'],
                                "secretary_mobile" => $expiring_companies_data['secretary_mobile'],
                                "expire_date" => $expireDate,
                            ];

                            // Today
                            if ($expireDate == $today) {
                                $todayList[] = $company;
                            }

                            // Yesterday
                            if ($expireDate == $yesterday) {
                                $yesterdayList[] = $company;
                            }

                            // Last 7 days (excluding today)
                            if ($expireDate >= $last7daysStart && $expireDate < $today) {
                                $last7daysList[] = $company;
                            }

                            // Tomorrow
                            if ($expireDate == $tomorrow) {
                                $tomorrowList[] = $company;
                            }

                            // Upcoming 7 days
                            if ($expireDate > $today && $expireDate <= $upcoming7daysEnd) {
                                $upcoming7daysList[] = $company;
                            }
                        }

                        // if sub_type is set -> show company details for that bucket
                        if (isset($_GET['sub_type']) && !empty($_GET['sub_type'])) {
                            $sub_type = intval($_GET['sub_type']);
                            $companies = [];
                            $title = "";

                            switch ($sub_type) {
                                case 1:
                                    $companies = $todayList;
                                    $title = "Today's Expiring Companies";
                                    break;
                                case 2:
                                    $companies = $yesterdayList;
                                    $title = "Yesterday's Expired Companies";
                                    break;
                                case 3:
                                    $companies = $last7daysList;
                                    $title = "Last 7 Days Expired Companies";
                                    break;
                                case 4:
                                    $companies = $tomorrowList;
                                    $title = "Tomorrow's Expiring Companies";
                                    break;
                                case 5:
                                    $companies = $upcoming7daysList;
                                    $title = "Upcoming 7 Days Expiring Companies";
                                    break;
                            }

                            if (empty($companies)) {
                                $message = "\n*$title:*\nNo companies found.";
                            } else {
                                $message = "\n*$title:*\n";
                                $i = 1;
                                foreach ($companies as $comp) {
                                    $message .= "\n*$i.* {$comp['society_name']}\n";
                                    $message .= "   Expiry Date: ".date("d M Y", strtotime($comp['expire_date']))."\n";
                                    $message .= "   Contact: {$comp['country_code']}{$comp['secretary_mobile']}\n";
                                    $i++;
                                }
                            }
                            if (strlen($message) >= 4096) {
                                // collect first 4090 characters, then append "..."
                                $message = substr($message, 0, 4092) . '...';
                            }
                            $response["data"] = $message;
                            echo $d->manage_encryption($is_encrypted, $response, $compress);
                            exit;
                        }

                        // else default summary message
                        $message = "\n*Expiring Companies Summary:*\n";
                        $message .= "1. *Today Expiring Companies:* " . count($todayList) . "\n";
                        $message .= "2. *Yesterday Expired Companies:* " . count($yesterdayList) . "\n";
                        $message .= "3. *Last 7 Days Expired Companies:* " . count($last7daysList) . "\n";
                        $message .= "4. *Tomorrow Expiring Companies:* " . count($tomorrowList) . "\n";
                        $message .= "5. *Upcoming 7 Days Expiring Companies:* " . count($upcoming7daysList) . "\n\n";
                        $message .= "*Reply with 1-5 to get company details.*\n";

                        if (strlen($message) >= 4096) {
                            // collect first 4090 characters, then append "..."
                            $message = substr($message, 0, 4092) . '...';
                        }
                        $response["data"] = $message;
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit;
                    }
                } else {
                    // access denied
                    $response["data"] = "You do not have the necessary permissions to view details of expiring companies.";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit;
                }
            }
        } elseif (isset($type) && $type == "revenue_report") {
            $user_mobile = $rawData['mobile'];
            $enc_user_mobile = $d->encryptDecrypt("encrypt", $user_mobile);
            $getUserDataQ = $d->selectRow(
                "rm.menu_id, bms.admin_id",
                "bms_admin_master as bms LEFT JOIN role_master as rm on rm.role_id = bms.role_id",
                "bms.display_admin_mobile = '$enc_user_mobile' "
            );
            if (mysqli_num_rows($getUserDataQ) == 0) {
                $response["data"] = "Your mobile number is not registered in MyCo Master.";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit;
            } else {
                $UserData = mysqli_fetch_array($getUserDataQ);
                $admin_id = $UserData['admin_id'];
                $menuAccessString = $UserData['menu_id'];
                $menuAccessArray = explode(",", $menuAccessString);

                // Require the same permission used earlier (347)
                if (in_array('347', $menuAccessArray)) {
                    // Country access
                    $countryAppendQuerySociety = "";
                    $cCheck = $d->select("admin_country_master", "bms_admin_id='$admin_id'");
                    $countryAryAccess = [];
                    while ($accessCountry = mysqli_fetch_assoc($cCheck)) {
                        $countryAryAccess[] = $accessCountry['country_id'];
                    }
                    if (!empty($countryAryAccess)) {
                        $countryids = implode("','", $countryAryAccess);
                        $countryAppendQuerySociety = " AND society_master.country_id IN ('$countryids')";
                    }

                    // Pull transactions for New Registrations(0), Renewals(1), Extensions(3) for last 7 days
                    $q = $d->selectRow(
                        "transection_master.*, society_master.society_name, society_master.employee_tracking_limit, 
                        society_master.employee_registration_limit, society_master.country_code, society_master.secretary_mobile, 
                        manage_plan.plan_name, society_analytics_master.total_users, society_analytics_master.active_tracking_users",
                        "transection_master 
                            LEFT JOIN society_master ON transection_master.society_id = society_master.society_id
                            LEFT JOIN manage_plan ON manage_plan.plan_value = society_master.package_id
                            LEFT JOIN society_analytics_master ON society_analytics_master.society_id = society_master.society_id",
                        "transection_master.society_id != 0 AND transection_master.is_renewal IN ('0','1','3') AND 
                        transection_master.transection_date > NOW() - INTERVAL 7 DAY" . $countryAppendQuerySociety,
                        "ORDER BY transection_master.transection_id DESC"
                    );

                    if (!$q || mysqli_num_rows($q) == 0) {
                        $response["data"] = "No revenue activity was found in last 7 days.";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit;
                    }

                    $today = date("Y-m-d");
                    $yesterday = date("Y-m-d", strtotime("-1 day"));
                    $last7daysStart = date("Y-m-d", strtotime("-7 days"));

                    // Buckets per category
                    $buckets = [
                        'today' => ['list' => [], 'totals' => ['new' => 0.0, 'renewal' => 0.0, 'extend' => 0.0]],
                        'yesterday' => ['list' => [], 'totals' => ['new' => 0.0, 'renewal' => 0.0, 'extend' => 0.0]],
                        'last7' => ['list' => [], 'totals' => ['new' => 0.0, 'renewal' => 0.0, 'extend' => 0.0]],
                    ];

                    while ($row = mysqli_fetch_array($q)) {
                        $tranDateFull = $row['transection_date'] ?? '';
                        $tranDate = !empty($tranDateFull) ? date('Y-m-d', strtotime($tranDateFull)) : null;
                        if (!$tranDate) { continue; }

                        $category = ($row['is_renewal'] == '1') ? 'renewal' : (($row['is_renewal'] == '3') ? 'extend' : 'new');
                        $amount = isset($row['transection_amount']) ? floatval($row['transection_amount']) : 0.0;

                        $company = [
                            "society_id" => $row['society_id'],
                            "society_name" => $row['society_name'],
                            "country_code" => $row['country_code'],
                            "secretary_mobile" => $row['secretary_mobile'],
                            "plan_name" => $row['plan_name'] ?? '',
                            "category" => $category, // new | renewal | extend
                            "amount" => $amount,
                            "date" => $tranDate,
                            "total_users" => isset($row['total_users']) ? intval($row['total_users']) : 0,
                            "active_tracking_users" => isset($row['active_tracking_users']) ? intval($row['active_tracking_users']) : 0,
                            "employee_tracking_limit" => isset($row['employee_tracking_limit']) ? intval($row['employee_tracking_limit']) : 0,
                            "employee_registration_limit" => isset($row['employee_registration_limit']) ? intval($row['employee_registration_limit']) : 0,
                        ];

                        if ($tranDate == $today) {
                            $buckets['today']['list'][] = $company;
                            $buckets['today']['totals'][$category] += $amount;
                        }
                        if ($tranDate == $yesterday) {
                            $buckets['yesterday']['list'][] = $company;
                            $buckets['yesterday']['totals'][$category] += $amount;
                        }
                        if ($tranDate >= $last7daysStart && $tranDate < $today) {
                            $buckets['last7']['list'][] = $company;
                            $buckets['last7']['totals'][$category] += $amount;
                        }
                    }

                    // If details requested
                    if (isset($_GET['sub_type']) && !empty($_GET['sub_type'])) {
                        $sub_type = intval($_GET['sub_type']);
                        $companies = [];
                        $title = "";
                        if ($sub_type == 1) { $companies = $buckets['today']['list']; $title = "Today's Revenue (New + Renewals + Extensions)"; }
                        if ($sub_type == 2) { $companies = $buckets['yesterday']['list']; $title = "Yesterday's Revenue (New + Renewals + Extensions)"; }
                        if ($sub_type == 3) { $companies = $buckets['last7']['list']; $title = "Last 7 Days Revenue (New + Renewals + Extensions)"; }

                        if (empty($companies)) {
                            $message = "\n*$title:*\nNo companies found.";
                        } else {
                            // Group by category to preserve clarity similar to previous outputs
                            $message = "\n*$title:*\n";
                            $order = ['new' => 'New Registration', 'renewal' => 'Renewal', 'extend' => 'Extended'];
                            foreach ($order as $catKey => $catLabel) {
                                $group = array_values(array_filter($companies, function($c) use ($catKey) { return $c['category'] === $catKey; }));
                                if (empty($group)) { continue; }
                                $message .= "\n*$catLabel:*\n";
                                $i = 1;
                                foreach ($group as $comp) {
                                    $message .= "\n*$i.* {$comp['society_name']}\n";
                                    if ($catKey === 'extend') {
                                        $message .= "   Extended Date: " . date("d M Y", strtotime($comp['date'])) . "\n";
                                    } else {
                                        $message .= "   Payment Date: " . date("d M Y", strtotime($comp['date'])) . "\n";
                                        $message .= "   Amount: Rs. " . number_format($comp['amount'], 2) . "\n";
                                    }
                                    if (!empty($comp['plan_name'])) { $message .= "   Plan: {$comp['plan_name']}\n"; }
                                    $message .= "   Users: " . ($comp['total_users'] ?? 0) . "/" . ($comp['employee_registration_limit'] ?? 0) . "\n";
                                    $message .= "   Tracking Users: " . ($comp['active_tracking_users'] ?? 0) . "/" . ($comp['employee_tracking_limit'] ?? 0) . "\n";
                                    $message .= "   Contact: {$comp['country_code']}{$comp['secretary_mobile']}\n";
                                    $i++;
                                }
                            }
                        }
                        if (strlen($message) >= 4096) {
                            // collect first 4090 characters, then append "..."
                            $message = substr($message, 0, 4092) . '...';
                        }
                        $response["data"] = $message;
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit;
                    }

					// Summary message (readable layout)
						$todayTotal = array_sum($buckets['today']['totals']);
						$yesterdayTotal = array_sum($buckets['yesterday']['totals']);
						$last7Total = array_sum($buckets['last7']['totals']);

						// Company counts per period
						$todayCompanyCount = count($buckets['today']['list']);
						$yesterdayCompanyCount = count($buckets['yesterday']['list']);
						$last7CompanyCount = count($buckets['last7']['list']);

						// Company counts per category within each period
						$todayCatCounts = ['new' => 0, 'renewal' => 0, 'extend' => 0];
						foreach ($buckets['today']['list'] as $c) { $todayCatCounts[$c['category']]++; }
						$yesterdayCatCounts = ['new' => 0, 'renewal' => 0, 'extend' => 0];
						foreach ($buckets['yesterday']['list'] as $c) { $yesterdayCatCounts[$c['category']]++; }
						$last7CatCounts = ['new' => 0, 'renewal' => 0, 'extend' => 0];
						foreach ($buckets['last7']['list'] as $c) { $last7CatCounts[$c['category']]++; }

						$message = "\n*Revenue Report*\n";
					$message .= "——————————————\n";
						$message .= "*1) Today* (Companies: $todayCompanyCount)\n";
						$message .= "   New (" .$todayCatCounts['new'] .")              : Rs. " . number_format($buckets['today']['totals']['new'], 2) . "\n";
						$message .= "   Renewals (" .$todayCatCounts['renewal'] .")     : Rs. " . number_format($buckets['today']['totals']['renewal'], 2) . "\n";
						$message .= "   Extensions (" .$todayCatCounts['extend'] .")   : Rs. " . number_format($buckets['today']['totals']['extend'], 2) . "\n";
					$message .= "   ————————————\n";
					$message .= "   *Total*       : Rs. " . number_format($todayTotal, 2) . "\n\n";

						$message .= "*2) Yesterday* (Companies: $yesterdayCompanyCount)\n";
						$message .= "   New (" .$yesterdayCatCounts['new'] .")              : Rs. " . number_format($buckets['yesterday']['totals']['new'], 2) . "\n";
						$message .= "   Renewals (" .$yesterdayCatCounts['renewal'] .")     : Rs. " . number_format($buckets['yesterday']['totals']['renewal'], 2) . "\n";
						$message .= "   Extensions (" .$yesterdayCatCounts['extend'] .")   : Rs. " . number_format($buckets['yesterday']['totals']['extend'], 2) . "\n";
					$message .= "   ————————————\n";
					$message .= "   *Total*       : Rs. " . number_format($yesterdayTotal, 2) . "\n\n";

						$message .= "*3) Last 7 Days* (Companies: $last7CompanyCount)\n";
						$message .= "   New (" .$last7CatCounts['new'] .")              : Rs. " . number_format($buckets['last7']['totals']['new'], 2) . "\n";
						$message .= "   Renewals (" .$last7CatCounts['renewal'] .")     : Rs. " . number_format($buckets['last7']['totals']['renewal'], 2) . "\n";
						$message .= "   Extensions (" .$last7CatCounts['extend'] .")   : Rs. " . number_format($buckets['last7']['totals']['extend'], 2) . "\n";
					$message .= "   ————————————\n";
					$message .= "   *Total*       : Rs. " . number_format($last7Total, 2) . "\n\n";

					$message .= "*Reply with 1-3* to get company details for that period (combined list).\n";

                    if (strlen($message) >= 4096) {
                        // collect first 4090 characters, then append "..."
                        $message = substr($message, 0, 4092) . '...';
                    }
                    $response["data"] = $message;
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit;
                } else {
                    $response["data"] = "You do not have the necessary permissions to view revenue reports.";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit;
                }
            }
        } else {
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
