<?php
  
include_once 'common/object.php';
error_reporting(0);
  $base_url=$m->base_url();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['batch_name'])) {
        $batch_name = $d->escapeSqlString(trim($_POST['batch_name']));
        $where='';
        $edit_batch_id = $d->sanitizeActionIdAsInt($_POST['edit_batch_id'] ?? 0);
        if ($edit_batch_id > 0) {
             $where="AND batch_id!='$edit_batch_id'";
        }
        // Query to check if the batch name already exists in the database
        $existing_batch = $d->select("training_batch_master", "batch_name = '$batch_name' $where");
        
        // Check if any row is returned with the same batch name
        if (mysqli_num_rows($existing_batch) > 0) {
            // Batch name exists, return a specific error message
            echo json_encode(['valid' => false, 'message' => 'This batch name already exists. Please choose a different one.']);
        } else {
            // Batch name does not exist, return true
            echo json_encode(['valid' => true]);
        }
    }
} 
?>
