<?php
include_once 'lib.php';
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
if(isset($_POST) && !empty($_POST)){

    if ($key==$keydb ) {
  
    $response = array();
    extract(array_map("test_input" , $_POST) );
        
            if($_POST['getApi']=="getApi"  && filter_var($society_id, FILTER_VALIDATE_INT) == true ) {
                    
                if (isset($kyc_api_type) && $kyc_api_type!="" && $kyc_api_type>0) {
                   $appendQuery =  " AND kyc_api_type='$kyc_api_type'";
                }

                $app_data =$d->select("document_kyc_master","status=0 AND kyc_parent_id=0 $appendQuery","");
                $qb=$d->selectRow("fidypay_master.*","fidypay_master");
                $bData=mysqli_fetch_array($qb);
             
                    
                if(mysqli_num_rows($app_data)>0){

                    $response["fidypay_api"] = array();

                    $parentRows = [];
                    $parentKycTypes = [];
                    while($data_app=mysqli_fetch_array($app_data)) {
                        $parentRows[] = $data_app;
                        $parentKycTypes[] = (int)$data_app["kyc_api_type"];
                    }

                    $subApisByParent = [];
                    if (!empty($parentKycTypes)) {
                        $parentKycTypesIn = implode(',', array_map('intval', $parentKycTypes));
                        $qs=$d->select("document_kyc_master","status=0 AND kyc_parent_id IN ($parentKycTypesIn)");
                        while ($subData=mysqli_fetch_array($qs)) {
                            $subApisByParent[(int)$subData['kyc_parent_id']][] = $subData;
                        }
                    }

                    foreach ($parentRows as $data_app) {
                        
                        $menu_title_name= $data_app["menu_title"];
                       
                        $kyc_api_type = $data_app["kyc_api_type"];
                    	$fidypay_api=array();
                    	$fidypay_api["kyc_api_type"]=$data_app["kyc_api_type"];
                        $fidypay_api["kyc_parent_id "]=$data_app['kyc_parent_id'];
                        $fidypay_api["kyc_api_name"]=$data_app["kyc_api_name"];
						$fidypay_api["kyc_api_price"]=$data_app["kyc_api_price"];
                        $kyc_api_url = str_replace("{{BaseUrl}}", $bData['base_url'], $data_app['kyc_api_url']);
                        $fidypay_api["kyc_api_url"]=$kyc_api_url;
                        $fidypay_api["kyc_api_response"]=$data_app["kyc_api_response"];
                    	$fidypay_api["curl_sample"]=$data_app["kyc_api_common"];
                        $fidypay_api["api_method"]=$data_app["api_method"];
                        $fidypay_api["send_number_in_url"]=$data_app["send_number_in_url"];
                        $fidypay_api["row_data_key"]=$data_app["row_data_key"];
                      

                        $fidypay_api["fidypay_sub_api"] = array();
                        
                        foreach ($subApisByParent[(int)$kyc_api_type] ?? [] as $subData) {

                            
                            $fidypay_sub_api = array();
                            $fidypay_sub_api["kyc_api_type"]=$subData["kyc_api_type"];
                            $fidypay_sub_api["kyc_parent_id"]=$subData['kyc_parent_id'];
                            $fidypay_sub_api["kyc_api_name"]=$subData["kyc_api_name"];
                            $fidypay_sub_api["kyc_api_price"]=$subData["kyc_api_price"];
                            $kyc_api_url_sub = str_replace("{{BaseUrl}}", $bData['base_url'], $subData['kyc_api_url']);
                            $fidypay_sub_api["kyc_api_url"]=$kyc_api_url_sub;
                            $fidypay_sub_api["kyc_api_response"]=$subData["kyc_api_response"];
                            $fidypay_sub_api["curl_sample"]=$subData["kyc_api_common"];
                            $fidypay_sub_api["api_method"]=$subData["api_method"];
                            $fidypay_sub_api["send_number_in_url"]=$subData["send_number_in_url"];
                            $fidypay_sub_api["row_data_key"]=$subData["row_data_key"];

                            array_push($fidypay_api["fidypay_sub_api"], $fidypay_sub_api); 

                        }

                    	array_push($response["fidypay_api"], $fidypay_api); 
                    }

                }
                    
             
             $response["base_url"]=$bData['base_url'];
             $response["client_id"]=$bData['client_id'];
             $response["client_secret"]=$bData['client_secret'];
             $response["authorization"]=$bData['authorization'];


             $response["message"]="success.";
             $response["status"]="200";
             echo json_encode($response);
        

       }  else {
                $response["message"]="wrong tag.";
                $response["status"]="201";
                echo json_encode($response);                  
            }
    }else{

         $response["message"]="wrong api key.";
        $response["status"]="201";
        echo json_encode($response);
    }
}

