<?php
include_once 'lib.php';
$_POST =  json_decode($d->manage_decryption("1", file_get_contents("php://input")), true);
$compress = resolve_api_compress();
// $_POST =  $d->manage_encryption("1", $_POST);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted, [
        "status" => "201",
        "message" => "Invalid Data"
    ], $compress);
    exit();
}
try {
    if(isset($_POST) && !empty($_POST)){
        if ($key==$keydb) {
            $response = array();
            extract(array_map("test_input" , $_POST));
            if($_POST['getEmoji']=="getEmoji"){
                // Fetch all emojis in a single query, then group by category (avoids N+1 queries)
                $q=$d->select("emoji","active_status=0","ORDER BY emoji_category ASC, emoji_id ASC");
                if(mysqli_num_rows($q)>0){
                    $emojiCategories = [];
                    $emojiCategoryOrder = [];

                    while($data=mysqli_fetch_array($q)) {
                        $cat = $data['emoji_category'];
                        if(!isset($emojiCategories[$cat])) {
                            $emojiCategories[$cat] = [
                                "emoji_category" => $cat,
                                "emoji" => []
                            ];
                            $emojiCategoryOrder[] = $cat;
                        }

                        $emoji = array(); 
                        $emoji["emoji_id"]=$data['emoji_id'];
                        $emoji["emoji_name"]=$data['emoji_name'];
                        $emoji["emoji_file"]=$base_url.'img/emoji/'.$data['emoji_file'];
                        array_push($emojiCategories[$cat]["emoji"], $emoji);
                    }

                    $response["emoji_category"] = array();
                    foreach($emojiCategoryOrder as $cat) {
                        array_push($response["emoji_category"], $emojiCategories[$cat]);
                    }
                    $response["message"]="Get Emoji Success.";
                    $response["status"]="200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }else{
                    $response["message"]="No Emoji Found.";
                    $response["audio_duration"]=30000;
                    $response["status"]="201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }
            }else{
                $response["message"] = "wrong tag";
                $response["status"]="201";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            }
        }else{
            $response["message"] = "wrong api key";
            $response["status"]="201";
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
