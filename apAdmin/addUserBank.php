<?php error_reporting(0);
$cId = isset($_REQUEST['cId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['cId']) : 0;
$dId = isset($_REQUEST['dId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['dId']) : 0;
$bId = isset($_REQUEST['bId']) ? $d->sanitizeReportFilterIdAsInt($_REQUEST['bId']) : 0;
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-3 col-md-6 col-6">
        <h4 class="page-title">Employee Bank Details</h4>
      </div>
      <div class="col-sm-3 col-md-6 col-6 text-right">
        <!-- <a href="hrDocReport" class=" btn btn-sm btn-warning waves-effect waves-light "  ><i class="fa fa-file mr-1"></i> Report</a> -->
        <a href="javascript:void(0);" class="btn btn-sm btn-warning waves-effect waves-light float-right  " data-toggle="modal" data-target="#bulkUpload">Update Bulk Employee Bank</a>
        <a href="addUserBankDetail"  class="btn btn-sm btn-primary waves-effect waves-light "><i class="fa fa-plus mr-1"></i> Add </a>
        <a href="javascript:void(0)" onclick="DeleteAll('deleteUserBank');" class="btn  btn-sm btn-danger  mr-1"><i class="fa fa-trash-o fa-lg"></i> Delete </a>
      </div>
    </div>
    <form action="" class="branchDeptFilter">
      <div class="row pt-2 pb-2">
        <?php include('selectBranchDeptForFilterAll.php'); ?>
        <div class="col-md-1 form-group ">
          <input class="btn btn-success btn-sm" type="submit" name="getReport" class="form-control" value="Get Data">
        </div>
      </div>
    </form>

    <?php
        $invalidDataAry = $_SESSION['invalidDataAry'];
        if (!empty($invalidDataAry)) {
        ?>
            <div class="row">
                <div class="col-lg-12">
                    <h6 class="text-danger">Below Data Is Invalid, Please Check And Upload CSV Again</h6>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Employee Name</th>
                                            <th>Employee Mobile</th>
                                            <th>Row No.</th>
                                            <th>Remark.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($invalidDataAry as $key => $value) {
                                        ?>
                                            <tr>
                                                <td><?php echo ($key + 1); ?></td>
                                                <td><?php echo $value['employee_name']; ?></td>
                                                <td><?php echo $value['mobile']; ?></td>
                                                <td><?php echo $value['rowNo']; ?></td>
                                                <td><?php echo $value['remark']; ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        unset($_SESSION['invalidDataAry']);
        ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <?php 
              ?>
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Sr.No</th>
                    <th>Action</th>
                    <th>Name</th>
                    <th>Branch</th>
                    <th>Department</th>
                    <th>Bank</th>
                    <th>Branch</th>
                    <th>Account No</th>
                    <th>Account Type</th>
                    <th>IFSC</th>
                    <th>Account Holder Name</th>
                    <th>Pan No.</th>
                    <th>CRN No.</th>
                    <th>Esic No.</th>
                    <th>PF/UAN No.</th>
                    <th>Is Primary Account</th>
                  </tr>
                </thead>
                <tbody id="showFilterData">
                  <?php
                  $i = 1;
                  if(isset($bId) && $bId>0) {
                    $blockFilterQuery = " AND users_master.block_id='$bId'";
                  }
                        
                  if (isset($cId) && $cId > 0) {
                    $categoryFilterQuery = " AND hr_document_master.hr_document_category_id='$cId'";
                  }
                  if (isset($dId) && $dId > 0) {
                    $departmentFilterQuery = " AND users_master.floor_id='$dId'";
                  }
                  $q = $d->select("block_master,floors_master,user_bank_master,users_master", "block_master.block_id=floors_master.block_id AND users_master.floor_id=floors_master.floor_id AND users_master.user_id=user_bank_master.user_id AND users_master.delete_status=0 AND user_bank_master.society_id='$society_id' $blockFilterQuery $departmentFilterQuery $blockAppendQueryUser");

                  $counter = 1;
                  while ($data = mysqli_fetch_array($q)) {
                  ?>
                    <tr>
                      <td class="text-center">
                        <input type="hidden" name="id" id="id" value="<?php echo $data['bank_id']; ?>">
                        <input type="checkbox" name="" class="multiDelteCheckbox" value="<?php echo $data['bank_id']; ?>">
                      </td>
                      <td><?php echo $counter++; ?></td>
                      <td>
                        <div class="d-flex align-items-center">
                          <form method="post" accept-charset="utf-8">
                            <input type="hidden" name="bank_id" value="<?php echo $data['bank_id']; ?>">
                            <input type="hidden" name="edit_hr_doc" value="edit_hr_doc">

                            <!-- onclick="UserBankDataSet(<?php //echo $data['bank_id']; ?>)" data-toggle="modal" data-target="#addModal" -->
                            <a href="addUserBankDetail?bank_id=<?php echo $data['bank_id'];?>" class="btn btn-sm btn-primary mr-1" > <i class="fa fa-pencil"></i></a>
                            
                          </form>
                          <?php if($data['change_request_data']!="") { ?>
                            <button type="button" class="btn btn-sm btn-info ml-2 mr-2" onclick='viewBankChangeRequest(<?php echo $data['bank_id']; ?>)'> <i class="fa fa-eye"></i> Change Request</button>

                          <?php } ?>
                        </div>
                      </td>
                      <td><?php echo $data['user_full_name']; ?> (<?php echo $data['user_designation']; ?>)</td>
                      <td><?php echo $data['block_name']; ?></td>
                      <td><?php echo $data['floor_name']; ?></td>
                      <td><?php echo $data['bank_name']; ?></td>
                      <td><?php echo $data['bank_branch_name']; ?></td>
                      <td><?php echo $data['account_no']; ?></td>
                      <td><?php echo $data['account_type']; ?></td>
                      <td><?php echo $data['ifsc_code']; ?></td>
                      <td><?php echo $data['account_holders_name']; ?></td>
                      <td><?php echo $data['pan_card_no']; ?></td>
                      <td><?php echo $data['crn_no']; ?></td>
                      <td><?php echo $data['esic_no']; ?></td>
                      <td><?php echo $data['pf_no']; ?></td>
                      <td><?php  echo  ($data['is_primary']==1) ? 'Yes' : 'No' ; ?></td>
                      
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div><!-- End Row-->

  </div>
  <!-- End container-fluid-->

</div>
<!--End content-wrapper-->
<!--Start Back To Top Button-->



<div class="modal fade" id="BankDetailsModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Employee bank change request</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="billPayDiv" style="align-content: center;">
        <div class="card-body">
          <div id="bankDetails" class="table-responsive">
          
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Import Bulk Bank Account Modal  -->
<div class="modal fade" id="bulkUpload">
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Import Bulk Employee Bank</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="controller/bulkUploadController.php" method="post" enctype="multipart/form-data">
                    <div class="form-group row">
                        <input type="hidden" name="ExportUserBankFormat" value="ExportUserBankFormat" />
                        <input type="hidden" name="blockAppendQueryUser" value="<?php echo $blockAppendQueryUser; ?>" />
                        <input type="hidden" name="blockAppendQuery" value="<?php echo $blockAppendQuery; ?>" />
                        <label class="col-sm-12 col-form-label"><?php echo $xml->string->step; ?> 1 -> <?php echo $xml->string->formatted_csv; ?> <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-check-square-o"></i> Download</button></label>
                        <label class="col-sm-12 col-form-label"><?php echo $xml->string->step; ?> 2 -> <?php echo $xml->string->fill_your_data; ?> </label>
                        <label class="col-sm-12 col-form-label"><?php echo $xml->string->step; ?> 3 -> <?php echo $xml->string->import_file; ?></label>
                        <label class="col-sm-12 col-form-label"><?php echo $xml->string->step; ?> 4 -> <?php echo $xml->string->click_upload_btn; ?></label>
                        <label for="input-10" class="col-sm-12 col-form-label text-danger"> Note: Please Do Not Change Mobile No.</label>


                    </div>
                </form>
                <form id="importValidation" action="controller/bulkUploadController.php" method="post" enctype="multipart/form-data">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label"><?php echo $xml->string->import; ?> CSV <?php echo $xml->string->file; ?> <span class="required">*</span></label>
                        <div class="col-sm-8" id="uploadableData">
                            <input required="" type="file" name="file" accept=".csv" class="form-control-file border">
                        </div>
                    </div>
                    <div class="form-footer text-center">
                        <input type="hidden" name="importBulkUserBank" value="importBulkUserBank">
                        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check-square-o"></i> <?php echo $xml->string->upload; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="assets/js/jquery.min.js"></script>

<script type="text/javascript">
  

  function popitup(url) {
    newwindow = window.open(url, 'name', 'height=800,width=900, location=0');
    if (window.focus) {
      newwindow.focus()
    }
    return false;
  }
</script>
<style>
  .hideupdate {
    display: none;
  }
</style>