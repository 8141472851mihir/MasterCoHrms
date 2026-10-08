<div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumb-->
    <!-- End Breadcrumb-->
     
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <form id="signupForm" action="controller/menuController.php" method="post" >
                <?php 
                  if(isset($_POST['editPage'])) {
                  extract(array_map("test_input" , $_POST));
                  $q=$d->select("master_menu","menu_id='$page_id'");
                  $data=mysqli_fetch_array($q);
                  // print_r($data);
                  } 
                   ?>
                <h4 class="form-header text-uppercase">
                  <i class="fa fa-file"></i>
                  Page
                </h4>
                <div class="form-group row">
                  <label for="input-16" class="col-sm-2 col-form-label">Main Menu <span class="required">*</span></label>
                  <div class="col-sm-10">
                    <select id="password2" name="parent_menu_id" required="" class="form-control chzn-select" tabindex="2" data-validation-required-message="Select Main Menu">
                        <option value="">-- Select -- </option>
                          <?php 
                      $i=1;
                      $q1=$d->select("master_menu","sub_menu=1 OR sub_menu=0 AND parent_menu_id=0");
                      while ($data2=mysqli_fetch_array($q1)) {
                       ?>
                          <option <?php if($data['parent_menu_id']==$data2['menu_id']) { echo "selected"; } ?> value="<?php echo $data2['menu_id']; ?>"><?php echo $data2['menu_name']; ?></option>
                      <?php } ?>
                      </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="input-10" class="col-sm-2 col-form-label">Page Name <span class="required">*</span></label>
                  <div class="col-sm-4">
                    <input type="text" maxlength="40" class="form-control" id="input-10" name="sub_menu_name" value="<?php echo $data['menu_name']; ?>" required="">
                  </div>
                  <label for="input-11" class="col-sm-2 col-form-label">Page Url <span class="required">*</span></label>
                  <div class="col-sm-4" >
                    <input type="text" maxlength="100" class="form-control" id="input-11" name="menu_link"  value="<?php echo $data['menu_link']; ?>" required="">
                  </div>
                </div>
                
                <div class="form-footer text-center">
                   <?php  if(isset($_POST['editPage'])) { ?>
                    <input name="pagesEdit" type="hidden" name="menu_id" value="<?php echo $data['menu_id']; ?>">
                    <input name="menu_id" type="hidden" name="menu_id" value="<?php echo $data['menu_id']; ?>">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> UPDATE</button>
                  <?php } else { ?>
                    <button name="addPage" value="add Page" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> ADD</button>
                  <?php } ?>
                    <button  type="reset" class="btn btn-danger"><i class="fa fa-times"></i> CANCEL</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div><!--End Row-->

    </div>
    <!-- End container-fluid-->
    
    </div><!--End content-wrapper-->
  