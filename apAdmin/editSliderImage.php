<?php
  $slider_id = $d->sanitizeReportFilterIdAsInt($_GET['id'] ?? 0);
  if($slider_id <= 0){
    $_SESSION['msg1']="Invalid Request.";
  }
  $slider = $d->select("app_slider_master","app_slider_id='$slider_id'");
  if(mysqli_num_rows($slider)==0){
    $_SESSION['msg1']="Invalid Slider Details.";
  }
  $sliderdetails = mysqli_fetch_array($slider);

?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-6">
        <h4 class="page-title">App Sliders</h4>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="welcome">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">App Sliders</li>
        </ol>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <?php if(mysqli_num_rows($slider)==0) { ?>
              <h4 class="form-header text-uppercase">
              Invalid Slider Details.
              </h4>
            <?php }else{ ?>
            <form id="sliderImageAddValidationEdit" method="post" action="controller/sliderController.php" enctype="multipart/form-data">
              <h4 class="form-header text-uppercase">
              <i class="fa fa-address-book-o"></i>
              Edit Slider Details 
              </h4>
              <div class="form-group row">
                <label for="input-12" class="col-sm-2 col-form-label">Slider Image</label>
                <div class="col-sm-6">
                  <input type="hidden" name="slider_id" value="<?php echo $slider_id; ?>">
                  <input type="hidden" id="old_slider_image" value="<?php echo $sliderdetails['slider_image_name']; ?>">
                  <input class="form-control-file border" type="file" accept="image/*" name="slider_image">
                </div>
                <div class="col-sm-4">
                  <a href="<?= $base_url.'img/sliders/'.$sliderdetails['slider_image_name']?>" target="_blank"><img src="<?= $base_url.'img/sliders/'.$sliderdetails['slider_image_name']?>" width="250"></a>
                </div>
              </div>
              <div class="form-group row">
                <label for="input-12" class="col-sm-2 col-form-label">Youtube Video Id </label>
                <div class="col-sm-10">
                  <input type="text" class="form-control"  name="youtube_url" minlength="5" maxlength="40" value="<?=$sliderdetails['youtube_url']?>">
                </div>
              </div>
              <div class="form-group row">
                <label for="input-12" class="col-sm-2 col-form-label">URL </label>
                <div class="col-sm-10">
                  <input class="form-control" maxlength="100" type="url"  name="page_url" value="<?=$sliderdetails['page_url']?>">
                </div>
              </div>
              <div class="form-group row">
                <label for="input-12" class="col-sm-2 col-form-label">Phone Number </label>
                <div class="col-sm-10">
                  <input class="form-control" id="trlDays" type="text" maxlength="12"  name="page_mobile" value="<?php if($sliderdetails['page_mobile']!=0){ echo $sliderdetails['page_mobile'];}?>">
                </div>
              </div>
              <div class="form-group row">
                <label for="input-12" class="col-sm-2 col-form-label">About Offer </label>
                <div class="col-sm-10">
                  <textarea class="form-control"  type="text" maxlength="250"  name="about_offer"> <?=$sliderdetails['about_offer']?></textarea>
                </div>
              </div>
              <div class="form-footer text-center">
                <button type="submit" class="btn btn-success" name = "editSliderImage"><i class="fa fa-check-square-o"></i> UPDATE</button>
               
              </div>
            </form>
          <?php } ?>
          </div>
        </div>
      </div>
      </div><!--End Row-->
      </div><!--End Row-->
    </div>
  </div>
</div>
<!-- End container-fluid-->
</div><!--End content-wrapper-->
<!--Start Back To Top Button-->