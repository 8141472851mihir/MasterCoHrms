<?php  error_reporting(0);
extract($_REQUEST);
if(isset($seasonal_greet_image_id)){
  $seasonal_greet_master=$d->select("seasonal_greet_image_master","  seasonal_greet_image_id  = '$seasonal_greet_image_id' ","");
  $seasonal_greet_master_data=mysqli_fetch_array($seasonal_greet_master);
  extract($seasonal_greet_master_data);
}
$seasonal_greet_master_qry=$d->select("seasonal_greet_master","seasonal_greet_id='$seasonal_greet_id'","");
$seasonal_greet_master_d=mysqli_fetch_array($seasonal_greet_master_qry);
?>
<style type="text/css">
  .gotham_bold {
    font-family: "gotham_bold" !important; 
  }
  .gotham_book {
    font-family: "gotham_book" !important; 
  }
  .gotham_black {
    font-family: "gotham_black" !important; 
  }
  .great_Vibes {
    font-family: "great_Vibes" !important; 
  }
  .montserrat_semi_bold {
    font-family: "montserrat_semi_bold" !important; 
  }
  .montserrat_regular {
    font-family: "montserrat_regular" !important; 
  }
  @font-face {font-family: "gotham_bold";
    src: url("assets/fonts/gotham_bold.ttf"); /* IE9*/
  }
  @font-face {font-family: "gotham_book";
    src: url("assets/fonts/gotham_book.ttf"); /* IE9*/
  }
  @font-face {font-family: "gotham_black";
    src: url("assets/fonts/gotham_black.ttf"); /* IE9*/
  }
  @font-face {font-family: "great_Vibes";
    src: url("assets/fonts/great_Vibes.ttf"); /* IE9*/
  }
  @font-face {font-family: "montserrat_semi_bold";
    src: url("assets/fonts/montserrat_semi_bold.ttf"); /* IE9*/
  }
  @font-face {font-family: "montserrat_regular";
    src: url("assets/fonts/montserrat_regular.ttf"); /* IE9*/
  }
</style>

