<?php
if (isset($json) && is_array($json) && count($json) > 0) {
    $employee_tracking_limit = $json["employee_tracking_limit"];
    $employee_registration_limit = $json["employee_registration_limit"];
    $society_name = $json["society_name"];
    $society_address = $json["society_address"];
    $socieaty_logo = $json["socieaty_logo"];
    $society_latitude = $json["society_latitude"];
    $society_longitude = $json["society_longitude"];
    $city_id = $json["city_id"];
    $secretary_email = $json["secretary_email"];
    $secretary_mobile = $json["secretary_mobile"];
    $crm_limit = $json["crm_limit"] ?? '0';
    $totalTrackingOn = $json["totalTrackingOn"];
    $totalGoogleVisit = $json["totalGoogleVisit"];
    $thisMonthGoogleVisit = $json["thisMonthGoogleVisit"];
    $preMonthGoogleVisit = $json["preMonthGoogleVisit"];
    $no_of_units = $json["no_of_units"];
    $no_of_blocks = $json["no_of_blocks"];
    $total_users = $json["total_users"];
    $total_notice_board = $json["total_notice_board"];
    $total_events = $json["total_events"];
    $total_sos_triger = $json["total_sos_triger"];
    $total_polls = $json["total_polls"];
    $total_document = $json["total_document"];
    $total_lost_found = $json["total_lost_found"];
    $total_penalty = $json["total_penalty"];
    $total_duscussion_foram = $json["total_duscussion_foram"];
    $total_timeline_post = $json["total_timeline"];
    $total_login_android = $json["total_login_android"];
    $total_login_ios = $json["total_login_ios"];
    $total_logged_in_users = $json["total_logged_in_users"] ?? $json["total_users"];
    $total_chat_msg = $json["total_chat_message"];
    $total_maintence_amount = $json["total_maintenance_amount"] ?? "";
    $total_received_maintenance_amount = $json["total_received_maintenance_amount"] ?? "";
    $total_received_maintenance_amount_online = $json["total_received_maintenance_amount_online"] ?? "";
    $total_attendace = $json["total_attendace"];
    $total_attendace_weekly = $json["total_attendace_weekly"];
    $total_attendace_this_month = $json["total_attendace_this_month"];
    $total_attendace_prev_month = $json["total_attendace_prev_month"];
    $total_work_from_home = $json["total_work_from_home"];
    $total_salary_slip = $json["total_salary_slip"];
    $total_monthly_salary_slip = $json["total_monthly_salary_slip"];
    $total_leaves = $json["total_leaves"];
    $total_work_report = $json["total_work_report"];
    $total_work_report_weekly = $json["total_work_report_weekly"];
    $total_work_report_this_month = $json["total_work_report_this_month"];
    $total_work_report_prev_month = $json["total_work_report_prev_month"];
    $total_dar_work_report = $json["total_dar_work_report"];
    $total_dar_work_report_weekly = $json["total_dar_work_report_weekly"];
    $total_dar_work_report_this_month = $json["total_dar_work_report_this_month"];
    $total_dar_work_report_prev_month = $json["total_dar_work_report_prev_month"];
    $total_assets = $json["total_assets"];
    $img_storage = $json["img_storage"];
    $db_size = $json["db_size"] ?? '';
    $admin_size = $json["admin_size"] ?? '';
    $total_loan = $json["total_loan"] ?? '';
    $advance_salary = $json["advance_salary"] ?? '';
    $newCurrentMonthAttendanceUniqueUser = $json["newCurrentMonthAttendanceUniqueUser"];
    $newPrevMonthAttendanceUniqueUser = $json["newPrevMonthAttendanceUniqueUser"];
    $newPrevToPrevMonthAttendanceUniqueUser = $json["newPrevToPrevMonthAttendanceUniqueUser"];

    $newCurrentMonthPayrollUniqueUser = $json["newCurrentMonthPayrollUniqueUser"];
    $newPrevMonthPayrollUniqueUser = $json["newPrevMonthPayrollUniqueUser"];
    $newPrevToPrevMonthPayrollUniqueUser = $json["newPrevToPrevMonthPayrollUniqueUser"];

    $totalWorkReportMonth = $json["total_work_report"];
    $second_last_month_work_report_count = $json["second_last_month_work_report_count"];

    $currentMonthCircular = $json["currentMonthCircular"];
    $prevMonthCircular = $json["prevMonthCircular"];
    $prevToPrevMonthCircular = $json["prevToPrevMonthCircular"];

    $currentMonthExpense = $json["currentMonthExpense"];
    $prevMonthExpense = $json["prevMonthExpense"];
    $prevToPrevMonthExpense = $json["prevToPrevMonthExpense"];

    $trackingUsersCurrentMonth = $json["trackingUsersCurrentMonth"];
    $trackingUsersPrevMonth = $json["trackingUsersPrevMonth"];
    $trackingUsersPrevToPrevMonth = $json["trackingUsersPrevToPrevMonth"];

    $totalGoogleVisit = $json["totalGoogleVisit"];
    $thisMonthGoogleVisit = $json["thisMonthGoogleVisit"];
    $preMonthGoogleVisit = $json["preMonthGoogleVisit"];
    $prevToPrevMonthGoogleVisit = $json["prevToPrevMonthGoogleVisit"];

    $currentMonthTask = $json["currentMonthTask"];
    $prevMonthTask = $json["prevMonthTask"];
    $prevToPrevMonthTask = $json["prevToPrevMonthTask"];

    $currentMonthVisitor = $json["currentMonthVisitor"];
    $prevMonthVisitor = $json["prevMonthVisitor"];
    $prevToPrevMonthVisitor = $json["prevToPrevMonthVisitor"];

    $currentMonthWFH = $json["currentMonthWFH"];
    $prevMonthWFH = $json["prevMonthWFH"];
    $prevToPrevMonthWFH = $json["prevToPrevMonthWFH"];

    $currentOpenOpenings = $json["currentOpenOpenings"];

    $currentMonthTimeline = $json["currentMonthTimeline"];
    $prevMonthTimeline = $json["prevMonthTimeline"];
    $prevToPrevTimeline = $json["prevToPrevTimeline"];

    $currentMonthEvent = $json["currentMonthEvent"];
    $prevMonthEvent = $json["prevMonthEvent"];
    $prevToPrevMonthEvent = $json["prevToPrevMonthEvent"];

    $currentMonthGallery = $json["currentMonthGallery"];
    $prevMonthGallery = $json["prevMonthGallery"];
    $prevToPrevMonthGallery = $json["prevToPrevMonthGallery"];

    $currentMonthPenalty = $json["currentMonthPenalty"];
    $prevMonthPenalty = $json["prevMonthPenalty"];
    $prevToPrevMonthPenalty = $json["prevToPrevMonthPenalty"];

    $currentVendorRegistered = $json["currentVendorRegistered"];

    $currentMonthSalesOrder = $json["currentMonthSalesOrder"];
    $prevMonthSalesOrder = $json["prevMonthSalesOrder"];
    $prevToPrevSalesOrder = $json["prevToPrevSalesOrder"];
    $adminViewAccess = $json["adminViewAccess"];
    $total_visitors = $json["total_visitors"];

    $user_version_data = $json["user_version_data"] ?? [];
    $user_mobile_os_data = $json["user_mobile_os_data"] ?? [];
    foreach ($user_version_data as $version_data) {
        $device_type = $version_data['device'] ?? "";
        $app_version_code = $version_data['app_version_code'];
        $user_version_insert = array(
            "society_id" => $society_id,
            "users_count" => $version_data['users_count'],
            "device_type" => $version_data['device'],
            "app_version_code" => $version_data['app_version_code'],
            "last_updated_date" => date("Y-m-d H:i:s"),
            "type" => '0'
        );
        $does_exist = $d->count_data_direct("user_version_data_id", "user_version_data_master", "app_version_code='$app_version_code' AND society_id='$society_id' AND device_type='$device_type' AND type='0'");
        if ($does_exist == '0') {
            $user_version = $d->insert("user_version_data_master", $user_version_insert);
        } else {
            $user_version = $d->update("user_version_data_master", $user_version_insert, "app_version_code='$app_version_code' AND society_id='$society_id' AND device_type='$device_type' AND type='0'");
        }
    }
    foreach ($user_mobile_os_data as $version_data) {
        $device_type = $version_data['device'];
        $app_version_os = $version_data['mobile_os_version'];
        $user_mobile_insert = array(
            "society_id" => $society_id,
            "users_count" => $version_data['users_count'],
            "device_type" => $version_data['device'],
            "app_version_os" => $version_data['mobile_os_version'],
            "last_updated_date" => date("Y-m-d H:i:s"),
            "type" => '1'
        );
        $does_exist = $d->count_data_direct("user_version_data_id", "user_version_data_master", "app_version_os='$app_version_os' AND society_id='$society_id' AND device_type='$device_type' AND type='1'");
        if ($does_exist == '0') {
            $user_version = $d->insert("user_version_data_master", $user_mobile_insert);
        } else {
            $user_version = $d->update("user_version_data_master", $user_mobile_insert, "app_version_os='$app_version_os' AND society_id='$society_id' AND device_type='$device_type' AND type='1'");
        }
    }
    $status = $json["status"];

    $m->set_data('society_id', $society_id);
    $m->set_data('totalGoogleVisit', $totalGoogleVisit);
    $m->set_data('thisMonthGoogleVisit', $thisMonthGoogleVisit);
    $m->set_data('preMonthGoogleVisit', $preMonthGoogleVisit);
    $m->set_data('no_of_units', $no_of_units);
    $m->set_data('no_of_blocks', $no_of_blocks);
    $m->set_data('total_users', $total_users);
    $m->set_data('total_notice_board', $total_notice_board);
    $m->set_data('total_events', $total_events);
    $m->set_data('total_sos_triger', $total_sos_triger);
    $m->set_data('total_polls', $total_polls);
    $m->set_data('total_document', $total_document);
    $m->set_data('total_facilities', $total_facilities ?? "");
    $m->set_data('total_lost_found', $total_lost_found);
    $m->set_data('total_penalty', $total_penalty);
    $m->set_data('total_duscussion_foram', $total_duscussion_foram);
    $m->set_data('total_login_user', $total_logged_in_users);
    $m->set_data('total_login_android', $total_login_android);
    $m->set_data('total_login_ios', $total_login_ios);
    $m->set_data('total_chat_msg', $total_chat_msg);
    $m->set_data('total_timeline_post', $total_timeline_post);
    $m->set_data('total_maintence_amount', $total_maintence_amount);
    $m->set_data('total_received_maintenance_amount', $total_received_maintenance_amount);
    $m->set_data('total_received_maintenance_amount_online', $total_received_maintenance_amount_online);
    $m->set_data('city_id', $city_id);
    $m->set_data('update_date', $today);
    $m->set_data('status', $status);
    $m->set_data('last_updated_date', date('Y-m-d H:i:s'));
    $m->set_data('total_attendace', $total_attendace);
    $m->set_data('total_attendace_weekly', $total_attendace_weekly);
    $m->set_data('total_attendace_this_month', $total_attendace_this_month);
    $m->set_data('total_attendace_prev_month', $total_attendace_prev_month);
    $m->set_data('total_work_from_home', $total_work_from_home);
    $m->set_data('total_salary_slip', $total_salary_slip);
    $m->set_data('total_monthly_salary_slip', $total_monthly_salary_slip);
    $m->set_data('total_leaves', $total_leaves);
    $m->set_data('total_work_report', $total_work_report);
    $m->set_data('total_work_report_weekly', $total_work_report_weekly);
    $m->set_data('total_work_report_this_month', $total_work_report_this_month);
    $m->set_data('total_work_report_prev_month', $total_work_report_prev_month);
    $m->set_data('total_dar_work_report', $total_dar_work_report);
    $m->set_data('total_dar_work_report_weekly', $total_dar_work_report_weekly);
    $m->set_data('total_dar_work_report_this_month', $total_dar_work_report_this_month);
    $m->set_data('total_dar_work_report_prev_month', $total_dar_work_report_prev_month);
    $m->set_data('total_assets', $total_assets);
    $m->set_data('img_storage', $img_storage);
    $m->set_data('db_size', $db_size);
    $m->set_data('admin_size', $admin_size);
    $m->set_data('total_loan', $total_loan);
    $m->set_data('advance_salary', $advance_salary);
    $m->set_data('active_tracking_users', $totalTrackingOn);
    // $m->set_data('total_users', $total_users);
    $m->set_data('current_month_attendance_count', $newCurrentMonthAttendanceUniqueUser);
    $m->set_data('last_month_attendance_count', $newPrevMonthAttendanceUniqueUser);
    $m->set_data('second_last_month_attendance_count', $newPrevToPrevMonthAttendanceUniqueUser);

    $m->set_data('current_month_payroll_count', $newCurrentMonthPayrollUniqueUser);
    $m->set_data('last_month_payroll_count', $newPrevMonthPayrollUniqueUser);
    $m->set_data('second_last_month_payroll_count', $newPrevToPrevMonthPayrollUniqueUser);

    $m->set_data('current_month_work_report_count', $totalWorkReportMonth);
    $m->set_data('last_month_work_report_count', $totalWorkReportPrvMonth ?? "");
    $m->set_data('second_last_month_work_report_count', $second_last_month_work_report_count);

    $m->set_data('current_month_circular_count', $currentMonthCircular);
    $m->set_data('last_month_circular_count', $prevMonthCircular);
    $m->set_data('second_last_month_circular_count', $prevToPrevMonthCircular);

    // $m->set_data('total_assets', $total_assets);

    $m->set_data('current_month_expense_count', $currentMonthExpense);
    $m->set_data('last_month_expense_count', $prevMonthExpense);
    $m->set_data('second_last_month_expense_count', $prevToPrevMonthExpense);

    $m->set_data('admin_view_access', $adminViewAccess);

    $m->set_data('current_month_tracking_user_count', $trackingUsersCurrentMonth);
    $m->set_data('last_month_tracking_user_count', $trackingUsersPrevMonth);
    $m->set_data('second_last_month_tracking_user_count', $trackingUsersPrevToPrevMonth);

    $m->set_data('total_google_visit_count', $totalGoogleVisit);
    $m->set_data('current_month_google_visit_count', $thisMonthGoogleVisit);
    $m->set_data('last_month_google_visit_count', $preMonthGoogleVisit);
    $m->set_data('second_last_month_google_visit_count', $prevToPrevMonthGoogleVisit);

    // $m->set_data('total_document', $total_document);

    $m->set_data('current_month_task_count', $currentMonthTask);
    $m->set_data('last_month_task_count', $prevMonthTask);
    $m->set_data('second_last_month_task_count', $prevToPrevMonthTask);

    $m->set_data('current_month_visitor_count', $currentMonthVisitor);
    $m->set_data('last_month_visitor_count', $prevMonthVisitor);
    $m->set_data('second_last_month_visitor_count', $prevToPrevMonthVisitor);

    $m->set_data('current_month_wfh_count', $currentMonthWFH);
    $m->set_data('last_month_wfh_count', $prevMonthWFH);
    $m->set_data('second_last_month_wfh_count', $prevToPrevMonthWFH);

    $m->set_data('current_opening_count', $currentOpenOpenings);

    $m->set_data('current_month_timeline_count', $currentMonthTimeline);
    $m->set_data('last_month_timeline_count', $prevMonthTimeline);
    $m->set_data('second_last_month_timeline_count', $prevToPrevTimeline);

    $m->set_data('current_month_event_count', $currentMonthEvent);
    $m->set_data('last_month_event_count', $prevMonthEvent);
    $m->set_data('second_last_month_event_count', $prevToPrevMonthEvent);

    $m->set_data('current_month_gallery_count', $currentMonthGallery);
    $m->set_data('last_month_gallery_count', $prevMonthGallery);
    $m->set_data('second_last_month_gallery_count', $prevToPrevMonthGallery);

    $m->set_data('current_month_penalty_count', $currentMonthPenalty);
    $m->set_data('last_month_penalty_count', $prevMonthPenalty);
    $m->set_data('second_last_month_penalty_count', $prevToPrevMonthPenalty);

    $m->set_data('total_registered_vendors', $currentVendorRegistered);

    $m->set_data('current_month_sales_order_count', $currentMonthSalesOrder);
    $m->set_data('last_month_sales_order_count', $prevMonthSalesOrder);
    $m->set_data('second_last_month_sales_order_count', $prevToPrevSalesOrder);
    $m->set_data('total_visitors', $total_visitors);
    $m->set_data('total_maintenance', $total_maintenance ?? "");
    $society_update_data = array(
        "society_name" => "$society_name",
        "society_address" => "$society_address",
        "society_latitude" => "$society_latitude",
        "society_longitude" => "$society_longitude",
        "socieaty_logo" => "$socieaty_logo",
        "secretary_email" => "$secretary_email",
        "secretary_mobile" => "$secretary_mobile",
    );
    if ($crm_limit != "0" && $crm_limit != '') {
        $society_update_data['crm_limit'] = "$crm_limit";
    }
    $tracking_status = $tracking_status ?? '';
    if ($employee_tracking_limit == '0' && $tracking_status == '0') {
        $society_update_data['employee_tracking_limit'] = "$totalTrackingOn";
        $society_update_data['tracking_status'] = "1";
    } else {
        $society_update_data['tracking_status'] = "1";
        $society_update_data['employee_tracking_limit'] = "$employee_tracking_limit";
    }
    if ($employee_registration_limit != "" && $employee_registration_limit != 0) {
        $society_update_data['employee_registration_limit'] = "$employee_registration_limit";
    }
    $update_tracking_data = $d->update("society_master", $society_update_data, "society_id='$society_id'");
    $a1 = array(
        'society_id' => $m->get_data('society_id'),
        'active_tracking_users' => $m->get_data('active_tracking_users'),
        'totalGoogleVisit' => $m->get_data('totalGoogleVisit'),
        'thisMonthGoogleVisit' => $m->get_data('thisMonthGoogleVisit'),
        'preMonthGoogleVisit' => $m->get_data('preMonthGoogleVisit'),
        'no_of_units' => $m->get_data('no_of_units'),
        'no_of_blocks' => $m->get_data('no_of_blocks'),
        'total_maintenance' => $m->get_data('total_maintenance'),
        'total_users' => $m->get_data('total_users'),
        'total_notice_board' => $m->get_data('total_notice_board'),
        'total_events' => $m->get_data('total_events'),
        'total_sos_triger' => $m->get_data('total_sos_triger'),
        'total_polls' => $m->get_data('total_polls'),
        'total_document' => $m->get_data('total_document'),
        'total_facilities' => $m->get_data('total_facilities'),
        'total_lost_found' => $m->get_data('total_lost_found'),
        'total_penalty' => $m->get_data('total_penalty'),
        'total_duscussion_foram' => $m->get_data('total_duscussion_foram'),
        'total_login_user' => $m->get_data('total_login_user'),
        'total_login_android' => $m->get_data('total_login_android'),
        'total_login_ios' => $m->get_data('total_login_ios'),
        'total_chat_msg' => $m->get_data('total_chat_msg'),
        'total_timeline_post' => $m->get_data('total_timeline_post'),
        'city_id' => $m->get_data('city_id'),
        'update_date' => $m->get_data('update_date'),
        'status' => $m->get_data('status'),
        'last_updated_date' => $m->get_data('last_updated_date'),
        'total_attendace' => $m->get_data('total_attendace'),
        'total_attendace_weekly' => $m->get_data('total_attendace_weekly'),
        'total_attendace_this_month' => $m->get_data('total_attendace_this_month'),
        'total_attendace_prev_month' => $m->get_data('total_attendace_prev_month'),
        'total_work_from_home' => $m->get_data('total_work_from_home'),
        'total_salary_slip' => $m->get_data('total_salary_slip'),
        'total_monthly_salary_slip' => $m->get_data('total_monthly_salary_slip'),
        'total_leaves' => $m->get_data('total_leaves'),
        'total_work_report' => $m->get_data('total_work_report'),
        'total_work_report_weekly' => $m->get_data('total_work_report_weekly'),
        'total_work_report_this_month' => $m->get_data('total_work_report_this_month'),
        'total_work_report_prev_month' => $m->get_data('total_work_report_prev_month'),
        'total_dar_work_report' => $m->get_data('total_dar_work_report'),
        'total_dar_work_report_weekly' => $m->get_data('total_dar_work_report_weekly'),
        'total_dar_work_report_this_month' => $m->get_data('total_dar_work_report_this_month'),
        'total_dar_work_report_prev_month' => $m->get_data('total_dar_work_report_prev_month'),
        'total_assets' => $m->get_data('total_assets'),
        'img_storage' => $m->get_data('img_storage'),
        'db_size' => $m->get_data('db_size'),
        'admin_size' => $m->get_data('admin_size'),
        'total_loan' => $m->get_data('total_loan'),
        'advance_salary' => $m->get_data('advance_salary'),
        // 'total_users' => $m->get_data('total_users'),
        'current_month_attendance_count' => $m->get_data('current_month_attendance_count'),
        'last_month_attendance_count' => $m->get_data('last_month_attendance_count'),
        'second_last_month_attendance_count' => $m->get_data('second_last_month_attendance_count'),

        'current_month_payroll_count' => $m->get_data('current_month_payroll_count'),
        'last_month_payroll_count' => $m->get_data('last_month_payroll_count'),
        'second_last_month_payroll_count' => $m->get_data('second_last_month_payroll_count'),

        'current_month_work_report_count' => $m->get_data('total_work_report_this_month'),
        'last_month_work_report_count' => $m->get_data('total_work_report_prev_month'),
        'second_last_month_work_report_count' => $m->get_data('second_last_month_work_report_count'),

        'current_month_circular_count' => $m->get_data('current_month_circular_count'),
        'last_month_circular_count' => $m->get_data('last_month_circular_count'),
        'second_last_month_circular_count' => $m->get_data('second_last_month_circular_count'),

        // 'total_assets' => $m->get_data('total_assets'),

        'current_month_expense_count' => $m->get_data('current_month_expense_count'),
        'last_month_expense_count' => $m->get_data('last_month_expense_count'),
        'second_last_month_expense_count' => $m->get_data('second_last_month_expense_count'),

        'admin_view_access' => $m->get_data('admin_view_access'),

        'current_month_tracking_user_count' => $m->get_data('current_month_tracking_user_count'),
        'last_month_tracking_user_count' => $m->get_data('last_month_tracking_user_count'),
        'second_last_month_tracking_user_count' => $m->get_data('second_last_month_tracking_user_count'),

        'total_google_visit_count' => $m->get_data('total_google_visit_count'),
        'current_month_google_visit_count' => $m->get_data('current_month_google_visit_count'),
        'last_month_google_visit_count' => $m->get_data('last_month_google_visit_count'),
        'second_last_month_google_visit_count' => $m->get_data('second_last_month_google_visit_count'),

        'current_month_task_count' => $m->get_data('current_month_task_count'),
        'last_month_task_count' => $m->get_data('last_month_task_count'),
        'second_last_month_task_count' => $m->get_data('second_last_month_task_count'),

        'current_month_visitor_count' => $m->get_data('current_month_visitor_count'),
        'last_month_visitor_count' => $m->get_data('last_month_visitor_count'),
        'second_last_month_visitor_count' => $m->get_data('second_last_month_visitor_count'),

        'current_month_wfh_count' => $m->get_data('current_month_wfh_count'),
        'last_month_wfh_count' => $m->get_data('last_month_wfh_count'),
        'second_last_month_wfh_count' => $m->get_data('second_last_month_wfh_count'),

        'current_opening_count' => $m->get_data('current_opening_count'),

        'current_month_timeline_count' => $m->get_data('current_month_timeline_count'),
        'last_month_timeline_count' => $m->get_data('last_month_timeline_count'),
        'second_last_month_timeline_count' => $m->get_data('second_last_month_timeline_count'),

        'current_month_event_count' => $m->get_data('current_month_event_count'),
        'last_month_event_count' => $m->get_data('last_month_event_count'),
        'second_last_month_event_count' => $m->get_data('second_last_month_event_count'),

        'current_month_gallery_count' => $m->get_data('current_month_gallery_count'),
        'last_month_gallery_count' => $m->get_data('last_month_gallery_count'),
        'second_last_month_gallery_count' => $m->get_data('second_last_month_gallery_count'),

        'current_month_penalty_count' => $m->get_data('current_month_penalty_count'),
        'last_month_penalty_count' => $m->get_data('last_month_penalty_count'),
        'second_last_month_penalty_count' => $m->get_data('second_last_month_penalty_count'),

        'total_registered_vendors' => $m->get_data('total_registered_vendors'),

        'current_month_sales_order_count' => $m->get_data('current_month_sales_order_count'),
        'last_month_sales_order_count' => $m->get_data('last_month_sales_order_count'),
        'second_last_month_sales_order_count' => $m->get_data('second_last_month_sales_order_count'),
        'total_visitors' => $m->get_data('total_visitors'),
    );

    $qc = $d->select("society_analytics_master", "society_id='$society_id'");
    if (mysqli_num_rows($qc) > 0) {
        $q = $d->update("society_analytics_master", $a1, "society_id='$society_id'");
    } else {
        $q = $d->insert("society_analytics_master", $a1);
    }

    $whatsapp_data = isset($json["whatsapp"]) && is_array($json["whatsapp"]) ? $json["whatsapp"] : [];
    $existing = [];
    $res = $d->select("whatsapp_access_master", "society_id='$society_id'");
    while ($row = mysqli_fetch_assoc($res)) {
        $existing[$row['user_mobile']] = $row;
    }
    $incoming = [];
    foreach ($whatsapp_data as $entry) {
        if (!empty($entry['user_mobile'])) {
            $incoming[$entry['user_mobile']] = $entry;
        }
    }
    $toDelete = array_diff_key($existing, $incoming);
    foreach ($toDelete as $user_mobile => $info) {
        $d->delete("whatsapp_access_master", "society_id='$society_id' AND user_mobile='" . addslashes($user_mobile) . "'");
    }
    foreach ($incoming as $user_mobile => $data) {
        $insertUpdate = array(
            "society_id" => $society_id,
            "user_full_name" => $data['user_full_name'],
            "user_mobile" => $data['user_mobile'],
            "country_code" => $data['country_code'],
            "mobile_number_only" => $data['mobile_number_only'],
            "app_access_id" => $data['app_access_id'],
            "type" => $data['type'],
        );
        if (isset($existing[$user_mobile])) {
            $d->update("whatsapp_access_master", $insertUpdate, "society_id='$society_id' AND user_mobile='" . addslashes($user_mobile) . "'");
        } else {
            $d->insert("whatsapp_access_master", $insertUpdate);
        }
    }
    $d->update("society_master", ["whatsapp_access_last_sync_date" => date('Y-m-d H:i:s')], "society_id='$society_id'");
    $d->syncWhatsAppAnalyticsCronFromAccess($society_id);
}
