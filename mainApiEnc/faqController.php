<?php
include_once 'lib.php';
include_once '../apAdmin/lib/crmconfig.php';

$_POST = json_decode($d->manage_decryption("1", file_get_contents("php://input")), true);
$compress = resolve_api_compress();
// $_POST =  $d->manage_encryption("1", $_POST);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted, [
        "status" => "201",
        "message" => $xml->string->invalid_request . ""
    ], $compress);
    exit();
}
try {
    if (isset($_POST) && !empty($_POST)) {
        $response = array();
        extract(array_map("test_input", $_POST));
        $today = date("Y-m-d");
        if ($_POST['getFaq'] == "getFaq") {
            if (isset($_POST['company_id']) && !empty($_POST['company_id'])) {
                $company_id = $_POST['company_id'];
                $appendQ = " AND resident_app_menu_society.society_id = $company_id";
            } else {
                $company_id = 0;
                $appendQ = "";
            }
            $language_id = $_POST['language_id'];
            $categoryQuery = $d->selectRow(
                "DISTINCT resident_app_menu.app_menu_id AS category_id, resident_app_menu.menu_title AS name,
                    faq_question_master.language_id",
                "faq_question_master 
                LEFT JOIN resident_app_menu ON faq_question_master.category_type=resident_app_menu.app_menu_id
                LEFT JOIN resident_app_menu_society ON faq_question_master.category_type = resident_app_menu_society.app_menu_id
                LEFT JOIN language_master ON faq_question_master.language_id=language_master.language_id",
                "(faq_question_master.category_type = 0 OR 
                (resident_app_menu.menu_status = 0 $appendQ)) 
                AND faq_question_master.status = 1 AND faq_question_master.language_id= $language_id",
                "ORDER BY resident_app_menu.app_menu_id DESC"
            );

            $faqQuery = $d->selectRow(
                "faq_question_master.faq_sub_master_id,faq_question_master.faq_question,faq_question_master.faq_answer,
                    faq_question_master.platform_type,faq_question_master.category_type,faq_question_master.status,faq_question_master.faq_attachment,
                    resident_app_menu_society.society_id,resident_app_menu_society.app_menu_id,resident_app_menu.menu_status,
                    resident_app_menu.app_menu_id,language_master.language_id,language_master.language_name",
                "faq_question_master 
                LEFT JOIN resident_app_menu ON faq_question_master.category_type=resident_app_menu.app_menu_id
                LEFT JOIN resident_app_menu_society ON faq_question_master.category_type = resident_app_menu_society.app_menu_id
                LEFT JOIN language_master ON faq_question_master.language_id=language_master.language_id",
                "(faq_question_master.category_type = 0 OR faq_question_master.category_type < 0 OR 
                (resident_app_menu.menu_status = 0 $appendQ)) 
                AND faq_question_master.status = 1 AND faq_question_master.language_id= $language_id",
                "ORDER BY faq_sub_master_id DESC"
            );

            $response["faq"] = array();
            $response["category"] = array();

            while ($data = mysqli_fetch_array($categoryQuery)) {
                $category = array();
                $category["category_id"] = $data['category_id'];
                $category["category_name"] = $data['name'];
                array_push($response["category"], $category);
            }

            if (mysqli_num_rows($faqQuery) > 0) {
                while ($data = mysqli_fetch_array($faqQuery)) {
                    $faq = array();
                    $faq["faq_id"] = $data['faq_sub_master_id'];
                    $faq["question"] = $data['faq_question'];
                    $faq["answer"] = $data['faq_answer'];
                    $faq["platform"] = $data['platform_type'];
                    $faq["category_id"] = $data['category_type'];

                    if (!empty($data['faq_attachment'])) {
                        $faq["attachment"] = $m->base_url() . 'img/' . $data['faq_attachment'];
                    } else {
                        $faq["attachment"] = "";
                    }
                    array_push($response["faq"], $faq);
                }
                $response["category"][] = array(
                    "category_id" => "0",
                    "category_name" => "Other"
                );
                $response["category"][] = array(
                    "category_id" => "-1",
                    "category_name" => "Tracking"
                );

                $response["message"] = $xml->string->data_found . "";
                $response["status"] = "200";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            } else {
                $response["message"] = $xml->string->no_data_found_web . '';
                $response["status"] = "201";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            }
        } else if ($_POST['addFeedback'] == "addFeedback") {
            $to = "yuvraj@chplgroup.org";
            $cc = "bhavesh@chplgroup.org";
            $subject = $subject;
            $message = "<table>
            <tr>Name : $name</tr>
            <tr>Email : $email</tr>
            <tr>Mobile : $mobile</tr>
            <tr>City : $city</tr>
            <tr>Message : </tr>
            <p>$message</p>
            </table>";
            include '../apAdmin/mail.php';
            $m->set_data('name', $name);
            $m->set_data('email', $email);
            $m->set_data('mobile', $mobile);
            $m->set_data('city', $city);
            $m->set_data('subject', $subject);
            $m->set_data('message', $_POST['message']);
            $m->set_data('add_date', date("Y-m-d H:i:s"));

            $value_array = array(
                'name' => $m->get_data('name'),
                'email' => $m->get_data('email'),
                'mobile' => $m->get_data('mobile'),
                'city' => $m->get_data('city'),
                'subject' => $m->get_data('subject'),
                'message' => $m->get_data('message'),
                'add_date' => $m->get_data('add_date'),
            );

            $insert = $d->insert("contact_us", $value_array);
            if ($insert > 0) {
                $names = explode(" ", $name);
                $firstName = ucfirst(strtolower($names[0]));
                $lastName = "";
                for ($i = 1; $i <= count($names); $i++) {
                    $lastName .= ucfirst(strtolower($names[$i])) . " ";
                }
                $country = $d->sanitizeActionIdAsInt($country);
                $country_q = $d->selectRow("name,phonecode", "countries", "country_id='$country'");
                $countrydata = mysqli_fetch_array($country_q);
                $countrycode = $countrydata["phonecode"];
                $selectedcountry = $countrydata["name"];
                $countryCode = str_replace("+", "", $countrycode);
                $finalcode = explode("-", $countryCode);
                $countryCode = $finalcode[0];
                $leadata["leads"][0]["firstName"] = trim($firstName);
                $leadata["leads"][0]["lastName"] = trim($lastName);
                $leadata["leads"][0]["designation"] = "";
                $leadata["leads"][0]["email"] = $email;
                $leadata["leads"][0]["countryCode"] = $countryCode;
                $leadata["leads"][0]["mobile"] = $mobile;
                $leadata["leads"][0]["phoneCountryCode"] = $countryCode;
                $leadata["leads"][0]["phone"] = $mobile;
                $leadata["leads"][0]["expectedRevenue"] = "";
                $leadata["leads"][0]["description"] = $_POST['message'];
                $leadata["leads"][0]["companyName"] = $company_name;
                $leadata["leads"][0]["companyState"] = "";
                $leadata["leads"][0]["companyStreet"] = "";
                $leadata["leads"][0]["companyCity"] = $city;
                $leadata["leads"][0]["companyCountry"] = $selectedcountry;
                $leadata["leads"][0]["companyPincode"] = "";
                $leadata["leads"][0]["leadPriority"] = "1";
                if ($partner_id == 1) { // USA CRM
                    $crmauthToken = "HVMsmi9WE5PfJ9B68IFuqg==.Em7ChCXb/i6BdO39sLhSwg==";
                }
                $curl1 = curl_init();

                curl_setopt_array($curl1, array(
                    CURLOPT_URL => $crmurl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($leadata),
                    CURLOPT_HTTPHEADER => array(
                        'authToken: ' . $crmauthToken,
                        'Content-Type: application/json'
                    ),
                ));

                $crmresponse = curl_exec($curl1);
                file_put_contents('log.txt', date('Y-m-d H:i:s') . "\n" . $crmresponse . "\n", FILE_APPEND);
                $code = curl_getinfo($curl1, CURLINFO_HTTP_CODE);
                curl_close($curl1);
                $response["message"] = $xml->string->thank_you_for_your_feedback . '';
                $response["status"] = "200";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            } else {
                $response["message"] = $xml->string->something_wrong . '';
                $response["status"] = "201";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            }
        } else {
            $response["message"] = $xml->string->wrong_tag . '';
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit();
        }
    } else {
        $response["message"] = $xml->string->wrong_tag . '';
        $response["status"] = "201";
        echo $d->manage_encryption($is_encrypted, $response, $compress);
        exit();
    }
} catch (Exception $e) {
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
    echo $d->manage_encryption($is_encrypted, $response, $compress);
    exit();
}
