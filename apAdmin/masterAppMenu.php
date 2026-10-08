<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">App Menu Master</h4>

      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Icon</th>
                    <th>No Data Image</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Type</th>
                    <th>Laguage Key Name</th>
                    <th>Page Link</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->selectRow("resident_app_menu.*,menu_category_master.*", "resident_app_menu LEFT JOIN menu_category_master ON menu_category_master.menu_category_id=resident_app_menu.menu_category_id", "", "ORDER BY app_menu_id ASC");
                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo $menu_title;
                          if ($wip_menu == 1) {
                            echo " <span class='badge badge-danger'>WIP</span";
                          } ?>
                      </td>
                      <td><?php echo $menu_category_name; ?>
                        <button data-toggle="modal" data-target="#changeCategoryModel" title="Change Category?" class="btn text-warning btn-sm ml-2" onclick="changeCategory('<?php echo $menu_category_id; ?>','<?php echo $app_menu_id; ?>','main')"> <i class="fa fa-pencil"></i> </button>
                      </td>
                      <td><img src="<?php echo $bucket_url; ?>icons/<?php echo $menu_icon; ?>" width='50' alt=""></td>
                      <td class="text-center"><img src="<?php echo $bucket_url; ?>icons/<?php echo $no_data_image; ?>" width='50' alt=""></td>
                      <td><?php if ($parent_menu_id == 0) {
                            echo "Main";
                          } else {
                            echo "Sub Menu";
                          } ?></td>
                      <td>
                        <!-- jainish start--- -->
                        <?php
                        $buttonClass = ($menu_status == "0") ? 'btn-success-new' : 'btn-danger';
                        $buttonCondition = ($menu_status == "0") ? 'Active' : 'Deactive';
                        $status = ($menu_status == "0") ? 'appMenuDeactive' : 'appMenuActive';
                        $newStatus = ($menu_status == "0") ? 'appMenuActive' : 'appMenuDeactive';
                        $newStatusVal = ($menu_status == "0") ? '1' : '0';
                        $statusValue = ($menu_status == "0") ? '0' : '1';
                        ?>
                        <input type="button" class="btn btn-sm pl-1 pr-1 w-100 <?php echo $buttonClass ?>" id="<?php echo 'menu_id_' . $app_menu_id; ?>" onclick="changeStatusNew('<?php echo $app_menu_id; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'menu_id_' . $app_menu_id; ?>');" data-size="small" value="<?php echo $buttonCondition ?>" />
                        <!-- jainish end--- -->
                      </td>
                      <td>
                        <?php
                        switch ($menu_type) {
                          case '1':
                            echo "Big Menu";
                            break;
                          case '2':
                            echo "Dashboard Menu";
                            break;
                          default:
                            echo "Other Menu";
                            break;
                        }
                        ?>
                      </td>
                      <td><?php echo $data['language_key_name'] ?> <button data-toggle="modal" data-target="#LangKeyModal" onclick="changeLangKey('<?php echo $app_menu_id; ?>');" title="Change Language Key" class="btn-sm btn btn-warning"><i class="fa fa-pencil"></i></button></td>
                      <td><?php echo $data['page_link'] ?? '' ?> <button data-toggle="modal" data-target="#PageLinkModal" onclick="changePageLink('<?php echo $app_menu_id; ?>');" title="Change Page Link" class="btn-sm btn btn-warning"><i class="fa fa-pencil"></i></button></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Icon</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Type</th>
                    <th>Laguage Key Name</th>
                    <th>Page Link</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->selectRow("resident_app_menu_utility.*,menu_category_master.*", "resident_app_menu_utility LEFT JOIN menu_category_master ON menu_category_master.menu_category_id=resident_app_menu_utility.menu_category_id", "", "ORDER BY app_menu_id ASC");

                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo $menu_title;
                          if ($wip_menu == 1) {
                            echo " <span class='badge badge-danger'>WIP</span";
                          } ?>
                      </td>
                      <td><?php echo $menu_category_name; ?>
                        <button data-toggle="modal" data-target="#changeCategoryModel" title="Change Category?" class="btn text-warning btn-sm ml-2" onclick="changeCategory('<?php echo $menu_category_id; ?>','<?php echo $app_menu_id; ?>','utility')"> <i class="fa fa-pencil"></i> </button>
                      </td>
                      <td><img src="<?php echo $bucket_url; ?>icons/<?php echo $menu_icon; ?>" width='50' alt=""></td>
                      <td><?php if ($parent_menu_id == 0) {
                            echo "Main";
                          } else {
                            echo "Sub Menu";
                          } ?></td>
                      <!-- jainish start--- -->
                      <td>
                        <?php
                        $buttonClass = ($menu_status == "0") ? 'btn-success-new' : 'btn-danger';
                        $buttonCondition = ($menu_status == "0") ? 'Active' : 'Deactive';
                        $status = ($menu_status == "0") ? 'utilityAppMenuDeactive' : 'utilityAppMenuActive';
                        $newStatus = ($menu_status == "0") ? 'utilityAppMenuActive' : 'utilityAppMenuDeactive';
                        $newStatusVal = ($menu_status == "0") ? '1' : '0';
                        $statusValue = ($menu_status == "0") ? '0' : '1';
                        ?>

                        <input type="button" class="btn btn-sm pl-1 pr-1 w-100 <?php echo $buttonClass ?>" id="<?php echo 'utility_id_' . $app_menu_id; ?>" onclick="changeStatusNew('<?php echo $app_menu_id; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'utility_id_' . $app_menu_id; ?>');" data-size="small" value="<?php echo $buttonCondition ?>" />
                      </td>
                      <!-- jainish end--- -->
                      <td>
                        <?php
                        switch ($menu_type) {
                          case '1':
                            echo "Big Menu";
                            break;
                          case '2':
                            echo "Dashboard Menu";
                            break;
                          default:
                            echo "Other Menu";
                            break;
                        }
                        ?>
                      </td>
                      <td><?php echo $data['language_key_name'] ?> <button data-toggle="modal" data-target="#LangKeyModal" onclick="changeUtilityLangKey('<?php echo $app_menu_id; ?>');" title="Change Language Key" class="btn-sm btn btn-warning"><i class="fa fa-pencil"></i></button></td>
                      <td><?php echo $data['page_link'] ?? '' ?> <button data-toggle="modal" data-target="#PageLinkModal" onclick="changeUtilityPageLink('<?php echo $app_menu_id; ?>');" title="Change Page Link" class="btn-sm btn btn-warning"><i class="fa fa-pencil"></i></button></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div><!-- End Row-->
  </div>
