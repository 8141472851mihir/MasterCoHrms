<?php
include_once 'lib.php';
// $is_encrypted = 0;
$_POST =  json_decode($d->manage_decryption("1", file_get_contents("php://input")),true);
$compress = resolve_api_compress();
// $_POST =  $d->manage_encryption("1", $_POST);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted,[
        "status" => "2012",
        "message" => $xml->string->invalid_request.""
    ], $compress);
    exit();
}
try {
if (isset($_POST) && !empty($_POST)) {

    if ($key == $keydb) {
        
        $response = array();
        extract(array_map("test_input", $_POST));

        if (isset($addTrainingVisit) && filter_var($society_id, FILTER_VALIDATE_INT) == true && filter_var($employee_id, FILTER_VALIDATE_INT) == true && filter_var($template_id, FILTER_VALIDATE_INT) == true) {

            $m->set_data('society_id', $society_id);
            $m->set_data('template_id', $template_id);
            $m->set_data('template_name', $template_name);
            $m->set_data('employee_id', $employee_id);
            $m->set_data('employee_name', $employee_name);
            $m->set_data('employee_mobile', $employee_mobile);
            $m->set_data('visit_start_datetime', $visit_start_datetime);
            $m->set_data('visit_end_datetime', $visit_end_datetime);
            $m->set_data('visit_remark', $visit_remark);
            $m->set_data('training_visit_data', $training_visit_data);

            $a = array(
            'society_id' => $m->get_data('society_id'),
            'template_id' => $m->get_data('template_id'),
            'template_name' => $m->get_data('template_name'),
            'employee_id' => $m->get_data('employee_id'),
            'employee_name' => $m->get_data('employee_name'),
            'employee_mobile' => $m->get_data('employee_mobile'),
            'visit_start_datetime' => $m->get_data('visit_start_datetime'),
            'visit_end_datetime' => $m->get_data('visit_end_datetime'),
            'visit_remark' => $m->get_data('visit_remark'),
            'training_visit_data' => $m->get_data('training_visit_data'),
            'added_dt' => date('Y-m-d H:i:s'),
            );

            $q = $d->insert("training_visit_master",$a);
            if ($q == true) {
                $response["message"] = "Training Visit Added.";
                $response["status"] = "200";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            } else {
                $response["message"] = "Something went wrong.";
                $response["status"] = "201";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            }
        } else {
            $response["message"] = "wrong tag.";
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit();
        }

    } else {
        $response["message"] = "wrong api key.";
        $response["status"] = "201";
        echo $d->manage_encryption($is_encrypted, $response, $compress);
        exit();
    }

}} catch (Exception $e) {
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
}
echo $d->manage_encryption($is_encrypted, $response, $compress);
exit();
?>
