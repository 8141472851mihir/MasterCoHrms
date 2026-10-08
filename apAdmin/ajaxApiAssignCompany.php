<?php

include_once 'common/object.php';
error_reporting(0);

extract(array_map("test_input" , $_POST));

if(isset($action) && $action == "checkApiAssignCompany" ) {

	$q = $d->select("kycapi_companyprice_master","society_id='$society_id' and kyc_api_type='$kyc_api_type'","");

	$nr = mysqli_num_rows($q);

	if ($nr> 0 ) {
		echo "false";
	}else{
		echo "true";
	}
}

?>