<?php
require_once 'vendor/autoload.php';
$q = $d->select("email_configuration", "");
$data = mysqli_fetch_array($q);
extract($data);
$client_id = $data['client_id'];
$subject = $subject??"";
$body = $message??"";
$cc=$cc??[];
$bcc=$bcc??[];
$senderEmail = $sender_email_id;
$sender_name = $sender_name;

$attachmentPath = isset($attachments) ? $attachments : null;
// echo $attachmentPath;exit;
use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
if(!function_exists('buildMimeMessage')){
    function buildMimeMessage($senderEmail, $sender_name, $to, $subject, $body, $cc = null, $attachmentPath = null,$bcc=null) {
        $boundary = "boundary_" . md5(time());
        $subject = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n");

        $headers = "From: $sender_name <$senderEmail>\r\n";
        $headers .= "Reply-To: $senderEmail\r\n";
        $headers .= "Subject: $subject\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

        if (!empty($to)) {
            $headers .= "To: " . (is_array($to) ? implode(", ", $to) : $to) . "\r\n";
        }
        if (!empty($cc)) {
            $headers .= "CC: " . (is_array($cc) ? implode(", ", $cc) : $cc) . "\r\n";
        }
        if (!empty($bcc)) {
            $headers .= "bcc: " . (is_array($bcc) ? implode(", ", $bcc) : $bcc) . "\r\n";
        }

        $bodyContent = "--$boundary\r\n";
        $bodyContent .= "Content-Type: text/html; charset=UTF-8\r\n";
        $bodyContent .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $bodyContent .= $body . "\r\n\r\n";

        $attachmentPath = is_array($attachmentPath) ? $attachmentPath : [$attachmentPath];
        foreach ($attachmentPath as $path) {
            if (!empty($path) && file_exists($path)) {
                $fileName = basename($path);
                $fileData = file_get_contents($path);
                $fileEncoded = chunk_split(base64_encode($fileData));

                $bodyContent .= "--$boundary\r\n";
                $bodyContent .= "Content-Type: application/octet-stream; name=\"$fileName\"\r\n";
                $bodyContent .= "Content-Transfer-Encoding: base64\r\n";
                $bodyContent .= "Content-Disposition: attachment; filename=\"$fileName\"\r\n\r\n";
                $bodyContent .= $fileEncoded . "\r\n\r\n";
            }
        }

        $bodyContent .= "--$boundary--";

        $mimeMessage = $headers . "\r\n\r\n" . $bodyContent;
        $encodedMessage = base64_encode($mimeMessage);
        $encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);
        // echo $encodedMessage;exit;
        return $encodedMessage;
    }
}

if(!function_exists('getAccessToken')){
    function getAccessToken($clientId, $clientSecret, $refreshToken, $redirect_url) {
        $client = new Client();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setAccessType('offline');
        $client->addScope(Gmail::GMAIL_SEND);
        $client->setRedirectUri("$redirect_url");
        $client->fetchAccessTokenWithRefreshToken($refreshToken);
        return $client->getAccessToken()['access_token'];
    }
}
if (!function_exists('sendEmail')) {
    function sendEmail($accessToken, $senderEmail, $sender_name, $to, $subject, $body, $cc, $attachmentPath,$bcc) {
        $client = new Google\Client();
        $client->setAccessToken($accessToken);
        $gmailService = new Google\Service\Gmail($client);
        $message = new Google\Service\Gmail\Message();

        $mimeMessage = buildMimeMessage($senderEmail, $sender_name, $to, $subject, $body, $cc, $attachmentPath,$bcc);
        $message->setRaw($mimeMessage);

        try {
            $gmailService->users_messages->send('me', $message);
            // echo "Email sent successfully using Gmail API!";
        } catch (Exception $e) {
            // echo 'Error sending email: ' . $e->getMessage();
        }
    }

}
if (!empty($client_id)) {
    try {
        $accessToken = getAccessToken($client_id, $client_secret, $refresh_token, $redirect_url);
        sendEmail($accessToken, $senderEmail, $sender_name, $to, $subject, $body, $cc, $attachmentPath, $bcc);
    } catch (Exception $e) {
        // echo "Gmail API failed: " . $e->getMessage(); 
    }
}else{
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $email_smtp;
        $mail->SMTPAuth   = true;
        $mail->Username   = $senderEmail;
        $mail->Password   = $email_password; // App password
        $mail->SMTPSecure = $smtp_type;
        $mail->Port       = $email_port;
        // $mail->SMTPDebug = 2;
        // $mail->Debugoutput = 'html';
        $mail->setFrom($senderEmail, $sender_name);
        $to = is_array($to) ? $to : [$to];
        $cc = is_array($cc) ? $cc : [$cc];
        $bcc = is_array($bcc) ? $bcc : [$bcc];
        foreach ($to as $addr) {
            $addr = trim($addr);
            if (!empty($addr)) {
                $mail->addAddress($addr);
            }
        }

        $cc = is_array($cc) ? $cc : explode(',', $cc);
        foreach ($cc as $addr) {
            $addr = trim($addr);
            if (!empty($addr)) {
                $mail->addCC($addr);
            }
        }

        $bcc = is_array($bcc) ? $bcc : explode(',', $bcc);
        foreach ($bcc as $addr) {
            $addr = trim($addr);
            if (!empty($addr)) {
                $mail->addBCC($addr);
            }
}
        $paths = is_array($attachmentPath) ? $attachmentPath : [$attachmentPath];
        foreach ($paths as $path) {
            if (!empty($path) && file_exists($path)) {
                $mail->addAttachment($path);
            }
        }

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        // echo "Email sent successfully using PHPMailer SMTP!";
    } catch (Exception $ex) {
        // echo "SMTP Email sending failed: " . $ex->getMessage();
    }
}