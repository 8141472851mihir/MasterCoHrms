<?php
include'../common/objectController.php';
extract($_POST);
if(isset($_POST['exportGreetingsData'])) {
  if(!isset($_POST['keyId']) || count($_POST['keyId'])<1) {
     $_SESSION['msg1']="Please select atleast one greeting.";
      header("location:../exportGreetings");
      exit();
  }
  $retun_url = $base_url."commonApi/importGreetings.php";
  $export_url= $export_url.'commonApi/importGreetingsController.php';
  $seasonal_greet_ids = implode(",",$_POST['keyId']);
  $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$export_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_POSTFIELDS,"retun_url=$retun_url&exportGreetings=exportGreetings&seasonal_greet_ids=$seasonal_greet_ids");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($code=='404') {
      $_SESSION['msg1']="Invalid Url ($code)";
      header("location:../exportGreetings");
      exit();
    } 
    curl_close ($ch);
    $json = json_decode($server_output,true);
    
    $_SESSION['msg']=$json['message'];
    header ("Location:../exportGreetings");
  
} else {
  $_SESSION['msg1']="Something Wrong";
  header ("Location:../exportGreetings");
}
?>