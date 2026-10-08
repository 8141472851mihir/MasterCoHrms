<?php 
 include '../common/objectController.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if(isset($_POST) && !empty($_POST) )//it can be $_GET doesn't matter
{
  // add main menu
  if(isset($_POST['manage_cron'])){
    // echo "<pre>";
    // print_r($_POST);
    // exit;
    $m->set_data('cron_name',$cron_name);
    $m->set_data('cron_url',$cron_url);
    $m->set_data('cron_tag',$cron_tag);
    $m->set_data('run_per_company',$run_per_company);
    $m->set_data('cron_category',$cron_category);
    $m->set_data('cron_value',$cron_value);
    $m->set_data('cron_status',0);
    $m->set_data('added_by',$bms_admin_id);
    $m->set_data('added_date',date('Y-m-d H:i:s'));
    $a['cron_name'] = $m->get_data('cron_name');
    $a['cron_url'] = $m->get_data('cron_url');
    $a['cron_value'] = $m->get_data('cron_value');
    $a['cron_tag'] = $m->get_data('cron_tag');
    $a['run_per_company'] = $m->get_data('run_per_company');
    $a['cron_status'] = $m->get_data('cron_status');
    if (isset($cron_category) &&  $cron_category!="") {
      $a['cron_category'] = $m->get_data('cron_category');
      
    }
    if($cron_id==""){
      $a['added_by'] = $m->get_data('added_by');
      $a['added_date'] = $m->get_data('added_date');
      $q=$d->insert("crons_master",$a);
      $cron_id = $con->insert_id;
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Cron Added");
      $_SESSION['msg']="New cron successfully added.";
    }else{
      $a['modify_by'] = $m->get_data('added_by');
      $a['modify_date'] = $m->get_data('added_date');
      $q=$d->update("crons_master",$a,"cron_id=$cron_id");
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Cron Added");
      $d->delete("crons_society_master","cron_id=$cron_id");
      $_SESSION['msg']="Cron successfully updated.";
    }
    if($q>0) {

      $lastDisplayNumberQuery = $d->selectRow("display_order_by","crons_society_master","cron_id=$cron_id","ORDER BY display_order_by DESC LIMIT 1");

      $lastDisplayNumberCount = 1;
      if(mysqli_num_rows($lastDisplayNumberQuery)>0){
        $lastDisplayNumber = mysqli_fetch_array($lastDisplayNumberQuery);
        $lastDisplayNumberCount = (int)$lastDisplayNumber['display_order_by'];
      }

      foreach($society_id as $societies){
        $a1['cron_id'] = $cron_id;
        $a1['display_order_by'] = $lastDisplayNumberCount++;
        $a1['society_id'] = $societies;
        $q=$d->insert("crons_society_master",$a1);
      }
      header("location:../crons");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../crons");
    }
  }else if (isset($_POST['order']) && isset($_POST['manage_cron_society_order']) && $_POST['manage_cron_society_order'] == 'manage_cron_society_order') {
    $order = $_POST['order'];

    $response = array();

    if (!empty($order)) {

      foreach ($order as $item) {

        $society_id = $item['id'];
        $cron_id    = $item['cron_id'];
        $display_order_by = $item['position'];

        $orderUpdate['display_order_by'] = $display_order_by;

        $q=$d->update("crons_society_master", $orderUpdate, " cron_id = '$cron_id' AND society_id = '$society_id' ");
      }

      $response['status']="200";
      $response['message'] = "Order updated successfully";
      echo json_encode($response);
    }else{
      $response['status']="201";
      $response['message'] = "No order data received";
      echo json_encode($response);
    }
  }
}
else{
  header('location:../login');
 }
 ?>
