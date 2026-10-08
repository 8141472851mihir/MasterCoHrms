<?php
include '../common/objectController.php';


if (isset($_POST['fetchPriority']) && isset($_POST['priority_id'])) {
    $priority_id = $_POST['priority_id'];
    $q = $d->selectRow("priority_id, priority_name, is_required", "training_module_priority_master", "priority_id='$priority_id'");
    $data = mysqli_fetch_assoc($q);
    echo json_encode($data);
    exit();
}

if (isset($_POST['saveTrainingPriority'])) {
    $priority_name = trim($_POST['priority_name']);
    
    if (empty($priority_name)) {
        $_SESSION['msg'] = "Priority name cannot be empty!";
        header("Location: ../manageTrainingPriority");
        exit();
    }

    $m->set_data('priority_name', $priority_name);

    if (!empty($_POST['priority_id'])) {
        $priority_id = $_POST['priority_id'];
        $data = array(
            'priority_name' => $m->get_data('priority_name') 
        );
        $query = $d->update("training_module_priority_master", $data, "priority_id='$priority_id'");
        $message = "Training priority updated successfully";
    } else {
        $m->set_data('is_required', 1); 
        $data = array(
            'priority_name' => $m->get_data('priority_name'),
            'is_required' => $m->get_data('is_required')
        );
        $query = $d->insert("training_module_priority_master", $data);
        $message = "Training priority added successfully";
    }

    $_SESSION['msg'] = $query ? $message : "Something went wrong!";
    header("Location: ../manageTrainingPriority");
    exit();
}


?>
