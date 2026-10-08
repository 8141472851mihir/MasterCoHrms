<?php 
  if (isset($_POST['log_type'])) {
    $log_type = $_POST['log_type'];
  }else{
    $log_type = 1;
  }
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Activity Logs</h4>
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
          <div class="card-header">
            <form method="POST">
              <select class="form-control single-select" name="log_type" onchange="this.form.submit()">
                <option value=""></option>
                <option <?php if($log_type==3){echo "selected";} ?> value="3">Company Requests</option>
                <!-- <option <?php if($log_type==4){echo "selected";} ?> value="4">KBG</option> -->
                <!-- <option <?php if($log_type==5){echo "selected";} ?> value="5">Housie</option> -->
                <option <?php if($log_type==6){echo "selected";} ?> value="6">Feedback</option>
                <option <?php if($log_type==7){echo "selected";} ?> value="7">CRM Inquiry Deletion Requests</option>
                <option <?php if($log_type==8){echo "selected";} ?> value="8">AI Settings</option>
              </select>
            </form>
          </div>
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
                  $q = $d->selectRow("log_master.*,society_master.society_name,society_master.city_name",
                  "log_master LEFT JOIN society_master ON log_master.society_id=society_master.society_id",
                  "log_type=$log_type","order by log_id  DESC limit 2000");

                  while ($data=mysqli_fetch_array($q)) {
                    $name = '';
                    $companyDisplay = '';
                    if ($data['society_id']!=0) {
                      if ($log_type==3) {
                        $socData =$d->selectArray("society_master_requests","request_society_id='$data[society_id]'");
                        $reqName = trim((string) ($socData['request_society_name'] ?? ''));
                        if ($reqName !== '') {
                          $name = $reqName." - ";
                          $companyDisplay = $reqName;
                        }
                      } else if ($log_type==1 || $log_type==2 || $log_type==8) {
                        $socName = trim((string) ($data['society_name'] ?? ''));
                        $cityName = trim((string) ($data['city_name'] ?? ''));
                        if ($socName === '' && $cityName === '') {
                          $socData = $d->selectArray("society_master","society_id='$data[society_id]'");
                          $socName = trim((string) ($socData['society_name'] ?? ''));
                          $cityName = trim((string) ($socData['city_name'] ?? ''));
                        }
                        $companyDisplay = trim($socName . ($socName !== '' && $cityName !== '' ? '-' : '') . $cityName, '-');
                        // Type 8 already includes company in Company Name column; avoid " - " prefix on Log
                        if ($log_type != 8 && $socName !== '') {
                          $name = $socName." - ";
                        }
                      }
                    }
                    
                    ?>
                    <tr>

                      <td><?php echo $i++; ?></td>
                      <td class="tableWidth"><?php echo "<b>".$name."</b>".$data["log_name"]; ?></td>
                      <td class="tableWidth"><?php echo $data["user_name"]; ?></td>
                      <td><?php 
                        if ($default_time_zone!="Asia/Kolkata") {
                              if (empty($default_time_zone) || !in_array($default_time_zone, timezone_identifiers_list())) {
                                  $default_time_zone = 'Asia/Kolkata';
                              }  
                              echo $d->change_timezone($data["log_time"],$default_time_zone,'d M Y h:i A');
                          } else {
                          echo $data["log_time"];
                          }
                      ?></td>
                      <td><?php echo $companyDisplay !== '' ? $companyDisplay : $data['society_name']; ?></td>
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