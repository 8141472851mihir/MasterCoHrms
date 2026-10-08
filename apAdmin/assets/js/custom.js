jQuery(document).ready(function ($) {
    is_swal_on = false;
    swalQueue = [];
    var country_code = $('#country_code_get').val();
    var country_code_alt = $('#country_code_get_alt').val();
    $("#country_code").val(country_code);
    $("#country_code_alt").val(country_code_alt);

    // Key Name: auto-convert pasted text to lower snake_case
    $(document).on('paste', '#key_name', function (e) {
        e.preventDefault();
        var pastedText = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
        var snakeCase = pastedText
            .trim()
            .replace(/\s+/g, '_')
            .replace(/([a-z])([A-Z])/g, '$1_$2')
            .replace(/[^a-zA-Z0-9_]+/g, '_')
            .replace(/_+/g, '_')
            .replace(/^_|_$/g, '')
            .toLowerCase();
        var input = this;
        var start = input.selectionStart, end = input.selectionEnd;
        var val = input.value;
        var newVal = val.substring(0, start) + snakeCase + val.substring(end);
        $(input).val(newVal);
    });

    $('input[name="key_type"]').click(function () {
        var key_type = $("input[name='key_type']:checked").val();
        if (key_type == 1) {

            $('#no_of_keyDiv').css('display', 'block');

        } else {
            $('#no_of_keyDiv').css('display', 'none');

        }
    });
    // Notice Board Start
    $('#summernoteEditor').summernote({
        height: 400,
        tabsize: 2,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ol', 'ul', 'paragraph', 'height']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['undo', 'redo', 'codeview', 'help']]
        ]
    });

    $('#summernoteImgage').summernote({
        height: 400,
        tabsize: 2,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ol', 'ul', 'paragraph', 'height']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['undo', 'redo', 'codeview', 'help']]
        ],
        callbacks: {
            onImageUpload: function (image) {
                uploadImage(image[0]);
            }
        }
    });

    function uploadImage(image) {
        var data = new FormData();
        data.append("image", image);
        $.ajax({
            url: 'controller/noticeBoardImage.php',
            cache: false,
            contentType: false,
            processData: false,
            data: data,
            type: "post",
            success: function (url) {
                var image = $('<img width=320>').attr('src', '<?php echo $base_url;?>img/noticeBoard/' + url);
                console.log(image);
                $('#summernoteImgage').summernote("insertNode", image[0]);
            },
            error: function (data) {
                console.log(data);
            }
        });
    }

    //IS_986
    $("#noticeBoard").submit(function (event) {
        if ($('#summernoteImgage').summernote('codeview.isActivated')) {
            $('#summernoteImgage').summernote('codeview.deactivate');
        }
    });

    //Notice Board End

    // data table start
    $('#default-datatable').DataTable();
    $('#default-datatable1').DataTable();
    $('#default-datatable2').DataTable();
    $('#default-datatable3').DataTable();
    $('#default-datatable4').DataTable();


    //IS_675 //26feb2020
    var table = $('#examplePenaltiesTbl').DataTable({
        lengthChange: false,
        order: [[8, 'desc']]
    });
    $('#examplePenaltiesTbl_wrapper').prepend(table.buttons().container());
    // table.buttons().container().appendTo('#examplePenaltiesTbl_wrapper .col-md-6:eq(0)');

    var oTable = $('#hide_sorting').DataTable({
        stateSave: true,
        lengthChange: true,
        columnDefs: [{ "orderable": false, "targets": 0 }],
        order: [[2, 'asc']]
    });
    var allPages = oTable.rows().nodes();
    $("#checkUncheck").click(function () {
        if ($(this).val() == 'CheckAll') {
            $('input[type="checkbox"]', allPages).prop('checked', true);
            $(this).val('UncheckAll');
            $('input:checkbox').prop('checked', true);
        } else {
            $('input[type="checkbox"]', allPages).prop('checked', false);
            $(this).val('CheckAll');
            $('input:checkbox').prop('checked', false);
        }
    });

    var sTable = $('#checkAllwithAllDelete').DataTable({
        stateSave: true,
        lengthChange: true,
        columnDefs: [{ "orderable": false, "targets": 0 }],
        order: [[1, 'asc']]
    });
    var commonSliderAllPages = sTable.rows().nodes();
    $("#checkUncheckAllDelete").click(function () {
        if ($(this).val() == 'CheckAll') {
            $('input[type="checkbox"]', commonSliderAllPages).prop('checked', true);
            $(this).val('UncheckAll');
            $('input:checkbox').prop('checked', true);
        } else {
            $('input[type="checkbox"]', commonSliderAllPages).prop('checked', false);
            $(this).val('CheckAll');
            $('input:checkbox').prop('checked', false);
        }
    });


    // OCT 25 start
    var table = $('#example,.exampleReport').DataTable({
        lengthChange: false,
        stateSave: true,
        drawCallback: function (settings) {
            $("img.lazyload").each(function () {
                var img = $(this);
                img.attr("src", img.data("src")); // Set the src to data-src
            });
        }
    });
    // OCT 25 end

    var table = $('.feedbackTable').DataTable({
        lengthChange: false,
        stateSave: true,
    });

    var table1 = $('#exampleReportBackup').DataTable({
        "bPaginate": false,
        "bLengthChange": false,
        "bFilter": true,
        "bInfo": false,
        "bAutoWidth": false
    });

    var table1 = $('#exampleReport').DataTable({
        stateSave: true,
        lengthChange: true,
        columnDefs: [{ "orderable": false, "targets": 0 }],
        order: [[1, 'asc']],
        "bPaginate": false,
        "bLengthChange": false,
        "bFilter": true,
        "bInfo": false,
        "bAutoWidth": false,
        "dom": 'Blfrtip',
        "buttons": [
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            }, 'colvis']
    });

    var table1 = $('#spTable').DataTable({
        stateSave: true,
        lengthChange: true,
        pageLength: 100,       // default selected value
        lengthMenu: [
            [10, 25, 50, 100, 500, -1],
            [10, 25, 50, 100, 500, "All"]
        ],
        columnDefs: [{ "orderable": false, "targets": 0 }],
        order: [[1, 'asc']],
        // "bPaginate": false,
        "bLengthChange": true,
        "bFilter": true,
        "bInfo": true,
        "bAutoWidth": false,
        "dom": 'Blfrtip',
        "buttons": [
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            }, 'colvis']
    });
    $('#example_wrapper').prepend(table.buttons().container());
    $('#example_wrapper').prepend(table1.buttons().container());

    // table.buttons().container().appendTo('#example_wrapper .col-md-6:eq(0)');
    // table1.buttons().container().appendTo('#example_wrapper .col-md-6:eq(0)');

    /*24-OCT-2024 mihir start*/
    if ($('#reportTable').length) {
        var reportTable = $('#reportTable').DataTable({
            lengthChange: false,
            buttons: ['copy', 'excel', 'pdf', 'csv', 'colvis'],
            select: true,
            bPaginate: false,
        });
        $('#reportTable tfoot th').each(function () {
            var $th = $(this);

            if ($th.hasClass('selectAllNew') || $th.hasClass('no-search-box')) {
            } else {
                var title = $th.text();
                $th.html('<input class="form-control tableSearch" type="text" placeholder="Search ' + title + '" />');
            }
        });

        if ($('#reportTable_wrapper').length) {
            $('#reportTable_wrapper').prepend(reportTable.buttons().container());
        }
        reportTable.columns().every(function () {
            var that = this;
            var footer = this.footer();
            if (!footer) {
                return;
            }
            $('input[type="text"]', footer).on('keyup change', function () {
                if (that.search() !== this.value) {
                    that
                        .search(this.value)
                        .draw();
                }
            });
        });
        var r = $('#reportTable tfoot tr');
        if (r.length) {
            r.find('th').each(function () {
                $(this).css('padding', 8);
            });
            $('#reportTable thead').append(r);
        }
        if ($('#search_0').length) {
            $('#search_0').css('text-align', 'center');
        }
    }
    // reportTable1 Started
    if ($('#reportTable1').length) {
    $('#reportTable1 tfoot.top-footer th').each(function () {
        var $th = $(this);
        if ($th.hasClass('selectAllNew')) {
        } else {
            var title = $th.text();
            $th.html('<input class="form-control tableSearch" type="text" placeholder="Search ' + title + '" />');
        }
    });

    var table = $('#reportTable1').DataTable({
        lengthChange: false,
        buttons: ['copy', 'excel', 'pdf', 'csv'],
        select: true,
        bPaginate: false,
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            $('#reportTable1 tfoot tr th').eq(0).html('Total');
            $('#reportTable1 tfoot .find-count').each(function () {
                var columnIndex = $(this).index();
                var columnData = api.column(columnIndex, { page: 'current' }).data();
                var sum = columnData.reduce(function (a, b) {
                    var numA = parseFloat(a) || 0;
                    var numB = parseFloat(b) || 0;
                    return numA + numB;
                }, 0);
                $(this).html(sum);
            });
        }
    });
    $('#reportTable1_wrapper').prepend(table.buttons().container());
    // table.buttons().container().appendTo('#reportTable1_wrapper .col-md-6:eq(0)');
    $('#reportTable1 tfoot .tableSearch').on('keyup change', function () {
        var columnIndex = $(this).parent().index();
        table
            .column(columnIndex)
            .search(this.value)
            .draw();
    });
    table.columns().every(function () {
        var that = this;
        $('input[type="text"]', this.footer()).on('keyup change', function () {
            if (that.search() !== this.value) {
                that
                    .search(this.value)
                    .draw();
            }
        });
    });
    var r = $('#reportTable1 tfoot.top-footer tr');
    r.find('th').each(function () {
        $(this).css('padding', 8);
    });
    $('#reportTable1 thead').append(r);
    var bottomFooter = $('#reportTable1 tfoot.bottom-footer');
    bottomFooter.find('th').each(function () {
        $(this).css('padding', 8);
    });
    $('#reportTable1').append(bottomFooter);
    if ($('#search_0').length) {
        $('#search_0').css('text-align', 'center');
    }
    }
    // Data Table End
});
/*24-OCT-2024 mihir end*/


// multiselect delete function

function deleteData(id) {
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data !",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                $('form.deleteForm' + id).submit();
                swal("Successfully Deleted !", {
                    icon: "success",
                });
            }
        });
}

// multiple delete

function DeleteAll(deleteValue) {
    var val = [];
    $('.multiDelteCheckbox:checkbox:checked').each(function (i) {
        val[i] = $(this).val();
    });
    if (val == "") {
        swal(
            'Warning !',
            'Please Select at least 1 item !',
            'warning'
        );
    } else {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this data!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    // $('form.deleteForm'+id).submit();
                    $.ajax({
                        url: "controller/deleteController.php",
                        cache: false,
                        type: "POST",
                        data: { ids: val, deleteValue: deleteValue },
                        success: function (response) {
                            console.log(response)
                            if (response == 1) {
                                history.go(0);
                            } else {
                                history.go(0);
                            }
                        }
                    });


                }
            });
    }
}
// change status 

$(function () {

    $('.selectAll').click(function () {
        $('.multiDelteCheckbox').prop('checked', this.checked);
    });
});

function changeStatus(id, status, value = null) {
    var csrf = $('input[name="csrf"]').val();
    $.ajax({
        url: "controller/statusController.php",
        cache: false,
        type: "POST",
        data: { id: id, status: status, value: value, csrf: csrf },
        success: function (response) {
            console.log(response)
            if (response == 1) {
                document.location.reload(true);
                swal("Status Changed", {
                    icon: "success",
                });
            } else {
                swal("Something Wrong!", {
                    icon: "error",
                });

            }
        }
    });
}

//IS_1045 7march2020
$('.block-id-cls').change(function () {
    var elem = $(this);
    var id = elem.closest('tr').attr('id');

    var detail = id.split('~');

    var block_id = detail[0];
    var society_id = detail[1];
    var block_sort = $(this).val();
    $.ajax({
        url: "controller/blockController.php",
        cache: false,
        type: "POST",
        data: { block_id: block_id, society_id: society_id, block_sort: block_sort, updateBlocksAjax: 'yes', csrf: csrf },
        success: function (response) {
            var obj = response.split('~');
            if (obj[0] == "error") {
                swal("Error! Duplicate Order Number!", {
                    icon: "error",
                });
                // $('#block_id_'+block_id).html('<center><label   style="color: #ff0000;"  >'+obj[1]+'</label></center>');
            } else {
                swal("Poof! Block Order Number Changed!", {
                    icon: "success",
                });
                // $('#block_id_'+block_id).html('<center><label   style="color: #2dce89;"  >'+obj[1]+'</label></center>');
                // location.reload();
            }


        }
    });
});
//IS_1045 7march2020

function getBlockList() {
    var no_of_blocks = $("#no_of_blocks").val();
    var block_type = $("#block_type").val();
    $.ajax({
        url: "getBlockList.php",
        cache: false,
        type: "POST",
        data: { no_of_blocks: no_of_blocks, block_type: block_type, csrf: csrf },
        success: function (response) {
            $('#BlockResp').html(response);


        }
    });
}

function getSubCategory() {
    var business_categories = $("#business_categories").val();
    $.ajax({
        url: "getBusSubCategory.php",
        cache: false,
        type: "POST",
        data: { business_categories: business_categories, csrf: csrf },
        success: function (response) {
            $('#business_categories_sub').html(response);
        }
    });
}

function getFloorList() {
    var no_of_floor = $("#no_of_floor").val();
    var floor_type = $("#floor_type").val();
    $.ajax({
        url: "getFloorList.php",
        cache: false,
        type: "POST",
        data: { no_of_floor: no_of_floor, floor_type: floor_type, csrf: csrf },
        success: function (response) {
            $('#floorResp').html(response);


        }
    });
}

function editAgent(agent_id) {

    $.ajax({
        url: "editAgent.php",
        cache: false,
        type: "POST",
        data: { agent_id: agent_id, csrf: csrf },
        success: function (response) {
            $('#agentEditDiv').html(response);
        }
    });
}

function DeleteBlock(block_id) {
    // alert(block_id);
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                // $('form.deleteForm'+id).submit();
                $.ajax({
                    url: "controller/blockController.php",
                    cache: false,
                    type: "POST",
                    data: { deleteBlock: block_id, csrf: csrf },
                    success: function (response) {
                        console.log(response)
                        if (response == 1) {

                            swal("Poof! Your data has been deleted!", {
                                icon: "success",
                            });
                            document.location.reload(true);
                        } else {
                            swal("Poof! Your data has been deleted!", {
                                icon: "error",
                            });
                        }
                    }
                });

            } else {
                //swal("Your data is safe!");
            }
        });
}

function DeleteFloor(floor_id) {
    // alert(floor_id);
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                // $('form.deleteForm'+id).submit();
                $.ajax({
                    url: "controller/floorController.php",
                    cache: false,
                    type: "POST",
                    data: { deleteFloor: floor_id, csrf: csrf },
                    success: function (response) {
                        console.log(response)
                        if (response == 1) {
                            swal("Poof! Your data has been deleted!", {
                                icon: "success",
                            });
                            document.location.reload(true);
                        } else {
                            swal("Poof! Your data has been deleted!", {
                                icon: "error",
                            });
                        }
                    }
                });


            } else {
                //swal("Your data is safe!");
            }
        });



}


function DeleteUnit(unit_id) {
    // alert(unit_id);
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                // $('form.deleteForm'+id).submit();
                $.ajax({
                    url: "controller/unitController.php",
                    cache: false,
                    type: "POST",
                    data: { deleteUnit: unit_id, csrf: csrf },
                    success: function (response) {
                        console.log(response)
                        if (response == 1) {
                            swal("Poof! Your data has been deleted!", {
                                icon: "success",
                            });
                            document.location.reload(true);
                        } else {
                            swal("Poof! Your data has been deleted!", {
                                icon: "error",
                            });
                        }
                    }
                });


            } else {
                //swal("Your data is safe!");
            }
        });
}


function DeleteParking(parking_id) {
    // alert(parking_id);
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                // $('form.deleteForm'+id).submit();
                $.ajax({
                    url: "controller/parkingController.php",
                    cache: false,
                    type: "POST",
                    data: { deleteParking_id: parking_id, csrf: csrf },
                    success: function (response) {
                        console.log(response)
                        if (response == 1) {
                            swal("Poof! Your data has been deleted!", {
                                icon: "success",
                            });
                            document.location.reload(true);
                        } else {
                            swal("Poof! Your data has been deleted!", {
                                icon: "error",
                            });
                        }
                    }
                });


            } else {
                //swal("Your data is safe!");
            }
        });
}

function editFloor(floor_id, floor_name) {
    $('#floorId').val(floor_id);
    $('#oldFloorname').val(floor_name);

}

function editBlock(block_id, block_name, block_sort) {
    $('#floorId').val(block_id);
    $('#oldFloorname').val(block_name);
    $('#block_sort').val(block_sort);
}


function editParking(parking_id, parking_name, society_parking_id) {
    $('#parking_id').val(parking_id);
    $('#oldParkingName').val(parking_name);
    $('#society_parking_id').val(society_parking_id);
}

function addParking(parking_id, Type, parking_name, society_parking_id) {
    $('#P_id').val(parking_id);
    $('#sParking_id').val(society_parking_id);
    $('#pType').html(Type);
    $('#CappType').html(Type);
    $('#parkingName').html(parking_name);
}

function updateParking(parking_id, Type, parking_name, society_parking_id, unit_id) {
    $('.P_id11').val(parking_id);
    $('.sParking_id1').val(society_parking_id);
    $('.unitId').val(unit_id);
    $('#pType1').html(Type);
    //IS_607
    $('#UpCappType').html(Type);
    $('#parkingName1').html(parking_name);
    $.ajax({
        url: "getParkingDetails.php",
        cache: false,
        type: "POST",
        data: { unit_id: unit_id, UpCappType: Type, parking_id: parking_id, csrf: csrf },
        success: function (response) {
            $('#getParkingDetails').html(response);
        }
    });
}

function approveParking(parking_id, Type, parking_name, society_parking_id, unit_id) {

    $.ajax({
        url: "getParkingDetailsPending.php",
        cache: false,
        type: "POST",
        data: { unit_id: unit_id, Type: Type, parking_name: parking_name, society_parking_id: society_parking_id, parking_id: parking_id, csrf: csrf },
        success: function (response) {
            $('#pendingParkingDiv').html(response);
        }
    });
}

function checkMobileSociety() {
    var secretary_mobile = $('#secretary_mobile').val();

    $.ajax({
        url: "controller/uniqueController.php",
        cache: false,
        type: "POST",
        data: { secretary_mobile: secretary_mobile, checkSocietyMobile: 'checkSocietyMobile', csrf: csrf },
        success: function (response) {
            if (response == 1) {
                document.getElementById("socAddBtn").disabled = true;
                Lobibox.notify('error', {
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'top right',
                    icon: 'fa fa-times-circle',
                    msg: 'This mobile number is Already Used.'
                });

            } else {
                document.getElementById("socAddBtn").disabled = false;
            }
        }
    });
}

function checkMobileUser() {
    var userMobile = $('#userMobile').val();

    $.ajax({
        url: "controller/uniqueController.php",
        cache: false,
        type: "POST",
        data: { userMobile: userMobile, checkUserMobile: 'checkUserMobile', csrf: csrf },
        success: function (response) {
            if (response == 1) {
                document.getElementById("socAddBtn").disabled = true;
                Lobibox.notify('error', {
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'top right',
                    icon: 'fa fa-times-circle',
                    msg: 'This mobile number is Already Used.'
                });

            } else {
                document.getElementById("socAddBtn").disabled = false;
            }
        }
    });
}


function checkMobileUserTenant() {
    var userMobile = $('#userMobileTenant').val();
    if (userMobile.length > 0) {

        $.ajax({
            url: "controller/uniqueController.php",
            cache: false,
            type: "POST",
            data: { userMobile: userMobile, checkUserMobile: 'checkUserMobile', csrf: csrf },
            success: function (response) {
                if (response == 1) {
                    document.getElementById("socAddBtnTenat").disabled = true;
                    Lobibox.notify('error', {
                        pauseDelayOnHover: true,
                        continueDelayOnInactiveTab: false,
                        position: 'top right',
                        icon: 'fa fa-times-circle',
                        msg: 'This mobile number is Already Used.'
                    });

                } else {
                    document.getElementById("socAddBtnTenat").disabled = false;
                }
            }
        });
    }
}



function checkEmailUser() {
    var userEmail = $('#userEmail').val();
    if (userEmail != '') {
        $.ajax({
            url: "controller/uniqueController.php",
            cache: false,
            type: "POST",
            data: { userEmail: userEmail, checkEmailMobile: 'checkEmailMobile', csrf: csrf },
            success: function (response) {
                if (response == 1) {
                    document.getElementById("socAddBtn").disabled = true;
                    Lobibox.notify('error', {
                        pauseDelayOnHover: true,
                        continueDelayOnInactiveTab: false,
                        position: 'top right',
                        icon: 'fa fa-times-circle',
                        msg: 'This Email is Already Used.'
                    });

                } else {
                    document.getElementById("socAddBtn").disabled = false;
                }
            }
        });
    } else {
        document.getElementById("socAddBtn").disabled = false;
    }
}

