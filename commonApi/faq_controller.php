<?php
include_once 'lib.php';
include_once '../apAdmin/lib/crmconfig.php';
// used on myco website too
if(isset($_POST) && !empty($_POST)){

	$response = array();
	extract(array_map("test_input" , $_POST));
  $today  = date("Y-m-d");
    if($_POST['getFaq']=="getFaq"){
           
        $q=$d->select("faq_question_master","status = 1","");
         
        if(mysqli_num_rows($q)>0){

            $response["faq"] = array();

            while($data=mysqli_fetch_array($q)) {
                     $faq = array(); 
                     $faq["faq_sub_master_id"]=$data['faq_sub_master_id'];
                     $faq["faq_question"]=html_entity_decode($data['faq_question']);
                     $faq["faq_answer"]=html_entity_decode($data['faq_answer']);
                     
                     array_push($response["faq"], $faq);
            
            }
           
             $response["message"]="Get Faq Successfully!";
             $response["status"]="200";
             echo json_encode($response);
             exit();

        }else{
            $response["message"]="No Data Availble.";
            $response["status"]="201";
            echo json_encode($response);
            exit();

        }
    } else  if($_POST['addFeedback']=="addFeedback"){
        // print_r($_POST);
        // exit;
	    $to ="yuvraj@chplgroup.org";
	    $cc="bhavesh@chplgroup.org";
      $subject =$subject;
      $message = "<table>
      <tr>Name : $name</tr>
      <tr>Email : $email</tr>
      <tr>Mobile : $mobile</tr>
      <tr>City : $city</tr>
      <tr>Message : </tr>
      <p>$message</p>
      </table>";

      include '../apAdmin/mail.php';

      $m->set_data('name',$name);
      $m->set_data('email',$email);
      $m->set_data('mobile',$mobile);
      $m->set_data('city',$city);
      $m->set_data('subject',$subject);
      $m->set_data('message',$_POST['message']);
      $m->set_data('add_date',date("Y-m-d H:i:s"));

       $value_array = array(
            'name'=>$m->get_data('name'),
            'email'=>$m->get_data('email'),
            'mobile'=>$m->get_data('mobile'),
            'city'=>$m->get_data('city'),
            'subject'=>$m->get_data('subject'),
            'message'=>$m->get_data('message'),
            'add_date'=>$m->get_data('add_date'),
    
        );

        $insert = $d->insert("contact_us",$value_array);
       if ($insert>0) {
            $names = explode(" ", $name);
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
            $leadata["leads"][0]["email"] =  $email;
            $leadata["leads"][0]["countryCode"] =  $countryCode;
            $leadata["leads"][0]["mobile"] =  $mobile;
            $leadata["leads"][0]["phoneCountryCode"] =  $countryCode;
            $leadata["leads"][0]["phone"] =  $mobile;
            $leadata["leads"][0]["expectedRevenue"] =  "";
            $leadata["leads"][0]["description"] =  $_POST['message'];
            $leadata["leads"][0]["companyName"] =  $company_name;
            $leadata["leads"][0]["companyState"] =  "";
            $leadata["leads"][0]["companyStreet"] =  "";
            $leadata["leads"][0]["companyCity"] =  $city;
            $leadata["leads"][0]["companyCountry"] =  $selectedcountry;
            $leadata["leads"][0]["companyPincode"] =  "";
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
            file_put_contents('log.txt', date('Y-m-d H:i:s') . "\n".$crmresponse."\n", FILE_APPEND);
            $code = curl_getinfo($curl1, CURLINFO_HTTP_CODE);
            curl_close($curl1);
         $response["message"]="Feedback sent Successfully";
        $response["status"]="200";
        echo json_encode($response);
        exit();
       } else {
        $response["message"]="Something went Wrong";
        $response["status"]="201";
        echo json_encode($response);
        exit();
       }


    } else{
      $response["message"]="wrong tag";
      $response["status"]="201";
      echo json_encode($response);
    }
  
}
