<?php 
include_once 'common/object.php';
//IS_846
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Kolkata');
extract(array_map("test_input" , $_POST));
if (isset($feedback_id)) {
	$q = $d->selectRow(
		"feedback_master.*,
		 society_master.society_name as sm_society_name,
		 society_master.city_name as sm_city_name,
		 society_master.sub_domain as sm_sub_domain,
		 society_master_white_label.society_name as wl_society_name,
		 COALESCE(wl_city.name, society_master_white_label.city_name) as wl_city_name,
		 society_master_white_label.sub_domain as wl_sub_domain",
		"feedback_master
		 LEFT JOIN society_master ON society_master.society_id = feedback_master.society_id AND COALESCE(feedback_master.is_whitelabel, 0) = 0
		 LEFT JOIN society_master_white_label ON society_master_white_label.society_id = feedback_master.society_id AND society_master_white_label.project_type = feedback_master.whitelabel_type AND COALESCE(feedback_master.is_whitelabel, 0) = 1
		 LEFT JOIN cities wl_city ON wl_city.city_id = society_master_white_label.city_id",
		"feedback_master.feedback_id='$feedback_id'",
		""
	);
	$row = mysqli_fetch_array($q);
	extract($row);

	// Normalize display fields for both normal + whitelabel tickets.
	if ((int)($is_whitelabel ?? 0) === 1) {
		$society_name = $wl_society_name ?? $society_name;
		$city_name = $wl_city_name ?? $city_name;
		$sub_domain = $wl_sub_domain ?? $sub_domain;
	} else {
		$society_name = $sm_society_name ?? $society_name;
		$city_name = $sm_city_name ?? $city_name;
		$sub_domain = $sm_sub_domain ?? $sub_domain;
	}

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

<?php
$companyHeading = $society_name . '-' . $city_name;
if ((int)($is_whitelabel ?? 0) === 1) {
	$whitelabelTypeLabels = [
		0 => 'MyCo',
		1 => 'Smart Society',
		2 => 'My Association',
	];
	$typeLabel = $whitelabelTypeLabels[(int)($whitelabel_type ?? 0)] ?? 'Whitelabel';
	$companyHeading = $society_name . '-' . $city_name . ' (' . $typeLabel . ' Whitelabel)';
}
?>
<h6 ondblclick="openLogin('<?php echo $sub_domain.'apAdmin';?>');">Company : <?php echo $companyHeading;?></h6>
<h6>Subject : <?php echo $subject;?></h6>
<?php
    $platform_name = "";
    if($platform==0){
        $platform_name = "Other";
    }else if($platform==1){
        $platform_name = "Android";
    }else if($platform==2){
        $platform_name = "iOS";
    }else if($platform==3){
        $platform_name = "Web";
    }else if($platform==4){
        $platform_name = "CRM";
    }
?>
<h6>Platform : <?php echo $platform_name;?></h6>
<p>Name : <?php echo $name;?></p>
<p>Mobile : <?php echo $mobile;?></p>
<?php if($email!=''){ ?><p>Email : <?php echo $email;?></p><?php } ?>
<?php if($app_version_code!=''){ ?><p>App Version : <?php echo $app_version_code;?></p> <?php } ?>
<?php if($device!=''){ ?><p>Device : <?php echo $device;?></p> <?php } ?>
<p>Message : <?php echo $feedback_msg;?></p>
<?php
    $adminDataCreated=$d->selectArray("bms_admin_master","admin_id='$created_by'");
?>
<p>Created By : <?=($adminDataCreated) ? $adminDataCreated['admin_name'] : 'App User';?></p>
<?php if($role_id==1) { ?>

<p>Admin Reply :<?php echo $client_reply_message; ?> </p>
 <?php } ?>
<p>Date Time : <?php 
    if ($default_time_zone!="Asia/Kolkata") {
        echo $d->change_timezone($feedback_date_time,$default_time_zone,'d M Y h:i A');
    } else {
        echo date("d M Y h:i A", strtotime($feedback_date_time)); 
            
    } 

    ?>
        </p>

<?php  if ($attachment!='') { ?>

    <p>Attachment 1 :  <a href="../img/fin_support/<?php echo $attachment;?>" data-fancybox="images" data-caption="">View</a></p>
<?php } ?>

<?php  if ($attachment_2!='') { ?>
    <p>Attachment 2 :  <a href="../img/fin_support/<?php echo $attachment_2;?>" data-fancybox="images" data-caption="">View </a></p>
<?php } ?>

<?php  if ($video!='') { ?>
    <p>Video :  <a target="_blank" href="../img/fin_support/<?php echo $video;?>">View </a></p>
<?php } ?>

<?php  if ($read_by!=0) { ?>
    <p>Read by :  <?php $adminData=$d->selectArray("bms_admin_master","admin_id='$read_by'"); echo $adminData['admin_name']; ?></p>
    <p>Read at : <?php if($read_time!='') {  
         if ($default_time_zone!="Asia/Kolkata") {
        echo $d->change_timezone($read_time,$default_time_zone,'d M Y h:i A');
        } else {
            echo date('d M Y H:i A',strtotime($read_time)); }
             } ?></p>
<?php } ?>
<?php
    $dev_tat = "";
    if($develeoper_assign_time!='' && $developer_solve_time!=''){
        $time1 = new DateTime($develeoper_assign_time);
        $time2 = new DateTime($developer_solve_time);
        $dev_tat = $time1->diff($time2);
    }
    if($dev_tat!=""){
?>
<p>Developer TAT : <?=$dev_tat->format('%m months %d days %h hours %i minutes')?></p>
<?php } ?>
<?php
    $feed_tat = "";
    if($feedback_date_time!='' && $feedback_solve_time!=''){
        $time1 = new DateTime($feedback_date_time);
        $time2 = new DateTime($feedback_solve_time);
        $feed_tat = $time1->diff($time2);
    }
    if($feed_tat!=""){
?>
<p>Feedback TAT : <?=$feed_tat->format('%m months %d days %h hours %i minutes')?></p>
<?php } ?>
<script type="text/javascript">
    function openLogin(url) {
         window.open(url); 
    }
</script>