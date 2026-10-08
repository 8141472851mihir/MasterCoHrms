<?php

if (isset($_POST['request_society_id_edit'])) {
  extract(array_map("test_input", $_POST));
  $q = $d->select("society_master_requests", "request_society_id='$request_society_id_edit'");
  $data = mysqli_fetch_array($q);
}
$dayName = date('l', strtotime('today'));
?>


<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-4">
        <h4 class="page-title">Add /Update (Company)</h4>

      </div>
      <?php
      $ongoing_patch = $d->count_data_direct("festival_id", "festival_master", "is_festival='1' AND ongoing_patch=1");
      $show_note = ($ongoing_patch > 0) ? "" : "d-none";
      ?>
      <div class="col-sm-8 text-danger text-right <?php echo $show_note; ?>">
        <p>Note: Ongoing patch, Please Wait. </p>
      </div>
    </div>


    <!-- Bulk Company Request - CSV Upload -->


    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="addSocietyRequestValidation" action="controller/societyRequestController.php" method="post"
              enctype="multipart/form-data">
              <h4 class="form-header text-uppercase">
                <i class="fa fa-building"></i>
                Company Information
              </h4>
              <div class="form-group row">
                <label class="form-label mx-3 h6">
                  Does company come during Rise event? <span class="text-danger">*</span>
                </label>
                <div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="from_rise_event" <?php echo (isset($data['from_rise_event']) && $data['from_rise_event'] == '1') ? "checked" : ""; ?> id="myco_yes" value="1" required>
                    <label class="form-check-label" for="myco_yes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="from_rise_event" <?php echo (isset($data['from_rise_event']) && $data['from_rise_event'] == '0') ? "checked" : ""; ?> id="myco_no" value="0">
                    <label class="form-check-label" for="myco_no">No</label>
                  </div>
                </div>
                <label id="from_rise_event-error" class="error" for="from_rise_event"></label>
              </div>




              <div class="form-group row">
                <label for="input-10" class="col-sm-2 col-form-label">Company Name <span
                    class="required">*</span></label>
                <div class="col-sm-10">
                  <?php if (isset($_POST['request_society_id_edit'])) { ?>
                    <input type="text" value="<?php echo $data['request_society_name']; ?>" autocomplete="off" required=""
                      class="form-control" id="input-10" name="society_name_edit">
                    <input type="hidden" value="<?php echo $data['request_society_id']; ?>" id="request_society_id_edit"
                      required="" class="form-control" id="input-10" name="request_society_id_edit">
                    <input type="hidden" value="<?php echo $data['request_society_logo']; ?>" required=""
                      class="form-control" id="input-10" name="society_logo_old">
                    <input type="hidden" value="<?php echo $data['payment_attachment']; ?>" required=""
                      class="form-control" id="input-10" name="payment_attachment_old">
                  <?php } else { ?>
                    <input type="text" value="<?php echo $data['request_society_name']; ?>" autocomplete="off" required=""
                      class="form-control" id="input-10" name="society_name">
                  <?php } ?>
                </div>

              </div>
              <div class="form-group row">
                <label for="account_type" class="col-sm-2 col-form-label"> Search With Company Code<span
                    class="required">*</span></label>
                <div class="col-sm-4 mt-2">
                  <?php if (isset($_POST['request_society_id_edit'])) { ?>
                    <div class="form-check-inline">
                      <label class="form-check-label">
                        <input type="radio" checked="" <?php if ($data['request_search_society_code'] == '0') {
                                                          echo "checked";
                                                        } ?> class="form-check-input" value="0" name="search_society_code_edit">No
                      </label>
                    </div>
                    <div class="form-check-inline">
                      <label class="form-check-label">
                        <input type="radio" <?php if ($data['request_search_society_code'] == '1') {
                                              echo "checked";
                                            } ?> class="form-check-input" value="1" name="search_society_code_edit">Yes
                      </label>
                    </div>
                  <?php } else { ?>
                    <div class="form-check-inline">
                      <label class="form-check-label">
                        <input type="radio" checked="" class="form-check-input" value="0" name="search_society_code">No
                      </label>
                    </div>
                    <div class="form-check-inline">
                      <label class="form-check-label">
                        <input type="radio" class="form-check-input" value="1" name="search_society_code">Yes
                      </label>
                    </div>
                  <?php } ?>
                </div>
                <label for="company_code" class="col-sm-2 col-form-label"> Company Code </label>
                <div class="col-sm-4">
                  <?php if (isset($_POST['request_society_id_edit'])) { ?>
                    <input type="text" maxlength="20" autocomplete="off" class="form-control" name="society_code_edit" value="<?php echo $data['request_society_code']; ?>">
                  <?php } else { ?>
                    <input type="text" maxlength="20" autocomplete="off" class="form-control" name="society_code" value="">
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <!-- Jainish Start -->
                <label for="account_type" class="col-sm-2 col-form-label"> Account Type <span
                    class="required">*</span></label>
                <div class="col-sm-4 mb-3">
                  <select type="text" required="" id="account_type" class="form-control" name="account_type">
                    <option value="0" <?php if (!isset($data['account_type']) || $data['account_type'] == 0) {
                                        echo "selected";
                                      } ?>>Normal Account</option>
                    <option value="1" <?php if (isset($data['account_type']) && $data['account_type'] == 1) {
                                        echo "selected";
                                      } ?>>Key Account</option>
                  </select>
                </div>
                <!-- Jainish End -->
                <label for="country_id" class="col-sm-2 col-form-label"> Country <span class="required">*</span></label>
                <div class="col-sm-4">

                  <select type="text" required="" id="country_id" onchange="getStates();"
                    class="form-control single-select" name="country_id">
                    <option value="">-- Select --</option>
                    <?php
                    $qc = $d->select("countries", "flag=1");
                    while ($cData = mysqli_fetch_array($qc)) {
                    ?>
                      <option <?php if ($data['request_country_id'] == $cData['country_id']) {
                                echo "selected";
                              } ?> value="<?php echo $cData['country_id']; ?>"><?php echo $cData['name']; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <label for="state_id" class="col-sm-2 col-form-label"> State <span class="required">*</span></label>
                <div class="col-sm-4">
                  <?php if (isset($_POST['request_society_id_edit'])) {

                  ?>
                    <select type="text" onchange="getCityAndUpdateDomains();" required="" class="form-control single-select" id="state_id" name="state_id">
                      <?php
                      $qs = $d->select("states", "country_id=$data[request_country_id]");
                      while ($sData = mysqli_fetch_array($qs)) {
                      ?>
                        <option <?php if (isset($data['request_state_id']) && $data['request_state_id'] == $sData['state_id']) {
                                  echo "selected";
                                } ?> value="<?php echo $sData['state_id']; ?>"><?php echo $sData['name']; ?>
                        </option>
                      <?php } ?>
                    </select>
                  <?php } else { ?>
                    <select type="text" onchange="getCityAndUpdateDomains();" required="" class="form-control single-select" id="state_id" name="state_id">
                      <option value="">-- Select --</option>
                    </select>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="input-101" class="col-sm-2 col-form-label"> City <span class="required">*</span></label>
                <div class="col-sm-4">
                  <?php if (isset($_POST['request_society_id_edit'])) {

                  ?>
                    <select type="text" required="" class="form-control single-select" id="city_id" name="city_id" onchange="getRecommendedDomain();">
                      <?php
                      $qcity = $d->select("cities", "state_id=$data[request_state_id]");
                      while ($cityData = mysqli_fetch_array($qcity)) {
                      ?>
                        <option <?php if (isset($data['request_city_id']) && $data['request_city_id'] == $cityData['city_id']) {
                                  echo "selected";
                                } ?> value="<?php echo $cityData['city_id']; ?>">
                          <?php echo $cityData['name']; ?></option>
                      <?php } ?>
                    </select>
                  <?php } else { ?>
                    <select type="text" required="" class="form-control single-select" name="city_id" id="city_id" onchange="getRecommendedDomain();">
                      <option value="">-- Select --</option>

                    </select>
                  <?php } ?>
                </div>
                <label for="input-101" class="col-sm-2 col-form-label"> Address <span class="required">*</span></label>
                <div class="col-sm-4">
                  <textarea required="" class="form-control" id="input-101"
                    name="society_address"><?php echo $data['request_society_address']; ?></textarea>
                  <?php if (isset($_POST['request_society_id_edit'])) { ?>
                    <input type="hidden" class="form-control" id="lat" name="society_latitude" placeholder="Lattitude"
                      value="<?php echo $data['request_society_latitude']; ?>">
                    <input type="hidden" class="form-control" id="lng" name="society_longitude"
                      placeholder="society_longitude" value="<?php echo $data['request_society_longitude']; ?>">
                  <?php } else { ?>
                    <input type="hidden" class="form-control" id="lat" name="society_latitude" placeholder="Lattitude"
                      value="23.0242625">
                    <input type="hidden" class="form-control" id="lng" name="society_longitude"
                      placeholder="society_longitude" value="72.5720625">
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="society_pincode" class="col-sm-2 col-form-label">Pincode </label>
                <div class="col-sm-4">
                  <input type="text" id="society_pincode" maxlength="6" value="<?php if ($data['request_society_pincode'] != '0') {
                                                                                  echo $data['request_society_pincode'];
                                                                                } ?>" class="form-control onlyNumber"
                    name="society_pincode">
                </div>

                <label for="industry_type" class="col-sm-2 col-form-label">Industry Type <span
                    class="required">*</span></label>
                <div class="col-sm-4">
                  <select type="text" id="industry_type" required class="form-control single-select"
                    name="industry_type">
                    <option value=""> Select Industry Type</option>
                    <?php $qi = $d->select("business_entity_master", "business_entity_master.status='0'", "ORDER BY name ASC");
                    while ($iData = mysqli_fetch_array($qi)) { ?>
                      <option <?php if ($iData['b_id'] == $data['industry_type']) {
                                echo 'selected';
                              } ?> value="<?php echo $iData['b_id']; ?>"> <?php echo $iData['name']; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label for="secretary_mobile" class="col-sm-2 col-form-label">Admin Name <span
                    class="required">*</span></label>
                <div class="col-sm-4">
                  <?php if (isset($_POST['request_society_id_edit'])) { ?>
                    <input type="text" maxlength="30" autocomplete="off" required="" class="form-control onlyName"
                      name="secretary_name" value="<?php echo $data['request_secretary_name'] ?>">
                  <?php } else { ?>
                    <input type="text" maxlength="30" value="" autocomplete="off" required=""
                      class="form-control onlyName" name="secretary_name">
                  <?php } ?>
                </div>
                <label for="secretary_mobile" class="col-sm-2 col-form-label">Currency<span
                    class="required">*</span></label>
                <div class="col-sm-4">

                  <input type="text" maxlength="4" autocomplete="off" required="" class="form-control" name="currency"
                    value="<?php if ($data['currency'] != '') {
                              echo $data['currency'];
                            } else {
                              echo '₹';
                            } ?>">

                </div>
              </div>
              <div class="form-group row">
                <label for="secretary_mobile" class="col-sm-2 col-form-label">Admin Mobile <span
                    class="required">*</span></label>
                <div class="col-lg-2">
                  <input type="hidden" value="<?php echo $data['country_code']; ?>" id="country_code_get" name="">
                  <select name="country_code" class="form-control single-select" id="country_code" required="">
                    <?php include 'country_code_option_list.php'; ?>
                  </select>
                </div>
                <div class="col-sm-2">
                  <?php if (isset($_POST['request_society_id_edit'])) { ?>
                    <input type="text" id="secretary_mobile" maxlength="13"
                      value="<?php echo $data['request_secretary_mobile']; ?>" required="" class="form-control"
                      name="secretary_mobile">
                    <input type="hidden" id="secretary_mobile_old" maxlength="13"
                      value="<?php echo $data['request_secretary_mobile']; ?>" required="" class="form-control">
                  <?php } else { ?>
                    <input type="text" id="secretary_mobile" maxlength="12"
                      value="<?php echo $data['request_secretary_mobile']; ?>" required="" class="form-control"
                      name="secretary_mobile">
                  <?php } ?>
                </div>
                <label for="input-13" class="col-sm-2 col-form-label">Admin Email <span
                    class="required">*</span></label>
                <div class="col-sm-4">
                  <?php if (isset($_POST['request_society_id_edit'])) { ?>
                    <input type="email" maxlength="80" id="secretary_email" required=""
                      value="<?php echo $data['request_secretary_email']; ?>" class="form-control" id="input-13"
                      name="secretary_email">
                    <input type="hidden" maxlength="80" id="secretary_email_old" required=""
                      value="<?php echo $data['request_secretary_email']; ?>" class="form-control" id="input-13">
                  <?php } else { ?>
                    <input type="email" maxlength="80" id="secretary_email" required=""
                      value="<?php echo $data['request_secretary_email']; ?>" class="form-control" id="input-13"
                      name="secretary_email">
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="domain_select" class="col-sm-2 col-form-label">Company Base URL <span class="required">*</span></label>
                <div class="col-sm-4">
                  <?php
                  $existingSubDomain = isset($data['request_sub_domain']) ? trim($data['request_sub_domain']) : '';
                  $parsedDomainName = '';
                  $parsedEndName = '';
                  if (!empty($existingSubDomain)) {
                    $existingSubDomain = rtrim($existingSubDomain, '/');
                    $lastSlashPos = strrpos($existingSubDomain, '/');
                    if ($lastSlashPos !== false) {
                      $parsedDomainName = substr($existingSubDomain, 0, $lastSlashPos + 1);
                      $parsedEndName = substr($existingSubDomain, $lastSlashPos + 1);
                    } else {
                      $parsedDomainName = $existingSubDomain;
                    }
                  }
                  ?>
                  <select id="domain_select" class="form-control single-select" name="domain_master_name" required>
                    <option value="">-- Select Domain --</option>
                    <?php
                    // Get all available domains (not filtered by city)
                    $existingDomainFound = false;
                    $qdom = $d->select("domain_master", "domain_active_status=0", "ORDER BY domain_name ASC");

                    while ($dm = mysqli_fetch_array($qdom)) {
                      $dn = $dm['domain_name'];
                      $selected = ($parsedDomainName == $dn) ? 'selected' : '';
                      if ($selected == 'selected') {
                        $existingDomainFound = true;
                      }
                      echo "<option value='" . htmlspecialchars($dn, ENT_QUOTES) . "' $selected>" . htmlspecialchars($dn) . "</option>";
                    }

                    if (!empty($parsedDomainName) && !$existingDomainFound) {
                      $existingDomainQuery = $d->select("domain_master", "domain_name='" . addslashes($parsedDomainName) . "' AND domain_active_status=0");
                      if (mysqli_num_rows($existingDomainQuery) > 0) {
                        echo "<option value='" . htmlspecialchars($parsedDomainName, ENT_QUOTES) . "' selected>" . htmlspecialchars($parsedDomainName) . " (Current)</option>";
                      }
                    }
                    ?>
                  </select>
                  <small id="full_url_preview1" class="form-text text-muted mt-1"></small>
                </div>
                <label for="end_url_name" class="col-sm-2 col-form-label">End URL Name <span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" id="end_url_name" autocomplete="off" maxlength="27" value="<?php echo htmlspecialchars($parsedEndName); ?>" required class="form-control" name="end_url_name">
                </div>
                <input type="hidden" id="sub_domain_full" name="sub_domain" value="<?php echo htmlspecialchars($existingSubDomain); ?>">
                <div class="col-sm-10" id="full_url_preview2"></div>
              </div>

              <div class="form-group row">
                <?php if (isset($_POST['request_society_id_edit'])) { ?>
                  <input type="hidden" min="0" max="100" maxlength="3" class="form-control" id="trlDays"
                    value="<?php echo $data['request_trial_days']; ?>" name="trial_days_old">
                  <input type="hidden" min="0" max="100" maxlength="3" class="form-control" id="package_id"
                    value="<?php echo $data['request_package_id']; ?>" name="package_id">
                  <input type="hidden" class="form-control" value="<?php echo $data['request_plan_expire_date']; ?>"
                    name="plan_expire_date_old">
                  <?php if ($data['request_package_id'] == 0) { ?>

                    <label for="input-12" class="col-sm-2 col-form-label">Trial Plan <span class="required">*</span></label>
                    <div class="col-sm-4">

                      <select required="" name="package_id" class="form-control check" id="input-14">
                        <option value="0">Trial</option>
                      </select>
                    </div>
                    <label for="input-13" class="col-sm-2 col-form-label">Trial Days <span class="required">*</span></label>
                    <div class="col-sm-4" id="TrialDiv2">
                      <input type="text" min="0" max="100" maxlength="3" class="form-control" id="trlDays"
                        value="<?php echo $data['request_trial_days']; ?>" name="trial_days">

                    </div>
                  <?php } else { ?>
                    <label for="plan_expire_date" class="col-sm-2 col-form-label">Plan Expire Date <span
                        class="required">*</span></label>
                    <div class="col-sm-4 ">
                      <input type="text" readonly="" id="plan_expire_date" maxlength="120" required=""
                        value="<?php echo $data['request_plan_expire_date']; ?>" class="form-control facility_datepicker"
                        name="plan_expire_date">
                    </div>


                  <?php }
                } else { ?>
                  <label for="input-12" class="col-sm-2 col-form-label">Plan <span class="required">*</span></label>
                  <div class="col-sm-4">
                    <select required name="package_id" class="form-control single-select check" id="input-14">
                      <option value="">-- Select Plan --</option>
                      <?php
                      $planQuery = $d->select("manage_plan", "status = 0");
                      $selectedPlanId = isset($existingPlanId) ? $existingPlanId : '';

                      if (mysqli_num_rows($planQuery) > 0) {
                        while ($plan = mysqli_fetch_array($planQuery)) {

                          $isSelected = ($plan['plan_value'] == $selectedPlanId) ? 'selected' : '';
                          echo "<option value='" . $plan['plan_value'] . "' $isSelected>" . htmlspecialchars($plan['plan_name']) . "</option>";
                        }
                      } else {
                        echo "<option value=''>No Plans Available</option>";
                      }
                      ?>
                    </select>
                  </div>
                  <label for="plan_expire_date" class="col-sm-2 col-form-label plan_expire_date">Plan Expire Date <span class="required">*</span></label>
                  <div class="col-sm-4 plan_expire_date_div">
                    <input type="text" readonly="" id="plan_expire_date" maxlength="120" value="" required=""
                      class="form-control facility_datepicker" name="plan_expire_date">
                  </div>

                  <label id="TrialDiv1" for="input-13" class="col-sm-2 mt-2 col-form-label">Trial Days <span
                      class="required">*</span></label>
                  <div class="col-sm-4" id="TrialDiv2">
                    <input type="text" min="0" max="100" maxlength="3" class="form-control mt-2" id="trlDays"
                      value="<?php echo $data['request_trial_days']; ?>" autocomplete="off" name="trial_days">

                  </div>
                <?php } ?>
              </div>
              <div class="form-group row">
                <label id="emp_registration_limit" for="employee_registration_limit"
                  class="col-sm-2 col-form-label">Employee Registration Limit <span class="required">*</span></label>
                <div class="col-sm-4" id="emp_registration_limit">
                  <input type="text" class="form-control onlyNumber" id="employee_registration_limit"
                    value="<?php echo $data['employee_registration_limit']; ?>" autocomplete="off"
                    name="employee_registration_limit">
                </div>

                <label for="expected_team_size" class="col-sm-2 col-form-label">Expected Team Size </label>
                <div class="col-sm-4">
                  <input type="text" class="form-control onlyNumber" id="expected_team_size"
                    value="<?php echo $data['expected_team_size']; ?>" autocomplete="off" name="expected_team_size"
                    maxlength="6">
                </div>
              </div>
              <div class="form-group row">

                <label id="emp_tracking_limit" for="employee_tracking_limit" class="col-sm-2 col-form-label">Employee
                  Tracking Limit <span class="required">*</span></label>
                <div class="col-sm-4" id="emp_tracking_limit">
                  <input type="text" class="form-control onlyNumber" id="employee_tracking_limit"
                    value="<?php echo $data['employee_tracking_limit']; ?>" autocomplete="off"
                    name="employee_tracking_limit">
                </div>
                <label id="per_emp_price" for="input-13" class="col-sm-2 col-form-label">Yearly ticket size</label>
                <div class="col-sm-4" id="per_emp_price">
                  <input type="text" maxlength="10" class="form-control onlyNumber"
                    value="<?php echo $data['yearly_ticket_size']; ?>" autocomplete="off" name="yearly_ticket_size">
                </div>

              </div>
              <div class="form-group row">
                <label id="per_emp_price" for="input-13" class="col-sm-2 col-form-label">Received ticket size</label>
                <div class="col-sm-4" id="per_emp_price">
                  <input type="text" maxlength="10" class="form-control onlyNumber"
                    value="<?php echo $data['received_ticket_size']; ?>" autocomplete="off" name="received_ticket_size">
                </div>
                <label id="per_emp_price" for="per_employee_price" class="col-sm-2 col-form-label">Per Employee Price
                  <span class="required">*</span></label>
                <div class="col-sm-4" id="per_emp_price">
                  <input type="text" class="form-control onlyNumber" id="per_emp_price"
                    value="<?php echo $data['per_employee_price']; ?>" autocomplete="off" required
                    name="per_employee_price">
                </div>


              </div>

              <div class="form-group row">
                <label id="sales_personName" for="input-13" class="col-sm-2 col-form-label">Sales Person Name <span
                    class="required">*</span></label>
                <div class="col-sm-4" id="sales_personName">
                  <input type="text" class="form-control" id="sales_personName"
                    value="<?php echo $data['sales_person_name']; ?>" autocomplete="off" name="sales_person_name">
                </div>
                <label id="sales_personName" for="input-13" class="col-sm-2 col-form-label">Implementation Executive Name <span
                    class="required">*</span></label>
                <div class="col-sm-4" id="sales_personName">
                  <select name="implementation_name" class="form-control single-select" id="implementation_name"
                    required="">
                    <?php
                    echo "<option value='' selected disabled>-- Select Admin --</option>";
                    $result = $d->select("bms_admin_master", "");
                    while ($row = mysqli_fetch_array($result)) {
                      $is_selected = ($row['admin_name'] == $data['implementation_name']) ? 'selected' : "";
                      echo "<option $is_selected value='{$row['admin_name']}'>{$row['admin_name']}</option>";
                    }
                    ?>
                  </select>
                </div>

              </div>

              <div class="form-group row">
                <label id="sales_personName" for="sales_closure_date" class="col-sm-2 col-form-label">Sales Closure Date
                  <span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" id="autoclose-datepicker-futureDateRestricted"
                    value="<?php echo $data['sales_closure_date']; ?>" name="sales_closure_date"
                    placeholder="Sales Closure Date" readonly required>
                </div>
                <label id="support_name" for="support_name" class="col-sm-2 col-form-label">Support Person Name <span
                    class="required">*</span></label>
                <div class="col-sm-4" id="support_name">

                  <select name="support_name" class="form-control single-select" id="support_name" required onchange="getSupportPersonContact(this.value)">
                    <option value="">-- Select Support Person --</option>
                    <?php
                    mysqli_data_seek($result, 0);
                    while ($row = mysqli_fetch_array($result)) {
                      $is_selected = ($row['admin_name'] == $data['support_name']) ? 'selected' : "";
                      echo "<option $is_selected value='{$row['admin_name']}'>{$row['admin_name']}</option>";
                    }
                    ?>
                  </select>

                </div>
              </div>
              <div class="form-group row">
                <label for="support_mobile_no" class="col-sm-2 col-form-label">Support Person Mobile <span
                    class="required">*</span></label>
                <div class="col-lg-2">
                  <input type="hidden" value="<?php echo $data['support_country_code']; ?>" id="country_code_get"
                    name="">
                  <select name="support_country_code" class="form-control single-select" id="support_country_code"
                    required="">
                    <?php include 'country_code_option_list.php'; ?>
                  </select>
                </div>
                <div class="col-sm-2">
                  <input type="text" id="support_mobile_no" maxlength="13"
                    value="<?php echo ($data['support_mobile_no'] != '0') ? $data['support_mobile_no'] : ""; ?>"
                    class="form-control onlyNumber" name="support_mobile_no" />
                </div>
                <label id="referenceFrom" for="input-13" class="col-sm-2 col-form-label">Reference From</label>
                <div class="col-sm-4" id="referenceFrom">
                  <input type="text" class="form-control " id="referenceFrom"
                    value="<?php echo $data['reference_from']; ?>" autocomplete="off" name="reference_from">
                </div>


              </div>

              <div class="form-group row">
                <label for="input-6" class="col-sm-2 col-form-label">Lead Sources <span class="required">*</span></label>
                <div class="col-sm-4">
                  <select class="form-control single-select" required name="lead_sources">
                    <option value="">-- Select --</option>

                    <?php 
                    $leadSources = [
                      1 => 'Meta',
                      2 => 'Inbound',
                      3 => 'Walk IN',
                      4 => 'Cold Data',
                      5 => 'BA / Director Reference',
                      6 => 'BNI Reference',
                      7 => 'Event - Exhibitor',
                      8 => 'Event - Exhibitor ( Exhibitor Cards )',
                      9 => 'Event - Exhibitor ( Visitor Cards )',
                      10 => 'Event - Industry Specific',
                      11 => 'Event - Networking',
                      12 => 'Existing Client',
                      13 => 'Nikseam BPO',
                      14 => 'Old Lead ( Any Source)',
                      15 => 'Personal Reference',
                      16 => 'Reference from demo client',
                      17 => 'Reference from existing client',
                      18 => 'Reference from Implementation team',
                      19 => 'Rise',
                      20 => 'Tech Imply',
                      21 => 'Tech Jockey',
                      22 => 'Website / Landing Page',
                      23 => 'Website / Landing Page / ChatBot /MyCo App',
                      24 => 'Whatsapp Bulkshoot'
                    ];
                    foreach ($leadSources as $value => $label) { ?>
                      <option value="<?php echo $value; ?>"
                        <?php echo ($data['requests_lead_sources'] == $value) ? 'selected' : ''; ?>>
                        <?php echo $label; ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
                <label for="input-7" class="col-sm-2 col-form-label">Campaign Region <span class="required">*</span></label>
                <div class="col-sm-4">
                  <select class="form-control single-select" name="region" required>
                    <option value="">-- Select --</option>
                    <?php
                    $cacheFile = '../img/cacheBlocks.json';
                    $todayDate = date('Y-m-d');
                    $callApi = true;
                    $data1 = [];

                    if (file_exists($cacheFile)) {
                      $cacheContent = json_decode(file_get_contents($cacheFile), true);
                      if (isset($cacheContent['cache_date']) && $cacheContent['cache_date'] === $todayDate) {
                        $data1 = $cacheContent['data'];
                        $callApi = false;
                      }
                    }

                    if ($callApi) {
                      $curl = curl_init();
                      $target_url = "https://ahmedabad.my-company.app/fincasys/residentApiNew/societyAnalytics.php";
                      // $target_url = 'http://192.168.5.189/MyCompanyWeb/residentApiNew/societyAnalytics.php';

                      curl_setopt_array($curl, array(
                        CURLOPT_URL => $target_url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => array('getBlocks' => 'getBlocks'),
                        CURLOPT_HTTPHEADER => array(
                          'key: ' . $keydb
                        ),
                      ));

                      $response = curl_exec($curl);
                      curl_close($curl);

                      $apiData = json_decode($response, true);
                      if ($apiData['status'] == 200) {
                        if (isset($apiData['block']) && is_array($apiData['block'])) {
                          $cacheToSave = [
                            'cache_date' => $todayDate,
                            'data' => $apiData
                          ];
                          file_put_contents($cacheFile, json_encode($cacheToSave));
                          $data1 = $apiData;
                        }
                      }
                    }

                    if (isset($data1['block']) && is_array($data1['block'])) {
                      foreach ($data1['block'] as $resondata) {
                    ?>
                        <option <?php echo ($data['request_region_name'] == $resondata['block_name']) ? 'selected' : ''; ?> value="<?php echo $resondata['block_name']; ?>">
                          <?php echo $resondata['block_name']; ?>
                        </option>
                    <?php
                      }
                    } else {
                      echo "<option disabled>No branch found</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>

              <div class="form-group row">
                <label for="input-6" class="col-sm-2 col-form-label">Year Type <span class="required">*</span></label>
                <div class="col-sm-4">
                  <select id="input-6" value="<?php echo $pan_number; ?>" class="form-control single-select"
                    name="calender_type">
                    <option <?php echo ($data['calender_type'] == 0) ? 'selected' : ''; ?> value="0">Calendar Year (1st
                      Jan to 31st Dec)</option>
                    <option <?php echo ($data['calender_type'] == 1) ? 'selected' : ''; ?> value="1">Financial Year (1st
                      Apr to 31st March)</option>
                  </select>
                </div>
              </div>
              <!-- mihir Changes Oct-1-2024 -->
              <div class="form-group row">
                <label id="referenceFrom" for="input-13" class="col-sm-2 col-form-label">Company Priority</label>
                <div class="col-sm-4 d-inline-flex" id="referenceFrom">
                  <div class="slider ">
                    <div id="output" class="output "> 50 </div>
                    <input class="" type="range" min="1" max="10"
                      value="<?php echo (isset($data['society_rating']) && $data['society_rating'] != '') ? $data['society_rating'] : '5' ?>"
                      step="1" name="society_rating" id="range">
                  </div>
                </div>
              </div>



              <!-- mihir Changes Oct-1-2024 -->
              <div class="form-group row" id="PaymentDiv">
                <label for="input-12" class="col-sm-2 col-form-label">Payment Status <span
                    class="required">*</span></label>
                <div class="col-sm-4">
                  <select class="form-control paymentSelect" name="amountReceivedType">
                    <!-- <option <?php if (isset($request_society_id_edit) && $data['payment_status'] == 0) {
                                    echo "selected";
                                  } ?> value="0">Not Received</option> -->
                    <option <?php if (isset($request_society_id_edit) && $data['payment_status'] == 1) {
                              echo "selected";
                            } ?> value="1">Received</option>
                  </select>
                </div>
                <label for="input-12" id="amoutLable" class="col-sm-2 col-form-label">Received Amount <span
                    class="required">*</span></label>
                <div class="col-sm-4" id="amoutInput">
                  <input type="text" id="working_days" name="amountReceived" autocomplete="0"
                    class="form-control onlyNumber" value="<?php echo $data['payment_amount'] ?>" maxlength="13"
                    minlength="1">
                </div>
                <label for="input-12" id="amoutLable" class="col-sm-2 mt-2 col-form-label">Payment Mode <span
                    class="required">*</span></label>
                <div class="col-sm-4 mt-2" id="amoutInput">
                  <select required name="payment_mode" autocomplete="0" class="form-control" minlength="1">
                    <option value="">-- Select --</option>
                    <option <?php if ($data['payment_mode'] == 1) {
                              echo 'selected';
                            } ?> value="1">Online Bank Transfer
                    </option>
                    <option <?php if ($data['payment_mode'] == 2) {
                              echo 'selected';
                            } ?> value="2">Cheque</option>
                    <option <?php if ($data['payment_mode'] == 3) {
                              echo 'selected';
                            } ?> value="3">UPI</option>
                    <option <?php if ($data['payment_mode'] == 4) {
                              echo 'selected';
                            } ?> value="4">Cash</option>
                  </select>
                </div>
                <label for="input-12" id="amoutLable" class="col-sm-2 mt-2  col-form-label">Payment Attachment</label>
                <div class="col-sm-4 mt-2 " id="payment_attachment">
                  <input type="file" accept="image/*,.pdf" id="payment_attachment" name="payment_attachment"
                    class="form-control" value="<?php echo $data['payment_attachment'] ?>">
                </div>
                <label for="input-12" id="amoutLable" class="col-sm-2 mt-2  col-form-label">Company GST/Tax Number
                </label>
                <div class="col-sm-4 mt-2 " id="gst_number">
                  <input type="text" id="gst_number" name="gst_number" autocomplete="0"
                    class="form-control text-uppercase" maxlength="24" value="<?php echo $data['gst_number'] ?>"
                    maxlength="13" minlength="1">
                </div>
              </div>
              <div class="form-group row">
                <label id="trainingTypeLabel" for="trainingType" class="col-sm-2 col-form-label">Training Type</label>
                <div class="col-sm-4" id="trainingTypeDropdown">
                  <select name="training_type" class="form-control single-select" id="training_type">
                    <option value="1" <?php if (!isset($data['training_type']) || $data['training_type'] == '1') {
                                        echo 'selected';
                                      } ?>>Online</option>
                    <option value="0" <?php if (isset($data['training_type']) && $data['training_type'] == 0) {
                                        echo 'selected';
                                      } ?>>Offline</option>
                  </select>
                </div>
              </div>
              <!-- Remark Textarea (Initially hidden) -->

              <div
                class="form-group row <?php echo (isset($data['training_type']) && $data['training_type'] == 0) ? '' : 'd-none'; ?>"
                id="remarkField">
                <label for="input-10" class="col-sm-2 col-form-label">Remark </label>
                <div class="col-sm-10">
                  <textarea name="remark_edit" id="remark" class="form-control" autocomplete="off"
                    placeholder="Enter remark for offline training"> <?php if (isset($data['remark'])) {
                                                                        echo $data['remark'];
                                                                      } ?></textarea>
                </div>
              </div>

              <div class="form-group row">
                <label for="society_remark" class="col-sm-2 col-form-label">Company Remark</label>
                <div class="col-sm-10">
                  <textarea class="form-control" id="society_remark" name="society_remark"
                    autocomplete="off"><?php echo $data['society_remark']; ?></textarea>
                </div>
              </div>

              <div class="form-group row">
                <label for="sales_remark" class="col-sm-2 col-form-label">Sales Remarks <span
                    class="required">*</span></label>
                <div class="col-sm-10">
                  <textarea class="form-control" id="sales_remark" name="sales_remark"
                    autocomplete="off" required><?php echo $data['sales_remark']; ?></textarea>
                </div>
              </div>

              <div class="form-group row">
                <label for="implementation_remark" class="col-sm-2 col-form-label">Implementation Remark</label>
                <div class="col-sm-10">
                  <textarea class="form-control" id="implementation_remark" name="implementation_remark"
                    autocomplete="off"><?php echo $data['implementation_remark']; ?></textarea>
                </div>
              </div>
              <fieldset id="addContactBtn">
                <button type="button" class="btn btn-sm btn-primary mb-3" onclick="addContact()">Add Contact
                  Person</button>
              </fieldset>

              <div id="contactFormsContainer">
                <?php
                $contact_q = $d->select("society_contact_person_details", "request_society_id='$request_society_id_edit'");
                while ($contact = mysqli_fetch_array($contact_q)) {
                ?>
                  <div class="card mb-4 contact-form">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <span class="contact-title">Contact Person Information</span>
                      <button type="button" class="btn-close" aria-label="Close" onclick="removeCard(this)">
                        <i class="fa fa-times"></i>
                      </button>
                    </div>
                    <div class="card-body">
                      <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control contact-name" name="contact_name[]"
                            id="add_contact_name_<?= $refCount ?>"
                            value="<?= htmlspecialchars($contact['contact_person_name']) ?>"
                            placeholder="Enter contact person name">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Phone Number</label>
                        <div class="col-sm-10">
                          <input type="tel" class="form-control onlyNumber contact-number" name="contact_number[]"
                            id="add_contact_number_<?= $refCount ?>"
                            value="<?= htmlspecialchars($contact['contact_person_no']) ?>"
                            placeholder="Enter contact number">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Designation</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control contact-designation" name="designation[]" id="add_designation_<?= $refCount ?>" value="<?= htmlspecialchars($contact['designation']) ?>" placeholder="Enter designation">
                        </div>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>

              <div class="card mb-4 contact-form-template d-none">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <span>Contact Person Information</span>
                  <button type="button" class="btn-close" aria-label="Close" onclick="removeCard(this)">
                    <i class="fa fa-times"></i>
                  </button>
                </div>
                <div class="card-body">
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" name="contact_name[]" value="" placeholder="Enter contact person name">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Phone Number</label>
                    <div class="col-sm-10">
                      <input type="tel" class="form-control onlyNumber" name="contact_number[]" value="" placeholder="Enter contact number">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Designation</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" name="designation[]" value="" placeholder="Enter designation">
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <input id="searchInput5" class="form-control" type="text" placeholder="Enter a Google location">
                <div class="map" id="map" style="width: 100%; height: 400px;"></div>
              </div>
              <?php if ($dayName == 'Saturday' || $dayName == 'Sunday') { ?>
                <div class="form-group row">
                  <label for="day" class="col-sm-12 col-form-label"> <input required="" type="checkbox" name="dayName"> I
                    agree today is <?php echo $dayName; ?> & Company will be created on monday </label>
                </div>
              <?php } ?>

              <div class="form-footer text-center">
                <button type="submit" id="socAddBtn" class="btn btn-success"><i class="fa fa-check-square-o"></i>
                  SAVE</button>
                <button type="reset" class="btn btn-danger"><i class="fa fa-times"></i> RESET</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h6 class="form-header text-uppercase">
              <i class="fa fa-upload"></i>
              Bulk Trial Plan Company Request Only for India (CSV Upload)
            </h6>
            <form action="controller/societyRequestBulkController.php" method="post" enctype="multipart/form-data">
              <div class="form-group row">
                <label for="bulk_csv" class="col-sm-2 col-form-label">Upload CSV <span class="text-danger">*</span></label>
                <div class="col-sm-6">
                  <input type="file" class="form-control-file" id="bulk_csv" name="bulk_csv" accept=".csv" required>
                  <small class="form-text text-muted">CSV with columns: Company Name, State, City, Rise Event (Yes/No), Address, Industry Type, Admin Name, Admin Mobile, Admin Email, Trial Days, Employee Registration Limit, Expected Team Size, Employee Tracking Limit, Per Employee Price, Sales Person Name, Implementation Executive Name, Sales Closure Date, Support Person Name, Support Person Mobile, Lead Sources (Meta/Inbound/Walk IN/Cold Data), Year Type (Calendar Year/Financial), Sales Remarks, Campaign Region (e.g. Ahmedabad, Hyderabad). Download sample CSV for format.</small>
                </div>
                <div class="col-sm-4">
                  <a href="controller/societyRequestBulkController.php?download_sample=1" class="btn btn-outline-secondary btn-sm"><i class="fa fa-download"></i> Download Sample CSV</a>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-sm-10">
                  <button type="submit" name="bulk_company_submit" class="btn btn-primary">Upload &amp; Create Requests</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>


  </div>
</div>

<script src="assets/js/jquery.min.js"></script>
<?php if (isset($_POST['request_society_id_edit'])) { ?>
  <script type="text/javascript">
    // $(document).ready(function () {
    //   $("#addSocietyRequestValidation").validate();
    //   reindexContacts();
    // });
  </script>
  <?php if ($data['request_package_id'] == 0) { ?>
    <script type="text/javascript">
      $(document).ready(function() {
        $('#amoutInput').hide();
        $('#amoutLable').hide();
        $('#PaymentDiv').hide();
        $('.check').change(function() {
          var data = $(this).val();
          // alert(data);
          if (data != 0) {
            $('#PaymentDiv').show();
            $('#amoutInput').show();
            $('#amoutLable').show();
            $('#TrialDiv1').hide();
            $('#TrialDiv2').hide();
          } else {
            $('#TrialDiv1').show();
            $('#PaymentDiv').hide();
            $('#TrialDiv2').show();
          }
        });
      });
    </script>
    <?php } else {
    if ($data['payment_status'] == 0) { ?>
      <script type="text/javascript">
        $(document).ready(function() {
          $('#amoutInput').show();
          $('#amoutLable').show();
          $('#PaymentDiv').show();
          $('.check').change(function() {
            var data = $(this).val();
            // alert(data);
            if (data != 0) {
              $('#PaymentDiv').show();
              $('#amoutInput').show();
              $('#amoutLable').show();
              $('#TrialDiv1').hide();
              $('#TrialDiv2').hide();
            } else {
              $('#TrialDiv1').show();
              $('#PaymentDiv').hide();
              $('#TrialDiv2').show();
            }
          });
        });
      </script>
    <?php } else { ?>

      <script type="text/javascript">
        var plan_expire_date = '';

        function formatDate(date) {
          var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

          if (month.length < 2)
            month = '0' + month;
          if (day.length < 2)
            day = '0' + day;

          return [year, month, day].join('-');
        }

        $(document).ready(function() {
          $('#amoutInput').hide();
          $('#amoutLable').hide();
          $('#PaymentDiv').hide();
          $('.plan_expire_date').hide();
          $('.plan_expire_date_div').hide();
          $('.check').change(function() {
            var data = $(this).val();
            // alert(data);
            if (data != 0) {
              $('#PaymentDiv').show();
              $('#amoutInput').show();
              $('#amoutLable').show();
              $('.plan_expire_date').show();
              $('.plan_expire_date_div').show();
              $('#TrialDiv1').hide();
              $('#TrialDiv2').hide();
            } else {
              $('#TrialDiv1').show();
              $('#PaymentDiv').hide();
              $('.plan_expire_date').hide();
              $('.plan_expire_date_div').hide();
              $('#TrialDiv2').show();
            }
          });
        });
      </script>
  <?php }
  }
} else { ?>
  <script type="text/javascript">
    var plan_expire_date = '';

    function formatDate(date) {
      var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2)
        month = '0' + month;
      if (day.length < 2)
        day = '0' + day;

      return [year, month, day].join('-');
    }

    $(document).ready(function() {
      $('#amoutInput').hide();
      $('#amoutLable').hide();
      $('#PaymentDiv').hide();
      $('.plan_expire_date').hide();
      $('.plan_expire_date_div').hide();
      $('.check').change(function() {
        var data = $(this).val();

        var now = new Date();
        var dataTemp = parseInt(data);
        // Add one month to the current date
        var next_month = new Date(now.setMonth(now.getMonth() + dataTemp));

        // Manual date formatting
        var day = ("0" + next_month.getDate()).slice(-2);
        var month = ("0" + (next_month.getMonth() + 1)).slice(-2);
        var next_month_string = next_month.getFullYear() + "-" + (month) + "-" + (day);

        $('#plan_expire_date').datepicker('setDate', next_month_string);
        if (data != 0) {
          $('#PaymentDiv').show();
          $('#amoutInput').show();
          $('#amoutLable').show();
          $('.plan_expire_date').show();
          $('.plan_expire_date_div').show();
          $('#TrialDiv1').hide();
          $('#TrialDiv2').hide();
        } else {
          $('#TrialDiv1').show();
          $('#PaymentDiv').hide();
          $('.plan_expire_date').hide();
          $('.plan_expire_date_div').hide();
          $('#TrialDiv2').show();
        }
      });



    });
  </script>
<?php } ?>
<script
  src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=<?php echo $d->map_key(); ?>"></script>
<script src="app-assets/vendors/js/core/jquery-3.3.1.min.js"></script>
<script>
  /* script */
  function initialize() {
    // var latlng = new google.maps.LatLng(23.05669,72.50606);


    var latitute = document.getElementById('lat').value;
    var longitute = document.getElementById('lng').value;
    var latlng = new google.maps.LatLng(latitute, longitute);

    var map = new google.maps.Map(document.getElementById('map'), {
      center: latlng,
      zoom: 13
    });
    var marker = new google.maps.Marker({
      map: map,
      position: latlng,
      draggable: true,
      anchorPoint: new google.maps.Point(0, -29)
      // icon:'img/direction/'+dirction+'.png'
    });
    var parkingRadition = 5;
    var citymap = {
      newyork: {
        center: {
          lat: latitute,
          lng: longitute
        },
        population: parkingRadition
      }
    };

    var input = document.getElementById('searchInput5');
    // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    var geocoder = new google.maps.Geocoder();
    var autocomplete10 = new google.maps.places.Autocomplete(input);
    autocomplete10.bindTo('bounds', map);
    var infowindow = new google.maps.InfoWindow();
    autocomplete10.addListener('place_changed', function() {
      infowindow.close();
      marker.setVisible(false);
      var place5 = autocomplete10.getPlace();
      if (!place5.geometry) {
        window.alert("Autocomplete's returned place5 contains no geometry");
        return;
      }

      // If the place5 has a geometry, then present it on a map.
      if (place5.geometry.viewport) {
        map.fitBounds(place5.geometry.viewport);
      } else {
        map.setCenter(place5.geometry.location);
        map.setZoom(17);
      }

      marker.setPosition(place5.geometry.location);
      marker.setVisible(true);

      var pincode = "";
      for (var i = 0; i < place5.address_components.length; i++) {
        for (var j = 0; j < place5.address_components[i].types.length; j++) {
          if (place5.address_components[i].types[j] == "postal_code") {
            pincode = place5.address_components[i].long_name;
            // alert(pincode);
          }
        }
      }
      bindDataToForm(place5.formatted_address, place5.geometry.location.lat(), place5.geometry.location.lng(), pincode, place5.name);
      infowindow.setContent(place5.formatted_address);
      infowindow.open(map, marker);

    });
    // this function will work on marker move event into map 
    google.maps.event.addListener(marker, 'dragend', function() {
      geocoder.geocode({
        'latLng': marker.getPosition()
      }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[0]) {
            var places = results[0];
            console.log(places);
            var pincode = "";
            var serviceable_area_locality = places.address_components[4].long_name;
            // alert(serviceable_area_locality);
            for (var i = 0; i < places.address_components.length; i++) {
              for (var j = 0; j < places.address_components[i].types.length; j++) {
                if (places.address_components[i].types[j] == "postal_code") {
                  pincode = places.address_components[i].long_name;
                  // alert(pincode);
                }
              }
            }
            bindDataToForm(results[0].formatted_address, marker.getPosition().lat(), marker.getPosition().lng(), pincode, serviceable_area_locality);
            // infowindow.setContent(results[0].formatted_address);
            // infowindow.open(map, marker);
          }
        }
      });
    });
  }

  function bindDataToForm(address, lat, lng, pin_code, serviceable_area_locality) {
    // document.getElementById('poi_point_address').value = address;
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;
  }
  google.maps.event.addDomListener(window, 'load', initialize);

  let refCount = 0;

  function addContact(data = null) {
    const div = document.createElement('div');
    div.className = 'card mb-4 contact-form';

    div.innerHTML = `
    <div class="card-header d-flex justify-content-between align-items-center">
      <span class="contact-title">Contact Person Information</span>
      <button type="button" class="btn-close" aria-label="Close" onclick="removeContact(this)">
        <i class="fa fa-times"></i>
      </button>
    </div>
    <div class="card-body">
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Name <span class="required">*</span></label>
        <div class="col-sm-10">
          <input type="text" class="form-control contact-name" name="contact_name[]" id="add_contact_name_${refCount}" value="${data?.contact_name || ''}" placeholder="Enter contact person name">
        </div>
      </div>
      <div class="form-group row mt-3">
        <label class="col-sm-2 col-form-label">Phone Number <span class="required">*</span></label>
        <div class="col-sm-10">
          <input type="tel" class="form-control onlyNumber contact-number" name="contact_number[]" id="add_contact_number_${refCount}" value="${data?.contact_number || ''}" placeholder="Enter contact number">
        </div>
      </div>
      <div class="form-group row mt-3">
        <label class="col-sm-2 col-form-label">Designation <span class="required">*</span></label>
        <div class="col-sm-10">
          <input type="text" class="form-control contact-designation" id="add_designation_${refCount}" name="designation[]" value="${data?.designation || ''}" placeholder="Enter designation">
        </div>
      </div>
    </div>
  `;

    document.getElementById('addContactBtn').appendChild(div);

    reindexContacts();
  }

  function removeContact(button) {
    const card = button.closest('.contact-form');
    card.remove();
    reindexContacts();
  }

  function reindexContacts() {
    const forms = document.querySelectorAll('.contact-form');
    // const format = document.querySelectorAll('.cardContainerForm');
    forms.forEach((form, index) => {
      const i = index + 1;
      form.querySelector('.contact-title').textContent = `Contact Person Information ${i}`;

      const nameInput = form.querySelector('.contact-name');
      const numberInput = form.querySelector('.contact-number');
      const designationInput = form.querySelector('.contact-designation');

      nameInput.id = `add_contact_name_${i}`;
      numberInput.id = `add_contact_number_${i}`;
      designationInput.id = `add_designation_${i}`;

      $(nameInput).next("label.error").remove();
      $(numberInput).next("label.error").remove();
      $(designationInput).next("label.error").remove();

      $(nameInput).rules('remove');
      $(numberInput).rules('remove');
      $(designationInput).rules('remove');
      console.log(nameInput);
      $(nameInput).rules('add', {
        required: true,
        minlength: 2,
        maxlength: 50,
        noSpace: true,
        messages: {
          required: "Contact name is required",
          minlength: "Name must be at least 2 characters",
          maxlength: "Name must not exceed 50 characters"
        }
      });

      $(numberInput).rules('add', {
        required: true,
        digits: true,
        minlength: 8,
        maxlength: 15,
        messages: {
          required: "Phone number is required",
          digits: "Only numbers are allowed",
        }
      });

      $(designationInput).rules('add', {
        required: true,
        minlength: 2,
        maxlength: 30,
        noSpace: true,
        messages: {
          required: "Designation is required",
          minlength: "Designation must be at least 2 characters",
          maxlength: "Designation must not exceed 30 characters"
        }
      });


      $("#addSocietyRequestValidation").validate().element(`#${nameInput.id}`);
      $("#addSocietyRequestValidation").validate().element(`#${numberInput.id}`);
      $("#addSocietyRequestValidation").validate().element(`#${designationInput.id}`);
    });
  }


  function removeCard(button) {
    button.closest('.card').remove();
  }

  // Domain-City Integration Functions
  function getCityAndUpdateDomains() {
    var state_id = $("#state_id").val();
    $(".ajax-loader").show();
    $.ajax({
      url: "getCities.php",
      cache: false,
      type: "POST",
      data: {
        state_id: state_id,
        getCity: 'getCity',
        csrf: csrf
      },
      success: function(response) {
        $('#city_id').html(response);
        $(".ajax-loader").hide();

        // Update domain selection based on city
        // updateDomainsByCity();
      }
    });
  }

  $(document).ready(function() {
    var existingDomain = $('#domain_select option:selected').val();
    if (existingDomain && existingDomain != '') {
      window.existingDomain = existingDomain;
    }
  });

  function getRecommendedDomain() {
    var city_id = $("#city_id").val();
    if (city_id != '') {
      $.ajax({
        url: "getDomainsByCity.php",
        cache: false,
        type: "POST",
        data: {
          city_id: city_id
        },
        success: function(response) {
          $('#domain_select').html(response);
          $('#domain_select').trigger('change');
          var dn = $('#domain_select').val();
          if (dn) {
            fetchDomainInfo(dn);
          }
        },
        error: function() {}
      });
    } else {
      $("#full_url_preview1").html('');
    }
  }

  $(document).ready(function() {
    var initialDomain = $('#domain_select').val();
    if (initialDomain) {
      fetchDomainInfo(initialDomain);
    } else {
      $("#full_url_preview2").html("");
    }
  });

  function fetchDomainInfo(domainName) {
    $.ajax({
      url: 'controller/domainController.php',
      type: 'POST',
      dataType: 'json',
      data: {
        getDomainInfo: 'getDomainInfo',
        domain_name: domainName
      },
      success: function(resp) {
        if (resp && resp.success) {
          $("#full_url_preview2").html(
            '<span class="text-info">Server: ' + (resp.server_name || '') + ' (' + (resp.server_ip || '') + '), Companies: ' + (resp.company_count || 0) + '</span>'
          );
        } else {
          $("#full_url_preview2").html("<b class='text-danger'><br>This Domain Not Added in domain master</b>");
        }
      },
      error: function() {
        $("#full_url_preview2").html("<b class='text-danger'><br>This Domain Not Added in domain master</b>");
      }
    });
  }

  function getSupportPersonContact(adminName) {
    if (adminName == '' || adminName == null) {
      $('#support_country_code').val('').trigger('change');
      $('#support_mobile_no').val('');
      return;
    }

    $.ajax({
      url: 'ajax/getAdminContact.php',
      type: 'POST',
      dataType: 'json',
      data: {
        admin_name: adminName
      },
      success: function(response) {
        if (response.success) {
          // Set country code - handle select2 if present
          var countryCodeSelect = $('#support_country_code');
          countryCodeSelect.val(response.country_code);
          if (countryCodeSelect.hasClass('select2-hidden-accessible')) {
            countryCodeSelect.trigger('change.select2');
          } else {
            countryCodeSelect.trigger('change');
          }
          // Set mobile number
          $('#support_mobile_no').val(response.mobile);
        } else {
          // Clear fields if admin not found
          var countryCodeSelect = $('#support_country_code');
          countryCodeSelect.val('');
          if (countryCodeSelect.hasClass('select2-hidden-accessible')) {
            countryCodeSelect.trigger('change.select2');
          } else {
            countryCodeSelect.trigger('change');
          }
          $('#support_mobile_no').val('');
          if (response.message) {
            alert(response.message);
          }
        }
      },
      error: function() {
        // Clear fields on error
        var countryCodeSelect = $('#support_country_code');
        countryCodeSelect.val('');
        if (countryCodeSelect.hasClass('select2-hidden-accessible')) {
          countryCodeSelect.trigger('change.select2');
        } else {
          countryCodeSelect.trigger('change');
        }
        $('#support_mobile_no').val('');
        alert('Error fetching admin contact details');
      }
    });
  }
</script>