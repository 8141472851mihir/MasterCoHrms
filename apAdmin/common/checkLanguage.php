<?php 
if (isset($_COOKIE['language_id'])) {
	$language_id = $_COOKIE['language_id'];
} else {
	$language_id = $_REQUEST['language_id'];
}
// Try to load XML from S3 first
if ($language_id != "") {
	$xml = $d->loadLanguageXmlFromS3($language_id, "xml");
	if ($xml === false) {
		$q=$d->select("bms_admin_master","admin_id='$bms_admin_id'");
		$data=mysqli_fetch_array($q);
		$language_id = $data['primary_language_id'];
		setcookie('country_id', $country_id, time() + (86400 * 1 ), "/"); // 86400 = 1 day
		$d->createLanguageFiles($language_id, $base_url, '', 'both');
		$xml = $d->loadLanguageXmlFromS3($language_id, "xml");
	}
}
