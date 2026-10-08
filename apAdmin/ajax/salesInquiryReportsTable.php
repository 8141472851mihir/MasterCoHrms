<?php
include_once('../common/objectController.php');

$i = 1;
$data2 = [];

$q = $d->select(
  "feedback_master fm 
   LEFT JOIN bms_admin_master bam ON fm.forwarded_by = bam.admin_id",
  "fm.feedback_type = 3 AND fm.feedback_status AND fm.with_developer = 0",
  "ORDER BY fm.feedback_id DESC"
);

while ($data = mysqli_fetch_array($q)) {
  $a = [];
  $a['sr_no'] = $i++;
  $a['id'] = "FB_" . $data['feedback_id'] .
             ($data['read_by'] == 0 ? ' <span class="badge badge-warning">New</span>' : '');
  $a['name'] = $data['name'];
  $a['mobile'] = $data['country_code'] . ' ' . $data['mobile'];
  $a['company'] = $data['inquiry_company_name'];
  $a['employee'] = $data['no_of_employee'];
  $a['email'] = $data['email'];
  $a['type'] = $data['inquiry_type'] == 1 ? "Sales" : ($data['inquiry_type'] == 2 ? "App Support" : "Unknown");

  $statusMap = [
    0 => "Pending",
    1 => "In Progress",
    3 => "On Hold",
    4 => "Rejected",
    5 => "Closed by Developer",
    6 => "Rejected by Developer"
  ];
  $a['status'] = $statusMap[$data['feedback_status']] ?? "Unknown";

  $a['address'] = $data['feedback_msg'];
  $a['date'] = $data['feedback_date_time'];
  $a['type'] = $data['partner_id'] == 1 ? "USA" : "MyCo"; // This is the second 'type' in your table

  $data2[] = $a;
}

echo json_encode($data2);
?>