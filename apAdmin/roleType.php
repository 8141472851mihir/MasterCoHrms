  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
        <h4 class="page-title">Role Types</h4>
        
     </div>
     <div class="col-sm-3">
       <div class="btn-group float-sm-right">
        <a href="role" class="btn btn-primary btn-sm waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add New</a>
        
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
                        <th>Sr.No</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                  $i=1;
                  $q=$d->select("role_master","");
                  while ($data=mysqli_fetch_array($q)) {
                    
                   ?>
                  <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $data['role_name']; ?></td>
                    
                    <td>
                      <!-- <form action="pagePrivilege" method="post" style ='float: left;margin-right: 5px;'>
                        <input type="hidden" name="role_id" value="<?php echo $data['role_id']; ?>">
                        <button name="editRole" class="btn btn-sm btn-success" data-toggle="tooltip" title="Page Privilege"> <i class="fa fa-edit"></i> Page Privileges</button>
                      </form> -->
                      <form action="role" method="post" style ='float: left;margin-right: 5px;'>
                        <input type="hidden" name="role_id" value="<?php echo $data['role_id']; ?>">
                        <button name="editRole" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Edit Role"> <i class="fa fa-pencil"></i> Menu Privileges</button>
                      </form>
                     <!--  <?php 
                      if ($role!=1 && $role!=2 && $role!=3 && $role!=4 && $roleName!= 'Company Admin' && $roleName!='Supervisor') {
                        ?>
                       
                    
                      <form class="deleteForm<?php echo $data['role_id']; ?>" style ='float: left;' action="controller/menuController.php" method="post">
                          <input type="hidden" name="role_id_delete" value="<?php echo $data['role_id']; ?>">
                        <button   name="deleteRole" type="button" class="btn btn-danger btn-sm" onclick="deleteData('<?php echo $data['role_id']; ?>');" data-toggle="tooltip" title="Delete Role"><i class="fa fa-trash-o"></i></button>
                        </form>
                      <?php } ?> -->
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
    <!-- End container-fluid-->
    
    </div><!--End content-wrapper-->
   <!--Start Back To Top Button-->



