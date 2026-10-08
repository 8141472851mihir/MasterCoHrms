<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Company Plan Expire</h4>
       
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Id</th>
                    <th>Company Name</th>
                    <th>Mobile</th>
                    <th>Plan</th>
                    <th>Change</th>
                    <th>Days Left</th>
                    <th>Plan Expire</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                 <?php 
                 $i=1;
                 $q = $d->selectRow("society_master.*,manage_plan.plan_value,manage_plan.plan_name",
                 "society_master LEFT JOIN manage_plan ON manage_plan.plan_value = society_master.package_id" ,
                 "society_id!=0 $countryAppendQuerySocietySingle","order by plan_expire_date ASC");
                 while ($data=mysqli_fetch_array($q)) {
                  extract($data);
                
                  ?>
                  <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo ''.$d->short_app_name().'_'.$society_id; ?></td>
                    <td><a href="<?php echo $sub_domain; ?>apAdmin/" target="_blank"><?php echo $society_name . '-' . $city_name;  ?></a></td>

                    <td><?php echo $secretary_mobile; ?></td>
                    <td>
                    <?php 
                        if($plan_value == 0){
                        echo "Custome Plan";
                        }else{
                          echo $plan_name;
                        }
                    ?>
                    </td>
                    <td>
                     <button onclick="changePlan('<?php echo (int)$society_id; ?>','<?php echo (int)$employee_tracking_limit; ?>','<?php echo (int)$employee_registration_limit; ?>','<?php echo (int)$crm_limit; ?>','<?php echo (int)$crm_created; ?>');" data-toggle="modal" data-target="#planModal" class="btn btn-sm btn-danger">Change</button>
                    </td>
                    <td>
                      <?php 
                      $now = time();
                      $your_date = strtotime("$plan_expire_date");
                      $datediff = $your_date - $now;
                      if ($datediff>0) {
                        echo round($datediff / (60 * 60 * 24));
                      } else{
                        echo "Expired";
                      }
                      ?>
                    </td>
                    <td><?php 
                     if ($default_time_zone!="Asia/Kolkata") {
                              echo $d->change_timezone($plan_expire_date,$default_time_zone,'Y-m-d');
                          } else {
                      echo $plan_expire_date;
                      } ?>
                        
                      </td>

                    <td>
                       <button   data-toggle="modal" data-target="#editFloor" onclick="getSocietyData1('<?php echo $data['society_id'] ?>')" class="btn btn-sm btn-warning">View</button>
                      <!-- <button data-toggle="modal" data-target="#updatePlan" onclick="updateSocPlan('<?php echo $data['sub_domain'] ?>','<?php echo $data['society_id'] ?>','<?php echo $data['plan_expire_date'] ?>','<?php echo $data['package_id'] ?>','<?php echo $data['society_name'].'-'.$data['city_name'] ?>')" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></button> -->
                      
                      <?php if($data['refund_status'] == 0) { ?>
                        <button class="btn btn-sm bg-primary text-white" data-toggle="modal" data-target="#refundModal"onclick="setRefundSociety('<?php echo $data['society_id']; ?>')">Refund</button>
                      <?php } ?>
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
              <input required="" type="text" autocomplete="off" class="form-control" id="autoclose-datepicker" name="update_plan">
            </div>
          </div>
          <div class="form-footer text-center">
            <button type="submit" name="update" value="update" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function updateSocPlan (base_url,society_id,plan_expire_date,package_id,society_name) {
    $('#society_id').val(society_id);
    $('#base_url').val(base_url); 
    $('#autoclose-datepicker').val(plan_expire_date); 
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
  
function  getSocietyData1(society_id) {
    var csrf =$('input[name="csrf"]').val();
    $('#BlockResp').html("Please Wait..!");
  $.ajax({
        url: "controller/cronGetData.php",
        cache: false,
        type: "POST",
        data: {society_id : society_id,csrf:csrf},
        success: function(response){
            $('#BlockResp').html(response);
            
        }
     });
}
</script>


   <style>
    #planModal.modal {
      overflow: hidden;
    }
    #planModal .modal-dialog {
      max-height: calc(100vh - 2rem);
      margin: 1rem auto;
    }
    #planModal .modal-content {
      max-height: calc(100vh - 2rem);
      display: flex;
      flex-direction: column;
    }
    #planModal .modal-header {
      flex-shrink: 0;
    }
    #planModal .modal-body {
      overflow-y: auto;
    }
  </style>
   <div class="modal fade" id="planModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Change Company Plan</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" >
         <form id="planChangeModal" action="controller/buildingController.php" method="post" enctype="multipart/form-data">
          <input type="hidden" name="societyId" value="" id="societyId">
          <div class="form-group row">
            <label for="input-12" class="col-sm-4 col-form-label">Plan Type <span class="required">*</span></label>
            <div class="col-sm-8">
              <select required name="plan_type" id="plan_type" class="form-control single-select">
                <option value="">-- Select Plan --</option>
                <option value="1">Renewal</option>
                <option value="3">Extend</option>
                <option value="4">Temporary Extension</option>
              </select>
            </div> 
          </div>
        <div class="form-group row" id="planFormRes">
          <label for="input-12" class="col-sm-4 col-form-label">Renewal Plan <span class="required">*</span></label>
            <div class="col-sm-8">

            <select required name="package_id" class="form-control single-select check" id="input-14">
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
          
        </div>
        <div class="form-group row" id="amoutLDiv">

            <label for="plan_expire_date" class="col-sm-4 col-form-label plan_expire_date">Plan Expire Date <span class="required">*</span></label>
            <div class="col-sm-8 plan_expire_date_div">
              <input type="text" readonly="" id="plan_expire_date"  maxlength="120" value="" required="" class="form-control facility_datepicker" name="plan_expire_date">
            </div>
        </div>
        <div class="form-group row" id="amoutLDiv">
          <label for="input-12" class="col-sm-4 col-form-label">Received Amount <span class="required" id="amount_required">*</span></label>
           <div class="col-sm-8" >
             <input type="text" maxlength="15" required="" id="working_days" name="amountReceived" class="form-control">
           </div>
        </div>
       <div class="form-group row">
          <h6 id="amountWarning" class="text-danger d-none ml-3">
            Received amount is less than ₹1000. Please enter a valid remark.
          </h6>
        </div>
        <div class="form-group row d-none" id="remarkDiv">
          <label for="remark" class="col-sm-4 col-form-label">Remark <span class="required d-none" id="remark_required">*</span></label>
          <div class="col-sm-8">
            <textarea name="transaction_amount_remark" id="remark" class="form-control" maxlength="150" placeholder="Enter remark"></textarea>
          </div>
        </div>

        <div class="form-group row" >
          <label for="input-12" id="amoutLable" class="col-sm-4 mt-2 col-form-label">Payment Mode <span class="required" id="payment_mode_required">*</span></label>
           <div class="col-sm-8 mt-2" id="amoutInput">
             <select required name="payment_mode" autocomplete="0" class="form-control single-select"  minlength="1">
              <option value="">-- Select --</option>
              <option <?php if($data['payment_mode']==1) { echo 'selected'; } ?> value="1">Online Bank Transfer</option>
              <option <?php if($data['payment_mode']==2) { echo 'selected'; } ?> value="2">Cheque</option>
              <option <?php if($data['payment_mode']==3) { echo 'selected'; } ?> value="3">UPI</option>
              <option <?php if($data['payment_mode']==4) { echo 'selected'; } ?> value="4">Cash</option>
             </select>
           </div>
        </div>
        <div class="form-group row" id="">
          <label for="input-12" id="amoutLable" class="col-sm-4 mt-2  col-form-label">Payment Attachment <span class="required d-none" id="payment_attachment_required">*</span></label>
           <div class="col-sm-8 mt-2">
             <input type="file" accept="image/*,.pdf" id="payment_attachment" name="payment_attachment"  class="form-control" value="<?php echo $data['payment_attachment'] ?>" >
           </div>
        </div>
        <div class="form-group row" id="">
            <label for="input-12" class="col-sm-4 col-form-label">
                Received By <span class="required" id="received_by_required">*</span>
            </label>          
            <div class="col-sm-8" >
              <input type="text" maxlength="100" required="" id="received_by" name="received_by" class="form-control">
            </div>
        </div>

        <div class="form-group row">
            <label for="closure_city" class="col-sm-4 col-form-label">Closure City <span class="required">*</span></label>
            <div class="col-sm-8">
                <input type="text" maxlength="100" required id="closure_city" name="closure_city" class="form-control">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Update limit? <span class="required">*</span></label>
            <div class="col-sm-8 pt-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="update_limits" id="update_limits_yes" value="yes">
                    <label class="form-check-label" for="update_limits_yes">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="update_limits" id="update_limits_no" value="no">
                    <label class="form-check-label" for="update_limits_no">No</label>
                </div>
            </div>
        </div>
        <div id="planLimitFields" style="display:none;">
            <div class="form-group row">
                <label for="plan_employee_tracking_limit" class="col-sm-4 col-form-label">Employee Tracking Limit <span class="required">*</span></label>
                <div class="col-sm-8">
                    <input type="text" autocomplete="off" class="form-control onlyNumber" id="plan_employee_tracking_limit" name="employee_tracking_limit">
                </div>
            </div>
            <div class="form-group row">
                <label for="plan_employee_registration_limit" class="col-sm-4 col-form-label">Employee Registration Limit <span class="required">*</span></label>
                <div class="col-sm-8">
                    <input type="text" autocomplete="off" class="form-control onlyNumber" id="plan_employee_registration_limit" name="employee_registration_limit">
                </div>
            </div>
            <div class="form-group row" id="planCrmLimitRow">
                <label for="plan_crm_limit" class="col-sm-4 col-form-label">CRM Limit <span class="required">*</span></label>
                <div class="col-sm-8">
                    <input type="text" autocomplete="off" maxlength="6" class="form-control onlyNumber" id="plan_crm_limit" name="crm_limit">
                </div>
            </div>
        </div>
          
         <div class="form-footer text-center">
          <input type="hidden" name="updatePlan" value="updatePlan">
            <button type="submit" id=""   class="btn btn-success"><i class="fa fa-check-square-o"></i> Update</button>
        </div>

        </form>
      </div>
     
    </div>
  </div>
