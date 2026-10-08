<?php
include_once 'lib.php';

if (isset($_POST) && !empty($_POST))
{
    $response = array();
    extract(array_map("test_input", $_POST));
    if ($_POST['addDemoRequest'] == "addDemoRequest")
    {
        $m->set_data('contact_names', $contact_names);
        $m->set_data('email', $contact_email);
        $m->set_data('mobile_number', $mobile_number);
        $m->set_data('message', $contact_message);

        $value_array = array(
            'name' => $m->get_data('contact_names'),
            'email' => $m->get_data('email'),
            'mobile_number' => $m->get_data('mobile_number'),
            'message' => $m->get_data('message'),
            'added_date' => date("Y-m-d H:i:s")
        );
        $insert = $d->insert("demo_request_from_chpl", $value_array);
        if ($insert)
        {
            $response["message"] = "We received your message and you will hear from us soon. Thank You!";
            $response["status"] = "200";
            echo json_encode($response);
            exit();
        }
        else
        {
            $response["message"] = "Something went wrong!";
            $response["status"] = "201";
            echo json_encode($response);
            exit();
        }
    }
    else
    {
        $response["message"] = "wrong tag";
        $response["status"] = "201";
        echo json_encode($response);exit;
    }
}
?>