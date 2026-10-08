<?php $timeline_id = isset($_POST['timeline_id']) ? $d->sanitizeActionIdAsInt($_POST['timeline_id']) : 0; $timeline_master = $d->selectArray("timeline_master","timeline_id='$timeline_id'"); 
//echo "<pre>";print_r($timeline_master);

$selected_soca = explode(",", $timeline_master['society_id']);
 
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Edit Timeline</h4>
       
      </div>
    </div>
    <!-- End Breadcrumb-->
     <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
           <form id="editTimelineFrm" action="controller/newsFeedController.php" method="post" enctype="multipart/form-data">
            
            <input type="hidden" name="timeline_id" value="<?php echo $timeline_master['timeline_id'];?>">
           



            <div class="form-group row">
               <label class="col-sm-3 col-form-label form-control-label">Title <span class="required">*</span></label>
              <div class="col-sm-9" id="">
                <?php //echo "<pre>";print_r($timeline_master); ?>
                 <input class="form-control" required="" name="timeline_text"  id="timeline_text" type="text"   placeholder="Title" minlength="3" maxlength="100" value="<?php echo  $timeline_master['timeline_text'];?>">
                
              </div>  
           </div> 
             
                <div class="form-group row">
                   <label class="col-sm-3 col-form-label form-control-label">Image </label>
                    <div class="col-sm-9" id="">
                      <a href="../img/post_timeline/<?php echo  $timeline_master['post_image'] ?>" data-fancybox="images" data-caption="Photo Name : <?php echo $timeline_master['post_image']; ?>">
                    <img src="../img/post_timeline/<?php echo  $timeline_master['post_image']; ?>" alt="<?php echo  $timeline_master['post_image']; ?>" class="lightbox-thumb img-thumbnail" style="width:100% !important;">
                  </a>

                      <input   type="file" accept="image/*" name="image"  class="form-control-file border photoOnly">
                      <input type="hidden" name="image_old" value="<?php echo $timeline_master['post_image'];?>">

                    </div>
                </div>
         
                <div class="form-footer text-center">
                  <input type="hidden" name="editFeed" value="editFeed">
                  <button type="submit" name="" value="" class="btn btn-sm btn-success"><i class="fa fa-check-square-o"></i> Update</button>
                </div>

          </form> 
          </div>
        </div>
      </div>
    </div>
  </div>
</div>  