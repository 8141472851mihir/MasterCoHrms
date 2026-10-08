<?php
include '../common/objectController.php';
if (isset($_POST) && !empty($_POST)) {
    if (isset($_POST['subDomainCheck'])) {
        $sub_domain_check = trim($sub_domain_check);
        $edit_society_id = isset($_POST['edit_society_id']) ? $_POST['edit_society_id'] : '';
        $where = "sub_domain = '$sub_domain_check'";
        if (is_numeric($edit_society_id)) {
            $where .= " AND society_id != '$edit_society_id'";
        }
        $existing = $d->select("society_master_white_label", $where);
        echo json_encode(['valid' => mysqli_num_rows($existing) === 0]);
        exit;
    }
    if (isset($addWhitelabel)) {
        $m->set_data('master_company_id', test_input($master_company_id));
        $m->set_data('society_name', test_input($society_name));
        $m->set_data('country_id', test_input($country_id));
        $m->set_data('state_id', test_input($state_id));
        $m->set_data('city_id', test_input($city_id));
        $m->set_data('society_address', $society_address);
        $m->set_data('sub_domain', $sub_domain);
        $m->set_data('project_type', $project_type);
        // optional logo upload
        $powered_by_logo = '';
        if (isset($_FILES['powered_by_logo']['tmp_name']) && file_exists($_FILES['powered_by_logo']['tmp_name'])) {
            $acceptable = array('jpeg','jpg','png','webp','svg');
            $extId = strtolower(pathinfo($_FILES['powered_by_logo']['name'], PATHINFO_EXTENSION));
            $dirPath = "../../img/whitelabel/";
            if (in_array($extId, $acceptable)) {
                if (!is_dir($dirPath)) { @mkdir($dirPath, 0755, true); }
                $temp = explode(".", $_FILES["powered_by_logo"]["name"]);
                $powered_by_logo = 'WL_' . round(microtime(true)) . '.' . end($temp);
                $destinationPath = $dirPath . $powered_by_logo;
                if ($extId === 'svg') {
                    move_uploaded_file($_FILES['powered_by_logo']['tmp_name'], $destinationPath);
                } else {
                    $d->resizeImage($_FILES['powered_by_logo']['tmp_name'], $destinationPath, 600, 600, $extId);
                }
            }
        }

        $map_project_type = [0=>'MyCo', 1=>'Smart Society', 2=>'My Assosiation'];

        $a = array(
            'master_company_id' => $m->get_data('master_company_id'),
            'society_name' => $m->get_data('society_name'),
            'country_id' => $m->get_data('country_id'),
            'state_id' => $m->get_data('state_id'),
            'city_id' => $m->get_data('city_id'),
            'society_address' => $m->get_data('society_address'),
            'sub_domain' => $m->get_data('sub_domain'),
            'project_type' => $m->get_data('project_type'),
        );
        if (!empty($powered_by_logo)) { $a['powered_by_logo'] = $powered_by_logo; }
        $q = $d->insert("society_master_white_label", $a);
        if ($q > 0) {
            $d->insert_log("$society_name", "$bms_admin_id", "$created_by", "White Label Added for $map_project_type[$project_type]");
            $_SESSION['msg'] = "White Label Added for $map_project_type[$project_type]";
            $_SESSION['active_tab'] = $project_type; 
            header("location:../manageWhiteLabel"); 
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("location:../manageWhiteLabel");
        }
    }
    if (isset($editWhitelabel)) {
        $m->set_data('master_company_id', test_input($master_company_id));
        $m->set_data('society_name', test_input($society_name));
        $m->set_data('country_id', test_input($country_id));
        $m->set_data('state_id', test_input($state_id));
        $m->set_data('city_id', test_input($city_id));
        $m->set_data('society_address', $society_address);
        $m->set_data('sub_domain', $sub_domain);
        $m->set_data('project_type', $project_type);
        // optional logo upload retain old when not provided
        $powered_by_logo = isset($_POST['powered_by_logo_old']) ? $_POST['powered_by_logo_old'] : '';
        if (isset($_FILES['powered_by_logo']['tmp_name']) && file_exists($_FILES['powered_by_logo']['tmp_name'])) {
            $acceptable = array('jpeg','jpg','png','webp','svg');
            $extId = strtolower(pathinfo($_FILES['powered_by_logo']['name'], PATHINFO_EXTENSION));
            $dirPath = "../../img/whitelabel/";
            if (in_array($extId, $acceptable)) {
                if (!is_dir($dirPath)) { @mkdir($dirPath, 0755, true); }
                $temp = explode(".", $_FILES["powered_by_logo"]["name"]);
                $powered_by_logo = 'WL_' . round(microtime(true)) . '.' . end($temp);
                $destinationPath = $dirPath . $powered_by_logo;
                if ($extId === 'svg') {
                    move_uploaded_file($_FILES['powered_by_logo']['tmp_name'], $destinationPath);
                } else {
                    $d->resizeImage($_FILES['powered_by_logo']['tmp_name'], $destinationPath, 600, 600, $extId);
                }
            }
        }

        $map_project_type = [0=>'MyCo', 1=>'Smart Society', 2=>'My Assosiation'];

        $a = array(
            'master_company_id' => $m->get_data('master_company_id'),
            'society_name' => $m->get_data('society_name'),
            'country_id' => $m->get_data('country_id'),
            'state_id' => $m->get_data('state_id'),
            'city_id' => $m->get_data('city_id'),
            'society_address' => $m->get_data('society_address'),
            'sub_domain' => $m->get_data('sub_domain'),
            'project_type' => $m->get_data('project_type'),
        );
        if (!empty($powered_by_logo)) { $a['powered_by_logo'] = $powered_by_logo; }

        $q = $d->update("society_master_white_label", $a, "society_id='$society_id'");
        if ($q > 0) {
            $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "White Label Updated for $map_project_type[$project_type]");
            $_SESSION['msg'] = "White Label Updated for $map_project_type[$project_type]";
            $_SESSION['active_tab'] = $project_type; 
            header("location:../manageWhiteLabel");
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("location:../manageWhiteLabel");
        }
    } else if (isset($_POST['deleteWhiteLable'])) {
        $society_delete_id = $d->sanitizeActionIdAsInt($_POST['society_delete_id'] ?? ($society_delete_id ?? 0));
        $society_id = $_POST['society_delete_id'];//sagar add 23-05-2025
        $project_type = $_POST['project_type'];
        $map_project_type = [0=>'MyCo', 1=>'Smart Society', 2=>'My Assosiation'];
        $q = $d->delete("society_master_white_label", "society_id='$society_id'");
        if ($q == TRUE) {
            $_SESSION['msg'] = "White Label Deleted for {$map_project_type[$project_type]}";//sagar change my co replace with  $map_project_type[$project_type] 23-05-2025
            $_SESSION['active_tab'] = $project_type; 
            header("Location: ../manageWhiteLabel");
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("Location: ../manageWhiteLabel");
        }
    }
}