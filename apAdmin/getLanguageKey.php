<?php

include_once 'common/object.php';
// Mukesh start 6-6-24

session_start();

extract(array_map("test_input" , $_POST));

if(isset($app_menu_id) && $app_menu_id != "") {
    $data=$d->selectArray("resident_app_menu ","app_menu_id = '$app_menu_id'");
    extract($data);

?>

    <div class="row "> 
        
        <label for="language_key_name" class="col-form-label col-sm-3">Language Key Name </label>
        <input  type="text" class="form-control col-sm-6"   id="language_key_name" name="language_key_name"  value="<?php echo $language_key_name; ?>"   autocomplete="off" maxlength="250" />    
    </div>
    
    <div class="form-footer text-center">
        <input type="hidden" name="action" value="update_lang_key_name" />          
        <input type="hidden" name="app_menu_id" value="<?php echo $app_menu_id; ?>" />
        <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" id="token">
        
        <button type="submit"   class="btn btn-success"><i class="fa fa-check-square-o"></i> Update </button>
    </div>

<?php }elseif(isset($app_menu_utility_id) && $app_menu_utility_id != ""){
    $data=$d->selectArray("resident_app_menu_utility ","app_menu_id = '$app_menu_utility_id'");
    extract($data);
?>

    <div class="row "> 
        
        <label for="language_key_name" class="col-form-label col-sm-3">Language Key Name </label>
        <input  type="text" class="form-control col-sm-6"   id="language_key_name" name="language_key_name"  value="<?php echo $language_key_name; ?>"   autocomplete="off" maxlength="250" />    
    </div>
    
    <div class="form-footer text-center">
        <input type="hidden" name="action" value="update_utility_lang_key_name" />          
        <input type="hidden" name="app_menu_utility_id" value="<?php echo $app_menu_utility_id; ?>" />
        <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" id="token">
        
        <button type="submit"   class="btn btn-success"><i class="fa fa-check-square-o"></i> Update </button>
    </div>

<?php } else{
    echo "Something Wrong. Try again after sometime.";
} 
// Mukesh end 6-6-24

?>

