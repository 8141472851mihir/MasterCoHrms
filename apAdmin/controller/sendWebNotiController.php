<?php
include '../common/objectController.php';
// Server Key
$cloud_messaging_server_key = $d->get_server_key();

if(isset($_POST) && !empty($_POST) ){
    if (isset($_POST['sendWebNoti'])) {
        if(!empty($_FILES['noti_notiUrl']['name'])){
            $file_noti_notiUrl = $_FILES['noti_notiUrl']['tmp_name'];
            if (file_exists($file_noti_notiUrl)) {
              $acceptable = array("jpeg","jpg","png");
              $extId = pathinfo($_FILES['noti_notiUrl']['name'], PATHINFO_EXTENSION);
              $dirPath = "../../img/noticeBoard/";
              $maxsize    = 2 * 1024 * 1024;
              if (in_array($extId, $acceptable) && (!empty($_FILES["noti_notiUrl"]["type"]))) {
                $temp = explode(".", $_FILES["noti_notiUrl"]["name"]);
                $noti_notiUrl = 'Notice_'.rand() . '.' . end($temp);
                $destinationPath = $dirPath . $noti_notiUrl;
                $d->resizeImage($file_noti_notiUrl, $destinationPath, 1280, 720, $extId);
                $imagefile = $noti_notiUrl;
              } else {
                $_SESSION['msg1'] = "Invalid Image.";
                header("location:../welcome");
                exit();
              }
            } else {
                $_SESSION['msg1'] = "Invalid Image.";
                header("location:../welcome");
                exit();
            }
        }else{
            $imagefile = '';
        }
        if($imagefile==''){
            $imageurl = '';
        }else{
            $imageurl = $base_url.'apAdmin/img/noticeBoard/'.$imagefile;
        }
        $click_action = 'welcome';        
        $getTokens = $d->getWebFcm("web_fcm_master","");
        $nAdmin->sendAdminNotification($getTokens,$noti_title,$noti_description,$click_action,$imageurl);
        
        $_SESSION['msg']="Notification Sent";
        header("Location: ../welcome");
    } else{
        $_SESSION['msg1']="Invalid Form Submit.";
        header("Location: ../welcome");
    }
} else{
    $_SESSION['msg1']="Invalid Request.";
    header("Location: ../welcome");
}
?>