<?php
include_once 'lib.php';
if (isset($_POST) && !empty($_POST)) {
	if ($key == $keydb) {
		$response = array();
		extract(array_map("test_input", $_POST));
		if (isset($uploadImageToTemp) && $uploadImageToTemp == 'uploadImageToTemp') {
            $imgCount=count($_FILES["img"]["tmp_name"]);
            $i=0;
            $imgNameArr=[];
            while($i<$imgCount){
                $uploadedFile = $_FILES["img"]["tmp_name"][$i];
                $ext = pathinfo($_FILES['img']['name'][$i], PATHINFO_EXTENSION);
                if (file_exists($uploadedFile)) {
                    $sourceProperties = getimagesize($uploadedFile);
                    $newFileName = rand() . $user_id;
                    $dirPath = "../img/temp_images/";
                    $upload_pic = $newFileName . "." . $ext;
                    move_uploaded_file($uploadedFile, "../img/temp_images/" . $upload_pic);
                    array_push($imgNameArr,$upload_pic);
                }
                $i++;
            }
            $response["base_url"] = $base_url . "img/temp_images/";
            $response["img_name_arr"] = $imgNameArr;
            $response["message"] = "Image Uploaded ";
            $response["status"] = "200";
            echo json_encode($response);
		}else {
			$response["message"] = "wrong tag. !";
			$response["status"] = "201";
			echo json_encode($response);
		}
	} else {
		$response["message"] = "wrong api key.";
		$response["status"] = "201";
		echo json_encode($response);
	}
}else{
    $response["message"] = "wrong method.";
    $response["status"] = "201";
    echo json_encode($response);
}
