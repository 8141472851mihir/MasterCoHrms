<?php

include '../common/objectController.php';
if (isset($_POST) && !empty($_POST)) //it can be $_GET doesn't matter
{

  if (isset($_POST['society_name'])) {

    $qcb = $d->select("society_master", "sub_domain='$sub_domain'");
    if (mysqli_num_rows($qcb) > 0) {
      $_SESSION['msg1'] = "Company Already Created on this Url";
      header("location:../myCompanyRequests");
      exit();
    }

    $qcb11 = $d->select("society_master_requests", "request_sub_domain='$sub_domain' AND request_society_create_status=0");
    if (mysqli_num_rows($qcb11) > 0) {
      $_SESSION['msg1'] = "Company Request is Already Pending";
      header("location:../myCompanyRequests");
      exit();
    }

    $file_society_logo = $_FILES['society_logo']['tmp_name'];
    if (file_exists($file_society_logo)) {
      $acceptable = array('jpeg', 'jpg', 'png');
      $extId = pathinfo($_FILES['society_logo']['name'], PATHINFO_EXTENSION);
      $dirPath = "../../img/society_requests/";
      $maxsize    = 1097152;
      if (in_array($extId, $acceptable) && (!empty($_FILES["society_logo"]["type"]))) {
        $temp = explode(".", $_FILES["society_logo"]["name"]);
        $society_logo = 'Society_' . round(microtime(true)) . '.' . end($temp);
        $destinationPath = $dirPath . $society_logo;
        $d->resizeImage($file_society_logo, $destinationPath, 500, 500, $extId);
      } else {
        $_SESSION['msg1'] = "Invalid File format, Only JPEG, JPG and PNG are allowed.";
        header("location:../addCompany");
        exit();
      }
    } else {
      $society_logo = "";
    }

    $file_payment_attachment = $_FILES['payment_attachment']['tmp_name'];
    if (file_exists($file_payment_attachment)) {
      $acceptable = array('jpeg', 'jpg', 'png');
      $extId = pathinfo($_FILES['payment_attachment']['name'], PATHINFO_EXTENSION);
      $dirPath = "../../img/society_requests/";
      $maxsize    = 10097152;
      if (in_array($extId, $acceptable) && (!empty($_FILES["payment_attachment"]["type"]))) {
        $temp = explode(".", $_FILES["payment_attachment"]["name"]);
        $payment_attachment = 'Society_' . round(microtime(true)) . '.' . end($temp);
        $destinationPath = $dirPath . $payment_attachment;
        $d->resizeImage($file_payment_attachment, $destinationPath, 800, 600, $extId);
      } else {
        $_SESSION['msg1'] = "Invalid File format, Only JPEG, JPG and PNG are allowed.";
        header("location:../addCompany");
        exit();
      }
    } else {
      $payment_attachment = $payment_attachment_old;
    }

    $m->set_data('society_name', $society_name);
    // Jainish Start
    $m->set_data('account_type', $account_type);
    // Jainish End
    $m->set_data('society_address', $society_address);
    $m->set_data('society_latitude', $society_latitude);
    $m->set_data('society_longitude', $society_longitude);
    $m->set_data('secretary_name', $secretary_name);
    $m->set_data('secretary_email', $secretary_email);
    $m->set_data('country_code', $country_code);
    $m->set_data('secretary_mobile', $secretary_mobile);
    $m->set_data('society_logo', $society_logo);
    $m->set_data('society_type', $society_type);
    $m->set_data('builder_name', $builder_name);
    $m->set_data('country_id', $country_id);
    $m->set_data('state_id', $state_id);
    $m->set_data('city_id', $city_id);
    $m->set_data('society_pincode', $society_pincode);
    $m->set_data('state_id', $state_id);
    $m->set_data('sub_domain', $sub_domain);
    $m->set_data('builder_address', $builder_address);
    $m->set_data('builder_mobile', $builder_mobile);
    $m->set_data('package_id', $package_id);
    $m->set_data('payment_status', $amountReceivedType);
    $m->set_data('payment_amount', $amountReceived);
    $m->set_data('trial_days', $trial_days);
    $m->set_data('plan_expire_date', $plan_expire_date);
    $m->set_data('employee_tracking_limit', $employee_tracking_limit);
    $m->set_data('expected_team_size', $expected_team_size);
    $m->set_data('employee_registration_limit', $employee_registration_limit);
    $m->set_data('per_employee_price', $per_employee_price);
    $m->set_data('sales_person_name', $sales_person_name);
    $m->set_data('sales_closure_date', $sales_closure_date);
    $m->set_data('reference_from', $reference_from);
    $m->set_data('api_key', $keydb);
    $m->set_data('currency', $currency);
    $m->set_data('calender_type', $calender_type);
    $m->set_data('yearly_ticket_size', $yearly_ticket_size);
    $m->set_data('received_ticket_size', $received_ticket_size);
    $m->set_data('implementation_name', $implementation_name);
    $m->set_data('payment_mode', $payment_mode);
    $m->set_data('payment_attachment', $payment_attachment);
    $m->set_data('industry_type', $industry_type);
    $m->set_data('gst_number', strtoupper($gst_number));
    $m->set_data('request_added_by', $bms_admin_id);
    $m->set_data('requested_date', date("Y-m-d H:i:s"));
    $m->set_data('created_date',  date("Y-m-d H:i:s"));
    $m->set_data('society_rating', $society_rating);
    $m->set_data('support_name', $support_name);
    $m->set_data('support_country_code', $support_country_code);
    $m->set_data('support_mobile_no', $support_mobile_no);
    $m->set_data('training_type', $training_type);
    $m->set_data('remark_edit', $remark_edit);
    $m->set_data('society_remark', $society_remark);
    $m->set_data('sales_remark', $sales_remark);
    $m->set_data('implementation_remark', $implementation_remark);
    $m->set_data('expected_team_size', $expected_team_size);
    $m->set_data('lead_sources', $lead_sources);
    $m->set_data('region', $region);
    $m->set_data('request_search_society_code', $search_society_code);
    $m->set_data('request_society_code', $society_code);
    $m->set_data('from_rise_event', $from_rise_event??0);


    $a = array(
      'request_society_name' => $m->get_data('society_name'),
      // Jainish Start
      'account_type' => $m->get_data('account_type'),
      // Jainish End
      'request_society_address' => $m->get_data('society_address'),
      'request_society_latitude' => $m->get_data('society_latitude'),
      'request_society_longitude' => $m->get_data('society_longitude'),
      'request_secretary_name' => $m->get_data('secretary_name'),
      'request_secretary_email' => $m->get_data('secretary_email'),
      'country_code' => $m->get_data('country_code'),
      'request_secretary_mobile' => $m->get_data('secretary_mobile'),
      'request_society_logo' => $m->get_data('society_logo'),
      'society_type' => $m->get_data('society_type'),
      'request_builder_name' => $m->get_data('builder_name'),
      'request_country_id' => $m->get_data('country_id'),
      'request_state_id' => $m->get_data('state_id'),
      'request_city_id' => $m->get_data('city_id'),
      'request_society_pincode' => $m->get_data('society_pincode'),
      'request_sub_domain' => $m->get_data('sub_domain'),
      'request_api_key' => $m->get_data('api_key'),
      'currency' => $m->get_data('currency'),
      'request_builder_address' => $m->get_data('builder_address'),
      'request_builder_mobile' => $m->get_data('builder_mobile'),
      'request_package_id' => $m->get_data('package_id'),
      'payment_status' => $m->get_data('payment_status'),
      'payment_amount' => $m->get_data('payment_amount'),
      'request_trial_days' => $m->get_data('trial_days'),
      'employee_tracking_limit' => $m->get_data('employee_tracking_limit'),
      'expected_team_size' => $m->get_data('expected_team_size'),
      'employee_registration_limit' => $m->get_data('employee_registration_limit'),
      'per_employee_price' => $m->get_data('per_employee_price'),
      'sales_person_name' => $m->get_data('sales_person_name'),
      'sales_closure_date' => $m->get_data('sales_closure_date'),
      'reference_from' => $m->get_data('reference_from'),
      'request_plan_expire_date' => $m->get_data('plan_expire_date'),
      'request_last_renew_date' => date('Y-m-d'),
      'request_added_by' => $m->get_data('request_added_by'),
      'requested_date' => $m->get_data('requested_date'),
      'created_date' => $m->get_data('created_date'),
      'calender_type' => $m->get_data('calender_type'),
      'yearly_ticket_size' => $m->get_data('yearly_ticket_size'),
      'received_ticket_size' => $m->get_data('received_ticket_size'),
      'implementation_name' => $m->get_data('implementation_name'),
      'payment_mode' => $m->get_data('payment_mode'),
      'payment_attachment' => $m->get_data('payment_attachment'),
      'gst_number' => $m->get_data('gst_number'),
      'industry_type' => $m->get_data('industry_type'),
      'society_rating' => $m->get_data('society_rating'),
      'support_name' => $m->get_data('support_name'),
      'support_country_code' => $m->get_data('support_country_code'),
      'support_mobile_no' => $m->get_data('support_mobile_no'),
      'training_type' => $m->get_data('training_type'),
      'remark' => $m->get_data('remark_edit'),
      'society_remark' => $m->get_data('society_remark'),
      'sales_remark' => $m->get_data('sales_remark'),
      'implementation_remark' => $m->get_data('implementation_remark'),
      'expected_team_size' => $m->get_data('expected_team_size'),
      'requests_lead_sources' => $m->get_data('lead_sources'),
      'request_region_name' => $m->get_data('region'),
      'request_search_society_code' => $m->get_data('request_search_society_code'),
      'request_society_code' => $m->get_data('request_society_code'),
      'from_rise_event' => $m->get_data('from_rise_event'),

    );
    // print_r($a);die();
    $q = $d->insert("society_master_requests", $a);
    $request_society_id = $con->insert_id;

    if ($q > 0) {
      if (!empty($_POST['contact_name']) && is_array($_POST['contact_name'])) {
        foreach ($_POST['contact_name'] as $index => $name) {
          $contact_name = isset($_POST['contact_name'][$index]) ? trim($_POST['contact_name'][$index]) : '';
          $contact_number = isset($_POST['contact_number'][$index]) ? trim($_POST['contact_number'][$index]) : '';
          $designation = isset($_POST['designation'][$index]) ? trim($_POST['designation'][$index]) : '';

          // Only insert if all fields are filled
          if (!empty($contact_name) && !empty($contact_number) && !empty($designation)) {
            $m->set_data('contact_name', mysqli_real_escape_string($con, $contact_name));
            $m->set_data('contact_number', mysqli_real_escape_string($con, $contact_number));
            $m->set_data('designation', mysqli_real_escape_string($con, $designation));
            $m->set_data('request_society_id', $request_society_id);

            $contactData = array(
              'contact_person_name' => $m->get_data('contact_name'),
              'contact_person_no' => $m->get_data('contact_number'),
              'designation' => $m->get_data('designation'),
              'added_by' => $created_by,
              'added_date' => date('Y-m-d H:i:s'),
              'request_society_id' => $m->get_data('request_society_id')
            );

            $insertContact = $d->insert("society_contact_person_details", $contactData);

            if ($insertContact > 0) {
              $d->insert_log("$request_society_id", "$bms_admin_id", "$created_by", "Contact Person Added");
            } else {
              $_SESSION['msg1'] = "One or more Contact Person insert failed.";
              error_log("Failed to insert contact person: " . print_r($contactData, true)); // Debugging
            }
          } else {
            error_log("Empty contact person fields: Name: $contact_name, Number: $contact_number, Designation: $designation");
          }
        }
      }




      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "<b>$society_name</b> - Company Creation Request Added", 3);

      $to = array();
      $added_by = $admin_name;
      $query = $d->select("society_create_email_receipt", "active_status=0");
      while ($data = mysqli_fetch_array($query)) {
        if ($data['email_id'] != '') {
          array_push($to, $data['email_id']);
        }
        $msg = "Creation of New Company $society_name Requested by $added_by";
      }
      $subject = "Creation of New Company $society_name Requested";

      include '../mail/newSocietyCreateMail.php';
      include '../mail.php';


      $admins = $d->select("admin_fcm_notification_master", "fcm_notifications=3");
      $total_admins = mysqli_num_rows($admins);
      if ($total_admins > 0) {
        $noti_title = "New Company Request";
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
      $_SESSION['msg'] = "Company Request Added";
      header("location:../manageCompanyRequests");
    } else {
      $_SESSION['msg1'] = "Something Wrong";
      header("location:../addCompany");
    }
  }
  if (isset($_POST['society_name_edit'])) {

    $qcb = $d->select("society_master", "sub_domain='$sub_domain'");
    if (mysqli_num_rows($qcb) > 0) {
      $_SESSION['msg1'] = "Company Already Created on this Url";
      header("location:../myCompanyRequests");
      exit();
    }

    /*dhruvi sojitra --start*/
    $societyServerQuery = $d->selectRow("society_master.society_id,society_master.society_name,
     society_master_requests.request_society_id,society_master_requests.society_id_added",
     "society_master LEFT JOIN society_master_requests  ON society_master.society_id = society_master_requests.society_id_added",
     "society_master.created_on_society_server = '1' 
     AND society_master_requests.request_society_id = '$request_society_id_edit'");

    if (mysqli_num_rows($societyServerQuery) > 0) {
        $_SESSION['msg1'] = "This company is already created on the server and cannot be edited.";
        header("location:../manageCompanyRequests");
        exit();
    }
    /*dhruvi sojitra -- end*/

    if ($amountReceivedType == 0) {
      $amountReceived = 0;
    }

    // Handle society logo
    $file_society_logo = $_FILES['society_logo']['tmp_name'];
    if (file_exists($file_society_logo)) {
      $acceptable = array('jpeg', 'jpg', 'png');
      $extId = pathinfo($_FILES['society_logo']['name'], PATHINFO_EXTENSION);
      $dirPath = "../../img/society_requests/";
      if (in_array($extId, $acceptable)) {
        $temp = explode(".", $_FILES["society_logo"]["name"]);
        $society_logo = 'Society_' . round(microtime(true)) . '.' . end($temp);
        $destinationPath = $dirPath . $society_logo;
        $d->resizeImage($file_society_logo, $destinationPath, 500, 500, $extId);
      } else {
        $_SESSION['msg1'] = "Invalid File format, Only JPEG, JPG and PNG are allowed.";
        header("location:../manageCompanyRequests");
        exit();
      }
    } else {
      $society_logo = $society_logo_old;
    }

    // Handle payment attachment
    $file_payment_attachment = $_FILES['payment_attachment']['tmp_name'];
    if (file_exists($file_payment_attachment)) {
      $acceptable = array('jpeg', 'jpg', 'png');
      $extId = pathinfo($_FILES['payment_attachment']['name'], PATHINFO_EXTENSION);
      $dirPath = "../../img/society_requests/";
      if (in_array($extId, $acceptable)) {
        $temp = explode(".", $_FILES["payment_attachment"]["name"]);
        $payment_attachment = 'Society_' . round(microtime(true)) . '.' . end($temp);
        $destinationPath = $dirPath . $payment_attachment;
        $d->resizeImage($file_payment_attachment, $destinationPath, 800, 600, $extId);
      } else {
        $_SESSION['msg1'] = "Invalid File format, Only JPEG, JPG and PNG are allowed.";
        header("location:../manageCompanyRequests");
        exit();
      }
    } else {
      $payment_attachment = $payment_attachment_old;
    }

    // Set all form fields
    $m->set_data('society_name', $society_name_edit);
    $m->set_data('account_type', $account_type);
    $m->set_data('society_address', $society_address);
    $m->set_data('society_latitude', $society_latitude);
    $m->set_data('society_longitude', $society_longitude);
    $m->set_data('secretary_name', $secretary_name);
    $m->set_data('secretary_email', $secretary_email);
    $m->set_data('secretary_mobile', $secretary_mobile);
    $m->set_data('country_code', $country_code);
    $m->set_data('society_logo', $society_logo);
    $m->set_data('society_type', $society_type);
    $m->set_data('builder_name', $builder_name);
    $m->set_data('country_id', $country_id);
    $m->set_data('state_id', $state_id);
    $m->set_data('city_id', $city_id);
    $m->set_data('society_pincode', $society_pincode);
    $m->set_data('sub_domain', $sub_domain);
    $m->set_data('api_key', $keydb);
    $m->set_data('currency', $currency);
    $m->set_data('calender_type', $calender_type);
    $m->set_data('yearly_ticket_size', $yearly_ticket_size);
    $m->set_data('received_ticket_size', $received_ticket_size);
    $m->set_data('implementation_name', $implementation_name);
    $m->set_data('payment_mode', $payment_mode);
    $m->set_data('payment_attachment', $payment_attachment);
    $m->set_data('gst_number', strtoupper($gst_number));
    $m->set_data('payment_status', $amountReceivedType);
    $m->set_data('payment_amount', $amountReceived);
    $m->set_data('trial_days', $trial_days);
    $m->set_data('sales_person_name', $sales_person_name);
    $m->set_data('sales_closure_date', $sales_closure_date);
    $m->set_data('plan_expire_date', $plan_expire_date);
    $m->set_data('industry_type', $industry_type);
    $m->set_data('society_rating', $society_rating);
    $m->set_data('support_name', $support_name);
    $m->set_data('support_country_code', $support_country_code);
    $m->set_data('support_mobile_no', $support_mobile_no);
    $m->set_data('training_type', $training_type);
    $m->set_data('remark_edit', $remark_edit);
    $m->set_data('society_remark', $society_remark);
    $m->set_data('sales_remark', $sales_remark);
    $m->set_data('implementation_remark', $implementation_remark);
    $m->set_data('expected_team_size', $expected_team_size);
    $m->set_data('lead_sources', $lead_sources);
    $m->set_data('region', $region);
    $m->set_data('request_search_society_code', $search_society_code_edit);
    $m->set_data('request_society_code', $society_code_edit);
    $m->set_data('from_rise_event', $from_rise_event??0);

    $a = array(
      'request_society_name' => $m->get_data('society_name'),
      'account_type' => $m->get_data('account_type'),
      'request_society_address' => $m->get_data('society_address'),
      'request_society_latitude' => $m->get_data('society_latitude'),
      'request_society_longitude' => $m->get_data('society_longitude'),
      'request_secretary_name' => $m->get_data('secretary_name'),
      'request_secretary_email' => $m->get_data('secretary_email'),
      'request_secretary_mobile' => $m->get_data('secretary_mobile'),
      'country_code' => $m->get_data('country_code'),
      'request_society_logo' => $m->get_data('society_logo'),
      'society_type' => $m->get_data('society_type'),
      'request_builder_name' => $m->get_data('builder_name'),
      'request_country_id' => $m->get_data('country_id'),
      'request_state_id' => $m->get_data('state_id'),
      'request_city_id' => $m->get_data('city_id'),
      'request_society_pincode' => $m->get_data('society_pincode'),
      'request_sub_domain' => $m->get_data('sub_domain'),
      'request_api_key' => $m->get_data('api_key'),
      'currency' => $m->get_data('currency'),
      'request_builder_address' => $m->get_data('builder_address'),
      'request_builder_mobile' => $m->get_data('builder_mobile'),
      'payment_status' => $m->get_data('payment_status'),
      'payment_amount' => $m->get_data('payment_amount'),
      'request_trial_days' => $m->get_data('trial_days'),
      'sales_person_name' => $m->get_data('sales_person_name'),
      'sales_closure_date' => $m->get_data('sales_closure_date'),
      'request_plan_expire_date' => $m->get_data('plan_expire_date'),
      'calender_type' => $m->get_data('calender_type'),
      'yearly_ticket_size' => $m->get_data('yearly_ticket_size'),
      'received_ticket_size' => $m->get_data('received_ticket_size'),
      'implementation_name' => $m->get_data('implementation_name'),
      'payment_mode' => $m->get_data('payment_mode'),
      'payment_attachment' => $m->get_data('payment_attachment'),
      'gst_number' => $m->get_data('gst_number'),
      'industry_type' => $m->get_data('industry_type'),
      'society_rating' => $m->get_data('society_rating'),
      'support_name' => $m->get_data('support_name'),
      'support_country_code' => $m->get_data('support_country_code'),
      'support_mobile_no' => $m->get_data('support_mobile_no'),
      'training_type' => $m->get_data('training_type'),
      'remark' => $m->get_data('remark_edit'),
      'society_remark' => $m->get_data('society_remark'),
      'sales_remark' => $m->get_data('sales_remark'),
      'implementation_remark' => $m->get_data('implementation_remark'),
      'expected_team_size' => $m->get_data('expected_team_size'),
      'requests_lead_sources' => $m->get_data('lead_sources'),
      'request_region_name' => $m->get_data('region'),
      'request_search_society_code' => $m->get_data('request_search_society_code'),
      'request_society_code' => $m->get_data('request_society_code'),
      'from_rise_event' => $m->get_data('from_rise_event'),
    );

    $q = $d->update("society_master_requests", $a, "request_society_id='$request_society_id_edit'");

    if ($q > 0) {
      $d->delete("society_contact_person_details", "request_society_id='$request_society_id_edit'");

      if (!empty($_POST['contact_name']) && is_array($_POST['contact_name'])) {
        foreach ($_POST['contact_name'] as $index => $name) {
          $contact_name = trim(mysqli_real_escape_string($con, $name));
          $contact_number = trim(mysqli_real_escape_string($con, $_POST['contact_number'][$index]));
          $designation = trim(mysqli_real_escape_string($con, $_POST['designation'][$index]));

          // Only insert if at least one field has value
          if (!empty($contact_name) || !empty($contact_number) || !empty($designation)) {
            $m->set_data('contact_name', $contact_name);
            $m->set_data('contact_number', $contact_number);
            $m->set_data('designation', $designation);
            $m->set_data('request_society_id', $request_society_id_edit);

            $updateData = array(
              'contact_person_name' => $m->get_data('contact_name'),
              'contact_person_no' => $m->get_data('contact_number'),
              'designation' => $m->get_data('designation'),
              'request_society_id' => $m->get_data('request_society_id'),
              'added_by' => $created_by,
              'added_date' => date('Y-m-d H:i:s')
            );

            $d->insert("society_contact_person_details", $updateData);
          }
        }
      }

      $_SESSION['msg'] = "Company Details Updated.";
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "<b>$society_name_edit</b> - Company Creation Details Updated", 3);
      header("location:../manageCompanyRequests");
    } else {
      $_SESSION['msg1'] = "Something Wrong";
      header("location:../manageCompanyRequests");
    }
  }

  if (isset($_POST['rejected_society'])) {
    $m->set_data('soc_reject_reason', $soc_reject_reason);
    $m->set_data('deleted_date', date("Y-m-d H:i:s"));
    $m->set_data('deleted_by', $bms_admin_id);

    $a1 = array(
      'request_society_create_status' => 2,
      'reject_reason' => $m->get_data('soc_reject_reason'),
      'deleted_date' => $m->get_data('deleted_date'),
      'deleted_by' => $m->get_data('deleted_by'),
    );
    $q = $d->update('society_master_requests', $a1, "request_society_id='$rejected_society'");
    if ($q > 0) {
      $data = $d->selectArray('society_master_requests', "request_society_id='$rejected_society'");
      $society_name = $data['request_society_name'];
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "<b>$society_name</b> - Company Creation Request Rejected", 3);
      $mailTO = $d->selectArray("bms_admin_master", "admin_id='$request_added_by'");
      $to = $d->encryptDecrypt("decrypt", $mailTO['admin_email']);
      $admin_name = $mailTO['admin_name'];
      $message = "Your Request to Create Company  - $society_name is Rejected. \n Reason : $soc_reject_reason";
      $subject = "Company $society_name Rejected - " . $d->app_name() . " ";
      include '../mail/societyRejected.php';
      include '../mail.php';
      $_SESSION['msg'] = "Company Status Updated";
      header("location:../manageCompanyRequests");
    } else {
      $_SESSION['msg1'] = "Something Wrong";
      header("location:../addCompany");
    }
  }

  if (isset($_POST['created_society'])) {
    $a1 = array('request_society_create_status' => 1);
    $q = $d->update('society_master_requests', $a1, "request_society_id='$created_society'");
    if ($q > 0) {
      $data = $d->selectArray('society_master_requests', "request_society_id='$rejected_society'");
      $society_name = $data['created_society'];
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "<b>$society_name</b> - Company Created", 3);
      $_SESSION['msg'] = "Society Status Updated";
      header("location:../manageCompanyRequests");
    } else {
      $_SESSION['msg1'] = "Something Wrong";
      header("location:../addCompany");
    }
  }

  if (isset($_POST['mail_society'])) {

    $mailTO = $d->selectArray("bms_admin_master", "admin_id='$request_added_by'");
    $to = $d->encryptDecrypt("decrypt", $mailTO['admin_email']);
    $admin_name = $mailTO['admin_name'];
    $message = "Your Request of Company - $request_society_name is Successfully Created with Company Id - $mail_society";
    $subject = "Company $request_society_name Created - " . $d->app_name() . " ";
    include '../mail/societyCreated.php';
    include '../mail.php';
    $_SESSION['msg'] = "Mail Sent";
    header("location:../$redirect");
  }


  if (isset($addComaplintEmail)) {

    $q1 = $d->select("society_create_email_receipt", "email_id='$email_id'", "");
    if (mysqli_num_rows($q1) > 0) {
      $_SESSION['msg1'] = "Already Added";
      header("Location: ../companyRequestEmail");
      exit();
    }
    $m->set_data('email_id', $email_id);

    $a2 = array(
      'email_id' => $m->get_data('email_id'),
    );

    $q2 = $d->insert("society_create_email_receipt", $a2);


    if ($q2 == TRUE) {
      $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Eamil Added for Complaint Email Receipt");

      $_SESSION['msg'] = "Email Added";
      header("Location: ../companyRequestEmail");
    } else {
      $_SESSION['msg1'] = "Something Wrong";
      header("Location: ../companyRequestEmail");
    }
  }
} else {
  header('location:../logout.php');
}
