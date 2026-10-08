<?php
include_once 'lib.php';
$_POST = json_decode($d->manage_decryption("1", file_get_contents("php://input")), true);
$compress = resolve_api_compress();
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted, [
        "status" => "201",
        "message" => "Invalid Data"
    ], $compress);
    exit();
}
try {
    if (isset($_POST) && !empty($_POST)) {
        $response = array();
        extract(array_map("test_input", $_POST));
        $temDate = date("Y-m-d h:i:s");
        
            if($_POST['getCountries']=="getCountries"){

                $qcountries=$d->selectSpArray("getCountry");
               
                if(count($qcountries)>0){
                        $response["countries"] = array();

                    // Batch fetch localized country names to avoid query-per-country
                    $countryIds = [];
                    for ($ic=0; $ic <count($qcountries) ; $ic++) {
                        $countryIds[] = (int)$qcountries[$ic]['country_id'];
                    }
                    $countryIds = array_values(array_unique($countryIds));
                    $languageValueById = [];
                    if (!empty($countryIds)) {
                        $countryIdsIn = implode(',', $countryIds);
                        $langRes = $d->selectRow(
                            'common_id_csc, language_value_name',
                            'country_state_city_language',
                            "common_id_csc IN ($countryIdsIn) AND cat_type=0 AND language_id='$language_id'",
                            ''
                        );
                        while ($langRow = mysqli_fetch_array($langRes)) {
                            $languageValueById[(int)$langRow['common_id_csc']] = $langRow['language_value_name'];
                        }
                    }

                    for ($ic=0; $ic <count($qcountries) ; $ic++) { 

                        $country_id= $qcountries[$ic]['country_id'];

                        $language_value_name = $languageValueById[(int)$country_id] ?? '';
                        if ($language_value_name == '') {
                            $language_value_name= html_entity_decode($qcountries[$ic]['name']);
                        }

                            $countries = array(); 
                            $countries["country_id"]=$qcountries[$ic]['country_id'];
                            $countries["name"]=$language_value_name;
                            $countries["name_search"]=$qcountries[$ic]['name'];
                            $countries["iso3"]=$qcountries[$ic]['iso3'];
                            $countries["iso2"]=$qcountries[$ic]['iso2'];
                            $countries["phonecode"]=$qcountries[$ic]['phonecode'];
                            $countries["capital"]=$qcountries[$ic]['capital'];
                            $countries["currency"]=$qcountries[$ic]['currency'];
                            $countries["country_code"]=$qcountries[$ic]['phonecode'];
                            $country_id=$qcountries[$ic]['country_id'];

                            array_push($response["countries"], $countries);
                    }

                    $response["demo_status"]="0";
                    $response["user_mobile"]="9099360078";
                    $response["user_password"]="123456";
                    $response["sub_domain"]="https://www.my-company.app/";
                    $response["api_key"]=EnvLoader::get('API_KEY');
                    $response["society_name"]=$d->app_name();
                    $response["society_logo"]="https://www.my-company.app/assets/img/logo.png";
                    $response["society_address"]="Sardar Patel Ring Road, Near, Bopal Circle, Bopal,Ahmedabad";
                    $response["society_latitude"]="23.0303765";
                    $response["society_longitude"]="72.47743830000002";

                    $response["message"]="Get countries success.";
                    $response["status"]="200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }else{

                    $response["message"]="No countries Found.";
                    $response["status"]="201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }

            }else if($_POST['getCountriesMaster']=="getCountriesMaster"){

                if ($_POST['countryids']!="") {
                    $cIdsArray = explode(",", $_POST['countryids']);
                }

                $qcountries=$d->selectSpArray("getCountry");
               
                if(count($qcountries)>0){
                        $response["countries"] = array();

                    // Batch fetch localized country names to avoid query-per-country
                    $countryIds = [];
                    for ($ic=0; $ic <count($qcountries) ; $ic++) {
                        $countryIds[] = (int)$qcountries[$ic]['country_id'];
                    }
                    $countryIds = array_values(array_unique($countryIds));
                    $languageValueById = [];
                    if (!empty($countryIds)) {
                        $countryIdsIn = implode(',', $countryIds);
                        $langRes = $d->selectRow(
                            'common_id_csc, language_value_name',
                            'country_state_city_language',
                            "common_id_csc IN ($countryIdsIn) AND cat_type=0 AND language_id='$language_id'",
                            ''
                        );
                        while ($langRow = mysqli_fetch_array($langRes)) {
                            $languageValueById[(int)$langRow['common_id_csc']] = $langRow['language_value_name'];
                        }
                    }

                    for ($ic=0; $ic <count($qcountries) ; $ic++) { 

                        $country_id= $qcountries[$ic]['country_id'];

                        $language_value_name = $languageValueById[(int)$country_id] ?? '';
                        if ($language_value_name == '') {
                            $language_value_name= html_entity_decode($qcountries[$ic]['name']);
                        }

                            $countries = array(); 
                            $countries["country_id"]=$qcountries[$ic]['country_id'];
                            $countries["name"]=$language_value_name;
                            $countries["name_search"]=$qcountries[$ic]['name'];
                            $countries["iso3"]=$qcountries[$ic]['iso3'];
                            $countries["iso2"]=$qcountries[$ic]['iso2'];
                            $countries["phonecode"]=$qcountries[$ic]['phonecode'];
                            $countries["capital"]=$qcountries[$ic]['capital'];
                            $countries["currency"]=$qcountries[$ic]['currency'];
                            $countries["country_code"]=$qcountries[$ic]['phonecode'];
                            $country_id=$qcountries[$ic]['country_id'];

                            if(in_array($qcountries[$ic]['country_id'], $cIdsArray)){
                                array_push($response["countries"], $countries);
                            }
                    }

                    $response["demo_status"]="0";
                    $response["user_mobile"]="9099360078";
                    $response["user_password"]="123456";
                    $response["sub_domain"]="https://www.my-company.app/";
                    $response["api_key"]=EnvLoader::get('API_KEY');
                    $response["society_name"]=$d->app_name()."";
                    $response["society_logo"]="https://www.my-company.app/assets/img/logo.png";
                    $response["society_address"]="Sardar Patel Ring Road, Near, Bopal Circle, Bopal,Ahmedabad";
                    $response["society_latitude"]="23.0303765";
                    $response["society_longitude"]="72.47743830000002";

                    $response["message"]="Get countries success.";
                    $response["status"]="200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }else{

                    $response["message"]="No countries Found.";
                    $response["status"]="201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }

            }else if($_POST['getState']=="getState" && filter_var($country_id, FILTER_VALIDATE_INT) == true){


                $qstates=$d->selectSpArray("getState('$country_id')");


                if(count($qstates)>0){

                    $response["states"] = array();

                    // Batch fetch localized state names to avoid query-per-state
                    $stateIds = [];
                    for ($ic=0; $ic <count($qstates) ; $ic++) {
                        $stateIds[] = (int)$qstates[$ic]['state_id'];
                    }
                    $stateIds = array_values(array_unique($stateIds));
                    $languageValueById = [];
                    if (!empty($stateIds)) {
                        $stateIdsIn = implode(',', $stateIds);
                        $langRes = $d->selectRow(
                            'common_id_csc, language_value_name',
                            'country_state_city_language',
                            "common_id_csc IN ($stateIdsIn) AND cat_type=1 AND language_id='$language_id'",
                            ''
                        );
                        while ($langRow = mysqli_fetch_array($langRes)) {
                            $languageValueById[(int)$langRow['common_id_csc']] = $langRow['language_value_name'];
                        }
                    }

                    for ($ic=0; $ic <count($qstates) ; $ic++) { 

                        $state_id= $qstates[$ic]['state_id'];

                        $language_value_name = $languageValueById[(int)$state_id] ?? '';
                        if ($language_value_name == '') {
                            $language_value_name= html_entity_decode($qstates[$ic]['name']);
                        }

                        $states = array(); 
                        $states["state_id"]=$qstates[$ic]['state_id'];
                        $states["name"]=$language_value_name;
                        $states["name_search"]=$qstates[$ic]['name'];
                        $states["country_id"]=$qstates[$ic]['country_id'];
                       
                        $state_id=$qstates[$ic]['state_id'];
                        array_push($response["states"], $states);
                    }


                    $response["message"]="Get State success.";
                    $response["status"]="200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }else{

                    $response["message"]="No State Found.";
                    $response["status"]="201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }

            }else if($_POST['getCity']=="getCity" && filter_var($state_id, FILTER_VALIDATE_INT) == true){


                $qcities=$d->selectSpArray("getCity('$state_id')");
                if(count($qcities)>0){
                    
                    $response["cities"] = array();

                    // Batch fetch localized city names to avoid query-per-city
                    $cityIds = [];
                    for ($ic=0; $ic <count($qcities) ; $ic++) {
                        $cityIds[] = (int)$qcities[$ic]['city_id'];
                    }
                    $cityIds = array_values(array_unique($cityIds));
                    $languageValueById = [];
                    if (!empty($cityIds)) {
                        $cityIdsIn = implode(',', $cityIds);
                        $langRes = $d->selectRow(
                            'common_id_csc, language_value_name',
                            'country_state_city_language',
                            "common_id_csc IN ($cityIdsIn) AND cat_type=2 AND language_id='$language_id'",
                            ''
                        );
                        while ($langRow = mysqli_fetch_array($langRes)) {
                            $languageValueById[(int)$langRow['common_id_csc']] = $langRow['language_value_name'];
                        }
                    }

                    for ($ic=0; $ic <count($qcities) ; $ic++) { 

                        $city_id= $qcities[$ic]['city_id'];

                        $language_value_name = $languageValueById[(int)$city_id] ?? '';
                        if ($language_value_name == '') {
                            $language_value_name= html_entity_decode($qcities[$ic]['name']);
                        }

                        $cities = array(); 

                        $cities["city_id"]=$qcities[$ic]['city_id'];
                        $cities["name"]=$language_value_name;
                        $cities["state_id"]=$qcities[$ic]['state_id'];
                        $cities["name_search"]=$qcities[$ic]['name'];
                        $cities["country_id"]=$qcities[$ic]['country_id'];
                        
                        array_push($response["cities"], $cities);
                    }


                    $response["message"]="Get City success.";
                    $response["status"]="200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }else{

                    $response["message"]="No City Found.";
                    $response["status"]="201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }

            }else if($_POST['getCountriesSingle']=="getCountriesSingle" && filter_var($country_id, FILTER_VALIDATE_INT) == true){

                $qcountries=$d->select("countries","country_id='$country_id'");
                if(mysqli_num_rows($qcountries)>0){

                    $cData=mysqli_fetch_array($qcountries);
                    
                    $response["name"]=$cData['name'];
                    $response["message"]="Get countries success.";
                    $response["status"]="200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }else{

                    $response["message"]="No countries Found.";
                    $response["status"]="201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }

            }else if($_POST['getStateSingle']=="getStateSingle" && filter_var($state_id, FILTER_VALIDATE_INT) == true){


                $qcountries=$d->select("states","state_id='$state_id'");
                if(mysqli_num_rows($qcountries)>0){

                    $cData=mysqli_fetch_array($qcountries);
                    
                    $response["name"]=$cData['name'];
                    $response["message"]="Get State success.";
                    $response["status"]="200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }else{

                    $response["message"]="No State Found.";
                    $response["status"]="201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }

            }else if($_POST['getCitySingle']=="getCitySingle" && filter_var($city_id, FILTER_VALIDATE_INT) == true){


               
                $qcountries=$d->select("cities","city_id='$city_id'");
                if(mysqli_num_rows($qcountries)>0){

                    $cData=mysqli_fetch_array($qcountries);
                    
                    $response["name"]=$cData['name'];
                    $response["message"]="Get City success.";
                    $response["status"]="200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }else{

                    $response["message"]="No City Found.";
                    $response["status"]="201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }

            }else if($_POST['getCityAll']=="getCityAll" ){


                $qcities=$d->select("cities,society_master","cities.city_id=society_master.city_id");
                if(mysqli_num_rows($qcities)>0){
                    
                    $response["cities"] = array();

                    // Materialize rows, then batch fetch localized city names
                    $cityRows = [];
                    $cityIds = [];
                    while($data=mysqli_fetch_array($qcities)) {
                        $cityRows[] = $data;
                        $cityIds[] = (int)$data['city_id'];
                    }

                    $cityIds = array_values(array_unique($cityIds));
                    $languageValueById = [];
                    if (!empty($cityIds)) {
                        $cityIdsIn = implode(',', $cityIds);
                        $langRes = $d->selectRow(
                            'common_id_csc, language_value_name',
                            'country_state_city_language',
                            "common_id_csc IN ($cityIdsIn) AND cat_type=2 AND language_id='$language_id'",
                            ''
                        );
                        while ($langRow = mysqli_fetch_array($langRes)) {
                            $languageValueById[(int)$langRow['common_id_csc']] = $langRow['language_value_name'];
                        }
                    }

                    foreach ($cityRows as $data) {
                        $city_id= (int)$data['city_id'];
                        $language_value_name = $languageValueById[$city_id] ?? '';
                        if ($language_value_name == '') {
                            $language_value_name= html_entity_decode($data['name']);
                        }

                        $cities = array(); 

                        $cities["city_id"]=$data['city_id'];
                        $cities["name"]=$language_value_name;
                        $cities["state_id"]=$data['state_id'];
                        $cities["name_search"]=$data['name'];
                        $cities["country_id"]=$data['country_id'];
                        
                        array_push($response["cities"], $cities);
                    }


                    $response["message"]="Get City success.";
                    $response["status"]="200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }else{

                    $response["message"]="No City Found.";
                    $response["status"]="201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }

            } else if($_POST['getCityAllMaster']=="getCityAllMaster" ){

                if ($_POST['countryids']!="") {
                    $cIdsArray = explode(",", $_POST['countryids']);
                }

                if (!isset($cIdsArray)) {
                    $cIdsArray = [];
                }
                $cIdsArray = array_values(array_unique(array_map('intval', $cIdsArray)));

                $qcities=$d->select("cities,society_master","cities.city_id=society_master.city_id");
                if(mysqli_num_rows($qcities)>0){
                    
                    $response["cities"] = array();

                    // Materialize rows, then batch fetch localized city names
                    $cityRows = [];
                    $cityIds = [];
                    while($data=mysqli_fetch_array($qcities)) {
                        $cityRows[] = $data;
                        $cityIds[] = (int)$data['city_id'];
                    }

                    $cityIds = array_values(array_unique($cityIds));
                    $languageValueById = [];
                    if (!empty($cityIds)) {
                        $cityIdsIn = implode(',', $cityIds);
                        $langRes = $d->selectRow(
                            'common_id_csc, language_value_name',
                            'country_state_city_language',
                            "common_id_csc IN ($cityIdsIn) AND cat_type=2 AND language_id='$language_id'",
                            ''
                        );
                        while ($langRow = mysqli_fetch_array($langRes)) {
                            $languageValueById[(int)$langRow['common_id_csc']] = $langRow['language_value_name'];
                        }
                    }

                    foreach ($cityRows as $data) {
                        $city_id = (int)$data['city_id'];
                        $language_value_name = $languageValueById[$city_id] ?? '';
                        if ($language_value_name == '') {
                            $language_value_name = html_entity_decode($data['name']);
                        }

                        $cities = array(); 
                        $cities["city_id"]=$data['city_id'];
                        $cities["name"]=$language_value_name;
                        $cities["state_id"]=$data['state_id'];
                        $cities["name_search"]=$data['name'];
                        $cities["country_id"]=$data['country_id'];

                        if(in_array((int)$data['country_id'], $cIdsArray, true)){
                            array_push($response["cities"], $cities);
                        }
                    }


                    $response["message"]="Get City success.";
                    $response["status"]="200";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }else{

                    $response["message"]="No City Found.";
                    $response["status"]="201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

                }

            }else{
                $response["message"]="wrong tag.";
                $response["status"]="201";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();

            }


    } else {
        $response = array();
        $response["message"] = "wrong tag.";
        $response["status"] = "201";
        echo $d->manage_encryption($is_encrypted, $response, $compress);
        exit();
    }
} catch (Exception $e) {
    $response = array();
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
    echo $d->manage_encryption($is_encrypted, $response, $compress);
    exit();
}
