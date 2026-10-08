<?php 
include '../common/objectController.php';

if(isset($_POST) && !empty($_POST) ){
// add new employee

 
  if ( !isset($changeStausComp) && (isset($compalain_title) && !preg_match('/^[a-z\d\-_\s]+$/i',$compalain_title))   ){ 
        $_SESSION['msg1']="Special Characters are Not Allowed, please provide Alphanumeric string";
           header("location:../complaints");
        exit;
  }  


if(isset($changeStausComp)) {

        $m->set_data('complain_status',$complain_status);
        $m->set_data('complain_review_msg',$complain_review_msg);
        if ($complain_status==1) {
        $m->set_data('complain_closed_by',$bms_admin_id);
        } else{
        $m->set_data('complain_closed_by',0);
        }

         $a1= array (
            
            'complain_status'=> $m->get_data('complain_status'),
            'complain_closed_by'=> $m->get_data('complain_closed_by'),
            'complain_review_msg'=> $m->get_data('complain_review_msg'),
            //IS_723
            'flag_delete'=> "0",
        );

    $q=$d->update("complains_master",$a1,"complain_id='$complain_id'");


    if ($complain_review_msg!='' || $_FILES["complains_track_img"]!='' || $_FILES["complains_track_voice"]!='') {

      if(file_exists($_FILES["complains_track_img"]["tmp_name"])) {

        $temp = explode(".", $_FILES["complains_track_img"]["name"]);
        $complains_track_img = 'Complaint_'.round(microtime(true)) . '.' . end($temp);
        move_uploaded_file($_FILES["complains_track_img"]["tmp_name"], "../../img/complain/".$complains_track_img);
      }
      
      if(file_exists($_FILES["complains_track_voice"]["tmp_name"])) {
        $temp = explode(".", $_FILES["complains_track_voice"]["name"]);
        $complains_track_voice = 'ComplaintA_'.round(microtime(true)) . '.' . end($temp);
        move_uploaded_file($_FILES["complains_track_voice"]["tmp_name"], "../../img/complain/".$complains_track_voice);
      }

   
                 if ($complain_status==0) { $cStatus="OPEN";} 
                 elseif ($complain_status==1) { $cStatus="CLOSED";  } 
                 elseif ($complain_status==2) { $cStatus="REOPEN";} 
                 elseif ($complain_status==3) { $cStatus="In Progress";}

      $m->set_data('complain_id',$complain_id);
      $m->set_data('society_id',$society_id);
      $m->set_data('complains_track_msg',$complain_review_msg);
      $m->set_data('complains_track_img',$complains_track_img);
      $m->set_data('complains_track_voice',$complains_track_voice);
      $m->set_data('admin_id',$bms_admin_id);
      $m->set_data('complains_track_date_time',date('Y-m-d H:i:s'));

      $a2= array (
        'complains_track_by'=> 1,
        'society_id'=> $m->get_data('society_id'),
        'complain_id'=> $m->get_data('complain_id'),
        'complains_track_msg'=> $m->get_data('complains_track_msg'),
        'complains_track_img'=> $m->get_data('complains_track_img'),
        'complains_track_voice'=> $m->get_data('complains_track_voice'),
        'admin_id'=> $m->get_data('admin_id'),
        'complains_track_date_time'=> $m->get_data('complains_track_date_time'),
         'complaint_status_view' => $cStatus,
      );
      $d->insert("complains_track_master",$a2);
    }
  if($q==TRUE) {


            $qUserToken=$d->select("users_master","society_id='$society_id' AND unit_id='$unit_id' AND user_id='$user_id'");
            $data_notification=mysqli_fetch_array($qUserToken);
            $sos_user_token=$data_notification['user_token'];
            $device=$data_notification['device'];
            if ($device=='android') {
               $nResident->noti("ComplainFragment","",$society_id,$sos_user_token,"Your Complaint for $compalain_title is","$cStatus ",'complain');
            }  else if($device=='ios') {
              $nResident->noti_ios("ComplaintsVC","",$society_id,$sos_user_token,"Your Complaint for $compalain_title is","$cStatus ",'complain');
            }
            // $nResident->noti("ComplainFragment","",$society_id,$sos_user_token,"Your Complain for $compalain_title is","$cStatus ",'complain');

            $notiAry = array(
            'society_id'=>$society_id,
            'user_id'=>$data_notification['user_id'],
            'notification_title'=>"Your Complaint for $compalain_title is",
            'notification_desc'=>"$cStatus ",    
            'notification_date'=>date('Y-m-d H:i'),
            'notification_action'=>'complain',
             'notification_logo'=>'Comp.png',
            );
            $d->insert("user_notification",$notiAry);

    $_SESSION['msg']="Complain Status Updated";
            $d->insert_log("$society_id","$bms_admin_id","$created_by","Complaint Status Updated");
    
    header("Location: ../complaints");
  } else {
    header("location: ../complaints");
  }
}


if (isset($addComplaint)) {

   
    extract(array_map("test_input" , $_POST));

    $file = $_FILES['complain_photo']['tmp_name'];
    if(file_exists($file)) {

      $temp = explode(".", $_FILES["complain_photo"]["name"]);
      $complain_photo = "BillCat_".round(microtime(true)) . '.' . end($temp);
      move_uploaded_file($_FILES['complain_photo']['tmp_name'], '../../img/complain/'.$complain_photo);    
    } 
    $complain_date =date('Y-m-d h:i A');

    $unitData = $d->selectArray("users_master","user_id='$user_id'");
    $unitDetailsData = $d->selectArray("unit_master","unit_id='$unitData[unit_id]'");
    $blockData = $d->selectArray("block_master","block_id='$unitData[block_id]'");
    $block_no = $blockData['block_name']."-".$unitDetailsData['unit_name'];

    $m->set_data('society_id',$society_id);
    $m->set_data('complain_no',"CN".$complain_id);
    $m->set_data('complain_id',$complain_id);
    $m->set_data('unit_id',$unitData['unit_id']);
    $m->set_data('complain_assing_to',$block_no);
    $m->set_data('user_id',$user_id);
    $m->set_data('complain_photo',$complain_photo);
    $m->set_data('complaint_category',$complaint_category);
    $m->set_data('compalain_title',$compalain_title);
    $m->set_data('complain_description',$complain_description);
    $m->set_data('complain_date',$complain_date);
    $m->set_data('admin_id',$bms_admin_id);


    $a2 = array(
      'society_id'=>$m->get_data('society_id'),
      'complain_no'=>$m->get_data('complain_no'),
      'unit_id'=>$m->get_data('unit_id'),
      'complain_assing_to'=>$m->get_data('complain_assing_to'),
      'user_id'=>$m->get_data('user_id'),
      'complain_photo'=>$m->get_data('complain_photo'),
      'complaint_category'=>$m->get_data('complaint_category'),
      'compalain_title'=>$m->get_data('compalain_title'),
      'complain_description'=>$m->get_data('complain_description'),
      'complain_date'=>$m->get_data('complain_date'),
      'complain_status'=>0,
    );

    $q2 = $d->insert("complains_master",$a2);
     $complain_id = $con->insert_id;

    $a3 = array(
      'complains_track_by'=>1,
      'society_id'=>$m->get_data('society_id'),
      'complains_track_msg'=>$m->get_data('compalain_title'),
      'complains_track_img'=>$m->get_data('complain_photo'),
      'complain_id'=>$m->get_data('complain_id'),
      'admin_id'=>$m->get_data('admin_id'),
      'complains_track_date_time'=>$m->get_data('complain_date'),
      'complaint_status_view'=>"Open",

    );

    $q2 = $d->insert("complains_track_master",$a3);


    if($q2==TRUE) {
      $qUserToken=$d->select("users_master","society_id='$society_id' AND user_id='$user_id'");
      $data_notification=mysqli_fetch_array($qUserToken);
      $sos_user_token=$data_notification['user_token'];

      $device=$data_notification['device'];
      if ($device=='android') {
         $nResident->noti("ComplainFragment","",$society_id,$sos_user_token,"Your Complaint for $compalain_title is Registered","Complaint Registered Successfully by $created_by",'complain');
      }  else if($device=='ios') {
        $nResident->noti_ios("ComplaintsVC","",$society_id,$sos_user_token,"Your Complaint for $compalain_title is Registered","Complaint Registered Successfully by $created_by",'complain');
      }

      $notiAry = array(
        'society_id'=>$society_id,
        'user_id'=>$data_notification['user_id'],
        'notification_title'=>"Your Complaint for $compalain_title is Registered",
        'notification_desc'=>"Complaint Registered Successfully by $created_by",    
        'notification_date'=>date('Y-m-d H:i'),
        'notification_action'=>'complain',
        'notification_logo'=>'Comp.png',

      );
      $d->insert("user_notification",$notiAry);


      $_SESSION['msg']="Complaint Added";
      header("Location: ../complaints");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("Location: ../complaints");
    }
  }


}
 ?>