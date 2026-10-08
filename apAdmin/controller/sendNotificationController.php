<?php
include '../common/objectController.php';
if(isset($_POST)){
	extract($_POST);
	if(isset($sendNoti)){
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
	    $q = $d->select("society_master" ,"society_id='$noti_society_id'","order by society_id DESC");
	    $data=mysqli_fetch_array($q);
	    if($imagefile==''){
	    	$imageurl = '';
	    }else{
	    	$imageurl = $base_url.'apAdmin/img/noticeBoard/'.$imagefile;
	    }
	    $hit_url = $data['sub_domain'];
	    $post = array(
	      'send_notofication' => 'send_notofication',
	      'society_id' => $noti_society_id,
	      'title' => $noti_title,
	      'description' => $noti_description,
	      'notiUrl' => $imageurl,
	      'sendto' => $noti_send_to,
	    );
	    $res = $d->callCompanyApiEnc($hit_url, 'sendNotificationCurlController.php', $post);

	      if(is_array($res) && isset($res['status']) && $res['status']=='200'){
	        $_SESSION['msg']=$res['message'];
			header("Location: ../welcome");
	      }else{
	      	$_SESSION['msg1']=is_array($res) && isset($res['message']) ? $res['message'] : 'Something went wrong.';
			header("Location: ../welcome");
	      }
	} else {
		$_SESSION['msg1']="Invalid Form Submit.";
		header("Location: ../welcome");
	}
}else{
	$_SESSION['msg1']="Invalid Request.";
	header("Location: ../welcome");
}
