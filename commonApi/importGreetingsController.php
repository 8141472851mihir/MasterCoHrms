<?php 
include_once 'lib.php';
if (isset($_POST) && !empty($_POST)) {
  extract(array_map("test_input", $_POST));
  if (isset($exportGreetings)) {
    $response = array();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$retun_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,"getGreetings=getGreetings&seasonal_greet_ids=$seasonal_greet_ids");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    curl_close ($ch);
    $json = json_decode($server_output, true);

    $totalKeysFound = count($json['Greetings']);

    for ($i=0; $i < $totalKeysFound; $i++) { 

      $title = $json['Greetings'][$i]['title'];
      $start_date = $json['Greetings'][$i]['start_date'];
      $end_date = $json['Greetings'][$i]['end_date'];
      $order_date = $json['Greetings'][$i]['order_date'];
      $is_expiry = $json['Greetings'][$i]['is_expiry'];
      $status = $json['Greetings'][$i]['status'];

      $m->set_data('title',$title);
      $m->set_data('start_date',$start_date);
      $m->set_data('end_date',$end_date);
      $m->set_data('order_date',$order_date);
      $m->set_data('is_expiry',$is_expiry);
      $m->set_data('status',$status);
      $m->set_data('created_by',$_COOKIE['bms_admin_id']);
      $m->set_data('created_date',date('Y-m-d H:i:s'));

      $a = array('title'=>$m->get_data('title'),
        'start_date'=>$m->get_data('start_date'),
        'end_date'=>$m->get_data('end_date'),
        'order_date'=>$m->get_data('order_date'),
        'is_expiry'=>$m->get_data('is_expiry'),
        'status'=>$m->get_data('status'),
        'created_by'=>$m->get_data('created_by'),
        'created_at'=>$m->get_data('created_date'));

      $title = str_replace("'", "\'", $title);
      
      $qalerady=$d->selectRow("title","seasonal_greet_master","title='$title' AND start_date='$start_date'");
      if (mysqli_num_rows($qalerady)==0) {
        $q=$d->insert("seasonal_greet_master",$a);
        $seasonal_greet_id =  $con->insert_id;
        $totalValues = count($json['Greetings'][$i]['Greetings_image']);
        $totalCountry = count($json['Greetings'][$i]['Greetings_country']);
        for ($i2=0; $i2 < $totalCountry; $i2++) { 
          $country_id = $json['Greetings'][$i]['Greetings_country'][$i2]['country_id'];
          $m->set_data('country_id',$country_id);
          $m->set_data('seasonal_greet_id',$seasonal_greet_id);
          $aCountry = array(
            'country_id'=> $m->get_data('country_id'),
            'seasonal_greet_id'=> $m->get_data('seasonal_greet_id'),
          );
          $d->insert("seasonal_greet_countries",$aCountry);
        }
        for ($i1=0; $i1 < $totalValues; $i1++) { 

          $cover_img_url = $json['Greetings'][$i]['Greetings_image'][$i1]['cover_img_url'];
          $background_img_url = $json['Greetings'][$i]['Greetings_image'][$i1]['background_img_url'];

          $cover_image = $json['Greetings'][$i]['Greetings_image'][$i1]['cover_image'];
          $background_image = $json['Greetings'][$i]['Greetings_image'][$i1]['background_image'];
          $page_alignment = $json['Greetings'][$i]['Greetings_image'][$i1]['page_alignment'];
          $logo_alignment = $json['Greetings'][$i]['Greetings_image'][$i1]['logo_alignment'];
          $to_text_alignment = $json['Greetings'][$i]['Greetings_image'][$i1]['to_text_alignment'];
          $from_text_alignment = $json['Greetings'][$i]['Greetings_image'][$i1]['from_text_alignment'];
          $show_to_name = $json['Greetings'][$i]['Greetings_image'][$i1]['show_to_name'];
          $to_name_font_color = $json['Greetings'][$i]['Greetings_image'][$i1]['to_name_font_color'];
          $to_name_font_name = $json['Greetings'][$i]['Greetings_image'][$i1]['to_name_font_name'];
          $to_name_font_size = $json['Greetings'][$i]['Greetings_image'][$i1]['to_name_font_size'];
          $show_from_name = $json['Greetings'][$i]['Greetings_image'][$i1]['show_from_name'];
          $from_name_font_color = $json['Greetings'][$i]['Greetings_image'][$i1]['from_name_font_color'];
          $from_name_font_name = $json['Greetings'][$i]['Greetings_image'][$i1]['from_name_font_name'];
          $from_name_font_size = $json['Greetings'][$i]['Greetings_image'][$i1]['from_name_font_size'];
          $status = $json['Greetings'][$i]['Greetings_image'][$i1]['status'];

          $file = $cover_img_url;
          $file1 = $background_img_url;

          $data = file_get_contents($file);
          $data1 = file_get_contents($file1);
          $new = '../img/promotion/'.$cover_image;
          $new1 = '../img/promotion/'.$background_image;
          file_put_contents($new,$data);
          file_put_contents($new1,$data1);

          $m->set_data('seasonal_greet_id',$seasonal_greet_id);
          $m->set_data('cover_image',$json['Greetings'][$i]['Greetings_image'][$i1]['cover_image']);
          $m->set_data('background_image',$json['Greetings'][$i]['Greetings_image'][$i1]['background_image']);
          $m->set_data('page_alignment',$json['Greetings'][$i]['Greetings_image'][$i1]['page_alignment']);
          $m->set_data('logo_alignment',$json['Greetings'][$i]['Greetings_image'][$i1]['logo_alignment']);
          $m->set_data('to_text_alignment',$json['Greetings'][$i]['Greetings_image'][$i1]['to_text_alignment']);
          $m->set_data('from_text_alignment',$json['Greetings'][$i]['Greetings_image'][$i1]['from_text_alignment']);
          $m->set_data('show_to_name',$json['Greetings'][$i]['Greetings_image'][$i1]['show_to_name']);
          $m->set_data('to_name_font_color',$json['Greetings'][$i]['Greetings_image'][$i1]['to_name_font_color']);
          $m->set_data('to_name_font_name',$json['Greetings'][$i]['Greetings_image'][$i1]['to_name_font_name']);
          $m->set_data('to_name_font_size',$json['Greetings'][$i]['Greetings_image'][$i1]['to_name_font_size']);
          $m->set_data('show_from_name',$json['Greetings'][$i]['Greetings_image'][$i1]['show_from_name']);
          $m->set_data('from_name_font_color',$json['Greetings'][$i]['Greetings_image'][$i1]['from_name_font_color']);
          $m->set_data('from_name_font_name',$json['Greetings'][$i]['Greetings_image'][$i1]['from_name_font_name']);
          $m->set_data('from_name_font_size',$json['Greetings'][$i]['Greetings_image'][$i1]['from_name_font_size']);
          $m->set_data('status',$json['Greetings'][$i]['Greetings_image'][$i1]['status']);
          $m->set_data('created_by',$_COOKIE['bms_admin_id']);
          $m->set_data('created_date',date('Y-m-d H:i:s'));

          $a1 = array(
            'seasonal_greet_id'=> $m->get_data('seasonal_greet_id'),
            'cover_image'=> $m->get_data('cover_image'),
            'background_image'=> $m->get_data('background_image'),
            'page_alignment'=> $m->get_data('page_alignment'),
            'logo_alignment'=> $m->get_data('logo_alignment'),
            'to_text_alignment'=> $m->get_data('to_text_alignment'),
            'from_text_alignment'=> $m->get_data('from_text_alignment'),
            'show_to_name'=> $m->get_data('show_to_name'),
            'to_name_font_color'=> $m->get_data('to_name_font_color'),
            'to_name_font_name'=> $m->get_data('to_name_font_name'),
            'to_name_font_size'=> $m->get_data('to_name_font_size'),
            'show_from_name'=> $m->get_data('show_from_name'),
            'from_name_font_color'=> $m->get_data('from_name_font_color'),
            'from_name_font_name'=> $m->get_data('from_name_font_name'),
            'from_name_font_size' => $m->get_data('from_name_font_size'),
            'status'=> $m->get_data('status'),
            'created_by'=> $m->get_data('created_by'),
            'created_at' =>  $m->get_data('created_at'));

          $d->insert("seasonal_greet_image_master",$a1);

        }
      }
    }

    $response["message"] = "Greetings Export Successfully";
    $response["status"] = "200";
    echo json_encode($response);

  }else {
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




