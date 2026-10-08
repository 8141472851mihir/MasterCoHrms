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
                 if(isset($_POST['editSubMenu'])) {
                  $btnName="Update";
                  extract(array_map("test_input" , $_POST));
                  $q=$d->select("master_menu","menu_id='$menu_id'");
                  $data=mysqli_fetch_array($q);
                  } else {
                  $btnName="Add";
                  }
                   ?>
                <h4 class="form-header text-uppercase">
                  <i class="fa fa-file"></i>
                  Sub Menu
                </h4>
                
                <div class="form-group row">
                  <label for="input-10" class="col-sm-2 col-form-label">Main Menu <span class="required">*</span></label>
                  <div class="col-sm-4">
                     <select type="text" name="parent_menu_id" class="form-control">
                        <option value="">-- Select  --</option>
                        <?php 
                          $i=1;
                          $q3=$d->select("master_menu","sub_menu=1");
                          while ($data3=mysqli_fetch_array($q3)) {
                            $mId=$data3['menu_id'];
                           ?>
                              <option <?php if($data['parent_menu_id']==$mId) { echo "selected"; } ?> value="<?php echo $data3['menu_id']; ?>"><?php echo $data3['menu_name']; ?></option>
                          <?php } ?>
                    </select>
                  </div>
                  <label for="input-11" class="col-sm-2 col-form-label">Sub Menu Name <span class="required">*</span></label>
                  <div class="col-sm-4" >
                    <input type="text" maxlength="30" class="form-control" id="input-11" name="sub_menu_name"  value="<?php echo $data['menu_name']; ?>" required="">
                  </div>
                </div>
                <div class="form-group row">
                  
                  <label for="input-11" class="col-sm-2 col-form-label">Sub Menu Url <span class="required">*</span></label>
                  <div class="col-sm-4" >
                    <input  type="text"  class="form-control" name="menu_link"  value="<?php echo $data['menu_link']; ?>" required=""  >
                  </div>
               
                  <label for="input-11" class="col-sm-2 col-form-label">Menu Order No <span class="required">*</span></label>
                  <div class="col-sm-4" >
                    <input maxlength="30" type="number" name="order_no" class="form-control" required data-validation-required-message="Enter  Menu Order Number" value="<?php echo $data['order_no']; ?>">
                  </div>
                </div>
                
                <div class="form-footer text-center">
                   <?php  if(isset($_POST['editSubMenu'])) { ?>
                    <input name="SubmenuEdit" type="hidden" name="menu_id" value="<?php echo $data['menu_id']; ?>">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> UPDATE</button>
                  <?php } else { ?>
                    <button name="SubmenuAdd" value="add Page" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> ADD</button>
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
  <!--select icon modal -->
