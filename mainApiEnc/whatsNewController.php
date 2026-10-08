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
        if ($key == $keydb) {
            $response = array();
            extract(array_map("test_input", $_POST));
            if (isset($whatsNewListing) && $whatsNewListing == "whatsNewListing") {
                if(isset($platform) && $platform != ""){
                    if(isset($language_id) && $language_id != "") {
                        $lan_where=" AND language_id='$language_id'";
                    }
                    $list_qry=$d->select("patch_master","platform='$platform' $lan_where","ORDER BY patch_id DESC LIMIT 1");
                    if(mysqli_num_rows($list_qry)>0){
                        while($list_data=mysqli_fetch_array($list_qry)){
                            $response['patch_id']=$list_data['patch_id'].'';
                            $response['patch_title']=$list_data['patch_title'].'';
                            $response['language_id']=$list_data['language_id'].'';
                            $response['whats_new']=$list_data['whats_new'].'';
                            $response['version']=$list_data['version'].'';
                            $date = new DateTime($list_data['created_date']);
                            $formattedDate = $date->format('d M Y h:i A');
                            $response['created_date']=($list_data['created_date']!='')?$formattedDate.'':'';
                        }
                        $response["message"] = $xml->string->data_found."";
                        $response["status"] = "200";
                        echo $d->manage_encryption($is_encrypted,$response, $compress);
                        exit;
                    }else{
                        $response["message"] = $xml->string->no_data_found_web.'';
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted,$response, $compress);
                        exit;
                    }
                }else{
                    $response["message"] = $xml->string->please_provide_required_data.'';
                    $response["status"] = "201";
                    echo $d->manage_encryption($is_encrypted,$response, $compress);
                    exit;
                }
            } else {
                $response["message"] = $xml->string->wrong_tag.'';
                $response["status"] = "201";
                echo $d->manage_encryption($is_encrypted,$response, $compress);
                exit;
            }
        } else {

            $response["message"] = $xml->string->wrong_api_key.'';
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted,$response, $compress);
            exit;
        }
    }
} catch (Exception $e) {
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
}
echo $d->manage_encryption($is_encrypted,$response, $compress);
exit();
