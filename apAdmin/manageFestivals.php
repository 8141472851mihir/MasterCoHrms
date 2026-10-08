<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-9 col-5">
        <h4 class="page-title">Festivals</h4>
      </div>
      <div class="col-sm-3 col-7">
        <div class="btn-group float-sm-right">
          <a href="festivals" class="btn btn-primary btn-sm waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add New</a>
          <a href="javascript:void(0)" onclick="DeleteAll('deleteFestivals');" class="btn btn-danger btn-sm waves-effect waves-light"><i class="fa fa-trash-o fa-lg"></i> Delete </a>
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
                    <th>#</th>
                    <th>Action</th>
                    <th>Image</th>
                    <th>Date</th>
                    <th>Festival</th>
                    <th>View Status</th>
                    <th>Active Status</th>

                  </tr>
                </thead>
                <tbody>
                  <?php
                  unset($_SESSION['post']);
                  $i = 1;
                  $q = $d->select("festival_master", "festival_id!=0 $countryAppendQuerySocietySingle OR festival_id!=0 AND country_id=0 AND is_festival=0", "ORDER BY festival_date DESC");
                  while ($row = mysqli_fetch_array($q)) {
                    extract($row);
                  ?>
                    <tr>
                      <td class='text-center'>
                        <input type="checkbox" class="multiDelteCheckbox" value="<?php echo $row['festival_id']; ?>">
                      </td>
                      <td><?php echo $i++; ?></td>
                      <td>
                        <form action="festivals" method="post">
                          <input type="hidden" name="festival_id" value="<?php echo $festival_id; ?>">
                          <button class="btn btn-sm btn-primary" name="updateSos" value="Edit"><i class="fa fa-pencil"></i></button>
                        </form>
                      </td>
                      <td><img alt="Festival Image" src="../img/festival/<?php echo $festival_image; ?>" width='50px' ;></td>
                      <td><?php if ($default_time_zone != "Asia/Kolkata") {
                            echo $d->change_timezone($festival_date, $default_time_zone, 'Y-m-d');
                          } else {
                            echo $festival_date;
                          } ?></td>
                      <td><?php echo $festival_name; ?></td>
                      <td>
                        <?php 
                        if ($festival_view_status == 1) { ?>
                          <label class="switch-custom">
                            <input type="checkbox" data-color="#15ca20" checked data-size="small" onchange="changeStatus('<?php echo $row['festival_id']; ?>','viewSingle');" />
                            <span class="slider-custom round"></span>
                          </label>
                          <br>
                          Every Time When App Open
                        <?php } else { ?>
                          <label class="switch-custom">
                          <input type="checkbox" data-color="#15ca20" data-size="small" onchange="changeStatus('<?php echo $row['festival_id']; ?>','viewMultiple');" />
                          <span class="slider-custom round"></span>
                          </label>
                          <br>
                          1 Time When App Open
                        <?php } ?>

                      </td>
                      <td>
                        <?php
                        $buttonClass = ($festival_active_status == "0") ? 'btn-success-new' : 'btn-danger';
                        $buttonCondition = ($festival_active_status == "0") ? 'Active' : 'Deactive';
                        $status = ($festival_active_status == "0") ? 'statusDeactive' : 'statusActive';
                        $newStatus = ($festival_active_status == "0") ? 'statusActive' : 'statusDeactive';
                        $newStatusVal = ($festival_active_status == "0") ? '1' : '0';
                        $statusValue = ($festival_active_status == "0") ? '0' : '1';
                        ?>

                        <input type="button" class="btn btn-sm pl-1 pr-1 w-75 <?php echo $buttonClass ?>" id="<?php echo 'festival_' . $festival_id; ?>" onclick="changeStatusNew('<?php echo $festival_id; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'festival_' . $festival_id; ?>');" data-size="small" value="<?php echo $buttonCondition ?>" />
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