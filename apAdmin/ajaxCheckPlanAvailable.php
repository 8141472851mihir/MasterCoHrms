<?php

include_once 'common/object.php';
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_value = $_POST['plan_value'] ?? '';
    $eid = $_POST['editId'] ?? '';

    if (!empty($plan_value)) {
        $where = "plan_value = '$plan_value'";
        
        if (!empty($eid)) {
            $where .= " AND plan_id != '$eid'";
        }

        $duplicateCheck = $d->select("manage_plan", $where);

        if (mysqli_num_rows($duplicateCheck) > 0) {
            echo json_encode([
                'status' => false,
                'message' => "Plan value already exists."
            ]);
            exit();
        } else {
            echo json_encode([
                'status' => true,
                'message' => "Plan value is unique."
            ]);
            exit();
        }
    } else {
        echo json_encode([
            'status' => false,
            'message' => "Plan value is required."
        ]);
        exit();
    }
} else {
    echo json_encode([
        'status' => false,
        'message' => "Invalid request method."
    ]);
    exit();
}



