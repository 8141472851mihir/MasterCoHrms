<?php


error_reporting(0);



class firebase_gaurd
{



    function  noti($registrationIds, $title, $body, $activity,$m)
    {

        if (is_array($registrationIds)) {
            $registrationIds=$registrationIds;
        } else {
            $registrationIds = array($registrationIds);
        }

        if (!class_exists('EnvLoader')) {
            require_once dirname(__DIR__, 2) . '/EnvLoader.php';
        }
        EnvLoader::load(dirname(__DIR__, 2) . '/.env');
        if (!defined('API_ACCESS_KEY')) {
            define('API_ACCESS_KEY', (string) EnvLoader::get('FCM_SERVER_KEY'));
        }


        #prep the bundle

        // $msg = array(


        //     'click_action'     => $activity,


        //     'icon'    => 'myicon', /*Default Icon*/

        //     '	sound' => 'mySound' /*Default sound*/

        // );
        $data = array(

            'url'     => $url,

            "image" => "".$m->base_url()."img/logo.png",

            'body'     => $body,

            'click_action'     => $activity,

            'title'    => $title,

            'sound'=>'default',


        );



        $fields = array(

            // 'to'        => $registrationIds,
             'registration_ids' =>  $registrationIds,

            // 'notification'    => $data,

            'data'    => $data

        );



        $headers = array(

            'Authorization: key=' . API_ACCESS_KEY,

            'Content-Type: application/json'

        );

        #Send Reponse To FireBase Server	

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);

        curl_close($ch);



        $result = json_decode($result, true);

        $re = $result['success'];


        if ($re == 1) {


            // header("location:$url");

            return true;
        } else {


            // header("location:$url");

            return false;
        }
    }

}



