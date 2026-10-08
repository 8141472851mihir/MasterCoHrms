<?php 
   $cq=$d->select("society_payment_getway","society_id='$society_id'");
      $paydata=mysqli_fetch_array($cq);

 ?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      
      <div class="col-sm-3 col-12 text-right">
        <?php 
         $cq1=$d->select("society_payment_getway","society_id='$society_id'");
         $paydata11=mysqli_fetch_array($cq1);
         // print_r($paydata11);
         if ($paydata11>1) {
         ?>
        <form id="signupForm"method="post" action="controller/buildingController.php">
          <input type="hidden"  name="removePaymentGetway" value="removePaymentGetway">
           <button type="submit" class="btn btn-danger "><i class="fa fa-trash-o"></i> Remove Details</button>
        </form>
        <?php } ?>
     </div>

    </div>
    <!-- End Breadcrumb-->

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="signupForm" enctype="multipart/form-data" method="post" action="controller/buildingController.php">
              <h4 class="form-header text-uppercase">
                <i class="fa fa-address-book-o"></i>
                Payment Gateway Setting  
              </h4>
              <div class="form-group row">
                <label for="input-10" class="col-sm-2 col-form-label">Payment Getway Company <i class="text-danger">*</i></label>
                <div class="col-sm-10">
                  <select required="" name="payment_getway_master_id" class="form-control">
                   <?php 
                   $i=1;
                   $q=$d->select("payment_getway_master","");
                   while ($data=mysqli_fetch_array($q)) {
                     ?>
                    <option value="<?php echo $data['payment_getway_master_id']; ?>"><?php echo $data['payment_getway_name']; ?></option>
                  <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group row">
                
                <label for="input-17" class="col-sm-2 col-form-label">Merchant Id <i class="text-danger">*</i></label>
                <div class="col-sm-4">
                 <input maxlength="60" type="text" required="" class="form-control" id="input-17" value="<?php echo $paydata['merchant_id']; ?>" name="merchant_id">
                </div>
                <label for="input-19" class="col-sm-2 col-form-label">Merchant Key <i class="text-danger">*</i></label>
                <div class="col-sm-4">
                  <input maxlength="60" type="text" required="" class="form-control" id="input-19" name="merchant_key" value="<?php echo $paydata['merchant_key']; ?>">
                </div>
              </div>

              <div class="form-group row">
               
            
                <label for="input-14" class="col-sm-2 col-form-label">Salt Key <i class="text-danger">*</i></label>
                <div class="col-sm-4">
                  <input maxlength="60" type="text" required="" class="form-control" id="input-14" name="salt_key"  value="<?php echo $paydata['salt_key']; ?>">
                </div>
                 
              </div>

              <div class="form-footer text-center">
                <button type="submit" name="addPaymentGetwat" value="addPaymentGetwat" class="btn btn-success"><i class="fa fa-check-square-o"></i> Update</button>
               <button type="reset"  class="btn btn-danger"><i class="fa fa-times"></i> Reset</button>
                
              </div>
            </form>
          </div>
        </div>
      </div>
    </div><!--End Row-->

  </div>
  <!-- End container-fluid-->

    </div><!--End content-wrapper-->