function checkEmailUserTenant() {
    var userEmail = $('#userEmailTenant').val();
    if (userEmail != '') {
        $.ajax({
            url: "controller/uniqueController.php",
            cache: false,
            type: "POST",
            data: { userEmail: userEmail, checkEmailMobile: 'checkEmailMobile', csrf: csrf },
            success: function (response) {
                if (response == 1) {
                    document.getElementById("socAddBtnTenat").disabled = true;
                    Lobibox.notify('error', {
                        pauseDelayOnHover: true,
                        continueDelayOnInactiveTab: false,
                        position: 'top right',
                        icon: 'fa fa-times-circle',
                        msg: 'This Email is Already Used.'
                    });

                } else {
                    document.getElementById("socAddBtnTenat").disabled = false;
                }
            }
        });
    } else {
        document.getElementById("socAddBtnTenat").disabled = false;
    }
}

function checkMobileUser1() {
    var userMobile = $('#userMobile1').val();

    $.ajax({
        url: "controller/uniqueController.php",
        cache: false,
        type: "POST",
        data: { userMobile: userMobile, checkUserMobile: 'checkUserMobile', csrf: csrf },
        success: function (response) {
            if (response == 1) {
                document.getElementById("socAddBtn1").disabled = true;
                Lobibox.notify('error', {
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'top right',
                    icon: 'fa fa-times-circle',
                    msg: 'This mobile number is Already Used.'
                });

            } else {
                document.getElementById("socAddBtn1").disabled = false;
            }
        }
    });
}



function checkEmailUser1() {
    var userEmail = $('#userEmail1').val();

    $.ajax({
        url: "controller/uniqueController.php",
        cache: false,
        type: "POST",
        data: { userEmail: userEmail, checkEmailMobile: 'checkEmailMobile', csrf: csrf },
        success: function (response) {
            if (response == 1) {
                document.getElementById("socAddBtn1").disabled = true;
                Lobibox.notify('error', {
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'top right',
                    icon: 'fa fa-times-circle',
                    msg: 'This Email is Already Used.'
                });

            } else {
                document.getElementById("socAddBtn1").disabled = false;
            }
        }
    });
}


function checkMobileSocietyEdit() {

    var secretary_mobile = $('#secretary_mobile').val();
    var secretary_mobile_old = $('#secretary_mobile_old').val();
    if (secretary_mobile_old != secretary_mobile) {
        // alert("call");
        $.ajax({
            url: "controller/uniqueController.php",
            cache: false,
            type: "POST",
            data: { secretary_mobile: secretary_mobile, secretary_mobile_old: secretary_mobile_old, checkSocietyMobileEdit: 'checkSocietyMobileEdit', csrf: csrf },
            success: function (response) {
                if (response == 1) {
                    document.getElementById("socAddBtn").disabled = true;
                    Lobibox.notify('error', {
                        pauseDelayOnHover: true,
                        continueDelayOnInactiveTab: false,
                        position: 'top right',
                        icon: 'fa fa-times-circle',
                        msg: 'This Mobile Number  is Already Used.'
                    });

                } else {
                    document.getElementById("socAddBtn").disabled = false;
                }
            }
        });
    } else {
        document.getElementById("socAddBtn").disabled = false;
    }
}

function checkemailSociety() {
    var secretary_email = $('#secretary_email').val();

    $.ajax({
        url: "controller/uniqueController.php",
        cache: false,
        type: "POST",
        data: { secretary_email: secretary_email, checkSocietyEmail: 'checkSocietyEmail', csrf: csrf },
        success: function (response) {
            if (response == 1) {
                document.getElementById("socAddBtn").disabled = true;
                Lobibox.notify('error', {
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'top right',
                    icon: 'fa fa-times-circle',
                    msg: 'Email already used'
                });

            } else {
                document.getElementById("socAddBtn").disabled = false;
            }
        }
    });
}


function checkemailSocietyEdit() {

    var secretary_email = $('#secretary_email').val();
    var secretary_email_old = $('#secretary_email_old').val();
    if (secretary_email_old != secretary_email) {
        // alert("call");
        $.ajax({
            url: "controller/uniqueController.php",
            cache: false,
            type: "POST",
            data: { secretary_email: secretary_email, secretary_email_old: secretary_email_old, checkSocietyEmailEdit: 'checkSocietyEmailEdit', csrf: csrf },
            success: function (response) {
                if (response == 1) {
                    document.getElementById("socAddBtn").disabled = true;
                    Lobibox.notify('error', {
                        pauseDelayOnHover: true,
                        continueDelayOnInactiveTab: false,
                        position: 'top right',
                        icon: 'fa fa-times-circle',
                        msg: 'Email already used'
                    });

                } else {
                    document.getElementById("socAddBtn").disabled = false;
                }
            }
        });
    } else {
        document.getElementById("socAddBtn").disabled = false;
    }
}


function checkMobileEmp() {
    var emp_mobile = $('#empNumber').val();

    $.ajax({
        url: "controller/uniqueController.php",
        cache: false,
        type: "POST",
        data: { emp_mobile: emp_mobile, checkUserEmp: 'checkUserEmp', csrf: csrf },
        success: function (response) {
            if (response == 1) {
                document.getElementById("socAddBtn").disabled = true;
                Lobibox.notify('error', {
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'top right',
                    icon: 'fa fa-times-circle',
                    msg: 'This mobile number is Already Used.'
                });

            } else {
                document.getElementById("socAddBtn").disabled = false;
            }
        }
    });
}


function checkMobileEmpEdit() {
    var emp_mobile = $('#empNumber').val();
    var empNumberOld = $('#empNumberOld').val();
    if (empNumberOld != emp_mobile) {
        $.ajax({
            url: "controller/uniqueController.php",
            cache: false,
            type: "POST",
            data: { emp_mobile: emp_mobile, checkUserEmp: 'checkUserEmp', csrf: csrf },
            success: function (response) {
                if (response == 1) {
                    document.getElementById("socAddBtn").disabled = true;
                    Lobibox.notify('error', {
                        pauseDelayOnHover: true,
                        continueDelayOnInactiveTab: false,
                        position: 'top right',
                        icon: 'fa fa-times-circle',
                        msg: 'This mobile number is Already Used.'
                    });

                } else {
                    document.getElementById("socAddBtn").disabled = false;
                }
            }
        });
    }
}



function checkMobileUserEdit() {
    var userMobile = $('#userMobile').val();
    var userMobileOld = $('#userMobileOld').val();
    if (userMobile != '') {
        if (userMobile != userMobileOld) {
            $.ajax({
                url: "controller/uniqueController.php",
                cache: false,
                type: "POST",
                data: { userMobile: userMobile, checkUserMobile: 'checkUserMobile', csrf: csrf },
                success: function (response) {
                    if (response == 1) {
                        document.getElementById("socAddBtn").disabled = true;
                        Lobibox.notify('error', {
                            pauseDelayOnHover: true,
                            continueDelayOnInactiveTab: false,
                            position: 'top right',
                            icon: 'fa fa-times-circle',
                            msg: 'This mobile number is Already Used.'
                        });

                    } else {
                        document.getElementById("socAddBtn").disabled = false;
                    }
                }
            });
        }
    } else {
        document.getElementById("socAddBtn").disabled = false;
    }
}


function checkEmailUserEdit() {
    var userEmail = $('#userEmail').val();
    var userEmailOld = $('#userEmailOld').val();
    if (userEmail != '') {
        if (userEmail != userEmailOld) {
            $.ajax({
                url: "controller/uniqueController.php",
                cache: false,
                type: "POST",
                data: { userEmail: userEmail, checkEmailMobile: 'checkEmailMobile', csrf: csrf },
                success: function (response) {
                    if (response == 1) {
                        document.getElementById("socAddBtn").disabled = true;
                        Lobibox.notify('error', {
                            pauseDelayOnHover: true,
                            continueDelayOnInactiveTab: false,
                            position: 'top right',
                            icon: 'fa fa-times-circle',
                            msg: 'This Email is Already Used.'
                        });

                    } else {
                        document.getElementById("socAddBtn").disabled = false;
                    }
                }
            });
        }
    } else {
        document.getElementById("socAddBtn").disabled = false;
    }
}


function changePlan(society_id, tracking_limit, registration_limit, crm_limit, crm_created) {
    $("#societyId").val(society_id);
    $("#plan_employee_tracking_limit").val(tracking_limit || 0);
    $("#plan_employee_registration_limit").val(registration_limit || 0);
    $("#plan_crm_limit").val(crm_limit || 0);
    $("#update_limits_yes, #update_limits_no").prop("checked", false);
    $("#planLimitFields").hide();
    if (String(crm_created) === "1") {
        $("#planCrmLimitRow").show();
    } else {
        $("#planCrmLimitRow").hide();
    }
}


function getStates() {
    $(".ajax-loader").show();
    var country_id = $("#country_id").val();
    $.ajax({
        url: "getStates.php",
        cache: false,
        type: "POST",
        data: { country_id: country_id, getStates: 'getStates', csrf: csrf },
        success: function (response) {
            $('#state_id').html(response);
            $(".ajax-loader").hide();

        }
    });
}

function getStatesSp() {
    var country_id = $("#country_id_sp").val();
    $(".ajax-loader").show();
    $.ajax({
        url: "getStates.php",
        cache: false,
        type: "POST",
        data: { country_id: country_id, getStates: 'getStates', csrf: csrf },
        success: function (response) {
            $('#state_id_sp').html(response);
            $(".ajax-loader").hide();

        }
    });
}

function getStatesNew() {
    var country_id = $("#countryId").val();
    $(".ajax-loader").show();
    $.ajax({
        url: "getStates.php",
        cache: false,
        type: "POST",
        data: { country_id: country_id, getStates: 'getStates', csrf: csrf },
        success: function (response) {
            $('#stateId').html(response);
            $(".ajax-loader").hide();

        }
    });
}

function getCity() {
    var state_id = $("#state_id").val();
    $(".ajax-loader").show();
    $.ajax({
        url: "getCities.php",
        cache: false,
        type: "POST",
        data: { state_id: state_id, getCity: 'getCity', csrf: csrf },
        success: function (response) {
            $('#city_id').html(response);
            $(".ajax-loader").hide();
        }
    });
}

function getCityAndUpdateDomains() {
    var state_id = $("#state_id").val();
    $(".ajax-loader").show();
    $.ajax({
        url: "getCities.php",
        cache: false,
        type: "POST",
        data: { state_id: state_id, getCity: 'getCity', csrf: csrf },
        success: function (response) {
            $('#city_id').html(response);
            $(".ajax-loader").hide();

            if (typeof getRecommendedDomain == 'function') {
                getRecommendedDomain();
            }
        }
    });
}
function getCitySp() {
    var state_id = $("#state_id_sp").val();
    $(".ajax-loader").show();
    $.ajax({
        url: "getCities.php",
        cache: false,
        type: "POST",
        data: { state_id: state_id, getCity: 'getCity', csrf: csrf },
        success: function (response) {
            $('#city_id_sp').html(response);

            $(".ajax-loader").hide();
        }
    });
}



function getSocietyList() {
    var city_id = $("#city_id").val();
    $(".ajax-loader").show();
    $.ajax({
        url: "getSocietyList.php",
        cache: false,
        type: "POST",
        data: { city_id: city_id, getSocietyList: 'getSocietyList', csrf: csrf },
        success: function (response) {
            $('#societyId').html(response);
            $(".ajax-loader").hide();
        }
    });
}


function getCityNew() {
    var state_id = $("#stateId").val();
    $(".ajax-loader").show();
    $.ajax({
        url: "getCities.php",
        cache: false,
        type: "POST",
        data: { state_id: state_id, getCity: 'getCity', csrf: csrf },
        success: function (response) {
            $('#cityId').html(response);
            $(".ajax-loader").hide();

        }
    });
}


function deletePost(feed_id) {
    // alert(block_id);
    swal({
        title: "Are you sure to Delete this Post ?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                // $('form.deleteForm'+id).submit();
                $.ajax({
                    url: "controller/newsFeedController.php",
                    cache: false,
                    type: "POST",
                    data: { feed_id_delete: feed_id, csrf: csrf },
                    success: function (response) {
                        console.log(response)
                        if (response == 1) {

                            swal("Poof! Your data has been deleted!", {
                                icon: "success",
                            });
                            document.location.reload(true);
                        } else {
                            swal("Poof! Your data has been deleted!", {
                                icon: "error",
                            });
                        }
                    }
                });

            } else {
                //swal("Your data is safe!");
            }
        });

}

function deleteComment(comments_id) {
    // alert(block_id);
    swal({
        title: "Are you sure delete this Comment ?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                // $('form.deleteForm'+id).submit();
                $.ajax({
                    url: "controller/newsFeedController.php",
                    cache: false,
                    type: "POST",
                    data: { comments_id_delete: comments_id, csrf: csrf },
                    success: function (response) {
                        console.log(response)
                        if (response == 1) {

                            swal("Poof! Your data has been deleted!", {
                                icon: "success",
                            });
                            document.location.reload(true);
                        } else {
                            swal("Poof! Your data has been deleted!", {
                                icon: "error",
                            });
                        }
                    }
                });

            } else {
                //swal("Your data is safe!");
            }
        });

}



function showError(msg) {
    swal(msg, {
        icon: "warning",
    });
}
$('.eventPrice').hide();

$('input[type=radio][name=event_type]').change(function () {
    if (this.value == '1') {
        $('.eventPrice').show();

    }
    else if (this.value == '0') {
        $('.eventPrice').hide();

    }
});

function hideData() {
    var empType = $('.employment_type').val();
    if (empType == 'Not employed' || empType == 'Student' || empType == 'Others') {
        $('.proExtDiv').hide();
    } else {
        $('.proExtDiv').show();
    }

}

$(".onlyNumber,#secretary_mobile,#trlDays,#emp_sallary,#no_of_option,#month_working_days,#working_days,#leave_days,#no_of_person,#no_of_month,#expAmoint,#no_of_unit_bill,#no_of_unit,#no_of_blocks,#no_of_floor,#emrNumber,#userMobile,#ownerMobile,#empNumber,#cMobile,#editMobile1,#noofCar,#noofBike,#person_limit_day,#person_limit").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
        // Allow: Ctrl+A, Command+A
        (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});

//IS_250 21FEB2020
$("#no_of_person,#no_of_month").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    //110 for dot(.)
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
        // Allow: Ctrl+A, Command+A
        (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        return;
    }
    // Ensure that it is a number and stop the keypress

    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});



//IS_250 21FEB2020


//24feb2020 IS_387
function hideNewsFeedInfo(id) {
    if ($("#collapse" + id).hasClass("show")) {
        $("#showNewsFeedIfo" + id).css("display", "block");
    } else {
        $("#showNewsFeedIfo" + id).css("display", "none");
    }
}
//24feb2020 IS_387

//28feb2020 IS_837
function resetFrm(id) {
    $('#' + id).validate().resetForm();
}

//28feb2020 IS_837

//2march2020
/*//28feb2020-new  IS_853
$('input').change(function(){
        this.value = $.trim(this.value);
    });

    $('textarea').change(function(){
        this.value = $.trim(this.value);
    });
    //28feb2020-new  IS_853*/
//2march2020


$('.form-btn').on('click', function (e) {
    e.preventDefault();
    var form = $(this).parents('form');
    swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        buttons: ['Cancel', 'Yes, I am sure !'],
    })
        .then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });

});

$('.inprogress-btn').on('click', function (e) {
    e.preventDefault();
    var form = $(this).parents('form');
    swal({
        title: "Are you sure?",
        text: "The feedback status will be updated to 'In Progress'",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        buttons: ['Cancel', 'Yes, I am sure !'],
    })
        .then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });

});

$('.solved-btn').on('click', function (e) {
    e.preventDefault();
    var form = $(this).parents('form');
    swal({
        title: "Are you sure?",
        text: "The feedback status will be updated to 'Solved'",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        buttons: ['Cancel', 'Yes, I am sure !'],
    })
        .then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });

});



$('.content-wrapper').click(function (e) {
    if ($("#wrapper").hasClass("toggled")) {
        $('#wrapper').removeClass("toggled");
        e.preventDefault();
    }
});
$('.toggle-menu').click(function (event) {
    event.stopPropagation();
});



function getSocietyData(society_id) {

    $.ajax({
        url: "controller/cronGetData.php",
        cache: false,
        type: "POST",
        data: { society_id: society_id, csrf: csrf },
        success: function (response) {
            $('#BlockResp').html(response);

        }
    });
}

function getPenalty(penalty_id) {
    // alert(receive_bill_id);
    $.ajax({
        url: "getPenalty.php",
        cache: false,
        type: "POST",
        data: { penalty_id: penalty_id, csrf: csrf },
        success: function (response) {
            $('#billPayDiv').html(response);


        }
    });
}

//IS_605 26feb2020
function getPenaltyEdit(penalty_id) {
    // alert(receive_bill_id);
    $.ajax({
        url: "getPenalty.php",
        cache: false,
        type: "POST",
        data: { penalty_id: penalty_id, EditFlg: "Yes", csrf: csrf },
        success: function (response) {
            $('#EditPanDiv').html(response);


        }
    });
}
//IS_605 26feb2020


//12march2020
function getCommEntry(common_entry_id) {
    $.ajax({
        url: "getCommEntry.php",
        cache: false,
        type: "POST",
        data: { common_entry_id: common_entry_id, csrf: csrf },
        success: function (response) {
            $('#editCMDiv').html(response);


        }
    });
}
//12march2020



//13march2020
function editMainType(visitor_main_type_id) {
    $.ajax({
        url: "getVisitorDetails.php",
        cache: false,
        type: "POST",
        data: { visitor_main_type_id: visitor_main_type_id, csrf: csrf },
        success: function (response) {
            $('#updateModalDiv').html(response);


        }
    });
}

function editSubcategory(visitor_sub_type_id) {
    $.ajax({
        url: "getVisitorDetails.php",
        cache: false,
        type: "POST",
        data: { visitor_sub_type_id: visitor_sub_type_id, csrf: csrf },
        success: function (response) {
            $('#updateSubModalDiv').html(response);


        }
    });
}
function DeleteVisitorMain(visitor_main_type_id) {


    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                // $('form.deleteForm'+id).submit();


                $.ajax({
                    url: 'controller/addMainTypeVisitorsController.php',
                    cache: false,
                    type: "POST",
                    data: { visitor_main_type_id: visitor_main_type_id, deleteData: 'deleteData', csrf: csrf },
                    success: function (response) {

                        if (response == 1217) {

                            swal("Not Deleted", "Visitor sub type has records with this Main Type, Please Delete Those records before you delete main visitor type...!", "error");
                        } else if (response == 1) {

                            swal("Poof! Your data has been deleted!", {
                                icon: "success",
                            });
                            location.reload();
                        } else {
                            swal("Poof! Your data has been deleted!", {
                                icon: "error",
                            });
                        }
                    }
                });
            } else {
                swal("Your data is safe!");
            }
        });

}

//13march2020

function deleteClassifiedCategory(classified_cat_id) {
    // alert(block_id);
    swal({
        title: "Are you sure to Delete this Category ?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                // $('form.deleteForm'+id).submit();
                $.ajax({
                    url: "controller/classifiedController.php",
                    cache: false,
                    type: "POST",
                    data: { classified_cat_id_delete: classified_cat_id, csrf: csrf },
                    success: function (response) {
                        console.log(response)
                        if (response == 1) {
                            swal("Poof! Your data has been deleted!", {
                                icon: "success",
                            });
                            document.location.reload(true);
                        }
                    }
                });
            } else {
            }
        });
}
function deleteClassifiedSubCategory(classified_sub_id) {
    // alert(block_id);
    swal({
        title: "Are you sure to Delete this Sub Category ?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                // $('form.deleteForm'+id).submit();
                $.ajax({
                    url: "controller/classifiedController.php",
                    cache: false,
                    type: "POST",
                    data: { classified_sub_id_delete: classified_sub_id, csrf: csrf },
                    success: function (response) {
                        console.log(response)
                        if (response == 1) {
                            swal("Poof! Your data has been deleted!", {
                                icon: "success",
                            });
                            document.location.reload(true);
                        }
                    }
                });
            } else {
            }
        });
}


function editinstallationteam(societyid, memberid, membername, membermobile) {
    $('#member_society_id option[value=' + societyid + ']').attr('selected', 'selected');
    $('#member_id').val(memberid);
    $('#member_full_name').val(membername);
    $('#member_mobile_number').val(membermobile);
}



$(".docOnly").change(function () {
    var fileExtension = ['jpeg', 'jpg', 'png', 'doc', 'docx', 'pdf', 'jpg', 'csv', 'xls', 'xlsx'];
    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        // alert("Only formats are allowed : "+fileExtension.join(', '));
        swal("Only formats are allowed : " + fileExtension.join(', '), { icon: "error", });
        $('.idProof').val('');
    }
});

$(".idProof").change(function () {
    var fileExtension = ['jpeg', 'jpg', 'png', 'doc', 'docx', 'pdf', 'jpg'];
    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        // alert("Only formats are allowed : "+fileExtension.join(', '));
        swal("Only formats are allowed : " + fileExtension.join(', '), { icon: "error", });
        $('.idProof').val('');
    }
});

$(".photoOnly").change(function () {
    // alert(this.files[0].size);
    var fileExtension = ['jpeg', 'jpg', 'png'];
    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        // alert("Only formats are allowed : "+fileExtension.join(', '));
        swal("Only formats are allowed : " + fileExtension.join(', '), { icon: "error", });
        $('.photoOnly').val('');
    }
});

$(".mp3Only").change(function () {
    // alert(this.files[0].size);
    var fileExtension = ['mp3'];
    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        // alert("Only formats are allowed : "+fileExtension.join(', '));
        swal("Only formats are allowed : " + fileExtension.join(', '), { icon: "error", });
        $('.mp3Only').val('');
    }
});

