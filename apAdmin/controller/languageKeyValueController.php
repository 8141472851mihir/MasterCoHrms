<?php include '../common/objectController.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

extract($_POST);
if (isset($_POST['AddLanguageKeyValue'])) {

	$language_id = $d->sanitizeActionIdAsInt($language_id ?? ($_POST['language_id'] ?? 0));
	$valueNamesToCheck = [];
	for ($i1 = 0; $i1 < count($_POST['value_name']); $i1++) {
		$key_value_id = $d->sanitizeActionIdAsInt($_POST['key_value_id'][$i1] ?? 0);
		if ($key_value_id <= 0) {
			$valueName = $d->escapeSqlString($_POST['value_name'][$i1]);
			$valueNamesToCheck[$valueName] = true;
		}
	}
	$existingByValue = [];
	if (!empty($valueNamesToCheck)) {
		$valueNameList = [];
		foreach (array_keys($valueNamesToCheck) as $vn) {
			$valueNameList[] = "'$vn'";
		}
		$valueNamesIn = implode(',', $valueNameList);
		$qc = $d->select("language_key_value_master", "language_id='$language_id' AND value_name IN ($valueNamesIn)");
		while ($er = mysqli_fetch_array($qc)) {
			$existingByValue[$er['value_name']] = true;
		}
	}

	for ($i1 = 0; $i1 < count($_POST['value_name']); $i1++) {

		# code...
		$language_key_id = $d->sanitizeActionIdAsInt($_POST['language_key_id'][$i1] ?? 0);
		$key_value_id = $d->sanitizeActionIdAsInt($_POST['key_value_id'][$i1] ?? 0);
		$value_name = $_POST['value_name'][$i1];



		$m->set_data('language_id', $language_id);
		$m->set_data('language_key_id', $language_key_id);
		$m->set_data('value_name', $value_name);

		$m->set_data('updated_by',$bms_admin_id);
		$m->set_data('updated_date',date('Y-m-d H:i:s'));

		$a = array(
			'language_id' => $m->get_data('language_id'),
			'language_key_id' => $m->get_data('language_key_id'),
			'value_name' => $m->get_data('value_name'),
			'updated_by'=>$m->get_data('updated_by'),
			'updated_date'=>$m->get_data('updated_date')
		);
		if ($key_value_id > 0) {
			$q = $d->update("language_key_value_master", $a, "language_id='$language_id' AND key_value_id='$key_value_id'");
		} else {
			$valueName = $d->escapeSqlString($value_name);

			if (!empty($existingByValue[$value_name])) {
				$q = $d->update("language_key_value_master", $a, "language_id='$language_id' AND value_name='$valueName'");
			} else {
				$q = $d->insert("language_key_value_master", $a);
				$existingByValue[$value_name] = true;
			}
		}
	}
	if ($q === TRUE) {

		$_SESSION['msg'] = "Add value Successfully";
		header("Location:../keyValue?language_id=$language_id&limit=$limit");
	}
} else if (isset($_POST['AddLanguageKeyValueAll'])) {

	$pairsToCheck = [];
	for ($i1 = 0; $i1 < count($_POST['value_name']); $i1++) {
		$key_value_id = $d->sanitizeActionIdAsInt($_POST['key_value_id'][$i1] ?? 0);
		if ($key_value_id <= 0) {
			$lid = (int)$_POST['language_id'][$i1];
			$valueName = $d->escapeSqlString($_POST['value_name'][$i1]);
			$pairsToCheck[] = "(language_id='$lid' AND value_name='$valueName')";
		}
	}
	$existingByLangValue = [];
	if (!empty($pairsToCheck)) {
		$qc = $d->select("language_key_value_master", implode(' OR ', $pairsToCheck));
		while ($er = mysqli_fetch_array($qc)) {
			$existingByLangValue[(int)$er['language_id'] . '|' . $er['value_name']] = true;
		}
	}

	for ($i1 = 0; $i1 < count($_POST['value_name']); $i1++) {

		# code...
		$language_key_id = $_POST['language_key_id'][$i1];
		$key_value_id = $_POST['key_value_id'][$i1];
		$value_name = $_POST['value_name'][$i1];
		$language_id = $_POST['language_id'][$i1];



		$m->set_data('language_id', $language_id);
		$m->set_data('language_key_id', $language_key_id);
		$m->set_data('value_name', $value_name);
		$m->set_data('updated_by',$bms_admin_id);
		$m->set_data('updated_date',date('Y-m-d H:i:s'));
		$a = array(
			'language_id' => $m->get_data('language_id'),
			'language_key_id' => $m->get_data('language_key_id'),
			'value_name' => $m->get_data('value_name'),
			'updated_by'=>$m->get_data('updated_by'),
			'updated_date'=>$m->get_data('updated_date')
		);
		if (isset($key_value_id) && $key_value_id != '') {
			$q = $d->update("language_key_value_master", $a, "language_id='$language_id' AND key_value_id='$key_value_id'");
		} else {
			$valueName = mysqli_real_escape_string($con, $value_name);
			$mapKey = (int)$language_id . '|' . $value_name;

			if (!empty($existingByLangValue[$mapKey])) {
				$q = $d->update("language_key_value_master", $a, "language_id='$language_id' AND value_name='$value_name'");
			} else {
				$q = $d->insert("language_key_value_master", $a);
				$existingByLangValue[$mapKey] = true;
			}
		}
		$d->createLanguageFiles($language_id, $base_url, '', 'both');
	}
	if ($q === TRUE) {

		$_SESSION['msg'] = "Value Update Successfully";
		if ($backMenu != "") {
			header("Location:../lgnValueFind?key_name=$key_name&language_id=$language_id_selected");
		} else {
			header("Location:../manageLanguageValue?key_name=$key_name&language_id=$language_id_selected");
		}
	}
} else if (isset($_POST['delete_key_value_id'])) {
  $delete_key_value_id = $d->sanitizeActionIdAsInt($_POST['delete_key_value_id']);
	//dharti 9-1-2025 
	$q1 = $d->select("language_key_value_master", "key_value_id='$delete_key_value_id'");
	$data = mysqli_fetch_array($q1);
	$q = $d->delete("language_key_value_master", "key_value_id='$delete_key_value_id'");

	if ($q === TRUE) {

		$_SESSION['msg'] = $data['value_name'] . ' Language Key Value Deleted Successfully';
		//dharti 9-1-2025 end
		$d->insert_log("$society_id", "$bms_admin_id", "$created_by", $_SESSION['msg']);

		header("Location:../manageLanguageValue");
	} else {
		$_SESSION['msg1'] = "Something Wrong";
		header("Location:../manageLanguageValue");
	}
} elseif (isset($_POST['UpdateLanguageKeyValue'])) {

	$key_value_id = $d->sanitizeActionIdAsInt($key_value_id ?? ($_POST['key_value_id'] ?? 0));
	$m->set_data('value_name', $value_name);
	$m->set_data('updated_by',$bms_admin_id);
	$m->set_data('updated_date',date('Y-m-d H:i:s'));
	$a = array(
		'value_name' => $m->get_data('value_name'),
		'updated_by'=>$m->get_data('updated_by'),
		'updated_date'=>$m->get_data('updated_date')
	);
	$q = $d->update("language_key_value_master", $a, "key_value_id='$key_value_id'");

	if ($q === TRUE) {

		$_SESSION['msg'] = "Language Key Value Updated Successfully";
		$d->insert_log("$society_id", "$bms_admin_id", "$created_by", $_SESSION['msg']);

		header("Location:../manageLanguageValue");
	} else {
		$_SESSION['msg1'] = "Something Wrong";
		header("Location:../manageLanguageValue");
	}
} else if (isset($_POST['delete_key_name_company'])) {
  $delete_key_name_company = $d->escapeSqlString($_POST['delete_key_name_company'] ?? '');
  $cId = $d->sanitizeActionIdAsInt($cId ?? ($_POST['cId'] ?? 0));

	// Get all language_ids that had this key for this society before deletion
	$q_lang = $d->select("language_key_value_master_society", "key_name='$delete_key_name_company' AND society_id='$cId'");
	$language_ids_to_update = array();
	while ($lang_data = mysqli_fetch_array($q_lang)) {
		if (!in_array($lang_data['language_id'], $language_ids_to_update)) {
			$language_ids_to_update[] = $lang_data['language_id'];
		}
	}

	$q = $d->delete("language_key_value_master_society", "key_name='$delete_key_name_company' AND society_id='$cId'");
	if ($q === TRUE) {
		$has_custom_lang = $d->count_data_direct("value_master_society_id","language_key_value_master_society", "society_id='$cId'");
		if ($has_custom_lang > 0) {
			foreach ($language_ids_to_update as $lang_id) {
				$d->createCompanyLanguageFiles($cId, $lang_id,$bms_admin_id, $created_by);
			}
		} else {
			$d->deleteCompanyLanguageFiles($cId,$bms_admin_id, $created_by);
		}
		$_SESSION['msg'] = "Language Key Value Deleted Successfully";
		$d->insert_log("$society_id", "$bms_admin_id", "$created_by", $_SESSION['msg']);

		header("Location:../manageLanguageValueCompany?cId=$cId");
	} else {
		$_SESSION['msg1'] = "Something Wrong";
		header("Location:../manageLanguageValueCompany?cId=$cId");
	}
} else if (isset($_POST['addLanguageCustomeCompany'])) {

	$q = $d->delete("language_key_value_master_society", "key_name='$key_name' AND society_id='$cId'");

	$language_ids_updated = array();
	for ($i1 = 0; $i1 < count($_POST['language_id']); $i1++) {

		$language_id = $_POST['language_id'][$i1];
		$value_name_society = $_POST['value_name_society'][$i1];

		$m->set_data('language_id', $language_id);
		$m->set_data('key_name', $key_name);
		$m->set_data('society_id', $cId);
		$m->set_data('value_name_society', $value_name_society);

		$a = array(
			'language_id' => $m->get_data('language_id'),
			'key_name' => $m->get_data('key_name'),
			'society_id' => $m->get_data('society_id'),
			'value_name_society' => $m->get_data('value_name_society'),
		);

		$q = $d->insert("language_key_value_master_society", $a);
		
		// Track which language_ids were updated
		if (!in_array($language_id, $language_ids_updated)) {
			$language_ids_updated[] = $language_id;
		}
	}

	// Create company-specific XML and JSON files for each updated language
	if ($q === TRUE) {
		foreach ($language_ids_updated as $lang_id) {
			// Create company-specific files (company_{society_id}_{language_id}.xml/json)
			$d->createCompanyLanguageFiles($cId, $lang_id,$bms_admin_id, $created_by);
		}
		
		$_SESSION['msg'] = "Value Update Successfully";
		if ($backMenu != "") {
			header("Location:../manageLanguageValueCompany?cId=$cId");
		} else {
			header("Location:../manageLanguageValueCompany?cId=$cId");
		}
	}
} else {
	$_SESSION['msg1'] = "Something Wrong";
	header("Location:../manageLanguageValue");
}
