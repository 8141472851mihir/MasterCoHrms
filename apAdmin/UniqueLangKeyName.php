<?php 
include_once 'common/object.php';
//IS_742 wholefile
session_start();
$url=$_SESSION['url'] = $_SERVER['REQUEST_URI']; 
$activePage = basename($_SERVER['PHP_SELF'], ".php");
 

extract(array_map("test_input" , $_POST));
if(isset($language_key_id)){
	 $language_key_master=$d->select("language_key_master","   ( upper(key_name) = '".$key_name."' OR lower(key_name) = '".$key_name."' )    and language_key_id !=".$language_key_id);
   
   if(mysqli_num_rows($language_key_master) >0 ){
      echo "false";
   } else {
    echo "true";
   }  
} else {
	$language_key_master=$d->select("language_key_master","   ( upper(key_name) = '".$key_name."' OR lower(key_name) = '".$key_name."' )    " );
   
   if(mysqli_num_rows($language_key_master) >0 ){
      echo "false";
   } else {
    echo "true";
   }  
}
exit;
        
 ?>