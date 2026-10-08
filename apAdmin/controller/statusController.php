<?php
include '../common/objectController.php';

if (isset($id)) {
  if (is_array($id)) {
    $id = $d->sanitizeActionIds($id);
  } elseif (is_string($id) && strpos($id, '~') !== false) {
    $parts = explode('~', $id);
    $safeParts = array();
    foreach ($parts as $part) {
      $safeParts[] = (string) $d->sanitizeActionIdAsInt($part);
    }
    $id = implode('~', $safeParts);
  } else {
    $id = $d->sanitizeActionIdAsInt($id);
  }
}

if (isset($_POST) && !empty($_POST)) {
  if (isset($status) && $_POST['status'] == "deactiveUtilityMenu") {
    $idArray = explode("~", $id);
    $app_menu_id = $idArray[0];
    $society_id_block = $idArray[1];
    $m->set_data('society_id', $society_id_block);
    $m->set_data('app_menu_id', $app_menu_id);
    $created_date = date("Y-m-d H:i:s");
    $a1 = array(
      'society_id' => $m->get_data('society_id'),
      'app_menu_id' => $m->get_data('app_menu_id'),
      'created_date' => $created_date
    );
    $q = $d->insert('resident_app_menu_utility_resticted_master', $a1);
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "activeUtilityMenu") {
    $q = $d->delete('resident_app_menu_utility_resticted_master', "resticted_id='$id' ");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "langDeactive") {
    $active_status = 1;
    $m->set_data('active_status', $active_status);
    $a1 = array(
      'active_status' => $m->get_data('active_status')
    );
    $q = $d->update('language_master', $a1, "language_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "langActive") {
    $active_status = 0;
    $m->set_data('active_status', $active_status);
    $a1 = array(
      'active_status' => $m->get_data('active_status')
    );
    $q = $d->update('language_master', $a1, "language_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "langKeyDeactive") {
    $key_status = 1;
    $m->set_data('key_status', $key_status);
    $a1 = array(
      'key_status' => $m->get_data('key_status')
    );
    $q = $d->update('language_key_master', $a1, "language_key_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "langKeyActive") {
    $key_status = 0;
    $m->set_data('key_status', $key_status);
    $a1 = array(
      'key_status' => $m->get_data('key_status')
    );
    $q = $d->update('language_key_master', $a1, "language_key_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }
  if (isset($status) && $_POST['status'] == "langKeyDeactiveGp") {
    $key_status = 1;
    $m->set_data('key_status', $key_status);
    $a1 = array(
      'key_status' => $m->get_data('key_status')
    );
    $q = $d->update('language_key_master_gatekeeper', $a1, "language_key_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "langKeyActiveGp") {
    $key_status = 0;
    $m->set_data('key_status', $key_status);
    $a1 = array(
      'key_status' => $m->get_data('key_status')
    );

    $q = $d->update('language_key_master_gatekeeper', $a1, "language_key_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "deavtivepost") {
    $isActive = 1;
    $m->set_data('active_status', $isActive);
    $a1 = array(
      'active_status' => $m->get_data('active_status')
    );
    $q = $d->update('timeline_master', $a1, "timeline_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "avtivepost") {
    $isActive = 0;
    $m->set_data('active_status', $isActive);
    $a1 = array(
      'active_status' => $m->get_data('active_status')
    );
    $q = $d->update('timeline_master', $a1, "timeline_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }
  if (isset($status) && $_POST['status'] == "viewSingle") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'festival_view_status' => $m->get_data('isActive')
    );
    $q = $d->update('festival_master', $a1, "festival_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "viewMultiple") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'festival_view_status' => $m->get_data('isActive')
    );
    $q = $d->update('festival_master', $a1, "festival_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "officeDeactive") {
    $isActive = 0;
    $m->set_data('status', $isActive);
    $a1 = array(
      'status' => $m->get_data('status')
    );
    $q = $d->update('offices_master', $a1, "office_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "officeActive") {
    $isActive = 1;
    $m->set_data('status', $isActive);
    $a1 = array(
      'status' => $m->get_data('status')
    );
    $q = $d->update('offices_master', $a1, "office_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "faqActive") {
    $isActive = 1;
    $m->set_data('status', $isActive);
    $a1 = array(
      'status' => $m->get_data('status')
    );
    $q = $d->update('faq_question_master', $a1, "faq_sub_master_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "faqDeactive") {
    $isActive = 2;
    $m->set_data('status', $isActive);
    $a1 = array(
      'status' => $m->get_data('status')
    );
    $q = $d->update('faq_question_master', $a1, "faq_sub_master_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "clientDeactive") {
    $isActive = 0;
    $m->set_data('status', $isActive);
    $a1 = array(
      'status' => $m->get_data('status')
    );
    $q = $d->update('clients_master', $a1, "client_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "clientActive") {
    $isActive = 1;
    $m->set_data('status', $isActive);
    $a1 = array(
      'status' => $m->get_data('status')
    );
    $q = $d->update('clients_master', $a1, "client_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }


  if (isset($status) && $_POST['status'] == "statusDeactive") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'festival_active_status' => $m->get_data('isActive')
    );
    $q = $d->update('festival_master', $a1, "festival_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "statusActive") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'festival_active_status' => $m->get_data('isActive')
    );
    $q = $d->update('festival_master', $a1, "festival_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }



  if (isset($status) && $_POST['status'] == "eventDeactive") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'event_status' => $m->get_data('isActive')
    );
    $q = $d->update('event_master', $a1, "event_id='$id' AND society_id='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "eventActive") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'event_status' => $m->get_data('isActive')
    );
    $q = $d->update('event_master', $a1, "event_id='$id' AND society_id='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "SliderSocietyActive") {
    $status = 0;
    $m->set_data('status', $status);
    $a1 = array(
      'status' => $m->get_data('status')
    );
    $q = $d->update('app_common_slider_master', $a1, "app_common_slider_id='$id' ");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "SliderSocietyDeactive") {
    $status = 1;
    $m->set_data('status', $status);
    $a1 = array(
      'status' => $m->get_data('status')
    );
    $q = $d->update('app_common_slider_master', $a1, "app_common_slider_id='$id' ");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "phoneUnlock") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'device_lock' => $m->get_data('isActive')
    );
    $q = $d->update('employee_master', $a1, "emp_id='$id' AND society_id='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "phoneLock") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'device_lock' => $m->get_data('isActive')
    );

    $q = $d->update('employee_master', $a1, "emp_id='$id' AND society_id='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "eventClose") {
    $eventSelect = $d->selectArray("event_master", "event_id='$id' AND society_id='$society_id'");
    $eventName = $eventSelect['event_title'];
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'booking_open' => $m->get_data('isActive')
    );
    $q = $d->update('event_master', $a1, "event_id='$id' AND society_id='$society_id'");
    if ($q > 0) {
      $fcmArray = $d->get_android_fcm("users_master", "user_token!='' AND society_id='$society_id' AND device='android'");
      $fcmArrayIos = $d->get_android_fcm("users_master", "user_token!='' AND society_id='$society_id' AND device='ios'");
      $nResident->noti("EventDetailsFragment", "", $society_id, $fcmArray, "Booking Closed for Event " . $eventSelect['event_title'], "By Admin " . $admin_name, 'eventClose');
      $nResident->noti_ios("EventsVC", "", $society_id, $fcmArrayIos, "Booking Closed for Event " . $eventSelect['event_title'], "By Admin " . $admin_name, 'eventClose');

      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "eventOpen") {
    $eventSelect = $d->selectArray("event_master", "event_id='$id' AND society_id='$society_id'");
    $eventName = $eventSelect['event_title'];

    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'booking_open' => $m->get_data('isActive')
    );

    $q = $d->update('event_master', $a1, "event_id='$id' AND society_id='$society_id'");
    if ($q > 0) {
      $fcmArray = $d->get_android_fcm("users_master", "user_token!='' AND society_id='$society_id' AND device='android'");
      $fcmArrayIos = $d->get_android_fcm("users_master", "user_token!='' AND society_id='$society_id' AND device='ios'");
      $nResident->noti("EventDetailsFragment", "", $society_id, $fcmArray, "Booking Open for " . $eventSelect['event_title'], "By Admin " . $admin_name, 'eventClose');
      $nResident->noti_ios("EventsVC", "", $society_id, $fcmArrayIos, "Booking Open for " . $eventSelect['event_title'], "By Admin " . $admin_name, 'eventClose');
      echo 1;
    } else {
      echo 0;
    }
  }


  if (isset($status) && $_POST['status'] == "societyDeactive") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'society_status' => $m->get_data('isActive')
    );
    $q = $d->update('society_master', $a1, "society_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "societyActive") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'society_status' => $m->get_data('isActive')
    );

    $q = $d->update('society_master', $a1, "society_id='$id' ");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "appMenuDeactive") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'menu_status' => $m->get_data('isActive')
    );
    $q = $d->update('resident_app_menu', $a1, "app_menu_id='$id' ");
    if ($q > 0) {
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "App Menu Deactive ($id)", 5);
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "appMenuActive") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'menu_status' => $m->get_data('isActive')
    );
    $q = $d->update('resident_app_menu', $a1, "app_menu_id='$id' ");
    if ($q > 0) {
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "App Menu Active ($id)", 5);
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "appMenuDeactiveSociety") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'menu_status' => $m->get_data('isActive')
    );

    $q = $d->update('resident_app_menu_society', $a1, "app_menu_society_id='$id' ");
    if ($q > 0) {
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "Company App Menu Deactive ($id)", 5);
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "appMenuActiveSociety") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'menu_status' => $m->get_data('isActive')
    );

    $q = $d->update('resident_app_menu_society', $a1, "app_menu_society_id='$id' ");
    if ($q > 0) {
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "Company App Menu Active ($id)", 5);
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "appMenuDeactiveSocietyAndroid") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'menu_status_android' => $m->get_data('isActive')
    );

    $q = $d->update('resident_app_menu_society', $a1, "app_menu_society_id='$id' ");
    if ($q > 0) {
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "Android App Menu Deactive ($id)", 5);
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "appMenuActiveSocietyAndroid") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'menu_status_android' => $m->get_data('isActive')
    );

    $q = $d->update('resident_app_menu_society', $a1, "app_menu_society_id='$id' ");
    if ($q > 0) {
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "Android App Menu Active ($id)", 5);
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "appMenuDeactiveSocietyIos") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'menu_status_ios' => $m->get_data('isActive')
    );

    $q = $d->update('resident_app_menu_society', $a1, "app_menu_society_id='$id' ");
    if ($q > 0) {
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "IOS App Menu Deactive ($id)", 5);
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "appMenuActiveSocietyIos") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'menu_status_ios' => $m->get_data('isActive')
    );

    $q = $d->update('resident_app_menu_society', $a1, "app_menu_society_id='$id' ");
    if ($q > 0) {
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "IOS App Menu Active ($id)", 5);
      echo 1;
    } else {
      echo 0;
    }
  }


  if (isset($status) && $_POST['status'] == "GroupChatDeactive") {
    $group_chat_status = 1;
    $m->set_data('group_chat_status', $group_chat_status);
    $a1 = array(
      'group_chat_status' => $m->get_data('group_chat_status')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "GroupChatActive") {
    $group_chat_status = 0;
    $m->set_data('group_chat_status', $group_chat_status);
    $a1 = array(
      'group_chat_status' => $m->get_data('group_chat_status')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "VisitorApprovalDeactive") {
    $visitor_on_off = 1;
    $m->set_data('visitor_on_off', $visitor_on_off);
    $a1 = array(
      'visitor_on_off' => $m->get_data('visitor_on_off')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "VisitorApprovalActive") {
    $visitor_on_off = 0;
    $m->set_data('visitor_on_off', $visitor_on_off);
    $a1 = array(
      'visitor_on_off' => $m->get_data('visitor_on_off')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "residentinoutActive") {
    $resident_in_out = 0;
    $m->set_data('resident_in_out', $resident_in_out);
    $a1 = array(
      'resident_in_out' => $m->get_data('resident_in_out')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "residentinoutDeactive") {
    $resident_in_out = 1;
    $m->set_data('resident_in_out', $resident_in_out);
    $a1 = array(
      'resident_in_out' => $m->get_data('resident_in_out')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "visitorMobileActive") {
    $visitor_mobile_number_show_gatekeeper = 0;
    $m->set_data('visitor_mobile_number_show_gatekeeper', $visitor_mobile_number_show_gatekeeper);
    $a1 = array(
      'visitor_mobile_number_show_gatekeeper' => $m->get_data('visitor_mobile_number_show_gatekeeper')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "visitorMobiletDeactive") {
    $visitor_mobile_number_show_gatekeeper = 1;
    $m->set_data('visitor_mobile_number_show_gatekeeper', $visitor_mobile_number_show_gatekeeper);
    $a1 = array(
      'visitor_mobile_number_show_gatekeeper' => $m->get_data('visitor_mobile_number_show_gatekeeper')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "CreateGroupActive") {
    $create_group = 0;
    $m->set_data('create_group', $create_group);
    $a1 = array(
      'create_group' => $m->get_data('create_group')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "CreateGroupDeactive") {
    $create_group = 1;
    $m->set_data('create_group', $create_group);
    $a1 = array(
      'create_group' => $m->get_data('create_group')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }


  if (isset($status) && $_POST['status'] == "TenantRegActive") {
    $tenant_registration = 0;
    $m->set_data('tenant_registration', $tenant_registration);
    $a1 = array(
      'tenant_registration' => $m->get_data('tenant_registration')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "TenantRegDeactive") {
    $tenant_registration = 1;
    $m->set_data('tenant_registration', $tenant_registration);
    $a1 = array(
      'tenant_registration' => $m->get_data('tenant_registration')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "entryAllVisitorGroupActive") {
    $entry_all_visitor_group = 0;
    $m->set_data('entry_all_visitor_group', $entry_all_visitor_group);
    $a1 = array(
      'entry_all_visitor_group' => $m->get_data('entry_all_visitor_group')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "entryAllVisitorGroupDeactive") {
    $entry_all_visitor_group = 1;
    $m->set_data('entry_all_visitor_group', $entry_all_visitor_group);
    $a1 = array(
      'entry_all_visitor_group' => $m->get_data('entry_all_visitor_group')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    echo $q;
    exit;
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "screensortActive") {
    $screen_sort_capture_in_timeline = 0;
    $m->set_data('screen_sort_capture_in_timeline', $screen_sort_capture_in_timeline);
    $a1 = array(
      'screen_sort_capture_in_timeline' => $m->get_data('screen_sort_capture_in_timeline')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "screensortDeactive") {
    $screen_sort_capture_in_timeline = 1;
    $m->set_data('screen_sort_capture_in_timeline', $screen_sort_capture_in_timeline);
    $a1 = array(
      'screen_sort_capture_in_timeline' => $m->get_data('screen_sort_capture_in_timeline')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }


  if (isset($status) && $_POST['status'] == "registrationRequestDeactive") {
    $registration_request_from_app = 1;
    $m->set_data('registration_request_from_app', $registration_request_from_app);
    $a1 = array(
      'registration_request_from_app' => $m->get_data('registration_request_from_app')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "registrationRequestActive") {
    $registration_request_from_app = 0;
    $m->set_data('registration_request_from_app', $registration_request_from_app);
    $a1 = array(
      'registration_request_from_app' => $m->get_data('registration_request_from_app')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "scannerActive") {
    $event_scanner_for_gatekeeper = 0;
    $m->set_data('event_scanner_for_gatekeeper', $event_scanner_for_gatekeeper);
    $a1 = array(
      'event_scanner_for_gatekeeper' => $m->get_data('event_scanner_for_gatekeeper')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "scannerDeactive") {
    $event_scanner_for_gatekeeper = 1;
    $m->set_data('event_scanner_for_gatekeeper', $event_scanner_for_gatekeeper);
    $a1 = array(
      'event_scanner_for_gatekeeper' => $m->get_data('event_scanner_for_gatekeeper')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "documentActive") {
    $member_document_access = 0;
    $m->set_data('member_document_access', $member_document_access);
    $a1 = array(
      'member_document_access' => $m->get_data('member_document_access')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "documentDeactive") {
    $member_document_access = 1;
    $m->set_data('member_document_access', $member_document_access);
    $a1 = array(
      'member_document_access' => $m->get_data('member_document_access')
    );

    $q = $d->update('society_master', $a1, "society_id ='$society_id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "cityActive") {
    $a1 = array('flag' => 1);
    $data = $d->selectArray('cities', "city_id='$id'");
    $name = $data['name'];
    $q = $d->update('cities', $a1, "city_id='$id'");
    if ($q > 0) {
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "City- $name Activated", 1);
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "cityDeactive") {
    $a1 = array('flag' => 0);
    $data = $d->selectArray('cities', "city_id='$id'");
    $name = $data['name'];
    $q = $d->update('cities', $a1, "city_id='$id'");
    if ($q > 0) {
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "City- $name Deactivated", 1);
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "tracking_status") {
    $a1 = array('tracking_status' => $value);
    $data1 = $d->select('society_master', "society_id='$society_id'");
    $data = mysqli_fetch_array($data1);
    $society_name = $data['society_name'];
    $society_base_url = $data['sub_domain'];
    $city_name = $data['city_name'];
    $company_name = $society_name . '-' . $city_name;
    $employee_tracking_limit = $data['employee_tracking_limit'];
    $employee_registration_limit = $data['employee_registration_limit'];
    if ($employee_tracking_limit > 0) {
      $post = array(
      'society_id' => $society_id,
      'changeTrackingLimit' => 'changeTrackingLimit',
      'employee_tracking_limit' => $employee_tracking_limit,
      'employee_registration_limit' => $employee_registration_limit,
      'tracking_status' => $value,
      );
      $json = $d->callCompanyApiEnc($society_base_url, 'buildingChangePlanController.php', $post);

      if (count($json) > 0) {
        if ($value == '1') {
          $meg = "Employee Tracking $company_name Activated";
        } else {
          $meg = "Employee Tracking $company_name Deactivated";
        }
        $q = $d->update('society_master', $a1, "society_id='$society_id'");
        if ($q > 0) {
          $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "$meg", 1);
          $_SESSION['msg'] = $meg;
          header("location:../trackingLimit");
          exit();
        } else {
          $_SESSION['msg1'] = "Something Wrong";
          header("location:../trackingLimit");
          exit();
        }
      } else {
        $_SESSION['msg1'] = "Something Wrong";
        header("location:../trackingLimit");
        exit();
      }
    } else {
      $_SESSION['msg1'] = "First Add Employee Tracking Limit Then Active Status";
      header("location:../trackingLimit");
      exit();
    }
  }

  if (isset($Status) && $_POST['Status'] == "CountryStatus") {
    $country_id = $d->sanitizeActionIdAsInt($country_id ?? ($_POST['country_id'] ?? 0));
    $a1 = array('flag' => $flag);
    if ($flag == '1') {
      $meg = "Country $name Activated Successfully.";
    } else {
      $meg = "Country $name Deactivated Successfully.";
    }
    $q = $d->update("countries", $a1, "country_id=$country_id");
    if ($q > 0) {
      $_SESSION['msg'] = $meg;
      $d->insert_log("0", "$bms_admin_id", "$created_by", "$meg");
      header("location:../manageCountry");
      exit();
    } else {
      $_SESSION['msg1'] = "Something Went Wrong?";
      header("location:../manageCountry");
      exit();
    }
  }

  if (isset($Status) && $_POST['Status'] == "StateStatus") {
    $state_id = $d->sanitizeActionIdAsInt($state_id ?? ($_POST['state_id'] ?? 0));
    $country_id = $d->sanitizeActionIdAsInt($country_id ?? ($_POST['country_id'] ?? 0));
    $a1 = array('flag' => $flag);
    if ($flag == '1') {
      $meg = "State $name Activated Successfully.";
    } else {
      $meg = "State $name Deactivated Successfully.";
    }
    $q = $d->update("states", $a1, "state_id=$state_id");
    if ($q > 0) {
      $_SESSION['msg'] = $meg;
      $d->insert_log("0", "$bms_admin_id", "$created_by", "$meg");
      header("location:../manageState?countryId=$country_id");
      exit();
    } else {
      $_SESSION['msg1'] = "Something Went Wrong?";
      header("location:../manageState?countryId=$country_id");
      exit();
    }
  }

  if (isset($Status) && $_POST['Status'] == "CityStatus") {
    $city_id = $d->sanitizeActionIdAsInt($city_id ?? ($_POST['city_id'] ?? 0));
    $a1 = array('flag' => $flag);
    if ($flag == '1') {
      $meg = "City $name Activated Successfully.";
    } else {
      $meg = "City $name Deactivated Successfully.";
    }
    $q = $d->update("cities", $a1, "city_id=$city_id");
    if ($q > 0) {
      $_SESSION['msg'] = $meg;
      $d->insert_log("0", "$bms_admin_id", "$created_by", "$meg");
      header("location:../manageCity?countryId=$country_id&sId=$state_id");
      exit();
    } else {
      $_SESSION['msg1'] = "Something Went Wrong?";
      header("location:../manageCity?countryId=$country_id&sId=$state_id");
      exit();
    }
  }

  if (isset($status) && $_POST['status'] == "kycApiDeactive") {
    $status = 1;
    $m->set_data('status', $status);
    $a = array(
      'status' => $m->get_data('status')
    );

    $q = $d->update('document_kyc_master', $a, "kyc_api_type='$id'");
    if ($q > 0) {
      $d->insert_log("0", "$bms_admin_id", "$created_by", "kycApiDeactive");
      echo 1;
    } else {
      echo 0;
    }
  }
  if (isset($status) && $_POST['status'] == "kycApiActive") {
    $status = 0;
    $m->set_data('status', $status);
    $a1 = array(
      'status' => $m->get_data('status')
    );

    $q = $d->update('document_kyc_master', $a1, "kyc_api_type='$id'");
    if ($q > 0) {
      $d->insert_log("0", "$bms_admin_id", "$created_by", "kycApiDeactive");
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "serverDeactive") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'server_active_status' => $m->get_data('isActive')
    );
    $q = $d->update('server_master', $a1, "server_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "serverActive") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'server_active_status' => $m->get_data('isActive')
    );

    $q = $d->update('server_master', $a1, "server_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }
  // Domain Status
  if (isset($status) && $_POST['status'] == "domainDeactive") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'domain_active_status' => $m->get_data('isActive')
    );
    $q = $d->update('domain_master', $a1, "domain_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "domainActive") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'domain_active_status' => $m->get_data('isActive')
    );

    $q = $d->update('domain_master', $a1, "domain_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "utilityAppMenuDeactive") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array('menu_status' => $m->get_data('isActive'));
    $q = $d->update('resident_app_menu_utility', $a1, "app_menu_id='$id' ");
    if ($q > 0) {
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "App Menu Utility Deactive ($id)", 5);
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "utilityAppMenuActive") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array('menu_status' => $m->get_data('isActive'));
    $q = $d->update('resident_app_menu_utility', $a1, "app_menu_id='$id' ");
    if ($q > 0) {
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "App Menu Utility Active ($id)", 5);
      echo 1;
    } else {
      echo 0;
    }
  }

  //satyajeet start
  if (isset($Status) && $_POST['Status'] == "leaveTypeStatus") {
    $a1 = array('leave_type_status' => $leave_type_status);
    if ($leave_type_status == '0') {
      $meg = "Leave Type $leave_type_name Activated.";
    } else {
      $meg = "Leave Type $leave_type_name Deactivated.";
    }
    $q = $d->update("leave_types_master", $a1, "leave_type_id=$leave_type_id");
    if ($q > 0) {
      $_SESSION['msg'] = $meg;
      $d->insert_log("0", "$bms_admin_id", "$created_by", "$meg");
      header("location:../leaveTypes");
      exit();
    } else {
      $_SESSION['msg1'] = "Something Went Wrong?";
      header("location:../leaveTypes");
      exit();
    }
  }
  //satyajeet end


  //start dharti 14-2-2025
  if (isset($Status) && $_POST['Status'] == "holidayStatus") {
    $a1 = array('holiday_status' => $holiday_status);
    if ($holiday_status == '0') {
      $meg = "Holiday $festival_name Activated Successfully.";
    } else {
      $meg = "Holiday $festival_name Deactivated Successfully.";
    }
    $q = $d->update("holidays_master", $a1, "holiday_id=$holiday_id");
    if ($q > 0) {
      $_SESSION['msg'] = $meg;
      $d->insert_log("0", "$bms_admin_id", "$created_by", "$meg");
      header("location:../holidays");
      exit();
    } else {
      $_SESSION['msg1'] = "Something Went Wrong?";
      header("location:../holidays");
      exit();
    }
  }

  if (isset($Status) && $_POST['Status'] == "expenseStatus") {
    $a1 = array('expense_status' => $expense_status);
    if ($expense_status == '0') {
      $meg = "Expense $expense_title Activated Successfully.";
    } else {
      $meg = "Expense $expense_title Deactivated Successfully.";
    }
    $q = $d->update("expense_master", $a1, "expense_id=$expense_id");
    if ($q > 0) {
      $_SESSION['msg'] = $meg;
      $d->insert_log("0", "$bms_admin_id", "$created_by", "$meg");
      header("location:../expense");
      exit();
    } else {
      $_SESSION['msg1'] = "Something Went Wrong?";
      header("location:../expense");
      exit();
    }
  }
  //end dharti 14-2-2025

  if (isset($status) && $_POST['status'] == "moduleStatusDeactive") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'training_module_status' => $m->get_data('isActive')
    );
    $q = $d->update('training_module_master', $a1, "training_module_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "moduleStatusActive") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'training_module_status' => $m->get_data('isActive')
    );
    $q = $d->update('training_module_master', $a1, "training_module_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "batchStatusDeactive") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'status' => $m->get_data('isActive')
    );
    $q = $d->update('training_batch_master', $a1, "batch_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "batchStatusActive") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $a1 = array(
      'status' => $m->get_data('isActive')
    );
    $q = $d->update('training_batch_master', $a1, "batch_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }
  //end dharti 14-2-2025


  //start dhruvi_raval 27-3-2025
  if (isset($_POST['status']) && $_POST['status'] == "sessionStatusDeactive") {
    $session_status = 1;
    $m->set_data('session_status', $session_status);
    $updateData = array(
      'session_status' => $m->get_data('session_status')
    );
    $q = $d->update('session_master', $updateData, "session_id='$id'");
    echo ($q > 0) ? 1 : 0;
  }

  if (isset($_POST['status']) && $_POST['status'] == "sessionStatusActive") {
    $session_status = 0;
    $m->set_data('session_status', $session_status);
    $updateData = array(
      'session_status' => $m->get_data('session_status')
    );
    $q = $d->update('session_master', $updateData, "session_id='$id'");
    echo ($q > 0) ? 1 : 0;
  }

  if (isset($_POST['status']) && $_POST['status'] == "priorityStatusDeactive") {
    $isRequired = 0;
    $m->set_data('isRequired', $isRequired);
    $updateData = array(
      'is_required' => $m->get_data('isRequired')
    );
    $q = $d->update('training_module_priority_master', $updateData, "priority_id='$id'");
    echo ($q > 0) ? 1 : 0;
  }

  if (isset($_POST['status']) && $_POST['status'] == "priorityStatusActive") {
    $isRequired = 1;
    $m->set_data('isRequired', $isRequired);
    $updateData = array(
      'is_required' => $m->get_data('isRequired')
    );
    $q = $d->update('training_module_priority_master', $updateData, "priority_id='$id'");
    echo ($q > 0) ? 1 : 0;
  }


  if (isset($_POST['status']) && $_POST['status'] == "participantStatusDeactive") {
    $isActive = 1;
    $m->set_data('isActive', $isActive);
    $updateData = array(
      'status' => $m->get_data('isActive')
    );
    $q = $d->update('training_participants_type', $updateData, "participants_type_id='$id'");
    echo ($q > 0) ? 1 : 0;
  }

  if (isset($_POST['status']) && $_POST['status'] == "participantStatusActive") {
    $isActive = 0;
    $m->set_data('isActive', $isActive);
    $updateData = array(
      'status' => $m->get_data('isActive')
    );
    $q = $d->update('training_participants_type', $updateData, "participants_type_id='$id'");
    echo ($q > 0) ? 1 : 0;
  }

  // Deactivate suggestion
  if (isset($_POST['status']) && $_POST['status'] == "suggestionStatusDeactive") {
    $status = 1;
    $m->set_data('suggestion_status', $status);
    $updateData = array(
      'suggestion_status' => $m->get_data('suggestion_status')
    );
    $q = $d->update('suggestion_master', $updateData, "suggestion_id='$id'");
    echo ($q > 0) ? 1 : 0;
  }

  // Activate suggestion
  if (isset($_POST['status']) && $_POST['status'] == "suggestionStatusActive") {
    $status = 0;
    $m->set_data('suggestion_status', $status);
    $updateData = array(
      'suggestion_status' => $m->get_data('suggestion_status')
    );
    $q = $d->update('suggestion_master', $updateData, "suggestion_id='$id'");
    echo ($q > 0) ? 1 : 0;
  }


  if (isset($_POST['status']) && $_POST['status'] == "planStatusDeactive") {
    $status = 1;
    $m->set_data('status', $status);
    $updateData = array(
      'status' => $m->get_data('status')
    );
    $q = $d->update('manage_plan', $updateData, "plan_id='$id'");
    echo ($q > 0) ? 1 : 0;
  }



  if (isset($_POST['status']) && $_POST['status'] == "planStatusActive") {
    $session_status = 0;
    $m->set_data('status', $status);
    $updateData = array(
      'status' => $m->get_data('status')
    );
    $q = $d->update('manage_plan', $updateData, "plan_id='$id'");
    echo ($q > 0) ? 1 : 0;
  }
//     print_r($_POST);
// exit;
  //added by prashil 6-6-2025
  if (isset($_POST['status']) && $_POST['status'] == 'IdProofStatusActive') {

    $m->set_data('id_proof_id', test_input($id));
    $delete_data = array(
      "active_status" => 0,
    );
    $where = "id_proof_id = '" . $m->get_data('id_proof_id') . "'";
    $q = $d->update("id_proof_master", $delete_data, $where);
    echo ($q > 0) ? 1 : 0;
  }
  if (isset($_POST['status']) && $_POST['status'] == 'IdProofStatusDeactive') {

    $m->set_data('id_proof_id', test_input($id));
    $delete_data = array(
      "active_status" => 1,
    );
    $where = "id_proof_id = '" . $m->get_data('id_proof_id') . "'";
    $q = $d->update("id_proof_master", $delete_data, $where);
    echo ($q > 0) ? 1 : 0;
  }


 if (isset($status) && $_POST['status'] == "taxBenefitCatDeactive") {
    $isActive = 0;
    $m->set_data('active_status', $isActive);
    $a1 = array(
      'active_status' => $m->get_data('active_status')
    );
    $q = $d->update('tax_benefit_category', $a1, "tax_benefit_category_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "taxBenefitCatActive") {
    $isActive = 1;
    $m->set_data('active_status', $isActive);
    $a1 = array(
      'active_status' => $m->get_data('active_status')
    );
    $q = $d->update('tax_benefit_category', $a1, "tax_benefit_category_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }
  if (isset($status) && $_POST['status'] == "appMenuCategoryActive") {
    $isActive = 0;
    $m->set_data('menu_category_status', $isActive);
    $a1 = array(
      'menu_category_status' => $m->get_data('menu_category_status')
    );
    $q = $d->update('menu_category_master', $a1, "menu_category_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }
  if (isset($status) && $_POST['status'] == "appMenuCategoryDeactive") {
    $isActive = 1;
    $m->set_data('menu_category_status', $isActive);
    $a1 = array(
      'menu_category_status' => $m->get_data('menu_category_status')
    );
    $q = $d->update('menu_category_master', $a1, "menu_category_id='$id'");
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }

  // Responding Status handlers
  if (isset($status) && $_POST['status'] == "respondingStatusActive") {
    $is_not_responding = 0;
    $m->set_data('is_not_responding', $is_not_responding);
    $a1 = array(
      'is_not_responding' => $m->get_data('is_not_responding')
    );
    $q = $d->update('society_master', $a1, "society_id='$id'");
    if ($q > 0) {
      $d->insert_log("$id", "$bms_admin_id", "$created_by", "Responding Status Updated to: Responding");
      echo 1;
    } else {
      echo 0;
    }
  }

  if (isset($status) && $_POST['status'] == "respondingStatusDeactive") {
    $is_not_responding = 1;
    $m->set_data('is_not_responding', $is_not_responding);
    $a1 = array(
      'is_not_responding' => $m->get_data('is_not_responding')
    );
    $q = $d->update('society_master', $a1, "society_id='$id'");
    if ($q > 0) {
      $d->insert_log("$id", "$bms_admin_id", "$created_by", "Responding Status Updated to: Not Responding");
      echo 1;
    } else {
      echo 0;
    }
  }
  if (isset($status) && $_POST['status'] == "removeFromRise") {
    $from_rise_event = 0;
    $m->set_data('from_rise_event', $from_rise_event);
    $a1 = array(
      'from_rise_event' => $m->get_data('from_rise_event')
    );
    $q = $d->update('society_master', $a1, "society_id='$id'");
    if ($q > 0) {
      $d->insert_log("$id", "$bms_admin_id", "$created_by", "Company $id Switched to Rise Event");
      echo 1;
    } else {
      echo 0;
    }
  }
  if (isset($status) && $_POST['status'] == "addToRise") {
    $from_rise_event = 1;
    $m->set_data('from_rise_event', $from_rise_event);
    $a1 = array(
      'from_rise_event' => $m->get_data('from_rise_event')
    );
    $q = $d->update('society_master', $a1, "society_id='$id'");
    if ($q > 0) {
      $d->insert_log("$id", "$bms_admin_id", "$created_by", "Company $id Switched to Without Rise Event");
      echo 1;
    } else {
      echo 0;
    }
  }
} else {
  header('location:../logout');
}
