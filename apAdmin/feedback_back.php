<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Feedback</h4>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="welcome">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Feedback</li>
        </ol>
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <a href="javascript:void(0)" onclick="DeleteAll('deleteFeedback');" class="btn  btn-sm btn-danger pull-right"><i class="fa fa-trash-o fa-lg"></i> Delete </a>

        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->


    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th class="deleteTh">
                      <input type="checkbox" class="selectAll" label="check all"  />
                    </th>
                    <th>#</th>
                    <?php  if ($role_id==1 ) { ?>
                      <th>Company Name</th>
                    <?php } ?>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Message</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Attachment</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $i=1;
                    if ($role_id==1) {
                      $q=$d->select("feedback_master,society_master","society_master.society_id=feedback_master.society_id  ","ORDER BY feedback_id DESC");

                    } else {
                      $q=$d->select("feedback_master,society_master","society_master.society_id=feedback_master.society_id  ","ORDER BY feedback_id DESC");

                    }
                    while ($data=mysqli_fetch_array($q)) {
                    extract($data);
                  ?>
                    <tr>
                      <td class='text-center'>
                        <input type="checkbox" class="multiDelteCheckbox"  value="<?php echo $data['feedback_id']; ?>">
                      </td>
                      <td><?php echo $i++; ?></td>
                      <?php  if ($role_id==1) { ?>
                        <td><?php echo $society_name; ?></td>
                      <?php } ?>
                      <td><?php echo $name; ?></td>
                      <td><?php echo $email; ?></td>
                      <td><?php echo $mobile; ?></td>
                      <td><?php  $content = $feedback_msg;


                        $feedback_msg_new = (strlen($feedback_msg) > 20) ? substr($feedback_msg,0,20) : $feedback_msg;
                        echo   $feedback_msg_new;
                        if(strlen($content) > 20){ 
                          echo "...";
                          ?>
                          &nbsp;
                          <button data-toggle="modal" data-target="#ContentModal"
                          class="btn btn-primary btn-sm" onclick="contemtModal('<?php echo $feedback_id; ?>')" >See Message</button>
                          <?php 
                        }  
                        ?>
                      </td>
                      <td><?php echo custom_echo($subject,30); ?></td>
                      <td><?php echo date("d-m-Y h:i A", strtotime($feedback_date_time)); ?></td>
                      <td><?php  if ($attachment!='') { ?>
                        <a target="_blank" href="../img/fin_support/<?php echo $attachment;?>">View </a>
                        <?php } ?>
                      </td>
                      <td>
                        <?php 
                          if ($feedback_status==0) {
                            echo "Pending";
                          } else if($feedback_status==1){
                            echo "In Progress";
                          } else{
                            echo "Solved";
                          }
                        ?>
                        <?php if($feedback_status==0){ ?>
                        <form class="d-inline-block" method="POST" action="controller/feedbackController.php">
                          <input type="hidden" name="inprogress_feedback_id" value="<?php echo $feedback_id; ?>">    
                          <button type="submit" name="inProgessStatus" class="btn btn-sm btn-warning inprogress-btn" data-toggle="tooltip" title="Query In Progress?"><i class="fa fa-spinner"></i></button>
                        </form>
                        <?php } ?>
                        <?php if($feedback_status!=2){ ?>
                        <form class="d-inline-block" method="POST" action="controller/feedbackController.php">
                          <input type="hidden" name="solved_feedback_id" value="<?php echo $feedback_id; ?>">    
                          <button type="submit" name="solvedStatus" class="btn btn-sm btn-success solved-btn" data-toggle="tooltip" title="Query Solved?"><i class="fa fa-check"></i></button>
                        </form>
                        <?php } ?>
                      </td>
                      <td>
                        <button data-toggle="modal" data-target="#replyModal"
                        class="btn btn-primary btn-sm d-inline-block" onclick="replyFeedback('<?php echo $feedback_id; ?>','<?php echo $email; ?>');" ><i class="fa fa-reply"></i></button>

                        <form class="d-inline-block" action="controller/feedbackController.php" method="post">    
                          <input type="hidden" name="feedback_id" value="<?php echo $feedback_id; ?>">    
                          <input type="hidden" name="deleteFeedback" value="deleteFeedback">                 
                          <button type="submit" name="" class="btn btn-danger btn-sm form-btn"><i class="fa fa-trash-o fa-lg"> </i></button>
                        </form>
                      </td>
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
</div>

<div class="modal fade" id="replyModal">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Reply Feedback</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="replyFeedbackFrm" action="controller/feedbackController.php" method="post">
          <input type="hidden" id="society_id" name="society_id" value="<?php echo $society_id;?>">
          <input type="hidden" id="feedback_id" name="feedback_id">
          <input type="hidden" id="feedback_email" name="feedback_email">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Reply</label>
            <div class="col-sm-8">
              <textarea maxlength="300" class="form-control" required="" name="reply"></textarea>
            </div>
          </div>
          <div class="form-footer text-center">
            <button type="submit" name="replyFeedback" value="replyFeedback" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Reply</button>
          </div>

        </form>
      </div>
      
    </div>
  </div>
</div><!--End Modal -->

<div class="modal fade" id="ContentModal">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Message</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group row">
         <div class="col-sm-12" id="content">  </div>
       </div>
     </div>

   </div>
 </div>
</div><!--End Modal -->
<script type="text/javascript">
  function replyFeedback (feedback_id,email,name) {
    $('#feedback_id').val(feedback_id);
    $('#feedback_email').val(email); 

  }
  function contemtModal(feedback_id){
     // $('#content').html(content); 
     $.ajax({
      url: "viewFeedbackMessage.php",
      cache: false,
      type: "POST",
      data: {feedback_id:feedback_id},
      success: function(response){
        $('#content').html(response);

      }
    });
   }
 </script>