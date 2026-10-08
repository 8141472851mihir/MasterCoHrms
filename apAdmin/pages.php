  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
        <h4 class="page-title">Pages </h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="welcome">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pages</li>
         </ol>
     </div>
     <div class="col-sm-3">
       <div class="btn-group float-sm-right">
        <a href="addPage" class="btn btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add New</a>
       
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
                      <th>Page Name</th>
                      <th>Main Menu</th>
                      <th>Menu Url</th>
                      <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i=1;
                    $q=$d->select("master_menu","parent_menu_id!='NULL' AND page_status=1");
                    $pages = array();
                    $parentMenuIds = array();
                    while ($data=mysqli_fetch_array($q)) {
                      $pages[] = $data;
                      if (isset($data['parent_menu_id']) && $data['parent_menu_id'] !== '' && $data['parent_menu_id'] !== null) {
                        $parentMenuIds[(string)$data['parent_menu_id']] = (int)$data['parent_menu_id'];
                      }
                    }
                    $parentMenusById = array();
                    if (!empty($parentMenuIds)) {
                      $parentIdsIn = implode(',', array_map('intval', $parentMenuIds));
                      $pq = $d->select("master_menu", "menu_id IN ($parentIdsIn)");
                      while ($pdata = mysqli_fetch_array($pq)) {
                        $parentMenusById[$pdata['menu_id']] = $pdata;
                      }
                    }
                    foreach ($pages as $data) {
                     ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo $data['menu_name']; ?></td>
                      <td><?php 
                      $data1 = isset($parentMenusById[$data['parent_menu_id']]) ? $parentMenusById[$data['parent_menu_id']] : null;
                      echo $data1 ? $data1['menu_name'] : ''; ?></td>
                      <td><?php echo $data['menu_link']; ?></td>
                     
                      <td>
                        <form action="addPage" method="post" style ='float: left;'>
                          <input type="hidden" name="page_id" value="<?php echo $data['menu_id']; ?>">
                          <button name="editPage" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Edit Page Details"> <i class="fa fa-pencil"></i></button>
                        </form>
                        <form class="deleteForm<?php echo $data['menu_id']; ?>" style ='float: left;margin-left: 5px;' action="controller/menuController.php" method="post">
                          <input type="hidden" name="page_id_delete" value="<?php echo $data['menu_id']; ?>">
                        <button   name="deletePage" type="button" class="btn btn-danger btn-sm" onclick="deleteData('<?php echo $data['menu_id']; ?>');" data-toggle="tooltip" title="Delete Page"><i class="fa fa-trash-o"></i></button>
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



