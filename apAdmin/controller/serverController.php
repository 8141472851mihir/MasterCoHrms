<?php 
  include '../common/objectController.php';
if(isset($_POST) && !empty($_POST) ) {
  if(isset($addServer)) {
    $m->set_data('server_name',test_input($server_name));
    $m->set_data('server_ip',test_input($server_ip));
    $m->set_data('server_remark',test_input($server_remark));
    $m->set_data('created_by',$bms_admin_id);
    $m->set_data('created_date',date('Y-m-d H:i:s'));
    $add_server = array(
      'server_name'=>$m->get_data('server_name'),
      'server_ip'=>$m->get_data('server_ip'),
      'server_remark'=>$m->get_data('server_remark'),
      'created_by'=>$m->get_data('created_by'),
      'created_date'=>$m->get_data('created_date'),
    );
      
      $q=$d->insert("server_master",$add_server);
    
    if($q==TRUE) {
      $_SESSION['msg']="Server Added Successfully";
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Server $server_name Added");
      header("Location: ../manageServer");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("Location: ../manageServer");
    }
  }elseif(isset($editServer)) {
    $m->set_data('server_name',test_input($server_name));
    $m->set_data('server_ip',test_input($server_ip));
    $m->set_data('server_remark',test_input($server_remark));
    $m->set_data('updated_by',$bms_admin_id);
    $m->set_data('updated_date',date('Y-m-d H:i:s'));
    $edit_server = array(
      'server_name'=>$m->get_data('server_name'),
      'server_ip'=>$m->get_data('server_ip'),
      'server_remark'=>$m->get_data('server_remark'),
      'updated_by'=>$m->get_data('updated_by'),
      'updated_date'=>$m->get_data('updated_date'),
    );
      
      $q=$d->update("server_master",$edit_server,"server_id='$server_id'");
    
    if($q==TRUE) {
      $_SESSION['msg']="Server Updated Successfully";
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Server $server_name Updated");
      header("Location: ../manageServer");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("Location: ../manageServer");
    }
  }
}
?>