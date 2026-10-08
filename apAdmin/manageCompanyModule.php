<?php
extract($_GET);
if (isset($_GET['sId'])) {
  $sId = $d->sanitizeReportFilterIdAsInt($sId);
}
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12 col-sm-6 col-md-3">
        <h4 class="page-title"> Company Modules </h4>
      </div>
      <div class="col-12 col-sm-6 col-md-3 pb-2">
        <form method="get">
          <select class="form-control single-select" name="sId" onchange="this.form.submit()">
            <option value=""> Select Company </option>
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
      </div>
    </div>
    <!-- Table Section -->
    <div class="row" >
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
                      <th style="max-width: 120px !important;">Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $i = 1;
                    $q = $d->select("module_master,company_module_master,society_master", "society_master.society_id=company_module_master.society_id AND module_master.module_status=0  AND company_module_master.module_id=module_master.module_id AND company_module_master.society_id='$sId' $countryAppendQuerySociety", "ORDER BY company_module_master.module_id ASC");
                    while ($data = mysqli_fetch_array($q)) {
                      extract($data);
                      
                    ?>
                      <tr id="<?php echo $company_module_id . "-" . $sId; ?>">
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $module_name; ?></td>
                        <td>
                          <?php
                            $buttonClass = ($active_status == "0") ? 'btn-success-new' : 'btn-danger';
                            $buttonCondition = ($active_status == "0") ? 'Active' : 'Deactive';
                            $status = ($active_status == "0") ? ',moduleDeactiveSociety' : 'moduleActiveSociety';
                            $newStatus = ($active_status == "0") ? 'moduleActiveSociety' : ',moduleDeactiveSociety';
                            $newStatusVal = ($active_status == "0") ? '1' : '0';
                            $statusValue = ($active_status == "0") ? '0' : '1';
                          ?>
                       
                          <input type="button" class="btn btn-sm pl-1 pr-1 w-100 <?php echo $buttonClass ?>" id="<?php echo 'module_id_'.$company_module_id;?>"  onclick ="changeStatusNew('<?php echo $company_module_id; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'module_id_'.$company_module_id; ?>');" data-size="small" value="<?php echo $buttonCondition ?>"/>
                        </td>
                        <td>
                          <form method="POST" class="form-horizontal" action="controller/societyAppmenuController.php">
                            <input type="hidden" name="sId" value="<?php echo $_GET['sId'] ?>" />
                            <input type="hidden" name="menuName" value="<?php echo $menu_title; ?>" />
                            <input type="hidden" name="company_module_id" value="<?php echo $data['company_module_id'] ?>" />
                            <button class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></button>
                          </form>
                        </td>
                      </tr>
                    <?php  } ?>
                  </tbody>
                </table>
              </div>
            <?php } else { echo "Please Select Company"; } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>