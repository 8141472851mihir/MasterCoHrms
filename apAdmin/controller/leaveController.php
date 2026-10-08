<?php
include '../common/objectController.php';

if (isset($_POST) && !empty($_POST)) {
    extract($_POST);

    if (isset($_POST['leaveTypeNameCheck'])) {
        $leave_type_name_check = $d->escapeSqlString(trim($_POST['leave_type_name_check'] ?? ''));
        $edit_leave_type_id = $d->sanitizeActionIdAsInt($_POST['edit_leave_type_id'] ?? 0);
        $country_id = $d->sanitizeActionIdAsInt($_POST['country_id'] ?? 0);

        $where = "leave_type_name = '$leave_type_name_check'";
        if ($country_id > 0) {
            $where .= " AND country_id = '$country_id'";
        }
        if ($edit_leave_type_id > 0) {
            $where .= " AND leave_type_id != '$edit_leave_type_id'";
        }

        $existing = $d->select("leave_types_master", $where);
        echo json_encode(['valid' => mysqli_num_rows($existing) === 0]);
        exit;
    } elseif (isset($_POST['addLeaveType']) && $_POST['addLeaveType'] == "addLeaveType") {
        $m->set_data('country_id', test_input($country_id));
        $m->set_data('leave_type_name', test_input($leave_type_name));
        $m->set_data('leave_type_short_name', test_input($leave_type_short_name)); // <-- Add this line
        $m->set_data('leave_for', test_input($leave_for));
        $m->set_data('leave_apply_on_date', test_input($leave_apply_on_date));
        $m->set_data('no_of_leaves', test_input($no_of_leaves));
        $m->set_data('sandwich_leave_applicable', test_input($sandwich_leave_applicable));
        $m->set_data('added_by', $bms_admin_id);
        $m->set_data('added_date', date('Y-m-d H:i:s'));

        $add_data = [
            'country_id' => $m->get_data('country_id'),
            'leave_type_name' => $m->get_data('leave_type_name'),
            'leave_type_short_name' => $m->get_data('leave_type_short_name'), // <-- Add this line
            'leave_for' => $m->get_data('leave_for'),
            'leave_apply_on_date' => $m->get_data('leave_apply_on_date'),
            'no_of_leaves' => $m->get_data('no_of_leaves'),
            'sandwich_leave_applicable' => $m->get_data('sandwich_leave_applicable'),
            'added_by' => $m->get_data('added_by'),
            'added_date' => $m->get_data('added_date'),
        ];

        $q = $d->insert("leave_types_master", $add_data);

        if ($q === TRUE) {
            $_SESSION['msg'] = "Leave Type Added Successfully";
            $d->insert_log($society_id, $bms_admin_id, $created_by, "Leave Type $leave_type_name Added");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong";
        }

        $redirectCountryId = (int)$m->get_data('country_id');
        header("Location: ../leaveTypes?countryId=$redirectCountryId");
        exit;

    } elseif (isset($_POST['editLeaveType']) && $_POST['editLeaveType'] == "editLeaveType") {
        $m->set_data('country_id', test_input($country_id));
        $m->set_data('leave_type_name', test_input($leave_type_name));
        $m->set_data('leave_type_short_name', test_input($leave_type_short_name)); // <-- Add this line
        $m->set_data('leave_for', test_input($leave_for));
        $m->set_data('leave_apply_on_date', test_input($leave_apply_on_date));
        $m->set_data('no_of_leaves', test_input($no_of_leaves));
        $m->set_data('sandwich_leave_applicable', test_input($sandwich_leave_applicable));
        $m->set_data('modify_by', $bms_admin_id);
        $m->set_data('modify_date', date('Y-m-d H:i:s'));

        $edit_data = [
            'country_id' => $m->get_data('country_id'),
            'leave_type_name' => $m->get_data('leave_type_name'),
            'leave_type_short_name' => $m->get_data('leave_type_short_name'), // <-- Add this line
            'leave_for' => $m->get_data('leave_for'),
            'leave_apply_on_date' => $m->get_data('leave_apply_on_date'),
            'no_of_leaves' => $m->get_data('no_of_leaves'),
            'sandwich_leave_applicable' => $m->get_data('sandwich_leave_applicable'),
            'modify_by' => $m->get_data('modify_by'),
            'modify_date' => $m->get_data('modify_date'),
        ];

        $leave_type_id = $d->sanitizeActionIdAsInt($leave_type_id ?? ($_POST['leave_type_id'] ?? 0));
        $q = $d->update("leave_types_master", $edit_data, "leave_type_id=$leave_type_id");

        if ($q === TRUE) {
            $_SESSION['msg'] = "Leave Type Updated Successfully";
            $d->insert_log($society_id, $bms_admin_id, $created_by, "Leave Type $leave_type_name Updated");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong";
        }

        $redirectCountryId = (int)$m->get_data('country_id');
        header("Location: ../leaveTypes?countryId=$redirectCountryId");
        exit;
    } elseif (isset($_POST['deleteLeaveType']) && $_POST['deleteLeaveType'] == "deleteLeaveType") {
        $leave_type_id = $d->sanitizeActionIdAsInt($_POST['leave_type_id'] ?? ($leave_type_id ?? 0));
        $leave_type_name = $leave_type_name ?? '';

        $q = $d->delete("leave_types_master", "leave_type_id=$leave_type_id");

        if ($q === TRUE) {
            $_SESSION['msg'] = "Leave Type Deleted Successfully";
            $d->insert_log($society_id, $bms_admin_id, $created_by, "Leave Type $leave_type_name Deleted");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong";
        }

        $redirectCountryId = (isset($country_id) && (int)$country_id > 0) ? (int)$country_id : 101;
        header("Location: ../leaveTypes?countryId=$redirectCountryId");
        exit;
    } else {
        $_SESSION['msg1'] = "Invalid Action";
        header("Location: ../leaveTypes");
        exit;
    }

} else {
    $_SESSION['msg1'] = "Invalid Request Method";
    header("Location: ../leaveTypes");
    exit;
}

?>