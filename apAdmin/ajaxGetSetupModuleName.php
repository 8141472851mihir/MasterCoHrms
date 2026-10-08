<?php

include_once 'common/object.php';
error_reporting(0);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$base_url = $m->base_url();
extract(array_map("test_input", $_POST));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($getSetupModule) && $getSetupModule == 'getSetupModule' && isset($society_id)) {
        
        $q = $d->selectRow(
            "training_module_master.training_module_name, 
            module_training_status_master.module_id, 
            module_training_status_master.training_status, 
            module_training_status_master.data_receive_status, 
            module_training_status_master.onboarding_status,
            module_training_status_master.training_date, 
            training_module_priority_master.priority_name, 
            module_training_status_master.data_receive_date, 
            module_training_status_master.onboarding_date,bms1.admin_name AS data_receive_change_by_name,bms2.admin_name AS onboarding_change_by_name",
            "training_module_master 
            LEFT JOIN module_training_status_master 
            ON training_module_master.training_module_id = module_training_status_master.module_id 
            AND module_training_status_master.company_id = '$society_id'
            LEFT JOIN training_module_priority_master 
            ON training_module_master.module_priority = training_module_priority_master.priority_id 
            LEFT JOIN bms_admin_master as bms1 on bms1.admin_id= module_training_status_master.data_receive_change_by
            LEFT JOIN bms_admin_master as bms2 on bms2.admin_id= module_training_status_master.onboarding_change_by", 
            "training_module_master.training_module_status='0' AND training_module_master.module_type='0'"
        );
        
        
        if (mysqli_num_rows($q) > 0) {
            echo '<table>';
            echo '<thead>';
            echo '<tr><th>Module Name</th>';
            echo '<th>Data Receive Status</th>
            <th>Data Receive Date</th>
            <th>Data Receive By</th>
            <th>Data Upload Status</th>
            <th>Upload Date</th>
            <th>Data Upload By</th></tr>';
            echo '</thead>';
            echo '<tbody>';
            
            while ($row = mysqli_fetch_array($q)) {
                // Handle Training Status
                switch ($row['training_status']) {
                    case 1:
                        $status = 'Yes';
                        $color = 'green'; 
                        break;
                    case 0:
                        $status = 'No';
                        $color = 'red'; 
                        break;
                    case 2:
                        $status = 'Not Applicable';
                        $color = 'blue'; 
                        break;
                    default:
                        $status = 'No'; 
                        $color = 'gray'; 
                }
        
                // Handle Data Receive Status
                switch ($row['data_receive_status']) {
                    case 1:
                        $data_status = 'Received';
                        $data_color = 'green';
                        break;
                    case 0:
                        $data_status = 'Not Received';
                        $data_color = 'red';
                        break;
                        case 2:
                            $status = 'Not Applicable';
                            $color = 'blue'; 
                            break;
                    default:
                        $data_status = 'Not Received';
                        $data_color = 'gray';
                }
        
                // Handle Onboarding Status
                switch ($row['onboarding_status']) {
                    case 1:
                        $onboarding_status = 'Completed';
                        $onboarding_color = 'green';
                        break;
                    case 0:
                        $onboarding_status = 'In Progress';
                        $onboarding_color = 'red';
                        break;
                        case 2:
                            $status = 'Not Applicable';
                            $color = 'blue'; 
                            break;
                    default:
                        $onboarding_status = 'Pending';
                        $onboarding_color = 'gray';
                }
        
                $training_date = ($row['training_date'] != '0000-00-00 00:00:00' && !empty($row['training_date'])) ? date("d F Y D, H:i A", strtotime($row['training_date'])) : '';
                $data_receive_date = '';
                if (isset($row['data_receive_date']) && $row['data_receive_date'] != '0000-00-00 00:00:00' && $row['data_receive_date'] != '' && !empty($row['data_receive_date'])) {
                    $data_receive_date = date("d F Y D, H:i A", strtotime($row['data_receive_date']));
                }
                $onboarding_date = '';
                if (isset($row['onboarding_date']) && $row['onboarding_date'] != '0000-00-00 00:00:00' && $row['onboarding_date'] != '' && !empty($row['onboarding_date'])) {
                    $onboarding_date = date("d F Y D, H:i A", strtotime($row['onboarding_date']));
                }
        
                echo '<tr>';
                echo '<td style="color: black;">' . $row['training_module_name'] . " (".$row['priority_name'].') </td>';
                // echo '<td style="color: ' . $color . ';">' . $status . '</td>';
                // echo '<td>' . $training_date . '</td>';
                echo '<td style="color: ' . $data_color . ';">' . $data_status . '</td>';
                echo '<td>' . $data_receive_date . '</td>';
                echo '<td>' . $row['data_receive_change_by_name'] . '</td>';
                echo '<td style="color: ' . $onboarding_color . ';">' . $onboarding_status . '</td>';
                echo '<td>' . $onboarding_date . '</td>';
                echo '<td>' . $row['onboarding_change_by_name'] . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No modules found</p>';
        }
    }
}
?>
