<?php
include '../common/objectController.php';
// print_r($_POST);
if (isset($_POST) && !empty($_POST)) //it can be $_GET doesn't matter
{
  if (isset($publishPostNew)) {
    $qqq = $d->select("timeline_master", "timeline_id='$timeline_id' and active_status =0 ");
    $data = mysqli_fetch_array($qqq);
    $post_log_master = $d->select("post_log_master", "post_id='$timeline_id' ");
    $already_tried_sosa = array('0');
    while ($row = mysqli_fetch_array($post_log_master)) {
      $already_tried_sosa[] = $row['society_id'];
    }
    $already_tried_sosa = implode(",", $already_tried_sosa);
    $society_master_qry = $d->select("society_master", "   society_id  ='$society_id_post' and society_id not in ($already_tried_sosa )   ");
    while ($society_master_data = mysqli_fetch_array($society_master_qry)) {
      $feed_msg = $data['timeline_text'];
      $image = $base_url . "img/post_timeline/" . $data['post_image'];
      $post = array(
        'addFeedAuto' => 'addFeedAuto',
        'society_id' => $society_master_data['society_id'],
        'image' => $image,
        'feed_msg' => $feed_msg,
        'filename' => $data['post_image'],
        'newImageWidth' => $data['newImageWidth'],
        'newImageHeight' => $data['newImageHeight']
      );
      $json = $d->callCompanyApiEnc($society_master_data['sub_domain'], 'newsFeedControllerAuto.php', $post);

      $m->set_data('society_id', $society_master_data['society_id']);
      $m->set_data('post_id', $data['timeline_id']);
      $result2 = $json["message"];
      $m->set_data('result', $result2);
      $status = $json["status"];
      $m->set_data('status', $status);
      $m->set_data('created_date', date('Y-m-d H:i:s'));
      if ($result2 != "") {
        $a1 = array(
          'society_id' => $m->get_data('society_id'),
          'post_id' => $m->get_data('post_id'),
          'result' => $m->get_data('result'),
          'status' => $m->get_data('status'),
          'created_at' => $m->get_data('created_date')
        );
        $q = $d->insert("post_log_master", $a1);
      } else {
        $a1 = array(
          'society_id' => $m->get_data('society_id'),
          'post_id' => $m->get_data('post_id'),
          'result' => 'No Response',
          'status' => '202',
          'created_at' => $m->get_data('created_date')
        );
        $q = $d->insert("post_log_master", $a1);
      }
      $_SESSION['msg'] = "Post Uploaded";
      if ($result2 == "") {
        echo " - No Response:201";
        exit;
      } else {
        echo $json["message"] . ':' . $json["status"];
        exit;
      }
    }
  }
  if (isset($deletepost)) {
    $qqq = $d->select("timeline_master", "timeline_id='$timeline_id' ");
    $data = mysqli_fetch_array($qqq);
    $abspath = $_SERVER['DOCUMENT_ROOT'];
    $path = $abspath . "/img/post_timeline/" . $data['post_image'];
    unlink($path);

    $q = $d->delete("timeline_master", "timeline_id='$timeline_id' ");

    if ($q > 0) {
      $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Post Deleted", 1);
      $_SESSION['msg'] = "Post Added";
      header("Location: ../timeline");
    } else {
      $_SESSION['msg1'] = "Post Not Added";
      header("Location: ../timeline");
    }
  }




  if (isset($addFeed)) {
    $file_image = $_FILES['image']['tmp_name'];
    if (file_exists($file_image)) {
      $acceptable = array("jpeg", "jpg", "png", "gif");
      $extId = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
      $dirPath = "../../img/post_timeline/";
      if (in_array($extId, $acceptable) && (!empty($_FILES["image"]["type"]))) {
        $temp = explode(".", $_FILES["image"]["name"]);
        $newFileName = rand() . $user_id;
        $image = $newFileName . '_admin' . end($temp);
        $destinationPath = $dirPath . $image;
        $d->resizeImage($file_image, $destinationPath, 1280, 720, $extId);
        $post_image = $image;
      } else {
        $_SESSION['msg1'] = "Invalid Image";
        header("Location: ../timeline");
        exit;
      }
    } else {
      $post_image = "";
    }
    $m->set_data('newImageWidth', $newImageWidth);
    $m->set_data('newImageHeight', $newImageHeight);
    $m->set_data('post_image', $post_image);
    $m->set_data('timeline_text', $timeline_text);
    $m->set_data('created_date', date('Y-m-d H:i:s'));
    $admin_id = $bms_admin_id;
    $m->set_data('admin_id', $admin_id);
    $a1 = array(
      'timeline_text' => $m->get_data('timeline_text'),
      'newImageWidth' => $m->get_data('newImageWidth'),
      'newImageHeight' => $m->get_data('newImageHeight'),
      'post_image' => $m->get_data('post_image'),
      'admin_id' => $m->get_data('admin_id'),
      'created_date' => $m->get_data('created_date')
    );
    $q = $d->insert("timeline_master", $a1);
    if ($q == TRUE) {
      $_SESSION['msg'] = "Post Added";
      $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Post Added", 1);
      header("Location: ../timeline");
      exit;
    } else {
      header("Location: ../timeline");
      exit;
    }
  }

  if (isset($editFeed)) {
    $file_image = $_FILES['image']['tmp_name'];
    if (file_exists($file_image)) {
      $acceptable = array("jpeg", "jpg", "png", "gif");
      $extId = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
      $dirPath = "../../img/post_timeline/";
      if (in_array($extId, $acceptable) && (!empty($_FILES["image"]["type"]))) {
        $temp = explode(".", $_FILES["image"]["name"]);
        $newFileName = rand() . $user_id;
        $image = $newFileName . '_admin' . end($temp);
        $destinationPath = $dirPath . $image;
        $d->resizeImage($file_image, $destinationPath, 1280, 720, $extId);
        $post_image = $image;
      } else {
        $_SESSION['msg1'] = "Invalid Image";
        header("Location: ../timeline");
        exit;
      }
    } else {
      $post_image = $image_old;
    }
    $m->set_data('post_image', $post_image);
    $m->set_data('newImageWidth', $newImageWidth);
    $m->set_data('newImageHeight', $newImageHeight);
    $m->set_data('timeline_text', $timeline_text);
    $m->set_data('updated_at', date('Y-m-d H:i:s'));
    $admin_id = $bms_admin_id;
    $m->set_data('admin_id', $admin_id);
    $a1 = array(
      'timeline_text' => $m->get_data('timeline_text'),
      'newImageWidth' => $m->get_data('newImageWidth'),
      'newImageHeight' => $m->get_data('newImageHeight'),
      'post_image' => $m->get_data('post_image'),
      'admin_id' => $m->get_data('admin_id'),
      'updated_at' => $m->get_data('updated_at')
    );
    $q = $d->update("timeline_master", $a1, " timeline_id ='$timeline_id' ");
    if ($q == TRUE) {
      $_SESSION['msg'] = "Post Updated";
      $d->insert_log_specific("$society_id", "$bms_admin_id", "$created_by", "Post Updated", 1);
      header("Location: ../timeline");
    } else {
      header("Location: ../timeline");
    }
  }
} else {
  header('location:../login');
}
