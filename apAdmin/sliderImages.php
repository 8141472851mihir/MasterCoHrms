<?php
extract($_REQUEST);
$societyQuery = $d->select("society_master", "society_status=0" . ($countryAppendQuerySocietySingle ?? ""), "ORDER BY society_name ASC");
$selectedSocietyId = isset($society_id) && $society_id !== '' ? intval($society_id) : null;
$companySliders = [];
if ($selectedSocietyId) {
  $defaultQ = $d->select("app_slider_master", "slider_type=1 AND slider_status=0", "ORDER BY app_slider_id ASC");
  while ($row = mysqli_fetch_assoc($defaultQ)) {
    $row['source'] = 'Default';
    $companySliders[] = $row;
  }
  $companyQ = $d->selectRow("asm.app_slider_id, asm.slider_image_name, asm.slider_type", "app_slider_master asm, app_common_slider_master acsm", "asm.app_slider_id = acsm.app_slider_id AND acsm.society_id = '$selectedSocietyId' AND acsm.status = 0", "ORDER BY asm.app_slider_id ASC");
  while ($row = mysqli_fetch_assoc($companyQ)) {
    $row['source'] = 'Company';
    $companySliders[] = $row;
  }
}
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-6">
        <h4 class="page-title">App Banner</h4>

      </div>
      <div class="col-sm-6 text-right">
        <a href="javascript:void(0)" onclick="DeleteAll('deleteAppBanner');" class="btn  btn-sm btn-danger  mr-1"><i class="fa fa-trash-o fa-lg"></i> Delete </a>
      </div>
    </div>


    <!--End Row-->
    <!-- Msanage Slider -->

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>


                    <th>#</th>
                    <th>#</th>
                    <th>Slider</th>
                    <th>Company</th>
                    <th>Action</th>

                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->select("app_slider_master", "");
                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);
                    // echo "<pre>";
                    // print_r($data);
                    $cnt = $d->count_data_direct("app_slider_id", "app_common_slider_master,society_master", "app_common_slider_master.app_slider_id='$app_slider_id' AND app_common_slider_master.society_id=society_master.society_id");
                  ?>
                    <tr>

                      <td><?php echo $i++; ?></td>
                      <td class='text-center' onclick="event.stopPropagation();">
                        <?php if ($cnt == 0 && $slider_type != 1) { ?>
                          <input type="checkbox" class="multiDelteCheckbox" value="<?php echo $data['app_slider_id']; ?>">
                        <?php } ?>
                      </td>
                      <?php $slider = $base_url . 'img/sliders/' . $slider_image_name; ?>
                      <td><a data-fancybox="images" data-caption="Photo Name : <?php echo $slider_image_name; ?>" href="<?php echo $slider; ?>" target="_blank"><img class="lazyload" src='../img/ajax-loader.gif' width="100" data-src="<?php echo $slider; ?>"></a></td>
                      <td>
                        <?php if ($slider_type != 1) {
                          echo $cnt; ?>

                        <?php } else {
                          echo "Default";
                        } ?>

                      </td>
                      <td>
                        <?php if ($slider_type != 1) { ?>
                          <a name="manageSp" value="manageSp" href="commonSliderSetting?id=<?php echo $app_slider_id; ?>" class="btn btn-sm btn-primary">Manage </a>
                        <?php } ?>
                        <a name="editSlider" value="editSlider" href="editSliderImage?id=<?php echo $app_slider_id; ?>" class="btn btn-sm btn-warning">Edit </a>
                        <?php if ($slider_type != 1) {
                          if ($cnt == 0) { ?>
                            <a href="javascript:void();" onclick="deleteSliderImage(<?= $app_slider_id ?>);" class="btn btn-sm btn-danger shadow-primary">Delete</a>
                          <?php } ?>
                        <?php } ?>
                      </td>



                    </tr>

                  <?php } ?>
                </tbody>

              </table>
            </div>
          </div>
        </div>
      </div>
    </div><!-- End Row-->

    <div class="row mt-4">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header"><i class="fa fa-building"></i> Sliders by Company</div>
          <div class="card-body">
            <form id="companySliderFilterForm" method="get" action="sliderImages">
              <div class="form-group row mb-3">
                <label class="col-sm-2 col-form-label">Select Company</label>
                <div class="col-sm-6">
                  <select name="society_id" id="companySliderFilter" class="form-control single-select">
                    <option value="">-- Select Company --</option>
                    <?php
                    mysqli_data_seek($societyQuery, 0);
                    while ($soc = mysqli_fetch_array($societyQuery)) {
                      $sel = ($selectedSocietyId && $soc['society_id'] == $selectedSocietyId) ? ' selected' : '';
                      ?>
                      <option value="<?= $soc['society_id'] ?>"<?= $sel ?>><?= htmlspecialchars($soc['society_name'] . ' (' . ($soc['city_name'] ?? '') . ')') ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </form>
            <div class="table-responsive">
              <table class="table table-bordered table-sm" id="companySlidersTable">
                <thead class="thead-light">
                  <tr>
                    <th>#</th>
                    <th>Slider</th>
                    <th>Name</th>
                    <th>Source</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="companySlidersTableBody">
                  <?php if (count($companySliders) > 0) {
                    $i = 1;
                    $imgBase = $base_url . 'img/sliders/';
                    foreach ($companySliders as $s) {
                      $badgeClass = $s['source'] === 'Default' ? 'badge-info' : 'badge-primary';
                      ?>
                      <tr>
                        <td><?= $i++ ?></td>
                        <td><a href="<?= $imgBase . htmlspecialchars($s['slider_image_name'] ?? '') ?>" data-fancybox="company-sliders"><img src="<?= $imgBase . ($s['slider_image_name'] ?? '') ?>" width="80" height="45" class="img-thumbnail" onerror="this.src='../img/dummy-image.jpg'"></a></td>
                        <td><?= htmlspecialchars($s['slider_image_name'] ?? '') ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($s['source']) ?></span></td>
                        <td>
                          <?php if ($s['source'] === 'Company' && $selectedSocietyId) { ?>
                            <form action="controller/sliderController.php" method="post" class="d-inline-block" onsubmit="return confirm('Remove this slider from the company?');">
                              <input type="hidden" name="id" value="<?= $s['app_slider_id'] ?>">
                              <input type="hidden" name="society_id_delete_from_slider" value="<?= $selectedSocietyId ?>">
                              <input type="hidden" name="return_to_slider_images" value="1">
                              <button type="submit" class="btn btn-sm btn-danger" title="Remove from company"><i class="fa fa-trash-o"></i> Remove</button>
                            </form>
                          <?php } else { ?>
                            <span class="text-muted">—</span>
                          <?php } ?>
                        </td>
                      </tr>
                    <?php }
                  } else { ?>
                    <tr>
                      <td colspan="5" class="text-center text-muted"><?= $selectedSocietyId ? 'No sliders found for this company' : 'Select a company to view sliders' ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div><!--End Row-->
</div>
</div>
</div>
<!-- End container-fluid-->
</div><!--End content-wrapper-->
<script src="assets/js/jquery.min.js"></script>
<script>
$(document).ready(function() {
  $('#companySliderFilter').on('change', function() {
    $('#companySliderFilterForm').submit();
  });
});
</script>
<!--Start Back To Top Button-->