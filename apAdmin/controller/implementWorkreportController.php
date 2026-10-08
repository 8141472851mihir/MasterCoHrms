<?php
include '../common/objectController.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (isset($_POST) && !empty($_POST)) {
    if (isset($_POST['admin_id']) && !empty($_POST['admin_id'])) {
        $admin_id = $_POST['admin_id'];
    } else {
        $result = $d->select("bms_admin_master", "1 LIMIT 1");
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $admin_id = $row['admin_id'];
        } else {
            $_SESSION['msg1'] = "No admin found in the system.";
            header("location:../implementWorkreport");
            exit();
        }
    }


    if (isset($_POST['workReport']) && $_POST['workReport'] == "workReport") {
        $report_date = $_POST['report_date'];
        $report_type = $_POST['report_type'];
        $no_of_call = $_POST['no_of_call'];
        $no_of_linedup = $_POST['no_of_linedup'];
        $company_ids = isset($_POST['society_id']) ? implode(',', $d->sanitizeActionIds($_POST['society_id'])) : '';
        $report_desc = $_POST['report_desc'];


        $where = "admin_id = '$admin_id' AND report_date = '$report_date' AND report_type = '$report_type'";
        $duplicateCheck = $d->select("implementation_work_report", $where);

        if (mysqli_num_rows($duplicateCheck) > 0) {
            $_SESSION['msg1'] = "You have already added a report of this type for the selected date.";
            //header("location:../implementWorkreport");
            exit();
        }

        // Set values in the model
        $m->set_data('report_date', $report_date);
        $m->set_data('report_type', $report_type);
        $m->set_data('no_of_call', $no_of_call);
        $m->set_data('no_of_linedup', $no_of_linedup);
        $m->set_data('company_ids', $company_ids);
        $m->set_data('report_desc', $report_desc);

        $data = array(
            'report_date' => $m->get_data('report_date'),
            'report_type' => $m->get_data('report_type'),
            'admin_id' => $admin_id,
            'no_of_call' => $m->get_data('no_of_call'),
            'no_of_linedup' => $m->get_data('no_of_linedup'),
            'company_ids' => $m->get_data('company_ids'),
            'report_desc' => $m->get_data('report_desc'),
            'added_by' => $bms_admin_id,
            'added_date' => date("y-m-d h:i:s"),
        );

        $insert_query = $d->insert("implementation_work_report", $data);
        if ($insert_query) {
            $_SESSION['msg'] = "Work report added successfully.";
            header("location:../implementWorkreport");
            exit();
        } else {
            $_SESSION['msg1'] = "Something went wrong.";
            header("location:../implementWorkreport");
            exit();
        }
    }

    // Editing an existing report (workReportEdit)
    else if (isset($_POST['workReport']) && $_POST['workReport'] == 'workReportEdit') {

        $report_date = $_POST['report_date'];
        $no_of_call = $_POST['no_of_call'];
        $no_of_linedup = $_POST['no_of_linedup'];
        $company_ids = isset($_POST['society_id']) ? implode(',', $d->sanitizeActionIds($_POST['society_id'])) : '';
        $report_desc = $_POST['report_desc'];
        $editId = $_POST['editId'];


        $m->set_data('report_date', $report_date);
        $m->set_data('no_of_call', $no_of_call);
        $m->set_data('no_of_linedup', $no_of_linedup);
        $m->set_data('company_ids', $company_ids);
        $m->set_data('report_desc', $report_desc);


        $data = array(
            'report_date' => $m->get_data('report_date'),
            'admin_id' => $admin_id,
            'no_of_call' => $m->get_data('no_of_call'),
            'no_of_linedup' => $m->get_data('no_of_linedup'),
            'company_ids' => $m->get_data('company_ids'),
            'report_desc' => $m->get_data('report_desc'),
            'updated_by' => $bms_admin_id,
            'updated_date' => date("y-m-d h:i:s"),
        );


        $update_query = $d->update("implementation_work_report", $data, "implementation_work_report_id='$editId'");
        if ($update_query) {
            $_SESSION['msg'] = "Work report updated successfully.";
            header("location:../implementWorkreport");
            exit();
        } else {
            $_SESSION['msg1'] = "Something went wrong.";
            header("location:../implementWorkreport");
            exit();
        }
    } else {
        $_SESSION['msg1'] = "Something went wrong.";
        header("location:../implementWorkreport");
        exit();
    }
}
