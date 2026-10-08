 <?php
 $admin_id = $bms_admin_id;
 ?>
 <div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-6">
        <h4 class="page-title">Manage Developers</h4>
      </div>
      <!-- <div class="col-sm-6 text-right">
        <button type="button" class="btn btn-primary btn-sm mx-2" data-toggle="modal" data-target="#bulkUploadModal" style="border-radius: 5px;">Import Bulk</button>
        <a  href="#" class="btn btn-primary btn-sm" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#addEmp" id="addDeveloperBtn"><i class="fa fa-plus mr-1" ></i> Add Developer</a>
      </div> -->
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card mt-4">
          <div class="card-body px-0">
            <div id="tabe-13" class="container-fluid tab-pane active show">
              <div class="">
                <div class="">
                  <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
                  <div class="">
                    <div class="table-responsive">
                      <table id="default-datatable1" class="table table-bordered">
                        <thead>
                          <tr>
                            <th class=''>#</th>
                            <th>Name</th>
                            <th>Technology</th>
                            <th>Email</th>
                            <th>Mobile No</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $curl = curl_init();
                          curl_setopt_array($curl, array(
                            CURLOPT_URL => $d->support_url().'adminController.php',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => array('getAdminList' => 'getAdminList', 'bug_platform_id' => '0'),
                            CURLOPT_HTTPHEADER => array(
                              'Cookie: PHPSESSID=00l5h8m23lu92oclec56tfokqd'
                            ),
                          ));
                          $response = curl_exec($curl);
                          curl_close($curl);
                          $developers = json_decode($response, true);
                          if (!empty($developers)) {
                            $i = 1;
                            foreach ($developers['list'] as $data) {
                              ?>
                              <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $data['admin_name'] ?></td>
                                <td>
                                  <?php
                                  $tech_map = [1 => "Android", 2 => "IOS", 3 => "Web", 4 => "Api", 5 => "Flutter", 6 => "QA"];
                                  echo implode(', ', array_map(fn($p) => $tech_map[(int)$p] ?? '', explode(',', $data['bug_platform'])));
                                  ?>
                                </td>
                                <td><?php echo $data['admin_mail'] ?></td>
                                <td><?php echo $data['admin_mobile'] ?></td>
                              </tr>
                              <?php
                            }
                          }
                          
                          ?>
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
  <!-- add developer start -->
  <div class="modal fade" id="addEmp">
    <div class="modal-dialog">
      <div class="modal-content border-primary">
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white">ADD developer</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="controller/developerController.php" method="post" id="addNewDeveloper">
            <input type="hidden" name="addDeveloper" value="addDeveloper">
            <input type="hidden" name="admin_id" value="<?= $admin_id ?>">
            
            <div class="form-group row mx-0">
              <label class="col-sm-3  col-form-label">Name <span class="required">*</span></label>
              <div class="col-sm-8">
                <input type="text" autocomplete="off" class="form-control" maxlength="50" name="developer_name" placeholder="Full Name" required="">
              </div>
            </div>

            <div class="form-group row mx-0">
              <label class="col-sm-3  col-form-label">Technology<span class="required">*</span></label>
              <div class="col-sm-8">
                <select  type="text" autocomplete="off" class="form-control form-select single-select" name="developer_technology" id="add_developer_technology" required="">
                  <option value="">-- Select Type --</option>
                  <option value="1">IOS</option>
                  <option value="2">Web </option>
                  <option value="3">Android</option>
                  <option value="4">Flutter</option>
                  <option value="5">QA</option>
                </select>
              </div>
            </div>

            <div class="row mx-0 justify-content-center card-footer">
              <button type="submit" class="btn btn-primary">ADD</button>
              <button type="reset" class="btn btn-danger mx-2" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i> CLOSE</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- add developer end-->
  <!-- edit developer start -->
  <div class="modal fade" id="editEmp">
    <div class="modal-dialog">
      <div class="modal-content border-primary">
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white">EDIT developer</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="controller/developerController.php" method="post" id="editDeveloper">
            <input type="hidden" name="editDeveloper" value="editDeveloper">
            <input type="hidden" name="admin_id" value="<?= $admin_id ?>">
            <input type="hidden" name="developer_id" value="" id="developer_id">
            
            <div class="form-group row mx-0">
              <label class="col-sm-3  col-form-label">Name <span class="required">*</span></label>
              <div class="col-sm-8">
                <input type="text" class="form-control" autocomplete="off" maxlength="50" name="developer_name" id="developer_name" placeholder="Full Name" value="" required="">
              </div>
            </div>

            <div class="form-group row mx-0">
              <label class="col-sm-3  col-form-label">Technology<span class="required">*</span></label>
              <div class="col-sm-8">
                <select  type="text" autocomplete="off" class="form-control single-select" id="developer_technology" name="developer_technology" required="">
                  <option value="">-- Select Type --</option>
                  <option value="1">IOS</option>
                  <option value="2">Web </option>
                  <option value="3">Android</option>
                  <option value="4">Flutter</option>
                  <option value="5">QA</option>
                </select>
              </div>
            </div>

            <div class="row mx-0 justify-content-center card-footer">
              <button type="submit" class="btn btn-primary">UPDATE</button>
              <button type="reset" class="btn btn-danger mx-2" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i> CLOSE</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- edit developer end-->

  <!-- Import bulk modal start -->
  <div class="modal fade" id="bulkUploadModal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content border-primary">
        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white">Import Bulk</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <form action="controller/developerController.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?php echo $_SESSION['token']; ?>">
            <div class="form-group row mx-0">
              <label class="col-sm-12 col-form-label">STEP 1 -> DOWNLOAD FORMATTED CSV <button type="submit" name="ExportDeveloperInfoFormat" value="ExportDeveloperInfoFormat" class="btn btn-sm btn-primary"><i class="fa fa-check-square-o"></i> Download</button></label>
              <label class="col-sm-12 col-form-label">STEP 2 -> Fill ALL MANDATORY FIELDS ACCURATELY</label>
              <!-- <label class="col-sm-12 col-form-label">STEP 3 -> SET PRICE TO ZERO IF SERVICE NOT PROVIDED</label> -->
              <label class="col-sm-12 col-form-label">STEP 3 -> FILL YOUR DATA</label>
              <label class="col-sm-12 col-form-label">STEP 4 -> IMPORT THIS FILE HERE</label>
              <label class="col-sm-12 col-form-label">STEP 5 -> CLICK ON UPLOAD BUTTON</label>
              <label class="col-sm-12 col-form-label">STEP 6 -> WRITE A TECHNOLOGY NUMBER IN TECHNOLOGY FIELD.
                <ol>
                  <li> IOS</li>
                  <li> PHP</li>
                  <li> ANDROID</li>
                  <li> FLUTTER</li>
                  <li> NODE</li>
                  <li> QA</li>
                </ol>
              </label>
              <label class="col-sm-12 col-form-label text-danger">NOTE : PLEASE ENTER ALL VALID DETAILS</label>
            </div>
          </form>
          <form id="devBulkImport" action="controller/developerController.php" method="post" enctype="multipart/form-data">
            <div class="form-group row mx-0">
              <label for="csv_file" class="col-sm-4 col-form-label">Import CSV File<span class="text-danger">*</span></label>
              <div class="col-sm-8">
                <input  type="file" name="file" id="csv_file" autocomplete="off"  accept=".csv" class="form-control px-1-file " required="">
              </div>
            </div>
            <div class="text-center">
              <input type="hidden" name="developerDataBulkupload" value="developerDataBulkupload">
            </div>
            <div class="modal-footer">
              <input type="hidden" name="csrf" value="<?php echo $_SESSION['token']; ?>">
              <button type="button" class="btn btn-danger btn-sm mmw-70" data-dismiss="modal">CLOSE</button>
              <button type="submit" name="" value="" class="btn btn-sm btn-success">UPLOAD</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
<!-- Import bulk modal end -->