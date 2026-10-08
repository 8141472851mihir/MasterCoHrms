<?php 

include_once 'common/object.php';
error_reporting(0);
extract(array_map("test_input" , $_POST));
?>
<option value="">-- Select --</option>
<?php
  $qs=$d->select("states","country_id=$country_id AND flag=1");
    while ($sData=mysqli_fetch_array($qs)) {
 ?>
 <option value="<?php echo $sData['state_id'];?>"><?php echo $sData['name'];?></option>

<?php }  ?>