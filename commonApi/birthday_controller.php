<?php
include_once 'lib.php';
// remove after patch in mcyo

/*ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);*/

if (isset($_POST) && !empty($_POST)) {

	
		$response = array();
		extract(array_map("test_input", $_POST));

		if($_POST['getBirthdayTemplate']=="getBirthdayTemplate" && filter_var($society_id, FILTER_VALIDATE_INT) == true){

			$templateType = $d->escapeSqlString($templateType ?? '');
			$birthdayQry = $d->selectRow("wish_template_id,wish_template,wish_template_type","wishes_template_master","wish_template_status = '0' AND wish_template_type = '$templateType'");


			if (mysqli_num_rows($birthdayQry) > 0) {
			
				$response["birthday_template"] = array();			

				while($birthday_data = mysqli_fetch_array($birthdayQry)){
					$birthDay = array();

					$birthDay['wish_template_id'] = $birthday_data['wish_template_id'];
					$birthDay['wish_template_type'] = $birthday_data['wish_template_type'];

					if ($birthday_data['wish_template'] != '') {
						$birthDay['wish_template'] = $base_url . "img/birthday_template/" .  $birthday_data['wish_template'];
					}else{
						$birthDay['wish_template'] = "";
					}
				
					array_push($response["birthday_template"],$birthDay);
				}

	            $response["message"] = "Success";
				$response["status"] = "200";
				echo json_encode($response);
            }else{
            	$response["message"] = "No Templates Found";
				$response["status"] = "201";
				echo json_encode($response);
            }
                      
        }else {
			$response["message"] = "wrong tag.";
			$response["status"] = "201";
			echo json_encode($response);
		}
	
}
?>