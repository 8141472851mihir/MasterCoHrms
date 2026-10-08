<?php
	
include_once 'common/object.php';
error_reporting(0);
	extract(array_map("test_input" , $_POST));
	$timeline_id = $d->sanitizeActionIdAsInt($timeline_id ?? ($_POST['timeline_id'] ?? 0));
	$sosa_city_id = $d->sanitizeActionIdAsInt($sosa_city_id ?? ($_POST['sosa_city_id'] ?? 0));
	if (isset($sosa_city_id) && $sosa_city_id > 0) {
?>
<div class="form-group row">
	 
	<div class="col-sm-6"> <b>Company Not Posted</b>

		 

          

		<?php
		$post_log_master = $d->select("post_log_master"," post_id = '$timeline_id' and status !='202'   ");
		$sosa = array();
		 
		while ($post_log_master_data = mysqli_fetch_array($post_log_master)) {
		$sosa[] = (int)$post_log_master_data['society_id'];
		 
		}


		$post_log_master2 = $d->select("post_log_master"," post_id = '$timeline_id' and status ='202'  ");
		 
		$failure_array = array();
		while ($post_log_master_data2 = mysqli_fetch_array($post_log_master2)) {
		 
		$failure_array[$post_log_master_data2['society_id'].'_'.$post_log_master_data2['post_id']] = $post_log_master_data2['result'];
		}


		$sosa = implode(",",  $sosa);
			$con ="";
			$con2 ="";
		if(!empty($sosa)){
			$con = "and society_id not in ($sosa) ";
			$con2 = "and society_master.society_id   in ($sosa) ";
		} else {
			$con2 = "and society_master.society_id  =0  ";
		}
  
				$query = $d->select("society_master","  city_id = '$sosa_city_id'  	$con   "); 

				if(mysqli_num_rows($query) > 0){
				?>
              <label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
			  <input   type="checkbox" class="chk_boxes" value="0" name="society_id[]">
			 <span class="custom-control-description">Check All</span>
		    </label>


				<?php } else { ?>
					 <br>
					 <span class="text-danger" ><b>Published in all Company</b></span> 
				 <?php }
		while ($society_master_data = mysqli_fetch_array($query)) {
		
		?>
		
		<label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
			<input   type="checkbox" class="pagePrivilege" value="<?php echo $society_master_data['society_id']; ?>" name="society_id[]">
			
			<span class="custom-control-description"><?php echo $society_master_data['society_name']; ?></span>
			<span id="result_<?php echo $society_master_data['society_id']; ?>"></span>
			<input type="hidden" id="val_<?php echo $society_master_data['society_id']; ?>" value="1" />
			<?php $res = $failure_array[$society_master_data['society_id'].'_'.$timeline_id]; 

			if(!empty($res)){?>
				<span class="text-danger"> - <?php echo $res; ?></span>
			<?php } ?> 
		</label>
		
		
		
		<?php  } ?>
	</div>
	<div class="col-sm-6"> <b>Company Posted</b>
		<?php
			
				$query = $d->select("society_master,post_log_master","  post_log_master.post_id = '$timeline_id' and society_master.city_id = '$sosa_city_id' AND   post_log_master.status !='202' 	$con2 group by society_master.society_id    ");
				$cnt = 1;
		while ($society_master_data = mysqli_fetch_array($query)) {
		
		?>
		
		
		
		<label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
			 
			 
			<span class="custom-control-description"><?php echo $cnt.'). '.$society_master_data['society_name']; ?></span>

			<?php $cls =""; 
			if($society_master_data['status'] =="200"){
				$cls ="text-success"; 
			} else {
				$cls ="text-danger"; 
			}
			?>
			<span class="<?php echo $cls;?>" > - <?php echo $society_master_data['result']; ?></span>
		</label>
		
		
		<?php $cnt++; } ?>
	</div>
</div>
<?php
}
?><script type="text/javascript">

  $(function() {

    $('.chk_boxes').click(function() {

        $('.pagePrivilege').prop('checked', this.checked);

    });

});

</script>