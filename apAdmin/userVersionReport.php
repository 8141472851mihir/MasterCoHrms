<?php
extract(array_map("test_input" , $_REQUEST));
error_reporting(0);
$qcountries=$d->selectSpArray("getCountry");
$cIdsArray = explode(",", $countryids);
$sId = $d->sanitizeReportFilterIdAsInt($sId);
$countryId = $d->sanitizeReportFilterIdAsInt($countryId, 101);
$cId = $d->sanitizeReportFilterIdAsInt($cId);
 ?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-4">
        <h4 class="page-title">Users Version Report</h4>
        
      </div>
      <div class="col-sm-8 text-right">
       <?php if (isset($cId) && $cId>0 ) {  ?>
         <form action="companyAnalyticsGetData" method="POST">
           <input type="hidden" name="country_id" value="<?php if(isset($_GET['countryId'])){echo $_GET['countryId']; } ?>">
           <input type="hidden" name="state_id" value="<?php if(isset($_GET['sId'])){echo $_GET['sId'];} ?>">
           <input type="hidden" name="city_id" value="<?php if(isset($_GET['cId'])){echo $_GET['cId'];} ?>">
           <button type="submit" name="publishPost" value="publishPost" class="open-AddBookDialog btn btn-secondary btn-sm"><i class="fa fa-database"></i> Get Bulk Data</button>
         </form>
         <!-- <a data-toggle="modal" onclick="getDataSociety()" data-target="#publishPostModal" class="open-AddBookDialog btn btn-secondary btn-sm" href="#"><i class="fa fa-database"></i> Get Bulk Data</a> -->
       <?php } ?>
     </div>
   </div>
   <!-- End Breadcrumb-->
   <div class="row pt-2 pb-2">
    <div class="col-lg-12">
      <form action="" method="get" accept-charset="utf-8" id="userVersionReportForm">

        <div class="form-group row">
          <label for="country_id" class="col-sm-1 col-form-label"> Country </label>
          <div class="col-sm-3">
            <select type="text" required="" id="country_id" onchange="this.form.submit()" class="form-control single-select" name="countryId">
              <option value="">-- Select --</option>
              <?php 
              for ($ic=0; $ic <count($qcountries) ; $ic++) { 
                if(in_array($qcountries[$ic]['country_id'], $cIdsArray)){
                  ?>
                  <option <?php if( isset($_GET['countryId']) && $qcountries[$ic]['country_id']==$_GET['countryId']) {echo "selected";} ?> value="<?php echo $qcountries[$ic]['country_id'];?>"><?php echo $qcountries[$ic]['name'];?></option>
                <?php } }?>
              </select>
            </div>
            <label for="state_id" class="col-sm-1 col-form-label"> State </label>
            <div class="col-sm-3">
              <?php  if(isset($_GET['sId'])) {
                $countryIdFilter = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId']) : 0;
                $qstates=$d->selectSpArray("getState('$countryIdFilter')");
                
                ?>
                <select type="text" onchange="this.form.submit()"  required="" class="form-control single-select" id="state_id" name="sId">
                  <option value="">Select</option>
                  <?php
                  for ($is=0; $is <count($qstates) ; $is++) { 
                    ?>
                    <option <?php if( isset($_GET['sId']) && $qstates[$is]['state_id']==$_GET['sId']) {echo "selected";} ?> value="<?php echo $qstates[$is]['state_id'];?>"><?php echo $qstates[$is]['name'];?></option>
                  <?php }  ?>
                </select>
              <?php } else { ?>
                <select type="text" onchange="this.form.submit()" required="" class="form-control single-select" id="state_id" name="sId">
                  <option value="">-- Select --</option>
                </select>
              <?php } ?>
            </div>

            <label for="input-101" class="col-sm-1 col-form-label"> City </label>
            <div class="col-sm-3">
              <?php  if(isset($_GET['cId'])) {
                $sIdFilter = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : 0;
                $qcities=$d->selectSpArray("getCity('$sIdFilter')");
                ?>
                <select onchange="this.form.submit()" type="text"  required="" class="form-control single-select" id="city_id" name="cId">
                  <option value="">Select</option>
                  <?php
                  for ($icity=0; $icity <count($qcities) ; $icity++) { 
                    ?>
                    <option <?php if( isset($_GET['cId']) && $qcities[$icity]['city_id']==$_GET['cId']) {echo "selected";} ?> value="<?php echo $qcities[$icity]['city_id'];?>"><?php echo $qcities[$icity]['name'];?></option>
                  <?php }  ?>
                </select>
              <?php } else { ?>
                <select  onchange="this.form.submit()" type="text" required="" class="form-control single-select" name="cId" id="city_id">
                  <option value="">-- Select --</option>
                </select>
              <?php } ?>
            </div>

          </div>
          <div class="form-group row">
            <label for="version_type" class="col-sm-1 col-form-label"> Version Type </label>
            <div class="col-sm-2">
              <select type="text" required="" id="version_type" onchange="this.form.submit()" class="form-control single-select" name="version_type">
                <option <?php if(!isset($_GET['version_type']) || '0'==$_GET['version_type']) {echo "selected";} ?> value="0">App version code</option>
                <option <?php if(isset($_GET['version_type']) && '1'==$_GET['version_type']) {echo "selected";} ?> value="1">App mobile os</option>
              </select>
            </div>
            <label for="Device_type" class="col-sm-1 col-form-label"> Device Type </label>
            <div class="col-sm-2">
              <select type="text" required="" id="device_type" onchange="this.form.submit()" class="form-control single-select" name="device_type">
                <option <?php if(!isset($_GET['device_type']) || 'All' ==$_GET['device_type']) {echo "selected";} ?> value="All">All</option>
                <option <?php if( 'android' ==$_GET['device_type']) {echo "selected";} ?> value="android">Android</option>
                <option <?php if('ios' ==$_GET['device_type']) {echo "selected";} ?> value="ios">IOS</option>
                
              </select>
            </div>

            <label for="version_from" class="col-sm-1 col-form-label"> Version From </label>
            <div class="col-sm-2">
             <input type="text" name="version_from"  onchange="this.form.submit()" value="<?php echo $version_from; ?>"  class="form-control">
           </div>            
           <label for="version_to" class="col-sm-1 col-form-label"> to </label>
           <div class="col-sm-2">
             <input type="text" name="version_to"  onchange="this.form.submit()" value="<?php echo $version_to; ?>"  class="form-control">
           </div>
         </div>
           
        </form>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <div class="card">

        <div class="card-body">
          <div class="table-responsive">
            <table id="userVersionReportTable" class="table table-bordered">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Company</th>
                  <th>users count</th>
                  <th>device Type</th>
                  <th>version type</th>
                  <?php if (isset($version_type) && $version_type=="1") { ?>
                   <th>app mobile os</th>
                 <?php }else{ ?>
                  <th>app version code</th>
                <?php }?>
                <th>Updated Date</th>
              </tr>
            </thead>
            <tfoot class="bottom-footer">
              <tr>
                <th class="no-search-box"></th>
                <th></th>
                <th class="find-count"></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </tfoot>
            <tbody>
            </tbody>
            <tfoot class="top-footer">
              <tr>
                <th class="no-search-box"></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </tfoot>

          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script>
