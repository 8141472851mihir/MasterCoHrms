<?php
$cy = date("Y");
$message = "
<html>
<head>
    <meta http-equiv='content-type' content='text/html; charset=UTF-8'>
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
        .content {
            padding: 20px;
            text-align: center;
        }
        .content h2 {
            color: #ff4d4d;
        }
        .content p {
            font-size: 16px;
            line-height: 1.5;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            background: red;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            margin: 20px 0;
        }
        .credentials {
            background: #d2d2d2;
            color: #000;
            padding: 15px;
            border-radius: 10px;
            margin: 20px auto;
            text-align: left;
            width: 80%;
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
     <div class='email-box'
        <!-- Header -->
            <div class='email-header'>
                <a href='https://www.my-company.app/'>
                    <img src='".$m->base_url()."img/logo.png' alt='".$d->app_name()." Logo'>
                </a>
            </div>
            <!-- Content -->
            <div class='content'>
                <h2>Hello <span>$admin_name</span>!</h2>
                <p>Welcome to ".$d->app_name()."! We're excited to provide you our <strong>$societyLngName</strong> management service. Your ".$d->app_name()." Master Panel account has been created successfully. Use the credentials below to log in:</p>
                <div class='credentials'>
                    <p>Username: $country_code $admin_mobile</p>
                    <p>Password: $admin_password</p>
                </div>
                <p><strong>Please change your password after logging in.</strong></p>
                <a href='$forgotLink' class='btn'>Login</a>
                <p>If you did not request this, please ignore this email or <a href='mailto:contact@mycompany.app' style='color: #ff4d4d;'>contact support</a>.</p>
                <p>If the button doesn't work, copy and paste this link into your browser:</p>
                <p><a href='$forgotLink' style='color: #fff;'>$forgotLink</a></p>
            </div>

            <!-- Footer -->
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
