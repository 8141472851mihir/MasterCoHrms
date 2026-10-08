<?php
$cy = date("Y");
$message = "
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
    <title>OTP Verification</title>
</head>
<body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
    <table width='100%' bgcolor='#f4f4f4' cellpadding='0' cellspacing='0' border='0'>
        <tr>
            <td align='center'>
                <table width='600' bgcolor='#ffffff' cellpadding='20' cellspacing='0' border='0' style='border: 1px solid #ddd; border-radius: 10px; overflow: hidden; margin: 20px auto;'>
                    <tr>
                        <td align='center' style='background-color: #e3e8f1;'>
                            <a href='$base_url' style='text-decoration: none;'>
                                <img src='".$base_url."img/logo.png' alt='".$d->app_name()." Logo' style='height: 80px; width: auto;'>
                            </a>
                        </td>
                    </tr>
                    
                    <tr>
                        <td align='center'>
                            <h1 style='color: #333; font-size: 24px;'>Hi <span style='color: red;'>$admin_name</span>,</h1>
                            <p style='font-size: 18px; color: #555;'>
                                Please use the following OTP to login to your ".$d->app_name()." Master Panel account:
                            </p>
                            <p style='font-size: 20px; font-weight: bold; color: #000;'>Your OTP is: <span style='color: #000;'>$msg</span></p>
                        </td>
                    </tr>
                    <tr>
                        <td align='center'>
                            <p style='font-size: 16px; color: #555;'>
                                If you did not request this OTP, please ignore this email or 
                                <a href='mailto:contact@my-company.app' style='color: #007BFF;'>contact support</a>.
                            </p>
                            <p style='font-size: 16px; color: #555;'>
                                Thank you,<br>The ".$d->app_name()." Team
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align='center' style='background-color: #e3e8f1; padding: 15px;'>
                            <p style='font-size: 14px; color: #555; margin: 0;'>&#169; $cy 
                                <a href='https://www.my-company.app/' target='_blank' style='color: #007BFF; text-decoration: none;'>".$d->app_name()."</a>. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>";
?>
