<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Seasonal Greetings List</h4>
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <a href="seasonalGreet"  class="btn  btn-sm btn-primary pull-right"><i class="fa fa-plus fa-lg"></i> Add New </a>
        </div>
      </div>
    </div>
  <!-- End Breadcrumb-->
  <div class="row">
    <div class="col-lg-12">
      <div class="card">

        <div class="card-body">
          <div class="table-responsive">
            <form method="POST" id="form" action="controller/exportGreetingsController.php">
              <input type="hidden" name="exportGreetingsData" value="exportGreetingsData">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th></th>
                    <th class="text-right">#</th>
                    <th>Title</th>
                    <th> Expiry</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Active Images</th>
                    <th>InActive Images</th>
                    <th>Created By</th>
                    <th>Created at</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i=1;
                  $q=$d->selectRow("sg.*,bam.*,COUNT(CASE WHEN sgim.status = 'Active' THEN 1 END) AS totalActiveImages, COUNT(CASE WHEN sgim.status = 'InActive' THEN 1 END) AS totalInactiveImages","seasonal_greet_master sg LEFT JOIN bms_admin_master bam ON sg.created_by = bam.admin_id LEFT JOIN seasonal_greet_image_master sgim ON sgim.seasonal_greet_id = sg.seasonal_greet_id","sg.is_expiry='No' OR sg.start_date>=CURRENT_DATE()","GROUP BY sg.seasonal_greet_id order by sg.start_date DESC");
                  while ($row=mysqli_fetch_array($q)) {
                    extract($row);
                    ?>
                    <tr>
                      <td>
                        <input name="keyId[]" type="checkbox" class="" value="<?php echo $row['seasonal_greet_id'];?>">
                      </td>
                      <td class="text-right"><?php echo $i++; ?></td>
                      <td><?php echo  $title; ?></td>
                      <td><?php echo $is_expiry; ?></td>
                      <td><?php if($is_expiry=="Yes" && $start_date!="0000-00-00" ) {echo date("d-m-Y", strtotime($start_date));} else echo "-"; ?></td>
                      <td><?php if($is_expiry=="Yes" && $end_date!="0000-00-00") {echo date("d-m-Y", strtotime($end_date));} else echo "-"; ?></td>
                      <td class="text-right"><?php echo $row['totalActiveImages']; ?></td>
                      <td class="text-right"><?php echo $row['totalInactiveImages']; ?></td>
                      <td><?php echo $admin_name; ?></td>
                      <td data-order="<?php echo date("U",strtotime($created_at)); ?>"><?php echo date("d-m-Y h:i:s A", strtotime($created_at)); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
              </table>
              <div class="text-center mt-2 row">
                <div class="col-lg-6 offset-lg-2">
                  <select class="form-control single-select" required name="export_url">
                    <option value=""> Select Export to</option>
                    <?php 
                    $q=$d->select("seasonal_greetings_company_list","","");
                    while ($row=mysqli_fetch_array($q)) {
                      ?>
                      <option value="<?php echo $row['company_master_url']?>"><?php echo $row['company_name']?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-lg-2">
                  <button type="submit" class="btn btn-success">Export</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>