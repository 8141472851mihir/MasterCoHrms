<?php
include '../common/objectController.php';

if (isset($_POST) && !empty($_POST)) {
	extract($_POST);
	if (isset($update)) {
		$amountReceived = trim((string)($amountReceived ?? ''));
		$payment_mode = (string)($payment_mode ?? '');
		$received_by = trim((string)($received_by ?? ''));
		if (!isset($employee_tracking_limit) || !preg_match('/^\d+$/', (string)$employee_tracking_limit)) {
			$_SESSION['msg1'] = 'Please enter a valid employee tracking limit.';
			header('Location: ../trackingLimit');
			exit;
		}
		if (!isset($employee_registration_limit) || !preg_match('/^\d+$/', (string)$employee_registration_limit)) {
			$_SESSION['msg1'] = 'Please enter a valid employee registration limit.';
			header('Location: ../trackingLimit');
			exit;
		}
		if ($crm_limit !== '' && $crm_limit !== null && !preg_match('/^\d+$/', (string)$crm_limit)) {
			$_SESSION['msg1'] = 'Please enter a valid CRM limit.';
			header('Location: ../trackingLimit');
			exit;
		}

		$oldValuesQ = $d->selectRow("employee_tracking_limit, employee_registration_limit, crm_limit, society_name, secretary_mobile, secretary_email, package_id, country_code", "society_master", "society_id='$society_id'");
		$oldValues = mysqli_fetch_assoc($oldValuesQ) ?: array();
		$oldTrackingLimit = $oldValues['employee_tracking_limit'] ?? 0;
		$oldRegistrationLimit = $oldValues['employee_registration_limit'] ?? 0;
		$oldCrmLimit = $oldValues['crm_limit'] ?? 0;
		$limitsUnchanged = (int)$oldTrackingLimit === (int)$employee_tracking_limit
			&& (int)$oldRegistrationLimit === (int)$employee_registration_limit
			&& (int)$oldCrmLimit === (int)($crm_limit ?? 0);
		if ($limitsUnchanged) {
			$_SESSION['msg1'] = 'No limit was changed. Update was not saved.';
			header('Location: ../trackingLimit');
			exit;
		}

		if (!isset($payment_received) || !in_array((string)$payment_received, array('0', '1'), true)) {
			$_SESSION['msg1'] = 'Please select whether payment was received.';
			header('Location: ../trackingLimit');
			exit;
		}
		$requirePayment = (string)$payment_received === '1';

		$payment_attachment = '';
		if ($requirePayment && ($amountReceived === '' || !preg_match('/^\d+(\.\d{1,2})?$/', $amountReceived))) {
			$_SESSION['msg1'] = 'Please enter a valid amount.';
			header('Location: ../trackingLimit');
			exit;
		}
		if ($requirePayment && !in_array($payment_mode, array('1', '2', '3', '4'), true)) {
			$_SESSION['msg1'] = 'Please select a payment mode.';
			header('Location: ../trackingLimit');
			exit;
		}
		if ($requirePayment && ($received_by === '' || strlen($received_by) > 100)) {
			$_SESSION['msg1'] = 'Please enter who received the amount.';
			header('Location: ../trackingLimit');
			exit;
		}
		if (!$requirePayment) {
			$amountReceived = '';
			$payment_mode = '';
			$received_by = '';
		}
		if ($requirePayment && (!isset($_FILES['payment_attachment']['tmp_name']) || !is_uploaded_file($_FILES['payment_attachment']['tmp_name']))) {
			$_SESSION['msg1'] = 'Please upload a payment attachment.';
			header('Location: ../trackingLimit');
			exit;
		}
		if ($requirePayment) {
			$acceptable = array('jpeg', 'jpg', 'png', 'pdf');
			$extId = strtolower(pathinfo($_FILES['payment_attachment']['name'], PATHINFO_EXTENSION));
			if (!in_array($extId, $acceptable, true) || empty($_FILES['payment_attachment']['type'])) {
				$_SESSION['msg1'] = 'Invalid payment attachment. Allowed: jpg, jpeg, png, pdf.';
				header('Location: ../trackingLimit');
				exit;
			}
			$dirPath = '../../img/society_requests/';
			$payment_attachment = $d->short_app_name() . '_' . round(microtime(true)) . '.' . $extId;
			$destinationPath = $dirPath . $payment_attachment;
			if ($extId === 'pdf') {
				move_uploaded_file($_FILES['payment_attachment']['tmp_name'], $destinationPath);
			} else {
				$d->resizeImage($_FILES['payment_attachment']['tmp_name'], $destinationPath, 1280, 720, $extId);
			}
			if (!file_exists($destinationPath)) {
				$_SESSION['msg1'] = 'Unable to upload payment attachment.';
				header('Location: ../trackingLimit');
				exit;
			}
		}

		
		// Company server
		// if($employee_tracking_limit > 0){
		$tracking_status = '1';
		$m->set_data('employee_tracking_limit', $employee_tracking_limit);
		// }elseif($employee_tracking_limit == 0){
		// 	$tracking_status = '0';
		// 	$m->set_data('employee_tracking_limit','0');
		// }elseif(empty($employee_tracking_limit)){
		// 	$tracking_status = '0';
		// 	$m->set_data('employee_tracking_limit','0');
		// }

		$now = date('Y-m-d');
		$post = array(
			'society_id' => $society_id,
			'changeTrackingLimit' => 'changeTrackingLimit',
			'employee_tracking_limit' => $employee_tracking_limit,
			'employee_registration_limit' => $employee_registration_limit,
			'tracking_status' => $tracking_status,
		);
		$json = $d->callCompanyApiEnc($society_base_url, 'buildingChangePlanController.php', $post);

		// main server
		if (count($json) > 0) {

			if ($json['status'] == 200 && $json['status'] == 200) {
				$m->set_data('employee_registration_limit', $employee_registration_limit);
				$societyUrlData = $d->selectRow("domain_master.domain_name,society_master.sub_domain,society_master.employee_registration_limit,society_master.crm_created", "society_master LEFT JOIN domain_master ON domain_master.domain_id = society_master.domain_id", "society_master.society_id='$society_id'");
				if (mysqli_num_rows($societyUrlData) > 0) {

					$societyData = mysqli_fetch_array($societyUrlData);
					$sub_domain = $societyData['sub_domain'];

					if ($employee_registration_limit >= $crm_limit) {
						$m->set_data('crm_limit', $crm_limit);
						$now = date('Y-m-d');
						$a = array(
							'employee_tracking_limit' => $m->get_data('employee_tracking_limit'),
							'tracking_status' => $tracking_status,
							'employee_registration_limit' => $m->get_data('employee_registration_limit'),
						);
						if($societyData['crm_created']==1){
							$target_url = $sub_domain . "residentApiNew/societyAnalytics.php";
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $target_url);
							curl_setopt($ch, CURLOPT_POST, 1);
							curl_setopt($ch, CURLOPT_POSTFIELDS, "setCrmLimit=setCrmLimit&society_id=$society_id&language_id=1&crmLimit=$crm_limit");
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER, array(
								'key: ' . $keydb
							));
							$result = curl_exec($ch);
							curl_close($ch);

							$a['crm_limit'] = $m->get_data('crm_limit');
						}

						$q = $d->update("society_master", $a, "society_id='$society_id'");
						if ($q == TRUE) {
							// Build detailed log message with previous and new values
							$logMessages = [];
							if ($oldTrackingLimit != $employee_tracking_limit) {
								$logMessages[] = "Tracking Limit: $oldTrackingLimit → $employee_tracking_limit";
							}
							if ($oldRegistrationLimit != $employee_registration_limit) {
								$logMessages[] = "Registration Limit: $oldRegistrationLimit → $employee_registration_limit";
							}
							if ($oldCrmLimit != $crm_limit && isset($a['crm_limit'])) {
								$logMessages[] = "CRM Limit: $oldCrmLimit → $crm_limit";
							}
							
							$logMessage = !empty($logMessages) ? implode(", ", $logMessages) : "Employee Limit Updated Successfully";
							$logMessage .= $requirePayment ? ", Amount: $amountReceived" : ", Payment: Not received";
							$newCrmLimit = isset($a['crm_limit']) ? $crm_limit : $oldCrmLimit;
							if ($requirePayment) {
							$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20) . $society_id;
							$paymentPhone = $oldValues['secretary_mobile'] ?? '';
							$d->insert('transection_master', array(
								'society_id' => $society_id,
								'package_id' => $oldValues['package_id'] ?? ($package_id ?? 0),
								'package_name' => 'Limit Increase',
								'user_mobile' => $paymentPhone,
								'payment_mode' => $payment_mode,
								'payment_attachment' => $payment_attachment,
								'transection_amount' => $amountReceived,
								'transaction_amount_remark' => '',
								'discount' => '',
								'transection_date' => date('Y-m-d H:i:s'),
								'payment_status' => 'success',
								'payment_firstname' => $oldValues['society_name'] ?? ($society_name ?? ''),
								'payment_phone' => $paymentPhone,
								'payment_email' => $oldValues['secretary_email'] ?? '',
								'invoice_no' => '0' . date('ymdi'),
								'received_by' => $received_by,
								'closure_city' => '',
								'payment_txnid' => $txnid,
								'is_renewal' => 5,
								'plan_change_by_id' => $bms_admin_id,
								'plan_changed_by' => $created_by,
								'old_tracking_limit' => (int)$oldTrackingLimit,
								'new_tracking_limit' => (int)$employee_tracking_limit,
								'old_employee_limit' => (int)$oldRegistrationLimit,
								'new_employee_limit' => (int)$employee_registration_limit,
								'old_crm_limit' => (int)$oldCrmLimit,
								'new_crm_limit' => (int)$newCrmLimit,
							));
							}
							$d->insert_log("$society_id", "$bms_admin_id", "$created_by", "$logMessage");
							// $_SESSION['msg']=$json['message'];
							$_SESSION['msg'] = "Employee Limit Updated Successfully";
							header("Location: ../trackingLimit");
						} else {
							$_SESSION['msg1'] = "Something Wrong";
							header("Location: ../trackingLimit");
						}
					} else {
						$_SESSION['msg1'] = "Crm limit is greater than registration limit";
						header("Location: ../trackingLimit");
					}
				} else {
					$_SESSION['msg1'] = "Something Wrong";
					header("Location: ../trackingLimit");
				}
			} else {
				$_SESSION['msg1'] = "Something Wrong in Company Server";
				header("Location: ../trackingLimit");
			}
		}
	} else if (isset($updatePerPrice) && $updatePerPrice == "updatePerPrice") {
		// Get old value before update for logging
		$oldValuesQ = $d->selectRow("per_employee_price", "society_master", "society_id='$society_id'");
		$oldValues = mysqli_fetch_assoc($oldValuesQ);
		$oldPerEmpPrice = $oldValues['per_employee_price'] ?? 0;
		
		$tracking_status = '1';
		$m->set_data('per_employee_price', $per_employee_price);
		$now = date('Y-m-d');
		$a = array(
			'per_employee_price' => $m->get_data('per_employee_price'),
		);
		$q = $d->update("society_master", $a, "society_id='$society_id'");
		if ($q == TRUE) {
			$logMessage = "Per Emp Price: $oldPerEmpPrice → $per_employee_price";
			$d->insert_log("$society_id", "$bms_admin_id", "$created_by", "$logMessage");
			echo '1';
		} else {
			echo '0';
		}
	} else if (isset($updatecrm) && $updatecrm == "updatecrm") {
		// Get old value before update for logging
		$oldValuesQ = $d->selectRow("crm_limit", "society_master", "society_id='$society_id'");
		$oldValues = mysqli_fetch_assoc($oldValuesQ);
		$oldCrmLimit = $oldValues['crm_limit'] ?? 0;
		
		$societyUrlData = $d->selectRow("domain_master.domain_name,society_master.sub_domain,society_master.employee_registration_limit", "society_master LEFT JOIN domain_master ON domain_master.domain_id = society_master.domain_id", "society_master.society_id='$society_id'");
		if (mysqli_num_rows($societyUrlData) > 0) {
			$societyData = mysqli_fetch_array($societyUrlData);
			$sub_domain = $societyData['sub_domain'];
			$employee_registration_limit = $societyData['employee_registration_limit'];
			if ($employee_registration_limit >= $crm_limit) {
				$target_url = $sub_domain . "residentApiNew/societyAnalytics.php";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $target_url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, "setCrmLimit=setCrmLimit&society_id=$society_id&language_id=1&crmLimit=$crm_limit");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'key: ' . $keydb
				));
				$result = curl_exec($ch);
				curl_close($ch);
				$tracking_status = '1';
				$m->set_data('crm_limit', $crm_limit);
				$now = date('Y-m-d');
				$a = array(
					'crm_limit' => $m->get_data('crm_limit'),
				);
				$q = $d->update("society_master", $a, "society_id='$society_id'");
				if ($q == TRUE) {
					$logMessage = "CRM Limit: $oldCrmLimit → $crm_limit";
					$d->insert_log("$society_id", "$bms_admin_id", "$created_by", "$logMessage");
					echo '1';
					exit;
				} else {
					echo '0';
					exit;
				}
			} else {
				echo '2';
				exit;
			}
		} else {
			echo '2';
			exit;
		}
	} else if (isset($updateCompanySettings) && $updateCompanySettings == "updateCompanySettings") {
		$m->set_data('face_attendance_delete_days', $face_attendance_delete_days);
		$m->set_data('work_report_delete_days', $work_report_delete_days);
		$m->set_data('visit_attachment_delete_days', $visit_attachment_delete_days);
		$m->set_data('expense_attachment_delete_days', $expense_attachment_delete_days);
		$m->set_data('chat_attachment_delete_days', $chat_attachment_delete_days);
		$m->set_data('task_attachment_delete_days', $task_attachment_delete_days);
		$m->set_data('circular_attachment_delete_days', $circular_attachment_delete_days);
		$m->set_data('discussion_attachment_delete_days', $discussion_attachment_delete_days);
		$m->set_data('meeting_attachment_delete_days', $meeting_attachment_delete_days);
		$m->set_data('tracking_attachment_delete_months', $tracking_attachment_delete_months);
		$m->set_data('clear_local_save_attendance_data_face_app', $clear_local_save_attendance_data_face_app);
		$now = date('Y-m-d');

		$a = array(
			'face_attendance_delete_days' => $m->get_data('face_attendance_delete_days'),
			'work_report_delete_days' => $m->get_data('work_report_delete_days'),
			'visit_attachment_delete_days' => $m->get_data('visit_attachment_delete_days'),
			'expense_attachment_delete_days' => $m->get_data('expense_attachment_delete_days'),
			'chat_attachment_delete_days' => $m->get_data('chat_attachment_delete_days'),
			'task_attachment_delete_days' => $m->get_data('task_attachment_delete_days'),
			'circular_attachment_delete_days' => $m->get_data('circular_attachment_delete_days'),
			'discussion_attachment_delete_days' => $m->get_data('discussion_attachment_delete_days'),
			'meeting_attachment_delete_days' => $m->get_data('meeting_attachment_delete_days'),
			'tracking_attachment_delete_months' => $m->get_data('tracking_attachment_delete_months'),
		);
		$society_settings = json_encode($a);
		$a = array(
			'attendance_map_type' => $attendance_map_type,
			'visit_map_type' => $visit_map_type,
			'clear_local_save_attendance_data_face_app' => $m->get_data('clear_local_save_attendance_data_face_app'),
			'society_settings' => $society_settings,
		);
		$q = $d->update("society_master", $a, "society_id='$society_id'");
		$societyQry = $d->selectRow("society_master.sub_domain", "society_master", "society_id='$society_id'");
		if (mysqli_num_rows($societyQry) > 0) {
			$societyData = mysqli_fetch_assoc($societyQry);
			$sub_domain = $societyData['sub_domain'];
			$target_url = $sub_domain . "residentApiNew/societyAnalytics.php";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $target_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "updateAttachmentDeleteDays=updateAttachmentDeleteDays&society_id=$society_id&language_id=1&attendance_map_type=$attendance_map_type&visit_map_type=$visit_map_type&society_settings=$society_settings&clear_local_save_attendance_data_face_app=$clear_local_save_attendance_data_face_app");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'key: ' . $keydb
			));
			$result = curl_exec($ch);
			curl_close($ch);
			$json = json_decode($result, true);
			$result2 = $json["message"];
		}
		if ($q == TRUE) {
			$d->insert_log("$society_id", "$bms_admin_id", "$created_by", "$society_name attachment delete days updated successfully");
			$_SESSION['msg'] = "Updated successfully";
			header("Location: ../trackingLimit");
			exit;
		} else {
			$_SESSION['msg1'] = "Something Wrong";
			header("Location: ../trackingLimit");
			exit;
		}
	} else if (isset($_POST['status']) && $_POST['status'] == "startVisitWithOtpDeactive") {
		$id = $d->sanitizeActionIdAsInt($id ?? ($_POST['id'] ?? 0));
		$isActive = 0;
		$m->set_data('start_visit_with_otp', $isActive);
		$updateData = array(
			'start_visit_with_otp' => $m->get_data('start_visit_with_otp')
		);
		$q = $d->update('society_master', $updateData, "society_id='$id'");
		$societyQry = $d->selectRow("society_master.sub_domain", "society_master", "society_id='$id'");
		if (mysqli_num_rows($societyQry) > 0) {
			$societyData = mysqli_fetch_assoc($societyQry);
			$sub_domain = $societyData['sub_domain'];
			$target_url = $sub_domain . "residentApiNew/societyAnalytics.php";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $target_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "startVisitWithOtp=startVisitWithOtp&society_id=$id&language_id=1&start_visit_with_otp=0");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'key: ' . $keydb
			));
			$result = curl_exec($ch);
			curl_close($ch);
			$json = json_decode($result, true);
			$result2 = $json["message"];
		}
		echo ($q > 0) ? 1 : 0;
	} else if (isset($_POST['status']) && $_POST['status'] == "startVisitWithOtpActive") {
		$id = $d->sanitizeActionIdAsInt($id ?? ($_POST['id'] ?? 0));
		$isActive = 1;
		$m->set_data('start_visit_with_otp', $isActive);
		$updateData = array(
			'start_visit_with_otp' => $m->get_data('start_visit_with_otp')
		);
		$q = $d->update('society_master', $updateData, "society_id='$id'");
		$societyQry = $d->selectRow("society_master.sub_domain", "society_master", "society_id='$id'");
		if (mysqli_num_rows($societyQry) > 0) {
			$societyData = mysqli_fetch_assoc($societyQry);
			$sub_domain = $societyData['sub_domain'];
			$target_url = $sub_domain . "residentApiNew/societyAnalytics.php";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $target_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "startVisitWithOtp=startVisitWithOtp&society_id=$id&language_id=1&start_visit_with_otp=1");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'key: ' . $keydb
			));
			$result = curl_exec($ch);
			curl_close($ch);
			$json = json_decode($result, true);
			$result2 = $json["message"];
		}
		echo ($q > 0) ? 1 : 0;
	}
}

