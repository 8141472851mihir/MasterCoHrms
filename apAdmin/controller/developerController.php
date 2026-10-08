<?php 
include '../common/objectController.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$admin_id = $bms_admin_id;
if(isset($_POST) && !empty($_POST))
{	
	extract($_POST);
	if (isset($addDeveloper) && $addDeveloper == "addDeveloper") {
		if (isset($developer_name) && !empty($developer_name) &&  isset($developer_technology) && !empty($developer_technology)) {
			$developer_data = array(
				'developer_name' => test_input($developer_name),
				'developer_technology' => test_input($developer_technology),
				'created_by' => $admin_id,
				'created_date' => date("Y-m-d"),
			);
			$insert_developer = $d->insert('developer_master', $developer_data);
			if($insert_developer) {
				$d->insert_log("$society_id", "$admin_id", "$created_by", "developer $developer_name is added by $created_by");
				$_SESSION['msg'] = "developer Added Successfully";
				header("location:../manageDeveloper");
			} else {
				$_SESSION['msg1'] = "Something went wrong";
				header("location:../manageDeveloper");
				exit();
			}
		} else {
			$_SESSION['msg1'] = "Please fill all required fields";
			header("location:../manageDeveloper");
			exit();
		}
	}
	// Edit developer
	else if(isset($editDeveloper) && $editDeveloper == "editDeveloper"){
		if (isset($developer_name) && !empty($developer_name) && isset($developer_technology) && !empty($developer_technology)) {
			$developer_data = array(
				'developer_name' => test_input($developer_name),
				'developer_technology' => test_input($developer_technology),
				'updated_by' => $admin_id,
				'updated_date' => date("Y-m-d"),
			);
			$update_developer = $d->update('developer_master', $developer_data , "developer_id='$developer_id'");
			if ($update_developer) {
				$d->insert_log("$society_id", "$admin_id", "$created_by", "developer $developer_name is updated by $created_by");
				$_SESSION['msg'] = "developer updated Successfully";
				header("location:../manageDeveloper");
			} else {
				$_SESSION['msg1'] = "Something went wrong";
				header("location:../manageDeveloper");
				exit();
			}
		}else {
			$_SESSION['msg1'] = "Please fill all required fields";
			header("location:../manageDeveloper");
			exit();
		}
	}
	// delete developer
	else if(isset($deleteDeveloper) && $deleteDeveloper == "deleteDeveloper"){
		if(isset($developer_id) && !empty($developer_id)){
			$developer_data['delete_status'] = 1;
			$update_developer = $d->update('developer_master', $developer_data , "developer_id='$developer_id'");
			if ($update_developer) {
				$d->insert_log("$society_id", "$admin_id", "$created_by", "developer $developer_name is deleted by $created_by");
				$_SESSION['msg'] = "developer deleted Successfully";
				header("location:../manageDeveloper");
			} else {
				$_SESSION['msg1'] = "Something went wrong";
				header("location:../manageDeveloper");
				exit();
			}
		}
	}
	// Download CSV file
	else if(isset($ExportDeveloperInfoFormat) && $ExportDeveloperInfoFormat == "ExportDeveloperInfoFormat"){
		$contents = "No,Name,Technology\n";
		$contents = strip_tags($contents);
		header("Content-Disposition: attachment; filename=developerDataImport" . date('Y-m-d-h-i') . ".csv");
		print $contents;
	}

	// Bulk Import
	else if(isset($developerDataBulkupload) && $developerDataBulkupload == "developerDataBulkupload"){

		$technology_numbers = [1,2,3,4,5];
		$pattern = "/^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/";
		$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

		if ($ext == 'csv') {
			$filename = $_FILES["file"]["tmp_name"];
			if ($_FILES["file"]["size"] > 0) {
				$flag = true;
				$file = fopen($filename, "r");
				if ($file === FALSE) {
					die('Error opening file');
				}
				$i = 0;
				$errordata = array();
				while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
					// To Ignore headers or first row of csv filex
					if ($flag) {
						$flag = false;
						continue;
					}
					$developer_name = $getData[1];
					$developer_technology = $getData[2];

					if($developer_name != "" &&  in_array($developer_technology,$technology_numbers) && !preg_match('/[0-9\W]/', $developer_name) && !preg_match('/\s/', $developer_name)){
						$developer_data = array(
							'developer_name'=>test_input($developer_name),
							'developer_technology'=>test_input($developer_technology),
							'created_date' => date("Y-m-d H:i:s"),
							'created_by' => $admin_id,
						);
						$emp_bulk_insert = $d->insert('developer_master',$developer_data);
					}else{

						$i++;
						$reason = "ERROR : ";
						if($developer_name == ""){
							$reason .= "Developer Name is required, ";
						}
						if(preg_match('/[0-9\W]/', $developer_name)){
							$reason .= "Developer Name is Invalid Format. Only Alphabtes are allowed ";
						}
						if(preg_match('/\s/', $developer_name)){
							$reason .= "Developer Name is Invalid Format. Only Alphabtes are allowed ";
						}
						if($developer_technology == ""){
							$reason .= "Developer Technology is required, ";
						}
						if(!in_array($developer_technology,$technology_numbers)){
							$reason .= "Developer Technology Number is Invalid, ";
						}
						$e = array(
							'developer_name' => $developer_name,
							'developer_technology' => $developer_technology,
							'reason' => rtrim($reason, ", "),
						);
						array_push($errordata, $e);		
					}
				}
				if (!empty($errordata)) {
					$csv_filename = "developerDataImport" . date('Y-m-d-h-i') . ".csv";
					header('Content-type: application/csv');
					header("Content-Disposition: attachment; filename=$csv_filename");
					$contents = array("No,Name,Technology");

					foreach ($errordata as $key => $getData) {
						$contents[] = ($key + 1) . "," . $getData['developer_name'] . "," . $getData['developer_technology'] . "," . $getData['reason'];
					}
					$output = fopen("../../img/bulk_price_csv_error_file/" . $csv_filename, 'a');
					foreach ($contents as $line) {
						$val = explode(",", $line);
						fputcsv($output, $val);
					}
					fclose($output);
				}else{
					$d->insert_log("", "$admin_id", "$created_by", "Bulk developer Added By ($created_by).");
				}

				fclose($file);
				if ($i == 0) {
					$_SESSION['msg'] = "Uploaded Successfully";
					header("location:../manageDeveloper");
					exit();
				} else {
					$_SESSION['msg1'] = "Incorrect CSV file. Some error found in csv file !";
					header("location:../manageDeveloper");
					exit();
				}
			}else{
				$_SESSION['msg1'] = "File is too large !";
				header("location:../manageDeveloper");
				exit();
			}
		}else{
			$_SESSION['msg1'] = "Please Select csv File";
			header("location:../manageDeveloper");
			exit();
		}
	}
}else{
	$_SESSION['msg1']="Invalid Resquest";
	header("location:../manageDeveloper");
}
?>