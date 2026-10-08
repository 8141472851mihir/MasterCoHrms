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
                  <h4 class="page-title">Company Plan Expire</h4>
              </div>
              <div class="col-sm-9">
                  <form action="" class="branchDeptFilter">
                      <div class="row ">
                          <!-- <div class="col-sm-3">
                <select type="text" id="years" class="form-control single-select" name="plan_expire">
                  <option value="">Select</option>
                  <option value="0" <?php if (isset($plan_expire) && $plan_expire == 0) {
                                        echo "selected";
                                    } ?> >Expired</option>
                  <option value="30" <?php if (isset($plan_expire) && $plan_expire == 30) {
                                            echo "selected";
                                        } ?> >Nearby Expired</option>
                  <option value="31" <?php if (isset($plan_expire) && $plan_expire == 31) {
                                            echo "selected";
                                        } ?> >Not Expired</option>
                </select>
              </div> -->
                          <div class="col-md-2 col-6 form-group">
                              <input type="text" class="form-control" autocomplete="off" id="autoclose-datepickerFrom" name="from" value="<?php echo htmlspecialchars($from); ?>">
                          </div>
                          <div class="col-md-2 col-6 form-group">
                              <input type="text" class="form-control" autocomplete="off" id="autoclose-datepickerTo" name="toDate" value="<?php echo htmlspecialchars($toDate); ?>">
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
                                          <th>Company Name</th>
                                          <th>City</th>
                                          <th>Rise Event</th>
                                          <th>Campaign Region</th>
                                          <th>Mobile</th>
                                          <th>Plan</th>
                                          <th>Days Left</th>
                                          <th>Plan Expire</th>
                                          <th>Ticket Size</th>
                                          <th>Yearly Ticket Size</th>
                                          <th>Per Employee Price</th>
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
                                      </tr>
                                  </tfoot>
                                  <tbody>
                                      <?php
                                        $i = 1;
                                        if (isset($from) && isset($from) && $toDate != '' && $toDate) {
                                            $where1 = " AND plan_expire_date BETWEEN '$from' AND '$toDate'";
                                        } else {
                                            $where1 = "";
                                        }

                                        $q = $d->selectRow(
                                            "society_master.*,manage_plan.plan_value,manage_plan.plan_name",
                                            "society_master LEFT JOIN manage_plan ON manage_plan.plan_value = society_master.package_id",
                                            "society_id!=0 $where $where1",
                                            "order by plan_expire_date ASC"
                                        );
                                        while ($data = mysqli_fetch_array($q)) {

                                            extract($data);
                                        ?>
                                          <tr>
                                              <td><?php echo $i++; ?></td>
                                              <td><?php echo '' . $d->short_app_name() . '_' . $society_id; ?></td>
                                              <td><?php echo $society_name; ?></td>
                                              <td><?php echo $city_name;; ?></td>
                                              <td><?php echo ($from_rise_event == '1') ? 'Yes' : 'No'; ?></td>
                                              <td><?php echo $region_name; ?></td>
                                              <td><?php echo $secretary_mobile; ?></td>
                                              <td>
                                                  <?php
                                                    if ($plan_value == 0) {
                                                        echo "Custome Plan";
                                                    } else {
                                                        echo $plan_name;
                                                    }
                                                    ?>
                                              </td>
                                              <td>
                                                  <?php
                                                    $now = time();
                                                    $your_date = strtotime("$plan_expire_date");
                                                    $datediff = $your_date - $now;
                                                    if ($datediff > 0) {
                                                        echo $todayTime = round($datediff / (60 * 60 * 24));
                                                    } else {
                                                        echo "Expired";
                                                    }
                                                    ?>
                                              </td>
                                              <td>
                                                  <?php
                                                    if ($default_time_zone != "Asia/Kolkata") {
                                                        echo $d->change_timezone($plan_expire_date, $default_time_zone, 'Y-m-d');
                                                    } else {
                                                        echo $plan_expire_date;
                                                    } ?>
                                              </td>
                                              <td><?php echo $received_ticket_size;; ?></td>
                                              <td><?php echo $yearly_ticket_size; ?></td>
                                              <td><?php echo $per_employee_price; ?></td>
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