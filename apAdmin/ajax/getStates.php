<?php
include '../common/objectController.php';

if(isset($_POST['country_id']) && $_POST['country_id'] != '') {
    $country_id = $d->sanitizeActionIdAsInt($_POST['country_id']);
    
    $q = $d->select("states", "country_id='$country_id'", "ORDER BY name ASC");
    
    echo '<option value="">--- Select State ---</option>';
    while($data = mysqli_fetch_array($q)) {
        echo '<option value="'.$data['state_id'].'">'.$data['name'].'</option>';
    }
} else {
    echo '<option value="">--- Select State ---</option>';
}
?>
