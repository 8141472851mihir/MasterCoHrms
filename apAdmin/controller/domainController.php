<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include '../common/objectController.php';
// Tag-based actions
if(isset($_POST) && !empty($_POST) ) {
  extract($_POST);
  if(isset($addDomain)) {
    $m->set_data('domain_name',test_input($domain_name));
    $m->set_data('server_id',test_input($server_id));
    $m->set_data('is_remote_db',test_input($remote_db));
    if ($remote_db=="0") {
      $m->set_data('remote_ip',test_input($remote_ip));
    }else{
      $m->set_data('remote_ip',"");
    }
    $m->set_data('domain_remark',test_input($domain_remark));
    $m->set_data('created_by',$bms_admin_id);
    $m->set_data('created_date',date('Y-m-d H:i:s'));
    $add_domain = array(
      'domain_name'=>$m->get_data('domain_name'),
      'server_id'=>$m->get_data('server_id'),
      'is_remote_db'=>$m->get_data('is_remote_db'),
      'remote_ip'=>$m->get_data('remote_ip'),
      'domain_remark'=>$m->get_data('domain_remark'),
      'created_by'=>$m->get_data('created_by'),
      'created_date'=>$m->get_data('created_date'),
    );

    $q=$d->insert("domain_master",$add_domain);
    
    if($q==TRUE) {
      $_SESSION['msg']="Domain Added Successfully";
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Domain $domain_name Added");
      header("Location: ../manageDomain");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("Location: ../manageDomain");
    }
  }elseif(isset($editDomain)) {
    $m->set_data('domain_name',test_input($domain_name));
    $m->set_data('server_id',test_input($server_id));
    $m->set_data('server_id_hidden',test_input($server_id_hidden));
    $m->set_data('is_remote_db',test_input($remote_db));
    if ($remote_db!="0") {
      $m->set_data('remote_ip',test_input($remote_ip));
    }else{
      $m->set_data('remote_ip',"");
    }
    $m->set_data('domain_remark',test_input($domain_remark));
    $m->set_data('updated_by',$bms_admin_id);
    $m->set_data('updated_date',date('Y-m-d H:i:s'));
    $edit_domain = array(
      'domain_name'=>$m->get_data('domain_name'),
      'is_remote_db'=>$m->get_data('is_remote_db'),
      'remote_ip'=>$m->get_data('remote_ip'),
      'domain_remark'=>$m->get_data('domain_remark'),
      'updated_by'=>$m->get_data('updated_by'),
      'updated_date'=>$m->get_data('updated_date'),
    );
    if ($server_id_hidden!="") {
      $edit_domain['server_id']=$m->get_data('server_id_hidden');
    }else{
      $edit_domain['server_id']=$m->get_data('server_id');
    }
    $q=$d->update("domain_master",$edit_domain,"domain_id='$domain_id'");
    
    if($q==TRUE) {
      $_SESSION['msg']="Domain Updated Successfully";
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Domain $domain_name Updated");
      header("Location: ../manageDomain");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("Location: ../manageDomain");
    }
  }elseif(isset($updateMaintenance) && $updateMaintenance=="updateMaintenance") {
    $file_maintenance_attachment = $_FILES['maintenance_attachment']['tmp_name'];
    if (file_exists($file_maintenance_attachment)) {
      $acceptable = array("jpeg","jpg","png");
      $extId = pathinfo($_FILES['maintenance_attachment']['name'], PATHINFO_EXTENSION);
      $dirPath = "../../img/festival/";
      if (in_array($extId, $acceptable) && (!empty($_FILES["maintenance_attachment"]["type"]))) {
        $temp = explode(".", $_FILES["maintenance_attachment"]["name"]);
        $maintenance_attachment = 'maintenance_'.round(microtime(true)).'.'.end($temp);
        $destinationPath = $dirPath . $maintenance_attachment;
        $d->resizeImage($file_maintenance_attachment, $destinationPath, 1280, 720, $extId);
      }else{
        $_SESSION['msg1'] = "Invalid Photo";
        header("location:../addmaintenance");
        exit();
      }
    }else {
      $maintenance_attachment = $maintenance_attachment_old;
    }
    $m->set_data('maintenance_attachment',test_input($maintenance_attachment));
    $m->set_data('maintenance_desc',test_input($maintenance_desc));
    $m->set_data('update_by',$bms_admin_id);
    $m->set_data('update_date',date('Y-m-d H:i:s'));
    $edit_maintenance = array(
      'festival_name'=>$m->get_data('maintenance_desc'),
      'festival_image'=>$m->get_data('maintenance_attachment'),
      'is_festival'=>1,
    );
    $maintenanceArray = $d->count_data_direct("festival_id","festival_master","is_festival='1'");
    if ($maintenanceArray > '0') {
      $q=$d->update("festival_master",$edit_maintenance,"is_festival='1'");
    }else{
      $q=$d->insert("festival_master",$edit_maintenance);
    }
    
    if($q==TRUE) {
      $_SESSION['msg']="Maintenance Updated Successfully";
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Maintenance Updated");
      header("Location: ../addMaintenance");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("Location: ../addMaintenance");
    }
  }elseif(isset($scheduleMaintenance) && $scheduleMaintenance=="scheduleMaintenance") {
    $value_parts = explode(':', $maintenanceDateTime);
    $value_parts[2] = '00'; 
    $festival_time = new DateTime(); 
    $festival_time->setTime($value_parts[0], $value_parts[1], $value_parts[2]); 
    $formatted_festival_time = $festival_time->format('Y-m-d H:i:s');
    $m->set_data('festival_time',test_input($formatted_festival_time));
    $edit_maintenance = array(
      'festival_time'=>$m->get_data('festival_time'),
    );
    $m->set_data('isActive','1');
    $a1= array ('maintainance_active_status'=> $m->get_data('isActive'));
    $domainIdsSafe = $d->sanitizeActionIds(isset($domain_ids) ? (is_array($domain_ids) ? $domain_ids : explode(',', (string)$domain_ids)) : []);
    if (empty($domainIdsSafe)) {
      $_SESSION['msg1']="Invalid domain selection";
      header("location:../manageDomain");
      exit;
    }
    $domain_ids = implode(',', $domainIdsSafe);
    $q=$d->update('domain_master',$a1,"domain_id IN ($domain_ids)");
    $maintenanceArray = $d->count_data_direct("festival_id","festival_master","is_festival='1'");
    if ($maintenanceArray > '0') {
      $q=$d->update("festival_master",$edit_maintenance,"is_festival='1'");
    }
    
    if($q==TRUE) {
      $_SESSION['msg']="Maintenance Scheduled Successfully";
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Maintenance Scheduled");
      header("Location: ../manageDomain");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("Location: ../manageDomain");
    }
  }elseif(isset($removeMaintenance) && $removeMaintenance=="removeMaintenance") {
    $edit_maintenance = array(
      'festival_date'=>'',
      'festival_time'=>'',
    );
    $m->set_data('isActive','0');
    $a1= array ('maintainance_active_status'=> $m->get_data('isActive'));
    $q=$d->update('domain_master',$a1,"");
    $q=$d->update("festival_master",$edit_maintenance,"is_festival='1'");
    
    if($q==TRUE) {
      $_SESSION['msg']="Scheduled Maintenance Removed Successfully";
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Scheduled Maintenance Removed.");
    } else {
      $_SESSION['msg1']="Something Wrong";
    }
  }elseif(isset($updatePatch) && $updatePatch=="updatePatch") {
    $edit_maintenance = array(
      'ongoing_patch'=>$patch_val,
    );
    if ($patch_val=='1') {
      $log_msg="Patch Started.";
    }else{
      $log_msg="Patch ended.";
    }
    $q=$d->update("festival_master",$edit_maintenance,"is_festival='1'");
    
    if($q==TRUE) {
      $_SESSION['msg']=$log_msg;
      $d->insert_log("$society_id","$bms_admin_id","$created_by","$log_msg");
    } else {
      $_SESSION['msg1']="Something Wrong";
    }
  }elseif(isset($_POST['getDomainInfo']) && $_POST['getDomainInfo'] === 'getDomainInfo') {
    header('Content-Type: application/json');
    $domain_name = isset($_POST['domain_name']) ? trim($_POST['domain_name']) : '';
    if ($domain_name === '') {
      echo json_encode(['success' => false, 'message' => 'domain_name is required']);
      exit;
    }
    $conn = $d->dbCon();
    $dnEsc = mysqli_real_escape_string($conn, $domain_name);
  
    $q = $d->selectRow(
      "dm.domain_id, dm.domain_name, sm.server_name, sm.server_ip,
      COUNT(DISTINCT s.society_id) AS company_count",
      "domain_master AS dm
      LEFT JOIN server_master AS sm ON sm.server_id = dm.server_id
      LEFT JOIN society_master AS s ON s.domain_id = dm.domain_id",
      "dm.domain_active_status=0 AND dm.domain_name='".$dnEsc."' GROUP BY dm.domain_id LIMIT 1"
    );
  
    if(!$q || mysqli_num_rows($q) === 0) {
      echo json_encode(['success' => false, 'message' => 'Domain not found']);
      exit;
    }
  
    $info = mysqli_fetch_array($q);
    echo json_encode([
      'success' => true,
      'server_name' => $info['server_name'],
      'server_ip' => $info['server_ip'],
      'company_count' => (int)$info['company_count'],
    ]);
    exit;
  }
}
?>
