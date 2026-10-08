<?php 
include '../common/objectController.php';


if(!empty($_POST)){

  extract($_POST);
 
  if(isset($_POST['action']) && $_POST['action'] == "addKyc" ) {

    $m->set_data('kyc_api_name',test_input($kyc_api_name));
    $m->set_data('kyc_api_price',test_input($kyc_api_price));
    $m->set_data('kyc_api_response',test_input($kyc_api_response));
    $m->set_data('kyc_api_common',test_input($kyc_api_common));
    $m->set_data('kyc_api_url',test_input($kyc_api_url));
    $m->set_data('api_method',test_input($api_method));
    $m->set_data('send_number_in_url',test_input($send_number_in_url));
    $m->set_data('kyc_parent_id',test_input($kyc_parent_id));
    $m->set_data('row_data_key',test_input($row_data_key));
            
    $a =array(
      'kyc_api_name'=> $m->get_data('kyc_api_name'),
      'kyc_api_price'=>$m->get_data('kyc_api_price'),
      'kyc_api_response'=>$m->get_data('kyc_api_response'),
      'kyc_api_common'=>$m->get_data('kyc_api_common'),
      'kyc_api_url'=>$m->get_data('kyc_api_url'),
      'api_method'=>$m->get_data('api_method'),
      'send_number_in_url'=>$m->get_data('send_number_in_url'),
      'kyc_parent_id'=>$m->get_data('kyc_parent_id'),
      'row_data_key'=>$m->get_data('row_data_key'),
      'created_by'=>$admin_name,
      'created_date'=>date("Y-m-d H:i:s"),
      
    );

    $q=$d->insert("document_kyc_master",$a);
    
    if($q>0) {
      
      $d->insert_log("$society_id","$bms_admin_id","$created_by","New Kyc Api $kyc_api_name Added");
      $_SESSION['msg']="API Added Successfully";
      header("location:../documentKycList");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../documentKycList");
    }
  }

  
  if(isset($_POST['action']) && $_POST['action'] == "editKyc" ) {

    $m->set_data('kyc_api_name',test_input($kyc_api_name));
    $m->set_data('kyc_api_price',test_input($kyc_api_price));
    $m->set_data('kyc_api_response',test_input($kyc_api_response));
    $m->set_data('kyc_api_common',test_input($kyc_api_common));
    $m->set_data('kyc_api_url',test_input($kyc_api_url));
    $m->set_data('api_method',test_input($api_method));
    $m->set_data('send_number_in_url',test_input($send_number_in_url));
    $m->set_data('kyc_parent_id',test_input($kyc_parent_id));
    $m->set_data('row_data_key',test_input($row_data_key));

    $a =array(
      'kyc_api_name'=> $m->get_data('kyc_api_name'),
      'kyc_api_price'=>$m->get_data('kyc_api_price'),
      'kyc_api_response'=>$m->get_data('kyc_api_response'),
      'kyc_api_common'=>$m->get_data('kyc_api_common'),
      'kyc_api_url'=>$m->get_data('kyc_api_url'),
      'api_method'=>$m->get_data('api_method'),
      'send_number_in_url'=>$m->get_data('send_number_in_url'),
      'kyc_parent_id'=>$m->get_data('kyc_parent_id'),
      'row_data_key'=>$m->get_data('row_data_key'),
      'updated_by'=>$admin_name,
      'updated_date'=>date("Y-m-d H:i:s"),
      
    );
    $q=$d->update("document_kyc_master",$a,"kyc_api_type='$kyc_api_type'");
    if($q>0) {
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Api $kyc_api_name Edited");
      $_SESSION['msg']="API Updated Successfully";
      header("location:../documentKycList");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../documentKycList");
    }
  }

  if(isset($_POST['action']) && $_POST['action'] == "delKyc" ) {
    
    $q=$d->delete("document_kyc_master","kyc_api_type='$kyc_api_type'");
    if($q>0) {
      $d->insert_log("$society_id","$bms_admin_id","$created_by","Api $kyc_api_name  Deleted Successfully");
      $_SESSION['msg']="Document Kyc Deleted Successfully";
      header("location:../documentKycList");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../documentKycList");
    }

  }
  // Edit fidypay cred
  if(isset($_POST['action']) && $_POST['action'] == "editFidyPay" ) {

    $m->set_data('base_url',test_input($base_url));
    $m->set_data('client_id',test_input($client_id));
    $m->set_data('client_secret',test_input($client_secret));
    $m->set_data('authorization',test_input($authorization));
    $m->set_data('bank_name',test_input($bank_name));
    $m->set_data('account_number',test_input($account_number));
    $m->set_data('ifsc_code',test_input($ifsc_code));

     $a =array(
      'base_url'=> $m->get_data('base_url'),
      'client_id'=>$m->get_data('client_id'),
      'client_secret'=>$m->get_data('client_secret'),
      'authorization'=>$m->get_data('authorization'),
      'bank_name'=>$m->get_data('bank_name'),
      'account_number'=>$m->get_data('account_number'),
      'ifsc_code'=>$m->get_data('ifsc_code'),
    );

    $q=$d->update("fidypay_master",$a,"fidypay_id='$fidypay_id'");

    if($q>0) {
      $d->insert_log("$society_id","$bms_admin_id","$created_by","FidyPay Credentials Edited");
      $_SESSION['msg']="Fidypay Credentials Updated";
      header("location:../documentKycList");
    } else {
      $_SESSION['msg1']="Something Wrong";
      header("location:../documentKycList");
    }

  }

 

}else{
  header('location:../login');
}

?>
