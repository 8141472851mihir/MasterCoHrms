<?php 
session_start();
// error_reporting(0);
include 'common/object.php';
include 'common/checkLogin.php';
include 'common/accessControl.php';
date_default_timezone_set('Asia/Kolkata');
if (isset($society_id)) {
	extract($_REQUEST);

	 $m->set_data('read_status','1');
     $arrayName = array('read_status'=>$m->get_data('read_status'));
     $q2=$d->update("admin_notification",$arrayName,"society_id='$society_id' AND notification_id='$id'");

      header("location:$link");


}