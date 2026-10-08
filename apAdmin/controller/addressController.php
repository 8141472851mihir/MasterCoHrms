<?php
include '../common/objectController.php';
$faqQuery = $d->select("office_address",'','ORDER BY pos ASC');
$faqData = mysqli_fetch_all($faqQuery, MYSQLI_ASSOC);

//$data = json_decode($faqData);
 echo json_encode($faqData);
//print_r($faqData);
die();

?>
