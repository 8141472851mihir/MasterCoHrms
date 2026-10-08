<?php 
include './common/object.php';
if (isset($_POST) && !empty($_POST)) {
  extract($_POST);
  if (isset($_POST['getCompanyList'])) {
    $notisocities = $d->select('society_master', "society_status='0'", 'ORDER BY society_name ASC');
    $societies = [];
    while ($row = mysqli_fetch_assoc($notisocities)) {
     $societies[] = [
        'society_id' => $row['society_id'],
        'society_name' => $row['society_name'],
        'city_name' => $row['city_name'],
      ];
    }
    $response['success'] = true;
    $response['data']['societies'] = $societies;
    echo json_encode($response);
  }else if (isset($_POST['getCRMList'])) {
    $notisocities = $d->selectRow("society_crm_master.*,society_master.crm_created,society_master.society_crm_id AS selectedid", "society_crm_master,society_master","society_master.society_id='$request_society_id' AND society_crm_master.visible_create_request=0");
    $societies = [];
    while ($row = mysqli_fetch_assoc($notisocities)) {
     $societies[] = [
        'server' => $row['server'],
        'society_crm_id' => $row['society_crm_id'],
        'selected' => (($row['selectedid'] == $row['society_crm_id']) && $row['crm_created']!='0') ? "1" : "",
      ];
    }
    $response['success'] = true;
    $response['data']['societies'] = $societies;
    echo json_encode($response);
  }else{
    $response['success'] = false;
    echo json_encode($response);
  }
}else{
  $response['success'] = false;
  echo json_encode($response);
}