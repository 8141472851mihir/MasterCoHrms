<?php 
 include '../common/objectController.php';
/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */
// print_r($_POST);
if(isset($_POST) && !empty($_POST) )//it can be $_GET doesn't matter
{
  // add main menu
  if(isset($_POST['menu_name'])){
    $m->set_data('menu_name',$menu_name);
      $m->set_data('menu_link',$menu_link);
      $m->set_data('menu_icon',$menu_icon);
      $m->set_data('sub_menu',$sub_menu);
      $m->set_data('status',$status);
      $m->set_data('order_no',$order_no);
      $m->set_data('created_by',$created_by);
      $a =array(
        'menu_name'=> $m->get_data('menu_name'),
          'menu_link'=> $m->get_data('menu_link'),
          'menu_icon'=>$m->get_data('menu_icon'),
          'sub_menu'=>$m->get_data('sub_menu'),
          'status'=>$m->get_data('status'),
          'order_no'=>$m->get_data('order_no'),
          'created_by'=>$m->get_data('created_by'),
      );
      $q=$d->insert("master_menu",$a);
      if($q>0) {
        // create new file for new menu url
        if(!file_exists("../".$menu_link.".php") && $sub_menu==0){
          $myfile = fopen("../".$menu_link.".php", "w"); 
        }
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Menu Added");
        $_SESSION['msg']="New menu successfully  added.";
        header("location:../mainMenu");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../menu");
      }
  }
  
  
  // Edit main menu
  if(isset($_POST['menu_nameEdit'])){
    $m->set_data('menu_nameEdit',$menu_nameEdit);
      $m->set_data('menu_link',$menu_link);
      $m->set_data('menu_icon',$menu_icon);
      $m->set_data('sub_menu',$sub_menu);
      $m->set_data('status',$status);
      $m->set_data('order_no',$order_no);
      $m->set_data('updated_by',$updated_by);
      $a =array(
        'menu_name'=> $m->get_data('menu_nameEdit'),
          'menu_link'=> $m->get_data('menu_link'),
          'menu_icon'=>$m->get_data('menu_icon'),
          'sub_menu'=>$m->get_data('sub_menu'),
          'status'=>$m->get_data('status'),
          'order_no'=>$m->get_data('order_no'),
          'updated_by'=>$m->get_data('updated_by'),
      );
      // print_r($a);
      $q=$d->update("master_menu",$a,"menu_id='$menu_id'");
      if($q>0) {
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Updated Menu");
        $_SESSION['msg']=" Menu Successfully  Updated.";
        header("location:../mainMenu");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../menu");
      }
  }
  // add sub menu
  if(isset($_POST['SubmenuAdd'])){
    $m->set_data('parent_menu_id',$parent_menu_id);
    $m->set_data('menu_name',$sub_menu_name);
      $m->set_data('menu_link',$menu_link);
      $m->set_data('status',$status);
      $m->set_data('order_no',$order_no);
      $m->set_data('created_by',$created_by);
      $a =array(
        'parent_menu_id'=> $m->get_data('parent_menu_id'),
        'menu_name'=> $m->get_data('menu_name'),
          'menu_link'=> $m->get_data('menu_link'),
          'status'=>$m->get_data('status'),
          'order_no'=>$m->get_data('order_no'),
          'created_by'=>$m->get_data('created_by'),
      );
      $q=$d->insert("master_menu",$a);
      if($q>0) {
        // create new file
        if(!file_exists("../".$menu_link.".php")){
        $myfile = fopen("../".$menu_link.".php", "w"); 
        }
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Sub Menu Added");
        $_SESSION['msg']="New sub menu successfully  added.";
        header("location:../subMenu");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../addSubMenu");
      }
  }
  // Edit sub menu
  if(isset($_POST['SubmenuEdit'])){
    $m->set_data('parent_menu_id',$parent_menu_id);
    $m->set_data('menu_name',$sub_menu_name);
      $m->set_data('menu_link',$menu_link);
      $m->set_data('status',$status);
      $m->set_data('order_no',$order_no);
      $m->set_data('updated_by',$updated_by);
      $a =array(
        'parent_menu_id'=> $m->get_data('parent_menu_id'),
        'menu_name'=> $m->get_data('menu_name'),
          'menu_link'=> $m->get_data('menu_link'),
          'status'=>$m->get_data('status'),
          'order_no'=>$m->get_data('order_no'),
          'updated_by'=>$m->get_data('updated_by'),
      );
      $q=$d->update("master_menu",$a,"menu_id='$SubmenuEdit'");
      if($q>0) {
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Sub Menu Updated.");
        $_SESSION['msg']="Sub Menu Updated.";
        header("location:../subMenu");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../addSubMenu");
      }
  }
  // add new icon
  if(isset($_POST['icon_name'])){
    $m->set_data('icon_name',$icon_name);
    $m->set_data('icon_class',$icon_class);
      $m->set_data('status',$status);
      $m->set_data('created_by',$created_by);
      $a =array(
        'icon_name'=> $m->get_data('icon_name'),
        'icon_class'=> $m->get_data('icon_class'),
          'status'=>$m->get_data('status'),
          'created_by'=>$m->get_data('created_by'),
      );
      $q=$d->insert("icons",$a);
      if($q>0) {
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Icon Added.");
        $_SESSION['msg']="New Icon successfully  added.";
        header("location:../icons");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../icons");
      }
  }
  // delete Icon
  if(isset($_POST['deleteIcon'])) {
    $q=$d->delete("icons","icon_id='$icon_id'");
      if($q>0) {
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Icon Deleted.");
        $_SESSION['msg']="Icon Deleted  successfully.";
        header("location:../icons");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../icons");
      }
  }
  // delete Main Menu
  if(isset($_POST['menu_id_delete'])) {
    $menu_id_delete = $d->sanitizeActionIdAsInt($_POST['menu_id_delete']);
    $q=$d->delete("master_menu","menu_id='$menu_id_delete'");
    $q=$d->delete("master_menu","parent_menu_id='$menu_id_delete'");
      if($q>0) {
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Menu Deleted.");
        $_SESSION['msg']="Menu Deleted  successfully.";
        header("location:../mainMenu");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../mainMenu");
      }
  }
 
  // Add user Role
  if(isset($_POST['role_name'])){
    $_POST['pagePrivilege']=$_POST['pagePrivilege']??[];
    $_POST['menu_id']=$_POST['menu_id']??[];
    if (isset($_POST['menu_id'])) {
      $menu_id= implode(",", $_POST['menu_id']);
    }
    if (isset($_POST['sub_menu_id'])) {
      $sub_menu_id= implode(",", $_POST['sub_menu_id']);
    }
    $pagePrivilege= implode(",", $_POST['pagePrivilege']);
    $m->set_data('pagePrivilege',$pagePrivilege);
    $m->set_data('society_id',$society_id);
    $m->set_data('role_name',$role_name);
    $m->set_data('role_description',$role_description);
    $m->set_data('status',$status);
    $m->set_data('order_no',$order_no);
    $m->set_data('menu_id',$menu_id);
    $m->set_data('created_by',$created_by);
    $a =array(
      'society_id'=> $m->get_data('society_id'),
      'pagePrivilege'=>$m->get_data('pagePrivilege'),
      'role_name'=> $m->get_data('role_name'),
      'role_description'=> $m->get_data('role_description'),
      'role_status'=>$m->get_data('status'),
      'order_no'=>$m->get_data('order_no'),
      'menu_id'=>$m->get_data('menu_id'),
      'created_by'=>$m->get_data('created_by'),
    );
    $q=$d->insert("role_master",$a);
    if($q>0) {
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Role Type Added.");
      $_SESSION['msg']="New Role Successfully  Added.";
      header("location:../roleType");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../roleType");
    }
  }
  // Edit user Role
  if(isset($_POST['role_nameEdit'])){
    $_POST['pagePrivilege']=$_POST['pagePrivilege']??[];
    $_POST['menu_id']=$_POST['menu_id']??[];
    $pagePrivilege= implode(",", $_POST['pagePrivilege']);
    $m->set_data('pagePrivilege',$pagePrivilege);
    $menu_id= implode(",", $_POST['menu_id']);
    $m->set_data('society_id',$societyId);
    $m->set_data('role_nameEdit',$role_nameEdit);
    $m->set_data('role_description',$role_description);
    $m->set_data('status',$status);
    $m->set_data('order_no',$order_no);
    $m->set_data('menu_id',$menu_id);
      // $m->set_data('parent_menu_id',$parent_menu_id);
    $m->set_data('updated_by',$updated_by);
    $a =array(
      'society_id'=> $m->get_data('society_id'),
      'role_name'=> $m->get_data('role_nameEdit'),
      'role_description'=> $m->get_data('role_description'),
      'role_status'=>$m->get_data('status'),
      'order_no'=>$m->get_data('order_no'),
      'menu_id'=>$m->get_data('menu_id'),
      'updated_by'=>$m->get_data('updated_by'),
      'pagePrivilege'=>$m->get_data('pagePrivilege'),
    );
    $q=$d->update("role_master",$a,"role_id='$role_id'");
    $create_new_files='1';
    $global_role_id=$role_id;
    include_once '../common/accessControl.php'; 
    include_once '../common/sidebarController.php';
    if($q>0) {
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Role Type Updated");
      $_SESSION['msg']="Role Updated Successfully Added.";
      header("location:../roleType");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../roleType");
    }
  }
  // Delete Role Type
  if(isset($_POST['role_id_delete'])) {
    $role_id_delete = $d->sanitizeActionIdAsInt($_POST['role_id_delete']);
    $q=$d->delete("role_master","role_id='$role_id_delete'");
      if($q>0) {
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Role Type Deleted.");
        $_SESSION['msg']="Role Type Deleted  successfully.";
        header("location:../roleType");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../roleType");
      }
  }
  //Page Privilege
  if (isset($_POST['pagePri'])) {
    // print_r($_POST);
    $pagePrivilege= implode(",", $_POST['pagePrivilege']);
    $m->set_data('pagePrivilege',$pagePrivilege);
    $a =array(
          'pagePrivilege'=>$m->get_data('pagePrivilege'),
      );
    // print_r($a);
      $q=$d->update("role_master",$a,"role_id='$role_id'");
      if($q>0) {
        $create_new_files='1';
        $global_role_id=$role_id;
        include_once '../common/accessControl.php'; 
        include_once '../common/sidebarController.php';
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Page Previvileges Updated");
        $_SESSION['msg']="Privileges Updated Successfully";
        header("location:../roleType");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../roleType");
      }
  }
  //Pages
  if(isset($_POST['addPage'])){
    $m->set_data('parent_menu_id',$parent_menu_id);
    $m->set_data('menu_name',$sub_menu_name);
      $m->set_data('menu_link',$menu_link);
      $m->set_data('status',$status);
      $m->set_data('page_status',"1");
      $m->set_data('created_by',$created_by);
      $a =array(
        'parent_menu_id'=> $m->get_data('parent_menu_id'),
        'menu_name'=> $m->get_data('menu_name'),
          'menu_link'=> $m->get_data('menu_link'),
          'page_status'=>$m->get_data('page_status'),
          'status'=>$m->get_data('status'),
          'created_by'=>$m->get_data('created_by'),
      );
      $q=$d->insert("master_menu",$a);
      if($q>0) {
        // create new file
        if(!file_exists("../".$menu_link.".php")){
        $myfile = fopen("../".$menu_link.".php", "w"); 
        }
        $d->insert_log("$society_id","$bms_admin_id","$created_by","PageAdded");
        $_SESSION['msg']="New Page successfully  added.";
        header("location:../pages");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../pages");
      }
  }
  // Edit Pages
  if(isset($_POST['pagesEdit'])){
    $m->set_data('parent_menu_id',$parent_menu_id);
    $m->set_data('sub_menu_name',$sub_menu_name);
      $m->set_data('menu_link',$menu_link);
      $m->set_data('status',$status);
      $m->set_data('page_status',"1");
      $m->set_data('updated_by',$updated_by);
      $a =array(
        'parent_menu_id'=> $m->get_data('parent_menu_id'),
        'menu_name'=> $m->get_data('sub_menu_name'),
          'menu_link'=> $m->get_data('menu_link'),
          'status'=>$m->get_data('status'),
          'page_status'=>$m->get_data('page_status'),
          'updated_by'=>$m->get_data('updated_by'),
      );
      $q=$d->update("master_menu",$a,"menu_id='$menu_id'");
      if($q>0) {
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Page Updated.");
        $_SESSION['msg']="Page Updated.";
        header("location:../pages");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../pages");
      }
  }
  if(isset($_POST['page_id_delete'])) {
    $page_id_delete = $d->sanitizeActionIdAsInt($_POST['page_id_delete']);
    $q=$d->delete("master_menu","menu_id='$page_id_delete'");
      if($q>0) {
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Menu Deleted.");
        $_SESSION['msg']="Page Deleted  successfully.";
        header("location:../pages");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../pages");
      }
  }
  //Delete Sub Menu
  if(isset($_POST['sub_menu_id_delete'])) {
    $sub_menu_id_delete = $d->sanitizeActionIdAsInt($_POST['sub_menu_id_delete']);
    $q=$d->delete("master_menu","menu_id='$sub_menu_id_delete'");
      if($q>0) {
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Menu Deleted");
        $_SESSION['msg']="Menu Deleted  successfully.";
        header("location:../subMenu");
      } else {
        $_SESSION['msg1']="Something Wrong";
        header("location:../subMenu");
      }
  }
}
else{
  header('location:../login');
 }
 ?>
