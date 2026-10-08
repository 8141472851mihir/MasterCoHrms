<?php
extract(array_map("test_input", $_POST));
$q=$d->select("master_user_auth_master","","LIMIT 1");
$row=mysqli_fetch_array($q);
$masterAuth = $row['auth_passs'];
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
?>

<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Master Login Auth Management</h4>
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <a href="masterAuth?countryId=<?php echo $country_id; ?>&sId=<?php echo $state_id; ?>&cId=<?php echo $city_id; ?>" class="btn btn-primary btn-sm waves-effect waves-light"><i class="fa fa-back mr-1"></i> back</a>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-lg-12">
        <div class="card">
        <div class="card-body">
        <form id="publishMasterPasswordFrm" action="#" method="post" enctype="multipart/form-data">
          <div class="form-group row ">
             <input type="hidden" name="masterAuth" id="masterAuthPassword" value="<?php echo $row['auth_passs'] ; ?>">
             <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" />
            
            <div class="col-sm-6"> <b>Not Posted</b>
              <?php
              $post_log_master = $d->select("auth_log_master", " auth_password = '$masterAuth'   ");
              $success_array = array();
              $failure_array = array();
              while ($post_log_master_data = mysqli_fetch_array($post_log_master)) {
                if ($post_log_master_data['status'] == '200') {
                  array_push($success_array, $post_log_master_data['society_id']);
                } else {
                  array_push($failure_array, $post_log_master_data['society_id']);
                }
              }
              $ids= "";
              if (isset($success_array) && count($success_array)>0) {
                $ids = join("','", $success_array);
              }

              if (isset($ids) && $ids != "") {
                // code...
                if ($city_id == '') {
                  echo "Fff";
                  $query = $d->select("society_master", "society_id NOT IN ('$ids') ", "order by society_id  DESC");
                } else {
                  echo "Fff !!";
                  $query = $d->select("society_master", "city_id = '$city_id' AND society_id NOT IN ('$ids') ", "order by society_id  DESC");
                }
              } else {
                 $query = $d->select("society_master", "1=1 ", "order by society_id  DESC");
              }

              if (isset($query) && mysqli_num_rows($query) > 0) {
              ?>
                <label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
                  <input type="checkbox" class="chk_boxes" value="0" name="society_id[]">
                  <span class="custom-control-description">Check All</span>
                </label>


              <?php } else { ?>
                <br>
                <span class="text-danger"><b>Published in all Company</b></span>
              <?php }
              if (isset($query) && mysqli_num_rows($query) > 0) {
                while ($society_master_data = mysqli_fetch_array($query)) {

              ?>

                <label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
                  <input type="checkbox" class="pagePrivilege" value="<?php echo $society_master_data['society_id']; ?>" name="society_id[]">

                  <span class="custom-control-description"><?php echo $society_master_data['society_name']; ?>-<?php echo $society_master_data['city_name']; ?></span>
                  <span id="result_<?php echo $society_master_data['society_id']; ?>"></span>
                  <input type="hidden" id="val_<?php echo $society_master_data['society_id']; ?>" value="1" />
                  <?php $res = $failure_array[$society_master_data['society_id']] ?? "";

                  if (!empty($res)) { ?>
                    <span class="text-danger"> - <?php echo $res; ?></span>
                  <?php } ?>
                </label>



              <?php  }
              } ?>
            </div>
            <div class="col-sm-6"> <b>Company Changed Password</b>
              <?php
              if ($city_id == '') {
                $queryPost111 = $d->select("society_master,auth_log_master", "auth_log_master.society_id=society_master.society_id AND auth_log_master.auth_password='$masterAuthPassword' AND auth_log_master.status=200", "ORDER BY society_master.society_id DESC");
                echo "<br>";
                echo mysqli_num_rows($queryPost111) . ' Company Changed for View List Select Country, State & City';
              } else {
                $queryPost = $d->select("society_master,auth_log_master", "auth_log_master.society_id=society_master.society_id AND auth_log_master.auth_password='$masterAuthPassword' AND society_master.city_id='$city_id' AND auth_log_master.status=200", "ORDER BY society_master.society_id DESC");
              }
              $cnt = 1;
              if (isset($queryPost)) {
                echo '(' . mysqli_num_rows($queryPost) . ')';
                while ($society_master_data = mysqli_fetch_array($queryPost)) {
              ?>



                  <label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">


                    <span class="custom-control-description"><?php echo $cnt . '). ' . $society_master_data['society_name']; ?></span>

                    <?php $cls = "";
                    if ($society_master_data['status'] == "200") {
                      $cls = "text-success";
                    } else {
                      $cls = "text-danger";
                    }
                    ?>
                    <span class="<?php echo $cls; ?>"> - <?php echo $society_master_data['result']; ?></span>
                  </label>


              <?php $cnt++;
                }
              } ?>
            </div>
          </div>
          <div id="chkError" class=""></div>
          <div class="form-footer text-center">
             <a href="" class="btn btn-sm btn-danger"  type="button">Close </a>
            <button type="submit" name="publishPost" value="publishPost" class="btn btn-sm btn-success"><i class="fa fa-check-square-o"></i> Publish</button>
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