<?php 

include_once 'common/object.php';
error_reporting(0);
extract(array_map("test_input" , $_POST));



?>
<option value="">-- Select --</option>

<?php
   $q = $d->select("society_master","city_id='$city_id'","order by society_id  DESC");
   while ($data=mysqli_fetch_array($q)) {
 ?>
 <option value="<?php echo $data['society_id'];?>"><?php echo $data['society_name'];?></option>

<?php }  ?>