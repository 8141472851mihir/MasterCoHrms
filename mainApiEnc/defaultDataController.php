<?php
include_once 'lib.php';
$_POST =  json_decode($d->manage_decryption("1", file_get_contents("php://input")), true);
$compress = resolve_api_compress();
$is_encrypted = 1;
if (json_last_error() !== JSON_ERROR_NONE) {
	echo $d->manage_encryption($is_encrypted, [
		"status" => "201",
		"message" => "Invalid Request"
	], $compress);
	exit();
}
try {
	if (isset($_POST) && !empty($_POST)) {
		if ($key == $keydb) {
			$response = array();
			extract(array_map("test_input", $_POST));
			if (isset($getDefaultDataForSociety) && $_POST['getDefaultDataForSociety'] == "getDefaultDataForSociety") {
				$tax_slab_year  = $_POST['tax_slab_year'] ?? '';
				$country_id     = $_POST['country_id']    ?? '';
				$calender_type  = $_POST['calender_type'] ?? '0';
				$calender_year  = $_POST['calender_year'] ?? date('Y');

				if ($calender_type == '1') {
					[$startYear, $endYear] = explode('-', $calender_year);
					$startDate = $startYear . "-04-01";
					$endDate   = $endYear   . "-03-31";
				} else {
					$startDate = $calender_year . "-01-01";
					$endDate   = $calender_year . "-12-31";
				}
				$response["taxBenifitCategoryDataArray"] = array();
				$taxBenifitCategoryQry = $d->selectRow("tax_benefit_category.*", "tax_benefit_category", "tax_benefit_year='$tax_slab_year'");
				$tax_benefit_category_id_array= array();
				if (mysqli_num_rows($taxBenifitCategoryQry)>0) {
					while ($taxBenifitCategoryData = mysqli_fetch_assoc($taxBenifitCategoryQry)) {
						array_push($tax_benefit_category_id_array, $taxBenifitCategoryData['tax_benefit_category_id']);
						$response["taxBenifitCategoryDataArray"][] = $taxBenifitCategoryData;
					}
				}

				$ids = join("','",$tax_benefit_category_id_array); 
				if ($ids!="") {
					$response["taxBenifitSubCategoryDataArray"] = array();
					$taxBenifitSubCategoryQry = $d->select("tax_benefit_sub_category","tax_benefit_category_id IN ('$ids') ","ORDER BY tax_benefit_sub_category_id ASC");
					if (mysqli_num_rows($taxBenifitSubCategoryQry)>0) {
						while ($taxBenifitSubCategoryData = mysqli_fetch_assoc($taxBenifitSubCategoryQry)) {
							$response["taxBenifitSubCategoryDataArray"][] = $taxBenifitSubCategoryData;
						}
					}
				}

				$response["leaveDataArray"] = array();
				$leaveQry = $d->selectRow("leave_types_master.*", "leave_types_master", "country_id='$country_id' AND country_id!=''");
				if (mysqli_num_rows($leaveQry)>0) {
					while ($leaveData = mysqli_fetch_assoc($leaveQry)) {
						$response["leaveDataArray"][] = $leaveData;
					}
				}
				$response["taxLimitDataArray"]=[];
				$taxLimitQry = $d->selectRow("tax_limit_master.*", "tax_limit_master", "tax_year='$tax_slab_year'");
				if (mysqli_num_rows($taxLimitQry)>0) {
					while ($taxLimitData = mysqli_fetch_assoc($taxLimitQry)) {
						$response["taxLimitDataArray"][] = $taxLimitData;
					}
				}
				$response["holidayDataArray"] = [];
				$holidayWhere = "holiday_status='0' AND holiday_date BETWEEN '$startDate' AND '$endDate' AND country_id='$country_id' AND country_id!=''";
				$holidayQry = $d->selectRow("holidays_master.*", "holidays_master", $holidayWhere);
				if (mysqli_num_rows($holidayQry)>0) {
					while ($holidayData = mysqli_fetch_assoc($holidayQry)) {
						$response["holidayDataArray"][] = $holidayData;
					}
				}
				$expenseQry = $d->selectRow("expense_master.*", "expense_master", "expense_status='0'");
				$response['expenseDataArray'] = [];
				if (mysqli_num_rows($expenseQry)>0) {
					while ($expenseData = mysqli_fetch_assoc($expenseQry)) {
						$expenseData['expense_icon_full'] = $m->base_url() . "img/emp_icon/" . $expenseData['expense_icon'];
						$response['expenseDataArray'][] = $expenseData;
					}
				}

				$salaryQry = $d->selectRow("salary_earning_deduction_type_master.*", "salary_earning_deduction_type_master", "earn_deduct_is_delete='0' AND country_id='$country_id' AND country_id!=''");
				$response['salaryDataArray'] = [];
				if (mysqli_num_rows($salaryQry)>0) {
					while ($salaryData = mysqli_fetch_assoc($salaryQry)) {
						$response['salaryDataArray'][] = $salaryData;
					}
				}

				if(isset($tax_slab_year) && $tax_slab_year != '') {
					$slabQry = $d->selectRow("tax_slab_master.*", "tax_slab_master", "tax_slab_year='$tax_slab_year'");
					$response['slabDataArray'] = [];
					if (mysqli_num_rows($slabQry)>0) {
						while ($slabData = mysqli_fetch_assoc($slabQry)) {
							$response['slabDataArray'][] = $slabData;
						}
					}
				}

				$response["idProofDataArray"] = array(); //$tax_slab_year & $country_id is required
				$idProofWhere = "is_deleted='0' AND active_status='0' AND country_id='$country_id' AND country_id!=''";
				$idProofQry = $d->selectRow("id_proof_master.*", "id_proof_master", $idProofWhere);
				if (mysqli_num_rows($idProofQry)>0) {
					while ($idProofData = mysqli_fetch_assoc($idProofQry)) {
						$response["idProofDataArray"][] = $idProofData;
					}
				}
				$response["message"] = $xml->string->success . '';
				$response["status"] = "200";
				echo $d->manage_encryption($is_encrypted, $response, $compress);
				exit;
			} else if (isset($getDefaultHolidaysData) && $_POST['getDefaultHolidaysData'] == "getDefaultHolidaysData") {
				$response["holidaysData"] = array();
				if (isset($holiday_year) && $holiday_year > 0) {
					$holidayWhere = "holiday_year = '$holiday_year' AND country_id='$country_id' AND country_id!=''";
					$q = $d->selectRow("holidays_master.*", "holidays_master", $holidayWhere, "order by holiday_id  DESC");
					while ($data = mysqli_fetch_array($q)) {
						$holidayArray = array();
						$holidayArray["holiday_id"] = $data["holiday_id"];
						$holidayArray["holiday_year"] = $data["holiday_year"];
						$holidayArray["festival_name"] = $data["festival_name"];
						$holidayArray["festival_image"] = $data["festival_image"];
						$holidayArray["holiday_date"] = $data["holiday_date"];
						$holidayArray["holiday_desc"] = $data["holiday_desc"];
						if ($data["festival_image"] != "") {
							$holidayArray["festival_image"] = $base_url . "img/master/holiday/" . $data["festival_image"];
						} else {
							$holidayArray["festival_image"] = "";
						}
						$holidayArray["holiday_status"] = $data["holiday_status"];
						array_push($response["holidaysData"], $holidayArray);
					}
				}
				$response["message"] = 'Success';
				$response["status"] = "200";
				echo $d->manage_encryption($is_encrypted, $response, $compress);
				exit;
			} else {
				$response["message"] = 'Wrong Tag';
				$response["status"] = "201";
				echo $d->manage_encryption($is_encrypted, $response, $compress);
				exit;
			}
		} else {
			$response["message"] = 'Wrong api key';
			$response["status"] = "201";
			echo $d->manage_encryption($is_encrypted, $response, $compress);
			exit;
		}
	}
} catch (Exception $e) {
	$response['status'] = "201";
	$response['message'] = $e->getMessage();
}
echo $d->manage_encryption($is_encrypted, $response, $compress);
exit();
