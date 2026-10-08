<?php 
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $d->support_url() . 'taskController.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('feedback_reply' => 'feedback_reply','ticket_id' => $ticket_id,'message' => $reply_message,'status' => $feedback_status,'mobile_no' => $user_mobile_no),
    CURLOPT_HTTPHEADER => array(),
));
$response = curl_exec($curl);
curl_close($curl);
