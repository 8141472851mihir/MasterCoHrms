<?php
extract($_REQUEST);
$countryId = (isset($_GET['countryId']) && $_GET['countryId'] > 0) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId'], 101) : 101;
$sId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : (isset($sId) ? $d->sanitizeReportFilterIdAsInt($sId) : 0);
$cId = isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : (isset($cId) ? $d->sanitizeReportFilterIdAsInt($cId) : 0);
$sessionNames = [];
$qSession = $d->select("session_day_master", "session_day_status = 0");
while ($sessionData = mysqli_fetch_array($qSession)) {
	$sessionNames[$sessionData['session_day_id']] = $sessionData['session_day_name'];
}
if (isset($_GET['session_day_name']) && isset($_GET['csrf'])) {
	$selectedSessions = $_GET['session_day_name'];
} else if (isset($_GET['csrf'])) {
	$selectedSessions = [];
} else {
	$selectedSessions = array_keys($sessionNames);
}
$result = $d->select(
	'training_module_master tmm 
                 JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id',
	"tmm.module_type = 0 AND tmm.training_module_status = 0 AND tmpm.is_required = 1"
);
$module_count = mysqli_num_rows($result);
$session_module_count = [];
foreach ($sessionNames as $sessionId => $sessionName) {
	$session_module_count[$sessionId] = 0;
}
$sessionCountQ = $d->selectRow(
	"tmm.session_day_id, COUNT(DISTINCT tmm.training_module_id) AS cnt",
	"training_module_master tmm JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
	"tmm.module_type = 0 AND tmm.training_module_status = 0 AND tmpm.is_required = 1",
	"GROUP BY tmm.session_day_id"
);
while ($sc = mysqli_fetch_assoc($sessionCountQ)) {
	$session_module_count[(int)$sc['session_day_id']] = (int)$sc['cnt'];
}
?>
<div class="content-wrapper">
	<div class="container-fluid">
		<!-- Filters -->
		<div class="row pt-2 pb-2">
			<div class="col-sm-9">
				<h4 class="page-title">Setup Report</h4>
			</div>
		</div>
		<div class="row pt-2 pb-2">
			<div class="col-lg-12">
				<form action="" method="get" accept-charset="utf-8">
					<div class="form-group row">
						<label class="col-sm-1 col-form-label">Country <span class="required">*</span></label>
						<div class="col-sm-2 mb-3">
							<select required id="country_id" class="form-control single-select" name="countryId">
								<option value="">-- Select --</option>
								<?php
								$qc = $d->select("countries", "flag=1");
								while ($cData = mysqli_fetch_array($qc)) {
									$selected = ($cData['country_id'] == $countryId) ? "selected" : "";
									echo "<option value='{$cData['country_id']}' $selected>{$cData['name']}</option>";
								}
								?>
							</select>
						</div>

						<label class="col-sm-1 col-form-label">State</label>
						<div class="col-sm-2 mb-3">
							<select class="form-control single-select" id="state_id" name="sId">
								<option value="">All</option>
								<?php
								if (isset($countryId)) {
									$qs = $d->select("states", "country_id='$countryId'");
									while ($sData = mysqli_fetch_array($qs)) {
										$selected = (isset($_GET['sId']) && $sData['state_id'] == $_GET['sId']) ? "selected" : "";
										echo "<option value='{$sData['state_id']}' $selected>{$sData['name']}</option>";
									}
								}
								?>
							</select>
						</div>

						<label class="col-sm-1 col-form-label">City</label>
						<div class="col-sm-2 mb-3">
							<select class="form-control single-select" id="city_id" name="cId">
								<option value="">All</option>
								<?php
								if (isset($sId) && $sId > 0) {
									$qcity = $d->select("cities", "state_id='$sId'");
									while ($cityData = mysqli_fetch_array($qcity)) {
										$selected = (isset($_GET['cId']) && $cityData['city_id'] == $_GET['cId']) ? "selected" : "";
										echo "<option value='{$cityData['city_id']}' $selected>{$cityData['name']}</option>";
									}
								}
								?>
							</select>
						</div>

						<label class="col-sm-1 col-form-label">Session</label>
						<div class="col-sm-2 mb-3">
							<select multiple class="form-control multiple-select" name="session_day_name[]">
								<?php
								if (isset($_GET['fetch_type']) && $_GET['fetch_type'] >= '1' && $_GET['fetch_type'] <= '4') {
									if (isset($sessionNames[1])) {
										$sessionName = $sessionNames[1];
										echo "<option value='1' selected>{$sessionName}</option>";
									}
								} else if (isset($_GET['fetch_type']) && $_GET['fetch_type'] > '4') {
									if (isset($sessionNames[2])) {
										$sessionName = $sessionNames[2];
										echo "<option value='2' selected>{$sessionName}</option>";
									}
								} else {
									foreach ($sessionNames as $sessionId => $sessionName) {
										$selected = in_array($sessionId, $selectedSessions) ? "selected" : "";
										echo "<option value='{$sessionId}' $selected>{$sessionName}</option>";
									}
								}
								?>
							</select>
						</div>

						<label class="col-sm-1 col-form-label">Status</label>
						<div class="col-sm-2">
							<select class="form-control single-select" name="status">
								<option value="0" <?= (!isset($_GET['status']) || $_GET['status'] || isset($_GET['fetch_type']) == '0') ? "selected" : "" ?>>All</option>
								<option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? "selected" : "" ?>>Pending</option>
								<option value="2" <?= (isset($_GET['status']) && $_GET['status'] == '2') ? "selected" : "" ?>>Completed</option>
							</select>
						</div>
						<?php
						$query = $d->select("closure_date_setting");
						if (mysqli_num_rows($query) > 0) {
							$data = mysqli_fetch_array($query);
						}
						$session1_training_days_from_closure = $data['session1_training_days_from_closure'] ?? 0;
						$session1_data_receive_days_from_training = $data['session1_data_receive_days_from_training'] ?? 0;
						$session1_data_upload_days_from_receive = $data['session1_data_upload_days_from_receive'] ?? 0;
						$session1_completion_days_from_closure = $data['session1_completion_days_from_closure'] ?? 0;
						$session2_data_receive_days_from_training = $data['session2_data_receive_days_from_training'] ?? 0;
						$session2_data_upload_days_from_receive = $data['session2_data_upload_days_from_receive'] ?? 0;
						$session2_data_upload_days_from_closure = $data['session2_data_upload_days_from_closure'] ?? 0;
						?>

						<!-- <label for="fetch_type" class="col-sm-1 col-form-label">Fetch Data</label>
						<div class="col-sm-5">
							<select type="text" id="fetch_type" class="form-control single-select" name="fetch_type">
								<option value="0" <?php echo (!isset($_GET['fetch_type']) || $_GET['fetch_type'] == '0') ? "selected" : ""; ?>>All</option>
								<option value="1" <?php echo (isset($_GET['fetch_type']) && $_GET['fetch_type'] == '1') ? "selected" : ""; ?>>Closure to Setup Session 1 Training Done
									(<?php echo $session1_training_days_from_closure; ?> Days)</option>
								<option value="2" <?php echo (isset($_GET['fetch_type']) && $_GET['fetch_type'] == '2') ? "selected" : ""; ?>>Setup Session 1 Training Completion to Data Receive
									(<?php echo $session1_data_receive_days_from_training; ?> Days)</option>
								<option value="3" <?php echo (isset($_GET['fetch_type']) && $_GET['fetch_type'] == '3') ? "selected" : ""; ?>>Setup Session 1 Data Receive to Data Upload
									(<?php echo $session1_data_upload_days_from_receive; ?> Days)</option>
								<option value="4" <?php echo (isset($_GET['fetch_type']) && $_GET['fetch_type'] == '4') ? "selected" : ""; ?>>Closure to Setup Session 1 Completion
									(<?php echo $session1_completion_days_from_closure; ?> Days)</option>
								<option value="5" <?php echo (isset($_GET['fetch_type']) && $_GET['fetch_type'] == '5') ? "selected" : ""; ?>>Setup Session 2 Training to Data Receive
									(<?php echo $session2_data_receive_days_from_training; ?> Days)</option>
								<option value="6" <?php echo (isset($_GET['fetch_type']) && $_GET['fetch_type'] == '6') ? "selected" : ""; ?>>Setup Session 2 Data Receive to Data Upload
									(<?php echo $session2_data_upload_days_from_receive; ?> Days)</option>
								<option value="7" <?php echo (isset($_GET['fetch_type']) && $_GET['fetch_type'] == '7') ? "selected" : ""; ?>>Closure to Setup Session 2 Data Upload
									(<?php echo $session2_data_upload_days_from_closure; ?> Days)</option>
							</select>
						</div> -->
						<label for="" class="col-sm-1 col-form-label"></label>
						<div class="col-sm-2"><button type="submit" class="btn btn-sm btn-primary">Apply
								Filters</button></div>
					</div>
				</form>
			</div>
		</div>

		<?php if (isset($countryId)) { ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body">
							<div class="table-responsive">
								<table id="reportTable" class="table table-bordered">
									<thead>
										<tr>
											<th>#</th>
											<th>Id</th>
											<th>Company</th>
											<th>City</th>
											<th>Sales Closure Date</th>
											<th>Next Meeting Date</th>
											<!-- <th>Setup Training</th> -->
											<th>Data Receive</th>
											<th>Data Upload</th>
											<?php foreach ($selectedSessions as $sessionId) {
												// echo "<th>{$sessionNames[$sessionId]} Training</th>";
												echo "<th>{$sessionNames[$sessionId]} Data Receive</th>";
												echo "<th>{$sessionNames[$sessionId]} Data Upload</th>";
											} ?>
											<?php
											$modules = $d->select("training_module_master", "module_type = 0", "ORDER BY session_day_id ASC");

											$groupedModules = [];
											$groupedModulesDetail = [];
											while ($row = mysqli_fetch_assoc($modules)) {
												$sessionId = $row['session_day_id'];
												$groupedModules[$sessionId][] = $row['training_module_name'];
												$groupedModulesDetail[$sessionId][] = [
													'id' => $row['training_module_id'],
													'name' => $row['training_module_name']
												];
											}

											foreach ($selectedSessions as $sessionId) {
												if (isset($groupedModules[$sessionId])) {
													foreach ($groupedModules[$sessionId] as $moduleName) {
														// echo "<th>Setup Training ($moduleName)</th>";
														echo "<th>Data Receive ($moduleName)</th>";
														echo "<th>Data Upload ($moduleName)</th>";
													}
												}
											}
											?>



										</tr>
									</thead>
									<tbody>
										<?php
										$i = 1;
										$where = "society_master.created_on_society_server=1 AND country_id='$countryId'";
										if (!empty($sId))
											$where .= " AND state_id='$sId'";
										if (!empty($cId))
											$where .= " AND city_id='$cId'";
										$q = $d->selectRow(
											"society_master.*,module_training_status_master.*, transection_master.payment_mode,  transection_master.transection_amount, next_meeting.training_date AS next_training_date, next_meeting.start_time AS next_start_time, next_meeting.end_time AS next_end_time",
											"society_master LEFT JOIN module_training_status_master ON module_training_status_master.company_id = society_master.society_id LEFT JOIN transection_master  ON transection_master.society_id = society_master.society_id LEFT JOIN training_schedule_master AS next_meeting ON FIND_IN_SET(society_master.society_id, next_meeting.society_id) AND next_meeting.meeting_status = 0 AND next_meeting.training_date = ( SELECT MIN(tsm.training_date) FROM training_schedule_master AS tsm WHERE FIND_IN_SET(society_master.society_id, tsm.society_id) AND tsm.meeting_status = 0 AND tsm.training_date >= CURDATE() )",
											"$where",
											"GROUP BY society_master.society_id ORDER BY society_master.society_id DESC"
										);

										// Materialize societies + batch-load module statuses once
										$setupSocietyRows = [];
										$setupSocietyIds = [];
										while ($data = mysqli_fetch_array($q)) {
											$setupSocietyRows[] = $data;
											$setupSocietyIds[] = (int)$data['society_id'];
										}
										$allIndexedStatuses = []; // company_id => module_id => row
										if (!empty($setupSocietyIds)) {
											$setupIdsIn = implode(',', array_map('intval', $setupSocietyIds));
											$allStatusesQ = $d->select("module_training_status_master", "company_id IN ($setupIdsIn)");
											while ($statusRow = mysqli_fetch_assoc($allStatusesQ)) {
												$cid = (int)$statusRow['company_id'];
												$mid = $statusRow['module_id'];
												$allIndexedStatuses[$cid][$mid] = $statusRow;
											}
										}

										foreach ($setupSocietyRows as $data) {
											$setup_data = [];
											if ($data['setup_data'] != "") {
												$setup_data = json_decode($data['setup_data'], true) ?? [];
											}
											$allCompleted = true;
											$anyPending = false;
											$show = true;
											if (isset($fetch_type) && $fetch_type == '1') {
												$show = false;
												$salesClosureDate = $data['sales_closure_date'];
												$currentDate = date('Y-m-d');
												$dateDiff = date_diff(date_create($salesClosureDate), date_create($currentDate))->days;
												if ($dateDiff > $session1_training_days_from_closure) {
													$show = true;
													foreach ($setup_data as $key => $val) {
														if (isset($val['module_count']) && isset($val['training_completed']) && $val['module_count'] > $val['training_completed']) {
															$show = true;
														} else {
															$show = false;
														}
														break;
													}
												}
											} else if (isset($fetch_type) && $fetch_type == '2') {
												$show = false;
												$currentDate = date('Y-m-d');
												foreach ($setup_data as $key => $val) {
													if (isset($val['last_training_date']) && $val['last_training_date'] != null) {
														$lastTrainingDate = date('Y-m-d', strtotime($val['last_training_date']));
														$dateDiff = date_diff(date_create($lastTrainingDate), date_create($currentDate))->days;
														if ($dateDiff > $session1_data_receive_days_from_training) {
															if (isset($val['module_count']) && isset($val['training_completed']) && $val['module_count'] > $val['training_completed'] && isset($val['data_receive_completed']) && $val['module_count'] <= $val['data_receive_completed']) {
																$show = false;
															} else {
																$show = true;
															}
														}
													}
													break;
												}
											} else if (isset($fetch_type) && $fetch_type == '3') {
												$show = false;
												$currentDate = date('Y-m-d');
												foreach ($setup_data as $key => $val) {
													if (isset($val['last_data_receive_date']) && $val['last_data_receive_date'] != null) {
														$lastDataReceiveDate = date('Y-m-d', strtotime($val['last_data_receive_date']));
														$dateDiff = date_diff(date_create($lastDataReceiveDate), date_create($currentDate))->days;
														if ($dateDiff > $session1_data_upload_days_from_receive) {
															if (isset($val['module_count']) && isset($val['data_receive_completed']) && $val['module_count'] > $val['data_receive_completed'] && isset($val['onboarding_completed']) && $val['module_count'] <= $val['onboarding_completed']) {
																$show = false;
															} else {
																$show = true;
															}
														}
													}
													break;
												}
											} else if (isset($fetch_type) && $fetch_type == '4') {
												$show = false;
												$salesClosureDate = $data['sales_closure_date'];
												$currentDate = date('Y-m-d');
												$dateDiff = date_diff(date_create($salesClosureDate), date_create($currentDate))->days;
												if ($dateDiff > $session1_completion_days_from_closure) {
													$show = true;
													foreach ($setup_data as $key => $val) {
														if (
															$val['module_count'] > $val['training_completed'] ||
															$val['module_count'] > $val['data_receive_completed'] ||
															$val['module_count'] > $val['onboarding_completed']
														) {
															$show = true;
														} else {
															$show = false;
														}
														break;
													}
												}
											} else if (isset($fetch_type) && $fetch_type == '5') {
												$show = false;
												$currentDate = date('Y-m-d');
												$iteration = 0;
												foreach ($setup_data as $key => $val) {
													$iteration++;
													if ($iteration == 2) {
														if (isset($val['last_training_date']) && $val['last_training_date'] != null) {
															$lastTrainingDate = date('Y-m-d', strtotime($val['last_training_date']));
															$dateDiff = date_diff(date_create($lastTrainingDate), date_create($currentDate))->days;
															if ($dateDiff > $session2_data_receive_days_from_training) {
																if (isset($val['module_count']) && isset($val['training_completed']) && $val['module_count'] > $val['training_completed'] && isset($val['data_receive_completed']) && $val['module_count'] <= $val['data_receive_completed']) {
																	$show = false;
																} else {
																	$show = true;
																}
															}
														}
														break;
													}
												}
											} else if (isset($fetch_type) && $fetch_type == '6') {
												$show = false;
												$currentDate = date('Y-m-d');
												$iteration = 0;
												foreach ($setup_data as $key => $val) {
													$iteration++;
													if ($iteration == 2) {
														if (isset($val['last_data_receive_date']) && $val['last_data_receive_date'] != null) {
															$lastDataReceiveDate = date('Y-m-d', strtotime($val['last_data_receive_date']));
															$dateDiff = date_diff(date_create($lastDataReceiveDate), date_create($currentDate))->days;
															if ($dateDiff > $session2_data_upload_days_from_receive) {
																if (isset($val['module_count']) && isset($val['data_receive_completed']) && $val['module_count'] > $val['data_receive_completed'] && isset($val['onboarding_completed']) && $val['module_count'] <= $val['onboarding_completed']) {
																	$show = false;
																} else {
																	$show = true;
																}
															}
														}
														break;
													}
												}
											} else if (isset($fetch_type) && $fetch_type == '7') {
												$show = false;
												$salesClosureDate = $data['sales_closure_date'];
												$currentDate = date('Y-m-d');
												$iteration = 0;
												$dateDiff = date_diff(date_create($salesClosureDate), date_create($currentDate))->days;
												if ($dateDiff > $session2_data_upload_days_from_closure) {
													$show = true;
													foreach ($setup_data as $key => $val) {
														$iteration++;
														if ($iteration == 2) {
															if (
																$val['module_count'] > $val['training_completed'] ||
																$val['module_count'] > $val['data_receive_completed'] ||
																$val['module_count'] > $val['onboarding_completed']
															) {
																$show = true;
															} else {
																$show = false;
															}
															break;
														}
													}
												}
											}


											if ($show == true) {
												foreach ($selectedSessions as $sessionId) {
													$found = false;
													foreach ($setup_data as $session) {
														if ($session['session_day_id'] == $sessionId) {
															if (
																$session['training_completed'] < $session_module_count[$sessionId] ||
																$session['data_receive_completed'] < $session_module_count[$sessionId] ||
																$session['onboarding_completed'] < $session_module_count[$sessionId]
															) {
																$allCompleted = false;
																$anyPending = true;
															}
															$found = true;
															break;
														}
													}
													if (!$found) {
														$allCompleted = false;
														$anyPending = true;
													}
												}
												if (empty($_GET['session_day_name'])) {
													$overallCompleted = (
														$data['setup_training_status'] == '1' &&
														$data['setup_data_receive_status'] == '1' &&
														$data['setup_onboarding_status'] == '1'
													);
													$overallAnyPending = (
														$data['setup_training_status'] != '1' ||
														$data['setup_data_receive_status'] != '1' ||
														$data['setup_onboarding_status'] != '1'
													);

													if (
														(isset($_GET['status']) && $_GET['status'] == '1' && !$overallAnyPending) ||
														(isset($_GET['status']) && $_GET['status'] == '2' && !$overallCompleted)
													) {
														continue;
													}
												} else {
													if (
														(isset($_GET['status']) && $_GET['status'] == '1' && !$anyPending) ||
														(isset($_GET['status']) && $_GET['status'] == '2' && !$allCompleted)
													) {
														continue;
													}
												}

												echo "<tr>";
												echo "<td>" . $i++ . "</td>";
												echo "<td><span style='display:none;'>{$data['society_id']}</span>" . $d->short_app_name() . "_{$data['society_id']}</td>";
												echo "<td><a href='{$data['sub_domain']}apAdmin/' target='_blank'>{$data['society_name']}</a></td>";
												echo "<td>{$data['city_name']}</td>";
												$sales_closure_date = ($data['sales_closure_date'] != "") ? date("D, d-M-Y", strtotime($data['sales_closure_date'])) : "";
												echo "<td>{$sales_closure_date}</td>";
												$formattedDate = ($data['next_training_date'] != "") ? date('D, d M Y', strtotime($data['next_training_date'])) : "";
												echo "<td>{$formattedDate}</td>";

												// General statuses
												// old array =['setup_training', 'setup_data_receive', 'setup_onboarding'] 
												foreach (['setup_data_receive', 'setup_onboarding'] as $field) {
													$status = $data["{$field}_status"] == '1' ? 'Completed' : 'Pending';
													$percent = !empty($data["{$field}_percentage"]) ? $data["{$field}_percentage"] : '0';
													$color = $status == 'Completed' ? 'text-success' : 'text-danger';
													echo "<td class='{$color}'>{$status} {$percent}/{$module_count}</td>";
												}

												// Session statuses
												foreach ($selectedSessions as $sessionId) {
													$found = false;
													foreach ($setup_data as $session) {
														if ($session['session_day_id'] == $sessionId) {
															// old array ['training_completed', 'data_receive_completed', 'onboarding_completed'] 
															foreach (['data_receive_completed', 'onboarding_completed'] as $k => $field) {
																$val = $session[$field];
																$color = $val >= $session_module_count[$sessionId] ? 'green' : 'red';
																echo "<td style='color: $color'>{$val}/{$session_module_count[$sessionId]}</td>";
															}
															$found = true;
															break;
														}
													}
													if (!$found) {
														echo str_repeat("<td style='color: red;'>0/{$session_module_count[$sessionId]}</td>", 2);
													}
												}
												$societyId = $data['society_id'];

												$indexedStatuses = $allIndexedStatuses[(int)$societyId] ?? [];

												foreach ($selectedSessions as $sessionId) {
													if (isset($groupedModulesDetail[$sessionId])) {
														foreach ($groupedModulesDetail[$sessionId] as $module) {
															$moduleId = $module['id'];

															if (isset($indexedStatuses[$moduleId])) {
																$row = $indexedStatuses[$moduleId];
																$tStatus = $row['training_status'] == '1' ? "<span class='text-success'>Completed</span>" : ($row['training_status'] == '0' ? "<span class='text-danger'>Pending</span>" : ($row['training_status'] == '2' ? "Not Applicable" : '-'));

																$dStatus = $row['data_receive_status'] == '1' ? "<span class='text-success'>Completed</span>" : ($row['data_receive_status'] == '0' ? "<span class='text-danger'>Pending</span>" : ($row['data_receive_status'] == '2' ? "Not Applicable" : '-'));

																$oStatus = $row['onboarding_status'] === '1' ? "<span class='text-success'>Completed</span>" : ($row['onboarding_status'] == '0' ? "<span class='text-danger'>Pending</span>" : ($row['onboarding_status'] == '2' ? "Not Applicable" : '-'));

																// echo "<td>$tStatus</td>";
																echo "<td>$dStatus</td><td>$oStatus</td>";
															} else {
																// echo "<td><span class='text-danger'>Pending</span></td>";
																echo "<td><span class='text-danger'>Pending</span></td><td><span class='text-danger'>Pending</span></td>";
															}
														}
													}
												}
												echo "</tr>";
											}
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } else {
			echo "Select Country";
		} ?>
	</div>
</div>