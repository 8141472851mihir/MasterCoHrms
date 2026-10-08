<?php 
include_once './common/object.php';
extract(array_map("test_input", $_POST));
include_once 'sidebarController.php'; 
?>
<ul class="sidebar-menu do-nicescrol">
  <?php
  foreach ($sidebareData as $data) {
    $menu_id = $data['menu_id'] ?? null;
    $menu_icon = $data['menu_icon'] ?? null;
    $menu_name = $data['menu_name'] ?? null;
    $menu_link = $data['menu_link'] ?? null;
    $isSubMenu = !empty($data['subMenu']) ?? null;
    $language_key_name = $data['language_key_name'] ?? null;
    $lngMenuName = $xml->string->$language_key_name ?? null;
    if ($data['subMenu'] == "") {
      if (in_array($menu_id, $accessMenuIdArr)) {  ?>
        <li class="<?php echo ($_GET['f'] == $data['menu_link']) ? 'active' : ''; ?>">
          <a href="<?php echo $data['menu_link']; ?>" class="waves-effect ">
            <i class="<?php echo $data['menu_icon']; ?>"></i> <span><?php if ($lngMenuName != "") {
              echo  $lngMenuName;
            } else {
              echo $data['menu_name'];
            } ?></span>
          </a>
        </li>
      <?php }
    } else  if ($data['subMenu'] != "") {
      $parentActive = "";
      foreach ($data['subMenu'] as $temData) {
        $temData['menu_link'] . ',';
        if ($temData['menu_link'] == $_GET['f']) {
          $parentActive = "active";
        }
      }

      ?>
      <li class="<?php echo $parentActive; ?>">
        <a href="javaScript:void();" class="waves-effect">
          <i class="<?php echo $data['menu_icon']; ?>"></i>
          <span><?php if ($lngMenuName != "") {
            echo  $lngMenuName;
          } else {
            echo $data['menu_name'];
          } ?></span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="sidebar-submenu">
          <?php
          foreach ($data['subMenu'] as $temData) {
            $menu_id1 = $temData['menu_id'];
            $language_key_name = $temData['language_key_name'];
            $lngSubMenuName = $xml->string->$language_key_name ?? null;
            if (in_array($temData['menu_id'], $accessMenuIdArr)) {
              $active = "";
              if ($_GET['f'] == $temData['menu_link']) {
                $active = "active";
              }
              if ($lngSubMenuName != "") {
                $menu_name = $lngSubMenuName;
              } else {
                $menu_name = $temData['menu_name'];
              }
              ?>
              <li class="<?php echo $active; ?>"><a href="<?php echo $temData['menu_link']; ?>"><i class="fa fa-angle-double-right "></i><?php echo $menu_name; ?></a>
              </li>
              <?php
            }
          } ?>
        </ul>
      </li>
      <?php
    }
  }
  ?>

  <li class="sidebar-header no-border"></li>
  <li class="sidebar-header no-border"></li>
  <li class="sidebar-header no-border"></li>
</ul>