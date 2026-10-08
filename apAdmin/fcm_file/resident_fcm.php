<?php
error_reporting(0);
require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class firebase_resident
{
    
    function  noti($menuClick,$image,$society_id,$registrationIds, $title, $body, $click_action)
    {
        
        $title = html_entity_decode($title);
        $body = html_entity_decode($body);

        // Define the device tokens
        if (is_array($registrationIds)) {
            $registrationIds=$registrationIds;
            $registrationIds = array_unique($registrationIds);
             $registrationIds = array_values($registrationIds);  
        } else {
            $registrationIds = array($registrationIds);
        }

        if ($image=='') {
            $image= "";
        }

        if (is_array($click_action) &&  $click_action['userProfile']!='') {
            $small_icon=$click_action['userProfile'];
        }elseif ($base_url != '') {
            $small_icon = $base_url."img/logo.png";
        } else {
            $small_icon =$master_url."img/logo.png";
        }
       
        // Initialize the Firebase SDK
        $factory = (new Factory)->withServiceAccount(__DIR__.'/your-service-account.json');
        $messaging = $factory->createMessaging();

         $msg_id = date("YmdHis");
         $msg_id = (int)$msg_id;

        if ($title=='sos') {
            $sound = "beep_beep_beep.caf";
        } else if (is_array($activity) && $activity['title']=='newVisitorApprove' || is_array($activity) && $activity['title']=='newChildApprove') {
            $sound = "Doorbell.caf";
        } else {
            $sound = "just-saying.caf";
        }

        // Create the notification payload
        $notification = Notification::create($title, $body);
        $data = [
                    'menuClick' => $menuClick,
                    'image' => $image,
                    'small_icon' => $small_icon,
                    'body'     => $body,
                    'click_action'     => $click_action,
                    'title'    => $title,
                    'society_id' => $society_id,
                    'sound'=>$sound,
                    'msg_id'=>"$msg_id",
                ]; // Optional data payload


        // Create the message
        $message = CloudMessage::new()
            // ->withNotification($notification)
            ->withData($data);

        // Split device tokens into batches of 500
        $chunks = array_chunk($registrationIds, 500);

        foreach ($chunks as $chunk) {
            try {
                $report = $messaging->sendMulticast($message, $chunk);
                
                // echo "Sent " . $report->successes()->count() . " messages successfully.\n";
                // echo "Failed to send " . $report->failures()->count() . " messages.\n";
                
                foreach ($report->failures()->getItems() as $failure) {
                    // echo $failure->error()->getMessage() . "\n";
                }
            } catch (\Kreait\Firebase\Exception\MessagingException $e) {
                // echo "Error sending notification: " . $e->getMessage();
            }
        }
    }
    function  noti_ios($menuClick,$image,$society_id,$registrationIds, $title, $body, $click_action)
    {
        $title = html_entity_decode($title);
        $body = html_entity_decode($body);

        // Define the device tokens
        if (is_array($registrationIds)) {
            $registrationIds=$registrationIds;
            $registrationIds = array_unique($registrationIds);
             $registrationIds = array_values($registrationIds);  
        } else {
            $registrationIds = array($registrationIds);
        }

        if ($image=='') {
            $image= "";
        }

        if (is_array($click_action) &&  $click_action['userProfile']!='') {
            $small_icon=$click_action['userProfile'];
        }elseif ($base_url != '') {
            $small_icon = $base_url."img/logo.png";
        } else {
            $small_icon = $master_url."img/logo.png";
        }
       
        // Initialize the Firebase SDK
        $factory = (new Factory)->withServiceAccount(__DIR__.'/your-service-account.json');
        $messaging = $factory->createMessaging();

         $msg_id = date("YmdHis");
         $msg_id = (int)$msg_id;

        if ($title=='sos') {
            $sound = "beep_beep_beep.caf";
        } else if (is_array($activity) && $activity['title']=='newVisitorApprove' || is_array($activity) && $activity['title']=='newChildApprove') {
            $sound = "Doorbell.caf";
        } else {
            $sound = "just-saying.caf";
        }

        // Create the notification payload
        $notification = Notification::create($title, $body);
        $data = [
                    'menuClick' => $menuClick,
                    'image' => $image,
                    'small_icon' => $small_icon,
                    'body'     => $body,
                    'click_action'     => $click_action,
                    'title'    => $title,
                    'society_id' => $society_id,
                    'sound'=>$sound,
                    'msg_id'=>"$msg_id",
                ]; // Optional data payload


        // Create the message
        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData($data)
            ->withAndroidConfig([
                'notification' => [
                    'sound' => $sound, // For Android
                ],
            ])
            ->withApnsConfig([
                'payload' => [
                    'aps' => [
                        'sound' => $sound, // For iOS, include file extension
                    ],
                ],
            ]);

        // Split device tokens into batches of 500
        $chunks = array_chunk($registrationIds, 500);

        foreach ($chunks as $chunk) {
            try {
                $report = $messaging->sendMulticast($message, $chunk);
                
                // echo "Sent " . $report->successes()->count() . " messages successfully.\n";
                // echo "Failed to send " . $report->failures()->count() . " messages.\n";
                
                foreach ($report->failures()->getItems() as $failure) {
                    // echo $failure->error()->getMessage() . "\n";
                }
            } catch (\Kreait\Firebase\Exception\MessagingException $e) {
                // echo "Error sending notification: " . $e->getMessage();
            }
        }

    }
}
