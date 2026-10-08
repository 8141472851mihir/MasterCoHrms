<?php
include '../common/objectController.php';
	if($_POST['send_notofication']=="send_notofication"){
		if($society_id!='' && $title!='' && $description!='' && $notiUrl!='' && $sendto!=''){
			$title = ucfirst($title);
			$description = ucfirst($description);
			if($sendto=='Admin'){
				$fcmArray = $d->get_admin_fcm("bms_admin_master", "token!='' AND society_id='$society_id' AND device='android'");
				$fcmArrayIos = $d->get_admin_fcm("bms_admin_master", "token!='' AND society_id='$society_id' AND device!='android'");
				$nAdmin->noti("$notiUrl", $fcmArray, $title,$description, "");
				$nAdmin->noti_ios("$notiUrl", $fcmArrayIos, $title, $description, "");
			}else if($sendto=='Users'){
				$clickAray = array(
					'title' => $title,
					'description' => $description,
					'img_url' =>  $notiUrl,
					'notification_time' => date("d M Y h:i A"),
				);
				$fcmArray=$d->get_android_fcm("users_master","user_token!='' AND society_id='$society_id' AND device='android'");
				$fcmArrayIos=$d->get_android_fcm("users_master","user_token!='' AND society_id='$society_id' AND device='ios'");
				$nResident->noti("custom_notification",$notiUrl,$society_id,$fcmArray,$title,$description,$clickAray);
				$nResident->noti_ios("custom_notification",$notiUrl,$society_id,$fcmArrayIos,$title,$description,$clickAray);
			}else{
				// /****************** Admin ****************/
				// $fcmArrayAdmin = $d->get_admin_fcm("bms_admin_master", "token!='' AND society_id='$society_id' AND device='android'");
				// $fcmArrayIosAdmin = $d->get_admin_fcm("bms_admin_master", "token!='' AND society_id='$society_id' AND device!='android'");
				// $nAdmin->noti("$notiUrl", $fcmArrayAdmin, $title,$description, "");
				// $nAdmin->noti_ios("$notiUrl", $fcmArrayIosAdmin, $title, $description, "");
				// /****************** User ****************/
				// $clickAray = array(
				// 	'title' => $title,
				// 	'description' => $description,
				// 	'img_url' =>  $notiUrl,
				// 	'notification_time' => date("d M Y h:i A"),
				// );
				// $fcmArrayUser=$d->get_android_fcm("users_master","user_token!='' AND society_id='$society_id' AND device='android'");
				// $fcmArrayIosUser=$d->get_android_fcm("users_master","user_token!='' AND society_id='$society_id' AND device='ios'");
				// $nResident->noti("custom_notification",$notiUrl,$society_id,$fcmArrayUser,$title,$description,$clickAray);
				// $nResident->noti_ios("custom_notification",$notiUrl,$society_id,$fcmArrayIosUser,$title,$description,$clickAray);
				// /****************** Guard ****************/
				// $fcmArrayGuard = $d->get_emp_fcm("guard_master", "token!='' AND society_id='$society_id' AND device='android'");
				// $fcmArrayIosGuard = $d->get_emp_fcm("guard_master", "token!='' AND society_id='$society_id' AND device!='android'");
				// $nGaurd->noti($fcmArrayGuard, $title,$description, "",$m);
				// $nGaurd->noti_ios($fcmArrayIosGuard, $title, $description, "",$m);
			}
			$reArray = array('status'=>'200','message'=>'Notification Send');
		}else{ $reArray = array('status'=>'201','message'=>'Please Complete All Mandatory Fields.'); }
	}else{ $reArray = array('status'=>'201','message'=>'Wrong Tag.'); }
echo json_encode($reArray);
