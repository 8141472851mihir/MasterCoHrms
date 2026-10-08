  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
        <h4 class="page-title">Main Menu</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="welcome">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Main Menu</li>
         </ol>
     </div>
     <div class="col-sm-3">
       <div class="btn-group float-sm-right">
        <a href="menu" class="btn btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add New</a>
       
        </button>
        
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
                        <th>Menu Name</th>
                        <th>Menu Url</th>
                        <th> Icon</th>
                        <th>Sub Menu</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                 <?php 
                  $i=1;
                  $q=$d->select("master_menu","parent_menu_id=0","ORDER BY order_no ASC");
                  while ($data=mysqli_fetch_array($q)) {
                   ?>
                  <tr>
                    <td><?php echo $i++; ?></td>
                    <td>
                      <form action="subMenu" method="post">
                        <input type="hidden"  name="menu_id" value="<?php echo $data['menu_id']; ?>">
                        <button class="btn btn-link" type="submit" name="" value=""><?php echo $data['menu_name']; ?></button>
                      </form>
                      </td>
                    <td><?php echo $data['menu_link']; ?></td>
                    <td><i class="<?php echo $data['menu_icon']; ?>"></i></td>
                    <td><?php if($data['sub_menu']==0) { echo "No";} else { echo "Yes"; } ?></td>
                    <td>
                      <b><?php if($data['status']==0) { echo "<span class='text-success'>Active</span>";} else { echo "<span class='text-danger'>Deactive</span>"; } ?>
                    </b>
                    </td>
                    <td><?php echo $data['order_no']; ?></td>

                    <td class="">
                      
                      <form style="margin-right: 5px;float: left;" action="menu" method="post" style ='float: left;'>
                        <input type="hidden" name="menu_id" value="<?php echo $data['menu_id']; ?>">
                        <button name="editMenu" class="btn btn-primary btn-sm" data-toggle="tooltip" title="edit menu"> <i class="fa fa-pencil"></i></button>
                      </form>
                      <form class="deleteForm<?php echo $data['menu_id']; ?>" style ='float: left;' action="controller/menuController.php" method="post">
                        <input type="hidden" name="menu_id_delete" value="<?php echo $data['menu_id']; ?>">
                      <button   name="deleteMenu" type="button" class="btn btn-danger btn-sm" onclick="deleteData('<?php echo $data['menu_id']; ?>');" data-toggle="tooltip" title="delete menu"><i class="fa fa-trash-o"></i></button>
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
    <!-- End container-fluid-->
    
    </div><!--End content-wrapper-->
   <!--Start Back To Top Button-->



