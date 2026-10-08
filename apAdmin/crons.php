  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
        <h4 class="page-title">Crons</h4>
        
     </div>
     <div class="col-sm-3">
       <div class="btn-group float-sm-right">
        <a href="managecron" class="btn btn-primary btn-sm waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add New</a>
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
                        <th>Sr.No</th>
                        <th>Cron ID</th>
                        <th>Cron Name</th>
                        <th>Cron URL</th>
                        <th>Companies</th>
                        <th>Last Run Time</th>
                        <th>Category</th>
                        <th>Edit</th>
                        <th>Run Log</th>
                    </tr>
                </thead>
                <tbody>
                  <?php 
                    $i=1;
                    $q=$d->selectRow("crons_master.*,(SELECT COUNT(*) FROM crons_society_master WHERE crons_society_master.cron_id = crons_master.cron_id ) AS total_company","crons_master","");
                    while ($data=mysqli_fetch_array($q)) {
                  ?>
                  <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $data['cron_id']; ?></td>
                    <td><?php echo $data['cron_name']; ?></td>
                    <td><?php echo $data['cron_url']; ?></td>
                    <td><?php echo $data['total_company']; ?></td>
                    <td><?php echo $data['last_run_time']; ?></td>
                    <td><?php echo $data['cron_category']; ?></td>
                    
                    
                    <td>
                      <form action="managecron" method="get" style ='float: left;margin-right: 5px;'>
                        <input type="hidden" name="cron_id" value="<?php echo $data['cron_id']; ?>">
                        <button class="btn btn-sm btn-primary" data-toggle="tooltip" title="Edit Role"> <i class="fa fa-pencil"></i> Edit</button>
                      </form>
                    </td>
                    <td>
                      <form action="managecron" method="get" style ='float: left;margin-right: 5px;'>
                        <input type="hidden" name="runLog" value="1">
                        <input type="hidden" name="cron_id" value="<?php echo $data['cron_id']; ?>">
                        <button class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit Role"> <i class="fa fa-internet-explorer"></i> View</button>
                      </form>
                    </td>
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



