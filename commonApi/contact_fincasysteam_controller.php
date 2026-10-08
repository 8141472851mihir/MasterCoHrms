<?php
include_once 'lib.php';
include_once '../apAdmin/lib/crmconfig.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
if(isset($_POST) && !empty($_POST)){

    $response = array();

    extract(array_map("test_input" , $_POST));
        
            if($_POST['getTeamDetails']=="getTeamDetails"){

                if ($country_id!="") {
                    $country_id = $d->sanitizeActionIdAsInt($country_id);
                    $fincasys_contacts=$d->select("fincasys_contacts","country_id='$country_id'","");
                } else  {
                    $fincasys_contacts=$d->select("fincasys_contacts","fincasys_id=1","");
                }

                if (mysqli_num_rows($fincasys_contacts)==0) {
                    $fincasys_contacts=$d->select("fincasys_contacts","fincasys_id=1","");
                }

                if(mysqli_num_rows($fincasys_contacts)>0){

                    $data_fincasys_contacts=mysqli_fetch_array($fincasys_contacts);

                        $response["fincasys_mobile"]=$data_fincasys_contacts['fincasys_mobile'];
                        $response["fincasys_alternate_no"]=$data_fincasys_contacts['fincasys_alternate_no'];
                        $response["fincasys_email"]=$data_fincasys_contacts['fincasys_email'];
                        $response["fincasys_website"]=$data_fincasys_contacts['fincasys_website'];
                        $response["availble_time"]=$data_fincasys_contacts['availble_time'];


                         $fincasys_teamq=$d->select("fincasys_team","team_status='1'","");

                            if(mysqli_num_rows($fincasys_teamq)>0){

                                 $response["fincasys_team"] = array();


                             while($data_fincasys_team=mysqli_fetch_array($fincasys_teamq)) {
                                $fincasys_team = array();
                                $fincasys_team["fincasys_team_id"]=$data_fincasys_team['fincasys_team_id'];
                                $fincasys_team["team_name"]=$data_fincasys_team['team_name'];
                                $fincasys_team["team_designation"]=$data_fincasys_team['team_designation'];
                                $fincasys_team["team_photo"]=$base_url."images/team/".$data_fincasys_team['team_photo'];
                                array_push($response["fincasys_team"], $fincasys_team); 
               
                            } 
                    
                     }    
                    $response["message"]="Get Team Successfully.";
                    $response["status"]="200";
                    echo json_encode($response);

                }else{

                    $response["message"]="No Team Details Found.";
                    $response["status"]="201";
                    echo json_encode($response);

                }

            } else if($_POST['send_feedback']=="send_feedback" && filter_var($society_id, FILTER_VALIDATE_INT) == true) {

                if ($feedback_msg=='') {
                    $response["message"] = "Please enter your message";
                    $response["status"] = "201";
                    echo json_encode($response);
                    exit();
                }


                $maxsize    = 10097152;
                   $file11=$_FILES["attachmentImg1"]["tmp_name"];
                   if (file_exists($file11)) {
                    if(($_FILES['attachmentImg1']['size'] >= $maxsize) || ($_FILES["attachmentImg1"]["size"] == 0)) {
                        $response["message"]="Photo too large. Must be less than 10 MB";
                        $response["status"]="201";
                        echo json_encode($response);
                        exit();
                    }

                    $extId = pathinfo($_FILES['attachmentImg1']['name'], PATHINFO_EXTENSION);
                    $extAllow=array("jpeg","jpg","png","gif",'webp');
                    $extId=strtolower($extId);
                    if(in_array($extId,$extAllow)) {
                       $temp = explode(".", $_FILES["attachmentImg1"]["name"]);
                        $attachmentImg1 = "Support".$name.round(microtime(true)) . '.' . end($temp);
                        move_uploaded_file($_FILES["attachmentImg1"]["tmp_name"], "../img/fin_support/" . $attachmentImg1);
                    } else {
                      $response["message"]="Invalid attachmentImg1 only Photo, Video & Doc are allowed.";
                      $response["status"]="201";
                      echo json_encode($response);
                      exit();
                    }
                    $imageurl = $base_url."img/fin_support/".$attachmentImg1;
                  } else {
                    $attachmentImg1="";
                    $imageurl= "";
                 }

                 $file22=$_FILES["attachmentImg2"]["tmp_name"];
                   if (file_exists($file22)) {
                    if(($_FILES['attachmentImg2']['size'] >= $maxsize) || ($_FILES["attachmentImg2"]["size"] == 0)) {
                        $response["message"]="Photo too large. Must be less than 10 MB";
                        $response["status"]="201";
                        echo json_encode($response);
                        exit();
                    }

                    $extId = pathinfo($_FILES['attachmentImg2']['name'], PATHINFO_EXTENSION);
                    $extAllow=array("jpeg","jpg","png","gif",'webp');
                    $extId=strtolower($extId);
                    if(in_array($extId,$extAllow)) {
                       $temp = explode(".", $_FILES["attachmentImg2"]["name"]);
                        $attachmentImg2 = "Support1".$name.round(microtime(true)) . '.' . end($temp);
                        move_uploaded_file($_FILES["attachmentImg2"]["tmp_name"], "../img/fin_support/" . $attachmentImg2);
                    } else {
                      $response["message"]="Invalid Photo";
                      $response["status"]="201";
                      echo json_encode($response);
                      exit();
                    }
                    $imageurl = $base_url."img/fin_support/".$attachmentImg2;
                  } else {
                    $attachmentImg2="";
                    $imageurl= "";
                 }

                $maxsizeVideo    = 30097152;
                $file33=$_FILES["video"]["tmp_name"];
                 if (file_exists($file33)) {

                      if(($_FILES['video']['size'] >= $maxsizeVideo) || ($_FILES["video"]["size"] == 0)) {
                        $response["message"]="video too large. Must be less than 30 MB";
                        $response["status"]="201";
                        echo json_encode($response);
                        exit();
                      }

                      $extId = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
                      $extAllow=array('mov', 'mp4', 'webm');
                      $extId=strtolower($extId);
                      if(in_array($extId,$extAllow)) {
                         $temp = explode(".", $_FILES["video"]["name"]);
                          $video = "Support_3_".$mobile.round(microtime(true)) . '.' . end($temp);
                          move_uploaded_file($_FILES["video"]["tmp_name"], "../img/fin_support/" . $video);
                      } else {
                      $response["message"]="Invalid Video Attachment only Video are allowed.";
                      $response["status"]="201";
                      echo json_encode($response);
                      exit();
                      }
                    } else {
                       $video="";
                   }

                 $file44=$_FILES["document"]["tmp_name"];
                  if(file_exists($file44))
                  {
                    if(($_FILES['document']['size'] >= $maxsizeVideo) || ($_FILES["document"]["size"] == 0)) {
                      $_SESSION['msg1']="document too large. Must be less than 30 MB";
                      header("Location: ../feedback");
                      exit();
                    }
                    $extId = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
                    $extAllow=array("txt","xls","csv","pdf","docx","odt","ods","xlsx");
                    if(in_array($extId,$extAllow)) {
                        $extId=strtolower($extId);
                        $temp = explode(".", $_FILES["document"]["name"]);
                        $document = "Support_3_".$mobile.round(microtime(true)) . '.' . end($temp);
                        move_uploaded_file($_FILES["document"]["tmp_name"], "../img/fin_support/" . $document);
                     } else {
                      $response["message"]="Invalid Document Attachment";
                      $response["status"]="201";
                      echo json_encode($response);
                      exit();
                    }
                  }else{
                    $document="";
                  }


                $platform = ($device=='iOS') ? 2 : 1;

                $m->set_data('society_id', $society_id);
                $m->set_data('platform', $platform);
                $m->set_data('name', $name);
                $m->set_data('email', $email);
                $m->set_data('mobile', $mobile);
                $m->set_data('country_code', $country_code);
                $m->set_data('feedback_msg', $feedback_msg);
                $m->set_data('subject', $subject);
                $m->set_data('attachment', $attachmentImg1);
                $m->set_data('attachment_2', $attachmentImg2);
                $m->set_data('video', $video);
                $m->set_data('document', $document);
                $m->set_data('app_version_code', $app_version_code);
                $m->set_data('device', $device);
                $m->set_data('feedback_date_time',  date("d-m-Y H:i"));

                $a = array(
                    'society_id' => $m->get_data('society_id'),
                    'platform' => $m->get_data('platform'),
                    'name' => $m->get_data('name'),
                    'email' => $m->get_data('email'),
                    'mobile' => $m->get_data('mobile'),
                    'country_code' => $m->get_data('country_code'),
                    'feedback_msg' => $m->get_data('feedback_msg'),
                    'subject' => $m->get_data('subject'),
                    'attachment' => $m->get_data('attachment'),
                    'attachment_2' => $m->get_data('attachment_2'),
                    'video' => $m->get_data('video'),
                    'document' => $m->get_data('document'),
                    'feedback_date_time' => $m->get_data('feedback_date_time'),
                    'app_version_code' => $m->get_data('app_version_code'),
                    'device' => $m->get_data('device'),
                   
                );

                $q = $d->insert("feedback_master", $a);
                $feedback_id = $con->insert_id;

                if ($q == true) {
                    $admins = $d->select("admin_fcm_notification_master","fcm_notifications=1");
                    $total_admins = mysqli_num_rows($admins);
                    if($total_admins>0){
                        $noti_title = "New Feedback From $name ";
                        $noti_description = $subject;
                        $click_action = "feedback?feedback_id=$feedback_id";
                        $tokenwhere = "";
                        $ik=0;
                        while($adminsdata = mysqli_fetch_array($admins)){
                            $admin_id = $adminsdata['bms_admin_id'];
                            if($ik>0){ $tokenwhere .= " OR "; }
                            $tokenwhere .= "admin_id=$admin_id";
                            $ik++;
                        }
                        $getTokens = $d->getWebFcm("web_fcm_master","$tokenwhere");
                        $nAdmin->sendAdminNotification($getTokens,$noti_title,$noti_description,$click_action,$imageurl);
                    }

                    // $file1 = $file2 = $file3 = $file4 = "";
                    // if($attachment!=""){
                    //   $file1 = $base_url."img/fin_support/".$attachment;
                    // }
                    // $curl = curl_init();

                    // curl_setopt_array($curl, array(
                    //   CURLOPT_URL => 'https://support.chplgroup.org/mainApi/taskController.php',
                    //   CURLOPT_RETURNTRANSFER => true,
                    //   CURLOPT_ENCODING => '',
                    //   CURLOPT_MAXREDIRS => 10,
                    //   CURLOPT_TIMEOUT => 0,
                    //   CURLOPT_FOLLOWLOCATION => true,
                    //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    //   CURLOPT_CUSTOMREQUEST => 'POST',
                    //   CURLOPT_POSTFIELDS => array('add_feedback' => 'add_feedback','bug_platform' => $platform,'title' => $subject,'description' => $feedback_msg,'bug_screen' => 'App Support Page','bug_app' => 'mycompany','bug_version' => 'Production','file1' => $file1,'file2' => $file2,'file3' => $file3,'file4' => $file4),
                    //   CURLOPT_HTTPHEADER => array(),
                    // ));
                    // $curlresponse = curl_exec($curl);
                    // curl_close($curl);


                    $response["message"] = "Thank you for contacting us, we will reach back to you in a short time.";
                    $response["status"] = "200";
                    echo json_encode($response);
                } else {
                    $response["message"] = "wrong data.";
                    $response["status"] = "201";
                    echo json_encode($response);
                }

            }else if($_POST['send_feedback_sp']=="send_feedback_sp") {

                if ($feedback_msg=='') {
                    $response["message"] = "Please enter your message";
                    $response["status"] = "201";
                    echo json_encode($response);
                    exit();
                }


               $maxsize    = 10097152;
               $file11=$_FILES["attachment"]["tmp_name"];
               if (file_exists($file11)) {

                    if(($_FILES['attachment']['size'] >= $maxsize) || ($_FILES["attachment"]["size"] == 0)) {
                        $response["message"]="Attachment too large. Must be less than 10 MB";
                        $response["status"]="201";
                        echo json_encode($response);
                        exit();
                    }

                    $extId = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                    $extAllow=array("mp4","m4a","m4v","f4v","f4a","m4b","m4r","f4b","mov","3gp","avi","png","jpg","jpeg","gif","JPG","JPEG","PNG","PDF","pdf","Pdf","Doc","DOC","doc","ppt","PPT","Ppt","XLS","Xls","xls");
                    if(in_array($extId,$extAllow)) {
                       $temp = explode(".", $_FILES["attachment"]["name"]);
                        $attachment = "Support_".$name.round(microtime(true)) . '.' . end($temp);
                        move_uploaded_file($_FILES["attachment"]["tmp_name"], "../img/fin_support/" . $attachment);
                    } else {
                      $response["message"]="Invalid Attachment only Photo, Video & Doc are allowed.";
                      $response["status"]="201";
                      echo json_encode($response);
                      exit();
                    }
                  } else {
                     $attachment="";
                 }
                 $platform = ($device=='iOS') ? 2 : 1;

                $m->set_data('platform', $platform);
                $m->set_data('name', $name);
                $m->set_data('email', $email);
                $m->set_data('mobile', $mobile);
                $m->set_data('country_code', $country_code);
                $m->set_data('feedback_msg', $feedback_msg);
                $m->set_data('subject', $subject.' (Service Provider)');
                $m->set_data('attachment', $attachment);
                $m->set_data('app_version_code', $app_version_code);
                $m->set_data('device', $device);
                $m->set_data('feedback_date_time',  date("d-m-Y H:i"));
                
                $a = array(
                    'society_id' => $m->get_data('society_id'),
                    'platform' => $m->get_data('platform'),
                    'name' => $m->get_data('name'),
                    'email' => $m->get_data('email'),
                    'mobile' => $m->get_data('mobile'),
                    'country_code' => $m->get_data('country_code'),
                    'feedback_msg' => $m->get_data('feedback_msg'),
                    'subject' => $m->get_data('subject'),
                    'attachment' => $m->get_data('attachment'),
                    'feedback_date_time' => $m->get_data('feedback_date_time'),
                    'app_version_code' => $m->get_data('app_version_code'),
                    'device' => $m->get_data('device'),
                   
                );

                $q = $d->insert("feedback_master", $a);


                if ($q == true) {
                  
                    $response["message"] = "Thank you for your feedback.";
                    $response["status"] = "200";
                    echo json_encode($response);
                } else {
                    $response["message"] = "wrong data.";
                    $response["status"] = "201";
                    echo json_encode($response);
                }

            }else if($_POST['request_society']=="request_society") {

                if ($person_mobile=='') {
                    $response["message"] = "Please enter your phone number";
                    $response["status"] = "201";
                    echo json_encode($response);
                    exit();
                }

                if ($is_landing_page==1) {
                    $subject = "New ".$d->app_name()." $inquiry_type_view Inquiry From Landing Page ($company_name)";
                } else {
                    $subject = "New ".$d->app_name()." $inquiry_type_view Inquiry From App ($company_name Company Not Found)";
                }

                if ($inquiry_type==1) {
                    $inquiry_type_view = "Sales";
                    $fcm_type = 5;
                } else if ($inquiry_type==2) {
                    $inquiry_type_view = "App Support";
                    $fcm_type = 1;
                } else {
                     $inquiry_type_view = "";
                    $fcm_type = 0;
                }
                $to = $d->get_inquiry_emails($inquiry_type);

                $company_name = htmlentities($company_name);
                $feedback_msg = htmlentities($feedback_msg);
                $city = htmlentities($city);
                $feedback_msg = 'Address :'.$address;
                $company_name = ucfirst($company_name);
                $person_name = ucfirst($person_name);
                $platform = ($device=='iOS') ? 2 : 1;

                $m->set_data('platform', $platform);
                $m->set_data('name', $person_name);
                $m->set_data('email', $person_email);
                $m->set_data('mobile', $person_mobile);
                $m->set_data('city', $city);
                $m->set_data('country_code', $country_code);
                $m->set_data('feedback_msg', $feedback_msg);
                $m->set_data('subject', "New ".$d->app_name()." $inquiry_type_view Inquiry From App ($company_name Company Not Found)");
                $m->set_data('attachment', $attachment);
                $m->set_data('app_version_code', $app_version_code);
                $m->set_data('device', $device);
                $m->set_data('inquiry_company_name', $company_name);
                $m->set_data('no_of_employee', $no_of_employees);
                $m->set_data('inquiry_type', $inquiry_type);
                $m->set_data('feedback_date_time',  date("d-m-Y H:i"));
                
                $a = array(
                    'society_id' => $m->get_data('society_id'),
                    'name' => $m->get_data('name'),
                    'email' => $m->get_data('email'),
                    'mobile' => $m->get_data('mobile'),
                    'country_code' => $m->get_data('country_code'),
                    'feedback_msg' => $m->get_data('feedback_msg'),
                    'subject' => $m->get_data('subject'),
                    'attachment' => $m->get_data('attachment'),
                    'feedback_date_time' => $m->get_data('feedback_date_time'),
                    'app_version_code' => $m->get_data('app_version_code'),
                    'device' => $m->get_data('device'),
                    'inquiry_company_name' => $m->get_data('inquiry_company_name'),
                    'no_of_employee' => $m->get_data('no_of_employee'),
                    'feedback_type' => 3,
                    'inquiry_type' =>$m->get_data('inquiry_type'),
                   
                );

                $q = $d->insert("feedback_master", $a);
                $feedback_id = $con->insert_id;

                if ($q == true) {

                   
                    $to = explode(",", $to);

                    $subject = "New ".$d->app_name()." $inquiry_type_view Inquiry From $company_name";
                    include '../apAdmin/mail/requestCompanyEmail.php';
                    include '../apAdmin/mail.php';
                  
                    $admins = $d->select("admin_fcm_notification_master","fcm_notifications=$fcm_type");
                    $total_admins = mysqli_num_rows($admins);
                    if($total_admins>0 && $inquiry_type_view!=""){
                        $noti_title = "New $inquiry_type_view Inquiry";
                        $noti_description = $subject;
                        $click_action = "feedback?feedback_id=$feedback_id";
                        $tokenwhere = "";
                        $ik=0;
                        while($adminsdata = mysqli_fetch_array($admins)){
                            $admin_id = $adminsdata['bms_admin_id'];
                            if($ik>0){ $tokenwhere .= " OR "; }
                            $tokenwhere .= "admin_id=$admin_id";
                            $ik++;
                        }
                        $getTokens = $d->getWebFcm("web_fcm_master","$tokenwhere");
                        $nAdmin->sendAdminNotification($getTokens,$noti_title,$noti_description,$click_action);
                    }
                    $response["message"] = "Thank you for your feedback.";
                    $response["status"] = "200";
                    echo json_encode($response);
                } else {
                    $response["message"] = "wrong data.";
                    $response["status"] = "201";
                    echo json_encode($response);
                }

            /*
             * @deprecated Plain commonApi endpoint — will no longer be used after the next app patch.
             * Use mainApiEnc/contactFincasysTeamController.php (tag: request_app_landing_page) instead.
             */
            }else if($_POST['request_app_landing_page']=="request_app_landing_page") {
                if ($person_mobile=='') {
                    $response["message"] = "Please enter your phone number";
                    $response["status"] = "201";
                    echo json_encode($response);
                    exit();
                }

                $subject = "New ".$d->app_name()." $inquiry_type_view Inquiry From Landing Page ($company_name)";

                $inquiry_type_view = "Sales";
                $fcm_type = 5;
                
                $to = $d->get_inquiry_emails($inquiry_type);

                $company_name = htmlentities($company_name);
                $feedback_msg = htmlentities($feedback_msg);
                $city = htmlentities($city);
                $company_name = ucfirst($company_name);
                $person_name = ucfirst($person_name);
                $enquiry_from = 2;
                $partner_id=$partner_id??'0';
                $m->set_data('platform', $platform);
                $m->set_data('name', $person_name);
                $m->set_data('email', $person_email);
                $m->set_data('mobile', $person_mobile);
                $m->set_data('city', $city);
                $m->set_data('country', $country);
                $m->set_data('country_code', $country);
                $m->set_data('feedback_msg', $feedback_msg);
                $m->set_data('subject', $subject);
                $m->set_data('attachment', $attachment);
                $m->set_data('app_version_code', $app_version_code);
                $m->set_data('device', $device);
                $m->set_data('inquiry_company_name', $company_name);
                $m->set_data('no_of_employee', $no_of_employees);
                $m->set_data('inquiry_type', $inquiry_type);
                $m->set_data('enquiry_from', $enquiry_from);
                $m->set_data('feedback_date_time',  date("d-m-Y H:i"));
                $m->set_data('partner_id', $partner_id);
                
                $a = array(
                    'society_id' => $m->get_data('society_id'),
                    'name' => $m->get_data('name'),
                    'email' => $m->get_data('email'),
                    'mobile' => $m->get_data('mobile'),
                    'city' => $m->get_data('city'),
                    'country_code' => $m->get_data('country_code'),
                    'feedback_msg' => $m->get_data('feedback_msg'),
                    'subject' => $m->get_data('subject'),
                    'attachment' => $m->get_data('attachment'),
                    'feedback_date_time' => $m->get_data('feedback_date_time'),
                    'app_version_code' => $m->get_data('app_version_code'),
                    'device' => $m->get_data('device'),
                    'inquiry_company_name' => $m->get_data('inquiry_company_name'),
                    'no_of_employee' => $m->get_data('no_of_employee'),
                    'feedback_type' => 3,
                    'inquiry_type' =>$m->get_data('inquiry_type'),
                    'enquiry_from' =>$m->get_data('enquiry_from'),
                    'country' =>$m->get_data('country'),
                    'partner_id' =>$m->get_data('partner_id'),
                   
                );

                $q = $d->insert("feedback_master", $a);
                $feedback_id = $con->insert_id;

                if ($q == true) {

                   
                    $to = explode(",", $to);

                    include '../apAdmin/mail/requestCompanyEmail.php';
                    include '../apAdmin/mail.php';
                  
                    $admins = $d->select("admin_fcm_notification_master","fcm_notifications=$fcm_type");
                    $total_admins = mysqli_num_rows($admins);
                    if($total_admins>0 && $inquiry_type_view!=""){
                        $noti_title = "New $inquiry_type_view Inquiry";
                        $noti_description = $subject;
                        $click_action = "feedback?feedback_id=$feedback_id";
                        $tokenwhere = "";
                        $ik=0;
                        while($adminsdata = mysqli_fetch_array($admins)){
                            $admin_id = $adminsdata['bms_admin_id'];
                            if($ik>0){ $tokenwhere .= " OR "; }
                            $tokenwhere .= "admin_id=$admin_id";
                            $ik++;
                        }
                        $getTokens = $d->getWebFcm("web_fcm_master","$tokenwhere");
                        $nAdmin->sendAdminNotification($getTokens,$noti_title,$noti_description,$click_action);
                    }
                    
                    $names = explode(" ", $person_name);
                    $firstName = ucfirst(strtolower($names[0]));
                    $lastName = "";
                    for($i=1; $i<=count($names); $i++){
                        $lastName .= ucfirst(strtolower($names[$i]))." ";
                    }
                    $country = $d->sanitizeActionIdAsInt($country);
                    $country_q = $d->selectRow("name,phonecode","countries","country_id='$country'");
                    $countrydata = mysqli_fetch_array($country_q);
                    $countrycode = $countrydata["phonecode"];
                    $selectedcountry = $countrydata["name"];
                    $countryCode = str_replace("+", "", $countrycode);
                    $finalcode = explode("-", $countryCode);
                    $countryCode = $finalcode[0];
                    $leadata["leads"][0]["firstName"] =  trim($firstName);
                    $leadata["leads"][0]["lastName"] =  trim($lastName);
                    $leadata["leads"][0]["designation"] =  "";
                    $leadata["leads"][0]["email"] =  $person_email;
                    $leadata["leads"][0]["countryCode"] =  $countryCode;
                    $leadata["leads"][0]["mobile"] =  $person_mobile;
                    $leadata["leads"][0]["phoneCountryCode"] =  $countryCode;
                    $leadata["leads"][0]["phone"] =  $person_mobile;
                    $leadata["leads"][0]["expectedRevenue"] =  "";
                    $leadata["leads"][0]["description"] =  $feedback_msg;
                    $leadata["leads"][0]["companyName"] =  $company_name;
                    $leadata["leads"][0]["companyState"] =  "";
                    $leadata["leads"][0]["companyStreet"] =  "";
                    $leadata["leads"][0]["companyCity"] =  $city;
                    $leadata["leads"][0]["companyCountry"] =  $selectedcountry;
                    $leadata["leads"][0]["companyPincode"] =  null;
                    $leadata["leads"][0]["leadPriority"] =  "1";
                    // $leadata["leads"][0]["customFields"]["Home Address"] =  "";
                    // $leadata["leads"][0]["customFields"]["Date Optionals"] =  date("Y-m-d");
                    // $leadata["leads"][0]["customFields"]["Date And Time Opt"] =  date("Y-m-d H:i:s");
                    // $leadata["leads"][0]["customFields"]["Date Only"] =  date("Y-m-d");
                    // $leadata["leads"][0]["customFields"]["Date And Time"] =  date("Y-m-d H:i:s");
                    // $leadata["leads"][0]["customFields"]["Date"] =  date("Y-m-d");
                    // $leadata["leads"][0]["customFields"]["Country1"] =  $selectedcountry;
                    // $leadata["leads"][0]["customFields"]["Ceramic Background"] =  0;
                    // $leadata["leads"][0]["customFields"]["lead email"] =  0;
                    // $leadata["leads"][0]["customFields"]["Date with time"] =  date("Y-m-d H:i:s");
                    // $leadata["leads"][0]["customFields"]["Product"] =  "";
                    // $leadata["leads"][0]["customFields"]["Employee count"] =  $no_of_employees;
                    // echo json_encode($leadata, JSON_PRETTY_PRINT);
                    // echo $crmurl."<br>";
                    // echo $crmauthToken."<br>";
                    if($partner_id == 1){ // USA CRM 
                        $crmauthToken = "HVMsmi9WE5PfJ9B68IFuqg==.Em7ChCXb/i6BdO39sLhSwg==";
                    }
                    $curl1 = curl_init();

                    curl_setopt_array($curl1, array(
                      CURLOPT_URL => $crmurl,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'POST',
                      CURLOPT_POSTFIELDS =>json_encode($leadata),
                      CURLOPT_HTTPHEADER => array(
                        'authToken: '.$crmauthToken,
                        'Content-Type: application/json'
                      ),
                    ));

                    $crmresponse = curl_exec($curl1);
                    $d->update("feedback_master", array("feedback_crm_response"=>$crmresponse),"feedback_id='$feedback_id'");
                    file_put_contents('log.txt', date('Y-m-d H:i:s') . "\n".$crmresponse."\n", FILE_APPEND);
                    $code = curl_getinfo($curl1, CURLINFO_HTTP_CODE);
                    curl_close($curl1);
                    
                    $response["message"] = "Thank you for your enquiry.";
                    $response["status"] = "200";
                    echo json_encode($response);
                } else {
                    $response["message"] = "wrong data.";
                    $response["status"] = "201";
                    echo json_encode($response);
                }

            }else if($_POST['suppoetFincasysGatekeeper']=="suppoetFincasysGatekeeper"){

                $fincasys_contacts=$d->select("fincasys_contacts","fincasys_id=1","");

                if(mysqli_num_rows($fincasys_contacts)>0){

                    $data_fincasys_contacts=mysqli_fetch_array($fincasys_contacts);

                        $response["fincasys_mobile"]=$data_fincasys_contacts['fincasys_mobile_gatekeeper'];
                        $response["fincasys_email"]=$data_fincasys_contacts['fincasys_email'];
                        $response["fincasys_website"]=$data_fincasys_contacts['fincasys_website'];
                        $response["availble_time"]=$data_fincasys_contacts['availble_time'];

                      
                    $response["message"]="Get Team Successfully.";
                    $response["status"]="200";
                    echo json_encode($response);

                }else{

                    $response["message"]="No Team Details Found.";
                    $response["status"]="201";
                    echo json_encode($response);

                }

            } else if($_POST['send_feedback_gatekeeper']=="send_feedback_gatekeeper" && filter_var($society_id, FILTER_VALIDATE_INT) == true) {

                if ($feedback_msg=='') {
                    $response["message"] = "Please enter your message";
                    $response["status"] = "201";
                    echo json_encode($response);
                    exit();
                }


               $maxsize    = 10097152;
               $file11=$_FILES["attachment"]["tmp_name"];
               if (file_exists($file11)) {

                    if(($_FILES['attachment']['size'] >= $maxsize) || ($_FILES["attachment"]["size"] == 0)) {
                        $response["message"]="Attachment too large. Must be less than 10 MB";
                        $response["status"]="201";
                        echo json_encode($response);
                        exit();
                    }

                    $extId = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                    $extAllow=array("mp4","m4a","m4v","f4v","f4a","m4b","m4r","f4b","mov","3gp","avi","png","jpg","jpeg","gif","JPG","JPEG","PNG");
                    if(in_array($extId,$extAllow)) {
                       $temp = explode(".", $_FILES["attachment"]["name"]);
                        $attachment = "Support_".$name.round(microtime(true)) . '.' . end($temp);
                        move_uploaded_file($_FILES["attachment"]["tmp_name"], "../img/fin_support/" . $attachment);
                    } else {
                      $response["message"]="Invalid Attachment only Photo & Video are allowed.";
                      $response["status"]="201";
                      echo json_encode($response);
                      exit();
                    }
                  } else {
                     $attachment="";
                 }
                 $name = $name . ' (Gatekeeer)';
                 $platform = ($device=='iOS') ? 2 : 1;
                $m->set_data('society_id', $society_id);
                $m->set_data('platform', $platform);
                $m->set_data('name', $name);
                $m->set_data('email', $email);
                $m->set_data('mobile', $mobile);
                $m->set_data('feedback_msg', $feedback_msg);
                $m->set_data('subject', "Message From Gatekeepr");
                $m->set_data('attachment', $attachment);
                $m->set_data('app_version_code', $app_version_code);
                $m->set_data('device', $device);
                $m->set_data('feedback_date_time',  date("d-m-Y H:i"));
                
                $a = array(
                    'society_id' => $m->get_data('society_id'),
                    'platform' => $m->get_data('platform'),
                    'name' => $m->get_data('name'),
                    'email' => $m->get_data('email'),
                    'mobile' => $m->get_data('mobile'),
                    'feedback_msg' => $m->get_data('feedback_msg'),
                    'subject' => $m->get_data('subject'),
                    'attachment' => $m->get_data('attachment'),
                    'feedback_date_time' => $m->get_data('feedback_date_time'),
                    'app_version_code' => $m->get_data('app_version_code'),
                    'device' => $m->get_data('device'),
                   
                );

                $q = $d->insert("feedback_master", $a);


                if ($q == true) {
                  
                    $response["message"] = "Thank you for your feedback.";
                    $response["status"] = "200";
                    echo json_encode($response);
                } else {
                    $response["message"] = "wrong data.";
                    $response["status"] = "201";
                    echo json_encode($response);
                }

            } else if($_POST['send_feedback_admin']=="send_feedback_admin" && filter_var($society_id, FILTER_VALIDATE_INT) == true) {

                if ($feedback_msg=='') {
                    $response["message"] = "Please enter your message";
                    $response["status"] = "201";
                    echo json_encode($response);
                    exit();
                }


               $maxsize    = 10097152;
               $file11=$_FILES["file_contents"]["tmp_name"];
               if (file_exists($file11)) {

                    if(($_FILES['file_contents']['size'] >= $maxsize) || ($_FILES["file_contents"]["size"] == 0)) {
                        $response["message"]="Attachment photo is too large. Must be less than 10 MB";
                        $response["status"]="201";
                        echo json_encode($response);
                        exit();
                    }
                    $extId = pathinfo($_FILES['file_contents']['name'], PATHINFO_EXTENSION);
                    $extAllow=array("jpeg","jpg","png","gif",'webp');
                    $extId=strtolower($extId);
                    if(in_array($extId,$extAllow)) {
                       $temp = explode(".", $_FILES["file_contents"]["name"]);
                        $attachment = "Support_".$mobile.round(microtime(true)) . '.' .$extId;
                        move_uploaded_file($_FILES["file_contents"]["tmp_name"], "../img/fin_support/" . $attachment);
                    } else {
                      $response["message"]="Invalid Attachment only Photo are allowed.";
                      $response["status"]="201";
                      echo json_encode($response);
                      exit();
                    }
                   
                  } else {
                     $attachment="";
                 }

                
                $maxsizeVideo    = 30097152;
                $file33=$_FILES["video"]["tmp_name"];
                 if (file_exists($file33)) {

                      if(($_FILES['video']['size'] >= $maxsizeVideo) || ($_FILES["video"]["size"] == 0)) {
                        $response["message"]="video too large. Must be less than 30 MB";
                        $response["status"]="201";
                        echo json_encode($response);
                        exit();
                      }

                      $extId = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
                      $extAllow=array('mov', 'mp4', 'webm','flv','avi');
                      $extId=strtolower($extId);
                      if(in_array($extId,$extAllow)) {
                         $temp = explode(".", $_FILES["video"]["name"]);
                          $video = "Support_3_".$mobile.round(microtime(true)) . '.' . end($temp);
                          move_uploaded_file($_FILES["video"]["tmp_name"], "../img/fin_support/" . $video);
                      } else {
                      $response["message"]="Invalid Video Attachment only Video are allowed.";
                      $response["status"]="201";
                      echo json_encode($response);
                      exit();
                      }
                    } else {
                       $video="";
                   }

                $file44=$_FILES["document"]["tmp_name"];
                  if(file_exists($file44))
                  {
                    if(($_FILES['document']['size'] >= $maxsizeVideo) || ($_FILES["document"]["size"] == 0)) {
                      $_SESSION['msg1']="document too large. Must be less than 30 MB";
                      header("Location: ../feedback");
                      exit();
                    }
                    $extId = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
                    $extAllow=array("txt","xls","csv","pdf","docx","odt","ods","xlsx");
                    $extId=strtolower($extId);
                    if(in_array($extId,$extAllow)) {
                        $temp = explode(".", $_FILES["document"]["name"]);
                        $document = "Support_3_".$mobile.round(microtime(true)) . '.' . end($temp);
                        move_uploaded_file($_FILES["document"]["tmp_name"], "../img/fin_support/" . $document);
                    } else {
                      $response["message"]="Invalid Document Type";
                      $response["status"]="201";
                      echo json_encode($response);
                      exit();
                    }
                  }else{
                    $document="";
                  }


                 $platform =   ($platform!="") ? $platform : 3;
                $name = $name . ' (Admin)';
                $m->set_data('society_id', $society_id);
                $m->set_data('platform', $platform);
                $m->set_data('name', $name);
                $m->set_data('email', $email);
                $m->set_data('mobile', $mobile);
                $m->set_data('feedback_msg', $feedback_msg);
                $m->set_data('subject', $subject .'(Admin)');
                $m->set_data('attachment', $attachment);
                $m->set_data('video', $video);
                $m->set_data('document', $document);
                $m->set_data('app_version_code', "");
                $m->set_data('device', 'Admin Panel');
                $m->set_data('feedback_date_time',  date("d-m-Y H:i"));
                
                $a = array(
                    'society_id' => $m->get_data('society_id'),
                    'platform' => $m->get_data('platform'),
                    'name' => $m->get_data('name'),
                    'email' => $m->get_data('email'),
                    'mobile' => $m->get_data('mobile'),
                    'feedback_msg' => $m->get_data('feedback_msg'),
                    'subject' => $m->get_data('subject'),
                    'attachment' => $m->get_data('attachment'),
                    'video' => $m->get_data('video'),
                    'document' => $m->get_data('document'),
                    'feedback_date_time' => $m->get_data('feedback_date_time'),
                    'app_version_code' => $m->get_data('app_version_code'),
                    'device' => $m->get_data('device'),
                   
                );
                $q = $d->insert("feedback_master", $a);
                $feedback_id = $con->insert_id;

                if ($q == true) {
                  
                    $admins = $d->select("admin_fcm_notification_master","fcm_notifications=2");
                    $total_admins = mysqli_num_rows($admins);
                    if($total_admins>0){
                         $noti_title = "New Feedback From $name ";
                        $noti_description = $subject;
                        $click_action = "feedback?feedback_id=$feedback_id";
                        $tokenwhere = "";
                        $ik=0;
                        while($adminsdata = mysqli_fetch_array($admins)){
                            $admin_id = $adminsdata['bms_admin_id'];
                            if($ik>0){ $tokenwhere .= " OR "; }
                            $tokenwhere .= "admin_id=$admin_id";
                            $ik++;
                        }
                        $getTokens = $d->getWebFcm("web_fcm_master","$tokenwhere");
                        $nAdmin->sendAdminNotification($getTokens,$noti_title,$noti_description,$click_action);
                    }
                    $response["message"] = "Thank you for contacting us, we will reach back to you in a short time.";
                    $response["status"] = "200";
                    echo json_encode($response);
                } else {
                    $response["message"] = "wrong data.";
                    $response["status"] = "201";
                    echo json_encode($response);
                }

            } else if(isset($replyFeedback) && $replyFeedback=='replyFeedback'){

                $feedback_id = $d->sanitizeActionIdAsInt($feedback_id);
                $user_mobile = $d->escapeSqlString($user_mobile ?? '');
                $country_code = $d->escapeSqlString($country_code ?? '');
                $q=$d->select("feedback_master","feedback_id='$feedback_id' AND mobile='$user_mobile' AND country_code='$country_code'","");
                if (mysqli_num_rows($q)==0) {
                   $response["message"] = "Invalid Request.";
                    $response["status"] = "201";
                    echo json_encode($response);
                    exit();
                }

                $maxsize    = 10097152;
                // $acceptable_image = array('image/jpeg','image/jpg','image/png','image/gif','image/JPG','image/JPEG','image/PNG','image/docx','image/mp4','image/csv');
                $file11=$_FILES["attachment"]["tmp_name"];
                if(file_exists($file11))
                {
                  if(($_FILES['attachment']['size'] >= $maxsize) || ($_FILES["attachment"]["size"] == 0)) {
                    $response["message"]="Attachment too large. Must be less than 10 MB";
                    $response["status"] = "201";
                    echo json_encode($response);
                  }
                  // if(!in_array($file['type'], $acceptable_image) && (!empty($file["type"])))
                  // {
                  //   $data = implode(',', $acceptable_image);
                  //   $data1 = str_replace("image/"," ",$data);
                  //   $_SESSION['msg1']="Invalid  photo. Only ".$data1." are allowed.";
                  //   header("location:../feedback");
                  //   exit();
                  // }
                  $temp = explode(".", $_FILES["attachment"]["name"]);
                  $attachment = "Support_1_".$name.round(microtime(true)) . '.' . end($temp);
                  move_uploaded_file($_FILES["attachment"]["tmp_name"], "../img/fin_support/" . $attachment);
                } else {
                  $attachment="";
                }

                $msg = "Ticket #TKT00$feedback_id Reply";
                  
                $m->set_data('feedback_id',$feedback_id);
                $m->set_data('society_id',$society_id);
                $m->set_data('feedback_log', $msg);
                $m->set_data('feedback_log_msg', $feedback_log_msg);
                $m->set_data('feedback_log_attachment',$attachment);
                $m->set_data('feedback_log_date',date('Y-m-d H:i:s'));
                $m->set_data('log_added_type',1);
                $m->set_data('user_mobile',$user_mobile);
                $m->set_data('client_name',$client_name);
                $m->set_data('country_code',$country_code);
                $m->set_data('admin_name',$admin_name);

                $a1  =array(
                  'feedback_id'=> $m->get_data('feedback_id'),
                  'society_id'=> $m->get_data('society_id'),
                  'feedback_log'=> $m->get_data('feedback_log'),
                  'feedback_log_msg'=> $m->get_data('feedback_log_msg'),
                  'feedback_log_attachment'=> $m->get_data('feedback_log_attachment'),
                  'feedback_log_date'=> $m->get_data('feedback_log_date'),
                  'log_added_type'=> $m->get_data('log_added_type'),
                  'user_mobile'=> $m->get_data('user_mobile'),
                  'client_name'=> $m->get_data('client_name'),
                  'country_code'=> $m->get_data('country_code'),
                  'client_name'=> $m->get_data('admin_name'),
                );
                $d->insert("feedback_log_master",$a1);


                $response["message"] = "Reply Sent Successfully";
                $response["status"] = "200";
                echo json_encode($response);
                exit();

            } else if($_POST['send_feedback_guest']=="send_feedback_guest" ) {

                if ($feedback_msg=='') {
                    $response["message"] = "Please enter your message";
                    $response["status"] = "201";
                    echo json_encode($response);
                    exit();
                }

                $platform = 3;
                $name = $name . ' (Website Visitor)';
                $m->set_data('society_id', $society_id);
                $m->set_data('platform', $platform);
                $m->set_data('name', $name);
                $m->set_data('email', $email);
                $m->set_data('website_name', $website_name);
                $m->set_data('mobile', $mobile);
                $m->set_data('feedback_msg', $feedback_msg);
                $m->set_data('subject', $subject .'(Admin)');
                $m->set_data('attachment', $attachment);
                $m->set_data('app_version_code', "");
                $m->set_data('device', 'Admin Panel');
                $m->set_data('feedback_date_time',  date("d-m-Y H:i"));
                
                $a = array(
                    'society_id' => $m->get_data('society_id'),
                    'platform' => $m->get_data('platform'),
                    'name' => $m->get_data('name'),
                    'email' => $m->get_data('email'),
                    'mobile' => $m->get_data('mobile'),
                    'feedback_msg' => $m->get_data('feedback_msg'),
                    'subject' => $m->get_data('subject'),
                    'attachment' => $m->get_data('attachment'),
                    'feedback_date_time' => $m->get_data('feedback_date_time'),
                    'app_version_code' => $m->get_data('app_version_code'),
                    'device' => $m->get_data('device'),
                    'website_name' => $m->get_data('website_name'),
                   
                );
                $q = $d->insert("feedback_master", $a);


                if ($q == true) {
                  
                    $response["message"] = "Thank you for your feedback.";
                    $response["status"] = "200";
                    echo json_encode($response);
                } else {
                    $response["message"] = "wrong data.";
                    $response["status"] = "201";
                    echo json_encode($response);
                }

            } 
            // remove after patch in mcyo
            else if($_POST['addPaymentGetwatRequest']=="addPaymentGetwatRequest" && filter_var($society_id, FILTER_VALIDATE_INT) == true) {


                $m->set_data('society_id', $society_id);
                $m->set_data('payment_getway_name', $payment_getway_name);
                $m->set_data('contact_name', $contact_name);
                $m->set_data('contact_mobile', $contact_mobile);
                $m->set_data('email_id', $email_id);
                $m->set_data('registrationProof', $registrationProof);
                $m->set_data('panCard', $panCard);
                $m->set_data('gstCerty', $gstCerty);
                $m->set_data('cancelledCheque', $cancelledCheque);
                $m->set_data('authorizedAddress', $authorizedAddress);
                $m->set_data('authorizedPancard', $authorizedPancard);
                $m->set_data('created_by', $created_by);
                $m->set_data('created_date',  date("Y-m-d H:i:s"));
                
                $a = array(
                    'society_id' => $m->get_data('society_id'),
                    'payment_getway_name' => $m->get_data('payment_getway_name'),
                    'contact_name' => $m->get_data('contact_name'),
                    'contact_mobile' => $m->get_data('contact_mobile'),
                    'email_id' => $m->get_data('email_id'),
                    'registrationProof' => $m->get_data('registrationProof'),
                    'panCard' => $m->get_data('panCard'),
                    'gstCerty' => $m->get_data('gstCerty'),
                    'cancelledCheque' => $m->get_data('cancelledCheque'),
                    'authorizedAddress' => $m->get_data('authorizedAddress'),
                    'authorizedPancard' => $m->get_data('authorizedPancard'),
                    'created_date' => $m->get_data('created_date'),
                    'created_by' => $m->get_data('created_by'),
                   
                );

                $payment_getway_name = $d->escapeSqlString($payment_getway_name ?? '');
                $society_id = $d->sanitizeActionIdAsInt($society_id);
                $qq=$d->selectRow("requiest_id","payment_gateway_requiest_master","payment_getway_name='$payment_getway_name' AND   current_status=0 AND society_id='$society_id'");
                if(mysqli_num_rows($qq)>0){
                    $response["message"] = "Payment Request Already Sent.";
                    $response["status"] = "202";
                    echo json_encode($response);
                    exit();
                }

                $q = $d->insert("payment_gateway_requiest_master", $a);
                $requiest_id = $con->insert_id;

                if ($q == true) {

                    $aLog = array(
                        'requiest_id' => $requiest_id,
                        'society_id' => $m->get_data('society_id'),
                        'log_name' => 'Payment Gateway Request Send',
                        'log_time' => $m->get_data('created_date'),
                        'user_name' => $m->get_data('created_by'),
                    );
                    
                    $d->insert("payment_gateway_requiest_log_master", $aLog);

                    $response["message"] = "Payment Gateway Request Send Successfully !";
                    $response["status"] = "200";
                    echo json_encode($response);
                } else {
                    $response["message"] = "Something Wrong, Please contact to support team.";
                    $response["status"] = "201";
                    echo json_encode($response);
                }

            }else if($_POST['changeStausPaymentStatus']=="changeStausPaymentStatus" && filter_var($society_id, FILTER_VALIDATE_INT) == true && filter_var($requiest_id, FILTER_VALIDATE_INT) == true) {


                $m->set_data('society_id', $society_id);
                $m->set_data('requiest_id', $requiest_id);
                $m->set_data('user_name', $user_name);
                $m->set_data('file_name', $file_name);
                $m->set_data('log_name', $log_name);
                $m->set_data('user_name', $user_name);
                $m->set_data('created_date',  date("Y-m-d H:i:s"));

                 $aLog = array(
                        'requiest_id' => $m->get_data('requiest_id'),
                        'society_id' => $m->get_data('society_id'),
                        'log_name' => $m->get_data('log_name'),
                        'log_time' => $m->get_data('created_date'),
                        'user_name' => $m->get_data('user_name'),
                        'file_name' => $m->get_data('file_name'),
                    );
                
                $q =$d->insert("payment_gateway_requiest_log_master", $aLog);
                
                
                if ($q == true) {
                   
                    $response["message"] = "Reply Send Successfully !";
                    $response["status"] = "200";
                    echo json_encode($response);
                } else {
                    $response["message"] = "Something Wrong, Please contact to support team.";
                    $response["status"] = "201";
                    echo json_encode($response);
                }

            } else if($_POST['getPaymentGetwatRequest']=="getPaymentGetwatRequest" && filter_var($society_id, FILTER_VALIDATE_INT) == true) {

                $q=$d->select("payment_gateway_requiest_master","society_id='$society_id'","ORDER BY requiest_id DESC");
                if(mysqli_num_rows($q)>0){
                    $response["payment_gateway_requiest"] = array();

                    while($data=mysqli_fetch_array($q)) {

                            $payment_gateway_requiest = array(); 
                            $payment_gateway_requiest["requiest_id"]=$data['requiest_id'];
                            $payment_gateway_requiest["society_id"]=$data['society_id'];
                            $payment_gateway_requiest["payment_getway_name"]=$data['payment_getway_name'];
                            $payment_gateway_requiest["contact_name"]=$data['contact_name'];
                            $payment_gateway_requiest["contact_mobile"]=$data['contact_mobile'];
                            $payment_gateway_requiest["email_id"]=$data['email_id'];
                            $payment_gateway_requiest["registrationProof"]=$data['registrationProof'];
                            $payment_gateway_requiest["panCard"]=$data['panCard'];
                            $payment_gateway_requiest["gstCerty"]=$data['gstCerty'];
                            $payment_gateway_requiest["cancelledCheque"]=$data['cancelledCheque'];
                            $payment_gateway_requiest["authorizedAddress"]=$data['authorizedAddress'];
                            $payment_gateway_requiest["authorizedPancard"]=$data['authorizedPancard'];
                            $payment_gateway_requiest["created_date"]=$data['created_date'];
                            $payment_gateway_requiest["created_by"]=$data['created_by'];
                            $payment_gateway_requiest["merchant_id"]=$data['merchant_id'];
                            $payment_gateway_requiest["merchant_key"]=$data['merchant_key'];
                            $payment_gateway_requiest["salt_key"]=$data['salt_key'];
                            $payment_gateway_requiest["is_upi"]=$data['is_upi'];
                            switch ($data['current_status']) {
                                case '1':
                                   $current_status = "Under Process";
                                    break;
                                case '2':
                                   $current_status = "Rejected";
                                    break;
                                case '3':
                                   $current_status = "Completed";
                                    break;
                                case '4':
                                   $current_status = "On Hold";
                                    break;
                                default:
                                   $current_status = "Pending";
                                    break;
                            }
                            $payment_gateway_requiest["current_status"]=$current_status;
                            $payment_gateway_requiest["current_status_int"]=$data['current_status'];

                            array_push($response["payment_gateway_requiest"], $payment_gateway_requiest);


                    }
                    $response["message"]="Get Request Success.";
                    $response["status"]="200";
                    echo json_encode($response);

                }else{

                    $response["message"]="No Request Found.";
                    $response["status"]="201";
                    echo json_encode($response);

                }

            }else if($_POST['getPaymentGetwatRequestDetails']=="getPaymentGetwatRequestDetails" && filter_var($society_id, FILTER_VALIDATE_INT) == true && filter_var($requiest_id, FILTER_VALIDATE_INT) == true) {

                $q=$d->select("payment_gateway_requiest_master","society_id='$society_id' AND requiest_id='$requiest_id'","");
                if(mysqli_num_rows($q)>0){
                    $data=mysqli_fetch_array($q);

                    $q1=$d->select("payment_gateway_requiest_log_master","society_id='$society_id' AND requiest_id='$requiest_id'","ORDER BY requiest_log_id DESC");
                    $response["payment_gateway_requiest"] = array();
                    while($data11=mysqli_fetch_array($q1)) {

                        $payment_gateway_requiest = array(); 
                        $payment_gateway_requiest["requiest_id"]=$data11['requiest_id'];
                        $payment_gateway_requiest["society_id"]=$data11['society_id'];
                        $payment_gateway_requiest["log_name"]=$data11['log_name'];
                        $payment_gateway_requiest["log_time"]=$data11['log_time'];
                        $payment_gateway_requiest["admin_name"]=$data11['admin_name'];
                        $payment_gateway_requiest["user_name"]=$data11['user_name'];
                        $payment_gateway_requiest["file_name"]=$data11['file_name'];
                        array_push($response["payment_gateway_requiest"], $payment_gateway_requiest);


                    }


                    $response["requiest_id"]=$data['requiest_id'];
                    $response["society_id"]=$data['society_id'];
                    $response["payment_getway_name"]=$data['payment_getway_name'];
                    $response["contact_name"]=$data['contact_name'];
                    $response["contact_mobile"]=$data['contact_mobile'];
                    $response["email_id"]=$data['email_id'];
                    $response["registrationProof"]=$data['registrationProof'];
                    $response["panCard"]=$data['panCard'];
                    $response["gstCerty"]=$data['gstCerty'];
                    $response["cancelledCheque"]=$data['cancelledCheque'];
                    $response["authorizedAddress"]=$data['authorizedAddress'];
                    $response["authorizedPancard"]=$data['authorizedPancard'];
                    $response["created_date"]=$data['created_date'];
                    $response["created_by"]=$data['created_by'];
                    $response["merchant_id"]=$data['merchant_id'];
                    $response["merchant_key"]=$data['merchant_key'];
                    $response["salt_key"]=$data['salt_key'];
                    $response["is_upi"]=$data['is_upi'];
                    switch ($data['current_status']) {
                        case '1':
                           $current_status = "Under Process";
                            break;
                        case '2':
                           $current_status = "Rejected";
                            break;
                        case '3':
                           $current_status = "Completed";
                            break;
                        case '4':
                           $current_status = "On Hold";
                            break;
                        default:
                           $current_status = "Pending";
                            break;
                    }
                    $response["current_status"]=$current_status;
                    $response["current_status_int"]=$data['current_status'];

                    $response["message"]="Get Request Success.";
                    $response["status"]="200";
                    echo json_encode($response);

                }else{

                    $response["message"]="No Request Found.";
                    $response["status"]="201";
                    echo json_encode($response);

                }

            } else if($_POST['deletePaymentGetwatRequest']=="deletePaymentGetwatRequest" && filter_var($society_id, FILTER_VALIDATE_INT) == true && filter_var($requiest_id, FILTER_VALIDATE_INT) == true) {

                $q = $d->delete("payment_gateway_requiest_master","requiest_id='$requiest_id' AND society_id='$society_id' AND current_status=0 ");

                if ($q == true) {
                  
                    $response["message"] = "Payment Gateway Request Deleted Successfully !";
                    $response["status"] = "200";
                    echo json_encode($response);
                } else {
                    $response["message"] = "Something Wrong, Please contact to support team.";
                    $response["status"] = "201";
                    echo json_encode($response);
                }

            } 
            // remove after patch in mcyo end
            else {
                $response["message"]="wrong tag.";
                $response["status"]="201";
                echo json_encode($response);

            }

}?>