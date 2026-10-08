<?php extract($_REQUEST);
$seasonal_greet_id = (int)$seasonal_greet_id;
$seasonal_greet_master_qry=$d->select("seasonal_greet_master","  seasonal_greet_id  = '$seasonal_greet_id' ","");
$seasonal_greet_master_d=mysqli_fetch_array($seasonal_greet_master_qry);

?>

<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title"><?php echo $seasonal_greet_master_d['title'];?> -  Seasonal Greetings Image List</h4>
        
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
           <a href="seasonalGreetList"  class="btn  btn-sm btn-secondary pull-right">Seasonal Greetings</a>

          <a href="seasonalGreetImage?seasonal_greet_id=<?php echo $seasonal_greet_id;?>"  class="btn  btn-sm btn-primary pull-right"><i class="fa fa-plus fa-lg"></i> Add New </a>
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
                  
                  <th class="text-right">#</th>
                  <th>Action</th>
        
                  <th> Show To Name</th>
                  <th> Show From Name</th>
                  <th> Images</th>
                  <th>Created By</th>
                  <th>Created at</th>
                  
                </tr>
              </thead>
              <tbody>
                <?php
                $i=1;
                $q=$d->select("seasonal_greet_image_master LEFT JOIN bms_admin_master ON seasonal_greet_image_master.created_by=bms_admin_master.admin_id","seasonal_greet_image_master.seasonal_greet_id ='$seasonal_greet_id'   order by seasonal_greet_image_master.created_at
                  desc ");
                while ($data=mysqli_fetch_array($q)) {
                  extract($data);
                  ?>
                  <tr>
                    <td class="text-right"><?php echo $i++; ?></td>
                    <td>
                      <div style="display: inline-block;">
                        <form action="seasonalGreetImage" method="post">
                          <input type="hidden" name="seasonal_greet_image_id" value="<?php echo $seasonal_greet_image_id; ?>">
                          <button type="submit" name="" class="btn btn-primary btn-sm "> Edit</button>
                        </form>
                      </div>
                      <div style="display: inline-block;">
                        <form  action="controller/seasonalGreetController.php" method="post">
                          <input type="hidden" name="delete_seasonal_greet_image_id" value="<?php echo $seasonal_greet_image_id; ?>">
                           <input type="hidden" name="seasonal_greet_id" value="<?php echo $seasonal_greet_id; ?>">

                          <button type="submit" name="deleteCmp" class="form-btn btn btn-danger btn-sm "> Delete</button>
                        </form>
                      </div>
                    </td>
                    <td><?php echo $show_to_name; ?></td>
                    <td><?php echo $show_from_name; ?></td>
                     <td>
                      <?php if(file_exists('../img/promotion/'.$background_image)) { ?>
                      <a href="../img/promotion/<?php echo $background_image; ?>" data-fancybox="images" data-caption="Background Photo"><img class="lazyload" width="30" onerror="this.src='img/user.png'" height="30" src="img/user.png" data-src="../img/promotion/<?php echo $background_image; ?>"></a>
                      <?php } ?>
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
  </div><!-- End Row-->
</div>
<!-- End container-fluid-->
</div><!--End content-wrapper-->
    <!--Start Back To Top Button-->