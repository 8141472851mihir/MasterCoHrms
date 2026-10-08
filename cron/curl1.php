<?php 
  include_once 'lib.php';
  $cron_id = $_GET['cron_id'];
  $txt = "Master Cron ($cron_id) Start Time : ".date("Y-m-d h:i:s A");
  $myfile = file_put_contents('../img/cronTest.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
  // exit;


  // echo '<pre>';
  if($_GET && isset($_GET['cron_id']) &&  filter_var($_GET['cron_id'], FILTER_VALIDATE_INT) == true){
    $cron_id = $_GET['cron_id'];
    $q = $d->select("crons_master" ,"cron_id=$cron_id AND cron_status=0");
    if(mysqli_num_rows($q)>0){
      $crondata = mysqli_fetch_array($q);
      extract($crondata);
      $current_date = date("Y-m-d");
      $current_date_time = date("Y-m-d H:i:s");
      $current_month = date("Y-m");
         $firstTimeCronRun = false;

      if($last_run_date=="" || $last_run_date<$current_date){

        $q1 = $d->selectRow("crons_society_master.display_order_by, crons_society_master.society_id,society_master.society_name,society_master.support_name, society_master.support_country_code, society_master.support_mobile_no,society_master.sub_domain","crons_society_master,society_master","cron_id=$cron_id AND society_master.society_id=crons_society_master.society_id AND crons_society_master.active_status=0","ORDER BY crons_society_master.display_order_by ASC LIMIT 1");
         $firstTimeCronRun = true;
      }else{

        $q1 = $d->selectRow("crons_society_master.display_order_by, crons_society_master.society_id,society_master.society_name,society_master.support_name, society_master.support_country_code, society_master.support_mobile_no,society_master.sub_domain","crons_society_master,society_master","cron_id=$cron_id AND society_master.society_id=crons_society_master.society_id AND crons_society_master.active_status=0 AND crons_society_master.display_order_by>$last_society_id","ORDER BY crons_society_master.display_order_by ASC LIMIT 1");
        // echo "cron_id=$cron_id AND society_master.society_id=crons_society_master.society_id AND crons_society_master.active_status=0 AND crons_society_master.society_id>$last_society_id";
      }

 
      if(mysqli_num_rows($q1)>0){

        if (date("H")>=3 && date("H")<=16) {
          $cron_schedule_timing ='morning';
        } else {
          $cron_schedule_timing ='evening';
        }

        $societydetails = mysqli_fetch_array($q1);

       
        $society_id = $societydetails['society_id'];
        $society_name = $societydetails['society_name'];
        $display_order_by = $societydetails['display_order_by'];
        
        $support_name = $societydetails['support_name'];
        $support_country_code = $societydetails['support_country_code'];
        $support_mobile_no = $societydetails['support_mobile_no'];

        $supportPerson = $support_name;
        $supportNumber = $support_country_code.$support_mobile_no;
        $supportEmail = '';

        $sub_domain = $societydetails['sub_domain'];


        $api_url = $sub_domain.$cron_url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"$cron_tag=$cron_value&society_id=$society_id&cron_schedule_timing=$cron_schedule_timing&supportPerson=$supportPerson&supportNumber=$supportNumber&supportEmail=$supportEmail");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'key: '.$keydb
          ));
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $err = curl_error($ch);

        curl_close($ch);
        $is_error = false;
        if($err){
          $is_error = true;
        }else if($info['http_code']!='200'){
          $is_error = true;
        }else if($info['content_type']!='text/html; charset=UTF-8'){
          $res = json_decode($response, true);
          $cron_user_count = isset($res['total_users_count']) ? $res['total_users_count'] : 0;
          if($res['status']!='200'){
            $is_error = true;
          }
        }
        $fileName = $current_month."~".$cron_id.".txt";
        $fileFolder = "../img/";
        $filePath = $fileFolder.$fileName;
        if(file_exists($filePath)){
            $is_exist = true;
            $current_data = file_get_contents($filePath);
        }else{
          $is_exist = false;
          $file=fopen($filePath,"w");
          $current_data = "";
        }
        $current_data .=  "$society_name (".$d->short_app_name()."_$society_id) ($current_date_time)\n$response\n**************************************\n";
        if($is_exist==false){
          fclose($file);
        }

        file_put_contents($filePath, $current_data);
        $a['last_run_time'] = date('Y-m-d H:i:s');
        $a['last_run_date'] = $current_date;
        
        $erlog['society_id'] = $society_id;
        $erlog['cron_id'] = $cron_id;
        $erlog['log_date'] = date("Y-m-d H:i:s");
        $erlog['response'] = $response;
        $erlog['status_code'] = $info['http_code'];
        $d->insert("cron_error_logs",$erlog);

        $todayCronRunCount = $d->count_data_direct("society_id","cron_error_logs","society_id='$society_id' AND cron_id='$cron_id'");

        if (isset($res['run_cron'])) {
          if ($res['run_cron']==false) {
            $a['last_society_id'] = $display_order_by;
          } else if ($firstTimeCronRun==true) {
            $a['last_society_id'] = 0;
          }
        } else {
          $a['last_society_id'] = $display_order_by;
        }

        $d->update("crons_master",$a,"cron_id=$cron_id");

        if (isset($cron_user_count) && $cron_user_count > 0) {
          $cronUserUpdateArray['cron_user_count'] = $cron_user_count;
          $d->update("crons_society_master ", $cronUserUpdateArray, "cron_id = $cron_id AND society_id = $society_id ");
        }
      }
    }
  }
  $txt = "Master Cron ($cron_id) End Time : ".date("Y-m-d h:i:s A");
  $myfile = file_put_contents('../img/cronTest.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
?>
