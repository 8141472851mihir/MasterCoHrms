<?php
include '../common/objectController.php';
if(isset($_POST['long_desc']))
{
	$a=array(
		'reply'=> trim($long_desc),
		'reply_sent_date'=> date("Y-m-d H:i:s")
	);
	$q=$d->update("contact_us",$a,"id='$user_id'");
	try 
	{
	    $to= $user_email;
	    $subject="Contact Us Reply";
	    $message=trim($long_desc);
        include '../mail.php';
	    $_SESSION['msg']='Reply successfully!';
		header('location:../websiteFeedback');
	} 
	catch (Exception $e) 
	{
	    $_SESSION['msg1']='Something wrong!!';
		header('location:../websiteFeedback');
	}
}
else if(isset($_POST['contact_us_id']))
{
	$a122 = array(
		'status'			=> '0',
		'update_date'	=> date("Y-m-d H:i:s"));
	$d->update("contact_us",$a122,"id='$contact_us_id'");	
	$_SESSION['msg']='Moved to closed successfully!';
	header('location:../websiteFeedback');
}
else 
{
	$_SESSION['msg1']= "Something wrong.";
	header("location:../websiteFeedback");
}

?>