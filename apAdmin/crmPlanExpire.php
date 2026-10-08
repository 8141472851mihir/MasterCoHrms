<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-md-4">
        <h4 class="page-title">CRM Plan Expire</h4>
      </div>
      <div class="col-md-3">
        <form action="" method="GET">
          <div class="d-flex align-items-center">
            <select id="planType" name="planType" class="form-control single-select" onchange="this.form.submit()">
              <option value="2" <?= (!isset($_GET['planType']) || $_GET['planType'] == '2') ? 'selected' : '' ?>>Crm Created
              </option>
              <option value="0" <?= (isset($_GET['planType']) && $_GET['planType'] == '0') ? 'selected' : '' ?>>All
              </option>
              <option value="1" <?= (isset($_GET['planType']) && $_GET['planType'] == '1') ? 'selected' : '' ?>>Crm Not Created
              </option>
            </select>
          </div>
        </form>
      </div>
    </div>
    <?php
    $planFilter = '';
    if (isset($_GET['planType']) && $_GET['planType'] != '2') {
      $plan = $_GET['planType'];
      if ($plan == '0') {
        $planFilter = "";
      } else if ($plan == '1') {
        $planFilter = " AND crm_created = '0'";
      }
    } else {
      $planFilter = " AND crm_created = '1'";
    }
    ?>


    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
          <div class="card-body">
            <div class="table-responsive">
              <table id="reportTable" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Id</th>
                    <th>Company Name</th>
                    <th>Server Name</th>
                    <th>Mobile</th>
                    <th>CRM Create Date</th>
                    <th>Plan</th>
                    <th>Change</th>
                    <th>Days Left</th>
                    <th>Plan Expire</th>
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
                  </tr>
                </tfoot>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->selectRow(
                    "society_master.*,manage_plan.plan_value,manage_plan.plan_name,server_master.server_name,server_master.server_ip,domain_master.domain_name",
                    "society_master LEFT JOIN manage_plan ON manage_plan.plan_value = society_master.crm_package_id LEFT JOIN domain_master ON society_master.domain_id=domain_master.domain_id LEFT JOIN server_master ON server_master.server_id=domain_master.server_id",
                    "society_id!=0 $countryAppendQuerySocietySingle $planFilter",
                    "order by crm_plan_expiring_date ASC"
                  );
                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);

                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><a href="<?php echo $sub_domain; ?>apAdmin/" target="_blank"><?php echo '' . $d->short_app_name() . '_' . $society_id; ?></a></td>
                      <td><a href="<?php echo $sub_domain; ?>crm/" target="_blank"><?php echo $society_name . '-' . $city_name;  ?></a></td>
                      <!-- <td><?php echo $society_name . '-' . $city_name; ?></td> -->
                      <td><a target="_blank"
                          href="http://<?php echo $server_ip; ?>/phpmyadmin"><?php echo $server_ip; ?></a></td>
                      <td><?php echo $secretary_mobile; ?></td>
                      <td><?php echo $crm_created_date; ?></td>
                      <td>
                        <?php
                        if ($crm_created == 0) {
                          echo "<span class='text-danger'>CRM Not Created</span>";
                        } else {
                          if ($plan_value == 0) {
                            if ($crm_plan_expiring_date != '') {
                              echo "Custom Plan";
                            } else {
                              echo "";
                            }
                          } else {
                            echo $plan_name;
                          }
                        }
                        ?>
                      </td>
                      <td>
                        <?php if ($crm_created != 0) { ?>
                          <button class="btn btn-sm btn-danger openPlanModal" data-toggle="modal" data-target="#planModal"
                            data-society-id="<?php echo $society_id; ?>">
                            Change
                          </button>
                        <?php } ?>
                      </td>
                      <td>
                        <?php
                        if ($crm_created != 0) {
                          if ($crm_plan_expiring_date != '') {
                            $now = time();
                            $your_date = strtotime($crm_plan_expiring_date);
                            $datediff = $your_date - $now;
                            echo ($datediff > 0) ? round($datediff / (60 * 60 * 24)) : "Expired";
                          } else {
                            echo "";
                          }
                        }
                        ?>
                      </td>
                      <td>
                        <?php
                        if ($crm_created != 0) {
                          if ($default_time_zone != "Asia/Kolkata") {
                            echo $d->change_timezone($crm_plan_expiring_date, $default_time_zone, 'Y-m-d');
                          } else {
                            echo $crm_plan_expiring_date;
                          }
                        }
                        ?>
                      </td>

                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="updatePlan">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Update Plan</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="planUpdateValidation" action="controller/planController.php" method="post">
          <input type="hidden" id="society_id" name="society_id">
          <input type="hidden" id="base_url" name="society_base_url">
          <input type="hidden" id="package_id" name="package_id">
          <input type="hidden" id="society_name" name="society_name">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Date</label>
            <div class="col-sm-8">
              <input required="" type="text" autocomplete="off" class="form-control" id="autoclose-datepicker"
                name="update_plan">
            </div>
          </div>
          <div class="form-footer text-center">
            <button type="submit" name="update" value="update" class="btn btn-primary"><i
                class="fa fa-check-square-o"></i> Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function updateSocPlan(base_url, society_id, crm_plan_expiring_date, package_id, society_name) {
    $('#society_id').val(society_id);
    $('#base_url').val(base_url);
    $('#autoclose-datepicker').val(crm_plan_expiring_date);
    $('#package_id').val(package_id);
    $('#society_name').val(society_name);
  }
