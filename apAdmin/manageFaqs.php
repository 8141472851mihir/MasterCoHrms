<?php $token = $_SESSION['token']; ?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">FAQ</h4>
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <a href="#addcategory" data-toggle="modal" data-target="#addcategory"
            class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add New</a>
          <a href="#" onclick="DeleteAll('deletefaq');" class="btn btn-danger btn-sm waves-effect waves-light"><i
              class="fa fa-trash-o fa-lg"></i> Delete </a>

        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th class="sn-th">#</th>
                    <th>Question </th>
                    <th>Answer</th>
                    <th>PlatForm Type</th>
                    <th>Faq Attachment</th>
                    <th>Category Type</th>
                    <th>Language Type</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->selectRow("faq_question_master.*,resident_app_menu.menu_title,resident_app_menu.app_menu_id,
                  language_master.language_id,language_master.language_name", "faq_question_master
                  LEFT JOIN resident_app_menu ON faq_question_master.category_type=resident_app_menu.app_menu_id
                  LEFT JOIN language_master ON faq_question_master.language_id=language_master.language_id","");
                  while ($row = mysqli_fetch_array($q)) {
                    ?>
                    <tr>
                      <td class='text-center delete-th'>
                        <!-- dharti 9-1-2025 -->
                        <input type="checkbox" class="multiDelteCheckbox" value="<?php echo $row['faq_sub_master_id'] ?>">
                        <!-- dharti 9-1-2025 end-->
                      </td>
                      <td><?php echo $i++; ?></td>
                      <td class="tableWidth">
                        <?php
                        echo strlen($row['faq_question']) > 50
                          ? substr($row['faq_question'], 0, 50) . '...'
                          : $row['faq_question'];
                        ?>
                      </td>
                      
                      <td class="tableWidth">
                        <?php
                        echo strlen($row['faq_answer']) > 60
                          ? substr($row['faq_answer'], 0, 60) . '...'
                          : $row['faq_answer'];
                        ?>
                      </td>
                      
                      
                      <td>
                        <?php
                        if ($row['platform_type'] == 0) {
                          echo "All";
                        } elseif ($row['platform_type'] == 1) {
                          echo "App";
                        } elseif ($row['platform_type'] == 2) {
                          echo "Web";
                        }
                        ?>
                      </td>
                      <td>
                        <?php
                        $file = $row['faq_attachment'];
                        $filePath = "../img/" . $file;
                        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                        if (!empty($file) && file_exists($filePath)) {
                          if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo "<img src='$filePath' alt='attachment' width='80' height='80'>";
                          } elseif ($extension === 'pdf') {
                            echo "<a href='$filePath' target='_blank'>View PDF</a>";
                          } else {
                            echo "Unsupported File";
                          }
                        } else {
                          echo "No File";
                        }
                        ?>
                      </td>
                      <td>
                          <?php
                          if($row['category_type'] == 0){
                            echo "Other";
                          } elseif($row['category_type'] == -1){
                            echo "Tracking";
                          }else{
                            echo $row['menu_title'];
                          }
                          ?>
                      </td>
                      <td>
                          <?php echo $row['language_name']; ?>
                      </td>
                      <td class="tableWidth">
                        <label class="switch-custom">
                        <?php
                        if ($row['status'] == 1) { ?>
                            <input type="checkbox"  data-color="#15ca20" data-size="small"
                              onchange="changeStatus('<?php echo $row['faq_sub_master_id']; ?>','faqDeactive','<?php echo $token; ?>');"
                              checked />
                              
                              <?php
                        } else {
                          ?>
                            <input type="checkbox"  data-color="#15ca20" data-size="small"
                            onchange="changeStatus('<?php echo $row['faq_sub_master_id']; ?>','faqActive','<?php echo $token; ?>');" />
                          <?php } ?>
                          <span class="slider-custom round"></span>

                        </label>
                      </td>
                      <td>
                        <button name="editRole" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editFaq"
                          onclick="getEditData(<?php echo $row['faq_sub_master_id'] ?>)"> <i class="fa fa-pencil"></i>
                        </button>
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
<script type="text/javascript">
  function getEditData(faq_id) {
    $.ajax({
      url: 'ajax/editFaq.php',
      type: 'POST',
      data: { faq_id: faq_id }
    })
      .done(function (response) {
        $('#setEditData').html(response);
      });
  }
</script>
<div class="modal fade" id="addcategory">
<div class="modal-dialog modal-lg"> 
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">FAQ</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="faqQuestionValidation" action="controller/faqController.php" method="post"
          enctype="multipart/form-data">
          <input type="hidden" name="redirectURL" value="faq">
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Question <span class="required">*</span></label>
            <div class="col-sm-8">
              <textarea class="form-control" id="faq_question" name="faq_question" required=""></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label for="Answer" class="col-sm-4 col-form-label">Answer <span class="required">*</span></label>
            <div class="col-sm-8">
              <textarea class="form-control" id="faq_answer" name="faq_answer" required=""></textarea>
            </div>
          </div>

          <div class="form-group row">
            <label for="platform_type" class="col-sm-4 col-form-label"> Platform type <span
                class="required">*</span></label>
            <div class="col-sm-8">
              <select type="text" required="" id="platform_type" class="form-control single-select"
                name="platform_type">
                <option value="0">All</option>
                <option value="1">
                  App Platform</option>
                <option value="2">
                  Web Platform</option>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label for="faq_attachment" class="col-sm-4 col-form-label">Faq Attachment</label>
            <div class="col-sm-8">
              <input type="file" name="faq_attachment" id="faq_attachment" class="form-control">
            </div>
          </div>

          <div class="form-group row">
            <label for="category_type" class="col-sm-4 col-form-label">Category Type<span
            class="required">*</span></label>
            <div class="col-sm-8">
              <select name="category_type" id="category_type" class="form-control single-select" required="">
                <option value="">-- Select Category --</option>
                <option value="0">Other</option>
                <option value="-1">Tracking</option>
                <?php
                $query = $d->selectRow("app_menu_id,menu_title", "resident_app_menu", "");
                while ($row = mysqli_fetch_assoc($query)) {
                  echo '<option value="' . $row['app_menu_id'] . '">' . $row['menu_title'] . '</option>';
                }
                ?>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label for="language_id" class="col-sm-4 col-form-label">language Type<span
            class="required">*</span></label>
            <div class="col-sm-8">
              <select name="language_id" id="language_id" class="form-control single-select" required="">
                <option value="">-- Select language --</option>
                <?php
                $query = $d->selectRow("language_master.language_id,language_master.language_name", "language_master", "");
                while ($row = mysqli_fetch_assoc($query)) {
                  echo '<option value="' . $row['language_id'] . '">' . $row['language_name'] . '</option>';
                }
                ?>
              </select>
            </div>
          </div>


          <div class="form-footer text-center">
            <input type="hidden" name="faq_category_id" id="faq_category_id"
              value="<?php echo $res['faq_category_id']; ?>">
            <button type="submit" name="Addfaq" class="btn btn-success"><i class="fa fa-check-square-o"></i>
              Add</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="editFaq">
<div class="modal-dialog modal-lg"> 
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">FAQ</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="setEditData">
      </div>
    </div>
  </div>
</div> 