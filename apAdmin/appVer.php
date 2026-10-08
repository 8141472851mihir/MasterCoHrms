<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Minimum App Version</h4>
        
      </div>

    </div>
    <!-- End Breadcrumb-->

    <div class="row">
     

<div class="col-lg-12">
 <div class="card">
  <div class="card-body">
    <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
      <li class="nav-item">
        <a href="javascript:void();" data-target="#userApp" data-toggle="pill" class="nav-link active"><i class="fa fa-android"></i> <span class="hidden-xs">User Android</span></a>
      </li>
      <li class="nav-item">
        <a href="javascript:void();" data-target="#admin" data-toggle="pill" class="nav-link"><i class="fa fa-user"></i> <span class="hidden-xs">Admin Android</span></a>
      </li>
      
      <li class="nav-item">
        <a href="javascript:void();" data-target="#ios" data-toggle="pill" class="nav-link"><i class="fa fa-apple"></i> <span class="hidden-xs">User IOS</span></a>
      </li>
      <li class="nav-item">
        <a href="javascript:void();" data-target="#iosadmin" data-toggle="pill" class="nav-link"><i class="fa fa-user"></i> <span class="hidden-xs">Admin IOS</span></a>
      </li>
      <li class="nav-item">
        <a href="javascript:void();" data-target="#faceAttendance" data-toggle="pill" class="nav-link"><i class="fa fa-smile-o"></i> <span class="hidden-xs">Face Attendance</span></a>
      </li>
     

    </ul>
    <div class="tab-content p-3">
     
      
<div class="tab-pane active" id="userApp">
  <form action="controller/profileController.php" method="post" enctype="multipart/form-data">
    <?php 
      if(isset($bms_admin_id)) {
      $q=$d->select("version_master","version_app='1' AND mobile_app=1");
      $data=mysqli_fetch_array($q);
      extract($data);
      } ?>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Version <span class="required">*</span></label>
      <div class="col-lg-9">
        <input type="hidden" name="version_app" value="<?php echo $version_app; ?>">
        <input type="hidden" name="mobile_app" value="<?php echo $mobile_app; ?>">
        <input type="hidden" name="version_id" value="<?php echo $version_id; ?>">
        <input class="form-control" name="version_code" type="text" value="<?php echo $version_code; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Version View<span class="required">*</span></label>
      <div class="col-lg-9">
        <input class="form-control" name="version_name_view" type="text" value="<?php echo $version_name_view; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Language Version<span class="required">*</span></label>
      <div class="col-lg-9">
        <input class="form-control" name="language_version" type="number" value="<?php echo $language_version; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">FACE SDK Key </label>
      <div class="col-lg-9">
        <textarea name="face_sdk_key" class="form-control bg-light" rows="5" type="text" readonly disabled><?php echo $face_sdk_key; ?></textarea>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Last Modify Date</label>
      <div class="col-lg-9">
        <p><?php echo $modify_date; ?></p>
      </div>
    </div>
    
   
   
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label"></label>
      <div class="col-lg-9">
        
        <input type="hidden" name="updateVerUser" value="updateVerUser">
        <input type="submit" class="btn btn-primary" name=""  value="Update">
      </div>
    </div>
  </form>
</div>

<div class="tab-pane" id="admin">
        
      <div class="">
        <form action="controller/profileController.php" method="post" enctype="multipart/form-data">
    <?php 
      if(isset($bms_admin_id)) {
      $q=$d->select("version_master","version_app='3' AND mobile_app=1");
      $data=mysqli_fetch_array($q);
      extract($data);
      } ?>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Version View<span class="required">*</span></label>
      <div class="col-lg-9">
        <input type="hidden" name="version_app" value="<?php echo $version_app; ?>">
        <input type="hidden" name="mobile_app" value="<?php echo $mobile_app; ?>">
        <input type="hidden" name="version_id" value="<?php echo $version_id; ?>">

        <input class="form-control" name="version_code" type="text" value="<?php echo $version_code; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Version View<span class="required">*</span></label>
      <div class="col-lg-9">
        <input class="form-control" name="version_name_view" type="text" value="<?php echo $version_name_view; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">FACE SDK Key </label>
      <div class="col-lg-9">
        <textarea name="face_sdk_key" class="form-control bg-light" rows="5" type="text" readonly disabled><?php echo $face_sdk_key; ?></textarea>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Last Modify Date</label>
      <div class="col-lg-9">
        <p><?php echo $modify_date; ?></p>
      </div>
    </div>
    
   
   
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label"></label>
      <div class="col-lg-9">
        
        <input type="hidden" name="updateVerUser" value="updateVerUser">
        <input type="submit" class="btn btn-primary" name=""  value="Update">
      </div>
    </div>
  </form>
</div>
</div>

