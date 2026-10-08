<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">New Companies Request Report</h4>
            </div>
        </div>
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
                                        <th>Server</th>
                                        <th>Requested Date</th>
                                        <th>Requested By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $q = $d->selectRow("society_master_requests.*,bms_admin_master.admin_name,cities.name as city_name", "cities,society_master_requests LEFT JOIN bms_admin_master ON bms_admin_master.admin_id=society_master_requests.request_added_by", "cities.city_id=society_master_requests.request_city_id  AND society_master_requests.request_society_create_status=0 $countryAppendQuerySocietySingleReq", "order by request_society_id  DESC");
                                    $requestRows = [];
                                    while ($data = mysqli_fetch_array($q)) {
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
                                        $domainTemp = "https://" . $charge;

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
                                                <input type="checkbox" class="multiDelteCheckbox" value="<?php echo $data['request_society_id']; ?>">
                                            </td>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo 'R_' . $d->short_app_name() . '_' . $request_society_id; ?></td>
                                            <td><a href="javascript:void" data-toggle="modal" data-target="#buildingModal" onclick="getAllBuildingData(<?php echo $request_society_id; ?>)"><?php echo $request_society_name; ?></a></td>
                                            <td><?php echo $city_name; ?></td>
                                            <td>
                                                <?php echo $domainData['server_name'] . '-' . $domainData['server_ip'];
                                                ?>
                                            </td>
                                            <td><?php if ($default_time_zone != "Asia/Kolkata") {
                                                    echo $d->change_timezone($requested_date, $default_time_zone, 'Y-m-d h:i A');
                                                } else {
                                                    echo $requested_date;
                                                }  ?></td>
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