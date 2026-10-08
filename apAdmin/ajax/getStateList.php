<?php 
include_once('../common/objectController.php');
extract(array_map("test_input" , $_POST));
if (isset($getStateList)) { ?>
	<option value=""></option>
	<?php 
	$country_id = $d->sanitizeActionIdAsInt($country_id ?? 0);
	$q =$d->select("states","country_id = '$country_id'"); 
	while ($data = mysqli_fetch_array($q)) {?>
	  <option value="<?php echo $data['state_id'] ?>"><?php echo $data['name'] ?></option>
	<?php } 
} 
?>