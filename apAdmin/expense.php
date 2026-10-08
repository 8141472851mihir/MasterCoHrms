<?php
extract($_REQUEST);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>
<div class="content-wrapper">
  <div class="container-fluid">
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Manage Expense</h4>
      </div>
      <div class="col-sm-3 col-md-3 col-6">
        <div class="float-sm-right">
          <a href="syncSlabs?sync=Expense" class="btn mr-1 btn-sm btn-danger waves-effect waves-light"><i
              class="fa fa-plus mr-1"></i>Sync Data</a>
          <button onclick="openAddExpenseModal()" class="btn btn-sm btn-primary">
            <i class="fa fa-plus"></i> Add
          </button>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Expense Title</th>
                    <th>Expense Icon</th>
                    <th>Status</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  $q = $d->select("expense_master", "", "order by expense_id  DESC");
                  while ($data = mysqli_fetch_array($q)) {
                    extract($data);
                    ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?= $expense_title ?></td>
                      <td>
                        <a href="../img/emp_icon/<?php echo $expense_icon; ?>" data-fancybox="images"
                          data-caption="Photo Name : <?php echo $expense_icon; ?>">
                          <img id="blah" onerror="this.src='../img/dummy-image.jpg'"
                            src="../img/emp_icon/<?php echo $expense_icon; ?>" width="40" height="40"
                            alt="<?= $expense_title ?>" title="<?= $expense_title ?>" class="profile" />
                        </a>
                      </td>
                      <td>
                        <?php if ($expense_status == 1) { ?>
                          <form action="controller/statusController.php" method="post">
                            <input type="hidden" name="expense_id" value="<?= $expense_id ?>">
                            <input type="hidden" name="expense_status" value="0">
                            <input type="hidden" name="expense_title" value="<?= $expense_title ?>">
                            <input type="hidden" name="Status" value="expenseStatus">
                            <button type="submit" class="form-btn btn btn-sm btn-danger waves-effect waves-light m-1"
                              title="Update Status">Deactive</button>
                          </form>
                        <?php } elseif ($expense_status == 0) { ?>
                          <form action="controller/statusController.php" method="post">
                            <input type="hidden" name="expense_id" value="<?= $expense_id ?>">
                            <input type="hidden" name="expense_status" value="1">
                            <input type="hidden" name="expense_title" value="<?= $expense_title ?>">
                            <input type="hidden" name="Status" value="expenseStatus">
                            <button style="background-color: green;" type="submit"
                              class="form-btn btn btn-sm btn-info waves-effect waves-light m-1"
                              title="Update Status">Active</button>
                          </form>
                        <?php } ?>
                      </td>
                      <td>
                        <div class="row ml-1">
                          <button
                            onclick="openUpdateExpenseModal('<?= $expense_id ?>','<?= $expense_title ?>','<?= $expense_icon ?>')"
                            class="btn btn-sm btn-primary waves-effect waves-light m-1">
                            <i class="fa fa-pencil"></i>
                          </button>
                          <form action="controller/expenseController.php" method="post">
                            <input type="hidden" name="expense_id" value="<?= $expense_id ?>">
                            <input type="hidden" name="deleteExpense" value="deleteExpense">
                            <button type="submit" class="form-btn btn btn-sm btn-danger waves-effect waves-light m-1"
                              title="Delete"> <i class="fa fa-trash-o"></i> </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="expenseModal">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="modalTitle">Add Expense</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="expenseForm" action="controller/expenseController.php" method="post" enctype="multipart/form-data">
          <div class="form-group row">
            <label for="expense_title" class="col-sm-4 col-form-label">Expense Title<span
                class="required">*</span></label>
            <div class="col-sm-8">
              <input type="text" autocomplete="off" class="form-control" id="expense_title" name="expense_title"
                required>
            </div>
          </div>
          <div class="form-group row">
            <label for="expense_icon" class="col-sm-4 col-form-label">Expense Icon</label>
            <div class="col-sm-8 d-flex align-items-center">
              <button type="button" class="btn-outline-primary" id="iconPickerBtn" data-toggle="modal"
                data-target="#iconModal" style="width: 50px; height: 50px; font-size: 24px; padding: 0;">
                <i class="fas fa-plus"></i>
              </button>
              <div id="iconPickerBtnRemove" class="ml-2"></div>
            </div>
          </div>
          <div class="form-footer text-center">
            <input type="hidden" id="expense_type_action" name="" value="">
            <input type="hidden" id="expense_id" name="expense_id">
            <input type="hidden" id="expense_icon" name="expense_icon" value="">
            <input type="submit" class="btn btn-primary" value="Save">
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="iconModal" tabindex="-1" data-backdrop="static" data-keyboard="false"
  aria-labelledby="iconModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content custom-modal">
      <div class="modal-header border-0">
        <h5 class="modal-title">Select Icon</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="text" id="iconSearch" class="form-control mb-4" placeholder="Type icon name...">
        <div class="row" id="imageGrid">
          <?php
          $dirExistingImage = "../img/emp_icon/";
          $imagesDisplay = glob($dirExistingImage . "*.{jpg,jpeg,png,gif,svg}", GLOB_BRACE);
          foreach ($imagesDisplay as $image) {
            $imageName = basename($image);

            ?>
            <div class="col-sm-6 col-md-3 col-lg-1 icon-item text-center mb-4">
              <div class="card icon-card shadow-sm img-select" data-name="<?= $imageName ?>" data-url="<?= $image ?>">
                <img src="<?= $image ?>" class="card-img-top p-2" style="height:40px; object-fit:contain;"
                  alt="<?= $imageName ?>">
                <div class="card-body p-2">
                  <small class="text-muted d-block" style="font-size: 12px;"><?= $imageName ?></small>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>

        <div class="text-center mt-4">
          <form id="iconUploadForm" method="post" enctype="multipart/form-data"
            action="./controller/expenseController.php">
            <div class="mb-3 text-center">
              <label for="uploadIcon" class="form-label font-weight-bold">Upload New Icon</label>
              <input type="file" id="uploadIcon" name="uploadIcon" accept="image/png"
                class="form-control-file d-block mx-auto" style="max-width: 300px;">
            </div>
            <div class="mb-3">
              <input type="text" id="iconName" name="iconName" autocomplete="off"
                class="form-control mx-auto text-center" placeholder="Enter Icon Name" style="max-width: 300px;">
            </div>
            <button type="submit" class="btn btn-primary">Add Icon</button>
            <div id="uploadStatus" class="mt-2"></div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  function openAddExpenseModal() {
    $('#expenseForm')[0].reset();
    $('#modalTitle').text("Add Expense");
    $('#expense_type_action').attr('name', 'addExpenseData').val('addExpenseData');
    $('#expense_id').val("");
    $('#modalBtnText').text("Save");
    $('#expense_icon').val('');
    $('#expense_icon_preview').hide();
    $('#iconPickerBtn').html('+');
    $('#iconPickerBtnRemove').empty();

    $('#expenseModal').modal('show');
  }

  function openUpdateExpenseModal(expense_id, expense_title, expense_icon) {
    $('#expenseForm')[0].reset();
    $('#expense_title').val(expense_title);
    $('#expense_id').val(expense_id);
    $('#modalTitle').text("Update Expense");
    $('#expense_type_action').attr('name', 'updateExpenseData').val('updateExpenseData');
    $('#modalBtnText').text("Update");
    $('#expense_icon').val(expense_icon);

    if (expense_icon) {
      const iconPath = `../img/emp_icon/${expense_icon}`;
      $('#iconPickerBtn').html(`<img src="${iconPath}" height="30" class="selected-icon" />`);
      $('#iconPickerBtnRemove').html(`<button type="button" class="btn btn-sm btn-danger remove-icon-btn" title="Remove Icon">&times;</button>`);
    } else {
      $('#iconPickerBtn').html('+');
      $('#iconPickerBtnRemove').empty();
    }

    $('#expenseModal').modal('show');
  }

</script>