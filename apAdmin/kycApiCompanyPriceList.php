
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumb-->
       <div class="row pt-2 pb-2">
          <div class="col-sm-6">
          <h4 class="page-title">Kyc Api Company Price List</h4>
          </div>
          <div class="col-sm-6 text-right">
            <a href="kycApiCompanyPrice" class="btn btn-primary btn-sm waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add New</a>
          </div>
       </div>

      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="">
              <div id="tabe-13" class="container-fluid tab-pane" >
                  <div class="">
                    <div class="mt-2">
                      <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
                      <div class="">
                        <div class="table-responsive">
                        <table id="default-datatable1" class="table table-bordered">
                          <thead>
                            <tr>
                              <th class='deleteTh'>#</th>
                              <th>Action</th>
                              <th>Company Name</th>
                              <th>Api Name</th>
                              <th>Api Price</th>                          
                              <th>Created By</th>
                              <th>Created Date</th>
                              <th>Updated By</th>
                              <th>Updated Date</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $q=$d->selectRow("kycapi_companyprice_master.*,society_master.society_id,society_master.society_name, document_kyc_master.kyc_api_type,document_kyc_master.kyc_api_name","kycapi_companyprice_master LEFT JOIN society_master ON society_master.society_id=kycapi_companyprice_master.society_id LEFT JOIN document_kyc_master ON document_kyc_master.kyc_api_type=kycapi_companyprice_master.kyc_api_type","");
                              $i = 1;
                              while($row=mysqli_fetch_array($q)) {
                            ?>
                              <tr>

                                <td><?php echo $i++; ?></td>
                                <td>
                                  <form class="d-inline-block" method="GET" action="kycApiCompanyPrice">
                                    <input type="hidden" name="cp_id" value="<?php echo $row['cp_id']; ?>">
                                    <input type="hidden" name="action" value="editKycApiCompay">
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></button>
                                  </form>
                                  <form class="d-inline-block" method="POST" action="controller/kycApiCompanyPriceController.php">
                                    <input type="hidden" name="action" value="delKycApiCompany">
                                    <input type="hidden" name="cp_id" value="<?php echo $row['cp_id']; ?>">
                                    <button  class="btn btn-sm btn-danger form-btn"><i class="fa fa-trash-o"></i></button>
                                  </form>
                                </td>
                                <td><?php echo $row['society_name'] ?></td>
                                <td><?php echo $row['kyc_api_name'] ?></td>
                                <td><?php echo $row['custom_price'] ?></td>
                                
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
      </div>

    </div>
    <!-- End container-fluid-->
  </div><!--End content-wrapper-->
   <!--Start Back To Top Button-->



