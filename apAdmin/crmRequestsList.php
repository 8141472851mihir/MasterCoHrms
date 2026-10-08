<?php extract($_REQUEST);?>
<div class="content-wrapper">
	<div class="container-fluid">
		<div class="row pt-2 pb-2">
			<div class="col-sm-12">
				<h4 class="page-title">CRM Requests List</h4>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="default-datatable1" class="table table-bordered">
								<thead>
									<th>#</th>
									<th>Action</th>
									<th>Request Status</th>
									<th>id</th>
									<th>Company</th>
									<th>City</th>
									<th>CRM Package</th>
									<th>CRM Plan Expire Date</th>
									<th>Remaining Days</th>
									<th>CRM Limit</th>
									<th>CRM Payment</th>
									<th>CRM Payment Amount</th>
									<th>CRM Payment Mode</th>
									<th>Request Added By</th>
									<th>Request Added Date</th>
								</thead>
								<tbody>
									<?php
									$i = 1;
									$crmrequestdata = $d->selectRow("crm_request_master.*,society_master.society_id,society_master.society_name,society_master.city_name,manage_plan.plan_name,society_master.sub_domain", "crm_request_master,society_master,manage_plan", "crm_request_master.society_id=society_master.society_id AND crm_request_master.crm_package_id=manage_plan.plan_value AND (crm_request_master.request_status = 0 OR crm_request_master.request_status = 2)", "order by crm_request_master.crm_request_id DESC");
									while ($crmdata = mysqli_fetch_array($crmrequestdata)) {
										extract($crmdata);
										if ($crm_payment_status == 0) {
											$crmpayment = "Not received";
										} else {
											$crmpayment = "Received";
										}

										if ($crm_payment_mode == 1) {
											$paymentmode = "Online Bank Transfer";
										} elseif ($crm_payment_mode == 2) {
											$paymentmode = "Cheque";
										} elseif ($crm_payment_mode == 3) {
											$paymentmode = "UPI";
										} else {
											$paymentmode = "Cash";
										}

										?>
										<tr>
											<td><?php echo $i++; ?></td>
											<td>
												<?php 
												if($request_status == 0 || $request_status == 2){ ?>
												<a href="javascript:void(0);" data-toggle="modal"
													data-target="#viewCrmModal"
													onclick="viewCrmDetails('<?php echo $crm_request_id; ?>')"
													class="btn btn-sm btn-info m-1" title="View CRM Details">
													<i class="fa fa-eye"></i>
												</a>	
												<?php } if($request_status == 0){ ?>
												<a href="javascript:void" data-toggle="modal"
													onclick="requestCrmcreate('<?php echo $society_id; ?>',`<?php echo $society_name; ?>`,'<?php echo $sub_domain; ?>','<?php echo $crm_request_created_by; ?>','<?php echo $crm_request_created_date; ?>','<?php echo $crm_limit; ?>','<?php echo $crm_plan_expire_date; ?>','<?php echo $crm_package_id; ?>','<?php echo $crm_trial_days; ?>','<?php echo $crm_request_id; ?>')"
													data-target="#createCrm" onclick=""
													class="btn btn-sm btn-primary waves-effect waves-light m-1"
													title="Create CRM"> <i class="fa fa-users"></i>
												</a>
												<a href="javascript:void(0);" data-toggle="modal"
													data-target="#rejectCrmModal"
													onclick="openRejectCrmModal('<?php echo $society_id; ?>', '<?php echo $crm_request_id; ?>')"
													class="btn btn-sm btn-danger m-1" title="Reject CRM">
													<i class="fa fa-times"></i>
												</a>
												<?php }  ?>
											</td>
											</td>
											<td><?php
											if ($request_status == 0) {
													echo "<span class='text-info'>Pending</span>";
											} else if ($request_status == 2) {
													echo "<span class='text-danger'>Rejected</span>";
											}
											?></td>
											<td><a href="<?php echo $sub_domain; ?>apAdmin/"
											target="_blank"><?php echo '' . $d->short_app_name() . '_' . $society_id; ?></a></td>
											<td><a href="<?php echo $sub_domain; ?>crm/"
													target="_blank"><?php echo $society_name; ?></a></td>
											<td><?php echo $city_name; ?></td>
											<td>
												<?php
												if ($crm_package_id == 0) {
													echo "Custom Plan (" . $crm_trial_days . " Days)";
												} else {
													echo $plan_name;
												}
												?>
											</td>
											<td><?php echo $crm_plan_expire_date; ?></td>
											<td>
												<?php
												$now = time();
												$your_date = strtotime("$crm_plan_expire_date");
												$datediff = $your_date - $now;
												if ($datediff > 0) {
													echo round($datediff / (60 * 60 * 24));
												} else {
													echo "Expired";
												}
												?>
											</td>
											<td><?php echo $crm_limit; ?></td>
											<td><?php echo $crmpayment; ?></td>
											<td><?php echo $crm_payment_amount; ?></td>
											<td><?php echo $paymentmode; ?></td>
											
											<td><?php echo $crm_request_created_by; ?></td>
											<td> <?= isset($crm_request_created_date) ? date('d F Y, h:i A', strtotime($crm_request_created_date)) : '' ?>
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

<div class="modal fade" id="createCrm">
	<div class="modal-dialog ">
		<div class="modal-content border-primary">
			<div class="modal-header bg-primary">
				<h5 class="modal-title text-white">Create CRM</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="card-body">
					<div class="row ">
						<div class="col-md-12">
							<form id="createCrmForm" action="controller/crmRequestController.php" method="post">
								<select type="text" required="" id="society_crm_id" class="form-control single-select"
									name="society_crm_id">
								</select>
								<input type="hidden" name="companyId" class="companyId">
								<input type="hidden" name="companyName" id="companyName">
								<input type="hidden" name="subDomain" id="subDomain">
								<input type="hidden" name="crm_request_created_by" id="crmrequestcreatedby">
								<input type="hidden" name="crm_request_created_date" id="crmrequestcreateddate">
								<input type="hidden" name="crm_limit" id="crmlimit">
								<input type="hidden" name="crm_plan_expiring_date" id="crmplanexpiredate">
								<input type="hidden" name="crm_package_id" id="crmpackageid">
								<input type="hidden" name="crm_trial_days" id="crmtrialdays">
								<input type="hidden" name="society_id" id="society_id">
								<input type="hidden" name="Crm_request_id" id="crmrequestid">
								<input type="hidden" name="createCRM" value="createCRM">
								<div class="text-center mt-3">
									<button type="submit" class="btn btn-sm btn-primary waves-effect waves-light m-1"
										title="Create CRM">Create CRM <i class="fa fa-users"></i> </button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="rejectCrmModal">
	<div class="modal-dialog">
		<div class="modal-content border-primary">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title text-white">Reject CRM Request</h5>
				<button type="button" class="close text-white" data-dismiss="modal">&times;</button>
			</div>
			<form id="rejectCrmForm" action="controller/crmRequestController.php" method="post">
				<div class="modal-body">
					<input type="hidden" name="reject_society_id" id="reject_society_id">
					<input type="hidden" name="reject_crm_request_id" id="reject_crm_request_id">

					<div class="form-group">
						<label for="rejected_reason">Rejection Reason</label>
						<textarea name="rejected_reason" id="rejected_reason" class="form-control" required></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" name="rejectCrm" value="rejectCrm" class="btn btn-primary">Submit
						Rejection</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="viewCrmModal" tabindex="-1" role="dialog" aria-labelledby="viewCrmModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content border-primary">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title text-white" id="viewCrmModalLabel">CRM Request Details</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="crmDetailsContent">
				<div class="text-center text-muted">Loading...</div>
			</div>
		</div>
	</div>
</div>

<script>
	function openRejectCrmModal(societyId, crmRequestId) {
		document.getElementById('reject_society_id').value = societyId;
		document.getElementById('reject_crm_request_id').value = crmRequestId;
	}
</script>