<div class="tab-pane" id="faceAttendance">
        
      <div class="">
        <form action="controller/profileController.php" method="post" enctype="multipart/form-data">
    <?php 
      if(isset($bms_admin_id)) {
      $q=$d->select("version_master","version_app='4' AND mobile_app=1");
      $data1=mysqli_fetch_array($q);
      extract($data1);
      } ?>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Version View<span class="required">*</span></label>
      <div class="col-lg-9">
        <input type="hidden" name="version_app" value="<?php echo $version_app; ?>">
        <input type="hidden" name="mobile_app" value="<?php echo $mobile_app; ?>">
        <input type="hidden" name="version_id" value="<?php echo $version_id; ?>">

        <input class="form-control" name="version_code" type="text" value="<?php echo $version_code; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Version View<span class="required">*</span></label>
      <div class="col-lg-9">
        <input class="form-control" name="version_name_view" type="text" value="<?php echo $version_name_view; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">FACE SDK Key </label>
      <div class="col-lg-9">
        <textarea name="face_sdk_key" class="form-control bg-light" rows="5" type="text" readonly disabled><?php echo $face_sdk_key; ?></textarea>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Last Modify Date</label>
      <div class="col-lg-9">
        <p><?php echo $modify_date; ?></p>
      </div>
    </div>
    
   
   
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label"></label>
      <div class="col-lg-9">
        <input type="hidden" name="updateVerUser" value="updateVerUser">
        <input type="submit" class="btn btn-primary" name=""  value="Update">
      </div>
    </div>
  </form>
</div>
</div>



<div class="tab-pane" id="ios">
        
      <div class="">
        <form action="controller/profileController.php" method="post" enctype="multipart/form-data">
    <?php 
      if(isset($bms_admin_id)) {
      $q=$d->select("version_master","version_app='1' AND mobile_app=2");
      $data=mysqli_fetch_array($q);
      extract($data);
      } ?>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Version <span class="required">*</span></label>
      <div class="col-lg-9">
        <input type="hidden" name="version_app" value="<?php echo $version_app; ?>">
        <input type="hidden" name="mobile_app" value="<?php echo $mobile_app; ?>">
        <input type="hidden" name="version_id" value="<?php echo $version_id; ?>">

        <input class="form-control" name="version_code" type="text" value="<?php echo $version_code; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Version View<span class="required">*</span></label>
      <div class="col-lg-9">
        <input class="form-control" name="version_name_view" type="text" value="<?php echo $version_name_view; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Language Version<span class="required">*</span></label>
      <div class="col-lg-9">
        <input class="form-control" name="language_version" type="number" value="<?php echo $language_version; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">FACE SDK Key </label>
      <div class="col-lg-9">
        <textarea name="face_sdk_key" class="form-control bg-light" rows="5" type="text" readonly disabled><?php echo $face_sdk_key; ?></textarea>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Last Modify Date</label>
      <div class="col-lg-9">
        <p><?php echo $modify_date; ?></p>
      </div>
    </div>
   
   
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label"></label>
      <div class="col-lg-9">
        
        <input type="hidden" name="updateVerUser" value="updateVerUser">
        <input type="submit" class="btn btn-primary" name=""  value="Update">
      </div>
    </div>
  </form>
</div>
</div>

<div class="tab-pane" id="iosadmin">
        
      <div class="">
        <form action="controller/profileController.php" method="post" enctype="multipart/form-data">
    <?php 
      if(isset($bms_admin_id)) {
      $q=$d->select("version_master","version_app='3' AND mobile_app=2");
      $data=mysqli_fetch_array($q);
      extract($data);
      } ?>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Version View<span class="required">*</span></label>
      <div class="col-lg-9">
        <input type="hidden" name="version_app" value="<?php echo $version_app; ?>">
        <input type="hidden" name="mobile_app" value="<?php echo $mobile_app; ?>">
        <input type="hidden" name="version_id" value="<?php echo $version_id; ?>">
        
        <input class="form-control" name="version_code" type="text" value="<?php echo $version_code; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Version View<span class="required">*</span></label>
      <div class="col-lg-9">
        <input class="form-control" name="version_name_view" type="text" value="<?php echo $version_name_view; ?>">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">FACE SDK Key </label>
      <div class="col-lg-9">
        <textarea name="face_sdk_key" class="form-control bg-light" rows="5" type="text" readonly disabled><?php echo $face_sdk_key; ?></textarea>
      </div>
    </div>
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label">Last Modify Date</label>
      <div class="col-lg-9">
        <p><?php echo $modify_date; ?></p>
      </div>
    </div>
   
   
   
    <div class="form-group row">
      <label class="col-lg-3 col-form-label form-control-label"></label>
      <div class="col-lg-9">
        
        <input type="hidden" name="updateVerUser" value="updateVerUser">
        <input type="submit" class="btn btn-primary" name=""  value="Update">
      </div>
    </div>
  </form>
</div>
</div>



</div>
</div>
</div>
</div>

</div>

</div>
<!-- End container-fluid-->
</div><!--End content-wrapper-->

  <script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
  function readURL(input) {

  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {
      $('#blah').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$("#imgInp").change(function() {
  readURL(this);
});
</script>