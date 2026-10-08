<?php
include_once('../common/objectController.php');
extract(array_map("test_input", $_POST));
$faq_id = isset($_POST['faq_id']) ? $d->sanitizeActionIdAsInt($_POST['faq_id']) : 0;
$row = $d->selectArray("faq_question_master", "faq_sub_master_id = '$faq_id'");
?>
<form id="editFaqValidation" action="controller/faqController.php" method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>">
  <div class="form-group row">
    <input type="hidden" name="faq_sub_master_id" value="<?php echo $row['faq_sub_master_id']; ?>">

    <label class="col-sm-4 col-form-label">Question <span class="text-danger">*</span></label>
    <div class="col-sm-8">
      <textarea class="form-control" name="faq_question"><?php echo $row['faq_question']; ?></textarea>
    </div>
  </div>
  <div class="form-group row">
    <label for="Answer" class="col-sm-4 col-form-label">Answer <span class="text-danger">*</span></label>
    <div class="col-sm-8">
      <textarea class="form-control" name="faq_answer" rows="5"><?php echo $row['faq_answer']; ?></textarea>
    </div>
  </div>


  <div class="form-group row">
    <label for="platform_type" class="col-sm-4 col-form-label">Platform Type <span class="required">*</span></label>
    <div class="col-sm-8">
      <select required id="platform_type" class="form-control single-select-edit" name="platform_type">
        <option value="0" <?php if ($row['platform_type'] == '0')
          echo 'selected'; ?>>All</option>
        <option value="1" <?php if ($row['platform_type'] == '1')
          echo 'selected'; ?>>App Platform</option>
        <option value="2" <?php if ($row['platform_type'] == '2')
          echo 'selected'; ?>>Web Platform</option>
      </select>
    </div>
  </div>

  <div class="form-group row">
    <label for="faq_attachment" class="col-sm-4 col-form-label">Faq Attachment</label>
    <div class="col-sm-8">
      <?php if (!empty($row['faq_attachment'])): ?>
        <div class="mb-1" id="existingAttachment">
          <strong>Existing Attachment:</strong>
          <a href="../img/<?php echo $row['faq_attachment']; ?>" target="_blank">
            <?php echo $row['faq_attachment']; ?>
          </a>
          <span style="color: red; cursor: pointer;" onclick="removeAttachment()">&#10006;</span>
     </div>
      <?php endif; ?>
      <input type="file" name="faq_attachment" id="faq_attachment" class="form-control mb-2">
      <input type="hidden" name="faq_attachment_old" value="<?php echo $row['faq_attachment']; ?>">
    </div>
  </div>

  <div class="form-group row">
    <label for="category_type" class="col-sm-4 col-form-label">Category Type</label>
    <div class="col-sm-8">
      <select name="category_type" id="category_type" class="form-control single-select-edit" required>
        <option value="">-- Select Category --</option>
        <option value="0" <?= ($row['category_type'] == '0') ? 'selected' : '' ?>>Other</option>
        <option value="-1" <?= ($row['category_type'] == '-1') ? 'selected' : '' ?>>Tracking</option>
        <?php
        $query = $d->selectRow("app_menu_id,menu_title", "resident_app_menu", "");
        while ($menu = mysqli_fetch_assoc($query)) {
          $selected = ($menu['app_menu_id'] == $row['category_type']) ? 'selected' : '';
          echo '<option value="' . $menu['app_menu_id'] . '" ' . $selected . '>' . $menu['menu_title'] . '</option>';
        }
        ?> 
      </select>
    </div> 
  </div>

  <div class="form-group row">
  <label for="language_id" class="col-sm-4 col-form-label">Language Type<span class="required">*</span></label>
  <div class="col-sm-8">
    <select name="language_id" id="language_id" class="form-control single-select-edit" required>
      <option value="">-- Select language --</option>
      <?php
      $query = $d->selectRow("language_master.language_id, language_master.language_name", "language_master", "");
      while ($language = mysqli_fetch_assoc($query)) {
        $selected = ($language['language_id'] == $row['language_id']) ? 'selected' : '';
        echo '<option value="' . $language['language_id'] . '" ' . $selected . '>' . $language['language_name'] . '</option>';
      }
      ?>
    </select>
  </div>
</div>




  <div class="form-footer text-center">
    <button type="submit" name="Editfaq" class="btn btn-success"><i class="fa fa-check-square-o"></i> UPDATE</button>
  </div>

</form>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/select2/js/select2.min.js"></script>
<script type="text/javascript">
  $('.single-select-edit').select2({
    placeholder: "--SELECT--"
  });
</script>
<script src="assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript">
  $("#editFaqValidation").validate({
    errorPlacement: function (error, element) {
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
      faq_master_id: {
        required: true,
      },
      faq_question: {
        required: true,
        normalizer: function (value) {
          return $.trim(value);
        },
        maxlength: 200,
      },
      faq_answer: {
        required: true,
        normalizer: function (value) {
          return $.trim(value);
        },
        maxlength: 700,
      },
    }
  });
  function removeAttachment() {
    const attachmentDiv = document.getElementById('existingAttachment');
    const oldInput = document.getElementById('faq_attachment_old');
    const attachmentInput = document.getElementById('faq_attachment'); 
    if (attachmentDiv) {
        attachmentDiv.style.display = 'none';
    }
    if (oldInput) {
        oldInput.value = ''; 
    }
    if (attachmentInput) {
        attachmentInput.value = ''; 
    }
    
    const attachmentRemoved = document.createElement("input");
    attachmentRemoved.type = "hidden";
    attachmentRemoved.name = "remove_faq_attachment";
    attachmentRemoved.value = "1"; 
    document.getElementById("editFaqValidation").appendChild(attachmentRemoved);
}
</script>