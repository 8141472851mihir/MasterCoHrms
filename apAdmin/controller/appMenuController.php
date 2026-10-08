<?php 
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST) )
{

  if(isset($_POST['setClasCategoryLanguage']) && $_POST['csrf']==$_SESSION['token']){
    $language_id = (int)$language_id;
    $m->set_data('language_id',$language_id);

    $categoryIds = array_map('intval', $_POST['classified_category_id'] ?? []);
    $existingByCat = [];
    if (!empty($categoryIds)) {
      $categoryIdsIn = implode(',', array_map('intval', $categoryIds));
      $qqq = $d->select(
        "classified_category_language",
        "language_id='$language_id' AND cat_type=0 AND classified_category_id IN ($categoryIdsIn)"
      );
      while ($er = mysqli_fetch_array($qqq)) {
        $existingByCat[(int)$er['classified_category_id']] = true;
      }
    }

    for ($i=0; $i <count($_POST['classified_category_id']) ; $i++) { 

      $classified_category_id= (int)$_POST['classified_category_id'][$i];
      $language_category_name= $_POST['language_category_name'][$i];
      $m->set_data('classified_category_id',$classified_category_id);
      $m->set_data('language_category_name',$language_category_name);

      $a  =array(
        'language_id'=> $m->get_data('language_id'),
        'classified_category_id'=> $m->get_data('classified_category_id'),
        'language_category_name'=> $m->get_data('language_category_name'),
        'cat_type'=> 0,

      );


      if (!empty($existingByCat[$classified_category_id])) {
        $q=$d->update("classified_category_language",$a,"language_id='$language_id' AND classified_category_id='$classified_category_id' AND cat_type=0");
      } else if($language_category_name!='') {

        $q=$d->insert("classified_category_language",$a);
        $existingByCat[$classified_category_id] = true;
      }


    }
    
    if($q>0) {
     $_SESSION['msg']="Updated Successfully";
     $d->insert_log("$society_id","$bms_admin_id","$created_by","Category Language Name Updated");
     header("location:../classifiedCategoryLanguage?lId=$language_id");
   } else {
    $_SESSION['msg1']="Something Wrong";
    header("location:../classifiedCategoryLanguage?lId=$language_id");
  }
}


if(isset($_POST['setClasSubCategoryLanguage']) && $_POST['csrf']==$_SESSION['token']){


  $language_id = (int)$language_id;
  $m->set_data('language_id',$language_id);

  $subCategoryIds = array_map('intval', $_POST['classified_sub_category_id'] ?? []);
  $existingBySub = [];
  if (!empty($subCategoryIds)) {
    $subCategoryIdsIn = implode(',', array_map('intval', $subCategoryIds));
    $qqq = $d->select(
      "classified_category_language",
      "language_id='$language_id' AND cat_type=1 AND classified_category_id IN ($subCategoryIdsIn)"
    );
    while ($er = mysqli_fetch_array($qqq)) {
      $existingBySub[(int)$er['classified_category_id']] = true;
    }
  }

  for ($i=0; $i <count($_POST['classified_sub_category_id']) ; $i++) { 

    $classified_sub_category_id= (int)$_POST['classified_sub_category_id'][$i];
    $language_category_name= $_POST['language_category_name'][$i];
    $m->set_data('classified_sub_category_id',$classified_sub_category_id);
    $m->set_data('language_category_name',$language_category_name);

    $a  =array(
      'language_id'=> $m->get_data('language_id'),
      'classified_category_id'=> $m->get_data('classified_sub_category_id'),
      'language_category_name'=> $m->get_data('language_category_name'),
      'cat_type'=> 1,

    );


    if (!empty($existingBySub[$classified_sub_category_id])) {
      $q=$d->update("classified_category_language",$a,"language_id='$language_id' AND classified_category_id='$classified_sub_category_id' AND cat_type=1");
    } else if($language_category_name!='') {

      $q=$d->insert("classified_category_language",$a);
      $existingBySub[$classified_sub_category_id] = true;
    }


  }

  if($q>0) {
   $_SESSION['msg']="Updated Successfully";
   $d->insert_log("$society_id","$bms_admin_id","$created_by","Category Language Name Updated");
   header("location:../classifiedSubCategoryLanguage?lId=$language_id");
 } else {
  $_SESSION['msg1']="Something Wrong";
  header("location:../classifiedCategoryLanguage?lId=$language_id");
}
}



if(isset($_POST['action'])  && $_POST['action'] == "update_lang_key_name" && $_POST['csrf']==$_SESSION['token']){

  $m->set_data('language_key_name',$_POST['language_key_name']);

  $a  =array(
    'language_key_name'=> $m->get_data('language_key_name'),           
  );
  $q=$d->update("resident_app_menu",$a,"app_menu_id = '$app_menu_id'");

  if($q>0) {
    $_SESSION['msg']="Updated Successfully";
    $d->insert_log("$society_id","$bms_admin_id","$created_by"," App Menu Language Key Name Updated");
    header("location:../masterAppMenu");
  } else {
    $_SESSION['msg1']="Something Wrong";
    header("location:../masterAppMenu");
  }
}
if(isset($_POST['action'])  && $_POST['action'] == "update_utility_lang_key_name" && $_POST['csrf']==$_SESSION['token']){

  $m->set_data('language_key_name',$_POST['language_key_name']);

  $a  =array(
    'language_key_name'=> $m->get_data('language_key_name'),           
  );
  $q=$d->update("resident_app_menu_utility",$a,"app_menu_id = '$app_menu_utility_id'");

  if($q>0) {
    $_SESSION['msg']="Updated Successfully";
    $d->insert_log("$society_id","$bms_admin_id","$created_by"," App Menu Language Key Name Updated");
    header("location:../masterAppMenu");
  } else {
    $_SESSION['msg1']="Something Wrong";
    header("location:../masterAppMenu");
  }
}

if(isset($_POST['action'])  && $_POST['action'] == "update_page_link" && $_POST['csrf']==$_SESSION['token']){

  $m->set_data('page_link',$_POST['page_link']);

  $a  =array(
    'page_link'=> $m->get_data('page_link'),           
  );
  $q=$d->update("resident_app_menu",$a,"app_menu_id = '$app_menu_id'");

  if($q>0) {
    $_SESSION['msg']="Updated Successfully";
    $d->insert_log("$society_id","$bms_admin_id","$created_by"," App Menu Page Link Updated");
    header("location:../masterAppMenu");
  } else {
    $_SESSION['msg1']="Something Wrong";
    header("location:../masterAppMenu");
  }
}

if(isset($_POST['action'])  && $_POST['action'] == "update_utility_page_link" && $_POST['csrf']==$_SESSION['token']){

  $m->set_data('page_link',$_POST['page_link']);

  $a  =array(
    'page_link'=> $m->get_data('page_link'),           
  );
  $q=$d->update("resident_app_menu_utility",$a,"app_menu_id = '$app_menu_utility_id'");

  if($q>0) {
    $_SESSION['msg']="Updated Successfully";
    $d->insert_log("$society_id","$bms_admin_id","$created_by"," App Menu Page Link Updated");
    header("location:../masterAppMenu");
  } else {
    $_SESSION['msg1']="Something Wrong";
    header("location:../masterAppMenu");
  }
}



} else{
  header('location:../login');
}
?>
