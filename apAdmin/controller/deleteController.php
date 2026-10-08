<?php
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST) )//it can be $_GET doesn't matter
{
  if (isset($ids)) {
    $ids = $d->sanitizeActionIds($ids);
  }
  if (isset($id)) {
    $id = $d->sanitizeActionIdAsInt($id);
  }

  if($_POST['deleteValue']=="deleteComplaintEmail") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("society_create_email_receipt","email_receipt_id='$ids[$i]'");
    }
      if($q>0) {
        echo 1;
         $d->insert_log("$society_id","$bms_admin_id","$created_by","Company Create Email Receipt Deleted");
        $_SESSION['msg']="Email Deleted.";
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
      }
  }
  
  if($_POST['deleteValue']=="deletefaq") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("faq_question_master","faq_sub_master_id='$ids[$i]'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Faq Deleted");
        $_SESSION['msg']="Faq Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }


  if($_POST['deleteValue']=="deleteFestivals") {
    $idCount = count($ids);
    $festivalById = [];
    if ($idCount > 0) {
      $festivalIdsIn = implode(',', array_map('intval', $ids));
      $q1 = $d->select("festival_master", "festival_id IN ($festivalIdsIn)");
      while ($fr = mysqli_fetch_array($q1)) {
        $festivalById[(int)$fr['festival_id']] = $fr;
      }
    }
    for ($i=0; $i <$idCount ; $i++) {
       $fid = (int)$ids[$i];
       $data = $festivalById[$fid] ?? null;
       if ($data) {
         $profileUrl= "../../img/festival/" . $data['festival_image'];
         unlink ($profileUrl);
       }

      $q=$d->delete("festival_master","festival_id='$fid'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Festival Deleted");
        $_SESSION['msg']="Festival Banner Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }

   if($_POST['deleteValue']=="deleteUser") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("students","studentId='$ids[$i]'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","User Deleted");
        $_SESSION['msg']="User Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }


   if($_POST['deleteValue']=="deletePackage") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("gatekeeper_app_access","app_id='$ids[$i]'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Package Deleted");
        $_SESSION['msg']="Package Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }

  if($_POST['deleteValue']=="deletePackageReq") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("gatekeeper_app_access_request","requestId='$ids[$i]'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Package request Deleted");
        $_SESSION['msg']="request Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }


  if($_POST['deleteValue']=="deleteSessionlog") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("session_log","sessionId='$ids[$i]'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Session Log Deleted");
        $_SESSION['msg']="Session Log Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }


 if($_POST['deleteValue']=="deleteOtherlog") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("log_master","log_id='$ids[$i]'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Other Log Deleted");
        $_SESSION['msg']="Other Log Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }
  


  if($_POST['deleteValue']=="deleteNotification") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("notifcation_master","notification_id='$ids[$i]'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Notification Deleted");
        $_SESSION['msg']="Notification Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }

   if($_POST['deleteValue']=="deleteEmpType") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("emp_type_master","emp_type_id='$ids[$i]' AND society_id='$society_id'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Employee Type  Deleted");
        $_SESSION['msg']="Employee Type  Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }

 

   if($_POST['deleteValue']=="deleteBalance") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("balancesheet_master","balancesheet_id='$ids[$i]' AND society_id='$society_id' ");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Balancesheet  Deleted");
        $_SESSION['msg']="Balancesheet  Deleted";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }

  if($_POST['deleteValue']=="deleteFeedback") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("feedback_master","feedback_id='$ids[$i]' ");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Feedback $ids[$i] Deleted");
        $_SESSION['msg']="Feedback  Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  } 

   if($_POST['deleteValue']=="deleteEvent") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("event_master","event_id='$ids[$i]' AND society_id='$society_id'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Event  Deleted");
        $_SESSION['msg']="Event  Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }  

  if($_POST['deleteValue']=="deletePoll") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("voting_master","voting_id='$ids[$i]' AND society_id='$society_id'");
      $q=$d->delete("voting_option_master","voting_id='$ids[$i]'");
      $q=$d->delete("voting_result_master","voting_id='$ids[$i]'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Poll  Deleted");
        $_SESSION['msg']="Poll  Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }

   if($_POST['deleteValue']=="deleteDocumentType") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("document_type_master","  document_type_id='$ids[$i]' AND society_id='$society_id'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Document Type  Deleted");
        $_SESSION['msg']="Document Type   Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }  

  if($_POST['deleteValue']=="deleteDocument") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("document_master","document_id='$ids[$i]' AND society_id='$society_id'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Document  Deleted");
        $_SESSION['msg']="Document  Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }  


 if($_POST['deleteValue']=="deleteElection") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("election_master","election_id='$ids[$i]' AND society_id='$society_id'");
      $q=$d->delete("election_users","election_id='$ids[$i]' AND society_id='$society_id'");
      $q=$d->delete("election_result_master","election_id='$ids[$i]' AND society_id='$society_id'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Election  Deleted");
        $_SESSION['msg']="Election  Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  } 

 if($_POST['deleteValue']=="deleteEmergency") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("emergemcy_number_list","emergency_id='$ids[$i]'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Emergemcy Number  Deleted");
        $_SESSION['msg']="Emergemcy Number  Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }


  if($_POST['deleteValue']=="deleteAdminNotification") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("admin_notification","notification_id='$ids[$i]'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Notification  Deleted");
        $_SESSION['msg']="Notification  Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }

   if($_POST['deleteValue']=="deleteSubAdmin") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("bms_admin_master","admin_id='$ids[$i]' AND admin_type=0");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Notification  Deleted");
        $_SESSION['msg']="Notification  Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }

  if($_POST['deleteValue']=="deleteSos") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("sos_events_master","sos_event_id='$ids[$i]' AND society_id='$society_id'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","SOS Deleted");
        $_SESSION['msg']="SOS Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }

  if($_POST['deleteValue']=="deleteCompalaine") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $a = array('flag_delete' =>2 , );
      $q=$d->update("complains_master",$a,"complain_id='$ids[$i]' AND society_id='$society_id'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","complaint Deleted");
        $_SESSION['msg']="complaint Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }


  if($_POST['deleteValue']=="deleteBalanceFile") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("balancesheet_pdf_master","balancesheet_file_id='$ids[$i]' AND society_id='$society_id'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Balancesheet File Deleted");
        $_SESSION['msg']="File Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }

  if($_POST['deleteValue']=="deletePenalty") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("penalty_master","penalty_id='$ids[$i]'");
    }
      if($q>0) {
        echo 1;
        $d->insert_log("$society_id","$bms_admin_id","$created_by","Penalty Deleted");
        $_SESSION['msg']="Penalty Deleted.";
        // header("location:../categories");
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
        // header("location:../categories");
      }
  }

  if($_POST['deleteValue']=="deleteBillCategory") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("bill_category_master","bill_category_id='$ids[$i]'");
    }
      if($q>0) {
        echo 1;
       $d->insert_log("$society_id","$bms_admin_id","$created_by","Bill Category Deleted");
        $_SESSION['msg']="Bill Category Deleted.";
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
      }
  }

  if($_POST['deleteValue']=="deleteComplaintCategory") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $q=$d->delete("complaint_category","complaint_category_id='$ids[$i]'");
    }
      if($q>0) {
        echo 1;
       $d->insert_log("$society_id","$bms_admin_id","$created_by","Complaint Category Deleted");
        $_SESSION['msg']="Complaint Category Deleted.";
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
      }
  }

  if($_POST['deleteValue']=="deleteCommonSliderSettings") {
    $idCount = count($ids);
    for ($i=0; $i <$idCount ; $i++) { 
      $sliderData=$d->selectArray("app_common_slider_master","app_common_slider_id='$ids[$i]'");
      $q=$d->delete("app_common_slider_master","app_common_slider_id='$ids[$i]'");
      $id = $sliderData['society_id'];
      $slider_id = $sliderData['app_slider_id'];
      if($q>0) {
        $d->insert_log_specific("$id","$bms_admin_id","$created_by","Slider - $slider_id Removed from the Society",2);
      }
    }
      if($q>0) {
        echo 1;
        $_SESSION['msg']="Slider settings Deleted.";
      } else {
        echo 0;
        $_SESSION['msg1']="Something Wrong";
      }
  }

  if ($_POST['deleteValue'] == "deleteRequestSociety") {
    $idCount = count($ids);
    for ($i = 0; $i < $idCount; $i++) {
      $data = $d->selectArray("society_master_requests", "request_society_id='$ids[$i]'");
      $name = $data['request_society_name'];
      $q = $d->delete("society_master_requests", "request_society_id='$ids[$i]'");
      //dhara 15-3-2024
      $deleted_by = $bms_admin_id;
      $deleted_date = date('Y-m-d H:i:s');

      $update_data = array(
        'deleted_by' => $deleted_by,
        'deleted_date' => $deleted_date
      );
      $d->update("society_master_requests", $update_data, "request_society_id='$ids[$i]'");

      //dhara 15-3-2024
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "<b>$name</b> - Requested Company Deleted", 3);
    }
    if ($q > 0) {
      echo 1;
      $_SESSION['msg'] = "Request Societies Deleted.";
    } else {
      echo 0;
      $_SESSION['msg1'] = "Something Wrong";
    }
  }

  if ($_POST['deleteValue'] == "deleteAppBanner") {
    $idCount = count($ids);
    $sliderById = [];
    if ($idCount > 0) {
      $sliderIdsIn = implode(',', array_map('intval', $ids));
      $q1 = $d->selectRow("app_slider_id,slider_image_name", "app_slider_master", "app_slider_id IN ($sliderIdsIn)");
      while ($sr = mysqli_fetch_array($q1)) {
        $sliderById[(int)$sr['app_slider_id']] = $sr;
      }
    }
    for ($i = 0; $i < $idCount; $i++) {
      $sid = (int)$ids[$i];
      $data = $sliderById[$sid] ?? ['slider_image_name' => ''];
      $q = $d->delete("app_slider_master", "app_slider_id='$sid' AND slider_type='0'");
      $d->insert_log_specific("0", "$bms_admin_id", "$created_by", "$sid App Banner Deleted", 2);
      if ($data['slider_image_name'] != "") {
        $profileUrl = "../../img/sliders/" . $data['slider_image_name'];
        unlink($profileUrl);
      }
    }
    if ($q > 0) {
      echo 1;
    } else {
      echo 0;
    }
  }
  if($_POST['deleteValue']== "deleteSuggestion")
  {
    $q=$d->delete("suggestion_master","suggestion_id='$id'");
    if($q>0) {
      echo 1;
    }else{
      echo 0;
    }
  }
   if ($_POST['deleteValue'] == "deleteTaxBenefitCategory") {
        $idCount = count($ids);
        for ($i = 0; $i < $idCount; $i++) {
            $totalAssign = $d->count_data_direct("tax_benefit_sub_category_id", "tax_benefit_sub_category", "tax_benefit_category_id='$ids[$i]'");
            if ($totalAssign == 0) {
                $q = $d->update("tax_benefit_category", array("delete_status" => 1, 'updated_by_id' => $bms_admin_id, 'updated_by_name' => $created_by, 'updated_date' => date('Y-m-d H:i:s')), "tax_benefit_category_id='$ids[$i]'");
            }
        }
        if ($q > 0) {
            echo 1;
            $_SESSION['msg'] = "Deleted successfully";
        } else {
            echo 0;
            $_SESSION['msg1'] = "Something Went Wrong";
        }
    }
  

}
else{
  header('location:../logout');
}
 ?>
