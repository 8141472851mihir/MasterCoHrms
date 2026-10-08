<?php 
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST) )
{
  if(isset($_POST['festival_name']) && $_POST['csrf']==$_SESSION['token']){
    if (isset($active_status)) {
      $active_status = 0;
    }
    $file_festival_image = $_FILES['festival_image']['tmp_name'];
    if (file_exists($file_festival_image)) {
      $acceptable = array("jpeg","jpg","png");
      $extId = pathinfo($_FILES['festival_image']['name'], PATHINFO_EXTENSION);
      $dirPath = "../../img/festival/";
      if (in_array($extId, $acceptable) && (!empty($_FILES["festival_image"]["type"]))) {
        $temp = explode(".", $_FILES["festival_image"]["name"]);
        $festival_image = $festival_name.'_'.round(microtime(true)).'.'.end($temp);
        $destinationPath = $dirPath . $festival_image;
        $d->resizeImage($file_festival_image, $destinationPath, 1280, 720, $extId);
      }else{
        $_SESSION['msg1'] = "Invalid Photo";
        header("location:../festivals");
        exit();
      }
    }else {
      $festival_image = $old_festival_image;
    }
    $m->set_data('festival_name',$festival_name);
    $m->set_data('festival_date',$festival_date);
    $m->set_data('view_status',$view_status);
    $m->set_data('active_status',$active_status);
    $m->set_data('festival_image',$festival_image);
    $m->set_data('country_id',$country_id);
    $m->set_data('festival_number',$festival_number);
    $m->set_data('festival_video',$festival_video);
    $m->set_data('festival_url',$festival_url);
    $a =array(
      'festival_name'=> $m->get_data('festival_name'),
      'festival_date'=> $m->get_data('festival_date'),
      'festival_view_status'=> $m->get_data('view_status'),
      'festival_active_status'=> $m->get_data('active_status'),
      'festival_image'=> $m->get_data('festival_image'),
      'country_id'=> $m->get_data('country_id'),
      'festival_number'=> $m->get_data('festival_number'),
      'festival_video'=> $m->get_data('festival_video'),
      'festival_url'=> $m->get_data('festival_url'),
    );
    if (isset($festival_id)) {
      $_SESSION['msg']="Festival Updated";
      $q=$d->update("festival_master",$a,"festival_id='$festival_id'");
    }else{
      $_SESSION['msg']="Festival Added";
      $q=$d->insert("festival_master",$a);
    }

    if($q>0) {
      $d->insert_log("","$society_id","$bms_admin_id","$created_by","Festival Added");
      header("location:../manageFestivals");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../festivals");
    }
  }


} else{
  header('location:../login');
}
?>
