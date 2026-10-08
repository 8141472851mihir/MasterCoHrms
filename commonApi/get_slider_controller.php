<?php
include_once 'lib.php';

if(isset($_POST) && !empty($_POST)){



    if ($key==$keydb) {
        
    $response = array();
    extract(array_map("test_input" , $_POST));
            if($_POST['getSlider']=="getSlider" && filter_var($society_id, FILTER_VALIDATE_INT) == true ){

                $qnotification=$d->select("app_common_slider_master, app_slider_master","app_common_slider_master.app_slider_id=app_slider_master.app_slider_id AND app_common_slider_master.society_id='$society_id' AND app_common_slider_master.status=0","order by RAND()");

                $response["slider"] = array();

                $today= date("Y-m-d");
                $qa=$d->select("festival_master","festival_date='$today'");
                $advData=mysqli_fetch_array($qa);
                if ($advData>0) {
                    if ($advData['festival_view_status']==0) {
                      $festival_view_status='1';
                    } else {
                      $festival_view_status='0';
                    }
                    if ($advData['festival_active_status']==0) {
                      $festival_active_status='1';
                    } else {
                        $festival_active_status='0';
                    }
                $response["view_status"]=$festival_view_status.'';
                $response["active_status"]=$festival_active_status;
                $response["advertisement_url"]=$base_url."img/festival/".$advData['festival_image']; 
                } else {
                $response["view_status"]='0';
                $response["active_status"]='0';
                $response["advertisement_url"]=""; 
                }

                if(mysqli_num_rows($qnotification)>0){

                    while($data_notification=mysqli_fetch_array($qnotification)) {

                        // print_r($data_notification);

                        $slider = array(); 

                        $slider["app_slider_id"]=$data_notification['app_slider_id'];
                        $slider["society_id"]=$data_notification['society_id'];
                        $slider["slider_image_name"]=$base_url."img/sliders/".$data_notification['slider_image_name'];
                        $slider["youtube_url"]=($data_notification['youtube_url']!='' || $data_notification['youtube_url']!=null) ? $data_notification['youtube_url'] : "";
                        $slider["slider_status"]=$data_notification['slider_status'];
                        $slider["page_url"]=$data_notification['page_url'].'';
                        if ($data_notification['page_mobile']!=0) {
                            $slider["page_mobile"]=$data_notification['page_mobile'].'';
                        } else {
                            $slider["page_mobile"]='';

                        }
                        $slider["about_offer"]=$data_notification['about_offer'].'';

                        array_push($response["slider"], $slider); 
                    }

                    $response["message"]="Get slider success.";
                    $response["status"]="200";
                    echo json_encode($response);

                } else{

                    $qdefailt=$d->select("app_common_slider_master, app_slider_master","app_common_slider_master.app_slider_id=app_slider_master.app_slider_id AND app_common_slider_master.default_flag='1' AND app_common_slider_master.status=0","order by RAND()");

                    while($data_notification=mysqli_fetch_array($qdefailt)) {

                        $slider = array(); 

                        $slider["app_slider_id"]=$data_notification['app_slider_id'];
                        $slider["society_id"]=$data_notification['society_id'];
                        $slider["slider_image_name"]=$base_url."img/sliders/".$data_notification['slider_image_name'];
                        $slider["youtube_url"]=$data_notification['youtube_url'];
                        $slider["slider_status"]=$data_notification['slider_status'];
                        $slider["page_url"]=$data_notification['page_url'].'';
                         $slider["page_mobile"]=$data_notification['page_mobile'].'';
                        array_push($response["slider"], $slider); 
                    }

                    $response["message"]="Get slider success.";
                    $response["status"]="200";
                    echo json_encode($response);
                }

            }else{
                $response["message"]="wrong tag.";
                $response["status"]="201";
                echo json_encode($response);

            }

        

    }else{

         $response["message"]="wrong api key.";
        $response["status"]="201";
        echo json_encode($response);

    }

}?>