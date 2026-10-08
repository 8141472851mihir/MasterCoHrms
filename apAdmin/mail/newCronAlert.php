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
        <div class='email-box'>

            <!-- Header -->
            <div class='email-header'>
                <a href='https://www.my-company.app/'>
                    <img src='".$m->base_url()."img/logo.png' alt='".$d->app_name()." Logo'>
                </a>
            </div>
            <!-- Content -->
            <div class='content'>
                <h2>Hello <span>$admin_name</span>!</h2>
                <p>A new <strong>$cron_category</strong> cron has been created automatically for your system.</p>
                <p>Please review the details below and add the cron URL to your server’s scheduled jobs (CRON TAB):</p>
                <div class='credentials'>
                    <p><strong>Cron Name:</strong> $new_cron_name</p>
                    <p><strong>Cron Category:</strong> $cron_category</p>
                    <p><strong>Cron ID:</strong> $cron_id</p>
                </div>
                <p><strong>New Cron URL:</strong></p>
                <div class='credentials'>
                    <p>
                        <a href='$cron_full_url' style='color:#007bff;'>$cron_full_url</a>
                    </p>
                </div>
                <p>Please ensure this URL is configured in the server cron scheduler to keep jobs running.</p>
                <a href='$cron_full_url' class='btn'>Run Cron Now</a>
            </div>
            <!-- Footer -->
            <div class='email-footer'>
                <p>
                    &#169; $cy 
                    <a href='https://www.my-company.app/' target='_blank'>".$d->app_name()."</a>. 
                    All rights reserved.
                </p>
            </div>

        </div>
    </div>
</body>
</html>";
?>
