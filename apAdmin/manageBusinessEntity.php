<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-9">
                <h4 class="page-title">Business Entity</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="welcome">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Business Entity</li>
                </ol>
            </div>
            <div class="col-3 text-right">
                <div class="btn-group float-sm-right">
                    <button class="btn btn-sm btn-success payment_setting_btn" data-toggle="modal"
                        data-target="#addModal"><i class="fa fa-plus mr-1"></i> Add </button>
                </div>
            </div>
        </div>
        <!-- --------------new -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="">
                        <ul class="nav nav-tabs nav-tabs-info nav-justified">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tabe-13"> Active</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link  show" data-toggle="tab" href="#tabe-14"> Deactive</span></a>
                            </li>

                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div id="tabe-13" class="container-fluid tab-pane active show">
                                <div class="">
                                    <div class="">
                                        <!-- <div class="card-header"><i class="fa fa-table"></i> Data Exporting</div> -->
                                        <div class="">
                                            <div class="table-responsive">
                                                <table id="default-datatable1" class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class='deleteTh'>#</th>
                                                            <th>Action</th>
                                                            <th>Name</th>
                                                            <th>Created By</th>
                                                            <th>Created Date</th>
                                                            <th>Updated By</th>
                                                            <th>Updated Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        // $q = $d->selectRow("bms_admin_master.*,role_master.*,bms_admin_master.created_by as admin_created_by,bms_admin_master.updated_by as admin_updated_by,bms_admin_master.created_date as admin_created_date,bms_admin_master.updated_date as admin_updated_date", "bms_admin_master,role_master", "role_master.role_id=bms_admin_master.role_id AND bms_admin_master.active_status=0", "");
                                                        // $q = $d->select("business_entity_master", "", "ORDER BY b_id desc");
                                                        $q = $d->select("business_entity_master", "business_entity_master.status='0'", "ORDER BY b_id DESC");
                                                        $i = 1;
                                                        while ($data = mysqli_fetch_array($q)) {
                                                            // echo "<pre>"; print_r($data);
                                                            ?>
                                                            <tr>

                                                                <td>
                                                                    <?php echo $i++; ?>
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-primary"
                                                                        onclick="EditBusinessEntity(<?php echo $data['b_id']; ?>)"
                                                                        title="Edit Business Entity">
                                                                        <i class="fa fa-edit"></i>
                                                                    </button>
                                                                    <form class="d-inline-block" method="POST"
                                                                        action="controller/businessController.php">
                                                                        <input type="hidden" name="deleteUser">
                                                                        <input type="hidden" name="b_id"
                                                                            value="<?php echo $data['b_id']; ?>">
                                                                        <input type="hidden" name="admin_name"
                                                                            value="<?php echo $data['admin_name']; ?>">
                                                                        <button class="btn btn-sm btn-danger form-btn"><i
                                                                                class="fa fa-trash-o"></i></button>
                                                                    </form>
                                                                </td>
                                                                <td>
                                                                    <?php echo $data['name'] ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $data['created_by'] ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $data['created_date'] ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $data['updated_by'] ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $data['updated_date'] ?>
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
                            <div id="tabe-14" class="container-fluid tab-pane fade ">
                                <div class="">
                                    <div class="">
                                        <div class="">
                                            <div class="table-responsive">
                                                <table id="default-datatable2" class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class='deleteTh'>#</th>
                                                            <th>Action</th>
                                                            <th>Name</th>
                                                            <th>Created By</th>
                                                            <th>Created Date</th>
                                                            <th>Deleted By</th>
                                                            <th>Deleted Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $q = $d->select("business_entity_master", "business_entity_master.status='1'", "ORDER BY b_id DESC");

                                                        $i = 1;
                                                        while ($data = mysqli_fetch_array($q)) {
                                                            ?>
                                                            <tr>

                                                                <td>
                                                                    <?php echo $i++; ?>
                                                                </td>
                                                                <td>

                                                                    <form class="d-inline-block" method="POST"
                                                                        action="controller/businessController.php">
                                                                        <input type="hidden" name="deleteUserReactive">
                                                                        <input type="hidden" name="b_id"
                                                                            value="<?php echo $data['b_id']; ?>">
                                                                        <input type="hidden" name="admin_name"
                                                                            value="<?php echo $data['admin_name']; ?>">
                                                                        <button class="btn btn-sm btn-danger form-btn">Re
                                                                            Active</button>
                                                                    </form>
                                                                </td>
                                                                <td>
                                                                    <?php echo $data['name'] ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $data['created_by'] ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $data['created_date'] ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $data['deleted_by'] ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $data['deleted_date'] ?>
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
                </div>
            </div>
        </div>
        <!-- --------------new -->

        <!-- End Row-->
    </div>
    <!-- End container-fluid-->
</div>
<!-- End content-wrapper-->

<!-- Start Add modal -->
<div class="modal fade" id="addModal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Add Business Entity</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="businessEntity" action="controller/businessController.php" method="post"
                    enctype="multipart/form-data">
                    <div class="form-group row">
                        <label for="name1" class="col-lg-4 col-form-label">Business Name <i
                                class="text-danger">*</i></label>
                        <div class="col-lg-8">
                            <input type="text" required class="form-control" id="name1" name="name" maxlength="30"
                                placeholder="Please Enter Business Entity" minlength="5">
                        </div>
                    </div>
                    <div class="form-footer text-center">
                        <input type="hidden" name="addBusinessEntity" value="addBusinessEntity">
                        <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Add Modal -->

<!-- Start Edit Modal -->
<div class="modal fade" id="EditBusinessEntity" data-keyboard="false" data-backdrop="static"><!-- Edit Modal -->
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Edit Business Entity</h5>
                <!-- <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> -->
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-close"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editbusinessEntity" action="controller/businessController.php" method="post"
                    enctype="multipart/form-data">
                    <input type="hidden" name="b_id" id="b_id">
                    <div class="form-group row">
                        <label for="name" class="col-lg-4 col-form-label">Business Name <i
                                class="text-danger">*</i></label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control" autocomplete="off" name="name" id="name" required
                                maxlength="30" minlength="5">
                        </div>
                    </div>
                    <div class="form-footer text-center pb-3">
                        <input type="hidden" name="edit_businessentity" value="edit_businessentity">
                        <button type="submit" class="btn btn-success btn-sm mmw-70">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Edit Modal -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/he/1.2.0/he.min.js"></script>

<script>
    function EditBusinessEntity(argument) {
        $.ajax({
            url: 'controller/businessController.php',
            method: 'POST',
            dataType: "JSON",
            data: { b_id: argument, getBusinessEntityDetails: 'getBusinessEntityDetails', csrf: csrf },
            success: function (response) {
                if (response.status == '0') {
                    $('#b_id').val(response.data.b_id);
                    $('#name').val(response.data.name);
                    $('#EditBusinessEntity').modal('show');
                }
            },
            error: function () {
                alert('Error occurred while fetching data.');
            }
        });
    }
</script>