<?php 
include '../common/objectController.php';
// print_r($_POST);
if(isset($_POST) && !empty($_POST) )//it can be $_GET doesn't matter
{
  // add main menu
  extract(array_map("test_input" , $_POST));
  if(isset($_POST['checkSocietyMobile'])){
    $enc_secretary_mobile=$d->encryptDecrypt("encrypt",$secretary_mobile);
    $q=$d->select("bms_admin_master","admin_mobile='$enc_secretary_mobile'");
    $data=mysqli_fetch_array($q);
    if ($data>0) {
       echo 1;
    } else {
      echo 0;
    }

  }

if(isset($_POST['checkSocietyEmail'])){
    $enc_secretary_email=$d->encryptDecrypt("encrypt",$secretary_email);
    $q=$d->select("bms_admin_master","admin_email='$enc_secretary_email'");
    $data=mysqli_fetch_array($q);
    if ($data>0) {
       echo 1;
    } else {
      echo 0;
    }

  }


  if(isset($_POST['checkSocietyMobileEdit'])){
    $enc_secretary_mobile=$d->encryptDecrypt("encrypt",$secretary_mobile);
    $q11=$d->select("bms_admin_master","admin_mobile='$enc_secretary_mobile'");
    $data11=mysqli_fetch_array($q11);
    if ($data11>0) {
       echo 1;
    } else {
      echo 0;
    }

  }

  if(isset($_POST['checkSocietyEmailEdit'])){
    $enc_secretary_email=$d->encryptDecrypt("encrypt",$secretary_email);
    $q11=$d->select("bms_admin_master","admin_email='$enc_secretary_email'");
    $data11=mysqli_fetch_array($q11);
    if ($data11>0) {
       echo 1;
    } else {
      echo 0;
    }

  }
  

 // add main menu
  extract(array_map("test_input" , $_POST));
  if(isset($_POST['checkUserMobile'])){
    $enc_secretary_mobile=$d->encryptDecrypt("encrypt",$secretary_mobile);
    $q=$d->select("users_master","user_mobile='$userMobile'");
    $data=mysqli_fetch_array($q);
    if ($data>0) {
       echo 1;
    } else {
      echo 0;
    }

  }

   // add main menu
  extract(array_map("test_input" , $_POST));
  if(isset($_POST['checkEmailMobile'])){
    $q=$d->select("users_master","user_email='$userEmail'");
    $data=mysqli_fetch_array($q);
    if ($data>0) {
       echo 1;
    } else {
      echo 0;
    }

  }

  extract(array_map("test_input" , $_POST));
  if(isset($_POST['checkUserEmp'])){
    $q=$d->select("employee_master","emp_mobile='$emp_mobile' AND emp_type_id =0");
    $data=mysqli_fetch_array($q);
    if ($data>0) {
       echo 1;
    } else {
      echo 0;
    }

  }


}

 ?>
