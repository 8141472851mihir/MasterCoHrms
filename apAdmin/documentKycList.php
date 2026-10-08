 
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-6">
        <h4 class="page-title">Api Kyc List</h4>
      </div>
      <div class="col-sm-6 text-right">
        <a href="documentKycAdd" class="btn btn-primary btn-sm waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add New</a>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div id="tabe-13" class="container-fluid tab-pane">
            <div class="">
              <div class="mt-2">
                <div class="table-responsive">
                  <table id="default-datatable1" class="table table-bordered">
                    <thead>
                      <tr>
                        <th class='deleteTh'>#</th>
                        <th>Action</th>
                        <th>Api Name</th>
                        <th>Api Price</th>
                        <th>Is Sub Api</th>
                        <th>Api Url</th>
                        <th>Created By</th>
                        <th>Created Date</th>
                        <th>Updated By</th>
                        <th>Updated Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $q = $d->selectRow("document_kyc_master.*", "document_kyc_master", "document_kyc_master.status=0", "");
                      $i = 1;
                      while ($row = mysqli_fetch_array($q)) {
                        ?>
                        <tr>
                          <td><?php echo $i++; ?></td>
                          <td class="d-flex">
                            <form class="" method="GET" action="documentKycAdd">
                              <input type="hidden" name="kyc_api_type" value="<?php echo $row['kyc_api_type']; ?>">
                              <input type="hidden" name="action" value="editKyc">
                              <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></button>
                            </form>
                            <?php
                            $kyc_api_type=$row['kyc_api_type'];
                            $kyc_status=$row['status'];
                            $buttonClass = ($kyc_status == "0") ? 'btn-success-new' : 'btn-danger';
                            $buttonCondition = ($kyc_status == "0") ? 'Active' : 'Deactive';
                            $status = ($kyc_status == "0") ? 'kycApiDeactive' : 'kycApiActive';
                            $newStatus = ($kyc_status == "0") ? 'kycApiActive' : 'kycApiDeactive';
                            $newStatusVal = ($kyc_status == "0") ? '1' : '0';
                            $statusValue = ($kyc_status == "0") ? '0' : '1';
                            ?>

                             <input type="button" class="btn btn-sm mx-2 pl-1 pr-1 w-75 <?php echo $buttonClass ?>" id="<?php echo 'kyc_api_' . $kyc_api_type; ?>" onclick="changeStatusNew('<?php echo $kyc_api_type; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'kyc_api_' . $kyc_api_type; ?>','','1');" data-size="small" value="<?php echo $buttonCondition ?>" />
                          </td>
                          <td><?php echo $row['kyc_api_name'] ?></td>
                          <td><?php echo $row['kyc_api_price'] ?></td>
                          <td><?php echo ($row['kyc_parent_id'] > 0) ? 'Yes' : 'No'; ?></td>
                          <td><?php echo $row['kyc_api_url'] ?></td>
                          <td><?php echo $row['created_by'] ?></td>
                          <td><?php echo $row['created_date'] ?></td>
                          <td><?php echo $row['updated_by'] ?></td>
                          <td><?php echo $row['updated_date'] ?></td>
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

    <div class="row pt-2 pb-2">
      <div class="col-sm-12">
        <h4 class="page-title">Fidypay Credentials</h4>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div id="tabe-13" class="container-fluid tab-pane" >
            <div class="mt-2">
              <div class="table-responsive">
                <table id="default-datatable2" class="table table-bordered">
                  <thead>
                    <tr>
                      <th class='deleteTh'>#</th>
                      <th>Action</th>
                      <th>Base URL</th>
                      <th>Client Id</th>
                      <th>Bank name</th>
                      <th>Account Number</th>
                      <th>IFSC Code</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $q=$d->selectRow("fidypay_master.*","fidypay_master","","");
                    $i = 1;
                    while($row=mysqli_fetch_array($q)) {
                      ?>
                      <tr>

                        <td><?php echo $i++; ?></td>
                        <td>
                          <form class="d-inline-block" method="GET" action="fidyPayMasterEdit">
                            <input type="hidden" name="fidypay_id" value="<?php echo $row['fidypay_id']; ?>">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></button>
                          </form>

                        </td>
                        <td><?php echo $row['base_url'];?></td>
                        <td><?php echo $row['client_id'];?></td>
                        <td><?php echo $row['bank_name'];?></td>
                        <td><?php echo $row['account_number'];?></td>
                        <td><?php echo $row['ifsc_code'];?></td>
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
    <!-- End container-fluid-->
  </div><!--End content-wrapper-->
  <!--Start Back To Top Button-->



