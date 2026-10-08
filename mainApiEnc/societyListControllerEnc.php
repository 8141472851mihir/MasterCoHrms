<?php
include_once 'lib.php';
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
$_POST =  json_decode($d->manage_decryption("1", file_get_contents("php://input")),true);
$compress = resolve_api_compress();
// $_POST =  $d->manage_encryption("1", $_POST);
// echo $_POST;die();
// $is_encrypted=0;
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted,[
        "status" => "201",
        "message" => $xml->string->invalid_request.""
    ], $compress);
    exit();
}
try {
    if(isset($_POST) && !empty($_POST)){
        $response = array();
        extract(array_map("test_input" , $_POST));

        if($_POST['getSociety']=="getSociety" ){
            if (!$d->rl_rate_limit_myco('getSociety', 20, 120)) {
                http_response_code(429);
                $response["message"] = "Too many requests. Please try again later.";
                $response["status"] = "429";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            }
            if ($society_id>0 && isset($society_id)) {
                $qsociety=$d->select("society_master","society_id='$society_id' ","");
            } else if(isset($company_name) && strlen($company_name)>2) {
                $qsociety=$d->selectRow("society_master.*,countries.name,states.name as state_name,cities.name as city_name","countries,cities,states,society_master","countries.country_id=society_master.country_id AND states.state_id=society_master.state_id AND cities.city_id=society_master.city_id 
                    AND ((society_master.search_society_code = 1 AND society_master.society_code = '$company_name') OR 
                    (society_master.search_society_code = 0 AND (society_master.society_code = '$company_name' OR society_master.society_name LIKE '%$company_name%' OR society_master.company_full_name LIKE '%$company_name%' )))");
            } else {
                $response["message"]=$xml->string->no_data_found_web.'';
                $response["status"]="201";
                echo $d->manage_encryption($is_encrypted,$response, $compress);
                exit();
            }

            if(mysqli_num_rows($qsociety)>0){
                $response["society"] = array();
                while($data_society=mysqli_fetch_array($qsociety)) {
                    $society = array(); 
                    $society_type = $data_society['society_name'];
                    $app_url_ios = $data_society['app_url_ios'];
                    $app_url_android = $data_society['app_url_android'];
                    $society["society_id"]=$data_society['society_id'];
                    $society["country_id"]=$data_society['country_id'];
                    $society["state_id"]=$data_society['state_id'];
                    $society["city_id"]=$data_society['city_id'];
                    $society["society_type"]=$data_society['society_type'];
                    $society["societyUserId"]="";
                    $society["society_name"]=$data_society['society_name'];
                    $society["state_country_name"]=$data_society['state_name'].', '.$data_society['name'];
                    $society["society_address"]=$data_society['society_address'];
                    $society["secretary_email"]="";
                    $society["secretary_mobile"]="";
                    $society["country_code"]=$data_society['country_code'];
                    $society["secretary_name"]="";
                    $society["socieaty_logo"]=$data_society['socieaty_logo'];
                    $society["builder_name"]="";
                    $society["builder_address"]="";
                    $society["society_pincode"]=$data_society['society_pincode'];
                    $society["builder_mobile"]="";
                    $society["socieaty_status"]=$data_society['society_status'];
                    $society["sub_domain"]=$data_society['sub_domain'];
                    $society["city_name"]=$data_society['city_name'];
                    $society["api_key"]=$data_society['api_key'];
                    $society["currency"]=$data_society['currency'];
                    $society["login_via"]=$data_society['login_via'];
                    $society["google_login"]=$data_society['google_login'];
                    $society["is_firebase"]= ($data_society['is_firebase_otp']==1) ? true : false;
                    $society["is_society"] = true;
                    $society["label_member_type"] = "Employee";
                    $society["label_setting_apartment"] = "";
                    $society["label_setting_resident"] = "";
                    
                    if($data_society['visible_in_search_list']==1) {
                    $society["share_app_content"]="Hello \nI am referring you Mobile Application named $society_type, a Company Management Mobile Application (Platform). \n$society_type is a ‘Company Management’ mobile application that provides Company Management Services vide a comprehensive service providers management system that provides various maintenance services to\n•    Employee Directory\n•    Connect and Work together\n•    Service Provider\n•    Facilities & Resources\n•    Share Events, Polls & Circulars by way of technology platform to ensure safety, security, convenience, ease and leisure to its users.\n\nAndroid App: $app_url_android\niOS App: $app_url_ios";
                    } else {
                    $society["share_app_content"]="Hello \nI am referring you Mobile Application named ".$d->app_name().", a Company Management Mobile Application (Platform). \n".$d->app_name()." is a ‘Company Management’ mobile application that provides Company Management Services vide a comprehensive service providers management system that provides various maintenance services to\n•    Employee Directory\n•    Connect and Work together\n•    Attendance Management\n•    Leave Management\n•    Payroll Management\n•    Share Events, Polls & Circulars by way of technology platform to ensure safety, security, convenience, ease and leisure to its users.\n\nAndroid App: $app_url_android\niOS App: $app_url_ios";
                    
                    }
                   
                    if ($data_society['society_type'] == 0) {
                    	$society["hide_branch"]=false;
                    } else {
                    	$society["hide_branch"]=true;
                    }

                    array_push($response["society"], $society); 
                }
                if ($society_id==82) {
                $response["hide_branch"]=true;
                }else {
                $response["hide_branch"]=false;
                }
                $response["take_request_society"]=true;
                $response["message"]=$xml->string->data_found."";
                $response["status"]="200";
                echo $d->manage_encryption($is_encrypted,$response, $compress);
                exit();
            }else{
                $response["message"]=$xml->string->no_data_found_web.'';
                $response["status"]="201";
                echo $d->manage_encryption($is_encrypted,$response, $compress);
                exit();
            }
        } else if($_POST['getComapnySocietyFaceApp']=="getComapnySocietyFaceApp" ){

            if (!$d->rl_rate_limit_myco('getComapnySocietyFaceApp', 10, 60)) {
                http_response_code(429);
                $response["message"] = "Too many requests. Please try again later.";
                $response["status"] = "429";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            }

            $array1 = array();
            $array2 = array();

            if ($society_id>0 && isset($society_id)) {
                $qsociety=$d->select("society_master","society_id='$society_id' ","");
            } else if(isset($company_name) && strlen($company_name)>2) {
                $qsociety=$d->selectRow("society_master.*,countries.name,states.name as state_name,cities.name as city_name","countries,cities,states,society_master","countries.country_id=society_master.country_id AND states.state_id=society_master.state_id AND cities.city_id=society_master.city_id AND ((society_master.search_society_code = 1 AND society_master.society_code='$company_name') OR (society_master.search_society_code = 0 AND (society_master.society_code='$company_name' OR society_master.society_name LIKE '%$company_name%' OR society_master.company_full_name LIKE '%$company_name%' )))");

                $qsocietyWl=$d->selectRow("society_master_white_label.*,countries.name,states.name as state_name,cities.name as city_name","countries,cities,states,society_master_white_label","countries.country_id=society_master_white_label.country_id AND states.state_id=society_master_white_label.state_id AND cities.city_id=society_master_white_label.city_id  AND society_master_white_label.project_type<=1  AND (society_master_white_label.society_name LIKE '%$company_name%' OR society_master_white_label.company_full_name LIKE '%$company_name%' )");
            }  else {
                $response["message"]=$xml->string->no_data_found_web.'';
                $response["status"]="201";
                echo $d->manage_encryption($is_encrypted,$response, $compress);
                exit();
            }

            if(mysqli_num_rows($qsociety)>0){
                $array1["society"] = array();
                while($data_society=mysqli_fetch_array($qsociety)) {
                    $society = array(); 
                    $society_type = $data_society['society_name'];
                    $app_url_ios = $data_society['app_url_ios'];
                    $app_url_android = $data_society['app_url_android'];
                    $society["is_myco"]= true;
                    $society["society_id"]=$data_society['society_id'];
                    $society["country_id"]=$data_society['country_id'];
                    $society["state_id"]=$data_society['state_id'];
                    $society["city_id"]=$data_society['city_id'];
                    $society["society_type"]=$data_society['society_type'];
                    $society["societyUserId"]="";
                    $society["society_name"]=$data_society['society_name'];
                    $society["state_country_name"]=$data_society['state_name'].', '.$data_society['name'];
                    $society["society_address"]=$data_society['society_address'];
                    $society["secretary_email"]="";
                    $society["secretary_mobile"]="";
                    $society["country_code"]=$data_society['country_code'];
                    $society["secretary_name"]="";
                    $society["socieaty_logo"]=$data_society['socieaty_logo'];
                    $society["builder_name"]="";
                    $society["builder_address"]="";
                    $society["society_pincode"]="";
                    $society["builder_mobile"]="";
                    $society["socieaty_status"]=$data_society['society_status'];
                    $society["sub_domain"]=$data_society['sub_domain'];
                    $society["city_name"]=$data_society['city_name'];
                    $society["api_key"]=$data_society['api_key'];
                    $society["currency"]=$data_society['currency'];
                    $society["login_via"]=$data_society['login_via'];
                    $society["google_login"]=$data_society['google_login'];
                    $society["is_firebase"]=($data_society['is_firebase_otp']==1) ? true : false;;
                    $society["is_society"] = true;
                    $society["label_member_type"] = "Employee";
                    $society["label_setting_apartment"] = "";
                    $society["label_setting_resident"] = "";
                    
                    $society["share_app_content"]="";
                    if ($data_society['society_type'] == 0) {
                        $society["hide_branch"]=false;
                    } else {
                        $society["hide_branch"]=true;
                    }

                    array_push($array1["society"], $society); 
                }
                if ($society_id==82) {
                $array1["hide_branch"]=true;
                }else {
                $array1["hide_branch"]=false;
                }
                $array1["take_request_society"]=true;
            }

            if(mysqli_num_rows($qsocietyWl)>0){
                $array2["society"] = array();
                while($data_society=mysqli_fetch_array($qsocietyWl)) {
                    $society = array(); 
                    $society_type = $data_society['society_name'];
                    $app_url_ios = $data_society['app_url_ios'];
                    $app_url_android = $data_society['app_url_android'];
                    $society["is_myco"]= ($data_society['project_type']==1) ? false : true;
                    $society["society_id"]=$data_society['master_company_id'];
                    $society["country_id"]=$data_society['country_id'];
                    $society["state_id"]=$data_society['state_id'];
                    $society["city_id"]=$data_society['city_id'];
                    $society["society_type"]=$data_society['society_type'];
                    $society["societyUserId"]="";
                    $society["society_name"]=$data_society['society_name'];
                    $society["state_country_name"]=$data_society['state_name'].', '.$data_society['name'];
                    $society["society_address"]=($data_society['project_type']==1) ? "🏠 ".$data_society['society_address'] : $data_society['society_address'];
                    $society["secretary_email"]="";
                    $society["secretary_mobile"]="";
                    $society["country_code"]=$data_society['country_code'];
                    $society["secretary_name"]="";
                    $society["socieaty_logo"]=$data_society['socieaty_logo'];
                    $society["builder_name"]="";
                    $society["builder_address"]="";
                    $society["society_pincode"]=$data_society['society_pincode'];
                    $society["builder_mobile"]="";
                    $society["socieaty_status"]=$data_society['society_status'];
                    $society["sub_domain"]=$data_society['sub_domain'];
                    $society["city_name"]=$data_society['city_name'];
                    $society["api_key"]=($data_society['project_type']==1 && $data_society['api_key']=='') ? "smartapikey" : $data_society['api_key'];
                    $society["currency"]=$data_society['currency'];
                    $society["login_via"]=$data_society['login_via'];
                    $society["google_login"]=$data_society['google_login'];
                    $society["is_firebase"]=false;
                    $society["is_society"] = true;
                    $society["label_member_type"] = "Employee";
                    $society["label_setting_apartment"] = "";
                    $society["label_setting_resident"] = "";
                    
                    if($data_society['visible_in_search_list']==1) {
                    $society["share_app_content"]="Hello \nI am referring you Mobile Application named $society_type, a Company Management Mobile Application (Platform). \n$society_type is a ‘Company Management’ mobile application that provides Company Management Services vide a comprehensive service providers management system that provides various maintenance services to\n•    Employee Directory\n•    Connect and Work together\n•    Service Provider\n•    Facilities & Resources\n•    Share Events, Polls & Circulars by way of technology platform to ensure safety, security, convenience, ease and leisure to its users.\n\nAndroid App: $app_url_android\niOS App: $app_url_ios";
                    } else {
                    $society["share_app_content"]="Hello \nI am referring you Mobile Application named ".$d->app_name().", a Company Management Mobile Application (Platform). \n".$d->app_name()." is a ‘Company Management’ mobile application that provides Company Management Services vide a comprehensive service providers management system that provides various maintenance services to\n•    Employee Directory\n•    Connect and Work together\n•    Attendance Management\n•    Leave Management\n•    Payroll Management\n•    Share Events, Polls & Circulars by way of technology platform to ensure safety, security, convenience, ease and leisure to its users.\n\nAndroid App: $app_url_android\niOS App: $app_url_ios";
                    
                    }
                   
                    if ($data_society['society_type'] == 0) {
                        $society["hide_branch"]=false;
                    } else {
                        $society["hide_branch"]=true;
                    }

                    array_push($array2["society"], $society); 
                }
               
                $array2["hide_branch"]=false;
                
                $array2["take_request_society"]=true;
            }
            // print_r($array1);

            $response["society"] = array_merge($array1["society"] ?? [],$array2["society"] ?? []);

            if (mysqli_num_rows($qsociety)>0 || mysqli_num_rows($qsocietyWl)>0) {

                $response["message"]=$xml->string->data_found."";
                $response["status"]="200";
                echo $d->manage_encryption($is_encrypted,$response, $compress);
                exit();
            }else{
                $response["message"]=$xml->string->no_data_found_web.'';
                $response["status"]="201";
                echo $d->manage_encryption($is_encrypted,$response, $compress);
                exit();
            }
        }else if($_POST['getGroupCompanies']=="getGroupCompanies" ){
            if(isset($society_id) && !empty($society_id)){
                $qsociety=$d->selectRow("sm.society_id,sm.society_name,sm.sub_domain,sm.city_name","company_group_master cgm JOIN society_master sm ON FIND_IN_SET(sm.society_id, cgm.company_ids) > 0","FIND_IN_SET($society_id, cgm.company_ids) > 0 AND cgm.status=0");
                if(mysqli_num_rows($qsociety)>0){
                    $response["society_data"] = array();
                    while($data_society=mysqli_fetch_array($qsociety)) {
                        $society = array(); 
                        $society["id"]=$data_society['society_id'];
                        $society["name"]=$data_society['society_name']." (".$data_society['city_name'].")";
                        $society["url"]=$data_society['sub_domain'];
                        array_push($response["society_data"], $society); 
                    }
                    $response["message"]=$xml->string->data_found."";
                    $response["status"]="200";
                    echo $d->manage_encryption($is_encrypted,$response, $compress);
                    exit();
                }else{
                    $response["message"]=$xml->string->no_data_found_web.'';
                    $response["status"]="201";
                    echo $d->manage_encryption($is_encrypted,$response, $compress);
                    exit();
                }
            }else{
                $response["message"]=$xml->string->bad_request.'';
                $response["status"]="201";
                echo $d->manage_encryption($is_encrypted,$response, $compress);
                exit();
            }
        }else {
            $response["message"]=$xml->string->wrong_tag.'';
            $response["status"]="201";
            echo $d->manage_encryption($is_encrypted,$response, $compress);
            exit();
        }
    }
} catch (Exception $e) {
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
}
echo $d->manage_encryption($is_encrypted,$response, $compress);
exit();
