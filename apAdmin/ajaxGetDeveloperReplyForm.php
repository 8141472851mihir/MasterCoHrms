<?php 

include_once 'common/object.php';
error_reporting(0);
extract(array_map("test_input" , $_POST));
$data = $d->selectArray("feedback_master","feedback_id='$feedback_id'");
?>
<form id="developerReplyFeedbackFrm" action="controller/feedbackController.php" method="post" enctype="multipart/form-data">
  <input type="hidden" name="closebyDeveloper">
  <input type="hidden" id="society_id" name="society_id" value="<?php echo $data['society_id'];?>">
  <input type="hidden" id="feedback_id" name="feedback_id" value="<?php echo $feedback_id;?>">
  <input type="hidden" id="feedback_created_by" name="feedback_created_by" value="<?=$data['created_by']?>">
  <input type="hidden" id="csrf" name="csrf" value="<?php echo $csrf;?>">
  <div class="form-group row">
    <label for="input-10" class="col-sm-4 col-form-label">Reply</label>
    <div class="col-sm-8">
      <textarea maxlength="2500" style="resize: vertical;" class="form-control" name="reply"></textarea>
    </div>
  </div>
  <div class="form-group row">
    <label for="input-10" class="col-sm-4 col-form-label">Attachment File</label>
    <div class="col-sm-8">
      <input class="form-control-file border" type="file" name="attachment">
    </div>
  </div>
  <div class="form-group row">
    <label for="input-10" class="col-sm-4 col-form-label">Send Mail</label>
    <div class="col-sm-8">
      <select class="form-control single-select" name="send_mail">
        <option value="0">Client and Support Executive</option>
        <option value="1">Support Executive</option>
      </select>
    </div>
  </div>
  <!-- <div class="form-group row">
    <div class="col-sm-12">
      <input type="checkbox" name="send_msg" id="send_msg" > <span>Send Text Message?</span>
    </div>
  </div> -->
  <div class="form-footer text-center">
    <input type="hidden" name="previousURL" value="feedback">
    <button type="submit" class="btn btn-primary"><i class="fa fa-check-square-o"></i>Submit</button>
  </div>
</form>