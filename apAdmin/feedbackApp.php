<?php
extract(array_map("test_input" , $_REQUEST));
error_reporting(0);

?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-4">
        <h4 class="page-title">Company Not Found Request/Enquiry</h4>
        
      </div>
      <div class="col-sm-5">
        <form action="" method="get" accept-charset="utf-8">
          <select type="text" required="" id="type" onchange="this.form.submit()" class="form-control single-select" name="type">
              <option <?php if(isset($_GET['type']) && $_GET['type'] == 0) {echo "selected";} ?> value="0">All</option>
              <option <?php if(isset($_GET['type']) && $_GET['type'] == 2) {echo "selected";} ?> value="2">Landing Page</option>
              
          </select>
        </form>
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
          <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-info nav-justified">

              <?php
              error_reporting(0);
                extract($_REQUEST);
                $pending_table="";
                $withDeveloper="";
                $solved_table="";
                if($tab==0){
                   $pending_table="active";
                } else if($tab==1){
                  $withDeveloper="active";
                } else if($tab==2){
                  $solved_table="active";
                } else {
                  $pending_table="active";
                }

                if(isset($_GET['type']) && $_GET['type'] != 0 && $_GET['type']==2){
                          $where = " AND enquiry_from=2";
                }
              ?>
                  
              <li class="nav-item">
                <a class="nav-link <?php echo $pending_table;?>" data-toggle="tab" href="#pending_tab"><i class="fa fa-spinner"></i> <span>Pending</span></a>
              </li>
              
              <li class="nav-item">
                <a class="nav-link <?php echo $solved_table;?>" data-toggle="tab" href="#solved_tab"><i class="fa fa-check"></i> <span >Solved</span></a>
              </li>
            </ul>
            <div class="tab-content">
              <div id="pending_tab" class="tab-pane <?php echo $pending_table;?> show">
                <div class="table-responsive">
                  <table id="example" class="table table-bordered">
                    <thead>
                      <tr>
                        <th class="deleteTh">#</th>
                        <th>#</th>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Company</th>
                        <th>Employees</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>City</th>
                        <th>Country</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        $i=1;
                        $q=$d->select("feedback_master","feedback_type=3 AND feedback_status!='2' AND with_developer=0 $where","ORDER BY feedback_id DESC");
                        
                        while ($data=mysqli_fetch_array($q)) {
                        extract($data);
                      ?>
                        <tr>
                          <td class='text-center'>
                            <?php if($role_id==1) { ?>
                            <input type="checkbox" class="multiDelteCheckbox"  value="<?php echo $data['feedback_id']; ?>">
                          <?php } ?>
                          </td>
                          <td><?php echo $i++; ?></td>
                            <td>FB_<?php echo $data['feedback_id']; ?>&nbsp;&nbsp;<?php if($read_by==0){ ?><span class="badge badge-warning">New</span><?php } ?></td>
                            
                          <td class="tableWidth"><?php echo $name; ?></td>
                          <td class="tableWidth"><?php echo $country_code.' '.$mobile; ?></td>
                          <td class="tableWidth"><?php echo $inquiry_company_name; ?></td>
                          <td class="tableWidth"><?php echo $no_of_employee; ?></td>
                          <td class="tableWidth"><?php echo $email; ?></td>
                          <td class=""><?php if ($inquiry_type==1) {
                            echo "Sales";
                          } else  if ($inquiry_type==2) {
                            echo "App Support";
                           }; ?></td>
                          <td class="tableWidth"><?php echo $city; ?></td>
                          <td class="tableWidth"><?php echo $country; ?></td>
                           <td class=""><?php if ($enquiry_from==2) {
                            echo "Landing Page";
                          } else  {
                            echo "App/Web";
                           }; ?></td>

                          <td>
                            <?php 
                              if ($feedback_status==0) {
                                echo "Pending";
                              } else if($feedback_status==1){
                                echo "In Progress";
                              } else if($feedback_status==3){
                                echo "On Hold";
                              } else if($feedback_status==4){
                                echo "Rejected";
                              } else if($feedback_status==5){
                                echo "Closed by Developer";
                              } else if($feedback_status==6){
                                echo "Rejected by Developer";
                              }
                            ?>
                            <?php if(($isTicket==1 && $feedback_status==5) || $isTicket==0){ ?>
                            <button data-toggle="modal" data-target="#closeRemarksModal" title="Query Solved?" class="btn btn-primary btn-sm d-inline-block" onclick="remarkFeedback('<?php echo $feedback_id; ?>','<?php echo $email; ?>');" ><i class="fa fa-check"></i></button>
                            <?php } ?>
                            <?php 
                             
                            ?>
                            <form class="d-inline-block" action="feedbackTimeline" method="POST">
                              <input type="hidden" name="id" value="<?php echo $feedback_id; ?>">    
                              <button type="submit" title="Details" name="" class="btn btn-info btn-sm"><i class="fa fa-link fa-lg"> </i></button>
                            </form>
                          </td>
                          <td>
                            <button data-toggle="modal" data-target="#feedbackModal"
                            class="btn btn-info btn-sm d-inline-block" onclick="infoFeedback('<?php echo $feedback_id; ?>');" ><i class="fa fa-info-circle"></i></button>

                            
                           
                          </td>
                        </tr>

                      <?php } ?> 
                    </tbody>

                  </table>
                </div>
              </div>
              <?php if($tab = 'solved_tab'){ ?>
                <div id="solved_tab" class=" tab-pane <?php echo $solved_table;?> fade show">
                  <div class="table-responsive">
                    <table id="example" class="table table-bordered">
                      <thead>
                        <tr>
                          <th class="deleteTh">#</th>
                          <th>#</th>
                          <th>Id</th>
                          <th>Name</th>
                          <th>Mobile</th>
                          <th>Close Remarks</th>
                          <?php if($role_id==1) { ?>
                          <th>Reply Message</th>
                          <?php } ?>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          $i=1;
                          $q=$d->select("feedback_master","feedback_type=3 AND  feedback_status='2' $where","ORDER BY feedback_id DESC");
                          
                          while ($data=mysqli_fetch_array($q)) {
                          extract($data);
                        ?>
                          <tr>
                            <td>
                             <?php if($role_id==1) { ?>
                              <input type="checkbox" class="multiDelteCheckbox"  value="<?php echo $data['feedback_id']; ?>">
                            <?php } ?>
                          </td>
                            <td><?php echo $i++; ?></td>
                            <td>FB_<?php echo $data['feedback_id']; ?></td>
                            <td class="tableWidth"><?php echo $name; ?></td>
                              <td class="tableWidth"><?php echo $country_code.' '.$mobile; ?></td>
                            <td class="tableWidth"><?php echo $closing_remarks; ?></td>
                            <?php if($role_id==1) { ?>
                            <td class="tableWidth"><?php echo $client_reply_message; ?></td>
                          <?php } ?>
                            <td>
                              <button data-toggle="modal" data-target="#feedbackModal"
                              class="btn btn-info btn-sm d-inline-block" onclick="infoFeedback('<?php echo $feedback_id; ?>');" ><i class="fa fa-info-circle"></i></button>
                              <form class="d-inline-block" action="controller/feedbackController.php" method="post">    
                                <input type="hidden" name="feedback_id" value="<?php echo $feedback_id; ?>">    
                                <input type="hidden" name="deleteFeedback" value="deleteFeedback">                 
                                <button type="submit" name="" class="btn btn-danger btn-sm form-btn"><i class="fa fa-trash-o fa-lg"> </i></button>
                              </form>
                              <form class="d-inline-block" action="feedbackTimeline" method="POST">
                                <input type="hidden" name="id" value="<?php echo $feedback_id; ?>">    
                                <button type="submit" title="Details" name="" class="btn btn-primary btn-sm"><i class="fa fa-link fa-lg"> </i></button>
                              </form>
                            </td>
                          </tr>

                        <?php } ?> 
                      </tbody>

                    </table>
                  </div>
                </div>
              <?php } ?>
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
      <div class="modal-body" id="setReplyForm">
        
      </div>
      
    </div>
  </div>
