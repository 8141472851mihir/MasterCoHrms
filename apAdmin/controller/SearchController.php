<?php 
// session_start();
// $url=$_SESSION['url'] = $_SERVER['REQUEST_URI']; 
// $activePage = basename($_SERVER['PHP_SELF'], ".php");
// include_once '../lib/dao.php';
// include '../lib/model.php';
// $d = new dao();
// $m = new model();
// // $created_by=$_COOKIE['admin_name'];
// // $updated_by=$_COOKIE['admin_name'];
// $society_id=$_COOKIE['society_id'];
// $default_time_zone=$d->getTimezone($society_id);
// date_default_timezone_set($default_time_zone);
include_once '../common/objectController.php';
extract($_POST);
if(isset($_POST) && !empty($_POST)) //it can be $_GET doesn't matter
{
///////////// Search Expriy////////////
  if(isset($_POST['expirysearch']))
  {
    $expiry = isset($_POST['expiry']) ? (string)$_POST['expiry'] : '';
    if($expiry !== "" && in_array($expiry, ['0', '1'], true))
    {
      $where = "seasonal_greet_master.is_expiry='$expiry'";
    }else{
      $where = "";
    }
    $i=1;
    $q=$d->select("seasonal_greet_master LEFT JOIN bms_admin_master ON  seasonal_greet_master.created_by =bms_admin_master.admin_id","$where","order by seasonal_greet_master.created_at desc ");
    $greetRows = [];
    $greetIds = [];
    while ($data=mysqli_fetch_array($q))
    {
      $greetRows[] = $data;
      $greetIds[] = (int)$data['seasonal_greet_id'];
    }
    $imageCountByGreetStatus = [];
    if (!empty($greetIds)) {
      $greetIdsIn = implode(',', array_map('intval', $greetIds));
      $q3=$d->select("seasonal_greet_image_master"," seasonal_greet_id IN ($greetIdsIn) AND status IN ('Active','InActive')","");
      while ($img = mysqli_fetch_array($q3)) {
        $gid = (int)$img['seasonal_greet_id'];
        $st = $img['status'];
        if (!isset($imageCountByGreetStatus[$gid][$st])) {
          $imageCountByGreetStatus[$gid][$st] = 0;
        }
        $imageCountByGreetStatus[$gid][$st]++;
      }
    }
    $data= $greetRows[0] ?? null;
    print_r($data);exit;
    foreach ($greetRows as $data)
    {
      extract($data);
      $html = '<tr>
      <td class="text-right">'.$i++ .'</td>
      <td>
        <div style="display: inline-block;">
          <form action="manageSeasonalGreet" method="get">
            <input type="hidden" name="seasonal_greet_id" value="'.$seasonal_greet_id.'">
            <button type="submit" name="" class="btn btn-warning btn-sm "> Manage</button>
          </form>
        </div>
        <div style="display: inline-block;">
          <form action="seasonalGreet" method="post">
            <input type="hidden" name="seasonal_greet_id" value="'.$seasonal_greet_id.'">
            <button type="submit" name="" class="btn btn-primary btn-sm "> Edit</button>
          </form>
        </div>';
        $today = date("Y-m-d"); 
        if ($is_expiry=="Yes"  && strtotime($today) > strtotime($end_date) )
        {
          $html .='<div style="display: inline-block;">
            <form  action="controller/seasonalGreetController.php" method="post">
              <input type="hidden" name="delete_seasonal_greet_id" value="'.$seasonal_greet_id.'">
              <button type="submit" name="deleteCmp" class="form-btn btn btn-danger btn-sm "> Delete</button>
            </form>
          </div>
          <div style="display: inline-block;">
            <form action="seasonalGreet" method="post">
              <input type="hidden" name="copy_seasonal_greet_id" value="'.$seasonal_greet_id.'">
              <button type="submit" name="copySG" class="btn btn-green btn-sm "> Copy</button>
            </form>
          </div>';
        }
        $html .= '</td>
        <td>'.$title.'</td>
        <td>'.$is_expiry.'</td>
        <td>'; 
        if($is_expiry=="Yes" && $start_date!="0000-00-00" ){
          $html .= date("d-m-Y", strtotime($start_date));
        }else if($is_expiry=="Common" && $start_date!="00-00"){
          $html .= date("d-m", strtotime($start_date));
        }else {
          $html .= '-';
        }
        $html .= '</td>
        <td>';
        if($is_expiry=="Yes" && $end_date!="0000-00-00"){
          $html .= date("d-m-Y", strtotime($end_date));
        }else if($is_expiry=="Common" && $end_date!="00-00"){
          $html .= date("d-m", strtotime($end_date));
        }else{
          $html .= '-';
        }
        $html .= '</td>
        <td class="text-right">';
        $html .= $totalActiveImages = $imageCountByGreetStatus[(int)$seasonal_greet_id]['Active'] ?? 0;
        $html .= '</td>
        <td class="text-right">';
        $html .= $totalActiveImages = $imageCountByGreetStatus[(int)$seasonal_greet_id]['InActive'] ?? 0; 
        $html .='</td>
        <td>'.$admin_name.'</td>
        <td data-order="';
        $html .= date("U",strtotime($created_at)); $html .='">'; 
        $html .= date("d-m-Y h:i:s A", strtotime($created_at)); 
        $html .='</td>
      </tr>';
      print_r($html);
    }
  }

  if(isset($_POST['copyseasonalgreetings']))
  {
    // echo $copy_seasonal_greet_id;die;
    $copy_seasonal_greet_id = $d->sanitizeActionIdAsInt($copy_seasonal_greet_id ?? ($_POST['copy_seasonal_greet_id'] ?? 0));
    $q = $d->select("seasonal_greet_master","seasonal_greet_id='$copy_seasonal_greet_id'");
    $data = mysqli_fetch_array($q);
    echo "<pre>"; print_r($data);die;
  }
}
else{
  header('location:../logout');
}
 ?>
