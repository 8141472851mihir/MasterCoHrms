
<?php error_reporting(0);
  $bId = isset($_REQUEST['bId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['bId']) : 0;
  $dId = isset($_REQUEST['dId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['dId']) : 0;
  $uId = isset($_REQUEST['uId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['uId']) : 0;
  $currentYear = date('Y');
  $currentMonth = date('m');
  $nextYear = date('Y', strtotime('+1 year'));
  $onePreviousYear = date('Y', strtotime('-1 year'));
  $twoPreviousYear = date('Y', strtotime('-2 year'));
  $_GET['month_year'] = (isset($_REQUEST['month_year']) && $_REQUEST['month_year'] !== '')
      ? $d->sanitizeReportFilterMonthYear($_REQUEST['month_year'], date('Y-m'))
      : '';
  $from = $d->sanitizeReportFilterDate(isset($_GET['from']) ? $_GET['from'] : '', '');
  $toDate = $d->sanitizeReportFilterDate(isset($_GET['toDate']) ? $_GET['toDate'] : '', '');

  ?>
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumb-->
      <div class="row ">
        <div class="col-sm-3">
          <h4 class="page-title">Company Transaction Report</h4>
        </div>
        <div class="col-sm-9 d-none">
          <form action="" class="branchDeptFilter">
            <div class="row ">
              <!-- <div class="col-sm-3">
                <select type="text" id="years" class="form-control single-select" name="plan_expire">
                  <option value="">Select</option>
                  <option value="0" <?php if(isset($plan_expire) && $plan_expire==0) {echo "selected";} ?> >Expired</option>
                  <option value="30" <?php if(isset($plan_expire) && $plan_expire==30) {echo "selected";} ?> >Nearby Expired</option>
                  <option value="31" <?php if(isset($plan_expire) && $plan_expire==31) {echo "selected";} ?> >Not Expired</option>
                </select>
              </div> -->
              <div class="col-md-2 col-6 form-group">
                <input type="text" class="form-control" autocomplete="off" id="autoclose-datepickerFrom"  name="from" value="<?php echo htmlspecialchars($from); ?>">   
              </div>
              <div class="col-md-2 col-6 form-group">
                <input  type="text" class="form-control" autocomplete="off" id="autoclose-datepickerTo"  name="toDate" value="<?php echo htmlspecialchars($toDate); ?>">  
              </div>          
              <div class="col-md-3 form-group">
                <input class="btn btn-success btn-sm " type="submit" name="getReport" value="Get">
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
              <table id="reportTable" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Id</th>
                    <th>engagement call exe.</th>
                    <th>support exe.</th>
                    <th>Installation Status</th>
                    <th>Implementation Completion Date</th>
                    <th>Company Name</th>
                    <th>Implementation Executive</th>
                    <th>Implementation Executive Remarks</th>
                    <th>City</th>
                    <th>SPOC</th>
                    <th>SPOC Designation</th>
                    <th>SPOC Mobile</th>
                    <th>Leader Details</th>
                    <th>Reference</th>
                    <th>User Limit</th>
                    <th>Logged in</th>
                    <th>Free / Paid</th>
                    <th>Subsciption Fees</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Company URL</th>
                    <th>Last Call Date</th>
                    <th>Last Call Remarks</th>
                    <th>Next Followup Date</th>

                    <th>registered users count </th>
                    <th>current month attendance count</th>
                    <th>last month active tracking users count</th>
                    <th>second last month attendance count</th>
                    <th>current month payroll count </th>   
                    <th>last month payroll count </th>
                    <th>second last month payroll count</th>
                    <th>current month work report count</th>
                    <th>last month work report count</th>
                    <th>second last month work report count</th>

                    <th>current month circular count</th>   
                    <th>last month circular count</th>
                    <th>second last month circular count</th>

                    <th>total assets count</th>

                    <th>current month expense count</th>   
                    <th>last month expense count</th>
                    <th>second last month expense count</th>

                    <th>total users with admin view excess count</th>

                    <th>current month active tracking users count</th>   
                    <th>last month active tracking users count</th>
                    <th>second last month active tracking users count</th>

                    <th>total google visit count</th>
                    <th>current month google visit count</th>   
                    <th>last month google visit count</th>
                    <th>second last month google visit count</th>
                    <th>total documents count</th>

                    <th>current month assigned tasks count </th>   
                    <th>last month assigned tasks count </th>
                    <th>second last month assigned tasks count</th>
                    <th>current month no of visitors count </th>   
                    <th>last month no of visitors count</th>
                    <th>second last month no of visitors count</th>
                    <th>current month work from home(wfh) count </th>   
                    <th>last month work from home(wfh) count </th>
                    <th>second last month work from home(wfh) count</th>
                    <th>total current opening count </th>
                    <th>current month no of timeline count </th>   
                    <th>last month no of timeline count </th>
                    <th>second last month no of timeline count</th>
                    <th>current month events count</th>   
                    <th>last month events count </th>
                    <th>second last month events count </th>

                    <th>current month gallary count </th>   
                    <th>last month gallary count</th>
                    <th>second last month gallary count</th>
                    <th>current month penalty count</th>   
                    <th>last month penalty count</th>
                    <th>second last month penalty count</th>
                    <th>total no of vendors registered count (retailer)</th>
                    <th>current month no of sales order count (retailer)</th>   
                    <th>last month no of sales order count (retailer)</th>
                    <th>second last month no of sales order count (retailer)</th>            
                  </tr>
                </thead>
                <tfoot>
                <tr>
                  <th class="no-search-box"></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                </tr>
              </tfoot>
                <tbody>
                  <?php 
                  $i=1;
                  $q=$d->selectRow("society_analytics_master.*,society_master.*,society_master.created_date as society_created_date, CASE WHEN plan_expire_date < CURDATE() THEN 'YES' ELSE 'NO' END AS is_expired,server_master.server_name,server_master.server_ip,society_resent_analytics_master.*, IFNULL(trans_total.society_total, 0) AS society_total","society_analytics_master,society_master LEFT JOIN domain_master ON society_master.domain_id=domain_master.domain_id LEFT JOIN server_master ON server_master.server_id=domain_master.server_id LEFT JOIN society_resent_analytics_master ON society_master.society_id=society_resent_analytics_master.society_id LEFT JOIN (SELECT society_id, SUM(transection_amount) AS society_total FROM transection_master GROUP BY society_id) AS trans_total ON society_master.society_id = trans_total.society_id","society_master.society_id=society_analytics_master.society_id","order by society_analytics_master.analytics_id","");
                  while ($data=mysqli_fetch_array($q)) {
                    extract($data);
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo ''.$d->short_app_name().'_'.$society_id; ?></td>
                      <td></td>
                      <td><?php echo $support_name; ?></td>
                      <td></td>
                      <td></td>
                      <td><?php echo $society_name; ?></td>
                      <td><?php echo $implementation_name; ?></td>
                      <td></td>
                      <td><?php echo $city_name; ?></td>
                      <td><?php echo $secretary_name; ?></td>
                      <td></td>
                      <td><?php echo $country_code." ".$secretary_mobile; ?></td>
                      <td></td>
                      <td></td>
                      <td><?php echo $employee_registration_limit; ?></td>
                      <td><?php echo $total_login_user; ?></td>
                      <td><?php echo ($package_id=='' || $package_id=='0')?"Free" : "Paid"; ?></td>
                      <td><?php echo $society_total; ?></td>
                      <td><?php echo  (($society_created_date!='')? date('Y-m-d', strtotime($society_created_date)):"");
                      $society_created_date; ?></td>
                      <td><?php echo $plan_expire_date; ?></td>
                      <td><?php echo $sub_domain; ?></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td><?php echo $total_users; ?></td>
                      <td><?php echo $current_month_attendance_count; ?> </td>
                      <td><?php echo $last_month_attendance_count; ?></td>
                      <td><?php echo $second_last_month_attendance_count; ?></td>
                      <td><?php echo $current_month_payroll_count; ?></td>
                      <td><?php echo $last_month_payroll_count; ?></td>
                      <td><?php echo $second_last_month_payroll_count; ?></td>
                      <td><?php echo $current_month_work_report_count; ?></td>
                      <td><?php echo $last_month_work_report_count; ?> </td>
                      <td><?php echo $second_last_month_work_report_count; ?></td>
                      <td><?php echo $current_month_circular_count; ?></td>
                      <td><?php echo $last_month_circular_count; ?></td>
                      <td><?php echo $second_last_month_circular_count; ?></td>
                      <td><?php echo $total_assets; ?></td>
                      <td><?php echo $current_month_expense_count; ?></td>
                      <td><?php echo $last_month_expense_count; ?></td>
                      <td><?php echo $second_last_month_expense_count; ?></td>

                      <td><?php echo $admin_view_access; ?></td>

                      <td><?php echo $current_month_tracking_user_count; ?></td>
                      <td><?php echo $last_month_tracking_user_count; ?></td>
                      <td><?php echo $second_last_month_tracking_user_count; ?></td>
                      <td><?php echo $totalGoogleVisit; ?></td>
                      <td><?php echo $thisMonthGoogleVisit; ?></td>
                      <td><?php echo $preMonthGoogleVisit; ?></td>
                      <td><?php echo $second_last_month_google_visit_count; ?></td>
                      <td><?php echo $total_document; ?></td>
                      <td><?php echo $current_month_task_count; ?></td>
                      <td><?php echo $last_month_task_count; ?></td>
                      <td><?php echo $second_last_month_task_count; ?></td>
                      <td><?php echo $current_month_visitor_count; ?></td>
                      <td><?php echo $last_month_visitor_count; ?></td>
                      <td><?php echo $second_last_month_visitor_count; ?></td>
                      <td><?php echo $current_month_wfh_count; ?></td>
                      <td><?php echo $last_month_wfh_count; ?></td>
                      <td><?php echo $second_last_month_wfh_count; ?></td>
                      <td><?php echo $current_opening_count; ?></td>
                      <td><?php echo $current_month_timeline_count; ?></td>
                      <td><?php echo $last_month_timeline_count; ?></td>
                      <td><?php echo $second_last_month_timeline_count; ?></td>
                      <td><?php echo $current_month_event_count; ?></td>
                      <td><?php echo $last_month_event_count; ?></td>
                      <td><?php echo $second_last_month_event_count; ?></td>
                      <td><?php echo $current_month_gallery_count; ?></td>
                      <td><?php echo $last_month_gallery_count; ?></td>
                      <td><?php echo $second_last_month_gallery_count; ?></td>
                      <td><?php echo $current_month_penalty_count; ?></td>
                      <td><?php echo $last_month_penalty_count; ?></td>
                      <td><?php echo $second_last_month_penalty_count; ?></td>
                      <td><?php echo $total_registered_vendors; ?></td>
                      <td><?php echo $current_month_sales_order_count; ?></td>
                      <td><?php echo $last_month_sales_order_count; ?></td>
                      <td><?php echo $second_last_month_sales_order_count; ?></td>

                    </tr>
                    <?php 
                  } ?>
                </tbody>
              </table>
            </div>
            </div>
          </div>
        </div>
      </div><!-- End Row-->

    </div>
    <!-- End container-fluid-->
