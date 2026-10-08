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
          <a href="javascript:void(0)" onclick="DeleteAll('deletePackageReq');" class="btn  btn-sm btn-danger pull-right"><i class="fa fa-trash-o fa-lg"></i> Delete </a>

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
                    <th>Action</th>
                    <th>Package Name</th>
                    <th>Society</th>
                    <th>Mobile</th>
                    <th>Device</th>
                    <th>Name</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $i=1;
                    $q = $d->select("gatekeeper_app_access_request,society_master","gatekeeper_app_access_request.society_id=society_master.society_id $countryAppendQuerySociety","order by gatekeeper_app_access_request.requestId DESC");
                    while ($data=mysqli_fetch_array($q)) {
                      extract($data);
                  ?>
                    <tr>
                       <td class='text-center'>
                            <input type="checkbox" class="multiDelteCheckbox"  value="<?php echo $data['requestId']; ?>">
                        </td>
                      <td><?php echo $i++; ?></td>
                      <td>
                        <form id="addPackage" method="post" action="controller/gatekeeperController.php" enctype="multipart/form-data" novalidate="novalidate">
                             <input value="<?php echo $packClass; ?>" type="hidden" maxlength="250" class="form-control" name="app_package_name_r" id="app_package_name" required="">
                            
                            <input type="hidden" name="addPackageReq" value="addPackageReq">
                            <input type="hidden" name="requestId"  value="<?php echo $data['requestId']; ?>" >
                            <button type="submit" class="btn btn-sm form-btn btn-success" name="" id=""><i class="fa fa-check-square-o"></i> Add</button>
                        </form>
                      </td>
                        <td><?php echo $packClass; ?></td>
                        <td><?php echo $society_name; ?></td>
                        <td><?php echo $emp_mobile; ?></td>
                        <td><?php echo $brand; ?></td>
                        <td><?php echo $emp_name; ?></td>
                        <td><?php echo $created_date; ?></td>
                       
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
          
          <div class="form-footer text-center">
            <input type="hidden" name="addPackage" value="addPackage">
            <button type="submit" class="btn btn-success" name="videoUpload" id="videoUpload"><i class="fa fa-check-square-o"></i> Add</button>
           
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
