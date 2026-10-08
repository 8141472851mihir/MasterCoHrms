<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Company Requests </h4>
        
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <a href="addCompany" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add</a>
        </div>
      </div>
    </div>
  <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="default-datatable" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Requested Company Id</th>
                    <th>Company Id</th>
                    <th>Company</th>
                    <th>Requested Date</th>
                    <th>Created Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $i=1;
                    $q = $d->selectRow("society_master_requests.*,society_master.society_id","society_master_requests LEFT JOIN society_master ON society_master.society_id=society_master_requests.society_id_added","request_added_by='$bms_admin_id' $countryAppendQuerySocietySingleReq","order by request_society_id  DESC");
                    while ($data=mysqli_fetch_array($q)) {
                      extract($data);
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                        <td><?php echo 'R_'.$d->short_app_name().'_'.$request_society_id; ?></td>
                        <td>                          
                          <?php
                          if (!empty($society_id)) {
                            echo '' . $d->short_app_name() . '_' . $society_id;
                          } else {
                            echo "";
                          }
                          ?>
                        </td>
                        <td><a href="javascript:void" data-toggle="modal" data-target="#buildingModal" onclick="getAllBuildingData(<?php echo $request_society_id; ?>)"><?php echo $request_society_name; ?></a></td>
                         <td><?php if ($default_time_zone!="Asia/Kolkata") {
                              echo $d->change_timezone($requested_date,$default_time_zone,'Y-m-d h:i A');
                          } else {  echo $requested_date; }  ?></td>
                        <td><?php if ($default_time_zone!="Asia/Kolkata") {
                              echo $d->change_timezone($created_date,$default_time_zone,'Y-m-d h:i A');
                          } else {  echo $created_date; } ; ?></td>
                        <td>
                          <?php if ($request_society_create_status==0) { 
                            echo "Pending";
                          } else if ($request_society_create_status==1) {
                            echo "Created";
                          } else{
                            echo "Rejected";
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


<script type="text/javascript">
  function getAllBuildingData(request_society_id) {
    $.ajax({
      url: "getBuildingDetails.php",
      cache: false,
      type: "POST",
      data: {request_society_id:request_society_id},
      success: function(response){
        $('#buildingData').html(response);
      }
    });
  }
</script>


<div class="modal fade" id="buildingModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="buildingData">
        
      </div>
      
    </div>
  </div>
</div>