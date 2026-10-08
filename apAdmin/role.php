<?php
$type = (int)isset($_REQUEST['type']) && $_REQUEST['type'] > 0 ? 1 : 0;
if (isset($_POST['editRole'])) {
  extract(array_map("test_input", $_POST));
  $q = $d->select("role_master", "role_id='$role_id'");
  $data_main = mysqli_fetch_array($q);
}
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-12">
        <h4 class="page-title"> Role</h4>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">

            <form id="signupForm" action="controller/menuController.php" method="post">
              <input type="hidden" name="societyId" value="<?php echo $data_main['society_id']; ?>">
              <div class="form-group row">
                <label for="input-10" class="col-sm-2 col-form-label">Role Name <span class="required">*</span></label>
                <div class="col-sm-4">
                  <?php if (isset($_POST['editRole'])) { ?>
                    <input type="hidden" name="role_id" value="<?php echo $data_main['role_id']; ?>">
                    <input maxlength="30" type="text" name="role_nameEdit" class="form-control" required data-validation-required-message="Enter Role  Name" value="<?php echo $data_main['role_name']; ?>">
                  <?php } else { ?>
                    <input maxlength="30" type="text" name="role_name" class="form-control" required data-validation-required-message="Enter Role Name">
                  <?php } ?>
                </div>
                <label for="input-11" class="col-sm-2 col-form-label"> Description</label>
                <div class="col-sm-4">
                  <input maxlength="100" type="text" name="role_description" class="form-control" value="<?php echo $data_main['role_description']??""; ?>">
                </div>
              </div>
              <div class="form-group row">
                <div class="col-sm-6 offset-3">
                  <div class="controls">
                    <div class="text-center">
                      <h6>
                        Main Menu Permissions
                      </h6>
                      <input type="checkbox" id="user-checkbox" class="chk_boxes pagePrivilege" />
                      <label for="user-checkbox">Check/Uncheck</label>
                    </div>

                    <?php
                    $menuPrivilege = $data_main['menu_id']??"";
                    $pagePrivilege = $data_main['pagePrivilege']??"";
                    $menuPrivilege = explode(",", $menuPrivilege);
                    $pagePrivilege = explode(",", $pagePrivilege);

                    $menuId = $data_main['menu_id']??"";
                    $menuId = explode(",", $menuId);
                    $pagePrivilegeId = $data_main['pagePrivilege']??"";
                    $pagePrivilegeId = explode(",", $pagePrivilegeId);

                    $i = 1;
                    $q1 = $d->select("master_menu", "1", "ORDER BY order_no ASC");
                    $allMenus = array();
                    $menusByParentPageStatus = array();
                    while ($row = mysqli_fetch_array($q1)) {
                      $allMenus[] = $row;
                      $pid = $row['parent_menu_id'];
                      $ps = $row['page_status'];
                      if (!isset($menusByParentPageStatus[$pid])) {
                        $menusByParentPageStatus[$pid] = array();
                      }
                      if (!isset($menusByParentPageStatus[$pid][$ps])) {
                        $menusByParentPageStatus[$pid][$ps] = array();
                      }
                      $menusByParentPageStatus[$pid][$ps][] = $row;
                    }

                    foreach ($allMenus as $dataMenu) {
                      $menu_id = $dataMenu['menu_id'];
                      $lngMenuName = $dataMenu['menu_name'];

                      if ($dataMenu['sub_menu'] == 0) {
                        if ($dataMenu['parent_menu_id'] == 0 && $dataMenu['page_status'] == 0) { ?>
                          <div class="card-body">
                            <ul class="list-group ul_group">
                              <?php
                              if (in_array($menu_id, $menuPrivilege)) { ?>
                                <!-- Menu Name that do not have any sub menu -->
                                <li class="list-group-item list-group-item-primary">
                                  <label style="margin-left: -30px;" class="custom-control custom-checkbox error_color">
                                    <input <?php if (in_array($menu_id, $menuId)) {
                                              echo "checked";
                                            } ?> type="checkbox" class="pagePrivilege" value="<?php echo $dataMenu['menu_id']; ?>" name="menu_id[]">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description"><b><?php if ($lngMenuName != "") {
                                                                                  echo  $lngMenuName;
                                                                                } else {
                                                                                  echo $dataMenu['menu_name'];
                                                                                } ?></b></span>
                                  </label>
                                </li>
                              <?php } else { ?>
                                <li class="list-group-item list-group-item-primary">
                                  <label style="margin-left: -30px;" class="custom-control custom-checkbox error_color">
                                    <input <?php if (in_array($menu_id, $menuId)) {
                                              echo "checked";
                                            } ?> type="checkbox" class="pagePrivilege" value="<?php echo $dataMenu['menu_id']; ?>" name="menu_id[]">
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description"><b><?php if ($lngMenuName != "") {
                                                                                  echo  $lngMenuName;
                                                                                } else {
                                                                                  echo $dataMenu['menu_name'];
                                                                                } ?></b></span>
                                  </label>
                                </li>
                              <?php }
                              $pageRows = isset($menusByParentPageStatus[$menu_id][1]) ? $menusByParentPageStatus[$menu_id][1] : array(); ?>
                              <?php foreach ($pageRows as $pageData) {
                                $page_id = $pageData['menu_id'];
                                $lngMenuNamePage = $pageData['menu_name'];
                                if (in_array($page_id, $pagePrivilege)) {
                              ?>
                                  <!-- page name -->
                                  <li class="list-group-item list-group-item-secondary">
                                    <label class="custom-control custom-checkbox error_color">
                                      <input <?php if (in_array($page_id, $pagePrivilegeId)) {
                                                echo "checked";
                                              } ?> type="checkbox" class="pagePrivilege" value="<?php echo $pageData['menu_id']; ?>" name="pagePrivilege[]">
                                      <span class="custom-control-indicator"></span>
                                      <span class="custom-control-description"><b><?php if ($lngMenuNamePage != "") {
                                                                                    echo  $lngMenuNamePage;
                                                                                  } else {
                                                                                    echo $pageData['menu_name'];
                                                                                  } ?></b></span>
                                    </label>
                                  </li>
                                <?php } else { ?>
                                  <li class="list-group-item list-group-item-secondary">
                                    <label class="custom-control custom-checkbox error_color">
                                      <input <?php if (in_array($page_id, $pagePrivilegeId)) {
                                                echo "checked";
                                              } ?> type="checkbox" class="pagePrivilege" value="<?php echo $pageData['menu_id']; ?>" name="pagePrivilege[]">
                                      <span class="custom-control-indicator"></span>
                                      <span class="custom-control-description"><b><?php if ($lngMenuNamePage != "") {
                                                                                    echo  $lngMenuNamePage;
                                                                                  } else {
                                                                                    echo $pageData['menu_name'];
                                                                                  } ?></b></span>
                                    </label>
                                  </li>
                              <?php }
                              } ?>
                            </ul>
                          </div>
                          <?php }
                      } else {
                        if ($dataMenu['sub_menu'] == 1) {
                          $subMenuRows = isset($menusByParentPageStatus[$menu_id][0]) ? $menusByParentPageStatus[$menu_id][0] : array();
                          $pageChildRows = isset($menusByParentPageStatus[$menu_id][1]) ? $menusByParentPageStatus[$menu_id][1] : array();
                          if (count($subMenuRows) || count($pageChildRows)) {
                          ?>
                            <div class="card-body">
                              <ul class="list-group ">
                                <li class="list-group-item list-group-item-primary">
                                  <label class=" custom-checkbox error_color">
                                    <span class="custom-control-description"><b><?php if ($lngMenuName != "") {
                                                                                  echo  $lngMenuName;
                                                                                } else {
                                                                                  echo $dataMenu['menu_name'];
                                                                                } ?></b></span>
                                  </label>
                                </li>
                                <?php

                                foreach ($subMenuRows as $subMeneData) {
                                  $sub_menu_id = $subMeneData['menu_id'];
                                  $lngMenuNameSub = $subMeneData['menu_name'];
                                  if (in_array($sub_menu_id, $menuPrivilege)) { ?>
                                    <li class="list-group-item list-group-item-success">
                                      <label class="custom-control custom-checkbox error_color">
                                        <input <?php if (in_array($sub_menu_id, $menuId)) {
                                                  echo "checked";
                                                } ?> type="checkbox" class="pagePrivilege" value="<?php echo $subMeneData['menu_id']; ?>" name="menu_id[]">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description"><?php if ($lngMenuNameSub != "") {
                                                                                    echo  $lngMenuNameSub;
                                                                                  } else {
                                                                                    echo $pageData['menu_name'];
                                                                                  } ?></span>
                                      </label>
                                    </li>
                                  <?php } else { ?>
                                    <li class="list-group-item list-group-item-success">
                                      <label class="custom-control custom-checkbox error_color">
                                        <input <?php if (in_array($sub_menu_id, $menuId)) {
                                                  echo "checked";
                                                } ?> type="checkbox" class="pagePrivilege" value="<?php echo $subMeneData['menu_id']; ?>" name="menu_id[]">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description"><?php if ($lngMenuNameSub != "") {
                                                                                    echo  $lngMenuNameSub;
                                                                                  } else {
                                                                                    echo $pageData['menu_name'];
                                                                                  } ?></span>
                                      </label>
                                    </li>
                                <?php }
                                } ?>
                                <!-- page name -->
                                <?php
                                foreach ($pageChildRows as $data) {
                                  $page_id = $data['menu_id'];
                                  $lngMenuNamedata = $data['menu_name'];
                                  if (in_array($page_id, $pagePrivilege)) {
                                ?>
                                    <li class="list-group-item list-group-item-secondary">
                                      <label class="custom-control custom-checkbox error_color">
                                        <input <?php if (in_array($page_id, $pagePrivilegeId)) {
                                                  echo "checked";
                                                } ?> type="checkbox" class="pagePrivilege" value="<?php echo $data['menu_id']; ?>" name="pagePrivilege[]">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description"><?php if ($lngMenuNamedata != "") {
                                                                                    echo  $lngMenuNamedata;
                                                                                  } else {
                                                                                    echo $data['menu_name'];
                                                                                  } ?></span>
                                      </label>
                                    </li>
                                  <?php } else { ?>
                                    <li class="list-group-item list-group-item-secondary">
                                      <label class="custom-control custom-checkbox error_color">
                                        <input <?php if (in_array($page_id, $pagePrivilegeId)) {
                                                  echo "checked";
                                                } ?> type="checkbox" class="pagePrivilege" value="<?php echo $data['menu_id']; ?>" name="pagePrivilege[]">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description"><?php if ($lngMenuNamedata != "") {
                                                                                    echo  $lngMenuNamedata;
                                                                                  } else {
                                                                                    echo $data['menu_name'];
                                                                                  } ?></span>
                                      </label>
                                    </li>
                                <?php }
                                } ?>
                              </ul>
                            </div>
                    <?php }
                        }
                      }
                    } ?>
                  </div>
                </div>
              </div>
              <div class="form-footer text-center">
                <input type="hidden" name="role_type" value="<?php echo $type; ?>">
                <?php if (isset($_POST['editRole'])) { ?>
                  <button type="submit" name="updateRole" class="btn btn-success"><i class="fa fa-check-square-o"></i> update</button>
                <?php } else { ?>
                  <button name="addRole" value="add Role" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> add</button>
                <?php } ?>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
  $(function() {
    $('.chk_boxes').click(function() {
      $('.pagePrivilege').prop('checked', this.checked);
    });
  });
  $(".ul_group").each(function() {
    if ($(this).children('li').length == 0) {
      $(this).parent('div').remove()
    }
  });
</script>