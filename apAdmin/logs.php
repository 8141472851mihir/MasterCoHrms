<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Other Logs</h4>
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
                    <th>Log</th>
                    <th>User</th>
                    <th>Log Time</th>
                    <th>Company Name</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $i=1;
                  $q = $d->selectRow("log_master.*,society_master.society_name" ,
                  "log_master LEFT JOIN society_master ON log_master.society_id=society_master.society_id",
                  "log_type=0","order by log_time  DESC limit 5000");
                  while ($data=mysqli_fetch_array($q)) {
                    ?>
                    <tr>

                      <td><?php echo $i++; ?></td>
                      <td><?php echo $data["log_name"]; ?></td>
                      <td><?php echo $data["user_name"]; ?></td>
                      <td><?php 
                          if ($default_time_zone!="Asia/Kolkata") {
                              if (empty($default_time_zone) || !in_array($default_time_zone, timezone_identifiers_list())) {
                                  $default_time_zone = 'Asia/Kolkata';
                              }         
                              echo $d->change_timezone($data["log_time"],$default_time_zone,'d M Y h:i A');
                          } else {
                          echo $data["log_time"];
                          } ?></td>
                      <td><?php echo $data['society_name']; ?></td>
                      <!-- <td></td> -->
                    </tr>
                  <?php } ?>

                </tbody>

              </table>
            </div>
          </div>
        </div>
      </div>
    </div><!-- End Row-->

  </div>
</div>