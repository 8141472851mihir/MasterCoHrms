<?php
extract($_GET);
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-3">
        <h4 class="page-title">Reopen Query</h4>
      </div>
      <div class="col-sm-6">
        <form action="" method="get" accept-charset="utf-8">
          <div class="row">
            <div class="col-sm-6">
              <select type="text" required="" id="months" onchange="this.form.submit()" class="form-control single-select" name="months">
                <?php
                for($i=0;$i<=11;$i++){
                  $month = date("F", strtotime( date( 'Y-m-01' )." +$i months"));
                  ?>
                  <option value="<?=$month?>" <?php if(isset($months) && $months==$month) {echo "selected";} ?> ><?=$month?></option>
                  <?php
                }
                ?>
              </select>
            </div>
            <div class="col-sm-6">
              <select type="text" required="" id="years" onchange="this.form.submit()" class="form-control single-select" name="years">
                <?php
                for($i=0;$i<2;$i++)
                {
                  $year = date('Y', strtotime('-'.$i.' years'));
                  ?>
                  <option value="<?=$year?>" <?php if(isset($years) && $years==$year) {echo "selected";} ?> ><?=$year?></option>
                  <?php
                }
                ?>
              </select>
            </div>
          </div>
        </form>
      </div>
    </div>
    <?php
    if(isset($months) && !empty($months)){
      $nmonth = date('m',strtotime($months));
      $searchMonth =  $nmonth;
    }else{
      $searchMonth =  date('m');
    }
    if(isset($years) && !empty($years)){
      $searchyear =  $years;
    }else{
      $searchyear =  date('Y');
    }
    if(isset($months) && isset($years)){
      $date = $searchMonth."-".$searchyear;
      $whare = "AND feedback_master.reopen_date_time LIKE '%$date%'";
    }else{
      $date = date('m-Y');
      $whare = "AND feedback_master.reopen_date_time LIKE '%$date%'";
    }
    ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <!-- <table id="reportTable" class="table table-bordered"> -->
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Id</th>
                    <th>Company Name</th>
                    <th>Client Name</th>
                    <th>Subject</th>
                    <th>Feedback DESCRIPTION</th>
                    <th>Platform</th>
                    <th>Issue Type</th>
                    <th>Created Date</th>
                    <th>Reopen Date</th>
                    <th>Created By</th>
                    <th>Developer TAT</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                 
                  $i=1;
                  $q=$d->selectRow("bms_admin_master.*,society_master.*,feedback_master.*,bms_admin_master.platform as admin_platform","feedback_master LEFT JOIN bms_admin_master ON feedback_master.created_by=bms_admin_master.admin_id,society_master","society_master.society_id=feedback_master.society_id AND feedback_master.is_reopen='1' $whare $countryAppendQuerySociety","ORDER BY feedback_id DESC");
                  while($data=mysqli_fetch_array($q)){
                    extract($data);
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td>#TKT<?php echo $data['feedback_id']; ?>&nbsp;&nbsp;<?php if($read_by==0){ ?><span class="badge badge-warning">New</span><?php } ?></td>
                      <td ><?php echo $society_name.'-'.$city_name; ?></td>
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
                                  } else if($issueType==8){
                                    $IssueType = "Not an issue";
                                  } else if($issueType==9){
                                    $IssueType = "Internet Connectivity Issue";
                                  }
                                  echo $IssueType;
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
                    <td>
                      <?php
                      $dev_tat = "";
                      if($develeoper_assign_time!='' && $developer_solve_time!=''){
                        $time1 = new DateTime($develeoper_assign_time);
                        $time2 = new DateTime($developer_solve_time);
                        $dev_tat = $time1->diff($time2);
                      }
                      if($dev_tat!=""){
                        echo $dev_tat->format('%m months %d days %h hours %i minutes');
                      }
                      ?>
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