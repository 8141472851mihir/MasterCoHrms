<?php 

include_once 'common/object.php';
error_reporting(0);
extract(array_map("test_input",$_POST));
if(isset($business_categories)) {  ?>
<option value="">-- Select --</option>
 <?php 
  $q3=$d->select("business_categories","category_industry='$business_categories'","");
while ($blockRow=mysqli_fetch_array($q3)) {
 ?>
 <option value="<?php echo $blockRow['category_name'];?>"><?php echo $blockRow['category_name'];?></option>
<?php } }  ?>