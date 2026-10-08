<?php
include_once 'lib.php';

if(isset($_POST) && !empty($_POST)){

   $response = array();
   extract(array_map("test_input" , $_POST));
     
     if($_POST['getGreetings']=="getGreetings" ){

        $response["Greetings"] = array();


        $safeIds = array_filter(array_map('intval', explode(',', (string)$seasonal_greet_ids)));
        if (empty($safeIds)) {
            $response["message"] = "Invalid greetings ids.";
            $response["status"] = "201";
            echo json_encode($response);
            exit;
        }
        $ids = implode(',', $safeIds);
        $q = $d->select("seasonal_greet_master","seasonal_greet_id IN ($ids)","");
       

         while ($data=mysqli_fetch_array($q)) {
        
        $seasonal_greet_id=$data['seasonal_greet_id'];

        $Greetings = array();
           
            $Greetings['title']=$data['title'];
            $Greetings['start_date']=$data['start_date'];
            $Greetings['end_date']=$data['end_date'];
            $Greetings['order_date']=$data['order_date'];
            $Greetings['is_expiry']=$data['is_expiry'];
            $Greetings['status']=$data['status'];
            //$Greetings['created_by']=$data['created_by'];

            $Greetings["Greetings_image"] = array();
            $Greetings["Greetings_country"] = array();


            $q1 = $d->select("seasonal_greet_image_master","seasonal_greet_id='$seasonal_greet_id'","");

                 while ($data1=mysqli_fetch_array($q1)){

                $Greetings_image = array();


                $image_url=$base_url."img/promotion/".$data1['cover_image'];
                $image_url1=$base_url."img/promotion/".$data1['background_image'];

                $Greetings_image['cover_img_url']=$image_url;
                $Greetings_image['background_img_url']=$image_url1;

                $Greetings_image['seasonal_greet_id']=$data1['seasonal_greet_id'];
                $Greetings_image['cover_image']=$data1['cover_image'];
                $Greetings_image['background_image']=$data1['background_image'];
                $Greetings_image['page_alignment']=$data1['page_alignment'];
                $Greetings_image['logo_alignment']=$data1['logo_alignment'];
                $Greetings_image['to_text_alignment']=$data1['to_text_alignment'];
                $Greetings_image['from_text_alignment']=$data1['from_text_alignment'];
                $Greetings_image['show_to_name']=$data1['show_to_name'];
                $Greetings_image['to_name_font_color']=$data1['to_name_font_color'];
                $Greetings_image['to_name_font_name']=$data1['to_name_font_name'];
                $Greetings_image['to_name_font_size']=$data1['to_name_font_size'];
                $Greetings_image['show_from_name']=$data1['show_from_name'];
                $Greetings_image['from_name_font_color']=$data1['from_name_font_color'];
                $Greetings_image['from_name_font_name']=$data1['from_name_font_name'];
                $Greetings_image['from_name_font_size']=$data1['from_name_font_size'];
                $Greetings_image['status']=$data1['status'];
                

                        array_push($Greetings["Greetings_image"], $Greetings_image); 
             }


          $q2 = $d->select("seasonal_greet_countries","seasonal_greet_id='$seasonal_greet_id'","");

            while ($data2=mysqli_fetch_array($q2)){

            $Greetings_country = array();
            $Greetings_country['country_id']=$data2['country_id'];
            $Greetings_country['seasonal_greet_id']=$data2['seasonal_greet_id'];
            
            array_push($Greetings["Greetings_country"], $Greetings_country); 
         }


             array_push($response["Greetings"], $Greetings); 
        
}
             

         $response["message"]="Get Success";
         $response["status"]="200";
         echo json_encode($response);


    } else{
      $response["message"]="wrong tag..";
      $response["status"]="201";
      echo json_encode($response);
    }
 
}

