<?php 

$txt = "Cron Run Time GMT : ".date("Y-m-d h:i:s A");
$myfile = file_put_contents('../img/cronTest.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);

?>