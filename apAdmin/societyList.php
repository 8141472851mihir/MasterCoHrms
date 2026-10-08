<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Company</h4>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="welcome">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Company</li>
        </ol>
      </div>
       <div class="col-sm-3">
       <div class="btn-group float-sm-right">
       
      </div>
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
                    <th>Banners</th>
                    <th>Company Server</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $i=1;
                    if (isset($_GET['city_id'])) {
                      $city_id = $d->sanitizeReportFilterIdAsInt($_GET['city_id']);
                      $q = $d->select("society_master","city_id='$city_id'","order by society_id  DESC");
                    } else{
                      $q = $d->select("society_master","","order by society_id  DESC");
                    }
                    while ($data=mysqli_fetch_array($q)) {
                      extract($data);
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                        <td><span style="display: none;"><?php echo $society_id; ?></span><?php echo ''.$d->short_app_name().'_'.$society_id; ?></td>
                        <td><?php echo $society_name; ?></td>
                        <td><?php echo $city_name; ?></td>
                        <td><?php echo $d->count_data_direct("app_common_slider_id","app_common_slider_master","society_id='$society_id' AND status=0") ?>
                          <form action="companyBanners" method="GET" class="d-inline-block float-right">
                            <input type="hidden" name="id" value="<?php echo $society_id ?>">
                            <button class="btn btn-primary btn-sm" data-toggle="tooltip" title="Company Banners"><i class="fa fa-eye"></i></button>
                          </form>
                        </td>
                        <td>
                          <?php if ($created_on_society_server ==1 ) {
                            echo "Created";
                          } else { ?>
                            <form action="controller/createSocietyAutoController.php" method="post" class="d-inline-block float-right">
                              <input type="hidden" name="society_id" value="<?php echo $society_id ?>">
                              <input type="hidden" name="createSoceitySubdomain" value="<?php echo "createSoceitySubdomain" ?>">
                              <button class="btn btn-danger form-btn btn-form btn-sm" data-toggle="tooltip" title="Create Company"> Create</button>
                          </form>
                          <?php } ?>
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