<?php 

include_once 'common/object.php';
error_reporting(0);
session_start();
extract(array_map("test_input" , $_POST));

$qb=$d->select("society_master","society_id='$society_id'");
$bData=mysqli_fetch_array($qb);
if ($bData['splash_colour']=="") {
	$splash_colour = "#fffff";
} else {
	$splash_colour = $bData['splash_colour'];
}

if ($bData['splash_image']=="") {
	$splash_image = "logo.png";
} else {
	$splash_image = $bData['splash_image'];
}

?>

 <form id="tenantForm" action="controller/buildingController.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="changeSplash" value="changeSplash" id="changeSplash">
	<input type="hidden" name="society_id" value="<?php echo $society_id; ?>" id="society_id">
	<input type="hidden" name="countryId" value="<?php echo $bData['country_id']; ?>" id="countryId">
	<input type="hidden" name="sId" value="<?php echo $bData['state_id']; ?>" id="sId">
	<input type="hidden" name="cId" value="<?php echo $bData['city_id']; ?>" id="cId">
	<input type="hidden" name="splash_image_old" value="<?php echo $splash_image; ?>" id="splash_image_old">
	<input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" id="token">
    <div class="form-group row" >
       <label class="col-lg-3 col-form-label form-control-label">Background Colour </label>
       <div class="col-lg-3">
       <input onchange="test(this)"  type="color" id="splash_colour" name="splash_colour" value="<?php if($splash_colour!="" ){ echo $splash_colour; } ?>"> 
     </div>
      <label class="col-lg-2 col-form-label form-control-label">Logo </label>
       <div class="col-lg-4">
       	<input  accept="image/png" type="file" id="splash_image" name="splash_image" class="form-control-file border">
       </div>
    </div>
    <div class="form-group row" >
    	 <div class="col-lg-12 text-center">
    	 	<div id="elementToChange" style="width: 230px;height: 400px;border: 1px solid;margin: auto;<?php if($splash_colour!="" ) { echo 'background-color:'.$splash_colour; } ?>">
    	 		<img id="blah"   width="135" style="margin-top: 50%;" src="../img/society_requests/<?php echo $splash_image; ?>">
    	 	</div>
	    </div>
   	</div>     
    <div class="form-group row">
	    <div class="col-lg-12 text-center">
	      <input type="submit"  class="btn btn-danger" name=""  value="Update">
	    </div>
	</div>
 </form>
 <?php if ($bData['splash_image']!="") { ?>
  <form id="tenantForm" action="controller/buildingController.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="removeSplash" value="removeSplash" id="removeSplash">
	<input type="hidden" name="society_id" value="<?php echo $society_id; ?>" id="society_id">
	<input type="hidden" name="countryId" value="<?php echo $bData['country_id']; ?>" id="countryId">
	<input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" id="token">
	
	<input type="hidden" name="sId" value="<?php echo $bData['state_id']; ?>" id="sId">
	<input type="hidden" name="cId" value="<?php echo $bData['city_id']; ?>" id="cId">
	 <div class="form-group row">
	    <div class="col-lg-12 text-center">
	      <input type="submit"  class="btn btn-warning" name=""  value="Remove & Set Default">
	    </div>
	</div>
</form>
<?php } ?>
<script src="assets/js/jquery.min.js"></script>
<?php //IS_577 jquery.validate.min.js ?>
<script src="assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript">
	function test(t) {
		var newColour = t.value;
		$('#elementToChange').css('background-color', newColour);
	}
  function readURL(input) {
 

 
//IS_952 && input.files[0].size<= 3000000
    if (input.files && input.files[0] && input.files[0].size<= 3000000 ) {
      var reader = new FileReader();

      reader.onload = function(e) {
        $('#blah').attr('src', e.target.result);
      }

      reader.readAsDataURL(input.files[0]);
    } 
    //IS_952
    else {
       $('#blah').attr('src','../img/society_requests/<?php echo $splash_image; ?>');
      
    }
    //IS_952
  }

  $("#splash_image").change(function() {
    readURL(this);
  });



//IS_577

//IS_577
</script>