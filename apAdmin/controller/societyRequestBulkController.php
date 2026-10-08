<?php

include '../common/objectController.php';

if (!isset($bms_admin_id)) {
  $_SESSION['msg1'] = "Login required.";
  header("location:../addCompany");
  exit();
}

// Download sample CSV
if (isset($_GET['download_sample']) && $_GET['download_sample'] == '1') {
  $headers = [
    'Company Name',
    'State',
    'City',
    'Rise Event (Yes/No)',
    'Address',
    'Industry Type',
    'Admin Name',
    'Admin Mobile',
    'Admin Email',
    'Trial Days',
    'Employee Registration Limit',
    'Expected Team Size',
    'Employee Tracking Limit',
    'Per Employee Price',
    'Sales Person Name',
    'Implementation Executive Name',
    'Sales Closure Date',
    'Support Person Name',
    'Support Person Mobile',
    'Lead Sources',
    'Year Type',
    'Sales Remarks',
    'Campaign Region'
  ];
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="bulk_company_request_sample.csv"');
  $out = fopen('php://output', 'w');
  fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
  fputcsv($out, $headers);
  // One sample row
  fputcsv($out, [
    'Sample Company Pvt Ltd',
    'Gujarat',
    'Ahmedabad',
    'No',
    'Sample Address, City',
    'IT Services',
    'Admin User',
    '9876543210',
    'admin@sample.com',
    '30',
    '100',
    '50',
    '100',
    '5000',
    'Sales Person',
    'Implementation Admin',
    '2025-12-31',
    'Support Admin',
    '9876543211',
    'Inbound',
    'Calendar Year',
    'Sample remark',
    'Ahmedabad'
  ]);
  fclose($out);
  exit();
}

function getBulkCampaignRegionOptions($keydb)
{
  $cacheFile = dirname(__DIR__, 2) . '/img/cacheBlocks.json';
  $todayDate = date('Y-m-d');

  if (file_exists($cacheFile)) {
    $cacheContent = json_decode(file_get_contents($cacheFile), true);
    if (isset($cacheContent['cache_date']) && $cacheContent['cache_date'] === $todayDate && isset($cacheContent['data']['block'])) {
      return $cacheContent['data']['block'];
    }
  }

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://ahmedabad.my-company.app/fincasys/residentApiNew/societyAnalytics.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('getBlocks' => 'getBlocks'),
    CURLOPT_HTTPHEADER => array('key: ' . $keydb),
  ));
  $response = curl_exec($curl);
  curl_close($curl);
  $apiData = json_decode($response, true);
  if (isset($apiData['status']) && $apiData['status'] == 200 && isset($apiData['block']) && is_array($apiData['block'])) {
    file_put_contents($cacheFile, json_encode([
      'cache_date' => $todayDate,
      'data' => $apiData
    ]));
    return $apiData['block'];
  }

  return [];
}

