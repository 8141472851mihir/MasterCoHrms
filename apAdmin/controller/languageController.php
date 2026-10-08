<?php include'../common/objectController.php';


extract($_POST);
if(isset($_POST['AddLanguage'])) {
	$m->set_data('language_name',$language_name);
	$m->set_data('language_name_1',$language_name_1);
	$m->set_data('continue_btn_name',$continue_btn_name);
	
	$a = array(
		'language_name'=>$m->get_data('language_name'),
		'language_name_1'=>$m->get_data('language_name_1'),
		'continue_btn_name'=>$m->get_data('continue_btn_name') 
	);
	$q=$d->insert("language_master",$a);
	if ($q === TRUE) {

		$_SESSION['msg']="Language Added Successfully";
		$d->insert_log("$society_id","$bms_admin_id","$created_by",$_SESSION['msg']);

		header ("Location:../manageLanguage");

	} else {
		$_SESSION['msg1']="Something Wrong";
		header ("Location:../manageLanguage");
	}
} else if (isset($_POST['deleteid']))  {
  $deleteid = $d->sanitizeActionIdAsInt($_POST['deleteid']);
	$q= $d->delete("language_master","language_id='$deleteid'");
	if ($q === TRUE) {

		$_SESSION['msg']="Language Deleted Successfully";
		$d->insert_log("$society_id","$bms_admin_id","$created_by",$_SESSION['msg']);

		header ("Location:../manageLanguage");

	} else {
		$_SESSION['msg1']="Something Wrong";
		header ("Location:../manageLanguage");
	}
} elseif (isset($_POST['UpdateLanguage']))  {


	$m->set_data('language_name',$language_name);
	$m->set_data('language_name_1',$language_name_1);
	$m->set_data('continue_btn_name',$continue_btn_name);
	
	$a = array(
		'language_name'=>$m->get_data('language_name'),
		'language_name_1'=>$m->get_data('language_name_1'),
		'continue_btn_name'=>$m->get_data('continue_btn_name') 
	);

	$q=$d->update("language_master",$a,"language_id='$language_id'");

	if ($q === TRUE) {

		$_SESSION['msg']="Language Updated Successfully";
		$d->insert_log("$society_id","$bms_admin_id","$created_by",$_SESSION['msg']);

		header ("Location:../manageLanguage");

	} else {
		$_SESSION['msg1']="Something Wrong";
		header ("Location:../manageLanguage");
	}
} else {
	$_SESSION['msg1']="Something Wrong";
	header ("Location:../manageLanguage");
}
?>