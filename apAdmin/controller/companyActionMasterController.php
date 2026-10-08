<?php
include '../common/objectController.php';

if (isset($_POST) && !empty($_POST)) {
    extract($_POST);

    $access_by_role_csv = '';
    if (isset($_POST['access_by_role']) && is_array($_POST['access_by_role'])) {
        $roleIds = array_values(array_unique(array_filter(array_map('intval', $_POST['access_by_role']))));
        $access_by_role_csv = !empty($roleIds) ? implode(',', $roleIds) : '';
    }

    $access_by_user_csv = '';
    if (isset($_POST['access_by_user']) && is_array($_POST['access_by_user'])) {
        $userIds = array_values(array_unique(array_filter(array_map('intval', $_POST['access_by_user']))));
        $access_by_user_csv = !empty($userIds) ? implode(',', $userIds) : '';
    }

    if (isset($_POST['addCompanyAction']) && $_POST['addCompanyAction'] == "addCompanyAction") {
        $action = trim((string) ($action ?? ''));
        $value = trim((string) ($value ?? ''));

        if ($action === '' || $value === '') {
            $_SESSION['msg1'] = "Action and Value are required";
            header("Location: ../companyActionsAccess");
            exit;
        }

        $valueEsc = $d->escapeSqlString($value);
        $existing = $d->select("company_action_master", "`value`='$valueEsc'");
        if ($existing && mysqli_num_rows($existing) > 0) {
            $_SESSION['msg1'] = "Value already exists";
            header("Location: ../companyActionsAccess");
            exit;
        }

        $m->set_data('action', test_input($action));
        $m->set_data('value', test_input($value));
        $m->set_data('access_by_role', $access_by_role_csv);
        $m->set_data('access_by_user', $access_by_user_csv);

        $add_data = [
            'action' => $m->get_data('action'),
            'value' => $m->get_data('value'),
            'access_by_role' => $m->get_data('access_by_role'),
            'access_by_user' => $m->get_data('access_by_user'),
        ];

        $q = $d->insert("company_action_master", $add_data);

        if ($q === TRUE) {
            $_SESSION['msg'] = "Company Action Added Successfully";
            $d->insert_log($society_id, $bms_admin_id, $created_by, "Company Action $action Added");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong";
        }

        header("Location: ../companyActionsAccess");
        exit;

    } elseif (isset($_POST['editCompanyAction']) && $_POST['editCompanyAction'] == "editCompanyAction") {
        $action_id = $d->sanitizeActionIdAsInt($action_id ?? ($_POST['action_id'] ?? 0));
        $action = trim((string) ($action ?? ''));
        $value = trim((string) ($value ?? ''));

        if ($action_id <= 0 || $action === '' || $value === '') {
            $_SESSION['msg1'] = "Invalid data";
            header("Location: ../companyActionsAccess");
            exit;
        }

        $valueEsc = $d->escapeSqlString($value);
        $existing = $d->select("company_action_master", "`value`='$valueEsc' AND action_id!='$action_id'");
        if ($existing && mysqli_num_rows($existing) > 0) {
            $_SESSION['msg1'] = "Value already exists";
            header("Location: ../companyActionsAccess");
            exit;
        }

        $m->set_data('action', test_input($action));
        $m->set_data('value', test_input($value));
        $m->set_data('access_by_role', $access_by_role_csv);
        $m->set_data('access_by_user', $access_by_user_csv);

        $edit_data = [
            'action' => $m->get_data('action'),
            'value' => $m->get_data('value'),
            'access_by_role' => $m->get_data('access_by_role'),
            'access_by_user' => $m->get_data('access_by_user'),
        ];

        $q = $d->update("company_action_master", $edit_data, "action_id='$action_id'");

        if ($q === TRUE) {
            $_SESSION['msg'] = "Company Action Updated Successfully";
            $d->insert_log($society_id, $bms_admin_id, $created_by, "Company Action $action Updated");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong";
        }

        header("Location: ../companyActionsAccess");
        exit;

    } elseif (isset($_POST['deleteCompanyAction']) && $_POST['deleteCompanyAction'] == "deleteCompanyAction") {
        $action_id = $d->sanitizeActionIdAsInt($_POST['action_id'] ?? ($action_id ?? 0));
        $action = $action ?? '';

        $q = $d->delete("company_action_master", "action_id='$action_id'");

        if ($q === TRUE) {
            $_SESSION['msg'] = "Company Action Deleted Successfully";
            $d->insert_log($society_id, $bms_admin_id, $created_by, "Company Action $action Deleted");
        } else {
            $_SESSION['msg1'] = "Something Went Wrong";
        }

        header("Location: ../companyActionsAccess");
        exit;

    } else {
        $_SESSION['msg1'] = "Invalid Action";
        header("Location: ../companyActionsAccess");
        exit;
    }

} else {
    $_SESSION['msg1'] = "Invalid Request Method";
    header("Location: ../companyActionsAccess");
    exit;
}

?>
