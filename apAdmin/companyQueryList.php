<?php
$society_id = isset($_GET['society_id']) ? $d->sanitizeReportFilterIdAsInt($_GET['society_id']) : 0;
$is_whitelabel = isset($_GET['is_whitelabel']) ? (int)$_GET['is_whitelabel'] : 0;
$whitelabel_type = isset($_GET['whitelabel_type']) ? (int)$_GET['whitelabel_type'] : 0;
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-12">
        <h4 class="page-title">Company Query List</h4>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="reportTable" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Action</th>
                    <th>Id</th>
                    <th>Company Name</th>
                    <th>Client Name</th>
                    <th>Subject</th>
                    <th>Feedback DESCRIPTION</th>
                    <th>Platform</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Reopen Date</th>
                    <th>Created By</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $i=1;
                  $whitelabelTypeLabels = [
                    0 => 'MyCo',
                    1 => 'Smart Society',
                    2 => 'My Association',
                  ];
                  if ($is_whitelabel === 1) {
                    $q = $d->selectRow(
                      "bms_admin_master.*,feedback_master.*,bms_admin_master.platform as admin_platform,wl.society_name,c.name as city_name",
                      "feedback_master
LEFT JOIN bms_admin_master ON feedback_master.created_by=bms_admin_master.admin_id
LEFT JOIN society_master_white_label wl ON wl.society_id = feedback_master.society_id AND wl.project_type = feedback_master.whitelabel_type
LEFT JOIN cities c ON c.city_id = wl.city_id",
                      "feedback_master.society_id=$society_id AND COALESCE(feedback_master.is_whitelabel, 0) = 1 AND feedback_master.whitelabel_type='$whitelabel_type'",
                      "ORDER BY feedback_id DESC"
                    );
                  } else {
                    $q = $d->selectRow(
                      "bms_admin_master.*,society_master.*,feedback_master.*,bms_admin_master.platform as admin_platform",
                      "feedback_master LEFT JOIN bms_admin_master ON feedback_master.created_by=bms_admin_master.admin_id,society_master",
                      "feedback_master.society_id=$society_id AND COALESCE(feedback_master.is_whitelabel, 0) = 0 AND society_master.society_id=feedback_master.society_id $countryAppendQuerySociety",
                      "ORDER BY feedback_id DESC"
                    );
                  }
                  while($data=mysqli_fetch_array($q)){
                    extract($data);
                    $companyName = $society_name.'-'.$city_name;
                    if ((int)($is_whitelabel ?? 0) === 1) {
                      $typeLabel = $whitelabelTypeLabels[(int)($whitelabel_type ?? 0)] ?? 'Whitelabel';
                      $companyName = $society_name.'-'.$city_name.' ('.$typeLabel.' Whitelabel)';
                    }
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td>
                        <a href="feedbackTimeline?id=<?php echo (int)$data['feedback_id']; ?>" title="Details" class="btn btn-info btn-sm">View</a>
                      </td>
                      <td>#TKT<?php echo $data['feedback_id']; ?>&nbsp;&nbsp;<?php if($read_by==0){ ?><span class="badge badge-warning">New</span><?php } ?></td>
                      <td ><?php echo $companyName; ?></td>
                      <td class="tableWidth"><?php echo $name; ?></td>
                      <td class="tableWidth"><?php echo $subject; ?></td>
                      <td class="tableWidth"><?php echo $feedback_msg; ?></td>
                      <td class="tableWidth">
                        <?php
                        if($platform==0){
                          $platform_name = "Other";
                        }else if($platform==1){
                          $platform_name = "Android";
                        }else if($platform==2){
                          $platform_name = "iOS";
                        }else if($platform==3){
                          $platform_name = "Web";
                        }else if($platform==4){
                          $platform_name = "CRM";
                        }
                        echo $platform_name;
                        ?>
                      </td>
                      <td>
                        <?php 
                        if ($feedback_status==0 && $isTicket==0) {
                          echo "<span class='badge badge-warning'>Pending</span>";
                        } else if ($feedback_status==0 && $isTicket==1 && $is_reopen==1) {
                          echo "<span class='badge badge-warning'>Ticket Reopen</span>";
                        } else if ($feedback_status==0 && $isTicket==1 && $is_reopen==0) {
                          echo "<span class='badge badge-warning'>Ticket Generated</span>";
                        } else if ($feedback_status==0 && $isTicket==2) {
                          echo "<span class='badge badge-warning'>With Developer</span>";
                        } else if($feedback_status==1){
                          echo "<span class='badge badge-warning'>In Progress</span>";
                        } else if($feedback_status==2){
                          echo "<span class='badge badge-primary'>Solved</span>";
                        } else if($feedback_status==3){
                          echo "<span class='badge badge-warning'>On Hold</span>";
                        } else if($feedback_status==4){
                          echo "<span class='badge badge-warning'>Rejected</span>";
                        } else if($feedback_status==5){
                          echo "<span class='badge badge-warning'>Rejected</span>";
                        } else if($feedback_status==6){
                          echo "<span class='badge badge-danger'>Rejected By Developer</span>";
                        }
                        ?>
                      </td>
                      <td><?php echo $feedback_date_time;?></td>
                      <td><?php echo $reopen_date_time;?></td>
                      <td><?php if($created_by>0) {
                        echo $admin_name;
                      }else{
                        echo $d->app_name()." App";
                      }?>
                    </td>
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
<script src="assets/js/jquery.min.js"></script>