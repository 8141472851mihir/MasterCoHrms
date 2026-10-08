<?php
include '../common/objectController.php';

if (!function_exists('respondCompanyAction')) {
    function respondCompanyAction($message, $status)
    {
        header('Content-Type: application/json');
        echo json_encode(array(
            'message' => (string) $message,
            'status' => (string) $status,
        ));
        exit;
    }
}

if (isset($_POST) && !empty($_POST)) {
    if (isset($getCompanyData)) {
        if (isset($getDataType) && $getDataType == '13') {
            $customKeyCount = $d->count_data_direct("value_master_society_id", "language_key_value_master_society", "society_id='$society_id_post'");
            if ($customKeyCount > 0) {
                $languageQuery = $d->select("language_master", "active_status=0 ", "");
                while ($languageData = mysqli_fetch_array($languageQuery)) {
                    $l_id = $languageData['language_id'];
                    $d->createCompanyLanguageFiles($society_id_post, $l_id, $bms_admin_id, $created_by);
                }
                respondCompanyAction(' - Sync Language Data', '200');
            } else {
                respondCompanyAction(' - No Custom Keys Found', '201');
            }
        }
        if (isset($getDataType) && $getDataType == '25') {
            $society_id = (int) $society_id_post;
            $web_ai_status = isset($web_ai_status) ? ((int) $web_ai_status === 1 ? 1 : 0) : 0;
            $web_ai_credentials_id = isset($web_ai_credentials_id) ? (int) $web_ai_credentials_id : 0;
            $web_ai_url_mode = isset($web_ai_url_mode) ? trim((string) $web_ai_url_mode) : '';

            $societyQ = $d->selectRow(
                'society_id, society_name, city_name, sub_domain, web_ai_status, web_ai_credentials_id',
                'society_master',
                "society_id='$society_id'"
            );
            if (!$societyQ || mysqli_num_rows($societyQ) === 0) {
                respondCompanyAction(' - Company not found', '201');
            }
            $society = mysqli_fetch_assoc($societyQ);
            $company_label = trim(($society['society_name'] ?? '') . '-' . ($society['city_name'] ?? ''), '-');
            $oldStatus = (int) ($society['web_ai_status'] ?? 0);
            $existingCredId = (int) ($society['web_ai_credentials_id'] ?? 0);
            $baseUrl = '';
            $credId = null;
            $credLabel = '';

            if ($web_ai_status === 1) {
                if (!in_array($web_ai_url_mode, array('fill_blank', 'replace'), true)) {
                    $web_ai_url_mode = 'fill_blank';
                }
                if ($web_ai_credentials_id <= 0) {
                    respondCompanyAction(' - Please select a Web AI credential', '201');
                }
                $credQ = $d->selectRow(
                    'web_ai_credentials_id, credential_name, ai_base_url, credential_type, ai_status',
                    'web_ai_credentials_master',
                    "web_ai_credentials_id='$web_ai_credentials_id' AND ai_status='1'"
                );
                if (!$credQ || mysqli_num_rows($credQ) === 0) {
                    respondCompanyAction(' - Invalid Web AI credential', '201');
                }
                $cred = mysqli_fetch_assoc($credQ);
                $baseUrl = rtrim((string) ($cred['ai_base_url'] ?? ''), '/') . '/';
                $credId = (int) $cred['web_ai_credentials_id'];
                $credLabel = (string) ($cred['credential_name'] ?? '');
            } else {
                if (!in_array($web_ai_url_mode, array('keep', 'clear'), true)) {
                    $web_ai_url_mode = 'keep';
                }
            }

            $sync = $d->callCompanyApiEnc(
                $society['sub_domain'],
                'buildingChangePlanController.php',
                array(
                    'society_id' => $society_id,
                    'updateWebAiSetting' => 'updateWebAiSetting',
                    'web_ai_status' => $web_ai_status,
                    'web_ai_base_url' => $baseUrl,
                    'web_ai_url_mode' => $web_ai_url_mode,
                )
            );
            if (!is_array($sync) || (string) ($sync['status'] ?? '') !== '200') {
                $syncMsg = is_array($sync) ? trim((string) ($sync['message'] ?? '')) : '';
                $failMsg = $syncMsg !== '' ? $syncMsg : 'Unable to sync Web AI setting';
                $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Web AI bulk sync failed for $company_label: $failMsg", 8);
                respondCompanyAction(' - ' . $failMsg, '201');
            }

            $oldStatusText = $oldStatus === 1 ? 'On' : 'Off';
            $newStatusText = $web_ai_status === 1 ? 'On' : 'Off';

            if ($web_ai_status === 1) {
                $d->update(
                    'society_master',
                    array(
                        'web_ai_status' => 1,
                        'web_ai_credentials_id' => $credId,
                    ),
                    "society_id='$society_id'",
                    1
                );
                $logMsg = "Web AI Enabled (bulk). Status: $oldStatusText → $newStatusText, Mode: $web_ai_url_mode, Credential: $credLabel (ID $credId), URL: " . rtrim($baseUrl, '/');
                $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", $logMsg, 8);
                respondCompanyAction(' - Web AI Enabled', '200');
            } else {
                $masterUpdate = array('web_ai_status' => 0);
                if ($web_ai_url_mode === 'clear') {
                    $masterUpdate['web_ai_credentials_id'] = null;
                }
                $d->update('society_master', $masterUpdate, "society_id='$society_id'", 1);
                $credNote = ($web_ai_url_mode === 'clear')
                    ? ('cleared credential' . ($existingCredId > 0 ? " (was ID $existingCredId)" : ''))
                    : ('kept credential' . ($existingCredId > 0 ? " (ID $existingCredId)" : ' (none)'));
                $logMsg = "Web AI Disabled (bulk). Status: $oldStatusText → $newStatusText, Mode: $web_ai_url_mode, $credNote";
                $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", $logMsg, 8);
                respondCompanyAction(' - Web AI Disabled', '200');
            }
        }
        if (isset($getDataType) && $getDataType == '26') {
            $society_id = (int) $society_id_post;
            $ai_status = isset($ai_status) ? ((int) $ai_status === 1 ? 1 : 0) : 0;
            $ai_credentials_id = isset($ai_credentials_id) ? (int) $ai_credentials_id : 0;
            $mobile_ai_mode = isset($mobile_ai_mode) ? trim((string) $mobile_ai_mode) : '';

            $societyQ = $d->selectRow(
                'society_id, society_name, city_name, ai_status, ai_credentials_id',
                'society_master',
                "society_id='$society_id'"
            );
            if (!$societyQ || mysqli_num_rows($societyQ) === 0) {
                respondCompanyAction(' - Company not found', '201');
            }
            $society = mysqli_fetch_assoc($societyQ);
            $company_label = trim(($society['society_name'] ?? '') . '-' . ($society['city_name'] ?? ''), '-');
            $oldStatus = (int) ($society['ai_status'] ?? 0);
            $existingCredId = (int) ($society['ai_credentials_id'] ?? 0);
            $credId = null;
            $credLabel = '';
            $oldStatusText = $oldStatus === 1 ? 'On' : 'Off';

            if ($ai_status === 1) {
                if (!in_array($mobile_ai_mode, array('fill_blank', 'replace'), true)) {
                    $mobile_ai_mode = 'fill_blank';
                }
                if ($mobile_ai_mode === 'fill_blank' && $existingCredId > 0) {
                    $d->update(
                        'society_master',
                        array('ai_status' => 1),
                        "society_id='$society_id'",
                        1
                    );
                    $logMsg = "Mobile AI Enabled (bulk/keep existing). Status: $oldStatusText → On, Credential ID kept: $existingCredId";
                    $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", $logMsg, 8);
                    respondCompanyAction(' - Mobile AI Enabled', '200');
                }
                if ($ai_credentials_id <= 0) {
                    respondCompanyAction(' - Please select a Mobile AI credential', '201');
                }
                $credQ = $d->selectRow(
                    'ai_credentials_id, ai_base_url, ai_model, debug_key, ai_status',
                    'ai_credentials_master',
                    "ai_credentials_id='$ai_credentials_id' AND ai_status='1'"
                );
                if (!$credQ || mysqli_num_rows($credQ) === 0) {
                    respondCompanyAction(' - Invalid Mobile AI credential', '201');
                }
                $cred = mysqli_fetch_assoc($credQ);
                $credId = (int) $cred['ai_credentials_id'];
                $credUrl = rtrim(trim((string) ($cred['ai_base_url'] ?? '')), '/');
                $credLabel = trim(($cred['ai_model'] ?? '') . ' / ' . (((int) ($cred['debug_key'] ?? 0) === 1) ? 'Debug' : 'Live'));

                $d->update(
                    'society_master',
                    array(
                        'ai_status' => 1,
                        'ai_credentials_id' => $credId,
                    ),
                    "society_id='$society_id'",
                    1
                );
                $prevCredNote = $existingCredId > 0 ? " (previous credential ID $existingCredId)" : '';
                $logMsg = "Mobile AI Enabled (bulk/$mobile_ai_mode). Status: $oldStatusText → On, Credential: $credLabel (ID $credId)$prevCredNote, URL: $credUrl";
                $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", $logMsg, 8);
                respondCompanyAction(' - Mobile AI Enabled', '200');
            } else {
                if (!in_array($mobile_ai_mode, array('keep', 'clear'), true)) {
                    $mobile_ai_mode = 'keep';
                }
                $masterUpdate = array('ai_status' => 0);
                if ($mobile_ai_mode === 'clear') {
                    $masterUpdate['ai_credentials_id'] = null;
                }
                $d->update('society_master', $masterUpdate, "society_id='$society_id'", 1);
                $credNote = ($mobile_ai_mode === 'clear')
                    ? ('cleared credential' . ($existingCredId > 0 ? " (was ID $existingCredId)" : ''))
                    : ('kept credential' . ($existingCredId > 0 ? " (ID $existingCredId)" : ' (none)'));
                $logMsg = "Mobile AI Disabled (bulk). Status: $oldStatusText → Off, Mode: $mobile_ai_mode, $credNote";
                $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", $logMsg, 8);
                respondCompanyAction(' - Mobile AI Disabled', '200');
            }
        }
        $today = date("Y-m-d");
        $post_log_master = $d->select("society_analytics_master", " city_id = '$city_id'  AND status=200 AND society_id  ='$society_id_post' AND update_date='$today'");
        $success_array = array();
        while ($post_log_master_data = mysqli_fetch_array($post_log_master)) {
            array_push($success_array, $post_log_master_data['society_id']);
        }
        $ids = join("','", $success_array);
        $society_master_qry = $d->select("society_master", " society_id  ='$society_id_post' AND society_id NOT IN ('$ids') ");
        $societiesToProcess = array();
        while ($society_master_row = mysqli_fetch_array($society_master_qry)) {
            $societiesToProcess[] = $society_master_row;
        }
        $prefetchSocietyIds = array();
        foreach ($societiesToProcess as $_sm) {
            $prefetchSocietyIds[] = (int)$_sm['society_id'];
        }
        $prefetchSocietyIdsIn = !empty($prefetchSocietyIds) ? implode(',', $prefetchSocietyIds) : '0';

        $societyAnalyticsUsersMap = array();
        if (isset($getDataType) && $getDataType == '7' && !empty($prefetchSocietyIds)) {
            $analiticsPrefetch = $d->selectRow("society_id,total_users,active_tracking_users", "society_analytics_master", "society_id IN ($prefetchSocietyIdsIn)");
            while ($analiticsPrefetchRow = mysqli_fetch_assoc($analiticsPrefetch)) {
                $societyAnalyticsUsersMap[(int)$analiticsPrefetchRow['society_id']] = $analiticsPrefetchRow;
            }
        }

        $bucket_access_key = $bucket_secret_access_key = $bucket_region = $bucket_name = $bucket_service = $master_bucket_url = null;
        if (isset($getDataType) && $getDataType == '8') {
            $bucket_configuration_qry = $d->select("bucket_configuration", "1", "LIMIT 1");
            if (mysqli_num_rows($bucket_configuration_qry) > 0) {
                $bucket_configuration_data = mysqli_fetch_array($bucket_configuration_qry);
                $bucket_access_key = $bucket_configuration_data['bucket_access_key'];
                $bucket_secret_access_key = $bucket_configuration_data['bucket_secret_access_key'];
                $bucket_region = $bucket_configuration_data['bucket_region'];
                $bucket_name = $bucket_configuration_data['bucket_name'];
                $bucket_service = $bucket_configuration_data['bucket_service'];
                $master_bucket_url = $bucket_configuration_data['master_bucket_url'];
            }
        }

        $configuration_sender_email_id = $configuration_email_password = $configuration_email_smtp = $configuration_email_port = null;
        $configuration_sender_name = $configuration_smtp_type = $configuration_client_id = $configuration_client_secret = null;
        $configuration_refresh_token = $configuration_redirect_url = null;
        if (isset($getDataType) && $getDataType == '10') {
            $email_configuration_qry = $d->select("email_configuration", "1", "LIMIT 1");
            if (mysqli_num_rows($email_configuration_qry) > 0) {
                $email_configuration_data = mysqli_fetch_array($email_configuration_qry);
                $configuration_sender_email_id = $email_configuration_data['sender_email_id'];
                $configuration_email_password = $email_configuration_data['email_password'];
                $configuration_email_smtp = $email_configuration_data['email_smtp'];
                $configuration_email_port = $email_configuration_data['email_port'];
                $configuration_sender_name = $email_configuration_data['sender_name'];
                $configuration_smtp_type = $email_configuration_data['smtp_type'];
                $configuration_client_id = $email_configuration_data['client_id'];
                $configuration_client_secret = $email_configuration_data['client_secret'];
                $configuration_refresh_token = $email_configuration_data['refresh_token'];
                $configuration_redirect_url = $email_configuration_data['redirect_url'];
            }
        }

        $existingSocietyAnalyticsMap = array();
        if (isset($getDataType) && $getDataType == '6' && !empty($prefetchSocietyIds)) {
            $existingAnalyticsQ = $d->select("society_analytics_master", "society_id IN ($prefetchSocietyIdsIn)");
            while ($existingAnalyticsRow = mysqli_fetch_assoc($existingAnalyticsQ)) {
                $existingSocietyAnalyticsMap[(int)$existingAnalyticsRow['society_id']] = true;
            }
        }

        $whatsappAccessBySociety = array();
        if (isset($getDataType) && $getDataType == '9' && !empty($prefetchSocietyIds)) {
            $whatsappPrefetch = $d->select("whatsapp_access_master", "society_id IN ($prefetchSocietyIdsIn)");
            while ($whatsappPrefetchRow = mysqli_fetch_assoc($whatsappPrefetch)) {
                $sid = (int)$whatsappPrefetchRow['society_id'];
                if (!isset($whatsappAccessBySociety[$sid])) {
                    $whatsappAccessBySociety[$sid] = array();
                }
                $whatsappAccessBySociety[$sid][$whatsappPrefetchRow['user_mobile']] = $whatsappPrefetchRow;
            }
        }

        foreach ($societiesToProcess as $society_master_data) {


            $society_id = $society_master_data['society_id'];
            $target_url = $society_master_data['sub_domain'] . "residentApiNew/societyAnalytics.php";
            if (isset($getDataType) && $getDataType == '7') {
                if (isset($societyAnalyticsUsersMap[(int)$society_id])) {
                    $analiticsdata = $societyAnalyticsUsersMap[(int)$society_id];
                    $current_total_users = $analiticsdata['total_users'];
                    $current_active_tracking_users = $analiticsdata['active_tracking_users'];
                } else {
                    $current_total_users = "0";
                    $current_active_tracking_users = "0";
                }
                $employee_registration_limit = $society_master_data['employee_registration_limit'];
                $employee_tracking_limit = $society_master_data['employee_tracking_limit'];
                if (($employee_tracking_limit == "" || $employee_tracking_limit == 0)) {
                    $employee_tracking_limit = $current_active_tracking_users;
                }
                if (($employee_tracking_limit < $current_active_tracking_users)) {
                    $employee_tracking_limit = $current_active_tracking_users;
                }
                if ($employee_registration_limit == "" || $employee_registration_limit == 0) {
                    $employee_registration_limit = ceil($current_total_users * 1.3);
                }
                if ($employee_registration_limit < $current_total_users) {
                    $employee_registration_limit = $current_total_users;
                }
                $tracking_status = $society_master_data['tracking_status'];
                $Society_data['tracking_status'] = 1;
                $Society_data['employee_tracking_limit'] = $employee_tracking_limit;
                $Society_data['employee_registration_limit'] = $employee_registration_limit;
                $d->update("society_master", $Society_data, "society_id  ='$society_id'");
            }
            $skipCurl = false;
            $json = null;
            if (isset($getDataType) && $getDataType == '7') {
                $post = array(
                    'changeTrackingLimit' => 'changeTrackingLimit',
                    'society_id' => $society_id,
                    'language_id' => 1,
                    'employee_tracking_limit' => $employee_tracking_limit,
                    'employee_registration_limit' => $employee_registration_limit,
                );
                $json = $d->callCompanyApiEnc($society_master_data['sub_domain'], 'buildingChangePlanController.php', $post);

                $skipCurl = true;
            }
            if (isset($getDataType) && $getDataType == '11') {
                $post = array(
                    'getEmployees' => 'getEmployees',
                    'society_id' => $society_id,
                    'language_id' => 1,
                );
                $json = $d->callCompanyApiEnc($society_master_data['sub_domain'], 'employeeController.php', $post);

                $skipCurl = true;
            }
            if (!$skipCurl) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $target_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            if (isset($getDataType) && $getDataType == '1') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, "deleteDuplicateAttendance=deleteDuplicateAttendance&society_id=$society_id&language_id=1&startDate=2024-01-01&endDate=2024-09-11");
            } elseif (isset($getDataType) && $getDataType == '2') {
                $distance_get_type = 3;
                curl_setopt($ch, CURLOPT_POSTFIELDS, "updateTravelMode=updateTravelMode&society_id=$society_id&language_id=1&show_visit_distance_or_normal_distance=$distance_get_type&trackChange=$trackChange");
            } elseif (isset($getDataType) && $getDataType == '3') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, "buildingDetails=buildingDetails&society_id=$society_id&language_id=1");
            } elseif (isset($getDataType) && $getDataType == '4') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, "deleteOldAttendanceData=deleteOldAttendanceData&society_id=$society_id&language_id=1");
            } elseif (isset($getDataType) && $getDataType == '5') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, "updateEmailAndWhatsAppConfiguration=updateEmailAndWhatsAppConfiguration&society_id=$society_id&language_id=1&whatsAppConfiguration=1&configuration_id=2000227309&configuration_password=ehwe5us9&provider_name=GupShup");
            } elseif (isset($getDataType) && $getDataType == '10') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, "updateEmailAndWhatsAppConfiguration=updateEmailAndWhatsAppConfiguration&society_id=$society_id&language_id=1&emailConfiguration=1&configuration_sender_email_id=$configuration_sender_email_id&configuration_email_password=$configuration_email_password&configuration_email_smtp=$configuration_email_smtp&configuration_email_port=$configuration_email_port&configuration_sender_name=$configuration_sender_name&configuration_smtp_type=$configuration_smtp_type&configuration_client_id=$configuration_client_id&configuration_client_secret=$configuration_client_secret&configuration_refresh_token=$configuration_refresh_token&configuration_redirect_url=$configuration_redirect_url");
            } elseif (isset($getDataType) && $getDataType == '6') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, "getStorageData=getStorageData&society_id=$society_id&language_id=1");
            } elseif (isset($getDataType) && $getDataType == '8') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, "updateBucketDetails=updateBucketDetails&society_id=$society_id&language_id=1&bucket_access_key=$bucket_access_key&bucket_secret_access_key=$bucket_secret_access_key&bucket_region=$bucket_region&bucket_name=$bucket_name&bucket_service=$bucket_service&master_bucket_url=$master_bucket_url");
            } elseif (isset($getDataType) && $getDataType == '9') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, "getWhatsappAlerts=getWhatsappAlerts&society_id=$society_id&language_id=1");
            } elseif (isset($getDataType) && $getDataType == '12') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, "cleanupGpsJsonFiles=cleanupGpsJsonFiles&society_id=$society_id&language_id=1");
            } elseif (isset($getDataType) && $getDataType == '14') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, "resetCommonAdmonSession=resetCommonAdmonSession&society_id=$society_id&language_id=1");
            } elseif (isset($getDataType) && in_array($getDataType, array(
                'deleteTracking',
                'deleteVisitEnd',
                'deleteWorkReport',
                'deleteExpense',
                'deleteTask',
                'deleteCircular',
                'deleteDiscussion',
                'deleteMeeting',
                'deleteAttendance',
                'deleteChat',
            ), true)) {
                // societyAnalytics.php createAndSendAttachmentBackup — timePeriod must match company society_settings
                // use deleteOldAttachments if want to delete without sending email
                $deleteActionPeriodMap = array(
                    'deleteAttendance' => 'face_attendance_delete_days',
                    'deleteWorkReport' => 'work_report_delete_days',
                    'deleteVisitEnd' => 'visit_attachment_delete_days',
                    'deleteExpense' => 'expense_attachment_delete_days',
                    'deleteChat' => 'chat_attachment_delete_days',
                    'deleteTask' => 'task_attachment_delete_days',
                    'deleteCircular' => 'circular_attachment_delete_days',
                    'deleteDiscussion' => 'discussion_attachment_delete_days',
                    'deleteMeeting' => 'meeting_attachment_delete_days',
                    'deleteTracking' => 'tracking_attachment_delete_months',
                );
                $timePeriod = '';
                $settingsKey = $deleteActionPeriodMap[$getDataType] ?? '';
                $societySettingsRaw = $society_master_data['society_settings'] ?? '';
                if ($settingsKey !== '' && $societySettingsRaw !== '') {
                    $societySettings = json_decode($societySettingsRaw, true);
                    if (is_array($societySettings) && isset($societySettings[$settingsKey]) && $societySettings[$settingsKey] !== '') {
                        $timePeriod = $societySettings[$settingsKey];
                    }
                }
                if ($timePeriod === '') {
                    respondCompanyAction(' - Delete period not configured', '201');
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, "createAndSendAttachmentBackup=createAndSendAttachmentBackup&society_id=$society_id&language_id=1&deleteAction=$getDataType&timePeriod=$timePeriod");
            } elseif (isset($getDataType) && $getDataType !== '') {
                $actionValueEsc = $d->escapeSqlString($getDataType);
                $dynActionQ = $d->selectRow("action_id", "company_action_master", "`value`='$actionValueEsc'");
                if ($dynActionQ && mysqli_num_rows($dynActionQ) > 0) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $getDataType . '=' . $getDataType . '&society_id=' . $society_id . '&language_id=1');
                } else {
                    respondCompanyAction(' - Invalid Request', '201');
                }
            } else {
                respondCompanyAction(' - Invalid Request', '201');
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'key: ' . $keydb
            ));
            $result = curl_exec($ch);
            curl_close($ch);
            $json = json_decode($result, true);
            }
            $result2 = $json["message"];
            $result3 = $json["status"];
            if (isset($json) && is_array($json) && count($json) > 0) {
                if (isset($getDataType) && $getDataType == '3') {
                    require_once "../companyDataCommon.php";
                } else if (isset($getDataType) && $getDataType == '6') {
                    $storage_data = $json["storage_data"];
                    $a1 = array(
                        "society_id" => $society_id,
                        "storage_data" => json_encode($storage_data),
                    );
                    if (isset($existingSocietyAnalyticsMap[(int)$society_id])) {
                        $q = $d->update("society_analytics_master", $a1, "society_id='$society_id'");
                    } else {
                        $q = $d->insert("society_analytics_master", $a1);
                        $existingSocietyAnalyticsMap[(int)$society_id] = true;
                    }
                } else if (isset($getDataType) && $getDataType == '9') {
                    $whatsapp_data = isset($json["whatsapp"]) && is_array($json["whatsapp"]) ? $json["whatsapp"] : [];
                    $existing = isset($whatsappAccessBySociety[(int)$society_id]) ? $whatsappAccessBySociety[(int)$society_id] : [];
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
                    $whatsappAccessBySociety[(int)$society_id] = $incoming;
                    $d->update("society_master", ["whatsapp_access_last_sync_date" => date('Y-m-d H:i:s')], "society_id='$society_id'");
                    $d->syncWhatsAppAnalyticsCronFromAccess($society_id);
                } elseif (isset($getDataType) && $getDataType == '11') {
                    $employees = isset($json['employees']) && is_array($json['employees']) ? $json['employees'] : [];
                    $d->delete('society_users_master', "society_id='$society_id'");
                    $created_by = isset($bms_admin_id) ? $bms_admin_id : null;
                    $created_date = date('Y-m-d H:i:s');
                    foreach ($employees as $emp) {
                        $name = isset($emp['user_full_name']) ? $emp['user_full_name'] : '';
                        $phone = isset($emp['user_mobile']) ? $emp['user_mobile'] : '';
                        $designationVal = isset($emp['designation']) ? $emp['designation'] : '';
                        $branchVal = isset($emp['branch_name']) ? $emp['branch_name'] : '';
                        $deptVal = isset($emp['department_name']) ? $emp['department_name'] : '';
                        $row = array(
                            'society_id' => $society_id,
                            'society_user_name' => $name !== '' ? $d->encryptDecrypt('encrypt', $name) : '',
                            'society_user_phone' => $phone !== '' ? $d->encryptDecrypt('encrypt', $phone) : '',
                            'designation' => $designationVal !== '' ? $d->encryptDecrypt('encrypt', $designationVal) : '',
                            'branch_name' => $branchVal !== '' ? $d->encryptDecrypt('encrypt', $branchVal) : '',
                            'department_name' => $deptVal !== '' ? $d->encryptDecrypt('encrypt', $deptVal) : '',
                            'created_by' => $created_by,
                            'created_date' => $created_date,
                        );
                        $d->insert('society_users_master', $row);
                    }
                }
            }
            if ($result2 == "" && $result3 != "200") {
                respondCompanyAction(' - No Response', '201');
            } else {
                $json["message"] = ($json["message"] != "") ? $json["message"] : "Success";
                respondCompanyAction($json["message"], $json["status"]);
            }
        }
    } else {
        respondCompanyAction(' - Invalid Request', '201');
    }
} else {
    respondCompanyAction(' - Invalid Request', '201');
}