$('.csvupload').change(function () {
    var fileExtension = ['csv'];
    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        // alert("Only formats are allowed : "+fileExtension.join(', '));
        swal("Only formats are allowed : " + fileExtension.join(', '), { icon: "error", });
        $('.csvupload').val('');
    }
});


$(".alphanumeric").keypress(function (e) {
    var keyCode = e.which;
    if (!((keyCode >= 48 && keyCode <= 57)
        || (keyCode >= 65 && keyCode <= 90)
        || (keyCode >= 97 && keyCode <= 122))
        && keyCode != 8 && keyCode != 32) {
        e.preventDefault();
    }
});


//14march2020
/*$('.txtNumeric').keydown(function (e) { //alert();
  
  var key = e.keyCode;
  
    if (!(  (key == 8) ||   (key == 189) ||  (key == 32)  || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90) || (key >= 48 && key <= 57) || (key >= 96 && key <= 105) )) {
    e.preventDefault();
    } 
  });*/
$('.txtNumeric').keypress(function (event) {
    var kcode = event.keyCode;

    if (kcode == 8 ||
        kcode == 9 ||

        kcode == 95 ||
        kcode == 45 ||
        kcode == 32 ||
        (kcode > 47 && kcode < 58) ||
        (kcode > 64 && kcode < 91) ||
        (kcode > 96 && kcode < 123)) {
        return true;
    }
    else {
        return false;
    }
});
$('.txtNumeric').bind("cut copy paste", function (e) {
    e.preventDefault();
});
//14march2020

$('.number').keypress(function (event) {
    if (event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
});



$('.space').on('keypress', function (e) {
    if (e.which == 32) {
        return false;
    }
});



function deleteSliderImage(sliderid) {
    swal({
        title: "Are you sure to Delete this Slider ?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: "controller/sliderController.php",
                    cache: false,
                    type: "POST",
                    data: { deletesliderid: sliderid, csrf: csrf },
                    success: function (response) {
                        console.log(response)
                        if (response == 1) {
                            document.location.reload(true);
                        }
                    }
                });
            }
        });
}

function rejectSp(sp_request_id, service_provider_phone, service_provider_email, service_provider_name) {
    $('#sp_request_id').val(sp_request_id);
    $('#service_provider_phone').val(service_provider_phone);
    $('#service_provider_email').val(service_provider_email);
    $('#service_provider_name').val(service_provider_name);

}


//30sept2020
$(document).on("click", ".open-AddBookDialog", function () {
    var timeline_id = $(this).data('timeline_id');

    $("#publishTimelineFrm .modal-body #timeline_id").val('');
    $("#publishTimelineFrm   #timeline_id").val(timeline_id);
});


$(".city_id_timeline").change(function () {

    var sosa_city_id = $("#city_id option:selected").val();
    var timeline_id = $("#timeline_id").val();
    $.ajax({
        url: "ajaxGetSocietyDetails.php",
        cache: false,
        type: "POST",
        data: { sosa_city_id: sosa_city_id, timeline_id: timeline_id },
        success: function (response) {

            $('#sosa_detail').html(response);

        }
    });
});
$('#publishTimelineFrm').on('submit', function (e) {

    e.preventDefault();
    var timeline_id = $('#timeline_id').val();
    var val = [];
    $('.pagePrivilege:checkbox:checked').each(function (i) {
        val[i] = $(this).val();
        var society_id = $(this).val();
        var csrf = $('input[name="csrf"]').val();
        var isposted = $('#val_' + society_id).val();
        if (isposted == "1") {
            $.ajax({

                url: "controller/newsFeedController.php",
                cache: false,
                type: "POST",
                data: { society_id_post: society_id, timeline_id: timeline_id, publishPostNew: "publishPostNew", csrf: csrf },
                beforeSend: function () {
                    $('#result_' + society_id).html('<i class="fa fa fa-spinner fa-2x mr-3 text-secondary"></i>');
                    $(':input[type="submit"]').prop('disabled', true);
                    $("#sosa_city_id").prop("disabled", true);
                },
                success: function (response) {

                    var result = response.split(':');
                    // alert(result[1]);
                    if (result[1] == "201") {
                        $('#val_' + society_id).val('0');
                        $("#chkError").addClass('text-danger');
                        $('#result_' + society_id).html('<i class=text-danger>' + result[0] + '</i>');
                        $("#sosa_city_id").prop("disabled", false);
                        $(':input[type="submit"]').prop('disabled', false);
                        // $( "#sosa_city_id" ).change();
                    } else {
                        $('#val_' + society_id).val('1');
                        $("#chkError").addClass('text-primary');
                        $('#result_' + society_id).html('<i class=text-success>' + result[0] + '</i>');
                        $("#sosa_city_id").prop("disabled", false);
                        $(':input[type="submit"]').prop('disabled', false);
                        $("#sosa_city_id").change();
                    }

                }
            });
        }


    });
});


$('#publishMasterPasswordFrm').on('submit', function (e) {
    e.preventDefault();

    createSwalProgressBar();

    const masterAuth = $('#masterAuthPassword').val();
    const csrf = $('input[name="csrf"]').val();

    const selectedSocieties = $('.pagePrivilege:checkbox:checked').map(function () {
        return $(this).val();
    }).get();
    if (selectedSocieties.length === 0) {
        swal({
            title: "Error",
            text: "No societies selected!",
            icon: "error",
            timer: 3000,
        });
        return;
    }

    processRequests(selectedSocieties, function (society_id, callback) {
        const isposted = $('#val_' + society_id).val();
        console.log(title);

        if (isposted === "1") {
            $.ajax({
                url: "controller/profileController.php",
                type: "POST",
                data: {
                    society_id_post: society_id,
                    masterAuth: masterAuth,
                    changePasswordMaster: "changePasswordMaster",
                    csrf: csrf
                },
                beforeSend: function () {
                    $('#result_' + society_id).html('<i class="fa fa-spinner fa-2x text-secondary"></i>');
                },
                success: function (response) {
                    const result = response.split(':');
                    if (result[1] === "201") {
                        $('#val_' + society_id).val('0');
                        $('#result_' + society_id).html('<i class="text-danger">' + result[0] + '</i>');
                        callback(false);
                    } else {
                        $('#val_' + society_id).val('1');
                        $('#result_' + society_id).html('<i class="text-success">' + result[0] + '</i>');
                        callback(true);
                    }
                },
                error: function () {
                    $('#result_' + society_id).html('<i class="text-danger">Error processing request.</i>');
                    callback(false);
                }
            });
        } else {
            callback(false);
        }
    });
});
function checkMasterPassword(society_id, sub_domain, masterAuth) {
    var csrf = $('input[name="csrf"]').val();
    $.ajax({

        url: "controller/profileController.php",
        cache: false,
        type: "POST",
        data: { society_id: society_id, sub_domain: sub_domain, admin_password: masterAuth, masterAuthCheck: "masterAuthCheck", csrf: csrf },
        success: function (response) {

            var result = response.split(':');
            if (result[1] == "201") {
                $('#val_' + society_id).val('0');
                $("#chkError").addClass('text-danger');
                $('#result_' + society_id).html('<i class=text-danger>' + result[0] + '</i>');
                $(':input[type="submit"]').prop('disabled', false);
                // $( "#sosa_city_id" ).change();
            } else {
                swal(result[0], {
                    icon: "success",
                });
                // $( "#sosa_city_id" ).change();
            }

        }
    });

}

function SynchronizationSociety() {
    $(".ajax-loader").show();
    var csrf = $("#csrf").val();
    $.ajax({
        url: "../cron/datasynchronization.php",
        cache: false,
        type: "POST",
        data: { csrf: csrf },
        success: function (response) {
            $(".ajax-loader").hide();
            swal(response, {
                icon: "success",
            });
        }
    });
}



function changeSplash(society_id) {
    $(".ajax-loader").show();
    var csrf = $("#csrf").val();
    $.ajax({
        url: "getSplashData.php",
        cache: false,
        type: "POST",
        data: { csrf: csrf, society_id: society_id },
        success: function (response) {
            $(".ajax-loader").hide();
            $('#addUserDiv').html(response);
        }
    });
}


$('.lang_chk').change(function () {
    var elmId = $(this).attr("id");

    if (this.checked) {
        $('#detail_div' + elmId).css('display', 'block');

    } else {
        $('#detail_div' + elmId).css('display', 'none');


    }

});


$('.key_name_cls').change(function () {
    var elmId = $(this).attr("id");

    if (this.checked) {
        $('#txt_div' + elmId).css('display', 'block');

    } else {
        $('#txt_div' + elmId).css('display', 'none');


    }

});

function isExpire() {
    var is_expiry = $("#is_expiry option:selected").val();
    if (is_expiry == "Yes") {
        $('#date_div').css('display', 'block');

    } else {
        $('#date_div').css('display', 'none');

    }
}

function showToName() {
    var show_to_name = $("#show_to_name option:selected").val();
    if (show_to_name == "Yes") {
        $('#to_name_div').css('display', 'block');
    } else {
        $('#to_name_div').css('display', 'none');

    }
}

function toNameDemo() {
    var to_name_font_name = $("#to_name_font_name option:selected").val();
    $('#demo_to_text').html('<span class="' + to_name_font_name + '">Greetings</span>');
}
function fromNameDemo() {
    var from_name_font_name = $("#from_name_font_name option:selected").val();
    $('#demo_from_text').html('<span class="' + from_name_font_name + '">Greetings</span>');
}
function descNameDemo() {
    var description_font_name = $("#description_font_name option:selected").val();
    $('#demo_desc_text').html('<span class="' + description_font_name + '">Greetings</span>');
}
function titleNameDemo() {
    var title_font_name = $("#title_font_name option:selected").val();
    $('#demo_title_text').html('<span class="' + title_font_name + '">Greetings</span>');
}


function showFromName() {
    var show_from_name = $("#show_from_name option:selected").val();
    if (show_from_name == "Yes") {
        $('#from_name_div').css('display', 'block');
    } else {
        $('#from_name_div').css('display', 'none');

    }
}

function getDataSociety() {
    var country_id = $("#countryId").val();
    var state_id = $("#sId").val();
    var city_id = $("#cId").val();

    $.ajax({
        url: "ajaxGetSocietyDetailsData.php",
        cache: false,
        type: "POST",
        data: { country_id: country_id, city_id: city_id, state_id: state_id },
        success: function (response) {

            $('#sosa_detail').html(response);

        }
    });
}


$("#menu-list").sortable({
    placeholder: "ui-state-highlight",
    update: function (event, ui) {
        var csrf = $('input[name="csrf"]').val();
        var selectedData = new Array();
        $('.adminBox').each(function () {
            selectedData.push($(this).data("post-id"));
        });
        console.log(selectedData);
        $.ajax({
            url: "controller/changeOrderController.php",
            method: "POST",
            data: { position: selectedData, changeSocietyMenuOrder: 'changeSocietyMenuOrder', csrf: csrf },
            success: function (data) {
                if (data == 1) {
                    swal("Order Changed Successfully", {
                        icon: "success",
                    });
                } else {
                    swal("Failed to Changed Order", {
                        icon: "error",
                    });
                }
            }
        });
    }
});


$(".menu-listSub").sortable({
    placeholder: "ui-state-highlight",
    update: function (event, ui) {
        var csrf = $('input[name="csrf"]').val();
        var selectedData = new Array();
        $('.adminBoxSub').each(function () {
            selectedData.push($(this).data("post-id"));
        });
        console.log(selectedData);
        $.ajax({
            url: "controller/changeOrderController.php",
            method: "POST",
            data: { position: selectedData, changeSocietyMenuOrder: 'changeSocietyMenuOrder', csrf: csrf },
            success: function (data) {
                if (data == 1) {
                    swal("Order Changed Successfully", {
                        icon: "success",
                    });
                } else {
                    swal("Failed to Changed Order", {
                        icon: "error",
                    });
                }
            }
        });
    }
});

$("#menu-listBig").sortable({
    placeholder: "ui-state-highlight",
    update: function (event, ui) {
        var csrf = $('input[name="csrf"]').val();
        var selectedData = new Array();
        $('.adminBoxBig').each(function () {
            selectedData.push($(this).data("post-id"));
        });
        console.log(selectedData);
        $.ajax({
            url: "controller/changeOrderController.php",
            method: "POST",
            data: { position: selectedData, changeSocietyMenuOrder: 'changeSocietyMenuOrder', csrf: csrf },
            success: function (data) {
                if (data == 1) {
                    swal("Order Changed Successfully", {
                        icon: "success",
                    });
                } else {
                    swal("Failed to Changed Order", {
                        icon: "error",
                    });
                }
            }
        });
    }
});

$('#publishSocetyDataFrm').on('submit', function (e) {
    e.preventDefault(); // Prevent form submission

    createSwalProgressBar();

    var csrf = $('input[name="csrf"]').val();
    var city_id = $('#cId').val();
    var getDataType = $('#getDataType').val();
    var webAiStatus = $('#bulkWebAiStatus').length ? $('#bulkWebAiStatus').val() : '';
    var webAiCredentialsId = $('#bulkWebAiCredentialsId').length ? $('#bulkWebAiCredentialsId').val() : '';
    var webAiUrlMode = '';
    var mobileAiStatus = $('#bulkMobileAiStatus').length ? $('#bulkMobileAiStatus').val() : '';
    var mobileAiCredentialsId = $('#bulkMobileAiCredentialsId').length ? $('#bulkMobileAiCredentialsId').val() : '';
    var mobileAiMode = '';
    if (getDataType === '25') {
        webAiUrlMode = (webAiStatus === '1')
            ? ($('#bulkWebAiUrlModeOn').val() || 'fill_blank')
            : ($('#bulkWebAiUrlModeOff').val() || 'keep');
    }
    if (getDataType === '26') {
        mobileAiMode = (mobileAiStatus === '1')
            ? ($('#bulkMobileAiModeOn').val() || 'fill_blank')
            : ($('#bulkMobileAiModeOff').val() || 'keep');
    }
    var selectedSocieties = $('.pagePrivilege:checkbox:checked').map(function () {
        return $(this).val();
    }).get();

    if (selectedSocieties.length === 0) {
        swal({
            title: "Error",
            text: "No societies selected!",
            icon: "error",
            timer: 3000,
        });
        return;
    }
    if (getDataType === '0') {
        swal({
            title: "Error",
            text: "Please select type of data !",
            icon: "error",
            timer: 3000,
        });
        return;
    }
    if (getDataType === '25' && webAiStatus === '1' && (!webAiCredentialsId || webAiCredentialsId === '')) {
        swal({
            title: "Error",
            text: "Please select a Web AI credential to enable!",
            icon: "error",
            timer: 3000,
        });
        return;
    }
    if (getDataType === '26' && mobileAiStatus === '1' && mobileAiMode === 'replace' && (!mobileAiCredentialsId || mobileAiCredentialsId === '')) {
        swal({
            title: "Error",
            text: "Please select a Mobile AI credential to enable!",
            icon: "error",
            timer: 3000,
        });
        return;
    }
    if (getDataType === '26' && mobileAiStatus === '1' && mobileAiMode === 'fill_blank' && (!mobileAiCredentialsId || mobileAiCredentialsId === '')) {
        // Credential still required for companies that are blank; validation per-company is in backend.
        // Require selection so blank companies can be filled in the same run.
        swal({
            title: "Error",
            text: "Please select a Mobile AI credential (used when blank)!",
            icon: "error",
            timer: 3000,
        });
        return;
    }

    // Allow retry without page refresh (failed runs previously set val_ to 0)
    selectedSocieties.forEach(function (society_id) {
        $('#val_' + society_id).val('1');
    });

    processRequests(selectedSocieties, function (society_id, callback) {
        var isposted = $('#val_' + society_id).val();

        if (isposted === "1") {
            var ajaxData = {
                society_id_post: society_id,
                city_id: city_id,
                getDataType: getDataType,
                getCompanyData: "getCompanyData",
                csrf: csrf
            };
            if (getDataType === '25') {
                ajaxData.web_ai_status = webAiStatus;
                ajaxData.web_ai_credentials_id = webAiCredentialsId;
                ajaxData.web_ai_url_mode = webAiUrlMode;
            }
            if (getDataType === '26') {
                ajaxData.ai_status = mobileAiStatus;
                ajaxData.ai_credentials_id = mobileAiCredentialsId;
                ajaxData.mobile_ai_mode = mobileAiMode;
            }
            $.ajax({
                url: "controller/companyActionsController.php",
                type: "POST",
                dataType: "json",
                data: ajaxData,
                beforeSend: function () {
                    $('#result_' + society_id).html('<i class="fa fa-spinner fa-2x text-secondary"></i>');
                },
                success: function (response) {
                    var message = (response && response.message) ? response.message : 'Unknown response';
                    var status = (response && response.status != null) ? String(response.status) : '201';
                    if (status === "201") {
                        $('#val_' + society_id).val('1');
                        $('#society_chk_' + society_id).addClass('company-action-failed');
                        $('#result_' + society_id).html('<i class="text-danger">' + message + '</i>');
                        callback(false);
                    } else {
                        $('#val_' + society_id).val('1');
                        $('#society_chk_' + society_id).removeClass('company-action-failed');
                        $('#result_' + society_id).html('<i class="text-success">' + message + '</i>');
                        callback(true);
                    }
                },
                error: function () {
                    $('#val_' + society_id).val('1');
                    $('#society_chk_' + society_id).addClass('company-action-failed');
                    $('#result_' + society_id).html('<i class="text-danger">Error processing request.</i>');
                    callback(false);
                }
            });
        } else {
            callback(false); // Skip if not posted
        }
    });
});

$(document).on('click', '#refreshEmployeeInfoBtn', function () {
    var $btn = $(this);
    if ($btn.prop('disabled')) {
        return;
    }
    $btn.prop('disabled', true);

    createSwalProgressBar();

    $.ajax({
        url: 'controller/userController.php',
        type: 'POST',
        dataType: 'json',
        data: {
            refreshEmployeeInfoList: 'refreshEmployeeInfoList',
            csrf: typeof csrf !== 'undefined' ? csrf : $('input[name="csrf"]').val()
        },
        success: function (response) {
            var admins = (response && response.success && Array.isArray(response.admins)) ? response.admins : [];
            if (admins.length === 0) {
                swal({
                    title: 'Error',
                    text: (response && response.message) ? response.message : 'No bound employees found',
                    icon: 'error',
                    timer: 3000,
                });
                $btn.prop('disabled', false);
                return;
            }

            processRequests(admins, function (admin_id, callback) {
                $.ajax({
                    url: 'controller/userController.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        refreshEmployeeInfo: 'refreshEmployeeInfo',
                        admin_id: admin_id,
                        csrf: typeof csrf !== 'undefined' ? csrf : $('input[name="csrf"]').val()
                    },
                    success: function (itemResponse) {
                        callback(!!(itemResponse && itemResponse.success));
                    },
                    error: function () {
                        callback(false);
                    }
                });
            }, 1);
        },
        error: function () {
            swal({
                title: 'Error',
                text: 'Failed to fetch employee information',
                icon: 'error',
                timer: 3000,
            });
            $btn.prop('disabled', false);
        }
    });
});
$('#recentpublishSocetyDataFrm').on('submit', function (e) {
    e.preventDefault();
    createSwalProgressBar();

    var csrf = $('input[name="csrf"]').val();
    var city_id = $('input[name="cId"]').val();
    var days = $('input[name="days"]').val();

    let selectedSocieties = $('.pagePrivilege:checkbox:checked').map(function () {
        return $(this).val();
    }).get();

    if (selectedSocieties.length === 0) {
        swal({
            title: "Error",
            text: "No societies selected!",
            icon: "error",
            timer: 3000,
        });
        return;
    }

    processRequests(selectedSocieties, function (society_id, callback) {
        var isposted = $('#val_' + society_id).val();

        if (isposted == "1") {
            $.ajax({
                url: "controller/buildingController.php",
                type: "POST",
                data: {
                    society_id_post: society_id,
                    city_id: city_id,
                    days: days,
                    recentGetSoceietyData: "recentGetSoceietyData",
                    csrf: csrf
                },
                beforeSend: function () {
                    $('#result_' + society_id).html('<i class="fa fa-spinner fa-2x text-secondary"></i>');
                },
                success: function (response) {
                    var result = response.split(':');
                    if (result[1] === "201") {
                        $('#val_' + society_id).val('0');
                        $('#result_' + society_id).html('<i class="text-danger">' + result[0] + '</i>');
                        callback(false);
                    } else {
                        $('#val_' + society_id).val('1');
                        $('#result_' + society_id).html('<i class="text-success">' + result[0] + '</i>');
                        callback(true);
                    }
                },
                error: function () {
                    $('#result_' + society_id).html('<i class="text-danger">Error processing request.</i>');
                    callback(false);
                }
            });
        } else {
            callback(false);
        }
    });
});

/// DY 20-10-2023 End


