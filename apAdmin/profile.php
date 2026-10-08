<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">My Profile</h4>
      </div>
    </div>
    <!-- End Breadcrumb-->
<?php 
        if(isset($bms_admin_id)) {
          $q=$d->select("bms_admin_master","admin_id='$bms_admin_id'");
          $data=mysqli_fetch_array($q);
          extract($data);
        } ?>
    <div class="row">
      <div class="col-lg-4">
       <div class="card profile-card-2">
        <div class="card-img-block">
          <img class="img-fluid" src="img/31.jpg" alt="Card image cap">
        </div>
        <div class="card-body pt-5">
         <img id="blah"  onerror="this.src='img/user.png'" src="../img/profile/<?php echo $admin_profile; ?>"  width="75" height="75"   src="#" alt="your image" class='profile' />
         <h5 class="card-title"><?php echo $admin_name; ?></h5>
         <p class="card-text"><?php echo $society_name; ?></p>

       </div>

       <div class="card-body border-top">
         <div class="media align-items-center">
           <div>
             <i class="fa fa-phone"></i>
           </div>
           <div class="media-body text-left ml-3">
             <div class="progress-wrapper">
               <?php echo $data['country_code']; ?> <?php echo $d->encryptDecrypt("decrypt",$admin_mobile); ?>
             </div>                   
           </div>
         </div>
         <hr>
         <div class="media align-items-center">
           <div>
             <i class="fa fa-envelope"></i>
           </div>
           <div class="media-body text-left ml-3">
             <div class="progress-wrapper">
               <?php echo $d->encryptDecrypt("decrypt",$admin_email); ?>
             </div>                   
           </div>
         </div>
         <hr>



       </div>
     </div>

   </div>

   <div class="col-lg-8">
     <div class="card">
      <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
          <li class="nav-item">
            <a href="javascript:void();" data-target="#edit" data-toggle="pill" class="nav-link active"><i class="icon-note"></i> <span class="hidden-xs">Edit Profile</span></a>
          </li>
          <li class="nav-item">
            <a href="javascript:void();" data-target="#messages" data-toggle="pill" class="nav-link"><i class="fa fa-key"></i> <span class="hidden-xs">Change Password</span></a>
          </li>

        </ul>
        <div class="tab-content p-3">

          <div class="tab-pane" id="messages">

            <div class="">
             <?php //IS_577  signupForm to  chngProfileFrm?> 
             <form id="chngProfileFrm" action="controller/profileController.php" method="post">
              <input type="hidden" name="admin_mobile" value="<?php echo $d->encryptDecrypt("decrypt",$admin_mobile); ?>">
              <input type="hidden" name="admin_email" value="<?php echo $d->encryptDecrypt("decrypt",$admin_email); ?>">
              <input type="hidden" name="admin_name" value="<?php echo $admin_name; ?>">
              <div class="form-group row">
                <label class="col-lg-3 col-form-label form-control-label">Old Password <span class="required">*</span></label>
                <div class="col-lg-9">
                 <?php //IS_577   id="old_password"?> 
                 <input class="form-control" required="" name="old_password"  id="old_password" type="password" value="">
               </div>
             </div>
             <div class="form-group row">
              <label class="col-lg-3 col-form-label form-control-label">Password <span class="required">*</span></label>
              <div class="col-lg-9">
               <?php //IS_577   id="password"?> 
               <input class="form-control" required="" type="password" name="password" id="password" value="">
             </div>
           </div>
           <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label">Confirm password <span class="required">*</span></label>
            <div class="col-lg-9">
              <?php //IS_577   id="password2"?> 
              <input class="form-control" required="" name="password2" id="password2" type="password" value="">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label"></label>
            <div class="col-lg-9">
              <input type="submit" name="passwordChange" class="btn btn-primary" value="Change Password">
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="tab-pane active" id="edit">
      <?php //IS_573 signupForm to editProfileFrm ?> 
      <form id="editProfileFrm" action="controller/profileController.php" method="post" enctype="multipart/form-data">
        
        <div class="form-group row">
          <label class="col-lg-3 col-form-label form-control-label">Full Name <span class="required">*</span></label>
          <div class="col-lg-9">
            <?php //IS_573 id="admin_name" ?> 
            <input required="" class="form-control" name="admin_name" id="admin_name" type="text" value="<?php echo $admin_name; ?>">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-lg-3 col-form-label form-control-label">Mobile <span class="required">*</span></label>
          <div class="col-lg-3">
             <input type="hidden" value="<?php echo $country_code; ?>" id="country_code_get" name="">
              <select name="country_code" class="form-control single-select" id="country_code" required="">
                <?php include 'country_code_option_list.php'; ?>
              </select>
          </div>
          <div class="col-lg-6">

            <input class="form-control" readonly=""  type="text" value="<?php echo $d->encryptDecrypt("decrypt",$data['admin_mobile']); ?>">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-lg-3 col-form-label form-control-label">Email <span class="required">*</span></label>
          <div class="col-lg-9">
              <?php //IS_573  secretary_email to admin_email ?> 
            <input class="form-control" readonly="" type="email" id="admin_email" onblur="checkemailSociety()" name="admin_email" value="<?php echo $d->encryptDecrypt("decrypt",$data['admin_email']); ?>">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-lg-3 col-form-label form-control-label">Change profile</label>
          <div class="col-lg-9">

              <?php //IS_573   id="profile_image_old" ?> 

            <input class="form-control-file border photoOnly" id="imgInp" accept="image/*" name="profile_image" type="file">
            <input class="form-control-file border" value="<?php echo $admin_profile; ?>" name="profile_image_old" id="profile_image_old" type="hidden">

          </div>
        </div>


        <div class="form-group row">
          <label class="col-lg-3 col-form-label form-control-label"></label>
          <div class="col-lg-9">
            <input type="submit" class="btn btn-primary" name="updateProfile"  value="Update Profile">
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
<!-- End container-fluid-->
</div><!--End content-wrapper-->

<script src="assets/js/jquery.min.js"></script>
<?php //IS_577 jquery.validate.min.js ?>
<script src="assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript">
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
       $('#blah').attr('src','img/profile/<?php echo $admin_profile; ?>');
      
    }
    //IS_952
  }

  $("#imgInp").change(function() {
    readURL(this);
  });



//IS_577

//IS_577
</script>