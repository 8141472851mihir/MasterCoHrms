<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
include '../common/objectController.php';

if(isset($_POST) && !empty($_POST))
{
	if(isset($addOffice))
  {
    if($office_type == "" || $office_contact_one == "" || $office_email_one == "" || $office_address == "" || $country_code_one == "" || $country_code_one == 0 || $order_no == "" || $order_no == 0)
    {
      $_SESSION['msg1']="Please fill all mandatory fields!";
      header("location:../manageOffices");exit;
    }
    $on = $d->selectRow("order_no","offices_master","order_no = '$order_no'");
    if(mysqli_num_rows($on) > 0)
    {
      $_SESSION['msg1']="Order no already assigned!";
      header("location:../manageOffices");exit;
    }
    $office_type = ucwords($office_type);
    $m->set_data("office_type",test_input($office_type));
    $m->set_data("country_code_one",test_input($country_code_one));
    $m->set_data("office_contact_one",test_input($office_contact_one));
    $m->set_data("country_code_two",test_input($country_code_two));
    $m->set_data("office_contact_two",test_input($office_contact_two));
    $m->set_data("office_email_one",test_input($office_email_one));
    $m->set_data("office_email_two",test_input($office_email_two));
    $m->set_data("office_address",test_input($office_address));
    $m->set_data("order_no",test_input($order_no));
    $a = array(
      'office_type'=>$m->get_data('office_type'),
      'country_code_one'=>$m->get_data('country_code_one'),
      'office_contact_one'=>$m->get_data('office_contact_one'),
      'country_code_two'=>$m->get_data('country_code_two'),
      'office_contact_two'=>$m->get_data('office_contact_two'),
      'office_email_one'=>$m->get_data('office_email_one'),
      'office_email_two'=>$m->get_data('office_email_two'),
      'office_address'=>$m->get_data('office_address'),
      'order_no'=>$m->get_data('order_no'),
      'created_by'=>$_COOKIE['admin_id'],
      'created_date'=>date("Y-m-d H:i:s")
    );
    $q = $d->insert("offices_master",$a);
    if($q)
    {
      $_SESSION['msg']="Office successfully added.";
      header("location:../manageOffices");
    }
    else
    {
      $_SESSION['msg1']="Something went wrong!";
      header("location:../manageOffices");
    }
  }
  elseif(isset($getOfficeDetails))
  {
    $q = $d->select("offices_master","office_id = '$office_id'");
    $data = $q->fetch_assoc();
    echo json_encode($data);exit;
  }
  elseif(isset($editOffice))
  {
    if($office_type == "" || $office_contact_one == "" || $office_email_one == "" || $office_address == "" || $country_code_one == "" || $country_code_one == 0 || $order_no == "" || $order_no == 0)
    {
      $_SESSION['msg1']="Please fill all mandatory fields!";
      header("location:../manageOffices");exit;
    }
    $on = $d->selectRow("order_no","offices_master","order_no = '$order_no' AND office_id != '$office_id'");
    if(mysqli_num_rows($on) > 0)
    {
      $_SESSION['msg1']="Order no already assigned!";
      header("location:../manageOffices");exit;
    }
    $office_type = ucwords($office_type);
    $m->set_data("office_type",test_input($office_type));
    $m->set_data("country_code_one",test_input($country_code_one));
    $m->set_data("office_contact_one",test_input($office_contact_one));
    $m->set_data("country_code_two",test_input($country_code_two));
    $m->set_data("office_contact_two",test_input($office_contact_two));
    $m->set_data("office_email_one",test_input($office_email_one));
    $m->set_data("office_email_two",test_input($office_email_two));
    $m->set_data("office_address",test_input($office_address));
    $m->set_data("order_no",test_input($order_no));
    $a = array(
      'office_type'=>$m->get_data('office_type'),
      'country_code_one'=>$m->get_data('country_code_one'),
      'office_contact_one'=>$m->get_data('office_contact_one'),
      'country_code_two'=>$m->get_data('country_code_two'),
      'office_contact_two'=>$m->get_data('office_contact_two'),
      'office_email_one'=>$m->get_data('office_email_one'),
      'office_email_two'=>$m->get_data('office_email_two'),
      'office_address'=>$m->get_data('office_address'),
      'order_no'=>$m->get_data('order_no'),
      'modified_by'=>$_COOKIE['admin_id'],
      'modified_date'=>date("Y-m-d H:i:s")
    );
    $q = $d->update("offices_master",$a,"office_id = '$office_id'");
    if($q)
    {
      $_SESSION['msg']="Office successfully updated.";
      header("location:../manageOffices");
    }
    else
    {
      $_SESSION['msg']="Something went wrong!";
      header("location:../manageOffices");
    }
  }
  elseif(isset($check_order))
  {
    if(isset($office_id))
    {
      $q = $d->select("offices_master","order_no = '$order_no' AND office_id != '$office_id'");
    }
    else
    {
      $q = $d->select("offices_master","order_no = '$order_no'");
    }
    if(mysqli_num_rows($q) > 0)
    {
      echo "false";exit;
    }
    else
    {
      echo "true";exit;
    }
  }
  elseif(isset($_POST['getOfficeDetailsAll']) && $_POST['getOfficeDetailsAll'] == "getOfficeDetailsAll")
  {
      $office_arr = [];
      $get_office = $d->selectRow("office_type,country_code_one,office_contact_one,country_code_two,office_contact_two,office_email_one,office_email_two,office_address","offices_master","status = 1","ORDER BY order_no");
      while ($office_data = $get_office->fetch_assoc())
      {
          $office_arr[] = $office_data;
      }
      echo json_encode($office_arr);exit;
  }
}
?>