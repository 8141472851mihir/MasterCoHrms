<?php
include_once 'dbconnect.php';
include_once  'interface1.php';
include_once __DIR__ . '/sanitizeHelpers.php';
include_once dirname(dirname(__DIR__)) . '/EnvLoader.php';
EnvLoader::load(dirname(dirname(__DIR__)) . '/.env');

class dao implements interface1 
{
    use SanitizeHelpers;

    private $conn;
    function __construct() 
    {
        //include_once './config.php';
       
        $db=new DbConnect();
        $this->conn=$db->connect();
    }

    function dbCon() {
      $db=new dbconnect();
      return  $this->conn=$db->connect();
    }

    function getInsertId()
    {
        return (int) $this->conn->insert_id;
    }

    //data insert funtion
    function insert($table,$value)
    {
        $field="";
        $val="";
        $i=0;
        
        foreach ($value as $k => $v)
        {
            $v = $this->conn->real_escape_string($v);
            if($i == 0)
            {
                $field.=$k;
                $val.="'".$v."'";
            }
            
            else 
            {
                $field.=",".$k;
                $val.=",'".$v."'";
                
            }
            $i++;
            
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        return mysqli_query($this->conn,"INSERT INTO $table($field) VALUES($val)") or die(mysqli_error($this->conn));
    }
    
     function insert_app_menu($society_id)
    {

        mysqli_set_charset($this->conn,"utf8mb4");
        return mysqli_query($this->conn,"INSERT INTO resident_app_menu_society (society_id,app_menu_id,parent_menu_id,menu_status,menu_sequence) SELECT $society_id,app_menu_id,parent_menu_id,menu_status,menu_sequence FROM resident_app_menu WHERE menu_status=0 AND wip_menu=0") or die(mysqli_error($this->conn));
    }
    
    // insert log
    function insert_log($society_id,$user_id,$user_name,$log_name)
    {   
      $log_name = $this->conn->real_escape_string($log_name);
      $user_name = $this->conn->real_escape_string($user_name);
        $now=date("y-m-d H:i:s");
        $val="'$society_id','$user_id','$user_name','$log_name','$now'";
        mysqli_set_charset($this->conn,"utf8mb4");
        return mysqli_query($this->conn,"INSERT INTO log_master(society_id,user_id,user_name,log_name,log_time) VALUES($val)") or die(mysqli_error($this->conn));
    }

    function insert_log_specific($society_id,$user_id,$user_name,$log_name,$log_type)
    {   
      $log_name = $this->conn->real_escape_string($log_name);
      $user_name = $this->conn->real_escape_string($user_name);
        $now=date("y-m-d H:i:s");
        $val="'$society_id','$user_id','$log_type','$user_name','$log_name','$now'";
        mysqli_set_charset($this->conn,"utf8mb4");
        return mysqli_query($this->conn,"INSERT INTO log_master(society_id,user_id,log_type,user_name,log_name,log_time) VALUES($val)") or die(mysqli_error($this->conn));
    }

    //using insert funtion for procedures 
    function insert1($table, $value)
    {
        $field="";
        $val="";
        $i = 0;
        
          foreach($value as $k => $v)
          {
            $v = $this->conn->real_escape_string($v);
              if($i==0)
             
               {
                  $field.=$k;
                  $val.="'" . $v . "'";
              }
              else 
              {
                  $field.="," . $k ;
                  $val.=", '" . $v . "'";
              }
              $i++;
          }
          mysqli_set_charset($this->conn,"utf8mb4");
          return mysqli_query($this->conn,"CALL $table($val)")or die(mysqli_error($this->conn));;
    }
    
      //select funtion display data
    function select($table, $where='', $other='')
    {
        if($where != '')
        {
            $where= 'where ' .$where;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        $select = mysqli_query($this->conn,"SELECT * FROM $table $where $other") or die(mysqli_error($this->conn));
        return $select;
    }

      //select funtion display data
    function selectRow($colum,$table, $where='', $other='')
    {
        if($where != '')
        {
            $where= 'where ' .$where;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        $select = mysqli_query($this->conn,"SELECT $colum FROM $table $where $other") or die(mysqli_error($this->conn));
        return $select;
    }
    function select_row($table, $where='', $other='')
    {
        if($where != '')
        {
            $where= 'where ' .$where;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        $select = mysqli_query($this->conn,"SELECT COUNT(*) as num_rows FROM $table $where $other") or die(mysqli_error($this->conn));
        return $select;
    }
     //select funtion display data with DISTINCT  (not show duplicate)
    function select1($table, $column, $where='',$other='')
    {
        if($where != '')
        {
            $where= 'where ' .$where;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        $select = mysqli_query($this->conn,"SELECT DISTINCT $column FROM $table $where $other") or die(mysqli_error($this->conn));
        return $select;
    }
    function select2($table, $where='',$other='')
    {
        if($where != '')
        {
            $where= 'where ' .$where;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        $select = mysqli_query($this->conn,"SELECT DISTINCT * FROM $table $where $other") or die(mysqli_error($this->conn));
        return $select;
    }
    function selectColumnWise($table,$columnName='',$where=''){
        if($where != '')
        {
           $where= 'where ' .$where;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
         $select = mysqli_query($this->conn,"SELECT $columnName FROM $table $where") or die(mysqli_error($this->conn));
        return $select;
    }
  
    // using sp   
    function selectSp($spName) {

      mysqli_set_charset($this->conn, "utf8mb4");
      $result = mysqli_query($this->conn, "CALL $spName");
      return $result;
      // return mysqli_query($this->conn,"CALL $table")or die(mysqli_error($this->conn));;
    }
   
    function selectSpArray($spName) {

      $dataArray=array();
      mysqli_set_charset($this->conn, "utf8mb4");
      $result = mysqli_query($this->conn, "CALL $spName");
      while($data_countries_list=mysqli_fetch_array($result)) {
        array_push($dataArray, $data_countries_list);
      }
      mysqli_next_result($this->conn);
      return $dataArray;
      // return mysqli_query($this->conn,"CALL $table")or die(mysqli_error($this->conn));;
    }
      //delete using update query(active_flag)
     function delete1($table ,$var, $where)
    {
        if($where != '')
        {
            $where= 'where ' .$where;
        }
        if($var != '')
        {
            $var= 'active_flag= ' .$var;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        return mysqli_query($this->conn,"update $table set $var $where");
    }

    //Update Product View (view_status)
     function view_status($table ,$var, $where)
    {
        if($where != '')
        {
            $where= 'where ' .$where;
        }
        if($var != '')
        {
            $var= 'view_status= ' .$var;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        return mysqli_query($this->conn,"update $table set $var $where");
    }


     //Comment ()
     function comment($table ,$var, $where)
    {
        if($where != '')
        {
            $where= 'where ' .$where;
        }
        if($var != '')
        {
            $var= 'status= ' .$var;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        return mysqli_query($this->conn,"update $table set $var $where");
    }
     //delete permanataly  function
    function delete($table , $where='')
    {
        if($where != '')
        {
        $where= 'where ' .$where;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        return mysqli_query($this->conn,"delete FROM $table $where")or die(mysqli_error($this->conn));
    }

    //Upadate funtion
    function update($table, $value, $where, $allow_null = 0)
    {
        if ($where != '') {
            $where = 'WHERE ' . $where;
        }

        $val = "";
        $i = 0;

        foreach ($value as $k => $v) {

            // NULL handling logic
            if ($v === null) {
                if ($allow_null == 1) {
                    $v = "NULL";   // real SQL NULL
                    $quote = "";
                } else {
                    $v = "''";     // convert NULL to empty string
                    $quote = "";
                }
            } else {
                $v = $this->conn->real_escape_string($v);
                $quote = "'";
            }

            if ($i == 0) {
                $val .= "$k={$quote}{$v}{$quote}";
            } else {
                $val .= ", $k={$quote}{$v}{$quote}";
            }

            $i++;
        }

        mysqli_set_charset($this->conn, "utf8mb4");

        $sql = "UPDATE $table SET $val $where";

        return mysqli_query($this->conn, $sql);
    }
     //select next auto_increment_id
    function last_auto_id($table)
    {
        mysqli_set_charset($this->conn,"utf8mb4");
        $select_id = mysqli_query($this->conn,"SHOW TABLE STATUS LIKE '$table'" ) or die(mysqli_error($this->conn));
        return $select_id;
    }

        //Count Data of Table
    function count_data($field='' ,$table ,$where='')
    {
        if($where != '')
        {
            $where= 'where ' .$where;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        $count_data = mysqli_query($this->conn,"SELECT $field,COUNT(*)  FROM $table $where" ) or die(mysqli_error($this->conn));
        return $count_data;

    }

    //Count Data of Table
    function count_data_direct($field='' ,$table ,$where='')
    {
        if($where != '')
        {
            $where= 'where ' .$where;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        // $temp = mysqli_query($this->conn,"SELECT $field,COUNT(*)  FROM $table $where" ) or die(mysqli_error($this->conn));
        // while($rowCount=mysqli_fetch_array($temp)) {
        // $totalCount=$rowCount['COUNT(*)'];
        
        $result=mysqli_query($this->conn,"SELECT count(*) as $field from $table $where") or die(mysqli_error($this->conn));
        $data=mysqli_fetch_assoc($result);
        $totalCount= $data[$field];
        return $totalCount;
        // }

    }
     //Count sum of  Table field
    function sum_data($field='' ,$table ,$where='')
    {
        if($where != '')
        {
            $where= 'where ' .$where;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        $sum_data = mysqli_query($this->conn,"SELECT SUM($field) from `$table` $where" ) or die(mysqli_error($this->conn));
        return $sum_data;

    }

// sms send 
   function send_sms($mobile,$message) {
       
       $msg=urlencode($message);
      //  $temp = mysqli_query($this->conn,"SELECT *  FROM sms" ) or die(mysqli_error($this->conn));
      //  $smsData=mysqli_fetch_array($temp);
      //  extract($smsData);
       
       $sms= file_get_contents("https://2factor.in/API/R1/?module=TRANS_SMS&apikey=" . rawurlencode((string) EnvLoader::get('TWO_FACTOR_API_KEY')) . "&to=$mobile&from=FINCAS&msg=$msg");

    }

     function send_sms_rent_sale($mobile,$user_name) {
       
       $msg=urlencode($message??"");
      //  $temp = mysqli_query($this->conn,"SELECT *  FROM sms" ) or die(mysqli_error($this->conn));
      //  $smsData=mysqli_fetch_array($temp);
      //  extract($smsData);
       
       $sms= file_get_contents("https://2factor.in/API/R1/?module=TRANS_SMS&apikey=" . rawurlencode((string) EnvLoader::get('TWO_FACTOR_API_KEY')) . "&to=$mobile&from=FINCAA&templatename=Rent+%2F+Sale+Property+Listed+Confirmation&var1=$user_name");

    }

    function send_otp($mobile,$otp) {
       
       $sms= file_get_contents("https://2factor.in/API/V1/" . rawurlencode((string) EnvLoader::get('TWO_FACTOR_API_KEY')) . "/SMS/$mobile/$otp/FINCASYS+OTP");
       

    }

    // get fcm token
    function getFcm($fildName,$table,$where){
     mysqli_set_charset($this->conn,"utf8mb4");
     $sql="SELECT $fildName FROM $table WHERE $where";
     $temp=mysqli_query($this->conn,$sql);
     $data=mysqli_fetch_array($temp);
       if($data > 0){
        $fcm=$data[$fildName];
        return $fcm;
       }
       else{
        return false;
       }
      }


   function get_android_fcm($table,$where) {
        if($where != '')
        {
            $where= 'where ' .$where;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        $select = mysqli_query($this->conn,"SELECT * FROM $table $where") or die(mysqli_error($this->conn));
        $totalUsers = mysqli_num_rows($select);
        $loopCount= $totalUsers/1000;
        $loopCount= round($loopCount)+1;

           for ($i=0; $i <$loopCount ; $i++) { 
                $limit_users = $i."000";
                $fcmArray=array();
                $q1 = mysqli_query($this->conn,"SELECT user_token FROM $table $where GROUP BY user_token") or die(mysqli_error($this->conn));
                  while ($row=mysqli_fetch_array($q1)) {
                    $user_token= $row['user_token'];
                    array_push($fcmArray, $user_token);
                  }
                 return $fcmArray;
              }
   }

    function get_emp_fcm($table,$where) {
        if($where != '')
        {
            $where= 'where ' .$where;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        $select = mysqli_query($this->conn,"SELECT * FROM $table $where") or die(mysqli_error($this->conn));
        $totalUsers = mysqli_num_rows($select);
        $loopCount= $totalUsers/1000;
        $loopCount= round($loopCount)+1;

           for ($i=0; $i <$loopCount ; $i++) { 
                $limit_users = $i."000";
                $fcmArray=array();
                $q1 = mysqli_query($this->conn,"SELECT emp_token FROM $table $where") or die(mysqli_error($this->conn));
                  while ($row=mysqli_fetch_array($q1)) {
                    $emp_token= $row['emp_token'];
                    array_push($fcmArray, $emp_token);
                  }
                 return $fcmArray;
              }
   }

   function get_admin_fcm($table,$where) {
        if($where != '')
        {
            $where= 'where ' .$where;
        }
        mysqli_set_charset($this->conn,"utf8mb4");
        $select = mysqli_query($this->conn,"SELECT * FROM $table $where") or die(mysqli_error($this->conn));
        $totalUsers = mysqli_num_rows($select);
        $loopCount= $totalUsers/1000;
        $loopCount= round($loopCount)+1;

           for ($i=0; $i <$loopCount ; $i++) { 
                $limit_users = $i."000";
                $fcmArray=array();
                $q1 = mysqli_query($this->conn,"SELECT token FROM $table $where") or die(mysqli_error($this->conn));
                  while ($row=mysqli_fetch_array($q1)) {
                    $token= $row['token'];
                    array_push($fcmArray, $token);
                  }
                 return $fcmArray;
              }
   }
  //update counter
  function updateCounter($table ,$value='')
    {
        mysqli_set_charset($this->conn,"utf8mb4");
        return mysqli_query($this->conn,"update $table SET $value");
    }


      //select funtion display data
    function dbSize()
    {
       
        mysqli_set_charset($this->conn,"utf8mb4");
        $select = mysqli_query($this->conn,"SHOW TABLE STATUS") or die(mysqli_error($this->conn));
        return $select;
    }

    
    function selectArray($table, $where='', $other='')
    {
      if($where != '')
      {
          $where= 'where ' .$where;
      }
      mysqli_set_charset($this->conn,"utf8");
      mysqli_set_charset($this->conn,"utf8");
      $select = mysqli_query($this->conn,"SELECT * FROM $table $where $other") or die(mysqli_error($this->conn));
      $data = mysqli_fetch_array($select);
      return $data;
    }


    function GetCurrencySymbol($currency_char){
        $fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $fmt->setTextAttribute(NumberFormatter::CURRENCY_CODE, $currency_char);
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);
        $temp=$fmt->formatCurrency("0",$currency_char);
        $temp = preg_replace('/[0-9]+/', '', $temp);
        return $temp;
    }

    function send_sms_multiple($mobiles,$message) {
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "http://2factor.in/API/V1/" . rawurlencode((string) EnvLoader::get('TWO_FACTOR_API_KEY')) . "/ADDON_SERVICES/SEND/TSMS",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\"From\": \"FINCAS\",\"To\": \"$mobiles\", \"Msg\": \"$message\"}",
      ));
      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);
      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        echo $response;
      }
      return $error??"";
    }

    function truncate($table)
    {
        mysqli_set_charset($this->conn,"utf8mb4");
        return mysqli_query($this->conn,"TRUNCATE TABLE $table");
    }

    function getWebFcm($table,$where) {
      if($where != '')
      {
          $where= 'where ' .$where;
      }
      mysqli_set_charset($this->conn,"utf8mb4");
      $select = mysqli_query($this->conn,"SELECT * FROM $table $where") or die(mysqli_error($this->conn));
      $totalUsers = mysqli_num_rows($select);
      $loopCount= $totalUsers/1000;
      $loopCount= round($loopCount)+1;

      for ($i=0; $i <$loopCount ; $i++) { 
          $limit_users = $i."000";
          $fcmArray=array();
          $q1 = mysqli_query($this->conn,"SELECT fcm_token FROM $table $where GROUP BY fcm_token") or die(mysqli_error($this->conn));
            while ($row=mysqli_fetch_array($q1)) {
              $fcm_token= $row['fcm_token'];
              array_push($fcmArray, $fcm_token);
            }
           return $fcmArray;
        }
    }
    
    function bm_random_hex() {
     $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
     return '#' . $rand[rand(0,15)] . $rand[rand(0,15)] . $rand[rand(0,15)] . $rand[rand(0,15)] . $rand[rand(0,15)] . $rand[rand(0,15)];
    }

      function haversineGreatCircleDistance(
      $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
      // convert from degrees to radians
      $latFrom = deg2rad($latitudeFrom);
      $lonFrom = deg2rad($longitudeFrom);
      $latTo = deg2rad($latitudeTo);
      $lonTo = deg2rad($longitudeTo);

      $latDelta = $latTo - $latFrom;
      $lonDelta = $lonTo - $lonFrom;

      $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
      return $angle * $earthRadius;
    }

    function get_encrypt_key() {
        return EnvLoader::get('ENCRYPT_KEY');
    } 

    function get_encrypt_iv() {
        return EnvLoader::get('ENCRYPT_IV');
    } 

    function randomPassword() {
      $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890@_.*';
      $pass = array(); //remember to declare $pass as an array
      $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
      for ($i = 0; $i < 12; $i++) {
          $n = rand(0, $alphaLength);
          $pass[] = $alphabet[$n];
      }
      return implode($pass); //turn the array into a string
    }

    function change_timezone($dbtime,$newtimezone,$format) {
      $datetime = new DateTime($dbtime);
      $datetime->format($format);
      $la_time = new DateTimeZone($newtimezone);
      $datetime->setTimezone($la_time);
    return $datetime->format($format);
    }

    function get_inquiry_emails($inquiry_type=null){
        $to = "";
        if($inquiry_type==1){
            // $to = "priyank.shah@chplgroup.org,mehulinfo997@gmail.com,contact@my-company.app,nikhil.saggam@chplgroup.org,harshad.hadiya@chplgroup.org,parvez.shaikh@chplgroup.org,mycosupport@chlgroup.org,vineet.tripathi@chlgroup.org, sachin.sreekumar@chlgroup.org ";
            $to = "patelmihir0430@gmail.com";
        }else if($inquiry_type==2){
            // $to = "mycosupport@chplgroup.org";
            $to = "patelmihir0430@gmail.com";
        }
        return $to;
    }

    /**
     * Add a company to a cron category. If all buckets are full, create a new cron
     * with the same category (and send mail). Capacity defaults:
     * Analytics=300, W_Morning/W_Evening=100, others=300.
     *
     * @return array{status:string,message:string,cron_id?:int,created_new?:bool}
     */
    function addCompanyToCronCategory($company_id, $cron_category, $options = array())
    {
        global $m, $base_url;

        $company_id = intval($company_id);
        $cron_category = trim((string)$cron_category);
        if ($company_id <= 0 || $cron_category === '') {
            return array('status' => '201', 'message' => 'Company Id and Cron category are mandatory');
        }

        $cron_url = isset($options['cron_url']) ? trim((string)$options['cron_url']) : '';
        $cron_tag = isset($options['cron_tag']) ? (string)$options['cron_tag'] : '';
        $cron_value = isset($options['cron_value']) ? (string)$options['cron_value'] : '';
        $curl_script = isset($options['curl_script']) ? $options['curl_script'] : 'cron/curl1.php';
        $send_mail = !isset($options['send_mail']) || $options['send_mail'];

        $categoryLimits = array(
            'Analytics' => 300,
            'W_Morning' => 100,
            'W_Evening' => 100,
        );
        $limit = isset($options['cron_limit']) ? intval($options['cron_limit']) : (isset($categoryLimits[$cron_category]) ? $categoryLimits[$cron_category] : 300);
        if ($limit <= 0) {
            $limit = 100;
        }

        $safe_category = mysqli_real_escape_string($this->conn, $cron_category);

        $cron_ids_q = $this->selectRow(
            "TRIM(TRAILING ',' FROM GROUP_CONCAT(cron_id)) AS cron_ids",
            "crons_master",
            "cron_status='0' AND cron_category='$safe_category'"
        );
        $cron_ids_data = mysqli_fetch_array($cron_ids_q);
        $this_category_cron_ids = isset($cron_ids_data['cron_ids']) ? $cron_ids_data['cron_ids'] : '';

        if (!empty($this_category_cron_ids)) {
            $already = $this->count_data_direct(
                "main_id",
                "crons_society_master",
                "society_id='$company_id' AND active_status='0' AND cron_id IN ($this_category_cron_ids)"
            );
            if ($already > 0) {
                return array('status' => '201', 'message' => 'Company already exist inside this cron category');
            }
        }

        $cron_id = 0;
        $new_order = 1;
        $created_new = false;
        $new_cron_name = '';

        $check_available = $this->selectRow(
            "cm.cron_id, cm.cron_name, COUNT(csm.society_id) AS total_societies, MAX(csm.display_order_by) AS max_order",
            "crons_master cm LEFT JOIN crons_society_master csm ON cm.cron_id = csm.cron_id AND csm.active_status = 0",
            "cm.cron_status='0' AND cm.cron_category = '$safe_category'",
            "GROUP BY cm.cron_id HAVING COUNT(csm.society_id) < $limit ORDER BY cm.cron_id ASC"
        );

        if (mysqli_num_rows($check_available) > 0) {
            $available = mysqli_fetch_array($check_available);
            $cron_id = intval($available['cron_id']);
            $new_order = intval($available['max_order']) + 1;
        } else {
            $last_cron = $this->selectRow(
                "cron_id, cron_name, cron_tag, cron_value, cron_url, run_per_company",
                "crons_master",
                "cron_category = '$safe_category'",
                "ORDER BY cron_id DESC LIMIT 1"
            );

            $new_cron_data = null;
            if (mysqli_num_rows($last_cron) > 0) {
                $last = mysqli_fetch_array($last_cron);
                $last_cron_name = $last['cron_name'];
                $inherit_cron_tag = $last['cron_tag'];
                $inherit_cron_value = $last['cron_value'];
                $inherit_cron_url = $last['cron_url'];

                if (preg_match('/^(.*?)\s+(\d+)\s+to\s+(\d+)\s*$/i', $last_cron_name, $rangeMatch)) {
                    $prefix = $rangeMatch[1];
                    $prev_end = intval($rangeMatch[3]);
                    $new_start = $prev_end + 1;
                    $new_end = $prev_end + $limit;
                    $new_cron_name = $prefix . ' ' . $new_start . ' to ' . $new_end;
                } else {
                    preg_match('/(\d+)$/', $last_cron_name, $matches);
                    $last_num = isset($matches[1]) ? intval($matches[1]) : 1;
                    $new_num = $last_num + 1;
                    $new_cron_name = $cron_category . ' - ' . $new_num;
                }

                $new_cron_data = array(
                    "cron_name" => $new_cron_name,
                    "cron_category" => $cron_category,
                    "cron_url" => $inherit_cron_url,
                    "cron_tag" => $inherit_cron_tag,
                    "cron_value" => $inherit_cron_value,
                    "cron_status" => 0,
                    "added_date" => date('Y-m-d H:i:s'),
                );
            } elseif ($cron_url !== '') {
                if (in_array($cron_category, array('W_Morning', 'W_Evening'), true)) {
                    $slot = ($cron_category === 'W_Morning') ? 'Morning' : 'Evening';
                    $new_cron_name = "Ahmedabad WhatsApp Analytics $slot 1 to $limit";
                } else {
                    $new_cron_name = $cron_category . ' - 1';
                }
                $new_cron_data = array(
                    "cron_name" => $new_cron_name,
                    "cron_category" => $cron_category,
                    "cron_url" => $cron_url,
                    "cron_tag" => $cron_tag,
                    "cron_value" => $cron_value,
                    "cron_status" => 0,
                    "added_date" => date('Y-m-d H:i:s'),
                );
            }

            if ($new_cron_data === null) {
                return array('status' => '201', 'message' => 'No cron found for category and cron_url missing');
            }

            $this->insert("crons_master", $new_cron_data);
            $cron_id = intval($this->conn->insert_id);
            $new_order = 1;
            $created_new = true;

            if ($send_mail && $cron_id > 0) {
                $resolved_base = !empty($base_url) ? $base_url : (($m && method_exists($m, 'base_url')) ? $m->base_url() : '');
                $cron_full_url = rtrim($resolved_base, '/') . '/' . ltrim($curl_script, '/');
                $cron_full_url .= (strpos($cron_full_url, '?') === false ? '?' : '&') . 'cron_id=' . $cron_id;
                $to = "bhavesh@chlgroup.org";
                $admin_name = "Admin";
                $subject = "New $cron_category Cron Created – Action Required to Add Cron URL " . $this->app_name() . " ";
                $mailTemplateCandidates = array(
                    dirname(__DIR__) . '/mail/newCronAlert.php',
                    dirname(__DIR__) . '/../apAdmin/mail/newCronAlert.php',
                );
                $mailCandidates = array(
                    dirname(__DIR__) . '/mail.php',
                    dirname(__DIR__) . '/../apAdmin/mail.php',
                );
                foreach ($mailTemplateCandidates as $tpl) {
                    if (is_file($tpl)) {
                        include $tpl;
                        break;
                    }
                }
                foreach ($mailCandidates as $mailFile) {
                    if (is_file($mailFile)) {
                        include $mailFile;
                        break;
                    }
                }
            }
        }

        if ($cron_id > 0) {
            $this->insert("crons_society_master", array(
                "cron_id" => $cron_id,
                "society_id" => $company_id,
                "active_status" => 0,
                "display_order_by" => $new_order,
            ));
            return array(
                'status' => '200',
                'message' => 'Company added to cron',
                'cron_id' => $cron_id,
                'created_new' => $created_new,
            );
        }

        return array('status' => '201', 'message' => 'Unable to add company to cron');
    }

    /**
     * Remove a company from all active crons in a category.
     */
    function removeCompanyFromCronCategory($company_id, $cron_category)
    {
        $company_id = intval($company_id);
        $cron_category = trim((string)$cron_category);
        if ($company_id <= 0 || $cron_category === '') {
            return array('status' => '201', 'message' => 'Company Id and Cron category are mandatory');
        }
        $safe_category = mysqli_real_escape_string($this->conn, $cron_category);
        $cron_ids_q = $this->selectRow(
            "TRIM(TRAILING ',' FROM GROUP_CONCAT(cron_id)) AS cron_ids",
            "crons_master",
            "cron_status='0' AND cron_category='$safe_category'"
        );
        $cron_ids_data = mysqli_fetch_array($cron_ids_q);
        $this_category_cron_ids = isset($cron_ids_data['cron_ids']) ? $cron_ids_data['cron_ids'] : '';
        if (empty($this_category_cron_ids)) {
            return array('status' => '200', 'message' => 'Company Removed from this cron category');
        }
        $this->delete(
            "crons_society_master",
            "society_id='$company_id' AND cron_id IN ($this_category_cron_ids)"
        );
        return array('status' => '200', 'message' => 'Company Removed from this cron category');
    }

    /**
     * Sync W_Morning / W_Evening based on whatsapp_access_master.type for a company.
     * type: "Only Morning", "Only Evening", "Both Morning & Evening"
     */
    function syncWhatsAppAnalyticsCronFromAccess($society_id)
    {
        $society_id = intval($society_id);
        if ($society_id <= 0) {
            return false;
        }

        $morningCount = $this->count_data_direct(
            "total_access",
            "whatsapp_access_master",
            "society_id='$society_id'
             AND type IN ('Only Morning', 'Both Morning & Evening')"
        );

        $eveningCount = $this->count_data_direct(
            "total_access",
            "whatsapp_access_master",
            "society_id='$society_id'
             AND type IN ('Only Evening', 'Both Morning & Evening')"
        );

        // Prefer exact labels from analytics sync; fall back when type is empty
        if ($morningCount == 0 && $eveningCount == 0) {
            $anyCount = $this->count_data_direct("total_access", "whatsapp_access_master", "society_id='$society_id'");
            if ($anyCount > 0) {
                // Unknown/blank type → treat as both (legacy)
                $morningCount = $anyCount;
                $eveningCount = $anyCount;
            }
        }

        $opts = array(
            'cron_url' => 'cron/whats_app_message_cron.php',
            'cron_tag' => '',
            'cron_value' => '',
            'cron_limit' => 100,
            'curl_script' => 'cron/curl1.php',
            'send_mail' => true,
        );

        if ($morningCount > 0) {
            $this->addCompanyToCronCategory($society_id, 'W_Morning', $opts);
        } else {
            $this->removeCompanyFromCronCategory($society_id, 'W_Morning');
        }

        if ($eveningCount > 0) {
            $this->addCompanyToCronCategory($society_id, 'W_Evening', $opts);
        } else {
            $this->removeCompanyFromCronCategory($society_id, 'W_Evening');
        }

        return true;
    }


    function encryptDecrypt($action, $string, $enc_key = null, $enc_iv = null){
        $output=false;
        $method = "AES-256-CBC";
        $enc_key = $enc_key ?? $this->get_encrypt_key();
        $enc_iv = $enc_iv ?? $this->get_encrypt_iv();
        $key=hash("sha256", $enc_key);
        $iv=substr(hash("sha256", $enc_iv), 0, 16);
        if ($action=="encrypt"){
            $output=openssl_encrypt($string, $method, $key, 0, $iv);
            $output=base64_encode($output);
        }
        else if($action=="decrypt"){
            $output=openssl_decrypt($string, $method, $key, 0, $iv);
            $output=openssl_decrypt(base64_decode($string), $method, $key, 0, $iv);
        }
        return $output;
    }

    
    function manage_encryption($status, $response, $doCompress = null, $enc_key = null, $enc_iv = null){
        global $compress;
        if ($doCompress === null) {
            $doCompress = (isset($compress) && $compress != '') ? $compress : '0';
        }
        if($status==0){
            return json_encode($response);
        }else{
            $enc_key = $enc_key ?? $this->get_encrypt_key();
            $enc_iv = $enc_iv ?? $this->get_encrypt_iv();
            if ($doCompress == 1) {
                $compressedJson = gzencode(json_encode($response), 6);
                return $encryptedString = base64_encode(openssl_encrypt($compressedJson, 'AES-256-CBC', $enc_key, OPENSSL_RAW_DATA, $enc_iv));
            }
            return $encryptedString = base64_encode(openssl_encrypt(json_encode($response), 'AES-256-CBC', $enc_key, OPENSSL_RAW_DATA, $enc_iv));
        }
    }

    function manage_decryption($status, $response, $doCompress = 0, $enc_key = null, $enc_iv = null)
    {
        if ($status == 0) {
            return json_encode($response);
        } else {
            $enc_key = $enc_key ?? $this->get_encrypt_key();
            $enc_iv = $enc_iv ?? $this->get_encrypt_iv();
            $decryptedString = openssl_decrypt(base64_decode($response), 'AES-256-CBC', $enc_key, OPENSSL_RAW_DATA, $enc_iv);
            // Automatically detect gzip magic header (\x1f\x8b) OR explicit $doCompress flag
            if ($decryptedString !== false && is_string($decryptedString)) {
                $isGzip = strlen($decryptedString) >= 2 && substr($decryptedString, 0, 2) === "\x1f\x8b";
                if ($doCompress == 1 || $isGzip) {
                    $decompressed = @gzdecode($decryptedString);
                    if ($decompressed !== false && is_string($decompressed)) {
                        // Return only when it looks like JSON; otherwise fall back to raw decrypted bytes.
                        if (preg_match('/^\s*[\{\[]/', $decompressed) === 1) {
                            return $decompressed;
                        }
                    }
                }
            }
            return $decryptedString;
        }
    }

    /**
     * POST encrypted payload to a company masterApi endpoint; returns decoded array.
     */
    function callCompanyApiEnc($companyBaseUrl, $endpoint, array $postData, $apiKey = null)
    {
        if ($apiKey === null) {
            include_once __DIR__ . '/model.php';
            $m = new model();
            $apiKey = $m->api_key();
        }

        $encrypted = $this->manage_encryption('1', $postData);
        $url = rtrim($companyBaseUrl, '/') . '/masterApi/' . ltrim($endpoint, '/');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $encrypted,
            CURLOPT_HTTPHEADER => array('key: ' . $apiKey),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        if ($response === false || $response === '') {
            return null;
        }

        $plain = json_decode($response, true);
        if (is_array($plain)) {
            return $plain;
        }

        $decrypted = $this->manage_decryption('1', $response);
        if ($decrypted === false || $decrypted === '') {
            return null;
        }

        return json_decode($decrypted, true);
    }
    
    /**
     * Store source as-is when resize cannot run (unreadable image, GD failure, etc.).
     */
    private function storeOriginalImage($sourcePath, $destinationPath)
    {
        if ($destinationPath === '' || !is_string($destinationPath)) {
            return false;
        }
        if (!is_file($sourcePath) || !is_readable($sourcePath)) {
            return false;
        }
        if (is_uploaded_file($sourcePath)) {
            return move_uploaded_file($sourcePath, $destinationPath);
        }
        return copy($sourcePath, $destinationPath);
    }

    function resizeImage($sourcePath, $destinationPath, $maxWidth, $maxHeight, $ext) {
        $ext = strtolower((string) $ext);
        $maxWidth = (int) $maxWidth;
        $maxHeight = (int) $maxHeight;

        if ($sourcePath === '' || !is_string($sourcePath) || !is_file($sourcePath) || !is_readable($sourcePath)) {
            return false;
        }
        if ($maxWidth <= 0 || $maxHeight <= 0) {
            return $this->storeOriginalImage($sourcePath, $destinationPath);
        }

        $imageInfo = @getimagesize($sourcePath);
        if ($imageInfo === false || empty($imageInfo[0]) || empty($imageInfo[1])) {
            return $this->storeOriginalImage($sourcePath, $destinationPath);
        }

        $origWidth = (int) $imageInfo[0];
        $origHeight = (int) $imageInfo[1];
        if ($origWidth <= 0 || $origHeight <= 0) {
            return $this->storeOriginalImage($sourcePath, $destinationPath);
        }

        $ratio = $origWidth / $origHeight;

        if ($maxWidth / $maxHeight > $ratio) {
            $maxWidth = (int) max(1, round($maxHeight * $ratio));
        } else {
            $maxHeight = (int) max(1, round($maxWidth / $ratio));
        }

        $newImage = imagecreatetruecolor($maxWidth, $maxHeight);
        if ($newImage === false) {
            return $this->storeOriginalImage($sourcePath, $destinationPath);
        }

        // Preserve transparency for PNG
        if ($ext === 'png') {
            imagesavealpha($newImage, true);
            $transparentColor = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
            imagefill($newImage, 0, 0, $transparentColor);
        }

        switch ($ext) {
            case 'jpeg':
            case 'jpg':
                $sourceImage = @imagecreatefromjpeg($sourcePath);
                break;
            case 'png':
                $sourceImage = @imagecreatefrompng($sourcePath);
                break;
            case 'webp':
                $sourceImage = function_exists('imagecreatefromwebp')
                    ? @imagecreatefromwebp($sourcePath)
                    : false;
                break;
            default:
                imagedestroy($newImage);
                return $this->storeOriginalImage($sourcePath, $destinationPath);
        }

        if ($sourceImage === false) {
            imagedestroy($newImage);
            return $this->storeOriginalImage($sourcePath, $destinationPath);
        }

        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $maxWidth, $maxHeight, $origWidth, $origHeight);

        $saved = false;
        switch ($ext) {
            case 'jpeg':
            case 'jpg':
                $saved = imagejpeg($newImage, $destinationPath, 85);
                break;
            case 'png':
                $saved = imagepng($newImage, $destinationPath, 8);
                break;
            case 'webp':
                $saved = function_exists('imagewebp')
                    ? imagewebp($newImage, $destinationPath, 85)
                    : false;
                break;
        }

        imagedestroy($newImage);
        imagedestroy($sourceImage);

        if ($saved) {
            return true;
        }

        return $this->storeOriginalImage($sourcePath, $destinationPath);
    }

    function createLanguageFiles($language_id, $base_url = '', $filepath = '', $format = 'both')
    {
        $is_whitelabel = $this->is_whitelabel();
        if ($is_whitelabel == "false") {
            if (empty($language_id)) {
                return false;
            }

            $bucketConfig = $this->getBucketConfiguration();
            if (!$bucketConfig) {
                return false;
            }

            $q = $this->selectRow(
                "language_key_value_master.value_name, language_key_master.language_key_id, language_key_master.key_name, language_key_master.key_type",
                "language_key_value_master, language_key_master",
                "language_key_value_master.language_id='$language_id' AND language_key_master.language_key_id=language_key_value_master.language_key_id"
            );

            $singleValues = array();
            $multiValues = array();
            $keyTypes = array();

            while ($data = mysqli_fetch_array($q)) {
                $key_name = str_replace("'", '', $data['key_name']);
                $key_name = str_replace("?", '', $key_name);

                if ($data['key_type'] == 0) {
                    $singleValues[$key_name] = $data['value_name'];
                } else {
                    $keyTypes[$key_name] = $data['key_type'];
                    if (array_key_exists($key_name, $multiValues)) {
                        $multiValues[$key_name] .= '~' . $data['value_name'];
                    } else {
                        $multiValues[$key_name] = $data['value_name'];
                    }
                }
            }

            // Merge single and multi values
            $allValues = array_merge($singleValues, $multiValues);

            if (count($allValues) == 0) {
                return false;
            }

            // Upload to S3
            require_once __DIR__ . '/S3Storage.php';
            $accessKey = $bucketConfig['bucket_access_key'];
            $secretKey = $bucketConfig['bucket_secret_access_key'];
            $region    = $bucketConfig['bucket_region'];
            $bucket    = $bucketConfig['bucket_name'];

            $s3 = new S3Storage($accessKey, $secretKey, $region, $bucket);

            $resultXml = false;
            $resultJson = false;

            // Create XML file
            if ($format === 'both' || $format === 'xml') {
                $rss_txt = '<?xml version="1.0" encoding="utf-8"?>';
                $rss_txt .= "<rss version='2.0'>";
                $rss_txt .= '<string>';
                foreach ($allValues as $keyName => $val) {
                    $val = htmlspecialchars((string) $val, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                    $rss_txt .= "<$keyName>$val</$keyName>";
                }
                $rss_txt .= '</string>';
                $rss_txt .= '</rss>';

                $tmpFileXml = tempnam(sys_get_temp_dir(), 'lang_xml_');
                if ($tmpFileXml !== false) {
                    file_put_contents($tmpFileXml, $rss_txt);
                    $s3KeyXml = 'company/language/' . $language_id . '.xml';
                    $resultXml = $s3->uploadFileFromPath($tmpFileXml, $s3KeyXml);
                    @unlink($tmpFileXml);
                }
            }

            // Create JSON file
            if ($format === 'both' || $format === 'json') {
                foreach ($allValues as $key => $value) {
                    if (isset($keyTypes[$key]) && $keyTypes[$key] == 1) {
                        $jsonValues[$key] = explode('~', $value);
                    } else {
                        $jsonValues[$key] = $value;
                    }
                }
                $formatted_json_new = json_encode($jsonValues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                // $formatted_json = json_encode($allValues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                $tmpFileJson = tempnam(sys_get_temp_dir(), 'lang_json_');
                if ($tmpFileJson !== false) {
                    file_put_contents($tmpFileJson, $formatted_json_new);
                    $s3KeyJson = 'company/language/' . $language_id . '.json';
                    $resultJson = $s3->uploadFileFromPath($tmpFileJson, $s3KeyJson);
                    @unlink($tmpFileJson);
                }
            }

            // Return appropriate result based on format
            if ($format === 'both') {
                return array('xml' => $resultXml, 'json' => $resultJson);
            } else if ($format === 'xml') {
                return $resultXml;
            } else if ($format === 'json') {
                return $resultJson;
            }

            return false;
        } else {
            return false;
        }
    }

    function getBucketConfiguration() {
        $qry = $this->selectRow("bucket_configuration.*", "bucket_configuration", "1", "LIMIT 1");
        if (mysqli_num_rows($qry) > 0) {
            return mysqli_fetch_assoc($qry);
        }
        return false;
    }

    function getLanguageFileS3Url($filename) {
        $baseUrl = $this->getLanguageBucketUrl();
        if ($baseUrl === false) {
            return false;
        }
        return $baseUrl . $filename;
    }

    function languageFileExistsInS3($filename) {
        $bucketConfig = $this->getBucketConfiguration();
        if (!$bucketConfig) {
            return false;
        }
        require_once __DIR__ . '/S3Storage.php';
        $accessKey = $bucketConfig['bucket_access_key'];
        $secretKey = $bucketConfig['bucket_secret_access_key'];
        $region = $bucketConfig['bucket_region'];
        $bucket = $bucketConfig['bucket_name'];
        $s3 = new S3Storage($accessKey, $secretKey, $region, $bucket);
        $s3Key = 'company/language/' . $filename;
        return $s3->fileExists($s3Key);
    }


    function loadLanguageXmlFromS3($language_id = '', $file_type = 'xml', $base_url = '') {
        # file_type can be xml or json
        if (empty($language_id)) {
            return false;
        }
        
        $filename = $language_id . '.' . $file_type;
        
        if (!$this->languageFileExistsInS3($filename)) {
            $result = $this->createLanguageFiles($language_id, $base_url, '');
            if ($result === false) {
                return false;
            }
        }
        
        // Get the full S3 URL
        $s3Url = $this->getLanguageFileS3Url($filename);
        if (!$s3Url) {
            return false;
        }
        
        if ($file_type === 'xml') {
            // Use simplexml_load_file directly with S3 URL
            $xml = @simplexml_load_file($s3Url);
            return $xml !== false ? $xml : false;
        } else if ($file_type === 'json') {
            // Use file_get_contents directly with S3 URL
            $jsonContent = @file_get_contents($s3Url);
            if ($jsonContent === false) {
                return false;
            }
            $json = @json_decode($jsonContent, true);
            return $json !== null ? $json : false;
        }
        return false;
    }

    function getBucketUrl($base_url = '') {
        $bucketConfig = $this->getBucketConfiguration();
        if ($bucketConfig && !empty($bucketConfig['master_bucket_url'])) {
            return rtrim($bucketConfig['master_bucket_url'], '/') . '/';
        }
        return $base_url . 'img/';
    }

    function getLanguageBucketUrl() {
        $bucketConfig = $this->getBucketConfiguration();
        if (!$bucketConfig || empty($bucketConfig['bucket_name']) || empty($bucketConfig['bucket_region'])) {
            return false;
        }
        $bucketName = $bucketConfig['bucket_name'];
        $region = $bucketConfig['bucket_region'];
        return "https://{$bucketName}.s3.{$region}.amazonaws.com/company/language/";
    }
    
    function createCompanyLanguageFiles($society_id, $language_id,$bms_admin_id, $created_by) {
        $is_whitelabel = $this->is_whitelabel();
        if ($is_whitelabel == "false") {
            if (empty($society_id) || empty($language_id)) {
                return false;
            }
            
            $bucketConfig = $this->getBucketConfiguration();
            if (!$bucketConfig) {
                return false;
            }
            $society_qry=$this->selectRow("society_id,sub_domain,api_key","society_master","society_id='$society_id'");
            $society_data=mysqli_fetch_assoc($society_qry);
            $sub_domain=$society_data['sub_domain'];
            $api_key=$society_data['api_key'];
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $sub_domain.'residentApiNew/societyAnalytics.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array("changeLanguageType" => "changeLanguageType","society_id" => "$society_id","languageType" => "1"),
            CURLOPT_HTTPHEADER => array(
                "key: $api_key"
            ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $this->insert_log("$society_id", "$bms_admin_id", "$created_by", "Language type changed to common language");

            // First, fetch ALL global language values for this language_id
            $qGlobal = $this->selectRow(
                "language_key_value_master.value_name, language_key_master.language_key_id, language_key_master.key_name, language_key_master.key_type",
                "language_key_value_master, language_key_master",
                "language_key_value_master.language_id='$language_id' AND language_key_master.language_key_id=language_key_value_master.language_key_id"
            );

            $globalSingleValues = array();
            $globalMultiValues = array();
            $keyTypes = array();
            while ($globalData = mysqli_fetch_array($qGlobal)) {
                $key_name = str_replace("'", '', $globalData['key_name']);
                $key_name = str_replace("?", '', $key_name);
                $keyTypes[$key_name] = $globalData['key_type'];
                if ($globalData['key_type'] == 0) {
                    $globalSingleValues[$key_name] = $globalData['value_name'];
                } else {
                    if (array_key_exists($key_name, $globalMultiValues)) {
                        $globalMultiValues[$key_name] .= '~' . $globalData['value_name'];
                    } else {
                        $globalMultiValues[$key_name] = $globalData['value_name'];
                    }
                }
            }

            // Merge global single and multi values
            $allGlobalValues = array_merge($globalSingleValues, $globalMultiValues);

            // Now fetch company's custom values (these will override global values)
            $qc = $this->selectRow(
                "language_key_value_master_society.key_name, language_key_value_master_society.value_name_society, language_key_master.key_type",
                "language_key_value_master_society, language_key_master",
                "language_key_value_master_society.language_id='$language_id' AND language_key_value_master_society.society_id='$society_id' AND language_key_value_master_society.key_name=language_key_master.key_name"
            );

            $customSingleValues = array();
            $customMultiValues = array();

            while ($oldData = mysqli_fetch_array($qc)) {
                $key_name = str_replace("'", '', $oldData['key_name']);
                $key_name = str_replace("?", '', $key_name);
                $keyTypes[$key_name] = $oldData['key_type'];
                if ($oldData['key_type'] == 0) {
                    $customSingleValues[$key_name] = $oldData['value_name_society'];
                } else {
                    if (array_key_exists($key_name, $customMultiValues)) {
                        $customMultiValues[$key_name] .= '~' . $oldData['value_name_society'];
                    } else {
                        $customMultiValues[$key_name] = $oldData['value_name_society'];
                    }
                }
            }

            $customValues = array_merge($customSingleValues, $customMultiValues);

            // Merge: custom values override global values
            $allValues = array_merge($allGlobalValues, $customValues);
            foreach ($allValues as $key => $value) {
                if (isset($keyTypes[$key]) && $keyTypes[$key] == 1) {
                    $jsonValues[$key] = explode('~', $value);
                } else {
                    $jsonValues[$key] = $value;
                }
            }
            $formatted_json_new = json_encode($jsonValues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (count($allValues) == 0) {
                // No data - delete from S3 if exists
                require_once __DIR__ . '/S3Storage.php';
                $accessKey = $bucketConfig['bucket_access_key'];
                $secretKey = $bucketConfig['bucket_secret_access_key'];
                $region    = $bucketConfig['bucket_region'];
                $bucket    = $bucketConfig['bucket_name'];
                $s3 = new S3Storage($accessKey, $secretKey, $region, $bucket);
                
                $s3KeyXml = 'company/language/company_' . $society_id . '_' . $language_id . '.xml';
                $s3KeyJson = 'company/language/company_' . $society_id . '_' . $language_id . '.json';
                $s3->deleteFile($s3KeyXml);
                $s3->deleteFile($s3KeyJson);
                return false;
            }

            // Upload to S3
            require_once __DIR__ . '/S3Storage.php';
            $accessKey = $bucketConfig['bucket_access_key'];
            $secretKey = $bucketConfig['bucket_secret_access_key'];
            $region    = $bucketConfig['bucket_region'];
            $bucket    = $bucketConfig['bucket_name'];

            $s3 = new S3Storage($accessKey, $secretKey, $region, $bucket);

            // Create XML file
            $rss_txt = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
            $rss_txt .= "<rss version='2.0'>\n<string>\n";

            foreach ($allValues as $keyName => $val) {
                $val = htmlspecialchars((string) $val, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $rss_txt .= "<$keyName>$val</$keyName>\n";
            }

            $rss_txt .= "</string>\n</rss>";

            $tmpFileXml = tempnam(sys_get_temp_dir(), 'comp_lang_xml_');
            if ($tmpFileXml === false) {
                return false;
            }
            file_put_contents($tmpFileXml, $rss_txt);

            $s3KeyXml = 'company/language/company_' . $society_id . '_' . $language_id . '.xml';
            $resultXml = $s3->uploadFileFromPath($tmpFileXml, $s3KeyXml);
            @unlink($tmpFileXml);

            $tmpFileJson = tempnam(sys_get_temp_dir(), 'comp_lang_json_');
            if ($tmpFileJson === false) {
                return $resultXml; 
            }
            file_put_contents($tmpFileJson, $formatted_json_new);

            $s3KeyJson = 'company/language/company_' . $society_id . '_' . $language_id . '.json';
            $resultJson = $s3->uploadFileFromPath($tmpFileJson, $s3KeyJson);
            @unlink($tmpFileJson);

            return ($resultXml && $resultJson);
        }else{
            return false;
        }
    }
    function deleteCompanyLanguageFiles($society_id,$bms_admin_id, $created_by){
        $is_whitelabel = $this->is_whitelabel();
        if ($is_whitelabel == "false") {
            if (empty($society_id)) {
                return false;
            }
            $bucketConfig = $this->getBucketConfiguration();
            if (!$bucketConfig) {
                return false;
            }
            require_once __DIR__ . '/S3Storage.php';
            $accessKey = $bucketConfig['bucket_access_key'];
            $secretKey = $bucketConfig['bucket_secret_access_key'];
            $region    = $bucketConfig['bucket_region'];
            $bucket    = $bucketConfig['bucket_name'];
            $s3 = new S3Storage($accessKey, $secretKey, $region, $bucket);
            $language_qry=$this->selectRow("language_id","language_master","");
            while($language_data=mysqli_fetch_assoc($language_qry)){
                $language_id=$language_data['language_id'];
                $s3KeyXml = 'company/language/company_' . $society_id . '_' . $language_id . '.xml';
                $s3KeyJson = 'company/language/company_' . $society_id . '_' . $language_id . '.json';
                $s3->deleteFile($s3KeyXml);
                $s3->deleteFile($s3KeyJson);
            }
            $society_qry=$this->selectRow("society_id,sub_domain,api_key","society_master","society_id='$society_id'");
            $society_data=mysqli_fetch_assoc($society_qry);
            $sub_domain=$society_data['sub_domain'];
            $api_key=$society_data['api_key'];
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $sub_domain.'residentApiNew/societyAnalytics.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array("changeLanguageType" => "changeLanguageType","society_id" => "$society_id","languageType" => "0"),
            CURLOPT_HTTPHEADER => array(
                "key: $api_key"
            ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $this->insert_log("$society_id", "$bms_admin_id", "$created_by", "Language type changed to common language");
        }
    }

    // send otp on whatsapp
    function send_otp_on_mail()
    {
        return 0;
    }
    function whatsapp_api_data()
    {
        return [
            'company_api_key' => EnvLoader::get('WHATSAPP_COMPANY_API_KEY'),
            'whatsapp_api_access_token' => EnvLoader::get('WHATSAPP_ACCESS_TOKEN'),
            'whatsapp_api_url' => EnvLoader::get('WHATSAPP_API_URL'),
        ];
    }
    
    function get_server_key() {
        return 'key=' . EnvLoader::get('FCM_SERVER_KEY');
    }

    function map_key() {
        return EnvLoader::get('MAPS_API_KEY');
    }

    function translate_api_key() {
        return EnvLoader::get('TRANSLATE_API_KEY');
    }

    function language_translate_code_map()
    {
        static $map = null;
        if (is_array($map)) {
            return $map;
        }

        $map = [];
        $q = $this->select('language_master', '', 'ORDER BY language_id ASC');
        if ($q) {
            while ($row = mysqli_fetch_assoc($q)) {
                $name = trim((string) ($row['language_name'] ?? ''));
                $code = strtolower(trim((string) ($row['language_code'] ?? '')));
                if ($name !== '' && $code !== '') {
                    $map[$name] = $code;
                }
            }
        }
        return $map;
    }

    function language_translate_languages_from_db()
    {
        $languages = [];
        $q = $this->select('language_master', '', 'ORDER BY language_id ASC');
        if (!$q) {
            return $languages;
        }

        while ($row = mysqli_fetch_assoc($q)) {
            $languages[] = [
                'language_id' => (int) ($row['language_id'] ?? 0),
                'language_name' => trim((string) ($row['language_name'] ?? '')),
                'language_code' => strtolower(trim((string) ($row['language_code'] ?? ''))),
                'is_english_language' => (int) ($row['is_english_language'] ?? 0),
            ];
        }
        return $languages;
    }

    function language_translate_monthly_file()
    {
        return dirname(__DIR__) . '/../img/monthly_totals.txt';
    }

    function language_translate_monthly_limit()
    {
        return 490000;
    }

    function language_translate_get_monthly_total($monthlyTotalFile = null)
    {
        $monthlyTotalFile = $monthlyTotalFile ?: $this->language_translate_monthly_file();
        $currentMonthYear = date('Y-m');
        $currentTotal = 0;

        if (file_exists($monthlyTotalFile)) {
            $monthlyTotals = file($monthlyTotalFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($monthlyTotals as $entry) {
                $parts = explode(',', $entry);
                if (count($parts) >= 2 && $parts[0] === $currentMonthYear) {
                    $currentTotal = (int) $parts[1];
                    break;
                }
            }
        }

        return [$currentMonthYear, $currentTotal];
    }

    function language_translate_update_monthly_total($translatedCharacters, $monthlyTotalFile = null)
    {
        if ($translatedCharacters <= 0) {
            return $this->language_translate_get_monthly_total($monthlyTotalFile)[1];
        }

        $monthlyTotalFile = $monthlyTotalFile ?: $this->language_translate_monthly_file();
        list($currentMonthYear, $currentTotal) = $this->language_translate_get_monthly_total($monthlyTotalFile);
        $newTotal = $currentTotal + $translatedCharacters;

        $totals = [];
        if (file_exists($monthlyTotalFile)) {
            $monthlyTotals = file($monthlyTotalFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($monthlyTotals as $entry) {
                $parts = explode(',', $entry);
                if (count($parts) >= 2) {
                    $totals[$parts[0]] = (int) $parts[1];
                }
            }
        }
        $totals[$currentMonthYear] = $newTotal;

        $content = '';
        foreach ($totals as $monthYear => $total) {
            $content .= $monthYear . ',' . $total . PHP_EOL;
        }
        file_put_contents($monthlyTotalFile, $content);

        return $newTotal;
    }

    function language_translate_resolve_code($languageName, $languageCodeFromDb = '')
    {
        $languageCodeFromDb = strtolower(trim((string) $languageCodeFromDb));
        if ($languageCodeFromDb !== '') {
            return $languageCodeFromDb;
        }

        $map = $this->language_translate_code_map();
        $languageNameNew = trim((string) $languageName);
        return isset($map[$languageNameNew]) ? $map[$languageNameNew] : '';
    }

    function language_translate_is_english($language)
    {
        if (!empty($language['is_english_language'])) {
            return true;
        }

        $code = $this->language_translate_resolve_code(
            $language['language_name'] ?? '',
            $language['language_code'] ?? ''
        );

        return $code === 'en';
    }

    function language_translate_google($text, $targetLang)
    {
        $apiKey = $this->translate_api_key();
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://translation.googleapis.com/language/translate/v2?key=' . $apiKey,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'q' => $text,
                'target' => $targetLang,
                'format' => 'text',
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err) {
            return [
                'success' => false,
                'text' => '',
                'error' => [
                    'code' => $httpCode,
                    'message' => $err,
                    'response' => $response,
                ],
            ];
        }

        $responseData = json_decode($response, true);
        if (isset($responseData['data']['translations'][0]['translatedText'])) {
            return [
                'success' => true,
                'text' => $responseData['data']['translations'][0]['translatedText'],
                'error' => null,
            ];
        }

        return [
            'success' => false,
            'text' => '',
            'error' => [
                'code' => $httpCode,
                'message' => 'Translation API returned unexpected format',
                'response' => $responseData ?? $response,
            ],
        ];
    }

    function language_translate_all($text, $languages, &$translationErrors = [])
    {
        $results = [];
        $translatedCharacters = 0;
        $maxCharacters = $this->language_translate_monthly_limit();
        list($currentMonthYear, $currentTotal) = $this->language_translate_get_monthly_total();

        $potentialCharacters = 0;
        foreach ($languages as $language) {
            if ($this->language_translate_is_english($language)) {
                continue;
            }
            $targetLang = $this->language_translate_resolve_code(
                $language['language_name'] ?? '',
                $language['language_code'] ?? ''
            );
            if ($targetLang !== '') {
                $potentialCharacters += strlen($text);
            }
        }

        if ($currentTotal >= $maxCharacters) {
            $translationErrors[] = [
                'message' => 'Monthly character limit (4 lakh) already exceeded',
                'current_total' => $currentTotal,
                'month_year' => $currentMonthYear,
            ];
            foreach ($languages as $language) {
                $name = trim($language['language_name'] ?? '');
                $results[$name] = $this->language_translate_is_english($language) ? $text : '';
            }
            return $results;
        }

        if (($currentTotal + $potentialCharacters) > $maxCharacters) {
            $translationErrors[] = [
                'message' => 'This operation would exceed monthly character limit (4 lakh)',
                'current_total' => $currentTotal,
                'attempted_addition' => $potentialCharacters,
                'month_year' => $currentMonthYear,
            ];
            foreach ($languages as $language) {
                $name = trim($language['language_name'] ?? '');
                $results[$name] = $this->language_translate_is_english($language) ? $text : '';
            }
            return $results;
        }

        foreach ($languages as $language) {
            $languageNameNew = trim($language['language_name'] ?? '');
            $targetLang = $this->language_translate_resolve_code($languageNameNew, $language['language_code'] ?? '');

            if ($this->language_translate_is_english($language)) {
                $results[$languageNameNew] = $text;
                continue;
            }

            if ($targetLang === '') {
                $results[$languageNameNew] = '';
                $translationErrors[] = [
                    'language' => $languageNameNew,
                    'message' => 'Language code missing in language_master',
                ];
                continue;
            }

            $translated = $this->language_translate_google($text, $targetLang);
            if ($translated['success']) {
                $results[$languageNameNew] = $translated['text'];
                $translatedCharacters += strlen($translated['text']);
            } else {
                $results[$languageNameNew] = '';
                $translationErrors[] = array_merge(
                    ['language' => $languageNameNew],
                    $translated['error'] ?: ['message' => 'Translation failed']
                );
            }
        }

        $this->language_translate_update_monthly_total($translatedCharacters);

        return $results;
    }

    function language_key_to_snake_case($text)
    {
        $text = trim((string) $text);
        $text = preg_replace('/\s+/', '_', $text);
        $text = preg_replace('/([a-z])([A-Z])/', '$1_$2', $text);
        $text = preg_replace('/[^a-zA-Z0-9_]+/', '_', $text);
        $text = preg_replace('/_+/', '_', $text);
        $text = trim($text, '_');
        return strtolower($text);
    }

    function android_url() {
        return EnvLoader::get('ANDROID_URL');
    }

    function ios_url() {
        return EnvLoader::get('IOS_URL');
    }

    function support_url() {
        return EnvLoader::get('SUPPORT_URL');
    }

    function company_url() {
        return EnvLoader::get('COMPANY_URL');
    }

    function company_id() {
        return (int) (EnvLoader::get('COMPANY_ID') ?? 0);
    }

    function app_name()
    {
        return EnvLoader::get('APP_NAME');
    }
    function short_app_name()
    {
        return EnvLoader::get('APP_NAME_SHORT');
    }
    function is_whitelabel()
    {
        return EnvLoader::get('IS_WHITELABEL') ?? false;
    }
    function app_signin_key()
    {
        return EnvLoader::get('APP_SIGNIN_KEY') ?? false;
    }
    function aws_access_key()
    {
        return EnvLoader::get('AWS_ACCESS_KEY_ID') ?? '';
    }
    function aws_secret_key()
    {
        return EnvLoader::get('AWS_SECRET_ACCESS_KEY') ?? '';
    }
    function distance_matrix_key()
    {
        return EnvLoader::get('DISTANCE_MATRIX_API_KEY') ?? '';
    }
    function turnstile_site()
    {
        return [
            'site_key'   => EnvLoader::get('TURNSTILE_SITE_KEY') ?? false,
            'secret_key' => EnvLoader::get('TURNSTILE_SECRET_KEY') ?? false,
        ];
    }

    function verify_turnstile($token)
    {
        $keys = $this->turnstile_site();
        if (empty($keys['site_key']) || empty($keys['secret_key'])) {
            return true;
        }
        if (empty($token)) {
            return false;
        }
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'secret' => $keys['secret_key'],
                'response' => $token,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        $result = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($result, true);

        return !empty($data['success']);
    }

    function require_turnstile($redirectUrl = null)
    {
        if ($this->verify_turnstile($_POST['cf-turnstile-response'] ?? '')) {
            return true;
        }
        if ($redirectUrl !== null) {
            $_SESSION['msg1'] = "Security verification failed. Please try again.";
            header("location:$redirectUrl");
            exit;
        }

        return false;
    }

    // =======================
    // Rate limiting (file based)
    // =======================
    function rl_get_client_ip_myco()
    {
        // Cloudflare sets the real client IP here when traffic is proxied.
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($parts[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    /**
     * Simple file-based rate limit (per IP + action).
     * Returns true if request is allowed, false if blocked.
     */
    function rl_rate_limit_myco($action, $maxPerMinute, $maxPerHour)
    {
        $ip = $this->rl_get_client_ip_myco();
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'myco_rate_limit';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $key = hash('sha256', $ip . '|' . $action);
        $file = $dir . DIRECTORY_SEPARATOR . $key . '.json';
        $now = time();

        $data = ['minute' => [], 'hour' => []];

        $fp = @fopen($file, 'c+');
        if (!$fp) {
            // If storage fails, allow (avoid breaking all traffic).
            return true;
        }

        $okLock = flock($fp, LOCK_EX);
        if (!$okLock) {
            fclose($fp);
            return true;
        }

        $raw = stream_get_contents($fp);
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $data = array_merge($data, $decoded);
            }
        }

        // Keep only recent timestamps
        $data['minute'] = array_values(array_filter($data['minute'] ?? [], function ($t) use ($now) {
            return is_numeric($t) && (int)$t > ($now - 60);
        }));
        $data['hour'] = array_values(array_filter($data['hour'] ?? [], function ($t) use ($now) {
            return is_numeric($t) && (int)$t > ($now - 3600);
        }));

        $countMinute = count($data['minute']);
        $countHour = count($data['hour']);

        if ($countMinute >= (int)$maxPerMinute || $countHour >= (int)$maxPerHour) {
            flock($fp, LOCK_UN);
            fclose($fp);
            return false;
        }

        // Record current request
        $data['minute'][] = $now;
        $data['hour'][] = $now;

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($data));
        fflush($fp);

        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    }

    /**
     * Validate and store an uploaded file with extension + MIME checks.
     *
     * @param array  $file       One $_FILES[...] entry
     * @param string $destDir    Destination directory
     * @param string $prefix     Safe filename prefix
     * @param array  $allowedExt Lowercase extensions without dot
     * @param int    $maxBytes   Max size in bytes (exclusive upper bound)
     * @return array{ok:bool,filename:string,error:string}
     */
    function saveValidatedUpload($file, $destDir, $prefix, array $allowedExt, $maxBytes)
    {
        $empty = ['ok' => true, 'filename' => '', 'error' => ''];

        if (!is_array($file) || !isset($file['error'])) {
            return $empty;
        }

        if ((int)$file['error'] === UPLOAD_ERR_NO_FILE) {
            return $empty;
        }

        if ((int)$file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'filename' => '', 'error' => 'Upload failed. Please try again.'];
        }

        $tmp = isset($file['tmp_name']) ? (string)$file['tmp_name'] : '';
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return ['ok' => false, 'filename' => '', 'error' => 'Invalid upload.'];
        }

        $size = (int)($file['size'] ?? 0);
        if ($size <= 0 || $size >= (int)$maxBytes) {
            $mb = max(1, (int)floor(((int)$maxBytes) / 1048576));
            return ['ok' => false, 'filename' => '', 'error' => "Attachment too large or empty. Must be less than {$mb} MB"];
        }

        $original = (string)($file['name'] ?? '');
        if ($original === '' || strpos($original, "\0") !== false || preg_match('/[\\\\\/]/', $original)) {
            return ['ok' => false, 'filename' => '', 'error' => 'Invalid file name.'];
        }

        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $allowedExt = array_values(array_unique(array_map('strtolower', $allowedExt)));
        if ($ext === '' || !in_array($ext, $allowedExt, true)) {
            return [
                'ok' => false,
                'filename' => '',
                'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExt),
            ];
        }

        $mimeMap = [
            'jpg'  => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png'  => ['image/png'],
            'gif'  => ['image/gif'],
            'webp' => ['image/webp'],
            'mp4'  => ['video/mp4', 'application/mp4', 'video/mpeg'],
            'webm' => ['video/webm'],
            'avi'  => ['video/x-msvideo', 'video/avi', 'application/x-troff-msvideo'],
            'wmv'  => ['video/x-ms-wmv', 'video/x-msvideo'],
            'csv'  => ['text/plain', 'text/csv', 'application/csv', 'application/vnd.ms-excel', 'text/x-csv'],
            'docx' => [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/zip',
                'application/octet-stream',
            ],
            'pdf'  => ['application/pdf'],
        ];

        $mime = '';
        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = (string)$finfo->file($tmp);
        } elseif (function_exists('mime_content_type')) {
            $mime = (string)@mime_content_type($tmp);
        }

        if (isset($mimeMap[$ext]) && $mime !== '' && !in_array($mime, $mimeMap[$ext], true)) {
            return ['ok' => false, 'filename' => '', 'error' => 'File content does not match the allowed type.'];
        }

        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $imageExts, true)) {
            $imgInfo = @getimagesize($tmp);
            if ($imgInfo === false) {
                return ['ok' => false, 'filename' => '', 'error' => 'Invalid image file.'];
            }
        }

        if ($ext === 'docx') {
            $fh = @fopen($tmp, 'rb');
            $magic = $fh ? (string)fread($fh, 4) : '';
            if ($fh) {
                fclose($fh);
            }
            if (strpos($magic, 'PK') !== 0) {
                return ['ok' => false, 'filename' => '', 'error' => 'Invalid document file.'];
            }
        }

        $destDir = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $destDir), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!is_dir($destDir) && !@mkdir($destDir, 0755, true) && !is_dir($destDir)) {
            return ['ok' => false, 'filename' => '', 'error' => 'Upload directory is not available.'];
        }

        $safePrefix = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$prefix);
        if ($safePrefix === '') {
            $safePrefix = 'upload';
        }

        try {
            $rand = bin2hex(random_bytes(8));
        } catch (Exception $e) {
            $rand = bin2hex(openssl_random_pseudo_bytes(8));
        }

        $filename = $safePrefix . '_' . $rand . '_' . time() . '.' . $ext;
        $dest = $destDir . $filename;

        if (!move_uploaded_file($tmp, $dest)) {
            return ['ok' => false, 'filename' => '', 'error' => 'Could not save uploaded file.'];
        }

        @chmod($dest, 0644);

        return ['ok' => true, 'filename' => $filename, 'error' => ''];
    }
}
