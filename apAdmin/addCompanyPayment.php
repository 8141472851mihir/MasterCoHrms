<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Manage Company Payment</h4>
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <a href="#" id="addCompanyPaymentBtn" data-toggle="modal" data-target="#addCompanyPayment"
            class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add Company
            Payment</a>
        </div>
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
                    <th>Company Name</th>
                    <th>Company Transaction Amount</th>
                    <th>Company Payment Id</th>
                    <th>Company Response Status</th>
                    <th>Created By</th>
                    <th>Created Date</th>
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
                  </tr>
                </tfoot>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->selectRow("company_transaction_master.company_transaction_amount,company_transaction_master.society_id,society_master.society_id,society_master.society_name,company_transaction_master.company_transaction_date,company_transaction_master.company_transaction_status,company_transaction_master.company_response_status,company_transaction_master.company_payment_id", "company_transaction_master LEFT JOIN society_master ON society_master.society_id = company_transaction_master.society_id", "");
                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo $society_name; ?></td>
                      <td><?php echo $company_transaction_amount; ?></td>
                      <td><?php echo $company_payment_id; ?></td>
                      <td><?php
                      if ($company_response_status == '1') {
                        echo "<span class='text-success font-weight-bold'>Success</span>";
                      } else {
                        echo "<span class='text-danger font-weight-bold'>Failure</span>";
                      }
                      ?></td>
                      <td><?php echo $admin_name; ?></td>
                      <td>
                        <?php
                        if (!empty($company_transaction_date)) {
                          echo date('d F Y h:i A', strtotime($company_transaction_date));
                        } else {
                          echo "";
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

<div class="modal fade" id="addCompanyPayment">
  <div class="modal-dialog modal-md">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Add Company Payment</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="companyPaymentForm" action="controller/companyPaymentController.php" method="post">
          <div class="form-group row">
            <label for="payment_society_id" class="col-sm-4 col-form-label">Company Name<span class="required">
            *
            </span></label>
            <div class="col-sm-8">
              <select id="payment_society_id" name="payment_society_id" class="form-control single-select"
                placeholder="company_name" required>
                <?php
                $companyList = $d->select(
                  "society_master",
                  "society_status='0' AND society_name != '' AND (created_on_society_server = 1)",
                  "ORDER BY society_id DESC"
                );
                while ($data = mysqli_fetch_array($companyList)) {
                  ?>
                  <option value="">--select--</option>
                  <option value="<?php echo $data['society_id']; ?>" <?php echo $selected; ?>>
                    <?php echo $data["society_name"];?> - <?= $data["city_name"]; ?>
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label for="company_transaction_amount" class="col-sm-4 col-form-label">Company Trasaction Amount
              <span class="required">
            *
            </span>
            </label>
            <div class="col-sm-8">
              <input class="onlyNumber form-control" min="100" maxlength="10" autocomplete="off" name="company_transaction_amount"
                id="company_transaction_amount" required></input>
            </div>
          </div>

          <div class="form-footer text-center">
            <input type="hidden" name="payment_id" id="payment_id" value="cash">
            <input type="hidden" name="addCompanyPayment" id="addCompanyPayment" value="addCompanyPayment">
            <button id="rzp-button1" type="submit" class="btn btn-success"><i
                class="fa fa-check-square-o"></i>Add</button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>
<?php
include_once '../apAdmin/lib/model.php';
$m = new model();
$keydb = $m->api_key();
$adminQuery = $d->select("bms_admin_master", "admin_id = $bms_admin_id");
$adminData = mysqli_fetch_array($adminQuery);
$admin_mobile = mysqli_num_rows($adminQuery) > 0 ? $adminData['admin_mobile'] : 0;
$admin_email = mysqli_num_rows($adminQuery) > 0 ? $adminData['admin_email'] : 0;
$country_code = mysqli_num_rows($adminQuery) > 0 ? $adminData['country_code'] : 0;
?>
<script src="assets/js/jquery.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
  $(document).ready(function () {
    $("#companyPaymentForm").validate({
      errorPlacement: function(error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else if (element.hasClass('select2-hidden-accessible')) {
                    error.insertAfter(element.next('span')); 
                    element.next('span').addClass('error').removeClass('valid');
                } else {
                    error.insertAfter(element);
                }
            },
      rules: {
        payment_society_id:{
          required: true,
        },
        company_transaction_amount: {
          required: true,
          min: 100,
          maxlength: 10
        }
      },
      messages: {
        payment_society_id:{
          required: "Please Enter Company Name",
        },
        company_transaction_amount: {
          required: "Please Enter Amount",
          min: "Minimum amount is 100",
          maxlength: "Maximum length is 10"
        }
      },
      submitHandler: function (form, event) {
        event.preventDefault();
        const society_id = $('#payment_society_id').val();
        const societyName = $('#payment_society_id option:selected').text();
        const amount = $('#company_transaction_amount').val();

        $.ajax({
          url: "../commonApi/fidyPayController.php",
          cache: false,
          type: "POST",
          headers: {
            'key': '<?php echo $keydb; ?>'
          },
          data: {
            getApi: "getApi",
            kyc_api_type: 0,
            society_id: society_id
          },
          success: function (response) {
            $(':input[type="submit"]').prop('disabled', true);
            $(".ajax-loader").show();

            // const options = {
            //   "key": response.razorpay_key,
            //   "amount": amount * 100,
            //   "currency": response.currency,
            //   "name": societyName,
            //   "description": "Recharge fidyPay",
            //   "image": "<?php echo htmlspecialchars($base_url); ?>img/logo.png",
            //   "prefill": {
            //     "email": "<?php echo $admin_email; ?>",
            //     "contact": "<?php echo $country_code . '' . $admin_mobile; ?>",
            //   },
            $('#payment_id').val("cash");
            form.submit();
            //   "modal": {
            //     ondismiss: function () {
            //       window.location.reload();
            //     },
            //     escape: false,
            //     backdropclose: false
            //   }
            // };
            // const rzp1 = new Razorpay(options);
            // rzp1.open();
          }
        });
      }
    });

    $('#payment_society_id').on('change', function () {
      $('#payment_id').val('');
    });
  });
</script>