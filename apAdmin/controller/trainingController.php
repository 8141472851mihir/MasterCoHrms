<?php
include '../common/objectController.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
if (isset($_POST) && !empty($_POST)) {
	if (isset($scheduleTraining) && $scheduleTraining == 'scheduleTraining') {
		$society_id = ($society_id) ? implode(',', $society_id) : "";
		$m->set_data('society_id', $society_id);
		$m->set_data('host_id', $host_name);
		$m->set_data('training_date', $training_date);
		$m->set_data('start_time', $_POST['start_time']);
		$m->set_data('end_time', $_POST['end_time']);
		$m->set_data('session_id', $session_id);
		$m->set_data('session_day_id', $session_day_id);
		$session_name = isset($_POST['session_name']) ? $_POST['session_name'] : '';
		if (empty($session_name) && !empty($session_id)) {
			$query = $d->select("session_master", "session_id='$session_id'");
			if ($row = mysqli_fetch_assoc($query)) {
				$session_name = $row['session_name'];
			}
		}
		$day = date('D', strtotime($training_date));
		$date = date('Y_m_d', strtotime($training_date));

		$setup_meeting_name = $session_name . "_" . $day . "_" . $date;
		$m->set_data('setup_meeting_name', $setup_meeting_name);

		$data = array(
			'society_id' => $m->get_data('society_id'),
			'host_id' => $m->get_data('host_id'),
			'training_date' => $m->get_data('training_date'),
			'start_time' => $m->get_data('start_time'),
			'end_time' => $m->get_data('end_time'),
			'session_id' => $m->get_data('session_id'),
			'session_day_id' => $m->get_data('session_day_id'),
			'setup_meeting_name' => $m->get_data('setup_meeting_name'),
		);
		if (isset($training_schedule_master_id) && $training_schedule_master_id != '') {
			$data['updated_date'] = date("Y-m-d H:i:s");
			$data['updated_by'] = $bms_admin_id;
			$query = $d->update("training_schedule_master", $data, "training_schedule_master_id='$training_schedule_master_id'");
			$successMessage = "Training scheduled updated successfully";
			$redirectLocation = "../manageTraining";
		} else {
			$data['created_date'] = date("Y-m-d H:i:s");
			$data['created_by'] = $bms_admin_id;
			$query = $d->insert("training_schedule_master", $data);
			$successMessage = "Training scheduled successfully";
			$redirectLocation = "../manageTraining";
		}
		if ($query === true) {
			$_SESSION['msg'] = $successMessage;
		} else {
			$_SESSION['msg1'] = "Something went wrong!";
		}
		header("Location: $redirectLocation");
		exit();
	} else if (isset($deleteScheduleTraining) && $deleteScheduleTraining == 'deleteScheduleTraining' && isset($training_schedule_master_id) && $training_schedule_master_id != '') {
		$d1 = array(
			'schedule_status' => 1,
		);
		$que = $d->update("training_schedule_master", $d1, "training_schedule_master_id='$training_schedule_master_id'");
		if ($que == true) {
			$_SESSION['msg'] = "Scheduled Training deleted successfully";
			header("location:../manageTraining");
			exit();
		} else {
			$_SESSION['msg'] = "something Went Wrong!!";
			header("location:../manageTraining");
			exit();
		}
	} else if (isset($changeTrainers) && $changeTrainers == 'changeTrainers' && isset($slot_id) && $slot_id != '') {
		$update = array(
			'trainer_id' => $trainer_id,
		);
		$updateQuery = $d->update("batch_slot_master", $update, "slot_id='$slot_id'");
		if ($updateQuery == true) {
			$_SESSION['msg'] = "Trainer change successfully";
			header("Location: ../manageTrainingSlots?activeTab=$activeTab");
			exit();
		} else {
			$_SESSION['msg'] = "something Went Wrong!!";
			header("location:../manageTrainingSlots");
			exit();
		}
	} else if (isset($scheduleModule) && $scheduleModule == 'scheduleModule') {
		$m->set_data('training_module_name', $training_module_name);
		$m->set_data('module_type', $module_type);
		$m->set_data('module_priority', $priority_id);
		$module_url = isset($_POST['module_url']) ? trim($_POST['module_url']) : null;
		$estimated_minutes = isset($_POST['estimated_minutes']) && $_POST['estimated_minutes'] !== '' ? intval($_POST['estimated_minutes']) : null;
		$completion_days = isset($_POST['completion_days']) && $_POST['completion_days'] !== '' ? intval($_POST['completion_days']) : null;
		$topic_id = isset($_POST['topic_id']) && $_POST['topic_id'] !== '' ? intval($_POST['topic_id']) : null;
		if ($_POST['module_type'] == '1' || $_POST['module_type'] == '2') {
			// Training (HRMS) or CRM modules don't have sessions
			$m->set_data('session_id', 'N/A');
		} else {
			if (isset($_POST['session_id']) && is_array($_POST['session_id'])) {
				$session_id = implode(',', $session_id);
			} else {
				$session_id = $_POST['session_id'];
			}
			$session_day_id = $_POST['session_day_id'];
			$m->set_data('session_day_id', $session_day_id);
			$m->set_data('session_id', $session_id);
		}
		$data = array(
			'training_module_name' => $m->get_data('training_module_name'),
			'session_id' => $m->get_data('session_id'),
			'module_type' => $m->get_data('module_type'),
			'module_priority' => $m->get_data('module_priority'),
			'session_day_id' => $m->get_data('session_day_id'),
			'completion_days' => ($_POST['module_type'] == '1' || $_POST['module_type'] == '2') ? $completion_days : null,
			'module_url' => ($module_url !== '') ? $module_url : null,
			'estimated_minutes' => $estimated_minutes,
			'topic_id' => ($_POST['module_type'] == '1' || $_POST['module_type'] == '2') ? $topic_id : null,
		);
		if ($_POST['module_type'] == '1' || $_POST['module_type'] == '2') {
			// Calculate order separately for HRMS training (1) and CRM (2)
			$moduleTypeForOrder = intval($_POST['module_type']);
			$maxRes = $d->selectRow("MAX(training_module_order) AS max_order", "training_module_master", "module_type = $moduleTypeForOrder");
			$nextOrder = 1;
			if ($maxRes && mysqli_num_rows($maxRes) > 0) {
				$row = mysqli_fetch_assoc($maxRes);
				$maxOrder = isset($row['max_order']) ? intval($row['max_order']) : 0;
				$nextOrder = $maxOrder + 1;
			}
			$data['training_module_order'] = $nextOrder;
		}
		$query = $d->insert("training_module_master", $data);
		$redirectLocation = getManageTrainingModuleRedirectUrl();
		if ($query === true) {
			$_SESSION['msg'] = "Training scheduled successfully";
		} else {
			$_SESSION['msg'] = "Something went wrong!";
		}
		header("Location: $redirectLocation");
		exit();
	} else if (isset($scheduleModule) && $scheduleModule == 'scheduleModuleEdit') {
		$m->set_data('training_module_name', $_POST['training_module_name']);
		$completion_days = isset($_POST['completion_days']) && $_POST['completion_days'] !== '' ? intval($_POST['completion_days']) : null;
		$module_url = isset($_POST['module_url']) ? trim($_POST['module_url']) : null;
		$estimated_minutes = isset($_POST['estimated_minutes']) && $_POST['estimated_minutes'] !== '' ? intval($_POST['estimated_minutes']) : null;
		$topic_id = isset($_POST['topic_id']) && $_POST['topic_id'] !== '' ? intval($_POST['topic_id']) : null;
		if ($_POST['module_type'] == '1' || $_POST['module_type'] == '2') {
			// Training (HRMS) or CRM modules don't have sessions
			$m->set_data('session_id', 'N/A');
		} else {
			if (isset($_POST['session_id']) && is_array($_POST['session_id'])) {
				$session_id = implode(',', $_POST['session_id']);
			} else {
				$session_id = $_POST['session_id'];
			}

			$m->set_data('session_id', $session_id);
		}
		$m->set_data('module_type', $_POST['module_type']);
		$m->set_data('module_priority', $priority_id);
		$m->set_data('session_day_id', $session_day_id);
		$data = array(
			'training_module_name' => $m->get_data('training_module_name'),
			'session_id' => $m->get_data('session_id'),
			'module_type' => $m->get_data('module_type'),
			'module_priority' => $m->get_data('module_priority'),
			'session_day_id' => $m->get_data('session_day_id'),
			'completion_days' => ($_POST['module_type'] == '1' || $_POST['module_type'] == '2') ? $completion_days : null,
			'module_url' => ($module_url !== '') ? $module_url : null,
			'estimated_minutes' => $estimated_minutes,
			'topic_id' => ($_POST['module_type'] == '1' || $_POST['module_type'] == '2') ? $topic_id : null,
		);
		$query = $d->update("training_module_master", $data, "training_module_id='$editId'");
		$redirectLocation = getManageTrainingModuleRedirectUrl();
		if ($query === true) {
			$_SESSION['msg'] = "Training module updated successfully";
		} else {
			$_SESSION['msg'] = "Something went wrong!";
		}
		header("Location: $redirectLocation");
		exit();
	} else if (isset($getModule) && $getModule == 'getModule' && isset($session_id) && $session_id != '') {
		$session_id = intval($session_id);
		$result = $d->selectRow(
			"training_module_master.training_module_name",
			"training_module_master LEFT JOIN session_master ON 
			FIND_IN_SET(session_master.session_id, training_module_master.session_id) > 0",
			"session_master.session_id = '$session_id'"
		);

		if ($result) {
			$modules = [];
			while ($row = mysqli_fetch_assoc($result)) {
				$modules[] = $row['training_module_name'];
			}

			if (!empty($modules)) {
				$modules_data = implode(', ', $modules);
				echo json_encode($modules_data);
			} else {
				$_SESSION['msg1'] = 'Something went wrong';
				header("Location: ../manageTraining");
				exit();
			}
		} else {
			$_SESSION['msg1'] = 'Something went wrong';
			header("Location: ../manageTraining");
			exit();
		}
	} else if ($_POST['action'] == "fetchMeeting") {
		$session_id = isset($_POST['session_id']) ? $d->sanitizeActionIdAsInt($_POST['session_id']) : 0;
		$society_id = isset($_POST['society_id']) ? $d->sanitizeActionIdAsInt($_POST['society_id']) : 0;
		$training_date = isset($_POST['trainingDate']) && !empty($_POST['trainingDate'])
			? date('Y-m-d', strtotime($_POST['trainingDate']))
			: null;
		$q = $d->selectRow(
			"training_schedule_master.training_schedule_master_id, 
			 training_schedule_master.setup_meeting_name AS meeting_name,
			 training_schedule_master.society_id,
			 admin_table.admin_name AS host_name",
			"training_schedule_master
			 LEFT JOIN bms_admin_master AS admin_table ON training_schedule_master.host_id = admin_table.admin_id",
			"training_schedule_master.session_id = '$session_id' 
			 AND training_schedule_master.training_date = '$training_date' 
			 AND training_schedule_master.schedule_status = '0'"
		);
		$result = [];
		if ($q && mysqli_num_rows($q) > 0) {
			while ($row = mysqli_fetch_assoc($q)) {
				$companyIds = array_filter(explode(',', $row['society_id']));
				$flag = in_array($society_id, $companyIds) ? '1' : '0';
				$result[] = [
					"training_schedule_master_id" => $row['training_schedule_master_id'],
					"flag" => $flag,
					"meeting_name" => $row['meeting_name'] ?? "N/A",
					"host_name" => $row['host_name'] ?? "N/A"
				];
			}
			echo json_encode($result);
		} else {
			echo json_encode($result);
		}
	} else if ($_POST['action'] == "updateMeeting") {
		$training_schedule_master_id = intval($_POST['training_schedule_master_id']);
		$new_society_id = $d->sanitizeActionIdAsInt($_POST['society_id'] ?? 0);
		$host_id = intval($_POST['host_id']);
		$meeting_name = $_POST['meeting_name'];
		$existingSocieties = $d->selectRow(
			"society_id",
			"training_schedule_master",
			"training_schedule_master_id = $training_schedule_master_id"
		);
		if ($existingSocieties && mysqli_num_rows($existingSocieties) > 0) {
			$row = mysqli_fetch_assoc($existingSocieties);
			$existingSocietyIds = explode(",", $row['society_id']);
			if (!in_array($new_society_id, $existingSocietyIds)) {
				$existingSocietyIds[] = $new_society_id;
			}
			$updatedSocieties = implode(",", $existingSocietyIds);
		} else {
			$updatedSocieties = $new_society_id;
		}
		$data = [
			"society_id" => $updatedSocieties
		];
		$updateQuery = $d->update("training_schedule_master", $data, "training_schedule_master_id = '$training_schedule_master_id'");
		if ($updateQuery) {
			$_SESSION['msg'] = 'Scheduled Training updated successfully';
			exit();
		} else {
			$_SESSION['msg1'] = 'Something went wrong';
			exit();
		}
	} else if ($_POST['action'] == "insertMeeting") {
		$m->set_data('session_id', $_POST['session_id']);
		$m->set_data('training_date', date('Y-m-d', strtotime($_POST['trainingDate'])));
		$m->set_data('society_id', $_POST['society_id']);
		$m->set_data('host_id', $_POST['bms_admin_id']);
		$m->set_data('session_day_id', $_POST['session_day_id']);
		$m->set_data('start_time', $_POST['start_time']);
		$m->set_data('end_time', $_POST['end_time']);
		$session_name = isset($_POST['session_name']) ? $_POST['session_name'] : '';
		if (empty($session_name) && !empty($session_id)) {
			$query = $d->select("session_master", "session_id='$session_id'");
			if ($row = mysqli_fetch_assoc($query)) {
				$session_name = $row['session_name'];
			}
		}
		$day = date('D', strtotime($_POST['trainingDate']));
		$date = date('Y_m_d', strtotime($_POST['trainingDate']));
		$setup_meeting_name = $session_name . "_" . $day . "_" . $date;
		$m->set_data('setup_meeting_name', $setup_meeting_name);
		$data = array(
			"session_id" => $m->get_data('session_id'),
			"training_date" => $m->get_data('training_date'),
			"society_id" => $m->get_data('society_id'),
			"host_id" => $m->get_data('host_id'),
			"session_day_id" => $m->get_data('session_day_id'),
			"start_time" => $m->get_data('start_time'),
			"end_time" => $m->get_data('end_time'),
			"setup_meeting_name" => $m->get_data('setup_meeting_name')
		);
		$data['created_date'] = date("Y-m-d H:i:s");
		$data['created_by'] = $bms_admin_id;
		$insertQuery = $d->insert("training_schedule_master", $data);

		if ($insertQuery) {
			$_SESSION['msg'] = "Scheduled Training inserted successfully";
			exit();
		} else {
			$_SESSION['msg1'] = 'Something went wrong';
			exit();
		}
	} else if (isset($changeScheduleTrainers) && $changeScheduleTrainers == 'changeScheduleTrainers' && isset($training_schedule_master_id) && $training_schedule_master_id != '') {
		$host_id = isset($_POST['host_id']) ? $_POST['host_id'] : null;
		if (!$host_id) {
			$_SESSION['msg'] = "host id is missing";
			header("Location: ../manageTraining");
			exit();
		}
		$update = array(
			'host_id' => $host_id,
		);
		$updateQuery = $d->update("training_schedule_master", $update, "training_schedule_master_id='$training_schedule_master_id'");
		if ($updateQuery == true) {
			$_SESSION['msg'] = "Trainer change successfully";
			header("Location: ../manageTraining");
			exit();
		} else {
			$_SESSION['msg'] = "something Went Wrong!!";
			header("location:../manageTraining");
			exit();
		}
	} else if ($_POST['action'] == "fetchBatchMeeting") {
		$society_id = $d->sanitizeActionIdAsInt($_POST['society_id'] ?? 0);
		$training_date = isset($_POST['trainingDate']) && !empty($_POST['trainingDate'])
			? date('Y-m-d', strtotime($_POST['trainingDate']))
			: null;

		$q = $d->selectRow(
			"batch_slot_master.slot_id, 
			 batch_slot_master.date,
			 batch_slot_master.company_id,
			 batch_slot_master.from_time,
			 batch_slot_master.to_time,
			 batch_slot_master.slot_name AS meeting_name,
			 admin_table.admin_name AS host_name",
			"batch_slot_master
			 LEFT JOIN bms_admin_master AS admin_table ON batch_slot_master.trainer_id = admin_table.admin_id",
			"batch_slot_master.date >= '$training_date' AND meeting_status = 0",
			"LIMIT 50"
		);
		$result = [];
		if ($q && mysqli_num_rows($q) > 0) {
			while ($row = mysqli_fetch_assoc($q)) {
				$companyIds = array_filter(explode(',', $row['company_id']));
				$flag = in_array($society_id, $companyIds) ? '1' : '0';
				$result[] = [
					"slot_id" => $row['slot_id'],
					"flag" => $flag,
					"meeting_date" => date("D, d-M-Y", strtotime($row['date'])) . "<br>" . ' (' .
						date("g:i A", strtotime($row['from_time'])) . ' - ' .
						date("g:i A", strtotime($row['to_time'])) . ')',
					"meeting_name" => $row['meeting_name'] ?? "",
					"host_name" => $row['host_name'] ?? ""
				];
			}
			echo json_encode($result);
		} else {
			echo json_encode($result);
		}
	} else if ($_POST['action'] == "assignBatchMeeting") {
		$meetingType = $_POST['meeting_type'];
		$society_id = $d->sanitizeActionIdAsInt($_POST['society_id'] ?? 0);
		$reference_society_ids = isset($_POST['reference_society_id']) ? $_POST['reference_society_id'] : [];
		$company_type = $_POST['company_type'];
		$mainSocieties = explode(',', $society_id);
		$refSocieties = is_array($reference_society_ids) ? $reference_society_ids : explode(',', $reference_society_ids);
		if ($company_type == '1' && empty($refSocieties)) {
			$refSocieties = $mainSocieties;
		}

		$mergeSlotCompanies = function ($existingCompanyIds, $existingReferenceIds) use ($mainSocieties, $refSocieties, $company_type) {
			$existingCompanyIds = array_values(array_filter($existingCompanyIds));
			$existingReferenceIds = array_values(array_filter($existingReferenceIds));
			foreach ($mainSocieties as $mainId) {
				$mainId = trim((string)$mainId);
				if ($mainId !== '' && !in_array($mainId, $existingCompanyIds, true)) {
					$existingCompanyIds[] = $mainId;
				}
			}
			if ($company_type == '1') {
				foreach ($refSocieties as $refId) {
					$refId = trim((string)$refId);
					if ($refId === '') {
						continue;
					}
					if (!in_array($refId, $existingCompanyIds, true)) {
						$existingCompanyIds[] = $refId;
					}
					if (!in_array($refId, $existingReferenceIds, true)) {
						$existingReferenceIds[] = $refId;
					}
				}
			}
			return [
				'company_id' => implode(',', array_unique($existingCompanyIds)),
				'reference_company_ids' => implode(',', array_unique($existingReferenceIds)),
			];
		};

		if ($meetingType == "1") {
			$selectedSlots = isset($_POST['selected_batch_meeting']) ? (array)$_POST['selected_batch_meeting'] : [];
			$slotIdsInt = array_values(array_unique(array_filter(array_map('intval', $selectedSlots))));
			if (!empty($slotIdsInt)) {
				$slotsIn = implode(',', $slotIdsInt);
				$slotsQ = $d->selectRow(
					"slot_id, company_id, reference_company_ids",
					"batch_slot_master",
					"slot_id IN ($slotsIn)"
				);
				while ($row = mysqli_fetch_assoc($slotsQ)) {
					$slotId = (int)$row['slot_id'];
					$data_array = $mergeSlotCompanies(
						explode(',', (string)$row['company_id']),
						explode(',', (string)$row['reference_company_ids'])
					);
					$d->update("batch_slot_master", $data_array, "slot_id='$slotId'");
				}
			}
		}
		if ($meetingType == "0") {
			$slotIds = isset($_POST['slot_id']) ? (array)$_POST['slot_id'] : [];
			$slotIdsInt = array_values(array_unique(array_filter(array_map('intval', $slotIds))));
			if (!empty($slotIdsInt)) {
				$slotsIn = implode(',', $slotIdsInt);
				// Prefetch posted slots
				$postedSlots = [];
				$groupKeys = [];
				$slotsQ = $d->selectRow(
					"slot_id, batch_id, start_date, created_date, company_id, reference_company_ids",
					"batch_slot_master",
					"slot_id IN ($slotsIn)"
				);
				while ($row = mysqli_fetch_assoc($slotsQ)) {
					$postedSlots[] = $row;
					$gk = $row['batch_id'] . '|' . $row['start_date'] . '|' . $row['created_date'];
					$groupKeys[$gk] = [
						'batch_id' => $row['batch_id'],
						'start_date' => $row['start_date'],
						'created_date' => $row['created_date'],
					];
				}

				// Prefetch all similar slots for those (batch_id, start_date, created_date) groups once
				$similarByGroup = [];
				if (!empty($groupKeys)) {
					$orParts = [];
					foreach ($groupKeys as $g) {
						$bid = $d->escapeSqlString($g['batch_id']);
						$sd = $d->escapeSqlString($g['start_date']);
						$cd = $d->escapeSqlString($g['created_date']);
						$orParts[] = "(batch_id = '$bid' AND start_date = '$sd' AND created_date = '$cd')";
					}
					$similarQ = $d->selectRow(
						"slot_id, batch_id, start_date, created_date, company_id, reference_company_ids",
						"batch_slot_master",
						implode(' OR ', $orParts)
					);
					while ($slotRow = mysqli_fetch_assoc($similarQ)) {
						$gk = $slotRow['batch_id'] . '|' . $slotRow['start_date'] . '|' . $slotRow['created_date'];
						$similarByGroup[$gk][(int)$slotRow['slot_id']] = $slotRow;
					}
				}

				// Merge once per unique target slot
				$updatedTargets = [];
				foreach ($postedSlots as $row) {
					$gk = $row['batch_id'] . '|' . $row['start_date'] . '|' . $row['created_date'];
					foreach ($similarByGroup[$gk] ?? [] as $targetSlotId => $slotRow) {
						if (isset($updatedTargets[$targetSlotId])) {
							continue;
						}
						$data_array = $mergeSlotCompanies(
							explode(',', (string)$slotRow['company_id']),
							explode(',', (string)$slotRow['reference_company_ids'])
						);
						$d->update("batch_slot_master", $data_array, "slot_id = '$targetSlotId'");
						$updatedTargets[$targetSlotId] = true;
					}
				}
			}
			$_SESSION['msg'] = "Meeting scheduled successfully";
			header("Location: " . getCompanyOnboardingRedirectUrl());
			exit();
		}
		$_SESSION['msg'] = "Meeting scheduled successfully";
		header("Location: " . getCompanyOnboardingRedirectUrl());
		exit();
	} else if (isset($_POST['closureDate']) && $_POST['closureDate'] == 'closureDate') {
		$m->set_data('hr_training_days_from_closure', $hr_training_days_from_closure);
		$m->set_data('hr_training_days_from_first_meeting', $hr_training_days_from_first_meeting);
		$m->set_data('first_hr_training_days_from_closure', $first_hr_training_days_from_closure);
		$m->set_data('owner_team_leader_training_days_from_closure', $owner_team_leader_training_days_from_closure);
		$m->set_data('session1_training_days_from_closure', $session1_training_days_from_closure);
		$m->set_data('session1_data_receive_days_from_training', $session1_data_receive_days_from_training);
		$m->set_data('session1_data_upload_days_from_receive', $session1_data_upload_days_from_receive);
		$m->set_data('session1_completion_days_from_closure', $session1_completion_days_from_closure);
		$m->set_data('session2_data_receive_days_from_training', $session2_data_receive_days_from_training);
		$m->set_data('session2_data_upload_days_from_receive', $session2_data_upload_days_from_receive);
		$m->set_data('session2_data_upload_days_from_closure', $session2_data_upload_days_from_closure);
		$a = array(
			'hr_training_days_from_closure' => $m->get_data('hr_training_days_from_closure'),
			'hr_training_days_from_first_meeting' => $m->get_data('hr_training_days_from_first_meeting'),
			'first_hr_training_days_from_closure' => $m->get_data('first_hr_training_days_from_closure'),
			'owner_team_leader_training_days_from_closure' => $m->get_data('owner_team_leader_training_days_from_closure'),
			'session1_training_days_from_closure' => $m->get_data('session1_training_days_from_closure'),
			'session1_data_receive_days_from_training' => $m->get_data('session1_data_receive_days_from_training'),
			'session1_data_upload_days_from_receive' => $m->get_data('session1_data_upload_days_from_receive'),
			'session1_completion_days_from_closure' => $m->get_data('session1_completion_days_from_closure'),
			'session2_data_receive_days_from_training' => $m->get_data('session2_data_receive_days_from_training'),
			'session2_data_upload_days_from_receive' => $m->get_data('session2_data_upload_days_from_receive'),
			'session2_data_upload_days_from_closure' => $m->get_data('session2_data_upload_days_from_closure'),
		);
		$q = $d->update("Closure_Date_Setting", $a, "closure_date_id='1'");
		if ($q > 0) {
			$_SESSION['msg'] = "Closure Date successfully updated.";
			header("Location: " . getCompanyOnboardingRedirectUrl());
			exit;
		} else {
			$_SESSION['msg1'] = "Something went wrong.";
			header("Location: " . getCompanyOnboardingRedirectUrl());
			exit;
		}
	} else if ($_POST['action'] == "getSubtopics") {
		$module_id = $d->sanitizeActionIdAsInt($_POST['module_id'] ?? 0);
		if ($module_id <= 0) {
			echo '<div class="alert alert-info">No sub-topics found for this module.</div>';
			exit;
		}

		$subtopics = $d->select("training_module_subtopics", "training_module_id = $module_id", "ORDER BY display_order ASC");

		if (mysqli_num_rows($subtopics) > 0) {
			echo '<div class="list-group">';
			while ($subtopic = mysqli_fetch_assoc($subtopics)) {
				$statusBadge = "";
				echo '<div class="list-group-item d-flex justify-content-between align-items-center">';
				echo '<div>';
				echo '<h6 class="mb-1">' . htmlspecialchars($subtopic['subtopic_name']) . ' ' . $statusBadge;
				if (!empty($subtopic['estimated_minutes'])) {
					echo ' <span class="badge badge-secondary ml-2">' . intval($subtopic['estimated_minutes']) . ' min</span>';
				}
				echo '</h6>';
				if (!empty($subtopic['subtopic_description'])) {
					echo '<p class="mb-1 text-muted small">' . htmlspecialchars($subtopic['subtopic_description']) . '</p>';
				}
				echo '</div>';
				echo '<div>';
				echo '<a href="javascript:void(0);" class="btn btn-sm btn-primary mr-1" title="Edit Module" onclick="editSubtopic(' . $subtopic['subtopic_id'] . ')"><i class="fa fa-pencil"></i></a>';
				echo '<a href="javascript:void(0);" class="btn btn-sm btn-danger" title="Edit Module" onclick="deleteSubtopic(' . $subtopic['subtopic_id'] . ')"><i class="fa fa-trash"></i></a>';
				echo '</div>';
				echo '</div>';
			}
			echo '</div>';
		} else {
			echo '<div class="alert alert-info">No sub-topics found for this module.</div>';
		}
		exit;
	} else if ($_POST['action'] == "getSubtopic") {
		$subtopic_id = $d->sanitizeActionIdAsInt($_POST['subtopic_id'] ?? 0);
		if ($subtopic_id <= 0) {
			echo json_encode(['success' => false, 'message' => 'Sub-topic not found']);
			exit;
		}

		$subtopic = $d->select("training_module_subtopics", "subtopic_id = $subtopic_id");

		if (mysqli_num_rows($subtopic) > 0) {
			$data = mysqli_fetch_assoc($subtopic);
			echo json_encode(['success' => true, 'data' => $data]);
		} else {
			echo json_encode(['success' => false, 'message' => 'Sub-topic not found']);
		}
		exit;
	} else if ($_POST['action'] == "saveSubtopic") {
		$module_id = $d->sanitizeActionIdAsInt($_POST['module_id'] ?? 0);
		$subtopic_id = $d->sanitizeActionIdAsInt($_POST['subtopic_id'] ?? 0);
		if ($module_id <= 0) {
			echo json_encode(['success' => false, 'message' => 'Invalid module']);
			exit;
		}
		$data = [
			'training_module_id' => $module_id,
			'subtopic_name' => $_POST['subtopic_name'],
			'subtopic_description' => $_POST['subtopic_description'],
			'display_order' => $_POST['subtopic_order'],
			'estimated_minutes' => (isset($_POST['subtopic_minutes']) && $_POST['subtopic_minutes'] !== '') ? intval($_POST['subtopic_minutes']) : null,
		];

		if ($subtopic_id > 0) {
			$result = $d->update("training_module_subtopics", $data, "subtopic_id = $subtopic_id");
			$message = $result ? 'Sub-topic updated successfully' : 'Error updating sub-topic';
		} else {
			$result = $d->insert("training_module_subtopics", $data);
			$message = $result ? 'Sub-topic added successfully' : 'Error adding sub-topic';
		}

		// // Recalculate module estimated minutes as sum of active subtopics if any exist
		// $moduleId = intval($_POST['module_id']);
		// $sumRes = $d->selectRow("SUM(IFNULL(estimated_minutes,0)) AS total_minutes", "training_module_subtopics", "training_module_id = $moduleId AND (subtopic_status IS NULL OR subtopic_status != 0)");
		// if ($sumRes && mysqli_num_rows($sumRes) > 0) {
		// 	$total = intval(mysqli_fetch_assoc($sumRes)['total_minutes']);
		// 	$d->update("training_module_master", [ 'estimated_minutes' => $total ], "training_module_id = $moduleId");
		// }

		echo json_encode(['success' => $result, 'message' => $message]);
		exit;
    } else if ($_POST['action'] == "deleteSubtopic") {
        $subtopic_id = intval($_POST['subtopic_id']);
        // Get module id before deletion
        $fetch = $d->selectRow("training_module_id", "training_module_subtopics", "subtopic_id = $subtopic_id");
        $moduleId = 0;
        if ($fetch && mysqli_num_rows($fetch) > 0) {
            $moduleId = intval(mysqli_fetch_assoc($fetch)['training_module_id']);
        }
        // Hard delete
        $result = $d->delete("training_module_subtopics", "subtopic_id = $subtopic_id");
        $message = $result ? 'Sub-topic deleted successfully' : 'Error deleting sub-topic';
        echo json_encode(['success' => (bool)$result, 'message' => $message]);
        exit;
	} else if (isset($_POST['action']) && $_POST['action'] === 'getTopicsWithMeta') {
		// Filter by topic_type: 0 = HRMS, 1 = CRM
		$topicType = isset($_POST['topic_type']) ? intval($_POST['topic_type']) : 0;
		$whereClause = "tmt.topic_status = 1";
		if (isset($_POST['module_type'])) {
			$moduleType = intval($_POST['module_type']);
			if ($moduleType == 2) {
				// CRM modules need CRM topics (topic_type = 1)
				$whereClause .= " AND tmt.topic_type = 1";
			} else {
				// HRMS modules need HRMS topics (topic_type = 0)
				$whereClause .= " AND tmt.topic_type = 0";
			}
		} else {
			// Default to HRMS if no module_type specified
			$whereClause .= " AND tmt.topic_type = " . $topicType;
		}
		$result = $d->selectRow(
			"tmt.topic_id, tmt.topic_name, tmt.completion_days, tmt.next_start_days, tmt.participant_type, tmt.topic_type, tpt.participant_name",
			"training_module_topics tmt LEFT JOIN training_participants_type tpt ON tpt.participants_type_id = tmt.participant_type",
			$whereClause,
			"ORDER BY tmt.topic_name ASC"
		);
		$topics = [];
		if ($result && mysqli_num_rows($result) > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
				$topics[] = [
					'topic_id' => intval($row['topic_id']),
					'topic_name' => $row['topic_name'],
					'completion_days' => isset($row['completion_days']) ? intval($row['completion_days']) : null,
					'next_start_days' => isset($row['next_start_days']) ? intval($row['next_start_days']) : null,
					'topic_type' => isset($row['topic_type']) ? intval($row['topic_type']) : 0, // 0 = HRMS, 1 = CRM
					'participants_type_id' => isset($row['participant_type']) ? intval($row['participant_type']) : null,
					'participant_type_name' => isset($row['participant_name']) ? $row['participant_name'] : null,
				];
			}
		}
		echo json_encode(['success' => true, 'data' => $topics]);
		exit;
	} else if (isset($_POST['action']) && $_POST['action'] === 'saveTopicWithMeta') {
		$topic_id = isset($_POST['topic_id']) && $_POST['topic_id'] !== '' ? intval($_POST['topic_id']) : null;
		$topic_name = isset($_POST['topic_name']) ? trim($_POST['topic_name']) : '';
		$completion_days = isset($_POST['completion_days']) && $_POST['completion_days'] !== '' ? intval($_POST['completion_days']) : 0;
		$next_start_days = isset($_POST['next_start_days']) && $_POST['next_start_days'] !== '' ? intval($_POST['next_start_days']) : 0;
		$participants_type_id = isset($_POST['participants_type_id']) && $_POST['participants_type_id'] !== '' ? intval($_POST['participants_type_id']) : 0;
		$topic_type = isset($_POST['topic_type']) ? intval($_POST['topic_type']) : 0; // 0 = HRMS, 1 = CRM
		
		if ($topic_name === '') {
			echo json_encode(['success' => false, 'message' => 'Topic name is required']);
			exit;
		}
		
		// For HRMS topics (topic_type = 0), participant_type is required
		// For CRM topics (topic_type = 1), participant_type is optional/null
		if ($topic_type == 0 && $participants_type_id <= 0) {
			echo json_encode(['success' => false, 'message' => 'Participant type is required for HRMS topics']);
			exit;
		}
		
		$data = [
			'topic_name' => $topic_name,
			'completion_days' => $completion_days,
			'next_start_days' => $next_start_days,
			'topic_type' => $topic_type,
			'topic_status' => 1
		];
		
		// Only set participant_type for HRMS topics
		if ($topic_type == 0) {
			$data['participant_type'] = $participants_type_id;
		} else {
			// CRM topics don't have participant_type
			$data['participant_type'] = null;
		}
		if ($topic_id) {
			$ok = $d->update('training_module_topics', $data, "topic_id = '$topic_id'");
			echo json_encode(['success' => (bool)$ok, 'message' => $ok ? 'Topic updated' : 'Failed to update topic']);
		} else {
			$ok = $d->insert('training_module_topics', $data);
			echo json_encode(['success' => (bool)$ok, 'message' => $ok ? 'Topic created' : 'Failed to create topic']);
		}
		exit;
	} else if (isset($_POST['action']) && $_POST['action'] === 'checkTopicModules') {
		$topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
		if ($topic_id <= 0) {
			echo json_encode(['success' => false, 'message' => 'Invalid topic id']);
			exit;
		}
		
		try {
			// Check if topic has any modules assigned to it
			$result = $d->selectRow("COUNT(*) as module_count", "training_module_master", "topic_id = '$topic_id' AND training_module_status = 0");
			if (!$result) {
				echo json_encode(['success' => false, 'message' => 'Database query failed']);
				exit;
			}
			
			$row = mysqli_fetch_assoc($result);
			$hasModules = isset($row['module_count']) && intval($row['module_count']) > 0;
			
			echo json_encode(['success' => true, 'hasModules' => $hasModules]);
		} catch (Exception $e) {
			echo json_encode(['success' => false, 'message' => 'Error checking modules: ' . $e->getMessage()]);
		}
		exit;
	} else if (isset($_POST['action']) && $_POST['action'] === 'getTopicsWithModulesByParticipant') {
		$participant_type = isset($_POST['participant_type']) ? intval($_POST['participant_type']) : 0;
		if ($participant_type <= 0) {
			echo json_encode(['success' => false, 'message' => 'Invalid participant type']);
			exit;
		}

		$query = $d->selectRow(
			"tmt.topic_id, tmt.topic_name, 
			 tmm.training_module_id, tmm.training_module_name,
			 COALESCE(tmm.training_module_order, tmm.module_priority) AS sort_order",
			"training_module_topics tmt 
			 LEFT JOIN training_module_master tmm 
			   ON tmm.topic_id = tmt.topic_id 
			  AND tmm.module_type = 1 
			  AND tmm.training_module_status = 0",
			"tmt.topic_status = 1 AND tmt.topic_type = 0 AND tmt.participant_type = '$participant_type'",
			"ORDER BY tmt.topic_name ASC, sort_order ASC, tmm.training_module_id ASC"
		);

		$topicsMap = [];
		if ($query && mysqli_num_rows($query) > 0) {
			while ($row = mysqli_fetch_assoc($query)) {
				$tid = intval($row['topic_id']);
				if (!isset($topicsMap[$tid])) {
					$topicsMap[$tid] = [
						'topic_id' => $tid,
						'topic_name' => $row['topic_name'],
						'modules' => []
					];
				}
				if (!empty($row['training_module_id'])) {
					$topicsMap[$tid]['modules'][] = [
						'training_module_id' => intval($row['training_module_id']),
						'training_module_name' => $row['training_module_name'],
						'sort_order' => isset($row['sort_order']) ? intval($row['sort_order']) : null
					];
				}
			}
		}

		$topics = array_values($topicsMap);
		echo json_encode(['success' => true, 'data' => $topics]);
		exit;
	} else if (isset($_POST['action']) && $_POST['action'] === 'deleteTopicWithMeta') {
		$topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
		if ($topic_id <= 0) {
			echo json_encode(['success' => false, 'message' => 'Invalid topic id']);
			exit;
		}
		$ok = $d->update('training_module_topics', ['topic_status' => 0], "topic_id = '$topic_id'");
		echo json_encode(['success' => (bool)$ok, 'message' => $ok ? 'Topic deleted' : 'Failed to delete topic']);
		exit;
	} else if (isset($_POST['action']) && $_POST['action'] === 'getParticipantTypes') {
		$result = $d->selectRow(
			"participants_type_id, participant_name",
			"training_participants_type",
			"status = 0",
			"ORDER BY participant_name ASC"
		);
		$list = [];
		if ($result && mysqli_num_rows($result) > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
				$list[] = [
					'id' => intval($row['participants_type_id']),
					'name' => $row['participant_name']
				];
			}
		}
		echo json_encode(['success' => true, 'data' => $list]);
		exit;
	} else if (isset($_POST['action']) && $_POST['action'] === 'getTopics') {
		// Filter by topic_type if provided: 0 = HRMS, 1 = CRM
		$topicType = isset($_POST['topic_type']) ? intval($_POST['topic_type']) : 0;
		$whereClause = "topic_status = 1";
		if (isset($_POST['topic_type'])) {
			$whereClause .= " AND topic_type = " . $topicType;
		}
		$result = $d->selectRow("topic_id, topic_name", "training_module_topics", $whereClause, "ORDER BY topic_name ASC");
		$topics = [];
		if ($result && mysqli_num_rows($result) > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
				$topics[] = $row;
			}
		}
		echo json_encode(['success' => true, 'data' => $topics]);
		exit;
	} else if (isset($_POST['action']) && $_POST['action'] === 'createTopic') {
		$topic_name = trim($_POST['topic_name']);
		if ($topic_name === '') {
			echo json_encode(['success' => false, 'message' => 'Topic name is required']);
			exit;
		}
		// Check if exists
		$exists = $d->selectRow("topic_id", "training_module_topics", "topic_name='" . $topic_name . "'");
		if ($exists && mysqli_num_rows($exists) > 0) {
			$row = mysqli_fetch_assoc($exists);
			echo json_encode(['success' => true, 'data' => ['topic_id' => $row['topic_id']], 'message' => 'Topic already exists']);
			exit;
		}
		$insert = $d->insert("training_module_topics", [
			'topic_name' => $topic_name,
			'topic_status' => 1
		]);
		if ($insert) {
			$new = $d->selectRow("topic_id", "training_module_topics", "topic_name='" . $topic_name . "'", "LIMIT 1");
			$row = $new ? mysqli_fetch_assoc($new) : null;
			echo json_encode(['success' => true, 'data' => ['topic_id' => $row ? $row['topic_id'] : null], 'message' => 'Topic created']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Failed to create topic']);
		}
		exit;
	} else if (isset($_POST['action']) && $_POST['action'] === 'assignModulesToTopic') {
		$topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
		$module_ids = isset($_POST['module_ids']) ? $_POST['module_ids'] : [];
		if (!is_array($module_ids)) {
			$module_ids = explode(',', $module_ids);
		}
		$module_ids = array_filter(array_map('intval', $module_ids));
		if (empty($module_ids)) {
			echo json_encode(['success' => false, 'message' => 'Invalid topic or modules']);
			exit;
		}
		$idList = implode(',', $module_ids);
		$updateData = [ 'topic_id' => ($topic_id > 0 ? $topic_id : null) ];
		$updated = $d->update("training_module_master", $updateData, "training_module_id IN ($idList) AND module_type = 1");
		echo json_encode(['success' => (bool)$updated, 'message' => $updated ? ($topic_id > 0 ? 'Modules assigned to topic' : 'Modules unassigned from topic') : 'Failed to update modules']);
		exit;
	} else if (isset($_POST['action']) && $_POST['action'] === 'updateTrainingModuleOrder') {
		$module_id = isset($_POST['module_id']) ? intval($_POST['module_id']) : 0;
		$target_module_id = isset($_POST['target_module_id']) ? intval($_POST['target_module_id']) : 0;
		$direction = isset($_POST['direction']) ? $_POST['direction'] : '';
		
		if ($module_id <= 0 || $target_module_id <= 0 || !in_array($direction, ['up', 'down'])) {
			echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
			exit;
		}
		
		$module1 = $d->selectRow("training_module_order", "training_module_master", "training_module_id = '$module_id' AND module_type = 1");
		$module2 = $d->selectRow("training_module_order", "training_module_master", "training_module_id = '$target_module_id' AND module_type = 1");
		
		if (!$module1 || !$module2 || mysqli_num_rows($module1) == 0 || mysqli_num_rows($module2) == 0) {
			echo json_encode(['success' => false, 'message' => 'Training module not found']);
			exit;
		}
		
		$order1 = mysqli_fetch_assoc($module1)['training_module_order'];
		$order2 = mysqli_fetch_assoc($module2)['training_module_order'];
		
		$update1 = $d->update("training_module_master", ['training_module_order' => $order2], "training_module_id = '$module_id' AND module_type = 1");
		$update2 = $d->update("training_module_master", ['training_module_order' => $order1], "training_module_id = '$target_module_id' AND module_type = 1");
		
		if ($update1 && $update2) {
			echo json_encode(['success' => true, 'message' => 'Training module order updated successfully']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Failed to update training module order']);
		}
		exit;
	} else if (isset($_POST['action']) && $_POST['action'] === 'saveTrainingModuleOrder') {
		$module_orders = isset($_POST['module_orders']) ? $_POST['module_orders'] : [];
		if (!is_array($module_orders)) {
			echo json_encode(['success' => false, 'message' => 'Invalid module orders']);
			exit;
		}
		
		$caseParts = [];
		$moduleIds = [];
		foreach ($module_orders as $order_data) {
			$module_id = intval($order_data['id']);
			$new_order = intval($order_data['order']);
			if ($module_id > 0 && $new_order > 0) {
				$caseParts[] = "WHEN '$module_id' THEN '$new_order'";
				$moduleIds[] = $module_id;
			}
		}

		$success = true;
		if (!empty($caseParts) && !empty($moduleIds)) {
			$idsIn = implode(',', array_map('intval', $moduleIds));
			$caseSql = implode(' ', $caseParts);
			mysqli_set_charset($con, "utf8mb4");
			$ok = mysqli_query(
				$con,
				"UPDATE training_module_master SET training_module_order = CASE training_module_id $caseSql END WHERE training_module_id IN ($idsIn) AND module_type = 1"
			);
			$success = (bool)$ok;
		}
		
		if ($success) {
			echo json_encode(['success' => true, 'message' => 'Training module order saved successfully']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Failed to save training module order']);
		}
		exit;
	} else {
		$_SESSION['msg1'] = 'Something went wrong';
		header("Location: ../manageTraining");
		exit();
	}
} else {
	$_SESSION['msg1'] = 'Something went wrong';
	header("Location: ../welcome");
	exit();
}

// Helper function to build companyOnboarding redirect URL with filter parameters
function getCompanyOnboardingRedirectUrl() {
    $filters = [];
    $filterKeys = ['countryId', 'sId', 'cId', 'training_type', 'rise_filter', 'product_type'];
    foreach ($filterKeys as $key) {
        $value = '';
        if (isset($_POST[$key]) && $_POST[$key] != '') {
            $value = $_POST[$key];
        } elseif (isset($_GET[$key]) && $_GET[$key] != '') {
            $value = $_GET[$key];
        } elseif (isset($_SESSION['companyOnboarding_filters'][$key]) && $_SESSION['companyOnboarding_filters'][$key] != '') {
            $value = $_SESSION['companyOnboarding_filters'][$key];
        }
        if ($key === 'countryId') {
            if ($value != '' && $value > 0) {
                $filters[$key] = $value;
            } elseif (!isset($filters[$key])) {
                $filters[$key] = 101;
            }
        } elseif ($key === 'product_type') {
            if ($value === 'crm' || $value === 'hrms') {
                $filters[$key] = $value;
            } elseif (!isset($filters[$key])) {
                $filters[$key] = 'hrms';
            }
        } elseif ($value != '') {
            $filters[$key] = $value;
        }
    }
    if (!isset($filters['countryId'])) {
        $filters['countryId'] = 101;
    }
    if (!isset($filters['product_type'])) {
        $filters['product_type'] = 'hrms';
    }
    $queryString = !empty($filters) ? '?' . http_build_query($filters) : '';
    return '../companyOnboarding' . $queryString;
}

// Helper function to build manageTrainingModule redirect URL with filter parameters
function getManageTrainingModuleRedirectUrl() {
    $filters = [];
    $filterKeys = ['session_name', 'module_priority', 'training_module_type'];
    foreach ($filterKeys as $key) {
        $value = '';
        if (isset($_POST[$key]) && $_POST[$key] != '' && $_POST[$key] != 'all') {
            $value = $_POST[$key];
        } elseif (isset($_GET[$key]) && $_GET[$key] != '' && $_GET[$key] != 'all') {
            $value = $_GET[$key];
        } elseif (isset($_SESSION['manageTrainingModule_filters'][$key]) && $_SESSION['manageTrainingModule_filters'][$key] != '' && $_SESSION['manageTrainingModule_filters'][$key] != 'all') {
            $value = $_SESSION['manageTrainingModule_filters'][$key];
        }
        if ($value != '') {
            $filters[$key] = $value;
        }
    }
    $queryString = !empty($filters) ? '?' . http_build_query($filters) : '';
    return '../manageTrainingModule' . $queryString;
}