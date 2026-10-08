<?php 


if($_GET['action'] == "editKycApiCompay")
{
  $cp_id = $_GET['cp_id'];
  
  $q= $d->select("kycapi_companyprice_master","cp_id=$cp_id","");
  $data = mysqli_fetch_array($q);
  extract($data);
  
  $title = "Edit Api ";
}else{
  $title = "Add Api ";

}
    
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title"><?php echo $title; ?> </h4>
      </div>
    </div>
    <!-- End Breadcrumb-->

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="addKycApiCompanyPrice" action="controller/kycApiCompanyPriceController.php" method="post" >

              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Company Name <span class="required">*</span></label>
                <div class="col-sm-4">
                 <?php
                    $q_society_master= $d->select("society_master","",""); 
                 ?>
                   <select autocomplete="off" class="form-control single-select" name="society_id" id="society_id_kycApi"  
                    <?php 
                      if($_GET['action'] == "editKycApiCompay"){ echo "disabled";}
                    ?>  
                   >
                    <?php while($row_society_id = mysqli_fetch_array($q_society_master)){ ?>
                    <option value="">Select </option>
                    <option <?php if($society_id == $row_society_id['society_id']){echo "selected";} ?> value="<?php echo $row_society_id['society_id']?>"><?php echo $row_society_id['society_name']; ?></option>
                  <?php } ?>
                  </select>
                </div>
                <label class="col-sm-2 col-form-label">Api Name <span class="required">*</span></label>
                <div class="col-sm-4">
                 <?php
                    $q_kyc_pid= $d->select("document_kyc_master","status=0",""); 
                 ?>
                   <select autocomplete="off" class="form-control single-select" name="kyc_api_type" id="kyc_api_type_ccp"
                    <?php 
                      if($_GET['action'] == "editKycApiCompay"){ echo "disabled";}
                    ?>  
                   >
                    <?php while($row_kyc_pid = mysqli_fetch_array($q_kyc_pid)){ ?>
                    <option value="">Select </option>
                    <option <?php if($kyc_api_type == $row_kyc_pid['kyc_api_type']){echo "selected";} ?> value="<?php echo $row_kyc_pid['kyc_api_type']?>"><?php echo $row_kyc_pid['kyc_api_name']; ?></option>
                  <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group row">

                <label class="col-sm-2 col-form-label">Api Price</label>
                <div class="col-sm-4">
                  <input type="text" autocomplete="off" class="form-control " maxlength="13" name="custom_price" value="<?php echo $custom_price; ?>">
                </div>  
               
              </div>
              
              <div class="form-footer text-center">
                <?php if(isset($_GET['action']) && $_GET['action'] == "editKycApiCompay"){ ?>

                <input type="hidden" name="action" id="action" value="editCompanyPrice">
                <input type="hidden" name="cp_id" value="<?php echo $cp_id; ?>" >
                <input type="hidden" name="society_id"  value="<?php echo $society_id; ?>">
                <input type="hidden" name="kyc_api_type"  value="<?php echo $kyc_api_type; ?>">
                <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" >
                <button value="edit Page" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i>Update</button>
                <a href="kycApiCompanyPriceList" type="reset" class="btn btn-danger"><i class="fa fa-times"></i> CANCEL</a>

                <?php } else { ?>

                <input type="hidden" name="action" id="action" value="addCompanyPrice">
                <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" id="token">
                <button value="add Page" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> ADD</button>
                <a href="kycApiCompanyPriceList"  class="btn btn-danger"><i class="fa fa-times"></i> CANCEL</a>

                <?php } ?>
              </div>
              
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
    //$(".single-select").select2({placeholder: "--SELECT--",});
</script>