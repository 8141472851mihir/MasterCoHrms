<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Add Requests CRM</h4>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="addrequestcrmvalidation" action="controller/crmRequestController.php" method="post" enctype="multipart/form-data">
              <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="input-1">Company Name<span class="required">*</span></label>
                <div class="col-sm-4">
                  <select class="form-control single-select" name="company_name" onchange="societyselected(this)">
                    <option value="">--Select Company--</option>
                    <?php
                    $companylist = $d->selectRow(
                      'society_master.society_id,society_master.society_name,society_master.city_name,society_master.crm_created',
                      'society_master',
                      "society_master.crm_created != 1 AND (
                      NOT EXISTS (
                      SELECT 1 FROM crm_request_master crm 
                      WHERE crm.society_id = society_master.society_id
                      ) 
                     OR EXISTS (
                      SELECT 1 FROM crm_request_master crm2 
                      WHERE crm2.society_id = society_master.society_id 
                     AND crm2.request_status = 2
                      )
                    )",
                      ""
                    );

                      while($row = mysqli_fetch_array($companylist)){?>
                        <option value="<?=$row["society_id"];?>"><?= $row["society_name"];?> - <?= $row["city_name"];?></option>
                      <?php }?>
                  </select>
                </div>
                <label for="input-2" class="col-sm-2 col-form-label">CRM Plan <span class="required">*</span></label>
                <div class="col-sm-4">
                  <select required name="crm_package_id" class="form-control single-select check" id="crm_package_id">
                    <option value="">-- Select Plan --</option>
                    <?php
                      $planQuery = $d->select("manage_plan", "status = 0");
                      $selectedPlanId = isset($existingPlanId) ? $existingPlanId : '';

                      if (mysqli_num_rows($planQuery) > 0) {
                        while ($plan = mysqli_fetch_array($planQuery)) {
                          echo "<option value='" . $plan['plan_value'] . "'>" . htmlspecialchars($plan['plan_name']) . "</option>";
                        }
                      } else {
                        echo "<option value=''>No Plans Available</option>";
                      }
                    ?>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <div id="TrialDiv2" class="col-sm-6 mx-0 px-0" style="display: flex;">
                  <label id="TrialDiv1" class="col-sm-4 col-form-label" for="trlDays">CRM Trial Days <span class="required">*</span></label>
                  <div class="col-sm-8">
                    <input type="text" min="0" max="100" maxlength="3" class="form-control" autocomplete="off"
                      name="crm_trial_days" id="trlDays" value="">
                  </div>
                </div>
                <div class="plan_expire_date_div col-sm-6 mx-0 px-0" style="display: flex;">
                  <label for="input-3" class="col-sm-4 col-form-label">CRM Plan Expire Date <span class="required">*</span></label>
                  <div class="col-sm-8">
                      <input type="text" readonly maxlength="120" value="" required
                        class="form-control facility_datepicker" name="crm_plan_expiring_date" id="plan_expire_date">
                  </div>
                </div>
              <!-- </div> -->
              <!-- <div class="form-group row"> -->
                <label for="input-4" class="col-sm-2 col-form-label" style="min-width: 100;">CRM Limit <span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" autocomplete="off" class="form-control onlyNumber" name="crm_limit" id="crm_limit"
                    placeholder="Enter CRM Limit" required>
                  <input type="hidden" id="society_id" name="society_id">
                </div>
              </div>
              <div class="form-group row">
                <label for="input-5" class="col-sm-2 col-form-label">CRM Payment Status <span
                    class="required">*</span></label>
                <div class="col-sm-4">
                  <select class="form-control paymentSelect" name="amountReceivedType">
                    <!-- <option <?php if (isset($request_society_id_edit) && $data['payment_status'] == 0) {
                      echo "selected";
                    } ?> value="0">Not Received</option> -->
                    <option value="1">Received</option>
                  </select>
                </div>
                <label for="input-6" id="amoutLable" class="col-sm-2 col-form-label">CRM Received Amount <span
                    class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" id="working_days" name="amountReceived" autocomplete="0"
                    class="form-control onlyNumber" maxlength="13" minlength="1">
                </div>
              </div>
              <div class="form-group row">
                <label for="input-7" id="amoutLable" class="col-sm-2 col-form-label">CRM Payment Mode <span
                    class="required">*</span></label>
                <div class="col-sm-4">
                  <select required name="payment_mode" autocomplete="0" class="form-control" minlength="1">
                    <option value="">-- Select --</option>
                    <option value="1">Online Bank Transfer
                    </option>
                    <option value="2">Cheque</option>
                    <option value="3">UPI</option>
                    <option value="4">Cash</option>
                  </select>
                </div>
                <label for="input-8" id="amoutLable" class="col-sm-2 col-form-label">CRM Payment Attachment</label>
                <div class="col-sm-4">
                  <input type="file" accept="image/*,.pdf" id="payment_attachment" name="payment_attachment"
                    class="form-control">
                </div>
              </div>
              <div class="form-group row">
                <label for="input-9" class="col-sm-2 col-form-label">CRM Yearly ticket size</label>
                <div class="col-sm-4">
                  <input type="text" maxlength="10" class="form-control onlyNumber" autocomplete="off" name="yearly_ticket_size">
                </div>
                <label for="input-10" class="col-sm-2 col-form-label">CRM Received ticket size</label>
                <div class="col-sm-4">
                  <input type="text" maxlength="10" class="form-control onlyNumber" autocomplete="off" name="received_ticket_size">
                </div>
              </div>
              <div class="form-group row">
                <label for="input-11" class="col-sm-2 col-form-label">CRM Per Employee Price
                  <span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" class="form-control onlyNumber" id="per_emp_price" autocomplete="off" required
                    name="per_employee_price">
                </div>
                <label for="input-12" class="col-sm-2 col-form-label">CRM Remark</label>
                <div class="col-sm-4">
                  <input type="text" maxlength="250" class="form-control" id="crm_remark" autocomplete="off" 
                    name="crm_remark">
                </div>
              </div>

              <div class="form-footer text-center">
                <input type="hidden" name="addcrmrequest">
                <button value="add Page" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> ADD</button>
                <button  type="reset" class="btn btn-danger"><i class="fa fa-times"></i> CANCEL</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="assets/js/jquery.min.js"></script>
