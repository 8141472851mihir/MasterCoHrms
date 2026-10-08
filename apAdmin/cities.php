<?php
extract($_REQUEST);
error_reporting(0);
$sId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : (isset($sId) ? $d->sanitizeReportFilterIdAsInt($sId) : 0);
$countryId = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId'], 101) : (isset($countryId) ? $d->sanitizeReportFilterIdAsInt($countryId, 101) : 101);
?>

<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Cities</h4>
      </div>
       <div class="col-sm-3">
       <div class="btn-group float-sm-right">
       
      </div>
     </div>
    </div>
  <!-- End Breadcrumb-->
   <div class="row pt-2 pb-2">
      <div class="col-lg-12">
        <form action="" method="get" accept-charset="utf-8">

          <div class="form-group row">
            <label for="state_id" class="col-sm-1 col-form-label"> State </label>
            <div class="col-sm-3">
                <select type="text" onchange="this.form.submit()"  required="" class="form-control single-select" id="state_id" name="sId">
                  <option value=""> All</option>
                  <?php
                   $qs=$d->select("states","country_id=101");
                  while ($sData=mysqli_fetch_array($qs)) {
                    ?>
                    <option <?php if( isset($_GET['sId']) && $sData['state_id']==$_GET['sId']) {echo "selected";} ?> value="<?php echo $sData['state_id'];?>"><?php echo $sData['name'];?></option>
                  <?php }  ?>
                </select>
            </div>
           
          </div>
        </form>
      </div>
    </div>
 <?php  if (isset($sId) && filter_var($sId, FILTER_VALIDATE_INT) == true  ) {  ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>City</th>
                    <th>Recommended Domain</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $i=1;

                    if (isset($sId) && $sId>0) {
                      $appendStateQuery = " AND state_id='$sId'";
                    }

          
                    $q = $d->selectRow("cities.*, domain_master.domain_name", "cities LEFT JOIN domain_master ON cities.domain_id = domain_master.domain_id", "cities.country_id=101 $appendStateQuery", "order by cities.flag DESC, cities.name ASC");
                    while ($data=mysqli_fetch_array($q)) {
                      extract($data);
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                        <td><?php echo $name; ?></td>
                        <td><?=isset($domain_name) && $domain_name ? $domain_name : ''?></td>
                        <td class="text-center">
                          <!-- jainish start-------------- -->
                          <?php
                            $buttonClass = ($flag == "1") ? 'btn-success-new' : 'btn-danger';
                            $buttonCondition = ($flag == "1") ? 'Active' : 'Deactive';
                            $status = ($flag == "1") ? 'cityDeactive' : 'cityActive';
                            $newStatus = ($flag == "1") ? 'cityActive' : 'cityDeactive';
                            $newStatusVal = ($flag == "1") ? '1' : '0';
                            $statusValue = ($flag == "1") ? '0' : '1';
                          ?>
                       
                          <input type="button" class="btn btn-sm pl-1 pr-1 w-50 <?php echo $buttonClass ?>" id="<?php echo 'city_id_'.$city_id;?>"  onclick ="changeStatusNew('<?php echo $city_id; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'city_id_'.$city_id; ?>');" data-size="small" value="<?php echo $buttonCondition ?>"/>
                          <!-- jainish end ------------------ -->
                        </td>
                        <td class="text-center">
                          <button data-toggle="modal" data-target="#updateCityDomain" onclick="updateCityDomain('<?=$city_id?>','<?=$name?>','<?=isset($domain_id) ? $domain_id : ''?>')" class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-pencil"></i></button>
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
  <?php } else {
    echo "Select State";
  } ?>

  </div>
</div>


<div class="modal fade" id="splashModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Manage Company Splash</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="addUserDiv">
        
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="updateCityDomain">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Update City Domain</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="UpdateCityDomain" action="controller/CityController.php" method="post">
          <input type="hidden" id="edit_city_id_domain" name="city_id">
          <input type="hidden" name="sId" value="<?php echo isset($_GET['sId']) ? $_GET['sId'] : ''; ?>">
          <div class="form-group row">
            <label for="edit_city_name_domain" class="col-sm-4 col-form-label">City Name</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="edit_city_name_domain" readonly>
            </div>
          </div>
          <div class="form-group row">
            <label for="edit_domain_id_domain" class="col-sm-4 col-form-label">Recommended Domain</label>
            <div class="col-sm-8">
              <select class="form-control single-select" id="edit_domain_id_domain" name="domain_id">
                <option value="">-- Select Domain (Optional) --</option>
                <?php
                $qdom = $d->select("domain_master", "domain_active_status=0", "ORDER BY domain_name ASC");
                while ($dm = mysqli_fetch_array($qdom)) {
                  echo "<option value='" . $dm['domain_id'] . "'>" . htmlspecialchars($dm['domain_name']) . "</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="form-footer text-center">
            <button type="submit" name="EditCityDomain" value="EditCityDomain" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function updateCityDomain(city_id, name, domain_id) {
    $('#edit_city_id_domain').val(city_id);
    $('#edit_city_name_domain').val(name);
    $('#edit_domain_id_domain').val(domain_id).trigger('change');
  }
</script>