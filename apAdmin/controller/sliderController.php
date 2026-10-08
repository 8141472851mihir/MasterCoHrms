<?php 
include '../common/objectController.php';
// add new Notice Board
if(isset($_POST) && !empty($_POST) ){
  if(isset($_POST['deletesliderid'])) {
  $deletesliderid = $d->sanitizeActionIdAsInt($_POST['deletesliderid']);
    $q=$d->delete("app_slider_master","app_slider_id='$deletesliderid'");
    if($q==TRUE) {
        $_SESSION['msg']="Slider Deleted";
        $d->insert_log_specific("0","$bms_admin_id","$created_by","Slider-$deletesliderid Deleted",2);
        echo 1;
    } else {
        echo 0;
    }
  }

  if(isset($addSliderImage)) {
    $file_slider_image = $_FILES['slider_image']['tmp_name'];
    $newFileName = rand().$user_id;
    if (file_exists($file_slider_image)) {
      $acceptable = array("jpeg","jpg","png","gif");
      $extId = pathinfo($_FILES['slider_image']['name'], PATHINFO_EXTENSION);
      $dirPath = "../../img/sliders/";
      $maxsize    = 10097152;
      if (in_array($extId, $acceptable) && (!empty($_FILES["slider_image"]["type"]))) {
        $temp = explode(".", $_FILES["slider_image"]["name"]);
        $slider_image= $newFileName."_banner.".$extId;
        $destinationPath = $dirPath . $slider_image;
        $d->resizeImage($file_slider_image, $destinationPath, 1280, 720, $extId);
      } else {
        $_SESSION['msg1'] = "Invalid Image.";
        header("location:../sliderImages");
        exit();
      }
    } else {
      $_SESSION['msg1']="Invalid Image....";
      header("Location: ../sliderImages");
      exit;
    }

    $m->set_data('society_id', $society_id);
    $m->set_data('slider_image_name', $slider_image);
    $m->set_data('youtube_url', $youtube_url);
    $m->set_data('page_url', $page_url);
    $m->set_data('page_mobile', $page_mobile);
    $m->set_data('about_offer', $about_offer);
                
    $a = array(
      'society_id' => 0,
      'slider_image_name' => $m->get_data('slider_image_name'),
      'youtube_url' => $m->get_data('youtube_url'),
      'page_url' => $m->get_data('page_url'),
      'page_mobile' => $m->get_data('page_mobile'),
      'about_offer' => $m->get_data('about_offer'),
    );
    $q = $d->insert("app_slider_master", $a);
     $slider_id = $con->insert_id;
    if($q==TRUE) {
        $_SESSION['msg']="New Slider Added...";
        $d->insert_log_specific("0","$bms_admin_id","$created_by","New Slider-$slider_id Added.",2);
        header("Location: ../sliderImages");
    } else {
      $_SESSION['msg1']="Something Went Wrong.";
      header("Location: ../sliderImages");
    }
  }

  if (isset($editSliderImage)) {
    if (!empty($_FILES['slider_image']['name'])) {
      $file_slider_image = $_FILES['slider_image']['tmp_name'];
      $newFileName = rand().$user_id;
      if (file_exists($file_slider_image)) {
        $acceptable = array("jpeg","jpg","png","gif");
        $extId = pathinfo($_FILES['slider_image']['name'], PATHINFO_EXTENSION);
        $dirPath = "../../img/sliders/";
        $maxsize    = 10097152;
        if (in_array($extId, $acceptable) && (!empty($_FILES["slider_image"]["type"]))) {
          $temp = explode(".", $_FILES["slider_image"]["name"]);
          $slider_image= $newFileName."_banner.".$extId;
          $destinationPath = $dirPath . $slider_image;
          $d->resizeImage($file_slider_image, $destinationPath, 1280, 720, $extId);
        } else {
          $_SESSION['msg1'] = "Invalid Image.";
          header("location:../sliderImages");
          exit();
        }
      } else {
        $_SESSION['msg1']="Invalid Image....";
        header("Location: ../sliderImages");
        exit;
      }
    }
    $m->set_data('society_id', $society_id);
    if (!empty($_FILES['slider_image']['name'])) {
      $m->set_data('slider_image_name', $slider_image);
    }
    $m->set_data('youtube_url', $youtube_url);
    $m->set_data('page_url', $page_url);
    $m->set_data('page_mobile', $page_mobile);
    $m->set_data('about_offer', $about_offer);
    if (!empty($_FILES['slider_image']['name'])) {
      $a['slider_image_name'] = $m->get_data('slider_image_name');
    }
    $a['youtube_url'] = $m->get_data('youtube_url');
    $a['page_url'] = $m->get_data('page_url');
    $a['page_mobile'] = $m->get_data('page_mobile');
    $a['about_offer'] = $m->get_data('about_offer');
    $q = $d->update("app_slider_master", $a, "app_slider_id='$slider_id'");
    if ($q == TRUE) {
      $_SESSION['msg'] = "Slider Updated...";
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "Slider-$slider_id Updated", 2);
      header("Location: ../sliderImages");
    } else {
      $_SESSION['msg1'] = "Something Went Wrong.";
      header("Location: ../editSliderImage?id=$slider_id");
    }
  }

  if(isset($society_id_delete_from_slider)){
    $q=$d->delete("app_common_slider_master","app_slider_id='$id' AND society_id='$society_id_delete_from_slider'");
 
    if($q==TRUE) {
      $_SESSION['msg']="Society Removed Successfully..";
        $d->insert_log_specific("$society_id_delete_from_slider","$bms_admin_id","$created_by","Slider-$id Removed from the Society",2);
      $redirect = (isset($return_to_slider_images) && $return_to_slider_images) ? "../sliderImages?society_id=$society_id_delete_from_slider" : "../commonSliderSetting?id=$id";
      header("Location: " . $redirect);
    } else {
      $_SESSION['msg1']="Something Wrong";
      $redirect = (isset($return_to_slider_images) && $return_to_slider_images) ? "../sliderImages?society_id=$society_id_delete_from_slider" : "../commonSliderSetting?id=$id";
      header("Location: " . $redirect);
    }
  }

  if(isset($CommonSliderBtn)){

    if( (count($_POST['society_id']??[]) + count($_POST['selected_society']??[])) <= 0  ){
      $_SESSION['msg1']="Please Select Atleast One Society";
            header("location:../sliderImages");
            exit;
    }
    $app_common_slider_master_Added = $d->select("app_common_slider_master","  app_slider_id ='$app_slider_id' ");


    for ($i=0; $i < count($_POST['society_id']??[]) ; $i++) {
       $m->set_data('app_slider_id',$app_slider_id);
        $m->set_data('society_id',$_POST['society_id'][$i]);

        $a = array(
          'app_slider_id'=>$m->get_data('app_slider_id'),
          'society_id'=>$m->get_data('society_id'),
          'added_by_id'=>$bms_admin_id,
        );
      $q=$d->insert("app_common_slider_master",$a);
      if ($q>0) {
        $id = $_POST['society_id'][$i];
        $d->insert_log_specific("$id","$bms_admin_id","$created_by","Slider - $app_slider_id added to the Society",2);
      }
    }

    for ($i=0; $i < count($_POST['selected_society']??[]) ; $i++) {
      $m->set_data('app_slider_id',$app_slider_id);
      $m->set_data('society_id',$_POST['selected_society'][$i]);
        $a = array(
          'app_slider_id'=>$m->get_data('app_slider_id'),
          'society_id'=>$m->get_data('society_id'),
          'added_by_id'=>$bms_admin_id,
        );
      $q=$d->insert("app_common_slider_master",$a);
    }


    if($q){ 
      if(mysqli_num_rows($app_common_slider_master_Added)>0) {
         $_SESSION['msg']="Common Slider Updated";
         $msg= "Common Slider Updated";
       }else {
        $_SESSION['msg']="Slider added to the Society";
        $msg= "Slider added to the Society";
       }
      header("location:../commonSliderSetting?id=$app_slider_id");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../commonSliderSetting?id=$app_slider_id");
    } 
  }

  if(isset($CommonSliderCountryBtn)){

    if( (count($_POST['country_id']??[])) <= 0  ){
      $_SESSION['msg1']="Please Select Atleast One Country";
            header("location:../sliderImages");
            exit;
    }
    $app_common_slider_master_Added = $d->select("app_common_slider_master","  app_slider_id ='$app_slider_id' ");
    $num = 0;
    $countryIds = array_map('intval', $_POST['country_id'] ?? []);
    $countryIds = array_values(array_filter($countryIds));
    $countrySocieties = array();
    if (!empty($countryIds)) {
      $countryIdsIn = implode(',', $countryIds);
      $q = $d->select('society_master', "country_id IN ($countryIdsIn)");
      while ($countrydata = mysqli_fetch_array($q)) {
        $countrySocieties[] = $countrydata;
      }
    }
    $existingSliderSocieties = array();
    $countrySocietyIds = array();
    foreach ($countrySocieties as $_cs) {
      $countrySocietyIds[] = (int)$_cs['society_id'];
    }
    if (!empty($countrySocietyIds)) {
      $countrySocietyIdsIn = implode(',', $countrySocietyIds);
      $cPrefetch = $d->select('app_common_slider_master', "app_slider_id ='$app_slider_id' AND society_id IN ($countrySocietyIdsIn)");
      while ($cRow = mysqli_fetch_assoc($cPrefetch)) {
        $existingSliderSocieties[(int)$cRow['society_id']] = true;
      }
    }
    foreach ($countrySocieties as $countrydata) {
        $society = $countrydata['society_id'];
        $m->set_data('app_slider_id',$app_slider_id);
        $m->set_data('society_id',$society);

          $a = array(
            'app_slider_id'=>$m->get_data('app_slider_id'),
            'society_id'=>$m->get_data('society_id'),
            'added_by_id'=>$bms_admin_id, 
          );
          if(!isset($existingSliderSocieties[(int)$society])){
            if($d->insert("app_common_slider_master",$a)){
              $num++;
              $existingSliderSocieties[(int)$society] = true;
              $id = $_POST['society_id'][0] ?? $society;
              $d->insert_log_specific("$id","$bms_admin_id","$created_by","Slider added to the Society",2);
            }
          }              
    }

    if($num>0){
      $_SESSION['msg']="Slider added to the Society";
      $msg= "Slider added to the Society";
      $d->insert_log_specific("$society_id","$bms_admin_id","$created_by",$msg,2);
      header("location:../commonSliderSetting?id=$app_slider_id");
    } else {
      $_SESSION['msg1']="No societies in this country or already inserted";
      header("location:../commonSliderSetting?id=$app_slider_id");
    } 
  }

  if(isset($CommonSliderStateBtn)){

    if( (count($_POST['state_id']??[])) <= 0  ){
      $_SESSION['msg1']="Please Select Atleast One State";
            header("location:../sliderImages");
            exit;
    }
    $app_common_slider_master_Added = $d->select("app_common_slider_master","  app_slider_id ='$app_slider_id' ");
    $num = 0;
    $stateIds = array_map('intval', $_POST['state_id'] ?? []);
    $stateIds = array_values(array_filter($stateIds));
    $stateSocieties = array();
    if (!empty($stateIds)) {
      $stateIdsIn = implode(',', $stateIds);
      $q = $d->select('society_master', "state_id IN ($stateIdsIn)");
      while ($statedata = mysqli_fetch_array($q)) {
        $stateSocieties[] = $statedata;
      }
    }
    $existingSliderSocieties = array();
    $stateSocietyIds = array();
    foreach ($stateSocieties as $_ss) {
      $stateSocietyIds[] = (int)$_ss['society_id'];
    }
    if (!empty($stateSocietyIds)) {
      $stateSocietyIdsIn = implode(',', $stateSocietyIds);
      $cPrefetch = $d->select('app_common_slider_master', "app_slider_id ='$app_slider_id' AND society_id IN ($stateSocietyIdsIn)");
      while ($cRow = mysqli_fetch_assoc($cPrefetch)) {
        $existingSliderSocieties[(int)$cRow['society_id']] = true;
      }
    }
    foreach ($stateSocieties as $statedata) {
          $society = $statedata['society_id'];
          $m->set_data('app_slider_id',$app_slider_id);
          $m->set_data('society_id',$society);

            $a = array(
              'app_slider_id'=>$m->get_data('app_slider_id'),
              'society_id'=>$m->get_data('society_id'),
              'added_by_id'=>$bms_admin_id,
            );
            if(!isset($existingSliderSocieties[(int)$society])){
              if($d->insert("app_common_slider_master",$a)){
                $num++;
                $existingSliderSocieties[(int)$society] = true;
                $id = $_POST['society_id'][0] ?? $society;
                $d->insert_log_specific("$id","$bms_admin_id","$created_by","Slider added to the Society",2);
              }
            }              
    }

    if($num>0){
      $_SESSION['msg']="Slider added to the Society";
      $msg= "Slider added to the Society";
      $d->insert_log_specific("$society_id","$bms_admin_id","$created_by",$msg,2);
      header("location:../commonSliderSetting?id=$app_slider_id");
    } else {
      $_SESSION['msg1']="No societies in this state or already inserted";
      header("location:../commonSliderSetting?id=$app_slider_id");
    } 
  }

  if(isset($CommonSliderCityBtn)){

    if( (count($_POST['city_id']??[])) <= 0  ){
      $_SESSION['msg1']="Please Select Atleast One City";
            header("location:../sliderImages");
            exit;
    }
      $app_common_slider_master_Added = $d->select("app_common_slider_master","  app_slider_id ='$app_slider_id' ");
      $num = 0;
      $cityIds = array_map('intval', $_POST['city_id'] ?? []);
      $cityIds = array_values(array_filter($cityIds));
      $citySocieties = array();
      if (!empty($cityIds)) {
        $cityIdsIn = implode(',', $cityIds);
        $q = $d->select('society_master', "city_id IN ($cityIdsIn)");
        while ($citydata = mysqli_fetch_array($q)) {
          $citySocieties[] = $citydata;
        }
      }
      $existingSliderSocieties = array();
      $citySocietyIds = array();
      foreach ($citySocieties as $_cs) {
        $citySocietyIds[] = (int)$_cs['society_id'];
      }
      if (!empty($citySocietyIds)) {
        $citySocietyIdsIn = implode(',', $citySocietyIds);
        $cPrefetch = $d->select('app_common_slider_master', "app_slider_id ='$app_slider_id' AND society_id IN ($citySocietyIdsIn)");
        while ($cRow = mysqli_fetch_assoc($cPrefetch)) {
          $existingSliderSocieties[(int)$cRow['society_id']] = true;
        }
      }
      foreach ($citySocieties as $citydata) {
          $society = $citydata['society_id'];
          $m->set_data('app_slider_id',$app_slider_id);
          $m->set_data('society_id',$society);

            $a = array(
              'app_slider_id'=>$m->get_data('app_slider_id'),
              'society_id'=>$m->get_data('society_id') 
            );
            if(!isset($existingSliderSocieties[(int)$society])){
              if($d->insert("app_common_slider_master",$a)){
                $num++;
                $existingSliderSocieties[(int)$society] = true;
                $id = $_POST['society_id'][0] ?? $society;
                $d->insert_log_specific("$id","$bms_admin_id","$created_by","Slider added to the Society",2);
              }
            }               
      }

    if($num>0){
      $_SESSION['msg']="Slider added to the Society";
      $msg= "Slider added to the Society";
      $d->insert_log_specific("$society_id","$bms_admin_id","$created_by",$msg,2);
      header("location:../commonSliderSetting?id=$app_slider_id");
    } else {
      $_SESSION['msg1']="No societies in this city or already inserted";
      header("location:../commonSliderSetting?id=$app_slider_id");
    } 
  }

  if(isset($addSliderImageDefualt)) {
    $file_slider_image = $_FILES['slider_image']['tmp_name'];
    $newFileName = rand().$user_id;
    if (file_exists($file_slider_image)) {
      $acceptable = array("jpeg","jpg","png");
      $extId = pathinfo($_FILES['slider_image']['name'], PATHINFO_EXTENSION);
      $dirPath = "../../img/sliders/";
      $maxsize    = 2097152;
      if (in_array($extId, $acceptable) && (!empty($_FILES["slider_image"]["type"]))) {
        $temp = explode(".", $_FILES["slider_image"]["name"]);
        $slider_image = round(microtime(true)) . '.' . end($temp);
        $destinationPath = $dirPath . $slider_image;
        $d->resizeImage($file_slider_image, $destinationPath, 1280, 720, $extId);
      } else {
        $_SESSION['msg1'] = "Invalid  photo. Only  JPG and PNG are allowed.";
        header("location:../appSliderImages?society_id=$society_id");
        exit();
      }
    } else {
      $slider_image="default.png";
    }
    $m->set_data('society_id', 0);
    $m->set_data('slider_image_name', $slider_image);
                
    $a = array(
      'society_id' => $m->get_data('society_id'),
      'slider_image_name' => $m->get_data('slider_image_name'),
    );
    $q = $d->insert("app_slider_master", $a);
    if($q==TRUE) {
        $_SESSION['msg']="New Default Slider Added...";
        $d->insert_log_specific("$society_id","$bms_admin_id","$created_by","New Slider Added.",2);
        header("Location: ../appSliderImages?society_id=$society_id");
    } else {
      $_SESSION['msg1']="Something Went Wrong.";
      header("Location: ../appSliderImages?society_id=$society_id");
    }
  }

  if(isset($deleteSliderImage)) {

      $q=$d->delete("app_slider_master","app_slider_id='$app_slider_id_delete'");
      if($q==TRUE) {
           $_SESSION['msg']="Slider Deleted";
          $d->insert_log_specific("$society_id","$bms_admin_id","$created_by","Slider - $app_slider_id_delete Deleted",2);
          header("Location: ../sliderImages?society_id=$society_id");
      } else {
          header("Location: ../sliderImages?society_id=$society_id");
      }
  }

} ?>