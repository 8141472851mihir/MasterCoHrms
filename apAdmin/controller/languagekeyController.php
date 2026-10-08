<?php include'../common/objectController.php';

if (isset($_GET['clear_new_language_key_string'])) {
	unset($_SESSION['new_language_key_string']);
	header('Content-Type: application/json');
	echo json_encode(['status' => 'ok']);
	exit;
}

if (isset($_GET['download_failed_language_keys'])) {
	header('Content-Type: application/json; charset=utf-8');
	$failedJson = $_SESSION['bulk_language_key_failed_json'] ?? [];
	$filename = 'failed_language_keys_' . date('Ymd_His') . '.json';
	header('Content-Disposition: attachment; filename="' . $filename . '"');
	echo json_encode($failedJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	exit;
}

if (isset($_GET['clear_bulk_language_key_results'])) {
	unset($_SESSION['bulk_language_key_results'], $_SESSION['bulk_language_key_failed_json']);
	header('Content-Type: application/json');
	echo json_encode(['status' => 'ok']);
	exit;
}

extract($_POST);
if (isset($_POST['BulkUploadLanguageKeys'])) {
	@set_time_limit(300);
	$jsonRaw = '';
	if (!empty($_POST['bulk_json_text'])) {
		$jsonRaw = trim($_POST['bulk_json_text']);
	} elseif (!empty($_FILES['bulk_json_file']['tmp_name']) && is_uploaded_file($_FILES['bulk_json_file']['tmp_name'])) {
		$jsonRaw = file_get_contents($_FILES['bulk_json_file']['tmp_name']);
	}

	if ($jsonRaw === '') {
		$_SESSION['msg1'] = 'Please paste JSON or upload a .json file.';
		header('Location: ../manageLanguagKeys');
		exit;
	}

	$payload = json_decode($jsonRaw, true);
	if (!is_array($payload) || empty($payload)) {
		$_SESSION['msg1'] = 'Invalid JSON. Expected an object of key/value pairs, e.g. {"welcome_back":"Welcome Back"}.';
		header('Location: ../manageLanguagKeys');
		exit;
	}

	// Support list form: [{"key":"...","value":"..."}]
	$isListPayload = array_keys($payload) === range(0, count($payload) - 1)
		&& isset($payload[0])
		&& is_array($payload[0]);
	if ($isListPayload) {
		$normalized = [];
		foreach ($payload as $row) {
			$key = $row['key'] ?? $row['key_name'] ?? '';
			$value = $row['value'] ?? $row['english'] ?? $row['language_string'] ?? '';
			if ($key !== '') {
				$normalized[$key] = $value;
			}
		}
		$payload = $normalized;
	}

	if (empty($payload)) {
		$_SESSION['msg1'] = 'No valid key/value pairs found in the JSON.';
		header('Location: ../manageLanguagKeys');
		exit;
	}

	$languages = [];
	$languageQuery = $d->select('language_master', '', 'ORDER BY language_id ASC');
	while ($languageRow = mysqli_fetch_assoc($languageQuery)) {
		$languages[] = $languageRow;
	}

	if (empty($languages)) {
		$_SESSION['msg1'] = 'No languages found in language_master.';
		header('Location: ../manageLanguagKeys');
		exit;
	}

	$translateLanguages = [];
	foreach ($languages as $languageRow) {
		$translateLanguages[] = [
			'language_name' => trim((string) ($languageRow['language_name'] ?? '')),
			'language_code' => strtolower(trim((string) ($languageRow['language_code'] ?? ''))),
			'is_english_language' => (int) ($languageRow['is_english_language'] ?? 0),
		];
	}

	$createdDate = date('Y-m-d H:i:s');
	$results = [];
	$failedJson = [];
	$touchedLanguageIds = [];
	$successCount = 0;
	$failCount = 0;
	$stopRemainingReason = '';

	// Prefetch existing key_name → language_key_id for all payload keys
	$existingKeyIds = [];
	$candidateKeyNames = [];
	foreach ($payload as $rawKey => $englishValue) {
		$keyName = $d->language_key_to_snake_case(is_string($rawKey) || is_numeric($rawKey) ? (string) $rawKey : '');
		if ($keyName !== '') {
			$candidateKeyNames[$keyName] = true;
		}
	}
	if (!empty($candidateKeyNames)) {
		$keyNameList = [];
		foreach (array_keys($candidateKeyNames) as $cn) {
			$keyNameList[] = "'" . $d->escapeSqlString($cn) . "'";
		}
		$keyNamesIn = implode(',', $keyNameList);
		$existingQ = $d->selectRow('language_key_id, key_name', 'language_key_master', "key_name IN ($keyNamesIn)");
		while ($er = mysqli_fetch_assoc($existingQ)) {
			$existingKeyIds[$er['key_name']] = (int) $er['language_key_id'];
		}
	}

	foreach ($payload as $rawKey => $englishValue) {
		$keyName = $d->language_key_to_snake_case(is_string($rawKey) || is_numeric($rawKey) ? (string) $rawKey : '');
		$englishText = (is_array($englishValue) || is_object($englishValue))
			? ''
			: trim((string) $englishValue);

		$resultRow = [
			'raw_key' => is_scalar($rawKey) ? (string) $rawKey : '',
			'key_name' => $keyName,
			'english' => $englishText,
			'status' => 'failed',
			'reason' => '',
			'failed_languages' => [],
			'translations' => [],
		];

		if ($stopRemainingReason !== '') {
			$resultRow['reason'] = $stopRemainingReason;
			if ($keyName !== '' && $englishText !== '') {
				$failedJson[$keyName] = $englishText;
			}
			$results[] = $resultRow;
			$failCount++;
			continue;
		}

		if (is_array($englishValue) || is_object($englishValue)) {
			$resultRow['reason'] = 'Value must be a string.';
			$results[] = $resultRow;
			$failCount++;
			continue;
		}

		if ($keyName === '') {
			$resultRow['reason'] = 'Empty or invalid key name.';
			$results[] = $resultRow;
			$failCount++;
			continue;
		}

		if ($englishText === '') {
			$resultRow['reason'] = 'English value is empty.';
			$failedJson[$keyName] = '';
			$results[] = $resultRow;
			$failCount++;
			continue;
		}

		if (isset($existingKeyIds[$keyName])) {
			$resultRow['reason'] = 'Key already exists.';
			$failedJson[$keyName] = $englishText;
			$results[] = $resultRow;
			$failCount++;
			continue;
		}

		$translationErrors = [];
		$translations = $d->language_translate_all($englishText, $translateLanguages, $translationErrors);
		$resultRow['translations'] = $translations;

		$failedLanguages = [];
		$hitMonthlyLimit = false;
		foreach ($translationErrors as $translationError) {
			$msg = is_array($translationError)
				? ($translationError['message'] ?? 'Translation failed')
				: (string) $translationError;
			if (stripos($msg, 'monthly character limit') !== false) {
				$hitMonthlyLimit = true;
			}
			if (is_array($translationError) && !empty($translationError['language'])) {
				$failedLanguages[] = $translationError['language'];
			}
		}

		foreach ($translateLanguages as $languageMeta) {
			$languageName = trim($languageMeta['language_name'] ?? '');
			if ($d->language_translate_is_english($languageMeta)) {
				continue;
			}
			if (!isset($translations[$languageName]) || trim((string) $translations[$languageName]) === '') {
				if (!in_array($languageName, $failedLanguages, true)) {
					$failedLanguages[] = $languageName;
				}
			}
		}

		if (!empty($failedLanguages) || !empty($translationErrors)) {
			$failedLanguages = array_values(array_unique($failedLanguages));
			$resultRow['failed_languages'] = $failedLanguages;
			if ($hitMonthlyLimit) {
				$resultRow['reason'] = 'Translation failed due to monthly character limit.'
					. (!empty($failedLanguages) ? ' Languages: ' . implode(', ', $failedLanguages) . '.' : '');
				$stopRemainingReason = 'Skipped because monthly translation character limit was reached.';
			} elseif (!empty($failedLanguages)) {
				$resultRow['reason'] = 'Translation failed for: ' . implode(', ', $failedLanguages) . '.';
			} else {
				$resultRow['reason'] = 'Translation failed.';
			}
			$failedJson[$keyName] = $englishText;
			$results[] = $resultRow;
			$failCount++;
			continue;
		}

		$keyData = [
			'key_name' => $keyName,
			'key_type' => 0,
			'no_of_key' => 1,
			'created_by' => $bms_admin_id,
			'created_date' => $createdDate,
		];
		$insertKey = $d->insert('language_key_master', $keyData);
		if ($insertKey !== true) {
			$resultRow['reason'] = 'Database insert failed for language key.';
			$failedJson[$keyName] = $englishText;
			$results[] = $resultRow;
			$failCount++;
			continue;
		}

		$languageKeyId = (int) $con->insert_id;
		if ($languageKeyId <= 0) {
			$resultRow['reason'] = 'Could not resolve language_key_id after insert.';
			$failedJson[$keyName] = $englishText;
			$results[] = $resultRow;
			$failCount++;
			continue;
		}

		$existingKeyIds[$keyName] = $languageKeyId;

		foreach ($languages as $languageRow) {
			$languageId = (int) $languageRow['language_id'];
			$languageName = trim($languageRow['language_name']);
			$valueName = isset($translations[$languageName]) ? $translations[$languageName] : $englishText;

			$valueData = [
				'language_id' => $languageId,
				'language_key_id' => $languageKeyId,
				'value_name' => $valueName,
				'updated_by' => $bms_admin_id,
				'updated_date' => $createdDate,
			];
			$d->insert('language_key_value_master', $valueData);
			$touchedLanguageIds[$languageId] = true;
		}

		$resultRow['status'] = 'success';
		$resultRow['reason'] = 'Created with auto-translation for all languages.';
		$results[] = $resultRow;
		$successCount++;
	}

	foreach (array_keys($touchedLanguageIds) as $languageId) {
		$d->createLanguageFiles($languageId, $base_url, '', 'both');
	}

	$summary = "Bulk upload review: $successCount succeeded, $failCount failed.";
	$_SESSION['bulk_language_key_results'] = [
		'summary' => $summary,
		'success_count' => $successCount,
		'fail_count' => $failCount,
		'total_count' => count($results),
		'results' => $results,
		'failed_json' => $failedJson,
		'generated_at' => date('Y-m-d H:i:s'),
	];
	$_SESSION['bulk_language_key_failed_json'] = $failedJson;

	if ($successCount > 0) {
		$d->insert_log("$society_id", "$bms_admin_id", "$created_by", $summary);
	}

	header('Location: ../manageLanguagKeys#bulkUploadResults');
	exit;

} elseif (isset($_POST['AddLanguageKey'])) {
	$m->set_data('key_name',$key_name);
	$m->set_data('key_type',$key_type);
	$m->set_data('no_of_key',$no_of_key);
	$m->set_data('created_by',$bms_admin_id);
	$m->set_data('created_date',date('Y-m-d H:i:s'));
	
	$a = array('key_name'=>$m->get_data('key_name'),
		'key_type'=>$m->get_data('key_type'),
		'no_of_key'=>$m->get_data('no_of_key'),
		'created_by'=>$m->get_data('created_by'),
		'created_date'=>$m->get_data('created_date')
	);
	$q=$d->insert("language_key_master",$a);
	

	if ($q === TRUE) {
		if (!empty($language_string)) {
			$_SESSION['new_language_key_string'] = trim($language_string);
		}

		$_SESSION['msg']="Language Key Added Successfully";
		$d->insert_log("$society_id","$bms_admin_id","$created_by",$_SESSION['msg']);
		
		header ("Location:../lgnValueFind?key_name=$key_name");
		
	} else {
		$_SESSION['msg1']="Something Wrong";
		header ("Location:../manageLanguagKeys");
	}

} else if (isset($_POST['delete_language_key_id']))  {
	$delete_language_key_id = $d->sanitizeActionIdAsInt($_POST['delete_language_key_id']);

	
	$q= $d->delete("language_key_master","language_key_id='$delete_language_key_id'");
	if($q===TRUE) {
		$d->delete("language_key_value_master","language_key_id='$delete_language_key_id'");
		$_SESSION['msg']="Language Key Deleted $keyName";
		$d->insert_log("$society_id","$bms_admin_id","$created_by",$_SESSION['msg']);
		header ('location:../manageLanguagKeys');
	} 	else 	{
		$_SESSION['msg1']="Language Key Not Deleted";
		header ('location:../manageLanguagKeys');
	}
} elseif (isset($_POST['UpdateLanguageKey']))  {
	$key_name_esc = $d->escapeSqlString($key_name);
	$language_key_id = $d->sanitizeActionIdAsInt($language_key_id);
	$w = $d->select('language_key_master',"key_name='$key_name_esc' and language_key_id!='$language_key_id'");
	if(mysqli_num_rows($w)==0) {
		$m->set_data('key_name',$key_name);
		$m->set_data('key_type',$key_type);
		$m->set_data('no_of_key',$no_of_key);
		

		$a = array('key_name'=>$m->get_data('key_name'),
			'key_type'=>$m->get_data('key_type'),
			'no_of_key'=>$m->get_data('no_of_key'),
		);
		
		$q=$d->update("language_key_master",$a,"language_key_id='$language_key_id'");
		if ($q === TRUE) {
			
			$_SESSION['msg']="Language Key Updated Successfully";
			$d->insert_log("$society_id","$bms_admin_id","$created_by",$_SESSION['msg']);
			
			header ("Location:../manageLanguagKeys");
			
		} else {
			$_SESSION['msg1']="Something Wrong";
			header ("Location:../manageLanguagKeys");
		}
		
	} else {
		$_SESSION['msg1']="key data inserted";
		header ("Location:../language_key_master.php");
	}
} else {
	$_SESSION['msg1']="Something Wrong";
	header ("Location:../manageLanguagKeys");
}

?>