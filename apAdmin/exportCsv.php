<?php

include_once 'common/object.php';
session_start();
date_default_timezone_set('Asia/Kolkata');
$created_by=$admin_name;
$society_id=$society_id;
//Enter the headings of the excel columns
if(!isset($bms_admin_id))
{
  $_SESSION['msg1']= "Login First.";
  header("location:index.php?Login First");
}

extract(array_map("test_input" , $_GET));
$bill_master_id = (isset($bill_master_id) && $bill_master_id === 'all')
  ? 'all'
  : $d->sanitizeReportFilterIdAsInt($bill_master_id ?? 0);
$maintenance_id = (isset($maintenance_id) && $maintenance_id === 'all')
  ? 'all'
  : $d->sanitizeReportFilterIdAsInt($maintenance_id ?? 0);
$facility_id = (isset($facility_id) && (strtolower((string)$facility_id) === 'all'))
  ? 'All'
  : $d->sanitizeReportFilterIdAsInt($facility_id ?? 0);
$block_id = (isset($block_id) && (strtolower((string)$block_id) === 'all'))
  ? 'All'
  : $d->sanitizeReportFilterIdAsInt($block_id ?? 0);
$balancesheet_id = (isset($balancesheet_id) && (strtolower((string)$balancesheet_id) === 'all'))
  ? 'All'
  : $d->sanitizeReportFilterIdAsInt($balancesheet_id ?? 0);
$emp_id = (isset($emp_id) && (strtolower((string)$emp_id) === 'all'))
  ? 'All'
  : $d->sanitizeReportFilterIdAsInt($emp_id ?? 0);
