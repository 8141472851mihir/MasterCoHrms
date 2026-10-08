<?php
include_once 'lib.php';

if(isset($_POST) && !empty($_POST)){

    if ($key==$keydb) {
  
    $response = array();
    extract(array_map("test_input" , $_POST));
        
            if($_POST['appAccess']=="appAccess"){

                $appData=$d->select("resident_app_menu_society","menu_status='1' AND app_menu_id=37 AND society_id='$society_id'","");
                if (mysqli_num_rows($appData)>0) {
                    $response["child_security_menu_hide"]=true;  //0 for Hide 1 for Show
                } else {
                    $response["child_security_menu_hide"]=false;  //0 for Hide 1 for Show
                }
                $response["restart_status"]=false;

                $app_data=$d->select("gatekeeper_app_access","society_id=0 OR society_id='$society_id'","");

               
                if(mysqli_num_rows($app_data)>0){

                    $response["app"] = array();


                    while($data_app=mysqli_fetch_array($app_data)) {

                    	$app=array();
                    	$app["app_id"]=$data_app["app_id"];
                        $app["app_package_name"]=trim($data_app["app_package_name"]);
                        if ($data_app['is_package_all']==1) {
						$app["is_package_all"]=true;
                        } else {
                        $app["is_package_all"]=false;
                        }
                        
                    	array_push($response["app"], $app); 
                    }
                     

                    
                     $response["message"]="success.";
                     $response["status"]="200";
                     echo json_encode($response);
                }else{
                	 $response["message"]="faild.";
                     $response["status"]="201";
                     echo json_encode($response); 
                }

       }else {
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
