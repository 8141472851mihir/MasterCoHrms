<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-3">
                <h4 class="page-title">Pending Companies Report</h4>
            </div>
        </div>
        <!-- End Breadcrumb-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Company Id</th>
                                        <th>Company</th>
                                        <th>City</th>
                                        <th>Server</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $q = $d->select("society_master", "created_on_society_server=0 $countryAppendQuerySocietySingle", "order by society_id  DESC");
                                    $pendingRows = [];
                                    while ($data = mysqli_fetch_array($q)) {
                                        $pendingRows[] = $data;
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

                                    foreach ($pendingRows as $data) {
                                        extract($data);
                                        $charge = explode('/', $data['sub_domain']);
                                        $charge = $charge[2] ?? '';
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
                                            <td><?php echo $i++; ?></td>
                                            <td><span style="display: none;"><?php echo $society_id; ?></span><?php echo '' . $d->short_app_name() . '_' . $society_id; ?></td>
                                            <td><a href="javascript:void" data-toggle="modal" data-target="#buildingModal" onclick="getAllBuildingData(<?php echo $society_id; ?>)"><?php echo $society_name; ?></a></td>
                                            <!-- <td><?php echo $society_name; ?></td> -->
                                            <td><?php echo $city_name; ?></td>
                                            <td>
                                                <?php echo $domainData['server_name'] . '-' . $domainData['server_ip'];
                                                ?>
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
<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
    function getAllBuildingData(request_society_id) {
        $.ajax({
            url: "getBuildingDetails.php",
            cache: false,
            type: "POST",
            data: {
                request_society_id: request_society_id,
                csrf: csrf,
                is_pending: true
            },
            success: function(response) {
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