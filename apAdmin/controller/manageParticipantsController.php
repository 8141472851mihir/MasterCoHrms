<?php
include '../common/objectController.php';


if (isset($_POST['fetchParticipant']) && isset($_POST['participants_type_id'])) {
    $participants_type_id = $_POST['participants_type_id'];

    $q = $d->selectRow("participants_type_id, participant_name, status", "training_participants_type", "participants_type_id='$participants_type_id'");
    $data = mysqli_fetch_assoc($q);

    echo json_encode($data);
    exit();
}

if (isset($_POST['saveParticipant'])) {
    $participant_name = trim($_POST['participant_name']);

    if (empty($participant_name)) {
        $_SESSION['msg'] = "Participant name cannot be empty!";
        header("Location: ../manageParticipants");
        exit();
    }

    $m->set_data('participant_name', $participant_name);

    $data = array(
        'participant_name' => $m->get_data('participant_name')
    );

    if (!empty($_POST['participants_type_id'])) {
        $participants_type_id = $_POST['participants_type_id'];
        $query = $d->update("training_participants_type", $data, "participants_type_id='$participants_type_id'");
        $message = "Participant updated successfully";
    } else {
        $query = $d->insert("training_participants_type", $data);
        $message = "Participant added successfully";
    }

    $_SESSION['msg'] = $query ? $message : "Something went wrong!";
    
    header("Location: ../manageParticipants");
    exit();
}


?>
