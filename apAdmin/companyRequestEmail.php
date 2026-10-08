 <?php 
                 $i=1;
                 $q=$d->select("society_create_email_receipt","active_status=0 ","");
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9 col-9">
        <h4 class="page-title">Company Create Email Receipt </h4>
      </div>
      <div class="col-sm-3 col-3">
        <div class="btn-group float-sm-right">
          <a href="javascript:void(0)" onclick="DeleteAll('deleteComplaintEmail');" class="btn btn-danger btn-sm waves-effect waves-light"><i class="fa fa-trash-o fa-lg"></i> Delete </a> 
        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
             
                <form id="penaltyAdd" action="controller/societyRequestController.php" enctype="multipart/form-data" method="post">
              
               

                <div class="form-group row">
                    <label for="input-10" class="col-sm-2 col-form-label"> Email <span class="required">*</span></label>
                    <div class="col-sm-10" >
                      <input type="email" maxlength="300" required="" name="email_id" class="form-control "></textarea>
                    </div>
                </div>
                
               
                <div class="form-footer text-center">
                  <?php //IS_1019 addPenaltiesBtn ?>

                  <button id="addPenaltiesBtn" type="submit"  class="btn btn-success"><i class="fa fa-check-square-o"></i> Add</button>
                 <input type="hidden" name="addComaplintEmail"  value="addComaplintEmail">
                  <?php //IS_837 onclick="resetFrm('penaltyAdd');" ?>
                  <button type="reset"  value="add" class="btn btn-danger cancel" ><i class="fa fa-check-square-o"></i> Reset</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div><!--End Row-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>#</th>
                    <th>Email</th>

                  </tr>
                </thead>
                <tbody>
                <?php
                 while($row=mysqli_fetch_array($q))
                 {
                  extract($row);
                  ?>
                  <tr>
                    <td class='text-center'>
                        <input type="checkbox" class="multiDelteCheckbox"  value="<?php echo $row['email_receipt_id']; ?>">
                    </td>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $email_id; ?></td>
                  </tr>
                  <?php }?>

                </tbody>

              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

