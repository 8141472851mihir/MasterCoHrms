<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Templates</h4>
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <?php /* 
          */ ?>
          <a href="addTemplate" class="btn btn-primary btn-sm"><i class="fa fa-plus mr-1"></i>Add Template</a> 
        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered" >
                <thead>
                  <tr>
                    <th class='deleteTh'>#</th>
                    <th>Action</th>
                    <th>Template Name</th>
                    <th>Added Date</th>
                    <th>Added By</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q=$d->select("template_master,bms_admin_master","template_name!='' AND template_status = '0' AND bms_admin_master.admin_id = template_master.created_by_id","ORDER BY template_id DESC");
                  if(mysqli_num_rows($q)>0){
                  while($row=mysqli_fetch_array($q)) {
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td>
                          <form class="d-inline-block" method="POST" action="addTemplate">
                            <input type="hidden" name="editTemplate" value="editTemplate">
                            <input type="hidden" name="template_id" value="<?php echo $row['template_id']; ?>">
                            <button class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></button>
                          </form>
                          <form class="d-inline-block" method="POST" action="controller/templateController.php">
                            <input type="hidden" name="deleteTemplate" value="deleteTemplate">
                            <input type="hidden" name="template_id" value="<?php echo $row['template_id']; ?>">
                            <button class="btn btn-sm btn-danger form-btn"><i class="fa fa-trash-o"></i></button>
                          </form>
                      </td>
                      <td><?php echo $row['template_name']; ?></td>
                      <td><?php echo $row['created_dt']; ?></td>
                      <td><?php echo $row['admin_name']; ?></td>
                    </tr>
                      <?php }} ?>
                    </tbody>

                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div><!--End Modal -->



