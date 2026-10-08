<?php
include '../common/objectController.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (isset($_POST) && !empty($_POST)) {
  if (isset($update)) {
    $now = date('Y-m-d');
    $post = array(
    'society_id' => $society_id,
    'changePlan' => 'changePlan',
    'plan_expire_date' => $update_plan,
    'last_renew_date' => $now,
    'package_id' => $package_id,
    );
    $json = $d->callCompanyApiEnc($society_base_url, 'buildingChangePlanController.php', $post);

    // main server
    if (count($json) > 0) {
      if ($json['status'] == 200 && $json['status'] == 200) {
        $m->set_data('update_plan', $update_plan);
        $m->set_data('last_renew_date', $now);
        $a = array(
          'last_renew_date' => $m->get_data('last_renew_date'),
          'plan_expire_date' => $m->get_data('update_plan'),
        );
        $q = $d->update("society_master", $a, "society_id='$society_id'");
        if ($q == TRUE) {
          $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "$society_name Company Plan Changed");
          $_SESSION['msg'] = $json['message'];
          header("Location: ../companyPlanExpire");
        } else {
          $_SESSION['msg1'] = "Something Wrong";
          header("Location: ../companyPlanExpire");
        }
      } else {
        $_SESSION['msg1'] = "Something Wrong";
        header("Location: ../companyPlanExpire");
      }
    } else {
      $_SESSION['msg1'] = "Something Wrong in Company Server";
      header("Location: ../companyPlanExpire");
    }
  }
  if (isset($planModule) && $planModule == 'planModule') {
    $m->set_data('plan_name', trim($plan_name));
    $m->set_data('plan_value', $plan_value);
    $data = array(
      'plan_name' => $m->get_data('plan_name'),
      'plan_value' => $m->get_data('plan_value'),
      'added_by' => $bms_admin_id,
      'added_date' => date("y-m-d h:i:s"),
    );
    $q = $d->insert("manage_plan", $data);
    if ($q > 0) {
      $_SESSION['msg'] = "New plan added successfully.";
    } else {
      $_SESSION['msg1'] = "Failed to add plan.";
    }
    header("Location: ../managePlan");
    exit();
    
  } 
  else if(isset($planModule) && $planModule == 'planModuleEdit') {
    $m->set_data('plan_name', trim($plan_name));
    $m->set_data('plan_value', $plan_value);
    $data = array(
      'plan_name' => $m->get_data('plan_name'),
      'plan_value' => $m->get_data('plan_value'),
      'updated_by' => $bms_admin_id,
      'updated_date' => date("y-m-d h:i:s"),
    );
    $q = $d->update("manage_plan", $data, "plan_id='$editId'");
    if ($q > 0) {
      $_SESSION['msg'] = "Plan updated successfully.";
    } else {
      $_SESSION['msg1'] = "Failed to update plan.";
    }
    header("Location: ../managePlan");
    exit();
  } else {
    $_SESSION['msg1'] = "Invalid request.";
    header("Location: ../managePlan");
    exit();
  }
}