<?php
extract(array_map("test_input", $_REQUEST));
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pb-2">
      <div class="col-md-6 col-sm-12 align-self-end">
        <h4 class="page-title">Smart Society White Label Analytics Report</h4>
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
                    <th>White Label Smart Society Company Name</th>
                    <th>Smart Society Company Name</th>
                    <th>City Name</th>
                    <th>Total User</th>
                    <th>Login User</th>
                    <th>Android User</th>
                    <th>Ios User</th>
                    <th>Plan Expire Date</th>
                    <th>Fetch Date</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tfoot class="bottom-footer">
                  <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="find-count"></th>
                    <th class="find-count"></th>
                    <th class="find-count"></th>
                    <th class="find-count"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                </tfoot>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->selectRow("wla.society_name, wla.total_user, wla.login_user, wla.android_user, wla.ios_user, wla.plan_expire_date, wla.fetch_date, wla.created_at, wla.vendor_count, wl.society_name AS white_label_society_name,wla.city_name", "white_label_analytics_master AS wla INNER JOIN society_master_white_label AS wl ON wla.society_id = wl.society_id", "wla.fetch_month_year = '$selectedMonthAndYear' AND wla.project_type = 1");
                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);
                    $formatted_plan_expire_date = !empty($plan_expire_date) ? date('d-m-Y', strtotime($plan_expire_date)) : '';
                    $formatted_fetch_date = !empty($fetch_date) ? date('d-m-Y', strtotime($fetch_date)) : '';
                    $formatted_created_at = !empty($created_at) ? date('d-m-Y h:i A', strtotime($created_at)) : '';
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo $white_label_society_name; ?></td>
                      <td><?php echo $society_name; ?></td>
                      <td><?php echo $city_name; ?></td>
                      <td><?php echo $total_user; ?></td>
                      <td><?php echo $login_user; ?></td>
                      <td><?php echo $android_user; ?></td>
                      <td><?php echo $ios_user; ?></td>
                      <td><?php echo $formatted_plan_expire_date; ?></td>
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