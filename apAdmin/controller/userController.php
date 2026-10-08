<?php
include '../common/objectController.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

function fetchMycoEmployeesByUserId($d)
{
  $empResponse = $d->callCompanyApiEnc($d->company_url(), 'getAllEmployeesController.php', array(
    'getAllEmployees' => 'getAllEmployees',
    'society_id' => $d->company_id(),
  ));

  if (!isset($empResponse['status']) || (string)$empResponse['status'] !== '200' || !isset($empResponse['employees']) || !is_array($empResponse['employees'])) {
    return null;
  }

  $employeesById = array();
  foreach ($empResponse['employees'] as $emp) {
    if (!isset($emp['user_id']) || $emp['user_id'] === '' || $emp['user_id'] === null) {
      continue;
    }
    $employeesById[(string)$emp['user_id']] = $emp;
  }

  $_SESSION['bound_employee_refresh_map'] = $employeesById;
  return $employeesById;
}

function jsonEmployeeRefreshResponse($payload)
{
  header('Content-Type: application/json');
  echo json_encode($payload);
  exit;
}

if (isset($_POST) && !empty($_POST)) //it can be $_GET doesn't matter
{
  if (isset($_POST['checkMycoUserBind']) && $_POST['checkMycoUserBind'] === 'checkMycoUserBind') {
    header('Content-Type: application/json');
    $bindUserId = $d->sanitizeActionIdAsInt($_POST['user_id'] ?? 0);
    $bindAdminId = $d->sanitizeActionIdAsInt($_POST['admin_id'] ?? 0);

    if ($bindUserId <= 0) {
      echo json_encode(['valid' => true]);
      exit;
    }

    $where = "user_id='$bindUserId' AND user_id IS NOT NULL AND user_id != 0";
    if ($bindAdminId > 0) {
      $where .= " AND admin_id!='$bindAdminId'";
    }

    $existing = $d->selectRow("admin_id, admin_name", "bms_admin_master", $where);
    if ($existing && mysqli_num_rows($existing) > 0) {
      $row = mysqli_fetch_assoc($existing);
      $adminName = !empty($row['admin_name']) ? $row['admin_name'] : 'another admin';
      echo json_encode([
        'valid' => false,
        'message' => "This MyCo user is already bound to $adminName.",
      ]);
      exit;
    }

    echo json_encode(['valid' => true]);
    exit;
  }

  if (isset($_POST['refreshEmployeeInfoList']) && $_POST['refreshEmployeeInfoList'] === 'refreshEmployeeInfoList') {
    $employeesById = fetchMycoEmployeesByUserId($d);
    if ($employeesById === null) {
      jsonEmployeeRefreshResponse([
        'success' => false,
        'message' => 'Failed to fetch employee information',
      ]);
    }

    $boundAdmins = $d->selectRow(
      "admin_id",
      "bms_admin_master",
      "user_id IS NOT NULL AND user_id != 0 AND user_id != ''"
    );

    $adminIds = array();
    if ($boundAdmins && mysqli_num_rows($boundAdmins) > 0) {
      while ($admin = mysqli_fetch_array($boundAdmins)) {
        $adminId = $d->sanitizeActionIdAsInt($admin['admin_id']);
        if ($adminId > 0) {
          $adminIds[] = (string)$adminId;
        }
      }
    }

    if (empty($adminIds)) {
      jsonEmployeeRefreshResponse([
        'success' => false,
        'message' => 'No bound employees found',
      ]);
    }

    jsonEmployeeRefreshResponse([
      'success' => true,
      'admins' => $adminIds,
    ]);
  }

  if (isset($_POST['refreshEmployeeInfo']) && $_POST['refreshEmployeeInfo'] === 'refreshEmployeeInfo') {
    $adminId = $d->sanitizeActionIdAsInt($_POST['admin_id'] ?? 0);
    if ($adminId <= 0) {
      jsonEmployeeRefreshResponse([
        'success' => false,
        'message' => 'Invalid admin',
      ]);
    }

    $employeesById = (isset($_SESSION['bound_employee_refresh_map']) && is_array($_SESSION['bound_employee_refresh_map']))
      ? $_SESSION['bound_employee_refresh_map']
      : fetchMycoEmployeesByUserId($d);

    if ($employeesById === null) {
      jsonEmployeeRefreshResponse([
        'success' => false,
        'message' => 'Failed to fetch employee information',
      ]);
    }

    $adminQ = $d->selectRow(
      "admin_id, admin_name, user_id, user_full_name, branch_name, user_designation, department_name",
      "bms_admin_master",
      "admin_id='$adminId' AND user_id IS NOT NULL AND user_id != 0 AND user_id != ''"
    );
    $admin = ($adminQ && mysqli_num_rows($adminQ) > 0) ? mysqli_fetch_array($adminQ) : null;
    if (!$admin) {
      jsonEmployeeRefreshResponse([
        'success' => false,
        'message' => 'Bound employee not found',
      ]);
    }

    $boundUserId = (string)$admin['user_id'];
    if (!isset($employeesById[$boundUserId])) {
      jsonEmployeeRefreshResponse([
        'success' => false,
        'message' => 'Employee not found in MyCo',
      ]);
    }

    $emp = $employeesById[$boundUserId];
    $newFullName = isset($emp['user_full_name']) ? trim((string)$emp['user_full_name']) : '';
    $newBranch = isset($emp['branch_name']) ? trim((string)$emp['branch_name']) : '';
    $newDesignation = isset($emp['user_designation']) ? trim((string)$emp['user_designation']) : '';
    $newDepartment = isset($emp['department_name']) ? trim((string)$emp['department_name']) : '';

    if (
      (string)$admin['user_full_name'] === $newFullName &&
      (string)$admin['branch_name'] === $newBranch &&
      (string)$admin['user_designation'] === $newDesignation &&
      (string)$admin['department_name'] === $newDepartment
    ) {
      jsonEmployeeRefreshResponse([
        'success' => true,
        'message' => 'Unchanged',
      ]);
    }

    $m->set_data('user_full_name', $newFullName);
    $m->set_data('branch_name', $newBranch);
    $m->set_data('user_designation', $newDesignation);
    $m->set_data('department_name', $newDepartment);

    $a3 = array(
      'user_full_name' => $m->get_data('user_full_name'),
      'branch_name' => $m->get_data('branch_name'),
      'user_designation' => $m->get_data('user_designation'),
      'department_name' => $m->get_data('department_name'),
      'updated_by' => $updated_by,
      'updated_date' => date("Y-m-d"),
    );

    $q = $d->update("bms_admin_master", $a3, "admin_id='$adminId'");
    if (!$q) {
      jsonEmployeeRefreshResponse([
        'success' => false,
        'message' => 'Failed to update',
      ]);
    }

    $adminName = !empty($admin['admin_name']) ? $admin['admin_name'] : ('Admin #' . $adminId);
    $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Bound employee info refreshed for $adminName");
    jsonEmployeeRefreshResponse([
      'success' => true,
      'message' => 'Updated',
    ]);
  }

  if (isset($_POST['addUser'])) {
    $enc_admin_email = $d->encryptDecrypt("encrypt", $admin_email);
    $enc_admin_mobile = $d->encryptDecrypt("encrypt", $admin_mobile);
    $country_code = $d->escapeSqlString($country_code ?? '');
    $qcheck = $d->select("bms_admin_master", "admin_email='$enc_admin_email'");
    $qcheckMobile = $d->select("bms_admin_master", "admin_mobile='$enc_admin_mobile' AND country_code='$country_code'");
    if (mysqli_num_rows($qcheck) > 0) {
      $_SESSION['msg1'] = "Email Already Register";
      header("location:../addUsers");
      exit();
    }
    if (mysqli_num_rows($qcheckMobile) > 0) {
      $_SESSION['msg1'] = "Mobile Number Already Register";
      header("location:../addUsers");
      exit();
    }

    $randomPassword = $d->randomPassword();
    $hashed_password = password_hash($randomPassword, PASSWORD_DEFAULT);

    $m->set_data('admin_name', $admin_name);
    $m->set_data('role_id', $role_id);
    $m->set_data('admin_email', $enc_admin_email);
    $m->set_data('country_code', $country_code);
    $m->set_data('admin_mobile', $enc_admin_mobile);
    $m->set_data('admin_password', $hashed_password);
    $m->set_data('primary_language_id', $primary_language_id);
    $m->set_data('default_time_zone', $default_time_zone);
    $m->set_data('is_developer', $is_developer);
    $allowedProductsData = '';
    $allowedPlatformsData = '';
    $developerPermissionsData = '';
    if ($is_developer == 1) {
      if (isset($_POST['allowed_products']) && is_array($_POST['allowed_products'])) {
        $allowedProductsData = implode(",", $_POST['allowed_products']);
      }
      if (isset($_POST['allowed_platforms']) && is_array($_POST['allowed_platforms'])) {
        $allowedPlatformsData = implode(",", $_POST['allowed_platforms']);
      }
      if (isset($_POST['developer_permissions']) && is_array($_POST['developer_permissions'])) {
        $developerPermissionsData = implode(",", $_POST['developer_permissions']);
      }
    }
    $m->set_data('allowed_products', $allowedProductsData);
    $m->set_data('allowed_platforms', $allowedPlatformsData);
    $m->set_data('developer_permissions', $developerPermissionsData);
    $m->set_data('platform', $allowedPlatformsData);

    $bindUserId = isset($_POST['user_id']) ? trim((string)$_POST['user_id']) : '';
    $bindUserFullName = '';
    $bindBranchName = '';
    $bindUserDesignation = '';
    $bindDepartmentName = '';
    if ($bindUserId !== '' && ctype_digit($bindUserId)) {
      $bindUserFullName = isset($_POST['user_full_name']) ? trim((string)$_POST['user_full_name']) : '';
      $bindBranchName = isset($_POST['branch_name']) ? trim((string)$_POST['branch_name']) : '';
      $bindUserDesignation = isset($_POST['user_designation']) ? trim((string)$_POST['user_designation']) : '';
      $bindDepartmentName = isset($_POST['department_name']) ? trim((string)$_POST['department_name']) : '';
      $m->set_data('user_id', (int)$bindUserId);
      $m->set_data('user_full_name', $bindUserFullName);
      $m->set_data('branch_name', $bindBranchName);
      $m->set_data('user_designation', $bindUserDesignation);
      $m->set_data('department_name', $bindDepartmentName);
    } else {
      $m->set_data('user_id', null);
      $m->set_data('user_full_name', null);
      $m->set_data('branch_name', null);
      $m->set_data('user_designation', null);
      $m->set_data('department_name', null);
    }
    $a3 = array(
      'admin_name' => $m->get_data('admin_name'),
      'role_id' => $m->get_data('role_id'),
      'admin_email' => $m->get_data('admin_email'),
      'country_code' => $m->get_data('country_code'),
      'admin_mobile' => $m->get_data('admin_mobile'),
      'admin_password' => $m->get_data('admin_password'),
      'created_by' => $created_by,
      'created_date' => date("Y-m-d"),
      'primary_language_id' => $m->get_data('primary_language_id'),
      'default_time_zone' => $m->get_data('default_time_zone'),
      'is_developer' => $m->get_data('is_developer'),
      'allowed_products' => $m->get_data('allowed_products'),
      'allowed_platforms' => $m->get_data('allowed_platforms'),
      'developer_permissions' => $m->get_data('developer_permissions'),
      'platform' => $m->get_data('platform'),
      'user_id' => $m->get_data('user_id'),
      'user_full_name' => $m->get_data('user_full_name'),
      'branch_name' => $m->get_data('branch_name'),
      'user_designation' => $m->get_data('user_designation'),
      'department_name' => $m->get_data('department_name'),
    );

    if (!empty($country_code) && !empty($admin_mobile)) {
      $a3['display_admin_mobile'] = $d->encryptDecrypt("encrypt", $country_code . $admin_mobile);
    }

    $q = $d->insert("bms_admin_master", $a3);
    $bms_admin_id = $con->insert_id;

    if ($q > 0) {
      for ($i2 = 0; $i2 < count($_POST['country_id']); $i2++) {
        $m->set_data('country_id', $_POST['country_id'][$i2]);
        $m->set_data('bms_admin_id', $bms_admin_id);

        $a111 = array(
          'country_id' => $m->get_data('country_id'),
          'bms_admin_id' => $m->get_data('bms_admin_id'),
        );

        $d->insert("admin_country_master", $a111);
      }
      if (isset($_POST['fcm_notifications'])) {
        // code...
        for ($i3 = 0; $i3 < count($_POST['fcm_notifications']); $i3++) {
          $m->set_data('fcm_notifications', $_POST['fcm_notifications'][$i3]);
          $m->set_data('bms_admin_id', $bms_admin_id);

          $a111 = array(
            'fcm_notifications' => $m->get_data('fcm_notifications'),
            'bms_admin_id' => $m->get_data('bms_admin_id'),
          );

          $d->insert("admin_fcm_notification_master", $a111);
        }
      }

      $to = $admin_email;
      $subject = "Account Created For ".$d->app_name()." Master Panel";
      $forgotLink = "https://master.my-company.app/apAdmin/";
      $admin_password = $randomPassword;
      if ($to != '') {
        include '../mail/newAdminMail.php';
        include '../mail.php';
      }

      $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Master Panel User $admin_name Added");
      $_SESSION['msg'] = "New Admin Added";
      header("location:../manageUsers");
    } else {
      $_SESSION['msg1'] = "Something Wrong";
      header("location:../manageUsers");
    }
  }
  if (isset($_POST['editUser'])) {
    $admin_id = $d->sanitizeActionIdAsInt($admin_id);

    $enc_admin_email = $d->encryptDecrypt("encrypt", $admin_email);
    $enc_admin_mobile = $d->encryptDecrypt("encrypt", $admin_mobile);
    $country_code = $d->escapeSqlString($country_code ?? '');
    $qcheck = $d->select("bms_admin_master", "admin_email='$enc_admin_email' AND admin_id!='$admin_id'");
    $qcheckMobile = $d->select("bms_admin_master", "country_code='$country_code' AND admin_mobile='$enc_admin_mobile' AND admin_id!='$admin_id'");
    if (mysqli_num_rows($qcheck) > 0) {
      $_SESSION['msg1'] = "Email Already Register";
      header("location:../manageUsers");
      exit();
    }
    if (mysqli_num_rows($qcheckMobile) > 0) {
      $_SESSION['msg1'] = "Mobile Number Already Register";
      header("location:../manageUsers");
      exit();
    }

    $m->set_data('admin_name', $admin_name);
    $m->set_data('role_id', $role_id);
    $m->set_data('admin_email', $enc_admin_email);
    $m->set_data('country_code', $country_code);
    $m->set_data('admin_mobile', $enc_admin_mobile);
    $m->set_data('primary_language_id', $primary_language_id);
    $m->set_data('default_time_zone', $default_time_zone);
    $m->set_data('is_developer', $is_developer);
    $allowedProductsData = '';
    $allowedPlatformsData = '';
    $developerPermissionsData = '';
    if ($is_developer == 1) {
      if (isset($_POST['allowed_products']) && is_array($_POST['allowed_products'])) {
        $allowedProductsData = implode(",", $_POST['allowed_products']);
      }
      if (isset($_POST['allowed_platforms']) && is_array($_POST['allowed_platforms'])) {
        $allowedPlatformsData = implode(",", $_POST['allowed_platforms']);
      }
      if (isset($_POST['developer_permissions']) && is_array($_POST['developer_permissions'])) {
        $developerPermissionsData = implode(",", $_POST['developer_permissions']);
      }
    }
    $m->set_data('allowed_products', $allowedProductsData);
    $m->set_data('allowed_platforms', $allowedPlatformsData);
    $m->set_data('developer_permissions', $developerPermissionsData);
    $m->set_data('platform', $allowedPlatformsData);

    $bindUserId = isset($_POST['user_id']) ? trim((string)$_POST['user_id']) : '';
    $bindUserFullName = '';
    $bindBranchName = '';
    $bindUserDesignation = '';
    $bindDepartmentName = '';
    if ($bindUserId !== '' && ctype_digit($bindUserId)) {
      $bindUserFullName = isset($_POST['user_full_name']) ? trim((string)$_POST['user_full_name']) : '';
      $bindBranchName = isset($_POST['branch_name']) ? trim((string)$_POST['branch_name']) : '';
      $bindUserDesignation = isset($_POST['user_designation']) ? trim((string)$_POST['user_designation']) : '';
      $bindDepartmentName = isset($_POST['department_name']) ? trim((string)$_POST['department_name']) : '';
      $m->set_data('user_id', (int)$bindUserId);
      $m->set_data('user_full_name', $bindUserFullName);
      $m->set_data('branch_name', $bindBranchName);
      $m->set_data('user_designation', $bindUserDesignation);
      $m->set_data('department_name', $bindDepartmentName);
    } else {
      $m->set_data('user_id', null);
      $m->set_data('user_full_name', null);
      $m->set_data('branch_name', null);
      $m->set_data('user_designation', null);
      $m->set_data('department_name', null);
    }

    $a3 = array(
      'admin_name' => $m->get_data('admin_name'),
      'role_id' => $m->get_data('role_id'),
      'admin_email' => $m->get_data('admin_email'),
      'country_code' => $m->get_data('country_code'),
      'admin_mobile' => $m->get_data('admin_mobile'),
      'updated_by' => $updated_by,
      'updated_date' => date("Y-m-d"),
      'primary_language_id' => $m->get_data('primary_language_id'),
      'default_time_zone' => $m->get_data('default_time_zone'),
      'is_developer' => $m->get_data('is_developer'),
      'allowed_products' => $m->get_data('allowed_products'),
      'allowed_platforms' => $m->get_data('allowed_platforms'),
      'developer_permissions' => $m->get_data('developer_permissions'),
      'platform' => $m->get_data('platform'),
      'user_id' => $m->get_data('user_id'),
      'user_full_name' => $m->get_data('user_full_name'),
      'branch_name' => $m->get_data('branch_name'),
      'user_designation' => $m->get_data('user_designation'),
      'department_name' => $m->get_data('department_name'),
    );
    if (!empty($country_code) && !empty($admin_mobile)) {
      $a3['display_admin_mobile'] = $d->encryptDecrypt("encrypt", $country_code . $admin_mobile);
    }
    $q = $d->update("bms_admin_master", $a3, "admin_id='$admin_id'", 1);
    if ($q > 0) {
      $d->delete("admin_country_master", "bms_admin_id='$admin_id'");
      $d->delete("admin_fcm_notification_master", "bms_admin_id='$admin_id'");
      for ($i2 = 0; $i2 < count($_POST['country_id']); $i2++) {
        $m->set_data('country_id', $_POST['country_id'][$i2]);
        $m->set_data('bms_admin_id', $admin_id);
        $a111 = array(
          'country_id' => $m->get_data('country_id'),
          'bms_admin_id' => $m->get_data('bms_admin_id'),
        );
        $d->insert("admin_country_master", $a111);
      }
      if (isset($_POST['fcm_notifications'])) {
        for ($i3 = 0; $i3 < count($_POST['fcm_notifications']); $i3++) {
          $m->set_data('fcm_notifications', $_POST['fcm_notifications'][$i3]);
          $m->set_data('bms_admin_id', $admin_id);

          $a111 = array(
            'fcm_notifications' => $m->get_data('fcm_notifications'),
            'bms_admin_id' => $m->get_data('bms_admin_id'),
          );
          $d->insert("admin_fcm_notification_master", $a111);
        }
      }
      $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Panel User $admin_name Updated");
      $_SESSION['msg'] = "Admin Details Updated";
      header("location:../manageUsers");
    } else {
      $_SESSION['msg1'] = "Something Wrong";
      header("location:../manageUsers");
    }
  }
  if (isset($_POST['deleteUser'])) {
    $user_id = $d->sanitizeActionIdAsInt($_POST['user_id'] ?? ($user_id ?? 0));
    $a3 = array(
      'deleted_by' => $created_by,
      'deleted_date' => date('Y-m-d'),
      'active_status' => 1,
    );
    $q = $d->update("bms_admin_master", $a3, "admin_id='$admin_id'");
    if ($q > 0) {
      $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "$admin_name Admin User Deactivated");
      $_SESSION['msg'] = "User Deactivated";
      header("location:../manageUsers");
    } else {
      $_SESSION['msg1'] = "Something Wrong";
      header("location:../manageUsers");
    }
  }

  if (isset($_POST['deleteUserReactive'])) {
    $user_id = $d->sanitizeActionIdAsInt($_POST['user_id'] ?? ($user_id ?? 0));
    $a3 = array(
      'active_status' => 0,
    );
    $q = $d->update("bms_admin_master", $a3, "admin_id='$admin_id'");
    if ($q > 0) {
      $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "$admin_name Admin User Activated");
      $_SESSION['msg'] = "User Activated";
      header("location:../manageUsers");
    } else {
      $_SESSION['msg1'] = "Something Wrong";
      header("location:../manageUsers");
    }
  }
} else {
  header('location:../login');
}
