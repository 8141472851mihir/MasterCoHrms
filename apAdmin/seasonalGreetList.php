<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-5">
        <h4 class="page-title">Seasonal Greetings List</h4>
      </div>
      <div class="form-group col-sm-2 expiry">
        <form action="" method="get" accept-charset="utf-8">
          <select class="form-control" name="is_expiry" id="is_expiry" onchange="this.form.submit()">
            <option value="" <?php if($_GET['is_expiry'] == ""){ echo "selected"; } ?>>All</option>
            <option value="Yes" <?php if($_GET['is_expiry'] == "Yes"){ echo "selected"; } ?> >Yes</option>
            <option value="No" <?php if($_GET['is_expiry'] == "No"){ echo "selected"; } ?> >No</option> 
            <option value="Common" <?php if($_GET['is_expiry'] == "Common"){ echo "selected"; } ?> >Common</option> 
          </select>
        </form>
      </div>
      <div class="col-sm-5">
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
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th class="text-right">#</th>
                    <th>Action</th>
                    <th>Title</th>
                    <th>Expiry</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Active Images</th>
                    <th>InActive Images</th>
                    <th>Created By</th>
                    <th>Created at</th>
                  </tr>
                </thead>
                <tbody id="expiry_data">
                  <?php
                  if(isset($_GET['is_expiry']) && $_GET['is_expiry'] != "")
                  {
                    $is_expiry = $d->sanitizeReportFilterIdAsInt($_GET['is_expiry']);
                    $where = "seasonal_greet_master.is_expiry='$is_expiry'";
                  }else{
                    $where = "";
                  }
                  $i=1;
                  $q=$d->select("seasonal_greet_master LEFT JOIN bms_admin_master ON  seasonal_greet_master.created_by =bms_admin_master.admin_id","$where","order by seasonal_greet_master.created_at
                    desc ");
                  $greetRows = [];
                  $greetIds = [];
                  while ($data=mysqli_fetch_array($q)) {
                    $greetRows[] = $data;
                    $greetIds[] = (int)$data['seasonal_greet_id'];
                  }

                  // Prefetch Active/InActive image counts for all greetings (avoid N+1)
                  $imageCountsByGreet = [];
                  if (!empty($greetIds)) {
                    $greetIdsIn = implode(',', array_map('intval', $greetIds));
                    $imgQ = $d->selectRow(
                      "seasonal_greet_id, status, COUNT(*) AS cnt",
                      "seasonal_greet_image_master",
                      "seasonal_greet_id IN ($greetIdsIn) AND status IN ('Active','InActive')",
                      "GROUP BY seasonal_greet_id, status"
                    );
                    while ($imgRow = mysqli_fetch_assoc($imgQ)) {
                      $gid = (int)$imgRow['seasonal_greet_id'];
                      $imageCountsByGreet[$gid][$imgRow['status']] = (int)$imgRow['cnt'];
                    }
                  }

                  foreach ($greetRows as $data)
                  {
                    extract($data);
                    ?>
                    <tr>
                      <td class="text-right"><?php echo $i++; ?></td>
                      <td>
                        <div style="display: inline-block;">
                          <form action="manageSeasonalGreet" method="get">
                            <input type="hidden" name="seasonal_greet_id" value="<?php echo $seasonal_greet_id; ?>">
                            <button type="submit" name="" class="btn btn-warning btn-sm "> Manage</button>
                          </form>
                        </div>
                        <div style="display: inline-block;">
                          <form action="seasonalGreet" method="post">
                            <input type="hidden" name="edit_seasonal_greet_id" value="<?php echo $seasonal_greet_id; ?>">
                            <button type="submit" name="" class="btn btn-primary btn-sm "> Edit</button>
                          </form>
                        </div>
                      <?php $today = date("Y-m-d");
                        if ($is_expiry=="Yes"  && strtotime($today) > strtotime($end_date) ) { ?>
                        <div style="display: inline-block;">
                          <form  action="controller/seasonalGreetController.php" method="post">
                            <input type="hidden" name="delete_seasonal_greet_id" value="<?php echo $seasonal_greet_id; ?>">
                            <button type="submit" name="deleteCmp" class="form-btn btn btn-danger btn-sm "> Delete</button>
                          </form>
                        </div>
                        <div style="display: inline-block;">
                          <form action="seasonalGreet" method="post">
                            <input type="hidden" name="copy_seasonal_greet_id" value="<?php echo $seasonal_greet_id; ?>">
                            <button type="submit" name="copySG" class="btn btn-green btn-sm "> Copy</button>
                          </form>
                        </div>
                       <?php }  ?>
                      </td>
                      <td id="title_<?=$seasonal_greet_id?>"><?php echo  $title; ?></td>
                      <td id="is_expiry_<?=$seasonal_greet_id?>"><?php echo $is_expiry; ?></td>
                      <td>
                      <?php if($is_expiry=="Yes" && $start_date!="0000-00-00" ){ echo date("d-m-Y", strtotime($start_date));}
                        else if($is_expiry=="Common" && $start_date!="00-00"){ echo date("d-m", strtotime($start_date));}else { echo "-";} ?>
                      </td>
                      <td><?php if($is_expiry=="Yes" && $end_date!="0000-00-00") {echo date("d-m-Y", strtotime($end_date));} else if($is_expiry=="Common" && $end_date!="00-00") {echo date("d-m", strtotime($end_date));} else { echo "-"; } ?></td>

                       <td class="text-right"><?php
                        echo (int)($imageCountsByGreet[(int)$seasonal_greet_id]['Active'] ?? 0);
                       ?></td>
                       <td class="text-right"><?php
                        echo (int)($imageCountsByGreet[(int)$seasonal_greet_id]['InActive'] ?? 0);
                       ?></td>
                      <td><?php echo $admin_name; ?></td>
                      <td data-order="<?php echo date("U",strtotime($created_at)); ?>"><?php echo date("d-m-Y h:i:s A", strtotime($created_at)); ?></td>
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