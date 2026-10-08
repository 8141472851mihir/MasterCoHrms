<?php

include_once 'common/object.php';
extract(array_map("test_input", $_POST));
if (isset($getCrmDetailsList) && $getCrmDetailsList == "getCrmDetailsList") {
  if (isset($_POST['crm_request_id'])) {
    $crm_request_id = $d->sanitizeActionIdAsInt($_POST['crm_request_id']);
    $result = $d->selectRow("crm_request_master.*,society_master.society_id,society_master.society_name,manage_plan.plan_value,manage_plan.plan_name,bms_admin_master.admin_id,bms_admin_master.admin_name", "crm_request_master LEFT JOIN society_master ON crm_request_master.society_id = society_master.society_id LEFT JOIN manage_plan ON crm_request_master.crm_package_id = manage_plan.plan_value LEFT JOIN bms_admin_master ON crm_request_master.crm_rejected_by = bms_admin_master.admin_id", "crm_request_id = '$crm_request_id'");

    if ($result && $row = mysqli_fetch_assoc($result)) {
      $payment_status = $row['crm_payment_status'] == 1 ? "Received" : "Not Received";
      $payment_modes = [
        1 => "Online Bank Transfer",
        2 => "Cheque",
        3 => "UPI",
        4 => "Cash"
      ];
      $payment_mode = isset($payment_modes[$row['crm_payment_mode']]) ? $payment_modes[$row['crm_payment_mode']] : "Unknown";
      $request_statuses = [
        0 => "Pending",
        1 => "Approved",
        2 => "Rejected"
      ];
      $request_status = isset($request_statuses[$row['request_status']]) ? $request_statuses[$row['request_status']] : "Unknown";

      echo "
<div class='container-fluid'>
  <div class='row'>
    " . displayField('Company Name', $row['society_name']) . "
    " . displayField('CRM Plan', $row['plan_name']) . "
    " . displayField('CRM Limit', $row['crm_limit']) . "
    " . displayField('CRM Trial Days', $row['crm_trial_days']) . "
    " . displayField('CRM Plan Expire Date', $row['crm_plan_expire_date']) . "
    " . displayField('CRM Yearly Ticket Size', $row['yearly_ticket_size']) . "
    " . displayField('CRM Received Ticket Size', $row['received_ticket_size']) . "
    " . displayField('CRM Per Employee Price', $row['per_employee_price']) . "
    " . displayField('CRM Payment Status', $payment_status) . "
    " . displayField('CRM Payment Amount', $row['crm_payment_amount']) . "
    " . displayField('CRM Payment Mode', $payment_mode) . "
    " . displayField('CRM Request Created By', $row['crm_request_created_by']) . "
    " . displayField('CRM Request Date', $row['crm_request_created_date']) . "
    " . displayField('CRM Request Status', $request_status) . "

    <div class='col-12 mb-2'>
      <div class='d-flex border-bottom pb-1'>
        <span class='text-muted' style='min-width: 150px;'>CRM Remark:</span>
        <strong class='text-dark' style='white-space: pre-wrap; word-break: break-word;'>{$row['crm_remark']}</strong>
      </div>
    </div>";

      if (!empty($row['crm_payment_attachment']) && file_exists("../img/society_requests/" . $row['crm_payment_attachment'])) {
        $attachment = $row['crm_payment_attachment'];
        $ext = strtolower(pathinfo($attachment, PATHINFO_EXTENSION));
        $image_exts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $icon_html = "";
        if (in_array($ext, $image_exts)) {
          $icon_html = "<img src='../img/society_requests/{$attachment}' alt='Attachment' width='30' height='30' class='category-icon float-right'>";
        } else {
          $icon_html = "<i class='fa fa-file-word-o text-primary' style='font-size:30px;float:right;'></i>";
          if ($ext === 'pdf') {
            $icon_html = "<i class='fa fa-file-pdf-o text-danger' style='font-size:30px;float:right;'></i>";
          } elseif (in_array($ext, ['doc', 'docx'])) {
            $icon_html = "<i class='fa fa-file-word-o text-primary' style='font-size:30px;float:right;'></i>";
          } elseif (in_array($ext, ['xls', 'xlsx'])) {
            $icon_html = "<i class='fa fa-file-excel-o text-success' style='font-size:30px;float:right;'></i>";
          }
        }
        echo "
    <div class='col-md-6 mb-2'>
        <div class='d-flex justify-content-between border-bottom pb-1'>
            <span class='text-muted'>CRM Payment Attachment:</span>
            <a data-fancybox='images'
               data-caption='Payment Attachment'
               href='../img/society_requests/{$attachment}'
               target='_blank'>
               {$icon_html}
            </a>
        </div>
    </div>";
      }

      if (isset($row['request_status']) && $row['request_status'] == 2) {
        echo "
    <div class='col-12 mt-4'>
      <h6 class='text-danger border-bottom pb-1'>Rejection Details</h6>
    </div>
    " . displayField('CRM Rejected Date', $row['crm_rejected_date']) . "
    " . displayField('CRM Rejected By', $row['admin_name']) . "
    <div class='col-12 mb-2'>
      <div class='d-flex border-bottom pb-1'>
        <span class='text-muted' style='min-width: 150px;'>Rejected Reason:</span>
        <strong class='text-dark' style='white-space: pre-wrap; word-break: break-word;'>{$row['crm_rejected_reason']}</strong>
      </div>
    </div>";
      }
      echo "</div></div>";
    } else {
      echo "<div class='alert alert-warning'>No CRM data found for this request.</div>";
    }
  } else {
    echo "<div class='alert alert-danger'>Invalid request.</div>";
  }
} else {
  echo "<div class='alert alert-danger'>Invalid request.</div>";
}

function displayField($label, $value)
{
  return "
    <div class='col-md-6 mb-2'>
      <div class='d-flex justify-content-between border-bottom pb-1'>
        <span class='text-muted'>{$label}:</span>
        <strong class='text-dark text-right'>{$value}</strong>
      </div>
    </div>";
}
?>