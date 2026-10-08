<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-lg-9 col-sm-8">
                <h4 class="page-title">Suggestion</h4>
            </div>
            <div class="col-lg-3 col-sm-4">
                <div class="btn-group float-sm-right">
                    <a href="javascript:void();" class="btn btn-primary btn-sm waves-effect waves-light"
                        data-toggle="modal" data-target="#suggestionModal" onclick="openModalForAdd();">
                        <i class="fa fa-plus mr-1"></i> Add New
                    </a>
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
                                        <th>Sr.No</th>
                                        <th>Suggestion Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $q = $d->select("suggestion_master", "1 ORDER BY suggestion_id DESC");
                                    while ($data = mysqli_fetch_array($q)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px;"
                                                title="<?php echo ($data['suggestion_name']); ?>">
                                                <?php echo ($data['suggestion_name']); ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo ($data['suggestion_type'] == '1') ? 'Reply' : 'Closing';
                                                ?>
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-sm btn-danger mb-2 remove_slab_container"
                                                    onclick="deleteSuggestion(<?= $data['suggestion_id'] ?>)">Delete</i></button>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-primary btn-sm edit-suggestion-btn" 
                                                    data-id="<?php echo htmlspecialchars($data['suggestion_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <input type="hidden" id="suggestion_status_hidden" name="suggestion_status">
                                                <input type="hidden" name="suggestion_id">
                                                <!-- <form class="deleteForm<?php echo $data['suggestion_id']; ?>" style ='float: left;'  action="controller/suggestionController.php" method="post">
                        <input type="hidden" name="suggestion_id_delete" value="<?php echo $data['suggestion_id']; ?>">
                        <button  name="deleteSuggestion" type="button" class="btn btn-danger button-r btn-sm" onclick="deleteData('<?php echo $data['suggestion_id']; ?>');" data-toggle="tooltip" title="Delete Suggestion"><i class="fa fa-trash-o"></i></button>
                      </form> -->
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

<!-- Suggestion Modal -->
<div class="modal fade" id="suggestionModal" tabindex="-1" role="dialog" aria-labelledby="suggestionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-uppercase text-white">
                    <span id="suggestionModalLabel">Add Suggestion</span>
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="suggestionForm" action="controller/suggestionController.php" method="post">
                    <input type="hidden" id="suggestion_id" name="suggestion_id">
                    <!-- Hidden Status Field -->
                    <input type="hidden" id="suggestion_status" name="suggestion_status">
                    <div class="form-group">
                        <label for="suggestion_name">Suggestion Name <span class="text-danger">*</span></label>
                        <!-- Remove `required` from these fields -->
                        <textarea class="form-control" id="suggestion_name" name="suggestion_name" rows="4"
                            required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="suggestion_type">Suggestion Type <span class="text-danger">*</span></label>
                        <select class="form-control single-select" id="suggestion_type" name="suggestion_type" required>
                            <option value="" disabled selected>-- Select --</option>
                            <option value="0">Closing</option>
                            <option value="1">Reply</option>
                        </select>
                    </div>
                    <div class="form-footer text-center">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-check-square-o"></i> <span>ADD</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script>
    function openModalForAdd() {
        $("#suggestion_id").val("");
        $("#suggestion_name").val("");
        $("#suggestion_type").val("").trigger("change");
        $("#suggestion_status").val("0");
        $("#charCount").text("0 / 150 characters used");
        $("#suggestionModalLabel").text("Add Suggestion");
        $("#suggestionForm button[type='submit']").text("ADD");
        $("#suggestionModal").modal("show");
    }

    function openModalForEdit(id, name, type, status) {
        $("#suggestion_id").val(id);
        $("#suggestion_name").val(name);
        $("#suggestion_type").val(type).trigger("change");
        $("#suggestion_status").val(status);
        $("#charCount").text(name.length + " / 150 characters used");
        $("#suggestionModalLabel").text("Edit Suggestion");
        $("#suggestionForm button[type='submit']").text("EDIT");
        $("#suggestionModal").modal("show");
    }

    // Handle edit button clicks using AJAX to fetch data
    $(document).on('click', '.edit-suggestion-btn', function() {
        var suggestionId = $(this).data('id');
        
        $.ajax({
            url: 'controller/suggestionController.php',
            type: 'POST',
            data: {
                fetchSuggestion: true,
                suggestion_id: suggestionId
            },
            dataType: 'json',
            success: function(response) {
                if (response) {
                    openModalForEdit(
                        response.suggestion_id,
                        response.suggestion_name,
                        response.suggestion_type,
                        response.suggestion_status
                    );
                } else {
                    swal({
                        text: "Failed to fetch suggestion data",
                        icon: "error",
                    });
                }
            },
            error: function() {
                swal({
                    text: "Error fetching suggestion data",
                    icon: "error",
                });
            }
        });
    });
</script>
<script>
    function deleteSuggestion(id) {
        swal({
            title: "Are you sure?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "controller/deleteController.php",
                        cache: false,
                        type: "POST",
                        data: { id: id, deleteValue: "deleteSuggestion" },
                        success: function (response) {
                            if (response == 1) {
                                $(".remove_data_" + id).remove();
                                swal({
                                    text: "Suggestion deleted Successfully",
                                    icon: "success",
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        }

                    });
                }
            });
    }

</script>