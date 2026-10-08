<?php
$cy = date("Y");
$message = "
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
    <title>Reset Password</title>
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
            margin: 15px 0;
        }
        .reset-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #22BC66;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin: 20px 0;
        }
        .reset-button:hover {
            background-color: #1e9b57;
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
            <!-- Email Content -->
            <div class='email-content'>
                <h1>Hi <span style='color: red;'>$admin_name</span>,</h1>
                <p>
                    You recently requested to reset your password for your ".$d->app_name()." Master Panel account. 
                    Use the button below to reset it. <br><br>
                    <strong>This password reset is only valid for the next 24 hours.</strong>
                </p>
                <a class='reset-button' href='$forgotLink'>Reset Password</a>
                <p>
                    If you did not request a password reset, please ignore this email or 
                    <a href='mailto:contact@my-company.app' style='color: #007BFF;'>contact support</a> 
                    if you have any questions.
                </p>
                <p>If you’re having trouble with the button above, copy and paste the URL below into your web browser:</p>
                <p style='font-size: 12px; color: #333;'>$forgotLink</p>
            </div>
            <!-- Email Footer -->
            <div class='email-footer'>
                <p>
                    &#169; $cy 
                    <a href='https://www.my-company.app/' target='_blank'>".$d->app_name()."</a>. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>";
?>
