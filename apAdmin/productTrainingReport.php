<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

extract($_REQUEST);
$countryId = (isset($_GET['countryId']) && $_GET['countryId'] > 0) ? $d->sanitizeReportFilterIdAsInt($_GET['countryId'], 101) : 101;
$sId = isset($_GET['sId']) ? $d->sanitizeReportFilterIdAsInt($_GET['sId']) : (isset($sId) ? $d->sanitizeReportFilterIdAsInt($sId) : 0);
$cId = isset($_GET['cId']) ? $d->sanitizeReportFilterIdAsInt($_GET['cId']) : (isset($cId) ? $d->sanitizeReportFilterIdAsInt($cId) : 0);
$include_subtopics = isset($_GET['subtopic_mode']) && $_GET['subtopic_mode'] == '1';

$allParticipants = $d->select("training_participants_type", "1");
$participantList = [];
$selected_participants = [];

if (isset($_GET['participant_name'])) {
	$selected_participants = array_map('intval', $_GET['participant_name']);
}

while ($p = mysqli_fetch_array($allParticipants)) {
	$participantId = $p['participants_type_id'];
	if (empty($selected_participants) || in_array($participantId, $selected_participants)) {
		$participantList[] = $p;
	}
}

// Get topics with their modules
$topicsQuery = $d->selectRow(
	"tmt.topic_id, tmt.topic_name, tmt.participant_type,
	 tmm.training_module_id, tmm.training_module_name, tmm.module_priority,
	 tmpm.priority_name, tmpm.is_required",
	"training_module_topics tmt
	 LEFT JOIN training_module_master tmm ON tmt.topic_id = tmm.topic_id 
	  AND tmm.module_type = 1 AND tmm.training_module_status = 0
	 LEFT JOIN training_module_priority_master tmpm ON tmm.module_priority = tmpm.priority_id",
	"tmt.topic_status = 1 AND tmt.topic_type = '0' AND tmpm.is_required = 1",
	"ORDER BY tmt.topic_name ASC, tmm.training_module_id ASC"
);

$topics = [];
$moduleTopicMap = [];
$participantModuleCounts = [];
while ($row = mysqli_fetch_array($topicsQuery)) {
	$topicId = $row['topic_id'];
	$moduleId = $row['training_module_id'];
	$participantType = $row['participant_type'];
	
	if (!isset($topics[$topicId])) {
		$topics[$topicId] = [
			'topic_id' => $topicId,
			'topic_name' => $row['topic_name'],
			'participant_type' => $participantType,
			'modules' => []
		];
	}
	
	if (!empty($moduleId)) {
		$topics[$topicId]['modules'][$moduleId] = [
			'training_module_id' => $moduleId,
			'training_module_name' => $row['training_module_name'],
			'module_priority' => $row['module_priority'],
			'priority_name' => $row['priority_name']
		];
		$moduleTopicMap[$moduleId] = $topicId;
		
		// Count modules per participant type
		if (!isset($participantModuleCounts[$participantType])) {
			$participantModuleCounts[$participantType] = 0;
		}
		$participantModuleCounts[$participantType]++;
	}
}

// Get training status data
$trainingStatusData = $d->selectRow(
	"s.society_id, s.company_full_name, p.participants_type_id, p.participant_name, 
	 m.training_module_id, m.training_module_name, m.module_type, m.module_priority, 
	 COALESCE(bts.training_status, '0') AS training_status, bts.training_date",
	"society_master s 
	 CROSS JOIN training_participants_type p 
	 CROSS JOIN training_module_master m 
	 LEFT JOIN batch_training_status_master bts 
	 ON bts.company_id = s.society_id 
	 AND bts.participant_id = p.participants_type_id 
	 AND bts.module_id = m.training_module_id",
	"m.training_module_status = 0 AND m.module_type='1'",
	"ORDER BY s.society_id, p.participants_type_id, m.training_module_id"
);

$modules_status = [];
while ($row = mysqli_fetch_array($trainingStatusData)) {
	$company_id = $row['society_id'];
	$participant_id = $row['participants_type_id'];
	$module_id = $row['training_module_id'];
	$modules_status[$company_id][$participant_id][$module_id] = [
		'training_status' => $row['training_status'],
		'training_date'   => $row['training_date'],
	];
}
?>

