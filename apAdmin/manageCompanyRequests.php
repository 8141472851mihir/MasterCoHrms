<?php 
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

?>

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
          <?php if ($role_id==1) { ?>
            <a href="javascript:void(0)" onclick="DeleteAll('deleteRequestSociety');" class="btn  btn-sm btn-danger pull-right"><i class="fa fa-trash-o fa-lg"></i> Delete </a>
          <?php } ?>
        </div>
      </div>
    </div>
  <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="spTable" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>#</th>
                    <th>Company Id</th>
                    <th>Company</th>
                    <th>City</th>
                    <th>Action</th>
                    <th>Server</th>
                    <th>Requested Date</th>
                    <th>Requested By</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $i=1;
                    $q = $d->selectRow("society_master_requests.*,bms_admin_master.admin_name,cities.name as city_name","cities,society_master_requests LEFT JOIN bms_admin_master ON bms_admin_master.admin_id=society_master_requests.request_added_by","cities.city_id=society_master_requests.request_city_id  AND society_master_requests.request_society_create_status=0 $countryAppendQuerySocietySingleReq","order by request_society_id  DESC");
                    $requestRows = [];
                    while ($data=mysqli_fetch_array($q)) {
                      $requestRows[] = $data;
                    }

                    $allDomains = [];
                    $qdomainAll = $d->selectRow(
                      "domain_master.domain_id,server_master.server_name,server_master.server_ip,domain_master.domain_name",
                      "domain_master LEFT JOIN server_master ON server_master.server_id=domain_master.server_id",
                      "1=1"
                    );
                    while ($dom = mysqli_fetch_array($qdomainAll)) {
                      $allDomains[] = $dom;
                    }

                    foreach ($requestRows as $data) {
                      extract($data);
                      $charge = explode('/', $data['request_sub_domain']);
                      $charge = $charge[2] ?? ''; //assuming that the url starts with http:// or https://
                      $domainTemp = "https://".$charge;

                      $domainData = ['domain_id' => null, 'server_name' => '', 'server_ip' => '', 'domain_name' => ''];
                      if ($domainTemp !== 'https://') {
                        foreach ($allDomains as $dom) {
                          if (strpos((string)$dom['domain_name'], $domainTemp) !== false) {
                            $domainData = $dom;
                            break;
                          }
                        }
                      }
                      $domain_id = $domainData['domain_id'];
                  ?>
                    <tr>
                      <td class='text-center'>
                        <input type="checkbox" class="multiDelteCheckbox"  value="<?php echo $data['request_society_id']; ?>">
                      </td>
                      <td><?php echo $i++; ?></td>
                        <td><?php echo 'R_'.$d->short_app_name().'_'.$request_society_id; ?></td>
                        <td><a href="javascript:void" data-toggle="modal" data-target="#buildingModal" onclick="getAllBuildingData(<?php echo $request_society_id; ?>)"><?php echo $request_society_name; ?></a></td>
                        <td><?php echo $city_name; ?></td>
                        
                        <td>
                          <?php if ($request_society_create_status==0) { ?>
                            <form action="addCompany" method="POST" class="d-inline-block">
                              <input type="hidden" name="request_society_id_edit" value="<?php echo $request_society_id ?>">
                              <button class="btn btn-sm btn-info"><i class="fa fa-pencil"></i></button>
                            </form> 
                            <a href="javascript:void(0)" onclick="rejectModal(<?php echo $request_society_id; ?>,<?php echo $request_added_by ?>)" name="rejected" class="btn btn-sm btn-warning d-inline-block" data-toggle="modal" data-target="#rejectedSoc" title="Rejected?"><i class="fa fa-times"></i></a>
                            <form class="d-inline-block" method="POST" action="createCompany">
                              <input type="hidden" name="created_society" value="<?php echo $request_society_id; ?>">
                              <button type="submit" name="created" class="btn btn-sm btn-success " data-toggle="tooltip" title="Created?"><i class="fa fa-check"></i></button>
                            </form>
                          <?php } else if ($request_society_create_status==1) {
                            echo "Created";
                          } else{
                            echo "Rejected";
                          } ?>
                        </td>
                        <td>
                          <?php echo $domainData['server_name'].'-'.$domainData['server_ip']; 
                          ?>
                        </td>
                        <td><?php if ($default_time_zone!="Asia/Kolkata") {
                              echo $d->change_timezone($requested_date,$default_time_zone,'Y-m-d h:i A');
                          } else {  echo $requested_date; }  ?></td>
                        <td><?php echo $admin_name; ?></td>

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

<script type="text/javascript">
  function rejectModal(request_society_id,admin_id) {
    $('#reject_id').val(request_society_id);
    $('#request_added_by').val(admin_id);
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


<div class="modal fade" id="rejectedSoc">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Reject Reason</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="rejectSocForm" method="POST" action="controller/societyRequestController.php">
          <div class="row form-group">
            <input type="hidden" name="rejected_society" id="reject_id">
            <input type="hidden" name="request_added_by" id="request_added_by">
            <label for="input-10" class="col-sm-2 col-form-label">Reason <span class="required">*</span></label>
            <div class="col-sm-10">
              <textarea rows="2" name="soc_reject_reason" class="form-control"></textarea>
            </div>
          </div>
          <div class="form-footer text-center">
            <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>