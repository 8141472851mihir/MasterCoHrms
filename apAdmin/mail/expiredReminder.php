<?php
$cy = date("Y");
$message = "
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
    <title>Company Creation Request</title>
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
            text-align: left;
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
            .container { width: 100%; padding-left: 0%; } 
            .content p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class='container'>
        <!-- Header -->
        <div class='email-box'>
            <div class='email-header'>
                <a href='https://my-company.app/'>
                    <img src='" . $m->base_url() . "img/logo.png'>
                </a>
            </div>

            <div class='email-content'>
                <p><b>Dear $secretary_name,</b></p>
                <p>We hope this message finds you well. This is a courteous reminder that your subscription for <b>$societyName ($fullSocietyId)</b> with us has expired as <b>$expired_date</b>. In accordance with our data retention policy, $remainingTimeMessage After this date, we will no longer be able to retrieve any of your stored data.</p>
                <p>If you wish to retain your data or renew your subscription, $stepsDeadlineMessage.</p>
                <p>Thank you for your prompt attention to this matter.</p>
                <p>Warm regards,</p>
                <p><b>The " . $d->app_name() . " Team</b></p>
            </div>

            <!-- email- -->
            <div class='email-footer'>
                &#169; $cy 
                <a href='https://www.my-company.app/' target='_blank'>" . $d->app_name() . "</a>. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>";
