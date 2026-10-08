<?php 
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST) )
{
  if(isset($_POST['setCountryLanguage']) && $_POST['csrf']==$_SESSION['token']){


    $lId = (int)$lId;
    $m->set_data('language_id',$lId);

    $countryIds = array_map('intval', $_POST['country_id'] ?? []);
    $existingByCsc = [];
    if (!empty($countryIds)) {
      $countryIdsIn = implode(',', array_map('intval', $countryIds));
      $qqq = $d->select(
        "country_state_city_language",
        "language_id='$lId' AND cat_type=0 AND common_id_csc IN ($countryIdsIn)"
      );
      while ($er = mysqli_fetch_array($qqq)) {
        $existingByCsc[(int)$er['common_id_csc']] = true;
      }
    }

    for ($i=0; $i <count($_POST['country_id']) ; $i++) { 

      $common_id_csc= (int)$_POST['country_id'][$i];
      $language_value_name= $_POST['language_value_name'][$i];
      $m->set_data('common_id_csc',$common_id_csc);
      $m->set_data('language_value_name',$language_value_name);
      $m->set_data('cat_type',0);
   
         $a  =array(
          'language_id'=> $m->get_data('language_id'),
          'common_id_csc'=> $m->get_data('common_id_csc'),
          'language_value_name'=> $m->get_data('language_value_name'),
          'cat_type'=> $m->get_data('cat_type'),
         
        );

        if (!empty($existingByCsc[$common_id_csc])) {
          $q=$d->update("country_state_city_language",$a,"language_id='$lId' AND common_id_csc='$common_id_csc' AND cat_type=0");
        } else if($language_value_name!='') {
          $q=$d->insert("country_state_city_language",$a);
        }

        
    }
    if($q>0) {
       $_SESSION['msg']="Updated Successfully";
      header("location:../countryLanguage?lId=$lId");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../countryLanguage?lId=$lId");
    }
  }

   if(isset($_POST['setStateLanguage']) && $_POST['csrf']==$_SESSION['token']){
    

    $lId = (int)$lId;
    $m->set_data('language_id',$lId);

    $stateIds = array_map('intval', $_POST['state_id'] ?? []);
    $existingByCsc = [];
    if (!empty($stateIds)) {
      $stateIdsIn = implode(',', array_map('intval', $stateIds));
      $qqq = $d->select(
        "country_state_city_language",
        "language_id='$lId' AND cat_type=1 AND common_id_csc IN ($stateIdsIn)"
      );
      while ($er = mysqli_fetch_array($qqq)) {
        $existingByCsc[(int)$er['common_id_csc']] = true;
      }
    }

    for ($i=0; $i <count($_POST['state_id']) ; $i++) { 

      $common_id_csc= (int)$_POST['state_id'][$i];
      $language_value_name= $_POST['language_value_name'][$i];
      $m->set_data('common_id_csc',$common_id_csc);
      $m->set_data('language_value_name',$language_value_name);
      $m->set_data('cat_type',1);
   
         $a  =array(
          'language_id'=> $m->get_data('language_id'),
          'common_id_csc'=> $m->get_data('common_id_csc'),
          'language_value_name'=> $m->get_data('language_value_name'),
          'cat_type'=> $m->get_data('cat_type'),
         
        );

        if (!empty($existingByCsc[$common_id_csc])) {
          $q=$d->update("country_state_city_language",$a,"language_id='$lId' AND common_id_csc='$common_id_csc' AND cat_type=1");
        } else if($language_value_name!='') {
          $q=$d->insert("country_state_city_language",$a);
        }

        
    }
    if($q>0) {
       $_SESSION['msg']="Updated Successfully";
      header("location:../stateLanguage?lId=$lId");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../stateLanguage?lId=$lId");
    }
  }


   if(isset($_POST['setCityLanguage']) && $_POST['csrf']==$_SESSION['token']){
    

    $lId = (int)$lId;
    $m->set_data('language_id',$lId);

    $cityIds = array_map('intval', $_POST['city_id'] ?? []);
    $existingByCsc = [];
    if (!empty($cityIds)) {
      $cityIdsIn = implode(',', array_map('intval', $cityIds));
      $qqq = $d->select(
        "country_state_city_language",
        "language_id='$lId' AND cat_type=2 AND common_id_csc IN ($cityIdsIn)"
      );
      while ($er = mysqli_fetch_array($qqq)) {
        $existingByCsc[(int)$er['common_id_csc']] = true;
      }
    }

    for ($i=0; $i <count($_POST['city_id']) ; $i++) { 

      $common_id_csc= (int)$_POST['city_id'][$i];
      $language_value_name= $_POST['language_value_name'][$i];
      $m->set_data('common_id_csc',$common_id_csc);
      $m->set_data('language_value_name',$language_value_name);
      $m->set_data('cat_type',2);
   
         $a  =array(
          'language_id'=> $m->get_data('language_id'),
          'common_id_csc'=> $m->get_data('common_id_csc'),
          'language_value_name'=> $m->get_data('language_value_name'),
          'cat_type'=> $m->get_data('cat_type'),
         
        );

        if (!empty($existingByCsc[$common_id_csc])) {
          $q=$d->update("country_state_city_language",$a,"language_id='$lId' AND common_id_csc='$common_id_csc' AND cat_type=2");
        } else if($language_value_name!='') {
          $q=$d->insert("country_state_city_language",$a);
        }

        
    }
    if($q>0) {
       $_SESSION['msg']="Updated Successfully";
      header("location:../cityLanguage?lId=$lId&&sId=$sId");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../cityLanguage?lId=$lId&&sId=$sId");
    }
  }

} else{
  header('location:../login');
}
?>
