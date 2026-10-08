<?php 
session_start();
session_destroy();
setcookie('master_token', '', -1, '/'); 
header("location:index.php");
 ?>