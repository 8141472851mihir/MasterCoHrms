<?php 
include_once('../common/objectController.php');
extract($_POST);
if (isset($id)) 
{ 
  $contactId = isset($_POST['id']) ? $d->sanitizeActionIdAsInt($_POST['id']) : 0;
  $condata =$d->selectArray("contact_us","id = '$contactId'");
?>
  <div class="modal-body">
        <div class="row">
          <div class="col-lg-2">
            <label>Name : </label>
          </div>
          <div class="col-lg-10">
            <?php echo $condata['name']; ?>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-2">
            <label>Email : </label>
          </div>
          <div class="col-lg-10">
            <?php echo $condata['email']; ?>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-2">
            <label>Subject : </label>
          </div>
          <div class="col-lg-10">
            <?php echo $condata['subject']; ?>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-2">
            <label>Message : </label>
          </div>
          <div class="col-lg-10">
            <?php echo $condata['message']; ?>
          </div>
        </div>
      
      <?php 
      if(!empty($condata['reply']))
      {
      ?>
      <br>
      <div class="row border">
          <div class="col-lg-2">
            <label>Reply Date : </label>
          </div>
          <div class="col-lg-10">
            <?php echo $condata['reply_sent_date']; ?>
          </div>
        </div>
        <div class="row border">
          <div class="col-lg-2">
            <label>Reply : </label>
          </div>
          <div class="col-lg-10">
            <?php echo $condata['reply']; ?>
          </div>
        </div>
      <?php
      }
      ?>
  </div>
<?php 
} 
?>