<?php
include_once 'lib.php';

$_POST = json_decode($d->manage_decryption("1", file_get_contents("php://input")), true);
$compress = resolve_api_compress();

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

        if ($key != $keydb) {
            $response["message"] = $xml->string->wrong_tag . '';
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit();
        }

        if ($_POST['getCompanyBiometric'] == "getCompanyBiometric") {
            $array1 = array();
            $array2 = array();

            if (isset($company_name) && strlen($company_name) > 2) {
                $qsociety = $d->selectRow(
                    "society_master.*,countries.name,states.name as state_name,cities.name as city_name",
                    "countries,cities,states,society_master",
                    "countries.country_id=society_master.country_id AND states.state_id=society_master.state_id AND cities.city_id=society_master.city_id AND (society_master.society_name LIKE '%$company_name%' OR society_master.company_full_name LIKE '%$company_name%' )"
                );

                $qsocietyWl = $d->selectRow(
                    "society_master_white_label.*,countries.name,states.name as state_name,cities.name as city_name",
                    "countries,cities,states,society_master_white_label",
                    "countries.country_id=society_master_white_label.country_id AND states.state_id=society_master_white_label.state_id AND cities.city_id=society_master_white_label.city_id AND society_master_white_label.project_type=0  AND  (society_master_white_label.society_name LIKE '%$company_name%' OR society_master_white_label.company_full_name LIKE '%$company_name%' )"
                );
            }

            if (isset($qsociety) && mysqli_num_rows($qsociety) > 0) {
                $array1["company"] = array();
                while ($data_society = mysqli_fetch_array($qsociety)) {
                    $company = array();
                    $company["company_id"] = $data_society['society_id'];
                    $company["company_name"] = $data_society['society_name'];
                    $company["sub_domain"] = $data_society['sub_domain'];
                    array_push($array1["company"], $company);
                }
            }

            if (isset($qsocietyWl) && mysqli_num_rows($qsocietyWl) > 0) {
                $array2["company"] = array();
                while ($data_society = mysqli_fetch_array($qsocietyWl)) {
                    $company = array();
                    $company["company_id"] = $data_society['master_company_id'];
                    $company["company_name"] = $data_society['society_name'];
                    $company["sub_domain"] = $data_society['sub_domain'];
                    array_push($array2["company"], $company);
                }
            }

            $response = array_merge($array1, $array2);

            if ((isset($qsociety) && mysqli_num_rows($qsociety) > 0) || (isset($qsocietyWl) && mysqli_num_rows($qsocietyWl) > 0)) {
                $response["message"] = "Get Company success.";
                $response["status"] = "200";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            }

            $response["message"] = "No Company Found.";
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit();
        }

        $response["message"] = $xml->string->wrong_tag . '';
        $response["status"] = "201";
        echo $d->manage_encryption($is_encrypted, $response, $compress);
        exit();
    }

    $response["message"] = $xml->string->wrong_tag . '';
    $response["status"] = "201";
    echo $d->manage_encryption($is_encrypted, $response, $compress);
    exit();
} catch (Exception $e) {
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
    echo $d->manage_encryption($is_encrypted, $response, $compress);
    exit();
}
