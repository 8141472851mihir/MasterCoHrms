<?php
include './common/object.php';
if (isset($_POST) && !empty($_POST)) {
	extract($_POST);
	$response = array();
	$data1 = array();
	$data2 = array();
	$data3 = array();
	$data4 = array();
	$data5 = array();
	$i = 1;
	$where = isset($_POST['where']) ? $_POST['where'] : '';
	$Status = isset($_POST['Status']) ? $_POST['Status'] : '';
	$platform_filter = isset($_POST['platform_filter']) ? $_POST['platform_filter'] : '';
	$notifications = isset($_POST['notifications']) ? $_POST['notifications'] : '';
	$role_id = isset($_POST['role_id']) ? $_POST['role_id'] : '';
	$countryAppendQuerySociety = isset($_POST['countryAppendQuerySociety']) ? $_POST['countryAppendQuerySociety'] : '';
	$module_type_filter = isset($_POST['module_type_filter']) ? $_POST['module_type_filter'] : '';
	$whereCondition = ($module_type_filter !== 'All' && $module_type_filter !== '' ? " AND feedback_master.module_type = '$module_type_filter'" : "");
	$support_person_filter = isset($_POST['support_person_filter']) ? trim((string) $_POST['support_person_filter']) : 'all';
	if (strcasecmp($support_person_filter, 'other') === 0) {
		$whereCondition .= " AND COALESCE(feedback_master.is_whitelabel, 0) = 1";
	} elseif ($support_person_filter !== '' && strcasecmp($support_person_filter, 'all') !== 0) {
		$escapedSupportName = $support_person_filter;
		$whereCondition .= " AND COALESCE(feedback_master.is_whitelabel, 0) = 0 AND society_master.support_name = '$escapedSupportName'";
	}
	$feedbackFilterHiddens = $d->feedbackListHiddenInputs($_POST);
	$feedbackFilterQuery = $d->feedbackListQueryString($_POST);
	$feedbackTimelineQuery = $feedbackFilterQuery !== '' ? '&' . $feedbackFilterQuery : '';
	// $where='';
	$countryAppendQuerySociety = '';
	$societyIdFilter = "feedback_master.society_id IS NOT NULL AND feedback_master.society_id > 0";
	$feedbackCompanySelect = "COALESCE(wl.society_name, society_master.society_name) AS society_name, COALESCE(wl_city.name, society_master.city_name) AS city_name, COALESCE(society_master.society_rating, 0) AS society_rating, society_master.support_name AS society_support_name";
	$feedbackSocietyJoin = "feedback_master
LEFT JOIN society_master ON society_master.society_id = feedback_master.society_id AND COALESCE(feedback_master.is_whitelabel, 0) = 0
LEFT JOIN society_master_white_label wl ON wl.society_id = feedback_master.society_id AND wl.project_type = feedback_master.whitelabel_type AND COALESCE(feedback_master.is_whitelabel, 0) = 1
LEFT JOIN cities wl_city ON wl_city.city_id = wl.city_id
LEFT JOIN bms_admin_master ON feedback_master.created_by = bms_admin_master.admin_id";
	function getOverdueMessage($status, $dateField, $limitHours, $label = '')
	{
		if (empty($dateField)) return '';

		$feedbackDate = new DateTime($dateField);
		$now = new DateTime();
		$interval = $feedbackDate->diff($now);
		$hoursDiff = ($interval->days * 24) + $interval->h + ($interval->i / 60);

		if ($hoursDiff <= $limitHours) return '';

		$overHours = floor($hoursDiff - $limitHours);
		$overMinutes = round(($hoursDiff - $limitHours - $overHours) * 60);

		$message = "<br><span class='text-danger'>Overdue by ";
		if ($overHours > 0) {
			$message .= "{$overHours} hour ";
		}
		$message .= "{$overMinutes} minute<br> $label</span>";

		return $message;
	}

	function formatFeedbackCompanyName($data)
	{
		$societyName = $data['society_name'] ?? '';
		$cityName = $data['city_name'] ?? '';
		if ((int)($data['is_whitelabel'] ?? 0) === 1) {
			$whitelabelTypeLabels = [
				0 => 'MyCo',
				1 => 'Smart Society',
				2 => 'My Association',
			];
			$typeLabel = $whitelabelTypeLabels[(int)($data['whitelabel_type'] ?? 0)] ?? 'Whitelabel';
			return $societyName . '-' . $cityName . ' (' . $typeLabel . ' Whitelabel)';
		}
		return $societyName . '-' . $cityName . ' (' . ($data['society_rating'] ?? 0) . "<i class='fa fa-star fa-sm text-warning'></i>)";
	}

	function formatFeedbackSupportPersonName($data)
	{
		if ((int)($data['is_whitelabel'] ?? 0) === 1) {
			return 'Other';
		}
		$supportName = trim((string)($data['society_support_name'] ?? ''));
		return $supportName !== '' ? htmlspecialchars($supportName) : '-';
	}

	function feedbackModuleTypeLabel($data, $forceShow = false)
	{
		static $moduleTypes = [
			0 => "Other",
			1 => "My Visits",
			2 => "Sales & Order",
			3 => "Work Report",
			4 => "Leave",
			5 => "Payroll",
			6 => "Attendance",
			7 => "Tasks",
			8 => "Tax Exemption",
			9 => "Performance Matrix",
			10 => "CRM",
			11 => "Tracking",
			12 => "Biometric Attendance",
			13 => "Face App",
			14 => "Loan",
			15 => "Expense",
			16 => "Advance Expense",
			17 => "Advance Salary",
			18 => "Holiday",
			19 => "Employee Document & Letter",
			20 => "App Not Opening",
			21 => "Log Issue",
			22 => "Management",
			23 => "Employee",
			24 => "Server Down",
			25 => "Server Timeout",
			26 => "Third Party Integration",
			27 => "Chat",
			28 => "Timeline",
			29 => "Assets",
			30 => "Site Management",
			31 => "Penalty",
		];
		$status = (int)($data['feedback_status'] ?? 0);
		$isTicket = (int)($data['isTicket'] ?? 0);
		$withDev = (int)($data['with_developer'] ?? 0);
		$show = $forceShow || $status === 5 || $withDev === 1 || $isTicket !== 0;
		if (!$show) {
			return '-';
		}
		$modKey = (int)($data['module_type'] ?? 0);
		return $moduleTypes[$modKey] ?? 'Other';
	}

	$overdueSettings = $d->select("feedback_overdue_settings", "feedback_overdue_id=1");
	if (mysqli_num_rows($overdueSettings) > 0) {
		$overdueData = mysqli_fetch_array($overdueSettings);

		$supportNewTicketOverdueLimit = !empty($overdueData['support_feedback_overdue_new_ticket_hours']) ? $overdueData['support_feedback_overdue_new_ticket_hours'] : '';
		$developerTicketOverdueLimit  = !empty($overdueData['developer_feedback_overdue_hours']) ? $overdueData['developer_feedback_overdue_hours'] : '';
		$closedDeveloperTicketOverdueLimit   = !empty($overdueData['support_feedback_overdue_after_close_hours']) ? $overdueData['support_feedback_overdue_after_close_hours'] : '';
	}

	if (isset($_POST['get_pending_tab_feedback'])) {
		$data1 = array();
		$count_data = array();
		$admin_wise_data = array();
		$q = $d->selectRow("bms_admin_master.*, feedback_master.*, $feedbackCompanySelect, bms_admin_master.platform as admin_platform, feedback_master.created_by as feedback_created_by, bms_admin_master.admin_name, bms_admin_master.active_status as admin_active_status", "$feedbackSocietyJoin", "$societyIdFilter AND feedback_master.feedback_status != '2' AND feedback_master.inquiry_type='0' AND feedback_master.with_developer = 0 $countryAppendQuerySociety $where $whereCondition", "ORDER BY feedback_id DESC");
		while ($data = mysqli_fetch_array($q)) {
			if ($bms_admin_id == $data['feedback_created_by']) {
				$status = $data['feedback_status'];
				if (isset($count_data[$status])) {
					$count_data[$status]['count']++;
				} else {
					$count_data[$status] = array('feedback_status' => $status, 'count' => 1);
				}
			}
			if ($role_id == '1') {
				$admin_id = $data['feedback_created_by'];
				$admin_name = $data['admin_name'];
				$feedback_status = $data['feedback_status'];
				if (!isset($admin_wise_data[$admin_id])) {
					$admin_wise_data[$admin_id] = [
						'id' => $admin_id,
						'name' => $admin_name,
						'active_status' => $data['admin_active_status'] ?? '',
						'status_count' => []
					];
				}
				if (!isset($admin_wise_data[$admin_id]['status_count'][$feedback_status])) {
					$admin_wise_data[$admin_id]['status_count'][$feedback_status] = 0;
				}
				$admin_wise_data[$admin_id]['status_count'][$feedback_status]++;
			}

			$row = array();
			$row['abc'] = '<td class="text-center">' .
				($role_id == 1 ?
					'<input type="checkbox" class="multiDelteCheckbox" value="' . $data['feedback_id'] . '">'
					: '') .
				'</td>';

			$row['sr_no'] = $i++;
			$action = "<td class='tableWidth'>";
			if (($data['isTicket'] == 1 && $data['feedback_status'] == 5) || $data['isTicket'] == 0) {
				$action .= "<button data-toggle='modal' data-target='#closeRemarksModal' title='Query Solved?' class='btn btn-primary btn-sm mx-1 d-inline-block' onclick='remarkFeedback({$data['feedback_id']}, \"{$data['email']}\")'><i class='fa fa-check'></i></button>";
			}
			$action .= "<a href='feedbackTimeline?id={$data['feedback_id']}{$feedbackTimelineQuery}' class='btn btn-info btn-sm mx-1 d-inline-block'>View</a>";
			if ($notifications) {
				$action .= "<span class='badge badge-warning'>New Reply</span>";
			}
			if ($role_id == 1) {
				$action .= "<form id='delete-form-{$data['feedback_id']}' action='controller/feedbackController.php' method='POST' class='d-inline-block'>
				<input type='hidden' name='feedback_id' value='{$data['feedback_id']}'>
				<input type='hidden' name='deleteFeedback' value='deleteFeedback'>
				{$feedbackFilterHiddens}
				<input type='hidden' name='csrf' value='{$csrf}'> 
				<button type='button' data-feedback-id='{$data['feedback_id']}' id='deleteButton' class='deleteButton btn btn-danger btn-sm mx-1 d-inline-block'><i class='fa fa-trash-o'></i></button>
				</form>";
			}
			if ($data['isTicket'] == 0 && $data['feedback_status'] != 6) {
				$action .= "<button data-toggle='modal' data-target='#forwardToDeveloperModal' class='btn btn-dark btn-sm mx-1 d-inline-block' onclick='setForwardData({$data['feedback_id']}, {$data['society_id']})'> <i class='fa fa-arrow-right'></i></button>";
			}
			$row['action'] = $action . "</td>";
			$row['feedback_id'] = "<td class='tableWidth'>" . '#TKT' . $data['feedback_id'] . '&nbsp;&nbsp;' . (($data['read_by'] == 0) ? "<span class='badge badge-warning'>Unread</span>" : '') . "</td>";
			$row['subject'] = "<td><div style='overflow-y: auto; max-width: 400px; max-height: 6em; overflow-x: hidden; word-wrap: break-word; white-space: normal;'>" . htmlspecialchars($data['subject']) . "</div></td>";
			$row['feedback_msg'] = $data['feedback_msg'];
			$row['onlyfeedback_id'] = $data['feedback_id'];
			$statusBadges = [
				0 => "<span class='badge badge-warning'>Pending</span>",
				1 => "<span class='badge badge-default'>In Progress</span>",
				3 => "<span class='badge badge-warning'>In Progress</span>",
				4 => "<span class='badge badge-danger'>Rejected</span>",
				5 => "<span class='badge badge-success'>Closed by Developer</span>",
				6 => "<span class='badge badge-danger'>Rejected by Developer</span>"
			];
			$row['status'] = $statusBadges[$data['feedback_status']] ?? "<span class='badge badge-secondary'></span>";

			if ($data['feedback_status'] == 5) {
				$todayDateTime = date('Y-m-d H:i:s');
				$developerSolveDateTime = date('Y-m-d H:i:s', strtotime("+24 hours", strtotime($data['developer_solve_time'])));
				if ($developerSolveDateTime > $todayDateTime) {
					$row['status'] .= "<button data-toggle='modal' data-target='#reopenRemarksModal' class='badge badge-primary mx-1 btn-sm d-inline-block' onclick='reopenRemarkFeedback({$data['feedback_id']}, \"{$data['developer_solve_time']}\");'>Reopen</button>";
				}
			}

			if ($data['feedback_status'] == 0 && !empty($data['feedback_date_time'])) {
				$overdueMessage1 = getOverdueMessage($data['feedback_status'], $data['feedback_date_time'], $supportNewTicketOverdueLimit, '(New Ticket)');
				$row['status'] .= $overdueMessage1;
			}

			if ($data['feedback_status'] == 5 && !empty($data['developer_solve_time'])) {
				$overdueMessage2 = getOverdueMessage($data['feedback_status'], $data['developer_solve_time'], $closedDeveloperTicketOverdueLimit, '');
				$row['status'] .= $overdueMessage2;
			}

			$row['company_name'] = formatFeedbackCompanyName($data);
			$row['support_person_name'] = formatFeedbackSupportPersonName($data);

			$row['name'] = $data['name'];
			$row['platform'] = match ((int) $data['platform']) {
				0 => "Other",
				1 => "Android",
				2 => "iOS",
				3 => "Web",
				4 => "CRM",
				default => ""
			};
			$row['issue_type'] = match ((int) $data['issueType']) {
				1 => "Bug",
				2 => "Configuration Issue",
				3 => "Training Issue",
				4 => "Issue Not Found",
				5 => "Change Request by Client",
				6 => "Device Specific Issue",
				7 => "Data Delete Request",
				8 => "Not an issue",
				9 => "Internet Connectivity Issue",
				default => "-"
			};
			$row['module_type'] = feedbackModuleTypeLabel($data);
			$row['created_date'] = $data['feedback_date_time'];
			$row['created_by'] = ($data['created_by'] > 0) ? $data['admin_name'] : $d->app_name() . " App";

			array_push($data1, $row);
		}

		$loggedInClosedByDevCount = 0;
		$loggedInRejectedByDevCount = 0;
		if ($role_id == '1') {
			foreach ($admin_wise_data as $admin_id => $adminRow) {
				$admin_wise_data[$admin_id]['status_count'][5] = 0;
				$admin_wise_data[$admin_id]['status_count'][6] = 0;
			}
		}
		$whereWithoutStatus = preg_replace("/AND feedback_master\\.feedback_status='[0-9]+'/", '', (string) $where);
		$devActionQ = $d->selectRow(
			"feedback_master.created_by as feedback_created_by, feedback_master.feedback_status, bms_admin_master.admin_name, bms_admin_master.active_status as admin_active_status",
			$feedbackSocietyJoin,
			"$societyIdFilter AND feedback_master.feedback_status IN ('5','6') AND feedback_master.inquiry_type='0' AND feedback_master.with_developer = 0 $countryAppendQuerySociety $whereWithoutStatus $whereCondition"
		);
		if ($devActionQ) {
			while ($cbd = mysqli_fetch_array($devActionQ)) {
				$admin_id = $cbd['feedback_created_by'];
				$fbStatus = (int) $cbd['feedback_status'];
				if ((string) $admin_id === (string) $bms_admin_id) {
					if ($fbStatus === 5) {
						$loggedInClosedByDevCount++;
					} elseif ($fbStatus === 6) {
						$loggedInRejectedByDevCount++;
					}
				}
				if ($role_id == '1') {
					if (!isset($admin_wise_data[$admin_id])) {
						$admin_wise_data[$admin_id] = [
							'id' => $admin_id,
							'name' => $cbd['admin_name'],
							'active_status' => $cbd['admin_active_status'] ?? '',
							'status_count' => []
						];
					} elseif (!isset($admin_wise_data[$admin_id]['active_status']) || $admin_wise_data[$admin_id]['active_status'] === '') {
						$admin_wise_data[$admin_id]['active_status'] = $cbd['admin_active_status'] ?? '';
					}
					if (!isset($admin_wise_data[$admin_id]['status_count'][$fbStatus])) {
						$admin_wise_data[$admin_id]['status_count'][$fbStatus] = 0;
					}
					$admin_wise_data[$admin_id]['status_count'][$fbStatus]++;
				}
			}
		}
		if ($role_id == '1') {
			foreach ($admin_wise_data as $admin_id => $adminRow) {
				if (!isset($admin_wise_data[$admin_id]['status_count'][5])) {
					$admin_wise_data[$admin_id]['status_count'][5] = 0;
				}
				if (!isset($admin_wise_data[$admin_id]['status_count'][6])) {
					$admin_wise_data[$admin_id]['status_count'][6] = 0;
				}
			}
		}

		echo json_encode(array(
			"data" => $data1,
			"admin_wise_data" => $admin_wise_data,
			"count_data" => $count_data,
			"logged_in_closed_by_dev_count" => $loggedInClosedByDevCount,
			"logged_in_rejected_by_dev_count" => $loggedInRejectedByDevCount,
			"logged_in_admin_id" => $bms_admin_id
		));
	} else if (isset($_POST['need_spec_tab_feedback'])) {
		$data3 = [];
		$count_data = array();
		$admin_wise_data = array();
		$i = 1;
		$q = $d->selectRow(
			"bms_admin_master.*, feedback_master.*, $feedbackCompanySelect, bms_admin_master.platform as admin_platform, feedback_master.created_by as feedback_created_by, bms_admin_master.admin_name, bms_admin_master.active_status as admin_active_status",
			"$feedbackSocietyJoin",
			"$societyIdFilter AND feedback_master.feedback_status = '7' AND feedback_master.inquiry_type='0' AND feedback_master.with_developer = 1 $countryAppendQuerySociety $where $whereCondition",
			"ORDER BY feedback_id DESC"
		);

		$feedbackRows = [];
		$feedbackIds = [];
		while ($data = mysqli_fetch_array($q)) {
			$feedbackRows[] = $data;
			$feedbackIds[] = (int)$data['feedback_id'];
		}

		$lastLogByFeedback = [];
		if (!empty($feedbackIds)) {
			$feedbackIdsIn = implode(',', array_map('intval', $feedbackIds));
			$checkLastLog = $d->selectRow(
				"feedback_id,feedback_log_date,feedback_log_id",
				"feedback_log_master",
				"feedback_id IN ($feedbackIdsIn)",
				"ORDER BY feedback_log_id DESC"
			);
			while ($logRow = mysqli_fetch_array($checkLastLog)) {
				$fid = (int)$logRow['feedback_id'];
				if (!isset($lastLogByFeedback[$fid])) {
					$lastLogByFeedback[$fid] = $logRow['feedback_log_date'];
				}
			}
		}

		foreach ($feedbackRows as $data) {
			if ($bms_admin_id == $data['created_by']) {
				$status = $data['feedback_status'];
				if (isset($count_data[$status])) {
					$count_data[$status]['count']++;
				} else {
					$count_data[$status] = array('feedback_status' => $status, 'count' => 1);
				}
			}
			if ($role_id == '1') {
				$admin_id = $data['feedback_created_by'];
				$admin_name = $data['admin_name'];
				$feedback_status = $data['feedback_status'];
				if (!isset($admin_wise_data[$admin_id])) {
					$admin_wise_data[$admin_id] = [
						'id' => $admin_id,
						'name' => $admin_name,
						'active_status' => $data['admin_active_status'] ?? '',
						'status_count' => []
					];
				}
				if (!isset($admin_wise_data[$admin_id]['status_count'][$feedback_status])) {
					$admin_wise_data[$admin_id]['status_count'][$feedback_status] = 0;
				}
				$admin_wise_data[$admin_id]['status_count'][$feedback_status]++;
			}
			$row = [];

			$row['#'] = '<td class="text-center">' .
				($role_id == 1 ?
					'<input type="checkbox" class="multiDelteCheckbox" value="' . $data['feedback_id'] . '">'
					: '') .
				'</td>';

			$row['sr_no'] = $i++;
			$action = '';
			if ($data['isTicket'] == 2 && $role_id == 1) {
				$action .= "<form class='d-inline-block' action='controller/feedbackController.php' method='post'>
				<input type='hidden' name='feedback_id' value='{$data['feedback_id']}'>
				<input type='hidden' name='society_id' value='{$data['society_id']}'>
				<input type='hidden' name='isTicket' value='{$data['isTicket']}'>
				<input type='hidden' name='csrf' value='{$csrf}'> 
				<button type='submit' title='Generate Ticket' class='btn mx-1 btn-warning btn-sm form-btn'><i class='fa fa-ticket fa-lg'></i></button>
				</form>";
				// Reject by Developer button
				$admin_email = $d->encryptDecrypt("decrypt", $data['admin_email']);
				$action .= "<button data-toggle='modal' data-target='#rejectbyDeveloper' title='Reject by Developer?' class='btn mx-1 btn-danger btn-sm' onclick='RejectByDeveloper(\"{$data['feedback_id']}\", \"{$admin_email}\")'>
				<i class='fa fa-times fa-lg'></i>
				</button>";
			}

			if ($role_id == 1 && $data['isTicket'] == 1 && $data['feedback_status'] != 5) {
				$action .= "<button data-toggle='modal' data-target='#closebyDeveloper' title='Closed by Developer?' class='btn mx-1 btn-primary btn-sm d-inline-block' 
				onclick='DeveloperReply(\"{$data['feedback_id']}\", \"{$data['email']}\", \"{$data['society_id']}\", \"{$data['created_by']}\")'>
				<i class='fa fa-check fa-lg'></i>
				</button>";
			}
			$action .= "<a href='feedbackTimeline?id={$data['feedback_id']}{$feedbackTimelineQuery}' class='btn btn-info btn-sm mx-1 d-inline-block'> <i class='fa fa-eye fa-lg'></i></a>";
			if ($notifications) {
				$action .= "<span class='badge badge-warning'>New Reply</span>";
			}
			if ($data['isTicket'] == 1) {
				$action .= "<button data-toggle='modal' data-target='#replyModal' class='btn mx-1 btn-primary btn-sm d-inline-block' 
					onclick='replyFeedback(\"{$data['feedback_id']}\", \"{$data['email']}\")'>
					<i class='fa fa-reply'></i>
					</button>";
			}
			if ($role_id == 1) {
				$action .= "<form id='delete-form-{$data['feedback_id']}' action='controller/feedbackController.php' method='POST' class='d-inline-block'>

				<input type='hidden' name='feedback_id' value='{$data['feedback_id']}'>
				<input type='hidden' name='deleteFeedback' value='deleteFeedback'>
				{$feedbackFilterHiddens}
				<input type='hidden' name='csrf' value='{$csrf}'> 
				<button type='button' data-feedback-id='{$data['feedback_id']}' id='deleteButton' class='deleteButton btn mx-1 btn-danger btn-sm form-btn'>
				<i class='fa fa-trash-o fa-lg'></i>
				</button>

				</form>";
			}
			$row['action'] = $action;
			$row['feedback_id'] = '#TKT' . $data['feedback_id'] . '&nbsp;&nbsp;' . (($data['read_by'] == 0) ? "<span class='badge badge-warning'>Unread</span>" : '');
			$row['subject'] = "<td><div style='overflow-y: auto; max-width: 400px; max-height: 6em; overflow-x: hidden; word-wrap: break-word; white-space: normal;'>" . htmlspecialchars($data['subject']) . "</div></td>";
			$row['feedback_msg'] = $data['feedback_msg'];
			$row['platform'] = match ((int) $data['platform']) {
				0 => "Other",
				1 => "Android",
				2 => "iOS",
				3 => "Web",
				4 => "CRM",
				default => ""
			};
			$statusBadges = [
				0 => "<span class='badge badge-warning'>Pending</span>",
				1 => "<span class='badge badge-default'>In Progress</span>",
				3 => "<span class='badge badge-warning'>On Hold</span>",
				4 => "<span class='badge badge-danger'>Rejected</span>",
				5 => "<span class='badge badge-success'>Closed by Developer</span>",
				6 => "<span class='badge badge-danger'>Rejected by Developer</span>",
				7 => "<span class='badge badge-danger'>Need More Specification</span>",
				8 => "<span class='badge badge-danger'>Resolved in next update</span>"
			];
			$row['status'] = $statusBadges[$data['feedback_status']] ?? "<span class='badge badge-secondary'></span>";
			if ($data['feedback_status'] == 5) {
				$todayDateTime = date('Y-m-d H:i:s');
				$developerSolveDateTime = date('Y-m-d H:i:s', strtotime("+24 hours", strtotime($data['developer_solve_time'])));
				if ($developerSolveDateTime > $todayDateTime) {
					$row['status'] .= "<button data-toggle='modal' data-target='#reopenRemarksModal' class='badge badge-primary mx-1 btn-sm d-inline-block' onclick='reopenRemarkFeedback({$data['feedback_id']}, \"{$data['developer_solve_time']}\");'>Reopen</button>";
				}
			}
			$feedback_id = (int)$data['feedback_id'];
			if (isset($lastLogByFeedback[$feedback_id])) {
				$lastLogDate = $lastLogByFeedback[$feedback_id];
				if ($data['feedback_status'] == 7 && !empty($lastLogDate)) {
					$overdueMessage3 = getOverdueMessage($data['feedback_status'], $lastLogDate, $closedDeveloperTicketOverdueLimit, '');
					$row['status'] .= $overdueMessage3;
				}
			}

			$row['company_name'] = formatFeedbackCompanyName($data);
			$row['support_person_name'] = formatFeedbackSupportPersonName($data);
			$row['name'] = $data['name'];
			$row['created_date'] = $data['feedback_date_time'];
			$row['assign_date'] = $data['develeoper_assign_time'];
			$row['created_by'] = ($data['created_by'] > 0) ? $data['admin_name'] : $d->app_name() . " App";
			$row['reopen_remarks'] = "<td><div style='overflow-y: auto; max-width: 400px; max-height: 6em; overflow-x: hidden; word-wrap: break-word; white-space: normal;'>" . htmlspecialchars($data['reopen_remarks']) . "</div></td>";
			array_push($data3, $row);
		}
		echo json_encode(array(
			"data" => $data3,
			"admin_wise_data" => $admin_wise_data,
			"count_data" => $count_data
		));
	} else if (isset($_POST['next_update_tab_feedback'])) {
		$data4 = [];
		$count_data = array();
		$admin_wise_data = array();
		$i = 1;
		$q = $d->selectRow(
			"bms_admin_master.*, feedback_master.*, $feedbackCompanySelect, bms_admin_master.platform as admin_platform",
			"$feedbackSocietyJoin",
			"$societyIdFilter AND feedback_master.feedback_status = '8' AND feedback_master.inquiry_type='0' AND feedback_master.with_developer = 1 $countryAppendQuerySociety $where $whereCondition",
			"ORDER BY feedback_id DESC"
		);

		while ($data = mysqli_fetch_array($q)) {
			if ($bms_admin_id == $data['created_by']) {
				$status = $data['feedback_status'];
				if (isset($count_data[$status])) {
					$count_data[$status]['count']++;
				} else {
					$count_data[$status] = array('feedback_status' => $status, 'count' => 1);
				}
			}
			$row = [];

			$row['#'] = '<td class="text-center">' .
				($role_id == 1 ?
					'<input type="checkbox" class="multiDelteCheckbox" value="' . $data['feedback_id'] . '">'
					: '') .
				'</td>';

			$row['sr_no'] = $i++;
			$action = '';
			if ($data['isTicket'] == 2 && $role_id == 1) {
				$action .= "<form class='d-inline-block' action='controller/feedbackController.php' method='post'>
				<input type='hidden' name='feedback_id' value='{$data['feedback_id']}'>
				<input type='hidden' name='society_id' value='{$data['society_id']}'>
				<input type='hidden' name='isTicket' value='{$data['isTicket']}'>
				<input type='hidden' name='csrf' value='{$csrf}'> 
				<button type='submit' title='Generate Ticket' class='btn mx-1 btn-warning btn-sm form-btn'><i class='fa fa-ticket fa-lg'></i></button>
				</form>";
				$admin_email = $d->encryptDecrypt("decrypt", $data['admin_email']);
				$action .= "<button data-toggle='modal' data-target='#rejectbyDeveloper' title='Reject by Developer?' class='btn mx-1 btn-danger btn-sm' onclick='RejectByDeveloper(\"{$data['feedback_id']}\", \"{$admin_email}\")'>
				<i class='fa fa-times fa-lg'></i>
				</button>";
			}

			if ($role_id == 1 && $data['isTicket'] == 1 && $data['feedback_status'] != 5) {
				$action .= "<button data-toggle='modal' data-target='#closebyDeveloper' title='Closed by Developer?' class='btn mx-1 btn-primary btn-sm d-inline-block' 
				onclick='DeveloperReply(\"{$data['feedback_id']}\", \"{$data['email']}\", \"{$data['society_id']}\", \"{$data['created_by']}\")'>
				<i class='fa fa-check fa-lg'></i>
				</button>";
			}
			$action .= "<a href='feedbackTimeline?id={$data['feedback_id']}{$feedbackTimelineQuery}' class='btn btn-info btn-sm mx-1 d-inline-block'> <i class='fa fa-eye fa-lg'></i></a>";
			if ($notifications) {
				$action .= "<span class='badge badge-warning'>New Reply</span>";
			}
			if ($data['isTicket'] == 1) {
				$action .= "<button data-toggle='modal' data-target='#replyModal' class='btn mx-1 btn-primary btn-sm d-inline-block' 
				onclick='replyFeedback(\"{$data['feedback_id']}\", \"{$data['email']}\")'>
				<i class='fa fa-reply'></i>
				</button>";
			}
			if ($role_id == 1) {
				$action .= "<form id='delete-form-{$data['feedback_id']}' action='controller/feedbackController.php' method='POST' class='d-inline-block'>

				<input type='hidden' name='feedback_id' value='{$data['feedback_id']}'>
				<input type='hidden' name='deleteFeedback' value='deleteFeedback'>
				{$feedbackFilterHiddens}
				<input type='hidden' name='csrf' value='{$csrf}'> 
				<button type='button' data-feedback-id='{$data['feedback_id']}' id='deleteButton' class='deleteButton btn mx-1 btn-danger btn-sm form-btn'>
				<i class='fa fa-trash-o fa-lg'></i>
				</button>

				</form>";
			}
			$row['action'] = $action;
			$row['feedback_id'] = '#TKT' . $data['feedback_id'] . '&nbsp;&nbsp;' . (($data['read_by'] == 0) ? "<span class='badge badge-warning'>Unread</span>" : '');
			$row['subject'] = "<td><div style='overflow-y: auto; max-width: 400px; max-height: 6em; overflow-x: hidden; word-wrap: break-word; white-space: normal;'>" . htmlspecialchars($data['subject']) . "</div></td>";
			$row['feedback_msg'] = $data['feedback_msg'];
			$row['platform'] = match ((int) $data['platform']) {
				0 => "Other",
				1 => "Android",
				2 => "iOS",
				3 => "Web",
				4 => "CRM",
				default => ""
			};
			$statusBadges = [
				0 => "<span class='badge badge-warning'>Pending</span>",
				1 => "<span class='badge badge-default'>In Progress</span>",
				3 => "<span class='badge badge-warning'>On Hold</span>",
				4 => "<span class='badge badge-danger'>Rejected</span>",
				5 => "<span class='badge badge-success'>Closed by Developer</span>",
				6 => "<span class='badge badge-danger'>Rejected by Developer</span>",
				7 => "<span class='badge badge-danger'>Need More Specification</span>",
				8 => "<span class='badge badge-danger'>Resolved in next update</span>"
			];
			$row['status'] = $statusBadges[$data['feedback_status']] ?? "<span class='badge badge-secondary'></span>";
			if ($data['feedback_status'] == 5) {
				$todayDateTime = date('Y-m-d H:i:s');
				$developerSolveDateTime = date('Y-m-d H:i:s', strtotime("+24 hours", strtotime($data['developer_solve_time'])));
				if ($developerSolveDateTime > $todayDateTime) {
					$row['status'] .= "<button data-toggle='modal' data-target='#reopenRemarksModal' class='badge badge-primary mx-1 btn-sm d-inline-block' onclick='reopenRemarkFeedback({$data['feedback_id']}, \"{$data['developer_solve_time']}\");'>Reopen</button>";
				}
			}

			if ($data['feedback_status'] == 8 && !empty($data['develeoper_assign_time'])) {
				$overdueMessage4 = getOverdueMessage($data['feedback_status'], $data['develeoper_assign_time'], $developerTicketOverdueLimit, '');
				$row['status'] .= $overdueMessage4;
			}

			$row['company_name'] = formatFeedbackCompanyName($data);
			$row['support_person_name'] = formatFeedbackSupportPersonName($data);

			$row['name'] = $data['name'];
			$row['created_date'] = $data['feedback_date_time'];
			$row['assign_date'] = $data['develeoper_assign_time'];
			$row['created_by'] = ($data['created_by'] > 0) ? $data['admin_name'] : $d->app_name() . " App";
			$row['reopen_remarks'] = "<td><div style='overflow-y: auto; max-width: 400px; max-height: 6em; overflow-x: hidden; word-wrap: break-word; white-space: normal;'>" . htmlspecialchars($data['reopen_remarks']) . "</div></td>";
			array_push($data4, $row);
		}
		echo json_encode(array(
			"data" => $data4,
			"count_data" => $count_data
		));
	} else if (isset($_POST['withDeveloper_tab_feedback'])) {
		$data5 = [];
		$count_data = array();
		$i = 1;
		$q = $d->selectRow(
			"bms_admin_master.*, feedback_master.*, $feedbackCompanySelect, bms_admin_master.platform as admin_platform",
			"$feedbackSocietyJoin",
			"$societyIdFilter AND feedback_master.feedback_status != '2' AND feedback_master.feedback_status != '7' AND feedback_master.feedback_status != '8' AND feedback_master.inquiry_type='0' AND feedback_master.with_developer = 1 $countryAppendQuerySociety $where $whereCondition",
			"ORDER BY feedback_id DESC"
		);

		while ($data = mysqli_fetch_array($q)) {
			if ($bms_admin_id == $data['created_by']) {
				$status = $data['feedback_status'];
				if (isset($count_data[$status])) {
					$count_data[$status]['count']++;
				} else {
					$count_data[$status] = array('feedback_status' => $status, 'count' => 1);
				}
			}
			$row = [];

			$row['#'] = '<td class="text-center">' .
				($role_id == 1 ?
					'<input type="checkbox" class="multiDelteCheckbox" value="' . $data['feedback_id'] . '">'
					: '') .
				'</td>';

			$row['sr_no'] = $i++;
			$action = '';
			if ($data['isTicket'] == 2 && $role_id == 1) {
				$action .= "<form class='d-inline-block' action='controller/feedbackController.php' method='post'>
				<input type='hidden' name='feedback_id' value='{$data['feedback_id']}'>
				<input type='hidden' name='society_id' value='{$data['society_id']}'>
				<input type='hidden' name='isTicket' value='{$data['isTicket']}'>
				<input type='hidden' name='csrf' value='{$csrf}'> 
				<button type='submit' title='Generate Ticket' class='btn mx-1 btn-warning btn-sm form-btn'><i class='fa fa-ticket fa-lg'></i></button>
				</form>";
				$admin_email = $d->encryptDecrypt("decrypt", $data['admin_email']);
				$action .= "<button data-toggle='modal' data-target='#rejectbyDeveloper' title='Reject by Developer?' class='btn mx-1 btn-danger btn-sm' onclick='RejectByDeveloper(\"{$data['feedback_id']}\", \"{$admin_email}\")'>
				<i class='fa fa-times fa-lg'></i>
				</button>";
			}
			if ($role_id == 1 && $data['isTicket'] == 1 && $data['feedback_status'] != 5) {
				$action .= "<button data-toggle='modal' data-target='#closebyDeveloper' title='Closed by Developer?' class='btn mx-1 btn-primary btn-sm d-inline-block' 
				onclick='DeveloperReply(\"{$data['feedback_id']}\", \"{$data['email']}\", \"{$data['society_id']}\", \"{$data['created_by']}\")'>
				<i class='fa fa-check fa-lg'></i>
				</button>";
			}
			$action .= "<a href='feedbackTimeline?id={$data['feedback_id']}{$feedbackTimelineQuery}' class='btn btn-info btn-sm mx-1 d-inline-block'> <i class='fa fa-eye fa-lg'></i></a>";

			if ($notifications) {
				$action .= "<span class='badge badge-warning'>New Reply</span>";
			}
			if ($data['isTicket'] == 1) {
				$action .= "<button data-toggle='modal' data-target='#replyModal' class='btn mx-1 btn-primary btn-sm d-inline-block' 
				onclick='replyFeedback(\"{$data['feedback_id']}\", \"{$data['email']}\")'>
				<i class='fa fa-reply'></i>
				</button>";
			}
			if ($role_id == 1) {
				$action .= "<form id='delete-form-{$data['feedback_id']}' action='controller/feedbackController.php' method='POST' class='d-inline-block'>
				<input type='hidden' name='feedback_id' value='{$data['feedback_id']}'>
				<input type='hidden' name='deleteFeedback' value='deleteFeedback'>
				{$feedbackFilterHiddens}
				<input type='hidden' name='csrf' value='{$csrf}'> 
				<button type='button' data-feedback-id='{$data['feedback_id']}' id='deleteButton' class='deleteButton btn mx-1 btn-danger btn-sm form-btn'>
				<i class='fa fa-trash-o fa-lg'></i>
				</button>

				</form>";
			}
			$row['action'] = $action;
			$row['feedback_id'] = '#TKT' . $data['feedback_id'] . '&nbsp;&nbsp;' . (($data['read_by'] == 0) ? "<span class='badge badge-warning'>Unread</span>" : '');
			$row['subject'] = "<td><div style='overflow-y: auto; max-width: 400px; max-height: 6em; overflow-x: hidden; word-wrap: break-word; white-space: normal;'>" . htmlspecialchars($data['subject']) . "</div></td>";
			$row['feedback_msg'] = $data['feedback_msg'];
			$row['platform'] = match ((int) $data['platform']) {
				0 => "Other",
				1 => "Android",
				2 => "iOS",
				3 => "Web",
				4 => "CRM",
				default => ""
			};
			$row['module_type'] = feedbackModuleTypeLabel($data, true);
			$statusBadges = [
				0 => "<span class='badge badge-warning'>Pending</span>",
				1 => "<span class='badge badge-default'>In Progress</span>",
				3 => "<span class='badge badge-warning'>On Hold</span>",
				4 => "<span class='badge badge-danger'>Rejected</span>",
				5 => "<span class='badge badge-success'>Closed by Developer</span>",
				6 => "<span class='badge badge-danger'>Rejected by Developer</span>",
				7 => "<span class='badge badge-danger'>Need More Specification</span>",
				8 => "<span class='badge badge-danger'>Resolved in next update</span>"
			];
			$row['status'] = $statusBadges[$data['feedback_status']] ?? "<span class='badge badge-secondary'></span>";
			if ($data['feedback_status'] == 5) {
				$todayDateTime = date('Y-m-d H:i:s');
				$developerSolveDateTime = date('Y-m-d H:i:s', strtotime("+24 hours", strtotime($data['developer_solve_time'])));
				if ($developerSolveDateTime > $todayDateTime) {
					$row['status'] .= "<button data-toggle='modal' data-target='#reopenRemarksModal' class='badge badge-primary mx-1 btn-sm d-inline-block' onclick='reopenRemarkFeedback({$data['feedback_id']}, \"{$data['developer_solve_time']}\");'>Reopen</button>";
				}
			}

			if (!empty($data['develeoper_assign_time'])) {
				$overdueMessage4 = getOverdueMessage($data['feedback_status'], $data['develeoper_assign_time'], $developerTicketOverdueLimit, '');
				$row['status'] .= $overdueMessage4;
			}

			$row['company_name'] = formatFeedbackCompanyName($data);
			$row['support_person_name'] = formatFeedbackSupportPersonName($data);

			$row['name'] = $data['name'];
			$row['created_date'] = $data['feedback_date_time'];
			$row['assign_date'] = $data['develeoper_assign_time'];
			$row['created_by'] = ($data['created_by'] > 0) ? $data['admin_name'] : $d->app_name() . " App";
			$row['reopen_remarks'] = "<td><div style='overflow-y: auto; max-width: 400px; max-height: 6em; overflow-x: hidden; word-wrap: break-word; white-space: normal;'>" . htmlspecialchars($data['reopen_remarks']) . "</div></td>";
			array_push($data5, $row);
		}
		echo json_encode(array(
			"data" => $data5,
			"count_data" => array()
		));
	} else if (isset($_POST['solved_tab_feedback'])) {
		$data2 = array();
		$q = $d->selectRow(
			"bms_admin_master.*, feedback_master.*, $feedbackCompanySelect, bms_admin_master.platform as admin_platform",
			"$feedbackSocietyJoin",
			"$societyIdFilter AND feedback_master.feedback_status = '2' AND feedback_master.inquiry_type='0' $countryAppendQuerySociety $where $whereCondition",
			"ORDER BY feedback_id DESC"
		);

		$i = 1;
		while ($data = mysqli_fetch_array($q)) {
			$row = array();
			$row['abc'] = '<td class="text-center">' .
				($role_id == 1 ? '<input type="checkbox" class="multiDelteCheckbox" value="' . $data['feedback_id'] . '">' : '') .
				'</td>';
			$row['sr_no'] = "<td class='tableWidth'>" . $i++ . "</td>";
			$action = "<td>";
			$action .= "<a href='feedbackTimeline?id={$data['feedback_id']}{$feedbackTimelineQuery}' class='btn btn-info btn-sm mx-1 d-inline-block'> View</a>";
			$action .= "<form id='delete-form-{$data['feedback_id']}' class='d-inline-block' action='controller/feedbackController.php' method='POST'>
			<input type='hidden' name='feedback_id' value='{$data['feedback_id']}'>
			<input type='hidden' name='deleteFeedback' value='deleteFeedback'>
			{$feedbackFilterHiddens}
			<input type='hidden' name='csrf' value='{$csrf}'> 
			<button type='button' id='deleteButton' data-feedback-id='{$data['feedback_id']}' class='deleteButton btn btn-danger mx-1 btn-sm form-btn'><i class='fa fa-trash-o'></i></button>
			</form>";
			if ($notifications) {
				$action .= "<span class='badge badge-warning'>New Reply</span>";
			}
			$action .= "</td>";
			$row['action'] = "<td class='tableWidth'>" . $action . "</td>";
			$row['feedback_id'] = "<td class='tableWidth'>" . '#TKT' . $data['feedback_id'] . "</td>";
			$row['company_name'] = "<td class='tableWidth'>" . formatFeedbackCompanyName($data) . "</td>";
			$row['support_person_name'] = "<td class='tableWidth'>" . formatFeedbackSupportPersonName($data) . "</td>";
			$row['subject'] = "<td><div style='overflow-y: auto; max-width: 400px; max-height: 6em; overflow-x: hidden; word-wrap: break-word; white-space: normal;'>" . htmlspecialchars($data['subject']) . "</div></td>";
			$row['feedback_msg'] = $data['feedback_msg'];
			$row['name'] = "<td class='tableWidth'>" . $data['name'] . "</td>";
			$platform_name = match ((int) $data['platform']) {
				0 => "Other",
				1 => "Android",
				2 => "iOS",
				3 => "Web",
				4 => "CRM",
				default => ""
			};
			$row['platform'] = "<td class='tableWidth'>" . $platform_name . "</td>";
			$solvedModuleType = ((int)($data['isTicket'] ?? 0) !== 0) ? feedbackModuleTypeLabel($data, true) : '-';
			$row['module_type'] = "<td class='tableWidth'>" . htmlspecialchars($solvedModuleType) . "</td>";
			$row['close_remarks'] = "<td><div style='overflow-y: auto; max-width: 400px; max-height: 6em; overflow-x: hidden; word-wrap: break-word; white-space: normal;'>" . htmlspecialchars($data['closing_remarks']) . "</div></td>";
			if ($role_id == 1) {
				$row['client_reply_message'] = "<td><div style='overflow-y: auto; max-width: 400px; max-height: 6em; overflow-x: hidden; word-wrap: break-word; white-space: normal;'>" . htmlspecialchars($data['client_reply_message']) . "</div></td>";
			}
			$row['created_by'] = "<td class='tableWidth'>" . (($data['created_by'] > 0) ? $data['admin_name'] : $d->app_name() . " App") . "</td>";
			$row['created_date'] = $data['feedback_date_time'];
			$row['feedback_solve_time'] = $data['feedback_solve_time'];
			array_push($data2, $row);
		}
		echo json_encode(array(
			"data" => $data2
		));
	}
}
