<?php 
include '../common/objectController.php';
// print_r($_POST);
if(isset($_POST) && !empty($_POST) )//it can be $_GET doesn't matter
{
  // add main menu
  if(isset($_POST['name'])){

      $mobile= (int)$mobile;
        if (strlen($mobile)<3 ) {
            $_SESSION['msg1']="Invalid Number";
            header("Location: ../emergencyNumbers");
            exit;
        }

      $m->set_data('society_id',$society_id);
      $m->set_data('name',$name);
      $m->set_data('designation',$designation);
      $m->set_data('mobile',$mobile);

      $a =array(
        'society_id'=> $m->get_data('society_id'),
        'name'=> $m->get_data('name'),
        'designation'=> $m->get_data('designation'),
        'mobile'=> $m->get_data('mobile'),
      );
    
      $q=$d->insert("emergemcy_number_list",$a);
    
      if($q>0) {

        $_SESSION['msg']="Number Added";
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Emergency Number Added");
        header("location:../emergencyNumbers");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../emergencyNumbers");
      }
  }


  // add main menu
  if(isset($_POST['name_edit'])){
      $mobile= (int)$mobile;
          if (strlen($mobile)<3 ) {
            $_SESSION['msg1']="Invalid Number";
            header("Location: ../emergencyNumbers");
            exit;
        }

      $m->set_data('society_id',$society_id);
      $m->set_data('name',$name_edit);
      $m->set_data('designation',$designation);
      $m->set_data('mobile',$mobile);

      $a =array(
        'society_id'=> $m->get_data('society_id'),
        'name'=> $m->get_data('name'),
        'designation'=> $m->get_data('designation'),
        'mobile'=> $m->get_data('mobile'),
      );
    
      $q=$d->update("emergemcy_number_list",$a,"emergency_id='$emergency_id'");
    
      if($q>0) {

        $_SESSION['msg']="Details Updated";
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Emergency Number Update");

        header("location:../emergencyNumbers");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../emergencyNumbers");
      }
  }


  if (isset($deleteParking)) {

       $q=$d->delete("parking_master","society_parking_id='$society_parking_id' AND society_id='$society_id'");
       $q=$d->delete("society_parking_master","society_parking_id='$society_parking_id' AND society_id='$society_id'");
    
      if($q>0) {
         $_SESSION['msg']="Parking Deleted";
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Parking Deleted");
        header("location:../parkings");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../parkings");
      }
  }


  
}
else{
  header('location:../login');
}
 ?>
