    <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
        <h4 class="page-title">Session Logs</h4>
        
     </div>
     <div class="col-sm-3">
       <div class="btn-group float-sm-right">
        <a href="#" class="btn btn-sm btn-danger shadow-danger waves-effect waves-light"><i class="fa fa-trash-o mr-1"></i> Delete</a>
       
        </button>
        
      </div>
     </div>
     </div>
    <!-- End Breadcrumb-->
     

      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
            <div class="card-body">
              <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                    <tr>
                      <th class="deleteTh">
                          <input type="checkbox" class="selectAll" label="check all"  />
                      </th>
                      <th>#</th>
                      <th>Name</th>
                      <th>User Role</th>
                      <th>Login Time</th>
                      <th>Ip Address</th>
                      <th>Browser</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i=1;
                    $q = $d->select("session_log" ,"","order by sessionId  DESC");
                    while ($data=mysqli_fetch_array($q)) {
                      // print_r($data);
                     ?>
                    <tr>
                    <td class='text-center'>
                      <input type="checkbox" class="multiDelteCheckbox"  value="<?php echo $data['sessionId']; ?>">
                    </td>
                      <td><?php echo $i++; ?></td>
                        <td><?php echo $data["name"]; ?></td>
                        <td><?php echo $data["role_name"]; ?></td>
                        <td><?php 
                          if ($default_time_zone!="Asia/Kolkata" && $default_time_zone!="") {
                            if (empty($default_time_zone) || !in_array($default_time_zone, timezone_identifiers_list())) {
                              $default_time_zone = 'Asia/Kolkata';
                            }  
                            echo $d->change_timezone($data["loginTime"],$default_time_zone,'d M Y h:i A');
                          } else {
                            echo $data["loginTime"];
                          }
                        ?></td>
                        <td><a target="_blank" href="https://www.opentracker.net/feature/ip-tracker?ip=<?php echo $data["ip_address"]; ?>&k="><?php echo $data["ip_address"]; ?></a></td>
                        <td><?php echo $data["browser"]; ?></td>
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
    <!-- End container-fluid-->
    
    </div><!--End content-wrapper-->
   <!--Start Back To Top Button-->



