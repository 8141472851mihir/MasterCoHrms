<?php
echo 2;

if(isset($_GET['fidypay_id']) && $_GET['fidypay_id'] !=""){

  $fidypay_id = $_GET['fidypay_id'];
  $q= $d->select("fidypay_master","fidypay_id='$fidypay_id'","");
  $data = mysqli_fetch_array($q);
  extract($data);

}
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Fidypay Credentials Edit</h4>
      </div>
    </div>
    <!-- End Breadcrumb-->

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="editFidyPayValidation" action="controller/documentKycController.php" method="post" >

              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Base URL<span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" autocomplete="off" class="form-control" maxlength="250" required="" name="base_url" value="<?php echo $base_url; ?>">
                </div>
                <label class="col-sm-2 col-form-label">Client Id<span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" autocomplete="off"  class="form-control " maxlength="250"  required="" name="client_id" value="<?php echo $client_id; ?>">
                </div>               
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Client Secret<span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" autocomplete="off" class="form-control" maxlength="250" name="client_secret" value="<?php echo $client_secret; ?>">
                </div>
                <label class="col-sm-2 col-form-label">Authorization<span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" autocomplete="off" class="form-control" maxlength="250" required="" name="authorization" value="<?php echo $authorization; ?>">
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Bank name<span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" autocomplete="off" class="form-control" maxlength="56"  name="bank_name" value="<?php echo $bank_name; ?>">
                </div>
                <label class="col-sm-2 col-form-label">Account Number<span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" autocomplete="off" class="form-control" maxlength="20" required="" name="account_number" value="<?php echo $account_number; ?>">
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">IFSC Code<span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" autocomplete="off" class="form-control" maxlength="28" name="ifsc_code" value="<?php echo $ifsc_code; ?>">
                </div>
              </div>
              
              <div class="form-footer text-center">
                

                <input type="hidden" name="action" value="editFidyPay">
                <input type="hidden" name="fidypay_id" value="<?php echo $fidypay_id; ?>" >
                <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" >
                <button value="edit Page" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> Update</button>
                <a href="documentKycList" type="reset" class="btn btn-danger"><i class="fa fa-times"></i> CANCEL</a>
              </div>
              
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
