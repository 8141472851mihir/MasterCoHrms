<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include('common/object.php');
$query = $d->select("rent_sale_master","","ORDER BY rent_sale_id DESC");
if($query->num_rows > 0){
    $delimiter = ",";
    $filename = "properties" . date('Y-m-d H:i:s') . ".csv";
    $f = fopen('php://memory', 'w');
    $fields = array('Society','Country','State','City','User','Type','For','Category','Address','Contact No.','Bedroom','Bathroom','Kitchen','Balcony','Puja Ghar','Car Parking','Seat','Cabin','Floor No.','Furnishing','Carpet Area','Area','Price','Deposit','Negotiable','Other Charges','Maintainence','Stamp Duty','Status','Availibilty','Share with Agents','User type','Approved Status','Created Date',);
    fputcsv($f, $fields, $delimiter);
    while($row = mysqli_fetch_assoc($query)){
      
        /************************************************************************************/
        if($row['property_type']==0){$property_type = "Residential";}else{$property_type = "Commercial";}
        /************************************************************************************/
        if($row['property_for']==0){$property_for = "Rent";}else{$property_for = "Sale";}
        /************************************************************************************/
        if($row['stamp_duaty']==0){$stamp_duty = "Excluded";}else{$stamp_duty = "Included";}
        /************************************************************************************/
        if($row['share_with_agents']==0){$share_with_agents = "Yes";}else{$share_with_agents = "No";}
        /************************************************************************************/
        if($row['user_type']==0){$user_type = "User";}else{$user_type = "Agent";}
        /************************************************************************************/
        if($row['approved_status']==0){$approved_status = "Yes";}else{$approved_status = "No";}
        /************************************************************************************/
            $lineData = array($row['society_name'], $row['country_name'], $row['state_name'], $row['city_name'], $row['property_holder_name'],$property_type , $property_for, $row['property_category'], $row['property_address'], $row['contact_no'], $row['bedroom'], $row['bathroom'], $row['kitchen'], $row['balcony'], $row['puja_ghar'], $row['car_parking'], $row['seats'], $row['cabins'], $row['no_of_floors'], $row['furnishing'], $row['property_area_carpet'], $row['property_area_super'], $row['expected_price'], $row['deposit'], $row['negotiable'], $row['other_charges'], $row['maintence'], $stamp_duty, $row['property_status'], $row['avail_from'], $share_with_agents, $user_type, $approved_status, $row['modify_date']);
        // print_r($lineData);
        /************************************************************************************/
        fputcsv($f, $lineData, $delimiter);
    }
    fseek($f, 0);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    fpassthru($f);
}
exit;
?>