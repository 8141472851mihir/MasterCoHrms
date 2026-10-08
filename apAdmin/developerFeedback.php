<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-12">
        <h4 class="page-title">Developer Feedback</h4>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <!-- <table id="reportTable" class="table table-bordered"> -->
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <!-- <th class="deleteTh">#</th> -->
                    <th>#</th>
                    <!-- <th>Action</th> -->
                    <th>Id</th>
                    <th>Company Name</th>
                    <th>Client Name</th>
                    <th>Subject</th>
                    <th>Feedback DESCRIPTION</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Issue Type</th>
                    <th>Created Date</th>
                    <th>Created By</th>
                    <th>Developer TAT</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i=1;
                  $q=$d->select("feedback_master LEFT JOIN bms_admin_master ON feedback_master.created_by=bms_admin_master.admin_id,society_master","society_master.society_id=feedback_master.society_id AND feedback_master.feedback_status!='2' AND feedback_master.with_developer=0 AND feedback_master.feedback_status >='5' $countryAppendQuerySociety","ORDER BY feedback_id DESC");
                  while($data=mysqli_fetch_array($q)){
                    extract($data);
                    ?>
                    <tr>
                      <!-- <td class='text-center'>
                        <?php if($role_id==1) { ?>
                          <input type="checkbox" class="multiDelteCheckbox"  value="<?php echo $data['feedback_id']; ?>">
                        <?php } ?>
                      </td> -->
                      <td><?php echo $i++; ?></td>
                      <!-- <td>
                        <form class="d-inline-block" action="feedbackTimeline" method="POST">
                          <input type="hidden" name="id" value="<?php echo $feedback_id; ?>">    
                          <button type="submit" title="Details" name="" class="btn btn-info btn-sm">View</button>
                        </form>
                      </td> -->
                      <td>#TKT<?php echo $data['feedback_id']; ?>&nbsp;&nbsp;<?php if($read_by==0){ ?><span class="badge badge-warning">New</span><?php } ?></td>
                      <td ><?php echo $society_name.'-'.$city_name; ?></td>
                      <td class="tableWidth"><?php echo $name; ?></td>
                      <td class="tableWidth"><?php echo $subject; ?></td>
                      <td class="tableWidth"><?php echo $feedback_msg; ?></td>
                      <td>
                        <?php
                        if($feedback_status==5){
                          echo "<span class='badge badge-success'>Closed by Developer</span>";
                        }elseif($feedback_status==6){
                          echo "<span class='badge badge-danger'>Rejected by Developer</span>";
                        }
                        ?>
                      </td>
                      <td class="tableWidth">
                        <?php
                        if($feedback_status==5){
                          echo $closing_remarks;
                        }elseif($feedback_status==6){
                          echo $reject_remarks;
                        }
                        ?>
                      </td>
                      <td class="tableWidth">
                          <?php  if($issueType==0){
                                    $IssueType = "-";
                                  }else if($issueType==1){
                                    $IssueType = "Bug";
                                  }else if($issueType==2){
                                    $IssueType = "Configuration Issue";
                                  }else if($issueType==3){
                                    $IssueType = "Training Issue";
                                  }else if($issueType==4){
                                    $IssueType = "Issue Not Found";
                                  }else if($issueType==8){
                                    $IssueType = "Not an issue";
                                  }else if($issueType==9){
                                    $IssueType = "Internet Connectivity Issue";
                                  }
                                  echo $IssueType;
                                   ?>                                                    
                      </td>
                      <td><?php echo $feedback_date_time;?></td>
                      <td><?php if($created_by>0) {
                        echo $admin_name;
                      }else{
                        echo $d->app_name()." App";
                      }?>
                    </td>
                    <?php
                    $dev_tat = "";
                    if($develeoper_assign_time!='' && $developer_solve_time!=''){
                      $time1 = new DateTime($develeoper_assign_time);
                      $time2 = new DateTime($developer_solve_time);
                      $dev_tat = $time1->diff($time2);
                    }
                    if($dev_tat != ''){ ?>
                      <td class="tableWidth"><?=$dev_tat->format('%m months %d days %h hours %i minutes')?></td>
                    <?php }else{ ?>
                      <td class="tableWidth"><?=$dev_tat->format('%m months %d days %h hours %i minutes')?></td>
                    <?php }
                    ?>
                  </tr>
                  <?php
                } ?> 
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>