</div><!--End Modal -->

<div class="modal fade" id="refundModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h4 class="modal-title text-uppercase text-white" id="refund">
          Add Refund
        </h4>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="refundForm" action="controller/buildingController.php" method="POST">
          <div class="container mt-4">

            <div class="form-group row">
              <label for="refund_amount" class="col-sm-3 col-form-label">Refund Amount <span class="required">*</span></label>
              <div class="col-sm-9">
                <input autocomplete="off" type="text" class="form-control onlyNumber" name="refund_amount" placeholder="Enter refund amount">
              </div>
            </div>
            <div class="form-group row">
              <label for="refund_description" class="col-sm-3 col-form-label">Refund Description <span class="required">*</span></label>
              <div class="col-sm-9">
                <textarea class="form-control" name="refund_description" id="refund_description" rows="3" placeholder="Enter reason or details"></textarea>
              </div>
            </div>
            <div class="form-group row">
              <label for="refund_person_id" class="col-sm-3 col-form-label">Refund Coordinator<span class="required">*</span></label>
              <div class="col-sm-9">
                <select name="refund_person_id" id="refund_person_id" class="form-control single-select" required>
                  <option value="">-- Select --</option>
                  <?php
                    $admins = $d->select("bms_admin_master", "active_status = 0");
                    while ($row = mysqli_fetch_array($admins)) {
                      echo '<option value="' . $row['admin_id'] . '">' .($row['admin_name']) . '</option>';
                    }
                    ?>
                </select>
              </div>
            </div>
            <div class="form-footer text-center mt-4">
              <input type="hidden" name="refundModule" value="refundModule"> 
              <input type="hidden" name="society_id" id="refund_society_id">
              <button type="submit" class="btn btn-success">
                <i class="fa fa-check-square-o"></i> Submit
              </button>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="assets/js/jquery.min.js"></script>

