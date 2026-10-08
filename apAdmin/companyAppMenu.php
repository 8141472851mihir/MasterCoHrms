<?php
$apiGridWidth = '3';
$gridWidth = 12 / $apiGridWidth;
extract($_GET);
if (isset($_GET['sId'])) {
  $sId = $d->sanitizeReportFilterIdAsInt($sId);
}
if (isset($sId)) {
  $id_data = $d->selectRow("app_menu_id", "resident_app_menu_society", "society_id = '$sId'", "");
} else {
  $id_data = $d->selectRow("app_menu_id", "resident_app_menu_society", "", "");
}
$id_arr = [];
$id_str = "";
while ($iddata = mysqli_fetch_array($id_data)) {
  $id_arr[] = $iddata['app_menu_id'];
}
$id_str = implode(",", $id_arr);
unset($id_arr);
?>
<!-- <link href="assets/css/jquery-ui.css" rel="stylesheet"> -->
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12 col-sm-6 col-md-3">
        <h4 class="page-title"><?php echo $xml->string->society; ?> App Menu </h4>
      </div>
      <div class="col-12 col-sm-6 col-md-3 pb-2">
        <form method="get">
          <input type="hidden" name="changeOrder" value="<?php echo $_REQUEST['changeOrder']; ?>">
          <select class="form-control single-select" name="sId" onchange="this.form.submit()">
            <option value=""> Select <?php echo $xml->string->society; ?> </option>
            <?php $qs = $d->select("society_master", "society_status=0 $countryAppendQuerySocietySingle", "order by society_id  DESC ");
            while ($sdata = mysqli_fetch_array($qs)) { ?>
              <option <?php if (isset($_GET['sId']) && $sdata['society_id'] == $_GET['sId']) {
                        echo "selected";
                      } ?> value="<?php echo $sdata['society_id']; ?>"><?php echo $sdata['society_name']; ?>-<?php echo $sdata['city_name']; ?></option>
            <?php } ?>
          </select>
        </form>
      </div>
      <div class="col-12 col-sm-12 col-md-6 text-right pb-2">
        <?php
        if (isset($sId)) {
          if ($_GET['changeOrder'] == 'yes') {
            $dragId = "menu-list";
            $dragIdSub = "menu-listSub";
            $dragIdBig = "menu-listBig";
            $cursorpoin = 'style="cursor: pointer;"';
        ?>
            <span class="text-warning"><b>Drag & Drop Change Order </b></span>
            <a href="companyAppMenu?sId=<?= $sId ?>&changeOrder=&csrf=<?php echo $_SESSION['token']; ?>" class="btn btn-info btn-sm waves-effect waves-light float-right"><i class="fa fa-reply" aria-hidden="true"></i> Go Back</a>
          <?php
          } else {
          ?>
            <a href="companyAppMenu?sId=<?= $sId ?>&changeOrder=yes" class="btn btn-info btn-sm waves-effect waves-light"><i class="fa fa-swap mr-1"></i> Change Menu Order</a>
        <?php
          }
        }
        ?>
        <?php if (isset($_GET['sId']) &&  $_GET['changeOrder'] != 'yes') { ?>
          <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal"> +ADD </button>
        <?php } ?>
      </div>
    </div>

    <!-- Table Section -->
    <div class="row" <?php echo ($_GET['changeOrder'] == 'yes') ? "style='display:none'" : ""; ?>>
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <?php if (isset($_GET['sId'])) { ?>
              <div class="table-responsive">
                <table id="example" class="table table-bordered">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Icon</th>
                      <th>New Icon</th>
                      <th>Type</th>
                      <th style="max-width: 120px !important;">Status</th>
                      <th style="max-width: 120px !important;">Android Status</th>
                      <th style="max-width: 120px !important;">iOs Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $i = 1;
                    $q = $d->select("resident_app_menu,resident_app_menu_society,society_master", "society_master.society_id=resident_app_menu_society.society_id AND resident_app_menu.menu_status=0  AND resident_app_menu_society.app_menu_id=resident_app_menu.app_menu_id AND resident_app_menu_society.society_id='$sId' $countryAppendQuerySociety", "ORDER BY resident_app_menu_society.menu_sequence ASC");

                    while ($data = mysqli_fetch_array($q)) {
                      extract($data);
                    ?>
                      <tr id="<?php echo $app_menu_society_id . "-" . $sId; ?>">
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $menu_title; ?></td>
                        <td><img src="<?php echo $bucket_url; ?>icons/<?php echo $menu_icon_new; ?>" width='50' alt=""></td>
                        <td><img src="<?php echo $bucket_url; ?>icons/<?php echo $menu_icon; ?>" width='50' alt=""></td>
                        <td><?php echo ($parent_menu_id == 0) ? "Main" : "Sub Menu"; ?></td>
                        <td>
                          <?php
                          $buttonClass = ($menu_status == "0") ? 'btn-success-new' : 'btn-danger';
                          $buttonCondition = ($menu_status == "0") ? 'Active' : 'Deactive';
                          $status = ($menu_status == "0") ? 'appMenuDeactiveSociety' : 'appMenuActiveSociety';
                          $newStatus = ($menu_status == "0") ? 'appMenuActiveSociety' : 'appMenuDeactiveSociety';
                          $newStatusVal = ($menu_status == "0") ? '1' : '0';
                          $statusValue = ($menu_status == "0") ? '0' : '1';
                          ?>

                          <input type="button" class="btn btn-sm pl-1 pr-1 w-100 <?php echo $buttonClass ?>" id="<?php echo 'menu_id_' . $app_menu_society_id; ?>" onclick="changeStatusNew('<?php echo $app_menu_society_id; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'menu_id_' . $app_menu_society_id; ?>');" data-size="small" value="<?php echo $buttonCondition ?>" />
                        </td>
                        <td>
                          <?php if ($menu_status == "0") {
                            $buttonClass = ($menu_status_android == "0") ? 'btn-success-new' : 'btn-danger';
                            $buttonCondition = ($menu_status_android == "0") ? 'Active' : 'Deactive';
                            $status = ($menu_status_android == "0") ? 'appMenuDeactiveSocietyAndroid' : 'appMenuActiveSocietyAndroid';
                            $newStatus = ($menu_status_android == "0") ? 'appMenuActiveSocietyAndroid' : 'appMenuDeactiveSocietyAndroid';
                            $newStatusVal = ($menu_status_android == "0") ? '1' : '0';
                            $statusValue = ($menu_status_android == "0") ? '0' : '1';
                          ?>

                            <input type="button" class="btn btn-sm pl-1 pr-1 w-100 <?php echo $buttonClass ?>" id="<?php echo 'menu_id_android_' . $app_menu_society_id; ?>" onclick="changeStatusNew('<?php echo $app_menu_society_id; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'menu_id_android_' . $app_menu_society_id; ?>');" data-size="small" value="<?php echo $buttonCondition ?>" />
                          <?php } ?>
                        </td>
                        <td>
                          <?php if ($menu_status == "0") {
                            $buttonClass = ($menu_status_ios == "0") ? 'btn-success-new' : 'btn-danger';
                            $buttonCondition = ($menu_status_ios == "0") ? 'Active' : 'Deactive';
                            $status = ($menu_status_ios == "0") ? 'appMenuDeactiveSocietyIos' : 'appMenuActiveSocietyIos';
                            $newStatus = ($menu_status_ios == "0") ? 'appMenuActiveSocietyIos' : 'appMenuDeactiveSocietyIos';
                            $newStatusVal = ($menu_status_ios == "0") ? '1' : '0';
                            $statusValue = ($menu_status_ios == "0") ? '0' : '1';
                          ?>

                            <input type="button" class="btn btn-sm pl-1 pr-1 w-100 <?php echo $buttonClass ?>" id="<?php echo 'menu_id_ios_' . $app_menu_society_id; ?>" onclick="changeStatusNew('<?php echo $app_menu_society_id; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'menu_id_ios_' . $app_menu_society_id; ?>');" data-size="small" value="<?php echo $buttonCondition ?>" />
                          <?php } ?>
                        </td>
                        <td>
                          <form method="POST" class="form-horizontal" action="./controller/societyAppmenuController.php">
                            <input type="hidden" name="sId" value="<?php echo $_GET['sId'] ?>" />
                            <input type="hidden" name="delete_menu" value="delete_menu" />
                            <input type="hidden" name="menuName" value="<?php echo $menu_title; ?>" />
                            <input type="hidden" name="app_menu_society_id" value="<?php echo $data['app_menu_society_id'] ?>" />
                            <input type="hidden" name="csrf" id="csrf" value="<?php echo $_SESSION['token']; ?>" />
                            <button class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></button>
                          </form>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php } else {
              echo "Please Select Company";
            } ?>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid" <?php echo (!isset($_GET['changeOrder'])) ? "style='display:none'" : "" ?>>
      <?php
      $bigMenus = [];
      $dashboardMenus = [];
      $subMenusByCategory = [];
      $otherMenus = [];
      $menuAppendQuery = " AND resident_app_menu_society.menu_status='0'";
      $orderBy = "ORDER BY CASE  WHEN resident_app_menu.menu_type = 1 THEN 1  WHEN resident_app_menu.menu_type = 2 THEN 2  WHEN resident_app_menu_society.alternate_sequence > 0 THEN 3  ELSE 4 END,resident_app_menu_society.alternate_sequence ASC,resident_app_menu_society.menu_sequence ASC,resident_app_menu_society.app_menu_id ASC";
      $singleQry = $d->selectRow("resident_app_menu.*,resident_app_menu_society.*", "resident_app_menu,resident_app_menu_society", "resident_app_menu.menu_status=0  AND resident_app_menu_society.app_menu_id=resident_app_menu.app_menu_id  AND resident_app_menu.parent_menu_id=0 AND resident_app_menu_society.society_id='$society_id' AND resident_app_menu.menu_type IN (0, 1, 2) $menuAppendQuery", "$orderBy");
      if (mysqli_num_rows($singleQry) > 0) {
        $dashboardCount = 0;
        while ($data_app = mysqli_fetch_array($singleQry)) {
          $menuId = $data_app["app_menu_id"];
          $menuType = $data_app["menu_type"];
          $alternateSequence = $data_app["alternate_sequence"];
          $categoryId = $data_app['menu_category_id'];
          $appmenu = array();

          if ($menuId == 37 || $menuId == 41) {
            array_push($bigMenus, $data_app);
          } else if ($menuType == '2' || $menuId == 1 || $menuId == 2) {
            array_push($dashboardMenus, $data_app);
            $dashboardCount++;
          } else if ($alternateSequence > 0) {
            if (($dashboardCount % $apiGridWidth) != 0 || $dashboardCount == 0) {
              array_push($dashboardMenus, $data_app);
              $dashboardCount++;
            } else {
              if ($categoryId != '0') {
                if (!isset($subMenusByCategory[$categoryId])) {
                  $subMenusByCategory[$categoryId] = [];
                }
                array_push($subMenusByCategory[$categoryId], $data_app);
              } else {
                array_push($otherMenus, $data_app);
              }
            }
          } else if ($menuType == '0') {
            if (($dashboardCount % $apiGridWidth) != 0) {
              $data_app["alternate_sequence"]='1';
              array_push($dashboardMenus, $data_app);
              $dashboardCount++;
            } else {
              if ($categoryId != '0') {
                if (!isset($subMenusByCategory[$categoryId])) {
                  $subMenusByCategory[$categoryId] = [];
                }
                array_push($subMenusByCategory[$categoryId], $data_app);
              } else {
                array_push($otherMenus, $data_app);
              }
            }
          }
        }
      }
      ?>
      <div class="col-12">
        <h5>Big Menu</h5>
      </div>
      <div class="row" id="<?php echo $dragIdBig; ?>" <?php echo $cursorpoin; ?>>
        <?php foreach ($bigMenus as $data12) {
          extract($data12);
          $homeGridWidth = '6';
        ?>
          <div class="col-12 col-md-4 col-lg-<?php echo $homeGridWidth; ?> adminBoxBig p-1" data-post-id="<?php echo $app_menu_society_id . "-" . $sId; ?>">
            <div class="card radius-15 h-100">
              <div class="card-body text-center">
                <div class="p-2 ">
                  <img onerror="this.src='img/user.png'" src="img/user.png" data-src="<?php echo $bucket_url; ?>icons/<?php echo $menu_icon; ?>" width="70" height="70" class=" lazyload" alt="">
                  <h6 class="mb-0 mt-5"><?= $menu_title ?> </h6>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
      <div class="col-12 mt-3">
        <h5>Dashboard Menu</h5>
      </div>
      <div class="row" id="<?php echo $dragId; ?>" <?php echo $cursorpoin; ?>>
        <?php
        $spot_id = 0;
        $dashboardMenuCount = count($dashboardMenus);
        $empty_spots = $apiGridWidth - ($dashboardMenuCount % $apiGridWidth);
        $empty_spots = ($empty_spots != $apiGridWidth) ? $empty_spots : 0;

        foreach ($dashboardMenus as $data12) {
          extract($data12);
          // $gridWidth = '2';
        ?>
          <div class="col-12 col-md-4 col-lg-<?php echo $gridWidth; ?> adminBox p-1" data-post-id="<?php echo $app_menu_society_id . "-" . $sId; ?>">
            <div class="card radius-15 h-100">
              <div class="card-body text-center">
                <div class="p-2 ">
                  <img onerror="this.src='img/user.png'" src="img/user.png" data-src="<?php echo $bucket_url; ?>icons/<?php echo $menu_icon; ?>" width="70" height="70" class=" lazyload" alt="">
                  <h6 class="mb-0 mt-5"><?= $menu_title ?> </h6>
                  <?php if ($alternate_sequence > 0):
                  ?>
                    <input type="hidden" name="alternate_menus[]" value="<?php echo $app_menu_id; ?>">
                    <div class="mt-2">
                       <button type="button" class="btn btn-primary btn-circle btn-sm" data-toggle="modal"  data-target="#dashboardMenuModal" onclick="setEmptySpot(<?php echo $spot_id++; ?>)">
                          <i class="fa fa-plus"> Change Menu</i>
                        </button>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>

    <div class="col-12 mt-3">
      <h5>Sub Menu</h5>
    </div>

    <?php
    $menu_category_master = $d->select("menu_category_master", "");
    $categories = [];
    while ($category = mysqli_fetch_array($menu_category_master)) {
      $categories[$category['menu_category_id']] = $category;
    }
    foreach ($subMenusByCategory as $categoryId => $menus) {
      if (isset($categories[$categoryId]) && count($menus) > 0) {
        $category = $categories[$categoryId];
        $catId = $category['menu_category_id'];
        $catName = $category['menu_category_name'];
        $caticon = $category['menu_category_icon'];
    ?>
        <div class="col-12 mx-2 px-3 py-2 mt-2 bg-primary rounded d-flex justify-content-between align-items-center">
          <span class="text-white fw-bold"><?= $catName ?></span>

          <?php if (file_exists("<?php echo $bucket_url; ?>icons/$caticon")) { ?>
            <a data-fancybox="images"
              data-caption="Photo Name : <?php echo $caticon; ?>"
              href="<?php echo $bucket_url; ?>icons/<?php echo $caticon ?>"
              target="_blank">
              <img src="<?= "<?php echo $bucket_url; ?>icons/" . $caticon ?>"
                alt="Category Icon"
                width="35"
                height="35"
                class="category-icon rounded shadow-sm border bg-white p-1">
            </a>
          <?php } ?>
        </div>

        <div class="row w-100 p-4 <?php echo $dragIdSub ?>" <?php echo $cursorpoin; ?>>
          <?php foreach ($menus as $data12) {
            extract($data12);
            // $gridWidth = '2';
          ?>
            <div class="col-12 col-md-4 col-lg-<?php echo $gridWidth; ?> mt-2 adminBoxSub p-1" data-post-id="<?php echo $app_menu_society_id . "-" . $sId; ?>">
              <div class="card radius-15 h-100">
                <div class="card-body text-center">
                  <div class="p-2 ">
                    <img onerror="this.src='img/user.png'" src="img/user.png" data-src="<?php echo $bucket_url; ?>icons/<?php echo $menu_icon; ?>" width="70" height="70" class=" lazyload" alt="">
                    <h6 class="mb-0 mt-5"><?= $menu_title ?> </h6>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      <?php
      }
    }

    if (count($otherMenus) > 0) {
      ?>
      <div class="col-12 mt-3 bg-primary">
        <p class="text-white mt-2">Other</p>
      </div>
      <div class="row w-100 p-4 <?php echo $dragIdSub ?>" <?php echo $cursorpoin; ?>>
        <?php foreach ($otherMenus as $data13) {
          extract($data13);
          // $gridWidth = '2';
        ?>
          <div class="col-12 col-md-4 col-lg-<?php echo $gridWidth; ?> mt-2 adminBoxSub p-1" data-post-id="<?php echo $app_menu_society_id . "-" . $sId; ?>">
            <div class="card radius-15 h-100">
              <div class="card-body text-center">
                <div class="p-2 ">
                  <img onerror="this.src='img/user.png'" src="img/user.png" data-src="<?php echo $bucket_url; ?>icons/<?php echo $menu_icon; ?>" width="70" height="70" class=" lazyload" alt="">
                  <h6 class="mb-0 mt-5"><?= $menu_title ?> </h6>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    <?php } ?>
  </div>
</div>
</div>
</div>

<div class="modal fade" id="exampleModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="exampleModalLabel">Add New Menu </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="chngProfileFrm" method="POST" class="form-horizontal" action="controller/societyAppmenuController.php" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Select Menu<span class="required">*</span></label>
            <div class="col-sm-8">
              <select required="" multiple="multiple" class="form-control multiple-select" name="app_menu_id[]" id="app_menu_id">
                <option class="form-control"></option>
                <?php
                if ($id_str != "") {
                  $q = $d->select("resident_app_menu", "app_menu_id NOT IN ($id_str)", "");
                } else {
                  $q = $d->select("resident_app_menu", "", "");
                }
                while ($data = mysqli_fetch_array($q)) { ?>
                  <option value="<?= $data['app_menu_id']; ?>"><?= $data['menu_title']; ?>
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="col-md-12 text-center">
            <?php if (isset($_GET['sId'])) { ?>
              <input type="hidden" name="sId" id="sId" value="<?php echo $_GET['sId'] ?>" />
              <input type="hidden" name="add_menu" id="add_menu" value="add_menu" />
              <input type="hidden" name="csrf" id="csrf" value="<?php echo $_SESSION['token']; ?>" />
              <button type="submit" class="btn btn-success" name="add_menu" id="add_menu" value="submit">Submit</button>
            <?php } ?>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="dashboardMenuModal" role="dialog" aria-labelledby="dashboardMenuModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="dashboardMenuModalLabel">Add Menu to Dashboard</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="dashboardMenuForm" method="POST" action="controller/societyAppmenuController.php">
        <div class="modal-body">
          <div class="form-group">
            <label for="dashboardMenuSelect">Select Menu to Add</label>
            <select class="form-control single-select" id="dashboardMenuSelect" name="app_menu_id" required>
              <option value="">-- Select Menu --</option>
              <?php
              $nonDashboardMenus = $d->select(
                "resident_app_menu,resident_app_menu_society",
                "resident_app_menu_society.app_menu_id=resident_app_menu.app_menu_id 
                 AND resident_app_menu_society.society_id='$sId' 
                 AND resident_app_menu.menu_type = '0' AND (resident_app_menu_society.alternate_sequence IS NULL OR resident_app_menu_society.alternate_sequence = 0)
                 AND resident_app_menu.menu_status=0 
                 AND resident_app_menu_society.menu_status=0",
                "ORDER BY resident_app_menu.menu_title ASC"
              );

              while ($menu = mysqli_fetch_array($nonDashboardMenus)) {
                echo '<option value="' . $menu['app_menu_id'] . '">' . $menu['menu_title'] . '</option>';
              }
              ?>
            </select>
          </div>
          <input type="hidden" name="spot_number" id="spotNumber">
          <input type="hidden" name="alternateMenuIds" id="alternateMenuIds">
          <input type="hidden" name="sId" value="<?php echo $sId; ?>">
          <input type="hidden" name="addToDashboard" value="addToDashboard">
          <input type="hidden" name="csrf" value="<?php echo $_SESSION['token']; ?>">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add to Dashboard</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script>
  function setEmptySpot(spotNumber) {
    document.getElementById('spotNumber').value = spotNumber;
    let menus = document.querySelectorAll('input[name="alternate_menus[]"]');
    let menuIds = Array.from(menus).map(input => input.value);
    document.getElementById("alternateMenuIds").value = menuIds.join(",");
  }

  $('#dashboardMenuModal').on('shown.bs.modal', function() {
    $('#dashboardMenuSelect').select2('destroy');
    $('#dashboardMenuSelect').select2({
      placeholder: "-- Select Menu --",
      allowClear: true,
      dropdownParent: $('#dashboardMenuModal'),
      width: '100%'
    });
  });

  $('#dashboardMenuModal').on('hidden.bs.modal', function() {
    $('#dashboardMenuSelect').select2('destroy');
  });

  function removeFromDashboard(app_menu_society_id, sId) {
    Swal.fire({
      title: "Are you sure?",
      text: "Do you really want to remove this menu from dashboard?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Yes, remove it!",
      cancelButtonText: "Cancel"
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "controller/societyAppmenuController.php",
          type: "POST",
          data: {
            removeFromDashboard: true,
            app_menu_society_id: app_menu_society_id,
            sId: sId,
            csrf: '<?php echo $_SESSION["token"]; ?>'
          },
          success: function(response) {
            location.reload();
          }
        });
      }
    });
  }
</script>