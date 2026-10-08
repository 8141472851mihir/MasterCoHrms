<?php
extract($_REQUEST);
if(isset($language_key_id)){
extract($_POST);

$qry = $d->select("language_key_master_gatekeeper","language_key_id='$language_key_id'");
$languageKey = mysqli_fetch_array($qry);
extract($languageKey);
}

?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title"> Add Language Key </h4>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="welcome">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Add Language Key</li>
        </ol>
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          
          <a href="manageLanguagKeys" class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-list mr-1"></i>View List</a>
        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->
    
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
          <div class="card-body">
            <form <?php if(isset($language_key_id)){ ?> id="EditlangKeyFrm" <?php  } else {?> id="langKeyFrm" <?php } ?>  method="POST" class="form-horizontal" action="controller/languagekeyControllerGatekeeper.php">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="key_name">Key Name <span class="required">*</span></label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="key_name" name="key_name" placeholder="Key Name" required="" value="<?php if(isset($language_key_id)){ echo $key_name;} ?>" />
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="key_name">Type  <span class="required">*</span></label>
                <div class="col-sm-10">
                  <div class="form-check-inline">
                    <label class="form-check-label">
                      <?php if(isset($language_key_id)) { ?>
                      <input type="radio" <?php if(isset($language_key_id) && $key_type=='0'){echo "checked";} ?>  class="form-check-input" value="0" name="key_type"> String  
                    <?php } else { ?>
                      <input type="radio" checked class="form-check-input" value="0" name="key_type"> String  
                    <?php } ?>
                    </label>
                  </div>
                  <div class="form-check-inline">
                    <label class="form-check-label">
                      <?php if(isset($language_key_id)) { ?>
                      <input type="radio"  <?php if(isset($language_key_id) && $key_type=='1'){echo "checked";} ?>  class="form-check-input" value="1" name="key_type"> Array
                      <?php } else { ?>
                      <input type="radio"  class="form-check-input" value="1" name="key_type"> Array  
                    <?php } ?>
                    </label>
                  </div>
                </div>
              </div>
              <div id="no_of_keyDiv"  <?php if(isset($language_key_id) && $key_type=='1') { echo "style='display:block'"; } else { echo "style='display:none'"; } ?>>
               <div class="form-group row" >
                <label class="col-sm-2 col-form-label" for="no_of_key">Number of Values <span class="required">*</span></label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="no_of_key" name="no_of_key" placeholder="Number of Values" required="" value="<?php if(isset($language_key_id)){ echo $no_of_key;} ?>" />
                </div>
              </div>
             </div>
              <div class="form-footer text-center">
                <?php if(isset($language_key_id)){ ?>
                <input type="hidden" name="language_key_id" value="<?php echo $language_key_id;?>">
                <button type="submit" class="btn btn-success" name="UpdateLanguageKey"  value="Update">Submit</button>
                <?php } else {?>
                <button type="submit" class="btn btn-success" name="AddLanguageKey"  value="Save">Submit</button>
                <?php } ?>
                
              </div>
            </form>
            
          </div>
          
        </div>
      </form>
    </div>
  </div>
</div>
</div>
</div><!--End wrapper-->