<?php 
// used in crm
include_once 'lib.php';
$compress = resolve_api_compress();
$is_encrypted = 0;
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted, [
        "status" => "201",
        "message" => "Invalid Request"
    ], $compress);
    exit();
}
try {
    if (isset($_POST) && !empty($_POST)) {
        $response = array();
        extract(array_map("test_input", $_POST));
        if ($key == $keydb) {
            if (isset($_POST['getAdminUsersList']) && $_POST['getAdminUsersList'] == 'getAdminUsersList') {
                $filter = "1=1";
                if (isset($active_status) && strtolower((string)$active_status) === 'all') {
                    // no status filter
                } elseif (isset($active_status) && $active_status !== '') {
                    $activeStatus = (int)$active_status;
                    $filter .= " AND bms_admin_master.active_status='$activeStatus'";
                } else {
                    $filter .= " AND bms_admin_master.active_status='0'";
                }

                if (isset($role_id) && $role_id !== '') {
                    $roleId = (int)$role_id;
                    $filter .= " AND bms_admin_master.role_id='$roleId'";
                }

                if (isset($is_developer) && $is_developer !== '') {
                    $isDeveloper = (int)$is_developer;
                    $filter .= " AND bms_admin_master.is_developer='$isDeveloper'";
                }

                $q = $d->selectRow(
                    "bms_admin_master.admin_id,
                     bms_admin_master.admin_name,
                     bms_admin_master.admin_email,
                     bms_admin_master.admin_mobile,
                     bms_admin_master.country_code,
                     bms_admin_master.role_id,
                     bms_admin_master.active_status,
                     bms_admin_master.is_developer,
                     bms_admin_master.default_time_zone,
                     bms_admin_master.user_id,
                     bms_admin_master.user_full_name,
                     bms_admin_master.user_designation,
                     bms_admin_master.department_name,
                     bms_admin_master.branch_name,
                     bms_admin_master.created_date,
                     bms_admin_master.updated_date,
                     role_master.role_name",
                    "bms_admin_master
                     LEFT JOIN role_master ON role_master.role_id = bms_admin_master.role_id",
                    $filter,
                    "ORDER BY bms_admin_master.admin_name ASC"
                );

                $response["adminUsers"] = array();
                if ($q && mysqli_num_rows($q) > 0) {
                    while ($row = mysqli_fetch_assoc($q)) {
                        $adminUser = array();
                        $adminUser["admin_id"] = (int)$row['admin_id'];
                        $adminUser["admin_name"] = $row['admin_name'] ?? '';
                        $adminUser["admin_email"] = !empty($row['admin_email'])
                            ? $d->encryptDecrypt("decrypt", $row['admin_email'])
                            : '';
                        $adminUser["admin_mobile"] = !empty($row['admin_mobile'])
                            ? $d->encryptDecrypt("decrypt", $row['admin_mobile'])
                            : '';
                        $adminUser["country_code"] = $row['country_code'] ?? '';
                        $adminUser["role_id"] = (int)($row['role_id'] ?? 0);
                        $adminUser["role_name"] = $row['role_name'] ?? '';
                        $adminUser["active_status"] = (string)($row['active_status'] ?? '0');
                        $adminUser["is_developer"] = (string)($row['is_developer'] ?? '0');
                        $adminUser["default_time_zone"] = $row['default_time_zone'] ?? '';
                        $adminUser["user_id"] = $row['user_id'] ?? '';
                        $adminUser["user_full_name"] = $row['user_full_name'] ?? '';
                        $adminUser["user_designation"] = $row['user_designation'] ?? '';
                        $adminUser["department_name"] = $row['department_name'] ?? '';
                        $adminUser["branch_name"] = $row['branch_name'] ?? '';
                        $adminUser["created_date"] = $row['created_date'] ?? '';
                        $adminUser["updated_date"] = $row['updated_date'] ?? '';
                        array_push($response["adminUsers"], $adminUser);
                    }
                }

                $response["message"] = "Success";
                $response["status"] = "200";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            } else {
                $response["message"] = "Wrong Tag";
                $response["status"] = "201";
                echo $d->manage_encryption($is_encrypted, $response, $compress);
                exit();
            }
        } else {
            $response["message"] = "Wrong Api Key";
            $response["status"] = "201";
            echo $d->manage_encryption($is_encrypted, $response, $compress);
            exit();
        }
    } else {
        $response["message"] = "Invalid Request Method";
        $response["status"] = "201";
        echo $d->manage_encryption($is_encrypted, $response, $compress);
        exit();
    }
} catch (Exception $e) {
    $response['status'] = "201";
    $response['message'] = $e->getMessage();
}
echo $d->manage_encryption($is_encrypted, $response, $compress);
exit();
