<?php
include_once 'lib.php';

if(isset($_POST) && !empty($_POST)){
  if ($key==$keydb) {
     $response = array();
     extract(array_map("test_input" , $_POST));
    if($_POST['getLanguageValues']=="getLanguageValues"  ){
        $response["language_key"] = array();

        if ($language_id=="" || $language_id==0) {
           $language_id=1;
        }
        // LIMIT 1132
        /*$q = $d->selectRow("language_key_master.key_name,language_key_value_master.value_name,language_key_master.key_type,language_key_value_master_society.value_name_society","language_key_value_master,language_key_master  Left join language_key_value_master_society on language_key_master.key_name=language_key_value_master_society.key_name AND language_key_value_master_society.language_id='$language_id' AND language_key_value_master_society.society_id='$society_id'","language_key_master.key_type=0 AND language_key_master.language_key_id=language_key_value_master.language_key_id AND language_key_value_master.language_id='$language_id'","");
         while ($data=mysqli_fetch_array($q)) {
            $language_key=array();
            $key_value = str_replace("'", '', $data['key_name']);
            $key_value = str_replace("?", '', $key_value);

             $language_key['key_name']=$key_value;
              if ($data['value_name_society']!='') {
                $language_key['key_value']=$data['value_name_society'].'';
              } else {
                $language_key['key_value']=$data['value_name'].'';
              }
             
             array_push($response["language_key"], $language_key); 
         }*/

         $arrayKeyValueSingle = array();
         $arrayKeyValue = array();
        $qc=$d->selectRow("language_key_value_master.value_name,language_key_master.language_key_id,language_key_master.key_name, language_key_master.key_type, language_key_value_master_society.value_name_society","language_key_value_master, language_key_master Left join language_key_value_master_society on language_key_master.key_name=language_key_value_master_society.key_name AND language_key_value_master_society.language_id='$language_id' AND language_key_value_master_society.society_id='$society_id'","language_key_value_master.language_id='$language_id' AND language_key_master.language_key_id=language_key_value_master.language_key_id");
        // echo mysqli_num_rows($qc);
        while($oldData=mysqli_fetch_array($qc)){

            $key_value = str_replace("'", '', $oldData['key_name']);
            $key_value = str_replace("?", '', $key_value);

            if ($oldData['key_type'] == 0) {
                $arrayKeyValueSingle[$key_value] = $oldData['value_name_society'] != '' ? $oldData['value_name_society'] : $oldData['value_name'];
            } else {
                if (array_key_exists($key_value, $arrayKeyValue)) {
                    $arrayKeyValue[$key_value] = $arrayKeyValue[$key_value] .'~' .$oldData['value_name'];
                } else {
                    $arrayKeyValue[$key_value] = $oldData['value_name'];
                }                
            }
        }

        $newArray = array_merge($arrayKeyValueSingle, $arrayKeyValue);

        foreach ($newArray as $key => $value) {

            $result = array(
                'key_name' => $key,
                'key_value' => $value
            );

           array_push($response["language_key"], $result); 
        }

         /*$qA = $d->select("language_key_master","key_type=1","");
         while ($data=mysqli_fetch_array($qA)) {
            $language_key=array();
            $key_value = str_replace("'", '', $data['key_name']);
            $key_value = str_replace("?", '', $key_value);
            $language_key_id =$data['language_key_id'];
            $valuArray=array();
                $qc=$d->select("language_key_value_master","language_id='$language_id' AND language_key_id='$language_key_id'");
                while($oldData=mysqli_fetch_array($qc)){
                  array_push($valuArray,  $oldData['value_name']);
                }

             $language_key['key_name']=$key_value;
             
              $arrayString = implode("~", $valuArray);
              $language_key['key_value']=$arrayString;
             array_push($response["language_key"], $language_key); 
         }*/
         
         $response["language_id"]=$language_id;
         $response["message"]="Get Success";
         $response["status"]="200";
         echo json_encode($response);

     }else if($_POST['getChangebleLanguage']=="getChangebleLanguage"  && filter_var($language_id, FILTER_VALIDATE_INT) == true  && filter_var($society_id, FILTER_VALIDATE_INT) == true){
         $response["language_key"] = array();
         $q = $d->select("language_key_master","is_changeble=1");
         while ($data=mysqli_fetch_array($q)) {
            $language_key=array();

            $language_key_id =$data['language_key_id'];
            $valuArray=array();
            $qc=$d->select("language_key_value_master_society","language_id='$language_id' AND society_id='$society_id' AND key_name= '$data[key_name]'");
            $oldData=mysqli_fetch_array($qc);
               
            $language_key['key_name']=$data['key_name'];
            $language_key['value_name_society']=$oldData['value_name_society'].'';

            array_push($response["language_key"], $language_key); 

         }
        $response["message"]="Get Success";
        $response["status"]="200";
        echo json_encode($response);

    } else if($_POST['setChangebleLanguage']=="setChangebleLanguage"  && filter_var($language_id, FILTER_VALIDATE_INT) == true  && filter_var($society_id, FILTER_VALIDATE_INT) == true){
        
        // print_r($_POST['key_name']);
        $valueArray = explode("~", $value_name_society);
        $keyArray = explode("~", $key_name);

        for ($i=0; $i <count($valueArray) ; $i++) { 
                $safeKeyName = $d->escapeSqlString($keyArray[$i] ?? '');

                $m->set_data('language_id', $language_id);
                $m->set_data('society_id', $society_id);
                $m->set_data('key_name', $keyArray[$i]);
                $m->set_data('value_name_society', $valueArray[$i]);

                $a1 = array(
                    'key_name' => $m->get_data('key_name'),
                    'value_name_society' => $m->get_data('value_name_society'),
                    'society_id' => $m->get_data('society_id'),
                    'language_id' => $m->get_data('language_id')
                );
                // already cehck
                $qc = $d->select("language_key_value_master_society","language_id='$language_id' AND society_id='$society_id' AND key_name='$safeKeyName'");
                if (mysqli_num_rows($qc)>0) {
                    $q = $d->update('language_key_value_master_society', $a1,"language_id='$language_id' AND society_id='$society_id' AND key_name='$safeKeyName'");
                } else {
                    $q = $d->insert('language_key_value_master_society', $a1);

                }
        }

        if ($q === TRUE) {
            $d->createCompanyLanguageFiles($society_id, $language_id,$bms_admin_id, $created_by);
        }

        $response["message"]="Update Successfully";
        $response["status"]="200";
        echo json_encode($response);

    }else if($_POST['getLanguageValuesAdmin']=="getLanguageValuesAdmin"  ){
       
        if ($language_id!="" &&  $language_id !='0') {
            $q11=$d->select("language_master","active_status=0 AND language_id='$language_id' "," ORDER BY language_id DESC LIMIT 1");
        } else {
            $q11=$d->select("language_master","active_status=0 AND is_english_language=1 AND country_id='$country_id'"," ORDER BY language_id DESC LIMIT 1");
        }
        
        if(mysqli_num_rows($q11)==0){
          $q11=$d->select("language_master","active_status=0 AND is_english_language=1 AND country_id='101'","LIMIT 1");
        }

        $dataPrimary=mysqli_fetch_array($q11);

        $language_id = $dataPrimary['language_id'];;
        
         $response["language_id"]=$language_id;
         $response["message"]="Get Success";
         $response["status"]="200";
         echo json_encode($response);

     }else {
        $response["message"]="Something Wrong";
      $response["status"]="201";
      echo json_encode($response);
     }
    }
}