<script type="text/javascript">
  function clearPlanExpireDateError() {
    var $field = $('#plan_expire_date');
    if ($.trim($field.val()) === '') {
      return;
    }
    var validator = $('#planChangeModal').data('validator');
    if (validator) {
      validator.element($field[0]);
    } else {
      $field.removeClass('error');
      $('#plan_expire_date-error').remove();
    }
  }

  $(document).on('changeDate change', '#plan_expire_date', clearPlanExpireDateError);

  $(document).ready(function() {
     
      $('.check').change(function(){
        var data= $(this).val();
        if (data!='') {

        var now = new Date();
        var dataTemp = parseInt(data);
        var next_month = new Date(now.setMonth(now.getMonth() + dataTemp));
        
        var day = ("0" + next_month.getDate()).slice(-2);
        var month = ("0" + (next_month.getMonth() + 1)).slice(-2);
        var next_month_string = next_month.getFullYear() + "-" + (month) + "-" + (day);

        $('#plan_expire_date').val(next_month_string);
        clearPlanExpireDateError();
         
        }
      });

    });

    $('#working_days').on('input', function () {
        var amount = parseFloat($(this).val());
        let planType = $('#plan_type').val();
        if (planType == '4') {

            $('#remarkDiv').addClass('d-none');
            $('#amountWarning').addClass('d-none');
            $('#remark')
                .removeAttr('required');
            $('#remark')
                .rules('remove');
            $('#remark')
                .val('');
            return;
        }

        if (!isNaN(amount) && amount < 1000) {
            $('#remarkDiv').removeClass('d-none');
            $('#amountWarning').removeClass('d-none');
            $('#remark')
                .attr('required', true);
            $('#remark')
                .rules('add', {
                    required: true
                });
            $('#remark_required').removeClass('d-none');
        } else {
            $('#remarkDiv').addClass('d-none');
            $('#amountWarning').addClass('d-none');
            $('#remark')
                .removeAttr('required');
            $('#remark')
                .rules('remove');
            $('#remark_required').addClass('d-none');
        }
    });

   function setRefundSociety(societyId) {
    document.getElementById('refund_society_id').value = societyId;
  }

  $(document).on('change', 'input[name="update_limits"]', function () {
    if ($('#update_limits_yes').is(':checked')) {
      $('#planLimitFields').show();
    } else {
      $('#planLimitFields').hide();
      $('#planLimitFields').find('label.error').remove();
      $('#planLimitFields').find('.error').removeClass('error');
    }
  });

  $(document).on('input', '#closure_city', function () {
    let value = $(this).val();
    if (value.length > 0) {
        $(this).val(value.charAt(0).toUpperCase() + value.slice(1));
    }
  });
  
  function initPlanModalSelect2() {
    $('#planModal .single-select').each(function () {
      var $el = $(this);
      if ($el.hasClass('select2-hidden-accessible')) {
        $el.select2('destroy');
      }
      $el.select2({
        width: '100%',
        dropdownParent: $('#planModal')
      });
    });
  }

  $('#planModal').on('shown.bs.modal', initPlanModalSelect2);

  $(document).ready(function () {
    let originalOptions = $('#input-14').html();
    $('#plan_type').on('change', function () {
        let type = $(this).val();
        if ($('#input-14').hasClass('select2-hidden-accessible')) {
          $('#input-14').select2('destroy');
        }
        $('#input-14').html(originalOptions);
      if (type == '3') {
            $('#input-14 option').each(function () {
                let text = $(this).text().trim();
                if (text == '-- Select Plan --') {
                    return;
                }
                if (
                    text != '1 Month' &&
                    text != '2 Month'
                ) {
                    $(this).remove();
                }
            });
            $('#plan_expire_date').datepicker('destroy');
            let today = new Date();
            let maxDate = new Date();
            maxDate.setMonth(maxDate.getMonth() + 4);
            $('#plan_expire_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                startDate: today,
                endDate: maxDate
            });
            $('#received_by')
            .prop('required', true);

            $('#received_by')
                .attr('required', 'required');

            $('#received_by')
                .rules('remove');

            $('#received_by')
                .rules('add', {
                    required: true,
                    noSpace: true
                });

            $('#received_by')
                .removeClass('error');

            $('#received_by-error').remove();

            $('#received_by_required')
                .removeClass('d-none');
      }
      else if (type == '4') {
            $('#input-14 option').each(function () {
                let text = $(this).text().trim();
                if (
                    text != '-- Select Plan --' &&
                    text != '1 Month'
                ) {
                    $(this).remove();
                }
            });

            $('#input-14').val('1');
            $('#plan_expire_date').datepicker('destroy');
            let today = new Date();
            let max7Days = new Date();
            max7Days.setDate(max7Days.getDate() + 7);
            $('#plan_expire_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                startDate: today,
                endDate: max7Days
            });

            let nextMonth = new Date();
            nextMonth.setMonth(nextMonth.getMonth() + 1);
            let day = ("0" + nextMonth.getDate()).slice(-2);
            let month = ("0" + (nextMonth.getMonth() + 1)).slice(-2);
            let finalDate =
                nextMonth.getFullYear() + "-" + month + "-" + day;
            $('#plan_expire_date').val(finalDate);
            clearPlanExpireDateError();

            $('textarea[name="transaction_amount_remark"]')
            .rules('remove', 'required');

            $('select[name="payment_mode"]')
                .rules('remove', 'required');

            $('textarea[name="transaction_amount_remark"]')
                .removeAttr('required');

            $('select[name="payment_mode"]')
                .removeAttr('required');

            $('textarea[name="transaction_amount_remark"]')
                .removeClass('error');

            $('select[name="payment_mode"]')
                .removeClass('error');

            $('label.error').hide();
            $('#working_days')
                .removeAttr('required');

            $('#working_days')
                .rules('remove');

            $('#working_days')
                .removeClass('error');

            $('#working_days-error').hide();

            $('#remarkDiv').addClass('d-none');
            $('#amountWarning').addClass('d-none');
            $('#amount_required').addClass('d-none');
            $('#payment_mode_required').addClass('d-none');
            $('#remark_required').addClass('d-none');
            $('#payment_attachment_required').addClass('d-none');
            $('#received_by')
            .prop('required', false);
            $('#received_by')
                .removeAttr('required');

            $('#received_by')
                .rules('remove');
            $('#received_by')
                .val('');
            $('#received_by')
                .removeClass('error is-invalid');
            $('#received_by-error').remove();
            $('#received_by_required')
                .addClass('d-none');
      }
      else {
            $('#plan_expire_date').datepicker('destroy');
            let today = new Date();
            $('#plan_expire_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                startDate: today
            });

            $('textarea[name="transaction_amount_remark"]').rules('add', {
                required: true
            });

            $('select[name="payment_mode"]').rules('add', {
                required: true
            });

            $('#working_days')
                .attr('required', true);

            $('#working_days')
                .rules('add', {
                    required: true
                });
            $('#amount_required').removeClass('d-none');
            $('#payment_mode_required').removeClass('d-none');
              $('#received_by')
              .attr('required', true);
            $('#received_by')
            .removeAttr('required');
            $('#received_by')
                .rules('remove');
            $('#received_by')
                .attr('required', true);

            $('#received_by')
                .rules('add', {
                    required: true,
                    noSpace: true
                });

            $('#received_by')
                .valid();
            $('#received_by_required')
                .removeClass('d-none');
        }
        if (type == '1') {
          $('#payment_attachment_required').removeClass('d-none');
        } else {
            $('#payment_attachment_required').addClass('d-none');
        }
        initPlanModalSelect2();
      });
  });
</script>