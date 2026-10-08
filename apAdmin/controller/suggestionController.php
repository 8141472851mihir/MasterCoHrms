<?php
include '../common/objectController.php';

if (isset($_POST['fetchSuggestion']) && isset($_POST['suggestion_id'])) {
    $suggestion_id = $_POST['suggestion_id'];
    $q = $d->selectRow("suggestion_id, suggestion_name, suggestion_type, suggestion_status", "suggestion_master", "suggestion_id='$suggestion_id'");
    $data = mysqli_fetch_assoc($q);
    echo json_encode($data);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['suggestion_name'])) {
    $suggestion_name = trim(test_input($_POST['suggestion_name']));
    $suggestion_type = isset($_POST['suggestion_type']) ? test_input($_POST['suggestion_type']) : '';
    $suggestion_status = isset($_POST['suggestion_status']) ? test_input($_POST['suggestion_status']) : 0;

    if (empty($suggestion_name)) {
        $_SESSION['msg'] = "Suggestion name cannot be empty!";
        header("Location: ../suggestion");
        exit();
    }

    $m->set_data('suggestion_name', $suggestion_name);
    $m->set_data('suggestion_type', $suggestion_type);
    $m->set_data('suggestion_status', $suggestion_status);

    if (!empty($_POST['suggestion_id'])) {
        // Update case
        $suggestion_id = test_input($_POST['suggestion_id']);
        $m->set_data('updated_by', $bms_admin_id);
        $m->set_data('updated_date', date("Y-m-d H:i:s"));

        $data = [
            'suggestion_name' => $m->get_data('suggestion_name'),
            'suggestion_type' => $m->get_data('suggestion_type'),
            'suggestion_status' => $m->get_data('suggestion_status'),
            'updated_by' => $m->get_data('updated_by'),
            'updated_date' => $m->get_data('updated_date')
        ];

        $query = $d->update("suggestion_master", $data, "suggestion_id='$suggestion_id'");
        $message = "Suggestion updated successfully";
    } else {
        $m->set_data('created_by', $bms_admin_id);
        $m->set_data('created_date', date("Y-m-d H:i:s"));

        $data = [
            'suggestion_name' => $m->get_data('suggestion_name'),
            'suggestion_type' => $m->get_data('suggestion_type'),
            'suggestion_status' => $m->get_data('suggestion_status'),
            'created_by' => $m->get_data('created_by'),
            'created_date' => $m->get_data('created_date')
        ];

        $query = $d->insert("suggestion_master", $data);
        $message = "Suggestion added successfully";
    }

    $_SESSION['msg'] = $query ? $message : "Something went wrong!";
    header("Location: ../suggestion");
    exit();
} else {
    $_SESSION['msg1'] = "Invalid request. Please try again.";
    header("Location: ../welcome");
    exit();
}
?>