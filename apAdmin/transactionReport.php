<?php

$bId = isset($_REQUEST['bId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['bId']) : 0;
$dId = isset($_REQUEST['dId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['dId']) : 0;
$uId = isset($_REQUEST['uId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['uId']) : 0;
$societyId = isset($_GET['society_id']) ? $d->sanitizeReportFilterIdAsInt($_GET['society_id']) : 0;
$currentYear = date('Y');
$currentMonth = date('m');
$nextYear = date('Y', strtotime('+1 year'));
$onePreviousYear = date('Y', strtotime('-1 year'));
$twoPreviousYear = date('Y', strtotime('-2 year'));
$_GET['month_year'] = (isset($_REQUEST['month_year']) && $_REQUEST['month_year'] !== '')
    ? $d->sanitizeReportFilterMonthYear($_REQUEST['month_year'], date('Y-m'))
    : '';

?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row ">
      <div class="col-sm-2">
        <h4 class="page-title">Company Transaction Report</h4>
      </div>
      <div class="col-sm-10">
        <form action="" class="branchDeptFilter">
          <div class="row ">
            <div class="col-md-3 form-group">
              <select class="form-control single-select" name="reg_type">
                <option value="all" <?php if (isset($_GET['reg_type']) && $_GET['reg_type'] === 'all') echo "selected"; ?>>All</option>
                <option value="0" <?php if (isset($_GET['reg_type']) && $_GET['reg_type'] === '0') echo "selected"; ?>>New Registration</option>
                <option value="1" <?php if (isset($_GET['reg_type']) && $_GET['reg_type'] === '1') echo "selected"; ?>>Renewal</option>
                <option value="2" <?php if (isset($_GET['reg_type']) && $_GET['reg_type'] === '2') echo "selected"; ?>>Trial Registration</option>
                <option value="3" <?php if (isset($_GET['reg_type']) && $_GET['reg_type'] === '3') echo "selected"; ?>>Extend</option>
                <option value="4" <?php if (isset($_GET['reg_type']) && $_GET['reg_type'] === '4') echo "selected"; ?>>Temporary Extension</option>
                <option value="5" <?php if (isset($_GET['reg_type']) && $_GET['reg_type'] === '5') echo "selected"; ?>>Limit Increase</option>
              </select>
            </div>

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
            <div class="col-md-3 form-group">
              <select class="form-control single-select" id="society_id" name="society_id">
                <option value="0" <?php echo ($societyId === 0) ? 'selected' : ''; ?>>All Companies</option>
                <?php
                $qsoc = $d->select("society_master", "1=1", "ORDER BY society_name ASC");
                while ($socData = mysqli_fetch_assoc($qsoc)) {
                  $selected = ($societyId > 0 && (int)$socData['society_id'] === $societyId) ? "selected" : "";
                  echo "<option value='" . (int)$socData['society_id'] . "' $selected>"
                    . htmlspecialchars($socData['society_name'])
                    . " (" . $d->short_app_name() . "_" . (int)$socData['society_id'] . ")"
                    . "</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-3 col-12 form-group">
              <input type="text" class="form-control" id="transactionDateRange" name="transaction_range" placeholder="Select date range" autocomplete="off" readonly
                value="<?php echo isset($_GET['transaction_range']) ? $_GET['transaction_range'] : ''; ?>">
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
              <table id="reportTable1" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Id</th>
                    <th>Company Name</th>
                    <th>City</th>
                    <th>Closure City</th>
                    <th>Mobile</th>
                    <th>Mode</th>
                    <th>Amount</th>
                    <th>Plan Name</th>
                    <th>Transaction Date</th>
                    <th>Received By</th>
                    <th>Perform By</th>
                    <th>Type</th>
                    <th>Old Tracking Limit</th>
                    <th>New Tracking Limit</th>
                    <th>Old Employee Limit</th>
                    <th>New Employee Limit</th>
                    <th>Old CRM Limit</th>
                    <th>New CRM Limit</th>
                    <th>Payment Attachment</th>
                  </tr>
                </thead>
                <tfoot class="bottom-footer">
                  <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="find-count"></th>
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
                  $where1 = "";
                  if (!empty($_GET['transaction_range'])) {
                    $range = explode(' - ', $_GET['transaction_range']);
                    if (count($range) == 2) {
                      $from = date('Y-m-d', strtotime($range[0]));
                      $toDate = date('Y-m-d', strtotime($range[1]));
                      $where1 .= " AND DATE_FORMAT(transection_master.transection_date,'%Y-%m-%d') BETWEEN '$from' AND '$toDate'";
                    }
                  }
                  if (isset($_GET['reg_type']) && $_GET['reg_type'] !== 'all') {
                    $regType = (int)$_GET['reg_type'];
                    $where1 .= " AND transection_master.is_renewal = '$regType'";
                  }
                  if ($societyId > 0) {
                    $where1 .= " AND transection_master.society_id = '$societyId'";
                  }
                  $q = $d->selectRow(
                    "transection_master.*, transection_master.payment_attachment as payment_attachment_plan, society_master.*, manage_plan.plan_value, manage_plan.plan_name",
                    "transection_master 
                   LEFT JOIN society_master ON transection_master.society_id = society_master.society_id
                   LEFT JOIN manage_plan ON manage_plan.plan_value = society_master.package_id",
                    "transection_master.society_id != 0 $where1",
                    "ORDER BY transection_master.transection_id DESC"
                  );

                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);

                    switch ($payment_mode) {
                      case '1':
                        $pMode = "Online Bank Transfer";
                        break;
                      case '2':
                        $pMode = "Cheque";
                        break;
                      case '3':
                        $pMode = "UPI";
                        break;

                      default:
                        $pMode = "Cash";
                        break;
                    }
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo '' . $d->short_app_name() . '_' . $society_id; ?></td>
                      <td><?php echo $society_name; ?></td>
                      <td><?php echo $city_name; ?></td>
                      <td><?php echo $closure_city; ?></td>
                      <td><?php echo $user_mobile; ?></td>
                      <td><?php echo ($is_renewal != 2) ? $pMode : ""; ?></td>
                      <td><?php echo ($is_renewal != 2) ? $transection_amount : ""; ?></td>
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
                        if ($default_time_zone != "Asia/Kolkata") {
                          echo $d->change_timezone($transection_date, $default_time_zone, 'Y-m-d');
                        } else {
                          echo $transection_date;
                        } ?>
                      </td>
                      <td> <?php echo $received_by; ?> </td>
                      <td> <?php echo $plan_changed_by; ?> </td>
                      <td>
                        <?php
                        if ($is_renewal == 1) {
                          echo 'Renewal';
                        } elseif ($is_renewal == 2) {
                          echo 'Trial Registration';
                        } elseif ($is_renewal == 3) {
                          echo 'Extend';
                        } elseif ($is_renewal == 4) {
                          echo 'Temporary Extension';
                        } elseif ($is_renewal == 5) {
                          echo 'Limit Increase';
                        } else {
                          echo 'New Registration';
                        }
                        ?>
                      </td>
                      <td><?php echo isset($old_tracking_limit) && $old_tracking_limit !== '' ? (int)$old_tracking_limit : '-'; ?></td>
                      <td><?php echo isset($new_tracking_limit) && $new_tracking_limit !== '' ? (int)$new_tracking_limit : '-'; ?></td>
                      <td><?php echo isset($old_employee_limit) && $old_employee_limit !== '' ? (int)$old_employee_limit : '-'; ?></td>
                      <td><?php echo isset($new_employee_limit) && $new_employee_limit !== '' ? (int)$new_employee_limit : '-'; ?></td>
                      <td><?php echo isset($old_crm_limit) && $old_crm_limit !== '' ? (int)$old_crm_limit : '-'; ?></td>
                      <td><?php echo isset($new_crm_limit) && $new_crm_limit !== '' ? (int)$new_crm_limit : '-'; ?></td>
                      <td class="text-center">
                          <?php if (!empty($payment_attachment_plan) && file_exists("../img/society_requests/" . $payment_attachment_plan)) { ?>
                              <a href="../img/society_requests/<?php echo $payment_attachment_plan; ?>" target="_blank" class="btn btn-sm btn-primary">
                                  View
                              </a>
                          <?php } else { ?>
                              <span>-</span>
                          <?php } ?>
                      </td>
                    </tr>
                  <?php
                  } ?>
                </tbody>
                <tfoot class="top-footer">
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
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div><!-- End Row-->

  </div>
  <!-- End container-fluid-->
  <script src="assets/js/jquery.min.js"></script>
  <script>
    $(function() {
      moment.locale('en');

      function initDateRangePicker(selector, defaultStart, defaultEnd) {
        $(selector).daterangepicker({
          startDate: defaultStart,
          endDate: defaultEnd,
          autoUpdateInput: false,
          locale: {
            format: 'MMMM DD, YYYY',
            applyLabel: "Apply",
            cancelLabel: "Cancel",
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: moment.months(),
            firstDay: 0
          },
          maxDate: moment(),
          maxSpan: {
            days: 400
          },
          ranges: {
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          }
        });

        $(selector).on('apply.daterangepicker', function(ev, picker) {
          $(this).val(picker.startDate.format('MMMM DD, YYYY') + ' - ' + picker.endDate.format('MMMM DD, YYYY'));
        });

        $(selector).on('cancel.daterangepicker', function() {
          $(this).val('');
        });
      }

      const rangeStr = "<?php echo isset($_GET['transaction_range']) ? addslashes($_GET['transaction_range']) : ''; ?>";
      const parts = rangeStr.split(' - ');
      const defaultStart = parts[0] ? moment(parts[0], 'MMMM DD, YYYY') : moment().startOf('month');
      const defaultEnd = parts[1] ? moment(parts[1], 'MMMM DD, YYYY') : moment().endOf('month');

      initDateRangePicker('#transactionDateRange', defaultStart, defaultEnd);
    });
  </script>