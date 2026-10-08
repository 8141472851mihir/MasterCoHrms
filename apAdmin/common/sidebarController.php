<?php 
if((isset($create_new_files) && $create_new_files=="1") || !isset($_SESSION["menu_role_$global_role_id"]) || !file_exists("../img/menu_file_new_$global_role_id.json"))
{
  $q=$d->select("master_menu","page_status='0'","ORDER BY order_no ASC");
  $menuArray = array();
  $allMenus = array();
  $subMenusByParent = array();
  while ($data=mysqli_fetch_array($q)) {
    $allMenus[] = $data;
    if ($data['parent_menu_id'] != 0) {
      $subMenusByParent[$data['parent_menu_id']][] = $data;
    }
  }
  foreach ($allMenus as $data) {
      
    $menu_id=$data['menu_id'];   
    if($data['parent_menu_id']==0 && $data['sub_menu']==0) {
      if(in_array($menu_id, $accessMenuIdArr)){ 

        $menu = array(
          "menu_id" => $data['menu_id'],
          "menu_link" => $data['menu_link'],
          "menu_icon" => $data['menu_icon'],
          "menu_name" => $data['menu_name'],
          "language_key_name" => $data['language_key_name'],
          "subMenu"   => ""
        ); 
        array_push($menuArray, $menu);
      }
         
    }else  if($data['parent_menu_id']==0 && $data['sub_menu']==1){
      $subMenuArray = array();
      // $subMenuArray = array();
      $childSubMenus = isset($subMenusByParent[$menu_id]) ? $subMenusByParent[$menu_id] : array();
      $subMenuContent="";
      $subMenuCount = 0;
      foreach ($childSubMenus as $temData) {
        if(in_array($temData['menu_id'], $accessMenuIdArr)){
          $submenu = array(
            "menu_id" => $temData["menu_id"],
            "menu_link" => $temData["menu_link"],
            "menu_name" => $temData["menu_name"],
            "menu_icon" => $temData["menu_icon"],
            "language_key_name" => $temData["language_key_name"],
          );
            array_push($subMenuArray,$submenu);
            $subMenuCount++;
        } 
        if(in_array($temData['menu_id'], $pagePrivilegeArr)){
          $submenu = array(
            "menu_id" => $temData["menu_id"],
            "menu_link" => $temData["menu_link"],
            "menu_name" => $temData["menu_name"],
            "menu_icon" => $temData["menu_icon"],
            "language_key_name" => $temData["language_key_name"],
          );
            array_push($subMenuArray,$submenu);
        } 
      }
      if(!empty($subMenuArray) && $subMenuCount > 0)
      {  

        $menu = array(
          "menu_id" => $data['menu_id'],
          "menu_link" => $data['menu_link'],
          "menu_icon" => $data['menu_icon'],
          "menu_name" => $data['menu_name'],
          "language_key_name" => $data['language_key_name'],
          "subMenu"   => $subMenuArray
        );

        array_push($menuArray, $menu);

      }
    }
  } 
  
  $_SESSION["menu_role_$global_role_id"]="menu_file_new_$global_role_id.json";
  if(isset($create_new_files) && $create_new_files=="1"){
    file_put_contents("../../img/menu_file_new_$global_role_id.json", json_encode($menuArray));
  }else{
    file_put_contents("../img/menu_file_new_$global_role_id.json", json_encode($menuArray));
  }

  $sidebareData = file_get_contents("../img/menu_file_new_$global_role_id.json");
  $sidebareData = json_decode($sidebareData,true);
}else{
  $sidebareData = file_get_contents("../img/menu_file_new_$global_role_id.json");
  $sidebareData = json_decode($sidebareData,true);
}
?>