$from = $d->sanitizeReportFilterDate(isset($from) ? $from : ($_GET['from'] ?? ''), date('Y-m-d'));
$toDate = $d->sanitizeReportFilterDate(isset($toDate) ? $toDate : ($_GET['toDate'] ?? ''), date('Y-m-d'));
if ($report=="Bill") {

$contents="No,Bill Name,Block Unit,User,Amount,No of Unit,Unit Price,Status,Bill Paid Date,Generated Date\n";
 $qb=$d->select("bill_master","bill_master_id='$bill_master_id' ");
 $billData=mysqli_fetch_array($qb);
$bill_name= $billData['bill_name'];
$auto_created_date= $billData['auto_created_date'];
switch ($_GET['type']) {
  case '100':
  if ($bill_master_id=='all') {
     $q=$d->select("receive_bill_master,bill_master,unit_master,block_master,users_master","users_master.unit_id=receive_bill_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_bill_master.unit_id AND receive_bill_master.bill_master_id=bill_master.bill_master_id AND receive_bill_master.society_id='$society_id'  AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
    } else {
       $q=$d->select("receive_bill_master,bill_master,unit_master,block_master,users_master","users_master.unit_id=receive_bill_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_bill_master.unit_id AND receive_bill_master.bill_master_id=bill_master.bill_master_id AND receive_bill_master.society_id='$society_id' AND receive_bill_master.bill_master_id='$bill_master_id' AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
    }
    break;
  case '3':
   if ($bill_master_id=='all') {
     $q=$d->select("receive_bill_master,bill_master,unit_master,block_master,users_master","users_master.unit_id=receive_bill_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_bill_master.unit_id AND receive_bill_master.bill_master_id=bill_master.bill_master_id AND receive_bill_master.society_id='$society_id'  AND receive_bill_master.receive_bill_status=3 AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
    } else {
      $q=$d->select("receive_bill_master,bill_master,unit_master,block_master,users_master","users_master.unit_id=receive_bill_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_bill_master.unit_id AND receive_bill_master.bill_master_id=bill_master.bill_master_id AND receive_bill_master.society_id='$society_id' AND receive_bill_master.bill_master_id='$bill_master_id' AND receive_bill_master.receive_bill_status=3 AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
    }
    break;
  case '2':
   if ($bill_master_id=='all') {
     $q=$d->select("receive_bill_master,bill_master,unit_master,block_master,users_master","users_master.unit_id=receive_bill_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_bill_master.unit_id AND receive_bill_master.bill_master_id=bill_master.bill_master_id AND receive_bill_master.society_id='$society_id' AND receive_bill_master.receive_bill_status=2 AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
    } else {
      $q=$d->select("receive_bill_master,bill_master,unit_master,block_master,users_master","users_master.unit_id=receive_bill_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_bill_master.unit_id AND receive_bill_master.bill_master_id=bill_master.bill_master_id AND receive_bill_master.society_id='$society_id' AND receive_bill_master.bill_master_id='$bill_master_id' AND receive_bill_master.receive_bill_status=2 AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
    }
  break;
  case '0':
   if ($bill_master_id=='all') {
       $q=$d->select("receive_bill_master,bill_master,unit_master,block_master,users_master","users_master.unit_id=receive_bill_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_bill_master.unit_id AND receive_bill_master.bill_master_id=bill_master.bill_master_id AND receive_bill_master.society_id='$society_id'  AND receive_bill_master.receive_bill_status=0 AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
      } else {
        $q=$d->select("receive_bill_master,bill_master,unit_master,block_master,users_master","users_master.unit_id=receive_bill_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_bill_master.unit_id AND receive_bill_master.bill_master_id=bill_master.bill_master_id AND receive_bill_master.society_id='$society_id' AND receive_bill_master.bill_master_id='$bill_master_id' AND receive_bill_master.receive_bill_status=0 AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
      }
  break;
  default:
   if ($bill_master_id=='all') {
     $q=$d->select("receive_bill_master,bill_master,unit_master,block_master,users_master","users_master.unit_id=receive_bill_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_bill_master.unit_id AND receive_bill_master.bill_master_id=bill_master.bill_master_id AND receive_bill_master.society_id='$society_id' AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
  } else {
      $q=$d->select("receive_bill_master,bill_master,unit_master,block_master,users_master","users_master.unit_id=receive_bill_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_bill_master.unit_id AND receive_bill_master.bill_master_id=bill_master.bill_master_id AND receive_bill_master.society_id='$society_id' AND receive_bill_master.bill_master_id='$bill_master_id' AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
  }
    break;
}

$i=1;
//While loop to fetch the records
while($row = mysqli_fetch_array($q))
{
    if ($row['receive_bill_status']==1) {
        $status= "Bill Genereted";
       } elseif ($row['receive_bill_status']==2) {
        $status = "Unpaid";
       } elseif ($row['receive_bill_status']==3) {
        $status = "Paid";
       } elseif ($row['receive_bill_status']==0) {
        $status = "Bill Not Generate";
       }
$contents.=$i++.",";
$contents.=$row['bill_name']."-".$row['auto_created_date'].",";
$contents.=$row['block_name']."-".$row['unit_name'].",";
$contents.=$row['user_full_name'].",";
$contents.=$row['bill_amount'].",";
$contents.=$row['no_of_unit'].",";
$contents.=$row['unit_price'].",";
$contents.=$status.",";
$contents.=$row['bill_payment_date'].",";
$contents.=$row['bill_genrate_date']."\n";
}

$contents = strip_tags($contents); 

header("Content-Disposition: attachment; filename=Bill_Report_".date('Y-m-d-h-i').".csv");
print $contents;

}

// Bill Report
if ($report=="Maintenance") {

$contents="No,Maintenance Name,Block Unit,User,Amount,Status,Maintenance Paid Date,Generated Date\n";
$qb=$d->select("maintenance_master","maintenance_id='$maintenance_id' ");
$maintenanceData=mysqli_fetch_array($qb);
$maintenance_name= $maintenanceData['maintenance_name'];
$auto_created_date= $maintenanceData['created_date'];

switch ($_GET['type']) {
  case '100':
   if ($maintenance_id=='all') {
      $q=$d->select("receive_maintenance_master,maintenance_master,unit_master,block_master,users_master","users_master.unit_id=receive_maintenance_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_maintenance_master.unit_id AND receive_maintenance_master.maintenance_id =maintenance_master.maintenance_id  AND receive_maintenance_master.society_id='$society_id' AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
   } else {
     $q=$d->select("receive_maintenance_master,maintenance_master,unit_master,block_master,users_master","users_master.unit_id=receive_maintenance_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_maintenance_master.unit_id AND receive_maintenance_master.maintenance_id =maintenance_master.maintenance_id  AND receive_maintenance_master.society_id='$society_id' AND receive_maintenance_master.maintenance_id ='$maintenance_id' AND users_master.member_status=0 ","ORDER BY unit_master.unit_id ASC");
   }
    break;
  case '0':
  if ($maintenance_id=='all') {
    $q=$d->select("receive_maintenance_master,maintenance_master,unit_master,block_master,users_master","users_master.unit_id=receive_maintenance_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_maintenance_master.unit_id AND receive_maintenance_master.maintenance_id =maintenance_master.maintenance_id  AND receive_maintenance_master.society_id='$society_id'  AND receive_maintenance_master.receive_maintenance_status=0 AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
  } else {
     $q=$d->select("receive_maintenance_master,maintenance_master,unit_master,block_master,users_master","users_master.unit_id=receive_maintenance_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_maintenance_master.unit_id AND receive_maintenance_master.maintenance_id =maintenance_master.maintenance_id  AND receive_maintenance_master.society_id='$society_id' AND receive_maintenance_master.maintenance_id ='$maintenance_id' AND receive_maintenance_master.receive_maintenance_status=0 AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
  }
    break;
  case '1':
  if ($maintenance_id=='all') {
     $q=$d->select("receive_maintenance_master,maintenance_master,unit_master,block_master,users_master","users_master.unit_id=receive_maintenance_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_maintenance_master.unit_id AND receive_maintenance_master.maintenance_id =maintenance_master.maintenance_id  AND receive_maintenance_master.society_id='$society_id'  AND receive_maintenance_master.receive_maintenance_status=1 AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
    } else {
      $q=$d->select("receive_maintenance_master,maintenance_master,unit_master,block_master,users_master","users_master.unit_id=receive_maintenance_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_maintenance_master.unit_id AND receive_maintenance_master.maintenance_id =maintenance_master.maintenance_id  AND receive_maintenance_master.society_id='$society_id' AND receive_maintenance_master.maintenance_id ='$maintenance_id' AND receive_maintenance_master.receive_maintenance_status=1 AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
    }
  break;
  default:
  if ($maintenance_id=='all') {
     $q=$d->select("receive_maintenance_master,maintenance_master,unit_master,block_master,users_master","users_master.unit_id=receive_maintenance_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_maintenance_master.unit_id AND receive_maintenance_master.maintenance_id =maintenance_master.maintenance_id  AND receive_maintenance_master.society_id='$society_id' AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
    } else {
      $q=$d->select("receive_maintenance_master,maintenance_master,unit_master,block_master,users_master","users_master.unit_id=receive_maintenance_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=receive_maintenance_master.unit_id AND receive_maintenance_master.maintenance_id =maintenance_master.maintenance_id  AND receive_maintenance_master.society_id='$society_id' AND receive_maintenance_master.maintenance_id ='$maintenance_id' AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
    }
    break;
}

$i=1;
//While loop to fetch the records
while($row = mysqli_fetch_array($q))
{
    if ($row['receive_maintenance_status']==1) {
        $status= "Paid";
       } else if ($row['receive_maintenance_status']==0) {
        $status = "Unpaid";
       } 
$contents.=$i++.",";
$contents.=$row['maintenance_name']."-".$row['auto_created_date'].",";
$contents.=$row['block_name']."-".$row['unit_name'].",";
$contents.=$row['user_full_name'].",";
$contents.=$row['maintence_amount'].",";
$contents.=$status.",";
$contents.=$row['receive_maintenance_date'].",";
$contents.=$row['auto_created_date']."\n";
}

$contents = strip_tags($contents); 

header("Content-Disposition: attachment; filename=Maintenance_Report_".date('Y-m-d-h-i').".csv");
print $contents;

}


// facility Report
if ($report=="Facility") {

$contents="No,Facility,Block Unit,User,Amount,Status,Booking Date,Payment Date\n";
$qb=$d->select("facilities_master","facility_id='$facility_id' ");
$facilityData=mysqli_fetch_array($qb);
$facility_name= $facilityData['facility_name'];

if ($facility_id=="All") {
     $q=$d->select("facilitybooking_master,facilities_master,unit_master,block_master,users_master","users_master.unit_id=facilitybooking_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=facilitybooking_master.unit_id AND facilitybooking_master.facility_id =facilities_master.facility_id  AND facilitybooking_master.society_id='$society_id' AND facilitybooking_master.book_status=1 AND facilitybooking_master.booked_date BETWEEN '$from' AND '$toDate' AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
  } else {
     $q=$d->select("facilitybooking_master,facilities_master,unit_master,block_master,users_master","users_master.unit_id=facilitybooking_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=facilitybooking_master.unit_id AND facilitybooking_master.facility_id =facilities_master.facility_id  AND facilitybooking_master.society_id='$society_id' AND facilitybooking_master.facility_id ='$facility_id' AND facilitybooking_master.book_status=1 AND facilitybooking_master.booked_date BETWEEN '$from' AND '$toDate' AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");
}


$i=1;
//While loop to fetch the records
while($row = mysqli_fetch_array($q))
{
    if ($row['payment_status']==0) {
        $status= "Paid";
       } else if ($row['payment_status']==1) {
        $status = "Unpaid";
       } 
$contents.=$i++.",";
$contents.=$facility_name.",";
$contents.=$row['block_name']."-".$row['unit_name'].",";
$contents.=$row['user_full_name'].",";
$contents.=$row['receive_amount'].",";
$contents.=$status.",";
$contents.=$row['booked_date'].",";
$contents.=$row['payment_received_date']."\n";
}

$contents = strip_tags($contents); 

header("Content-Disposition: attachment; filename=Facility_Report_".date('Y-m-d-h-i').".csv");
print $contents;

}



// Visitor Report
if ($report=="Visitor") {

$contents="No,Visitor,Mobile,Block Unit,User,In Time,Out Time,Status\n";

 if ($block_id=="All") {
           $q=$d->select("visitors_master,unit_master,block_master,users_master","users_master.unit_id=visitors_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=visitors_master.unit_id AND visitors_master.society_id='$society_id' AND users_master.user_id=visitors_master.user_id AND visitors_master.visit_date BETWEEN '$from' AND '$toDate'");
        } else {
           $q=$d->select("visitors_master,unit_master,block_master,users_master","users_master.unit_id=visitors_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.unit_id=visitors_master.unit_id AND visitors_master.society_id='$society_id' AND visitors_master.block_id ='$block_id' AND users_master.user_id=visitors_master.user_id AND visitors_master.visit_date BETWEEN '$from' AND '$toDate'");
      }


$i=1;
//While loop to fetch the records
while($row = mysqli_fetch_array($q))
{
    if ($row['visitor_status']==0) {
      $status= "Pending";
     } elseif ($row['visitor_status']==1) {
      $status= "Approved ";
     } elseif ($row['visitor_status']==2) {
      $status= "Entered ";
     } elseif ($row['visitor_status']==3) {
      $status= "Exit ";
     } elseif ($row['visitor_status']==4) {
      $status= "Rejected ";
     } elseif ($row['visitor_status']==6) {
      $status= "Hold";
     } 
$contents.=$i++.",";
$contents.=$row['visitor_name'].",";
$contents.=$row['visitor_mobile'].",";
$contents.=$row['user_full_name'].",";
$contents.=$row['visit_date']."-".$row['visit_time'].",";
$contents.=$row['exit_date']."-".$row['exit_time'].",";
$contents.=$row['block_name']."-".$row['unit_name'].",";
$contents.=$status."\n";
}

$contents = strip_tags($contents); 

header("Content-Disposition: attachment; filename=Visitor_Report_".date('Y-m-d-h-i').".csv");
print $contents;

}

//IS_1218
// Visitor Report
if ($report=="CommonVisitor") {

$contents="No,Visitor,Mobile,In Time,Out Time,Status\n";

  
           

         $q=$d->select("visitors_master"," visitors_master.visitor_type=5 and  visitors_master.society_id='$society_id' AND visitors_master.visit_date BETWEEN '$from' AND '$toDate'");


$i=1;
//While loop to fetch the records
while($row = mysqli_fetch_array($q))
{
    if ($row['visitor_status']==0) {
      $status= "Pending";
     } elseif ($row['visitor_status']==1) {
      $status= "Approved ";
     } elseif ($row['visitor_status']==2) {
      $status= "Entered ";
     } elseif ($row['visitor_status']==3) {
      $status= "Exit ";
     } elseif ($row['visitor_status']==4) {
      $status= "Rejected ";
     } elseif ($row['visitor_status']==6) {
      $status= "Hold";
     } 

$contents.=$i++.",";
$contents.=$row['visitor_name'].",";
$contents.=$row['visitor_mobile'].",";
$contents.= date ("d-m-Y h:i A", strtotime($row['visit_date'].' '.$row['visit_time'])).",";
$contents.=date ("d-m-Y h:i A", strtotime($row['exit_date'].' '.$row['exit_time'])).","; 
$contents.=$status."\n";
}

$contents = strip_tags($contents); 

header("Content-Disposition: attachment; filename=Common_Visitor_Report_".date('Y-m-d-h-i').".csv");
print $contents;

}
//IS_1218

// Employee Report
if ($report=="EmployeeAtt") {

$contents="No,Employee,Type,Mobile,In Time,Out Time,Working, Status\n";

if ($emp_id=="All") {
     $q=$d->select("employee_master,staff_visit_master","employee_master.emp_id =staff_visit_master.emp_id  AND employee_master.society_id='$society_id' AND staff_visit_master.filter_data BETWEEN '$from' AND '$toDate'");
  } else {

     $q=$d->select("employee_master,staff_visit_master","employee_master.emp_id =staff_visit_master.emp_id  AND employee_master.society_id='$society_id' AND staff_visit_master.filter_data BETWEEN '$from' AND '$toDate' AND staff_visit_master.emp_id='$emp_id'");
}


$i=1;
//While loop to fetch the records
$empRows = [];
$empTypeIds = [];
while($row = mysqli_fetch_array($q))
{
  $empRows[] = $row;
  $empTypeIds[] = (int)$row['emp_type_id'];
}

$empTypeById = [];
if (!empty($empTypeIds)) {
  $empTypeIdsIn = implode(',', array_map('intval', array_unique($empTypeIds)));
  $EW = $d->select("emp_type_master", "emp_type_id IN ($empTypeIdsIn)");
  while ($empTypeData = mysqli_fetch_array($EW)) {
    $empTypeById[(int)$empTypeData['emp_type_id']] = $empTypeData;
  }
}

foreach ($empRows as $row)
{

    $empTypeData = $empTypeById[(int)$row['emp_type_id']] ?? [];

    if ($row['visit_status']==1) {
      $status= "Exit";
     } elseif ($row['visit_status']==0) {
      $status= "In ";
     } 
     $intTime=$row['visit_entry_date_time'];
      $outTime=$row['visit_exit_date_time'];
      $date_a = new DateTime($intTime);
      $date_b = new DateTime($outTime);
      $interval = date_diff($date_a,$date_b);
      $wHours= $interval->format('%h:%i:%s');

$contents.=$i++.",";
$contents.=$row['emp_name'].",";
$contents.=($empTypeData['emp_type_name'] ?? '').",";
$contents.=$row['emp_mobile'].",";
$contents.=$row['visit_entry_date_time'].",";
$contents.=$row['visit_exit_date_time'].",";
$contents.=$wHours.",";
$contents.=$status."\n";
}

$contents = strip_tags($contents); 

header("Content-Disposition: attachment; filename=Employee_Report_".date('Y-m-d-h-i').".csv");
print $contents;

}





// Employee Report
if ($report=="EmployeeAll") {

$contents="No,Employee Name,Mobile,Email,Joining Date,Salary,Type, Emp For\n";

 if ($_GET['emp_type']=="All") {
    $q=$d->select("employee_master","society_id='$society_id'");
  } elseif ($_GET['emp_type']==0) {
    $q=$d->select("employee_master","society_id='$society_id' AND emp_type=0");
  } elseif ($_GET['emp_type']==1) {
   $q=$d->select("employee_master","society_id='$society_id' AND emp_type=1");
  } else {
    $q=$d->select("employee_master","society_id='$society_id'");
  }


$i=1;
//While loop to fetch the records
$empRows = [];
$empTypeIds = [];
while($row = mysqli_fetch_array($q))
{
  $empRows[] = $row;
  $empTypeIds[] = (int)$row['emp_type_id'];
}

$empTypeById = [];
if (!empty($empTypeIds)) {
  $empTypeIdsIn = implode(',', array_map('intval', array_unique($empTypeIds)));
  $EW = $d->select("emp_type_master", "emp_type_id IN ($empTypeIdsIn)");
  while ($empTypeData = mysqli_fetch_array($EW)) {
    $empTypeById[(int)$empTypeData['emp_type_id']] = $empTypeData;
  }
}

foreach ($empRows as $row)
{

    $empTypeData = $empTypeById[(int)$row['emp_type_id']] ?? [];

    if ($row['emp_type']==0) {
      $status= "Soiciety";
     } elseif ($row['emp_type']==1) {
      $status= "Users";
     } 

    if (!empty($empTypeData['emp_type_name'])) {
       $emp_type_name=$empTypeData['emp_type_name'];
    } else {
       $emp_type_name="Gatekeeper";
    } 
    
$contents.=$i++.",";
$contents.=$row['emp_name'].",";
$contents.=$row['emp_mobile'].",";
$contents.=$row['emp_email'].",";
$contents.=$row['emp_date_of_joing'].",";
$contents.=$row['emp_sallary'].",";
$contents.=$emp_type_name.",";
$contents.=$status."\n";
}

$contents = strip_tags($contents); 

header("Content-Disposition: attachment; filename=EmployeesAll_Report_".date('Y-m-d-h-i').".csv");
print $contents;

}




// Expencse Report
if ($report=="Expense") {

$contents="No,Expense,Date,Amount,Balancesheet\n";

 if ($balancesheet_id=="All") {
     $q=$d->select("balancesheet_master,expenses_balance_sheet_master","balancesheet_master.balancesheet_id =expenses_balance_sheet_master.balancesheet_id  AND expenses_balance_sheet_master.society_id='$society_id' AND  expenses_balance_sheet_master.expenses_add_date BETWEEN '$from' AND '$toDate' AND expenses_balance_sheet_master.expenses_amount!=0");
  } else {
     $q=$d->select("balancesheet_master,expenses_balance_sheet_master","balancesheet_master.balancesheet_id =expenses_balance_sheet_master.balancesheet_id  AND balancesheet_master.society_id='$society_id' AND balancesheet_master.balancesheet_id ='$balancesheet_id' AND  expenses_balance_sheet_master.expenses_add_date BETWEEN '$from' AND '$toDate' AND expenses_balance_sheet_master.expenses_amount!=0");
}


$i=1;
//While loop to fetch the records
while($row = mysqli_fetch_array($q))
{


  $contents.=$i++.",";
  $contents.=$row['expenses_title'].",";
  $contents.=$row['expenses_add_date'].",";
  $contents.=$row['expenses_amount'].",";
  $contents.=$row['balancesheet_name']."\n";
}

$contents = strip_tags($contents); 

header("Content-Disposition: attachment; filename=Expenses_Report_".date('Y-m-d-h-i').".csv");
print $contents;

}


// Users Report
if ($report=="users") {

$contents="No,Block,Type,Name,Mobile,Email,Permanet Address,Device\n";

// /IS_1220
 $unit_master_qry=$d->select("unit_master","");
              $unit_master_array = array();
              while ($data_unit=mysqli_fetch_array($unit_master_qry)) {
                $unit_master_array[$data_unit['unit_id']] =$data_unit;
              }

               $block_master_qry=$d->select("block_master","");
              $block_master_array = array();
              while ($data_block=mysqli_fetch_array($block_master_qry)) {
                $block_master_array[$data_block['block_id']] =$data_block;
              }

               $floors_master_qry=$d->select("floors_master","");
              $floors_master_array = array();
              while ($data_floor=mysqli_fetch_array($floors_master_qry)) {
                $floors_master_array[$data_floor['floor_id']] =$data_floor;
              }


//$q=$d->select("unit_master,block_master,users_master,floors_master","block_master.block_id=floors_master.block_id AND users_master.floor_id=floors_master.floor_id AND users_master.unit_id=unit_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.society_id='$society_id'  AND unit_master.unit_status!=4","ORDER BY unit_master.unit_id ASC");
$q=$d->select("users_master","society_id='$society_id'   ","ORDER BY user_id ASC");
//IS_1220

$i=1;
//While loop to fetch the records
while($row = mysqli_fetch_array($q))
{

//IS_1220
                   $unit_master_detail =  $unit_master_array[$row['unit_id']];
                   $block_master_detail =  $block_master_array[$row['block_id']];
                    $floors_master_detail =  $floors_master_array[$row['floor_id']];

                     if($row['user_type']=="0" && $row['member_status']=="0" ){
                            $status= "Owner";
                        } else  if($row['user_type']=="0" && $row['member_status']=="1" ){
                            $status= "Owner Family";
                        } else  if($row['user_type']=="1" && $row['member_status']=="0" && $unit_master_detail['unit_status']==3   ){
                            $status= "Tenant";
                        } else  if($row['user_type']=="0" && $row['member_status']=="0" && $unit_master_detail['unit_status']==2   ){
                            $status= 'Owner-Defaulter';
                        } else  if($row['user_type']=="1" && $row['member_status']=="0" && $unit_master_detail['unit_status']==2   ){
                            $status= 'Tenant-Defaulter';
                        } else {
                          $status= "Owner";
                        } 
//IS_1220

   /* if ($row['unit_status']==1) {
      $status=  "Owner";
     } elseif ($row['unit_status']==3) {
      $status=  "Tenant";
     } elseif ($row['unit_status']==2) {
       if ($row['user_type']==0) {
         $status=  'Owner-Defaulter';
       } else {
        $status=  "Tenant-Defaulter";
       }
     } */

    if ($row['member_status']==1) {
        $rName= '-'.$row['member_relation_name'];
     } else {
      $rName= "";
     }

  $contents.=$i++.",";
  $contents.=$block_master_detail['block_name']/*."-".$floors_master_detail['floor_name']*/."-".$unit_master_detail['unit_name'].",";
  $contents.=$status.$rName.",";
  $contents.=$row['user_full_name'].",";
  $contents.=$row['user_mobile'].",";
  $contents.=$row['user_email'].",";
  $contents.=$row['last_address'].",";
  $contents.=$row['device']."\n";
}

$contents = strip_tags($contents); 

header("Content-Disposition: attachment; filename=Users_Report_".date('Y-m-d-h-i').".csv");
print $contents;

}


// Events Report
if ($report=="Events") {

$contents="No,Event,Description,Event MOM,Start Date,End Date,Type,Received Amount,Going Persons\n";
$date=date_create($from);
$dateTo=date_create($toDate);
$nFrom= date_format($date,"Y-m-d 00:00:01");
$nTo= date_format($dateTo,"Y-m-d 23:59:59");
       $q=$d->select("event_master","society_id='$society_id' AND event_start_date BETWEEN '$nFrom' AND '$nTo'");


$i=1;
//While loop to fetch the records
while($row = mysqli_fetch_array($q))
{

   if($row['event_type']=="0"){
    $eventType="Free";
   } else {
    $eventType="Paid";
   }
  $count5=$d->sum_data("going_person","event_attend_list","event_id='$row[event_id]'");
    while($data=mysqli_fetch_array($count5))
   {
        $sum1=$data['SUM(going_person)'];
    }
    $count6=$d->sum_data("going_child","event_attend_list","event_id='$row[event_id]'");
    while($data=mysqli_fetch_array($count6))
   {
        $sum2=$data['SUM(going_child)'];
    }

     $count6=$d->sum_data("recived_amount","event_attend_list","event_id='$row[event_id]'");
      while($row11=mysqli_fetch_array($count6))
     {
       echo   $sumTotal=$row11['SUM(recived_amount)'];
             
      }
                            
  $contents.=$i++.",";
  $contents.=$row['event_title'].",";
  $contents.=$row['event_description'].",";
  $contents.=$row['eventMom'].",";
  $contents.=$row['event_start_date'].",";
  $contents.=$row['event_end_date'].",";
  $contents.=$eventType.",";
  $contents.=$sumTotal.",";
  $contents.=$sum1+$sum2."\n";
}

$contents = strip_tags($contents); 

header("Content-Disposition: attachment; filename=Events_Report_".date('Y-m-d-h-i').".csv");
print $contents;

}



// Users Report
if ($report=="usersPayment") {

$contents="No,Block,Type,Name,Mobile,Unpaid Maintenance,Paid Maintenance,Unpaid Bill,Paid Bill\n";

$q=$d->select("unit_master,block_master,users_master,floors_master","block_master.block_id=floors_master.block_id AND users_master.floor_id=floors_master.floor_id AND users_master.unit_id=unit_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.society_id='$society_id' AND unit_master.unit_status!=0 AND unit_master.unit_status!=4 AND users_master.member_status=0 AND users_master.member_status=0","ORDER BY unit_master.unit_id ASC");


$i=1;
//While loop to fetch the records
while($row = mysqli_fetch_array($q))
{
  $unit_id=$row['unit_id'];

   $count5=$d->sum_data("maintence_amount","receive_maintenance_master","received_amount='0' AND unit_id='$unit_id' AND receive_maintenance_status=0");
      while($row222=mysqli_fetch_array($count5))
     {
          $asif=$row222['SUM(maintence_amount)'];
        $totalUnpaidMan=number_format($asif,2,'.','');
             
      }

     $count6=$d->sum_data("received_amount","receive_maintenance_master","received_amount!='0' AND unit_id='$unit_id' AND receive_maintenance_status=1");
      while($row1=mysqli_fetch_array($count6))
     {
          $asif=$row1['SUM(received_amount)'];
        $totalMainPaid=number_format($asif,2,'.','');
             
      }

      $count7=$d->sum_data("bill_amount","receive_bill_master","received_amount='0' AND unit_id='$unit_id' AND receive_bill_status=2");
      while($row3=mysqli_fetch_array($count7))
     {
          $asif11=$row3['SUM(bill_amount)'];
     $UnpadiBill=   number_format($asif11,2,'.','');
             
      }

      $count8=$d->sum_data("received_amount","receive_bill_master","received_amount!='0' AND unit_id='$unit_id' AND receive_bill_status=3");
      while($row4=mysqli_fetch_array($count8))
     {
          $asif44=$row4['SUM(received_amount)'];
      $paidBill=  number_format($asif44,2,'.','');
             
      }

   if ($row['unit_status']==1) {
      $status=  "Owner";
     } elseif ($row['unit_status']==3) {
      $status=  "Tenant";
     } elseif ($row['unit_status']==2) {
       if ($row['user_type']==0) {
         $status=  'Owner-Defaulter';
       } else {
        $status=  "Tenant-Defaulter";
       }
     }
  $contents.=$i++.",";
  $contents.=$row['block_name']."-".$row['floor_name']."-".$row['unit_name'].",";
  $contents.=$status.",";
  $contents.=$row['user_full_name'].",";
  $contents.=$row['user_mobile'].",";
  $contents.=$totalUnpaidMan.",";
  $contents.=$totalMainPaid.",";
  $contents.=$UnpadiBill.",";
  $contents.=$paidBill."\n";
}

$contents = strip_tags($contents); 

header("Content-Disposition: attachment; filename=Users_PaymentReport_".date('Y-m-d-h-i').".csv");
print $contents;

}


// Users Report
if ($report=="usersPaymentSingle") {

$contents="No,Block,User Type,Name,Mobile,Type,Name,Paid,Unpaid,Total Amount,Status,Month\n";

$q=$d->select("unit_master,block_master,users_master,floors_master,maintenance_master,receive_maintenance_master","block_master.block_id=floors_master.block_id AND users_master.floor_id=floors_master.floor_id AND users_master.unit_id=unit_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.society_id='$society_id' AND unit_master.unit_status!=0 AND unit_master.unit_status!=4 AND receive_maintenance_master.maintenance_id=maintenance_master.maintenance_id AND receive_maintenance_master.unit_id='$unit_id' AND receive_maintenance_master.unit_id=users_master.unit_id AND users_master.member_status=0","");
$i=1;
$totalMain = mysqli_num_rows($q);
while($row = mysqli_fetch_array($q))
{
  $unit_id=$row['unit_id'];
   if ($row['unit_status']==1) {
      $status=  "Owner";
     } elseif ($row['unit_status']==3) {
      $status=  "Tenant";
     } elseif ($row['unit_status']==2) {
       if ($row['user_type']==0) {
         $status=  'Owner-Defaulter';
       } else {
        $status=  "Tenant-Defaulter";
       }
     }

    if ($row['received_amount']==0) {
      $paymentStatus="Unpaid";
      $unPaid=$row['maintence_amount'];
    } else {
      $paymentStatus="Paid";
      $unPaid=0;
    }
  $contents.=$i++.",";
  $contents.=$row['block_name']."-".$row['floor_name']."-".$row['unit_name'].",";
  $contents.=$status.",";
  $contents.=$row['user_full_name'].",";
  $contents.=$row['user_mobile'].",";
  $contents.="Maintenance,";
  $contents.=$row['maintenance_name'].",";
  $contents.=$row['received_amount'].",";
  $contents.=$unPaid.",";
  $contents.=$row['maintence_amount'].",";
  $contents.=$paymentStatus.",";
  $contents.=$row['auto_created_date']."\n";
}

$q1=$d->select("unit_master,block_master,users_master,floors_master,bill_master,receive_bill_master","block_master.block_id=floors_master.block_id AND users_master.floor_id=floors_master.floor_id AND users_master.unit_id=unit_master.unit_id AND block_master.block_id=unit_master.block_id AND unit_master.society_id='$society_id' AND unit_master.unit_status!=0 AND unit_master.unit_status!=4 AND receive_bill_master.bill_master_id=bill_master.bill_master_id AND receive_bill_master.unit_id='$unit_id' AND receive_bill_master.unit_id=users_master.unit_id AND receive_bill_master.bill_amount!=0","");
$i=$totalMain+1;
while($row = mysqli_fetch_array($q1))
{
  $unit_id=$row['unit_id'];
    if ($row['unit_status']==1) {
      $status=  "Owner";
     } elseif ($row['unit_status']==3) {
      $status=  "Tenant";
     } elseif ($row['unit_status']==2) {
       if ($row['user_type']==0) {
         $status=  'Owner-Defaulter';
       } else {
        $status=  "Tenant-Defaulter";
       }
     }

    if ($row['received_amount']==0) {
      $paymentStatus="Unpaid";
      $unPaid=$row['bill_amount'];
    } else {
      $paymentStatus="Paid";
      $unPaid=0;
    }
  $contents.=$i++.",";
  $contents.=$row['block_name']."-".$row['floor_name']."-".$row['unit_name'].",";
  $contents.=$status.",";
  $contents.=$row['user_full_name'].",";
  $contents.=$row['user_mobile'].",";
  $contents.="Bill,";
  $contents.=$row['bill_name'].",";
  $contents.=$row['received_amount'].",";
  $contents.=$unPaid.",";
  $contents.=$row['bill_amount'].",";
  $contents.=$paymentStatus.",";
  $contents.=$row['auto_created_date']."\n";
}

$contents = strip_tags($contents); 

header("Content-Disposition: attachment; filename=Users_PaymentReport_Single".date('Y-m-d-h-i').".csv");
print $contents;

}

?>