<?php 


if($_GET['action'] == "editKyc")
{
  $kyc_api_type = $_GET['kyc_api_type'];
  
  $q= $d->select("document_kyc_master","kyc_api_type=$kyc_api_type","");
  $data = mysqli_fetch_array($q);
  extract($data);
  
  $title = "Edit Api Kyc";
}else{
  $title = "Add Api Kyc";
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
            <form id="addKycValidation" action="controller/documentKycController.php" method="post" >

              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kyc Api Name <span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" autocomplete="off"  class="form-control" maxlength="250" required="" name="kyc_api_name" value="<?php echo $kyc_api_name; ?>">
                </div>
                <label class="col-sm-2 col-form-label">Kyc Api Price <span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" autocomplete="off" class="form-control " maxlength="13" required="" name="kyc_api_price" value="<?php echo $kyc_api_price; ?>">
                </div>               
              </div>
              <div class="form-group row">
              <label class="col-sm-2 col-form-label">kyc api url <span class="required">*</span></label>
                <div class="col-sm-4">
                  <input type="text" autocomplete="off" class="form-control" maxlength="250" id="kyc_api_url"  name="kyc_api_url" value="<?php echo $kyc_api_url; ?>">
                </div>
              <label class="col-sm-2 col-form-label">kyc api Method <span class="required">*</span></label>
                <div class="col-sm-4">
                   <select autocomplete="off" class="form-control single-select" name="api_method" required >
                    <option <?php if($api_method == "POST"){echo "selected";} ?> value="POST">POST</option>
                    <option <?php if($api_method == "GET"){echo "selected";} ?> value="GET">GET</option>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">Send Number In Url <span class="required">*</span></label>
                <div class="col-sm-4">
                  <select autocomplete="off" class="form-control single-select " name="send_number_in_url" required >
                    <option <?php if($send_number_in_url == "0"){echo "selected";} ?> value="0">NO</option>
                    <option <?php if($send_number_in_url == "1"){echo "selected";} ?> value="1">YES</option>
                  </select>
                </div>
                <label class="col-sm-2 col-form-label">Kyc Parent API</label>
                <div class="col-sm-4">
                  <select class="form-control single-select" name="kyc_parent_id">
                      <option value="">-- Select --</option>
                      <?php $qKycMaster=$d->selectRow("document_kyc_master.*","document_kyc_master","document_kyc_master.status=0","");
                              while($row=mysqli_fetch_array($qKycMaster)) { ?>
                                <option <?php echo ($kyc_parent_id==$row['kyc_api_type']) ? 'selected' : '' ; ?> value="<?php echo $row['kyc_api_type']; ?>"><?php echo $row['kyc_api_name']; ?>  </option>
                      <?php } ?>
                  </select>
                </div>    
              </div>
              <div class="form-group row"> 
                <label class="col-sm-2 col-form-label">Row Data Key</label>
                  <div class="col-sm-4">
                    <textarea autocomplete="off" id="row_data_key" class="form-control" name="row_data_key"  aria-invalid="false" rows="5"><?php echo $row_data_key; ?></textarea>
                  </div>  
              </div>
              
              <div class="form-group row">
                <label class="col-sm-2 col-form-label">kyc api curl</label>
                <div class="col-sm-4">
                  <textarea autocomplete="off" id="kyc_api_common" class="form-control" name="kyc_api_common"  aria-invalid="false" rows="5"><?php echo $kyc_api_common; ?></textarea>
                </div> 
                <label class="col-sm-2 col-form-label">Kyc Api Response </label>
                <div class="col-sm-4">
                <textarea autocomplete="off" id="kyc_api_response" class="form-control" name="kyc_api_response"  aria-invalid="false" rows="5"><?php echo $kyc_api_response; ?></textarea>
                </div>
                              
              </div>
              
              <div class="form-footer text-center">
                <?php if(isset($_GET['action']) && $_GET['action'] == "editKyc"){ ?>

                <input type="hidden" name="action" value="editKyc">
                <input type="hidden" name="kyc_api_type" value="<?php echo $kyc_api_type; ?>" >
                <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" >
                <button value="edit Page" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i>Update</button>
                <a href="documentKycList" type="reset" class="btn btn-danger"><i class="fa fa-times"></i> CANCEL</a>

                <?php } else { ?>

                <input type="hidden" name="action" value="addKyc">
                <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" id="token">
                <button value="add Page" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> ADD</button>
                <a href="documentKycList"  class="btn btn-danger"><i class="fa fa-times"></i> CANCEL</a>

                <?php } ?>
              </div>
              
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>