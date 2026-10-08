<?php 
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST) )
{
  if(isset($_POST['addPackage']) && $_POST['csrf']==$_SESSION['token']){
    

    $m->set_data('app_package_name',$app_package_name);
    $m->set_data('is_package_all',$is_package_all);
    $m->set_data('societyId',$societyId);
   
     $a  =array(
      'app_package_name'=> $m->get_data('app_package_name'),
      'is_package_all'=> $m->get_data('is_package_all'),
      'society_id'=> $m->get_data('societyId'),
     
    );

    $qqq=$d->select("gatekeeper_app_access","app_package_name='$app_package_name' AND society_id=0 OR society_id='$societyId'");
    if (mysqli_num_rows($qqq)>0) {
      $_SESSION['msg1']="Already Added";
      header("location:../unlockPackages");
      exit();
    }

    
    $q=$d->insert("gatekeeper_app_access",$a);

    if($q>0) {
       $_SESSION['msg']="Added Successfully";
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Package $app_package_name Added");
      header("location:../unlockPackages");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../unlockPackages");
    }
  }


  if(isset($_POST['addPackageReq']) && $_POST['csrf']==$_SESSION['token']){
    

      $m->set_data('app_package_name',$app_package_name_r);
     
       $a  =array(
        'app_package_name'=> $m->get_data('app_package_name'),
      );

      $qqq=$d->select("gatekeeper_app_access","app_package_name='$app_package_name_r'");
      if (mysqli_num_rows($qqq)>0) {
        $d->delete("gatekeeper_app_access_request","packClass='$app_package_name_r'");
        $_SESSION['msg1']="Already Added";
        header("location:../requestPacakges");
        exit();
      }

    
    $q=$d->insert("gatekeeper_app_access",$a);

    if($q>0) {
      $d->delete("gatekeeper_app_access_request","packClass='$app_package_name_r'");
       $_SESSION['msg']="Added Successfully";
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Package $app_package_name_r Added From Request");
      header("location:../requestPacakges");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../requestPacakges");
    }
  }


} else{
  header('location:../login');
}
?>
