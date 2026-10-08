<?php
  
include_once 'common/object.php';
// ini_set('display_errors', 1);
//   ini_set('display_startup_errors', 1);
//   error_reporting(E_ALL);
  
  
  if (isset($_POST['session_day_id'])) {
      $session_day_id = (int)$_POST['session_day_id'];
      $result = $d->selectRow("session_id, session_name","session_master","session_day_id = '$session_day_id' AND session_status='0' AND delete_status='0'");
      $sessions = [];
      if ($result) {
          while ($row = mysqli_fetch_assoc($result)) {
              $sessions[] = [
                  'session_id' => $row['session_id'],
                  'session_name' => $row['session_name']
              ];
          }
      }

      // Return the session data as JSON
      echo json_encode($sessions);
  } else {
      echo json_encode([]);
  }
?>
