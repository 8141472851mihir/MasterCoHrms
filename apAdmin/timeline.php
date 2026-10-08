<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Timeline</h4>

      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">


          <a data-toggle="modal" data-target="#feed" class="btn btn-primary btn-sm" href="#"><i class="fa fa-plus mr-1"></i>Add New Post</a>


        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered" >
                <thead>
                  <tr>
                    <th class='deleteTh'>#</th>
                    <th>Action</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Company Posted</th>
                    <th>Date</th>
                    <th>Added By</th>
                  </tr>
                </thead>
                <tbody>
                  <?php

                  $society_master=$d->select("society_master");
                  $i = 1;
                  $society_array = array();
                  while($society_master_data=mysqli_fetch_array($society_master)) {
                    $society_array[$society_master_data['society_id']]  = $society_master_data['society_name'];
                  }
                  $post_log_master=$d->select("post_log_master");
                  $i = 1;
                  $post_array = array();
                  while($post_log_master_data=mysqli_fetch_array($post_log_master)) {
                    $post_array[$post_log_master_data['post_id']][]  = $post_log_master_data['society_id'];
                  }

                  $q=$d->select("bms_admin_master,timeline_master","bms_admin_master.admin_id =timeline_master.admin_id  "," ORDER BY timeline_master.`timeline_id` DESC");
                  $i = 1;
                  while($row=mysqli_fetch_array($q)) {
                    $post_id = $row['timeline_id'];
                    $active_status = $row['active_status'];
                        $totalCnt = $d->count_data_direct("post_id","post_log_master"," post_id ='$post_id' "); 
                    ?>
                    <tr>

                      <td><?php echo $i++; ?></td>
                      <td>
                          <?php
                        $buttonClass = ($active_status == "0") ? 'btn-success-new' : 'btn-danger';
                        $buttonCondition = ($active_status == "0") ? 'Active' : 'Deactive';
                        $status = ($active_status == "0") ? 'deavtivepost' : 'avtivepost';
                        $newStatus = ($active_status == "0") ? 'avtivepost' : 'deavtivepost';
                        $newStatusVal = ($active_status == "0") ? '1' : '0';
                        $statusValue = ($active_status == "0") ? '0' : '1';
                        ?>

                        <input type="button" class="btn btn-sm pl-1 pr-1 w-25 <?php echo $buttonClass ?>" id="<?php echo 'timeline_' . $post_id; ?>" onclick="changeStatusNew('<?php echo $post_id; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'timeline_' . $post_id; ?>','','1');" data-size="small" value="<?php echo $buttonCondition ?>" />
                        <?php if ($totalCnt==0) { ?>

                          <form class="d-inline-block" method="POST" action="editTimeline">
                            <input type="hidden" name="timeline_id" value="<?php echo $row['timeline_id']; ?>">
                            <button class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></button>
                          </form>

                          <form class="d-inline-block" method="POST" action="controller/newsFeedController.php">
                            <input type="hidden" name="deletepost">
                            <input type="hidden" name="timeline_id" value="<?php echo $row['timeline_id']; ?>">
                            <button class="btn btn-sm btn-danger form-btn"><i class="fa fa-trash-o"></i></button>
                          </form>

                        <?php } if($row['active_status']=="0"){ ?>

                          <a data-toggle="modal" data-timeline_id="<?php echo $row['timeline_id']; ?>" data-target="#publishPostModal" class="open-AddBookDialog btn btn-secondary btn-sm px-1" href="#"><i class="fa fa-bullhorn"></i>Publish</a>

                        <?php }  ?>

                      </td>
                      <td> <?php 
                      if($row['post_image'] !=""){

                        ?>

                        <a href="../img/post_timeline/<?php echo  $row['post_image'] ?>" data-fancybox="images" data-caption="Photo Name : <?php echo $row['post_image']; ?>">
                          <img src="../img/post_timeline/<?php echo  $row['post_image']; ?>" alt="<?php echo  $row['post_image']; ?>" class="lightbox-thumb img-thumbnail" style="width:150px !important;">
                        </a>

                        <?php } else{ echo "-";} ?>
                      </td>
                      <td><?php echo $row['timeline_text'] ?></td>
                      <td>
                        <?php 
                        
                        echo $totalCnt;
                        ?>
                      </td>
                      
                        <td><?php 
                        if ($default_time_zone!="Asia/Kolkata") {
                          echo $d->change_timezone($row["created_date"],$default_time_zone,'d M Y h:i A');
                        } else {
                          echo date("d M Y h:i A", strtotime($row['created_date'])); } ?></td>
                          <td><?php echo $row['admin_name'] ?></td>

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


    <div class="modal fade" id="publishPostModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content border-primary">
          <div class="modal-header bg-primary">
            <h5 class="modal-title text-white">Publish Post</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="publishTimelineFrm" action="" method="post" enctype="multipart/form-data">
              <input type="hidden" name="timeline_id" id="timeline_id" value="">
              <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" />
              <?php 
              $society_master_qry = $d->select("society_master");
              $sosa_cities = array();
              while ($society_master_data = mysqli_fetch_array($society_master_qry)) {
                $sosa_cities[] = $society_master_data['city_id'];
              }
              $sosa_cities = implode(",",  $sosa_cities);

              ?>
              <div class="form-group row">
                <label for="input-10" class="col-sm-2 col-form-label">Country <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                  <select type="text" required="" id="country_id" onchange="getStates();" class="form-control single-select" name="country_id">
                    <option value="">-- Select --</option>
                    <?php 
                    $qc=$d->select("countries","flag=1");
                  while ($cData=mysqli_fetch_array($qc)) {
                      ?>
                      <option value="<?php echo $cData['country_id'];?>"><?php echo $cData['name'];?></option>
                    <?php }?>
                  </select>
                </div>
              </div>

              <div class="form-group row">
                <label for="input-10" class="col-sm-2 col-form-label">State <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                  <select type="text" onchange="getCity();"  required="" class="form-control single-select" id="state_id" name="state_id">
                    <option value="">-- Select --</option>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label form-control-label">City <span class="required">*</span></label>
                <div class="col-sm-10">
                  <select class="form-control  single-select city_id_timeline" name="sosa_city_id"  id="city_id">
                    <option value="">--Select--</option>


                  </select>
                </div>

              </div> 
              <div id="sosa_detail">



              </div>
              <div id="chkError" class=""></div>




              <div class="form-footer text-center">
                <button type="submit" name="publishPost" value="publishPost" class="btn btn-sm btn-success"><i class="fa fa-check-square-o"></i> Publish</button>
              </div>

            </form> 
          </div>

        </div>
      </div>
    </div><!--End Modal -->

    <div class="modal fade" id="feed">
      <div class="modal-dialog">
        <div class="modal-content border-primary">
          <div class="modal-header bg-primary">
            <h5 class="modal-title text-white">Add News Feed</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="addTimelineFrm" action="controller/newsFeedController.php" method="post" enctype="multipart/form-data">


              <div class="form-group row">
                <label class="col-sm-3 col-form-label form-control-label">Title <span class="required">*</span></label>
                <div class="col-sm-9" id="">
                  <input class="form-control" required="" name="timeline_text"  id="timeline_text" type="text" value="" placeholder="Title" minlength="3" maxlength="500">

                </div>  
              </div> 

              <div class="form-group row">
                <label class="col-sm-3 col-form-label form-control-label">Image <span class="required">*</span></label>
                <div class="col-sm-9" id="">
                  <input required="" type="file" accept="image/*" name="image"  class="form-control-file border photoOnly">
                </div>
              </div>

              <div class="form-footer text-center">
                <button type="submit" name="addFeed" value="addFeed" class="btn btn-sm btn-success"><i class="fa fa-check-square-o"></i> Add</button>
              </div>

            </form> 
          </div>

        </div>
      </div>
    </div><!--End Modal -->