$('#companyId').on('change', function () {
    var society_id = $("#companyId").val();
    var sourceType = $('#ticket_source_type').val() ?? '0';
    if (!society_id) {
        $('#email').val('');
        $('#name').val('');
        $('#mobile').val('');
        return;
    }
    if (sourceType === '1') {
        $('#email').val('');
        $('#name').val('');
        $('#mobile').val('');
        return;
    }
    $.ajax({
        url: "controller/feedbackController.php",
        type: "POST",
        dataType: 'JSON',
        data: {
            society_id: society_id,
            csrf: csrf,
            getCompanyDetails: 'getCompanyDetails',
            source_type: sourceType,
            whitelabel_type: $('#whitelabel_type').val() ?? ''
        },
        success: function (response) {
            if (response.status == "200") {
                $('#email').val(response.secretary_email);
                $('#name').val(response.secretary_name);
                $('#mobile').val(response.secretary_mobile);
            } else {
                $('#email').val('');
                $('#name').val('');
                $('#mobile').val('');
            }
        }
    });
})

function AddInstitute(request_society_id) {
    $("#instituteCompanyId").val(request_society_id);
}


$(document).ready(function () {
    $('.single-select').select2();
    $('.single-select-new').select2({
        minimumInputLength: 3
    });
});

function editDeveloperModel(developer_name, developer_phone, developer_email, developer_technology, developer_id) {
    if ($("#editDeveloper").valid()) {
        $("#developer_name").removeClass('error');
    }

    $("#developer_name").val(developer_name);
    $("#developer_phone").val(developer_phone);
    $("#developer_email").val(developer_email);
    $("#developer_technology").val(developer_technology).change();
    $("#developer_id").val(developer_id);
    // $('#editEmp').modal();
}

$("#addDeveloperBtn").on('click', function () {
    $("#addNewDeveloper")[0].reset();

})

function previewDoc(e, id) {
    file = e.files[0];
    if (file) {
        let reader = new FileReader();
        if (file.type !== "application/pdf" && (file.type === "image/jpeg" || file.type === "image/jpg" || file.type === "image/png" || file.type === "image/gif")) {
            reader.onload = function (event) {
                $("#" + id).children("img").attr("src", event.target.result);
            };
            reader.readAsDataURL(file);
            $("#" + id).children("button").removeClass("d-none");
            $("#" + id).children("img").removeClass("d-none");
            $("#" + id).removeClass("d-none")
        } else if (file.type === "application/pdf") {
            reader.onload = function (event) {
                $("#" + id).children("img").attr("src", "../img/pdfIcon.png");
                reader.readAsDataURL(file);
            }
            $("#" + id).children("button").removeClass("d-none");
            $("#" + id).children("img").removeClass("d-none");
            $("#" + id).removeClass("d-none")
        } else {
            $("#" + id).children("button").addClass("d-none");
        }

    } else {
        $("#" + id).children("button").addClass("d-none");
        $("#" + id).children("img").addClass("d-none");
        $("#" + id).removeClass("d-none")
    }
}

function removePreview(e) {
    $(e).siblings("img").attr("src", "");
    $(e).parent("div").addClass("d-none");
    $($(e).parent("div")).siblings("input").val("");
}



// Add Rajesh 02-09-2024
$('#addServerBtn').on('click', function () {

    $('#serverForm').trigger('reset');
    $('#modeshow').html("Add");
    $('#submitButton').attr("name", "addServer");
    $('#submitText').text("ADD");

    $("#edit_server").val("");
});
function editServer(id, name, server_ip, server_remark, edit_server) {
    $("#server_name_edit").val(name);
    $("#server_ip_edit").val(server_ip);
    $("#server_remark_edit").val(server_remark);
    $("#server_id_edit").val(id);
    $("#edit_server").val(edit_server);
    if (edit_server !== "") {
        $("#modeshow").html("Edit");
        $("#submitButton").attr("name", "editServer");
        $("#submitText").text("UPDATE");
    }
}
$(document).ready(function () {
    $('#addDomainBtn').on('click', function () {
        $('#domainForm').trigger('reset');
        $("#modeshow").html("Add");
        $("#submitButton").attr("name", "addDomain");
        $("#submitText").text("ADD");
        $("#subDomainAdded").show();

        $("edit_server").val("");
    });
});
function editDomain(id, name, domain_remark, server_id, edit_domain, is_remote_db, remote_ip) {
    $("#domain_name_edit").val(name);
    $("#domain_remark_edit").val(domain_remark);
    $("#domain_id_edit").val(id);
    if (is_remote_db == '1') {
        $("#server_id_edit").val(server_id).prop('disabled', true).trigger('change');
        $("#server_id_hidden").val(server_id);
    } else {
        $("#server_id_edit").val(server_id).trigger('change');
        $("#server_id_hidden").val();
    }
    $("#remote_db").val(is_remote_db);
    if (typeof toggleRemoteIpField === 'function') { toggleRemoteIpField(); }
    $("#remote_ip").val(remote_ip);
    $("#edit_domain").val(edit_domain);
    $("#subDomainAdded").hide();
    if (edit_domain !== "") {
        $("#modeshow").html("Edit");
        $("#submitButton").attr("name", "editDomain");
        $("#submitText").text("UPDATE");
    }


}

$(document).ready(function () {
    $('[id^="commonTextEditor"]').each(function () {
        var editorId = $(this).attr('id');
        var index = editorId.replace('commonTextEditor', '');
        $('#' + editorId).summernote({
            height: 200,
            tabsize: 2,
            tooltip: false,
            callbacks: {
                onChange: function (contents, $editable) {
                    $('#whats_new_updates_' + index).val(contents);
                }
            }
        });
        var contentToCopy = $('#' + editorId).html();
        $('#whats_new_updates_' + index).val(contentToCopy);
    });
});
$(document).ready(function () {
    window.dynamicRules = {};
    window.dynamicMessages = {};
    $('[id^="whats_new_updates_"]').each(function () {
        var fieldId = $(this).attr('id');
        window.dynamicRules[fieldId] = {
            required: true,
            noSpace: true,
            checkspace: true,
        };
        window.dynamicMessages[fieldId] = {
            required: "Content cannot be empty.",
        };
    });
});


function webUpdates() {
    var platform = $('#platform').val();
    if (platform == '2') {
        $(".versionbox").hide();
    } else {
        $(".versionbox").show();
    }
}
webUpdates();


function deleteSingleWhatsNew(patch_id) {
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: "controller/whatsNewController.php",
                    cache: false,
                    type: "POST",
                    data: { "patch_id": patch_id, "singleDelete": "singleDelete", csrf: csrf },
                    success: function (response) {
                        if (response == 1) {
                            history.go(0);
                        } else {
                            history.go(0);
                        }
                    }
                });
            }
        });
}
// whatsnew end
// done

$(document).ready(function () {
    $('#range').on('input', function () {
        var x = $(this).val();
        $('#output').css('margin-left', (x - 1) * 10 + '%').text(x);
    }).trigger('input'); // Trigger input event to set initial value on page load
});


/*24-OCT-2024 mihir start*/
function maintenanceAll(deleteValue) {
    var val = [];
    $('.multiMaintenanceCheckbox:checkbox:checked').each(function (i) {
        val[i] = $(this).val();
    });
    if (val == "") {
        swal(
            'Warning !',
            'Please Select at least 1 domain !',
            'warning'
        );
    } else {
        $('#domain_ids').val(val.join(','));
        $('#startMaintenance').modal('show');
    }
}
function endMaintenance() {
    swal({
        title: "Are you sure?",
        text: "Maintenance will be removed from all domain!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: "controller/domainController.php",
                    cache: false,
                    type: "POST",
                    data: { 'removeMaintenance': 'removeMaintenance', 'csrf': csrf },
                    success: function (response) {
                        history.go(0);
                    }
                });
            }
        });
}

$('.selectAllMaintenanceCheckbox').click(function () {
    if (this.checked) {
        $('.multiMaintenanceCheckbox').prop('checked', true);
    } else {
        $('.multiMaintenanceCheckbox').prop('checked', false);
    }
});
$('.multiMaintenanceCheckbox').change(function () {
    if ($('.multiMaintenanceCheckbox:checked').length === $('.multiMaintenanceCheckbox').length) {
        $('.selectAllMaintenanceCheckbox').prop('checked', true);
    } else {
        $('.selectAllMaintenanceCheckbox').prop('checked', false);
    }
});

function ongoingPatch(is_under_patch) {
    if (is_under_patch > 0) {
        patch_val = '0';
        tostmsg = "Current patch will be removed!";
    } else {
        patch_val = '1';
        tostmsg = "A new patch will start!";
    }
    swal({
        title: "Are you sure?",
        text: tostmsg,
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: "controller/domainController.php",
                    cache: false,
                    type: "POST",
                    data: { 'updatePatch': 'updatePatch', 'patch_val': patch_val, 'csrf': csrf },
                    success: function (response) {
                        history.go(0);
                    }
                });
            }
        });
}
function createCrm(request_society_id, society_name, sub_domain) {
    $("#society_id").val(request_society_id);
    $(".companyId").val(request_society_id);
    $("#companyName").val(society_name);
    $("#subDomain").val(sub_domain);
    $.ajax({
        url: './companyListAjax.php',
        type: 'POST',
        data: {
            getCRMList: "getCRMList",
            csrf: csrf,
            request_society_id: request_society_id,
        },
        dataType: 'json',
        success: function (response) {
            if (response.success && response.data.societies.length > 0) {
                var options = '<option value="">-- Select --</option>';
                $.each(response.data.societies, function (index, crm) {
                    if (crm.selected != "1") {
                        options += '<option value="' + crm.society_crm_id + '">' + crm.server + '</option>';
                    } else {
                        options += '<option selected value="' + crm.society_crm_id + '">' + crm.server + '</option>';
                    }
                });
                $('#society_crm_id').html(options);
            } else {
                $('#society_crm_id').html('<option value="">No CRM available</option>');
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX error: ' + error);
        }
    });
}
//Progessbar Model Start JS
function updateProgressBar(completed, total, requestTimes, successCount = 0, failedCount = 0) {

    var progressPercent = ((completed / total) * 100).toFixed(2);
    $(".custom-progress-style-bar").css("width", `${progressPercent}%`).text(`${progressPercent}%`);

    var avgTimePerRequest = requestTimes.reduce((a, b) => a + b, 0) / completed;
    var remainingRequests = total - completed;
    var remainingTime = remainingRequests * avgTimePerRequest;

    var hours = Math.floor(remainingTime / 1000 / 3600);
    var minutes = Math.floor((remainingTime / 1000 / 60) % 60);
    var seconds = Math.ceil((remainingTime / 1000) % 60);

    var estimatedTimeText = '';
    if (hours > 0) {
        estimatedTimeText += `${hours} hour${hours !== 1 ? 's' : ''}`;
    }
    if (minutes > 0) {
        if (estimatedTimeText) estimatedTimeText += ' and ';
        estimatedTimeText += `${minutes} minute${minutes !== 1 ? 's' : ''}`;
    }
    if (seconds > 0 && !hours && !minutes) {
        estimatedTimeText = `${seconds} second${seconds !== 1 ? 's' : ''}`;
    } else if (seconds > 0) {
        estimatedTimeText += ` and ${seconds} second${seconds !== 1 ? 's' : ''}`;
    }

    $(".custom-progress-style-time").text(`Estimated Time: ${estimatedTimeText}`);

    $(".custom-progress-bar-total-count").text(`${total}`);
    $(".custom-progress-bar-success-count").text(`${successCount}`);
    $(".custom-progress-bar-failed-count").text(`${failedCount}`);

}

function processRequests(requests, progressBarCallBack, reload = 0) {
    var completedRequests = 0;
    var successRequests = 0;
    var failedRequests = 0;
    var requestTimes = [];

    function processNext(index) {
        if (index >= requests.length) {
            $(".custom-progress-bar-swal-title").text('Your request has now been completed!').css({ color: 'green' });
            $(".custom-progress-relax").html(`<img src="../img/done.gif" style="width: 110px;" />`);
            $(".custom-progress-style-time").text('');
            $(".custom-progress-close-button").html(`<button class="btn btn-outline-primary btn-sm swal-close-button">CLOSE</button>`);

            document.querySelector(".swal-close-button").addEventListener("click", () => {
                swal.close();
                is_swal_on = false;
                // displayQueuedSwals();
                if (reload == 1) {
                    window.location.reload();
                }
            });

            return;
        }

        var requestStartTime = Date.now();
        progressBarCallBack(requests[index], function (success) {
            var requestDuration = Date.now() - requestStartTime;
            requestTimes.push(requestDuration);

            completedRequests++;
            if (success) {
                successRequests++;
            } else {
                failedRequests++;
            }

            updateProgressBar(completedRequests, requests.length, requestTimes, successRequests, failedRequests);

            processNext(index + 1);
        });
    }

    processNext(0);
}

//Progessbar Model
function createSwalProgressBar() {
    is_swal_on = true;
    var div = document.createElement("div");
    div.innerHTML = `
      <div style="margin-top: 20px; text-align: left;">
        <div>
          <h5 class='custom-progress-bar-swal-title' align='center'>Please relax, we are processing your request...</h5>
        </div>  
        <div class="custom-progress-style">
          <div class="custom-progress-style-bar" role="progressbar" style="width: 0%;">
            <span class="progress-style-text"></span>
          </div>
        </div>
        <p class="custom-progress-style-time"></p>
      </div>
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 custom-progress-bar-table-background">
            <table class="table text-center custom-progress-count-record">
              <tbody class="no-border">
                <tr class="no-border">
                  <td class="no-border" align=left>Total Selected</td>
                  <td class="custom-progress-bar-total-count no-border">0</td>
                </tr>
                <tr>
                  <td class="no-border" align=left>Success</td>
                  <td class="custom-progress-bar-success-count no-border">0</td>
                </tr>
                <tr>
                  <td class="no-border" align=left>Failed</td>
                  <td class="custom-progress-bar-failed-count no-border">0</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-md-6 custom-progress-relax">
            <img src="../img/relax.gif" style="width: 110px;" />
          </div>
          <div class="col-md-12 mt-3 custom-progress-close-button"></div>
        </div>
    </div>`;
    swal({
        content: div,
        buttons: false,
        closeOnClickOutside: false,
        closeOnEsc: false
    });
    $(".custom-progress-style-time").text("Calculating estimated time...");
}

function ajaxProgerssBarRequest(url, method, data, headers = {}, onSuccess, onError, front = 0) {
    var fullUrl = '../residentApiNew/' + url;

    if (front == 1) {
        fullUrl = url;
    }

    $.ajax({
        url: fullUrl,
        type: method,
        data: data,
        beforeSend: function (xhr) {
            for (const key in headers) {
                xhr.setRequestHeader(key, headers[key]);
            }
        },
        success: onSuccess,
        error: onError
    });
}

$('#syncCrmDataFrm').on('submit', function (e) {
    e.preventDefault();

    createSwalProgressBar();

    var csrf = $('input[name="csrf"]').val();
    var city_id = $('#cId').val();
    var selectedSocieties = $('.pagePrivilege:checkbox:checked').map(function () {
        return $(this).val();
    }).get();

    if (selectedSocieties.length === 0) {
        swal({
            title: "Error",
            text: "No societies selected!",
            icon: "error",
            timer: 3000,
        });
        return;
    }

    processRequests(selectedSocieties, function (society_id, callback) {
        var isposted = $('#val_' + society_id).val();

        if (isposted === "1") {
            $.ajax({
                url: "controller/buildingController.php",
                type: "POST",
                data: {
                    society_id_post: society_id,
                    city_id: city_id,
                    syncSocietyData: "syncSocietyData",
                    csrf: csrf
                },
                beforeSend: function () {
                    $('#result_' + society_id).html(
                        '<i class="fa fa-spinner fa-2x text-secondary"></i>'
                    );
                },
                success: function (response) {

                    /*
                        Response Format:
                        Company Sync Success||200::CRM Response Success||200
                    */

                    var sections = response.split("::");

                    var companyPart = sections[0].split("||");
                    var crmPart = sections[1].split("||");

                    var companyMessage = companyPart[0];
                    var companyCode = companyPart[1];

                    var crmMessage = crmPart[0];
                    var crmCode = crmPart[1];

                    var companyClass =
                        companyCode == "200"
                            ? "text-success"
                            : "text-danger";

                    var crmClass =
                        crmCode == "200"
                            ? "text-success"
                            : "text-danger";

                    var html = `
                        <div class="${companyClass}">
                            <b>Company:</b> ${companyMessage}
                            (${companyCode})
                        </div>

                        <div class="${crmClass}">
                            <b>CRM:</b> ${crmMessage}
                            (${crmCode})
                        </div>
                    `;

                    $('#result_' + society_id).html(html);

                    if (companyCode == "200" && crmCode == "200") {

                        $('#val_' + society_id).val('1');
                        callback(true);
                    } else {
                        $('#val_' + society_id).val('0');
                        callback(false);
                    }
                },
                error: function () {
                    $('#result_' + society_id).html(`
                        <div class="text-danger">
                            Request Failed
                        </div>
                    `);
                    callback(false);
                }
            });
        } else {
            callback(false);
        }
    });
});

//let counter = 1;
$("#rowAdder").click(function () {
    let counter = $('#counter').val();
    let add_image_only = $('#add_image_only').val();
    counter++;
    $.ajax({
        url: 'ajaxSeasonalGreet.php',
        type: 'POST',
        data: {
            action: 'addDynamicBlock',
            counter: counter
        },
        success: function (response) {
            if ($("#dynamicFieldsContainer").find('.dynamic-block').length === 0) {
                $("#dynamicFieldsContainer").append(response);
            } else {
                $("#dynamicFieldsContainer .dynamic-block:last").after(response);
            }

            if (add_image_only != "add_image_only") {
                $('form.check-validate').validate();
                $('[id="background_image_' + counter + '"]').each(function () {
                    $(this).rules('add', {
                        required: function () {
                            return $('[name^="background_image[' + counter + ']"]').length > 0;
                        }, messages: {
                            required: "Please upload an image"
                        }
                    });
                });
                $("#seasonalGreetFrm").validate().element($('[id="background_image_' + counter + '"]'));
            }

            $('#counter').val(counter);
        },
        error: function (xhr, status, error) {
            alert("Error adding new block. Please try again.");
        }
    });
});

$(document).on("click", ".remove-block", function () {
    const blockElement = $(this).closest('.dynamic-block');

    if ($("#dynamicFieldsContainer").find('.dynamic-block').length > 0) {
        $.ajax({
            url: 'ajaxSeasonalGreet.php',
            type: 'POST',
            data: {
                action: 'removeDynamicBlock',
                blockId: blockElement.attr('id')
            },
            success: function (response) {
                blockElement.fadeOut(400, function () {
                    $(this).remove();
                    counter++;
                    // $("#seasonalGreetFrm").valid();
                });
            },
            error: function (xhr, status, error) {
                alert("Error removing block. Please try again.");
            }
        });
    }
});

$(function () {
    $("#searchMenu").autocomplete({
        // source: 'menu-search.php',

        source: function (request, response) {
            $.getJSON('menu-search.php', { term: $("#searchMenu").val() },
                response);
        },
        select: function (e, ui) {
            var menuUrl = ui['item']['id'];

            window.location.href = menuUrl;
        }
    });
});
function changeStatusNew(id, status, newStatus, statusValue, newStatusVal, buttonId, value = null, reload = 0, url = "", buttonNegText = "", buttonPosText = "") {
    if (url == "") {
        url = "controller/statusController.php";
    }
    swal({
        title: "Are you sure?",
        text: "",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        buttons: ['Cancel', 'Yes, I am sure !'],
    })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: url,
                    cache: false,
                    type: "POST",
                    data: { id: id, status: status, value: value, csrf: csrf },
                    success: function (response) {
                        if (response == 1) {
                            Lobibox.notify('success', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                icon: 'fa fa-check-circle',
                                msg: "Status Changed"
                            });
                            setTimeout(() => {
                                if (reload == '1') {
                                    document.location.reload(true);
                                }
                            }, 1000)
                        } else {
                            Lobibox.notify('error', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                icon: 'fa fa-check-circle',
                                msg: "Something went wrong"
                            });
                        }
                        if (statusValue == '0') {
                            $('#' + buttonId).attr('onclick', 'changeStatusNew(\'' + id + '\',\'' + newStatus + '\',\'' + status + '\',\'' + newStatusVal + '\',\'' + statusValue + '\',\'' + buttonId + '\',\'' + value + '\',\'' + reload + '\',\'' + url + '\',\'' + buttonNegText + '\',\'' + buttonPosText + '\');');
                            $('#' + buttonId).addClass('btn-danger');
                            $('#' + buttonId).removeClass('btn-success-new');
                            if (buttonNegText != "") {
                                $('#' + buttonId).val(buttonNegText);
                            } else {
                                $('#' + buttonId).val('Deactive');
                            }
                        } else if (statusValue == '1') {
                            $('#' + buttonId).attr('onclick', 'changeStatusNew(\'' + id + '\',\'' + newStatus + '\',\'' + status + '\',\'' + newStatusVal + '\',\'' + statusValue + '\',\'' + buttonId + '\',\'' + value + '\',\'' + reload + '\',\'' + url + '\',\'' + buttonNegText + '\',\'' + buttonPosText + '\');');
                            $('#' + buttonId).addClass('btn-success-new');
                            $('#' + buttonId).removeClass('btn-danger');
                            if (buttonPosText != "") {
                                $('#' + buttonId).val(buttonPosText);
                            } else {
                                $('#' + buttonId).val('Active');
                            }
                        }
                    }
                });
            }
        });
}