</script>


<div class="modal fade" id="editFloor">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Company Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="BlockResp">

      </div>

    </div>
  </div>
</div><!--End Modal -->

<script type="text/javascript">
  function getSocietyData1(society_id) {
    var csrf = $('input[name="csrf"]').val();
    $('#BlockResp').html("Please Wait..!");
    $.ajax({
      url: "controller/cronGetData.php",
      cache: false,
      type: "POST",
      data: {
        society_id: society_id,
        csrf: csrf
      },
      success: function(response) {
        $('#BlockResp').html(response);

      }
    });
  }
</script>


<div class="modal fade" id="planModal">
  <div class="modal-dialog ">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Change CRM Plan</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="card-body">
          <div class="row ">
            <div class="col-md-12">
              <form id="createCrmForm" action="controller/buildingController.php" method="post">
                <div class="form-group align-items-center">
                  <div class="form-group">
                    <label for="input-14">Plan <span class="required">*</span></label>
                    <select required name="crm_package_id" class="form-control single-select check" id="input-14">
                      <option value="">-- Select Plan --</option>
                      <?php
                      $planQuery = $d->select("manage_plan", "plan_value != 0 AND status = 0");
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
                  <div class="form-group plan_expire_date_div">
                    <label for="plan_expire_date" class="plan_expire_date">CRM Plan Expire Date <span
                        class="required">*</span></label>
                    <input type="text" readonly maxlength="120" value="" required
                      class="form-control facility_datepicker" name="crm_plan_expiring_date" id="plan_expire_date">
                  </div>
                  <div class="form-group" id="TrialDiv2">
                    <label id="TrialDiv1" for="trlDays">Trial Days <span class="required">*</span></label>
                    <input type="text" min="0" max="100" maxlength="3" class="form-control" autocomplete="off"
                      name="crm_trial_days" id="trlDays" value="">
                  </div>
                </div>
                <input type="hidden" name="companyId" class="companyId" value="">
                <input type="hidden" name="updateCRMPlan" value="updateCRMPlan">
                <div class="text-center mt-3">
                  <button type="submit" class="btn btn-sm btn-primary waves-effect waves-light m-1" title="Create CRM">
                    Update Plan <i class="fa fa-users"></i> </button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="assets/js/jquery.min.js"></script>

<script>
  $(document).on('click', '.openPlanModal', function() {
    let societyId = $(this).data('society-id');
    $('#planModal .companyId').val(societyId);
  });

  $(document).ready(function() {
    $('.check').change(function() {
      var data = $(this).val();
      if (data != '') {

        var now = new Date();
        var dataTemp = parseInt(data);
        // Add one month to the current date
        var next_month = new Date(now.setMonth(now.getMonth() + dataTemp));

        // Manual date formatting
        var day = ("0" + next_month.getDate()).slice(-2);
        var month = ("0" + (next_month.getMonth() + 1)).slice(-2);
        var next_month_string = next_month.getFullYear() + "-" + (month) + "-" + (day);

        $('#plan_expire_date').val(next_month_string);

      }
    });

    function toggleFields(packageId) {
      if (packageId == '0') {
        $('#TrialDiv1, #TrialDiv2').show();
        $('.plan_expire_date, .plan_expire_date_div').hide();
      } else {
        $('#TrialDiv1, #TrialDiv2').hide();
        $('.plan_expire_date, .plan_expire_date_div').show();
      }
    }
    var initialPackageId = $('.check').val();
    toggleFields(initialPackageId);

    $('.check').change(function() {
      var selectedPackageId = $(this).val();
      toggleFields(selectedPackageId);
    });
  });
  $(document).ready(function() {

    $('.check').change(function() {
      var data = $(this).val();
      if (data != '') {

        var now = new Date();
        var dataTemp = parseInt(data);
        // Add one month to the current date
        var next_month = new Date(now.setMonth(now.getMonth() + dataTemp));

        // Manual date formatting
        var day = ("0" + next_month.getDate()).slice(-2);
        var month = ("0" + (next_month.getMonth() + 1)).slice(-2);
        var next_month_string = next_month.getFullYear() + "-" + (month) + "-" + (day);

        $('#plan_expire_date').val(next_month_string);
        $('#plan_expire_date').datepicker('setDate', next_month_string)

      }
    });

  });

  function setRefundSociety(societyId) {
    document.getElementById('refund_society_id').value = societyId;
  }
</script>