<div class="content-wrapper">
	<div class="container-fluid">
		<!-- Header -->
		<div class="row">
			<div class="col-12">
				<h3 class="page-title mb-4">
					<i class="fas fa-chart-line"></i> Product Training Report
				</h3>
			</div>
		</div>

		<!-- Filters Card -->
		<div class="row mb-4">
			<div class="col-12">
				<div class="card shadow-sm">
					<div class="card-body">
						<form action="" method="get" accept-charset="utf-8">
							<div class="row">
								<!-- Country -->
								<div class="col-md-3 mb-3">
									<label class="form-label fw-bold">Country <span class="text-danger">*</span></label>
									<select required id="country_id" class="form-select single-select" name="countryId">
										<option value="">-- Select Country --</option>
										<?php
										$qc = $d->select("countries", "flag=1");
										while ($cData = mysqli_fetch_array($qc)) {
											$selected = ($cData['country_id'] == $countryId) ? "selected" : "";
											echo "<option value='{$cData['country_id']}' $selected>{$cData['name']}</option>";
										}
										?>
									</select>
								</div>

								<!-- State -->
								<div class="col-md-3 mb-3">
									<label class="form-label fw-bold">State</label>
									<select class="form-select single-select" id="state_id" name="sId">
										<option value="">All States</option>
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

								<!-- City -->
								<div class="col-md-3 mb-3">
									<label class="form-label fw-bold">City</label>
									<select class="form-select single-select" id="city_id" name="cId">
										<option value="">All Cities</option>
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

								<!-- Participant Name -->
								<div class="col-md-3 mb-3">
									<label class="form-label fw-bold">Participants</label>
									<select multiple class="form-select multiple-select" name="participant_name[]" style="min-height: 100px;">
										<?php
										$participantReset = $d->select("training_participants_type", "1");
										while ($participant = mysqli_fetch_array($participantReset)) {
											$participantId = $participant['participants_type_id'];
											$isSelected = empty($selected_participants) || in_array($participantId, $selected_participants);
											$selected = $isSelected ? "selected" : "";
											echo "<option value='$participantId' $selected>" . htmlspecialchars($participant['participant_name']) . "</option>";
										}
										?>
									</select>
									<small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
								</div>
							</div>

							<div class="row">
								<!-- Status -->
								<div class="col-md-3 mb-3">
									<label class="form-label fw-bold">Training Status</label>
									<select class="form-select single-select" name="status">
										<option value="0" <?= (!isset($_GET['status']) || $_GET['status'] == '0') ? "selected" : "" ?>>All Status</option>
										<option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? "selected" : "" ?>>Pending</option>
										<option value="2" <?= (isset($_GET['status']) && $_GET['status'] == '2') ? "selected" : "" ?>>Completed</option>
									</select>
								</div>

								<!-- Subtopic Detail -->
								<!-- <div class="col-md-3 mb-3">
									<label class="form-label fw-bold">Subtopic Details</label>
									<select class="form-select single-select" name="subtopic_mode">
										<option value="0" <?= ($include_subtopics === false) ? 'selected' : '' ?>>Hide Subtopic Details</option>
										<option value="1" <?= ($include_subtopics === true) ? 'selected' : '' ?>>Show Subtopic Details</option>
									</select>
								</div> -->

								<!-- Action Buttons -->
								<div class="col-md-6 mb-3 d-flex align-items-end">
									<button type="submit" class="btn btn-primary  me-2">Apply Filters
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<?php if (isset($countryId)) { ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body">
							<div class="table-responsive">
								<table id="reportTableNew" class="table table-bordered">
									<thead>
										<tr>
											<th>#</th>
											<th>Id</th>
											<th>Company</th>
											<th>Account Type</th>
											<th>City</th>
											<th>State</th>
											<th>Industry Type</th>
											<th>Secretary Name</th>
											<th>Secretary Mobile No.</th>
											<th>Secretary Email</th>
											<th>Employee Registration Limit</th>
											<th>Employee Tracking Limit</th>
											<th>Plan Name</th>
											<th>Sales closure Date</th>
											<th>Training Status</th>
											<?php
											foreach ($participantList as $participant_type) {
												echo "<th>{$participant_type['participant_name']} Training</th>";
											}
											// Add columns for topics and their modules - Only show modules from topics associated with selected participants
											foreach ($topics as $topic) {
												$topicParticipantType = $topic['participant_type'];
												
												// Check if this topic's participant type is in the selected participants list
												$topicParticipantSelected = false;
												if (count($participantList) > 0) {
													foreach ($participantList as $participant_type) {
														if ($participant_type['participants_type_id'] == $topicParticipantType) {
															$topicParticipantSelected = true;
															break;
														}
													}
												}
												
												// Only show modules if the topic's participant type is selected
												if ($topicParticipantSelected) {
													foreach ($topic['modules'] as $module) {
														echo "<th>{$topic['topic_name']} - {$module['training_module_name']}</th>";
													}
												}
											}
											?>
										</tr>
									</thead>
									<tbody>
										<?php
										$i = 1;
										$where = "created_on_society_server=1 AND society_master.country_id='$countryId'";
										if (!empty($sId))
											$where .= " AND society_master.state_id='$sId'";
										if (!empty($cId))
											$where .= " AND society_master.city_id='$cId'";

										$q = $d->selectRow(
											"society_master.*, society_master.account_type,society_master.industry_type, society_master.secretary_name,society_master.secretary_mobile,society_master.secretary_email,society_master.employee_registration_limit,society_master.employee_tracking_limit,transection_master.payment_mode,transection_master.package_name, transection_master.transection_amount, states.name AS state_name,business_entity_master.name AS industry_name,manage_plan.plan_name,MIN(batch_training_status.training_date) AS first_training_date, CASE WHEN society_master.created_on_society_server = 1 THEN society_master.society_name ELSE NULL END AS society_name",
											"society_master LEFT JOIN transection_master ON transection_master.society_id = society_master.society_id
											LEFT JOIN batch_training_status ON batch_training_status.company_id=society_master.society_id
											LEFT JOIN states ON states.state_id=society_master.state_id
											LEFT JOIN business_entity_master ON business_entity_master.b_id= society_master.industry_type 
											LEFT JOIN manage_plan ON manage_plan.plan_value=society_master.package_id",
											$where,
											"GROUP BY society_master.society_id ORDER BY society_id DESC"
										);

										$selectedStatus = $_GET['status'] ?? '0';

										while ($data = mysqli_fetch_array($q)) {
											$training_data = json_decode($data['training_data'], true) ?? [];
											$percentage = !empty($data['training_percentage']) ? $data['training_percentage'] : 0;

											// Calculate overall status
											if (count($participantList) > 0) {
												$pendingExists = false;
												$totalModules = 0;

												foreach ($participantList as $participant_type) {
													$participantId = $participant_type['participants_type_id'];
													$found = false;
													
													if (!empty($training_data)) {
														foreach ($training_data as $participant) {
															if ($participant['participant_id'] == $participantId) {
																$found = true;
																break;
															}
														}
													}
													
													if (!$found) {
														$pendingExists = true;
													}
												}

												$modeStatus = (!$pendingExists) ? 'completed' : 'pending';
											} else {
												$modeStatus = ($data['training_status'] == '1') ? 'completed' : 'pending';
											}

											$showRow = false;
											if ($selectedStatus == '0') {
												$showRow = true;
											} elseif ($selectedStatus == '2' && $modeStatus == 'completed') {
												$showRow = true;
											} elseif ($selectedStatus == '1' && $modeStatus == 'pending') {
												$showRow = true;
											}

											if (!$showRow) {
												continue;
											}

											echo "<tr>";
											echo "<td>" . $i++ . "</td>";
											echo "<td><span style='display:none;'>{$data['society_id']}</span>" . $d->short_app_name() . "_{$data['society_id']}</td>";
											echo "<td><a href='{$data['sub_domain']}apAdmin/' target='_blank'>{$data['society_name']}</a></td>";
											$accountTypeLabel = ($data['account_type'] == 1) ? 'Key Account' : 'Normal Account';
											echo "<td>{$accountTypeLabel}</td>";
											echo "<td>{$data['city_name']}</td>";
											echo "<td>{$data['state_name']}</td>";
											echo "<td>{$data['industry_name']}</td>";
											echo "<td>{$data['secretary_name']}</td>";
											echo "<td>{$data['secretary_mobile']}</td>";
											echo "<td>{$data['secretary_email']}</td>";
											echo "<td>{$data['employee_registration_limit']}</td>";
											echo "<td>{$data['employee_tracking_limit']}</td>";
											echo "<td>{$data['plan_name']}</td>";

											if (!empty($data['sales_closure_date'])) {
												$formattedDate = date("D, d-M-Y", strtotime($data['sales_closure_date']));
												echo "<td>$formattedDate</td>";
											} else {
												echo "<td></td>";
											}
											
											// Calculate total modules across all participant types
											$totalModules = 0;
											foreach ($participantModuleCounts as $pid => $count) {
												$totalModules += $count;
											}
											$completedModules = !empty($data['training_percentage']) ? intval($data['training_percentage']) : 0;
											
											$trainingStatus = (($data['training_status'] == '1') ? 'Completed' : 'Pending');
											$colorClass = ($trainingStatus == 'Completed') ? "text-success" : "text-danger";
											$total_percentage = ($totalModules > 0) ? min(100, max(0, round(($completedModules / $totalModules) * 100))) : 0;
											echo "<td><span class='$colorClass'>$trainingStatus</span><br>{$completedModules}/{$totalModules} ({$total_percentage}%)</td>";

                                            // Display per-participant training progress
											if (count($participantList) > 0) {
												foreach ($participantList as $participant_type) {
													$participantId = $participant_type['participants_type_id'];
													$found = false;
													$completed = 0;
													$ptotal = 0;
													
													if (!empty($training_data)) {
														foreach ($training_data as $participant) {
															if ($participant['participant_id'] == $participantId) {
																$completed = isset($participant['completed_percentage']) ? intval($participant['completed_percentage']) : 0;
																$ptotal = isset($participant['total_participant_modules']) ? intval($participant['total_participant_modules']) : 0;
																$found = true;
																break;
															}
														}
													}
													
													// If not found, use the participant module counts we computed
													if (!$found) {
														$pid = intval($participantId);
														$ptotal = isset($participantModuleCounts[$pid]) ? $participantModuleCounts[$pid] : 0;
														$completed = 0;
													}
													
													$pIsCompleted = ($ptotal == 0) ? true : ($completed >= $ptotal);
													$pColor = $pIsCompleted ? 'green' : 'red';
													$pPercentage = ($ptotal > 0) ? min(100, max(0, round(($completed / $ptotal) * 100))) : 0;
													echo "<td style='color: $pColor;'>{$completed}/{$ptotal} ({$pPercentage}%)</td>";
												}
											}

											// Topic-Module status columns - Only show modules from topics associated with selected participants
											foreach ($topics as $topic) {
												$topicParticipantType = $topic['participant_type']; // Get the participant type this topic is for
												
												// Check if this topic's participant type is in the selected participants list
												$topicParticipantSelected = false;
												if (count($participantList) > 0) {
													foreach ($participantList as $participant_type) {
														if ($participant_type['participants_type_id'] == $topicParticipantType) {
															$topicParticipantSelected = true;
															break;
														}
													}
												}
												
												// Only show modules if the topic's participant type is selected
												if ($topicParticipantSelected) {
													foreach ($topic['modules'] as $module) {
														$moduleId = $module['training_module_id'];
														$society_id = $data['society_id'];
														$statusText = "";
														
														// Only show status for the participant type associated with this topic
														if (!empty($topicParticipantType)) {
															$participantId = intval($topicParticipantType);
															$this_module_status = $modules_status[$society_id][$participantId][$moduleId]['training_status'];
															
															if ($this_module_status == '0') {
																$this_module_status_display = 'Pending';
																$this_module_training_date = "";
															} else if ($this_module_status == '1') {
																$this_module_status_display = 'Completed';
																$this_module_training_date = $modules_status[$society_id][$participantId][$moduleId]['training_date'];
																if (!empty($this_module_training_date) && $this_module_training_date !== '0000-00-00 00:00:00' && strtotime($this_module_training_date)) {
																	$this_module_training_date = date('d M Y, h:i A', strtotime($this_module_training_date));
																} else {
																	$this_module_training_date = '';
																}
															} else if ($this_module_status == '2') {
																$this_module_status_display = 'N/A';
																$this_module_training_date = $modules_status[$society_id][$participantId][$moduleId]['training_date'];
																if (!empty($this_module_training_date) && $this_module_training_date !== '0000-00-00 00:00:00' && strtotime($this_module_training_date)) {
																	$this_module_training_date = date('d M Y, h:i A', strtotime($this_module_training_date));
																} else {
																	$this_module_training_date = '';
																}
															}
															
															$statusText = $this_module_status_display;
															if ($this_module_training_date != "") {
																$statusText .= " (" . $this_module_training_date . ")";
															}
														} else {
															$statusText = "N/A";
														}
														
														echo "<td style='vertical-align: top;'>" . $statusText . "</td>";
													}
												}
											}
											
											echo "</tr>";
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

<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Add any necessary JavaScript here
	});
</script>