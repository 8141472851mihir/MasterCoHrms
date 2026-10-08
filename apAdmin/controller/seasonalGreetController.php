<?php 
include '../common/objectController.php';
if(isset($_POST) && !empty($_POST) )//it can be $_GET doesn't matter
{
  if(isset($_POST['addSeasonalGreet'])){
    mysqli_autocommit($con, False);
    $m->set_data('title',ucfirst($_POST['title']));
    $m->set_data('is_expiry',$_POST['is_expiry']);
    $m->set_data('start_date',date("Y-m-d",strtotime($_POST['start_date'])));
    $m->set_data('end_date',date("Y-m-d",strtotime($_POST['end_date'])));
    $m->set_data('order_date',date("Y-m-d",strtotime($_POST['order_date'])));
    $m->set_data('status',$_POST['status']);
    $m->set_data('created_by',$bms_admin_id);
    $created_at = date('Y-m-d H:i:s');
    $m->set_data('created_at',$created_at);
    $a =array(
      'title'=> $m->get_data('title'),
      'is_expiry'=> $m->get_data('is_expiry'),
      'status'=> $m->get_data('status'),
      'start_date'=> $m->get_data('start_date'),
      'end_date'=> $m->get_data('end_date'),
      'order_date'=> $m->get_data('order_date'),
      'created_by'=> $m->get_data('created_by')  ,
      'created_at'=> $m->get_data('created_at') 
    );
    $q=$d->insert("seasonal_greet_master",$a);
    $seasonal_greet_id = $con->insert_id;
    if($q>0)
    {
      for($i2=0; $i2 <count($_POST['country_id']) ; $i2++){ 
        $m->set_data('country_id',$_POST['country_id'][$i2]);
        $m->set_data('seasonal_greet_id', $seasonal_greet_id);
        $a111 = array(
          'country_id'=>$m->get_data('country_id'),
          'seasonal_greet_id'=>$m->get_data('seasonal_greet_id') ,
        );
        $d->insert("seasonal_greet_countries",$a111);
      }
      if(isset($_FILES['background_image']) && !empty($_FILES['background_image']['name'])){
        for($j=0;$j<=$counter; $j++)
        {
          if (isset($_FILES['background_image']['tmp_name'][$j]) && $_FILES['background_image']['tmp_name'][$j] != "") {
            $file_background_image = $_FILES['background_image']['tmp_name'][$j];
            if (file_exists($file_background_image)) {
              $acceptable = array("jpeg","jpg","png","gif");
              $extId = pathinfo($_FILES['background_image']['name'][$j], PATHINFO_EXTENSION);
              $dirPath = "../../img/promotion/";
              $maxsize    = 3000000;
              if (in_array($extId, $acceptable) && (!empty($_FILES["background_image"]["type"][$j]))) {
                $temp = explode(".", $_FILES["background_image"]["name"][$j]);
                $background_image = 'sg_'.rand() . '.' . end($temp);
                $destinationPath = $dirPath . $background_image;
                $d->resizeImage($file_background_image, $destinationPath, 1280, 720, $extId);
                $background_image = $background_image;
                $show_to_name_val=$show_to_name[$j];
                $show_from_name_val=$show_from_name[$j];
                $status_val=$other_status[$j];
                $m->set_data('seasonal_greet_id',$seasonal_greet_id);
                $m->set_data('cover_image',$background_image);
                $m->set_data('background_image',$background_image);
                $m->set_data('page_alignment',"Top Top From");
                $m->set_data('logo_alignment',"Bottom");
                $m->set_data('show_to_name',$show_to_name_val);
                $m->set_data('show_from_name',$show_from_name_val);
                $m->set_data('status',$status_val);
                $m->set_data('to_name_font_color',"#3C3C3C");
                $m->set_data('to_text_alignment',"Bottom");
                $m->set_data('to_name_font_name',"gotham_black");
                $m->set_data('to_name_font_size',"Medium");
                $m->set_data('from_name_font_color',"#3C3C3C");
                $m->set_data('from_text_alignment',"Start");
                $m->set_data('from_name_font_name',"gotham_black"); 
                $m->set_data('from_name_font_size',"Medium"); 
                $m->set_data('created_by',$bms_admin_id);
                $m->set_data('created_at',date('Y-m-d H:i:s'));
                $a =array(
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
                  'created_at' =>  $m->get_data('created_at')
                );
                $im=$d->insert("seasonal_greet_image_master",$a);
              } else {
                $_SESSION['msg1'] = "Invalid Image.";
                header("location:../seasonalGreetList");
                exit();
              }
            } else {
              $_SESSION['msg1'] = "Invalid Image.";
              header("location:../seasonalGreetList");
              exit();
            }
          }
        }
      }
      $_SESSION['msg']=ucfirst($title)." Seasonal Greeting Added";
      $d->insert_log("","0","$bms_admin_id","$created_by",$_SESSION['msg']);
      mysqli_commit($con);
      header("location:../seasonalGreetList");
    }else{
      $_SESSION['msg1']="Something Wrong";
      header("location:../seasonalGreetList");
    } 

  }else if(isset($_POST['addSeasonalGreetOld'])){
    $m->set_data('title',ucfirst($_POST['title']));
    $m->set_data('is_expiry',$_POST['is_expiry']);
    $m->set_data('start_date',date("Y-m-d",strtotime($_POST['start_date'])));
    $m->set_data('end_date',date("Y-m-d",strtotime($_POST['end_date'])));
    $m->set_data('order_date',date("Y-m-d",strtotime($_POST['order_date'])));
    $m->set_data('status',$_POST['status']);
    $m->set_data('created_by',$bms_admin_id);
    $created_at = date('Y-m-d H:i:s');
    $m->set_data('created_at',$created_at);
    $a =array(
      'title'=> $m->get_data('title'),
      'is_expiry'=> $m->get_data('is_expiry'),
      'status'=> $m->get_data('status'),
      'start_date'=> $m->get_data('start_date'),
      'end_date'=> $m->get_data('end_date'),
      'order_date'=> $m->get_data('order_date'),
      'created_by'=> $m->get_data('created_by')  ,
      'created_at'=> $m->get_data('created_at') 
    );
    $q=$d->insert("seasonal_greet_master",$a);
    $seasonal_greet_id = $con->insert_id;
    if($q>0)
    {
      for($i2=0; $i2 <count($_POST['country_id']) ; $i2++)
      { 
        $m->set_data('country_id',$_POST['country_id'][$i2]);
        $m->set_data('seasonal_greet_id', $seasonal_greet_id);
        $a111 = array(
          'country_id'=>$m->get_data('country_id'),
          'seasonal_greet_id'=>$m->get_data('seasonal_greet_id') ,
        );
        $d->insert("seasonal_greet_countries",$a111);
      }
      if(isset($_FILES['background_image']) && $_FILES['background_image']['name'][0]!=""){
        for($j=0;$j<count($_FILES['background_image']['tmp_name']); $j++)
        {
          $file_background_image = $_FILES['background_image']['tmp_name'][$j];
          if (file_exists($file_background_image)) {
            $acceptable = array("jpeg","jpg","png","gif");
            $extId = pathinfo($_FILES['background_image']['name'][$j], PATHINFO_EXTENSION);
            $dirPath = "../../img/promotion/";
            $maxsize    = 3000000;
            if (in_array($extId, $acceptable) && (!empty($_FILES["background_image"]["type"][$j]))) {
              $temp = explode(".", $_FILES["background_image"]["name"][$j]);
              $background_image = 'sg_'.rand() . '.' . end($temp);
              $destinationPath = $dirPath . $background_image;
              $d->resizeImage($file_background_image, $destinationPath, 1280, 720, $extId);
              $background_image = $background_image;

              $m->set_data('seasonal_greet_id',$seasonal_greet_id);
              $m->set_data('cover_image',ucfirst($background_image));
              $m->set_data('background_image',ucfirst($background_image));
              $m->set_data('page_alignment',"Top Top From");
              $m->set_data('logo_alignment',"Bottom");
              $m->set_data('show_to_name',"No");
              $m->set_data('to_name_font_color',"#3C3C3C");
              $m->set_data('to_text_alignment',"Bottom");
              $m->set_data('to_name_font_name',"gotham_black");
              $m->set_data('to_name_font_size',"Medium");
              $m->set_data('show_from_name',"Yes");
              $m->set_data('from_name_font_color',"#3C3C3C");
              $m->set_data('from_text_alignment',"Start");
              $m->set_data('from_name_font_name',"gotham_black"); 
              $m->set_data('from_name_font_size',"Medium"); 
              $m->set_data('status',"Active"); 
              $m->set_data('created_by',$bms_admin_id);
              $m->set_data('created_at',date('Y-m-d H:i:s'));
              $a =array(
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
                'created_at' =>  $m->get_data('created_at')
              );
              $im=$d->insert("seasonal_greet_image_master",$a);
            } else {
              $_SESSION['msg1'] = "Invalid Image.";
              header("location:../seasonalGreetList");
              exit();
            }
          } else {
            $_SESSION['msg1'] = "Invalid Image.";
            header("location:../seasonalGreetList");
            exit();
          }
          
        }
      }
      $_SESSION['msg']=ucfirst($title)." Seasonal Greeting Added";
      $d->insert_log("","0","$bms_admin_id","$created_by",$_SESSION['msg']);
      header("location:../seasonalGreetList");
    }else{
      $_SESSION['msg1']="Something Wrong";
      header("location:../seasonalGreetList");
    } 

  }else if(isset($_POST['updateSeasonalGreet']))
  {
    $m->set_data('title',ucfirst($_POST['title']));
    $m->set_data('is_expiry',$_POST['is_expiry']);
    $m->set_data('start_date',date("Y-m-d",strtotime($_POST['start_date'])));
    $m->set_data('end_date',date("Y-m-d",strtotime($_POST['end_date'])));
    $m->set_data('order_date',date("Y-m-d",strtotime($_POST['order_date'])));
    $m->set_data('status',$_POST['status']);
    $m->set_data('created_by',$bms_admin_id);
    $created_at = date('Y-m-d H:i:s');
    $a =array(
      'title'=> $m->get_data('title'),
      'is_expiry'=> $m->get_data('is_expiry'),
      'status'=> $m->get_data('status'),
      'start_date'=> $m->get_data('start_date'),
      'end_date'=> $m->get_data('end_date'),
      'order_date'=> $m->get_data('order_date'),
      'created_by'=> $m->get_data('created_by')  
    );
    $seasonal_greet_id = $d->sanitizeActionIdAsInt($_POST['seasonal_greet_id'] ?? 0);
    $q=$d->update("seasonal_greet_master",$a,"seasonal_greet_id ='$seasonal_greet_id'");
    if($q>0)
    {
      $d->delete("seasonal_greet_countries","seasonal_greet_id='$seasonal_greet_id'");
      for ($i2=0; $i2 <count($_POST['country_id']) ; $i2++) { 
        $m->set_data('country_id',$_POST['country_id'][$i2]);
        $m->set_data('seasonal_greet_id', $seasonal_greet_id);
        $a111 = array(
          'country_id'=>$m->get_data('country_id'),
          'seasonal_greet_id'=>$m->get_data('seasonal_greet_id') ,
        );
        $d->insert("seasonal_greet_countries",$a111);
      }
      $_SESSION['msg']=ucfirst($title)." Seasonal Greeting Updated";
      $d->insert_log("","0","$bms_admin_id","$_POST[created_by]",$_SESSION['msg']);
      header("location:../seasonalGreetList");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../seasonalGreetList");
    }
  }else  if(isset($_POST['copySeasonalGreet']))
  {
    $copy_seasonal_greet_id = $_POST['copy_seasonal_greet_id'];
    $m->set_data('title',ucfirst($_POST['title']));
    $m->set_data('is_expiry',$_POST['is_expiry']);
    $m->set_data('start_date',date("Y-m-d",strtotime($_POST['start_date'])));
    $m->set_data('end_date',date("Y-m-d",strtotime($_POST['end_date'])));
    $m->set_data('order_date',date("Y-m-d",strtotime($_POST['order_date'])));
    $m->set_data('status',$_POST['status']);
    $m->set_data('created_by',$bms_admin_id);
    $created_at = date('Y-m-d H:i:s');
    $m->set_data('created_at',$created_at);
    $a =array(
      'title'=> $m->get_data('title'),
      'is_expiry'=> $m->get_data('is_expiry'),
      'status'=> $m->get_data('status'),
      'start_date'=> $m->get_data('start_date'),
      'end_date'=> $m->get_data('end_date'),
      'order_date'=> $m->get_data('order_date'),
      'created_by'=> $m->get_data('created_by')  ,
      'created_at'=> $m->get_data('created_at') 
    );
    $q=$d->insert("seasonal_greet_master",$a);
    $last_seasonal_greet_id = $con->insert_id;
    if($q>0)
    {
      for($i2=0; $i2 <count($_POST['country_id']) ; $i2++)
      { 
        $m->set_data('country_id',$_POST['country_id'][$i2]);
        $m->set_data('seasonal_greet_id', $last_seasonal_greet_id);
        $a111 = array(
          'country_id'=>$m->get_data('country_id'),
          'seasonal_greet_id'=>$m->get_data('seasonal_greet_id') ,
        );
        $d->insert("seasonal_greet_countries",$a111);
      }
      $img = $d->select("seasonal_greet_image_master","seasonal_greet_id='$copy_seasonal_greet_id'");
      while ($data = mysqli_fetch_array($img))
      {
        $m->set_data('seasonal_greet_id',$last_seasonal_greet_id);
        $m->set_data('cover_image',$data['cover_image']);
        $m->set_data('background_image',$data['background_image']);
        $m->set_data('page_alignment',"Top Top From");
        $m->set_data('logo_alignment',"Bottom");
        $m->set_data('show_to_name',"No");
        $m->set_data('to_name_font_color',"#3C3C3C");
        $m->set_data('to_text_alignment',"Bottom");
        $m->set_data('to_name_font_name',"gotham_black");
        $m->set_data('to_name_font_size',"Medium");
        $m->set_data('show_from_name',"Yes");
        $m->set_data('from_name_font_color',"#3C3C3C");
        $m->set_data('from_text_alignment',"Start");
        $m->set_data('from_name_font_name',"gotham_black"); 
        $m->set_data('from_name_font_size',"Medium"); 
        $m->set_data('status',"Active"); 
        $m->set_data('created_by',$bms_admin_id);
        $m->set_data('created_at',date('Y-m-d H:i:s'));
        $a =array(
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
          'created_at' =>  $m->get_data('created_at')
        );
        $im=$d->insert("seasonal_greet_image_master",$a);
      }
      $_SESSION['msg']=ucfirst($title)." Seasonal Greeting Copyed.";
      $d->insert_log("","0","$bms_admin_id","$created_by",$_SESSION['msg']);
      header("location:../seasonalGreetList");
    }else{
      $_SESSION['msg1']="Something Wrong";
      header("location:../seasonalGreetList");
    } 
  }else if(isset($_POST['addSeasonalGreetImage']))
  {
    mysqli_autocommit($con, False);
    if(isset($_FILES['background_image']) && !empty($_FILES['background_image']['name'])){
      for($j=0;$j<=$counter; $j++)
      {
        if (isset($_FILES['background_image']['tmp_name'][$j]) && $_FILES['background_image']['tmp_name'][$j] != "") {
          $file_background_image = $_FILES['background_image']['tmp_name'][$j];
          if (file_exists($file_background_image)) {
            $acceptable = array("jpeg","jpg","png","gif");
            $extId = pathinfo($_FILES['background_image']['name'][$j], PATHINFO_EXTENSION);
            $dirPath = "../../img/promotion/";
            $maxsize    = 3000000;
            if (in_array($extId, $acceptable) && (!empty($_FILES["background_image"]["type"][$j]))) {
              $temp = explode(".", $_FILES["background_image"]["name"][$j]);
              $background_image = 'sg_'.rand() . '.' . end($temp);
              $destinationPath = $dirPath . $background_image;
              $d->resizeImage($file_background_image, $destinationPath, 1280, 720, $extId);
              $background_image = $background_image;
              $show_to_name_val=$show_to_name[$j];
              $show_from_name_val=$show_from_name[$j];
              $status_val=$other_status[$j];
              $m->set_data('seasonal_greet_id',$_POST['seasonal_greet_id']);
              $m->set_data('cover_image',$background_image);
              $m->set_data('background_image',$background_image);
              $m->set_data('page_alignment',"Top Top From");
              $m->set_data('logo_alignment',"Bottom");
              $m->set_data('show_to_name',$show_to_name_val);
              $m->set_data('to_name_font_color',"#3C3C3C");
              $m->set_data('to_text_alignment',"Bottom");
              $m->set_data('to_name_font_name',"gotham_black");
              $m->set_data('to_name_font_size',"Medium");
              $m->set_data('show_from_name',$show_from_name_val);
              $m->set_data('from_name_font_color',"#3C3C3C");
              $m->set_data('from_text_alignment',"Start");
              $m->set_data('from_name_font_name',"gotham_black"); 
              $m->set_data('from_name_font_size',"Medium"); 
              $m->set_data('status',$status_val); 
              $m->set_data('created_by',$bms_admin_id);
              $m->set_data('created_at',date('Y-m-d H:i:s'));
              $a =array(
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
                'created_at' =>  $m->get_data('created_at')
              );
              $q=$d->insert("seasonal_greet_image_master",$a);
              if($q>0)
              {
                $_SESSION['msg']="Seasonal Greeting Image Added";
                $d->insert_log("","0","$bms_admin_id","$_POST[created_by]",$_SESSION['msg']);
                mysqli_commit($con);
                header("location:../manageSeasonalGreet?seasonal_greet_id=$_POST[seasonal_greet_id]");
              } else {
                unlink("../../img/promotion/".$background_image);
                $_SESSION['msg1']="Something Wrong !!!";
                header("location:../manageSeasonalGreet?seasonal_greet_id=$_POST[seasonal_greet_id]");
              }
            }else {
              $_SESSION['msg1'] = "Invalid Image.";
              header("location:../seasonalGreetImage");
              exit();
            }
          }
        }
      }
    } else {
      $_SESSION['msg1'] = "Invalid Image.";
      header("location:../seasonalGreetImage");
      exit();
    }
  }else if(isset($_POST['updateSeasonalGreetImage']))
  {
    $file_background_image = $_FILES['background_image']['tmp_name'];
    if (file_exists($file_background_image)) {
      $acceptable = array("jpeg","jpg","png","gif");
      $extId = pathinfo($_FILES['background_image']['name'], PATHINFO_EXTENSION);
      $dirPath = "../../img/promotion/";
      $maxsize    = 3000000;
      if (in_array($extId, $acceptable) && (!empty($_FILES["background_image"]["type"]))) {
        $temp = explode(".", $_FILES["background_image"]["name"]);
        $background_image = 'sg_'.rand() . '.' . end($temp);
        $destinationPath = $dirPath . $background_image;
        $d->resizeImage($file_background_image, $destinationPath, 1280, 720, $extId);
        $background_image = $background_image;
        $m->set_data('background_image',$background_image);
        $a =array(
          'background_image'=> $m->get_data('background_image')
        );
      }
    }
    
    $m->set_data('show_to_name',$_POST['show_to_name']);
    $m->set_data('show_from_name',$_POST['show_from_name']);
    $m->set_data('status',$_POST['status']); 
    $m->set_data('created_by',$bms_admin_id);
    $a['show_to_name']= $m->get_data('show_to_name');
    $a['show_from_name']= $m->get_data('show_from_name');
    $a['status']= $m->get_data('status');
    $a['created_by']= $m->get_data('created_by');
    
    $q=$d->update("seasonal_greet_image_master",$a,"seasonal_greet_image_id = '$seasonal_greet_image_id'");
    if($q>0) {
      $_SESSION['msg']="Seasonal Greeting Image Updated";
      $d->insert_log("","0","$bms_admin_id","$created_by",$_SESSION['msg']);
      header("location:../manageSeasonalGreet?seasonal_greet_id=$_POST[seasonal_greet_id]");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../manageSeasonalGreet?seasonal_greet_id=$_POST[seasonal_greet_id]");
    }
  }
  else if(isset($_POST['delete_seasonal_greet_id']))
  {
    $delete_seasonal_greet_id = $d->sanitizeActionIdAsInt($_POST['delete_seasonal_greet_id']);
    $adm_data=$d->selectRow("title","seasonal_greet_master"," seasonal_greet_id='$delete_seasonal_greet_id'");
    $data_q=mysqli_fetch_array($adm_data);
    $q=$d->delete("seasonal_greet_master","seasonal_greet_id='$delete_seasonal_greet_id'  ");
    if($q>0 )
    {
      $qqq=$d->select("seasonal_greet_image_master","seasonal_greet_id='$delete_seasonal_greet_id' ");
      while($iData=mysqli_fetch_array($qqq))
      {
        $path = "../../img/promotion/".$iData['cover_image'];
        unlink($path);
        $path2 = "../../img/promotion/".$iData['background_image'];
        unlink($path2);
        $q2=$d->delete("seasonal_greet_image_master","seasonal_greet_id='$delete_seasonal_greet_id'  ");
      }
      $_SESSION['msg']=$data_q['title']." Seasonal Greeting Deleted";
      $d->insert_log("","0","$bms_admin_id","$created_by",$_SESSION['msg']);
      header("location:../seasonalGreetList");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../seasonalGreetList");
    }
  }  

  else if(isset($_POST['delete_seasonal_greet_image_id']))
  {
    $delete_seasonal_greet_image_id = $d->sanitizeActionIdAsInt($_POST['delete_seasonal_greet_image_id']);
    $delete_seasonal_greet_id = $d->sanitizeActionIdAsInt($_POST['delete_seasonal_greet_id'] ?? 0);
    $seasonal_greet_id = $d->sanitizeActionIdAsInt($_POST['seasonal_greet_id'] ?? 0);
    $adm_data=$d->selectRow("*","seasonal_greet_image_master"," seasonal_greet_image_id='$delete_seasonal_greet_image_id'");
    $data_q=mysqli_fetch_array($adm_data);
    $q=$d->delete("seasonal_greet_image_master","seasonal_greet_image_id='$delete_seasonal_greet_image_id'");
    if($q>0)
    {
      $path = "../../img/promotion/".$data_q['cover_image'];
      unlink($path);
      $path2 = $abspath."../../img/promotion/".$data_q['background_image'];
      unlink($path2);
      $q2=$d->delete("seasonal_greet_image_master","seasonal_greet_id='$delete_seasonal_greet_id'");
      $_SESSION['msg']="Seasonal Image Deleted Successfully";
      $d->insert_log("","0","$bms_admin_id","$created_by",$_SESSION['msg']);
      header("location:../manageSeasonalGreet?seasonal_greet_id=$seasonal_greet_id");
    }else{
      $_SESSION['msg1']="Something Wrong";
      header("location:../manageSeasonalGreet?seasonal_greet_id=$seasonal_greet_id");
    }
  }else{
    header('location:../login');
  }

}else{
  header('location:../login');
}
?>