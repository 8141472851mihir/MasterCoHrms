<?php
include_once 'lib.php';

if(isset($_POST) && !empty($_POST))
{
	$response = array();
	extract(array_map("test_input" , $_POST));
  if ($key != $keydb) {
    echo json_encode(["status" => "201", "message" => "Wrong Api Key"]);
    exit;
  }
  $today  = date("Y-m-d");
  if(isset($getClientDetailsAll))
  {
    $q = $d->select("clients_master","status = 1","ORDER BY order_no");
    $clients_array = [];
    while($data = $q->fetch_assoc())
    {
      $clients_array[] = $data;
    }
    echo json_encode($clients_array);exit;
  }
}
?>
