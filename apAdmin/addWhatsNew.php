<?php
if (isset($_GET) && $_GET['patch_id'] != '') {
  $patch_id = $_GET['patch_id']??"";
  $language_id = $_GET['language_id']??"";
  $patch_qry = $d->selectRow("patch_master.*", "patch_master", "patch_id='$patch_id'", "ORDER BY patch_id DESC");
  if (mysqli_num_rows($patch_qry) > 0) {
    $patch_data = mysqli_fetch_array($patch_qry);
    $patch_title = $patch_data['patch_title'] ?? "";
    $version = $patch_data['version'] ?? "";
    $platform = $patch_data['platform'] ?? "";
    $whats_new = $patch_data['whats_new'] ?? "";
  } else {
    header("location:../addWhatsNew");
  }
}
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title"><?php echo ($patch_id == '') ? "Add" : "Edit"; ?> What's New</h4>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="welcome">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">What's New</li>
        </ol>
      </div>
      <div class="col">
        <div class="btn-group float-sm-right">
          <a href="whatsNew" class="btn btn-sm btn-primary"><i class="fa fa-list"></i> View List</a>
        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->

    <div class="row">
      <div class="col-lg-12">
        <form id="whatsNewForm" action="controller/whatsNewController.php" method="post" enctype="multipart/form-data">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="title">Title <span class="required">*</span></label>
                    <div class="col-sm-4">
                      <input type="text" autocomplete="off" maxlength="50" minlength="5" class="form-control" id="title" name="title" value="<?php echo ($patch_id != '') ? $patch_title : ''; ?>" required>
                    </div>
                    <label for="platform" class="col-sm-2 col-form-label">Platform <span class="required">*</span></label>
                    <div class="col-sm-4">
                      <select type="text" required onchange="webUpdates()" id="platform" class="form-control single-select" <?php echo ($patch_id != '') ? 'disabled' : ''; ?> name="platform">
                        <option value="0" <?php echo ($patch_id != '' && $platform == '0') ? 'selected' : ''; ?>>Android</option>
                        <option value="1" <?php echo ($patch_id != '' && $platform == '1') ? 'selected' : ''; ?>>iOS</option>
                        <option value="2" <?php echo ($patch_id != '' && $platform == '2') ? 'selected' : ''; ?>>Web</option>
                      </select>
                    </div>
                  </div>
                  <?php if($patch_id!=""){
                    ?>
                    <input type="hidden" class="mt-5" name="platform" id="platform" value="<?php echo $platform; ?>" />
                    <?php
                  }?>
                  <div class="form-group row versionbox">
                    <label class="col-sm-2 col-form-label" for="version">Version <span class="required">*</span></label>
                    <div class="col-sm-4">
                      <input type="text" autocomplete="off" maxlength="20" <?php echo ($patch_id != '') ? 'readonly' : ''; ?> value="<?php echo ($patch_id != '') ? $version : ''; ?>" class="form-control onlyNumber" id="version" name="version" required>
                    </div>
                  </div>
                <div id="accordion">
                  <?php 
                  if(isset($patch_id) && $patch_id!="" && isset($language_id) && $language_id!="" && isset($version) && $version!="" && isset($platform) && $platform!=""){
                    $language_qry = $d->selectRow("language_master.*, patch_master.whats_new","language_master LEFT JOIN patch_master ON language_master.language_id = patch_master.language_id AND patch_master.version = '$version' AND patch_master.platform = '$platform'","language_master.active_status = '0'");
                  }else{
                    $language_qry = $d->selectRow("language_master.*", "language_master", "language_master.active_status='0'");
                  }
                  $i = 0;
                  while($lan_data = mysqli_fetch_array($language_qry)){
                  ?>
                  <div class="mt-3">
                   <input type="hidden" class="mt-5" name="whats_new_updates_<?php echo $i; ?>" id="whats_new_updates_<?php echo $i; ?>" value="<?php echo htmlspecialchars($lan_data['whats_new'], ENT_QUOTES, 'UTF-8'); ?>" />

                    <input type="hidden" class="mt-5" name="language_id[]" id="language_id<?php echo $i; ?>" value="<?php echo $lan_data['language_id']; ?>" />
                  </div>
                  <div class="card mb-0 pb-0">
                    <div class="card-header">
                      <a class="card-link text-uppercase" data-toggle="collapse" href="#collapse<?php echo $i; ?>">
                        whats new in <?php echo $lan_data['language_name']; ?>
                      </a>
                    </div>
                    <div id="collapse<?php echo $i; ?>" class="collapse <?php echo ($i == '0') ? "show" : ""; ?>" data-parent="#accordion">
                      <div class="card-body p-0">
                        <div id="commonTextEditor<?php echo $i; ?>"><?php echo $lan_data['whats_new']; ?></div>
                      </div>
                    </div>
                  </div>
                  <?php
                    $i++;
                  }
                  ?>
                  <div class="form-footer text-center">
                    <input type="hidden" value="<?php echo ($patch_id != '') ? $patch_id : ''; ?>" id="patch_id" name="patch_id">
                    <?php if ($patch_id != '') { ?>
                      <input type="hidden" value="editWhatsNew" name="editWhatsNew">
                    <?php } else { ?>
                      <input type="hidden" value="addWhatsNew" name="addWhatsNew">
                    <?php } ?>
                    <button type="submit" id="UpdateBtn" class="btn btn-success"><i class="fa fa-check-square-o"></i><?php echo ($patch_id != '') ? ' Update' : ' Add'; ?></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>


      </div>
    </div>
  </div>
</div>