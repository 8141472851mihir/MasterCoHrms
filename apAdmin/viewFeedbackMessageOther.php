<?php 
include_once 'common/object.php';
//IS_846
session_start();
error_reporting(0);
date_default_timezone_set('Asia/kolkata');
// include 'common/checkLanguage.php';


extract(array_map("test_input" , $_POST));
if (isset($feedback_id)) {
	$q=$d->select("feedback_master","feedback_id='$feedback_id'","");
	$row=mysqli_fetch_array($q);
	extract($row);

	if ($read_by==0) {
        $read_by=$bms_admin_id;
        $m->set_data('read_by',$read_by);
        $m->set_data('read_time',date('Y-m-d H:i:s'));
        $a1= array (
        	'read_by'=> $m->get_data('read_by'),
        	'read_time'=> $m->get_data('read_time'),
        );
      	$q=$d->update('feedback_master',$a1,"feedback_id='$feedback_id'");
	}
}
?>


<h6>Subject : <?php echo $subject;?></h6>
<p>Name : <?php echo $name;?></p>
<p>Mobile : <?php echo  $country_code.' '.$mobile;?></p>
<p>Email : <?php echo $email;?></p>
<p>App Version : <?php echo $app_version_code;?></p>
<p>Device : <?php echo $device;?></p>
<p>Message: <?php echo $feedback_msg;?></p>
<?php if($role_id==1) { ?>

<p>Admin Reply :<?php echo $client_reply_message; ?> </p>
 <?php } ?>
<p>Date Time : <?php 
    if ($default_time_zone!="Asia/Kolkata" && !empty($default_time_zone)) {
        echo $d->change_timezone($feedback_date_time,$default_time_zone,'d M Y h:i A');
    } else {
        echo date("d M Y h:i A", strtotime($feedback_date_time)); 
            
    } 

    ?>
        </p>

<?php  if ($attachment!='') { ?>
    Attachment:  <a target="_blank" href="../img/fin_support/<?php echo $attachment;?>">View </a>
<?php } ?>

<?php  if ($read_by!=0) { ?>
    <p>Read by:  <?php $adminData=$d->selectArray("bms_admin_master","admin_id='$read_by'"); echo $adminData['admin_name']; ?></p>
    <p>Read at: <?php if($read_time!='') {  
         if ($default_time_zone!="Asia/Kolkata") {
        echo $d->change_timezone($read_time,$default_time_zone,'d M Y h:i A');
        } else {
            echo date('d M Y H:i A',strtotime($read_time)); }
             } ?></p>
<?php } ?>
