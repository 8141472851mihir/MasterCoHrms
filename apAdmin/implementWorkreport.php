<style>
    .report-desc-col {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row align-items-center py-2 d-flex flex-nowrap">
            <div class="d-flex align-items-center mr-3">
                <h4 class="page-title mb-0">Work Report (Implementation)</h4>
            </div>
            <form action="" method="get" class="form-inline d-flex align-items-center ml-5 mr-3 flex-nowrap">
                <div class="d-flex align-items-center mr-3">
                    <label for="report_date" class="mb-0 mr-2 font-weight-bold">Date</label>
                    <input type="text" class="form-control form-control-sm" autocomplete="off" readonly
                        name="report_date" id="report-datepicker"
                        value="<?php echo isset($_GET['report_date']) ? $_GET['report_date'] : date('Y-m-d'); ?>">
                </div>
                <div class="d-flex align-items-center">
                    <label for="report_type" class="mb-0 mr-2 font-weight-bold">Report Type</label>
                    <select class="form-control form-control-sm" name="report_type" onchange="this.form.submit()">
                        <option value="2" <?php echo (!isset($_GET['report_type']) || $_GET['report_type'] == '2') ? "selected" : ""; ?>>All</option>
                        <option value="0" <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == '0') ? "selected" : ""; ?>>Setup Training</option>
                        <option value="1" <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == '1') ? "selected" : ""; ?>>Product Training</option>
                    </select>
                </div>
            </form>
            <div class="ml-auto">
                <a href="javascript:void(0);" data-toggle="modal" onclick="addForm()" data-target="#addReport"
                    class="btn btn-primary btn-sm waves-effect waves-light" title="Add Report">
                    <i class="fa fa-plus mr-1"></i> Add Report
                </a>
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
                                        <th>Sr.No</th>
                                        <th>Action</th>
                                        <th>Trainer Name</th>
                                        <th>Report Date</th>
                                        <th>Report Type</th>
                                        <th>NO of calls</th>
                                        <th>NO of lined up</th>
                                        <th>Total company</th>
                                        <th>Added Date</th>
                                        <th class="report-desc-col">Report Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $where = "";
                                    $report_date = isset($_GET["report_date"]) && $_GET["report_date"] != "" ? $_GET["report_date"] : date('Y-m-d');
                                    $where .= " AND iwr.report_date = '$report_date'";

                                    if (isset($_GET["report_type"]) && $_GET["report_type"] != "2") {
                                        $report_type = $_GET["report_type"];
                                        $where .= " AND iwr.report_type = '$report_type'";
                                    }
                                    $q = $d->selectRow(
                                        "iwr.implementation_work_report_id, iwr.admin_id, bms.admin_name, iwr.report_date, iwr.report_type, iwr.company_ids, iwr.no_of_call, iwr.no_of_linedup, iwr.report_desc, iwr.added_date",
                                        "implementation_work_report iwr JOIN bms_admin_master bms ON iwr.admin_id = bms.admin_id",
                                        "iwr.admin_id = '$bms_admin_id' $where",
                                        "ORDER BY iwr.implementation_work_report_id DESC"
                                    );
                                    $i = 1;
                                    while ($data = mysqli_fetch_array($q)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td>
                                                <?php
                                                $currentDate = date('Y-m-d');
                                                $reportDate = date('Y-m-d', strtotime($data['report_date']));
                                                ?>
                                                <div style="display: flex; gap: 8px; align-items: center;">
                                                    <?php if ($bms_admin_id == $data['admin_id'] && $reportDate == $currentDate): ?>
                                                        <div>
                                                            <a href="javascript:void(0);" class="btn btn-sm btn-primary"
                                                                title="Edit Report"
                                                                onclick="editForm('<?php echo $data['implementation_work_report_id']; ?>', '<?php echo ($data['report_type']); ?>', '<?php echo $data['company_ids']; ?>', '<?php echo $data['no_of_linedup']; ?>', '<?php echo $data['report_desc']; ?>', '<?php echo $data['no_of_call']; ?>', '<?php echo $data['report_date']; ?>')">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <form action="viewWorkReport" method="GET" style="display: inline;">
                                                            <input type="hidden" name="implementation_work_report_id"
                                                                value="<?php echo $data['implementation_work_report_id']; ?>">
                                                            <input type="hidden" name="source" value="implementWorkreport">
                                                            <input type="hidden" name="report_date"
                                                                value="<?php echo isset($_GET['report_date']) ? $_GET['report_date'] : date('Y-m-d'); ?>">
                                                            <input type="hidden" name="report_type"
                                                                value="<?php echo isset($_GET['report_type']) ? $_GET['report_type'] : '2'; ?>">
                                                            <button type="submit" class="btn btn-sm btn-info"
                                                                title="View Report">
                                                                <i class="fa fa-eye"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>

                                            <td><?php echo $data['admin_name']; ?></td>
                                            <td><?php echo date("d F Y", strtotime($data['report_date'])); ?></td>
                                            <td><?php echo ($data['report_type'] == 0) ? "Setup Training" : "Product Training"; ?>
                                            </td>
                                            <td><?php echo $data['no_of_call']; ?></td>
                                            <td><?php echo $data['no_of_linedup']; ?></td>
                                            <td><?php echo count(explode(",", $data['company_ids'])); ?></td>
                                            <td><?php echo date("d F Y D, H:i A", strtotime($data['added_date'])); ?></td>
                                            <td class="report-desc-col"><?php echo $data['report_desc']; ?></td>
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
<div class="modal fade" id="addReport">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-primary">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-uppercase text-white">Add Report
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addimplementWorkReportForm" action="controller/implementWorkreportController.php"
                    method="POST">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="report_date" class="col-form-label">Date<span class="required"> *</span></label>
                            <input type="text" class="form-control" name="report_date" readonly
                                value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="report_type" class="col-form-label">Type<span> *</span></label>
                            <select name="report_type" id="report_type" class="form-control single-select" required>
                                <option value="" disabled>-- SELECT --</option>
                                <option value="0" <?php echo ($data['report_type'] == 0) ? 'selected' : ''; ?>>Setup
                                </option>
                                <option value="1" <?php echo ($data['report_type'] == 1) ? 'selected' : ''; ?>>Product
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="no_of_call" class="col-form-label">NO. of calls<span class="required">
                                    *</span></label>
                            <input type="text" autocomplete="off" maxlength="3" class="form-control onlyNumber"
                                id="no_of_call" name="no_of_call" required>
                        </div>
                        <div class="col-md-6">
                            <label for="no_of_linedup" class="col-form-label">NO. of lined up<span class="required">
                                    *</span></label>
                            <input type="text" autocomplete="off" maxlength="3" class="form-control onlyNumber"
                                id="no_of_linedup" name="no_of_linedup" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="society_id" class="col-form-label">Company Name<span class="required">
                                    *</span></label>
                            <select name="society_id[]" id="society_id" class="form-control multiple-select"
                                multiple="multiple" required>
                                <?php
                                $societies = $d->selectRow("society_master.society_name,society_master.society_id", "society_master", "society_status='0' AND society_name!=''AND created_on_society_server='1'", "ORDER BY society_id DESC");
                                while ($data = mysqli_fetch_array($societies)) { ?>
                                    <option value="<?php echo $data['society_id']; ?>">
                                        <?php echo htmlspecialchars($data['society_name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="report_desc" class="col-form-label">Report Description</label>
                            <textarea class="form-control" id="report_desc" maxlength="200" name="report_desc"
                                placeholder="Work Report" rows="2"
                                style="width: 100%; max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></textarea>
                        </div>
                    </div>
                    <div class="form-footer text-center">
                        <input type="hidden" name="workReport" id="workReport" value="workReport">
                        <input type="hidden" name="editId" id="editId" value="" />
                        <input type="hidden" name="bms_admin_id" id="bms_admin_id" value="<?php echo $bms_admin_id; ?>">
                        <button type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i><span
                                id="submitButton">ADD</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function addForm() {
        $("#workReport").val("workReport");
        $("#report_type").prop("disabled", false);
        $("#report_type").val('0').trigger("change");
        $("#no_of_call").val("");
        $("#no_of_linedup").val("");
        $("#report_desc").val("");
        $("#editId").val("");
        $("#submitButton").text("ADD");
        $(".modal-title").text("Add Report");
        $("#society_id").val([]).trigger("change");
        $("#addReport").modal("show");
    }
    function editForm(id, type, company_ids, lineup, desc, noOfcall, report_date) {
        let currentDate = new Date().toISOString().split('T')[0];
        if (report_date !== currentDate) {
            alert("You can only edit reports for the current date.");
            return;
        }
        $("#workReport").val("workReportEdit");
        $("#report_type").val(type).trigger("change");
        $("#no_of_linedup").val(lineup);
        $("#no_of_call").val(noOfcall);
        $("#report_desc").val(desc);
        $("#editId").val(id);
        $("#submitButton").text("UPDATE");
        $(".modal-title").text("Update Report");
        let companyArray = company_ids.split(",");
        $("#society_id").val([]).trigger("change");
        companyArray.forEach(function (companyId) {
            $("#society_id option[value='" + companyId.trim() + "']").prop("selected", true);
        });
        $("#report_type").prop("disabled", true);
        $("#society_id").trigger("change");
        $("#addReport").modal("show");
    }
</script>