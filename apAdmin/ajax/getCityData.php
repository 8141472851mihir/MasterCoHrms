<?php
include '../common/objectController.php';

if(isset($_POST['city_id']) && $_POST['city_id'] != '') {
    $city_id = $d->sanitizeActionIdAsInt($_POST['city_id']);
    
    $q = $d->selectRow("c.city_id, c.name as city_name, c.state_id, s.name as state_name, s.country_id, co.name as country_name", 
                       "cities c LEFT JOIN states s ON s.state_id = c.state_id LEFT JOIN countries co ON co.country_id = s.country_id", 
                       "c.city_id='$city_id'");
    
    if(mysqli_num_rows($q) > 0) {
        $data = mysqli_fetch_array($q);
        $response = array(
            'success' => true,
            'city_id' => $data['city_id'],
            'city_name' => $data['city_name'],
            'state_id' => $data['state_id'],
            'state_name' => $data['state_name'],
            'country_id' => $data['country_id'],
            'country_name' => $data['country_name']
        );
    } else {
        $response = array('success' => false);
    }
    
    echo json_encode($response);
} else {
    echo json_encode(array('success' => false));
}
?>
