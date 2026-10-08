<?php
extract($_POST);?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Recent Activities Data</h4>
       
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="recentpublishSocetyDataFrm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
              <input type="hidden" name="countryId" id="countryId" value="<?php echo $country_id;?>">
              <input type="hidden" name="sId" id="sId" value="<?php echo $state_id;?>">
              <input type="hidden" name="cId" id="cId" value="<?php echo $city_id;?>">
              <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" />
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Days <span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" autocomplete="off" maxlength="2" min="1" max="90" class="form-control" name="days" value="30" required="">
                </div>
              </div>
              <?php
                $today = date("Y-m-d");
                ?>
                <!-- <input type="hidden" name="banner_url" id="banner_url" value="<?php // echo $kbgPhoto?>"> -->
                <div class="form-group row">
                  <div class="col-sm-6"> <b>Not Sync Company</b>
                    <?php
                    $post_log_master = $d->select("society_resent_analytics_master","update_date='$today'");
                    $success_array = array();
                    $failure_array = array();
                    while($post_log_master_data = mysqli_fetch_array($post_log_master)){
                      if($post_log_master_data['status']=='200'){
                        array_push($success_array,$post_log_master_data['society_id']);
                      }else{
                        array_push($failure_array,$post_log_master_data['society_id']);
                      }
                    }
                    $ids = join("','",$success_array);
                    $query = $d->select("society_master" ," society_id NOT IN ('$ids') ","order by society_id  DESC");
                    if(mysqli_num_rows($query) > 0){
                      ?>
                      <label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
                        <input type="checkbox" class="chk_boxes" value="0" name="society_id[]">
                        <span class="custom-control-description">Check All</span>
                      </label>
                      <?php
                    }else{ ?>
                      <br>
                      <span class="text-danger" ><b>Data Sync Company </b></span> 
                      <?php
                    }
                    while($society_master_data = mysqli_fetch_array($query)){
                      ?>
                      <label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
                        <input   type="checkbox" class="pagePrivilege" value="<?php echo $society_master_data['society_id']; ?>" name="society_id[]">
                        <span class="custom-control-description"><?php echo $society_master_data['society_name']; ?> (<?php echo $society_master_data['city_name']; ?>)</span>
                        <span id="result_<?php echo $society_master_data['society_id']; ?>"></span>
                        <input type="hidden" id="val_<?php echo $society_master_data['society_id']; ?>" value="1" />
                        <?php
                        $res = $society_master_data['society_id'];
                        if(!empty($res)){ ?>
                          <!-- <span class="text-danger"> - <?php // echo $res; ?></span> -->
                          <?php
                        } ?> 
                      </label>
                      <?php
                    } ?>
                  </div>
                  <div class="col-sm-6"> <b>Today Sync Data</b>
                    <?php
                    $queryPost = $d->select("society_master,society_resent_analytics_master","society_resent_analytics_master.city_id = '$city_id' AND society_resent_analytics_master.update_date = '$today' AND   society_resent_analytics_master.status ='200' AND society_master.society_id=society_resent_analytics_master.society_id");
                    $cnt = 1;
                    echo '('.mysqli_num_rows($queryPost).')';
                    while($society_master_data = mysqli_fetch_array($queryPost)){
                      ?>
                      <label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
                        <span class="custom-control-description"><?php echo $cnt.'). '.$society_master_data['society_name']; ?> (<?php echo $society_master_data['city_name']; ?>)</span>
                        <?php
                        $cls ="";
                        if($society_master_data['status'] =="200"){
                          $cls ="text-success"; 
                        }else{
                          $cls ="text-danger"; 
                        }
                        ?>
                        <span class="<?php echo $cls;?>" > - <?php echo $society_master_data['last_updated_date']; ?></span>
                      </label>
                      <?php $cnt++; 
                    } ?>
                  </div>
                </div>
                
              <span id="result"></span>
              <div class="form-footer text-center">
                <button type="submit" name="publishPost" value="publishPost" class="btn btn-sm btn-success publishPost"><i class="fa fa-check-square-o"></i> Get Data</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
  $(function() {
    $('.chk_boxes').click(function() {
      $('.pagePrivilege').prop('checked', this.checked);
    });
  });
</script>