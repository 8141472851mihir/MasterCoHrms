<?php 
  if(isset($_POST['festival_id'])) {
    $btnName="Update";
    extract(array_map("test_input" , $_POST));
    $q=$d->select("festival_master","festival_id='$festival_id'");
    $data=mysqli_fetch_array($q);
  } else { $btnName="Add"; }


?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="addFestival" action="controller/festivalController.php" enctype="multipart/form-data" method="post">
              <h4 class="form-header text-uppercase">
                <i class="fa fa-gift"></i>
                 Festivals
              </h4>
              <div class="form-group row">
                <label for="input-10" class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                  <?php if(isset($_POST['festival_id'])) { ?>
                  <input maxlength="20" type="text" class="form-control text-capitalize" value="<?php echo $data['festival_name']; ?>" id="input-10" name="festival_name" required="" maxlength="20">
                  <input type="hidden" name="festival_id" value="<?php echo $data['festival_id']; ?>">
                  <?php } else {?>
                  <input  maxlength="20" type="text" class="form-control text-capitalize" id="input-10" name="festival_name" required="" maxlength="20">
                 <?php } ?>
                </div>
              </div>
             
              <div class="form-group row">
                <label for="input-16" class="col-sm-2 col-form-label">Date<span class="text-danger">*</span></label>
                <div class="col-sm-10">
                  <?php if(isset($_POST['festival_id'])) { ?>
                  <input type="text" class="form-control" value="<?php echo $data['festival_date']; ?>" id="autoclose-datepicker" name="festival_date" required="">
                  <?php } else {?>
                  <input type="text" required="" class="form-control" id="autoclose-datepicker" name="festival_date">
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="festival_image" class="col-sm-2 col-form-label">Image<?php if(!isset($festival_id)){ ?> <span class="text-danger">*</span> <?php } ?></label>
                <div class="col-sm-10">
                  <?php if(isset($_POST['festival_id'])) { ?>
                  <input accept="image/*" type="file" class="form-control-file border photoOnly" value="<?php echo $data['festival_image']; ?>" id="festival_image" name="festival_image"  >
                  <input type="hidden" name="old_festival_image" id="old_festival_image" value="<?php echo $data['festival_image']; ?>">
                  <?php } else {?>
                  <input accept="image/*" type="file" class="form-control-file border photoOnly" id="festival_image" name="festival_image" required="" >
                 <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">View<span class="text-danger">*</span></label>
                <div class="col-sm-4">
                  <input type="radio" name="view_status" <?php if(isset($festival_id)){ if ($data['festival_view_status']==0) {echo "checked"; }} else { echo "checked";} ?>  value="0"><label class="pl-1">Single</label>
                  <input type="radio" name="view_status" <?php if(isset($festival_id)){if ($data['festival_view_status']==1) {echo "checked"; }} ?>  value="1"><label class="pl-1">Multiple</label>
                </div>
                 
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Country</label>
                <div class="col-sm-10" >
                  <select  type="text" class="form-control single-select" name="country_id" >
                    <option value=""> All Country </option>
                    <?php 
                     $qc=$d->select("countries","flag=1");
                      while ($cData=mysqli_fetch_array($qc)) {
                     ?>
                      <option  <?php if($cData['country_id']==$data['country_id']){ echo 'selected'; } ?>  value="<?php echo $cData['country_id'];?>"><?php echo $cData['name'];?> </option>
                      <?php }?>
                    </select>
                </div>
                
              </div>
              <div class="form-group row">
                <label for="input-12" class="col-sm-2 col-form-label">Youtube Video Id </label>
                <div class="col-sm-10">
                  <input type="text" class="form-control"  name="festival_video" minlength="5" maxlength="40" value="<?php echo $data['festival_video']; ?>">
                </div>
              </div>
              <div class="form-group row">
                <label for="input-12" class="col-sm-2 col-form-label">URL </label>
                <div class="col-sm-10">
                  <input class="form-control" maxlength="100" type="url"  name="festival_url" value="<?php echo $data['festival_url']; ?>">
                </div>
              </div>
              <div class="form-group row">
                <label for="input-12" class="col-sm-2 col-form-label">Phone Number </label>
                <div class="col-sm-10">
                  <input class="form-control" id="festival_number" type="text" maxlength="13" minlength="8" name="festival_number" value="<?php echo $data['festival_number']; ?>">
                </div>
              </div>
              <div class="form-footer text-center">
                <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> SAVE</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>