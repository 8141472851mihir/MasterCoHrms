<?php
include_once 'lib.php';

if(isset($_POST) && !empty($_POST)){

	$response = array();
	extract(array_map("test_input" , $_POST));
    $today  = date("Y-m-d");
    if($_POST['getCompanyBanner']=="getCompanyBanner" && filter_var($society_id, FILTER_VALIDATE_INT) == true  && filter_var($admin_id, FILTER_VALIDATE_INT) == true){

           
       $qnotification=$d->select("app_common_slider_master, app_slider_master","app_common_slider_master.app_slider_id=app_slider_master.app_slider_id AND app_common_slider_master.society_id='$society_id' AND app_common_slider_master.status=0 AND app_common_slider_master.added_by=1","order by RAND()");


            if(mysqli_num_rows($qnotification)>0){
                $response["slider"] = array();
                while($data_notification=mysqli_fetch_array($qnotification)) {

                    // print_r($data_notification);

                    $slider = array(); 

                    $slider["app_slider_id"]=$data_notification['app_slider_id'];
                    $slider["society_id"]=$data_notification['society_id'];
                    $slider["slider_image_name"]=$base_url."img/sliders/".$data_notification['slider_image_name'];
                    $slider["youtube_url"]=($data_notification['youtube_url']!='' || $data_notification['youtube_url']!=null) ? $data_notification['youtube_url'] : "";
                    $slider["slider_status"]=$data_notification['slider_status'];
                    $slider["added_by"]=$data_notification['added_by'];
                    $slider["added_by_id"]=$data_notification['added_by_id'];
                    $slider["page_url"]=$data_notification['page_url'].'';
                    if ($data_notification['page_mobile']!=0) {
                        $slider["page_mobile"]=$data_notification['page_mobile'].'';
                    } else {
                        $slider["page_mobile"]='';
                    }
                    $slider["date_view"]="";
                    $slider["about_offer"]=$data_notification['about_offer'].'';

                    array_push($response["slider"], $slider); 
                }

                $response["message"]="Banner Availble.";
                $response["status"]="200";
                echo json_encode($response);

            }else{
            $response["message"]="No Banner Availble.";
            $response["status"]="201";
            echo json_encode($response);

            }
    }else if(isset($_POST['addNewBanner'])  && filter_var($society_id, FILTER_VALIDATE_INT) == true  && filter_var($admin_id, FILTER_VALIDATE_INT) == true ) {

        
        $img = '../img/sliders/'.$slider_image;
        file_put_contents($img, file_get_contents($slider_image_url));

        $m->set_data('society_id', $society_id);
        $m->set_data('slider_image_name', $slider_image);
        $m->set_data('youtube_url', $youtube_url);
        $m->set_data('page_url', $page_url);
        $m->set_data('page_mobile', $page_mobile);
        $m->set_data('about_offer', $about_offer);
                    
        $a = array(
          'society_id' => 0,
          'slider_image_name' => $m->get_data('slider_image_name'),
          'youtube_url' => $m->get_data('youtube_url'),
          'page_url' => $m->get_data('page_url'),
          'page_mobile' => $m->get_data('page_mobile'),
          'about_offer' => $m->get_data('about_offer'),
        );
        
        $q = $d->insert("app_slider_master", $a);
        $slider_id = $con->insert_id;

        if ($q==true) {
            
            $m->set_data('app_slider_id',$slider_id);
            $m->set_data('society_id',$society_id);

            $a111 = array(
              'app_slider_id'=>$m->get_data('app_slider_id'),
              'society_id'=>$m->get_data('society_id'),
              'added_by_id'=>$admin_id,
              'added_by'=>1,
            );

            $d->insert("app_common_slider_master",$a111);

            $response["message"]="Banner Added Successfully";
            $response["status"]="200";
            echo json_encode($response);
        } else {
            $response["message"]="Something went wrong.";
            $response["status"]="201";
            echo json_encode($response);
        }
          
     
    } else if(isset($_POST['deleteSliderImage'])  && filter_var($society_id, FILTER_VALIDATE_INT) == true  && filter_var($admin_id, FILTER_VALIDATE_INT) == true  && filter_var($app_slider_id, FILTER_VALIDATE_INT) == true ) {

        $totalAssign = $d->count_data_direct("society_id","app_common_slider_master","app_slider_id='$app_slider_id' AND society_id!='$society_id'"); 

        $q=$d->delete("app_common_slider_master","app_slider_id='$app_slider_id' AND society_id='$society_id'");
        if ($totalAssign<1) {
            $d->delete("app_slider_master","app_slider_id='$app_slider_id' ");
        }

        $response["message"]="Banner Deleted Successfully";
        $response["status"]="200";
        echo json_encode($response);
        exit();
    }else{
      $response["message"]="wrong tag";
      $response["status"]="201";
      echo json_encode($response);
    }
  
        
}
