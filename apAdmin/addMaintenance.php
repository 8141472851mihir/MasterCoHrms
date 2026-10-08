<?php 
extract(array_map("test_input" , $_POST));
$maintenanceArray = $d->selectRow("festival_id,festival_name,festival_image","festival_master", "is_festival=1","LIMIT 1");
if (mysqli_num_rows($maintenanceArray)>0) {
  $maintenanceData = mysqli_fetch_assoc($maintenanceArray);
}
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Add /Update (Maintenance)</h4>
        
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <form id="MaintenanceValidation" action="controller/domainController.php" method="post" enctype="multipart/form-data">
              <div class="form-group row">           
               <label for="maintenance_attachment" id="maintenance_attachment" class="col-sm-2 mt-2  col-form-label">Maintenance Attachment</label>

               <div class="<?php echo ($maintenanceData['festival_image']!='' && file_exists("../img/festival/".$maintenanceData['festival_image'])) ? "col-sm-8": "col-sm-10"?> mt-2 " id="maintenance_attachment">
                 <input type="file" accept="image/*" id="maintenance_attachment" name="maintenance_attachment"  class="form-control" >
               </div>
               <div class="form-group col-sm-2">
                <?php ;
                if($maintenanceData['festival_image']!='' && file_exists("../img/festival/".$maintenanceData['festival_image'])){
                  $attachment="../img/festival/".$maintenanceData['festival_image'];
                  ?>
                  <a  data-fancybox="images"  href="<?php echo $attachment; ?>" target="_blank"><img class="lazyload"  src='../img/ajax-loader.gif'  width="100" data-src="<?php echo $attachment; ?>"></a>
                  <?php
                }
                ?>
              </div>
              <input type="hidden" name="maintenance_attachment_old" value="<?php echo $maintenanceData['festival_image'] ?>">
            </div>
            <div class="form-group row">           
              <label for="maintenance_desc" id="maintenance_desc" class="col-sm-2 mt-2  col-form-label">Maintenance Description </label>
              <div class="col-sm-10 mt-2 ">
                <textarea id="maintenance_desc" name="maintenance_desc" autocomplete="off" rows="4" class="form-control"><?php echo $maintenanceData['festival_name'] ?></textarea>
             </div>
           </div>

           <div class="form-footer text-center">
            <input type="hidden" name="updateMaintenance" value="updateMaintenance">
            <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> SAVE</button>
            <button type="reset" class="btn btn-danger"><i class="fa fa-times"></i> RESET</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
</div>
