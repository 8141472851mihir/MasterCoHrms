<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">App Menu Category Master</h4>
            </div>
            <div class="col-sm-3 col-md-3 col-6">
                <div class="float-sm-right">
                    <button onclick="openAddCategoryModal()" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add</button>
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
                                        <th>Action</th>
                                        <th>Name</th>
                                        <th>Icon</th>
                                        <th>Status</th>
                                        <th>Laguage Key Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $q = $d->select("menu_category_master", "", "ORDER BY menu_category_id ASC");
                                    while ($data = mysqli_fetch_array($q)) {
                                        extract($data);
                                    ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><button onclick="openEditCategoryModal('<?= $menu_category_id ?>','<?= $menu_category_name ?>','<?= $menu_category_key ?>','<?= $menu_category_icon ?>')" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></button>
                                            </td>
                                            <td><?php echo $menu_category_name; ?></td>
                                            <td><img src="<?php echo $bucket_url; ?>icons/<?php echo $menu_category_icon; ?>" width='50' alt=""></td>
                                            <td>
                                                <?php
                                                $buttonClass = ($menu_category_status == "0") ? 'btn-success-new' : 'btn-danger';
                                                $buttonCondition = ($menu_category_status == "0") ? 'Active' : 'Deactive';
                                                $status = ($menu_category_status == "0") ? 'appMenuCategoryDeactive' : 'appMenuCategoryActive';
                                                $newStatus = ($menu_category_status == "0") ? 'appMenuCategoryActive' : 'appMenuCategoryDeactive';
                                                $newStatusVal = ($menu_category_status == "0") ? '1' : '0';
                                                $statusValue = ($menu_category_status == "0") ? '0' : '1';
                                                ?>
                                                <input type="button" class="btn btn-sm pl-1 pr-1 w-75 <?php echo $buttonClass ?>" id="<?php echo 'menu_category_id_' . $menu_category_id; ?>" onclick="changeStatusNew('<?php echo $menu_category_id; ?>','<?php echo $status; ?>','<?php echo $newStatus; ?>','<?php echo $statusValue; ?>','<?php echo $newStatusVal; ?>','<?php echo 'menu_category_id_' . $menu_category_id; ?>');" data-size="small" value="<?php echo $buttonCondition ?>" />
                                            </td>
                                            <td><?php echo $menu_category_key ?>
                                                <!-- <button data-toggle="modal" data-target="#LangKeyModal" onclick="changeLangKey('<?php echo $menu_category_id; ?>');" title="Change Language Key" class="btn-sm btn btn-warning"><i class="fa fa-pencil"></i></button> -->
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


<div class="modal fade" id="categoryModal">
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="modalTitle">Add category</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="menuCategoryForm" action="controller/menuCategoryController.php" method="post" enctype="multipart/form-data">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Category Name <span class="required">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" autocomplete="off" class="form-control" id="menu_category_name" name="menu_category_name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Category Language Key <span class="required">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" autocomplete="off" class="form-control" id="menu_category_key" name="menu_category_key" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Category Icon <span class="required">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" autocomplete="off" class="form-control" id="menu_category_icon" name="menu_category_icon" required>
                        </div>
                    </div>

                    <div class="form-footer text-center">
                        <input type="hidden" id="category_type_action" name="addMenuCategory" value="addMenuCategory">
                        <input type="hidden" name="menu_category_id" id="menu_category_id">
                        <input type="hidden" name="old_menu_category_icon" id="old_menu_category_icon">
                        <button type="submit" id="formSubmitBtn" class="btn btn-primary"><i class="fa fa-check-square-o"></i>
                            Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    function openAddCategoryModal() {
        $('#menuCategoryForm')[0].reset();
        $('#modalTitle').text('Add Menu Category');
        $('#category_type_action').attr('name', 'addMenuCategory').val('addMenuCategory');
        $('#menu_category_id').val('');
        $('#old_menu_category_icon').val('');
        $('#menu_category_icon').val('');
        $('#categoryModal').modal('show');
    }

    function openEditCategoryModal(id, name, menu_category_key, menu_category_icon) {
        $('#modalTitle').text('Update Menu Category');
        $('#category_type_action').attr('name', 'editMenuCategory').val('editMenuCategory');
        $('#menu_category_id').val(id);
        $('#menu_category_name').val(name);
        $('#menu_category_key').val(menu_category_key);
        $('#old_menu_category_icon').val(menu_category_icon);
        $('#menu_category_icon').val(menu_category_icon);
        $('#categoryModal').modal('show');
    }
</script>