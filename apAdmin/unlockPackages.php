<?php $videoguide=TRUE ?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Gatekeeper App Unlock Packages</h4>
       
      </div>
       <div class="col-sm-3">
       <div class="btn-group float-sm-right">
        <a href="#" class="btn btn-primary waves-effect btn-sm waves-light" data-toggle="modal" data-target="#addTenents" ><i class="fa fa-plus mr-1"></i> Add New</a>
          <a href="javascript:void(0)" onclick="DeleteAll('deletePackage');" class="btn  btn-sm btn-danger pull-right"><i class="fa fa-trash-o fa-lg"></i> Delete </a>

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
                    <th class="deleteTh">#</th>
                    <th>#</th>
                    <th>Package /Activity</th>
                    <th>Type</th>
                    <th>For</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $i=1;
                    $q = $d->select("gatekeeper_app_access","","order by  app_id  DESC");
                    $accessRows = [];
                    $societyIds = [];
                    while ($data=mysqli_fetch_array($q)) {
                      $accessRows[] = $data;
                      if ((int)$data['society_id'] != 0) {
                        $societyIds[] = (int)$data['society_id'];
                      }
                    }

                    $societyNameById = [];
                    if (!empty($societyIds)) {
                      $societyIdsIn = implode(',', array_map('intval', array_unique($societyIds)));
                      $qs = $d->selectRow("society_id,society_name", "society_master", "society_id IN ($societyIdsIn) $countryAppendQuerySociety ");
                      while ($sData = mysqli_fetch_array($qs)) {
                        $societyNameById[(int)$sData['society_id']] = $sData['society_name'];
                      }
                    }

                    foreach ($accessRows as $data) {
                      extract($data);
                  ?>
                    <tr>
                       <td class='text-center'>
                            <input type="checkbox" class="multiDelteCheckbox"  value="<?php echo $data['app_id']; ?>">
                          </td>
                      <td><?php echo $i++; ?></td>
                        <td><?php echo $app_package_name; ?></td>
                        <td><?php if($is_package_all==0) {
                          echo "Activity";
                        } else {
                          echo "Package";
                        } ?></td>
                       
                       <td>
                         <?php if($society_id==0) {
                          echo "All";
                         } else {
                          echo $societyNameById[(int)$society_id] ?? '';
                         } ?>
                       </td>
                    </tr>
                  <?php } ?>
                </tbody>
                
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="addTenents">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Pcakge</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addPackage" method="post" action="controller/gatekeeperController.php" enctype="multipart/form-data" novalidate="novalidate">
          
          <div class="form-group row">
            <label for="app_package_name" class="col-sm-2 col-form-label">Package Name <span class="required">*</span> </label>
            <div class="col-sm-10">
              <input type="text" maxlength="250" class="form-control" name="app_package_name" id="app_package_name" required="">
            </div>
          </div>
          <div class="form-group row">
            <label for="app_package_name" class="col-sm-2 col-form-label">Type <span class="required">*</span> </label>
            <div class="col-sm-10">
              <select class="form-control" name="is_package_all" id="is_package_all" required="">
                <option value="0">Activity</option>
                <option value="1">All App (Package)</option>
              </select>
            </div>
          </div>
           <div class="form-group row">
            <label for="app_package_name" class="col-sm-2 col-form-label">All/ Company <span class="required">*</span> </label>
            <div class="col-sm-10">
              <select class="form-control single-select" name="societyId" id="societyId" required="">
                <option value="0">All</option>
                <?php $qss=$d->select("society_master","society_status = 0");
                   while ($sData=mysqli_fetch_array($qss)) {
                ?>
                <option value="<?php echo $sData['society_id']; ?>"><?php echo $sData['society_name']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          

          <div class="form-footer text-center">
            <input type="hidden" name="addPackage" value="addPackage">
            <button type="submit" class="btn btn-success" name="videoUpload" id="videoUpload"><i class="fa fa-check-square-o"></i> Add</button>
           
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
