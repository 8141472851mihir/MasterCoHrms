<?php
include_once 'lib.php';

if(isset($_POST) && !empty($_POST)){

    if ($key==$keydb) {
	$response = array();
	extract(array_map("test_input" , $_POST));
    $today  = date("Y-m-d");
    
    if($_POST['contacts_us']=="contacts_us" && filter_var($user_id, FILTER_VALIDATE_INT) == true  ){
           
             $response["fincasys_mobile"]="+91 9712483997";
             $response["fincasys_email"]="contact@mycompany.app";
             $response["availble_time"]="10.00 AM To 7.00 PM";
             $response["message"]="Get Contact Us Successfully!";
             $response["status"]="200";
             echo json_encode($response);

    } else{
      $response["message"]="wrong tag";
      $response["status"]="201";
      echo json_encode($response);
    }
  }
    else{
        $response["message"]="wrong api key";
        $response["status"]="201";
        echo json_encode($response);

    }
}
