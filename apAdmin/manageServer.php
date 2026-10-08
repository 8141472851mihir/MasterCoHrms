<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Manage Server</h4>

      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <a href="#" id="addServerBtn" data-toggle="modal" data-target="#addServer"
            class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add Server</a>
        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    $companyArray = array();
    $companyArrayExpire = array();
    $qcompany = $d->selectRow("server_master.server_id,CASE WHEN plan_expire_date < CURDATE() THEN 'YES' ELSE 'NO' END AS is_expired", "society_master,server_master,domain_master", "server_master.server_id=domain_master.server_id AND society_master.domain_id=domain_master.domain_id");
    while ($companyCountData = mysqli_fetch_assoc($qcompany)) {

      if (array_key_exists($companyCountData['server_id'], $companyArray)) {
        $companyArray[$companyCountData['server_id']] = $companyArray[$companyCountData['server_id']] + 1;
        if ($companyCountData['is_expired'] == 'YES') {
          $companyArrayExpire[$companyCountData['server_id']] = $companyArrayExpire[$companyCountData['server_id']] + 1;
        }
      } else {
        $companyArray[$companyCountData['server_id']] = 1;
        $companyArrayExpire[$companyCountData['server_id']] = 0;
      }
    }

    ?>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="reportTable" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Server Name</th>
                    <th>Server Ip</th>
                    <th>Company Count</th>
                    <th>Crm Count</th>
                    <th>Active Company</th>
                    <th>Expire Company</th>
                    <th>Active %</th>
                    <th>Domain Count</th>
                    <th>Login Employees</th>
                    <th>Tracking Employees</th>
                    <th>Active Tracking Employees</th>
                    <th>Server Remark</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th class="no-search-box"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="no-search-box"></th>
                  </tr>
                </tfoot>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->selectRow("server_master.*, (SELECT COUNT(domain_id) FROM domain_master where domain_master.server_id=server_master.server_id) as domain_count,
                   (SELECT COUNT(sm.society_id)
        FROM society_master sm
        INNER JOIN domain_master dm ON sm.domain_id = dm.domain_id
        WHERE dm.server_id = server_master.server_id AND sm.crm_created = 1
    ) as crm_count,
                  ( SELECT SUM(sam.total_login_user)  FROM society_analytics_master sam INNER JOIN society_master sm ON sm.society_id = sam.society_id INNER JOIN domain_master dm ON dm.domain_id = sm.domain_id WHERE dm.server_id = server_master.server_id ) AS total_login_user_sum, ( SELECT SUM(sam.active_tracking_users) FROM society_analytics_master sam INNER JOIN society_master sm ON sm.society_id = sam.society_id INNER JOIN domain_master dm ON dm.domain_id = sm.domain_id WHERE dm.server_id = server_master.server_id ) AS active_tracking_users_sum, ( SELECT SUM(sam.last_month_tracking_user_count) FROM society_analytics_master sam INNER JOIN society_master sm ON sm.society_id = sam.society_id INNER JOIN domain_master dm ON dm.domain_id = sm.domain_id WHERE dm.server_id = server_master.server_id ) AS last_month_tracking_user_count_sum", "server_master", "");
                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);

                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><a href="manageDomain?sId=<?php echo $server_id; ?>"><?php echo $server_name; ?></a></td>
                      <td><?php echo $server_ip; ?></td>
                      <td><?php if (array_key_exists($server_id, $companyArray)) {
                        echo $companyArray[$server_id];
                      } ?></td>
                       <td><?php
                      if ($crm_count == 0) {
                        echo "";
                      } else {
                        echo $crm_count;
                      } ?>
                      </td>
                      <td><?php if (array_key_exists($server_id, $companyArrayExpire)) {
                        echo $activeCompany = $companyArray[$server_id] - $companyArrayExpire[$server_id];
                      } ?></td>
                     
                      <td><?php if (array_key_exists($server_id, $companyArrayExpire)) {
                        echo $companyArrayExpire[$server_id];
                      } ?></td>
                      <td><?php if ($companyArray[$server_id]>0 ) {
                        echo (int) ($activeCompany*100/$companyArray[$server_id]);
                      } ?></td>
                      <td><?php echo $domain_count; ?></td>
                      <td><?php echo $total_login_user_sum; ?></td>
                      <td><?php echo $active_tracking_users_sum; ?></td>
                      <td><?php echo $last_month_tracking_user_count_sum; ?></td>
                      <td><?php echo $server_remark; ?></td>
                      <td class="d-flex">
                        <button type="button" data-toggle="modal" data-target="#addServer" class="btn btn-info btn-sm "
                          onclick="editServer(<?php echo $server_id ?>,'<?php echo $server_name ?>','<?php echo $server_ip ?>','<?php echo $server_remark ?>')"><i
                            class="fa fa-pencil"></i></button>
                        <?php
                        $buttonClass = ($server_active_status == "0") ? 'btn-success-new' : 'btn-danger';
                        $buttonCondition = ($server_active_status == "0") ? 'Active' : 'Deactive';
                        $status = ($server_active_status == "0") ? 'serverDeactive' : 'serverActive';
                        $newStatus = ($server_active_status == "0") ? 'serverActive' : 'serverDeactive';
                        $newStatusVal = ($server_active_status == "0") ? '1' : '0';
                        $statusValue = ($server_active_status == "0") ? '0' : '1';
                        ?>
                        <input type="button" class="btn btn-sm pl-1 pr-1 mx-1 w-75 <?php echo $buttonClass ?>"
                          id="<?php echo 'server_' . $server_id; ?>"
                          onclick="changeStatusNew('<?php echo $server_id; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'server_' . $server_id; ?>','');"
                          data-size="small" value="<?php echo $buttonCondition ?>" />
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

<div class="modal fade" id="addServer">
  <div class="modal-dialog modal-md">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white"><span id="modeshow">Add</span> Server</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="serverForm" action="controller/serverController.php" method="post">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Server Name <span class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" maxlength="100" class="form-control" name="server_name"
                id="server_name_edit" value="" required="">
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Server Ip <span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control " name="server_ip" value="" id="server_ip_edit"
                required="">
            </div>
          </div>

          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Server Remark </label>
            <div class="col-sm-8">
              <textarea class="form-control" name="server_remark" maxlength="300" id="server_remark_edit"></textarea>
            </div>
          </div>

          <div class="form-footer text-center">
            <input type="hidden" name="server_id" id="server_id_edit">
            <button name="addServer" id="submitButton" type="submit" class="btn btn-success"><i
                class="fa fa-check-square-o"></i> <span id="submitText">Add</span></button>

            <button type="reset" class="btn btn-danger"><i class="fa fa-times"></i> CANCEL</button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>