$(document).ready(function () {
    $('body').on('click', '.editable-price-box', function () {
        const span = $(this).find('.editable-price');
        const currentValue = span.text().trim();
        const societyId = span.data('id');
        if (span.find('.edit-input').length > 0) {
            return;
        }
        span.html(`<input type="text" maxlength="6" class="edit-input form-control onlyNumber" value="${currentValue}" />`);
        const input = span.find('.edit-input');
        $(document).on('input', '.onlyNumber', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        input.focus().on('blur keyup', function (e) {
            if (e.type === 'blur' || e.key === 'Enter') {
                const newValue = input.val();
                if (newValue !== currentValue) {
                    $.ajax({
                        url: 'controller/TrackingLimitController.php',
                        type: 'POST',
                        data: {
                            society_id: societyId,
                            per_employee_price: newValue,
                            updatePerPrice: "updatePerPrice",
                            csrf: csrf
                        },
                        success: function (response) {
                            if (response === '1') {
                                Lobibox.notify('success', {
                                    pauseDelayOnHover: true,
                                    continueDelayOnInactiveTab: false,
                                    position: 'top right',
                                    icon: 'fa fa-check-circle',
                                    msg: "Price Updated Successfully"
                                });
                                span.text(newValue);
                            } else {
                                Lobibox.notify('error', {
                                    pauseDelayOnHover: true,
                                    continueDelayOnInactiveTab: false,
                                    position: 'top right',
                                    icon: 'fa fa-times-circle',
                                    msg: "Something went wrong"
                                });
                                span.text(currentValue);
                            }
                        },
                        error: function () {
                            Lobibox.notify('error', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                icon: 'fa fa-times-circle',
                                msg: "Something went wrong"
                            });
                            span.text(currentValue);
                        }
                    });
                } else {
                    span.text(currentValue);
                }
            }
        });
    });
});


$('#publishSlabDataFrm').on('submit', function (e) {
    e.preventDefault(); // Prevent form submission

    createSwalProgressBar();

    var csrf = $('input[name="csrf"]').val();
    var city_id = $('#cId').val();
    var selectedSocieties = $('.pagePrivilege:checkbox:checked').map(function () {
        return $(this).val();
    }).get();

    if (selectedSocieties.length === 0) {
        swal({
            title: "Error",
            text: "No societies selected!",
            icon: "error",
            timer: 3000,
        });
        return;
    }

    processRequests(selectedSocieties, function (society_id, callback) {
        var isposted = $('#val_' + society_id).val();
        var year = $('#year').val();

        if (isposted === "1") {
            $.ajax({
                url: "controller/buildingController.php",
                type: "POST",
                data: {
                    society_id_post: society_id,
                    city_id: city_id,
                    year: year,
                    syncSlabData: "syncSlabData",
                    csrf: csrf
                },
                beforeSend: function () {
                    $('#result_' + society_id).html('<i class="fa fa-spinner fa-2x text-secondary"></i>');
                },
                success: function (response) {
                    var result = response.split(':');
                    if (result[1] === "201") {
                        $('#val_' + society_id).val('0');
                        $('#result_' + society_id).html('<i class="text-danger">' + result[0] + '</i>');
                        callback(false);
                    } else {
                        $('#val_' + society_id).val('1');
                        $('#result_' + society_id).html('<i class="text-success">' + result[0] + '</i>');
                        callback(true);
                    }
                },
                error: function () {
                    $('#result_' + society_id).html('<i class="text-danger">Error processing request.</i>');
                    callback(false);
                }
            });
        } else {
            callback(false); // Skip if not posted
        }
    });
});
$(document).ready(function () {
    $('[id="summernoteEditor"]').each(function () {
        var editorId = $(this).attr('id');
        var index = editorId.replace('summernoteEditor', '');
        $('#' + editorId).summernote({
            height: 200,
            tabsize: 2,
            tooltip: false,
            callbacks: {
                onChange: function (contents, $editable) {
                    $('#template_text_error').val(contents);
                }
            }
        });
        var contentToCopy = $('#' + editorId).html();
        $('#whats_new_updates_' + index).val(contentToCopy);
    });
});
$(document).ready(function () {
    window.dynamicRules = {};
    window.dynamicMessages = {};
    $('[id^="whats_new_updates_"]').each(function () {
        var fieldId = $(this).attr('id');
        window.dynamicRules[fieldId] = {
            required: true,
            noSpace: true,
            checkspace: true,
        };
        window.dynamicMessages[fieldId] = {
            required: "Content cannot be empty.",
        };
    });
});
$(document).ready(function () {
    $('input[name=emailType]').change(function () {
        var value = $('input[name=emailType]:checked').val();
        if (value == "1") {
            $(".pay_div").removeClass("d-none");
        } else {
            $(".pay_div").addClass("d-none");
        }
    });
});

//changes--dhruvi


$(document).ready(function () {
    window.dynamicRules = {};
    window.dynamicMessages = {};
    $('[id^="user_training_status"], [id^="reasonBox_"]').each(function () {
        var fieldName = $(this).attr('name');

        if (!window.dynamicRules[fieldName]) {
            window.dynamicRules[fieldName] = {
                required: true
            };
            window.dynamicMessages[fieldName] = {
                required: "required"
            };
        }
    });

    $.each(window.dynamicRules, function (field, rules) {
        $('#attendanceForm').validate().settings.rules[field] = rules;
    });

    $.each(window.dynamicMessages, function (field, messages) {
        $('#attendanceForm').validate().settings.messages[field] = messages;
    });

    $('#attendanceForm').validate({
        errorPlacement: function (error, element) {
            error.insertAfter(element.closest('.training_status'));
        },
        submitHandler: function (form) {
            form.submit();
        }
    });

});

$(document).ready(function () {
    const $selectElement = $('#attendanceCompanySelect');
    const $changeButton = $('#changeButton');

    $selectElement.select2();
    const getSelectedValues = () => $selectElement.val() ? $selectElement.val().sort().join(',') : '';

    let initialSelection = getSelectedValues();
    $('#attendanceCompanySelect').change(function () {
        let selectedCompanies = $selectElement.val() || [];
        let currentSelection = selectedCompanies.sort().join(',');
        // console.log('Current Selection:', currentSelection);

        if (initialSelection !== currentSelection) {
            $changeButton.show();
        } else {
            $changeButton.hide();
        }
    });
});
$(document).ready(function () {
    const $selectElement = $('#attendanceCompanySelect');
    const $changeButton = $('#changeButton');

    $selectElement.select2();
    const getSelectedValues = () => $selectElement.val() ? $selectElement.val().sort().join(',') : '';
    let initialSelection = getSelectedValues();
    $('#attendanceCompanySelect').change(function () {
        let selectedCompanies = $selectElement.val() || [];
        let currentSelection = selectedCompanies.sort().join(',');
        console.log('Current Selection:', currentSelection);

        if (initialSelection !== currentSelection) {
            $changeButton.show();
        } else {
            $changeButton.hide();
        }
    });
});

// updated by om joshi 9_4  
$(document).ready(function () {
    let fullCompanyList = [];

    $('#batchCompanySelect option').each(function () {
        fullCompanyList.push({
            value: $(this).val(),
            text: $(this).text()
        });
    });

    $('#batchCompanySelect, #referenceCompanySelect').on('change', function () {
        updateMutualSelections();
        triggerCompanyAjax();
    });

    function updateMutualSelections() {
        const selectedBatchCompanies = $('#batchCompanySelect').val() || [];
        const selectedReferenceCompanies = $('#referenceCompanySelect').val() || [];

        $('#batchCompanySelect').empty();
        $('#referenceCompanySelect').empty();

        fullCompanyList.forEach(item => {
            if (!selectedReferenceCompanies.includes(item.value)) {
                $('#batchCompanySelect').append(
                    `<option value="${item.value}" ${selectedBatchCompanies.includes(item.value) ? 'selected' : ''}>${item.text}</option>`
                );
            }
        });

        fullCompanyList.forEach(item => {
            if (!selectedBatchCompanies.includes(item.value)) {
                $('#referenceCompanySelect').append(
                    `<option value="${item.value}" ${selectedReferenceCompanies.includes(item.value) ? 'selected' : ''}>${item.text}</option>`
                );
            }
        });
    }

    function triggerCompanyAjax() {
        const selectedBatchCompanies = $('#batchCompanySelect').val() || [];
        const selectedReferenceCompanies = $('#referenceCompanySelect').val() || [];

        const allowedParticipants = $("#allowed_participants").val();
        const batch_id = $("#batch_id").val();
        const slot_id = $("#slot_id").val();
        const meeting_day = $("#meeting_day").val();
        const csrf = $("input[name='csrf']").val();

        $.ajax({
            url: "ajaxGetCompanyTrainingForm.php",
            type: "POST",
            data: {
                allowed_Participants: allowedParticipants,
                batch_id: batch_id,
                slot_id: slot_id,
                meeting_day: meeting_day,
                csrf: csrf,
                company_ids: selectedBatchCompanies,
                reference_company_ids: selectedReferenceCompanies
            },
            success: function (response) {
                $('#companyUIContainer').html(response);
                applyValidation();
                $('#trainingLinkContainer').removeClass('d-none').addClass('d-flex');
                $('#trainingPasswordContainer').removeClass('d-none').addClass('d-flex');
            }
        });
    }

    function applyValidation() {
        if ($("#trainingMeetingForm").length) {
            var validator = $("#trainingMeetingForm").validate({
                errorPlacement: function (error, element) {
                    if (element.attr("type") === "radio") {
                        error.appendTo(element.closest("td").find(".radio-error"));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });

            $('input[name^="training_status"]').each(function () {
                var fieldName = $(this).attr('name');
                if (fieldName && !(fieldName in validator.settings.rules)) {
                    validator.settings.rules[fieldName] = { required: true };
                    validator.settings.messages[fieldName] = { required: "Required." };
                }
            });

            if ($('#mom_attachment').length) {
                validator.settings.rules['mom_attachment'] = { required: true };
                validator.settings.messages['mom_attachment'] = { required: "MOM attachment screenshot is required." };
            }

            validator.form();
        }
    }

    if ($("#batchCompanySelect").length > 0) {
        $('#batchCompanySelect').change();
    }
});

// add by om joshi
$(document).ready(function () {

    function initializeDatePicker() {
        $('#start_date').datepicker('destroy').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            beforeShowDay: function (date) {
                let selectedBatch = $("#batch_id").find(":selected");
                let batchType = parseInt(selectedBatch.attr("data-batch-type"), 10);

                let day = date.getDay();
                if (batchType === 0 && (day === 6 || day === 0)) {
                    return { enabled: false, classes: 'disabled-date' };
                }
                if (batchType === 1 && (day >= 1 && day <= 5)) {
                    return { enabled: false, classes: 'disabled-date' };
                }
                return { enabled: true };
            }
        });
    }

    $("#batch_id").change(function () {
        totalDaysRequired = parseInt($(this).find(":selected").attr("data-days")) || 0;
        $("#training_days_label").text(totalDaysRequired);
        $("#training_days").val(totalDaysRequired);

        let startDate = $("#start_date").val();
        if (startDate) {
            generateSchedule(startDate, totalDaysRequired);
        }

        initializeDatePicker();
    });

    $(document).ready(function () {
        initializeDatePicker();
    });

    let schedule = [];
    let removedDays = [];
    let totalDaysRequired = 0;
    function isWeekend(date) {
        let day = date.getDay();
        return day === 0 || day === 6;
    }
    function generateSchedule(startDate, trainingDays) {
        schedule = [];
        let date = new Date(startDate);
        let addedDays = 0;

        let selectedBatch = $("#batch_id").find(":selected");
        let batchType = parseInt(selectedBatch.attr("data-batch-type"), 10);
        while (addedDays < trainingDays) {
            let day = date.getDay();
            if (batchType === 9 ||
                (batchType === 1 && (day === 6 || day === 0)) ||
                (batchType === 0 && (day >= 1 && day <= 5) ||
                    (batchType === 2 && (day == 1)) ||
                    (batchType === 3 && (day == 2)) ||
                    (batchType === 4 && (day == 3)) ||
                    (batchType === 5 && (day == 4)) ||
                    (batchType === 6 && (day == 5)) ||
                    (batchType === 7 && (day == 6)) ||
                    (batchType === 8 && (day == 0))
                )) {
                schedule.push(new Date(date));
                addedDays++;
            }
            date.setDate(date.getDate() + 1);
        }

        renderSchedule();
    }

    function renderSchedule() {
        $("#scheduleCard").empty();
        schedule.forEach((date, index) => {
            $("#scheduleCard").append(`
                <div class="schedule-item card p-2 m-1 d-inline-block">
                    <span>${date.toDateString()}</span>
                    <button type="button" class="btn btn-sm btn-danger remove-day" data-index="${index}">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
        `);
        });
        console.log(schedule);
        checkSlotAvailability(schedule);
        if (schedule.length < totalDaysRequired) {
            $("#scheduleCard").append(`
                <button type="button" id="addDayBtn" class="btn btn-primary btn-sm mt-2">
                   <i class="fa fa-plus"></i> Add Day
                </button>
        `);
        }
    }
    function renderRemovedDays() {
        $("#removedDaysCard").empty();
        removedDays.forEach((date, index) => {
            $("#removedDaysCard").append(`
                <div class="removed-item card p-2 m-1 d-inline-block">
                    <span>${date.toDateString()}</span>
                    <button type="button" class="btn btn-sm btn-success add-day" data-index="${index}">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
        `);
        });
    }
    function checkSlotAvailability(selectedDate = []) {
        const batchId = $("#batch_id").val();

        if (selectedDate && batchId) {
            $.ajax({
                url: 'ajaxCheckSlotAvailability.php',
                type: 'POST',
                data: {
                    selectedDates: selectedDate,
                    batch_id: batchId
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'error') {
                        $('#scheduleCardError').html(`
                       <div class="alert alert-danger">${response.message}</div>
                    `);
                        $('button[type="submit"]').prop('disabled', true);
                    } else {
                        $('#scheduleCardError').html(``);
                        $('button[type="submit"]').prop('disabled', false);

                    }
                },
                error: function () {

                }
            });
        } else {
            slotConflict = true;
            $('#scheduleCardError').empty();
            $('button[type="submit"]').prop('disabled', true);
        }
    }


    $(document).on("click", ".remove-day", function () {
        let index = $(this).data("index");
        let removedDate = schedule.splice(index, 1)[0];
        removedDays.push(removedDate);
        renderSchedule();
        renderRemovedDays();
    });
    $(document).on("click", "#addDayBtn", function () {
        $("#datePickerModal").modal("show");
        $("#customDatePicker").datepicker("update");
    });
    $("#confirmAddDate").click(function () {
        let selectedDate = $("#customDatePicker").val();
        if (selectedDate) {
            let newDate = new Date(selectedDate);
            if (schedule.length >= totalDaysRequired) {
                Swal.fire("Limit Reached", `You can only have ${totalDaysRequired} training days.`, "warning");
                return;
            }
            if (!schedule.some(d => d.getTime() === newDate.getTime())) {
                schedule.push(newDate);
                schedule.sort((a, b) => a - b);
                renderSchedule();
                $("#datePickerModal").modal("hide");
            }
        }
    });
    $("#batch_id").change(function () {
        let selectedBatch = $(this).find(":selected");
        totalDaysRequired = parseInt(selectedBatch.attr("data-days")) || 0;
        let batchType = "";
        if (selectedBatch.attr("data-batch-type") == "0") {
            batchType = "MON To FRI";
        } else if (selectedBatch.attr("data-batch-type") == "1") {
            batchType = "SAT & SUN";
        } else if (selectedBatch.attr("data-batch-type") == "2") {
            batchType = "Monday";
        } else if (selectedBatch.attr("data-batch-type") == "3") {
            batchType = "Tuesday";
        } else if (selectedBatch.attr("data-batch-type") == "4") {
            batchType = "Wednesday";
        } else if (selectedBatch.attr("data-batch-type") == "5") {
            batchType = "Thursday";
        } else if (selectedBatch.attr("data-batch-type") == "6") {
            batchType = "Friday";
        } else if (selectedBatch.attr("data-batch-type") == "7") {
            batchType = "Saturday";
        } else if (selectedBatch.attr("data-batch-type") == "8") {
            batchType = "Sunday";
        } else if (selectedBatch.attr("data-batch-type") == "9") {
            batchType = "Any Day";
        }
        let allowedParticipants = selectedBatch.attr("data-allowed-participants");

        $("#allowed_participants_label").text(allowedParticipants);
        $("#training_days_label").text(totalDaysRequired);
        $("#training_days").val(totalDaysRequired);
        $("#batch_type_label").text(batchType);
        $("#batch_type").val(batchType);

        let startDate = $("#start_date").val();
        schedule = [];
        removedDays = [];
        $("#scheduleCard").empty();
        $("#removedDaysCard").empty();
        if (startDate) {
            generateSchedule(startDate, totalDaysRequired);
        }
    });


    $('#start_date').datepicker('destroy').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        beforeShowDay: function (date) {
            let selectedBatch = $("#batch_id").find(":selected");
            let batchType = parseInt(selectedBatch.attr("data-batch-type"), 10);

            let day = date.getDay();
            if (batchType === 9) {
                return { enabled: true };
            }
            if (batchType === 0 && (day === 6 || day === 0)) {
                return { enabled: false, classes: 'disabled-date' };
            }
            if (batchType === 1 && (day >= 1 && day <= 5)) {
                return { enabled: false, classes: 'disabled-date' };
            }

            return { enabled: true };
        }
    });

    $("#start_date").change(function () {
        let startDate = $(this).val();
        if (startDate && totalDaysRequired) {
            generateSchedule(startDate, totalDaysRequired);
            $("#removedDaysCard").empty();
            removedDays = [];
        }
    });

    $("#customDatePicker").datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        beforeShowDay: function (date) {
            let selectedBatch = $("#batch_id").find(":selected");

            if (!selectedBatch.length) return { enabled: false, classes: 'disabled-date' };

            let batchType = parseInt(selectedBatch.attr("data-batch-type"), 10);
            let day = date.getDay();

            let isValidDay = (batchType === 9) ||
                (batchType === 1 && (day === 6 || day === 0)) ||
                (batchType === 0 && (day >= 1 && day <= 5));

            if (!isValidDay) {
                return { enabled: false, classes: 'disabled-date' };
            }

            let dateString = date.toISOString().split("T")[0];
            let isAlreadyScheduled = schedule.some(d => d.toISOString().split("T")[0] === dateString);

            if (isAlreadyScheduled) {
                return { enabled: false, classes: 'disabled-date text-muted' };
            }

            return { enabled: true };
        }
    });
    $(document).on("click", "#addDayBtn", function () {
        $("#datePickerModal").modal("show");
        $("#datePickerModal").on("shown.bs.modal", function () {
            $("#customDatePicker").datepicker("update");
        });
    });

    $("#confirmAddDate").click(function () {
        let selectedDate = $("#customDatePicker").val();
        if (selectedDate) {
            let newDate = new Date(selectedDate);
            if (schedule.length >= totalDaysRequired) {
                // Swal.fire("Limit Reached", `You can only have ${totalDaysRequired} training days.`, "warning");
                return;
            }
            if (!schedule.some(d => d.getTime() === newDate.getTime())) {
                schedule.push(newDate);
                schedule.sort((a, b) => a - b);
                renderSchedule();
                $("#datePickerModal").modal("hide");
            }
        }
    });

    $(document).on("click", ".add-day", function () {
        if (schedule.length >= totalDaysRequired) {
            Swal.fire("Training Schedule is Full", "You cannot add more training days.", "warning");
            return;
        }
        let index = $(this).data("index");
        let restoredDate = removedDays.splice(index, 1)[0];
        if (!schedule.some(date => date.getTime() === restoredDate.getTime())) {
            schedule.push(restoredDate);
        }
        schedule.sort((a, b) => a - b);
        renderSchedule();
        renderRemovedDays();
    });

    $(document).on("change", "input[name='meeting_from_time[]'], input[name='meeting_to_time[]']", function () {
        let row = $(this).closest(".row");
        let fromTimeInput = row.find("input[name='meeting_from_time[]']");
        let toTimeInput = row.find("input[name='meeting_to_time[]']");
        let fromTime = fromTimeInput.val();
        let toTime = toTimeInput.val();
        let now = new Date();
        let currentHours = now.getHours();
        let currentMinutes = now.getMinutes();
        let currentTime = `${currentHours.toString().padStart(2, '0')}:${currentMinutes.toString().padStart(2, '0')}`;
        if (fromTime && toTime) {
            let fromHour = parseInt(fromTime.split(":")[0], 10);
            let fromMinute = parseInt(fromTime.split(":")[1], 10);
            let toHour = parseInt(toTime.split(":")[0], 10);
            let toMinute = parseInt(toTime.split(":")[1], 10);
            if (toHour < fromHour || (toHour === fromHour && toMinute <= fromMinute)) {
                Swal.fire({
                    icon: "error",
                    title: "Invalid Time Selection",
                    text: "End time must be later than the start time.",
                });
                toTimeInput.val("");
                return;
            }
        }
    });

    let validator = $("#batchSlotForm").validate({
        errorPlacement: function (error, element) {
            if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('.select2-container'));
            } else if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            batch_id: {
                required: true
            },
            city: {
                required: false,
                minlength: 2,
                maxlength: 50,
                cityPattern: true
            },
            start_date: {
                required: true
            }
        },
        messages: {
            batch_id: {
                required: "Please select a batch."
            },
            city: {
                minlength: "City must be at least 2 characters long.",
                maxlength: "City must not exceed 50 characters.",
                cityPattern: "City must start with a capital letter."
            },
            start_date: {
                required: "Please select a date."
            }
        },
        submitHandler: function (form) {
            let startDate = $("#start_date").val();
            let trainingDaysCount = schedule.length;
            let selectedDates = schedule.map(date => date.toISOString().split("T")[0]);

            $("#selected_dates").val(JSON.stringify(selectedDates));

            if (!startDate) return;

            if (trainingDaysCount < totalDaysRequired) {
                Swal.fire({
                    title: "Incomplete Training Days",
                    text: `You are submitting with only ${trainingDaysCount} training days instead of ${totalDaysRequired}.`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Proceed Anyway",
                    cancelButtonText: "Cancel",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(':input[type="submit"]').prop('disabled', true);
                        $(".ajax-loader").show();
                        form.submit();
                    }
                });
            } else {
                $(':input[type="submit"]').prop('disabled', true);
                $(".ajax-loader").show();
                form.submit();
            }
        }
    });

    $('[id^="meeting_trainer_id_"]').each(function () {
        let fieldName = $(this).attr('name');
        if (fieldName && !(fieldName in validator.settings.rules)) {
            validator.settings.rules[fieldName] = { required: true };
            validator.settings.messages[fieldName] = { required: "Trainer selection is required." };
        }
    });

    $("#start_date").on("change", function () {
        $(this).removeClass("is-invalid");
        $(this).next("label.error").remove();
        $(this).css("color", "black");
    });
});
// om code end

