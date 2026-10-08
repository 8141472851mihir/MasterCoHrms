<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-3">
        <h4 class="page-title">What's New</h4>
      </div>
      <div class="col">
      <div class="btn-group float-sm-right">
        <a href="addWhatsNew" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add</a>
      </div>
    </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="" class="table table-bordered feedbackTable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Action</th>
                    <th>Title</th>
                    <th>Platform</th>
                    <th>Version</th>
                    <th>Language</th>
                    <th>Created Date</th>
                    <th>Created By</th>
                    <th>Updated Date</th>
                    <th>Updated By</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i=1;
                  $patch_qry=$d->selectRow("patch_master.*,bms_admin_master.admin_name,updated.admin_name AS updated_by_name,language_master.language_name","patch_master LEFT JOIN bms_admin_master ON bms_admin_master.admin_id=patch_master.created_by LEFT JOIN bms_admin_master AS updated ON updated.admin_id=patch_master.updated_by LEFT JOIN language_master ON language_master.language_id=patch_master.language_id","","ORDER BY patch_id DESC");
                  while($patch_data=mysqli_fetch_array($patch_qry)) {
                    ?>
                    <tr>
                    <td><?php echo $i++; ?></td>
                    <td class="d-flex">
                      <form action="addWhatsNew" class="mx-1" method="get">
                        <input type="hidden" name="patch_id" id="patch_id" value="<?php echo $patch_data['patch_id']; ?>">
                        <input type="hidden" name="language_id" id="language_id" value="<?php echo $patch_data['language_id']; ?>">
                        <button class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></button>
                      </form>
                      <a href="javascript:void(0)" onclick="deleteSingleWhatsNew('<?php echo $patch_data['patch_id']; ?>');" class="btn  btn-sm btn-danger pull-right"><i class="fa fa-trash-o fa-lg"></i></a>
                    </td>
                    <td><?php echo $patch_data['patch_title']; ?></td>
                    <td><?php 
                    if($patch_data['platform']=='0'){
                      echo "Android";
                    }elseif($patch_data['platform']=='1'){
                      echo "iOS";
                    }else{
                      echo "Web";
                    }?></td>
                    <td><?php echo $patch_data['version']; ?></td>
                    <td><?php echo $patch_data['language_name']; ?></td>
                    <td><?php $date1 = new DateTime($patch_data['created_date']);
                    $formattedDate1 = $date1->format('d M Y h:i A'); echo ($patch_data['created_date']!='')?$formattedDate1:''; ?></td>
                    <td><?php echo $patch_data['admin_name']; ?></td>
                    <td><?php
                    $date = new DateTime($patch_data['updated_date']);
                    $formattedDate = $date->format('d M Y h:i A'); echo ($patch_data['updated_date']!='')?$formattedDate:''; ?></td>
                    <td><?php echo $patch_data['updated_by_name']; ?></td>
                  </tr>
                    <?php
                  }
                  ?>
                  
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<style>
    [role="log"] {
        display: none !important;
    }
</style>