<?php 
  include_once 'lib.php';
  echo '<pre>';
  $q = $d->select("society_master" ,"","order by society_id DESC");
  $i = 0;
  while($data=mysqli_fetch_array($q)){
    $baseUrl= $data['sub_domain'];
    $society_id= $data['society_id'];
    if($data['society_total_units']=='0'){
      $post = array(
        'count_units' => 'count_units',
        'society_id' => $society_id,
      );
      $res = $d->callCompanyApiEnc($baseUrl, 'countSocietyUnitsController.php', $post);

      if(isset($res['status']) && $res['status']=='200' && isset($res['totalUnits']) && $res['totalUnits'] > '0'){
        $i++;
        print_r($res);
        $a['society_total_units'] = $res['totalUnits'];
        $d->update("society_master",$a,"society_id='$society_id'");
      }
    }
  }
  if($i>0){ echo 'Company units count updated'; }else{ echo 'All Company are already updated'; }
?>
