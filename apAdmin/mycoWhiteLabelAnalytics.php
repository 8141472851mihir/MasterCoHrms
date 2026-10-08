<?php
extract(array_map("test_input", $_REQUEST));
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pb-2">
      <div class="col-md-6 col-sm-12 align-self-end">
        <h4 class="page-title">My Association White Label Analytics Report</h4>
      </div>
      <div class="col-md-6 col-sm-12">
        <form action="" method="get" accept-charset="utf-8" class="d-flex gap-3 justify-content-end">
          <?php
          $months = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
          ];
          $selectedMonth = $d->sanitizeReportFilterMonth(isset($_GET['month']) ? $_GET['month'] : '', date('m'));
          $selectedYear = $d->sanitizeReportFilterYear(isset($_GET['year']) ? $_GET['year'] : '', date('Y'));
          ?>
          <div class="col-md-6">
            <select name="month" id="month" onchange="this.form.submit()" class="form-control single-select" required>
              <option value="">-- Select --</option>
              <?php foreach ($months as $num => $name): ?>
                <option value="<?php echo $num; ?>" <?php if ($num == $selectedMonth)
                     echo 'selected'; ?>>
                  <?php echo $name; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <select name="year" id="year" onchange="this.form.submit()" class="form-control single-select" required>
              <option value="">-- Select --</option>
              <?php
              $currentYear = date('Y');
              for ($y = $currentYear - 2; $y <= $currentYear; $y++): ?>
                <option value="<?php echo $y; ?>" <?php if ($y == $selectedYear)
                     echo 'selected'; ?>>
                  <?php echo $y; ?>
                </option>
              <?php endfor; ?>
            </select>
          </div>
        </form>
        <?php
        if ($selectedMonth && $selectedYear) {
          $selectedMonthAndYear = $selectedMonth . "-" . $selectedYear;
        }
        ?>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="reportTable1" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>MyCo White Label Name</th>
                    <th>City name</th>
                    <th>Total User</th>
                    <th>Login User</th>
                    <th>Android User</th>
                    <th>Ios User</th>
                    <th>Admin User Count</th>
                    <th>Vendor Count</th>
                    <!-- <th>Plan Expire Date</th> -->
                    <th>Fetch Date</th>
                    <th>Created At</th>

                  </tr>
                </thead>
                <tfoot class="bottom-footer">
                  <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="find-count"></th>
                    <th class="find-count"></th>
                    <th class="find-count"></th>
                    <th class="find-count"></th>
                    <th class="find-count"></th>
                    <th class="find-count"></th>
                    <!-- <th></th> -->
                    <th></th>
                    <th></th>

                  </tr>
                </tfoot>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->selectRow("society_whitelable_analytics_master.*,society_master_white_label.*,cities.name AS analytics_city_name,society_master_white_label.created_date as society_created_date, CASE WHEN plan_expire_date < CURDATE() THEN 'YES' ELSE 'NO' END AS is_expired", "society_master_white_label,society_whitelable_analytics_master LEFT JOIN cities ON cities.city_id=society_whitelable_analytics_master.city_id", "society_master_white_label.society_id=society_whitelable_analytics_master.society_id AND society_whitelable_analytics_master.fetch_month_year = '$selectedMonthAndYear'", "order by society_whitelable_analytics_master.analytics_id", "");
                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);
                    $formatted_plan_expire_date = !empty($plan_expire_date) ? date('d-m-Y', strtotime($plan_expire_date)) : '';
                    $formatted_fetch_date = !empty($fetch_date) ? date('d-m-Y', strtotime($fetch_date)) : '';
                    $formatted_created_at = !empty($created_at) ? date('d-m-Y h:i A', strtotime($created_at)) : '';
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo $society_name; ?></td>
                      <td><?php echo $analytics_city_name; ?></td>
                      <td><?php echo $total_users; ?></td>
                      <td><?php echo $total_login_user; ?></td>
                      <td><?php echo $total_login_android; ?></td>
                      <td><?php echo $total_login_ios; ?></td>
                      <td><?php echo $admin_size; ?></td>
                      <td><?php echo $total_registered_vendors; ?></td>
                      <!-- <td><?php echo $formatted_plan_expire_date; ?></td> -->
                      <td><?php echo $formatted_fetch_date; ?></td>
                      <td><?php echo $formatted_created_at; ?></td>
                    </tr>
                  <?php } ?>
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
                    <!-- <th></th> -->
                    <th></th>
                    <th></th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>