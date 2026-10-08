<?php
extract($_REQUEST);
error_reporting(0);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
?>

<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Manage City</h4>
      </div>
       <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <?php  if (isset($countryId) && filter_var($countryId, FILTER_VALIDATE_INT) == true  ) {
            if (isset($sId) && filter_var($sId, FILTER_VALIDATE_INT) == true  ) {  ?>
              <button data-toggle="modal" data-target="#AddCity" onclick="AddCity('<?=$countryId?>','<?=$sId?>')" class="btn btn-sm btn-primary waves-effect waves-light m-1"><i class="fa fa-plus"></i> Add</button>
        <?php }  }?>
        </div>
      </div>
    </div>
  <!-- End Breadcrumb-->
   <div class="row pt-2 pb-2">
      <div class="col-lg-12">
        <form action="" method="get" accept-charset="utf-8">
          <div class="form-group row">
            <label for="country_id" class="col-sm-1 col-form-label"> Country <span class="required">*</span></label>
            <div class="col-sm-3">
              <select type="text" required="" id="country_id" onchange="this.form.submit()" class="form-control single-select" name="countryId">
                <option value="">-- Select --</option>
                <?php 
                 $qc=$d->select("countries","flag=1");
                  while ($cData=mysqli_fetch_array($qc)) {
                  ?>
                  <option <?php if( isset($_GET['countryId']) && $cData['country_id']==$_GET['countryId']) {echo "selected";} ?> value="<?php echo $cData['country_id'];?>"><?php echo $cData['name'];?></option>
                <?php }?>
              </select>
            </div>
            <label for="state_id" class="col-sm-1 col-form-label"> State <span class="required">*</span></label>
            <div class="col-sm-3">
              <?php  if(isset($_GET['sId'])) {
               ?>
                <select type="text" onchange="this.form.submit()"  required="" class="form-control single-select" id="state_id" name="sId">
                  <option value=""> All</option>
                  <?php
                   $countryIdFilter = isset($_GET['countryId']) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId']) : 0;
                   $qs=$d->select("states","country_id=$countryIdFilter AND flag=1");
                  while ($sData=mysqli_fetch_array($qs)) {
                    ?>
                    <option <?php if( isset($_GET['sId']) && $sData['state_id']==$_GET['sId']) {echo "selected";} ?> value="<?php echo $sData['state_id'];?>"><?php echo $sData['name'];?></option>
                  <?php }  ?>
                </select>
              <?php } else { ?>
                <select type="text" onchange="getCity();"  required="" class="form-control single-select" id="state_id" name="sId">
                  <option value="">-- Select --</option>
                </select>
              <?php } ?>
            </div>
          </div>
        </form>
      </div>
    </div>
 <?php  if (isset($countryId) && filter_var($countryId, FILTER_VALIDATE_INT) == true  ) { 
  if (isset($sId) && filter_var($sId, FILTER_VALIDATE_INT) == true  ) {  ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Recommended Domain</th>
                    <th>Status</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $i=1;

                    if (isset($sId) && $sId>0) {
                      $appendStateQuery = " AND state_id='$sId'";
                    }

          
                    $q = $d->selectRow("cities.*, domain_master.domain_name", "cities LEFT JOIN domain_master ON cities.domain_id = domain_master.domain_id", "cities.country_id='$countryId' AND cities.state_id='$sId'", "order by cities.city_id DESC");
                    while ($data=mysqli_fetch_array($q)) {
                      extract($data);
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                        <td><?=$name?></td>
                        <td><?=isset($domain_name) && $domain_name ? $domain_name : ''?></td>
                        <td>
                          <?php if($flag == 0){ ?>
                            <form action="controller/statusController.php" method="post" >
                              <input type="hidden" name="country_id" value="<?=$country_id?>">
                              <input type="hidden" name="state_id" value="<?=$state_id?>">
                              <input type="hidden" name="city_id" value="<?=$city_id?>">
                              <input type="hidden" name="flag" value="1">
                              <input type="hidden" name="name" value="<?=$name?>">
                              <input type="hidden" name="Status" value="CityStatus">
                              <button type="submit" class="form-btn btn btn-sm btn-danger waves-effect waves-light m-1" title="Update Status">Deactive</button>
                            </form>
                          <?php }elseif($flag == 1){ ?>
                            <form action="controller/statusController.php" method="post" >
                              <input type="hidden" name="country_id" value="<?=$country_id?>">
                              <input type="hidden" name="state_id" value="<?=$state_id?>">
                              <input type="hidden" name="city_id" value="<?=$city_id?>">
                              <input type="hidden" name="flag" value="0">
                              <input type="hidden" name="name" value="<?=$name?>">
                              <input type="hidden" name="Status" value="CityStatus">
                              <button style="background-color: green;" type="submit" class="form-btn btn btn-sm btn-info waves-effect waves-light m-1" title="Update Status">Active</button>
                            </form>
                          <?php } ?>
                        </td>
                        <td>
                          <div class="row ml-1">
                          <button data-toggle="modal" data-target="#updatePlan" onclick="updateCity('<?=$country_id?>','<?=$state_id?>','<?=$city_id?>','<?=$name?>','<?=isset($domain_id) ? $domain_id : ''?>')" class="btn btn-sm btn-primary waves-effect waves-light m-1"><i class="fa fa-pencil"></i></button>
                          <form action="controller/CityController.php" method="post" >
                            <input type="hidden" name="country_id" value="<?=$country_id?>">
                            <input type="hidden" name="state_id" value="<?=$state_id?>">
                            <input type="hidden" name="city_id" value="<?=$city_id?>">
                            <input type="hidden" name="delectCity" value="delectCity">
                            <button type="submit" class="form-btn btn btn-sm btn-danger waves-effect waves-light m-1" title="Delete"> <i class="fa fa-trash-o"></i> </button>
                          </form>
                          </div>
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
  <?php } else {
    echo "Select Country";
  } ?>
  </div>
</div>


<div class="modal fade" id="AddCity">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Add City</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="CityAdd" action="controller/CityController.php" method="post">
          <input type="hidden" id="Add_country_id" name="country_id">
          <input type="hidden" id="Add_state_id" name="state_id">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Name <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="name" name="name" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="domain_id" class="col-sm-4 col-form-label">Recommended Domain</label>
            <div class="col-sm-8">
              <select class="form-control single-select" id="domain_id" name="domain_id">
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
            <button type="submit" name="AddCity" value="AddCity" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="updatePlan">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Update City</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="UpdateCity" action="controller/CityController.php" method="post">
          <input type="hidden" id="edit_country_id" name="country_id">
          <input type="hidden" id="edit_state_id" name="state_id">
          <input type="hidden" id="edit_city_id" name="city_id">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Name <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="edit_name" name="name" required="">
            </div>
          </div>
          <div class="form-group row">
            <label for="edit_domain_id" class="col-sm-4 col-form-label">Recommended Domain</label>
            <div class="col-sm-8">
              <select class="form-control single-select" id="edit_domain_id" name="domain_id">
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
            <button type="submit" name="EditCity" value="EditCity" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- <script src="assets/js/jquery.min.js"></script> -->
<script type="text/javascript">
  function updateCity(country_id,state_id,city_id,name,domain_id)
  {
    $('#edit_country_id').val(country_id);
    $('#edit_state_id').val(state_id); 
    $('#edit_city_id').val(city_id); 
    $('#edit_name').val(name);
    $('#edit_domain_id').val(domain_id).trigger('change');

  }

  function AddCity(country_id,state_id)
  {
    $('#Add_country_id').val(country_id);
    $('#Add_state_id').val(state_id); 
  }

  
</script>