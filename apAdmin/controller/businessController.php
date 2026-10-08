<?php
include '../common/objectController.php';
$bms_admin_id = $bms_admin_id;
$admin_name = $admin_name;
if (isset($_POST) && !empty($_POST))//it can be $_GET doesn't matter
{
  extract($_POST);
  if (isset($_POST['addBusinessEntity']) && $_POST['addBusinessEntity'] == "addBusinessEntity") {
    $m->set_data('name', test_input($name));

    $a = array(
      'name' => $m->get_data('name'),
      'created_by' => $admin_name,
      'created_date' => date('Y-m-d H:i:s')
    );

    $q = $d->insert("business_entity_master", $a);
    if ($q > 0) {
      $d->insert_log("", "$bms_admin_id", "$created_by", "$name Business Entity Added.");
      $_SESSION['msg'] = "Business Entity Added.";
    } else {
      $_SESSION['msg1'] = "Something Wrong";
    }
    header("location:../manageBusinessEntity");
  } elseif (isset($_POST['getBusinessEntityDetails']) && $_POST['getBusinessEntityDetails'] == "getBusinessEntityDetails") {
    $query = $d->select("business_entity_master", "b_id = '$b_id'");
    $data = mysqli_fetch_object($query);
    $response['status'] = '0';
    $response['data'] = $data;
    echo json_encode($response);
  } elseif (isset($_POST['edit_businessentity']) && $_POST['edit_businessentity'] == "edit_businessentity") {

    $m->set_data('name', test_input($name));

    $a = array(
      'name' => $m->get_data('name'),
      'updated_by' => $admin_name,
      'updated_date' => date('Y-m-d H:i:s'),
    );
    $q = $d->update("business_entity_master", $a, "b_id='$b_id'");
    if ($q > 0) {
      $d->insert_log("", "$bms_admin_id", "$created_by", "$name Business Entity Update.");
      $_SESSION['msg'] = "Business Entity Updated.";
    } else {
      $_SESSION['msg1'] = "Something Wrong";
    }
    header("location:../manageBusinessEntity");
  } elseif (isset($_POST['deleteUser'])) {
    $user_id = $d->sanitizeActionIdAsInt($_POST['user_id'] ?? ($user_id ?? 0));

    $a3 = array(
      'deleted_by' => $admin_name,
      'deleted_date' => date('Y-m-d'),
      'status' => 1,
    );
    // print_r($a3);exit;
    $q = $d->update("business_entity_master", $a3, "b_id='$b_id'");
    if ($q > 0) {
      $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "$admin_name Admin Business Deactivated");
      $_SESSION['msg'] = "Business Deactivated";
      header("location:../manageBusinessEntity");
    } else {
      $_SESSION['msg1'] = "Something Wrong";
      header("location:../manageBusinessEntity");
    }
  } elseif (isset($_POST['deleteUserReactive'])) {
    $user_id = $d->sanitizeActionIdAsInt($_POST['user_id'] ?? ($user_id ?? 0));

    $a3 = array(
      'status' => 0,
    );

    $q = $d->update("business_entity_master", $a3, "b_id='$b_id'");
    if ($q > 0) {
      $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "$admin_name Admin Business Activated");
      $_SESSION['msg'] = "Business Activated";
      header("location:../manageBusinessEntity");
    } else {
      $_SESSION['msg1'] = "Something Wrong";
      header("location:../manageBusinessEntity");
    }
  }
} else {
  header('location:../login');
} ?>