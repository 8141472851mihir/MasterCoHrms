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
		<div class="row pt-2 pb-2">
			<div class="col-lg-12 d-flex justify-content-between align-items-center">
				<h4 class="page-title mb-0">Manage Daily Reports</h4>
				<form action="" method="get">
					<div class="row d-flex justify-content-between align-items-center mr-0">
						<label for="report-datepicker" class="mr-2 mb-0">Date:</label>
						<div class="col">
							<input type="text" class="form-control mr-3" autocomplete="off" readonly name="report_date"
								id="report-datepicker"
								value="<?php echo isset($_GET['report_date']) ? $_GET['report_date'] : date('Y-m-d'); ?>">
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<ul class="nav nav-tabs nav-tabs-info nav-justified" id="trainingTabs" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="setup-tab" data-toggle="tab" href="#setup" role="tab"
									aria-controls="setup" aria-selected="true">Setup Training</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="product-tab" data-toggle="tab" href="#product" role="tab"
									aria-controls="product" aria-selected="false">Product Training</a>
							</li>
						</ul>

						<div class="tab-content" id="trainingTabsContent">
							<!-- Setup Training Tab -->
							<div class="tab-pane fade show active" id="setup" role="tabpanel"
								aria-labelledby="setup-tab">
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
												$where = "1=1";
												$report_date = isset($_GET["report_date"]) && $_GET["report_date"] != "" ? $_GET["report_date"] : date('Y-m-d');
												$where .= " AND iwr.report_date = '$report_date' AND iwr.report_type = 0";


												$q = $d->selectRow(
													"iwr.implementation_work_report_id, iwr.admin_id, bms.admin_name, iwr.report_date, iwr.report_type, iwr.company_ids, iwr.no_of_call, iwr.no_of_linedup, iwr.report_desc, iwr.added_date",
													"implementation_work_report iwr
										JOIN bms_admin_master bms ON iwr.admin_id = bms.admin_id",
													"$where",
													"ORDER BY iwr.implementation_work_report_id DESC"
												);
												$i = 1;
												while ($data = mysqli_fetch_array($q)) {
													?>
													<tr>
														<td><?php echo $i++; ?></td>
														<td>
															<form action="viewWorkReport" method="GET"
																style="display: inline;">
																<input type="hidden" name="implementation_work_report_id"
																	value="<?php echo $data['implementation_work_report_id']; ?>">
																<input type="hidden" name="active_tab" value="setup">
																<input type="hidden" name="source"
																	value="manageDailyReports">
																<input type="hidden" name="report_date"
																	value="<?php echo isset($_GET['report_date']) ? $_GET['report_date'] : date('Y-m-d'); ?>">

																<button type="submit" class="btn btn-sm btn-info"
																	title="View Report">
																	<i class="fa fa-eye"></i>
																</button>
															</form>

														</td>
														<td><?php echo $data['admin_name']; ?></td>
														<td><?php echo date("d F Y", strtotime($data['report_date'])); ?>
														</td>
														<td><?php echo ($data['report_type'] == 0) ? "Setup Training" : "Product Training"; ?>
														</td>
														<td><?php echo $data['no_of_call']; ?></td>
														<td><?php echo $data['no_of_linedup']; ?></td>
														<td><?php echo count(explode(",", $data['company_ids'])); ?></td>
														<td><?php echo date("d F Y D, H:i A", strtotime($data['added_date'])); ?>
														</td>
														<td class="report-desc-col"><?php echo $data['report_desc']; ?></td>
													</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>

							<!-- Product Training Tab -->
							<div class="tab-pane fade" id="product" role="tabpanel" aria-labelledby="product-tab">
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
											</thead>
											<tbody>
												<?php
												$where = "1=1";
												$report_date = isset($_GET["report_date"]) && $_GET["report_date"] != "" ? $_GET["report_date"] : date('Y-m-d');
												$where .= " AND iwr.report_date = '$report_date' AND iwr.report_type = 1";



												$q = $d->selectRow(
													"iwr.implementation_work_report_id, iwr.admin_id, bms.admin_name, iwr.report_date, iwr.report_type, iwr.company_ids, iwr.no_of_call, iwr.no_of_linedup, iwr.report_desc, iwr.added_date",
													"implementation_work_report iwr
													JOIN bms_admin_master bms ON iwr.admin_id = bms.admin_id",
													"$where",
													"ORDER BY iwr.implementation_work_report_id DESC"
												);
												$i = 1;
												while ($data = mysqli_fetch_array($q)) {
													?>
													<tr>
														<td><?php echo $i++; ?></td>
														<td>
															<form action="viewWorkReport" method="GET"
																style="display: inline;">
																<input type="hidden" name="implementation_work_report_id"
																	value="<?php echo $data['implementation_work_report_id']; ?>">
																<input type="hidden" name="source"
																	value="manageDailyReports">
																<input type="hidden" name="active_tab" value="product">

																<input type="hidden" name="report_date"
																	value="<?php echo isset($_GET['report_date']) ? $_GET['report_date'] : date('Y-m-d'); ?>">

																<button type="submit" class="btn btn-sm btn-info"
																	title="View Report">
																	<i class="fa fa-eye"></i>
																</button>
															</form>

														</td>
														<td><?php echo $data['admin_name']; ?></td>
														<td><?php echo date("d F Y", strtotime($data['report_date'])); ?>
														</td>
														<td><?php echo ($data['report_type'] == 0) ? "Setup Training" : "Product Training"; ?>
														</td>
														<td><?php echo $data['no_of_call']; ?></td>
														<td><?php echo $data['no_of_linedup']; ?></td>
														<td><?php echo count(explode(",", $data['company_ids'])); ?></td>
														<td><?php echo date("d F Y D, H:i A", strtotime($data['added_date'])); ?>
														</td>
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
		</div>
	</div>
</div>
<script>
	document.addEventListener("DOMContentLoaded", function () {
		const urlParams = new URLSearchParams(window.location.search);
		const activeTab = urlParams.get('active_tab') || 'setup';
		const tabLink = document.querySelector(`#${activeTab}-tab`);
		const tabPane = document.querySelector(`#${activeTab}`);
		if (!tabLink || !tabPane) {
			return;
		}

		document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
		document.querySelectorAll('.tab-pane').forEach(pane => {
			pane.classList.remove('active', 'show');
		});

		tabLink.classList.add('active');
		tabPane.classList.add('active', 'show');
	});
</script>