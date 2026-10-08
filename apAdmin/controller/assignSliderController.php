<?php  include '../common/objectController.php';

  if(isset($_POST) && !empty($_POST) ){
    extract($_POST);
    if (isset($assignSliders)) {
      $count = count($app_slider_id);
      if ($count<=0) {
        $_SESSION['msg1'] ="Please Select at least one Slider";
        header("location:../companyBanners?id=$society_id");
        exit();
      }
      for ($i=0; $i < $count; $i++) { 
        $m->set_data('society_id',$society_id);
        $m->set_data('app_slider_id',$app_slider_id[$i]);

        $a = array(
          'society_id'=>$m->get_data('society_id'),
          'app_slider_id'=>$m->get_data('app_slider_id'),
        );
        $id = $app_slider_id[$i];
        $query = $d->insert("app_common_slider_master",$a);
        if ($query>0) {
          $d->insert_log_specific("$society_id","$bms_admin_id","$created_by","Slider - $id Assigned to the Society",2);
        }
      }
      if ($query>0) {
        $_SESSION['msg'] ="Sliders Added";
        header("location:../companyBanners?id=$society_id");
      } else{
        $_SESSION['msg1']="Something Wrong";
        header("location:../companyBanners?id=$society_id");
      }
    }
  } else{
    header('location:../login');
  }
?>
