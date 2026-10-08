<?php
include '../common/objectController.php';
extract($_POST);
$feedbackListLocation = '../' . $d->feedbackListRedirectPath('feedback');
if (isset($_POST) && !empty($_POST)) {
    $finSupportDir = '../../img/fin_support/';
    $feedbackImageExt = ['jpeg', 'jpg', 'png', 'gif'];
    $feedbackVideoExt = ['mp4', 'webm', 'avi', 'wmv'];
    $feedbackDocExt = ['csv', 'docx'];
    $feedbackReplyExt = array_merge($feedbackImageExt, $feedbackVideoExt, $feedbackDocExt);
    $maxsize = 10097152;
    $maxsizeVideo = 30097152;

    if (isset($replyFeedback)) {
        $q = $d->selectRow(
            "feedback_master.*,bms_admin_master.*,feedback_master.society_id AS ticket_society_id,COALESCE(wl.sub_domain, society_master.sub_domain) AS sub_domain",
            "feedback_master
LEFT JOIN bms_admin_master ON feedback_master.created_by = bms_admin_master.admin_id
LEFT JOIN society_master ON society_master.society_id = feedback_master.society_id AND COALESCE(feedback_master.is_whitelabel, 0) = 0
LEFT JOIN society_master_white_label wl ON wl.society_id = feedback_master.society_id AND wl.project_type = feedback_master.whitelabel_type AND COALESCE(feedback_master.is_whitelabel, 0) = 1",
            "feedback_master.feedback_id='$feedback_id'",
            ""
        );
        $query_data = mysqli_fetch_array($q);
        $to = $d->encryptDecrypt("decrypt", $query_data['admin_email']);
        $client_name =  $query_data['name'];
        $user_mobile =  $query_data['mobile'];
        $ticket_society_id =  $query_data['ticket_society_id'];
        $society_id =  $query_data['ticket_society_id'];
        $user_name =  $query_data['admin_name'];
        $feedback_society_id =  $query_data['society_id'];
        $subject = "" . $d->app_name() . " Support Ticket Reply (#TKT$feedback_id) - $query_data[subject]";
        $reply = $reply;
        $feedback_msg = "Query : " . $query_data['feedback_msg'];
        $agent_mobile = $query_data['mobile'];
        $feedback_status_old = $query_data['feedback_status'];
        $feedback_created_by = $query_data['admin_id'];
        $upload = $d->saveValidatedUpload($_FILES['attachment'] ?? [], $finSupportDir, 'Support_1', $feedbackReplyExt, $maxsize);
        if (!$upload['ok']) {
            $_SESSION['msg1'] = $upload['error'];
            header("Location: " . $feedbackListLocation);
            exit();
        }
        $attachment = $upload['filename'];
        $attachmentFile = $attachment !== '' ? ($base_url . 'img/fin_support/' . $attachment) : '';
        $a = array(
            'client_reply_message' => $reply,
        );
        if ($feedback_status != "") {
            $a['feedback_status'] = $feedback_status;
        } else {
            $a['feedback_status'] = $feedback_status_old;
        }

        $q = $d->update("feedback_master", $a, "feedback_id='$feedback_id'");
        if ($reply != '' && $to != "") {
            include '../mail/feedbackReply.php';
            include '../mail.php';
        }
        if (isset($send_msg) && $reply != '') {
            $msg = "Ticket #TKT$feedback_id\n" . $reply;
            $send_txt = 1;
        } else {
            $send_txt = 0;
        }
        $m->set_data('feedback_id', $feedback_id);
        $m->set_data('society_id', $society_id);
        $m->set_data('feedback_log', $reply);
        $m->set_data('feedback_log_status', $feedback_status);
        $m->set_data('feedback_msg_status', $send_txt);
        $m->set_data('feedback_log_attachment', $attachment);
        $m->set_data('feedback_log_date', date('Y-m-d H:i:s'));
        $m->set_data('feedback_added_by', $bms_admin_id);

        $a1  = array(
            'feedback_id' => $m->get_data('feedback_id'),
            'society_id' => $m->get_data('society_id'),
            'feedback_log' => $m->get_data('feedback_log'),
            'feedback_msg_status' => $m->get_data('feedback_msg_status'),
            'feedback_log_attachment' => $m->get_data('feedback_log_attachment'),
            'feedback_log_date' => $m->get_data('feedback_log_date'),
            'feedback_added_by' => $m->get_data('feedback_added_by'),
        );
        if ($feedback_status != "") {
            $a1['feedback_log_status'] = $feedback_status;
        } else {
            $a1['feedback_log_status'] = '4';
        }
        if (isset($hideToUser) && $hideToUser == 'on') {
            $a1['hide_to_user'] = '1';
        } else {
            $a1['hide_to_user'] = '0';
            $ticket_id = $feedback_id;
            $reply_message = $reply;
            $feedback_status = $feedback_status;
            $user_mobile_no = $agent_mobile;
            include '../feedbackFcmCurl.php';
        }
        $d->insert("feedback_log_master", $a1);

        $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Feedback #TKT$feedback_id Reply", "6");

        if ($feedback_created_by != $bms_admin_id) {
            $notiAry = array(
                'admin_id' => $feedback_created_by,
                'society_id' => $society_id,
                'notification_tittle' => "Reply for Feedback - #TKT$feedback_id",
                'notification_description' => $reply,
                'notifiaction_date' => date('Y-m-d H:i'),
                'admin_click_action' => 'feedbackTimeline?id=' . $feedback_id,
            );
            $d->insert("admin_notification", $notiAry);
        }
        $hit_url = $query_data['sub_domain'] ?? '';
        $noti_title = "Reply for Feedback - #TKT$feedback_id";
        $noti_description = $reply;
        if (!(isset($hideToUser) && $hideToUser == 'on')) {
            $post = array(
                'send_notification' => 'send_notification_ticket',
                'title' => $noti_title,
                'description' => $noti_description,
                'society_id' => $ticket_society_id,
                'mobile_no' => $user_mobile,
                'feedback_id' => $feedback_id,
            );
            $d->callCompanyApiEnc($hit_url, 'sendNotificationCurlController.php', $post);

        }
        if ($feedback_created_by != $bms_admin_id) {
            $has_permission = $d->count_data_direct("fcm_id", "admin_fcm_notification_master", "bms_admin_id='$feedback_created_by' AND fcm_notifications='1' AND active_status='0'");
            if ($feedback_created_by > 0 && $has_permission > 0) {
                $noti_title = "" . $d->app_name() . " #TKT$feedback_id new reply";
                $noti_description = $reply;
                $click_action = "feedbackTimeline?id=$feedback_id";
                $getTokens = $d->getWebFcm("web_fcm_master", "admin_id='$feedback_created_by'");
                $nAdmin->sendAdminNotification($getTokens, $noti_title, $noti_description, $click_action);
            }
        }
        $_SESSION['msg'] = "Reply Sent Successfully to '$to'..!";
        header("Location: ../" . $d->feedbackListRedirectPath($previousURL ?? '', 'feedback'));
    } else if (isset($deleteFeedback)) {
        $q = $d->delete("feedback_master", "feedback_id='$feedback_id'");
        if ($q == TRUE) {
            $_SESSION['msg'] = "Feedback Deleted";
            $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Feedback #TKT$feedback_id Deleted", "6");
            header("Location: " . $feedbackListLocation);
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("Location: " . $feedbackListLocation);
        }
    } else if (isset($isTicket)) {
        $q = $d->select("feedback_master LEFT JOIN bms_admin_master ON  feedback_master.created_by =bms_admin_master.admin_id", "feedback_master.feedback_id='$feedback_id'", "");
        $agentDetails = mysqli_fetch_array($q);
        $isTicketGenerated = $agentDetails['isTicket'];
        if ($isTicketGenerated != "1") {
            $a = array(
                'isTicket' => 1,
            );

            $q = $d->update("feedback_master", $a, "feedback_id='$feedback_id'");
            $user_name = $agentDetails['name'];
            $to = $agentDetails['email'];
            $agent_mobile = $agentDetails['mobile'];
            $subject = $agentDetails['subject'];
            $feedback_id = $agentDetails['feedback_id'];
            $feedback_msg = $agentDetails['feedback_msg'];
            $created_date = $agentDetails['feedback_date_time'];
            $ticketNumber = "#TKT$feedback_id";

            $m->set_data('feedback_id', $feedback_id);
            $m->set_data('society_id', $society_id);
            $m->set_data('feedback_log', "Ticket Generated for #TKT$feedback_id");
            $m->set_data('feedback_log_msg', $msg);
            $m->set_data('feedback_log_date', date('Y-m-d H:i:s'));
            $m->set_data('feedback_added_by', $bms_admin_id);

            $a1  = array(
                'feedback_id' => $m->get_data('feedback_id'),
                'society_id' => $m->get_data('society_id'),
                'feedback_log' => $m->get_data('feedback_log'),
                'feedback_log_msg' => $m->get_data('feedback_log_msg'),
                'feedback_msg_status' => 1,
                'feedback_log_date' => $m->get_data('feedback_log_date'),
                'feedback_added_by' => $m->get_data('feedback_added_by'),
            );
            $d->insert("feedback_log_master", $a1);

            if ($q == TRUE) {
                $cc = $d->encryptDecrypt("decrypt", $agentDetails['admin_email']);
                $subject = "" . $d->app_name() . " Support Ticket Opened - $ticketNumber";
                if (isset($feedback_msg) && $to != '') {
                    include '../mail/ticketCreate.php';
                    include '../mail.php';
                }
                $_SESSION['msg'] = "Ticket Generated";
                $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Feedback #TKT$feedback_id Ticket Generated", "6");
                header("Location: ../feedbackTimeline?id=$feedback_id" . ($d->feedbackListQueryString() !== '' ? '&' . $d->feedbackListQueryString() : ''));
            } else {
                $_SESSION['msg1'] = "Something Wrong";
                header("Location: ../feedbackTimeline?id=$feedback_id" . ($d->feedbackListQueryString() !== '' ? '&' . $d->feedbackListQueryString() : ''));
            }
        } else {
            $_SESSION['msg1'] = "Ticket already generated.";
            header("Location: ../feedbackTimeline?id=$feedback_id" . ($d->feedbackListQueryString() !== '' ? '&' . $d->feedbackListQueryString() : ''));
        }
    } else if (isset($forwardtoDeveloper)) {
        $m->set_data('forwarded_by', $bms_admin_id);
        $a = array(
            'forwarded_by' => $m->get_data('forwarded_by'),
            'develeoper_assign_time' => date('Y-m-d H:i:s'),
            'module_type' => $module_type,
            'isTicket' => 2,
            'with_developer' => 1,
        );
        $q = $d->update("feedback_master", $a, "feedback_id='$feedback_id'");

        $qf = $d->select("feedback_master", "feedback_id='$feedback_id'");
        $agentDetails = mysqli_fetch_array($qf);
        extract($agentDetails);
        $user_name = $agentDetails['name'];
        $agent_name = $agentDetails['name'];
        $to = $agentDetails['email'];
        $agent_mobile = $agentDetails['mobile'];
        $subject = $agentDetails['subject'];
        $feedback_msg = $agentDetails['feedback_msg'];
        $feedback_id = $agentDetails['feedback_id'];
        $platform = $agentDetails['platform'];
        $file1 = $file2 = $file3 = $file4 = "";
        if ($attachment != "") {
            $file1 = $base_url . "img/fin_support/" . $attachment;
        }
        if ($attachment_2 != "") {
            $file2 = $base_url . "img/fin_support/" . $attachment_2;
        }
        if ($video != "") {
            $file3 = $base_url . "img/fin_support/" . $video;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $d->support_url() . 'taskController.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('add_feedback' => 'add_feedback', 'feedback_id' => $feedback_id, 'bug_platform' => $platform, 'title' => $subject, 'description' => $feedback_msg, 'bug_screen' => 'App Support Page', 'bug_app' => 'mycompany', 'bug_version' => 'Production', 'file1' => $file1, 'file2' => $file2, 'file3' => $file3, 'file4' => $file4),
            CURLOPT_HTTPHEADER => array(),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $m->set_data('feedback_id', $feedback_id);
        $m->set_data('society_id', $society_id);
        $m->set_data('feedback_log', "Query #TKT$feedback_id forwarded to Developer");
        $m->set_data('feedback_log_date', date('Y-m-d H:i:s'));
        $m->set_data('feedback_added_by', $bms_admin_id);

        $a1  = array(
            'feedback_id' => $m->get_data('feedback_id'),
            'society_id' => $m->get_data('society_id'),
            'feedback_log' => $m->get_data('feedback_log'),
            'feedback_msg_status' => 0,
            'feedback_log_date' => $m->get_data('feedback_log_date'),
            'feedback_added_by' => $m->get_data('feedback_added_by'),
        );
        $d->insert("feedback_log_master", $a1);

        if ($q == TRUE) {
            $data = $d->selectRow("bms_admin_master.admin_id,count(fcm_id) AS has_permission", "bms_admin_master LEFT JOIN admin_fcm_notification_master ON admin_fcm_notification_master.bms_admin_id=bms_admin_master.admin_id AND admin_fcm_notification_master.fcm_notifications='6' AND admin_fcm_notification_master.active_status='0'", "bms_admin_master.is_developer='1' AND FIND_IN_SET($platform,platform)", "GROUP BY bms_admin_master.admin_id");
            while ($row = mysqli_fetch_array($data)) {
                $feedback_created_by = $row['admin_id'];
                $has_permission = $row['has_permission'];
                if (($feedback_created_by != $bms_admin_id) && ($feedback_created_by > 0) && ($has_permission > 0)) {
                    $devnotiAry = array(
                        'admin_id' => $feedback_created_by,
                        'society_id' => $society_id,
                        'notification_tittle' => "" . $d->app_name() . " #TKT$feedback_id forwarded to Developer",
                        'notifiaction_date' => date('Y-m-d H:i'),
                        'admin_click_action' => 'feedbackTimeline?id=' . $feedback_id,
                    );
                    $d->insert("admin_notification", $devnotiAry);
                    $noti_title = "" . $d->app_name() . " #TKT$feedback_id forwarded to Developer";
                    $noti_description = "Please close query from your end now.";
                    $click_action = "feedbackTimeline?id=$feedback_id";
                    $getTokens = $d->getWebFcm("web_fcm_master", "admin_id='$feedback_created_by'");
                    $nAdmin->sendAdminNotification($getTokens, $noti_title, $noti_description, $click_action);
                }
            }
            $_SESSION['msg'] = "Forwarded to Developer";
            $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "" . $d->app_name() . " Ticket number #TKT$feedback_id forwarded to technical department", "6");
            header("Location: " . $feedbackListLocation);
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("Location: " . $feedbackListLocation);
        }
    } else if (isset($rejectbyDeveloper)) {
        $q = $d->selectRow("feedback_master.*,bms_admin_master.*,feedback_master.created_by AS feedback_created_by", "feedback_master LEFT JOIN bms_admin_master ON  feedback_master.created_by=bms_admin_master.admin_id", "feedback_master.feedback_id='$feedback_id'");
        $agentDetails = mysqli_fetch_array($q);
        if ($agentDetails['feedback_status'] != "6") {
            $upload = $d->saveValidatedUpload($_FILES['reject_attachment'] ?? [], $finSupportDir, 'ticket_reject', $feedbackReplyExt, $maxsize);
            if (!$upload['ok']) {
                $_SESSION['msg1'] = $upload['error'];
                header("Location: " . $feedbackListLocation);
                exit();
            }
            $reject_attachment = $upload['filename'];
            $a = array(
                'feedback_status' => 6,
                'isTicket' => 0,
                'with_developer' => 0,
                'reject_remarks' => $resion,
                'developer_solve_time' => date('Y-m-d H:i:s'),
            );
            $q = $d->update("feedback_master", $a, "feedback_id='$feedback_id'");
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $d->support_url() . 'taskController.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('feedback_id' => $feedback_id, 'reject_feedback' => 'reject_feedback', 'attahment' => $reject_attachment, 'remarks' => $resion),
                CURLOPT_HTTPHEADER => array(),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $reply = $resion;
            $q = $d->selectRow("feedback_master.*,bms_admin_master.*,feedback_master.created_by AS feedback_created_by", "feedback_master LEFT JOIN bms_admin_master ON  feedback_master.created_by=bms_admin_master.admin_id", "feedback_master.feedback_id='$feedback_id'");
            $agentDetails = mysqli_fetch_array($q);
            $to = $d->encryptDecrypt("decrypt", $agentDetails['admin_email']);
            $user_name = $agentDetails['admin_name'];
            $subject = $agentDetails['subject'];
            $feedback_msg = "Query #TKT$feedback_id rejected by Developer, $reply";

            $agent_name = $agentDetails['name'];
            $agent_mobile = $agentDetails['mobile'];
            $subject = $agentDetails['subject'];
            $feedback_id = $agentDetails['feedback_id'];
            $feedback_created_by = $agentDetails['feedback_created_by'];

            $m->set_data('feedback_id', $feedback_id);
            $m->set_data('society_id', $society_id);
            $m->set_data('feedback_log', "Query #TKT$feedback_id rejected by Developer, $reply");
            $m->set_data('feedback_log_date', date('Y-m-d H:i:s'));
            $m->set_data('feedback_added_by', $bms_admin_id);

            $a1  = array(
                'feedback_id' => $m->get_data('feedback_id'),
                'society_id' => $m->get_data('society_id'),
                'feedback_log' => $m->get_data('feedback_log'),
                'feedback_log_attachment' => $reject_attachment,
                'feedback_msg_status' => 0,
                'feedback_log_date' => $m->get_data('feedback_log_date'),
                'feedback_added_by' => $m->get_data('feedback_added_by'),
            );
            $d->insert("feedback_log_master", $a1);
            if ($q == TRUE) {
                if ($reply != '' && $to != "") {
                    include '../mail/feedbackReply.php';
                    include '../mail.php';
                }

                $has_permission = $d->count_data_direct("fcm_id", "admin_fcm_notification_master", "bms_admin_id='$feedback_created_by' AND fcm_notifications='1' AND active_status='0'");
                if ($feedback_created_by != $bms_admin_id && $feedback_created_by > 0 && $has_permission > 0) {
                    $devnotiAry = array(
                        'admin_id' => $feedback_created_by,
                        'society_id' => $society_id,
                        'notification_tittle' => "" . $d->app_name() . " #TKT$feedback_id rejected by Developer",
                        'notifiaction_date' => date('Y-m-d H:i'),
                        'admin_click_action' => 'feedbackTimeline?id=' . $feedback_id,
                    );
                    $d->insert("admin_notification", $devnotiAry);
                    $noti_title = "" . $d->app_name() . " #TKT$feedback_id rejected by Developer";
                    $noti_description = $reply;
                    $click_action = "feedbackTimeline?id=$feedback_id";
                    $getTokens = $d->getWebFcm("web_fcm_master", "admin_id='$feedback_created_by'");
                    $nAdmin->sendAdminNotification($getTokens, $noti_title, $noti_description, $click_action);
                }

                $_SESSION['msg'] = "Rejected By Developer";
                $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Query #TKT$feedback_id rejected by Developer", "6");
                header("Location: " . $feedbackListLocation);
            } else {
                $_SESSION['msg1'] = "Something Wrong";
                header("Location: " . $feedbackListLocation);
            }
        } else {
            $_SESSION['msg1'] = "Ticket already rejected";
            header("Location: " . $feedbackListLocation);
        }
    } else if (isset($closebyDeveloper)) {

        $q = $d->selectRow(
            "feedback_master.*,bms_admin_master.*,COALESCE(wl.sub_domain, society_master.sub_domain) AS sub_domain",
            "feedback_master
LEFT JOIN bms_admin_master ON feedback_master.created_by = bms_admin_master.admin_id
LEFT JOIN society_master ON society_master.society_id = feedback_master.society_id AND COALESCE(feedback_master.is_whitelabel, 0) = 0
LEFT JOIN society_master_white_label wl ON wl.society_id = feedback_master.society_id AND wl.project_type = feedback_master.whitelabel_type AND COALESCE(feedback_master.is_whitelabel, 0) = 1",
            "feedback_master.feedback_id='$feedback_id'",
            ""
        );
        $query_data = mysqli_fetch_array($q);
        if ($query_data['feedback_status'] != "5") {
            if ($send_mail == 0) {
                $to = $query_data['email'];
                $cc = $d->encryptDecrypt("decrypt", $query_data['admin_email']);
            } else {
                $to = $d->encryptDecrypt("decrypt", $query_data['admin_email']);
            }
            $user_name =  $query_data['name'];
            $created_date =  $query_data['feedback_date_time'];
            $feedback_society_id =  $query_data['society_id'];

            $ticketNumber = "#TKT$feedback_id";
            $subject = "Support Ticket Closed - #TKT$feedback_id";

            $feedback_created_by = $query_data['created_by'];

            $feedback_msg = $query_data['feedback_msg'];
            $agent_mobile = $query_data['mobile'];
            $upload = $d->saveValidatedUpload($_FILES['attachment'] ?? [], $finSupportDir, 'Support_1', $feedbackReplyExt, $maxsize);
            if (!$upload['ok']) {
                $_SESSION['msg1'] = $upload['error'];
                header("Location: " . $feedbackListLocation);
                exit();
            }
            $attachment = $upload['filename'];
            $attachmentFile = $attachment !== '' ? ($base_url . 'img/fin_support/' . $attachment) : '';
            $close_date = date('Y-m-d H:i:s');

            $a = array(
                'with_developer' => 0,
                'client_reply_message' => $reply,
                'closing_remarks' => $reply,
                'developer_solve_time' => $close_date,
                'feedback_status' => 5,
                'issueType' => $issue_type,
            );

            if ($issue_type != '1' && $issue_type != '6') {
                $bug_type = '';
            } else {
                $bug_type = 'Bug';
            }
            $route_type = match ((int) $issue_type) {
                1 => "1",
                2 => "2",
                3 => "3",
                4 => "4",
                5 => "5",
                6 => "7",
                7 => "8",
                9 => "9",
                default => "6"
            };
            $q = $d->update("feedback_master", $a, "feedback_id='$feedback_id'");

            $close_date = date('d-m-Y H:i:s');

            if ($to != "") {
                include '../mail/ticketClose.php';
                include '../mail.php';
            }
            if ($q > 0) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $d->support_url() . 'taskController.php',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array('feedback_id' => $feedback_id, 'close_feedback' => 'close_feedback', 'remarks' => $reply, 'bug_type' => $bug_type, "route_type" => $route_type),
                    CURLOPT_HTTPHEADER => array(),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
            }
            $agent_name = $query_data['name'];
            $agent_mobile = $query_data['mobile'];
            $subject = $query_data['subject'];
            $feedback_id = $query_data['feedback_id'];

            $m->set_data('feedback_id', $feedback_id);
            $m->set_data('society_id', $society_id);
            $m->set_data('feedback_log', "Query #TKT$feedback_id closed by Developer");
            $m->set_data('feedback_log_msg', $reply);
            $m->set_data('feedback_log_attachment', $attachment);
            $m->set_data('issue_type', $issue_type);
            $m->set_data('feedback_log_date', date('Y-m-d H:i:s'));
            $m->set_data('feedback_added_by', $bms_admin_id);

            $a1  = array(
                'feedback_id' => $m->get_data('feedback_id'),
                'society_id' => $m->get_data('society_id'),
                'feedback_log' => $m->get_data('feedback_log'),
                'feedback_log_msg' => $m->get_data('feedback_log_msg'),
                'feedback_log_attachment' => $m->get_data('feedback_log_attachment'),
                'feedback_msg_status' => 0,
                'feedback_log_date' => $m->get_data('feedback_log_date'),
                'feedback_added_by' => $m->get_data('feedback_added_by'),
                'issue_type' => $m->get_data('issue_type'),
            );
            $d->insert("feedback_log_master", $a1);

            $ticket_id = $feedback_id;
            $reply_message = $reply;
            $feedback_status = '5';
            $user_mobile_no = $agent_mobile;
            include '../feedbackFcmCurl.php';


            if ($q == TRUE) {

                $hit_url = $query_data['sub_domain'] ?? '';
                $noti_title = $subject;
                if ($reply != '') {
                    $noti_description = $reply;
                } else {
                    $noti_description = "Your support ticket, <strong>$ticketNumber</strong>, has been successfully resolved and is now closed.";
                }
                if ($send_mail == 0) {
                    $post = array(
                        'send_notification' => 'send_notification_ticket',
                        'title' => $noti_title,
                        'description' => $noti_description,
                        'society_id' => $society_id,
                        'mobile_no' => $user_mobile_no,
                        'feedback_id' => $feedback_id,
                    );
                    $d->callCompanyApiEnc($hit_url, 'sendNotificationCurlController.php', $post);

                }
                $feedback_forwarded_by = $query_data['forwarded_by'];
                $has_permission = $d->count_data_direct("fcm_id", "admin_fcm_notification_master", "bms_admin_id='$feedback_forwarded_by' AND fcm_notifications='7' AND active_status='0'");
                if (($feedback_forwarded_by != $bms_admin_id) && $feedback_forwarded_by > 0 && $has_permission > 0) {
                    $notiAry = array(
                        'admin_id' => $query_data['forwarded_by'],
                        'society_id' => $query_data['society_id'],
                        'notification_tittle' => "Query #TKT$feedback_id closed by Developer",
                        'notification_description' => "Please close query from your end now.",
                        'notifiaction_date' => date('Y-m-d H:i'),
                        'admin_click_action' => "feedbackTimeline?id=$feedback_id",
                    );
                    $d->insert("admin_notification", $notiAry);

                    $noti_title = "" . $d->app_name() . " #TKT$feedback_id closed by Developer";
                    $noti_description = "Please close query from your end now.";
                    $click_action = "feedbackTimeline?id=$feedback_id";
                    $getTokens = $d->getWebFcm("web_fcm_master", "admin_id='$feedback_forwarded_by'");
                    $nAdmin->sendAdminNotification($getTokens, $noti_title, $noti_description, $click_action);
                }
                $_SESSION['msg'] = "Query Closed";
                $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Query #TKT$feedback_id closed by Developer", "6");
                if ($previousURL == "feedbackTimeline") {
                    header("Location: ../feedbackTimeline?id=$feedback_id" . ($d->feedbackListQueryString() !== '' ? '&' . $d->feedbackListQueryString() : ''));
                } else {
                    header("Location: " . $feedbackListLocation);
                }
            } else {
                $_SESSION['msg1'] = "Something Wrong";
                header("Location: " . $feedbackListLocation);
            }
        } else {
            $_SESSION['msg1'] = "Ticket already closed by Developer";
            if ($previousURL == "feedbackTimeline") {
                header("Location: ../feedbackTimeline?id=$feedback_id" . ($d->feedbackListQueryString() !== '' ? '&' . $d->feedbackListQueryString() : ''));
            } else {
                header("Location: " . $feedbackListLocation);
            }
        }
    } else if (isset($inprogress_feedback_id)) {
        $a = array('feedback_status' => 1,);
        $q = $d->update("feedback_master", $a, "feedback_id='$inprogress_feedback_id'");
        if ($q == TRUE) {
            $_SESSION['msg'] = "Feedback Status Updated";
            $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Feedback #TKT$inprogress_feedback_id Status Updated to In Progress", "6");
            header("Location: " . $feedbackListLocation);
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("Location: " . $feedbackListLocation);
        }
    } else if (isset($remarkFeedback)) {
        $m->set_data('closing_remarks', $closing_remarks . ' - ' . $closing_by);
        $a = array(
            'feedback_status' => 2,
            'feedback_solve_time' => date('Y-m-d H:i:s'),
            'closing_remarks' => $m->get_data('closing_remarks'),
        );
        $q = $d->update("feedback_master", $a, "feedback_id='$feedback_id'");

        // DY 10-10-23 Start
        // $agentDetails = $d->selectArray("feedback_master","feedback_id='$feedback_id'");
        $q = $d->select("feedback_master LEFT JOIN bms_admin_master ON  feedback_master.created_by =bms_admin_master.admin_id", "feedback_master.feedback_id='$feedback_id'", "");
        $agentDetails = mysqli_fetch_array($q);
        // DY 10-10-23 End

        $user_name = $agentDetails['name'];
        $to = $agentDetails['email'];
        $agent_mobile = $agentDetails['mobile'];
        $msg1 = $agentDetails['feedback_msg'];
        $subject = "#TKT" . $feedback_id . " Query has been closed";
        $feedback_id = $agentDetails['feedback_id'];
        $reply = $closing_remarks;

        $feedback_msg = "Dear $user_name\nThe ticket #TKT00$feedback_id for your query: '$msg1' has been closed.\n\nThank you, Team " . $d->app_name() . " ";

        $m->set_data('feedback_id', $feedback_id);
        $m->set_data('society_id', $society_id);
        $m->set_data('feedback_log', "Ticket Resolved for #TKT$feedback_id");
        $m->set_data('feedback_log_msg', $closing_remarks . "-\n\n" . $feedback_msg);
        $m->set_data('feedback_log_date', date('Y-m-d H:i:s'));
        $m->set_data('feedback_added_by', $bms_admin_id);

        $a1  = array(
            'feedback_id' => $m->get_data('feedback_id'),
            'society_id' => $m->get_data('society_id'),
            'feedback_log' => $m->get_data('feedback_log'),
            'feedback_log_msg' => $m->get_data('feedback_log_msg'),
            'feedback_msg_status' => 1,
            'feedback_log_date' => $m->get_data('feedback_log_date'),
            'feedback_added_by' => $m->get_data('feedback_added_by'),
        );
        $d->insert("feedback_log_master", $a1);
        $cc = $d->encryptDecrypt("decrypt", $agentDetails['admin_email']);
        if ($reply != '' && $to != '') {
            include '../mail/feedbackReply.php';
            include '../mail.php';
        }
        if ($q == TRUE) {
            $_SESSION['msg'] = "Feedback Status Updated";
            $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Feedback #TKT$feedback_id Status Updated to Solved", "6");
            header("Location: " . $feedbackListLocation);
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("Location: " . $feedbackListLocation);
        }
    } else if (isset($addFeedback)) {
        if ($feedback_msg == '') {
            $_SESSION['msg1'] = "Please enter your message";
            header("Location: " . $feedbackListLocation);
            exit();
        }

        $upload = $d->saveValidatedUpload($_FILES['attachment'] ?? [], $finSupportDir, 'Support_1', $feedbackImageExt, $maxsize);
        if (!$upload['ok']) {
            $_SESSION['msg1'] = $upload['error'];
            header("Location: " . $feedbackListLocation);
            exit();
        }
        $attachment = $upload['filename'];

        $upload2 = $d->saveValidatedUpload($_FILES['attachment_2'] ?? [], $finSupportDir, 'Support_2', $feedbackImageExt, $maxsize);
        if (!$upload2['ok']) {
            $_SESSION['msg1'] = $upload2['error'];
            header("Location: " . $feedbackListLocation);
            exit();
        }
        $attachment_2 = $upload2['filename'];

        $uploadVideo = $d->saveValidatedUpload($_FILES['video'] ?? [], $finSupportDir, 'Support_3', $feedbackVideoExt, $maxsizeVideo);
        if (!$uploadVideo['ok']) {
            $_SESSION['msg1'] = $uploadVideo['error'];
            header("Location: " . $feedbackListLocation);
            exit();
        }
        $video = $uploadVideo['filename'];

        $uploadDoc = $d->saveValidatedUpload($_FILES['document'] ?? [], $finSupportDir, 'Support_doc', $feedbackDocExt, $maxsizeVideo);
        if (!$uploadDoc['ok']) {
            $_SESSION['msg1'] = $uploadDoc['error'];
            header("Location: " . $feedbackListLocation);
            exit();
        }
        $document = $uploadDoc['filename'];

        if (isset($_POST['email']) && $_POST['email'] != '') {
            $m->set_data('email', $_POST['email']);
        } else {
            $m->set_data('email', $secretary_email);
        }

        $m->set_data('platform', $platform);
        $m->set_data('society_id', $companyId);
        $m->set_data('name', $name);
        $m->set_data('mobile', $mobile);
        $m->set_data('feedback_msg', $feedback_msg);
        $m->set_data('subject', $subject);
        $m->set_data('attachment', $attachment);
        $m->set_data('attachment_2', $attachment_2);
        $m->set_data('video', $video);
        $m->set_data('document', $document);
        $m->set_data('feedback_date_time',  date("d-m-Y H:i"));
        $m->set_data('created_by',  $bms_admin_id);
        $a = array(
            'platform' => $m->get_data('platform'),
            'society_id' => $m->get_data('society_id'),
            'name' => $m->get_data('name'),
            'mobile' => $m->get_data('mobile'),
            'email' => $m->get_data('email'),
            'feedback_msg' => $m->get_data('feedback_msg'),
            'subject' => $m->get_data('subject'),
            'attachment' => $m->get_data('attachment'),
            'attachment_2' => $m->get_data('attachment_2'),
            'video' => $m->get_data('video'),
            'document' => $m->get_data('document'),
            'feedback_date_time' => $m->get_data('feedback_date_time'),
            'created_by' => $m->get_data('created_by'),
        );

        // Whitelabel: same society_id can exist for multiple project_type values.
        $isWhitelabel = (isset($source_type) && (int)$source_type === 1) ? 1 : 0;
        $storedWhitelabelType = ($isWhitelabel === 1 && isset($whitelabel_type) && $whitelabel_type !== '') ? (int)$whitelabel_type : 0;
        $a['is_whitelabel'] = $isWhitelabel;
        $a['whitelabel_type'] = $storedWhitelabelType;

        $q = $d->insert("feedback_master", $a);
        $feedback_id = $con->insert_id;


        if ($q == true) {

            $m->set_data('feedback_id', $feedback_id);
            $m->set_data('society_id', $companyId);
            $m->set_data('feedback_log', "Ticket Created for $subject - #TKT$feedback_id");
            $m->set_data('feedback_log_msg', $feedback_msg);
            $m->set_data('feedback_log_date', date('Y-m-d H:i:s'));
            $m->set_data('feedback_added_by', $bms_admin_id);

            $a1  = array(
                'feedback_id' => $m->get_data('feedback_id'),
                'society_id' => $m->get_data('society_id'),
                'feedback_log' => $m->get_data('feedback_log'),
                'feedback_log_msg' => $m->get_data('feedback_log_msg'),
                'feedback_msg_status' => 1,
                'feedback_log_date' => $m->get_data('feedback_log_date'),
                'feedback_added_by' => $m->get_data('feedback_added_by'),
            );
            $d->insert("feedback_log_master", $a1);

            $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Feedback Added for $subject", "6");
            $_SESSION['msg'] = "Feedback Added Successfylly";
            header("Location: " . $feedbackListLocation);
        } else {
            $_SESSION['msg1'] = "Something Went Wrong";
            header("Location: " . $feedbackListLocation);
        }
    } else if (isset($_POST['action']) && $_POST['action'] == "getCompanyList") {
        $con = "";
        $data = array();
        if (isset($_POST['search']) && $_POST['search'] != "") {
            $search = $d->escapeSqlLike($_POST['search']);

            $sourceType = isset($source_type) ? (int) $source_type : 0;
            $whitelabelType = isset($whitelabel_type) && $whitelabel_type !== '' ? (int)$whitelabel_type : null;

            if ($sourceType === 1) {
                if ($whitelabelType === null) {
                    echo json_encode([]);
                    exit();
                }

                $urlSearch = preg_replace('#^https?://#i', '', (string) $_POST['search']);
                $urlSearch = preg_replace('#^www\.#i', '', $urlSearch);
                $urlSearch = rtrim($urlSearch, '/');
                $urlSearch = $d->escapeSqlLike($urlSearch);
                $con = " AND (society_master_white_label.society_name LIKE '%$search%' OR society_master_white_label.sub_domain LIKE '%$search%'";
                if ($urlSearch !== '' && $urlSearch !== $search) {
                    $con .= " OR society_master_white_label.sub_domain LIKE '%$urlSearch%'";
                }
                $con .= ")";
                $q = $d->selectRow(
                    "society_master_white_label.society_id,society_master_white_label.society_name,society_master_white_label.sub_domain,c.name as city_name",
                    "society_master_white_label LEFT JOIN cities c ON c.city_id = society_master_white_label.city_id",
                    "society_master_white_label.project_type='" . (int)$whitelabelType . "' $con",
                    ""
                );
                if (mysqli_num_rows($q) > 0) {
                    while ($ud = $q->fetch_assoc()) {
                        $ud['id'] = $ud['society_id'];
                        $ud['text'] = $ud['society_name'] . " (" . ($ud['city_name'] ?? '') . ")";
                        if (!empty($ud['sub_domain'])) {
                            $ud['text'] .= " - " . $ud['sub_domain'];
                        }
                        array_push($data, $ud);
                    }
                }
                echo json_encode($data);
            } else {
                $con = " AND society_master.society_status = 0 AND society_master.society_name LIKE '%$search%'";
                $q = $d->selectRow(
                    "society_master.society_id,society_master.society_name,c.name as city_name",
                    "society_master JOIN cities AS c ON c.city_id = society_master.city_id",
                    "1=1 $con",
                    ""
                );
                if (mysqli_num_rows($q) > 0) {
                    while ($ud = $q->fetch_assoc()) {
                        $ud['id'] = $ud['society_id'];
                        $ud['text'] = $ud['society_name'] . " (" . ($ud['city_name'] ?? '') . ")";
                        array_push($data, $ud);
                    }
                }
                echo json_encode($data);
            }
        } else {
            echo json_encode([]);
        }
        exit();
    } else if (isset($_POST['reOpenQuery']) && $_POST['reOpenQuery'] == "reOpenQuery") {
        $a1['feedback_status'] = 0;
        $a1['with_developer'] = 1;
        $a1['isTicket'] = 1;
        $a1['is_reopen'] = 1;
        $a1['reopen_remarks'] = $reopen_remarks;
        $a1['reopen_date_time'] = date('d-m-Y h:i');
        $q = $d->update("feedback_master", $a1, "feedback_id=$feedback_id");
        if ($q > 0) {
            $m->set_data('feedback_id', $feedback_id);
            $m->set_data('society_id', $society_id);
            $m->set_data('feedback_log', "Query #TKT$feedback_id Ticket Reopen.");
            $m->set_data('feedback_log_attachment', $row['attachment']);
            $m->set_data('feedback_log_msg', $reopen_remarks);
            $m->set_data('feedback_log_date', date('Y-m-d H:i:s'));
            $m->set_data('feedback_added_by', $bms_admin_id);
            $a12  = array(
                'feedback_id' => $m->get_data('feedback_id'),
                'society_id' => $m->get_data('society_id'),
                'feedback_log' => $m->get_data('feedback_log'),
                'feedback_log_msg' => $m->get_data('feedback_log_msg'),
                'feedback_log_attachment' => $m->get_data('feedback_log_attachment'),
                'feedback_msg_status' => 0,
                'feedback_log_date' => $m->get_data('feedback_log_date'),
                'feedback_added_by' => $m->get_data('feedback_added_by'),
            );
            $d->insert("feedback_log_master", $a12);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $d->support_url() . 'taskController.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('reopen_feedback' => 'reopen_feedback', 'feedback_id' => $feedback_id, 'remark' => $reopen_remarks),
                CURLOPT_HTTPHEADER => array(),
            ));
            $response = curl_exec($curl);
            curl_close($curl);

            $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Query #TKT$feedback_id Ticket Reopen", "6");
            $_SESSION['msg'] = "Query #TKT$feedback_id Ticket Reopen Successfully.";
            $q = $d->select("feedback_master", "feedback_id=$feedback_id");
            $data = mysqli_fetch_array($q);
            $agent_mobile = $data['mobile'];
            $ticket_id = $feedback_id;
            $reply_message = $reopen_remarks;
            $feedback_status = '0';
            $user_mobile_no = $agent_mobile;
            include '../feedbackFcmCurl.php';
            $feedback_created_by = $data['created_by'];
            $has_permission = $d->count_data_direct("fcm_id", "admin_fcm_notification_master", "bms_admin_id='$feedback_created_by' AND fcm_notifications='6' AND active_status='0'");
            if ($feedback_created_by != $bms_admin_id && $feedback_created_by > 0 && $has_permission > 0) {
                $devnotiAry = array(
                    'admin_id' => $feedback_created_by,
                    'society_id' => $society_id,
                    'notification_tittle' => "" . $d->app_name() . " #TKT$feedback_id Reopened",
                    'notifiaction_date' => date('Y-m-d H:i'),
                    'admin_click_action' => 'feedbackTimeline?id=' . $feedback_id,
                );
                $d->insert("admin_notification", $devnotiAry);
                $noti_title = "" . $d->app_name() . " #TKT$feedback_id Reopened";
                $noti_description = "Query has been Reopened.";
                $click_action = "feedbackTimeline?id=$feedback_id";
                $getTokens = $d->getWebFcm("web_fcm_master", "admin_id='$feedback_created_by'");
                $nAdmin->sendAdminNotification($getTokens, $noti_title, $noti_description, $click_action);
            }
            header("Location: " . $feedbackListLocation);
        } else {
            $_SESSION['msg1'] = "Something Went Wrong";
            header("Location: " . $feedbackListLocation);
        }
    } else if (isset($_POST['getCompanyDetails']) && $_POST['getCompanyDetails'] == 'getCompanyDetails') {
        $sourceType = isset($source_type) ? (int)$source_type : 0;
        $societyId = isset($society_id) ? (int)$society_id : 0;
        if ($societyId <= 0) {
            $response['status'] = "400";
            $response['message'] = "Company required";
            $response['secretary_email'] = '';
            $response['secretary_name'] = '';
            $response['secretary_mobile'] = '';
            echo json_encode($response);
            exit();
        }
        if ($sourceType === 1) {
            $wlType = isset($whitelabel_type) && $whitelabel_type !== '' ? (int)$whitelabel_type : null;
            if ($wlType === null) {
                $response['status'] = "400";
                $response['message'] = "Whitelabel type required";
                echo json_encode($response);
                exit();
            }
            $wlQ = $d->select("society_master_white_label", "society_id=$societyId AND project_type=$wlType");
            if (mysqli_num_rows($wlQ) === 0) {
                $response['status'] = "404";
                $response['message'] = "Whitelabel company not found";
                echo json_encode($response);
                exit();
            }
            $wlData = mysqli_fetch_array($wlQ);
            $masterCompanyId = (int)($wlData['master_company_id'] ?? 0);
            $q = $masterCompanyId > 0 ? $d->select("society_master", "society_id=$masterCompanyId") : false;
        } else {
            $q = $d->select("society_master", "society_id=$societyId");
        }
        $data = ($q && mysqli_num_rows($q) > 0) ? mysqli_fetch_array($q) : [];
        $response['status'] = "200";
        $response['message'] = "Success";
        $response['secretary_email'] = $data['secretary_email'] ?? '';
        $response['secretary_name'] = $data['secretary_name'] ?? '';
        $secretaryMobile = $data['secretary_mobile'] ?? '';
        if ($sourceType === 1 && ($secretaryMobile === '0' || $secretaryMobile === 0 || $secretaryMobile === '')) {
            $secretaryMobile = '';
        }
        $response['secretary_mobile'] = $secretaryMobile;
        echo json_encode($response);
    } else if (isset($editFeedbackPlatform) && $editFeedbackPlatform == 'editFeedbackPlatform') {
        $a = array('platform' => $platform, 'module_type' => $module_type);
        $q = $d->update("feedback_master", $a, "feedback_id='$feedback_id'");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $d->support_url() . 'taskController.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('changePlatform' => 'changePlatform', 'feedback_id' => $feedback_id, 'platform_id' => $platform),
            CURLOPT_HTTPHEADER => array(),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        if ($q == TRUE) {
            $_SESSION['msg'] = "Feedback platform updated";
            $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Feedback #TKT$feedback_id platform updated", "6");
            header("Location: ../feedbackTimeline?id=" . $feedback_id . ($d->feedbackListQueryString() !== '' ? '&' . $d->feedbackListQueryString() : ''));
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("Location: ../feedbackTimeline?id=" . $feedback_id . ($d->feedbackListQueryString() !== '' ? '&' . $d->feedbackListQueryString() : ''));
        }
    } else if (isset($deleteFeedbackReply)) {
        if ($feedback_id != "" && $feedback_log_id != "") {
            $d->delete("feedback_log_master", "feedback_log_id='$feedback_log_id' AND feedback_id='$feedback_id'");
            $_SESSION['msg'] = "Reply Deleted Successfully.";
            header("Location: ../feedbackTimeline?id=" . $feedback_id . ($d->feedbackListQueryString() !== '' ? '&' . $d->feedbackListQueryString() : ''));
            exit;
        } else {
            $_SESSION['msg1'] = "Failed to delete reply.";
            header("Location: ../feedbackTimeline?id=" . $feedback_id . ($d->feedbackListQueryString() !== '' ? '&' . $d->feedbackListQueryString() : ''));
            exit;
        }
    } else if (isset($feedbackOverdueSettings)) {

        $m->set_data('developer_feedback_overdue_hours', $developer_feedback_overdue_hours);
        $m->set_data('support_feedback_overdue_after_close_hours', $support_feedback_overdue_after_close_hours);
        $m->set_data('support_feedback_overdue_new_ticket_hours', $support_feedback_overdue_new_ticket_hours);
        $m->set_data('created_by', $bms_admin_id);
        $m->set_data('created_date', date('Y-m-d H:i:s'));

        if (isset($feedback_overdue_id) && $feedback_overdue_id != "" && $feedback_overdue_id != 0) {

            $settingsUpdate = array(
                'developer_feedback_overdue_hours' => $m->get_data('developer_feedback_overdue_hours'),
                'support_feedback_overdue_after_close_hours' => $m->get_data('support_feedback_overdue_after_close_hours'),
                'support_feedback_overdue_new_ticket_hours' => $m->get_data('support_feedback_overdue_new_ticket_hours'),
                'updated_by' => $m->get_data('created_by'),
                'updated_date' => $m->get_data('created_date'),
            );
            $settingsUpdateq = $d->update("feedback_overdue_settings", $settingsUpdate, "feedback_overdue_id='$feedback_overdue_id'");
            if ($settingsUpdateq == TRUE) {
                $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Feedback Overdue Settings Updated", "6");
                $_SESSION['msg'] = "Feedback Overdue Settings Updated";
                header("Location: " . $feedbackListLocation);
            } else {
                $_SESSION['msg1'] = "Something Wrong";
                header("Location: " . $feedbackListLocation);
            }
        } else {
            $settingsInsert = array(
                'developer_feedback_overdue_hours' => $m->get_data('developer_feedback_overdue_hours'),
                'support_feedback_overdue_after_close_hours' => $m->get_data('support_feedback_overdue_after_close_hours'),
                'support_feedback_overdue_new_ticket_hours' => $m->get_data('support_feedback_overdue_new_ticket_hours'),
                'created_by' => $m->get_data('created_by'),
                'created_date' => $m->get_data('created_date'),
            );
            $settingsInsertq = $d->insert("feedback_overdue_settings", $settingsInsert);
            if ($settingsInsertq == TRUE) {
                $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Feedback Overdue Settings Added", "6");
                $_SESSION['msg'] = "Feedback Overdue Settings Added";
                header("Location: " . $feedbackListLocation);
            } else {
                $_SESSION['msg1'] = "Something Wrong";
                header("Location: " . $feedbackListLocation);
            }
        }
    } else {
        $_SESSION['msg1'] = "Something Wrong";
        header("Location: " . $feedbackListLocation);
    }
}
