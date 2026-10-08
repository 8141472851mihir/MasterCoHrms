<?php 
include '../common/objectController.php';


if(!empty($_POST)){

  //print_r($_POST);exit;

  extract($_POST);

  if(isset($_POST['action']) && $_POST['action'] == "addCompanyPrice" ) {

    $m->set_data('society_id',test_input($society_id));
    $m->set_data('kyc_api_type',test_input($kyc_api_type));
    $m->set_data('custom_price',test_input($custom_price));
           
    $a =array(
      'society_id'=> $m->get_data('society_id'),
      'kyc_api_type'=>$m->get_data('kyc_api_type'),
      'custom_price'=>$m->get_data('custom_price'),
      'created_by'=>$admin_name,
      'created_date'=>date("Y-m-d H:i:s"),      
    );

    $q=$d->insert("kycapi_companyprice_master",$a);
    
    if($q>0) {

      //company name and api name for log
      $q_society_master= $d->select("society_master","society_id=$society_id",""); 
      $society_row = mysqli_fetch_array($q_society_master);
      $society_name = $society_row['society_name'];

      $q_kyc_api_type= $d->select("document_kyc_master","kyc_api_type=$kyc_api_type",""); 
      $kyc_api_type_row = mysqli_fetch_array($q_kyc_api_type);
      $api_name = $kyc_api_type_row['kyc_api_name'];
      
      $d->insert_log("$society_id","$bms_admin_id","$created_by","New kyc Api $api_name Added with company $society_name Successfully ");
      $_SESSION['msg']="API Added Successfully";
      header("location:../kycApiCompanyPriceList");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../kycApiCompanyPriceList");
    }
  }

  
  if(isset($_POST['action']) && $_POST['action'] == "editCompanyPrice") {

    echo "edit";

    $m->set_data('custom_price',test_input($custom_price));

    $a =array(
      'custom_price'=>$m->get_data('custom_price'),
      'updated_by'=>$admin_name,
      'updated_date'=>date("Y-m-d H:i:s"),
      
    );
    $q=$d->update("kycapi_companyprice_master",$a,"cp_id=$cp_id");
    if($q>0) {

      //company name and api name for log
      $q_society_master= $d->select("society_master","society_id=$society_id",""); 
      $society_row = mysqli_fetch_array($q_society_master);
      $society_name = $society_row['society_name'];

      $q_kyc_api_type= $d->select("document_kyc_master","kyc_api_type=$kyc_api_type",""); 
      $kyc_api_type_row = mysqli_fetch_array($q_kyc_api_type);
      $api_name = $kyc_api_type_row['kyc_api_name'];

      $d->insert_log("$society_id","$bms_admin_id","$created_by","Api $api_name price edited with company $society_name Successfully");
      $_SESSION['msg']="API Updated Successfully";
      header("location:../kycApiCompanyPriceList");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../kycApiCompanyPriceList");
    }
  }

  if(isset($_POST['action']) && $_POST['action'] == "delKycApiCompany" ) {

  
    $q=$d->delete("kycapi_companyprice_master","cp_id='$cp_id'");
    if($q>0) {
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Api Deleted Successfully");
      $_SESSION['msg']="Api Deleted Successfully";
      header("location:../kycApiCompanyPriceList");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../kycApiCompanyPriceList");
    }

  }

}else{
  header('location:../login');
}

?>
