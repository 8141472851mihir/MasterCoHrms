<?php include'../common/objectController.php';


extract($_POST);
if(isset($_POST['AddLanguageKey'])) {
	$m->set_data('key_name',$key_name);
	$m->set_data('key_type',$key_type);
	$m->set_data('no_of_key',$no_of_key);
	
	$a = array('key_name'=>$m->get_data('key_name'),
		'key_type'=>$m->get_data('key_type'),
		'no_of_key'=>$m->get_data('no_of_key')
	);
	$q=$d->insert("language_key_master_gatekeeper",$a);
	

	if ($q === TRUE) {
		
		$_SESSION['msg']="Language Key Added Successfully";
		$d->insert_log("$society_id","$bms_admin_id","$created_by",$_SESSION['msg']);
		
		header ("Location:../manageLanguagKeysGuard");
		
	} else {
		$_SESSION['msg1']="Something Wrong";
		header ("Location:../manageLanguagKeysGuard");
	}

} else if (isset($_POST['delete_language_key_id']))  {
	$delete_language_key_id = $d->sanitizeActionIdAsInt($_POST['delete_language_key_id']);
	$q= $d->delete("language_key_master_gatekeeper","language_key_id='$delete_language_key_id'");
	if($q===TRUE) {
		$_SESSION['msg']="Language Key Deleted";
		$d->insert_log("$society_id","$bms_admin_id","$created_by",$_SESSION['msg']);
		header ('location:../manageLanguagKeysGuard');
	} 	else 	{
		$_SESSION['msg1']="Language Key Not Deleted";
		header ('location:../manageLanguagKeysGuard');
	}
} elseif (isset($_POST['UpdateLanguageKey']))  {
	$w = $d->select('language_key_master_gatekeeper',"key_name='$key_name' and language_key_id!='$language_key_id'");
	if(mysqli_num_rows($w)==0) {
		$m->set_data('key_name',$key_name);
		$m->set_data('key_type',$key_type);
		$m->set_data('no_of_key',$no_of_key);
		

		$a = array('key_name'=>$m->get_data('key_name'),
			'key_type'=>$m->get_data('key_type'),
			'no_of_key'=>$m->get_data('no_of_key'),
		);
		
		$q=$d->update("language_key_master_gatekeeper",$a,"language_key_id='$language_key_id'");
		if ($q === TRUE) {
			
			$_SESSION['msg']="Language Key Updated Successfully";
			$d->insert_log("$society_id","$bms_admin_id","$created_by",$_SESSION['msg']);
			
			header ("Location:../manageLanguagKeysGuard");
			
		} else {
			$_SESSION['msg1']="Something Wrong";
			header ("Location:../manageLanguagKeysGuard");
		}
		
	} else {
		$_SESSION['msg1']="key data inserted";
		header ("Location:../language_key_master_gatekeeper.php");
	}
} else {
	$_SESSION['msg1']="Something Wrong";
	header ("Location:../manageLanguagKeysGuard");
}

?>