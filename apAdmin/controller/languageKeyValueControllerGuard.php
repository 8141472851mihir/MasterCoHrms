<?php include'../common/objectController.php';


extract($_POST);
if(isset($_POST['AddLanguageKeyValue'])){

	$language_id = $d->sanitizeActionIdAsInt($language_id ?? ($_POST['language_id'] ?? 0));
	$valueNamesToCheck = [];
	for ($i1=0; $i1 <count($_POST['value_name']) ; $i1++) {
		$key_value_id = $d->sanitizeActionIdAsInt($_POST['key_value_id'][$i1] ?? 0);
		if ($key_value_id <= 0) {
			$valueName=$d->escapeSqlString($_POST['value_name'][$i1]);
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
		$qc=$d->select("language_key_value_master_gatekeeper","language_id='$language_id' AND value_name IN ($valueNamesIn)");
		while ($er = mysqli_fetch_array($qc)) {
			$existingByValue[$er['value_name']] = true;
		}
	}

	for ($i1=0; $i1 <count($_POST['value_name']) ; $i1++) { 
	
		# code...
		$language_key_id = $d->sanitizeActionIdAsInt($_POST['language_key_id'][$i1] ?? 0);
		$key_value_id = $d->sanitizeActionIdAsInt($_POST['key_value_id'][$i1] ?? 0);
		$value_name = $_POST['value_name'][$i1];

		
		
		$m->set_data('language_id',$language_id);
		$m->set_data('language_key_id',$language_key_id);
		$m->set_data('value_name',$value_name);

		$a = array(
			'language_id'=>$m->get_data('language_id'),
			'language_key_id'=>$m->get_data('language_key_id'),
			'value_name'=>$m->get_data('value_name')
		);
		if ($key_value_id > 0) {
				$q=$d->update("language_key_value_master_gatekeeper",$a,"language_id='$language_id' AND key_value_id='$key_value_id'");
		} else {
			$valueName=$d->escapeSqlString($value_name);

			if (!empty($existingByValue[$value_name])) {
				$q=$d->update("language_key_value_master_gatekeeper",$a,"language_id='$language_id' AND value_name='$valueName'");
			} else {
				$q=$d->insert("language_key_value_master_gatekeeper",$a);
				$existingByValue[$value_name] = true;
			}

		}


	}
	if ($q === TRUE)
	{
           
		$_SESSION['msg']="Add value Successfully";
		header ("Location:../keyValueGuard?language_id=$language_id&limit=$limit");
		
	}
}else if(isset($_POST['AddLanguageKeyValueAll'])){

	$pairsToCheck = [];
	for ($i1=0; $i1 <count($_POST['value_name']) ; $i1++) {
		$key_value_id = $d->sanitizeActionIdAsInt($_POST['key_value_id'][$i1] ?? 0);
		if ($key_value_id <= 0) {
			$lid = $d->sanitizeActionIdAsInt($_POST['language_id'][$i1] ?? 0);
			$valueName=$d->escapeSqlString($_POST['value_name'][$i1]);
			$pairsToCheck[] = "(language_id='$lid' AND value_name='$valueName')";
		}
	}
	$existingByLangValue = [];
	if (!empty($pairsToCheck)) {
		$qc=$d->select("language_key_value_master_gatekeeper", implode(' OR ', $pairsToCheck));
		while ($er = mysqli_fetch_array($qc)) {
			$existingByLangValue[(int)$er['language_id'] . '|' . $er['value_name']] = true;
		}
	}

	for ($i1=0; $i1 <count($_POST['value_name']) ; $i1++) { 
	
		# code...
		$language_key_id = $d->sanitizeActionIdAsInt($_POST['language_key_id'][$i1] ?? 0);
		$key_value_id = $d->sanitizeActionIdAsInt($_POST['key_value_id'][$i1] ?? 0);
		$value_name = $_POST['value_name'][$i1];
		$language_id = $d->sanitizeActionIdAsInt($_POST['language_id'][$i1] ?? 0);

		
		
		$m->set_data('language_id',$language_id);
		$m->set_data('language_key_id',$language_key_id);
		$m->set_data('value_name',$value_name);

		$a = array(
			'language_id'=>$m->get_data('language_id'),
			'language_key_id'=>$m->get_data('language_key_id'),
			'value_name'=>$m->get_data('value_name')
		);
		if ($key_value_id > 0) {
				$q=$d->update("language_key_value_master_gatekeeper",$a,"language_id='$language_id' AND key_value_id='$key_value_id'");
		} else {
			$valueName=$d->escapeSqlString($value_name);
			$mapKey = $language_id . '|' . $value_name;

			if (!empty($existingByLangValue[$mapKey])) {
				$q=$d->update("language_key_value_master_gatekeeper",$a,"language_id='$language_id' AND value_name='$valueName'");
			} else {
				$q=$d->insert("language_key_value_master_gatekeeper",$a);
				$existingByLangValue[$mapKey] = true;
			}

		}


	}
	if ($q === TRUE)
	{
           
		$_SESSION['msg']="Value Update Successfully";
		if ($backMenu!="") {
			header ("Location:../lgnValueFindGuard?key_name=$key_name&language_id=$language_id_selected");
		} else {
			header ("Location:../manageLanguageValueGuard?key_name=$key_name&language_id=$language_id_selected");
		}
		
	}
}else if (isset($_POST['delete_key_value_id'])) {
  $delete_key_value_id = $d->sanitizeActionIdAsInt($_POST['delete_key_value_id']);


	$q= $d->delete("language_key_value_master_gatekeeper","key_value_id='$delete_key_value_id'");
	if ($q === TRUE) {

		$_SESSION['msg']="Language Key Value Deleted Successfully";
		$d->insert_log("$society_id","$bms_admin_id","$created_by",$_SESSION['msg']);

		header ("Location:../manageLanguageValueGuard");

	} else {
		$_SESSION['msg1']="Something Wrong";
		header ("Location:../manageLanguageValue");
	}
}elseif (isset($_POST['UpdateLanguageKeyValue']))  {

	$m->set_data('value_name',$value_name);
	$a = array(
		'value_name'=>$m->get_data('value_name') 
	);
	$q=$d->update("language_key_value_master_gatekeeper",$a,"key_value_id='$key_value_id'");

	if ($q === TRUE){

		$_SESSION['msg']="Language Key Value Updated Successfully";
		$d->insert_log("$society_id","$bms_admin_id","$created_by",$_SESSION['msg']);

		header ("Location:../manageLanguageValueGuard");

	} else {
		$_SESSION['msg1']="Something Wrong";
		header ("Location:../manageLanguageValueGuard");
	}
}else{
	$_SESSION['msg1']="Something Wrong";
	header ("Location:../manageLanguageValueGuard");
}
?>
