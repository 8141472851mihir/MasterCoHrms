<?php
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST) ){
	if(isset($readNoti)) {
		echo "string";
		$a1= array (
        	'read_status'=> 1,
	    );
	    $update=$d->update('admin_notification',$a1,"notification_id='$id'"); 
	    print_r($d);
	}
}
?>