(function initUserVersionReportTable() {
  if (typeof jQuery === 'undefined' || !jQuery.fn.DataTable) {
    setTimeout(initUserVersionReportTable, 80);
    return;
  }
  jQuery(function() {
  if (jQuery.fn.DataTable.isDataTable('#userVersionReportTable')) {
    return;
  }
  var userVersionTable = jQuery('#userVersionReportTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: 'ajax/userVersionReportTable.php',
      type: 'POST',
      data: function(d) {
        var f = jQuery('#userVersionReportForm');
        d.countryId = f.find('[name=countryId]').val() || '';
        d.sId = f.find('[name=sId]').val() || '';
        d.cId = f.find('[name=cId]').val() || '';
        d.version_type = f.find('[name=version_type]').val() || '0';
        d.device_type = f.find('[name=device_type]').val() || 'All';
        d.version_from = f.find('[name=version_from]').val() || '';
        d.version_to = f.find('[name=version_to]').val() || '';
      }
    },
    pageLength: 50,
    lengthMenu: [[25, 50, 100, 200, 500, -1], [25, 50, 100, 200, 500, "All"]],
    order: [[2, 'desc']],
    columns: [
      { data: 0 },
      { data: 1 },
      { data: 2 },
      { data: 3 },
      { data: 4 },
      { data: 5 },
      { data: 6 }
    ],
    dom: 'Blfrtip',
    buttons: ['copy', 'excel', 'pdf', 'csv'],
    footerCallback: function(row, data, start, end, display) {
      var api = this.api();
      var $row = jQuery(row);
      $row.find('th').eq(0).html('Total');
      $row.find('.find-count').each(function() {
        var colIdx = jQuery(this).index();
        var colData = api.column(colIdx, { page: 'current' }).data();
        var sum = colData.reduce(function(a, b) {
          var n = parseFloat(String(b).replace(/[^0-9.-]/g, '')) || 0;
          return (parseFloat(a) || 0) + n;
        }, 0);
        jQuery(this).html(sum);
      });
    },
    initComplete: function() {
      var api = this.api();
      var $table = jQuery('#userVersionReportTable');
      // Populate top-footer with search inputs (except no-search-box)
      $table.find('tfoot.top-footer th').each(function(i) {
        var $th = jQuery(this);
        if ($th.hasClass('no-search-box')) return;
        var title = $table.find('thead th').eq(i).text() || ('Col ' + i);
        $th.html('<input class="form-control tableSearch" type="text" placeholder="Search ' + title + '" />');
      });
      // Wire footer search to column search (server-side)
      $table.find('tfoot.top-footer .tableSearch').on('keyup change', function() {
        var $input = jQuery(this);
        var colIdx = $input.closest('th').index();
        api.column(colIdx).search($input.val()).draw();
      });
      // Move top-footer row into thead (below header)
      var topFooterRow = $table.find('tfoot.top-footer tr');
      topFooterRow.find('th').css('padding', 8);
      $table.find('thead').append(topFooterRow);
      $table.find('tfoot.top-footer').remove();
      // Ensure bottom-footer is at end and styled
      var bottomFooter = $table.find('tfoot.bottom-footer');
      bottomFooter.find('th').css('padding', 8);
      $table.append(bottomFooter);
    }
  });
  });
})();
</script>

<div class="modal fade" id="publishPostModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Get <?php echo $xml->string->society; ?> Data</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="publishSocetyDataFrm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
          <input type="hidden" name="countryId" id="countryId" value="<?php echo $countryId;?>">
          <input type="hidden" name="sId" id="sId" value="<?php echo $sId;?>">
          <input type="hidden" name="cId" id="cId" value="<?php echo $cId;?>">
          <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" />
          <input type="hidden" name="publishPost" value="publishPost" />
          <div id="sosa_detail">
          </div>
          <div id="chkError" class=""></div>
          <div class="form-footer text-center">
            <button type="submit" name="publishPost" value="publishPost" class="btn btn-sm btn-success publishPost"><i class="fa fa-check-square-o"></i> Get Data</button>
          </div>
        </form> 
      </div>
    </div>
  </div>
</div>
