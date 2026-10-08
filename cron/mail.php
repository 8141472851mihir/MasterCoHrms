<?php
// the message
$msg = " Hi Bhavesh  First line of text\nSecond line of text";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

// send email
mail("bhavesh2484@gmail.com","My aws",$msg);
?>
