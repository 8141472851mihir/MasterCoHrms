<?php 
$cy = date("Y");
$message = "
<html>
<head>
  <meta http-equiv='content-type' content='text/html; charset=windows-1252'>
  <style>
    body { margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
         }
     .email-box {
        max-width: 900px;
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
    .container {
     width: 80%; 
     padding-left: 10%; 
        }
    @media only screen and (max-device-width: 480px) { 
        .container { width: 100%; padding-left: 0%; } 
    }
    .email-content { 
        padding: 10px;font-size: 18px;
         border-radius: 10px;
          }
  </style>
</head>
<body>
  <div class='container'>
    <div class='email-box'>
        <div class='email-header'>
          <a href='https://my-company.app/'><img src='".$m->base_url()."img/logo.png' alt='".$d->app_name()." Logo' style='height: 100px;'></a>
        </div>
        <div class='email-content'>
            <h4 style='text-align:center;'>Dear <span style='color: red;'>$user_name</span>!</h4>
            <p>I hope this message finds you well. We wanted to inform you that a support ticket has been opened in response to the issue you reported. Your satisfaction is our top priority, and we are committed to resolving your concerns as quickly as possible.
            </p>
            <p>Here are the details of your support ticket:</p>
            <ul>
                <li>Ticket Number: $ticketNumber</li>
                <li>Date Created: $created_date</li>
                <li>Issue Description: $feedback_msg</li>
            </ul>
            <p>Our dedicated support team is already reviewing your ticket and will work diligently to address your concerns.</p>
            <p>Please feel free to reach out to our support team if you have any additional information or questions related to your support ticket.</p>
            <p>Thank you for choosing ".$d->app_name().". We value your business and appreciate the opportunity to assist you. Rest assured, we are committed to delivering a timely and effective solution to your issue.</p>
            <p>Thank you for choosing ".$d->app_name().".</p>
        </div>
        <div style='padding: 20px 0;'>
          <p style='text-align:center;'>Thank You,<br/>The ".$d->app_name()." Support Team</p>
        </div>
        <div class='email-footer'>
          <p>&#169; $cy <a href='https://www.my-company.app/' target='_blank'>".$d->app_name()."</a>. All rights reserved.</p>
        </div>
    </div>
  </div>
</body>
</html>";
?>
