<?php
include_once 'lib.php';

if(isset($_POST) && !empty($_POST))
{
  
    $response = array();
    extract(array_map("test_input" , $_POST));
    if($_POST['sentExeNotWorkingAlert']=="sentExeNotWorkingAlert" && $company_id>0)
    {
        

        $mobileList = explode(",", "9737564998,9537943844,7802970024");

        $q = $d->selectRow("society_name", "society_master", "society_id='$company_id'");
        $companyData = mysqli_fetch_array($q);
        $companyName = $companyData['society_name'];
        $txt = "$companyName EXE Not Working Alert Received at " . date("Y-m-d h:i:s A");
        file_put_contents('../img/exeLog.txt', $txt . PHP_EOL, FILE_APPEND | LOCK_EX);

        $companyNameEncoded = urlencode($companyName); // Encode once

        foreach ($mobileList as $mobile) {
            $mobile = trim($mobile); // Remove any spaces
            $smsApiKey = rawurlencode((string) EnvLoader::get('TWO_FACTOR_API_KEY'));
            $url = "https://2factor.in/API/R1/?module=TRANS_SMS&apikey=$smsApiKey&to=91$mobile&from=CHPLGP&templatename=CHPLOTP&var1=EXENOT&var2=$companyNameEncoded";

            $msg = @file_get_contents($url); // @ suppresses warnings if fails
            if ($msg === false) {
                error_log("Failed to send SMS to $mobile. Response: " . var_export($http_response_header, true));
            } else {
                error_log("SMS sent to $mobile. Response: $msg");
            }
        }


        $response["message"]="Alert Sent";
        $response["status"]="200";
        echo json_encode($response);

    }
    else
    {
        $response["message"]="wrong tag...";
        $response["status"]="201";
        echo json_encode($response);
    }
}
?>
