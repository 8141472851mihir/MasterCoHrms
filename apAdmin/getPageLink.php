<?php

include_once 'common/object.php';

session_start();

extract(array_map("test_input" , $_POST));

if(isset($app_menu_id) && $app_menu_id != "") {
    $data=$d->selectArray("resident_app_menu ","app_menu_id = '$app_menu_id'");
    extract($data);

?>

    <div class="row"> 
        <label for="page_link" class="col-form-label col-sm-3">Page Link <span class="text-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" class="form-control" id="page_link" name="page_link" value="<?php echo $page_link ?? ''; ?>" autocomplete="off" maxlength="250" required oninput="this.value = this.value.replace(/\s+/g, '')" />    
        </div>
    </div>
    
    <div class="form-footer text-center mt-3">
        <input type="hidden" name="action" value="update_page_link" />          
        <input type="hidden" name="app_menu_id" value="<?php echo $app_menu_id; ?>" />
        <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" id="token">
        
        <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> Update </button>
    </div>

<?php }elseif(isset($app_menu_utility_id) && $app_menu_utility_id != ""){
    $data=$d->selectArray("resident_app_menu_utility ","app_menu_id = '$app_menu_utility_id'");
    extract($data);
?>

    <div class="row"> 
        <label for="page_link" class="col-form-label col-sm-3">Page Link <span class="text-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" class="form-control" id="page_link" name="page_link" value="<?php echo $page_link ?? ''; ?>" autocomplete="off" maxlength="250" required oninput="this.value = this.value.replace(/\s+/g, '')" />    
        </div>
    </div>
    
    <div class="form-footer text-center mt-3">
        <input type="hidden" name="action" value="update_utility_page_link" />          
        <input type="hidden" name="app_menu_utility_id" value="<?php echo $app_menu_utility_id; ?>" />
        <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" id="token">
        
        <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> Update </button>
    </div>

<?php } else{
    echo "Something Wrong. Try again after sometime.";
} 

?>
