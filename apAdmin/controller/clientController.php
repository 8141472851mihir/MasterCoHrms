<?php
include '../common/objectController.php';

if(isset($_POST) && !empty($_POST))
{
  if(isset($addClient))
  {
    if($client_name == "" || $url == "" || $order_no == "" || $order_no == 0)
    {
      $_SESSION['msg1']="Please fill all mandatory fields!";
      header("location:../manageClient");exit;
    }
    $file_image = $_FILES['image']['tmp_name'];
    if (file_exists($file_image)) {
      $acceptable = array("jpeg","jpg","png");
      $extId = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
      $dirPath = "../../img/clients_image/";
      if (in_array($extId, $acceptable) && (!empty($_FILES["image"]["type"]))) {
        $temp = explode(".", $_FILES["image"]["name"]);
        $user_name = str_replace(' ', '_', $client_name);
        $image = $user_name.'_'.round(microtime(true)) . '.' . end($temp);
        $destinationPath = $dirPath . $image;
        $d->resizeImage($file_image, $destinationPath, 1280, 720, $extId);
      }else{
        $_SESSION['msg1'] = "Invalid Photo";
        header("location:../manageClient");
        exit();
      }
    }else {
      $_SESSION['msg1'] = "Image not selected";
      header("location:../manageClient");
      exit();
    }
    $m->set_data("client_name",test_input($client_name));
    $m->set_data("url",test_input($url));
    $m->set_data("order_no",$_POST['order_no']);
    $m->set_data("image",$image);
    $a = array(
      'client_name'=>$m->get_data('client_name'),
      'url'=>$m->get_data('url'),
      'order_no'=>$m->get_data('order_no'),
      'image'=>$m->get_data('image'),
      'created_by'=>$_COOKIE['admin_id'],
      'created_date'=>date("Y-m-d H:i:s")
    );
    $q = $d->insert("clients_master",$a);
    if($q)
    {
      $_SESSION['msg']="Client successfully added.";
      header("location:../manageClient");
    }
    else
    {
      $_SESSION['msg1']="Something went wrong!";
      header("location:../manageClient");
    }
  }
  elseif(isset($getClientDetails))
  {
    $q = $d->select("clients_master","client_id = '$client_id'");
    $data = $q->fetch_assoc();
    echo json_encode($data);exit;
  }
  elseif(isset($editClient))
  {
    $file_image = $_FILES['image']['tmp_name'];
    if (file_exists($file_image)) {
      $acceptable = array("jpeg","jpg","png");
      $extId = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
      $dirPath = "../../img/clients_image/";
      if (in_array($extId, $acceptable) && (!empty($_FILES["image"]["type"]))) {
        $temp = explode(".", $_FILES["image"]["name"]);
        $user_name = str_replace(' ', '_', $client_name);
        $image = $user_name.'_'.round(microtime(true)) . '.' . end($temp);
        $destinationPath = $dirPath . $image;
        unlink("../img/clients_image/".$old_image);
        $d->resizeImage($file_image, $destinationPath, 1280, 720, $extId);
      }else{
        $_SESSION['msg1'] = "Invalid Photo";
        header("location:../manageClient");
        exit();
      }
    }else {
      $image = $old_image;
    }
    
    $m->set_data("client_name",test_input($client_name));
    $m->set_data("url",test_input($url));
    $m->set_data("order_no",$_POST['order_no']);
    $m->set_data("image",$image);
    $a = array(
      'client_name'=>$m->get_data('client_name'),
      'url'=>$m->get_data('url'),
      'order_no'=>$m->get_data('order_no'),
      'image'=>$m->get_data('image'),
      'modified_by'=>$_COOKIE['admin_id'],
      'modified_date'=>date("Y-m-d H:i:s")
    );
    $q = $d->update("clients_master",$a,"client_id = '$client_id'");
    if($q)
    {
      $_SESSION['msg']="Client successfully updated.";
      header("location:../manageClient");
    }
    else
    {
      $_SESSION['msg']="Something went wrong!";
      header("location:../manageClient");
    }
  }
  elseif(isset($check_order))
  {
    if(isset($client_id))
    {
      $q = $d->select("clients_master","order_no = '$order_no' AND client_id != '$client_id'");
    }
    else
    {
      $q = $d->select("clients_master","order_no = '$order_no'");
    }
    if(mysqli_num_rows($q) > 0)
    {
      echo "false";exit;
    }
    else
    {
      echo "true";exit;
    }
  }
}
?>