$(document).ready(function () {
    function toggleRemarkField() {
        var trainingType = $("#training_type").val();
        var remarkField = $("#remarkField");

        if (trainingType === "0") {
            remarkField.removeClass("d-none");
        } else {
            remarkField.addClass("d-none");
        }
    }
    $("#training_type").change(function () {
        toggleRemarkField();

    });
});


$("#submitMeeting").click(function (e) {
    e.preventDefault();
    let scheduleNextMeeting = "";
    if ($("#scheduleNextMeeting").length > 0) {
        scheduleNextMeeting = $("#scheduleNextMeeting").val();
    }
    let is_multiple = $("#is_multiple").val();
    if (is_multiple == '1') {
        reschedule_meeting_companies = $("#reschedule_meeting_companies").val();
    }
    let trainingScheduleId = $("#training_schedule_master_id").val();
    let sessionId = $("#session_id").val();
    let trainingDate = $("#training_date").val();
    let societyId = $("#society_id").val();
    let bms_admin_id = $("#bms_admin_id").val();
    let sessionDayId = $("#session_day_id").val();
    let startTime = $("#start_time").val();
    let endTime = $("#end_time").val();
    let sessionName = $("#session_name").val();
    let trainingDay = $("#training_day").val();

    let meetingName = `${sessionName}_${trainingDay}_${trainingDate}`;
    if (!meetingName || !trainingDate || !sessionId || (!societyId && is_multiple == '0')) {
        alert("Please ensure all fields are filled!");
        return;
    }

    if (scheduleNextMeeting == "scheduleNextMeeting") {
        if (is_multiple == '1') {
            let selected_meeting_text = $("input[name='selected_meeting']:checked").closest("label").text().trim();
            if (!selected_meeting_text || selected_meeting_text == "") {
                alert("Please select a meeting from the available list, or add one from the Manage Setup page.");
                return;
            }
            reschedule_meeting_companies.forEach(societyId => {
                let selected_meeting_val = $("input[name='selected_meeting']:checked").data("id");
                let selected_meeting_new_val = $("input[name='selected_meeting']:checked").val();
                if (selected_meeting_new_val == "new_meeting") {
                    $("input[name='next_meeting_id_[" + societyId + "]']").val("new_meeting");
                } else {
                    $("input[name='next_meeting_id_[" + societyId + "]']").val(selected_meeting_val);
                }
                $("input[name='next_meeting_session_date_[" + societyId + "]']").val(trainingDate);
                $("input[name='next_meeting_session_id_[" + societyId + "]']").val(sessionId);
                $("#next_meeting_view_society_id_" + societyId + "").html("Next Meeting : ");

                $("#next_meeting_view_session_id_" + societyId + "").html(selected_meeting_text);
            });
        } else {
            let selected_meeting_text = $("input[name='selected_meeting']:checked").closest("label").text().trim();
            if (!selected_meeting_text || selected_meeting_text == "") {
                alert("Please select a meeting from the available list, or add one from the Manage Setup page.");
                return;
            }
            let selected_meeting_val = $("input[name='selected_meeting']:checked").data("id");
            let selected_meeting_new_val = $("input[name='selected_meeting']:checked").val();
            if (selected_meeting_new_val == "new_meeting") {
                $("input[name='next_meeting_id_[" + societyId + "]']").val("new_meeting");
            } else {
                $("input[name='next_meeting_id_[" + societyId + "]']").val(selected_meeting_val);
            }
            $("input[name='next_meeting_session_date_[" + societyId + "]']").val(trainingDate);
            $("input[name='next_meeting_session_id_[" + societyId + "]']").val(sessionId);
            $("#next_meeting_view_society_id_" + societyId + "").html("Next Meeting : ");
            $("#next_meeting_view_session_id_" + societyId + "").html(selected_meeting_text);
        }

        $("#scheduleMeeting").modal("hide");

    } else {
        let actionType = trainingScheduleId ? "updateMeeting" : "insertMeeting";
        let requestData = {
            action: actionType,
            session_id: sessionId,
            trainingDate: trainingDate,
            society_id: societyId,
            bms_admin_id: bms_admin_id,
            meeting_name: meetingName,
            session_day_id: sessionDayId,
            start_time: startTime,
            end_time: endTime,
            csrf: csrf
        };
        if (trainingScheduleId) {
            requestData.training_schedule_master_id = trainingScheduleId;
        } else {
            requestData.training_schedule_master_id = 0;
        }
        $.ajax({
            url: "controller/trainingController.php",
            type: "POST",
            data: requestData,
            success: function () {
                window.location.href = '../apAdmin/companyOnboarding';
            },
            error: function () {
            }
        });
    }


});

function filterSessions(selected = "") {
    const session_day_id = $("#session_day_id").val();
    let selectedStr = "";
    if (selected) {
        if (Array.isArray(selected)) {
            selectedStr = selected.join(',');
        } else if (typeof selected === 'string') {
            selectedStr = selected;
        } else {
            selectedStr = String(selected);
        }
    }
    const selectedIds = selectedStr ? selectedStr.split(',') : [];
    $.ajax({
        url: './getSessionName.php',
        method: 'POST',
        data: { session_day_id: session_day_id, csrf: csrf },
        dataType: 'json',
        success: function (response) {
            const sessionSelect = $("#session_id");
            sessionSelect.empty();
            if (response.length > 0) {
                response.forEach(function (session) {
                    const isSelected = selectedIds.includes(session.session_id.toString());
                    sessionSelect.append('<option value="' + session.session_id + '"' +
                        (isSelected ? ' selected' : '') + '>' +
                        session.session_name + '</option>');
                    // sessionSelect.append('<option value="' + session.session_id + '">' + session.session_name + '</option>');
                });
            } else {
                sessionSelect.append('<option value="">No sessions available</option>');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching Sessions: " + error);
        }
    });
}
function loadTopicsForModuleType(moduleType) {
    const topicSelect = document.getElementById('topic_id');
    if (!topicSelect) return;

    // Clear existing options except the first one
    while (topicSelect.options.length > 1) {
        topicSelect.remove(1);
    }

    // Determine topic_type: 0 = HRMS, 1 = CRM
    const topicType = (moduleType === '2') ? 1 : 0;

    $.ajax({
        url: 'controller/trainingController.php',
        type: 'POST',
        data: {
            action: 'getTopicsWithMeta',
            module_type: moduleType,
            topic_type: topicType
        },
        dataType: 'json',
        success: function (response) {
            if (response && response.success && response.data) {
                response.data.forEach(function (topic) {
                    const option = document.createElement('option');
                    option.value = topic.topic_id;
                    option.textContent = topic.topic_name;
                    topicSelect.appendChild(option);
                });
                // Trigger change to update Select2 if it's initialized
                if ($(topicSelect).hasClass('single-select')) {
                    $(topicSelect).trigger('change');
                }
            }
        },
        error: function () {
            console.error('Error loading topics');
        }
    });
}

function toggleSessionField() {
    const moduleType = document.getElementById('module_type').value;
    const sessionField = document.getElementById('sessionField');
    const sessionDayField = document.getElementById('session_day');
    const topicField = document.getElementById('topicField');
    const completionDaysField = document.getElementById('completionDaysField');
    const completionDaysInput = document.getElementById('completion_days');

    const topic_id = document.getElementById('topic_id');
    const session_id = document.getElementById('session_id');
    const session_day_id = document.getElementById('session_day_id');

    if (moduleType === '0') { // Setup (HRMS)
        if (sessionField) sessionField.style.display = 'block';
        if (sessionDayField) sessionDayField.style.display = 'block';
        if (topicField) topicField.style.display = 'none';
        if (completionDaysField) completionDaysField.style.display = 'none';
        if (completionDaysInput) completionDaysInput.value = '';

        if (topic_id) topic_id.required = false;
        if (session_id) session_id.required = true;
        if (session_day_id) session_day_id.required = true;

    } else if (moduleType === '1' || moduleType === '2') { // Training (HRMS) or CRM
        if (sessionField) sessionField.style.display = 'none';
        if (sessionDayField) sessionDayField.style.display = 'none';
        if (topicField) topicField.style.display = 'block';
        if (completionDaysField) completionDaysField.style.display = 'block';

        if (topic_id) topic_id.required = true;
        if (session_id) session_id.required = false;
        if (session_day_id) session_day_id.required = false;

        // Reload topics based on module type (HRMS topics for type 1, CRM topics for type 2)
        if (topic_id) {
            loadTopicsForModuleType(moduleType);
        }

    } else { // None
        if (sessionField) sessionField.style.display = 'none';
        if (sessionDayField) sessionDayField.style.display = 'none';
        if (topicField) topicField.style.display = 'none';
        if (completionDaysField) completionDaysField.style.display = 'none';

        if (topic_id) topic_id.required = false;
        if (session_id) session_id.required = false;
        if (session_day_id) session_day_id.required = false;
    }
}

function editSessionModulesForm(id, modulename, session, moduletype, priority, session_day_id, completion_days, module_url, estimated_minutes, topic_id) {
    $("#scheduleModule").val("scheduleModuleEdit");
    $("#module_name").val(modulename);
    $("#module_type").val(moduletype).trigger("change");
    $("#priority_id").val(priority).trigger("change");
    $("#session_day_id").val(session_day_id).trigger("change");
    filterSessions(session);
    $("#completion_days").val(completion_days);
    if (typeof module_url != 'undefined') {
        $("#module_url").val(module_url);
    }
    if (typeof estimated_minutes != 'undefined') {
        $("#estimated_minutes").val(estimated_minutes);
    }
    if (typeof topic_id != 'undefined' && topic_id != '') {
        $("#topic_id").val(topic_id).trigger("change");
    }
    $("#editId").val(id);
    $("#submitButton").text("UPDATE");
    $(".modal-title").text("Update Module");
    $("#addModule").modal("show");
}

function editSessionForm(id, session_name, session_days, start_time, end_time, session_day_id) {
    $("#sessionModule").val("sessionModuleEdit");
    $("#session").text("UPDATE SESSION");
    $("#session_name").val(session_name);
    $("#session_days").val(session_days).trigger("change");
    $("#session_day_id").val(session_day_id).trigger("change");
    $("#start_time").val(start_time);
    $("#end_time").val(end_time);
    $("#editId").val(id);
    $("#submitButton").text("UPDATE");
    $("#addSession").modal("show");
}

function addSessionForm() {
    $("#session").text("ADD SESSION");
    $("#sessionModule").val("sessionModule");
    $("#session_name").val(null);
    $("#session_days").val(null);
    $("#start_time").val(null);
    $("#end_time").val(null);
    $("#session_day_id").val(null);

    $("#editId").val("");
    $("#submitButton").text("ADD");
}
$(document).ready(function () {
    if ($('#module_type').length > 0) {
        toggleSessionField();
    }
    // Delegated handler for edit buttons without inline onclick
    $(document).on('click', '.btn-edit-module', function (e) {
        e.preventDefault();
        const el = $(this);
        editSessionModulesForm(
            el.data('id') || '',
            el.data('name') || '',
            el.data('session') || '',
            el.data('type') || '',
            el.data('priority') || '',
            el.data('session-day') || '',
            el.data('completion-days') || '',
            el.data('url') || '',
            el.data('estimated-minutes') || '',
            el.data('topic-id') || ''
        );
    });

    (function AddBatchTopicGrouping() {
        const participantSelect = document.getElementById('participant_name');
        const topicContainer = document.getElementById('topicGroupsContainer');
        const daysInput = document.getElementById('training_days');
        const daysContainer = document.getElementById('trainingDaysContainer');
        const assignmentHidden = document.getElementById('dayTopicAssignments');
        const modulesAssignmentHidden = document.getElementById('dayModulesAssignments');

        if (!participantSelect || !topicContainer || !daysInput || !daysContainer || !assignmentHidden) {
            return;
        }

        let topics = [];
        let dayToTopicIds = {};
        let dayToModuleIds = {};

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }


        function renderTopicsList() {
            topicContainer.innerHTML = '';

            if (!Array.isArray(topics) || topics.length === 0) {
                topicContainer.innerHTML = '<div class="text-muted">No topic groups found for selected participant</div>';
                return;
            }

            topics.forEach(t => {
                const card = document.createElement('div');
                card.className = 'training-day-card mb-2';

                const header = document.createElement('div');
                header.className = 'd-flex justify-content-between align-items-center px-0';
                header.innerHTML = `
          <span><strong>Topic:</strong> ${escapeHtml(t.topic_name || '')}</span>
          <select class="form-control form-control-sm" style="width:160px" data-topic-id="${t.topic_id}">
            <option value="">Assign to day...</option>
          </select>
        `;

                const body = document.createElement('div');
                body.className = 'mt-2';

                const moduleNames = (t.modules || []).map(m => escapeHtml(m.training_module_name)).join(', ');
                const moduleIds = (t.modules || []).map(m => m.training_module_id);

                body.innerHTML = `
          <div><strong>Modules:</strong> ${moduleNames || '<span class="text-muted">No modules found</span>'}</div>
          <div class="hidden-inputs" data-topic-id="${t.topic_id}"></div>
        `;

                card.appendChild(header);
                card.appendChild(body);
                topicContainer.appendChild(card);
            });

            refreshDaySelectOptions();

            topicContainer.querySelectorAll('select[data-topic-id]').forEach(sel => {
                sel.addEventListener('change', function () {
                    const topicId = this.getAttribute('data-topic-id');
                    const selectedDay = this.value;
                    const topic = topics.find(t => t.topic_id == topicId);
                    const hiddenContainer = topicContainer.querySelector(`.hidden-inputs[data-topic-id="${topicId}"]`);
                    hiddenContainer.innerHTML = '';
                    if (selectedDay && topic && topic.modules) {
                        topic.modules.forEach(m => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = `day${selectedDay}[]`;
                            input.value = m.training_module_id;
                            hiddenContainer.appendChild(input);
                        });
                    }
                });
                sel.addEventListener('change', function (e) { onAssignDayChange(e); });
            });

            if (document.getElementById('edit_batch_id')) {
                autoAssignTopicsFromExisting();
            }
        }


        function refreshDaySelectOptions() {
            const days = parseInt(daysInput.value || '0', 10) || 0;
            topicContainer.querySelectorAll('select[data-topic-id]').forEach(sel => {
                const current = sel.value;
                sel.innerHTML = '<option value="">Assign to day...</option>';
                for (let d = 1; d <= days; d++) {
                    sel.insertAdjacentHTML('beforeend', '<option value="' + d + '">Day ' + d + '</option>');
                }
                if (current) sel.value = current;
            });
            renderDaysBuckets();
            syncHiddenAssignments();
        }

        function getSelectedModulesForDay(day) {
            const sel = findDayModuleSelect(day);
            if (!sel) return [];
            const vals = sel.val();
            if (Array.isArray(vals)) return vals.map(v => String(v));
            if (typeof vals === 'string' && vals.length) return [vals];
            return [];
        }

        function autoAssignTopicsFromExisting() {
            const days = parseInt(daysInput.value || '0', 10) || 0;
            if (days <= 0 || topics.length === 0) return;
            const moduleIdToDay = {};
            for (let d = 1; d <= days; d++) {
                const mods = getSelectedModulesForDay(d);
                mods.forEach(mid => { moduleIdToDay[String(mid)] = d; });
            }
            topicContainer.querySelectorAll('select[data-topic-id]').forEach(sel => {
                const topicId = parseInt(sel.getAttribute('data-topic-id'), 10);
                const t = topics.find(x => x.topic_id === topicId);
                if (!t || !Array.isArray(t.modules)) return;
                let assignedDay = null;
                for (let i = 0; i < t.modules.length; i++) {
                    const mid = String(t.modules[i].training_module_id);
                    if (moduleIdToDay[mid]) { assignedDay = moduleIdToDay[mid]; break; }
                }
                if (assignedDay) {
                    sel.value = String(assignedDay);
                    const evt = new Event('change', { bubbles: true });
                    sel.dispatchEvent(evt);
                }
            });
            recomputeAllDayModules();
            syncAllDayModuleSelects();
            syncHiddenAssignments();
        }

        function collectModuleIdsForTopic(topicId) {
            const t = topics.find(x => x.topic_id === topicId);
            if (!t || !Array.isArray(t.modules)) return [];
            return t.modules.map(m => m.training_module_id).filter(Boolean);
        }

        function findDayModuleSelect(day) {
            if (typeof $ === 'undefined') return null;
            // Prefer name-based selects used by backend: name="day{n}[]"
            let sel = $("select[name='day" + day + "[]']");
            if (!sel || sel.length === 0) {
                // Fallback id pattern if present
                sel = $('#day_' + day + '_modules');
            }
            if (!sel || sel.length === 0) {
                // Another common pattern: id="day{n}"
                sel = $('#day' + day);
            }
            return (sel && sel.length > 0) ? sel : null;
        }

        function recomputeAllDayModules() {
            // Reset and recompute modules for all days based on current topic assignments
            const newMap = {};
            Object.keys(dayToTopicIds).forEach(k => {
                const day = parseInt(k, 10);
                const topicIds = dayToTopicIds[k] || [];
                const allModules = [];
                topicIds.forEach(tid => { allModules.push(...collectModuleIdsForTopic(tid)); });
                newMap[day] = Array.from(new Set(allModules));
            });
            dayToModuleIds = newMap;
        }

        function syncAllDayModuleSelects() {
            const days = parseInt(daysInput.value || '0', 10) || 0;
            for (let d = 1; d <= days; d++) {
                const sel = findDayModuleSelect(d);
                if (!sel) continue;
                const mods = (dayToModuleIds[d] || []).map(String);
                sel.val(mods).trigger('change');
                try { sel.trigger('change.select2'); } catch (e) { }
            }
        }

        function onAssignDayChange(e) {
            const sel = e.target;
            const topicId = parseInt(sel.getAttribute('data-topic-id'), 10);
            const newDay = sel.value ? parseInt(sel.value, 10) : null;
            Object.keys(dayToTopicIds).forEach(k => {
                dayToTopicIds[k] = (dayToTopicIds[k] || []).filter(id => id !== topicId);
            });
            if (newDay) {
                dayToTopicIds[newDay] = dayToTopicIds[newDay] || [];
                dayToTopicIds[newDay].push(topicId);
            }
            recomputeAllDayModules();
            refreshDaySelectOptions();
            syncAllDayModuleSelects();
        }

        function renderDaysBuckets() {
            // Do not render lists; ensure the real day selects (day1[], day2[], ...) reflect assignments
            daysContainer.innerHTML = '';
            syncAllDayModuleSelects();
        }

        function syncHiddenAssignments() {
            const payload = [];
            Object.keys(dayToTopicIds).forEach(k => {
                (dayToTopicIds[k] || []).forEach(id => payload.push({ day: parseInt(k, 10), topic_id: id }));
            });
            assignmentHidden.value = JSON.stringify(payload);
            const modPayload = [];
            Object.keys(dayToModuleIds).forEach(k => {
                (dayToModuleIds[k] || []).forEach(mid => modPayload.push({ day: parseInt(k, 10), module_id: mid }));
            });
            modulesAssignmentHidden.value = JSON.stringify(modPayload);
        }

        function loadTopicsForParticipant(pid) {
            topics = [];
            topicContainer.innerHTML = '<div class="text-muted">Loading...</div>';
            $.post('controller/trainingController.php', { action: 'getTopicsWithModulesByParticipant', participant_type: pid }, function (resp) {
                try {
                    const r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                    if (r.success) {
                        topics = Array.isArray(r.data) ? r.data : [];
                    } else {
                        topics = [];
                    }
                } catch (e) { topics = []; }
                const maxDays = Math.max(0, topics.length);
                if (daysInput) {
                    daysInput.setAttribute('max', String(maxDays));
                    const currentVal = parseInt(daysInput.value || '0', 10) || 0;
                    if (currentVal > maxDays) {
                        daysInput.value = String(maxDays);
                    }
                }
                renderTopicsList();
                refreshDaySelectOptions();
                syncAllDayModuleSelects();
            }).fail(function () {
                topicContainer.innerHTML = '<div class="text-danger">Failed to load topic groups</div>';
            });
        }

        function onParticipantChange(val) {
            const pid = parseInt(val, 10);
            dayToTopicIds = {};
            dayToModuleIds = {};
            topicContainer.innerHTML = '';
            renderDaysBuckets();
            syncHiddenAssignments();
            if (pid > 0) {
                loadTopicsForParticipant(pid);
            }
        }

        participantSelect.addEventListener('change', function () {
            onParticipantChange(this.value);
        });

        if (typeof $ !== 'undefined') {
            $(document).on('change', '#participant_name', function () {
                onParticipantChange(this.value);
            });
            // Select2 specific event if used
            $(document).on('select2:select', '#participant_name', function (e) {
                onParticipantChange(e.params && e.params.data ? e.params.data.id : this.value);
            });
        }

        daysInput.addEventListener('input', function () {
            refreshDaySelectOptions();
            syncAllDayModuleSelects();
        });

        if (participantSelect.value) {
            const pid = parseInt(participantSelect.value, 10);
            if (pid > 0) loadTopicsForParticipant(pid);
        }
    })();
    // Delegated handler for copy URL buttons
    $(document).on('click', '.btn-copy-url', function () {
        const url = $(this).data('url');
        if (!url) { return; }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(function () {
                if (typeof Lobibox !== 'undefined' && Lobibox.notify) {
                    Lobibox.notify('success', {
                        pauseDelayOnHover: true,
                        size: 'mini',
                        position: 'top right',
                        msg: 'URL copied to clipboard'
                    });
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Copied', text: 'URL copied to clipboard', timer: 1200, showConfirmButton: false });
                }
            }).catch(function () { });
        } else {
            const temp = document.createElement('input');
            temp.type = 'text';
            temp.value = url;
            document.body.appendChild(temp);
            temp.select();
            temp.setSelectionRange(0, 99999);
            try { document.execCommand('copy'); } catch (e) { }
            document.body.removeChild(temp);
            if (typeof Lobibox !== 'undefined' && Lobibox.notify) {
                Lobibox.notify('success', { pauseDelayOnHover: true, size: 'mini', msg: 'URL copied to clipboard' });
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'success', title: 'Copied', text: 'URL copied to clipboard', timer: 1200, showConfirmButton: false });
            }
        }
    });

    // Delegated handler for Manage Subtopics button
    $(document).on('click', '.btn-manage-subtopics', function (e) {
        e.preventDefault();
        const moduleId = $(this).data('module-id');
        const moduleName = $(this).data('module-name');
        if (typeof manageSubtopics === 'function') {
            manageSubtopics(moduleId, moduleName);
        }
    });
});

