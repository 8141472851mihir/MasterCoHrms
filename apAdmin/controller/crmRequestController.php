<?php
include '../common/objectController.php';
if (isset($_POST) && !empty($_POST)) {
	$log_file = '../../img/crm_creation_logs.log';
	$writeCrmCreationLog = function ($message, $context = array()) use ($log_file) {
		$logLine = "[" . date("Y-m-d h:i:s A") . "] " . $message;
		if (!empty($context)) {
			$logLine .= " | " . json_encode($context);
		}
		$logLine .= PHP_EOL;
		file_put_contents($log_file, $logLine, FILE_APPEND | LOCK_EX);
	};

	if (isset($_POST['addcrmrequest'])) {

		$file_payment_attachment = $_FILES['payment_attachment']['tmp_name'];
		if ($_FILES['payment_attachment']['name'] != '' && $_FILES['payment_attachment']['error'] !== UPLOAD_ERR_OK) {
			$file_upload_error= "File upload error: ";
			switch ($_FILES['payment_attachment']['error']) {
				case UPLOAD_ERR_INI_SIZE:
					$file_upload_error= "The uploaded file exceeds the maximum upload size allowed.";
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$file_upload_error= "The uploaded file exceeds the maximum size allowed.";
					break;
				case UPLOAD_ERR_PARTIAL:
					$file_upload_error= "The uploaded file was only partially uploaded. Please try again.";
					break;
				case UPLOAD_ERR_NO_FILE:
					$file_upload_error= "No file was uploaded. Please try again.";
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$file_upload_error= "Missing a temporary folder. Please try again.";
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$file_upload_error= "Failed to write file to disk. Please try again.";
					break;
				case UPLOAD_ERR_EXTENSION:
					$file_upload_error= "File upload stopped by extension. Please try again.";
					break;
				default:
					$file_upload_error= "Unknown upload error. Please try again.";
			}
			$_SESSION['msg1'] = $file_upload_error;
			header("location:../requestsCrm");
			exit();
		}
		if (file_exists($file_payment_attachment)) {
			$acceptable = array('jpeg', 'jpg', 'png','pdf','doc','docx');
			$extId = pathinfo($_FILES['payment_attachment']['name'], PATHINFO_EXTENSION);
			$dirPath = "../../img/society_requests/";
			$maxsize = 10097152;
			if (in_array($extId, $acceptable) && (!empty($_FILES["payment_attachment"]["type"]))) {
				$temp = explode(".", $_FILES["payment_attachment"]["name"]);
				$payment_attachment = 'Society_' . round(microtime(true)) . '.' . end($temp);
				$destinationPath = $dirPath . $payment_attachment;
				$functionacceptable = array('jpeg', 'jpg', 'png');
				if (in_array($extId, $functionacceptable)) {
					$d->resizeImage($file_payment_attachment, $destinationPath, 800, 600, $extId);
				} else {
					move_uploaded_file($file_payment_attachment, $destinationPath);
				}
			}else{
				$_SESSION['msg1'] = "Invalid File format, Only JPEG, JPG, PDF, DOC, DOCX and PNG are allowed.";
				header("location:../requestsCrm");
				exit();
			}
		} else {
			$payment_attachment = $payment_attachment_old;
		}

		$m->set_data('company_name', $company_name);
		$m->set_data('crm_package_id', $crm_package_id);
		$m->set_data('crm_plan_expiring_date', $crm_plan_expiring_date);
		$m->set_data('crm_limit', $crm_limit);
		$m->set_data('amountReceivedType', $amountReceivedType);
		$m->set_data('amountReceived', $amountReceived);
		$m->set_data('payment_mode', $payment_mode);
		$m->set_data('payment_attachment', $payment_attachment);
		$m->set_data('crm_request_created_by', $created_by);
		$m->set_data('crm_trial_days', $crm_trial_days);
		$m->set_data('yearly_ticket_size', $yearly_ticket_size);
		$m->set_data('received_ticket_size', $received_ticket_size);
		$m->set_data('per_employee_price', $per_employee_price);
		$m->set_data('crm_request_created_by_id', $bms_admin_id);
		$m->set_data('crm_remark', $crm_remark);

		$addcrm = array(
			'society_id' => $m->get_data('company_name'),
			'crm_package_id' => $m->get_data('crm_package_id'),
			'crm_limit' => $m->get_data('crm_limit'),
			'crm_plan_expire_date' => $m->get_data('crm_plan_expiring_date'),
			'crm_payment_status' => $m->get_data('amountReceivedType'),
			'crm_payment_amount' => $m->get_data('amountReceived'),
			'crm_payment_mode' => $m->get_data('payment_mode'),
			'crm_payment_attachment' => $m->get_data('payment_attachment'),
			'crm_request_created_by' => $m->get_data('crm_request_created_by'),
			'crm_trial_days' => $m->get_data('crm_trial_days'),
			'yearly_ticket_size' => $m->get_data('yearly_ticket_size'),
			'received_ticket_size' => $m->get_data('received_ticket_size'),
			'per_employee_price' => $m->get_data('per_employee_price'),
			'crm_request_created_by_id' => $m->get_data('crm_request_created_by_id'),
			'crm_request_created_date' => date("Y-m-d H:i:s"),
			'crm_remark' => $m->get_data('crm_remark'),
		);

		$q = $d->insert("crm_request_master", $addcrm);
		$d->insert_log_specific("0", "$bms_admin_id", "$created_by", "<b>$company_name</b> - CRM Creation Request Added", 3);
		$writeCrmCreationLog("CRM creation request added", array(
			'companyId' => $company_name, // society_id posted as company_name
			'crm_package_id' => $crm_package_id,
			'crm_limit' => $crm_limit,
			'crm_plan_expiring_date' => $crm_plan_expiring_date,
			'crm_trial_days' => $crm_trial_days,
			'insert_status' => $q,
			'requested_by' => $created_by,
			'requested_by_id' => $bms_admin_id
		));
		$to = array();
		$added_by = $admin_name;
		$request_for = "CRM";
		$society_query = $d->selectRow("society_name", "society_master", "society_id='$company_name'");
		while ($society_data = mysqli_fetch_array($society_query)) {
			$society_name = $society_data['society_name'];
		}
		$query = $d->select("society_create_email_receipt", "active_status=0");
		while ($data = mysqli_fetch_array($query)) {
			if ($data['email_id'] != '') {
				array_push($to, $data['email_id']);
			}
			$msg = "Creation of New CRM $society_name Requested by $added_by";
		}
		$subject = "Creation of New CRM $society_name Requested";
		include '../mail/newSocietyCreateMail.php';
		include '../mail.php';
		$admins = $d->select("admin_fcm_notification_master", "fcm_notifications=3");
		$total_admins = mysqli_num_rows($admins);
		if ($total_admins > 0) {
			$noti_title = "New CRM Request";
			$click_action = "crmRequestsList";
			$noti_description = $subject;
			$tokenwhere = "";
			$ik = 0;
			while ($adminsdata = mysqli_fetch_array($admins)) {
				$admin_id = $adminsdata['bms_admin_id'];
				if ($ik > 0) {
					$tokenwhere .= " OR ";
				}
				$tokenwhere .= "admin_id=$admin_id";
				$ik++;
			}
			$getTokens = $d->getWebFcm("web_fcm_master", "$tokenwhere");
			$nAdmin->sendAdminNotification($getTokens, $noti_title, $noti_description, $click_action);
		}
		if ($q > 0) {
			$_SESSION['msg'] = "CRM Request Added";
			header("location:../crmRequestsList");
		} else {
			$_SESSION['msg1'] = "Something Wrong";
			header("location:../requestsCrm");
		}
	}

	if (isset($_POST['createCRM']) && $_POST['companyId'] > 0) {
		$curl = curl_init();
		$writeCrmCreationLog("createCRM request received", array(
			'companyId' => $companyId,
			'companyName' => $companyName,
			'society_crm_id' => $society_crm_id,
			'requested_by' => $created_by,
			'requested_by_id' => $bms_admin_id
		));

		$mycoBackendUrl = $subDomain . 'crmApi';
		$frontendUrl = $subDomain . 'crm';

		$postDataArray = array(
			'companyId' => $companyId,
			'companyName' => $companyName,
			'subDomain' => $subDomain,
			'mycoBackendUrl' => $mycoBackendUrl,
			'frontendUrl' => $frontendUrl,
			'planExpiringDate' => $crm_plan_expiring_date,
		);
		$qc = $d->select("society_crm_master", "society_crm_id='$society_crm_id'");
		$cData = mysqli_fetch_array($qc);
		$society_crm_id = $cData['society_crm_id'];
		$society_crm_url = $cData['url'];
		$society_crm_token = $cData['token'];
		$crm_token = $cData['token'];
		$crm_url = $cData['url'] . 'api/v1/tenant';
		$writeCrmCreationLog("CRM master data loaded", array(
			'crm_url' => $crm_url,
			'society_crm_url' => $society_crm_url
		));
		$societyUrlData = $d->selectRow("domain_master.domain_name,society_master.sub_domain,server_master.server_ip,domain_master.domain_name", "society_master LEFT JOIN domain_master ON domain_master.domain_id = society_master.domain_id  JOIN server_master ON server_master.server_id = domain_master.server_id", "society_master.society_id='$companyId'");
		if (mysqli_num_rows($societyUrlData) > 0) {
			$societyData = mysqli_fetch_array($societyUrlData);
			$server_ip = $societyData['server_ip'];
			$sub_domain = $societyData['sub_domain'];
			$writeCrmCreationLog("Society URL data loaded", array(
				'server_ip' => $server_ip,
				'sub_domain' => $sub_domain
			));
		} else {
			$writeCrmCreationLog("Society URL data missing", array(
				'companyId' => $companyId
			));
		}
		$remote_host = escapeshellarg("$server_ip");
		$new_domain_name = str_replace("https://", "", $sub_domain);
		$new_domain_name = rtrim($new_domain_name, '/');
		$company_url = escapeshellarg("$new_domain_name");
		$secrete_key = escapeshellarg('f3N2n2IelsKZhHB');
		$scriptPath = '/var/ps/mastercrm.sh';
		$cmd = "sudo $scriptPath $remote_host $company_url $secrete_key $society_crm_id 2>&1";
		$writeCrmCreationLog("CRM shell script starting", array(
			'company' => $companyName,
			'command' => $cmd
		));
		$descriptorspec = array(
			0 => array("pipe", "r"), 
			1 => array("pipe", "w"),
			2 => array("pipe", "w") 
		);
		$process = proc_open($cmd, $descriptorspec, $pipes);
		if (is_resource($process)) {
			fclose($pipes[0]);
			$output = stream_get_contents($pipes[1]);
			$error = stream_get_contents($pipes[2]);
			fclose($pipes[1]);
			fclose($pipes[2]);
			$return_value = proc_close($process);
			$writeCrmCreationLog("CRM shell script completed", array(
				'return_code' => $return_value,
				'output' => trim($output),
				'error' => trim($error)
			));
			if ($return_value != 0) {
				$writeCrmCreationLog("CRM shell script failed", array(
					'return_code' => $return_value
				));
				$_SESSION['msg1'] = "CRM shell script execution failed with return code: $return_value";
				header("location:../crmRequestsList");
				exit();
			}
		} else {
			$writeCrmCreationLog("Failed to execute CRM shell script");
			$_SESSION['msg1'] = "Failed to execute CRM shell script";
			header("location:../crmRequestsList");
			exit();
		}
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => $crm_url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($postDataArray),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer ' . $crm_token
			),
		));

		$writeCrmCreationLog("Calling CRM tenant API", $postDataArray);
		$response = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$curlError = curl_error($curl);
		curl_close($curl);
		$tenantResponseData = json_decode($response, true);
		$writeCrmCreationLog("CRM tenant API response received", array(
			'http_code' => $code,
			'curl_error' => $curlError,
			'response' => $response,
			'json_status' => is_array($tenantResponseData) ? ($tenantResponseData['status'] ?? '') : ''
		));
		$d->insert_log(
			"$companyId",
			"$bms_admin_id",
			"$created_by",
			"CRM tenant API http=" . $code . " status=" . (is_array($tenantResponseData) ? ($tenantResponseData['status'] ?? 'na') : 'na')
		);
		if ($code == '200') {
			$response1 = '';
			$response1Data = null;
			$code1 = null;
			mysqli_data_seek($societyUrlData,0);
			if (mysqli_num_rows($societyUrlData) > 0) {
				$societyData = mysqli_fetch_array($societyUrlData);
				$sub_domain = $societyData['sub_domain'];
				$crulData = array(
					'updateSocietyCrm' => 'updateSocietyCrm',
					'society_id' => "$companyId",
					'crm_url' => "$society_crm_url",
					'crm_token' => "$society_crm_token"
				);
				$curl1 = curl_init();
				curl_setopt_array($curl1, array(
					CURLOPT_URL => $sub_domain . 'residentApiNew/societyAnalytics.php',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS => $crulData,
					CURLOPT_HTTPHEADER => array(
						'key: ' . $keydb
					),
				));

				$writeCrmCreationLog("Calling society analytics update API", array(
					'url' => $sub_domain . 'residentApiNew/societyAnalytics.php',
					'payload' => $crulData
				));
				$response1 = curl_exec($curl1);
				$code1 = curl_getinfo($curl1, CURLINFO_HTTP_CODE);
				$curlError1 = curl_error($curl1);
				curl_close($curl1);
				$response1Data = json_decode($response1, true);
				$writeCrmCreationLog("Society analytics update API response received", array(
					'http_code' => $code1,
					'curl_error' => $curlError1,
					'response' => $response1,
					'json_status' => is_array($response1Data) ? ($response1Data['status'] ?? '') : ''
				));
				$d->insert_log(
					"$companyId",
					"$bms_admin_id",
					"$created_by",
					"CRM updateSocietyCrm http=" . $code1 . " status=" . (is_array($response1Data) ? ($response1Data['status'] ?? 'na') : 'na')
				);
			}
			if ($code1 == '200') {
				if (isset($_POST['crm_limit'])) {
					$crm_package_id = $_POST['crm_package_id'];
					$crm_plan_expiring_date = $_POST['crm_plan_expiring_date'];
					$crm_trial_days = $_POST['crm_trial_days'];
					$crm_limit = $_POST['crm_limit'];

					$a5 = array(
						'crm_limit' => $crm_limit,
						'society_crm_id' => $society_crm_id,
						'crm_created' => 1,
						'crm_created_by' => $crm_request_created_by,
						'crm_created_date' => date("Y-m-d H:i:s"),
						'crm_package_id' => $crm_package_id,
						'crm_trial_days' => $crm_trial_days,
						'crm_plan_expiring_date' => $crm_plan_expiring_date
					);
					$sub_domain = $societyData['sub_domain'];
					$target_url = $sub_domain . "residentApiNew/societyAnalytics.php";
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $target_url);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, "setCrmLimit=setCrmLimit&society_id=$companyId&language_id=1&crmLimit=$crm_limit");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						'key: ' . $keydb
					));
					$result = curl_exec($ch);
					$code2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					$curlError2 = curl_error($ch);
					curl_close($ch);
					$resultData = json_decode($result, true);
					$writeCrmCreationLog("CRM limit sync response received", array(
						'url' => $target_url,
						'response' => $result,
						'http_code' => $code2,
						'curl_error' => $curlError2,
						'json_status' => is_array($resultData) ? ($resultData['status'] ?? '') : ''
					));
					$d->insert_log(
						"$companyId",
						"$bms_admin_id",
						"$created_by",
						"CRM setCrmLimit http=" . $code2 . " status=" . (is_array($resultData) ? ($resultData['status'] ?? 'na') : 'na')
					);
					$d->update("society_master", $a5, "society_id='$companyId'");
					$a1 = array('society_crm_id' => $society_crm_id, 'request_status' => '1', 'crm_created_by' => $bms_admin_id, 'crm_created_date' => date('Y-m-d H:i:s'));
					$d->update("crm_request_master", $a1, "crm_request_id='$Crm_request_id'");
					$writeCrmCreationLog("CRM creation completed successfully", array(
						'companyId' => $companyId,
						'society_crm_id' => $society_crm_id,
						'crm_request_id' => $Crm_request_id
					));

					$d->insert_log("0", "$bms_admin_id", "$created_by", "CRM Activated for $companyName");
					$_SESSION['msg'] = "CRM Added Successfully";
					header("location:../crmRequestsList");
					exit();
				} else {
					$writeCrmCreationLog("CRM limit missing in request, company not updated", array(
						'companyId' => $companyId,
						'crm_request_id' => $Crm_request_id
					));
				}
			} else {
				$writeCrmCreationLog("Society analytics update API failed", array(
					'http_code' => $code1,
					'response' => isset($response1) ? $response1 : ''
				));
				$d->insert_log(
					"$companyId",
					"$bms_admin_id",
					"$created_by",
					"CRM updateSocietyCrm failed http=" . (isset($code1) ? $code1 : 'na') . " status=" . (isset($response1Data) && is_array($response1Data) ? ($response1Data['status'] ?? 'na') : 'na')
				);
				$_SESSION['msg1'] = "Something Wrong with company API";
				header("location:../crmRequestsList");
				exit();
			}
		} else {
			$writeCrmCreationLog("CRM tenant API failed", array(
				'http_code' => $code
			));
			$d->insert_log(
				"$companyId",
				"$bms_admin_id",
				"$created_by",
				"CRM tenant API failed http=" . $code . " status=na"
			);
			$_SESSION['msg1'] = "Something Wrong with CRM API";
			header("location:../crmRequestsList");
			exit();
		}
	}
	if (isset($_POST['rejectCrm']) && $_POST['rejectCrm'] == "rejectCrm") {
		$rejected_reason = $_POST['rejected_reason'];
		$reject_crm_request_id = $_POST['reject_crm_request_id'];
		$reject_society_id = $_POST['reject_society_id'];

		$m->set_data('rejected_reason', $rejected_reason);
		$m->set_data('rejected_date', date("Y-m-d H:i:s"));
		$m->set_data('rejected_by', $bms_admin_id);
		$m->set_data('request_status', 2);

		$rejectCrm = array(
			'crm_rejected_reason' => $m->get_data('rejected_reason'),
			'crm_rejected_date' => $m->get_data('rejected_date'),
			'crm_rejected_by' => $m->get_data('rejected_by'),
			'request_status' => $m->get_data('request_status'),
		);

		$q = $d->update("crm_request_master", $rejectCrm, "crm_request_id = '$reject_crm_request_id' AND society_id = '$reject_society_id'");
		$writeCrmCreationLog("CRM request rejected", array(
			'crm_request_id' => $reject_crm_request_id,
			'companyId' => $reject_society_id,
			'rejected_reason' => $rejected_reason,
			'update_status' => $q,
			'rejected_by' => $created_by,
			'rejected_by_id' => $bms_admin_id
		));

		if ($q > 0) {
			$_SESSION['msg'] = "Request rejected successfully.";
		} else {
			$_SESSION['msg1'] = "Error rejecting request.";
		}

		header("Location: ../crmRequestsList");
		exit();
	}
} else {
	header('location:../logout.php');
}
