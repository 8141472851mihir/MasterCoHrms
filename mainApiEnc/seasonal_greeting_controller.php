<?php
include_once 'lib.php';
$_POST =  json_decode($d->manage_decryption("1", file_get_contents("php://input")),true);
$compress = resolve_api_compress();
// $_POST =  $d->manage_encryption("1", $_POST);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted,[
        "status" => "201",
        "message" => $xml->string->invalid_request.""
    ], $compress);
    exit();
}
try {
	if (isset($_POST) && !empty($_POST)) {

		if ($key == $keydb) {

			$response = array();
			extract(array_map("test_input", $_POST));
			
			if ($_POST['getSeasonalGreetings'] == "getSeasonalGreetings") {

				$response["seasonalGreetings"] = array();



				$today = date("Y-m-d"); 

				$meq = $d->selectRow(" * ","seasonal_greet_master,seasonal_greet_image_master", "seasonal_greet_image_master.seasonal_greet_id =seasonal_greet_master.seasonal_greet_id AND   seasonal_greet_master.status='Active' AND  seasonal_greet_image_master.status='Active'  and ( seasonal_greet_master.is_expiry ='No' OR  seasonal_greet_master.end_date >= CURDATE() ) GROUP BY  seasonal_greet_master.seasonal_greet_id order by order_date asc");
				if( mysqli_num_rows($meq) >0 ){ 
					// Prefetch greeting ids + all active images (avoids per-greeting SQL)
					$greetIds = [];
					while ($tmp = mysqli_fetch_array($meq)) {
						$greetIds[] = (int)$tmp["seasonal_greet_id"];
					}

					$greetIds = array_values(array_unique($greetIds));
					$imagesByGreet = [];
					if (!empty($greetIds)) {
						$greetIdsIn = implode(',', $greetIds);
						$child_qry = $d->selectRow(
							" * ",
							"seasonal_greet_image_master",
							"status='Active' AND seasonal_greet_id IN ($greetIdsIn)",
							""
						);
						while ($imgRow = mysqli_fetch_array($child_qry)) {
							$rid = (int)$imgRow["seasonal_greet_id"];
							$imagesByGreet[$rid][] = $imgRow;
						}
					}

					mysqli_data_seek($meq, 0);
					while ($data_app=mysqli_fetch_array($meq)) {
						$seasonalGreetings = array();
						$seasonalGreetings["seasonal_greet_id"] = $data_app["seasonal_greet_id"];
						$seasonalGreetings["title"] = html_entity_decode($data_app["title"]) . '';

						

						$seasonalGreetings["image_array"] = array();
						$imageRows = $imagesByGreet[(int)$data_app["seasonal_greet_id"]] ?? [];
						foreach ($imageRows as $child_data) {
								$image_array = array();

								$image_array["main_title"] = html_entity_decode($data_app["title"]) . '';

								$image_array['seasonal_greet_image_id'] = $child_data['seasonal_greet_image_id'] ;

								if($child_data['cover_image'] ==""){
									$image_array["cover_image"] ="";
								} else {
									$image_array["cover_image"] = $base_url . "img/promotion/" . $child_data['cover_image'];
								}

								$image_array['page_alignment'] = $child_data['page_alignment'] ;
								$image_array['logo_alignment'] = $child_data['logo_alignment'] ;
								$image_array['to_text_alignment'] = $child_data['to_text_alignment'] ;
								$image_array['from_text_alignment'] = $child_data['from_text_alignment'] ;
								$image_array['title_alignment'] = '';
								$image_array['description_alignment'] = "" ;
								$image_array['background_image'] = $child_data['background_image'] ;
								if($child_data['background_image'] ==""){
									$image_array["background_image"] ="";
								} else {
									$image_array["background_image"] = $base_url . "img/promotion/" . $child_data['background_image'];
								}


								$image_array['title_on_image'] = "" ;
								$image_array['title_font_color'] = "";
								$image_array['title_font_name'] = "";
								$image_array['description_on_image'] = "";
								$image_array['description_font_color'] ="";
								$image_array['description_font_name'] = "" ;
								$image_array['show_to_name'] = $child_data['show_to_name'] ;

								if($child_data['show_to_name']=="Yes"){
									$image_array['to_name_font_color'] = $child_data['to_name_font_color'] ;
									$image_array['to_name_font_name'] = $child_data['to_name_font_name'] ;
									$image_array['to_name_font_size'] = $child_data['to_name_font_size'] ;
								} else {
									$image_array['to_name_font_color'] = "";
									$image_array['to_name_font_name'] = "";
									$image_array['to_name_font_size'] = "";
								}

								if($child_data["show_to_name"]=="Yes"){
									$image_array["show_to_name"] =true;
								} else{
									$image_array["show_to_name"] =false;
								}
								$image_array['show_from_name'] = $child_data['show_from_name'] ;

								if($child_data['show_from_name']=="Yes"){
									$image_array['from_name_font_color'] = $child_data['from_name_font_color'] ;
									$image_array['from_name_font_name'] = $child_data['from_name_font_name'] ;
									$image_array['from_name_font_size'] = $child_data['from_name_font_size'] ;
								} else {
									$image_array['from_name_font_color'] = "";
									$image_array['from_name_font_name'] = "";
									$image_array['from_name_font_size'] ="";
								}

							    if($child_data["show_from_name"]=="Yes"){
									$image_array["show_from_name"] =true;
								} else{
									$image_array["show_from_name"] =false;
								}
								//$image_array['status'] = $child_data['status'] ;
								array_push($seasonalGreetings["image_array"], $image_array);
							}
						array_push($response["seasonalGreetings"], $seasonalGreetings);
					}
					$response["message"] = $xml->string->data_found."";
					$response["status"] = "200";
					echo $d->manage_encryption($is_encrypted, $response, $compress);
					exit;
				} else {
					$response["message"] = $xml->string->no_data_found_web.'';
					$response["status"] = "201";
					echo $d->manage_encryption($is_encrypted, $response, $compress);
					exit;
				}
			}    else {
				$response["message"] = $xml->string->wrong_tag.'';
				$response["status"] = "201";
				echo $d->manage_encryption($is_encrypted, $response, $compress);
				exit;
			}
		} else {
			$response["message"] = $xml->string->wrong_api_key.'';
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