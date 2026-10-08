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
        <h4 class="page-title">Manage Country</h4>
      </div>
       <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <button data-toggle="modal" data-target="#AddCountry" class="btn btn-sm btn-primary waves-effect waves-light m-1"><i class="fa fa-plus"></i> Add</button>
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
                    <th>#</th>
                    <th>Name</th>
                    <th>Iso3</th>
                    <th>Iso2</th>
                    <th>Phone Code</th>
                    <th>Capital</th>
                    <th>Currency</th>
                    <th>Status</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $i=1;
                    $q = $d->select("countries","","order by country_id  DESC");
                    while ($data=mysqli_fetch_array($q)) {
                      extract($data);
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                        <td><?=$name?></td>
                        <td><?=$iso3?></td>
                        <td><?=$iso2?></td>
                        <td><?=$phonecode?></td>
                        <td><?=$capital?></td>
                        <td><?=$currency?></td>
                        <td>
                          <?php if($flag == 0){ ?>
                            <form action="controller/statusController.php" method="post" >
                              <input type="hidden" name="country_id" value="<?=$country_id?>">
                              <input type="hidden" name="flag" value="1">
                              <input type="hidden" name="name" value="<?=$name?>">
                              <input type="hidden" name="Status" value="CountryStatus">
                              <button type="submit" class="form-btn btn btn-sm btn-danger waves-effect waves-light m-1" title="Update Status">Deactive</button>
                            </form>
                          <?php }elseif($flag == 1){ ?>
                            <form action="controller/statusController.php" method="post" >
                              <input type="hidden" name="country_id" value="<?=$country_id?>">
                              <input type="hidden" name="flag" value="0">
                              <input type="hidden" name="name" value="<?=$name?>">
                              <input type="hidden" name="Status" value="CountryStatus">
                              <button style="background-color: green;" type="submit" class="form-btn btn btn-sm btn-info waves-effect waves-light m-1" title="Update Status">Active</button>
                            </form>
                          <?php } ?>
                        </td>
                        <td>
                          <div class="row ml-1">
                          <button data-toggle="modal" data-target="#updateCountry" onclick="updateCountry('<?=$country_id?>','<?=$name?>','<?=$iso3?>','<?=$iso2?>','<?=$phonecode?>','<?=$capital?>','<?=$currency?>')" class="btn btn-sm btn-primary waves-effect waves-light m-1"><i class="fa fa-pencil"></i></button>
                          <form action="controller/CountryController.php" method="post" >
                            <input type="hidden" name="country_id" value="<?=$country_id?>">
                            <input type="hidden" name="delectCountry" value="delectCountry">
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
  </div>
</div>


<div class="modal fade" id="AddCountry">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Add Country</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="AddCountrys" action="controller/CountryController.php" method="post">
          <input type="hidden" id="Add_country_id" name="country_id">
          <input type="hidden" id="Add_country_id" name="country_id">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Name <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="name" name="name" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">iso3 <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="iso3" name="iso3" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">iso2 <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="iso2" name="iso2" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Phone Code <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="phonecode" name="phonecode" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Capital <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="capital" name="capital" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Currency <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="currency" name="currency" required>
            </div>
          </div>
          <div class="form-footer text-center">
            <button type="submit" name="AddCountry" value="AddCountry" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="updateCountry">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Update Country</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="CountryUpdate" action="controller/CountryController.php" method="post">
          <input type="hidden" id="edit_country_id" name="country_id">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Name <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="edit_name" name="name" required="">
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">iso3 <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="edit_iso3" name="iso3" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">iso2 <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="edit_iso2" name="iso2" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Phone Code <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="edit_phonecode" name="phonecode" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Capital <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="edit_capital" name="capital" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Currency <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="edit_currency" name="currency" required>
            </div>
          </div>
          <div class="form-footer text-center">
            <button type="submit" name="EditCountry" value="EditCountry" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  function updateCountry(country_id,name,iso3,iso2,phonecode,capital,currency)
  {
    $('#edit_country_id').val(country_id);
    $('#edit_name').val(name);
    $('#edit_iso3').val(iso3);
    $('#edit_iso2').val(iso2);
    $('#edit_phonecode').val(phonecode);
    $('#edit_capital').val(capital);
    $('#edit_currency').val(currency);
  }
</script>