<?php
$cy = date("Y");
 $message = "
<html>
<link type='text/css' rel='stylesheet' id='dark-mode-general-link'>
<link type='text/css' rel='stylesheet' id='dark-mode-custom-link'>
<style type='text/css' id='dark-mode-custom-style'></style>
    <head>
        <meta http-equiv='content-type' content='text/html; charset=windows-1252'>
    </head>
    <body cz-shortcut-listen='true'>
        <div class='container'>
            <div style='padding: 0px;color: #fff;background:rgba(255, 255, 255, 1); max-width:680px; border:1px solid black; font-size: 20px; border-radius: 10px;overflow: hidden;box-sizing: border-box;margin: auto;'>
                <center style='background-color:#00000033'>
                    <img src=".$base_url.'img/logo.png'." style='height: 100px; width: 100px;padding-top: 12px;padding-bottom: 10px;'>
                </center>
                
                <div style='color: black;'>
                    <table style='width:100%;border-collapse:collapse;border:1px solid;font-size:12px;' border=1>
                        <tr>
                            <th style='padding: 5px;font-size: 15px;'  align='left'>Name</th>
                            <td style='padding: 5px;font-size: 15px;' >$person_name</td>
                        </tr>
                        <tr>
                            <th style='padding: 5px;font-size: 15px;' align='left'>Email</th>
                            <td style='padding: 5px;font-size: 15px;'><a href='mailto:$person_email'>$person_email</a></td>
                        </tr>
                        <tr>
                            <th style='padding: 5px;font-size: 15px;' align='left'>Mobile</th>
                            <td style='padding: 5px;font-size: 15px;'><a href='tel:$person_mobile'>$country_code $person_mobile</a></td>
                        </tr>
                        <tr>
                            <th style='padding: 5px;font-size: 15px;' align='left'>Comapny</th>
                            <td style='padding: 5px;font-size: 15px;'>$company_name</td>
                        </tr>
                        <tr>
                            <th style='padding: 5px;font-size: 15px;' align='left'>Employees</th>
                            <td style='padding: 5px;font-size: 15px;'>$no_of_employees</td>
                        </tr>
                        <tr>
                            <th style='padding: 5px;font-size: 15px;' align='left'>Address</th>
                            <td style='padding: 5px;font-size: 15px;'>$address</td>
                        </tr>
                        <tr>
                            <th style='padding: 5px;font-size: 15px;' align='left'>Type</th>
                            <td style='padding: 5px;font-size: 15px;'>$inquiry_type_view</td>
                        </tr>
                    </table>
                    

                    <p style='font-size: 18px;text-align: center;font-family: Poppins'>App Version Code : <span > $app_version_code</span>
                    </p>
                    <p style='font-size: 18px;text-align: center;font-family: Poppins'>Device : <span > $device</span>
                    </p>
                    
                    <p style='font-size: 18px;text-align:center;background-color:#00000033;padding-top:8px; padding-bottom:8px;color:#000;margin:0px;'>&#169; $cy <a href='https://www.my-company.app/' target='_blank'>".$d->app_name()."</a>. All rights reserved.
                    </p>
                    
                </div>
            </div>
        </div>
    </body>
</html>";
?>