</div>
<div class="modal fade" id="changeCategoryModel">
  <div class="modal-dialog modal-md">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Change Category</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="card-body">
          <form id="" method="POST" action="controller/menuCategoryController.php">
            <div class="row">
              <div class="col-md-12">
                <label for="menu_category_id" class="col-form-label">Category</label>
                <select name="menu_category_id" id="menu_category_id" class="form-control single-select" required>
                  <option value="">-- Select Category --</option>
                  <?php
                  $categorys = $d->select("menu_category_master", "menu_category_status='0'");
                  while ($row2 = mysqli_fetch_assoc($categorys)) {
                  ?>
                    <option value="<?php echo htmlspecialchars($row2['menu_category_id']); ?>">
                      <?php echo htmlspecialchars($row2['menu_category_name']); ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-footer text-center mt-3">
              <input type="hidden" name="changeCategory" value="changeCategory">
              <input type="hidden" name="app_menu_id" id="app_menu_id_assign">
              <input type="hidden" name="assign_type" id="assign_type">
              <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check-square-o"></i>
                Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  function changeCategory(menu_category_id, app_menu_id,assign_type) {
    $("#menu_category_id").val(menu_category_id).trigger('change');
    $("#app_menu_id_assign").val(app_menu_id);
    $("#assign_type").val(assign_type);
  }
  function changeLangKey(app_menu_id) {
    $.ajax({
      url: "getLanguageKey.php",
      cache: false,
      type: "POST",
      data: {
        app_menu_id: app_menu_id
      },
      success: function(response) {
        $('#langKeyFrm').html(response);
      }
    });
  }
  function changeUtilityLangKey(app_menu_id) {
    $.ajax({
      url: "getLanguageKey.php",
      cache: false,
      type: "POST",
      data: {
        app_menu_utility_id: app_menu_id
      },
      success: function(response) {
        $('#langKeyFrm').html(response);
      }
    });
  }
  function changePageLink(app_menu_id) {
    $.ajax({
      url: "getPageLink.php",
      cache: false,
      type: "POST",
      data: {
        app_menu_id: app_menu_id
      },
      success: function(response) {
        $('#pageLinkFrm').html(response);
        initPageLinkValidation();
      }
    });
  }
  function changeUtilityPageLink(app_menu_id) {
    $.ajax({
      url: "getPageLink.php",
      cache: false,
      type: "POST",
      data: {
        app_menu_utility_id: app_menu_id
      },
      success: function(response) {
        $('#pageLinkFrm').html(response);
        initPageLinkValidation();
      }
    });
  }
  function initPageLinkValidation() {
    if (typeof $.validator !== 'undefined') {
      $.validator.addMethod("noSpaceAllowed", function(value, element) {
        return value.indexOf(" ") < 0 && value.trim() != "";
      }, "Space Not Allowed");

      $("#pageLinkFrm").validate({
        errorPlacement: function(error, element) {
          error.insertAfter(element);
        },
        rules: {
          page_link: {
            required: true,
            noSpaceAllowed: true
          }
        },
        messages: {
          page_link: {
            required: "Please enter Page Link",
            noSpaceAllowed: "Space Not Allowed"
          }
        },
        submitHandler: function(form) {
          $(':input[type="submit"]').prop('disabled', true);
          form.submit();
        }
      });
    }
  }

</script>
<div class="modal fade" id="LangKeyModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Edit Language Key</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="langKeyData">
        <div class="m-2">
          <form id="langKeyFrm" action="controller/appMenuController.php" method="post">
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="PageLinkModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Edit Page Link</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="pageLinkData">
        <div class="m-2">
          <form id="pageLinkFrm" action="controller/appMenuController.php" method="post">
          </form>
        </div>
      </div>
    </div>
  </div>
</div>