</div><!--End Modal -->

<div class="modal fade" id="closeRemarksModal">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Closing Remarks</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="remarkFeedbackFrm" action="controller/feedbackController.php" method="POST">
          <input type="hidden" id="society_id" name="society_id" value="<?php echo $society_id;?>">
          <input type="hidden" id="feedback_remark_id" name="feedback_id">
          <input type="hidden" id="feedback_remark_email" name="feedback_email">
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Close By <span class="required">*</span></label>
            <div class="col-sm-8">
              <select class="form-control" required="" name="closing_by">
                <option value="">-- Select --</option>
                <option value="By Mail">By Mail</option>
                <option value="By Text Message">By Text Message</option>
                <option value="By Whatsapp">By Whatsapp</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="input-10" class="col-sm-4 col-form-label">Enter Last Message  <span class="required">*</span></label>
            <div class="col-sm-8">
              <textarea placeholder="Please mention how to this query resolved" class="form-control" rows="6" cols="30" maxlength="2500" style="resize: vertical;" required="" name="closing_remarks"></textarea>
            </div>
          </div>
          <div class="form-footer text-center">
            <button type="submit" name="remarkFeedback" value="remarkFeedback" class="btn btn-primary"><i class="fa fa-check-square-o"></i> Submit</button>
          </div>

        </form>
      </div>
      
    </div>
  </div>
</div>

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

<div class="modal fade" id="feedbackModal">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Feedback</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="infoDiv">
        
     </div>

   </div>
 </div>
</div><!--End Modal -->
<script type="text/javascript">
  function replyFeedback (feedback_id,email) {
    $.ajax({
      url:'ajaxGetReplyForm.php',
      type:'POST',
      data:{feedback_id:feedback_id,email:email,csrf:csrf}
    })
    .done(function(response){
      $('#setReplyForm').html(response);
    });

  }
</script>
<script type="text/javascript">
  function remarkFeedback (feedback_id,email,name) {
    $('#feedback_remark_id').val(feedback_id);
    $('#feedback_remark_email').val(email); 

  }
</script>
<script type="text/javascript">
  function infoFeedback(feedback_id) {
    $.ajax({
      url: "viewFeedbackMessageOther.php",
      cache: false,
      type: "POST",
      data: {feedback_id:feedback_id},
      success: function(response){
        $('#infoDiv').html(response);

      }
    });
  }
</script>