<script>
  $(document).ready(function () {
    function formatDate(date) {
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      return date.getFullYear() + "-" + month + "-" + day;
    }

    function updateTrialExpireDate() {
      const trialDays = parseInt($('#trlDays').val());
      if (!isNaN(trialDays) && trialDays > 0) {
        const now = new Date();
        now.setDate(now.getDate() + trialDays - 1);
        let expireDate = formatDate(now);
        $('#plan_expire_date').val(expireDate);
        $('#crm_trial_days_date').val(expireDate); 
      } else {
        $('#plan_expire_date').val('');
        $('#crm_trial_days_date').val(''); 
      }
    }


    function toggleFields(packageId) {
      if (packageId === '0') {
        $('#TrialDiv1, #TrialDiv2').show();
        $('.plan_expire_date_div, .plan_expire_date').hide(); 
        $('#plan_expire_date').val('');
      } else if (packageId !== '' && !isNaN(packageId)) {
        $('#TrialDiv1, #TrialDiv2').hide();
        $('.plan_expire_date_div, .plan_expire_date').show(); 
        $('#trlDays').val('');
        let dataTemp = parseInt(packageId);
        if (!isNaN(dataTemp)) {
          let now = new Date();
          let nextMonth = new Date(now.setMonth(now.getMonth() + dataTemp));
          $('#plan_expire_date').datepicker('setDate', nextMonth).datepicker('setStartDate', formatDate(new Date()));
        } else {
          $('#plan_expire_date').val('');
        }
      } else {
        $('#TrialDiv1, #TrialDiv2').hide();
        $('.plan_expire_date_div, .plan_expire_date').show();
        $('#trlDays').val('');
        $('#plan_expire_date').val('');
      }
    }

    $('.plan_expire_date_div, .plan_expire_date').show();

    $('.check').change(function () {
      toggleFields($(this).val());
    });

    $('#trlDays').on('input', function () {
      if ($('.check').val() === '0') {
        updateTrialExpireDate();
      }
    });

    toggleFields($('.check').val());
  });
</script>

