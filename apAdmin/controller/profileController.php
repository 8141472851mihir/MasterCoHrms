<?php
include '../common/objectController.php';

// $admin_user_id=$_SESSION['admin_user_id'];  
if (isset($_POST) && !empty($_POST)) //it can be $_GET doesn't matter
{
  if (isset($updateProfile)) {
    $file_profile_image = $_FILES['profile_image']['tmp_name'];
    if (file_exists($file_profile_image)) {
      $acceptable = array("jpeg","jpg","png","gif");
      $extId = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
      $dirPath = "../../img/profile/";
      if (in_array($extId, $acceptable) && (!empty($_FILES["profile_image"]["type"]))) {
        $temp = explode(".", $_FILES["profile_image"]["name"]);
        $newFileName = rand() . $user_id;
        $profile_image = $newFileName.'_user' . end($temp);
        $destinationPath = $dirPath . $profile_image;
        $d->resizeImage($file_profile_image, $destinationPath, 400, 400, $extId);
      }else{
        $_SESSION['msg1'] = "Invalid Photo";
        header("location:../profile");
        exit();
      }
    }else {
      $profile_image = $profile_image_old;
    }
    $m->set_data("admin_name", test_input($admin_name));
    $m->set_data("admin_email", test_input($d->encryptDecrypt("encrypt", $admin_email)));
    $m->set_data("profile_image", test_input($profile_image));
    $m->set_data("country_code", test_input($country_code));
    $a = array(
      'admin_name' => $m->get_data('admin_name'),
      'admin_email' => $m->get_data('admin_email'),
      'admin_profile' => $m->get_data('profile_image'),
      'country_code' => $m->get_data('country_code'),
    );

    $q_temp = $d->update("bms_admin_master", $a, "admin_id='$bms_admin_id'");
    if ($q_temp > 0) {

      $profile_image = $profile_image;
      $admin_name = $admin_name;
      $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Profile successfully updated.");

      $_SESSION['msg'] = "Profile successfully updated ";
      header("location:../profile");
    } else {
      $_SESSION['msg1'] = "Something Wrong";
      header("location:../profile");
    }
  }

  // Change Password
  if (isset($_POST["passwordChange"])) {
    extract(array_map("test_input", $_POST));
    if ($password == $password2) {
      $q = $d->select("bms_admin_master", "admin_id='$bms_admin_id'");
      $data = mysqli_fetch_array($q);
      if (password_verify($old_password, $data['admin_password'])) {


        $hashed_password = password_hash($password, PASSWORD_DEFAULT);


        $m->set_data('password', $hashed_password);
        $a1 = array(
          'admin_password' => $m->get_data('password'),
        );
        $insert = $d->update('bms_admin_master', $a1, "admin_id='$bms_admin_id'");
        if ($insert == true) {
          $_SESSION['msg'] = "Password Changed Successfully..!";
          $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Password Changed Successfully");



          $d->insert_log("0", "$bms_admin_id", "$created_by", "Password Changed");

          $_SESSION['msg'] = "Password Changed Successfully";
          header("location:../profile");
        } else {
          $_SESSION['msg1'] = "Somthig wrong..";
          header("location:../profile");
        }
      } else {
        $_SESSION['msg1'] = "Old Password is wrong!";
        header("location:../profile");
      }
    } else {
      //IS_592
      //$_SESSION['msg1']= "Comirm Password is wrong";
      $_SESSION['msg1'] = "confirm Password is wrong";

      header("location:../profile");
    }
  }


  // Change Password
  if (isset($_POST["masterpasswordChange"])) {
    extract(array_map("test_input", $_POST));
    if ($password == $cPassword) {
      $_SESSION['msg1'] = "Please Set Different password from current password";
      header("location:../masterAuth");
    } else if ($password == $password2) {

      $qcd = $d->select("auth_log_master", "auth_password='$password'");
      if (mysqli_num_rows($qcd) > 0) {
        $_SESSION['msg1'] = "new password cannot be the same as previously used passwords";
        header("location:../masterAuth");
        exit();
      }

      $m->set_data('password', $password);
      $a1 = array(
        'auth_passs' => $m->get_data('password'),
        'updated_date' => date("Y-m-d H:i:s"),
        'updated_by' => $bms_admin_id,
      );
      $insert = $d->update('master_user_auth_master', $a1, "auth_id='1'");
      if ($insert == true) {
        $_SESSION['msg'] = "Password Changed Successfully..!";
        $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Master Password Changed Successfully");
        $to = 'asif@silverwingteam.com';
        $message = "Master Password Changed by $created_by\nNew Password is : $password";
        $subject = "Master Password Changed";
        include '../mail.php';

        $_SESSION['msg'] = "Password Changed Successfully";
        header("location:../masterAuth");
      } else {
        $_SESSION['msg1'] = "Somthig wrong..";
        header("location:../masterAuth");
      }
    } else {
      //IS_592
      //$_SESSION['msg1']= "Comirm Password is wrong";
      $_SESSION['msg1'] = "confirm Password is wrong";

      header("location:../masterAuth");
    }
  }

  if (isset($_POST["changePasswordMaster"])) {


    $post_log_master = $d->select("auth_log_master", " auth_password = '$masterAuth'   ");
    $success_array = array();
    $failure_array = array();
    while ($post_log_master_data = mysqli_fetch_array($post_log_master)) {
      if ($post_log_master_data['status'] == '200') {
        array_push($success_array, $post_log_master_data['society_id']);
      } else {
        array_push($failure_array, $post_log_master_data['society_id']);
      }
    }

    $ids = join("','", $success_array);
    $society_master_qry = $d->select("society_master", " society_id  ='$society_id_post' AND society_id NOT IN ('$ids') ");

    $societyRows = [];
    while ($society_master_data = mysqli_fetch_array($society_master_qry)) {
      $societyRows[] = $society_master_data;
    }
    $existingAuthBySociety = [];
    if (!empty($societyRows)) {
      $authSocietyIds = array_map(function ($r) {
        return (int)$r['society_id'];
      }, $societyRows);
      $authSocietyIdsIn = implode(',', array_map('intval', $authSocietyIds));
      $qcdAll = $d->select("auth_log_master", "auth_password='$masterAuth' AND society_id IN ($authSocietyIdsIn)");
      while ($ar = mysqli_fetch_array($qcdAll)) {
        $existingAuthBySociety[(int)$ar['society_id']] = true;
      }
    }

    foreach ($societyRows as $society_master_data) {


      $post = array(
        'changePasswordMainAdmin' => 'changePasswordMainAdmin',
        'society_id' => $society_master_data['society_id'],
        'new_password' => $masterAuth,
        'language_id' => 1,
      );

      $json = $d->callCompanyApiEnc($society_master_data['sub_domain'], 'buildingChangePlanController.php', $post);


      $m->set_data('society_id', $society_master_data['society_id']);
      $m->set_data('auth_password', $masterAuth);


      $result2 = $json["message"];
      $m->set_data('result', $result2);
      $status = $json["status"];
      $m->set_data('status', $status);
      $m->set_data('created_date', date('Y-m-d H:i:s'));

      $sid = (int)$society_master_data['society_id'];
      if ($status == "200") {
        $result2 = "Password Changed";
        $a1 = array(
          'society_id' => $m->get_data('society_id'),
          'auth_password' => $m->get_data('auth_password'),
          'result' => $m->get_data('result'),
          'status' => $m->get_data('status'),
          'created_at' => $m->get_data('created_date')
        );
        if (!empty($existingAuthBySociety[$sid])) {
          $q = $d->update("auth_log_master", $a1, "auth_password='$masterAuth' AND society_id ='$society_master_data[society_id]'");
        } else {
          $q = $d->insert("auth_log_master", $a1);
          $existingAuthBySociety[$sid] = true;
        }
      } else {
        $a1 = array(
          'society_id' => $m->get_data('society_id'),
          'auth_password' => $m->get_data('auth_password'),
          'result' => 'No Response',
          'status' => '202',
          'created_at' => $m->get_data('created_date')
        );

        if (!empty($existingAuthBySociety[$sid])) {
          $q = $d->update("auth_log_master", $a1, "auth_password='$masterAuth' AND society_id ='$society_master_data[society_id]'");
        } else {
          $q = $d->insert("auth_log_master", $a1);
          $existingAuthBySociety[$sid] = true;
        }
      }

      // $_SESSION['msg']="Post Successfully";

      if ($result2 == "") {
        echo " - No Response:201";
        exit;
      } else {
        echo $result2 . ':' . $json["status"];
        exit;
      }
    }
  }


  if (isset($_POST["masterAuthCheck"])) {


    $post = array(
      'validateMainAdmin' => 'validateMainAdmin',
      'society_id' => $society_id,
      'admin_password' => $admin_password,
    );

    $json = $d->callCompanyApiEnc($sub_domain, 'buildingChangePlanController.php', $post);



    $result2 = $json["message"];
    $status = $json["status"];

    // $_SESSION['msg']="Post Successfully";

    if ($result2 == "") {
      echo " - No Response:201";
      exit;
    } else {
      echo $json["message"] . ':' . $json["status"];
      exit;
    }
  }

  if (isset($updateVerUser)) {



    $m->set_data('version_code', $version_code);
    $m->set_data('version_name_view', $version_name_view);
    $m->set_data('modify_date', date('Y-m-d H:i'));

    $a1 = array(

      'version_code' => $m->get_data('version_code'),
      'version_name' => $m->get_data('version_name_view'),
      'version_name_view' => $m->get_data('version_name_view'),
      'modify_date' => $m->get_data('modify_date'),
    );


    $q = $d->update("version_master", $a1, "version_app='$version_app' AND mobile_app='$mobile_app' AND version_id='$version_id'");
    if ($q == TRUE) {
      $_SESSION['msg'] = "Data Updated";
      $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "App Version Updated");
      header("Location: ../appVer");
    } else {
      $_SESSION['msg1'] = "Something Wrong";
      header("Location: ../appVer");
    }
  }

  //IS_577
  if ($validatePassword) {
    $q = $d->select("bms_admin_master", "admin_id='$bms_admin_id'");
    $data = mysqli_fetch_array($q);
    if ($data["admin_password"] == $old_password) {
      echo 'true';
      exit;
    } else {
      echo 'false';
      exit;
    }
  }
  //IS_577
}
