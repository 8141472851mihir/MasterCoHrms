<?php
include './common/object.php';
if(isset($_POST) && !empty($_POST))
{
  extract($_POST);
  if(isset($action) && $action=="fetch_module_data"){
    $moduleId = $_POST["module_id"];
    $moduleQuery = $d->select("module_master", "module_id = '$moduleId'", "");
    $module = mysqli_fetch_assoc($moduleQuery);
    $moduleName = $module['module_name'];
    $selectedMenus = ($module['menu_ids']!="")?(explode(",", $module['menu_ids'])):[];
    $selectedAppMenus = ($module['app_menu_ids']!="")?(explode(",", $module['app_menu_ids'])):[];
    // replace with crul
    $menuQuery = $d->select("resident_app_menu", "", "");
    $menus = [];
    while ($row = mysqli_fetch_assoc($menuQuery)) {
        $menus[] = ["id" => $row['app_menu_id'], "title" => $row['menu_title']];
    }
    // 
    $appMenuQuery = $d->select("resident_app_menu r", "NOT EXISTS ( SELECT 1 FROM module_master m WHERE FIND_IN_SET(r.app_menu_id, m.app_menu_ids) AND m.module_id != '$moduleId')", "");
    $appMenus = [];
    while ($row = mysqli_fetch_assoc($appMenuQuery)) {
        $appMenus[] = ["id" => $row['app_menu_id'], "title" => $row['menu_title']];
    }
    echo json_encode([
        "module_name" => $moduleName,
        "menus" => $menus,
        "app_menus" => $appMenus,
        "selected_menus" => $selectedMenus,
        "selected_app_menus" => $selectedAppMenus
    ]);
    exit;
  }else{
    echo "Something went wrong";
    exit;
  }
} else{
  echo "Something went wrong";
  exit;
}
?>