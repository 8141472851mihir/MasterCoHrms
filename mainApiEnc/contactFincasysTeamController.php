<?php
include_once 'lib.php';
include_once '../apAdmin/lib/crmconfig.php';
$_POST =  json_decode($d->manage_decryption("1", file_get_contents("php://input")),true);
$compress = resolve_api_compress();
// $_POST =  $d->manage_encryption("1", $_POST);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted,[
        "status" => "201",
        "message" => $xml->string->invalid_request.''
    ], $compress);
    exit();
}
try {
    if(isset($_POST) && !empty($_POST)){

        $response = array();

        extract(array_map("test_input" , $_POST));
            
                if($_POST['getTeamDetails']=="getTeamDetails"){

                    if ($country_id!="") {
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
                        $response["message"]=$xml->string->get_team_successfully.'';
                        $response["status"]="200";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();

                    }else{

                        $response["message"]=$xml->string->no_team_details_found.'';
                        $response["status"]="201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();

                    }

                } else if($_POST['send_feedback']=="send_feedback") {

                    if ($feedback_msg=='') {
                        $response["message"]=$xml->string->please_enter_message.'';
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                        
                    }

                        //  attachmentImg1 start
                        $source_image = '../img/temp_images/' . $attachmentImg1;
                        $maxsize    = 10097152;
                        $allowedExtensions = ["jpeg", "jpg", "png", "gif", "webp"];
                        $imageurl="";
                        if (file_exists($source_image) && $attachmentImg1!="") {
                            if (filesize($source_image) > $maxsize) {
                                $response["message"]=$xml->string->photo_too_large_must_be_less_than_ten_mb.'';
                                $response["status"] = "201";
                                echo $d->manage_encryption($is_encrypted, $response, $compress);
                                exit();
                            }
                            $fileExtension = strtolower(pathinfo($source_image, PATHINFO_EXTENSION));
                            if (in_array($fileExtension, $allowedExtensions)) {
                                $uniqueName = "Support_1_" . $mobile . round(microtime(true)) . '.' . $fileExtension;
                                define('UPLOAD_DIR', '../img/fin_support/');
                                $destination_path = UPLOAD_DIR . $uniqueName;
                                if (rename($source_image, $destination_path)) {
                                    $imageurl = $base_url . 'img/fin_support/' . $uniqueName;
                                    $attachmentImg1=$uniqueName;
                                }
                            } else {
                                $response["message"]=$xml->string->invalid_image_type.'';
                                $response["status"] = "201";
                                echo $d->manage_encryption($is_encrypted, $response, $compress);
                                exit();
                            }
                        } else {
                            $attachmentImg1="";
                        }
                         //  end

                        //  attachmentImg2 start
                        $source_image = '../img/temp_images/' . $attachmentImg2;
                        if (file_exists($source_image) && $attachmentImg2 !="") {
                            if (filesize($source_image) > $maxsize) {
                                $response["message"]=$xml->string->photo_too_large_must_be_less_than_ten_mb.'';
                                $response["status"] = "201";
                                echo $d->manage_encryption($is_encrypted, $response, $compress);
                                exit();
                            }
                            $fileExtension = strtolower(pathinfo($source_image, PATHINFO_EXTENSION));
                            if (in_array($fileExtension, $allowedExtensions)) {
                                $uniqueName = "Support_2_" . $mobile . round(microtime(true)) . '.' . $fileExtension;
                                define('UPLOAD_DIR', '../img/fin_support/');
                                $destination_path = UPLOAD_DIR . $uniqueName;
                                if (rename($source_image, $destination_path)) {
                                    $imageurl = $base_url . 'img/fin_support/' . $uniqueName;
                                    $attachmentImg2=$uniqueName;

                                }
                            } else {
                                $response["message"]=$xml->string->invalid_image_type.'';
                                $response["status"] = "201";
                                echo $d->manage_encryption($is_encrypted, $response, $compress);
                                exit();
                            }
                        } else {
                            $attachmentImg2="";
                        }
                         //  end
                        //  video start
                        $maxsizeVideo    = 30097152;
                        $extAllow=array('mov', 'mp4', 'webm');
                        $source_image = '../img/temp_images/' . $video;
                        if (file_exists($source_image) && $video!="") {
                            if (filesize($source_image) > $maxsizeVideo) {
                                $response["message"]=$xml->string->file_must_be_less_than_thirty_mb.'';
                                $response["status"] = "201";
                                echo $d->manage_encryption($is_encrypted, $response, $compress);
                                exit();
                            }
                            $fileExtension = strtolower(pathinfo($source_image, PATHINFO_EXTENSION));
                            if (in_array($fileExtension, $extAllow)) {
                                $uniqueName = "Support_3_" . $mobile . round(microtime(true)) . '.' . $fileExtension;
                                define('UPLOAD_DIR', '../img/fin_support/');
                                $destination_path = UPLOAD_DIR . $uniqueName;
                                if (rename($source_image, $destination_path)) {
                                    // $imageurl = $base_url . 'img/fin_support/' . $uniqueName;
                                    $video=$uniqueName;

                                }
                            } else {
                                $response["message"]=$xml->string->invalid_file_type.'';
                                $response["status"] = "201";
                                echo $d->manage_encryption($is_encrypted, $response, $compress);
                                exit();
                            }
                        } else {
                            $video="";
                        }
                         //  end
                        //  document start
                        $maxsizeDocument    = 30097152;
                        $extAllow=array("txt","xls","csv","pdf","docx","odt","ods","xlsx");
                        $source_image = '../img/temp_images/' . $document;
                        if (file_exists($source_image) && $document!="") {
                            if (filesize($source_image) > $maxsizeDocument) {
                                $response["message"]=$xml->string->file_must_be_less_than_thirty_mb.'';
                                $response["status"] = "201";
                                echo $d->manage_encryption($is_encrypted, $response, $compress);
                                exit();
                            }
                            $fileExtension = strtolower(pathinfo($source_image, PATHINFO_EXTENSION));
                            if (in_array($fileExtension, $extAllow)) {
                                $uniqueName = "Support_3_" . $mobile . round(microtime(true)) . '.' . $fileExtension;
                                define('UPLOAD_DIR', '../img/fin_support/');
                                $destination_path = UPLOAD_DIR . $uniqueName;
                                // $imageurl = "";
                                if (rename($source_image, $destination_path)) {
                                    // $imageurl = $base_url . 'img/fin_support/' . $uniqueName;
                                    $document=$uniqueName;
                                    
                                }
                            } else {
                                $response["message"]=$xml->string->invalid_file_type.'';
                                $response["status"] = "201";
                                echo $d->manage_encryption($is_encrypted, $response, $compress);
                                exit();
                            }
                        } else {
                            $document="";
                        }
                         //  end

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


                    if ($q == true) {
                        $admins = $d->select("admin_fcm_notification_master","fcm_notifications=1");
                        $total_admins = mysqli_num_rows($admins);
                        if($total_admins>0){
                            $noti_title = "New Feedback From $name ";
                            $noti_description = $subject;
                            $click_action = "feedback";
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


                        $response["message"]=$xml->string->thank_you_for_contact_reach_back_to_you.'';
                        $response["status"] = "200";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    } else {
                        $response["message"] = $xml->string->something_wrong.'';
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    }

                }else if($_POST['send_feedback_sp']=="send_feedback_sp") {

                    if ($feedback_msg=='') {
                        $response["message"] = $xml->string->please_enter_message.'';
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                        
                    }


                   $maxsize    = 10097152;
                   $file11=$_FILES["attachment"]["tmp_name"];
                   if (file_exists($file11)) {

                        if(($_FILES['attachment']['size'] >= $maxsize) || ($_FILES["attachment"]["size"] == 0)) {
                            $response["message"]=$xml->string->photo_too_large_must_be_less_than_ten_mb.'';
                            $response["status"]="201";
                            echo $d->manage_encryption($is_encrypted, $response, $compress);
                            exit();
                            
                        }

                        $extId = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                        $extAllow=array("mp4","m4a","m4v","f4v","f4a","m4b","m4r","f4b","mov","3gp","avi","png","jpg","jpeg","gif","JPG","JPEG","PNG","PDF","pdf","Pdf","Doc","DOC","doc","ppt","PPT","Ppt","XLS","Xls","xls");
                        if(in_array($extId,$extAllow)) {
                           $temp = explode(".", $_FILES["attachment"]["name"]);
                            $attachment = "Support_".$name.round(microtime(true)) . '.' . end($temp);
                            move_uploaded_file($_FILES["attachment"]["tmp_name"], "../img/fin_support/" . $attachment);
                        } else {
                            $response["message"]=$xml->string->invalid_file_type.'';
                            $response["status"]="201";
                            echo $d->manage_encryption($is_encrypted, $response, $compress);
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
                      
                        $response["message"] = $xml->string->thank_you_for_your_feedback.'';
                        $response["status"] = "200";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    } else {
                        $response["message"] = $xml->string->something_wrong.'';

                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    }

                }else if($_POST['request_society']=="request_society") {

                    if ($person_mobile=='') {
                        $response["message"] = $xml->string->please_enter_your_phone_number.'';
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                        
                    }
                    $company_name_clean = html_entity_decode($company_name);
                    if ($is_landing_page==1) {
                        $subject = "New ".$d->app_name()." $inquiry_type_view Inquiry From Landing Page ($company_name_clean)";
                    } else {
                        $subject = "New ".$d->app_name()." $inquiry_type_view Inquiry From App ($company_name_clean Company Not Found)";
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
                    $m->set_data('subject', "New ".$d->app_name()." $inquiry_type_view Inquiry From App ($company_name_clean Company Not Found)");
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


                    if ($q == true) {

                       
                        $to = explode(",", $to);

                        $subject = "New ".$d->app_name()." $inquiry_type_view Inquiry From $company_name_clean";
                        include '../apAdmin/mail/requestCompanyEmail.php';
                        include '../apAdmin/mail.php';
                      
                        $admins = $d->select("admin_fcm_notification_master","fcm_notifications=$fcm_type");
                        $total_admins = mysqli_num_rows($admins);
                        if($total_admins>0 && $inquiry_type_view!=""){
                            $noti_title = "New $inquiry_type_view Inquiry";
                            $noti_description = $subject;
                            $click_action = "feedback";
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
                        $countryCode = str_replace("+", "", $country_code);
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
                        $leadata["leads"][0]["description"] =  "Requested Company";
                        $leadata["leads"][0]["companyName"] =  $company_name;
                        $leadata["leads"][0]["companyState"] =  "";
                        $leadata["leads"][0]["companyStreet"] =  $address;
                        $leadata["leads"][0]["companyCity"] =  "";
                        $leadata["leads"][0]["companyCountry"] =  "";
                        $leadata["leads"][0]["companyPincode"] =  "";
                        $leadata["leads"][0]["leadPriority"] =  "1";
                        // $leadata["leads"][0]["customFields"]["Home Address"] =  "";
                        // $leadata["leads"][0]["customFields"]["Date Optionals"] =  date("Y-m-d");
                        // $leadata["leads"][0]["customFields"]["Date And Time Opt"] =  date("Y-m-d H:i:s");
                        // $leadata["leads"][0]["customFields"]["Date Only"] =  date("Y-m-d");
                        // $leadata["leads"][0]["customFields"]["Date And Time"] =  date("Y-m-d H:i:s");
                        // $leadata["leads"][0]["customFields"]["Date"] =  date("Y-m-d");
                        // $leadata["leads"][0]["customFields"]["Country1"] =  "";
                        // $leadata["leads"][0]["customFields"]["Ceramic Background"] =  0;
                        // $leadata["leads"][0]["customFields"]["lead email"] =  0;
                        // $leadata["leads"][0]["customFields"]["Date with time"] =  date("Y-m-d H:i:s");
                        // $leadata["leads"][0]["customFields"]["Product"] =  "";
                        // $leadata["leads"][0]["customFields"]["Employee count"] =  $no_of_employees;
                        // echo json_encode($leadata, JSON_PRETTY_PRINT);
                        // echo $crmurl."<br>";
                        // echo $crmauthToken."<br>";
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
                        $code = curl_getinfo($curl1, CURLINFO_HTTP_CODE);
                        curl_close($curl1);
                        $response["message"] = $xml->string->thank_you_for_your_feedback.'';
                        $response["status"] = "200";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    } else {
                        $response["message"] = $xml->string->something_wrong.'';

                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    }

                /*
                 * Encrypted replacement for commonApi/contact_fincasysteam_controller.php → request_app_landing_page.
                 * Apps should migrate here; plain commonApi handler will be removed after the next patch.
                 */
                }else if($_POST['request_app_landing_page']=="request_app_landing_page") {

                    if ($person_mobile=='') {
                        $response["message"] = "Please enter your phone number";
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
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
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    } else {
                        $response["message"] = "wrong data.";
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    }

                }else if($_POST['suppoetFincasysGatekeeper']=="suppoetFincasysGatekeeper"){

                    $fincasys_contacts=$d->select("fincasys_contacts","fincasys_id=1","");

                    if(mysqli_num_rows($fincasys_contacts)>0){

                        $data_fincasys_contacts=mysqli_fetch_array($fincasys_contacts);

                            $response["fincasys_mobile"]=$data_fincasys_contacts['fincasys_mobile_gatekeeper'];
                            $response["fincasys_email"]=$data_fincasys_contacts['fincasys_email'];
                            $response["fincasys_website"]=$data_fincasys_contacts['fincasys_website'];
                            $response["availble_time"]=$data_fincasys_contacts['availble_time'];

                          
                        $response["message"]= $xml->string->get_team_successfully."";
                        $response["status"]="200";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();

                    }else{

                        $response["message"]= $xml->string->no_team_details_found."";
                        $response["status"]="201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();

                    }

                } else if($_POST['send_feedback_gatekeeper']=="send_feedback_gatekeeper" && filter_var($society_id, FILTER_VALIDATE_INT) == true) {

                    if ($feedback_msg=='') {
                        $response["message"] = $xml->string->please_enter_message."";
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                        
                    }


                   $maxsize    = 10097152;
                   $file11=$_FILES["attachment"]["tmp_name"];
                   if (file_exists($file11)) {

                        if(($_FILES['attachment']['size'] >= $maxsize) || ($_FILES["attachment"]["size"] == 0)) {
                            $response["message"] = $xml->string->photo_too_large_must_be_less_than_ten_mb."";
                            $response["status"]="201";
                            echo $d->manage_encryption($is_encrypted, $response, $compress);
                            exit();
                            
                        }

                        $extId = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                        $extAllow=array("mp4","m4a","m4v","f4v","f4a","m4b","m4r","f4b","mov","3gp","avi","png","jpg","jpeg","gif","JPG","JPEG","PNG");
                        if(in_array($extId,$extAllow)) {
                           $temp = explode(".", $_FILES["attachment"]["name"]);
                            $attachment = "Support_".$name.round(microtime(true)) . '.' . end($temp);
                            move_uploaded_file($_FILES["attachment"]["tmp_name"], "../img/fin_support/" . $attachment);
                        } else {
                            $response["message"] = $xml->string->invalid_file_type."";
                            $response["status"]="201";
                            echo $d->manage_encryption($is_encrypted, $response, $compress);
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
                      
                        $response["message"] = $xml->string->thank_you_for_your_feedback.'';
                        $response["status"] = "200";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    } else {
                        $response["message"] = $xml->string->something_wrong.'';

                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    }

                } else if($_POST['send_feedback_admin']=="send_feedback_admin" && filter_var($society_id, FILTER_VALIDATE_INT) == true) {

                    if ($feedback_msg=='') {
                        $response["message"] =  $xml->string->please_enter_message."";
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                        
                    }


                   $maxsize    = 10097152;
                   $file11=$_FILES["file_contents"]["tmp_name"];
                   if (file_exists($file11)) {

                        if(($_FILES['file_contents']['size'] >= $maxsize) || ($_FILES["file_contents"]["size"] == 0)) {
                            $response["message"]=$xml->string->photo_too_large_must_be_less_than_ten_mb."";
                            $response["status"]="201";
                            echo $d->manage_encryption($is_encrypted, $response, $compress);
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
                          $response["message"]= $xml->string->invalid_file_type."";
                          $response["status"]="201";
                          echo $d->manage_encryption($is_encrypted, $response, $compress);
                          exit();
                          
                        }
                       
                      } else {
                         $attachment="";
                     }

                    
                    $maxsizeVideo    = 30097152;
                    $file33=$_FILES["video"]["tmp_name"];
                     if (file_exists($file33)) {

                          if(($_FILES['video']['size'] >= $maxsizeVideo) || ($_FILES["video"]["size"] == 0)) {
                            $response["message"]= $xml->string->file_must_be_less_than_thirty_mb ."";
                            $response["status"]="201";
                            echo $d->manage_encryption($is_encrypted, $response, $compress);
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
                                $response["message"]= $xml->string->invalid_file_type."";
                                $response["status"]="201";
                                echo $d->manage_encryption($is_encrypted, $response, $compress);
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
                          $response["message"]=$xml->string->invalid_file_type."";
                          $response["status"]="201";
                          echo $d->manage_encryption($is_encrypted, $response, $compress);
                          exit();
                          
                        }
                      }else{
                        $document="";
                      }

                    if ($attachment == "" && !empty($_POST['attachment_temp'])) {
                        $tempFile = '../img/temp_images/' . basename($_POST['attachment_temp']);
                        if (file_exists($tempFile)) {
                            $extId = strtolower(pathinfo($tempFile, PATHINFO_EXTENSION));
                            $attachment = "Support_" . $mobile . round(microtime(true)) . '.' . $extId;
                            rename($tempFile, '../img/fin_support/' . $attachment);
                        }
                    }
                    if ($video == "" && !empty($_POST['video_temp'])) {
                        $tempFile = '../img/temp_images/' . basename($_POST['video_temp']);
                        if (file_exists($tempFile)) {
                            $extId = strtolower(pathinfo($tempFile, PATHINFO_EXTENSION));
                            $video = "Support_3_" . $mobile . round(microtime(true)) . '.' . $extId;
                            rename($tempFile, '../img/fin_support/' . $video);
                        }
                    }
                    if ($document == "" && !empty($_POST['document_temp'])) {
                        $tempFile = '../img/temp_images/' . basename($_POST['document_temp']);
                        if (file_exists($tempFile)) {
                            $extId = strtolower(pathinfo($tempFile, PATHINFO_EXTENSION));
                            $document = "Support_3_" . $mobile . round(microtime(true)) . '.' . $extId;
                            rename($tempFile, '../img/fin_support/' . $document);
                        }
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


                    if ($q == true) {
                      
                        $admins = $d->select("admin_fcm_notification_master","fcm_notifications=2");
                        $total_admins = mysqli_num_rows($admins);
                        if($total_admins>0){
                             $noti_title = "New Feedback From $name ";
                            $noti_description = $subject;
                            $click_action = "feedback";
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
                        $response["message"] = $xml->string->thank_you_for_contact_reach_back_to_you ."";
                        $response["status"] = "200";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    } else {
                        $response["message"] = $xml->string->something_wrong.'';

                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    }

                } else if(isset($replyFeedback) && $replyFeedback=='replyFeedback'){

                    $q=$d->select("feedback_master","feedback_id='$feedback_id' AND mobile='$user_mobile' AND country_code='$country_code'","");
                    if (mysqli_num_rows($q)==0) {
                       $response["message"] = $xml->string->invalid_request ."";
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                        
                    }

                    $source_image = '../img/temp_images/' . $attachment;
                    $maxsize    = 10097152;
                    $imageurl="";
                    if (file_exists($source_image) && $attachment!="") {
                        if (filesize($source_image) > $maxsize) {
                            $response["message"] = $xml->string->photo_too_large_must_be_less_than_ten_mb.'';
                            $response["status"] = "201";
                            echo $d->manage_encryption($is_encrypted, $response, $compress);
                            exit();
                        }
                        $fileExtension = strtolower(pathinfo($source_image, PATHINFO_EXTENSION));
                        $uniqueName = "Support_1_" . $mobile . round(microtime(true)) . '.' . $fileExtension;
                        define('UPLOAD_DIR', '../img/fin_support/');
                        $destination_path = UPLOAD_DIR . $uniqueName;
                        if (rename($source_image, $destination_path)) {
                            $imageurl = $base_url . 'img/fin_support/' . $uniqueName;
                            $attachment=$uniqueName;
                        }
                    } else {
                        $attachment="";
                    }

                    $msg = "Ticket #TKT00$feedback_id Reply";

                    if (isset($client_name) && $client_name!='') {
                        $client_name = $client_name;
                    } else {
                        $client_name = $admin_name;
                    }
                      
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
                    );
                    $d->insert("feedback_log_master",$a1);


                    $response["message"] =  $xml->string->reply_sent_successfully."";
                    $response["status"] = "200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                } else if($_POST['send_feedback_guest']=="send_feedback_guest" ) {

                    if ($feedback_msg=='') {
                        $response["message"] =  $xml->string->please_enter_message."";
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
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
                      
                        $response["message"] = $xml->string->thank_you_for_your_feedback.'';
                        $response["status"] = "200";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    } else {
                        $response["message"] = $xml->string->something_wrong.'';

                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    }

                } else {
                    $response["message"] = $xml->string->wrong_tag ."" ;
                    $response["status"]="201";
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