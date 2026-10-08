<?php 
include_once('../common/objectController.php');
extract($_POST);
if (isset($id)) 
{ 
  $contactId = isset($_POST['id']) ? $d->sanitizeActionIdAsInt($_POST['id']) : 0;
  $condata =$d->selectArray("contact_us","id = '$contactId'");
?>
  <div class="modal-body">
    <form id="reply_user_form" name="reply_user_form" action="controller/replyController.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($condata['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="user_id" value="<?php echo (int)$contactId; ?>">

      <div class="form-group row">
        <div class="col-sm-8" >
          <textarea id='long_desc' name='long_desc' required></textarea>
        </div>
      </div>

      <div class="form-footer text-center">
        <button type="submit" id="categoryBtnEdit" name="send_reply" value="send_reply" class="btn btn-success"><i class="fa fa-check-square-o"></i>Reply</button>
      </div>
    </form>

  </div>
  <script src="assets/js/ckeditor/ckeditor.js" ></script>
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>
  <script type="text/javascript">
    CKEDITOR.replace('long_desc',{
      width: "480px",
      height: "170px"
    }); 
</script>

<script type="text/javascript">  
$(function() {
  $("form[name='reply_user_form']").validate({
    rules: 
    {
      long_desc: "required"
    },
    messages: 
    {
      long_desc: "Please enter your reply"
    },
    submitHandler: function(form) {
      form.submit();
    }
  });
});
  </script>
<?php 
} 
?>