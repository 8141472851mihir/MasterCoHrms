<?php
extract($_POST);
error_reporting(0);
$sId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
$sId = $sId > 0 ? $sId : '';
$dId = isset($_GET['dId']) ? $d->sanitizeReportFilterIdAsInt($_GET['dId']) : 0;
$dId = $dId > 0 ? $dId : '';
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-3">
        <h4 class="page-title">Company CRM Data</h4>
      </div>
      <div class="col-lg-9 ">
        <form action="" method="get" accept-charset="utf-8">
          <div class="col-lg-6 float-left">
            <select type="text" required="" id="sId" onchange="this.form.submit()" class="form-control single-select" name="sId">
              <option value="0">-- Select --</option>
              <?php
              $qc = $d->select("server_master", "");
              while ($cData = mysqli_fetch_array($qc)) {
              ?>
                <option <?php if (isset($sId) && $cData['server_id'] == $sId) {
                          echo "selected";
                        } ?> value="<?php echo $cData['server_id']; ?>"><?php echo $cData['server_name']; ?> (<?php echo $cData['server_ip']; ?>)</option>
              <?php } ?>
            </select>
          </div>
          <div class="col-lg-6 float-right">
            <select id="dId" name="dId" class="form-control single-select" required onchange="this.form.submit()">
              <option value="0">-- Select --</option>
              <?php
              if (isset($sId) && $sId != 0) {
                $domain_data = $d->select("domain_master", "server_id = '$sId'");
                while ($dData = mysqli_fetch_array($domain_data)) {
                  $selected = (isset($dId) && $dData['domain_id'] == $dId) ? "selected" : "";
                  echo "<option value='{$dData['domain_id']}' {$selected}>{$dData['domain_name']}</option>";
                }
              }
              ?>
            </select>
          </div>
        </form>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="syncCrmDataFrm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
              <input type="hidden" name="countryId" id="countryId" value="<?php echo $country_id; ?>">
              <input type="hidden" name="sId" id="sId" value="<?php echo $state_id; ?>">
              <input type="hidden" name="cId" id="cId" value="<?php echo $city_id; ?>">
              <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" />
              <?php
              $today = date("Y-m-d");
              if (isset($sId) && $sId > 0) {
                $appendSeverQuery = " AND domain_master.server_id='$sId'";
              }
              if (isset($dId) && $dId > 0 && $dId != '') {
                $appendDomainQuery = " AND domain_master.domain_id='$dId'";
              }
              ?>
              <!-- <input type="hidden" name="banner_url" id="banner_url" value="<?php echo $kbgPhoto ?>"> -->
              <div class="form-group row">

                <div class="col-sm-6"> <b>Company List</b>
                  <?php
                  $companySrId = 1;
                  $query = $d->selectRow("society_master.*,server_master.server_name,server_master.server_ip,domain_master.domain_name", "society_master,domain_master,server_master ", "society_master.domain_id=domain_master.domain_id AND server_master.server_id=domain_master.server_id AND society_master.crm_created='1' $appendSeverQuery $appendDomainQuery", "order by domain_master.domain_name  asc");
                  if (mysqli_num_rows($query) > 0) {
                  ?>
                    <label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
                      <input type="checkbox" class="chk_boxes" value="0" name="society_id[]">
                      <span class="custom-control-description">Check All</span>
                    </label>


                  <?php } else { ?>
                    <br>
                    <span class="text-danger"><b>Data Sync Company </b></span>
                  <?php }
                  while ($society_master_data = mysqli_fetch_array($query)) {

                  ?>

                    <label class="custom-control custom-checkbox error_color" style="padding: 5px !important;">
                      <input type="checkbox" class="pagePrivilege" value="<?php echo $society_master_data['society_id']; ?>" name="society_id[]">

                      <span class="custom-control-description"><?php echo $companySrId++; ?>. <?php echo $d->short_app_name() . '_' . $society_master_data['society_id']; ?>  <?php echo $society_master_data['society_name']; ?> (<?php echo $society_master_data['city_name']; ?>) <?php echo $society_master_data['server_ip']; ?></span>
                      <span id="result_<?php echo $society_master_data['society_id']; ?>"></span>
                      <input type="hidden" id="val_<?php echo $society_master_data['society_id']; ?>" value="1" />
                      <?php $res = $society_master_data['society_id'];

                      if (!empty($res)) { ?>
                        <!-- <span class="text-danger"> - <?php echo $res; ?></span> -->
                      <?php } ?>
                    </label>
                  <?php  } ?>
                </div>
              </div>

              <div class="form-footer text-center">
                <button type="submit" name="syncSocietyData" value="syncSocietyData" class="btn btn-sm btn-success syncSocietyData"><i class="fa fa-check-square-o"></i> Sync Data</button>
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
</script>