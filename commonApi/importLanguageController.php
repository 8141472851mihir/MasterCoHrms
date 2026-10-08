<?php 
include_once 'lib.php';
  if (isset($_POST) && !empty($_POST)) {
    extract(array_map("test_input", $_POST));
    if (isset($exortLangugeKey)) {
       $response = array();

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,$retun_url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      curl_setopt($ch, CURLOPT_POSTFIELDS,"getLanguageValues=getLanguageValues& language_key_ids=$language_key_ids");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $server_output = curl_exec($ch);
      curl_close ($ch);
      $json = json_decode($server_output, true);
      
      $totalKeysFound = count($json['language_key']);

      for ($i=0; $i <$totalKeysFound ; $i++) { 
          
          $key_name = $json['language_key'][$i]['key_name'];
          $key_type = $json['language_key'][$i]['key_type'];
          $m->set_data('key_name',$json['language_key'][$i]['key_name']);
          $m->set_data('key_type',$json['language_key'][$i]['key_type']);
          $m->set_data('no_of_key',$json['language_key'][$i]['no_of_key']);
          
          $a = array('key_name'=>$m->get_data('key_name'),
            'key_type'=>$m->get_data('key_type'),
            'no_of_key'=>$m->get_data('no_of_key')
          );

          $qalerady=$d->selectRow("key_name,language_key_id","language_key_master","key_name='$key_name'");
          if (mysqli_num_rows($qalerady)==0) {
            $q=$d->insert("language_key_master",$a);
            $language_key_id =  $con->insert_id;
             $totalValues = count($json['language_key'][$i]['language']);

            for ($i1=0; $i1 <$totalValues ; $i1++) { 

              if ($key_type==0) {
                $m->set_data('language_id',$json['language_key'][$i]['language'][$i1]['language_id']);
                $m->set_data('language_key_id',$language_key_id);
                $m->set_data('value_name',$json['language_key'][$i]['language'][$i1]['value_name']);
                $aValue = array(
                  'language_id'=>$m->get_data('language_id'),
                  'language_key_id'=>$m->get_data('language_key_id'),
                  'value_name'=>$m->get_data('value_name')
                );

                $d->insert("language_key_value_master",$aValue);
              } else {


                $mixValue  = $json['language_key'][$i]['language'][$i1]['value_name'];
                $subValuAray = explode("~", $mixValue);
                $totSubValue = count($subValuAray);
                for ($i2=0; $i2 <$totSubValue ; $i2++) { 
                  $m->set_data('language_id',$json['language_key'][$i]['language'][$i1]['language_id']);
                  $m->set_data('language_key_id',$language_key_id);
                  $m->set_data('value_name',$subValuAray[$i2]);

                   $aValueSub = array(
                    'language_id'=>$m->get_data('language_id'),
                    'language_key_id'=>$m->get_data('language_key_id'),
                    'value_name'=>$m->get_data('value_name')
                  );

                   $d->insert("language_key_value_master",$aValueSub);
                }

              }
            }

          } else {
            $oldData=mysqli_fetch_array($qalerady);
            $language_key_id =  $oldData['language_key_id'];

            $totalValues = count($json['language_key'][$i]['language']);

            for ($i1=0; $i1 <$totalValues ; $i1++) { 

              if ($key_type==0) {
                $language_id = $json['language_key'][$i]['language'][$i1]['language_id'];
                $m->set_data('language_id',$json['language_key'][$i]['language'][$i1]['language_id']);
                $m->set_data('language_key_id',$language_key_id);
                $m->set_data('value_name',$json['language_key'][$i]['language'][$i1]['value_name']);
                $aValue = array(
                  'language_id'=>$m->get_data('language_id'),
                  'language_key_id'=>$m->get_data('language_key_id'),
                  'value_name'=>$m->get_data('value_name')
                );

                $d->update("language_key_value_master",$aValue,"language_key_id='$language_key_id' AND language_id='$language_id'");
              } else {

                $mixValue  = $json['language_key'][$i]['language'][$i1]['value_name'];
                $subValuAray = explode("~", $mixValue);
                $totSubValue = count($subValuAray);

                $language_id = $json['language_key'][$i]['language'][$i1]['language_id']; 
                $olvValueArray = array();
                $ovq = $d->select("language_key_value_master","language_key_id='$language_key_id' AND language_id='$language_id' ");
                while ($oldData=mysqli_fetch_array($ovq)) {
                  array_push($olvValueArray, $oldData['key_value_id']);
                }
                
                for ($i2=0; $i2 <$totSubValue ; $i2++) { 
                  $key_value_id = $olvValueArray[$i2];
                  $m->set_data('value_name',$subValuAray[$i2]);

                   $aValueSub = array(
                    'value_name'=>$m->get_data('value_name')
                  );

                  $d->update("language_key_value_master",$aValueSub,"language_key_id='$language_key_id' AND language_id='$language_id' AND key_value_id='$key_value_id'");
                }

              }
            }
          } 

          // print_r($a);
          // echo "<br>";
       } 
     
      $response["message"] = "User Language Export Successfully ";
      $response["status"] = "200";
      echo json_encode($response);

    } else if (isset($exortLangugeKeyGuard)) {
       $response = array();

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,$retun_url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      curl_setopt($ch, CURLOPT_POSTFIELDS,"getLanguageValuesGuard=getLanguageValuesGuard& language_key_ids=$language_key_ids");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $server_output = curl_exec($ch);
      curl_close ($ch);
      $json = json_decode($server_output, true);
      
      $totalKeysFound = count($json['language_key']);

      for ($i=0; $i <$totalKeysFound ; $i++) { 
          
          $key_name = $json['language_key'][$i]['key_name'];
          $key_type = $json['language_key'][$i]['key_type'];
          $m->set_data('key_name',$json['language_key'][$i]['key_name']);
          $m->set_data('key_type',$json['language_key'][$i]['key_type']);
          $m->set_data('no_of_key',$json['language_key'][$i]['no_of_key']);
          
          $a = array('key_name'=>$m->get_data('key_name'),
            'key_type'=>$m->get_data('key_type'),
            'no_of_key'=>$m->get_data('no_of_key')
          );

          $qalerady=$d->selectRow("key_name,language_key_id","language_key_master_gatekeeper","key_name='$key_name'");
          if (mysqli_num_rows($qalerady)==0) {
            $q=$d->insert("language_key_master_gatekeeper",$a);
            $language_key_id =  $con->insert_id;
            $totalValues = count($json['language_key'][$i]['language']);

            for ($i1=0; $i1 <$totalValues ; $i1++) { 

              if ($key_type==0) {
                $m->set_data('language_id',$json['language_key'][$i]['language'][$i1]['language_id']);
                $m->set_data('language_key_id',$language_key_id);
                $m->set_data('value_name',$json['language_key'][$i]['language'][$i1]['value_name']);
                $aValue = array(
                  'language_id'=>$m->get_data('language_id'),
                  'language_key_id'=>$m->get_data('language_key_id'),
                  'value_name'=>$m->get_data('value_name')
                );

                $d->insert("language_key_value_master_gatekeeper",$aValue);
              } else {


                $mixValue  = $json['language_key'][$i]['language'][$i1]['value_name'];
                $subValuAray = explode("~", $mixValue);
                $totSubValue = count($subValuAray);
                for ($i2=0; $i2 <$totSubValue ; $i2++) { 
                  $m->set_data('language_id',$json['language_key'][$i]['language'][$i1]['language_id']);
                  $m->set_data('language_key_id',$language_key_id);
                  $m->set_data('value_name',$subValuAray[$i2]);

                   $aValueSub = array(
                    'language_id'=>$m->get_data('language_id'),
                    'language_key_id'=>$m->get_data('language_key_id'),
                    'value_name'=>$m->get_data('value_name')
                  );

                   $d->insert("language_key_value_master_gatekeeper",$aValueSub);
                }

              }
            }

          } else {
            $oldData=mysqli_fetch_array($qalerady);
            $language_key_id =  $oldData['language_key_id'];

            $totalValues = count($json['language_key'][$i]['language']);

            for ($i1=0; $i1 <$totalValues ; $i1++) { 

              if ($key_type==0) {
                $language_id = $json['language_key'][$i]['language'][$i1]['language_id'];
                $m->set_data('language_id',$json['language_key'][$i]['language'][$i1]['language_id']);
                $m->set_data('language_key_id',$language_key_id);
                $m->set_data('value_name',$json['language_key'][$i]['language'][$i1]['value_name']);
                $aValue = array(
                  'language_id'=>$m->get_data('language_id'),
                  'language_key_id'=>$m->get_data('language_key_id'),
                  'value_name'=>$m->get_data('value_name')
                );

                $d->update("language_key_value_master_gatekeeper",$aValue,"language_key_id='$language_key_id' AND language_id='$language_id'");
              } else {

                $mixValue  = $json['language_key'][$i]['language'][$i1]['value_name'];
                $subValuAray = explode("~", $mixValue);
                $totSubValue = count($subValuAray);

                $language_id = $json['language_key'][$i]['language'][$i1]['language_id']; 
                $olvValueArray = array();
                $ovq = $d->select("language_key_value_master_gatekeeper","language_key_id='$language_key_id' AND language_id='$language_id' ");
                while ($oldData=mysqli_fetch_array($ovq)) {
                  array_push($olvValueArray, $oldData['key_value_id']);
                }
                
                for ($i2=0; $i2 <$totSubValue ; $i2++) { 
                  $key_value_id = $olvValueArray[$i2];
                  $m->set_data('value_name',$subValuAray[$i2]);

                   $aValueSub = array(
                    'value_name'=>$m->get_data('value_name')
                  );

                  $d->update("language_key_value_master_gatekeeper",$aValueSub,"language_key_id='$language_key_id' AND language_id='$language_id' AND key_value_id='$key_value_id'");
                }

              }
            }
          } 

          // print_r($a);
          // echo "<br>";
       } 
     
      $response["message"] = "Guard Language Export Successfully ";
      $response["status"] = "200";
      echo json_encode($response);

    } else {
      $response["message"] = "Something went wrong";
      $response["status"] = "201";
      echo json_encode($response);
    }


         

  } else {
    $response["message"] = "Invalid Request";
      $response["status"] = "500";
      echo json_encode($response);
  }


 ?>
