<?php
include '../common/objectController.php';
if (isset($_POST) && !empty($_POST)) {
	if (isset($add_menu)) {
		$sId = $d->sanitizeActionIdAsInt($sId ?? ($_POST['sId'] ?? 0));
		$app_menu_ids = $d->sanitizeActionIds($_POST['app_menu_id'] ?? []);
		foreach ($app_menu_ids as $app_menu_id) {
			$m->set_data('society_id', $sId);
			$m->set_data('app_menu_id', $app_menu_id);
			$a = array(
				'society_id' => $m->get_data('society_id'),
				'app_menu_id' => $m->get_data('app_menu_id'),
			);
			$q = $d->insert("resident_app_menu_society", $a);
		}
		if ($q == TRUE) {
			$d->insert_log("0", "$bms_admin_id", "$created_by", "Society App Menu ($app_menu_id) Added");
			$_SESSION['msg'] = "Insert successfully";
			header("Location: ../companyAppMenu?sId=$sId");
		} else {
			$_SESSION['msg1'] = "Insert Unsuccessfull";
			header("Location: ../companyAppMenu?sId=$sId");
		}
	} else if (isset($delete_menu)) {
		$sId = $d->sanitizeActionIdAsInt($sId ?? ($_POST['sId'] ?? 0));
		$app_menu_society_id = $d->sanitizeActionIdAsInt($app_menu_society_id ?? ($_POST['app_menu_society_id'] ?? 0));
		$q = $d->delete("resident_app_menu_society", "app_menu_society_id='$app_menu_society_id'");
		if ($q == TRUE) {
			$d->insert_log("0", "$bms_admin_id", "$created_by", "Society App Menu ($menuName) Deleted");
			$_SESSION['msg'] = "Delete Successfully";
			header("Location:../companyAppMenu?sId=$sId");
		} else {
			$_SESSION['msg1'] = "Somthing Wrong";
			header("Location:../companyAppMenu?sId=$sId");
		}
	} else if (isset($_POST['addToDashboard']) && $_POST['addToDashboard'] == "addToDashboard") {
		$app_menu_id = $d->sanitizeActionIdAsInt($_POST['app_menu_id'] ?? 0);
		$sId = $d->sanitizeActionIdAsInt($_POST['sId'] ?? 0);
		$alternateMenuIds = $_POST['alternateMenuIds'] ?? "";
		$spot_number = (int) ($_POST['spot_number'] ?? 0);
		$app_menu_id_array = [];
		if ($alternateMenuIds != "") {
			foreach (explode(",", $alternateMenuIds) as $mid) {
				$app_menu_id_array[] = $d->sanitizeActionIdAsInt($mid);
			}
		}
		$app_menu_id_array[$spot_number] = $app_menu_id;
		$updateData = array('alternate_sequence' => 0);
		$clearDefault = $d->update("resident_app_menu_society", $updateData, "society_id = '$sId'");
		foreach ($app_menu_id_array as $alternate_sequence => $app_menu_id_new) {
			$updateData = array('alternate_sequence' => $alternate_sequence + 1);
			$result = $d->update("resident_app_menu_society", $updateData, "app_menu_id = '$app_menu_id_new' AND society_id = '$sId'");
		}
		if ($result) {
			$_SESSION['msg'] = "Menu added to dashboard successfully";
			header("Location:../companyAppMenu?changeOrder=&sId=$sId");
			exit();
		} else {
			$_SESSION['msg1'] = "Failed to add menu to dashboard";
			header("Location:../companyAppMenu?changeOrder=&sId=$sId");
			exit();
		}
		exit();
	} else if (isset($_POST['removeFromDashboard']) && $_POST['removeFromDashboard'] == true) {
		$app_menu_society_id = $d->sanitizeActionIdAsInt($_POST['app_menu_society_id'] ?? 0);
		$sId = $d->sanitizeActionIdAsInt($_POST['sId'] ?? 0);
		$updateData = array('alternate_sequence' => 0);
		$where = "app_menu_society_id = '$app_menu_society_id' AND society_id = '$sId'";
		$result = $d->update("resident_app_menu_society", $updateData, $where);
		if ($result) {
			$_SESSION['msg'] = "Menu removed from dashboard successfully";
			echo json_encode(array('success' => true));
		} else {
			$_SESSION['msg1'] = "Failed to remove menu from dashboard";
			echo json_encode(array('success' => false));
		}
		exit();
	} else {
		$_SESSION['msg1'] = "Something went wrong";
		header('location:../welcome');
		exit();
	}
} else {
	$_SESSION['msg1'] = "Something went wrong";
	header('location:../welcome');
	exit();
}