// Process bulk CSV upload
if (isset($_POST['bulk_company_submit']) && isset($_FILES['bulk_csv']) && $_FILES['bulk_csv']['error'] === UPLOAD_ERR_OK) {

  $file = $_FILES['bulk_csv']['tmp_name'];
  $country_id = 101; // India only
  $keydb = $m->api_key();
  $con = $d->dbCon();

  $year_type_map = [
    'calendar year' => 0,
    'financial' => 1,
    'financial year' => 1
  ];
  $adminQry = $d->selectRow("admin_name", "bms_admin_master", "");
  $adminArray = [];
  while ($adminData = mysqli_fetch_assoc($adminQry)) {
    $adminArray[] = $adminData['admin_name'];
  }
  function slugCompanyName($name, $maxLen = 27)
  {
    $s = preg_replace('/[^a-z0-9]/', '', strtolower(trim($name)));
    return substr($s, 0, $maxLen);
  }

  $validRegionNames = [];
  $regionBlocks = getBulkCampaignRegionOptions($keydb);
  foreach ($regionBlocks as $block) {
    if (!empty($block['block_name'])) {
      $validRegionNames[] = $block['block_name'];
    }
  }
  $inserted = 0;
  $errors = [];
  $rowNum = 0;

  // Prefetch lookup catalogs once (keyed by name) to avoid per-row SQL
  $statesByName = [];
  $qStates = $d->selectRow("state_id, name", "states", "country_id=101");
  while ($st = mysqli_fetch_assoc($qStates)) {
    $statesByName[strtolower(trim($st['name']))] = (int)$st['state_id'];
  }
  $citiesByStateAndName = [];
  $qCities = $d->selectRow("c.city_id, c.state_id, c.domain_id, c.name", "cities c INNER JOIN states s ON s.state_id=c.state_id", "s.country_id=101");
  while ($ct = mysqli_fetch_assoc($qCities)) {
    $citiesByStateAndName[(int)$ct['state_id'] . '|' . strtolower(trim($ct['name']))] = [
      'city_id' => (int)$ct['city_id'],
      'domain_id' => isset($ct['domain_id']) ? $ct['domain_id'] : null,
    ];
  }
  $domainsById = [];
  $qDomains = $d->selectRow("domain_id, domain_name", "domain_master", "domain_active_status=0");
  while ($dm = mysqli_fetch_assoc($qDomains)) {
    $domainsById[(int)$dm['domain_id']] = $dm['domain_name'];
  }
  $businessByName = [];
  $qBiz = $d->selectRow("b_id, name", "business_entity_master", "status='0'");
  while ($bz = mysqli_fetch_assoc($qBiz)) {
    $businessByName[strtolower(trim($bz['name']))] = $bz['b_id'];
  }
  $existingSubDomains = [];
  $qSubs = $d->selectRow("sub_domain", "society_master", "sub_domain IS NOT NULL AND sub_domain!=''");
  while ($sd = mysqli_fetch_assoc($qSubs)) {
    $existingSubDomains[$sd['sub_domain']] = true;
  }
  $pendingSubDomains = [];
  $qPend = $d->selectRow("request_sub_domain", "society_master_requests", "request_society_create_status=0 AND request_sub_domain IS NOT NULL AND request_sub_domain!=''");
  while ($pd = mysqli_fetch_assoc($qPend)) {
    $pendingSubDomains[$pd['request_sub_domain']] = true;
  }

  if (($handle = fopen($file, 'r')) !== false) {
    $header = fgetcsv($handle);
    $rowNum = 1;
    while (($row = fgetcsv($handle)) !== false) {
      $rowNum++;
      if (count($row) < 23) {
        $errors[] = "Row $rowNum: insufficient columns (23 required including Campaign Region).";
        continue;
      }

      $society_name       = isset($row[0]) ? trim($row[0]) : '';
      $state_name         = isset($row[1]) ? trim($row[1]) : '';
      $city_name          = isset($row[2]) ? trim($row[2]) : '';
      $rise_event         = isset($row[3]) ? trim($row[3]) : 'No';
      $society_address    = isset($row[4]) ? trim($row[4]) : '';
      $industry_type_name = isset($row[5]) ? trim($row[5]) : '';
      $secretary_name     = isset($row[6]) ? trim($row[6]) : '';
      $secretary_mobile   = isset($row[7]) ? trim($row[7]) : '';
      $secretary_email    = isset($row[8]) ? trim($row[8]) : '';
      $trial_days         = isset($row[9]) ? trim($row[9]) : '0';
      $employee_registration_limit = isset($row[10]) ? trim($row[10]) : '0';
      $expected_team_size = isset($row[11]) ? trim($row[11]) : '0';
      $employee_tracking_limit = isset($row[12]) ? trim($row[12]) : '0';
      $per_employee_price = isset($row[13]) ? trim($row[13]) : '0';
      $sales_person_name  = isset($row[14]) ? trim($row[14]) : '';
      $implementation_name = isset($row[15]) ? trim($row[15]) : '';
      $sales_closure_date_raw = isset($row[16]) ? trim($row[16]) : '';
      // CSV accepts dd-mm-yyyy or yyyy-mm-dd; DB always stores yyyy-mm-dd only
      $sales_closure_date = '';
      if ($sales_closure_date_raw !== '') {
        $parts = preg_split('/[-\/]/', $sales_closure_date_raw, 3);
        if (count($parts) === 3) {
          $a = (int)$parts[0];
          $b = (int)$parts[1];
          $c = (int)$parts[2];
          // dd-mm-yyyy: day <= 31, month <= 12, year 4 digit
          if ($a >= 1 && $a <= 31 && $b >= 1 && $b <= 12 && $c >= 1000 && $c <= 9999) {
            $sales_closure_date = sprintf('%04d-%02d-%02d', $c, $b, $a);
          }
          // yyyy-mm-dd: year 4 digit, month <= 12, day <= 31
          elseif ($a >= 1000 && $a <= 9999 && $b >= 1 && $b <= 12 && $c >= 1 && $c <= 31) {
            $sales_closure_date = sprintf('%04d-%02d-%02d', $a, $b, $c);
          }
        }
      }
      $support_name       = isset($row[17]) ? trim($row[17]) : '';
      $support_mobile_no  = isset($row[18]) ? trim($row[18]) : '';
      $lead_sources_str   = isset($row[19]) ? trim($row[19]) : '';
      $year_type_str      = isset($row[20]) ? trim($row[20]) : 'Calendar Year';
      $sales_remark       = isset($row[21]) ? trim($row[21]) : '';
      $campaign_region    = isset($row[22]) ? trim($row[22]) : '';

      if (empty($society_name)) {
        $errors[] = "Row $rowNum: Company Name is required.";
        continue;
      }
      if (empty($secretary_name)) {
        $errors[] = "Row $rowNum: Admin Name  is required.";
        continue;
      }
      if (empty($secretary_mobile)) {
        $errors[] = "Row $rowNum: Admin Mobile is required.";
        continue;
      }
      if (empty($secretary_email)) {
        $errors[] = "Row $rowNum: Admin Email  is required.";
        continue;
      }
      if (empty($sales_person_name)) {
        $errors[] = "Row $rowNum: Sales Person Name is required.";
        continue;
      }
      if (empty($implementation_name)) {
        $errors[] = "Row $rowNum: Implementation Executive Name is required.";
        continue;
      }
      if (!in_array($implementation_name, $adminArray)) {
        $errors[] = "Row $rowNum: Implementation Executive Name is not matching to any admin.";
        continue;
      }
      if (empty($sales_closure_date)) {
        $errors[] = "Row $rowNum: Sales Closure Date is required (use dd-mm-yyyy or yyyy-mm-dd).";
        continue;
      }
      if (empty($support_name)) {
        $errors[] = "Row $rowNum: Support Person Name is required.";
        continue;
      }
      if (!in_array($support_name, $adminArray)) {
        $errors[] = "Row $rowNum: Support Person Name is not matching to any admin.";
        continue;
      }
      if (empty($support_mobile_no)) {
        $errors[] = "Row $rowNum: Support Person Mobile is required.";
        continue;
      }
      if ($campaign_region === '') {
        $errors[] = "Row $rowNum: Campaign Region is required.";
        continue;
      }
      if (!empty($validRegionNames) && !in_array($campaign_region, $validRegionNames, true)) {
        $errors[] = "Row $rowNum: Campaign Region not found: " . $campaign_region;
        continue;
      }

      // Validate employee_tracking_limit <= employee_registration_limit (must be first)
      $emp_registration = (int)$employee_registration_limit;
      $emp_tracking = (int)$employee_tracking_limit;
      if ($emp_tracking > $emp_registration) {
        $errors[] = "Row $rowNum: Employee Tracking Limit ($emp_tracking) cannot be greater than Employee Registration Limit ($emp_registration).";
        continue;
      }

      // State: get state_id by name (country_id = 101)
      $stateKey = strtolower(trim($state_name));
      if (!isset($statesByName[$stateKey])) {
        $errors[] = "Row $rowNum: State not found: " . $state_name;
        continue;
      }
      $state_id = $statesByName[$stateKey];

      // City: get city_id by name and state_id
      $cityKey = $state_id . '|' . strtolower(trim($city_name));
      if (!isset($citiesByStateAndName[$cityKey])) {
        $errors[] = "Row $rowNum: City not found: " . $city_name . " (State: $state_name)";
        continue;
      }
      $cityRow = $citiesByStateAndName[$cityKey];
      $city_id = $cityRow['city_id'];
      $domain_id = isset($cityRow['domain_id']) ? $cityRow['domain_id'] : null;

      // Domain prefix from city
      $domain_prefix = '';
      if (!empty($domain_id) && isset($domainsById[(int)$domain_id])) {
        $domain_prefix = rtrim($domainsById[(int)$domain_id], '/') . '/';
      }

      if (empty($domain_prefix)) {
        $errors[] = "Row $rowNum: No domain configured for city. Skipping.";
        continue;
      }

      $end_url = slugCompanyName($society_name, 27);
      $sub_domain = rtrim($domain_prefix, '/') . '/' . $end_url . '/';

      // Check duplicate sub_domain (prefetched + in-batch)
      if (isset($existingSubDomains[$sub_domain])) {
        $errors[] = "Row $rowNum: Company URL already exists: $sub_domain";
        continue;
      }
      if (isset($pendingSubDomains[$sub_domain])) {
        $errors[] = "Row $rowNum: Company request already pending for URL: $sub_domain";
        continue;
      }

      // from_rise_event: Yes -> 1, else -> 0
      $from_rise_event = (strtoupper($rise_event) === 'YES') ? '1' : '0';

      // Industry type: get b_id from business_entity_master by name
      $industry_type = '';
      if (!empty($industry_type_name)) {
        $indKey = strtolower(trim($industry_type_name));
        if (isset($businessByName[$indKey])) {
          $industry_type = $businessByName[$indKey];
        } else {
          $errors[] = "Row $rowNum: Industry Type not found: " . $industry_type_name;
          continue;
        }
      } else {
        $errors[] = "Row $rowNum: Industry Type is required.";
        continue;
      }
      $leadSources = [
          1 => 'Meta',
          2 => 'Inbound',
          3 => 'Walk IN',
          4 => 'Cold Data',
          5 => 'BA / Director Reference',
          6 => 'BNI Reference',
          7 => 'Event - Exhibitor',
          8 => 'Event - Exhibitor ( Exhibitor Cards )',
          9 => 'Event - Exhibitor ( Visitor Cards )',
          10 => 'Event - Industry Specific',
          11 => 'Event - Networking',
          12 => 'Existing Client',
          13 => 'Nikseam BPO',
          14 => 'Old Lead ( Any Source)',
          15 => 'Personal Reference',
          16 => 'Reference from demo client',
          17 => 'Reference from existing client',
          18 => 'Reference from Implementation team',
          19 => 'Rise',
          20 => 'Tech Imply',
          21 => 'Tech Jockey',
          22 => 'Website / Landing Page',
          23 => 'Website / Landing Page / ChatBot /MyCo App',
          24 => 'Whatsapp Bulkshoot'
      ];
      
      // build reverse map
      $lead_sources_map = [];
      foreach ($leadSources as $id => $label) {
          $normalized = preg_replace('/[^a-z]/', '', strtolower($label));
          $lead_sources_map[$normalized] = $id;
      }
      
      // default
      $lead_sources = 1;
      
      // normalize input SAME way
      if (!empty($lead_sources_str)) {
          $key = preg_replace('/[^a-z]/', '', strtolower($lead_sources_str));
      
          if (isset($lead_sources_map[$key])) {
              $lead_sources = $lead_sources_map[$key];
          }
      }

      // Year type: 0 = Calendar Year, 1 = Financial
      $calender_type = 0;
      if (!empty($year_type_str)) {
        $key = strtolower(trim($year_type_str));
        if (isset($year_type_map[$key])) {
          $calender_type = $year_type_map[$key];
        }
      }

      // Trial days max 100
      $trial_days = min(100, max(0, (int)$trial_days));

      // request_plan_expire_date from trial_days (same as manual add: today + (trial_days - 1) days)
      $request_plan_expire_date = '';
      if ($trial_days > 0) {
        $request_plan_expire_date = date('Y-m-d', strtotime('+' . ($trial_days - 1) . ' days'));
      }

      // implementation_name and support_name: store as name (from bms_admin_master by name - optional validation)
      // We store the name as given; no need to resolve to id

      $request_added_by = $bms_admin_id;
      $requested_date = date("Y-m-d H:i:s");
      $created_date = date("Y-m-d H:i:s");

      // Re-validate employee_tracking_limit <= employee_registration_limit before insert
      if ((int)$employee_tracking_limit > (int)$employee_registration_limit) {
        $errors[] = "Row $rowNum: Employee Tracking Limit ($employee_tracking_limit) cannot be greater than Employee Registration Limit ($employee_registration_limit).";
        continue;
      }

      $a = [
        'request_society_name' => $society_name,
        'account_type' => 0,
        'request_society_address' => $society_address,
        'request_society_latitude' => '23.0242625',
        'request_society_longitude' => '72.5720625',
        'request_secretary_name' => $secretary_name,
        'request_secretary_email' => $secretary_email,
        'country_code' => '+91',
        'request_secretary_mobile' => $secretary_mobile,
        'request_society_logo' => '',
        'society_type' => 0,
        'request_builder_name' => '',
        'request_country_id' => $country_id,
        'request_state_id' => $state_id,
        'request_city_id' => $city_id,
        'request_society_pincode' => '0',
        'request_sub_domain' => $sub_domain,
        'request_api_key' => $keydb,
        'currency' => '₹',
        'request_builder_address' => '',
        'request_builder_mobile' => '',
        'request_package_id' => 0,
        'payment_status' => 0,
        'payment_amount' => 0,
        'request_trial_days' => $trial_days,
        'employee_tracking_limit' => (int)$employee_tracking_limit,
        'expected_team_size' => (int)$expected_team_size,
        'employee_registration_limit' => (int)$employee_registration_limit,
        'per_employee_price' => $per_employee_price,
        'sales_person_name' => $sales_person_name,
        'sales_closure_date' => $sales_closure_date,
        'reference_from' => '',
        'request_plan_expire_date' => $request_plan_expire_date,
        'request_last_renew_date' => date('Y-m-d'),
        'request_added_by' => $request_added_by,
        'requested_date' => $requested_date,
        'created_date' => $created_date,
        'calender_type' => $calender_type,
        'yearly_ticket_size' => 0,
        'received_ticket_size' => 0,
        'implementation_name' => $implementation_name,
        'payment_mode' => 0,
        'payment_attachment' => '',
        'gst_number' => '',
        'industry_type' => $industry_type,
        'society_rating' => 0,
        'support_name' => $support_name,
        'support_country_code' => '+91',
        'support_mobile_no' => $support_mobile_no,
        'training_type' => 1,
        'remark' => '',
        'society_remark' => '',
        'sales_remark' => ($sales_remark !== '') ? $sales_remark : 'Bulk Upload',
        'implementation_remark' => '',
        'requests_lead_sources' => $lead_sources,
        'request_region_name' => $campaign_region,
        'request_search_society_code' => 0,
        'request_society_code' => '',
        'from_rise_event' => $from_rise_event,
      ];

      $q = $d->insert("society_master_requests", $a);
      if ($q) {
        $inserted++;
        $pendingSubDomains[$sub_domain] = true;
        $request_society_id = mysqli_insert_id($con);
        $d->insert_log_specific("0", $bms_admin_id, $created_by, "<b>$society_name</b> - Company Creation Request Added (Bulk CSV)", 3);
      } else {
        $errors[] = "Row $rowNum: Insert failed for: " . $society_name;
      }
    }
    fclose($handle);
  } else {
    $_SESSION['bulk_csv_errors'] = ["Could not read CSV file."];
    header("location:../addCompany");
    exit();
  }

  // If any errors: redirect to building with full error list (show once via session)
  if (count($errors) > 0) {
    $_SESSION['bulk_csv_errors'] = $errors;
    if ($inserted > 0) {
      $_SESSION['bulk_csv_success'] = "Bulk upload: $inserted company request(s) added.";
    }
    header("location:../addCompany");
    exit();
  }

  if ($inserted == 0 && count($errors) == 0) {
    $_SESSION['bulk_csv_errors'] = ["No valid rows in CSV."];
    header("location:../addCompany");
    exit();
  }
  $society_name = "Bulk Trial Plan Company Request";
  $to = array();
  $added_by = $admin_name;
  $query = $d->select("society_create_email_receipt", "active_status=0");
  while ($data = mysqli_fetch_array($query)) {
    if ($data['email_id'] != '') {
      array_push($to, $data['email_id']);
    }
  }
  $subject = "Creation of Bulk Company Requested";

  include '../mail/newSocietyCreateMail.php';
  include '../mail.php';


  $admins = $d->select("admin_fcm_notification_master", "fcm_notifications=3");
  $total_admins = mysqli_num_rows($admins);
  if ($total_admins > 0) {
    $noti_title = "Bulk Trial Plan Company Request";
    $click_action = "manageCompanyRequests";
    $noti_description = $subject;
    $tokenwhere = "";
    $ik = 0;
    while ($adminsdata = mysqli_fetch_array($admins)) {
      $admin_id = $adminsdata['bms_admin_id'];
      if ($ik > 0) {
        $tokenwhere .= " OR ";
      }
      $tokenwhere .= "admin_id=$admin_id";
      $ik++;
    }
    $getTokens = $d->getWebFcm("web_fcm_master", "$tokenwhere");
    $nAdmin->sendAdminNotification($getTokens, $noti_title, $noti_description, $click_action);
  }

  $_SESSION['msg'] = "Bulk upload: $inserted company request(s) added.";
  header("location:../manageCompanyRequests");
  exit();
}

header("location:../addCompany");
exit();
