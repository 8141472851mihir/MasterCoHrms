<?php 
extract($_REQUEST);
if(isset($language_id)){


  extract($_POST);

  $qry = $d->select("language_master","language_id='$language_id'");
  $language_master = mysqli_fetch_array($qry);
  extract($language_master);
}

?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Add Language </h4>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="welcome">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Add Language</li>
        </ol>
      </div> 
      <div class="col-sm-3">
        <a href="manageLanguage" class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-list mr-1"></i>View List</a>
      </div>
    </div>
  </div>
  <!-- End Breadcrumb-->

  <div class="row">
    <div class="col-lg-12">
      <div class="card">

        <div class="card-body">

          <form id="langFrm" method="POST" class="form-horizontal" action="controller/languageController.php">
            <div class="form-group row">
              <label class="col-sm-2 col-form-label" for="language_name">Language Name <span class="required">*</span></label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="language_name" name="language_name" placeholder="Language Name" required="" value="<?php if(isset($language_id)){ echo $language_name;} ?>" />
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label" for="language_name_1">Language Name 1 </label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="language_name_1" name="language_name_1" placeholder="Language Name 1"  value="<?php if(isset($language_id)){ echo $language_name_1;} ?>" />
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label" for="language_name_1">continue Button name </label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="continue_btn_name" name="continue_btn_name" placeholder="Continue Button Name" required="" value="<?php if(isset($language_id)){ echo $continue_btn_name;} ?>" />
              </div>
            </div>
            

            <div class="form-footer text-center">
              <?php if(isset($language_id)){ ?> 
                <input type="hidden" name="language_id" value="<?php echo $language_id;?>">
                <button type="submit" class="btn btn-success" name="UpdateLanguage"  value="Update">Submit</button>
              <?php } else {?>
                <button type="submit" class="btn btn-success" name="AddLanguage"  value="Save">Submit</button>
              <?php } ?> 

            </div>
          </form>
        </div>
      </div>
    </div><!--End Modal -->

  </div><!--End wrapper-->

  <?php //include_once 'common/footer.php'; ?>