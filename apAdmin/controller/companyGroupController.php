<?php
include '../common/objectController.php';
if (isset($_POST) && !empty($_POST)) {
    extract($_POST);
    if (isset($addCompanyGroup) && $addCompanyGroup == 'addCompanyGroup') {
        $group_id = intval($_POST['group_id']);
        $group_name = trim($_POST['group_name']);
        $new_societies = isset($_POST['societies']) ? array_filter(array_map('intval', $_POST['societies'])) : [];
        if (count($new_societies) > 10) {
            echo json_encode([
                'status' => 'error',
                'message' => "A group can contain maximum 10 companies"
            ]);
            exit();
        }
        $current_societies = [];
        if ($group_id > 0) {
            $currentData = $d->selectRow("company_ids", "company_group_master", "company_group_id = '$group_id'");
            if (mysqli_num_rows($currentData) > 0) {
                $row = mysqli_fetch_array($currentData);
                $current_societies = !empty($row['company_ids']) ? explode(',', $row['company_ids']) : [];
            }
        }
        $added_societies = array_diff($new_societies, $current_societies);
        $removed_societies = array_diff($current_societies, $new_societies);
        if (!empty($new_societies)) {
            $societyIds = implode(',', $new_societies);
            $existingGroups = $d->selectRow(
                "cgm.company_group_id, cgm.company_group_name, GROUP_CONCAT(sm.society_name) as society_names",
                "company_group_master cgm JOIN society_master sm ON FIND_IN_SET(sm.society_id, cgm.company_ids)",
                "cgm.company_group_id != '$group_id' AND sm.society_id IN ($societyIds)",
                "GROUP BY cgm.company_group_id"
            );
            if ($existingGroups && mysqli_num_rows($existingGroups) > 0) {
                $conflicts = [];
                while ($row = mysqli_fetch_array($existingGroups)) {
                    $conflicts[] = "Group '{$row['company_group_name']}' contains: {$row['society_names']}";
                }
                echo json_encode([
                    'status' => 'error',
                    'message' => "Cannot assign societies. They already belong to: " . implode(", ", $conflicts)
                ]);
                exit();
            }
        }
        $group_array = [
            "company_group_name" => $group_name,
        ];
        if ($group_id > 0) {
            $group_array['updated_by'] = $bms_admin_id;
            $group_array['updated_date'] = date("Y-m-d H:i:s");
            $d->update("company_group_master", $group_array, "company_group_id = $group_id");
        } else {
            $group_array['added_by'] = $bms_admin_id;
            $group_array['added_date'] = date("Y-m-d H:i:s");
            $d->insert("company_group_master", $group_array);
            $group_id = $con->insert_id;
        }
        echo json_encode([
            'status' => 'processing',
            'added_societies' => array_values($added_societies),
            'removed_societies' => array_values($removed_societies),
            'group_id' => $group_id,
            'group_name' => $group_name
        ]);
        exit;
    } else if (isset($getGroupDetails) && $_POST['getGroupDetails'] == 'getGroupDetails' && isset($_POST['group_id'])) {
        $group_id = intval($_POST['group_id']);
        $groupDataQry = $d->selectRow("*", "company_group_master", "company_group_id = '$group_id'");
        if (mysqli_num_rows($groupDataQry) > 0) {
            $groupData = mysqli_fetch_array($groupDataQry);
            $currentCompanies = !empty($groupData['company_ids']) ? explode(',', $groupData['company_ids']) : [];
            $availableSocieties = $d->selectRow("society_id, society_name", "society_master", "society_id NOT IN (SELECT DISTINCT sm.society_id FROM society_master sm JOIN company_group_master cgm ON FIND_IN_SET(sm.society_id, cgm.company_ids) > 0 WHERE cgm.company_ids IS NOT NULL AND cgm.company_ids != '' AND cgm.company_group_id != '$group_id')");
            $companies = [];
            while ($row = mysqli_fetch_array($availableSocieties)) {
                $companies[] = [
                    'id' => $row['society_id'],
                    'name' => $row['society_name'],
                    'selected' => in_array($row['society_id'], $currentCompanies)
                ];
            }
            echo json_encode([
                'success' => true,
                'group_id' => $groupData['company_group_id'],
                'group_name' => $groupData['company_group_name'],
                'companies' => $companies
            ]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    } else if (isset($getAvailableSocieties) && $_POST['getAvailableSocieties'] == 'getAvailableSocieties') {
        $availableSocieties = $d->selectRow("society_id, society_name", "society_master", "society_id NOT IN (SELECT DISTINCT sm.society_id FROM society_master sm JOIN company_group_master cgm ON FIND_IN_SET(sm.society_id, cgm.company_ids) > 0 WHERE cgm.company_ids IS NOT NULL AND cgm.company_ids != '' )");
        $companies = [];
        while ($row = mysqli_fetch_array($availableSocieties)) {
            $companies[] = [
                'id' => $row['society_id'],
                'name' => $row['society_name']
            ];
        }
        echo json_encode([
            'success' => true,
            'companies' => $companies
        ]);
        exit;
    } else if (isset($_POST['updateSingleSocietyGroup'])) {
        $society_id_post = $d->sanitizeActionIdAsInt($_POST['society_id'] ?? 0);
        $group_id = $d->sanitizeActionIdAsInt($_POST['group_id'] ?? 0);
        $action = isset($_POST['action']) ? $_POST['action'] : 'add';
        $group_name = isset($_POST['group_name']) ? trim($_POST['group_name']) : '';

        $societyUrlData = $d->selectRow("society_master.sub_domain", "society_master", "society_master.society_id='$society_id_post'");
        if (mysqli_num_rows($societyUrlData) > 0) {
            $societyData = mysqli_fetch_array($societyUrlData);
            $sub_domain = $societyData['sub_domain'];
            $display_flag = ($action === 'add') ? "1" : "0";
            $crulData = array(
                'changeGroupCompanyStatus' => 'changeGroupCompanyStatus',
                'society_id' => "$society_id_post",
                'display_group_companies' => $display_flag,
            );

            $curl1 = curl_init();
            curl_setopt_array($curl1, array(
                CURLOPT_URL => $sub_domain . 'residentApiNew/societyAnalytics.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $crulData,
                CURLOPT_HTTPHEADER => array(
                    'key: ' . $keydb
                ),
            ));
            $response1 = curl_exec($curl1);
            $code1 = curl_getinfo($curl1, CURLINFO_HTTP_CODE);
            curl_close($curl1);

            if ($code1 == "200") {
                $responseData = json_decode($response1, true);
                if (isset($responseData['status']) && $responseData['status'] == '200') {
                    if ($action === 'add') {
                        $log_note = "added to";
                        $currentData = $d->selectRow("company_ids", "company_group_master", "company_group_id = '$group_id'");
                        if (mysqli_num_rows($currentData)) {
                            $row = mysqli_fetch_array($currentData);
                            $current_societies = !empty($row['company_ids']) ? explode(',', $row['company_ids']) : [];
                            if (!in_array($society_id_post, $current_societies)) {
                                $current_societies[] = $society_id_post;
                                $d->update("company_group_master", [
                                    'company_ids' => implode(',', $current_societies),
                                    'updated_by' => $bms_admin_id,
                                    'updated_date' => date("Y-m-d H:i:s")
                                ], "company_group_id = $group_id");
                            }
                        }
                    } else if ($action === 'remove') {
                        $log_note = "removed from";
                        $currentData = $d->selectRow("company_ids", "company_group_master", "company_group_id = '$group_id'");
                        if (mysqli_num_rows($currentData)) {
                            $row = mysqli_fetch_array($currentData);
                            $current_societies = !empty($row['company_ids']) ? explode(',', $row['company_ids']) : [];
                            $updated_societies = array_diff($current_societies, [$society_id_post]);
                            $d->update("company_group_master", [
                                'company_ids' => implode(',', $updated_societies),
                                'updated_by' => $bms_admin_id,
                                'updated_date' => date("Y-m-d H:i:s")
                            ], "company_group_id = $group_id");
                        }
                    }
                    $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Company $log_note company group ($group_name)");
                    echo json_encode([
                        'status' => 'success',
                        'message' => $responseData['message'] ?? 'Success'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => ($responseData['message'] ?? 'Unknown error') . " : " . ($responseData['status'] ?? 'No status')
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'API request failed with HTTP code ' . $code1
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Society not found'
            ]);
        }
        exit();
    } else if (isset($_POST['checkGroupName'])) {
        $groupName = trim($_POST['checkGroupName']);
        $groupId = isset($_POST['group_id']) ? $_POST['group_id'] : 0;
        $where = "company_group_name = '" . $groupName . "'";
        if ($groupId > 0) {
            $where .= " AND company_group_id != " . $groupId;
        }
        $exists = $d->selectRow("company_group_id", "company_group_master", $where);

        echo json_encode(mysqli_num_rows($exists) == 0);
        exit;
    } else {
        $_SESSION['msg1'] = "Something went wrong";
        header("Location: ../welcome");
        exit();
    }
} else {
    $_SESSION['msg1'] = "Something went wrong";
    header("Location: ../welcome");
    exit();
}
