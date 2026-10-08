<?php
include_once 'lib.php';

if(isset($_POST) && !empty($_POST))
{
	$response = array();
	extract(array_map("test_input" , $_POST));
  $today  = date("Y-m-d");
  if(isset($_POST['getOfficeDetailsAll']) && $_POST['getOfficeDetailsAll'] == "getOfficeDetailsAll")
  {
      $office_arr = [];
      $get_office = $d->selectRow("office_id,office_type,country_code_one,office_contact_one,country_code_two,office_contact_two,office_email_one,office_email_two,office_address","offices_master","status = 1","ORDER BY order_no");
      while ($office_data = $get_office->fetch_assoc())
      {
          $office_arr[] = $office_data;
      }
      echo json_encode($office_arr);exit;
  }
}
?>
