<?php  
extract($_REQUEST);
if(isset($key_value_id)  ){


    extract($_POST);
    
    $qry = $d->select("language_key_value_master,language_master, language_key_master "," language_key_value_master.language_id =language_master.language_id and  language_key_master.language_key_id =language_key_value_master.language_key_id and    language_key_value_master.key_value_id='$key_value_id'   ");
    $language_master = mysqli_fetch_array($qry);
     extract($language_master);
  }

   ?>

<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Edit Language Key Value</h4>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="welcome">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Edit Language Key Value</li>
        </ol>
      </div> 
      <div class="col-sm-3">

        <a href="manageLanguageValue" class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-list mr-1"></i>View List</a>
      </div>
    </div>
  </div>
  <!-- End Breadcrumb-->

  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        
        <div class="card-body">

          <form id="editLanguageValueKeyFrm" method="POST" class="form-horizontal" action="controller/languageKeyValueController.php">
            <div class="form-group row">
              <label class="col-sm-2 col-form-label" >Language Name </label>
              <div class="col-sm-10">
                <?php   echo $language_name; ?>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label" >Key Name </label>
              <div class="col-sm-10">
                <?php   echo $key_name; ?>
              </div>
            </div>

             <div class="form-group row">
              <label class="col-sm-2 col-form-label" for="value_name">value name <span class="required">*</span></label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="value_name" name="value_name" placeholder="Key Value Name" required="" value="<?php if(isset($language_id)){ echo htmlspecialchars($value_name, ENT_QUOTES, 'UTF-8');} ?>" />
              </div>
            </div>
            

           <div class="form-footer text-center">
            <?php if(isset($language_id)){ ?> 
              <input type="hidden" name="key_value_id" value="<?php echo $key_value_id;?>">
               <button type="submit" class="btn btn-success" name="UpdateLanguageKeyValue"  value="Update">Submit</button>
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