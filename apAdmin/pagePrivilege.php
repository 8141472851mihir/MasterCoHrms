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
                   if(isset($_POST['editRole'])) {
                    extract(array_map("test_input" , $_POST));
                    $q=$d->select("role_master","role_id='$role_id'");
                    $data=mysqli_fetch_array($q);
                    }
                   ?>
                <h4 class="form-header text-uppercase">
                  <i class="fa fa-lock"></i>
                  Page Privilage
                </h4>
                <input type="hidden" name="societyId" value="<?php echo $data['society_id']; ?>">
                <div class="form-group row">
                  <label for="input-10" class="col-sm-2 col-form-label">Role Name  <span class="required">*</span></label>
                  <div class="col-sm-10">
                    <?php if(isset($_POST['editRole'])) { ?>
                        <input type="hidden" name="role_id" value="<?php echo $data['role_id']; ?>">
                        <input maxlength="30" readonly="" type="text" class="form-control" required   value="<?php echo $data['role_name']; ?>">
                    <?php } ?>
                  </div>
                 
                </div>
                <div class="form-group row">
                  
                  <label for="input-11" class="col-sm-3 col-form-label">Page Permissions <span class="required">*</span></label>
                  <div class="col-sm-9" >
                    <div class="controls">
                      <?php
                      $pagePrivilege=$data['pagePrivilege'];
                      $pagePrivilege=explode(",", $pagePrivilege);
                      $i=1;
                      $pagesByParent = array();
                      $pq = $d->select("master_menu", "page_status=1");
                      while ($prow = mysqli_fetch_array($pq)) {
                        $pagesByParent[$prow['parent_menu_id']][] = $prow;
                      }
                      $q=$d->select("master_menu","parent_menu_id=0","ORDER BY order_no ASC");
                      while ($data=mysqli_fetch_array($q)) {
                      $menu_id=$data['menu_id'];
                      ?>

                      <label  style="margin-left: -30px;" class="custom-control custom-checkbox error_color">

                        

                        
                        <span class="custom-control-indicator"></span>

                        <span class="custom-control-description"><b><?php echo $data['menu_name']; ?></b></span>

                      </label>
                     
                      <!-- sub menu display -->
                      <?php

                      $childPages = isset($pagesByParent[$menu_id]) ? $pagesByParent[$menu_id] : array();
                      foreach ($childPages as $sdata) {
                      $sub_menu_id=$sdata['menu_id'];
                      ?>

                      <label class="custom-control custom-checkbox error_color">

                        <input  <?php if(in_array($sub_menu_id, $pagePrivilege)){ echo "checked"; } ?> type="checkbox" class="pagePrivilege" value="<?php echo $sdata['menu_id']; ?>" name="pagePrivilege[]">

                        <span class="custom-control-indicator"></span>

                        <span class="custom-control-description"><?php echo $sdata['menu_name']; ?></span>

                      </label>


                      <?php } } ?>
                </div>
                  </div>
               
                  
                </div>
                
                <div class="form-footer text-center">
                   <?php if(isset($_POST['editRole'])) { ?>
                    <button type="submit" name="pagePri" class="btn btn-success"><i class="fa fa-check-square-o"></i> UPDATE</button>
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
    <script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">

  $(function() {

    $('.chk_boxes').click(function() {

        $('.pagePrivilege').prop('checked', this.checked);

    });

});

</script>