<?php
include_once 'lib.php';
$_POST =  json_decode($d->manage_decryption("1", file_get_contents("php://input")),true);
$compress = resolve_api_compress();
// $_POST =  $d->manage_encryption("1", $_POST);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted,[
        "status" => "201",
        "message" => $xml->string->invalid_request.''
    ], $compress);
    exit();
}
try {
	if (isset($_POST) && !empty($_POST)) {
		$response = array();
		extract(array_map("test_input", $_POST));
		if($_POST['getBirthdayTemplate']=="getBirthdayTemplate" && filter_var($society_id, FILTER_VALIDATE_INT) == true){

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

	            $response["message"] = $xml->string->success.'';
				$response["status"] = "200";
				echo $d->manage_encryption($is_encrypted, $response, $compress);
				exit();
            }else{
            	$response["message"] = $xml->string->no_data_found_web.'';
				$response["status"] = "201";
				echo $d->manage_encryption($is_encrypted, $response, $compress);
				exit();
            }
                      
        }else {
			$response["message"] = $xml->string->wrong_tag.'';
			$response["status"] = "201";
			echo $d->manage_encryption($is_encrypted, $response, $compress);
			exit();
		}
		
	}
} catch (Exception $e) {
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
}
echo $d->manage_encryption($is_encrypted, $response, $compress);
exit();