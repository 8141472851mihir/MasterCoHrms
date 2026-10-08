<?php 
	// main menu
	$pageName=$_GET['f'];
	$accessPage=$d->select("master_menu","status=0 and menu_link='$pageName'");
	$accessPageData=mysqli_fetch_array($accessPage);
	$pageMenuId=$accessPageData['menu_id'];
	
	if(in_array($pageMenuId, $pagePrivilegeArr) OR in_array($pageMenuId, $accessMenuIdArr) OR $pageName=="profile"  OR $pageName=="welcome" OR $pageName=="success" OR $pageName=="failure" OR $pageName=="readNotification.php" OR $pageName=="bulkDeactiveCompanies" ){
	
	} else {
		$_SESSION['msg2']="Sorry! Access denied, You don't have permission to open this page.";
		// $_SESSION['403']="Sorry! Access denied, You don't have permission to open this page.";

		?>
<script language="javascript"> 
alert("Sorry! Access Denied,"); 
window.location = "welcome"; 
</script> 
		
<?php  
}
?>