let fullCompanyList = [];

$(document).ready(function () {
    $('#society_id option').each(function () {
        fullCompanyList.push({
            value: $(this).val(),
            text: $(this).text()
        });
    });

    updateMutualSelections();
    $('#society_id, #reference_society_id').on('change', function () {
        updateMutualSelections();
    });
});

function updateMutualSelections() {
    const selectedCompany = $('#society_id').val() || [];
    const selectedReference = $('#reference_society_id').val() || [];
    $('#society_id').empty();
    $('#reference_society_id').empty();

    fullCompanyList.forEach(item => {
        if (!selectedReference.includes(item.value)) {
            $('#society_id').append(`<option value="${item.value}" ${selectedCompany.includes(item.value) ? 'selected' : ''}>${item.text}</option>`);
        }
    });
    fullCompanyList.forEach(item => {
        if (!selectedCompany.includes(item.value)) {
            $('#reference_society_id').append(`<option value="${item.value}" ${selectedReference.includes(item.value) ? 'selected' : ''}>${item.text}</option>`);
        }
    });
}
$(document).ready(function () {
    $(document).on('keydown', '.onlyNumber', function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) &&
            (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
});


function reinitializeSelect2(container) {
    container.find('.single-select').each(function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            $(this).select2();
        }
    });

    container.find('.multiple-select').each(function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            $(this).select2({
                multiple: true
            });
        }
    });
}

$(document).ready(function () {
    function decodeHtmlEntities(text) {
        var txt = document.createElement("textarea");
        txt.innerHTML = text;
        return txt.value;
    }
    function cleanExportData(data) {
        return decodeHtmlEntities(
            data
                // Remove <span style="display:none;">...</span>
                .replace(/<span[^>]*style=['"]?display\s*:\s*none;?['"]?[^>]*>.*?<\/span>/gi, '')
                // Replace <br> with newline
                .replace(/<br\s*\/?>/gi, '\n')
                // Remove all other HTML tags
                .replace(/<[^>]*>?/gm, '')
                .trim()
        );
    }
    var table1 = $('#reportTableNew').DataTable({
        stateSave: true,
        lengthChange: true,
        columnDefs: [{ "orderable": true, "targets": 0 }],
        order: [[1, 'asc']],
        "bPaginate": false,
        "bLengthChange": false,
        "bFilter": true,
        "bInfo": false,
        "bAutoWidth": false,
        "dom": 'Blfrtip',
        "buttons": [
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data, row, column, node) {
                            return cleanExportData(data);
                        }
                    }
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data, row, column, node) {
                            return cleanExportData(data);
                        }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data, row, column, node) {
                            return cleanExportData(data);
                        }
                    }
                }
            },
            'colvis'
        ]
    });

});

$(document).ready(function () {
    $('body').on('click', '.editable-crm-box', function () {
        const span = $(this).find('.editable-crm');
        const currentValue = span.text().trim();
        const societyId = span.data('id');
        if (span.find('.edit-input').length > 0) {
            return;
        }
        span.html(`<input type="text" maxlength="6" class="edit-input form-control onlyNumber" value="${currentValue}" />`);
        const input = span.find('.edit-input');
        $(document).on('input', '.onlyNumber', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        input.focus().on('blur keyup', function (e) {
            if (e.type === 'blur' || e.key === 'Enter') {
                const newValue = input.val();
                if (newValue !== currentValue) {
                    $.ajax({
                        url: 'controller/TrackingLimitController.php',
                        type: 'POST',
                        data: {
                            society_id: societyId,
                            crm_limit: newValue,
                            updatecrm: "updatecrm",
                            csrf: csrf
                        },
                        success: function (response) {
                            if (response === '1') {
                                Lobibox.notify('success', {
                                    pauseDelayOnHover: true,
                                    continueDelayOnInactiveTab: false,
                                    position: 'top right',
                                    icon: 'fa fa-check-circle',
                                    msg: "CRM Limit Updated Successfully"
                                });
                                span.text(newValue);
                            } else if (response === '2') {
                                Lobibox.notify('error', {
                                    pauseDelayOnHover: true,
                                    continueDelayOnInactiveTab: false,
                                    position: 'top right',
                                    icon: 'fa fa-times-circle',
                                    msg: "CRM limit must be less or equal to employee limit."
                                });
                                span.text(currentValue);
                            } else {
                                Lobibox.notify('error', {
                                    pauseDelayOnHover: true,
                                    continueDelayOnInactiveTab: false,
                                    position: 'top right',
                                    icon: 'fa fa-times-circle',
                                    msg: "Something went wrong"
                                });
                                span.text(currentValue);
                            }
                        },
                        error: function () {
                            Lobibox.notify('error', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                icon: 'fa fa-times-circle',
                                msg: "Something went wrong"
                            });
                            span.text(currentValue);
                        }
                    });
                } else {
                    span.text(currentValue);
                }
            }
        });
    });
    $('#summernoteEditor1').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview']],
            ['mybutton', ['variablesDropdown']]
        ],
        buttons: {
            variablesDropdown: function (context) {
                var ui = $.summernote.ui;

                var buttonGroup = ui.buttonGroup([
                    ui.button({
                        className: 'dropdown-toggle',
                        contents: 'Variables <span class="caret"></span>',
                        tooltip: 'Insert Variable',
                        data: {
                            toggle: 'dropdown'
                        },
                        click: function () {
                            context.invoke('editor.saveRange');
                        }
                    }),
                    ui.dropdown({
                        className: 'dropdown-style',
                        contents: `
          <ul class="list-unstyled mb-0">
            <li><a class="dropdown-item" href="#" data-var="{Client's Name}">{Client's Name}</a></li>
            <li><a class="dropdown-item" href="#" data-var="{Contact Number}">{Contact Number}</a></li>
            <li><a class="dropdown-item" href="#" data-var="{Your Name}">{Your Name}</a></li>
            <li><a class="dropdown-item" href="#" data-var="{Implementation Manager's Name}">{Implementation Manager's Name}</a></li>
            <li><a class="dropdown-item" href="#" data-var="{Your Contact Information}">{Your Contact Information}</a></li>
            <li><a class="dropdown-item" href="#" data-var="{Payment Method}">{Payment Method}</a></li>
            <li><a class="dropdown-item" href="#" data-var="{Amount}">{Amount}</a></li>
          </ul>
        `,

                        callback: function ($dropdown) {
                            $dropdown.find('a').on('click', function (e) {
                                e.preventDefault();
                                var value = $(this).data('var');

                                context.invoke('editor.restoreRange');
                                context.invoke('editor.focus');
                                context.invoke('editor.insertText', value);
                            });
                        }

                    })
                ]);

                return buttonGroup.render();
            }
        }
    });
});


$(document).ready(function () {
    $('body').on('click', '.editable-remark-box', function () {
        const span = $(this).find('.editable-remark');
        const currentValue = span.text().trim();
        const societyId = span.data('id');

        if (span.find('.edit-input').length > 0) return;

        span.html(`<input type="text" maxlength="250" class="edit-input form-control" value="${currentValue}" />`);
        const input = span.find('.edit-input');

        input.focus().on('blur keyup', function (e) {
            if (e.type === 'blur' || e.key === 'Enter') {
                const newValue = input.val().trim();
                if (newValue !== currentValue) {
                    $.ajax({
                        url: 'controller/reportController.php',
                        type: 'POST',
                        data: {
                            society_id: societyId,
                            last_call_remark: newValue,
                            updateRemark: "updateRemark",
                            csrf: csrf
                        },
                        success: function (response) {
                            if (response === '1') {
                                Lobibox.notify('success', {
                                    pauseDelayOnHover: true,
                                    continueDelayOnInactiveTab: false,
                                    position: 'top right',
                                    icon: 'fa fa-check-circle',
                                    msg: "Remark Updated Successfully"
                                });
                                span.text(newValue);
                            } else {
                                Lobibox.notify('error', {
                                    pauseDelayOnHover: true,
                                    continueDelayOnInactiveTab: false,
                                    position: 'top right',
                                    icon: 'fa fa-times-circle',
                                    msg: "Something went wrong"
                                });
                                span.text(currentValue);
                            }
                        },
                        error: function () {
                            Lobibox.notify('error', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                icon: 'fa fa-times-circle',
                                msg: "Something went wrong"
                            });
                            span.text(currentValue);
                        }
                    });
                } else {
                    span.text(currentValue);
                }
            }
        });
    });
});


$(document).ready(function () {

    $('body').on('click', '.editable-date-box', function () {
        const span = $(this).find('.editable-date');
        const currentValue = span.text().trim();
        const societyId = span.data('id');

        if (span.find('.edit-date-input').length > 0) return;

        span.html(`<input type="text" class="edit-date-input form-control autoclose-datepicker-date" value="${currentValue}" autocomplete="off" />`);
        const input = span.find('.edit-date-input');

        input.datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd'
        }).datepicker('show').on('changeDate', function () {
            const newValue = input.val();
            if (newValue !== currentValue) {
                updateDateAjax(span, societyId, newValue, currentValue);
            } else {
                span.text(currentValue);
            }
        });
        input.on('blur', function () {
            setTimeout(function () {
                if (!input.data('datepicker')) return;
                const newValue = input.val();
                if (newValue !== currentValue) {
                    updateDateAjax(span, societyId, newValue, currentValue);
                } else {
                    span.text(currentValue);
                }
            }, 200);
        });
    });

    function updateDateAjax(span, societyId, newValue, currentValue) {
        $.ajax({
            url: 'controller/reportController.php',
            type: 'POST',
            data: {
                society_id: societyId,
                last_call_date: newValue,
                updateLastCallDate: "updateLastCallDate",
                csrf: csrf
            },
            success: function (response) {
                if (response === '1') {
                    Lobibox.notify('success', {
                        position: 'top right',
                        icon: 'fa fa-check-circle',
                        msg: "Last Call Date Updated"
                    });
                    span.text(newValue);
                } else {
                    Lobibox.notify('error', {
                        position: 'top right',
                        icon: 'fa fa-times-circle',
                        msg: "Failed to update Last Call Date"
                    });
                    span.text(currentValue);
                }
            },
            error: function () {
                Lobibox.notify('error', {
                    position: 'top right',
                    icon: 'fa fa-times-circle',
                    msg: "Something went wrong"
                });
                span.text(currentValue);
            }
        });
    }

    $('body').on('click', '.editable-followup-date-box', function () {
        const span = $(this).find('.editable-followup-date');
        const currentValue = span.text().trim();
        const societyId = span.data('id');

        if (span.find('.edit-followup-date-input').length > 0) return;

        span.html(`<input type="text" class="edit-followup-date-input form-control autoclose-datepicker-date" value="${currentValue}" autocomplete="off" />`);
        const input = span.find('.edit-followup-date-input');

        input.datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd'
        }).datepicker('show').on('changeDate', function () {
            const newValue = input.val();
            if (newValue !== currentValue) {
                updateFollowUpDateAjax(span, societyId, newValue, currentValue);
            } else {
                span.text(currentValue);
            }
        });

        input.on('blur', function () {
            setTimeout(function () {
                if (!input.data('datepicker')) return;
                const newValue = input.val();
                if (newValue !== currentValue) {
                    updateFollowUpDateAjax(span, societyId, newValue, currentValue);
                } else {
                    span.text(currentValue);
                }
            }, 200);
        });
    });

    function updateFollowUpDateAjax(span, societyId, newValue, currentValue) {
        $.ajax({
            url: 'controller/reportController.php',
            type: 'POST',
            data: {
                society_id: societyId,
                follow_up_date: newValue,
                updateFollowUpDate: "updateFollowUpDate",
                csrf: csrf
            },
            success: function (response) {
                if (response === '1') {
                    Lobibox.notify('success', {
                        position: 'top right',
                        icon: 'fa fa-check-circle',
                        msg: "Follow-Up Date Updated"
                    });
                    span.text(newValue);
                } else {
                    Lobibox.notify('error', {
                        position: 'top right',
                        icon: 'fa fa-times-circle',
                        msg: "Failed to update Follow-Up Date"
                    });
                    span.text(currentValue);
                }
            },
            error: function () {
                Lobibox.notify('error', {
                    position: 'top right',
                    icon: 'fa fa-times-circle',
                    msg: "Something went wrong"
                });
                span.text(currentValue);
            }
        });
    }

});
$('#reportTableReorderable tfoot th').each(function () {
    var $th = $(this);

    if ($th.hasClass('selectAllNew')) {
    } else {
        var title = $th.text();
        $th.html('<input class="form-control tableSearch" type="text" placeholder="Search ' + title + '" />');
    }
});

// Initialize reportTableReorderable only if it exists and hasn't been initialized yet
// Note: This table may be initialized with server-side processing in engagementReport.php
if (
    $('#reportTableReorderable').length > 0 &&
    !$.fn.DataTable.isDataTable('#reportTableReorderable') &&
    $('#reportTableReorderable').data('use-server') != 1
) {
    var table = $('#reportTableReorderable').DataTable({
        colReorder: true,
        lengthChange: false,
        buttons: [
            'copy', 'excel', 'pdf', 'csv', 'colvis',
            {
                text: 'Select All Columns',
                action: function (e, dt, node, config) {
                    dt.columns().every(function () {
                        this.visible(true, false);
                    });
                    dt.columns.adjust().draw(false);
                }
            },
            {
                text: 'Deselect All Columns',
                action: function (e, dt, node, config) {
                    dt.columns().every(function () {
                        this.visible(false, false);
                    });
                    dt.column(0).visible(true, false);
                    dt.columns.adjust().draw(false);
                }
            }
        ],
        select: true,
        bPaginate: false,
    });

    $('#reportTableReorderable_wrapper').prepend(table.buttons().container());
    table.columns().every(function () {
        var that = this;
        $('input[type="text"]', this.footer()).on('keyup change', function () {
            if (that.search() !== this.value) {
                that
                    .search(this.value)
                    .draw();
            }
        });
    });
    $('#search_0').css('text-align', 'center');
}

$('#publishLeaveDataFrm').on('submit', function (e) {
    e.preventDefault(); // Prevent form submission

    createSwalProgressBar();

    var csrf = $('input[name="csrf"]').val();
    var selectedSocieties = $('.pagePrivilege:checkbox:checked').map(function () {
        return $(this).val();
    }).get();

    if (selectedSocieties.length === 0) {
        swal({
            title: "Error",
            text: "No societies selected!",
            icon: "error",
            timer: 3000,
        });
        return;
    }

    processRequests(selectedSocieties, function (society_id, callback) {
        var isposted = $('#val_' + society_id).val();
        var year = $('#year').val();

        if (isposted === "1") {
            $.ajax({
                url: "controller/buildingController.php",
                type: "POST",
                data: {
                    society_id_post: society_id,
                    syncLeaveType: "syncLeaveType",
                    csrf: csrf
                },
                beforeSend: function () {
                    $('#result_' + society_id).html('<i class="fa fa-spinner fa-2x text-secondary"></i>');
                },
                success: function (response) {
                    var result = response.split(':');
                    if (result[1] === "201") {
                        $('#val_' + society_id).val('0');
                        $('#result_' + society_id).html('<i class="text-danger">' + result[0] + '</i>');
                        callback(false);
                    } else {
                        $('#val_' + society_id).val('1');
                        $('#result_' + society_id).html('<i class="text-success">' + result[0] + '</i>');
                        callback(true);
                    }
                },
                error: function () {
                    $('#result_' + society_id).html('<i class="text-danger">Error processing request.</i>');
                    callback(false);
                }
            });
        } else {
            callback(false); // Skip if not posted
        }
    });
});
$(document).ready(function () {
    $(document).on('click', '.img-select', function () {
        const selectedIconUrl = $(this).data('url');
        const selectedIconName = selectedIconUrl.split('/').pop();
        $('#expense_icon').val(selectedIconName);
        $('#iconPickerBtn').html(`<img src="${selectedIconUrl}" height="30" class="selected-icon" />`);
        $('#iconPickerBtnRemove').html(`<button type="button" class="btn btn-sm btn-danger remove-icon-btn" title="Remove Icon">&times;</button>`);
        $('#iconModal').modal('hide');
    });

    $('#iconSearch').on('keyup', function () {
        const search = $(this).val().toLowerCase();
        $('#imageGrid .icon-item').each(function () {
            const iconName = $(this).find('.img-select').data('name')?.toLowerCase() || '';
            $(this).toggle(iconName.includes(search));
        });
    });

    $('#iconModal').on('hidden.bs.modal', function () {
        $('#iconSearch').val('');
        $('#imageGrid .icon-item').show();
        $('.img-select').removeClass('selected');
    });

    $(document).on('click', '.remove-icon-btn', function () {
        $('#iconPickerBtn').html('+');
        $('#iconPickerBtnRemove').empty();
        $('#expense_icon').val('');
        $('#expense_icon_preview').hide();
    });

    $('#iconUploadForm').on('submit', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        var fileInput = $('#uploadIcon')[0];
        var iconName = $('#iconName').val().trim();

        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            $('#uploadStatus').html('<div class="text-danger">Please select an icon file.</div>');
            return;
        }

        if (!iconName) {
            $('#uploadStatus').html('<div class="text-danger">Icon name cannot be empty.</div>');
            return;
        }

        var fileName = fileInput.files[0].name;
        var fileExtension = fileName.split('.').pop().toLowerCase();

        // Extension validation
        if (!['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'].includes(fileExtension)) {
            $('#uploadStatus').html('<div class="text-danger">Invalid file type. Allowed: png, jpg, jpeg, gif, svg, webp.</div>');
            return;
        }

        formData.append('uploadIconData', 'uploadIconData');
        formData.set('iconName', iconName);

        $.ajax({
            url: './controller/expenseController.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (typeof response === "string") {
                    response = JSON.parse(response);
                }

                if (response.status === "success") {
                    $('#uploadStatus').html('<div class="text-success">' + response.message + '</div>');
                    $('#iconUploadForm')[0].reset();

                    let iconName = response.iconName;
                    let iconUrl = response.iconUrl;
                    const iconNameLower = iconName.toLowerCase();
                    const iconNameCapitalized = iconName.charAt(0).toUpperCase() + iconName.slice(1);

                    $('#imageGrid .icon-item').each(function () {
                        const existingName = $(this).find('.img-select').data('name')?.toLowerCase();
                        if (existingName === iconNameLower) {
                            $(this).remove();
                        }
                    });

                    const newIconHtml = `
          <div class="col-sm-6 col-md-3 col-lg-1 icon-item text-center mb-4">
            <div class="card icon-card shadow-sm img-select" data-name="${iconNameLower}" data-url="${iconUrl}">
              <img src="${iconUrl}" class="card-img-top p-2" style="height:40px; object-fit:contain;" alt="${iconNameCapitalized}">
              <div class="card-body p-2">
                <small class="text-muted d-block" style="font-size: 12px;">${iconNameCapitalized}</small>
              </div>
            </div>
          </div>`;

                    $('#imageGrid').append(newIconHtml);

                    const sortedItems = $('#imageGrid .icon-item').sort(function (a, b) {
                        const nameA = $(a).find('.img-select').data('name')?.toLowerCase();
                        const nameB = $(b).find('.img-select').data('name')?.toLowerCase();
                        return nameA.localeCompare(nameB);
                    });

                    $('#imageGrid').empty().append(sortedItems);
                } else {
                    $('#uploadStatus').html('<div class="text-danger">' + response.message + '</div>');
                }
            },
            error: function () {
                $('#uploadStatus').html('<div class="text-danger">Upload failed.</div>');
            }
        });
    });
});


