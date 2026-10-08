<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
if (defined('CRON_CREATE_SOCIETY_MODE') && CRON_CREATE_SOCIETY_MODE) {
  $redirect_allowed = false;
  session_start();
  date_default_timezone_set('Asia/Kolkata');
  extract($_POST);
} else {
  $redirect_allowed = true;
  include '../common/objectController.php';
}
if (isset($createSoceitySubdomain) && $createSoceitySubdomain != '') {
  $is_whitelabel = $d->is_whitelabel();
  $qc = $d->selectRow("society_master.*,server_master.server_ip,domain_master.domain_name", "society_master JOIN domain_master ON society_master.domain_id = domain_master.domain_id JOIN server_master ON server_master.server_id = domain_master.server_id", "society_id='$society_id'");
  $data = mysqli_fetch_array($qc);
  extract($data);

  if ($is_whitelabel == "false") {

    $txt = "Sh File Script Start " . date("Y-m-d h:i:s A");
    if (isset($redirect_allowed) && $redirect_allowed) {
      $myfile = file_put_contents('../../img/companyCreationLogs.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
    } else {
      $myfile = file_put_contents('../img/companyCreationLogs.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    $remote_host = escapeshellarg("$server_ip");
    $new_domain_name = str_replace("https://", "", $sub_domain);
    $new_domain_name = rtrim($new_domain_name, '/');
    $company_url = escapeshellarg("$new_domain_name");
    $secrete_key = escapeshellarg('f3N2n2IelsKZhHB');
    $scriptPath = '/var/ps/mastercompany.sh';
    $cmd = "sudo $scriptPath $remote_host $company_url $secrete_key 2>&1";

    $descriptorspec = array(
      0 => array("pipe", "r"),
      1 => array("pipe", "w"),
      2 => array("pipe", "w")
    );

    $process = proc_open($cmd, $descriptorspec, $pipes);
    if (is_resource($process)) {
      fclose($pipes[0]);
      $output = stream_get_contents($pipes[1]);
      $error = stream_get_contents($pipes[2]);
      fclose($pipes[1]);
      fclose($pipes[2]);
      $return_value = proc_close($process);
      $log_message = "Sh File Script Start " . date("Y-m-d h:i:s A") . PHP_EOL;
      $log_message .= "Command: $cmd" . PHP_EOL;
      $log_message .= "Output: $output" . PHP_EOL;
      if ($error) {
        $log_message .= "Error: $error" . PHP_EOL;
      }
      $log_message .= "Return Code: $return_value" . PHP_EOL;
      $log_message .= "Sh File Script End " . date("Y-m-d h:i:s A") . PHP_EOL;

      if (isset($redirect_allowed) && $redirect_allowed) {
        $myfile = file_put_contents('../../img/companyCreationLogs.txt', $log_message, FILE_APPEND | LOCK_EX);
      } else {
        $myfile = file_put_contents('../img/companyCreationLogs.txt', $log_message, FILE_APPEND | LOCK_EX);
      }
      if ($return_value != 0) {
        if (isset($redirect_allowed) && $redirect_allowed) {
          $_SESSION['msg1'] = "Shell script execution failed with return code: $return_value";
          header("location:../pendingCompanies");
          exit();
        } else {
          $log("Shell script execution failed with return code: $return_value");
          exit();
        }
      }
    } else {
      if (isset($redirect_allowed) && $redirect_allowed) {
        $_SESSION['msg1'] = "Failed to execute shell script";
        header("location:../pendingCompanies");
        exit();
      } else {
        $log('Failed to execute shell script');
        exit();
      }
    }
  }


  $bucket_configuration_qry = $d->selectRow("bucket_configuration.*", "bucket_configuration", "");
  $bucket_configuration_data = array();
  if (mysqli_num_rows($bucket_configuration_qry) > 0) {
    $bucket_configuration_data = mysqli_fetch_assoc($bucket_configuration_qry);
    $bucket_access_key = $bucket_configuration_data['bucket_access_key'];
    $bucket_secret_access_key = $bucket_configuration_data['bucket_secret_access_key'];
    $bucket_region = $bucket_configuration_data['bucket_region'];
    $bucket_name = $bucket_configuration_data['bucket_name'];
    $bucket_service = $bucket_configuration_data['bucket_service'];
    $master_bucket_url = $bucket_configuration_data['master_bucket_url'];
  }

  $qmm = $d->selectRow("auth_passs", "master_user_auth_master", "", "LIMIT 1");
  $rowAuth = mysqli_fetch_array($qmm);
  $masterAuth = $rowAuth['auth_passs'];
  $society_logo = $socieaty_logo;
  $post_data = [
    "society_id" => $society_id,
    "society_name" => $society_name,
    "society_address" => $society_address,
    "society_pincode" => $society_pincode,
    "soiciety_latitude" => $society_latitude,
    "sociaty_longitude" => $society_longitude,
    "secretary_email" => $secretary_email,
    "secretary_mobile" => $secretary_mobile,
    "socieaty_logo" => $society_logo,
    "society_type" => $society_type,
    "builder_name" => $builder_name,
    "country_id" => $country_id,
    "state_id" => $state_id,
    "city_id" => $city_id,
    "city_name" => $city_name,
    "sub_domain" => $sub_domain,
    "builder_address" => $builder_address,
    "builder_mobile" => $builder_mobile,
    "package_id" => $package_id,
    "trial_days" => $trial_days,
    "start_date" => $start_date,
    "api_key" => $api_key,
    "secretary_name" => $secretary_name,
    "plan_expire_date" => $plan_expire_date,
    "last_renew_date" => $last_renew_date,
    "currency" => $currency,
    "masterAuth" => $masterAuth,
    "country_code" => "$country_code",
    "calender_type" => $calender_type,
    "employee_tracking_limit" => $employee_tracking_limit,
    "employee_registration_limit" => $employee_registration_limit,
    "tracking_status" => $tracking_status,
    "society_settings" => $society_settings,
    "bucket_access_key" => $bucket_access_key ?? '',
    "bucket_secret_access_key" => $bucket_secret_access_key ?? '',
    "bucket_region" => $bucket_region ?? '',
    "bucket_name" => $bucket_name ?? '',
    "bucket_service" => $bucket_service ?? '',
    "master_bucket_url" => $master_bucket_url ?? '',
  ];

  $target_path = $sub_domain . 'apAdmin/controller/buildingControllerAuto.php';
  if ($is_whitelabel == false) {
    $max_attempts = 15;
  } else {
    $max_attempts = 1;
  }
  $sleep_seconds = 5;
  $is_ready = false;
  for ($attempt = 1; $attempt <= $max_attempts; $attempt++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sub_domain . 'apAdmin/controller/buildingControllerAuto.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    $json = json_decode($server_output, true);
    $health_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($health_code >= 200 && $health_code < 600 && $health_code != 000) {
      $is_ready = true;
      if (isset($redirect_allowed) && $redirect_allowed) {
        file_put_contents('../../img/companyCreationLogs.txt', "Health attempt (Success) $attempt/$max_attempts code:$health_code at " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND | LOCK_EX);
      } else {
        file_put_contents('../img/companyCreationLogs.txt', "Health attempt (Success) $attempt/$max_attempts code:$health_code at " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND | LOCK_EX);
      }
      sleep($sleep_seconds);
      break;
    }
    if ($is_whitelabel == false) {
      if (isset($redirect_allowed) && $redirect_allowed) {
        file_put_contents('../../img/companyCreationLogs.txt', "Health attempt $attempt/$max_attempts code:$health_code at " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND | LOCK_EX);
      } else {
        file_put_contents('../img/companyCreationLogs.txt', "Health attempt $attempt/$max_attempts code:$health_code at " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND | LOCK_EX);
      }
      sleep($sleep_seconds);
    } else {
      if (isset($redirect_allowed) && $redirect_allowed) {
        $_SESSION['msg1'] = "Target subdomain not ready after health checks";
        header("location:../pendingCompanies");
        exit();
      } else {
        $log('Target subdomain not ready after health checks');
        exit();
      }
    }
  }
  echo "json: " . json_encode($json);
  if (isset($json) && count($json) > 0) {
    if ($json['status'] == 200) {
      // success created
      $_SESSION['msg'] = $json['message'];
      // add data in master 
      // run synchronization cron to get in master table 

      // find domain

      $charge = explode('/', $sub_domain);
      $charge = $charge[2]; //assuming that the url starts with http:// or https://
      $domainTemp = "https://" . $charge;


      $qdomain = $d->selectRow("domain_id", "domain_master", "domain_name LIKE '%$domainTemp%'");
      $domainData = mysqli_fetch_array($qdomain);
      $domain_id = $domainData['domain_id'];

      $a1 = array('domain_id' => $domain_id, 'created_on_society_server' => 1);
      $d->update('society_master', $a1, "sub_domain='$sub_domain'");

      $aP = array(
        'society_id' => $society_id,
        'auth_password' => $masterAuth,
        'result' => 'Password Changed',
        'status' => '200',
        'created_at' => date('Y-m-d H:i:s')
      );
      $qcd = $d->select("auth_log_master", "auth_password='$masterAuth' AND society_id ='$society_id'");

      if (mysqli_num_rows($qcd) > 0) {
        $d->update("auth_log_master", $aP, "auth_password='$masterAuth' AND society_id ='$society_id'");
      } else {
        $d->insert("auth_log_master", $aP);
      }
      // $d->update('society_master',$a1,"society_id='$society_id'");
      if (isset($redirect_allowed) && $redirect_allowed) {
        header("location:../createdCompanyRequests");
        exit();
      } else {
        $log('Created successfully');
        exit();
      }
    } else {

      //error message in from main server
      if (isset($redirect_allowed) && $redirect_allowed) {
        $_SESSION['msg1'] = $json['message'];
        header("location:../pendingCompanies");
        exit();
      } else {
        $log("Something wrong - $health_code" . $json['message']);
        exit();
      }
    }
  } else {
    if (isset($redirect_allowed) && $redirect_allowed) {
      $_SESSION['msg1'] = "Something Wrong Null Json";
      header("location:../pendingCompanies");
      exit();
    } else {
      $log("Something wrong null json");
      exit();
    }
  }
} else if (isset($_POST['created_society'])) {
  if ($package_id == 0 && $package_id != '') {
    $q = $d->select("role_master", "role_id='2'", "");
    $row = mysqli_fetch_array($q);
    $menu_id = $row['menu_id'];
    $plan_expire_date = date('Y-m-d', strtotime(' +' . $trial_days . ' day'));
  }

  // Check duplicate sub_domain
  $sub_domain_esc = $d->escapeSqlString($sub_domain);
  $qcb = $d->select("society_master", "sub_domain='$sub_domain_esc'");
  if (mysqli_num_rows($qcb) > 0) {
    $_SESSION['msg1'] = "Company URL already exists: $sub_domain";
    header("location:../manageCompanyRequests");
    exit();
  }

  $qc = $d->selectRow("name", "cities", "city_id='$city_id'");
  $cityData = mysqli_fetch_array($qc);
  $city_name = $cityData['name'];

  if ($employee_tracking_limit > 0) {
    $tracking_status = 1;
  }
  $society_settings = json_encode([
    'face_attendance_delete_days' => 190,
    'work_report_delete_days' => 390,
    'visit_attachment_delete_days' => 70,
    'expense_attachment_delete_days' => 90,
    'chat_attachment_delete_days' => 200,
    'task_attachment_delete_days' => 390,
    'circular_attachment_delete_days' => 180,
    'discussion_attachment_delete_days' => 360,
    'meeting_attachment_delete_days' => 180,
    'tracking_attachment_delete_months' => 120
  ]);

  $charge = explode('/', $sub_domain);
  $charge = $charge[2]; //assuming that the url starts with http:// or https://
  $domainTemp = "https://" . $charge;

  $qdomain = $d->selectRow("domain_id", "domain_master", "domain_name LIKE '%$domainTemp%'");
  $domainData = mysqli_fetch_array($qdomain);
  $domain_id = $domainData['domain_id'];

  if (!isset($domain_id) || $domain_id == "") {
    $_SESSION['msg'] = "Domain Not Found.!";
    header("location:../manageCompanyRequests");
    exit();
  }

  $m->set_data('domain_id', $domain_id);
  $m->set_data('society_name', $society_name_edit);
  // Jainish Start
  $m->set_data('account_type', $account_type);
  // Jainish End
  $m->set_data('society_address', $society_address);
  $m->set_data('society_pincode', $society_pincode);
  $m->set_data('society_latitude', $society_latitude);
  $m->set_data('society_longitude', $society_longitude);
  $m->set_data('secretary_email', $secretary_email);
  $m->set_data('secretary_mobile', $secretary_mobile);
  $m->set_data('country_code', $country_code);
  $m->set_data('secretary_name', $secretary_name);
  $m->set_data('socieaty_logo', $society_logo_old);
  $m->set_data('builder_name', $builder_name);
  $m->set_data('country_id', $country_id);
  $m->set_data('state_id', $state_id);
  $m->set_data('city_id', $city_id);
  $m->set_data('state_id', $state_id);
  $m->set_data('city_name', $city_name);
  $m->set_data('sub_domain', $sub_domain);
  $m->set_data('builder_address', $builder_address);
  $m->set_data('builder_mobile', $builder_mobile);
  $m->set_data('package_id', $package_id);
  $m->set_data('trial_days', $trial_days);
  $m->set_data('employee_tracking_limit', $employee_tracking_limit);
  $m->set_data('tracking_status', $tracking_status);
  $m->set_data('employee_registration_limit', $employee_registration_limit);
  $m->set_data('expected_team_size', $expected_team_size);
  $m->set_data('per_employee_price', $per_employee_price);
  $m->set_data('sales_person_name', $sales_person_name);
  $m->set_data('sales_closure_date', $sales_closure_date);
  $m->set_data('reference_from', $reference_from);
  $m->set_data('plan_expire_date', $plan_expire_date);
  $m->set_data('last_renew_date', $last_renew_date);
  $m->set_data('api_key', $keydb);
  $m->set_data('currency', $currency);
  $m->set_data('society_type', $society_type);
  $m->set_data('created_date', date("Y-m-d H:i:s"));
  $m->set_data('calender_type', $calender_type);
  $m->set_data('yearly_ticket_size', $yearly_ticket_size);
  $m->set_data('received_ticket_size', $received_ticket_size);
  $m->set_data('implementation_name', $implementation_name);
  $m->set_data('payment_mode', $payment_mode);
  $m->set_data('payment_attachment', $payment_attachment_old);
  $m->set_data('industry_type', $industry_type);
  $m->set_data('gst_number', strtoupper($gst_number));
  $m->set_data('society_settings', $society_settings);
  $m->set_data('lead_sources', $lead_sources);
  $m->set_data('region', $region);
  $m->set_data('search_society_code', $search_society_code);
  $m->set_data('society_code', $society_code);
  $m->set_data('from_rise_event', $from_rise_event ?? 0);
  $society_request_query = $d->selectRow("society_rating,support_name,support_country_code,support_mobile_no", "society_master_requests", "request_sub_domain='$sub_domain' AND request_society_create_status=0");
  if (mysqli_num_rows($society_request_query) > 0) {
    $society_request_data = mysqli_fetch_array($society_request_query);
    $support_name = $society_request_data['support_name'];
    $support_country_code = $society_request_data['support_country_code'];
    $support_mobile_no = $society_request_data['support_mobile_no'];
    $society_rating = $society_request_data['society_rating'];
  }
  $m->set_data('society_rating', $society_rating ?? 5);
  $m->set_data('support_name', $support_name ?? "");
  $m->set_data('support_country_code', $support_country_code ?? "");
  $m->set_data('support_mobile_no', $support_mobile_no ?? "");


  $a = array(
    'domain_id' => $m->get_data('domain_id'),
    'society_type' => $m->get_data('society_type'),
    'society_name' => $m->get_data('society_name'),
    // Jainish Start
    'account_type' => $m->get_data('account_type'),
    // Jainish End
    'society_address' => $m->get_data('society_address'),
    'support_name' => $m->get_data('support_name'),
    'support_country_code' => $m->get_data('support_country_code'),
    'support_mobile_no' => $m->get_data('support_mobile_no'),
    'society_pincode' => $m->get_data('society_pincode'),
    'society_latitude' => $m->get_data('society_latitude'),
    'society_longitude' => $m->get_data('society_longitude'),
    'secretary_email' => $m->get_data('secretary_email'),
    'secretary_mobile' => $m->get_data('secretary_mobile'),
    'country_code' => $m->get_data('country_code'),
    'secretary_name' => $m->get_data('secretary_name'),
    'socieaty_logo' => $m->get_data('socieaty_logo'),
    'builder_name' => $m->get_data('builder_name'),
    'country_id' => $m->get_data('country_id'),
    'state_id' => $m->get_data('state_id'),
    'city_id' => $m->get_data('city_id'),
    'city_name' => $m->get_data('city_name'),
    'sub_domain' => $m->get_data('sub_domain'),
    'api_key' => $m->get_data('api_key'),
    'currency' => $m->get_data('currency'),
    'builder_address' => $m->get_data('builder_address'),
    'builder_mobile' => $m->get_data('builder_mobile'),
    'package_id' => $m->get_data('package_id'),
    'trial_days' => $m->get_data('trial_days'),
    'tracking_status' => $m->get_data('tracking_status'),
    'employee_tracking_limit' => $m->get_data('employee_tracking_limit'),
    'employee_registration_limit' => $m->get_data('employee_registration_limit'),
    'expected_team_size' => $m->get_data('expected_team_size'),
    'per_employee_price' => $m->get_data('per_employee_price'),
    'sales_person_name' => $m->get_data('sales_person_name'),
    'sales_closure_date' => $m->get_data('sales_closure_date'),
    'reference_from' => $m->get_data('reference_from'),
    'plan_expire_date' => $m->get_data('plan_expire_date'),
    'last_renew_date' => $m->get_data('last_renew_date'),
    'created_date' => $m->get_data('created_date'),
    'calender_type' => $m->get_data('calender_type'),
    'yearly_ticket_size' => $m->get_data('yearly_ticket_size'),
    'received_ticket_size' => $m->get_data('received_ticket_size'),
    'implementation_name' => $m->get_data('implementation_name'),
    'payment_mode' => $m->get_data('payment_mode'),
    'payment_attachment' => $m->get_data('payment_attachment'),
    'gst_number' => $m->get_data('gst_number'),
    'industry_type' => $m->get_data('industry_type'),
    'society_settings' => $m->get_data('society_settings'),
    'lead_sources' => $m->get_data('lead_sources'),
    'region_name' => $m->get_data('region'),
    'search_society_code' => $m->get_data('search_society_code'),
    'society_code' => $m->get_data('society_code'),
    'from_rise_event' => $m->get_data('from_rise_event'),
    'society_rating' => $m->get_data('society_rating'),
  );

  $q = $d->insert("society_master", $a);
  $society_id = $d->getInsertId();

  if ($q > 0) {
    $cQuery = $d->selectRow(
      "manage_plan.plan_value, manage_plan.plan_name",
      "manage_plan",
      "plan_value = '$package_id'"
    );

    if (mysqli_num_rows($cQuery) > 0) {
      $dataPlan = mysqli_fetch_array($cQuery);
      $maonthNameView = $dataPlan['plan_name'];
    } else {
      $maonthNameView = "Custome Plan";
    }

    $package_name = "" . $d->app_name() . " New Company Registration for $maonthNameView ($plan_expire_date)";

    $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20) . $society_id;
    $hash = hash("sha512", $retHashSeq);
    $invoice_no = "0" . date('ymdi') . $udf1;


    $a122 = array(
      'society_id' => $society_id,
      'package_id' => $package_id,
      'package_name' => $package_name,
      'user_mobile' => $secretary_mobile,
      'payment_mode' => $payment_mode,
      'payment_attachment' => $payment_attachment,
      'transection_amount' => $amountReceived,
      'transection_date' => date('Y-m-d H:i:s'),
      'payment_status' => "success",
      'payment_firstname' => $society_name,
      'payment_phone' => $secretary_mobile,
      'payment_email' => $secretary_email,
      'invoice_no' => $invoice_no,
      'received_by' => $sales_person_name,
      'payment_txnid' => $txnid,
      'is_renewal' => 0,
      'plan_change_by_id' => $request_added_by,
      'plan_changed_by' => $created_by,
    );
    if ($package_id == 0) {
      $a122['is_renewal'] = 2;
    }

    $qq = $d->selectRow("payment_txnid", "transection_master", "payment_txnid='$txnid'");
    $oT = mysqli_fetch_array($qq);
    if ($oT > 0) {
      $d->update("transection_master", $a122, "payment_txnid='$txnid'");
    } else {
      $d->insert("transection_master", $a122);
    }
    // success created
    $_SESSION['msg'] = "Created Successfully";

    $m->set_data('created_date', date("Y-m-d H:i:s"));
    $m->set_data('society_id_added', $society_id);
    $a2 = array(
      'request_society_create_status' => 1,
      'society_id_added' => $m->get_data('society_id_added'),
      'created_date' => $m->get_data('created_date'),
      // Jainish Start
      'account_type' => $m->get_data('account_type'),
      // Jainish End
    );
    $d->update('society_master_requests', $a2, "request_sub_domain='$sub_domain' AND request_society_create_status=0");

    $d->insert_app_menu($society_id);
    $is_whitelabel = $d->is_whitelabel();
    if ($is_whitelabel == "false") {
      // Regular Analytics cron (limit 300)
      $d->addCompanyToCronCategory($society_id, 'Analytics', array(
        'curl_script' => 'cron/curl2.php',
        'cron_limit' => 300,
        'send_mail' => true,
      ));
      // W_Morning / W_Evening are synced when WhatsApp employee access is granted/removed
    }
    header("location:../createdCompanyRequests");
    exit();
  } else {
    $_SESSION['msg'] = "Something Wrong";
    header("location:../manageCompanyRequests");
  }
} else if (isset($is_welcome_email) && $is_welcome_email == 'is_welcome_email' && isset($society_id_sendemail) && $society_id_sendemail != '') {
  $society_id = $_POST['society_id_sendemail'];
  $email_send = false;
  $emailQuery = $d->selectRow("support_name,support_mobile_no,secretary_email,secretary_name", "society_master", "society_id='$society_id'");
  $emailData = mysqli_fetch_assoc($emailQuery);
  $email_id = $emailData['secretary_email'];
  $to = $email_id;
  $subject = "Welcome to " . $d->app_name() . " Let's Begin Your Onboarding Journey!";
  $amount = $receivedamount;
  if ($pay_mode == 1) {
    $modeOfPayment = 'Online Bank Transfer';
  } elseif ($pay_mode == 2) {
    $modeOfPayment = 'Cheque';
  } elseif ($pay_mode == 3) {
    $modeOfPayment = 'UPI';
  } elseif ($pay_mode == 4) {
    $modeOfPayment = 'Cash';
  } else {
    $modeOfPayment = 'Bank';
  }
  $attachments = [];
  if (isset($_FILES['payment_attachment'])) {
    $uploadDir = '../../img/';
    $totalFiles = count($_FILES['payment_attachment']['name']);
    for ($i = 0; $i < $totalFiles; $i++) {
      if ($_FILES['payment_attachment']['error'][$i] === 0) {
        $originalName = basename($_FILES['payment_attachment']['name'][$i]);
        $fileName = "Tax_Invoice_" . time() . "_" . $i . "_" . $originalName;
        $targetFilePath = $uploadDir . $fileName;
        $targetFileFullPath = $m->base_url() . 'img/' . $fileName; //$base_url is "http://192.168.5.38/masterMyco/";
        if (move_uploaded_file($_FILES['payment_attachment']['tmp_name'][$i], $targetFilePath)) {
          $attachments[] = $targetFilePath;
        } else {
          $_SESSION['msg1'] = "File upload failed for file: $originalName";
          header("location:" . getCompanyOnboardingRedirectUrl());
          exit();
        }
      }
    }
  }

  $ccEmailsJson = $_POST['cc_emails']; // this comes from the Tagify input
  $cc = [];

  if (!empty($ccEmailsJson)) {
    $decoded = json_decode($ccEmailsJson, true);
    if (is_array($decoded)) {
      foreach ($decoded as $emailItem) {
        if (isset($emailItem['value']) && filter_var($emailItem['value'], FILTER_VALIDATE_EMAIL)) {
          $cc[] = trim($emailItem['value']);
        }
      }
    }
  }


  $welcomeTemplateId = isset($_POST['welcome_template_id']) ? trim($_POST['welcome_template_id']) : '';
  $welcomeProductType = isset($_POST['welcome_product_type']) ? trim($_POST['welcome_product_type']) : '';
  $templateId = '';

  if ($welcomeTemplateId !== '') {
    $templateId = $welcomeTemplateId;
  } else if ($emailType == 1 && $receivedamount != "" && $receivedamount > 0) {
    $templateId = '1';
  } else if ($emailType == 2 || $receivedamount == "" || $receivedamount <= 0) {
    $templateId = '2';
  } else {
    $_SESSION['msg1'] = "Something Went Wrong";
    header("location:" . getCompanyOnboardingRedirectUrl());
    exit();
  }
  $query = $d->selectRow("template_sub,template_text", "template_master", "template_id = '" . $templateId . "'");
  if (mysqli_num_rows($query) > 0) {
    $data = mysqli_fetch_assoc($query);
    $subject = $data['template_sub'];
    $content = $data['template_text'];
    $content = str_replace("{Client's Name}", "" . $emailData['secretary_name'], $content);
    $content = str_replace("{Contact Number}", "" . $emailData['support_mobile_no'], $content);
    $content = str_replace("{Your Name}", "" . $emailData['support_name'], $content);
    $content = str_replace("{Implementation Manager's Name}", "" . $emailData['support_name'], $content);
    $content = str_replace("{Your Contact Information}", "" . $emailData['support_mobile_no'], $content);
    $isInvoiceEmail = ($emailType == 1) || ((int)$templateId === 3);
    if ($isInvoiceEmail) {
      $content = str_replace("{Payment Method}", $modeOfPayment, $content);
      $content = str_replace("{Amount}", $amount, $content);
    }
    include '../mail/welcomeMail.php';
    include '../mail.php';
    $email_send = true;
    if (isset($attachments) && is_array($attachments)) {
      foreach ($attachments as $filePath) {
        if (file_exists($filePath)) {
          unlink($filePath);
        }
      }
    }
  }
  if ($email_send == true) {
    $isCrmTemplate = in_array((int)$templateId, array(3, 4), true);
    if ($isCrmTemplate) {
      $a1 = array(
        'is_crm_welcome_email_send' => 1,
        'crm_welcome_email_send_date' => date('Y-m-d H:i:s'),
      );
    } else {
      $a1 = array(
        'is_welcome_email_send' => 1,
        'welcome_email_send_date' => date('Y-m-d H:i:s'),
      );
    }

    $q1 = $d->update("society_master", $a1, "society_id='$society_id'");
    if ($q1 == true) {
      $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "welcome Email send for society id $society_id");
      $_SESSION['msg'] = "Welcome Email Send Successfully";
      header("location:" . getCompanyOnboardingRedirectUrl());
      exit();
    } else {
      $_SESSION['msg1'] = "Something Went Wrong";
      header("location:" . getCompanyOnboardingRedirectUrl());
      exit();
    }
  } else {
    $_SESSION['msg1'] = "Something Went Wrong";
    header("location:" . getCompanyOnboardingRedirectUrl());
    exit();
  }
} else if (isset($is_whatsapp_group_created) && $is_whatsapp_group_created = 'is_whatsapp_group_created' && isset($society_id) && $society_id != '') {
  $a2 = array(
    'is_whatsapp_group_created' => 1,
    'whatsapp_group_created_date' => date('Y-m-d H:i:s'),
  );
  $q2 = $d->update("society_master", $a2, "society_id='$society_id'");
  if ($q2 == true) {
    $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Whatsapp Group Created for society id $society_id");
    $_SESSION['msg'] = "Yes! Whatsapp Group Created Successfully";
    header("location:" . getCompanyOnboardingRedirectUrl());
    exit();
  } else {
    $_SESSION['msg1'] = "Something Went Wrong";
    header("location:" . getCompanyOnboardingRedirectUrl());
    exit();
  }
} else if (isset($updateWhatsappGroupLink) && $updateWhatsappGroupLink == 'updateWhatsappGroupLink' && isset($society_id) && $society_id != '') {
  $society_id = (int)$society_id;
  $link = isset($whatsapp_group_link) ? trim((string)$whatsapp_group_link) : '';
  if (strlen($link) > 255) {
    $link = substr($link, 0, 255);
  }
  if ($link !== '' && !preg_match('#^https?://#i', $link)) {
    echo '0';
    exit();
  }
  $m->set_data('whatsapp_group_link', $link);
  $aLink = array(
    'whatsapp_group_link' => $m->get_data('whatsapp_group_link'),
  );
  $qLink = $d->update("society_master", $aLink, "society_id='$society_id'");
  if ($qLink == true) {
    $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "WhatsApp group join link updated for society id $society_id");
    echo '1';
  } else {
    echo '0';
  }
  exit();
} else if (isset($is_data_onboarding_completed) && $is_data_onboarding_completed = 'is_data_onboarding_completed' && isset($society_id_onboarding) && $society_id_onboarding != '') {
  $society_id = $_POST['society_id_onboarding'];
  $a3 = array(
    'data_onboarding_date' => $data_onboarding_date,
    'is_data_onboarding' => $is_data_onboarding,
  );
  $q3 = $d->update("society_master", $a3, "society_id='$society_id'");
  if ($q3 == true) {
    if ($is_data_onboarding == 1) {
      $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Data Onboarding process Pending for society id $society_id");
      $_SESSION['msg'] = "Data Onboarding process Pending.";
      header("location:" . getCompanyOnboardingRedirectUrl());
      exit();
    } else if ($is_data_onboarding == 2) {
      $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Data Onboarding process Completed for society id $society_id");
      $_SESSION['msg'] = "Data Onboarding process Completed.";
      header("location:" . getCompanyOnboardingRedirectUrl());
      exit();
    }
  } else {
    $_SESSION['msg1'] = "Something Went Wrong";
    header("location:" . getCompanyOnboardingRedirectUrl());
    exit();
  }
} else {
  $_SESSION['msg1'] = "Something Wrong";
  header("location:../logout.php");
}
function getCompanyOnboardingRedirectUrl()
{
  $filters = [];
  $filterKeys = ['countryId', 'sId', 'cId', 'training_type', 'rise_filter', 'product_type'];
  foreach ($filterKeys as $key) {
    $value = '';
    if (isset($_POST[$key]) && $_POST[$key] != '') {
      $value = $_POST[$key];
    } elseif (isset($_GET[$key]) && $_GET[$key] != '') {
      $value = $_GET[$key];
    } elseif (isset($_SESSION['companyOnboarding_filters'][$key]) && $_SESSION['companyOnboarding_filters'][$key] != '') {
      $value = $_SESSION['companyOnboarding_filters'][$key];
    }
    if ($key === 'countryId') {
      if ($value != '' && $value > 0) {
        $filters[$key] = $value;
      } elseif (!isset($filters[$key])) {
        $filters[$key] = 101;
      }
    } elseif ($key === 'product_type') {
      if ($value === 'crm' || $value === 'hrms') {
        $filters[$key] = $value;
      } elseif (!isset($filters[$key])) {
        $filters[$key] = 'hrms';
      }
    } elseif ($value != '') {
      $filters[$key] = $value;
    }
  }
  if (!isset($filters['countryId'])) {
    $filters['countryId'] = 101;
  }
  if (!isset($filters['product_type'])) {
    $filters['product_type'] = 'hrms';
  }
  $queryString = !empty($filters) ? '?' . http_build_query($filters) : '';
  return '../companyOnboarding' . $queryString;
}
