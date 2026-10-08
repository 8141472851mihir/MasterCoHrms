<?php
include '../common/objectController.php';

if (isset($_POST) && !empty($_POST)) {
	extract($_POST);
	if (isset($updateRemark) && $updateRemark == "updateRemark") {
		$m->set_data('last_call_remark', $last_call_remark);
		$lastCallRemarkData = array(
			'last_call_remark' => $m->get_data('last_call_remark'),
		);
		$remarkQuery = $d->update("society_master", $lastCallRemarkData, "society_id='$society_id'");
		if ($remarkQuery == TRUE) {
			$d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Last call Remark Updated Successfully");
			echo '1';
		} else {
			echo '0';
		}
	} else if (isset($updateLastCallDate) && $updateLastCallDate == "updateLastCallDate") {
		$m->set_data('last_call_date', $last_call_date);
		$lastCallData = array(
			'last_call_date' => $m->get_data('last_call_date'),
		);
		$callDateQuery = $d->update("society_master", $lastCallData, "society_id='$society_id'");
		if ($callDateQuery == TRUE) {
			$d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Last Call Date Updated Successfully");
			echo '1';
		} else {
			echo '0';
		}
	} else if (isset($updateFollowUpDate) && $updateFollowUpDate == "updateFollowUpDate") {
		$m->set_data('follow_up_date', $follow_up_date);
		$followUpData = array(
			'follow_up_date' => $m->get_data('follow_up_date'),
		);
		$followUpDateQuery = $d->update("society_master", $followUpData, "society_id='$society_id'");
		if ($followUpDateQuery == TRUE) {
			$d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Followupdate Updated Successfully");
			echo '1';
		} else {
			echo '0';
		}
	} else if (isset($updateSpoc) && $updateSpoc == "updateSpoc") {
		$m->set_data('last_spoc', $last_spoc);
		$spocData = array(
			'last_spoc' => $m->get_data('last_spoc'),
		);
		$spocDataQuery = $d->update("society_master", $spocData, "society_id='$society_id'");
		if ($spocDataQuery == TRUE) {
			$d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Spoc Updated Successfully");
			echo '1';
		} else {
			echo '0';
		}
	} else if (isset($updateSpocDesignation) && $updateSpocDesignation == "updateSpocDesignation") {
		$m->set_data('last_spoc_designation', $last_spoc_designation);
		$spocDesignationData = array(
			'last_spoc_designation' => $m->get_data('last_spoc_designation'),
		);
		$spocDesignationDataQuery = $d->update("society_master", $spocDesignationData, "society_id='$society_id'");
		if ($spocDesignationDataQuery == TRUE) {
			$d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Spoc Designation Updated Successfully");
			echo '1';
		} else {
			echo '0';
		}
	} else if (isset($updateSpocMobile) && $updateSpocMobile == "updateSpocMobile") {
		$m->set_data('last_spoc_mobile_number', $last_spoc_mobile_number);
		$spocMobileData = array(
			'last_spoc_mobile_number' => $m->get_data('last_spoc_mobile_number'),
		);
		$spocMobileDataQuery = $d->update("society_master", $spocMobileData, "society_id='$society_id'");
		if ($spocMobileDataQuery == TRUE) {
			$d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Spoc Mobile Number Updated Successfully");
			echo '1';
		} else {
			echo '0';
		}
	} else if (isset($savePatchData) && $savePatchData == "savePatchData") {

		$result = $d->select("patch_file_master");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);

			$folder = !empty($folder_name) ? $folder_name : $row['folder_name'];
			$sql = !empty($sql_file_name) ? $sql_file_name : $row['sql_file_name'];

			$data = ['folder_name' => $folder, 'sql_file_name' => $sql];
			$patchDataQuery = $d->update("patch_file_master", $data, "1");
		} else {
			$data = ['folder_name' => $folder_name, 'sql_file_name' => $sql_file_name];
			$patchDataQuery = $d->insert("patch_file_master", $data);
		}
		echo $patchDataQuery ? '1' : '0';
	} else if (isset($updateImplementationRemark) && $updateImplementationRemark == "updateImplementationRemark") {
		$m->set_data('implementation_remark', $implementation_remark);
		$implementationRemarkData = array(
			'implementation_remark' => $m->get_data('implementation_remark'),
		);
		$implementationRemarkDataQuery = $d->update("society_master", $implementationRemarkData, "society_id='$society_id'");
		if ($implementationRemarkDataQuery == TRUE) {
			$d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Implementation Remark Updated Successfully");
			echo '1';
		} else {
			echo '0';
		}
	} else if (isset($changeEngagementName) && $changeEngagementName == 'changeEngagementName' && isset($society_id) && $society_id != '') {
		$update = array(
			'engagement_call_executive_name' => $engagement_call_executive_name,
		);
		$updateQuery = $d->update("society_master", $update, "society_id='$society_id'");
		if ($updateQuery == true) {
			$d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Engagement Executive Name Updated Successfully");
			$_SESSION['msg'] = "Engagement call executive name change successfully";
			header("Location: ../engagementReport");
			exit();
		} else {
			$_SESSION['msg'] = "something Went Wrong!!";
			header("location:../engagementReport");
			exit();
		}
	} else if (isset($changeSupportName) && $changeSupportName == 'changeSupportName' && isset($society_id) && $society_id != '') {
		$supportPersonQry = $d->selectRow("admin_id,admin_mobile,country_code","bms_admin_master", "admin_name='$support_name'");
		if(mysqli_num_rows($supportPersonQry) > 0) {
			$supportPersonData = mysqli_fetch_assoc($supportPersonQry);
			$support_mobile_no = $supportPersonData['admin_mobile'];
			$support_mobile_no = $d->encryptDecrypt("decrypt",$support_mobile_no);
			$support_country_code = $supportPersonData['country_code'];
		}
		$update = array(
			'support_name' => $support_name,
			'support_mobile_no' => $support_mobile_no,
			'support_country_code' => $support_country_code,
		);
		$updateQuery = $d->update("society_master", $update, "society_id='$society_id'");
		if ($updateQuery == true) {
			$d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Support Executive Name Updated Successfully");
			$_SESSION['msg'] = "Support executive name change successfully";
			if(isset($redirect_location) && $redirect_location != '') {
				header("Location: ../$redirect_location");
			} else {
				header("Location: ../engagementReport");
			}
			exit();
		} else {
			$_SESSION['msg'] = "something Went Wrong!!";
			if(isset($redirect_location) && $redirect_location != '') {
				header("Location: ../$redirect_location");
			} else {
				header("Location: ../engagementReport");
			}
			exit();
		}
	} else if (isset($changeImplementationName) && $changeImplementationName == 'changeImplementationName' && isset($society_id) && $society_id != '') {
		$m->set_data('implementation_name', $implementation_name);
		$update = array(
			'implementation_name' => $m->get_data('implementation_name'),
		);
		$updateQuery = $d->update("society_master", $update, "society_id='$society_id'");
		if ($updateQuery == true) {
			$d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Implementation Executive Name Updated Successfully");
			echo '1';
		} else {
			echo '0';
		}
	}
}
