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
                 if(isset($_POST['editMenu'])) {
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
                  Menu
                </h4>
                
                <div class="form-group row">
                  <label for="input-10" class="col-sm-2 col-form-label">Menu Name <span class="required">*</span></label>
                  <div class="col-sm-4">
                     <?php if(isset($_POST['editMenu'])) { ?>
                     <input type="hidden" name="menu_id" value="<?php echo $data['menu_id']; ?>">
                    <input type="text" maxlength="40" class="form-control" id="input-10" name="menu_nameEdit" value="<?php echo $data['menu_name']; ?>" required="">
                  <?php } else { ?>
                    <input type="text" maxlength="40" class="form-control" id="input-10" name="menu_name" value="<?php echo $data['menu_name']; ?>" required="">
                  <?php } ?>
                  </div>
                  <label for="input-11" class="col-sm-2 col-form-label">Page Url <span class="required">*</span></label>
                  <div class="col-sm-4" >
                    <input type="text" maxlength="100" class="form-control" id="input-11" name="menu_link"  value="<?php echo $data['menu_link']; ?>" required="">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="input-11" class="col-sm-2 col-form-label">Menu Icon <span class="required">*</span></label>
                  <div class="col-sm-4" >
                    <input id="getclass" type="text"  class="form-control" name="menu_icon"  value="<?php echo $data['menu_icon']; ?>" required="" data-toggle="modal" data-target="#iconModal" >
                  </div>
                  <label for="input-11" class="col-sm-2 col-form-label">Sub Menu <span class="required">*</span></label>
                  <div class="col-sm-4" >
                    <select type="text" name="sub_menu" class="form-control" required data-validation-required-message="Select Sub Menu Status">
                        <option value="">-- Select  --</option>
                        <option value="0" <?php if($data['sub_menu']==0) { echo "selected";} ?>>No</option>
                        <option value="1" <?php if($data['sub_menu']==1) { echo "selected";} ?>>Yes</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  
                  <label for="input-11" class="col-sm-2 col-form-label">Order No <span class="required">*</span></label>
                  <div class="col-sm-4" >
                    <input maxlength="30" type="number" name="order_no" class="form-control" required data-validation-required-message="Enter  Menu Order Number" value="<?php echo $data['order_no']; ?>">
                  </div>
                </div>
                
                <div class="form-footer text-center">
                   <?php  if(isset($_POST['editMenu'])) { ?>
                    <input name="menuEdit" value="Page Edit" type="hidden" name="menu_id" value="<?php echo $data['menu_id']; ?>">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> UPDATE</button>
                  <?php } else { ?>
                    <button name="menuAdd" value="add Page" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> ADD</button>
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
<div id="iconModal" class="modal fade pullDown" role="dialog" id="large">
  <div class="modal-dialog modal-lg" role="document" >

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header bg-primary white">
      <h4 class="modal-title" id="myModalLabel8">Select Icon</h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      </div>
      <div class="modal-body">
        <ul class="list-inline">
          <?php 
          $i=1;
          $q=$d->select("icons","");
          while ($data=mysqli_fetch_array($q)) {
          ?>
          <li title="<?php echo $data['icon_name']; ?>" style="display: inline; font-size: 22px; padding: 10px; cursor: pointer;"  ><i id="<?php echo $data['icon_class']; ?>" onclick="get_class(this);" class="fa <?php echo $data['icon_class']; ?>"></i></li>
          <?php } ?>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
    <div id="myform"></div>
  </div>
</div>

<script type="text/javascript">
   function get_class(x) {
   var element = $(x);
   var class_name = element.attr("class");
   document.getElementById('getclass').value=class_name;
   $('#iconModal').modal('toggle');
  }
</script>