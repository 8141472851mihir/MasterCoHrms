<?php
  
include_once 'common/object.php';
error_reporting(0);
//  ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL); 
  $base_url=$m->base_url();
  extract(array_map("test_input" , $_POST));
  if (isset($masterAuthPassword)) {
?>


<div class="form-group row">

  <div class="col-sm-6"> <b>Not Posted</b>

          

    <?php
    $post_log_master = $d->select("auth_log_master"," auth_password = '$masterAuthPassword'   ");
    $success_array = array();
    $failure_array = array();
    while ($post_log_master_data = mysqli_fetch_array($post_log_master)) {
      if ($post_log_master_data['status']=='200') {
        array_push($success_array,$post_log_master_data['society_id']);
      } else {
        array_push($failure_array,$post_log_master_data['society_id']);
      }
     
    }


    
        $ids = join("','",$success_array);
          if ($ids!="") {
            if ($city_id=='') {
              $query = $d->select("society_master" ,"society_id NOT IN ('$ids') ","order by society_id  DESC");
            } else {
              $query = $d->select("society_master" ,"city_id = '$city_id' AND society_id NOT IN ('$ids') ","order by society_id  DESC");
            }
          } else {
            $query = $d->select("society_master" ,"","order by society_id  DESC");
          }

        if(isset($query) && mysqli_num_rows($query) > 0){
        ?>
        <label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
        <input   type="checkbox" class="chk_boxes" value="0" name="society_id[]">
       <span class="custom-control-description">Check All</span>
        </label>


        <?php } else { ?>
           <br>
           <span class="text-danger" ><b>Published in all Company</b></span> 
         <?php }
        if (isset($query)) {
    while ($society_master_data = mysqli_fetch_array($query)) {
    
    ?>
    
    <label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
      <input   type="checkbox" class="pagePrivilege" value="<?php echo $society_master_data['society_id']; ?>" name="society_id[]">
      
      <span class="custom-control-description"><?php echo $society_master_data['society_name']; ?>-<?php echo $society_master_data['city_name']; ?></span>
      <span id="result_<?php echo $society_master_data['society_id']; ?>"></span>
      <input type="hidden" id="val_<?php echo $society_master_data['society_id']; ?>" value="1" />
      <?php $res = $failure_array[$society_master_data['society_id']]; 

      if(!empty($res)){?>
        <span class="text-danger"> - <?php echo $res; ?></span>
      <?php } ?> 
    </label>
    
    
    
    <?php  } } ?>
  </div>
  <div class="col-sm-6"> <b>Company Changed Password</b>
    <?php

      if (isset($city_id) && $city_id>0) {
        $appendCityQuery = " AND society_master.city_id='$city_id'";
      }
      if (isset($state_id) && $state_id>0) {
        $appendCityState = " AND society_master.state_id='$state_id'";
      }
      
        $queryPost111 = $d->select("society_master,auth_log_master","auth_log_master.society_id=society_master.society_id AND auth_log_master.auth_password='$masterAuthPassword' AND auth_log_master.status=200 $appendCityQuery $appendCityState","ORDER BY society_master.society_id DESC");
     
        $cnt = 1;
        if (isset($queryPost111) ) {
          echo '('.mysqli_num_rows($queryPost111).')';
    while ($society_master_data = mysqli_fetch_array($queryPost111)) {
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
    
    
    <?php $cnt++; } } ?>
  </div>
</div>
<?php  }?>

<script type="text/javascript">

  $(function() {

    $('.chk_boxes').click(function() {

        $('.pagePrivilege').prop('checked', this.checked);

    });

});

</script>