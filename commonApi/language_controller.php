<?php
include_once 'lib.php';
// remove after patch in mcyo
if(isset($_POST) && !empty($_POST)){

    if ($key==$keydb) {
    $response = array();
    extract(array_map("test_input" , $_POST));
  $today  = date("Y-m-d");
    if($_POST['getLanguage']=="getLanguage" ){

           
        $q=$d->select("language_master","active_status=0 AND language_id!=5","ORDER BY language_id ASC");
         
        if(mysqli_num_rows($q)>0){

            $response["language"] = array();

            while($data=mysqli_fetch_array($q)) {
                     $language = array(); 
                     $language["language_id"]=$data['language_id'];
                     $language["language_name"]=$data['language_name'];
                     $language["language_name_1"]=$data['language_name_1'];
                     $language["language_file"]=$data['language_file'];
                     $language["continue_btn_name"]=$data['continue_btn_name'];
                     $language["language_icon"]=$base_url.'img/language/'.$data['language_file'];
                     array_push($response["language"], $language);
            }
           
             $response["message"]="Get Language Successfully!";
             $response["status"]="200";
             echo json_encode($response);

        }else{
            $response["message"]="No Language Available.";
            $response["status"]="201";
            echo json_encode($response);

        }
    }else  if($_POST['getLanguageAll']=="getLanguageAll" ){

           
        $q=$d->select("language_master","","ORDER BY language_id ASC");
         
        if(mysqli_num_rows($q)>0){

            $response["language"] = array();

            while($data=mysqli_fetch_array($q)) {
                     $language = array(); 
                     $language["language_id"]=$data['language_id'];
                     $language["language_name"]=$data['language_name'];
                     $language["language_name_1"]=$data['language_name_1'];
                     $language["language_file"]=$data['language_file'];
                     $language["continue_btn_name"]=$data['continue_btn_name'];
                     $language["language_icon"]=$base_url.'img/language/'.$data['language_file'];
                     array_push($response["language"], $language);
            }
           
             $response["message"]="Get Language Successfully!";
             $response["status"]="200";
             echo json_encode($response);

        }else{
            $response["message"]="No Language Available.";
            $response["status"]="201";
            echo json_encode($response);

        }
    }else if($_POST['getLanguageNew']=="getLanguageNew" ){

        $response["language"] = array();
        $country_id = $d->sanitizeActionIdAsInt($country_id ?? 0);
           
        $q=$d->select("language_master","active_status=0 AND is_english_language=1 AND country_id='$country_id'"," ORDER BY language_id DESC LIMIT 1");
        
        if(mysqli_num_rows($q)==0){
            $q=$d->select("language_master","active_status=0 AND is_english_language=1 AND country_id='101'","LIMIT 1");
        }

        if(mysqli_num_rows($q)>0){

            while($data=mysqli_fetch_array($q)) {
                     $language = array(); 
                     $language["language_id"]=$data['language_id'];
                     $language["language_name"]=$data['language_name'];
                     $language["language_name_1"]=$data['language_name_1'];
                     $language["language_file"]=$data['language_file'];
                     $language["continue_btn_name"]=$data['continue_btn_name'];
                     $language["language_icon"]=$base_url.'img/language/'.$data['language_file'];
                     array_push($response["language"], $language);
            }
           
            $temp = true;
        }else{
             $temp = false;
        }

        $q1=$d->select("language_master","active_status=0 AND is_english_language=0 AND country_id=0 OR active_status=0 AND is_english_language=0 AND country_id='$country_id'","ORDER BY language_id ASC");
        if(mysqli_num_rows($q1)>0){

            while($data1=mysqli_fetch_array($q1)) {
                     $language = array(); 
                     $language["language_id"]=$data1['language_id'];
                     $language["language_name"]=$data1['language_name'];
                     $language["language_name_1"]=$data1['language_name_1'];
                     $language["language_file"]=$data1['language_file'];
                     $language["continue_btn_name"]=$data1['continue_btn_name'];
                     $language["language_icon"]=$base_url.'img/language/'.$data1['language_file'];
                     array_push($response["language"], $language);
            }
           
            $temp1 = true;
        }else{
             $temp1 = false;
        }

        if ($temp==true || $temp1 ==true) {
            $response["message"] = 'Get Language Successfully!';
            $response["status"] = "200";
            echo json_encode($response);
        } else {
            $response["message"] = 'No Language Found';
            $response["status"] = "201";
            echo json_encode($response);
        }

    }else if($_POST['getLanguageValues']=="getLanguageValues" ){

        if ($language_id=="" || $language_id==0) {
           $language_id=1;
        }

        // $q = $d->selectRow("language_key_master.key_name,language_key_value_master.value_name,language_key_master.key_type,language_key_value_master_society.value_name_society","language_key_value_master,language_key_master  Left join language_key_value_master_society on language_key_master.key_name=language_key_value_master_society.key_name AND language_key_value_master_society.language_id='$language_id'","language_key_master.key_type=0 AND language_key_master.language_key_id=language_key_value_master.language_key_id AND language_key_value_master.language_id='$language_id'","");
        //  while ($data=mysqli_fetch_array($q)) {
        //     if ($data['value_name_society']!='') {
        //         // $response[$data['key_name']]=$data['value_name_society'].'';
        //     } else {
        //         // $response[$data['key_name']]=$data['value_name'].'';
        //     }   

        //  }

         $arrayKeyValueSingle = array();
         $arrayKeyValue = array();
        $qc=$d->selectRow("language_key_value_master.value_name,language_key_master.language_key_id,language_key_master.key_name, language_key_master.key_type, language_key_value_master_society.value_name_society","language_key_value_master, language_key_master Left join language_key_value_master_society on language_key_master.key_name=language_key_value_master_society.key_name AND language_key_value_master_society.language_id='$language_id' AND language_key_value_master_society.society_id='$society_id'","language_key_value_master.language_id='$language_id' AND language_key_master.language_key_id=language_key_value_master.language_key_id");
        // echo mysqli_num_rows($qc);
        while($oldData=mysqli_fetch_array($qc)){
            if ($oldData['key_type'] == 0) {
                $arrayKeyValueSingle[$oldData['key_name']] = $oldData['value_name_society'] != '' ? $oldData['value_name_society'] : $oldData['value_name'];
            } else {
                if (array_key_exists($oldData['key_name'], $arrayKeyValue)) {
                    array_push($arrayKeyValue[$oldData['key_name']],$oldData['value_name']);
                } else {
                    $arrayKeyValue[$oldData['key_name']] = array($oldData['value_name']);
                }
            }       
        }



        $response = array_merge($arrayKeyValueSingle, $arrayKeyValue);
        $response["totalArray"]=count($arrayKeyValue);


        // $response=$arrayKeyValue;
        
        // print_r($arrayKeyValue);
        // exit();

         // $qA = $d->select("language_key_master","key_type=1");
         // while ($dataArray=mysqli_fetch_array($qA)) {
         //    $language_key_id =$dataArray['language_key_id'];
         //    $valuArray=array();
         //    $key_value_idArray=array();
            
         //    /*$qc=$d->select("language_key_value_master","language_id='$language_id' AND language_key_id='$language_key_id'");
         //        while($oldData=mysqli_fetch_array($qc)){
         //          array_push($valuArray,  $oldData['value_name']);
         //          array_push($key_value_idArray,  $oldData['key_value_id']);
         //        }*/
            
         //    $response[$dataArray['key_name']]=$arrayKeyValue[$language_key_id];

         // }
         // $response["totalArray"]=mysqli_num_rows($qA);
        echo json_encode($response);


    }else if($_POST['checkLangugeModify']=="checkLangugeModify"  && filter_var($language_id, FILTER_VALIDATE_INT) == true && filter_var($society_id, FILTER_VALIDATE_INT) == true) {

          $qc = $d->select("language_key_value_master_society","language_id='$language_id' AND society_id='$society_id'");
         if (mysqli_num_rows($qc)>0) {
             
            $response["is_language_re_downlaod"]=true;
         } else {
            $response["is_language_re_downlaod"]=false;
         }
         
        $response["message"]="Success";
        $response["status"]="200";
        echo json_encode($response);


    }else{
      $response["message"]="wrong tag";
      $response["status"]="201";
      echo json_encode($response);
    }
  }
    else{
        $response["message"]="wrong api key";
        $response["status"]="201";
        echo json_encode($response);

    }
}
