<?php 

include_once 'common/object.php';
error_reporting(0);
extract(array_map("test_input" , $_POST));
?>
<option value="">-- Select --</option>

<?php
   $qcity=$d->select("cities","state_id=$state_id AND flag=1");
   while ($cityData=mysqli_fetch_array($qcity)) {
 ?>
 <option value="<?php echo $cityData['city_id'];?>"><?php echo $cityData['name'];?></option>

<?php }  ?>