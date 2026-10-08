    <div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">App Default App Slider</h4>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="welcome">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">App  Default App Slider</li>
        </ol>
      </div>
      <div class="col-sm-3">
        
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <?php
        $slider_array = array();
        $sq = $d->select("app_slider_master","society_id = '0'");
        while ($sData = mysqli_fetch_array($sq)) {
        array_push($slider_array,$sData['slider_image_name']);
        }
        // print_r($slider_array);
        if(mysqli_num_rows($sq)>0){
        ?>
        <div class="card">
          <div id="carousel-A" class="carousel slide" data-ride="carousel">
            <ul class="carousel-indicators">
              <li data-target="#demo-a" data-slide-to="0" class="active"></li>
              <li data-target="#demo-b" data-slide-to="1"></li>
              <li data-target="#demo-c" data-slide-to="2"></li>
            </ul>
            
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img onerror="this.src='../img/sliders/slider.jpg'" class="d-block w-100" src="../img/sliders/<?php echo $slider_array[0] ?>" alt="Slider_Image_1" width="" height="400">
              </div>
              <div class="carousel-item">
                <img onerror="this.src='../img/sliders/slider.jpg'" class="d-block w-100" src="../img/sliders/<?php echo $slider_array[1] ?>" alt="Slider_Image_2" width="" height="400">
              </div>
              <div class="carousel-item">
                <img onerror="this.src='../img/sliders/slider.jpg'" class="d-block w-100" src="../img/sliders/<?php echo $slider_array[2] ?>" alt="Slider_Image_3" width="" height="400">
              </div>
            </div>
            
            <a class="carousel-control-prev" href="#carousel-A" data-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </a>
            <a class="carousel-control-next" href="#carousel-A" data-slide="next">
              <span class="carousel-control-next-icon"></span>
            </a>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
    
    <?php
    $rq1 = $d->select("app_slider_master","society_id='0'");
    $rows = mysqli_num_rows($rq1);
    if ($rows < 3) {
    ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="signupForm" method="post" action="controller/sliderController.php" enctype="multipart/form-data">
              <h4 class="form-header text-uppercase">
              <i class="fa fa-address-book-o"></i>
              Add Default App Slider Image(s)
              </h4>
              <div class="form-group row">
                <label for="input-12" class="col-sm-2 col-form-label">Slider Image</label>
                <div class="col-sm-10">
                  <input class="form-control" required="" type="file"  name="slider_image">
                </div>
              </div>
              <div class="form-footer text-center">
                <button type="submit" class="btn btn-danger"><i class="fa fa-times"></i> CANCEL</button>
                <button type="submit" class="btn btn-success" name = "addSliderImageDefualt"><i class="fa fa-check-square-o"></i> SAVE</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      </div><!--End Row-->
      <?php
      }
      ?>
      <!--End Row-->
      <!-- Msanage Slider -->
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <form id="signupForm" method="post" action="controller/sliderController.php" enctype="multipart/form-data">
                <h4 class="form-header text-uppercase">
                <i class="fa fa-address-book-o"></i>
                Manage Default App  Slider Image(s)
                </h4>
                <div class="row">
                  <?php
                  $sq = $d->select("app_slider_master","society_id = '$society_id'");
                  while ($sData = mysqli_fetch_array($sq)) {
                  ?>
                  <div class="col-md-6 col-lg-3 col-xl-3">
                    <img src="../img/sliders/<?php echo $sData['slider_image_name']; ?>" alt="Slider_Image" class="lightbox-thumb img-thumbnail" style="width:250px;height:150px;">
                    <form action="controller/sliderController.php" method="POST">
                      <input type="hidden" name="app_slider_id" value="<?php echo $sData['app_slider_id']; ?>">
                      <input type="hidden" name="slider_path" value="../img/sliders/<?php echo $sData['slider_image_name']; ?>">
                      <button type="submit" class="btn btn-sm btn-danger" name="deleteSliderImage" >Delete</button>
                    </form>
                  </div>
                  <?php
                  }
                  ?>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      </div><!--End Row-->
    </div>
  </div>
</div>
<!-- End container-fluid-->
</div><!--End content-wrapper-->
<!--Start Back To Top Button-->