<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Manage Seasonal Greetings - <?php echo $seasonal_greet_master_d['title'];?></h4>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="seasonalGreetImageFrm" action="controller/seasonalGreetController.php" method="post" enctype="multipart/form-data" >
             <input type="hidden" name="seasonal_greet_id" value="<?php echo $seasonal_greet_id;?>">
             <?php if(!isset($seasonal_greet_image_id)){ ?>
             <div id="dynamicFieldsContainer">
              <!-- Dynamic Block Start -->
              <div class="dynamic-block m-5 border border-1 p-2">
                <fieldset class="scheduler-border">
                  <legend class="scheduler-border">Image Details</legend>
                  <div class="form-group row">
                    <label for="background_image" class="col-sm-2 col-form-label">Image <span class="required">*</span></label>
                    <div class="col-sm-10">
                      <input class="form-control-file border" id="background_image_0" accept="image/*" type="file" name="background_image[0]" required>
                    </div>
                  </div>
                </fieldset>

                <fieldset class="scheduler-border">
                  <legend class="scheduler-border">Name Details</legend>
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Show To Name?</label>
                    <div class="col-sm-4">
                      <select class="form-control" name="show_to_name[0]" required>
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                      </select>
                    </div>
                    <label class="col-sm-2 col-form-label">Show From Name?</label>
                    <div class="col-sm-4">
                      <select class="form-control" name="show_from_name[0]" required>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                      </select>
                    </div>
                  </div>
                </fieldset>

                <fieldset class="scheduler-border">
                  <legend class="scheduler-border">Other Details</legend>
                  <div class="form-group row">
                    <label class="col-lg-2 col-form-label form-control-label">Status</label>
                    <div class="col-lg-4">
                      <select class="form-control" name="other_status[0]">
                        <option value="Active">Active</option>
                        <option value="InActive">InActive</option>
                      </select>
                    </div>
                  </div>
                </fieldset>
              </div>
              <button id="rowAdder" type="button" class="btn btn-dark mt-3">
                <i class="bi bi-plus-square-dotted"></i> ADD More Image
              </button>
              <input type="hidden" id="add_image_only" name="add_image_only" value="add_image_only">
              <input type="hidden" id="counter" name="counter" value="0">
              <!-- Dynamic Block End -->
            </div>
             <?php }else{ ?>
             <fieldset class="scheduler-border">
              <legend  class="scheduler-border">Image Details</legend>
              <div class="form-group row">

                <label for="background_image" class="col-sm-2 col-form-label"> Image <?php  if(isset($seasonal_greet_image_id) && $background_image !=''){ } else { 
                ?> <span class="required">*</span><?php } ?></label>
                <div class="col-sm-10">
                  <input id="background_image" class="form-control-file border"  accept="image/*"   type="file"  name="background_image">
                  <input id="background_image_old" type="hidden" value="<?php if(isset($seasonal_greet_image_id)){ echo $background_image;} ?>" name="background_image">
                  <?php if(isset($seasonal_greet_image_id) && $background_image!=''){ ?>
                   <a href="../img/promotion/<?php echo $background_image; ?>" data-fancybox="images2<?php echo $seasonal_greet_image_id;?>" data-caption="Photo Name : <?php echo $background_image; ?>">
                    <img style="max-height:40px;max-width:40px;" class="d-block w-100" src="../img/promotion/<?php echo $background_image; ?>" alt="">
                  </a>
                  <?php
                }?>
              </div>
            </div>
          </fieldset>
          <fieldset class="scheduler-border">
            <legend  class="scheduler-border">Name Details</legend>
            <div class="form-group row">
              <label for="show_to_name" class="col-sm-2 col-form-label">Show To Name?</label>
              <div class="col-sm-4">
                <select id="show_to_name"   class="form-control single-select" name="show_to_name" type="text" onchange="showToName();"  required=""  >
                  <option <?php if(isset($seasonal_greet_image_id ) && $show_to_name=="No"  ){ echo "selected";} ?>    value="No">No</option> 
                  <option <?php if(isset($seasonal_greet_image_id ) && $show_to_name=="Yes"  ){ echo "selected";} ?>   value="Yes">Yes</option>
                </select>
              </div>
              <label for="show_from_name" class="col-sm-2 col-form-label">Show From Name?</label>
              <div class="col-sm-4">
                <select id="show_from_name"   class="form-control single-select" name="show_from_name" type="text" onchange="showFromName();"  required=""  >
                  <option <?php if(isset($seasonal_greet_image_id ) && $show_from_name=="Yes" ){ echo "selected";} ?>   value="Yes">Yes</option>
                  <option  <?php if(isset($seasonal_greet_image_id ) && $show_from_name=="No" ){ echo "selected";} ?>  value="No">No</option> 
                </select>
              </div>
            </div>
          </fieldset>
          <fieldset class="scheduler-border">
            <legend  class="scheduler-border">Other Details</legend>
            <div   class="form-group row" >
              <label class="col-lg-2 col-form-label form-control-label">Status </label>
              <div class="col-lg-4">
                <select type="text"   id="status" 
                class="form-control single-select" name="status">
                <option  <?php if(isset($seasonal_greet_image_id ) && $status=="Active" ){ echo "selected";} ?> value="Active">Active</option>
                <option <?php if(isset($seasonal_greet_image_id ) && $status=="InActive" ){ echo "selected";} ?>  value="InActive">InActive</option>        
              </select>
            </div>
          </div>
        </fieldset>   
      <?php } ?> 
        <div class="form-footer text-center">
          <?php  if(isset($seasonal_greet_image_id)){ ?>
            <input type="hidden" name="seasonal_greet_image_id" value="<?php echo $seasonal_greet_image_id;?>">
            <input type="hidden" name="updateSeasonalGreetImage" value="updateSeasonalGreetImage">
            <button type="submit" name="" class="btn btn-success"><i class="fa fa-check-square-o"></i> UPDATE</button>
          <?php  } else {?>
            <input type="hidden" name="addSeasonalGreetImage" value="addSeasonalGreetImage">
            <button type="submit" name="" class="btn btn-success"><i class="fa fa-check-square-o"></i> SAVE</button>
          <?php }?>
          <a href="manageSeasonalGreet?seasonal_greet_id=<?php echo $seasonal_greet_id;?>" class="btn btn-danger">Cancel</a>

        </div>
      </form>
    </div>
  </div>
</div>
</div><!--End Row-->
</div>
<!-- End container-fluid-->
</div><!--End content-wrapper-->
