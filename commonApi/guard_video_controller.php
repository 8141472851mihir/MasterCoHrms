<?php
include_once 'lib.php';
if(isset($_POST)){
	extract($_POST);
	if($_POST['get_guard_video']=="get_guard_video"){
		if($language!=''){
			if($language=='E' || $language=='H' || $language=='G'){
				$qs=$d->select("video_guide","language_id='$language' AND video_status='0'","");
				if(mysqli_num_rows($qs)>0){
					$videos = array();
					while ($data=mysqli_fetch_array($qs)) {
		            $listedData = array();
		            $listedData["video_id"]=$data['video_id'];
		            $listedData["language_id"]=$data['language_id'];
		            $listedData["video_title"]=$data['video_title'];
		            $listedData["video_description"]=$data['video_description'];
		            $listedData["video_thumbnail"]=$base_url.'apAdmin/img/videos/thumbnails/'.$data['video_thumbnail'];
		            $listedData["video_file"]=$base_url.'apAdmin/img/videos/'.$data['video_file'];
		            $listedData["uploaded_date"]=$data['uploaded_date'];
		            array_push($videos, $listedData);
		          }
		          $reArray = array('status'=>'200','message'=>'Videos get Successfully.','allvideos'=>$videos);
				}else{ $reArray = array('status'=>'201','message'=>'No Video Available.'); }
			}else{ $reArray = array('status'=>'201','message'=>'Invalid Language.'); }
		}else{ $reArray = array('status'=>'201','message'=>'Please Complete All Mandatory Fields.'); }
	}else{ $reArray = array('status'=>'201','message'=>'Wrong Tag.'); }
}else{ $reArray = array('status'=>'201','message'=>'Invalid request.'); }
echo json_encode($reArray);