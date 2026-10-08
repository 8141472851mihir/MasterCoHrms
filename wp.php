<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    
<?php 
$mobileno =  $_GET['mobileno'];
$crmuserId = $_GET['crmuserId'];
$auth = $_GET['auth'];
$url="https://openchat.11za.in/chats?mobileno=".$mobileno."&amp;crmuserId=".$crmuserId."&amp;auth=".$auth;

?>

 <iframe title="Embedded Content" style="height:100vh; width:100%" src="<?php echo $url ?>" frameborder="3"></iframe>



</body>
</html>