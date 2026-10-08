<?php
session_start();
$_SESSION["token"] = md5(uniqid(mt_rand(), true));
include_once 'object.php';
include_once 'checkLogin.php';
include_once 'accessControl.php';
include_once 'accessControlPage.php';
date_default_timezone_set('Asia/Kolkata');
$bms_admin_id = $bms_admin_id ?? "";
$countryAryAccess = array();
$cCheck = $d->select("admin_country_master", "bms_admin_id='$bms_admin_id'");
$countryAryAccess = [];
while ($accessCountry = mysqli_fetch_assoc($cCheck)) {
  $countryAryAccess[] = $accessCountry['country_id'];
}
if (!empty($countryAryAccess)) {
  $countryids = implode("','", $countryAryAccess);
  $countryAppendQuerySocietySingle = " AND country_id IN ('$countryids')";
  $countryAppendQuerySocietySingleReq = " AND request_country_id IN ('$countryids')";
  $countryAppendQuerySociety = " AND society_master.country_id IN ('$countryids')";
} else {
  $countryAppendQuerySocietySingle = "";
  $countryAppendQuerySocietySingleReq = "";
  $countryAppendQuerySociety = "";
  $countryids = "";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title><?php echo ucwords($_GET['f']); ?> | <?php echo $d->app_name(); ?> </title>
  <!--favicon-->
  <link rel="icon" href="../img/fav.png" type="image/png">
  <?php include 'colours.php'; ?>
  <!-- simplebar CSS-->
  <link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
  <!-- Bootstrap core CSS-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
  <!-- animate CSS-->
  <link href="assets/css/animate.css" rel="stylesheet" type="text/css" />
  <!-- Icons CSS-->
  <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
  <!-- Sidebar CSS-->
  <link href="assets/css/sidebar-menu.css" rel="stylesheet" />
  <!-- Custom Style-->
  <link href="assets/css/app-style.css" rel="stylesheet" />
  <link href="assets/css/custom.css" rel="stylesheet" />

  <link href="assets/plugins/bootstrap-datatable/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
  <link href="assets/plugins/bootstrap-datatable/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css">
  
  <!-- ColReorder CSS -->
<link href="assets/plugins/bootstrap-datatable/css/colReorder.dataTables.css" rel="stylesheet" type="text/css">

  <!--Lightbox Css-->
  <link href="assets/plugins/fancybox/css/jquery.fancybox.min.css" rel="stylesheet" type="text/css" />

  <!-- notifications css -->
  <link rel="stylesheet" href="assets/plugins/notifications/css/lobibox.min.css" />

  <!--Select Plugins-->
  <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" />
  <!--inputtags-->
  <link href="assets/plugins/inputtags/css/bootstrap-tagsinput.css" rel="stylesheet" />
  <!--multi select-->
  <link href="assets/plugins/jquery-multi-select/multi-select.css" rel="stylesheet" type="text/css">
  <!--Bootstrap Datepicker-->
  <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

  <!--material datepicker css-->
  <link rel="stylesheet" href="assets/plugins/material-datepicker/css/bootstrap-material-datetimepicker.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

  <!--Select Plugins-->
  <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" />

  <link rel="stylesheet" href="assets/plugins/summernote/dist/summernote-bs4.css" />

    <link rel="stylesheet" type="text/css" href="assets/css/daterangepicker.css" />

  <link href="assets/css/jquery-ui.css" rel="stylesheet">
</head>
<style type="text/css">
.ui-autocomplete {
    z-index: 9999 !important; 
    position: absolute;      
    background-color: white; 
    border: 1px solid #ccc;  
    width: auto;               
}
</style>
<link rel="manifest" href="../manifest.json">
<meta name="theme-color" content="#000000">
<body>
  <div class="ajax-loader">
    <img src="../img/ajax-loader.gif" class="img-responsive" />
  </div>
  <div id="spinner"> </div>

  <!-- Start wrapper-->
  <div id="wrapper">

    <!--Start sidebar-wrapper-->
    <div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
      <div class="brand-logo">
        <a href="welcome">
          <img src="../img/logo.png" class="logo-icon" alt="logo">
          <h5 class="logo-text">MASTER </h5>
        </a>
      </div>
      <?php include 'sidebar.php'; ?>
    </div>
    <!--End sidebar-wrapper-->

    <!--Start topbar header-->
    <header class="topbar-nav">
      <nav class="navbar navbar-expand fixed-top bg-primary">
        <ul class="navbar-nav mr-auto align-items-center">
          <li class="nav-item">
            <a class="nav-link toggle-menu" href="javascript:void();">
              <i class="icon-menu menu-icon"></i>
            </a>
          </li>
          <li class="nav-item">
            <form autocomplete="off" class="search-bar" action="">
              <input type="text" id="searchMenu" class="form-control ui-autocomplete-input" placeholder="Search..">
              <a href="javascript:void();"><i class="icon-magnifier"></i></a>
            </form>
          </li>
        </ul>
        <ul class="navbar-nav align-items-center right-nav-link">
          <?php if ($role_id == 1) { ?>
            <li class="nav-item dropdown-lg">
              <a data-toggle="modal" data-target="#notification" class="nav-link dropdown-toggle dropdown-toggle-nocaret waves-effect" href="javascript:void();">
                <i class="fa fa-bullhorn"></i><span class="badge badge-warning badge-up">+</span></a>
            </li>
            <li class="nav-item dropdown-lg">
              <a data-toggle="modal" data-target="#webNotification" class="nav-link dropdown-toggle dropdown-toggle-nocaret waves-effect" href="javascript:void();">
                <i class="fa fa-comment"></i><span class="badge badge-yellow badge-up">+</span></a>
            </li>
          <?php } ?>
          <li class="nav-item dropdown-lg">
            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret waves-effect" data-toggle="dropdown" href="javascript:void();">
              <i class="fa fa-bell-o"></i><span class="badge badge-info badge-up"><?php echo $d->count_data_direct("notification_id", "admin_notification", "read_status=0 AND admin_id ='$bms_admin_id'"); ?></span></a>
            <div class="dropdown-menu dropdown-menu-right">
              <ul class="list-group list-group-flush">
              <?php 
                $aq = $d->select("admin_notification", " read_status=0 AND admin_id = '$bms_admin_id'", "ORDER BY notification_id DESC LIMIT 5");
                while ($adminNotification = mysqli_fetch_array($aq)) {
              ?>
          </li>
          <li class="list-group-item">
            <a onclick="readNotification('<?php echo $adminNotification['admin_click_action']; ?>',<?php echo $adminNotification['notification_id'] ?>)" href="javascript:void(0)">
              <div class="media">
                <i class="fa fa-bell fa-2x mr-3 text-primary"></i>
                <div class="media-body">
                  <h6 class="mt-0 msg-title"><?php echo $adminNotification['notification_tittle']; ?></h6>
                  <p class="msg-info"><?php echo $adminNotification['notification_description']; ?></p>
                </div>
              </div>
            </a>
          </li>
        <?php } ?>
        <li class="list-group-item"><a href="adminNotification">See All Notifications</a></li>
        </ul>
  </div>
  </li>

  <li class="nav-item">
    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
      <span class="user-profile"><img onerror="this.src='img/user.png'" src="../img/profile/<?php echo $admin_profile; ?>" class="img-circle" alt="user avatar"> <?php $nameAry = explode(" ", $admin_name); echo $nameAry[0]; ?> <i class="fa fa-angle-down"></i></span>
    </a>
    <ul class="dropdown-menu dropdown-menu-right">
      <li class="dropdown-item user-details">
        <a href="javascript:void();">
          <div class="media">
            <div class="avatar"><img onerror="this.src='img/user.png'" class="align-self-start mr-3" src="../img/profile/<?php echo $admin_profile; ?>" alt="user avatar"></div>
            <div class="media-body">
              <h6 class="mt-2 user-title"><?php echo $admin_name; ?></h6>
              <p class="user-subtitle"><?php echo $admin_email ?></p>
            </div>
          </div>
        </a>
      </li>
      <li class="dropdown-divider"></li>
      <a href="profile">
        <li class="dropdown-item"><i class="icon-wallet mr-2"></i> My Profile</li>
      </a>
      <li class="dropdown-divider"></li>
      <form method="POST" action="logout.php">
        <button class="form-btn dropdown-item p-0">
          <li class="dropdown-item"><i class="icon-power mr-2"></i> Logout</li>
        </button>
      </form>
    </ul>
  </li>
 
  </ul>
  </nav>
  </header>
  <!--End topbar header-->
  <div class="clearfix"></div>