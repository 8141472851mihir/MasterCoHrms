<?php

include_once 'common/object.php';
error_reporting(0);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_POST['admin_id'] ?? ''; 
     $report_date = $_POST['report_date'] ?? ''; 
     $report_type = $_POST['report_type'] ?? ''; 
     $eid = $_POST['editId'] ?? ''; 

    if (!empty($admin_id) && !empty($report_date) && $report_type!="") {
        $where = "admin_id = '$admin_id' AND report_date = '$report_date' AND report_type = $report_type";
        if(!empty($eid)){
            $where.=" and implementation_work_report_id!=$eid";
        }
        
        $duplicateCheck = $d->select("implementation_work_report", $where);

        if (mysqli_num_rows($duplicateCheck) > 0) {
            $response['status'] = false;
            $response['message'] = "You have already added a report of this type for the selected date.";
            echo json_encode($response);
            exit();
            header("Location: ../implementWorkreport"); // Redirect back to the report page
            exit();
        } else {
            $response['status'] = true;
            $response['message'] = "Report can be added.";
            echo json_encode($response);
            exit();
        }
    } else {
        $response['status'] = false;
        $response['message'] = "Missing required fields (admin_id, report_date, or report_type).";
        echo json_encode($response);
        exit();
    }
} else {
    $response['status'] = false;
    $response['message'] = "Invalid request.";
    echo json_encode($response);
    exit();
}
?>

