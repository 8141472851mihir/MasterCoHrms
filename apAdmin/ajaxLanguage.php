<?php
include_once './common/object.php';
if (isset($_POST) && !empty($_POST)) {
	extract($_POST);
	$response = array();
	$data1 = array();
	$data2 = array();
	$i = 1;
	if (isset($_POST['languageKeys'])) {
		$data = array();
		$q = $d->selectRow("language_key_master.*,bms_admin_master.admin_name","language_key_master LEFT JOIN bms_admin_master ON bms_admin_master.admin_id = language_key_master.created_by","","");

		while ($data = mysqli_fetch_array($q)) {
			$row = array();
			$row = array();
			$row['id'] = $i++;
			$row['key_name'] = "<td>".$data['key_name']."</td>";
			$row['created_date'] = "<td>".$data['created_date']."</td>";
			$row['created_by'] = "<td>".$data['admin_name']."</td>";
			$row['type'] = "<td>".(($data['key_type']=='1')?"Array":"String")."</td>";
			$language_key_id  = $data['language_key_id'];
			$key_name  = $data['key_name'];
			// dharti 10-1-2025
			$row['action'] = "<td>
			<div style='display: inline-block;'>" . 
			(($data['is_changeble'] == "0") ? "
				<form action='languageKey' method='post'>
				<input type='hidden' name='language_key_id' value='{$language_key_id}'>
				<button type='submit' class='btn btn-sm btn-primary' name='edit' value='edit'>
				<i class='fa fa-edit'></i>
				</button>
				</form>" : "") . 
			"</div>
			<div style='display: inline-block;'>
			<form action='controller/languagekeyController.php' id='delete-form-{$language_key_id}' method='post'>
			<input type='hidden' name='keyName' value='{$key_name}'>
			<input type='hidden' name='delete_language_key_id' value='{$language_key_id}'>
			<input type='hidden' name='csrf' value='{$csrf}'> 
			<button type='button' data-language_key_id='{$language_key_id}' class='deleteButton btn btn-sm btn-danger form-btn' name='delete' value='delete'>
			<i class='fa fa-trash'></i>
			</button>
			</form>
			</div>
			</td>";
			// dharti 10-1-2025 end
			array_push($data2, $row);
		}
		echo json_encode(array(
			"data" => $data2 
		));
	}elseif (isset($_POST['languageValues'])) {
		$language_id = $_POST['language_id_temp']; 
		$languageKeyQuery = $d->select("language_key_master", "", "");
		$languageKeyArray = array();
		while ($languageKeyData = mysqli_fetch_array($languageKeyQuery)) {
			$languageKeyArray[$languageKeyData['language_key_id']] = $languageKeyData['key_name'];
		}
		$languageMasterQuery = $d->select("language_master", "", "");
		$languageMasterDataArray = array();
		while ($languageMasterData = mysqli_fetch_array($languageMasterQuery)) {
			$languageMasterDataArray[$languageMasterData['language_id']] = $languageMasterData['language_name'] . '-' . $languageMasterData['language_name_1'];
		}
		$query = $d->selectRow("language_key_value_master.*,bms_admin_master.admin_name","language_key_value_master LEFT JOIN bms_admin_master ON bms_admin_master.admin_id=language_key_value_master.updated_by", "language_id='$language_id'", "");


		$i = 1; 
		while ($row = mysqli_fetch_array($query)) {
			$languageKeyId = $row['language_key_id'];
			$keyName = $languageKeyArray[$languageKeyId];

			$languageId = $row['language_id'];
			$languageName = $languageMasterDataArray[$languageId];

			$rowData = array();
			$rowData['id'] = $i++;
			$rowData['language_name'] = $languageName;
			$key_val='<?php echo $xml->string->'.$keyName.'; ?>';
			$rowData['key_name'] = $keyName . "<input type='hidden' id='copyText_$i' value='" . $key_val . "'> <i class='fa fa-copy' onclick='clickable($i)'></i>";

			$rowData['value_name'] = $row['value_name'];
			$rowData['updated_by'] = $row['admin_name'];
			$rowData['updated_date'] = $row['updated_date'];
			$rowData['action'] = "<div style='display: inline-block;'>";

			$rowData['action'] .= "
			<form action='edit_language_key_value' method='post'>
			<input type='hidden' name='key_value_id' value='{$row['key_value_id']}'>
			<input type='hidden' name='csrf' value='{$csrf}'> 
			<button type='submit' class='btn btn-sm btn-primary' name='edit' value='edit'>
			<i class='fa fa-edit'></i>
			</button>
			</form>
			";

			$rowData['action'] .= "
			</div>
			<div style='display: inline-block;'>
			<?php // dharti 9-1-2025  ?>
			<form id='delete-form-{$row['key_value_id']}' class='d-inline-block' action='controller/languageKeyValueController.php' method='post'>
			<input type='hidden' name='delete_key_value_id' value='{$row['key_value_id']}'>
			<input type='hidden' name='csrf' value='{$csrf}'> 
			<button type='button' id='deleteButton' data_key_value_id='{$row['key_value_id']}' class='deleteButton btn btn-sm btn-danger form-btn'>
			<i class='fa fa-trash'></i>
			</button>
			<?php //dharti 9-1-2025 ?>
			</form>
			</div>
			";
			array_push($data1, $rowData);
		}
		echo json_encode(array(
			"data" => $data1 
		));
	}
}
?>
