<?php 
	$access=$d->select("role_master","role_id='$role_id'");
	$accessData=mysqli_fetch_array($access);
	$accessMenuId=$accessData['menu_id'];
	$accessMenuIdArr=explode(",", $accessMenuId);
	$pagePrivilegeId=$accessData['pagePrivilege'];
	$pagePrivilegeArr=explode(",", $pagePrivilegeId);
?>