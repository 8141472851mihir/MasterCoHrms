 
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-6">
        <h4 class="page-title">Admins</h4>
        </div>
        <div class="col-sm-6 text-right">
          <button type="button" id="refreshEmployeeInfoBtn" class="btn btn-info btn-sm waves-effect waves-light mr-1" title="Refresh bound MyCo employee details">
            <i class="fa fa-refresh mr-1"></i> Refresh Employee Info
          </button>
          <a href="addUsers" class="btn btn-primary btn-sm waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add New</a>
        </div>
     </div>

     <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="">
          <ul class="nav nav-tabs nav-tabs-info nav-justified">
            <li class="nav-item">
              <a class="nav-link active" data-toggle="tab" href="#tabe-13"> Active</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link  show" data-toggle="tab" href="#tabe-14"> Deactive</span></a>
            </li>
            
          </ul>
          <!-- Tab panes -->
          <div class="tab-content">
            <div id="tabe-13" class="container-fluid tab-pane active show">
              <div class="">
                <div class="">
                  <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
                  <div class="">
                    <div class="table-responsive">
                    <table id="default-datatable1" class="table table-bordered">
                      <thead>
                        <tr>
                          <th class='deleteTh'>#</th>
                          <th>Action</th>
                          <th>Name</th>
                          <th>Role</th>
                          <th>Email</th>
                          <th>Mobile</th>
                          <th>Developer</th>
                          <th>Countries</th>
                          <th>Time Zone</th>
                          <th>Last Login</th>
                          <th>Created By</th>
                          <th>Created Date</th>
                          <th>Updated By</th>
                          <th>Updated Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          if($global_role_id==12 || $global_role_id==14 || $global_role_id==26) {
                            $role_query = "AND bms_admin_master.role_id!=1 AND bms_admin_master.role_id IN ('12','14','26')";
                          } else {
                            $role_query = "";
                          }
                          $q=$d->selectRow("bms_admin_master.*, session_log_latest.loginTime AS last_loginTime,role_master.*,bms_admin_master.created_by as admin_created_by,bms_admin_master.updated_by as admin_updated_by,bms_admin_master.created_date as admin_created_date,bms_admin_master.updated_date as admin_updated_date","bms_admin_master JOIN role_master ON role_master.role_id=bms_admin_master.role_id LEFT JOIN ( SELECT admin_id, MAX(loginTime) AS loginTime FROM session_log GROUP BY admin_id) AS session_log_latest ON session_log_latest.admin_id = bms_admin_master.admin_id ","bms_admin_master.active_status=0 $role_query","");
                          $i = 1;
                          while($row=mysqli_fetch_array($q)) {
                            // echo "<pre>"; print_r($row);
                        ?>
                          <tr>

                            <td><?php echo $i++; ?></td>
                            <td>
                              <form class="d-inline-block" method="POST" action="editUser">
                                <input type="hidden" name="admin_id" value="<?php echo $row['admin_id']; ?>">
                                <button class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></button>
                              </form>
                              <form class="d-inline-block" method="POST" action="controller/userController.php">
                                <input type="hidden" name="deleteUser">
                                <input type="hidden" name="admin_id" value="<?php echo $row['admin_id']; ?>">
                                <input type="hidden" name="admin_name" value="<?php echo $row['admin_name']; ?>">
                                <button class="btn btn-sm btn-danger form-btn"><i class="fa fa-trash-o"></i></button>
                              </form>
                            </td>
                            <td><?php echo $row['admin_name'] ?></td>
                            <td><?php echo $row['role_name'] ?></td>
                            <td><?php echo $d->encryptDecrypt("decrypt",$row['admin_email']); ?></td>
                            <td><?php echo $row['country_code']; ?> <?php echo $d->encryptDecrypt("decrypt",$row['admin_mobile']); ?></td>
                            <td><?php echo   ($row['is_developer']==1) ? 'Yes' : 'No' ; ?></td>
                            <td><?php 
                                 echo $d->count_data_direct("admin_country_id","admin_country_master","bms_admin_id='$row[admin_id]'");
                               ?>
                            </td>
                            <td><?php echo $row['default_time_zone'] ?></td>
                            <td><?php echo $row['last_loginTime'] ?></td>
                            <td><?php echo $row['admin_created_by'] ?></td>
                            <td><?php echo $row['admin_created_date'] ?></td>
                            <td><?php echo $row['admin_updated_by'] ?></td>
                            <td><?php echo $row['admin_updated_date'] ?></td>
                          </tr>
                        <?php } ?>
                      </tbody>

                    </table>
                  </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="tabe-14" class="container-fluid tab-pane fade ">
              <div class="">
                <div class="">
                  <div class="">
                    <div class="table-responsive">
                    <table id="default-datatable2" class="table table-bordered">
                      <thead>
                        <tr>
                          <th class='deleteTh'>#</th>
                          <th>Action</th>
                          <th>Name</th>
                          <th>Role</th>
                          <th>Email</th>
                          <th>Mobile</th>
                          <th>Countries</th>
                          <th>Last Login</th>
                          <th>Created By</th>
                          <th>Created Date</th>
                          <th>Deleted By</th>
                          <th>Deleted Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          // $q=$d->selectRow("bms_admin_master.*,role_master.*,bms_admin_master.created_by as admin_created_by,bms_admin_master.updated_by as admin_updated_by","bms_admin_master,role_master","role_master.role_id=bms_admin_master.role_id AND bms_admin_master.active_status=1","");
                        if($global_role_id==12 || $global_role_id==14) {
                          $role_query = "AND bms_admin_master.role_id!=1 AND bms_admin_master.role_id IN ('12','14')";
                        } else {
                          $role_query = "";
                        }
                        $q=$d->selectRow("bms_admin_master.*, session_log_latest.loginTime AS last_loginTime, role_master.* ,bms_admin_master.created_by as admin_created_by ,bms_admin_master.deleted_by as admin_deleted_by ,bms_admin_master.created_date as admin_created_date ,bms_admin_master.deleted_date as admin_deleted_date","bms_admin_master JOIN role_master ON role_master.role_id=bms_admin_master.role_id LEFT JOIN ( SELECT admin_id, MAX(loginTime) AS loginTime FROM session_log GROUP BY admin_id) AS session_log_latest ON session_log_latest.admin_id = bms_admin_master.admin_id ","bms_admin_master.active_status=1 $role_query","");
                          $i = 1;
                          while($row=mysqli_fetch_array($q)) {
                        ?>
                          <tr>

                            <td><?php echo $i++; ?></td>
                            <td>
                              
                              <form class="d-inline-block" method="POST" action="controller/userController.php">
                                <input type="hidden" name="deleteUserReactive">
                                <input type="hidden" name="admin_id" value="<?php echo $row['admin_id']; ?>">
                                <input type="hidden" name="admin_name" value="<?php echo $row['admin_name']; ?>">
                                <button class="btn btn-sm btn-danger form-btn">Re Active</button>
                              </form>
                            </td>
                            <td><?php echo $row['admin_name'] ?></td>
                            <td><?php echo $row['role_name'] ?></td>
                            <td><?php echo $d->encryptDecrypt("decrypt",$row['admin_email']); ?></td>
                            <td><?php echo $row['country_code']; ?> <?php echo $d->encryptDecrypt("decrypt",$row['admin_mobile']); ?>
                            </td>
                            <td><?php 
                            echo $d->count_data_direct("admin_country_id","admin_country_master","bms_admin_id='$row[admin_id]'");
                                ?>
                            </td>
                            <td><?php echo $row['last_loginTime'] ?></td>
                            <td><?php echo $row['admin_created_by'] ?></td>
                            <td><?php echo $row['admin_created_date'] ?></td>
                            <td><?php echo $row['admin_deleted_by'] ?></td>
                            <td><?php echo $row['admin_deleted_date'] ?></td>
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
    </div>
  </div>
    </div>
    <!-- End container-fluid-->
    </div><!--End content-wrapper-->
   <!--Start Back To Top Button-->



