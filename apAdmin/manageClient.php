<?php $token = $_SESSION['token']; ?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title"> Manage Clients</h4>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="welcome">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Manage Clients</li>
        </ol>
      </div>
      <div class="col-sm-3">
        <div class="btn-group float-sm-right">
          <a href="#addClient" data-toggle="modal" data-target="#addClient" class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add New</a>
          <a href="#" onclick="DeleteAll('deleteClient');" class="btn btn-danger btn-sm waves-effect waves-light"><i class="fa fa-trash-o fa-lg"></i> Delete </a>
        </div>
      </div>
    </div>
    <!-- End Breadcrumb-->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th class="sn-th">#</th>
                    <th>Client Name</th>
                    <th>URL</th>
                    <th>Order No</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i=1;
                  $q=$d->select("clients_master");
                  while($row = $q->fetch_assoc())
                  {
                  ?>
                    <tr>
                      <td class='text-center delete-th'>
                        <input type="checkbox" class="multiDelteCheckbox" value="<?php echo $row['client_id']?>">
                      </td>
                      <td><?php echo $i++; ?></td>
                      <td class="tableWidth"><?php echo $row['client_name']; ?></td>
                      <td class="tableWidth"><?php echo $row['url']; ?></td>
                      <td class="tableWidth"><?php echo $row['order_no']; ?></td>
                      <td class="tableWidth"><img src="../img/clients_image/<?php echo $row['image']; ?>" width="80" alt="<?php echo $row['client_name']; ?> Image"></td>
                      <td class="tableWidth">
                        <?php
                        if($row['status'] == 1)
                        { ?>
                          <div class="pt-1">
                          <label class="switch-custom">
                            <input type="checkbox"  data-color="#15ca20" data-size="small" onchange ="changeStatus('<?php echo $row['client_id']; ?>','clientDeactive','<?php echo $token; ?>');" checked/>
                            <span class="slider-custom round"></span>
                          </label>
                          </div>
                        <?php
                        }
                        else
                        {
                        ?>
                          <div class="pt-1">
                          <label class="switch-custom">
                            <input type="checkbox"  data-color="#15ca20" data-size="small" onchange ="changeStatus('<?php echo $row['client_id']; ?>','clientActive','<?php echo $token; ?>');" />
                            <span class="slider-custom round"></span>
                          </label>
                          </div>
                        <?php }?>
                      </td>
                      <td>
                       <!--  <button name="editClient" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editClient" onclick="editClient(<?php echo $row['client_id'] ?>,'<?php echo $token; ?>')"> <i class="fa fa-pencil"></i> </button> -->
                       <button name="editClient" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#editClient" data-client_id="<?php echo $row['client_id']; ?>" data-client_name="<?php echo htmlspecialchars($row['client_name'], ENT_QUOTES); ?>" data-url="<?php echo htmlspecialchars($row['url'], ENT_QUOTES); ?>" data-order_no="<?php echo $row['order_no']; ?>" data-image="<?php echo $row['image']; ?>" onclick="editClient(this)"> <i class="fa fa-pencil"></i></button>

                      </td>
                    </tr>
                  <?php }?>
                </tbody>
                <tfoot>
                  <tr>
                    <th class="hideSearch">#</th>
                    <th class="hideSearch">#</th>
                    <th>Client Name</th>
                    <th>URL</th>
                    <th>Order No</th>
                    <th class="hideSearch">#</th>
                    <th class="hideSearch">#</th>
                    <th class="hideSearch">#</th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addClient">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Add Client</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container">
          <form id="addClientForm" action="controller/clientController.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="redirectURL" value="manageClient">
            <div class="form-group row">
              <div class="col-sm-6">
                <label for="client_name" class="col-form-label">Client Name <span class="required">*</span></label>
                <input type="text" autocomplete="off" name="client_name" id="client_name" class="form-control" required>
              </div>
              <div class="col-sm-6">
                <label for="url" class="col-form-label">URL <span class="required">*</span></label>
                <input type="url" autocomplete="off" name="url" id="url" class="form-control" required>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-6">
                <label for="image" class="col-form-label">Image <span class="required">*</span></label>
                <input type="file" accept="image/png, image/jpg, image/jpeg" name="image" id="image" class="form-control image-preview" required>
              </div>
              <div class="col-sm-6">
                <label for="order_no" class="col-form-label">Order No <span class="required">*</span></label>
                <input type="number" autocomplete="off" name="order_no" id="order_no" class="form-control" required>
              </div>
            </div>
            <div class="form-group row pt-3">
              <div class="col-sm-6 text-center">
                <img src="" width="100" height="auto" class="preview_img">
              </div>
            </div>
            <div class="form-footer text-center">
              <input type="hidden" name="addClient" value="addClient">
              <input type="hidden" name="csrf" value="<?php echo $token; ?>">
              <button type="submit" name="addClient" class="btn btn-success"><i class="fa fa-check-square-o"></i> Add</button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="editClient">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Edit Client</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container">
          <form id="editClientForm" action="controller/clientController.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="redirectURL" value="manageClient">
            <div class="form-group row">
              <div class="col-sm-6">
                <label for="client_name" class="col-form-label">Client Name <span class="required">*</span></label>
                <input type="text" name="client_name" autocomplete="off" id="client_name_edit" class="form-control" required>
              </div>
              <div class="col-sm-6">
                <label for="url" class="col-form-label">URL <span class="required">*</span></label>
                <input type="url" name="url" autocomplete="off" id="url_edit" class="form-control" required>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-6">
                <label for="image" class="col-form-label">Image <span class="required">*</span></label>
                <input type="file" accept="image/png,image/jpg,image/jpeg" name="image" id="image" class="form-control image-preview">
              </div>
              <div class="col-sm-6">
                <label for="order_no" class="col-form-label">Order No <span class="required">*</span></label>
                <input type="number" autocomplete="off" name="order_no" id="order_no_edit" class="form-control" required>
              </div>
            </div>
            <div class="form-group row pt-3">
              <div class="col-sm-6 text-center">
                <img src="" width="100" height="auto" class="preview_img">
              </div>
            </div>
            <div class="form-footer text-center">
              <input type="hidden" name="old_image" id="old_image">
              <input type="hidden" name="client_id" id="client_id">
              <input type="hidden" name="editClient" value="editClient">
              <input type="hidden" name="csrf" value="<?php echo $token; ?>">
              <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  function editClient(button) {
  // Get data from button attributes
  let client_id = button.getAttribute("data-client_id");
  let client_name = button.getAttribute("data-client_name");
  let url = button.getAttribute("data-url");
  let order_no = button.getAttribute("data-order_no");
  let image = button.getAttribute("data-image");

  // Populate modal fields
  document.getElementById("client_id").value = client_id;
  document.getElementById("client_name_edit").value = client_name;
  document.getElementById("url_edit").value = url;
  document.getElementById("order_no_edit").value = order_no;
  document.getElementById("old_image").value = image;

  // Show preview image if available
  let imgPreview = document.querySelector(".preview_img");
  if (image) {
    imgPreview.src = "../img/clients_image/" + image;
  } else {
    imgPreview.src = "";
  }
}

</script>