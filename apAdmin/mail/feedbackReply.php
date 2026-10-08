<?php
$cy = date("Y");
$message = "
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
    <title>Reply Email</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            background-color: #f4f4f4;
            padding: 20px 0;
        }
        .email-box {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }
        .email-header {
            background-color: #e3e8f1;
            padding: 20px;
            text-align: center;
        }
        .email-header img {
            height: 80px;
            width: auto;
        }
        .email-content {
            padding: 20px;
            text-align: center;
        }
        .email-content h1 {
            color: #333;
            font-size: 24px;
        }
        .email-content p {
            font-size: 16px;
            color: #555;
            margin: 10px 0;
        }
        .email-footer {
            background-color: #e3e8f1;
            padding: 15px;
            text-align: center;
            font-size: 14px;
            color: #555;
        }
        .email-footer a {
            color: #007BFF;
            text-decoration: none;
        }
        @media only screen and (max-width: 480px) {
            .email-content h1 {
                font-size: 20px;
            }
            .email-content p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='email-box'>
            <!-- Email Header -->
            <div class='email-header'>
                <a href='$base_url'>
                    <img src='".$base_url."img/logo.png' alt='".$d->app_name()." Logo'>
                </a>
            </div>
            <div class='email-content'>
             <h2 style='text-align:center;color:#333;'>Hello <span style='color: red;'>$user_name</span> !</h2>

               <div style='padding:20px;padding-top:0px'>";
                if(isset($feedback_msg) && $feedback_msg!=''){
                    $message .=  "<p style='padding:3px; font-size: 18px;text-align: center;font-family: Poppins '>$feedback_msg</p>";
                }
                if(isset($feedback_msg2) && $feedback_msg2!=''){
                    $message .=  "<p style='padding:3px; font-size: 18px;text-align: center;font-family: Poppins '>$feedback_msg2</p>";
                }
                if(isset($reply) && $reply!=''){
                    $message .= "<p style='padding:3px; font-size: 18px;text-align: center;font-family: Poppins '>Reply : $reply</p>
                    <div style='padding:10px;'>
                    </div>";
                }
                if(!empty($attachment)){
                    $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                    if($extension == "png" || $extension == "jpeg" || $extension == "jpg" || $extension == "JPEG" || $extension == "JPG" || $extension == "PNG"){
                        $message .= "<p style='text-align:center;'><img src='$attachmentFile' style='height: 120px; padding-top: 12px;padding-bottom: 10px;'></p>";
                    }else{
                        $message .= "<p style='text-align:center;'><a style='color: #fff; background-color: #275F8E;;border-color: #11cdef;font-size: 12px;font-weight: 600;padding: 6px 12px;letter-spacing: 1px;border-radius: 0.25rem;text-transform: uppercase;box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);' href='$attachmentFile' title='Details' name='' class='btn btn-info btn-sm'>View Attachment</a></p>";
                    }
                }
                $message .= " <p style='text-align:center;color:#555;'>Thank You,<br />The ".$d->app_name()." Team </p>
            </div>

            <!-- Email Footer -->
            <div class='email-footer'>
                &#169; $cy 
                <a href='https://www.my-company.app/' target='_blank'>".$d->app_name()."</a>. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>";
?>
