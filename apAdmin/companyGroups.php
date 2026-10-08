<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Manage Company Group</h4>
            </div>
            <div class="col-sm-3">
                <div class="btn-group float-sm-right">
                    <a href="#" id="addGroupBtn" data-toggle="modal" data-target="#addGroup"
                        class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-plus mr-1"></i> Add New Group</a>
                </div>
            </div>
        </div>
        <?php
        $groupDataQry = $d->selectRow("company_group_master.*,added_bms.admin_name AS added_by_name,updated_bms.admin_name AS updated_by_name", "company_group_master LEFT JOIN bms_admin_master as added_bms ON added_bms.admin_id=company_group_master.added_by LEFT JOIN bms_admin_master as updated_bms ON updated_bms.admin_id=company_group_master.updated_by", "");
        ?>
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
                                        <th>Group Name</th>
                                        <th>Company Count</th>
                                        <th>Added By</th>
                                        <th>Added Date</th>
                                        <th>Updated By</th>
                                        <th>Updated Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    while ($groupData = mysqli_fetch_array($groupDataQry)) {
                                    ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="editGroup(<?php echo $groupData['company_group_id']; ?>)">
                                                    <i class="fa fa-pencil"></i> Edit
                                                </button>
                                            </td>
                                            <td><?php echo $groupData['company_group_name']; ?></td>
                                            <td>
                                                <?php
                                                $companyIds = trim($groupData['company_ids']);
                                                $count = ($companyIds !== '') ? count(explode(',', $companyIds)) : 0;
                                                echo $count;
                                                ?>
                                            </td>
                                            <td><?php echo $groupData['added_by_name']; ?></td>
                                            <td><?php echo $groupData['added_date']; ?></td>
                                            <td><?php echo $groupData['updated_by_name']; ?></td>
                                            <td><?php echo $groupData['updated_date']; ?></td>
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
<div class="modal fade" id="addGroup">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white"><span id="modeshow">Add</span> Group</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="companyGroupForm" action="controller/companyGroupController.php" method="post">
                    <div class="form-group row">
                        <label for="input-10" class="col-sm-4 col-form-label">Group Name <span class="required">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" autocomplete="off" maxlength="100" class="form-control" name="group_name"
                                id="group_name_edit" value="" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Companies <span class="required">*</span></label>
                        <div class="col-sm-8">
                            <select name="societies[]" id="societies_select" class="form-control single-select-new" multiple="multiple" required>
                                <?php
                                $availableSocieties = $d->selectRow("society_id,society_name", "society_master sm", "sm.society_id NOT IN ( SELECT DISTINCT sm2.society_id FROM society_master sm2 JOIN company_group_master cgm ON FIND_IN_SET(sm2.society_id, cgm.company_ids) > 0 WHERE cgm.company_ids IS NOT NULL AND cgm.company_ids != '')");
                                while ($society = mysqli_fetch_array($availableSocieties)) {
                                    echo '<option value="' . $society['society_id'] . '">' . $society['society_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-footer text-center">
                        <input type="hidden" name="group_id" id="group_id_edit">
                        <input type="hidden" name="addCompanyGroup" id="addCompanyGroup" value="addCompanyGroup">
                        <button id="submitButton" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> <span id="submitText">Add</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script>
    function editGroup(groupId) {
        $.ajax({
            url: 'controller/companyGroupController.php',
            method: 'POST',
            data: {
                group_id: groupId,
                'getGroupDetails': 'getGroupDetails',
                csrf: csrf
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#group_id_edit').val(res.group_id);
                    $('#group_name_edit').val(res.group_name);
                    $('#submitText').text('Update');
                    $('#modeshow').text('Edit');
                    var $select = $('#societies_select');
                    $select.empty();
                    $.each(res.companies, function(index, company) {
                        $select.append(
                            $('<option>', {
                                value: company.id,
                                text: company.name,
                                selected: company.selected
                            })
                        );
                    });

                    $('#addGroup').modal('show');
                } else {
                    alert('Group not found.');
                }
            }
        });
    }
    $(document).ready(function() {
        var validator = $("#companyGroupForm").validate({
            errorPlacement: function(error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else if (element.hasClass('select2-hidden-accessible')) {
                    error.insertAfter(element.next('span'));
                    element.next('span').addClass('error').removeClass('valid');
                } else {
                    error.insertAfter(element);
                }
            },
            rules: {
                group_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                    checkspace: true,
                    remote: {
                        url: "controller/companyGroupController.php",
                        type: "post",
                        data: {
                            checkGroupName: function() {
                                return $('#group_name_edit').val();
                            },
                            group_id: function() {
                                return $('#group_id_edit').val();
                            },
                            csrf: csrf
                        }
                    }
                },
                "societies[]": {
                    required: true,
                    minlength: 2,
                    maxlength: 10
                },
            },
            messages: {
                group_name: {
                    required: "Please enter group name",
                    maxlength: "Group name cannot be longer than 100 characters",
                    remote: "This group name is already in use"
                },
                "societies[]": {
                    required: "Please select at least 2 companies",
                    minlength: "Please select at least 2 companies",
                    maxlength: "Maximum 10 companies allowed per group"
                }
            },
            submitHandler: function(form) {
                $('#submitButton').prop('disabled', true);
                submitCompanyGroupForm();
                return false;
            }
        });
        $.validator.addMethod("minlengthSelect", function(value, element, param) {
            return $(element).select2('val').length >= param;
        }, "Please select at least {0} companies");

        $.validator.addMethod("maxlengthSelect", function(value, element, param) {
            return $(element).select2('val').length <= param;
        }, "Maximum {0} companies allowed per group");

        $("#companyGroupForm").rules("add", {
            "societies[]": {
                minlengthSelect: 2,
                maxlengthSelect: 10
            }
        });

        $('#societies_select').on('change', function() {
            $(this).valid();
        });

        $('#addGroup').on('hidden.bs.modal', function() {
            validator.resetForm();
            $('#companyGroupForm')[0].reset();
            $('#group_id_edit').val('');
            $('#submitText').text('Add');
            $('#modeshow').text('Add');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $.ajax({
                url: 'controller/companyGroupController.php',
                method: 'POST',
                data: {
                    'getAvailableSocieties': 'getAvailableSocieties',
                    csrf: csrf
                },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        var $select = $('#societies_select');
                        $select.empty();
                        $.each(res.companies, function(index, company) {
                            $select.append($('<option>', {
                                value: company.id,
                                text: company.name
                            }));
                        });
                        $select.trigger('change');
                    }
                }
            });
        });

        function submitCompanyGroupForm() {
            createSwalProgressBar();
            $.ajax({
                url: 'controller/companyGroupController.php',
                method: 'POST',
                data: $('#companyGroupForm').serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'processing') {
                        const allOperations = [];
                        response.added_societies.forEach(societyId => {
                            allOperations.push({
                                societyId: societyId,
                                action: 'add',
                                group_id: response.group_id,
                                group_name: response.group_name
                            });
                        });
                        response.removed_societies.forEach(societyId => {
                            allOperations.push({
                                societyId: societyId,
                                action: 'remove',
                                group_id: response.group_id,
                                group_name: response.group_name
                            });
                        });
                        const totalOperations = allOperations.length;
                        $('.custom-progress-bar-total-count').text(totalOperations);
                        if (totalOperations > 0) {
                            processRequests(allOperations, function(operation, callback) {
                                $.ajax({
                                    url: 'controller/companyGroupController.php',
                                    method: 'POST',
                                    data: {
                                        society_id: operation.societyId,
                                        group_id: operation.group_id,
                                        group_name: operation.group_name || '',
                                        action: operation.action,
                                        updateSingleSocietyGroup: '1',
                                        csrf: csrf
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        callback(response.status == 'success');
                                    },
                                    error: function() {
                                        callback(false);
                                    }
                                });
                            }, 1, function() {
                                handleFormSuccess();
                            });
                        } else {
                            handleFormSuccess();
                        }
                    } else if (response.status === 'error') {
                        handleFormError(response.message);
                    }
                },
                error: function() {
                    handleFormError("Failed to save group data");
                }
            });
        }

        function handleFormSuccess() {
            swal.close();
            swal({
                title: "Success",
                text: "Group updated successfully!",
                icon: "success",
                timer: 3000,
            }).then(() => {
                $('#addGroup').modal('hide');
                location.reload();
            });
        }

        function handleFormError(message) {
            $('#submitButton').prop('disabled', false);
            $(".ajax-loader").hide();
            swal.close();
            swal({
                title: "Error",
                text: message,
                icon: "error",
                timer: 5000,
            });
        }
    });
</script>