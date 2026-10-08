<?php
extract(array_map("test_input" , $_REQUEST));
error_reporting(0);

?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-9 col-5">
        <h4 class="page-title">Company Request Report</h4>
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
                    <th>ID</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Company</th>
                    <th>Employees</th>
                    <th>Email</th>
                    <th>Created Date</th>
                    <th>Source</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Close Remarks</th>
                    <?php if($role_id==1) { ?>
                      <th>Reply Message</th>
                    <?php } ?>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $i=1;
                  $q=$d->select("feedback_master","feedback_type=3 AND inquiry_type!=2","ORDER BY feedback_id DESC");

                  while ($data=mysqli_fetch_array($q)) {
                    extract($data);
                      // $forwardBy = $d->selectArray("bms_admin_master","admin_id='$forwarded_by'");
                    ?>
                    <tr>

                      <td><?php echo $i++; ?></td>
                      <td>FB_<?php echo $data['feedback_id']; ?>&nbsp;&nbsp;<?php if($read_by==0){ ?><span class="badge badge-warning">Un Read</span><?php } ?></td>

                      <td class="tableWidth"><?php echo $name;?></td>
                      <td class="tableWidth"><?php echo $country_code.' '.$mobile;?></td>
                      <td class="tableWidth"><?php echo $inquiry_company_name;?></td>
                      <td class="tableWidth"><?php echo $no_of_employee;?></td>
                      <td class="tableWidth"><?php echo $email;?></td>
                      <td class=""><?php echo $feedback_date_time;?></td>
                      <td class=""><?php if ($enquiry_from==2) {
                            echo "Landing Page";
                          } else  {
                            echo "App/Web";
                           }; ?></td>
                      <td>

                        <?php 
                        if ($inquiry_type==0) {
                        echo "-";
                      } if ($inquiry_type==1) {
                        echo "Sales";
                      }  if ($inquiry_type==2) {
                        echo "App Support";
                      }; ?>
                      </td>
                      <td>
                        <?php 
                        if ($feedback_status==0) {
                          echo "<span class='badge badge-warning'>Pending</span>";
                        } else if($feedback_status==1){
                          echo "<span class='badge badge-default'>In Progress</span>";
                        } else if($feedback_status==2){
                          echo "<span class='badge badge-success'>Solved</span>";
                        } else if($feedback_status==3){
                          echo "<span class='badge badge-warning'>On Hold</span>";
                        } else if($feedback_status==4){
                          echo "<span class='badge badge-danger'>Rejected</span>";
                        } else if($feedback_status==5){
                          echo "<span class='badge badge-success'>Closed by Developer</span>";
                        } else if($feedback_status==6){
                          echo "<span class='badge badge-danger'>Rejected by Developer</span>";
                        }
                        ?>

                      </td>
                      <td class="tableWidth"><?php echo $closing_remarks; ?></td>
                      <?php if($role_id==1) { ?>
                        <td class="tableWidth"><?php echo $client_reply_message; ?></td>
                      <?php } ?>
                    </tr>
                    <?php
                    } // end while loop
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