$('#publishHolidayDataFrm').on('submit', function (e) {
    e.preventDefault(); // Prevent form submission

    createSwalProgressBar();

    var csrf = $('input[name="csrf"]').val();
    var selectedSocieties = $('.pagePrivilege:checkbox:checked').map(function () {
        return $(this).val();
    }).get();

    if (selectedSocieties.length === 0) {
        swal({
            title: "Error",
            text: "No societies selected!",
            icon: "error",
            timer: 3000,
        });
        return;
    }

    processRequests(selectedSocieties, function (society_id, callback) {
        var isposted = $('#val_' + society_id).val();
        var year = $('#year').val();

        if (isposted === "1") {
            $.ajax({
                url: "controller/buildingController.php",
                type: "POST",
                data: {
                    society_id_post: society_id,
                    syncHolidayType: "syncHolidayType",
                    csrf: csrf
                },
                beforeSend: function () {
                    $('#result_' + society_id).html('<i class="fa fa-spinner fa-2x text-secondary"></i>');
                },
                success: function (response) {
                    var result = response.split(':');
                    if (result[1] === "201") {
                        $('#val_' + society_id).val('0');
                        $('#result_' + society_id).html('<i class="text-danger">' + result[0] + '</i>');
                        callback(false);
                    } else {
                        $('#val_' + society_id).val('1');
                        $('#result_' + society_id).html('<i class="text-success">' + result[0] + '</i>');
                        callback(true);
                    }
                },
                error: function () {
                    $('#result_' + society_id).html('<i class="text-danger">Error processing request.</i>');
                    callback(false);
                }
            });
        } else {
            callback(false); // Skip if not posted
        }
    });
});

$(document).ready(function () {
    $('body').on('click', '.editable-spoc-box', function () {
        const span = $(this).find('.editable-spoc');
        const currentValue = span.text().trim();
        const societyId = span.data('id');
        const defaultValue = span.data('default');
        if (span.find('.edit-input').length > 0) return;

        const inputHtml = `<input type="text" maxlength="100" class="edit-input form-control" value="${currentValue}" style="width: 70px;" />`;
        span.html(inputHtml);

        const input = span.find('.edit-input');

        input.on('input', function () {
            let sanitized = $(this).val().replace(/[^a-zA-Z0-9 .,!?@#\-]/g, '');
            $(this).val(sanitized);
        });

        input.focus().on('blur keyup', function (e) {
            if (e.type === 'blur' || e.key === 'Enter') {
                const newValue = input.val().trim();

                if (newValue !== currentValue) {
                    $.ajax({
                        url: 'controller/reportController.php',
                        type: 'POST',
                        data: {
                            society_id: societyId,
                            last_spoc: newValue,
                            updateSpoc: "updateSpoc",
                            csrf: csrf
                        },
                        success: function (response) {
                            if (response === '1') {
                                Lobibox.notify('success', {
                                    position: 'top right',
                                    icon: 'fa fa-check-circle',
                                    msg: "Spoc Updated Successfully"
                                });
                                const finalValue = newValue === '' ? defaultValue : newValue;
                                span.text(finalValue);
                            } else {
                                Lobibox.notify('error', {
                                    position: 'top right',
                                    icon: 'fa fa-times-circle',
                                    msg: "Something went wrong"
                                });
                                span.text(currentValue);
                            }
                        },
                        error: function () {
                            Lobibox.notify('error', {
                                position: 'top right',
                                icon: 'fa fa-times-circle',
                                msg: "Something went wrong"
                            });
                            span.text(currentValue);
                        }
                    });
                } else {
                    span.text(currentValue);
                }
            }
        });
    });
});

$(document).ready(function () {
    $('body').on('click', '.editable-spoc-designation-box', function () {
        const span = $(this).find('.editable-spoc-designation');
        const currentValue = span.text().trim();
        const societyId = span.data('id');
        if (span.find('.edit-input').length > 0) return;

        span.html(`<input type="text" maxlength="50" class="edit-input form-control" value="${currentValue}" />`);
        const input = span.find('.edit-input');

        input.on('input', function () {
            this.value = this.value.replace(/[^a-zA-Z0-9 .,!?@#\-]/g, '');
        });

        input.focus().on('blur keyup', function (e) {
            if (e.type === 'blur' || e.key === 'Enter') {
                const newValue = input.val().trim();
                if (newValue !== currentValue) {
                    $.ajax({
                        url: 'controller/reportController.php',
                        type: 'POST',
                        data: {
                            society_id: societyId,
                            last_spoc_designation: newValue,
                            updateSpocDesignation: "updateSpocDesignation",
                            csrf: csrf
                        },
                        success: function (response) {
                            if (response === '1') {
                                Lobibox.notify('success', {
                                    position: 'top right',
                                    icon: 'fa fa-check-circle',
                                    msg: "Spoc Designation Updated Successfully"
                                });
                                span.text(newValue);
                            } else {
                                Lobibox.notify('error', {
                                    position: 'top right',
                                    icon: 'fa fa-times-circle',
                                    msg: "Something went wrong"
                                });
                                span.text(currentValue);
                            }
                        },
                        error: function () {
                            Lobibox.notify('error', {
                                position: 'top right',
                                icon: 'fa fa-times-circle',
                                msg: "Something went wrong"
                            });
                            span.text(currentValue);
                        }
                    });
                } else {
                    span.text(currentValue);
                }
            }
        });
    });
});

$(document).ready(function () {
    $('body').on('click', '.editable-spoc-mobile-box', function () {
        const span = $(this).find('.editable-spoc-mobile');
        const currentValue = span.text().trim();
        const societyId = span.data('id');
        const defaultValue = span.data('default');
        if (span.find('.edit-input').length > 0) return;

        span.html(`<input type="text" maxlength="30" class="edit-input form-control" value="${currentValue}" />`);
        const input = span.find('.edit-input');

        input.on('input', function () {
            this.value = this.value.replace(/[^0-9+\-\s]/g, '');
        });

        input.focus().on('blur keyup', function (e) {
            if (e.type === 'blur' || e.key === 'Enter') {
                const newValue = input.val().trim();
                if (newValue !== currentValue) {
                    $.ajax({
                        url: 'controller/reportController.php',
                        type: 'POST',
                        data: {
                            society_id: societyId,
                            last_spoc_mobile_number: newValue,
                            updateSpocMobile: "updateSpocMobile",
                            csrf: csrf
                        },
                        success: function (response) {
                            if (response === '1') {
                                Lobibox.notify('success', {
                                    position: 'top right',
                                    icon: 'fa fa-check-circle',
                                    msg: "Spoc Mobile Number Updated Successfully"
                                });
                                const finalValue = newValue === '' ? defaultValue : newValue;
                                span.text(finalValue);
                            } else {
                                Lobibox.notify('error', {
                                    position: 'top right',
                                    icon: 'fa fa-times-circle',
                                    msg: "Something went wrong"
                                });
                                span.text(currentValue);
                            }
                        },
                        error: function () {
                            Lobibox.notify('error', {
                                position: 'top right',
                                icon: 'fa fa-times-circle',
                                msg: "Something went wrong"
                            });
                            span.text(currentValue);
                        }
                    });
                } else {
                    span.text(currentValue);
                }
            }
        });
    });
});
$('#publishExpenseDataFrm').on('submit', function (e) {
    e.preventDefault(); // Prevent form submission
    createSwalProgressBar();
    var csrf = $('input[name="csrf"]').val();
    var selectedSocieties = $('.pagePrivilege:checkbox:checked').map(function () {
        return $(this).val();
    }).get();
    if (selectedSocieties.length === 0) {
        swal({
            title: "Error",
            text: "No societies selected!",
            icon: "error",
            timer: 3000,
        });
        return;
    }
    processRequests(selectedSocieties, function (society_id, callback) {
        var isposted = $('#val_' + society_id).val();
        var year = $('#year').val();
        if (isposted === "1") {
            $.ajax({
                url: "controller/buildingController.php",
                type: "POST",
                data: {
                    society_id_post: society_id,
                    syncExpenseType: "syncExpenseType",
                    csrf: csrf
                },
                beforeSend: function () {
                    $('#result_' + society_id).html('<i class="fa fa-spinner fa-2x text-secondary"></i>');
                },
                success: function (response) {
                    var result = response.split(':');
                    if (result[1] === "201") {
                        $('#val_' + society_id).val('0');
                        $('#result_' + society_id).html('<i class="text-danger">' + result[0] + '</i>');
                        callback(false);
                    } else {
                        $('#val_' + society_id).val('1');
                        $('#result_' + society_id).html('<i class="text-success">' + result[0] + '</i>');
                        callback(true);
                    }
                },
                error: function () {
                    $('#result_' + society_id).html('<i class="text-danger">Error processing request.</i>');
                    callback(false);
                }
            });
        } else {
            callback(false); // Skip if not posted
        }
    });
});

// added by sagar vyas
$(document).ready(function () {
    $('#sendWhiteLabelDatafetch').on('submit', function (e) {
        e.preventDefault();
        createSwalProgressBar();
        const selectedDate = document.getElementById('selectedDate').value;
        // var csrf = $('input[name="csrf"]').val();
        var selectedIds = $('.pagePrivilege:checkbox:checked').map(function () {
            var checkbox = $(this);
            var societyId = checkbox.closest('label').find('input[type="hidden"][name="society_id"]').val();
            return societyId;
        }).get();


        if (selectedIds.length === 0) {
            swal({
                title: "Error",
                text: "No white label selected!",
                icon: "error",
                timer: 3000,
            });
            return;
        }
        processRequests(selectedIds, function (society, callback) {
            const society_id = society;
            var isposted = $('#val_' + society).val();
            if (isposted === "1") {
                $.ajax({
                    url: "controller/buildingController.php",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        society_ids: society_id,
                        selectedDate: selectedDate,
                        sendWhiteLabelData: "sendWhiteLabelData",
                        csrf: csrf
                    },
                    beforeSend: function () {
                        $('#result_' + society_id).html('<i class="fa fa-spinner fa-2x text-secondary"></i>');
                    },
                    success: function (response) {

                        if (response.status == 201) {
                            $('#val_' + society_id).val('0');
                            $('#result_' + society_id).html('<i class="text-danger">' + 'error' + '</i>');
                            callback(false);
                        } else {
                            $('#val_' + society_id).val('1');
                            $('#result_' + society_id).html('<i class="text-success">' + 'success' + '</i>');
                            callback(true);
                        }
                    },
                    error: function () {
                        $('#result_' + society_id).html('<i class="text-danger">Error processing request.</i>');
                        callback(false);
                    }
                });
            } else {
                callback(false); // Skip
            }
        });
    });
});

$(document).ready(function () {
    $('body').on('click', '.editable-implementation-remark-box', function () {
        const span = $(this).find('.editable-implementation-remark');
        const currentValue = span.text().trim();
        const societyId = span.data('id');

        if (span.find('.edit-input').length > 0) return;

        span.html(`<input type="text" maxlength="250" class="edit-input form-control" value="${currentValue}" />`);
        const input = span.find('.edit-input');

        input.focus().on('blur keyup', function (e) {
            if (e.type === 'blur' || e.key === 'Enter') {
                const newValue = input.val().trim();
                if (newValue !== currentValue) {
                    $.ajax({
                        url: 'controller/reportController.php',
                        type: 'POST',
                        data: {
                            society_id: societyId,
                            implementation_remark: newValue,
                            updateImplementationRemark: "updateImplementationRemark",
                            csrf: csrf
                        },
                        success: function (response) {
                            if (response === '1') {
                                Lobibox.notify('success', {
                                    pauseDelayOnHover: true,
                                    continueDelayOnInactiveTab: false,
                                    position: 'top right',
                                    icon: 'fa fa-check-circle',
                                    msg: "Implementation Remark Updated Successfully"
                                });
                                span.text(newValue);
                            } else {
                                Lobibox.notify('error', {
                                    pauseDelayOnHover: true,
                                    continueDelayOnInactiveTab: false,
                                    position: 'top right',
                                    icon: 'fa fa-times-circle',
                                    msg: "Something went wrong"
                                });
                                span.text(currentValue);
                            }
                        },
                        error: function () {
                            Lobibox.notify('error', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                icon: 'fa fa-times-circle',
                                msg: "Something went wrong"
                            });
                            span.text(currentValue);
                        }
                    });
                } else {
                    span.text(currentValue);
                }
            }
        });
    });
});

$(document).ready(function () {
    $('#patchForm').on('submit', function (e) {
        e.preventDefault();
        const folder_name = $('#folder_name').val().trim();
        const sql_file_name = $('#sql_file_name').val().trim();

        if (folder_name || sql_file_name) {
            $.ajax({
                url: 'controller/reportController.php',
                method: 'POST',
                data: {
                    folder_name: folder_name,
                    sql_file_name: sql_file_name,
                    savePatchData: "savePatchData",
                    csrf: csrf
                },
                success: function (response) {
                    if (response === '1') {
                        $('i[data-copy^="rm"]').each(function () {
                            const dataCopy = $(this).attr('data-copy');
                            if (dataCopy === 'rm apply_sql_patch.sh') return;
                            if (dataCopy.endsWith('.sql')) {
                                $(this).attr('data-copy', `rm ${sql_file_name}`);
                                $(this).parent().html(`6. rm ${sql_file_name} <i class="copy-icon fa fa-copy" data-copy="rm ${sql_file_name}"></i>`);
                            }
                        });
                        $('i[data-copy^="chown"]').attr('data-copy', `chown -R myco:chpl ${folder_name}/`)
                            .parent().html(`1. chown -R myco:chpl ${folder_name}/ <i class="copy-icon fa fa-copy" data-copy="chown -R myco:chpl ${folder_name}/"></i>`);
                        $('i[data-copy^="chmod -R 777"]').attr('data-copy', `chmod -R 777 ${folder_name}/img/`)
                            .parent().html(`2. chmod -R 777 ${folder_name}/img/ <i class="copy-icon fa fa-copy" data-copy="chmod -R 777 ${folder_name}/img/"></i>`);
                        $('i[data-copy^="rm"]').each(function () {
                            const dataCopy = $(this).attr('data-copy');

                            if (dataCopy === 'rm apply_sql_patch.sh') return;
                            if (dataCopy.endsWith('.sql')) return;

                            $(this).attr('data-copy', `rm ${folder_name}`);
                            $(this).parent().html(`8. rm ${folder_name} <i class="copy-icon fa fa-copy" data-copy="rm ${folder_name}"></i>`);
                        });
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Patch File Master updated successfully!',
                            timer: 1000,
                            confirmButtonColor: '#3085d6',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Update Failed',
                            text: 'Could not update the Patch File Master.',
                            confirmButtonColor: '#d33',
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong with the request.',
                        confirmButtonColor: '#d33',
                    });
                }

            });
        }
    });
});

$('#publishSalaryHeadDataFrm').on('submit', function (e) {
    e.preventDefault(); // Prevent form submission
    createSwalProgressBar();
    var csrf = $('input[name="csrf"]').val();
    var selectedSocieties = $('.pagePrivilege:checkbox:checked').map(function () {
        return $(this).val();
    }).get();
    if (selectedSocieties.length === 0) {
        swal({
            title: "Error",
            text: "No societies selected!",
            icon: "error",
            timer: 3000,
        });
        return;
    }
    processRequests(selectedSocieties, function (society_id, callback) {
        var isposted = $('#val_' + society_id).val();
        var year = $('#year').val();
        if (isposted === "1") {
            $.ajax({
                url: "controller/buildingController.php",
                type: "POST",
                data: {
                    society_id_post: society_id,
                    syncSalaryHeadType: "syncSalaryHeadType",
                    csrf: csrf
                },
                beforeSend: function () {
                    $('#result_' + society_id).html('<i class="fa fa-spinner fa-2x text-secondary"></i>');
                },
                success: function (response) {
                    var result = response.split(':');
                    if (result[1] === "201") {
                        $('#val_' + society_id).val('0');
                        $('#result_' + society_id).html('<i class="text-danger">' + result[0] + '</i>');
                        callback(false);
                    } else {
                        $('#val_' + society_id).val('1');
                        $('#result_' + society_id).html('<i class="text-success">' + result[0] + '</i>');
                        callback(true);
                    }
                },
                error: function () {
                    $('#result_' + society_id).html('<i class="text-danger">Error processing request.</i>');
                    callback(false);
                }
            });
        } else {
            callback(false); // Skip if not posted
        }
    });
});

$(".photoOnlyWithGif").change(function () {
    var fileExtension = ['png', 'gif'];
    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        // alert("Only formats are allowed : "+fileExtension.join(', '));
        swal("Only formats are allowed : " + fileExtension.join(', '), { icon: "error", });
        $('.photoOnlyWithGif').val('');
    }
});

$(document).ready(function () {
    $('body').on('click', '.editable-holiday-name-box', function () {
        const span = $(this).find('.editable-holiday-name');
        const currentValue = span.text().trim();
        if (span.find('.edit-input').length > 0) return;

        span.html(`<input type="text" maxlength="250" name="festival_name[]" class="edit-input form-control" value="${currentValue}" />`);
        span.find('.edit-input').focus();
    });

    $('body').on('click', '.editable-holiday-desc-box', function () {
        const span = $(this).find('.editable-holiday-desc');
        const currentValue = span.text().trim();
        if (span.find('.edit-input').length > 0) return;

        span.html(`<input type="text" maxlength="250" name="holiday_desc[]" class="edit-input form-control" value="${currentValue}" />`);
        span.find('.edit-input').focus();
    });

    const currentYear = new Date().getFullYear();

    const startDate = new Date(currentYear, 0, 1);
    const endDate = new Date(currentYear, 11, 31);
    $('body').on('click', '.editable-holiday-date-box', function () {
        const span = $(this).find('.editable-holiday-date');
        const currentValue = span.text().trim();
        if (span.find('.edit-date-input').length > 0) return;

        span.html(`<input type="text" name="holiday_date[]" class="edit-date-input form-control holiday-date-input" value="${currentValue}" autocomplete="off" />`);
        const input = span.find('.edit-date-input');

        input.datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            orientation: 'bottom auto',
            zIndexOffset: 9999,
            startDate: startDate,
            endDate: endDate
        }).datepicker('show');

        input.focus();
    });
});

function societyselected(selectElement) {
    var selectedValue = selectElement.value;
    $("#society_id").val(selectedValue);
}
function viewCrmDetails(crmRequestId) {
    document.getElementById("crmDetailsContent").innerHTML = '<div class="text-center text-muted">Loading...</div>';
    $.ajax({
        url: 'getCrmDetails.php',
        type: 'POST',
        data: {
            getCrmDetailsList: "getCrmDetailsList",
            crm_request_id: crmRequestId,
            csrf: csrf
        },
        success: function (response) {
            $('#crmDetailsContent').html(response);
        },
        error: function () {
            $('#crmDetailsContent').html('<div class="alert alert-danger">Failed to load CRM details. Please try again.</div>');
        }
    });
}

function requestCrmcreate(request_society_id, society_name, sub_domain, crm_request_created_by, crm_request_created_date, crm_limit, crm_plan_expire_date, crm_package_id, crm_trial_days, crm_request_id) {
    $("#society_id").val(request_society_id);
    $(".companyId").val(request_society_id);
    $("#companyName").val(society_name);
    $("#subDomain").val(sub_domain);
    $("#crmrequestcreatedby").val(crm_request_created_by);
    $("#crmrequestcreateddate").val(crm_request_created_date);
    $("#crmlimit").val(crm_limit);
    $("#crmplanexpiredate").val(crm_plan_expire_date);
    $("#crmpackageid").val(crm_package_id);
    $("#crmtrialdays").val(crm_trial_days);
    $("#crmrequestid").val(crm_request_id);
    $.ajax({
        url: './companyListAjax.php',
        type: 'POST',
        data: {
            getCRMList: "getCRMList",
            csrf: csrf,
            request_society_id: request_society_id,
        },
        dataType: 'json',
        success: function (response) {
            if (response.success && response.data.societies.length > 0) {
                var options = '<option value="">-- Select --</option>';
                $.each(response.data.societies, function (index, crm) {
                    if (crm.selected != "1") {
                        options += '<option value="' + crm.society_crm_id + '">' + crm.server + '</option>';
                    } else {
                        options += '<option selected value="' + crm.society_crm_id + '">' + crm.server + '</option>';
                    }
                });
                $('#society_crm_id').html(options);
            } else {
                $('#society_crm_id').html('<option value="">No CRM available</option>');
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX error: ' + error);
        }
    });
}

$(document).ready(function () {
    function updateSubDomain() {
        var domain = $('#domain_select').val();
        var endName = ($.trim($('#end_url_name').val()) || '').replace(/^\.+|\.+$/g, '');
        if (domain && endName) {
            var full = domain + endName + '/';
            $('#sub_domain_full').val(full);
            $('#full_url_preview1').text('' + full);
        } else {
            $('#full_url_preview1').text('');
            $('#sub_domain_full').val('');
        }
        if (domain) {
            fetchDomainInfo(domain);
        } else {
            $("#full_url_preview2").html("");
        }
    }
    $(document).on('change keyup blur', '#domain_select, #end_url_name', updateSubDomain);
    updateSubDomain();
});
