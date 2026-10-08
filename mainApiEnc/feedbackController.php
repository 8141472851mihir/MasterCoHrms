<?php
include_once 'lib.php';
$_POST =  json_decode($d->manage_decryption("1", file_get_contents("php://input")),true);
$compress = resolve_api_compress();
// $_POST =  $d->manage_encryption("1", $_POST);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted,[
        "status" => "201",
        "message" => $xml->string->invalid_request.""
    ], $compress);
    exit();
}
try {
    if (isset($_POST) && !empty($_POST)) {
        if ($key == $keydb) {
            $response = array();
            extract(array_map("test_input", $_POST));
            if (isset($feedbackListing) && $feedbackListing=="feedbackListing" && isset($society_id) && $society_id !="") {
                if (isset($for_employee) && $for_employee!='') { 
                    if ($for_employee == "0" && $user_mobile == "") {
                        $response["message"] = $xml->string->please_provide_employee_mobile_no.'';
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit;
                    }
                    $condition = ($for_employee == "0" && $user_mobile != "") ? "society_id='$society_id' AND mobile='$user_mobile' AND feedback_type='0'" : "society_id='$society_id' AND feedback_type='0'";
                    
                    $society_feedback_qry = $d->select("feedback_master", $condition,"ORDER BY feedback_id DESC");
                    $society_feedback_array = [];
                    while($society_feedback_data=mysqli_fetch_array($society_feedback_qry)){
                        $society_feedback['feedback_id']=$society_feedback_data['feedback_id'].'';
                        $society_feedback['feedback_id_view']='#TKT'.$society_feedback_data['feedback_id'].'';
                        if($society_feedback_data['platform']=='0'){
                            $society_feedback['platform']='Other';
                        }elseif($society_feedback_data['platform']=='1'){
                            $society_feedback['platform']='Android';
                        }else{
                            $society_feedback['platform']='Web';
                        }
                        $society_feedback['name']=$society_feedback_data['name'].'';
                        $society_feedback['email']=$society_feedback_data['email'].'';
                        $society_feedback['feedback_msg']=$society_feedback_data['feedback_msg'].'';
                        $society_feedback['subject']=$society_feedback_data['subject'].'';
                        $date = DateTime::createFromFormat('d-m-Y H:i', $society_feedback_data['feedback_date_time']);
                        $formattedDate = $date->format('d M Y h:i A');
                        $society_feedback['feedback_date_time']=$formattedDate.'';
                        $society_feedback['feedback_status']=$society_feedback_data['feedback_status'].'';
                        $society_feedback['isTicket']=$society_feedback_data['isTicket'].'';
                        $society_feedback['with_developer']=$society_feedback_data['with_developer'].'';
                        if($society_feedback_data['feedback_status']=='2' || $society_feedback_data['feedback_status']=='6' || $society_feedback_data['feedback_status']==5){
                            $society_feedback['status']='Closed';
                        } else if($society_feedback_data['feedback_status']=='8' && $society_feedback_data['with_developer']=='1'){
                            $society_feedback['status']='Resolved in next update';    
                        }else if($society_feedback_data['feedback_status']!='2' && $society_feedback_data['with_developer']=='1'){
                            $society_feedback['status']='WIP';
                        }else{
                            $society_feedback['status']='Open';
                        }
                        if($society_feedback_data['attachment']!=''){
                            $society_feedback['attachment']=$base_url."img/fin_support/".$society_feedback_data['attachment'].'';
                        }else{
                            $society_feedback['attachment']='';
                        }
                        if($society_feedback_data['attachment_2']!=''){
                            $society_feedback['attachment_2']=$base_url."img/fin_support/".$society_feedback_data['attachment_2'].'';
                        }else{
                            $society_feedback['attachment_2']='';
                        }

                        if($society_feedback_data['video']!=''){
                            $society_feedback['video']=$base_url."img/fin_support/".$society_feedback_data['video'].'';
                        }else{
                            $society_feedback['video']='';
                        }

                        if($society_feedback_data['document']!=''){
                            $society_feedback['document']=$base_url."img/fin_support/".$society_feedback_data['document'].'';
                        }else{
                            $society_feedback['document']='';
                        }
                        array_push($society_feedback_array,$society_feedback);
                    }
                    $response["message"] = $xml->string->data_found."";
                    $response["society_feedback"] = $society_feedback_array;
                    $response["status"] = "200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }else{
                    $response["message"] = $xml->string->please_provide_required_data.'';
                    $response["status"] = "201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }
            }else if (isset($feedbackDetails) && $feedbackDetails=="feedbackDetails" && isset($society_id) && $society_id !="") {


                if (isset($feedback_id) && $feedback_id!='') { 

                    $tQuery = $d->select("feedback_master","feedback_id='$feedback_id' AND society_id='$society_id'" ,"ORDER BY feedback_id DESC");

                    if (mysqli_num_rows($tQuery)==0) {
                        $response["message"] = $xml->string->invalid.$xml->string->ticket_id.'';
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    }

                    $TData= mysqli_fetch_array($tQuery);


                    $timelineQuery = $d->selectRow("feedback_log_master.*,bms_admin_master.admin_name","feedback_log_master LEFT JOIN bms_admin_master ON bms_admin_master.admin_id=feedback_log_master.feedback_added_by","feedback_id='$feedback_id' AND feedback_log_master.society_id='$society_id' AND feedback_log_master.hide_to_user='0'","ORDER BY feedback_log_id DESC");
                    $feedback_log = [];
                    if(mysqli_num_rows($tQuery)>0){
                        while($timelineData = mysqli_fetch_array($timelineQuery)){
                            $parent_feedback_log_id= $timelineData['feedback_log_id'];
                            $feedback_log_array['feedback_log_id']=$timelineData['feedback_log_id'].'';
                            $feedback_log_array['feedback_id']='#TKT'.$timelineData['feedback_id'].'';
                            $feedback_log_array['feedback_log']=$timelineData['feedback_log'].'';
                            $feedback_log_array['feedback_log_msg']=$timelineData['feedback_log_msg'].'';
                            if ($timelineData['user_mobile']>0 && $timelineData['user_mobile']==$user_mobile && $timelineData['country_code']==$country_code && $timelineData['log_added_type']==1) {
                                $feedback_log_array['msg_by']=  ($timelineData['client_name']!="") ? $timelineData['client_name'] : 'Compnay Admin';
                                $feedback_log_array['msg_side']='left';
                            } else {
                                $feedback_log_array['msg_by']=$timelineData['admin_name'];
                                $feedback_log_array['msg_side']='right';
                            }
                            if ($timelineData['feedback_log_status']==1) {
                                $feedback_log_msg = 'WIP';
                            }else if ($timelineData['feedback_log_status']==2) {
                                $feedback_log_msg = 'Closed';
                            }else if ($timelineData['feedback_log_status']==3) {
                                $feedback_log_msg = 'On Hold';
                            } else if ($timelineData['feedback_log_status']==3) {
                                $feedback_log_msg = 'Rejected';
                            }else {
                                $feedback_log_msg = "";
                            } 

                            $feedback_log_array['feedback_log_status']=$feedback_log_msg.'';
                            if($timelineData['feedback_log_attachment']!=''){
                                $feedback_log_array['feedback_log_attachment']=$base_url."img/fin_support/".$timelineData['feedback_log_attachment'].'';
                            }else{
                                $feedback_log_array['feedback_log_attachment']='';
                            }
                            $date = new DateTime($timelineData['feedback_log_date']);
                            $formattedDate = $date->format('M d h:i A');
                            $feedback_log_array['feedback_log_date']=$formattedDate.'';
                            array_push($feedback_log,$feedback_log_array);
                        }

                        $response['feedback_id']=$TData['feedback_id'].'';
                        $response['feedback_id_view']='#TKT'.$TData['feedback_id'].'';
                        if($TData['platform']=='0'){
                            $response['platform']='Other';
                        }elseif($TData['platform']=='1'){
                            $response['platform']='Android';
                        }else{
                            $response['platform']='Web';
                        }
                        $response['name']=$TData['name'].'';
                        $response['email']=$TData['email'].'';
                        $response['feedback_msg']=$TData['feedback_msg'].'';
                        $response['subject']=$TData['subject'].'';
                        $date = DateTime::createFromFormat('d-m-Y H:i', $TData['feedback_date_time']);
                        $formattedDate = $date->format('d M Y h:i A');
                        $response['feedback_date_time']=$formattedDate.'';
                        $response['feedback_status']=$TData['feedback_status'].'';
                        $response['isTicket']=$TData['isTicket'].'';
                        $response['with_developer']=$TData['with_developer'].'';
                        if($TData['feedback_status']=='2' || $TData['feedback_status']=='6' || $TData['feedback_status']==5){
                            $response['feedback_status']='Closed';
                        }else if($TData['feedback_status']!='2' && $TData['with_developer']=='1'){
                            $response['feedback_status']='WIP';
                        }else{
                            $response['feedback_status']='Open';
                        }
                        if($TData['attachment']!=''){
                            $response['attachment']=$base_url."img/fin_support/".$TData['attachment'].'';
                        }else{
                            $response['attachment']='';
                        }
                        if($TData['attachment_2']!=''){
                            $response['attachment_2']=$base_url."img/fin_support/".$TData['attachment_2'].'';
                        }else{
                            $response['attachment_2']='';
                        }

                        if($TData['video']!=''){
                            $response['video']=$base_url."img/fin_support/".$TData['video'].'';
                        }else{
                            $response['video']='';
                        }

                        if($TData['document']!=''){
                            $response['document']=$base_url."img/fin_support/".$TData['document'].'';
                        }else{
                            $response['document']='';
                        }

                        $response["message"] = $xml->string->data_found."";
                        $response["feedback_log_data"] = $feedback_log;
                        $response["status"] = "200";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();

                    }else{
                        $response["message"] = $xml->string->no_data_found_web.'';
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    }
                }else{
                    $response["message"] = $xml->string->please_provide_required_data.'';
                    $response["status"] = "201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }
            } else{
            $response["message"] = $xml->string->wrong_tag.'';
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit();

            }
        } else {
            $response["message"] = $xml->string->wrong_api_key.'';
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit();
        }
    }
} catch (Exception $e) {
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
}
echo $d->manage_encryption($is_encrypted, $response, $compress);
exit();
