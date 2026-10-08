<?php
include '../common/objectController.php';

if(isset($_POST['state_id']) && $_POST['state_id'] != '') {
    $state_id = $d->sanitizeActionIdAsInt($_POST['state_id']);
    
    $q = $d->select("cities", "state_id='$state_id'", "ORDER BY name ASC");
    
    echo '<option value="">--- Select City ---</option>';
    while($data = mysqli_fetch_array($q)) {
        echo '<option value="'.$data['city_id'].'">'.$data['name'].'</option>';
    }
} else {
    echo '<option value="">--- Select City ---</option>';
}
?>
