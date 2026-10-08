<?php
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST) ){
  extract($_POST);
  if (isset($sessionModule) && $sessionModule == 'sessionModule') {
    $startTime = date("H:i:00", strtotime($_POST['start_time']));
    $endTime = date("H:i:00", strtotime($_POST['end_time']));
    $m->set_data('session_name', test_input($session_name));
    $m->set_data('session_days', $session_days);
    $m->set_data('start_time', $startTime);
    $m->set_data('end_time', $endTime);
    $m->set_data('session_status', $session_status);
    $m->set_data('session_day_id', $session_day_id);

    $a = array(
        'session_name' => $m->get_data('session_name'),
        'session_days' => $m->get_data('session_days'),
        'start_time' => $m->get_data('start_time'),
        'end_time' => $m->get_data('end_time'),
        'session_status' => $m->get_data('session_status'),
        'session_day_id' => $m->get_data('session_day_id'),
    );

    $q = $d->insert("session_master", $a);
    if ($q > 0) {
        $d->insert_log("$society_id", "$_SESSION[bms_admin_id]", "$created_by", "New Session successfully added");
        $_SESSION['msg'] = "New Session successfully added.";
        header("Location: ../manageSession");
    } else {
        $_SESSION['msg1'] = "Something went wrong while adding the session.";
    }
  }
  else if(isset($sessionModule) && $sessionModule == 'sessionModuleEdit') {
    $startTime = date("H:i:00", strtotime($start_time));
    $endTime = date("H:i:00", strtotime($end_time));

    $m->set_data('session_name', test_input($session_name));
    $m->set_data('session_days', $session_days);
    $m->set_data('start_time', $startTime);
    $m->set_data('end_time', $endTime);
    $m->set_data('session_status', $session_status);
    $m->set_data('session_day_id', $session_day_id);

    $a = array(
        'session_name' => $m->get_data('session_name'),
        'session_days' => $m->get_data('session_days'),
        'start_time' => $m->get_data('start_time'),
        'end_time' => $m->get_data('end_time'),
        'session_status' => $m->get_data('session_status'),
        'session_day_id' => $m->get_data('session_day_id'), 
    );
    $editId = $d->sanitizeActionIdAsInt($editId ?? ($_POST['editId'] ?? 0));
    $q = $d->update("session_master", $a, "session_id='$editId'");

    if ($q > 0) {
        $d->insert_log("$society_id", "$_SESSION[bms_admin_id]", "$created_by", "Session successfully updated.");
        $_SESSION['msg'] = "Session successfully updated.";
        header("Location: ../manageSession");
    } else {
        $_SESSION['msg1'] = "Something went wrong while updating the session.";
    }
  }
  else{
    $_SESSION['msg1'] = "Invalid Request.";
    header("Location: ../manageSession");
    exit;
  }
}else{
  $_SESSION['msg1']="Something went wrong";
  header("Location: ../manageSession");
  exit;
}
