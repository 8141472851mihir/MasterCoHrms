<?php
include '../common/objectController.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (isset($_POST) && !empty($_POST)) //it can be $_GET doesn't matter
{
    // echo "<pre>";
    // print_r($_POST);
    // exit;


    if (isset($_POST['createCRM']) && $_POST['companyId'] > 0) {
        $curl = curl_init();

        $mycoBackendUrl = $subDomain . 'crmApi';
        $frontendUrl = $subDomain . 'crm';
        $crm_plan_expiring_date = $_POST['crm_plan_expiring_date'];

        $postDataArray =  array(
            'companyId' => $companyId,
            'companyName' => $companyName,
            'subDomain' => $subDomain,
            'mycoBackendUrl' => $mycoBackendUrl,
            'frontendUrl' => $frontendUrl,
            'planExpiringDate' => $crm_plan_expiring_date,
        );
        $qc = $d->select("society_crm_master", "society_crm_id='$society_crm_id'");
        $cData = mysqli_fetch_array($qc);
        $society_crm_id = $cData['society_crm_id'];
        $society_crm_url = $cData['url'];
        $society_crm_token = $cData['token'];
        $crm_token = $cData['token'];
        $crm_url = $cData['url'] . 'api/v1/tenant';
        curl_setopt_array($curl, array(
            CURLOPT_URL => $crm_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postDataArray),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $crm_token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $societyUrlData = $d->selectRow("domain_master.domain_name,society_master.sub_domain", "society_master LEFT JOIN domain_master ON domain_master.domain_id = society_master.domain_id", "society_master.society_id='$companyId'");
        if (mysqli_num_rows($societyUrlData) > 0) {
            $societyData = mysqli_fetch_array($societyUrlData);
            // upload crm data to society
            $sub_domain = $societyData['sub_domain'];
            $crulData = array(
                'updateSocietyCrm' => 'updateSocietyCrm',
                'society_id' => "$companyId",
                'crm_url' => "$society_crm_url",
                'crm_token' => "$society_crm_token"
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
            curl_close($curl1);
        }
        $code1 = curl_getinfo($curl1, CURLINFO_HTTP_CODE);
        if ($code1 == '200') {

            // active on company database
            if (isset($_POST['crm_limit'])) {
                $crm_package_id = $_POST['crm_package_id'];
                $crm_plan_expiring_date = $_POST['crm_plan_expiring_date'];
                $crm_trial_days = $_POST['crm_trial_days'];
                $crm_limit = $_POST['crm_limit'];

                $a5 = array(
                    'crm_limit' => $crm_limit,
                    'society_crm_id' => $society_crm_id,
                    'crm_created' => 1,
                    'crm_created_by' => $created_by,
                    'crm_created_date' => date("Y-m-d H:i:s"),
                    'crm_package_id' => $crm_package_id,
                    'crm_trial_days' => $crm_trial_days,
                    'crm_plan_expiring_date' => $crm_plan_expiring_date
                );
                $sub_domain = $societyData['sub_domain'];
                $target_url = $sub_domain . "residentApiNew/societyAnalytics.php";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $target_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "setCrmLimit=setCrmLimit&society_id=$companyId&language_id=1&crmLimit=$crm_limit");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'key: ' . $keydb
                ));
                $result = curl_exec($ch);
                curl_close($ch);
                $d->update("society_master", $a5, "society_id='$companyId'");

                $d->insert_log($companyId, "$bms_admin_id", "$created_by", "CRM Activated for $companyName");
                $_SESSION['msg'] = "CRM Added Successfully";
                header("location:../viewCompanies");
                exit();
            }
        } else {
            $_SESSION['msg1'] = "Something Wrong with CRM API";
            header("location:../viewCompanies");
            exit();
        }
    }

    // update building details
    if (isset($_POST['updateBuildingSingle']) && $society_address != '') {
        $file_socieaty_logo = $_FILES['socieaty_logo']['tmp_name'];
        if (file_exists($file_socieaty_logo)) {
            $acceptable = array("jpeg", "jpg", "png");
            $extId = pathinfo($_FILES['socieaty_logo']['name'], PATHINFO_EXTENSION);
            $dirPath = "../../img/society/";
            if (in_array($extId, $acceptable) && (!empty($_FILES["socieaty_logo"]["type"]))) {
                $temp = explode(".", $_FILES["socieaty_logo"]["name"]);
                $socieaty_logo = 'Society_' . round(microtime(true)) . '.' . end($temp);
                $destinationPath = $dirPath . $socieaty_logo;
                $d->resizeImage($file_socieaty_logo, $destinationPath, 500, 500, $extId);
            } else {
                $_SESSION['msg1'] = "Invalid Photo";
                header("location:../buildingDetails");
                exit();
            }
        } else {
            $socieaty_logo = $socieaty_logo_old;
        }
        $m->set_data('society_name', $society_name_update);
        $m->set_data('society_based', $society_based);
        $m->set_data('society_address', $society_address);
        // $m->set_data('secretary_email',$secretary_email);
        $m->set_data('socieaty_logo', $socieaty_logo);
        $m->set_data('builder_name', $builder_name);
        $m->set_data('builder_address', $builder_address);
        $m->set_data('builder_mobile', $builder_mobile);
        $m->set_data('gst_no', $gst_no);

        $a = array(
            'society_name' => $m->get_data('society_name'),
            'society_based' => $m->get_data('society_based'),
            'society_address' => $m->get_data('society_address'),
            // 'secretary_email'=>$m->get_data('secretary_email'),
            'socieaty_logo' => $m->get_data('socieaty_logo'),
            'builder_name' => $m->get_data('builder_name'),
            'builder_address' => $m->get_data('builder_address'),
            'builder_mobile' => $m->get_data('builder_mobile'),
            'gst_no' => $m->get_data('gst_no'),
        );

        $q = $d->update("society_master", $a, "society_id='$society_id'");
        if ($q > 0) {
            $society_name = $society_name_update;
            $_SESSION['msg'] = " Company Details Updated.";
            $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Company Details Updated.");
            header("location:../buildingDetails");
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("location:../buildingDetails");
        }
    }


    if (isset($society_id_delete)) {
        $society_id_delete = $d->sanitizeActionIdAsInt($society_id_delete);

        // check Company is active or remove
        $sq = $d->select("society_master", "society_id='$society_id_delete'");
        $socData = mysqli_fetch_array($sq);
        $server_url = $socData['sub_domain'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $server_url . "/apAdmin/index.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            "society_id=$society_id_delete&checkUrl=checkUrl"
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'key: ' . EnvLoader::get('API_KEY')
        ));

        $server_output2 = curl_exec($ch);

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code != '404') {
            $_SESSION['msg1'] = "Please Delete Server Code & Database First !";
            header("location:../viewCompanies?countryId=$country_id&sId=$state_id&cId=$city_id");
            exit();
        }


        // delete form master 
        $d->delete("app_common_slider_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("auth_log_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("feedback_master", "society_id='$society_id_delete' AND society_id!=0 AND is_whitelabel='0'");
        $d->delete("resident_app_menu_society", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("slider_post_log_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("post_log_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("society_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("society_analytics_master", "society_id='$society_id_delete' AND society_id!=0");

        $d->delete("admin_notification", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("crm_training_progress", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("cron_error_logs", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("crons_society_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete( "feedback_log_master", "feedback_id IS NOT NULL AND NOT EXISTS ( SELECT 1 FROM feedback_master fm WHERE fm.feedback_id = feedback_log_master.feedback_id AND fm.society_id='$society_id_delete' AND fm.society_id!=0)");

        $d->delete("kycapi_companyprice_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("language_key_value_master_society", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("society_resent_analytics_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("society_users_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("timeline_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("training_attend_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("training_completion_form_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("training_schedule_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("training_status_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("training_visit_master", "society_id='$society_id_delete' AND society_id!=0");
        $d->delete("whatsapp_access_master", "society_id='$society_id_delete' AND society_id!=0");
        



        $_SESSION['msg'] = "$sName Company Deleted.";
        $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "$sName  Company Deleted.");
        header("location:../viewCompanies?countryId=$country_id&sId=$state_id&cId=$city_id");
    }


    // update building details
    if (isset($_POST['addPaymentGetwat'])) {

        $m->set_data('society_id', $society_id);
        $m->set_data('payment_getway_master_id', $payment_getway_master_id);
        $m->set_data('merchant_id', $merchant_id);
        $m->set_data('merchant_key', $merchant_key);
        $m->set_data('salt_key', $salt_key);

        $a = array(
            'society_id' => $m->get_data('society_id'),
            'payment_getway_master_id' => $m->get_data('payment_getway_master_id'),
            'merchant_id' => $m->get_data('merchant_id'),
            'merchant_key' => $m->get_data('merchant_key'),
            'salt_key' => $m->get_data('salt_key'),
        );
        $cq = $d->select("society_payment_getway", "society_id='$society_id'");
        $data = mysqli_fetch_array($cq);
        if ($data > 0) {
            $q = $d->update("society_payment_getway", $a, "society_id='$society_id'");
        } else {
            $q = $d->insert("society_payment_getway", $a);
        }
        if ($q > 0) {
            $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Payment Getway Details Updated.");
            $_SESSION['msg'] = "Payment Getway Details Updated.";
            header("location:../paymentGatewaySetting");
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("location:../paymentGatewaySetting");
        }
    }

    if (isset($_POST['removePaymentGetway'])) {

        $q = $d->delete("society_payment_getway", "society_id='$society_id'");

        if ($q > 0) {
            $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Payment Getway Details Removed.");
            $_SESSION['msg'] = "Payment Getway Details Removed.";
            header("location:../paymentGatewaySetting");
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("location:../paymentGatewaySetting");
        }
    }

    if (isset($_POST['admin_name_add'])) {
        $file_admin_profile = $_FILES['admin_profile']['tmp_name'];
        if (file_exists($file_admin_profile)) {
            $acceptable = array("jpeg", "jpg", "png", "gif");
            $extId = pathinfo($_FILES['admin_profile']['name'], PATHINFO_EXTENSION);
            $dirPath = "../../img/profile/";
            if (in_array($extId, $acceptable) && (!empty($_FILES["admin_profile"]["type"]))) {
                $temp = explode(".", $_FILES["admin_profile"]["name"]);
                $newFileName = rand() . $user_id;
                $admin_profile = $newFileName . "_user." . end($temp);
                $destinationPath = $dirPath . $admin_profile;
                $d->resizeImage($file_admin_profile, $destinationPath, 1280, 720, $extId);
            } else {
                $_SESSION['msg1'] = "Invalid Photo";
                header("location:../buildingAdmin");
                exit();
            }
        } else {
            $admin_profile = "user.png";
        }
        $bytes = openssl_random_pseudo_bytes(4);
        $admin_password = bin2hex($bytes);

        $menu_id = implode(",", $_POST['menu_id']);
        $pagePrivilege = implode(",", $_POST['pagePrivilege']);
        $adminAppPrivilege = implode(",", $_POST['admin_app_right_id']);


        $complaint_category_id = implode(",", $_POST['complaint_category_id']);

        $m->set_data('society_id', $society_id);
        $m->set_data('admin_name_add', $admin_name_add);
        $m->set_data('admin_email', $d->encryptDecrypt("encrypt", $admin_email));
        $m->set_data('admin_mobile', $d->encryptDecrypt("encrypt", $admin_mobile));
        $m->set_data('admin_password', $admin_password);
        $m->set_data('admin_profile', $admin_profile);
        $m->set_data('complaint_category_id', $complaint_category_id);

        $a5 = array(
            'society_id' => $society_id,
            'role_name' => $role_name,
            'menu_id' => $menu_id,
            'pagePrivilege' => $pagePrivilege,
            'adminAppPrivilege' => $adminAppPrivilege,
            'admin_type' => 0,
        );

        $d->insert("role_master", $a5);
        $role_id = $d->getInsertId();


        $a3 = array(
            'role_id' => $role_id,
            'society_id' => $m->get_data('society_id'),
            'admin_name' => $m->get_data('admin_name_add'),
            'admin_email' => $m->get_data('admin_email'),
            'admin_mobile' => $m->get_data('admin_mobile'),
            'admin_password' => $admin_password,
            'admin_profile' => $m->get_data('admin_profile'),
            'created_date' => date("Y-m-d"),
            'complaint_category_id' => $m->get_data('complaint_category_id'),
        );
        if (isset($country_code) && isset($admin_mobile) && $country_code !== '' && $admin_mobile !== '') {
            $display_admin_mobile_plain = $country_code . $admin_mobile;
            $a3['display_admin_mobile'] = $d->encryptDecrypt("encrypt", $display_admin_mobile_plain);
        }



        $q = $d->insert("bms_admin_master", $a3);
        if ($q > 0) {


            $society_name = $society_name;

            $_SESSION['msg'] = "New Committee Member Added";
            $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "New Admin Added");

            $societyName = $society_name;
            $forgotLink = $base_url . "apAdmin/";


            $msg = "Dear $admin_name_add,\n We wish to inform you that your admin account with following details has been created.\n\nRole Name: $role_name\nSociety Name:$society_name\nMobile Number:$admin_mobile\nEmail Id:$admin_email\nPassword: $admin_password\n\nYou are requested to please click the following URL for Logging in the system: \n$forgotLink\n\nor download the " . $d->app_name() . "  Admin App by clicking following link :\n\n (If Android User) https://play.google.com/store/apps/details?id=com.fincasys.fincasysadmin\n\n(If IOS User) https://apps.apple.com/in/app/fincasys-admin/id1474040837\n\nThanks Team " . $d->app_name() . " ";
            // send mail to admin 
            $d->send_sms($admin_mobile, $msg);

            $to = $admin_email;
            $subject = "Account Created for $societyName - " . $d->app_name() . " ";

            include '../mail/newAdminMail.php';
            include '../mail.php';
            header("location:../buildingAdmins");
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("location:../buildingAdmin");
        }
    }


    if (isset($_POST['admin_name_edit'])) {


        $menu_id = implode(",", $_POST['menu_id']);
        $pagePrivilege = implode(",", $_POST['pagePrivilege']);
        $adminAppPrivilege = implode(",", $_POST['admin_app_right_id']);
        $complaint_category_id = implode(",", $_POST['complaint_category_id']);


        $m->set_data('admin_name_edit', $admin_name_edit);
        $m->set_data('admin_email', $d->encryptDecrypt("encrypt", $admin_email));
        $m->set_data('admin_mobile', $d->encryptDecrypt("encrypt", $admin_mobile));
        $m->set_data('complaint_category_id', $complaint_category_id);

        $a3 = array(
            'admin_name' => $m->get_data('admin_name_edit'),
            'admin_email' => $m->get_data('admin_email'),
            'admin_mobile' => $m->get_data('admin_mobile'),
            'created_date' => date("Y-m-d"),
            'complaint_category_id' => $m->get_data('complaint_category_id'),
        );

        $effective_country_code = '';
        if (isset($country_code) && $country_code !== '') {
            $effective_country_code = $country_code;
        } else {
            $adminRowForCode = $d->selectRow("country_code", "bms_admin_master", "admin_id='$admin_id_edit'");
            if (mysqli_num_rows($adminRowForCode) > 0) {
                $tmp = mysqli_fetch_array($adminRowForCode);
                $effective_country_code = $tmp['country_code'];
            }
        }
        if ($effective_country_code !== '' && isset($admin_mobile) && $admin_mobile !== '') {
            $display_admin_mobile_plain = $effective_country_code . $admin_mobile;
            $a3['display_admin_mobile'] = $d->encryptDecrypt("encrypt", $display_admin_mobile_plain);
        }


        $q = $d->update("bms_admin_master", $a3, "society_id='$society_id' AND admin_id='$admin_id_edit'");
        if ($q > 0) {

            $a5 = array(
                'role_name' => $role_name,
                'menu_id' => $menu_id,
                'pagePrivilege' => $pagePrivilege,
                'adminAppPrivilege' => $adminAppPrivilege,
                'admin_type' => 0,
            );

            $d->update("role_master", $a5, "role_id='$role_id_edit'");

            $_SESSION['msg'] = "Admin Data Updated";
            $d->insert_log("$society_id", "$bms_admin_id", "$created_by", "Admin Data Updated");


            header("location:../buildingAdmins");
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("location:../buildingAdmin");
        }
    }



    if (isset($_POST['admin_id_delete'])) {
        $admin_id_delete = $d->sanitizeActionIdAsInt($_POST['admin_id_delete']);
        $role_id_delete = $d->sanitizeActionIdAsInt($_POST['role_id_delete'] ?? ($role_id_delete ?? 0));

        $q = $d->delete("bms_admin_master", "admin_id='$admin_id_delete'");
        $q = $d->delete("role_master", "role_id='$role_id_delete'  AND admin_type=0");
        if ($q > 0) {
            $_SESSION['msg'] = "User Deleted";
            header("location:../buildingAdmins");
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("location:../buildingAdmins");
        }

        # code...
    }

    if (isset($_POST['updatePlan'])) {

        $cDataQuery = $d->selectRow("sub_domain,country_code,society_name,secretary_email,secretary_mobile,secretary_name,employee_tracking_limit,employee_registration_limit,crm_limit,crm_created", "society_master", "society_id='$societyId'");
        $comapnyData = mysqli_fetch_array($cDataQuery);
        $company_name = $comapnyData['society_name'];
        $company_admin = $comapnyData['secretary_name'];
        $company_admin_mobile = $comapnyData['secretary_mobile'];
        $company_admin_mobile_country_code = $comapnyData['country_code'];
        $company_admin_email = $comapnyData['secretary_email'];
        $society_base_url = $comapnyData['sub_domain'];
        $closure_city = $_POST['closure_city'];
        $updateLimits = (string)($update_limits ?? '');
        $oldTrackingLimit = (int)($comapnyData['employee_tracking_limit'] ?? 0);
        $oldRegistrationLimit = (int)($comapnyData['employee_registration_limit'] ?? 0);
        $oldCrmLimit = (int)($comapnyData['crm_limit'] ?? 0);
        $crmCreated = (int)($comapnyData['crm_created'] ?? 0);
        if (!in_array($updateLimits, array('yes', 'no'), true)) {
            $_SESSION['msg1'] = 'Please select whether to update the limit.';
            header("location:../companyPlanExpire");
            exit();
        }
        if ($updateLimits === 'yes') {
            if (!isset($employee_tracking_limit) || !preg_match('/^\d+$/', (string)$employee_tracking_limit) || !isset($employee_registration_limit) || !preg_match('/^\d+$/', (string)$employee_registration_limit)) {
                $_SESSION['msg1'] = 'Please enter valid limits.';
                header("location:../companyPlanExpire");
                exit();
            }
            if ($crmCreated === 1 && (!isset($crm_limit) || !preg_match('/^\d+$/', (string)$crm_limit))) {
                $_SESSION['msg1'] = 'Please enter a valid CRM limit.';
                header("location:../companyPlanExpire");
                exit();
            }
            if ($crmCreated === 1 && (int)$crm_limit > (int)$employee_registration_limit) {
                $_SESSION['msg1'] = 'Crm limit is greater than registration limit';
                header("location:../companyPlanExpire");
                exit();
            }
        }

        if ($plan_type == 4) {
            $qry = $d->select("transection_master", "society_id='$societyId'", "ORDER BY transection_id DESC LIMIT 3");
            $count = 0;
            while ($row = mysqli_fetch_assoc($qry)) {
                if ($row['is_renewal'] == 4) {
                    $count++;
                } else {
                    break;
                }
            }
            if ($count == 3) {
                $_SESSION['msg1'] = "Maximum 3 consecutive Temporary Extension plans are allowed. You cannot add another Temporary Extension plan at this time.";
                header("location:../companyPlanExpire");
                exit();
            }
        }

        $file_payment_attachment = $_FILES['payment_attachment']['tmp_name'];
        if (file_exists($file_payment_attachment)) {
            $acceptable = array("jpeg", "jpg", "png", "pdf");
            $extId = strtolower(pathinfo($_FILES['payment_attachment']['name'], PATHINFO_EXTENSION));
            $dirPath = "../../img/society_requests/";
            if (in_array($extId, $acceptable) && (!empty($_FILES["payment_attachment"]["type"]))) {
                $temp = explode(".", $_FILES["payment_attachment"]["name"]);
                $payment_attachment = $d->short_app_name() . '_' . round(microtime(true)) . '.' . end($temp);
                $destinationPath = $dirPath . $payment_attachment;
                if ($extId == "pdf") {
                    move_uploaded_file($file_payment_attachment, $destinationPath);
                } else {
                    $d->resizeImage($file_payment_attachment, $destinationPath, 1280, 720, $extId);
                }
            } else {
                $_SESSION['msg1'] = "Invalid File";
                header("location:../companyPlanExpire");
                exit();
            }
        } else {
            $payment_attachment = $payment_attachment_old ?? "";
        }
        $cQuery = $d->selectRow("manage_plan.plan_value, manage_plan.plan_name", "manage_plan", "plan_value = '$package_id'");
        if (mysqli_num_rows($cQuery) > 0) {
            $dataPlan = mysqli_fetch_array($cQuery);
            $maonthNameView = $dataPlan['plan_name'];
        } else {
            $maonthNameView = "Custome Plan";
        }

        $package_name = "" . $d->app_name() . " Renewal for $maonthNameView ($plan_expire_date)";

        $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20) . $societyId;
        $udf1 = $udf1 ?? "";
        $invoice_no = "0" . date('ymdi') . $udf1;
        $discount = $discount ?? "";
        $a122 = array(
            'society_id' => $societyId,
            'package_id' => $package_id,
            'package_name' => $package_name,
            'user_mobile' => $company_admin_mobile,
            'payment_mode' => $payment_mode,
            'payment_attachment' => $payment_attachment,
            'transection_amount' => $amountReceived,
            'transaction_amount_remark' => $transaction_amount_remark,
            'discount' => $discount,
            'transection_date' => date('Y-m-d H:i:s'),
            'payment_status' => "success",
            'payment_firstname' => $company_name,
            'payment_phone' => $company_admin_mobile,
            'payment_email' => $company_admin_email,
            'invoice_no' => $invoice_no,
            'received_by' => $received_by,
            'closure_city' => $closure_city,
            'payment_txnid' => $txnid,
            'is_renewal' => $plan_type,
            'plan_change_by_id' => $bms_admin_id,
            'plan_changed_by' => $created_by,
        );



        $now = date('Y-m-d');
        $post = array(
            'society_id' => $societyId,
            'changePlan' => 'changePlan',
            'plan_expire_date' => $plan_expire_date,
            'last_renew_date' => $now,
            'package_id' => $package_id,
        );
        $json = $d->callCompanyApiEnc($society_base_url, 'buildingChangePlanController.php', $post);


        if ($json === null) {
            $_SESSION['msg1'] = "Something went wrong with comapny url";
            header("location:../companyPlanExpire");
            exit();
        }

        if (count($json) > 0) {


            if ($json['status'] == 200) {
                $qq = $d->selectRow("payment_txnid", "transection_master", "payment_txnid='$txnid'");
                $oT = mysqli_fetch_array($qq);
                if ($oT > 0) {
                    $d->update("transection_master", $a122, "payment_txnid='$txnid'");
                } else {
                    $d->insert("transection_master", $a122);
                }
                $no_month = $no_month ?? "";
                $msg = "Hi $admin_name,\nYour Company Plan Upgraded to  $package_name for $no_month month,\nyour next renew date is : $plan_expire_date\n We have Received amount of $amountReceived INR for $package_name plan.";
                $notDes = "Received amount of $amountReceived INR";



                $m->set_data('societyId', $societyId);
                $m->set_data('package_id', $package_id);
                $m->set_data('trial_days', 0);
                $m->set_data('plan_expire_date', $plan_expire_date);
                $awww = array(
                    'society_id' => $m->get_data('societyId'),
                    'package_id' => $m->get_data('package_id'),
                    'trial_days' => $m->get_data('trial_days'),
                    'plan_expire_date' => $m->get_data('plan_expire_date'),
                    'last_renew_date' => date('Y-m-d'),
                );
                $q = $d->update("society_master", $awww, "society_id='$societyId'");
                if ($q > 0) {
                    $planLog = "$company_name Company Plan Changed ($plan_expire_date)";
                    if ($updateLimits === 'yes') {
                        $postLimit = array(
                            'society_id' => $societyId,
                            'changeTrackingLimit' => 'changeTrackingLimit',
                            'employee_tracking_limit' => $employee_tracking_limit,
                            'employee_registration_limit' => $employee_registration_limit,
                            'tracking_status' => '1',
                        );
                        $jsonLimit = $d->callCompanyApiEnc($society_base_url, 'buildingChangePlanController.php', $postLimit);
                        if (is_array($jsonLimit) && isset($jsonLimit['status']) && (int)$jsonLimit['status'] === 200) {
                            $m->set_data('employee_tracking_limit', $employee_tracking_limit);
                            $m->set_data('employee_registration_limit', $employee_registration_limit);
                            $limitUpdate = array(
                                'employee_tracking_limit' => $m->get_data('employee_tracking_limit'),
                                'employee_registration_limit' => $m->get_data('employee_registration_limit'),
                                'tracking_status' => '1',
                            );
                            $newCrmLimit = $oldCrmLimit;
                            if ($crmCreated === 1) {
                                $target_url = $society_base_url . "residentApiNew/societyAnalytics.php";
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $target_url);
                                curl_setopt($ch, CURLOPT_POST, 1);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, "setCrmLimit=setCrmLimit&society_id=$societyId&language_id=1&crmLimit=$crm_limit");
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array('key: ' . $keydb));
                                curl_exec($ch);
                                curl_close($ch);
                                $m->set_data('crm_limit', $crm_limit);
                                $limitUpdate['crm_limit'] = $m->get_data('crm_limit');
                                $newCrmLimit = (int)$crm_limit;
                            }
                            $d->update("society_master", $limitUpdate, "society_id='$societyId'");
                            $d->update("transection_master", array(
                                'old_tracking_limit' => $oldTrackingLimit,
                                'new_tracking_limit' => (int)$employee_tracking_limit,
                                'old_employee_limit' => $oldRegistrationLimit,
                                'new_employee_limit' => (int)$employee_registration_limit,
                                'old_crm_limit' => $oldCrmLimit,
                                'new_crm_limit' => $newCrmLimit,
                            ), "payment_txnid='$txnid'");
                            $planLog .= ", Tracking Limit: $oldTrackingLimit → $employee_tracking_limit, Employee Limit: $oldRegistrationLimit → $employee_registration_limit";
                            if ($crmCreated === 1) {
                                $planLog .= ", CRM Limit: $oldCrmLimit → $crm_limit";
                            }
                        } else {
                            $planLog .= ", Limit update failed";
                        }
                    }
                    $d->insert_log("$society_id", "$bms_admin_id", "$created_by", $planLog);
                    $_SESSION['msg'] = "Plan Updated";
                    header("location:../companyPlanExpire");
                } else {
                    $_SESSION['msg1'] = "Something Wrong";
                    header("location:../companyPlanExpire");
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
    if (isset($lost_found_master_id)) {
        $q = $d->delete("lost_found_master", "lost_found_master_id='$lost_found_master_id'");
        if ($q == TRUE) {
            $_SESSION['msg'] = "Deleted Successfully";
            header("location:../lostFound");
        } else {
            $_SESSION['msg1'] = "Something Wrong";
            header("location:../lostFound");
        } # code...
    }


    if (isset($_POST['changeSplash'])) {

        $file_splash_image = $_FILES['splash_image']['tmp_name'];
        if (file_exists($file_splash_image)) {
            $acceptable = array("png");
            $extId = pathinfo($_FILES['splash_image']['name'], PATHINFO_EXTENSION);
            $dirPath = "../../img/society_requests/";
            if (in_array($extId, $acceptable) && (!empty($_FILES["splash_image"]["type"]))) {
                $temp = explode(".", $_FILES["splash_image"]["name"]);
                $splash_image = 'Society_' . round(microtime(true)) . '.' . end($temp);
                $destinationPath = $dirPath . $splash_image;
                $d->resizeImage($file_splash_image, $destinationPath, 1280, 720, $extId);
            } else {
                $_SESSION['msg1'] = "Invalid Photo";
                header("location:../companyPlanExpire");
                exit();
            }
        } else {
            $splash_image = $splash_image_old;
        }

        $m->set_data('splash_colour', $splash_colour);
        $m->set_data('splash_image', $splash_image);

        $a1 = array(
            'splash_colour' => $m->get_data('splash_colour'),
            'splash_image' => $m->get_data('splash_image')
        );

        $q = $d->update("society_master", $a1, "society_id='$society_id'");

        if ($q > 0) {
            $_SESSION['msg'] = "Updated Successfully";
            $d->insert_log("0", "$bms_admin_id", "$created_by", "Company Splash Updated");
            header("location:../viewCompanies?countryId=$countryId&sId=$sId&cId=$cId");
        } else {
            $_SESSION['msg1'] = "Something went wrong";
            header("location:../viewCompanies?countryId=$countryId&sId=$sId&cId=$cId");
        }
    }

    if (isset($_POST['removeSplash'])) {
        $m->set_data('splash_colour', "");
        $m->set_data('splash_image', "");
        $a1 = array(
            'splash_colour' => $m->get_data('splash_colour'),
            'splash_image' => $m->get_data('splash_image')
        );
        $q = $d->update("society_master", $a1, "society_id='$society_id'");
        if ($q > 0) {
            $_SESSION['msg'] = "Updated Successfully";
            $d->insert_log("0", "$bms_admin_id", "$created_by", "Company Splash Removed");
            header("location:../viewCompanies?countryId=$countryId&sId=$sId&cId=$cId");
        } else {
            $_SESSION['msg1'] = "Something went wrong";
            header("location:../viewCompanies?countryId=$countryId&sId=$sId&cId=$cId");
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == "update_company_settings") {
        extract($_POST);

        switch ($distance_get_type) {
            case '0':
                $distance_get_type_name = "GPS";
                break;
            case '1':
                $distance_get_type_name = "Distancematrix";
                break;
            case '2':
                $distance_get_type_name = "Here Map";
                break;
            case '3':
                $distance_get_type_name = "Graphhopper";
                break;
            default:
                $distance_get_type_name = "Distancematrix";
                break;
        }

        switch ($distance_get_type_old) {
            case '0':
                $distance_get_type_name_old = "GPS";
                break;
            case '1':
                $distance_get_type_name_old = "Distancematrix";
                break;
            case '2':
                $distance_get_type_name_old = "Here Map";
                break;
            case '3':
                $distance_get_type_name_old = "Graphhopper";
                break;
            default:
                $distance_get_type_name_old = "Distancematrix";
                break;
        }

        $methods = [0 => 'Entire Day', 1 => 'Visit to Visit'];
        $visit_calculation_method_old_txt = $methods[$visit_calculation_method_old];
        $visit_calculation_method_txt = $methods[$visit_calculation_method];

        $m->set_data('society_name', $society_name);
        $m->set_data('company_full_name', $company_full_name);
        $m->set_data('society_address', $society_address);
        $m->set_data('secretary_email', $secretary_email);
        $m->set_data('secretary_mobile', $secretary_mobile);
        $m->set_data('secretary_name', $secretary_name);
        $m->set_data('country_code', test_input($country_code));
        $m->set_data('society_pincode', $society_pincode);
        $m->set_data('login_via', $login_via);
        $m->set_data('google_login', $google_login);
        $m->set_data('industry_type', $industry_type);
        $m->set_data('distance_get_type', $distance_get_type);
        $m->set_data('visit_calculation_method', $visit_calculation_method);
        $m->set_data('gst_number', strtoupper($gst_number));
        $m->set_data('search_society_code', strtoupper($search_society_code));
        $m->set_data('society_code', strtoupper($society_code));


        $a1 = array(
            'society_name' => $m->get_data('society_name'),
            'company_full_name' => $m->get_data('company_full_name'),
            'society_address' => $m->get_data('society_address'),
            'secretary_email' => $m->get_data('secretary_email'),
            'country_code' => $m->get_data('country_code'),
            'secretary_mobile' => $m->get_data('secretary_mobile'),
            'secretary_name' => $m->get_data('secretary_name'),
            'society_pincode' => $m->get_data('society_pincode'),
            'login_via' => $m->get_data('login_via'),
            'google_login' => $m->get_data('google_login'),
            'industry_type' => $m->get_data('industry_type'),
            'gst_number' => $m->get_data('gst_number'),
            'distance_get_type' => $m->get_data('distance_get_type'),
            'visit_calculation_method' => $m->get_data('visit_calculation_method'),
            'search_society_code' => $m->get_data('search_society_code'),
            'society_code' => $m->get_data('society_code')
        );
        $q = $d->update("society_master", $a1, "society_id='$society_id'");
        if ($q > 0) {

            if ($distance_get_type != $distance_get_type_old || $visit_calculation_method != $visit_calculation_method_old) {
                // update on company db
                $trackChange = '';
                if ($distance_get_type != $distance_get_type_old) {
                    $trackChange .= ", Travel Mode Changed From $distance_get_type_name_old to $distance_get_type_name";
                }
                if ($visit_calculation_method != $visit_calculation_method_old) {
                    $trackChange .= ", Travel Mode Changed From $visit_calculation_method_old_txt to $visit_calculation_method_txt";
                }
                $trackChange .= " from Master by $created_by";
                $qc = $d->selectRow("sub_domain,society_id,society_name,secretary_email,secretary_mobile,society_address,socieaty_logo", "society_master", "society_id='$society_id'");
                $companyData = mysqli_fetch_array($qc);
                extract($companyData);

                $target_url = $companyData['sub_domain'] . "residentApiNew/societyAnalytics.php";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $target_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "updateTravelMode=updateTravelMode&society_id=$society_id&language_id=1&show_visit_distance_or_normal_distance=$distance_get_type&visit_calculation_method=$visit_calculation_method&trackChange=$trackChange");

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'key: ' . $keydb
                ));
                $result = curl_exec($ch);
                curl_close($ch);
                $json = json_decode($result, true);
                $result2 = $json["message"];
            }
            $_SESSION['msg'] = "Updated Successfully";
            $d->insert_log("0", "$bms_admin_id", "$created_by", "$society_name Company Settings Updated $trackChange");
            header("location:../viewCompanies?countryId=$countryId&sId=$sId&cId=$cId");
        } else {
            $_SESSION['msg1'] = "Something went wrong";
            header("location:../viewCompanies?countryId=$countryId&sId=$sId&cId=$cId");
        }
    }

    if (isset($recentGetSoceietyData)) {
        $today = date("Y-m-d");
        $post_log_master = $d->select("society_resent_analytics_master", " city_id = '$city_id'  AND status=200 AND society_id  ='$society_id_post' AND update_date='$today'");
        $success_array = array();
        while ($post_log_master_data = mysqli_fetch_array($post_log_master)) {
            array_push($success_array, $post_log_master_data['society_id']);
        }
        $ids = join("','", $success_array);
        $society_master_qry = $d->select("society_master", " society_id  ='$society_id_post' AND society_id NOT IN ('$ids') ");
        $resentSocietiesToProcess = array();
        while ($society_master_row = mysqli_fetch_array($society_master_qry)) {
            $resentSocietiesToProcess[] = $society_master_row;
        }
        $resentSocietyIds = array();
        foreach ($resentSocietiesToProcess as $_sm) {
            $resentSocietyIds[] = (int)$_sm['society_id'];
        }
        $resentSocietyIdsIn = !empty($resentSocietyIds) ? implode(',', $resentSocietyIds) : '0';
        $existingResentAnalyticsMap = array();
        if (!empty($resentSocietyIds)) {
            $resentExistingQ = $d->select("society_resent_analytics_master", "society_id IN ($resentSocietyIdsIn)");
            while ($resentExistingRow = mysqli_fetch_assoc($resentExistingQ)) {
                $existingResentAnalyticsMap[(int)$resentExistingRow['society_id']] = true;
            }
        }
        foreach ($resentSocietiesToProcess as $society_master_data) {
            $society_id = $society_master_data['society_id'];
            $target_url = $society_master_data['sub_domain'] . "residentApiNew/societyAnalytics.php";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $target_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "recentActivities=recentActivities&society_id=$society_id&language_id=1&days=$days");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'key: ' . $keydb
            ));
            $result = curl_exec($ch);
            curl_close($ch);
            $json = json_decode($result, true);
            $result2 = $json["message"];
            $attendance_count = $json["attendance_count"];
            $leave_count = $json["leave_count"];
            $tracking_count = $json["tracking_count"];
            $salary_count = $json["salary_count"];
            $assets_count = $json["assets_count"];
            $expenses_count = $json["expenses_count"];
            $work_report_count = $json["work_report_count"];
            $task_count = $json["task_count"];
            $order_count = $json["order_count"];
            $visit_count = $json["visit_count"];
            $circular_count = $json["circular_count"];
            $document_count = $json["document_count"];
            $discussion_count = $json["discussion_count"];
            $status = $json["status"];
            $message = $json["message"];

            $m->set_data('society_id', $society_id);
            $m->set_data('city_id', $city_id);
            $m->set_data('days', $days);
            $m->set_data('attendance_count', $attendance_count);
            $m->set_data('leave_count', $leave_count);
            $m->set_data('tracking_count', $tracking_count);
            $m->set_data('salary_count', $salary_count);
            $m->set_data('assets_count', $assets_count);
            $m->set_data('expenses_count', $expenses_count);
            $m->set_data('work_report_count', $work_report_count);
            $m->set_data('task_count', $task_count);
            $m->set_data('order_count', $order_count);
            $m->set_data('visit_count', $visit_count);
            $m->set_data('circular_count', $circular_count);
            $m->set_data('document_count', $document_count);
            $m->set_data('discussion_count', $discussion_count);
            $m->set_data('status', $status);
            $m->set_data('message', $message);
            $m->set_data('last_updated_date', date('Y-m-d H:i:s'));
            $m->set_data('update_date', $today);

            $a1 = array(
                'society_id' => $m->get_data('society_id'),
                'city_id' => $m->get_data('city_id'),
                'days' => $m->get_data('days'),
                'attendance_count' => $m->get_data('attendance_count'),
                'leave_count' => $m->get_data('leave_count'),
                'tracking_count' => $m->get_data('tracking_count'),
                'salary_count' => $m->get_data('salary_count'),
                'assets_count' => $m->get_data('assets_count'),
                'expenses_count' => $m->get_data('expenses_count'),
                'work_report_count' => $m->get_data('work_report_count'),
                'task_count' => $m->get_data('task_count'),
                'order_count' => $m->get_data('order_count'),
                'visit_count' => $m->get_data('visit_count'),
                'circular_count' => $m->get_data('circular_count'),
                'document_count' => $m->get_data('document_count'),
                'discussion_count' => $m->get_data('discussion_count'),
                'status' => $m->get_data('status'),
                'message' => $m->get_data('message'),
                'last_updated_date' => $m->get_data('last_updated_date'),
                'update_date' => $m->get_data('update_date'),
            );
            if (isset($existingResentAnalyticsMap[(int)$society_id])) {
                $q = $d->update("society_resent_analytics_master", $a1, "society_id='$society_id'");
            } else {
                $q = $d->insert("society_resent_analytics_master", $a1);
                $existingResentAnalyticsMap[(int)$society_id] = true;
            }
            if ($result2 == "") {
                echo " - No Response:201";
                exit;
            } else {
                echo $json["message"] . ':' . $json["status"];
                exit;
            }
        }
    }

    if (isset($_POST["addInstitute"])) {

        $qc = $d->selectRow("sub_domain,society_id,society_name,secretary_email,secretary_mobile,society_address,socieaty_logo,plan_expire_date", "society_master", "society_id='$companyId'");
        $companyData = mysqli_fetch_array($qc);
        extract($companyData);

        $curl = curl_init();
        $hasMultiBranch = false;
        $id = false;
        $affiliationNo = null;
        $block = null;
        $district = null;
        $pspCode = null;
        $udiseCode = null;
        $collectorCode = null;
        $logo = null;

        $society_name = $society_name;
        // $society_name = $society_name . " Testing".date("his");

        $data = array(
            "Name" => $society_name,
            "Email" => $secretary_email,
            "ContactNumber" => $secretary_mobile,
            "AddressLine1" => $society_address,
            "HasMultiBranch" => $hasMultiBranch,
            "PlanExpireOn" => $plan_expire_date,
            "Id" => $id,
            "Logo" => $logo,
            "AffiliationNo" => $affiliationNo,
            "Block" => $block,
            "District" => $district,
            "PspCode" => $pspCode,
            "UdiseCode" => $udiseCode,
            "CollectorCode" => $collectorCode,
            "Password" => $institutePassword,
            "AcademicYear" => array(
                "Title" => $academicYearTitle,
                "Code" => $academicYearCode,
                "StartDate" => $academicYearStartDate,
                "EndDate" => $academicYearEndDate,
            )
        );
        $privateKey = "95ace055042353479a5a753cd5a02e45eae6ab79d4af4c58656f13e73ab0cda4bd00461720a6f26201a16b861c3ca5db16059e5ef5889111ea16e688d3b7179b";
        $hmacSignature = hash_hmac('sha256', $society_name, $privateKey);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://dev2-api.eduwity.com/public/manageInstitute',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'x-eduwity-signature: ' . $hmacSignature,
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        $responseArray = json_decode($response, true);
        if (isset($responseArray['userName'])) {
            $userName = $responseArray['userName'];
            $m->set_data('institute_username', $userName);
            $m->set_data('institute_password', $institutePassword);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://dev2-api.eduwity.com/login',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                "Username": "' . $userName . '",
                "Password": "' . $institutePassword . '"
                }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $loginResponseArray = json_decode($response, true);
            $institute_token = $loginResponseArray['data']['Token'];
            $m->set_data('institute_token', $institute_token);
            $data = array(
                'institute_username' => $m->get_data('institute_username'),
                'institute_password' => $m->get_data('institute_password'),
                'institute_token' => $m->get_data('institute_token'),
                'allow_institute' => 1,
            );
            $d->update("society_master", $data, "society_id='$society_id'");


            $target_url = $companyData['sub_domain'] . "residentApiNew/societyAnalytics.php";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $target_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "addInstitute=addInstitute&society_id=$society_id&language_id=1&institute_username=$userName&institute_password=$institutePassword&institute_token=$institute_token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'key: ' . $keydb
            ));
            $result = curl_exec($ch);
            curl_close($ch);
            $json = json_decode($result, true);
            $result2 = $json["message"];


            $_SESSION['msg'] = "Institute Added Successfully." . $result2;
            header("Location: ../viewCompanies");
        } else {
            $_SESSION['msg1'] = "Failed to add institute.";
            header("Location: ../viewCompanies");
        }
    }
    if (isset($syncSocietyData)) {
        $company_status = "Company Sync Failed";
        $company_code = "500";
        $crm_status = "CRM Response Error";
        $crm_code = "500";
        $societyUrlData = $d->selectRow(
            "domain_master.domain_name,society_crm_master.*,society_master.sub_domain,society_master.crm_plan_expiring_date",
            "society_master 
        LEFT JOIN society_crm_master ON society_crm_master.society_crm_id = society_master.society_crm_id 
        LEFT JOIN domain_master ON domain_master.domain_id = society_master.domain_id",
            "society_master.society_id='$society_id_post'"
        );
        if (mysqli_num_rows($societyUrlData) > 0) {
            $societyData = mysqli_fetch_array($societyUrlData);
            $sub_domain = $societyData['sub_domain'];
            $society_crm_id = $societyData['society_crm_id'];
            $society_crm_url = $societyData['url'];
            $society_crm_token = $societyData['token'];
            $crm_plan_expiring_date = $societyData['crm_plan_expiring_date'];
            $frontendUrl = $sub_domain . "crm";
            $postDataArray = array(
                'planExpiringDate' => $crm_plan_expiring_date,
                'frontendUrl' => $frontendUrl,
            );

            $crm_url = $society_crm_url . 'api/v1/tenant-subscription/' . $society_id_post;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $crm_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => json_encode($postDataArray),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $society_crm_token
                ),
            ));

            $response = curl_exec($curl);

            if ($response === false) {
                $crm_status = "CRM cURL Error";
                $crm_code = "500";
            } else {

                $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                if ($code == 200) {
                    $crm_status = "CRM Response Success";
                    $crm_code = "200";
                } else {
                    $crm_status = "CRM Response Error";
                    $crm_code = $code;
                }
            }

            curl_close($curl);

            // Company API
            $crulData = array(
                'updateSocietyCrm' => 'updateSocietyCrm',
                'society_id' => "$society_id_post",
                'crm_url' => "$society_crm_url",
                'crm_token' => "$society_crm_token"
            );

            $curl1 = curl_init();

            curl_setopt_array($curl1, array(
                CURLOPT_URL => $sub_domain . 'residentApiNew/societyAnalytics.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $crulData,
                CURLOPT_HTTPHEADER => array(
                    'key: ' . $keydb
                ),
            ));

            $response1 = curl_exec($curl1);

            if ($response1 === false) {

                $company_status = "Company cURL Error";
                $company_code = "500";
            } else {

                $company_http_code = curl_getinfo($curl1, CURLINFO_HTTP_CODE);

                if ($company_http_code == 200) {

                    $json = json_decode($response1, true);

                    if (!empty($json)) {
                        $company_status = "Company Sync Success";
                        $company_code = "200";
                    } else {
                        $company_status = "Company Sync Failed";
                        $company_code = "500";
                    }
                } else {

                    $company_status = "Company HTTP Error";
                    $company_code = $company_http_code;
                }
            }

            curl_close($curl1);

            echo $company_status . "||" . $company_code . "::" . $crm_status . "||" . $crm_code;
        }
    }

    if (isset($syncSalaryHeadType)) {
        $society_master_qry = $d->select("society_master", " society_id = '$society_id_post'");
        while ($society_master_data = mysqli_fetch_array($society_master_qry)) {
            $society_id = $society_master_data['society_id'];
            $country_id = $society_master_data['country_id'];
            $target_url = $society_master_data['sub_domain'] . "residentApiNew/societyAnalytics.php";
        }
        $salaryheadQry = $d->selectRow("salary_earning_deduction_type_master.*", "salary_earning_deduction_type_master", "earn_deduct_is_delete='0' AND country_id='$country_id' AND country_id!=''");
        $salaryheadDataArray = [];
        if (mysqli_num_rows($salaryheadQry)) {
            while ($salaryheadData = mysqli_fetch_assoc($salaryheadQry)) {
                $salaryheadDataArray[] = $salaryheadData;
            }
        }
        $jsonData = json_encode($salaryheadDataArray);
        $crulArr = array(
            "syncSalaryHead" => "syncSalaryHead",
            "society_id" => "$society_id",
            "salaryheadDataArray" => $jsonData,
        );
        if (mysqli_num_rows($society_master_qry) > 0) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $target_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $crulArr,
                CURLOPT_HTTPHEADER => array(
                    'key:' . $keydb
                ),
            ));
            $response = curl_exec($curl);
            $code1 = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($response === false) {
                echo "cURL Error: " . $error;
                exit;
            }
            $json = json_decode($response, true);
        }
        if ($code1 == "200" && isset($json["message"], $json["status"])) {
            $societyData = array(
                "salaryhead_synced" => "1",
            );
            $societyQry = $d->update("society_master", $societyData, "society_id='$society_id'");
            echo $json["message"] . ':' . $json["status"];
            exit;
        } else {
            echo " - No Response:201";
            exit;
        }
    }
    if (isset($syncLeaveType)) {
        $society_master_qry = $d->select("society_master", " society_id = '$society_id_post'");
        while ($society_master_data = mysqli_fetch_array($society_master_qry)) {
            $society_id = $society_master_data['society_id'];
            $country_id = $society_master_data['country_id'];
            $target_url = $society_master_data['sub_domain'] . "residentApiNew/societyAnalytics.php";
        }
        $leaveQry = $d->selectRow("leave_types_master.*", "leave_types_master", "country_id='$country_id' AND country_id!=''");
        $leaveDataArray = [];
        if (mysqli_num_rows($leaveQry)) {
            while ($leaveData = mysqli_fetch_assoc($leaveQry)) {
                $leaveDataArray[] = $leaveData;
            }
        }
        $jsonData = json_encode($leaveDataArray);
        $crulArr = array(
            "syncLeave" => "syncLeave",
            "society_id" => "$society_id",
            "leaveDataArray" => $jsonData,
        );
        if (mysqli_num_rows($society_master_qry) > 0) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $target_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $crulArr,
                CURLOPT_HTTPHEADER => array(
                    'key:' . $keydb
                ),
            ));
            $response = curl_exec($curl);
            $code1 = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($response === false) {
                echo "cURL Error: " . $error;
                exit;
            }
            $json = json_decode($response, true);
        }
        if ($code1 == "200" && isset($json["message"], $json["status"])) {
            $societyData = array(
                "leave_synced" => "1",
            );
            $societyQry = $d->update("society_master", $societyData, "society_id='$society_id'");
            echo $json["message"] . ':' . $json["status"];
            exit;
        } else {
            echo " - No Response:201";
            exit;
        }
    }
    if (isset($syncHolidayType)) {
        $society_master_qry = $d->select("society_master", " society_id = '$society_id_post'");
        while ($society_master_data = mysqli_fetch_array($society_master_qry)) {
            $society_id = $society_master_data['society_id'];
            $country_id = $society_master_data['country_id'];
            $target_url = $society_master_data['sub_domain'] . "residentApiNew/societyAnalytics.php";
        }
        $holidayQry = $d->selectRow("holidays_master.*", "holidays_master", "holiday_status='0' AND country_id='$country_id' AND country_id!=''");
        $holidayDataArray = [];
        if (mysqli_num_rows($holidayQry)) {
            while ($holidayData = mysqli_fetch_assoc($holidayQry)) {
                $holidayDataArray[] = $holidayData;
            }
        }
        $jsonData = json_encode($holidayDataArray);
        $crulArr = array(
            "syncHolidays" => "syncHolidays",
            "society_id" => "$society_id",
            "holidayDataArray" => $jsonData,
        );
        if (mysqli_num_rows($society_master_qry) > 0) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $target_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $crulArr,
                CURLOPT_HTTPHEADER => array(
                    'key:' . $keydb
                ),
            ));
            $response = curl_exec($curl);
            $code1 = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($response === false) {
                echo "cURL Error: " . $error;
                exit;
            }
            $json = json_decode($response, true);
        }
        if ($code1 == "200" && isset($json["message"], $json["status"])) {
            $societyData = array(
                "holiday_synced" => "1",
            );
            $societyQry = $d->update("society_master", $societyData, "society_id='$society_id'");
            echo $json["message"] . ':' . $json["status"];
            exit;
        } else {
            echo " - No Response:201";
            exit;
        }
    }
    if (isset($syncExpenseType)) {
        $society_master_qry = $d->select("society_master", " society_id = '$society_id_post'");
        while ($society_master_data = mysqli_fetch_array($society_master_qry)) {
            $society_id = $society_master_data['society_id'];
            $country_id = $society_master_data['country_id'];
            $target_url = $society_master_data['sub_domain'] . "residentApiNew/societyAnalytics.php";
        }
        $expenseQry = $d->selectRow("expense_master.*", "expense_master", "expense_status='0'");
        $expenseDataArray = [];
        if (mysqli_num_rows($expenseQry)) {
            while ($expenseData = mysqli_fetch_assoc($expenseQry)) {
                $expenseData['expense_icon_full'] = $m->base_url() . "img/emp_icon/" . $expenseData['expense_icon'];
                $expenseDataArray[] = $expenseData;
            }
        }
        $jsonData = json_encode($expenseDataArray);
        $crulArr = array(
            "syncExpenses" => "syncExpenses",
            "society_id" => "$society_id",
            "expenseDataArray" => $jsonData,
        );
        if (mysqli_num_rows($society_master_qry) > 0) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $target_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $crulArr,
                CURLOPT_HTTPHEADER => array(
                    'key:' . $keydb
                ),
            ));
            $response = curl_exec($curl);

            $code1 = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($response === false) {
                echo "cURL Error: " . $error;
                exit;
            }

            $json = json_decode($response, true);
        }
        if ($code1 == "200" && isset($json["message"], $json["status"])) {
            $societyData = array(
                "expense_synced" => "1",
            );
            $societyQry = $d->update("society_master", $societyData, "society_id='$society_id'");
            echo $json["message"] . ':' . $json["status"];
            exit;
        } else {
            echo " - No Response:201";
            exit;
        }
    }
    if (isset($_POST['checkCrmLimit'])) {
        $crm_limit = intval($_POST['crm_limit']);
        $society_id = intval($_POST['society_id']);

        $result = $d->select("society_master", "society_id = '$society_id'");
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $allowed_limit = intval($row['employee_registration_limit']);

            if ($crm_limit <= $allowed_limit) {
                echo json_encode(true);
                exit;
            } else {
                echo json_encode("CRM Limit cannot exceed $allowed_limit.");
                exit;
            }
        } else {
            echo json_encode("Please select proper company.");
            exit;
        }
        exit;
    }
    if (isset($_POST['refundModule']) && $_POST['refundModule'] == 'refundModule') {

        $m->set_data('refund_amount', test_input($refund_amount));
        $m->set_data('refund_description', test_input($refund_description));
        $m->set_data('refund_person_id', $refund_person_id);

        $a = array(
            'refund_amount' => $m->get_data('refund_amount'),
            'refund_description' => $m->get_data('refund_description'),
            'refund_person' => $m->get_data('refund_person_id'),
            'refund_status' => 1,
            'refund_date' => date('Y-m-d H:i:s')
        );

        $society_id = $d->sanitizeActionIdAsInt($society_id ?? ($_POST['society_id'] ?? 0));
        $q = $d->update("society_master", $a, "society_id=$society_id");


        if ($q > 0) {
            $d->insert_log("$society_id", $_SESSION['bms_admin_id'], $created_by, "New Refund successfully added.");
            $_SESSION['msg'] = "Refund successfully added.";
            header("Location: ../companyPlanExpire");
        } else {
            $_SESSION['msg1'] = "Something went wrong while adding the refund.";
            header("Location: ../companyPlanExpire");
        }
    }
    // 26-05-2025
    if (isset($_POST['sendWhiteLabelData'])) {
        $society_id = $d->sanitizeActionIdAsInt($_POST['society_ids'] ?? 0);
        $selectedDate = $_POST['selectedDate'];
        $month_year = $date = date('m-Y', strtotime($_POST['selectedDate']));
        $fetch_date = $date = date('Y-m-d', strtotime($_POST['selectedDate']));
        $cdate = date('Y-m-d H:i:s');
        $society_white_label_qry = $d->select("society_master_white_label", " society_id  ='$society_id'");
        while ($society_white_label_data = mysqli_fetch_array($society_white_label_qry)) {
            $white_label_society_name = $society_white_label_data['society_name'];
            $master_company_id = $society_white_label_data['master_company_id'];
            $project_type = $society_white_label_data['project_type'];
            $url = $society_white_label_data['sub_domain'];
        }
        if ($project_type == 0) {
            $target_url = $url . "residentApiNew/societyAnalytics.php";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $target_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "buildingDetails=buildingDetails&society_id=$master_company_id&language_id=1");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'key: ' . $keydb
            ));
            $result = curl_exec($ch);
            curl_close($ch);
            $json = json_decode($result, true);
            $result2 = $json["message"];
            $result3 = $json["status"];
            if (isset($json) && $result3 == 200) {
                $society_name = $json["society_name"];
                $society_address = $json["society_address"];
                $socieaty_logo = $json["socieaty_logo"];
                $society_latitude = $json["society_latitude"];
                $society_longitude = $json["society_longitude"];
                $city_id = $json["city_id"];
                $secretary_email = $json["secretary_email"];
                $secretary_mobile = $json["secretary_mobile"];
                $crm_limit = $json["crm_limit"] ?? '0';
                $totalTrackingOn = $json["totalTrackingOn"];
                $totalGoogleVisit = $json["totalGoogleVisit"];
                $thisMonthGoogleVisit = $json["thisMonthGoogleVisit"];
                $preMonthGoogleVisit = $json["preMonthGoogleVisit"];
                $no_of_units = $json["no_of_units"];
                $no_of_blocks = $json["no_of_blocks"];
                $total_users = $json["total_users"];
                $total_notice_board = $json["total_notice_board"];
                $total_events = $json["total_events"];
                $total_sos_triger = $json["total_sos_triger"];
                $total_polls = $json["total_polls"];
                $total_document = $json["total_document"];
                $total_lost_found = $json["total_lost_found"];
                $total_penalty = $json["total_penalty"];
                $total_duscussion_foram = $json["total_duscussion_foram"];
                $total_timeline_post = $json["total_timeline"];
                $total_login_android = $json["total_login_android"];
                $total_login_ios = $json["total_login_ios"];
                $total_logged_in_users = $json["total_logged_in_users"] ?? $json["total_users"];
                $total_chat_msg = $json["total_chat_message"];
                $total_maintence_amount = $json["total_maintenance_amount"];
                $total_received_maintenance_amount = $json["total_received_maintenance_amount"];
                $total_received_maintenance_amount_online = $json["total_received_maintenance_amount_online"];
                $total_attendace = $json["total_attendace"];
                $total_attendace_weekly = $json["total_attendace_weekly"];
                $total_attendace_this_month = $json["total_attendace_this_month"];
                $total_attendace_prev_month = $json["total_attendace_prev_month"];
                $total_work_from_home = $json["total_work_from_home"];
                $total_salary_slip = $json["total_salary_slip"];
                $total_monthly_salary_slip = $json["total_monthly_salary_slip"];
                $total_leaves = $json["total_leaves"];
                $total_work_report = $json["total_work_report"];
                $total_work_report_weekly = $json["total_work_report_weekly"];
                $total_work_report_this_month = $json["total_work_report_this_month"];
                $total_work_report_prev_month = $json["total_work_report_prev_month"];
                $total_dar_work_report = $json["total_dar_work_report"];
                $total_dar_work_report_weekly = $json["total_dar_work_report_weekly"];
                $total_dar_work_report_this_month = $json["total_dar_work_report_this_month"];
                $total_dar_work_report_prev_month = $json["total_dar_work_report_prev_month"];
                $total_assets = $json["total_assets"];
                $img_storage = $json["img_storage"];
                $db_size = $json["db_size"] ?? '';
                $admin_size = $json["admin_size"] ?? '';
                $total_loan = $json["total_loan"] ?? '';
                $advance_salary = $json["advance_salary"] ?? '';
                $newCurrentMonthAttendanceUniqueUser = $json["newCurrentMonthAttendanceUniqueUser"];
                $newPrevMonthAttendanceUniqueUser = $json["newPrevMonthAttendanceUniqueUser"];
                $newPrevToPrevMonthAttendanceUniqueUser = $json["newPrevToPrevMonthAttendanceUniqueUser"];

                $newCurrentMonthPayrollUniqueUser = $json["newCurrentMonthPayrollUniqueUser"];
                $newPrevMonthPayrollUniqueUser = $json["newPrevMonthPayrollUniqueUser"];
                $newPrevToPrevMonthPayrollUniqueUser = $json["newPrevToPrevMonthPayrollUniqueUser"];

                $totalWorkReportMonth = $json["total_work_report"];
                $second_last_month_work_report_count = $json["second_last_month_work_report_count"];

                $currentMonthCircular = $json["currentMonthCircular"];
                $prevMonthCircular = $json["prevMonthCircular"];
                $prevToPrevMonthCircular = $json["prevToPrevMonthCircular"];

                $currentMonthExpense = $json["currentMonthExpense"];
                $prevMonthExpense = $json["prevMonthExpense"];
                $prevToPrevMonthExpense = $json["prevToPrevMonthExpense"];

                $trackingUsersCurrentMonth = $json["trackingUsersCurrentMonth"];
                $trackingUsersPrevMonth = $json["trackingUsersPrevMonth"];
                $trackingUsersPrevToPrevMonth = $json["trackingUsersPrevToPrevMonth"];

                $totalGoogleVisit = $json["totalGoogleVisit"];
                $thisMonthGoogleVisit = $json["thisMonthGoogleVisit"];
                $preMonthGoogleVisit = $json["preMonthGoogleVisit"];
                $prevToPrevMonthGoogleVisit = $json["prevToPrevMonthGoogleVisit"];

                $currentMonthTask = $json["currentMonthTask"];
                $prevMonthTask = $json["prevMonthTask"];
                $prevToPrevMonthTask = $json["prevToPrevMonthTask"];

                $currentMonthVisitor = $json["currentMonthVisitor"];
                $prevMonthVisitor = $json["prevMonthVisitor"];
                $prevToPrevMonthVisitor = $json["prevToPrevMonthVisitor"];

                $currentMonthWFH = $json["currentMonthWFH"];
                $prevMonthWFH = $json["prevMonthWFH"];
                $prevToPrevMonthWFH = $json["prevToPrevMonthWFH"];

                $currentOpenOpenings = $json["currentOpenOpenings"];

                $currentMonthTimeline = $json["currentMonthTimeline"];
                $prevMonthTimeline = $json["prevMonthTimeline"];
                $prevToPrevTimeline = $json["prevToPrevTimeline"];

                $currentMonthEvent = $json["currentMonthEvent"];
                $prevMonthEvent = $json["prevMonthEvent"];
                $prevToPrevMonthEvent = $json["prevToPrevMonthEvent"];

                $currentMonthGallery = $json["currentMonthGallery"];
                $prevMonthGallery = $json["prevMonthGallery"];
                $prevToPrevMonthGallery = $json["prevToPrevMonthGallery"];

                $currentMonthPenalty = $json["currentMonthPenalty"];
                $prevMonthPenalty = $json["prevMonthPenalty"];
                $prevToPrevMonthPenalty = $json["prevToPrevMonthPenalty"];

                $currentVendorRegistered = $json["currentVendorRegistered"];

                $currentMonthSalesOrder = $json["currentMonthSalesOrder"];
                $prevMonthSalesOrder = $json["prevMonthSalesOrder"];
                $prevToPrevSalesOrder = $json["prevToPrevSalesOrder"];
                $adminViewAccess = $json["adminViewAccess"];
                $total_visitors = $json["total_visitors"];

                $status = $json["status"];

                $m->set_data('society_id', $society_id);
                $m->set_data('totalGoogleVisit', $totalGoogleVisit);
                $m->set_data('thisMonthGoogleVisit', $thisMonthGoogleVisit);
                $m->set_data('preMonthGoogleVisit', $preMonthGoogleVisit);
                $m->set_data('no_of_units', $no_of_units);
                $m->set_data('no_of_blocks', $no_of_blocks);
                $m->set_data('total_users', $total_users);
                $m->set_data('total_notice_board', $total_notice_board);
                $m->set_data('total_events', $total_events);
                $m->set_data('total_sos_triger', $total_sos_triger);
                $m->set_data('total_polls', $total_polls);
                $m->set_data('total_document', $total_document);
                $m->set_data('total_facilities', $total_facilities);
                $m->set_data('total_lost_found', $total_lost_found);
                $m->set_data('total_penalty', $total_penalty);
                $m->set_data('total_duscussion_foram', $total_duscussion_foram);
                $m->set_data('total_login_user', $total_logged_in_users);
                $m->set_data('total_login_android', $total_login_android);
                $m->set_data('total_login_ios', $total_login_ios);
                $m->set_data('total_chat_msg', $total_chat_msg);
                $m->set_data('total_timeline_post', $total_timeline_post);
                $m->set_data('total_maintence_amount', $total_maintence_amount);
                $m->set_data('total_received_maintenance_amount', $total_received_maintenance_amount);
                $m->set_data('total_received_maintenance_amount_online', $total_received_maintenance_amount_online);
                $m->set_data('city_id', $city_id);
                $m->set_data('update_date', $today);
                $m->set_data('last_updated_date', date("Y-m-d H:i:s"));
                $m->set_data('status', $status);
                $m->set_data('last_updated_date', date('Y-m-d H:i:s'));
                $m->set_data('total_attendace', $total_attendace);
                $m->set_data('total_attendace_weekly', $total_attendace_weekly);
                $m->set_data('total_attendace_this_month', $total_attendace_this_month);
                $m->set_data('total_attendace_prev_month', $total_attendace_prev_month);
                $m->set_data('total_work_from_home', $total_work_from_home);
                $m->set_data('total_salary_slip', $total_salary_slip);
                $m->set_data('total_monthly_salary_slip', $total_monthly_salary_slip);
                $m->set_data('total_leaves', $total_leaves);
                $m->set_data('total_work_report', $total_work_report);
                $m->set_data('total_work_report_weekly', $total_work_report_weekly);
                $m->set_data('total_work_report_this_month', $total_work_report_this_month);
                $m->set_data('total_work_report_prev_month', $total_work_report_prev_month);
                $m->set_data('total_dar_work_report', $total_dar_work_report);
                $m->set_data('total_dar_work_report_weekly', $total_dar_work_report_weekly);
                $m->set_data('total_dar_work_report_this_month', $total_dar_work_report_this_month);
                $m->set_data('total_dar_work_report_prev_month', $total_dar_work_report_prev_month);
                $m->set_data('total_assets', $total_assets);
                $m->set_data('img_storage', $img_storage);
                $m->set_data('db_size', $db_size);
                $m->set_data('admin_size', $admin_size);
                $m->set_data('total_loan', $total_loan);
                $m->set_data('advance_salary', $advance_salary);
                $m->set_data('active_tracking_users', $totalTrackingOn);
                // $m->set_data('total_users', $total_users);
                $m->set_data('current_month_attendance_count', $newCurrentMonthAttendanceUniqueUser);
                $m->set_data('last_month_attendance_count', $newPrevMonthAttendanceUniqueUser);
                $m->set_data('second_last_month_attendance_count', $newPrevToPrevMonthAttendanceUniqueUser);

                $m->set_data('current_month_payroll_count', $newCurrentMonthPayrollUniqueUser);
                $m->set_data('last_month_payroll_count', $newPrevMonthPayrollUniqueUser);
                $m->set_data('second_last_month_payroll_count', $newPrevToPrevMonthPayrollUniqueUser);

                $m->set_data('current_month_work_report_count', $totalWorkReportMonth);
                $m->set_data('last_month_work_report_count', $totalWorkReportPrvMonth);
                $m->set_data('second_last_month_work_report_count', $second_last_month_work_report_count);

                $m->set_data('current_month_circular_count', $currentMonthCircular);
                $m->set_data('last_month_circular_count', $prevMonthCircular);
                $m->set_data('second_last_month_circular_count', $prevToPrevMonthCircular);

                // $m->set_data('total_assets', $total_assets);

                $m->set_data('current_month_expense_count', $currentMonthExpense);
                $m->set_data('last_month_expense_count', $prevMonthExpense);
                $m->set_data('second_last_month_expense_count', $prevToPrevMonthExpense);

                $m->set_data('admin_view_access', $adminViewAccess);

                $m->set_data('current_month_tracking_user_count', $trackingUsersCurrentMonth);
                $m->set_data('last_month_tracking_user_count', $trackingUsersPrevMonth);
                $m->set_data('second_last_month_tracking_user_count', $trackingUsersPrevToPrevMonth);

                $m->set_data('total_google_visit_count', $totalGoogleVisit);
                $m->set_data('current_month_google_visit_count', $thisMonthGoogleVisit);
                $m->set_data('last_month_google_visit_count', $preMonthGoogleVisit);
                $m->set_data('second_last_month_google_visit_count', $prevToPrevMonthGoogleVisit);

                // $m->set_data('total_document', $total_document);

                $m->set_data('current_month_task_count', $currentMonthTask);
                $m->set_data('last_month_task_count', $prevMonthTask);
                $m->set_data('second_last_month_task_count', $prevToPrevMonthTask);

                $m->set_data('current_month_visitor_count', $currentMonthVisitor);
                $m->set_data('last_month_visitor_count', $prevMonthVisitor);
                $m->set_data('second_last_month_visitor_count', $prevToPrevMonthVisitor);

                $m->set_data('current_month_wfh_count', $currentMonthWFH);
                $m->set_data('last_month_wfh_count', $prevMonthWFH);
                $m->set_data('second_last_month_wfh_count', $prevToPrevMonthWFH);

                $m->set_data('current_opening_count', $currentOpenOpenings);

                $m->set_data('current_month_timeline_count', $currentMonthTimeline);
                $m->set_data('last_month_timeline_count', $prevMonthTimeline);
                $m->set_data('second_last_month_timeline_count', $prevToPrevTimeline);

                $m->set_data('current_month_event_count', $currentMonthEvent);
                $m->set_data('last_month_event_count', $prevMonthEvent);
                $m->set_data('second_last_month_event_count', $prevToPrevMonthEvent);

                $m->set_data('current_month_gallery_count', $currentMonthGallery);
                $m->set_data('last_month_gallery_count', $prevMonthGallery);
                $m->set_data('second_last_month_gallery_count', $prevToPrevMonthGallery);

                $m->set_data('current_month_penalty_count', $currentMonthPenalty);
                $m->set_data('last_month_penalty_count', $prevMonthPenalty);
                $m->set_data('second_last_month_penalty_count', $prevToPrevMonthPenalty);

                $m->set_data('total_registered_vendors', $currentVendorRegistered);

                $m->set_data('current_month_sales_order_count', $currentMonthSalesOrder);
                $m->set_data('last_month_sales_order_count', $prevMonthSalesOrder);
                $m->set_data('second_last_month_sales_order_count', $prevToPrevSalesOrder);
                $m->set_data('total_visitors', $total_visitors);
                $m->set_data('fetch_date', test_input($fetch_date));
                $m->set_data('fetch_month_year', test_input($month_year));
                $a1 = array(
                    'society_id' => $m->get_data('society_id'),
                    'master_company_id' => "$master_company_id",
                    'city_id' => $m->get_data('city_id'),
                    'active_tracking_users' => $m->get_data('active_tracking_users'),
                    'totalGoogleVisit' => $m->get_data('totalGoogleVisit'),
                    'thisMonthGoogleVisit' => $m->get_data('thisMonthGoogleVisit'),
                    'preMonthGoogleVisit' => $m->get_data('preMonthGoogleVisit'),
                    'no_of_units' => $m->get_data('no_of_units'),
                    'no_of_blocks' => $m->get_data('no_of_blocks'),
                    'total_maintenance' => $m->get_data('total_maintenance'),
                    'total_users' => $m->get_data('total_users'),
                    'total_notice_board' => $m->get_data('total_notice_board'),
                    'total_events' => $m->get_data('total_events'),
                    'total_sos_triger' => $m->get_data('total_sos_triger'),
                    'total_polls' => $m->get_data('total_polls'),
                    'total_document' => $m->get_data('total_document'),
                    'total_facilities' => $m->get_data('total_facilities'),
                    'total_lost_found' => $m->get_data('total_lost_found'),
                    'total_penalty' => $m->get_data('total_penalty'),
                    'total_duscussion_foram' => $m->get_data('total_duscussion_foram'),
                    'total_login_user' => $m->get_data('total_login_user'),
                    'total_login_android' => $m->get_data('total_login_android'),
                    'total_login_ios' => $m->get_data('total_login_ios'),
                    'total_chat_msg' => $m->get_data('total_chat_msg'),
                    'total_timeline_post' => $m->get_data('total_timeline_post'),
                    'update_date' => $m->get_data('update_date'),
                    'status' => $m->get_data('status'),
                    'last_updated_date' => $m->get_data('last_updated_date'),
                    'total_attendace' => $m->get_data('total_attendace'),
                    'total_attendace_weekly' => $m->get_data('total_attendace_weekly'),
                    'total_attendace_this_month' => $m->get_data('total_attendace_this_month'),
                    'total_attendace_prev_month' => $m->get_data('total_attendace_prev_month'),
                    'total_work_from_home' => $m->get_data('total_work_from_home'),
                    'total_salary_slip' => $m->get_data('total_salary_slip'),
                    'total_monthly_salary_slip' => $m->get_data('total_monthly_salary_slip'),
                    'total_leaves' => $m->get_data('total_leaves'),
                    'total_work_report' => $m->get_data('total_work_report'),
                    'total_work_report_weekly' => $m->get_data('total_work_report_weekly'),
                    'total_work_report_this_month' => $m->get_data('total_work_report_this_month'),
                    'total_work_report_prev_month' => $m->get_data('total_work_report_prev_month'),
                    'total_dar_work_report' => $m->get_data('total_dar_work_report'),
                    'total_dar_work_report_weekly' => $m->get_data('total_dar_work_report_weekly'),
                    'total_dar_work_report_this_month' => $m->get_data('total_dar_work_report_this_month'),
                    'total_dar_work_report_prev_month' => $m->get_data('total_dar_work_report_prev_month'),
                    'total_assets' => $m->get_data('total_assets'),
                    'img_storage' => $m->get_data('img_storage'),
                    'db_size' => $m->get_data('db_size'),
                    'admin_size' => $m->get_data('admin_size'),
                    'total_loan' => $m->get_data('total_loan'),
                    'advance_salary' => $m->get_data('advance_salary'),
                    'current_month_attendance_count' => $m->get_data('current_month_attendance_count'),
                    'last_month_attendance_count' => $m->get_data('last_month_attendance_count'),
                    'second_last_month_attendance_count' => $m->get_data('second_last_month_attendance_count'),

                    'current_month_payroll_count' => $m->get_data('current_month_payroll_count'),
                    'last_month_payroll_count' => $m->get_data('last_month_payroll_count'),
                    'second_last_month_payroll_count' => $m->get_data('second_last_month_payroll_count'),

                    'current_month_work_report_count' => $m->get_data('total_work_report_this_month'),
                    'last_month_work_report_count' => $m->get_data('total_work_report_prev_month'),
                    'second_last_month_work_report_count' => $m->get_data('second_last_month_work_report_count'),

                    'current_month_circular_count' => $m->get_data('current_month_circular_count'),
                    'last_month_circular_count' => $m->get_data('last_month_circular_count'),
                    'second_last_month_circular_count' => $m->get_data('second_last_month_circular_count'),
                    'current_month_expense_count' => $m->get_data('current_month_expense_count'),
                    'last_month_expense_count' => $m->get_data('last_month_expense_count'),
                    'second_last_month_expense_count' => $m->get_data('second_last_month_expense_count'),

                    'admin_view_access' => $m->get_data('admin_view_access'),

                    'current_month_tracking_user_count' => $m->get_data('current_month_tracking_user_count'),
                    'last_month_tracking_user_count' => $m->get_data('last_month_tracking_user_count'),
                    'second_last_month_tracking_user_count' => $m->get_data('second_last_month_tracking_user_count'),

                    'total_google_visit_count' => $m->get_data('total_google_visit_count'),
                    'current_month_google_visit_count' => $m->get_data('current_month_google_visit_count'),
                    'last_month_google_visit_count' => $m->get_data('last_month_google_visit_count'),
                    'second_last_month_google_visit_count' => $m->get_data('second_last_month_google_visit_count'),

                    'current_month_task_count' => $m->get_data('current_month_task_count'),
                    'last_month_task_count' => $m->get_data('last_month_task_count'),
                    'second_last_month_task_count' => $m->get_data('second_last_month_task_count'),

                    'current_month_visitor_count' => $m->get_data('current_month_visitor_count'),
                    'last_month_visitor_count' => $m->get_data('last_month_visitor_count'),
                    'second_last_month_visitor_count' => $m->get_data('second_last_month_visitor_count'),

                    'current_month_wfh_count' => $m->get_data('current_month_wfh_count'),
                    'last_month_wfh_count' => $m->get_data('last_month_wfh_count'),
                    'second_last_month_wfh_count' => $m->get_data('second_last_month_wfh_count'),

                    'current_opening_count' => $m->get_data('current_opening_count'),

                    'current_month_timeline_count' => $m->get_data('current_month_timeline_count'),
                    'last_month_timeline_count' => $m->get_data('last_month_timeline_count'),
                    'second_last_month_timeline_count' => $m->get_data('second_last_month_timeline_count'),

                    'current_month_event_count' => $m->get_data('current_month_event_count'),
                    'last_month_event_count' => $m->get_data('last_month_event_count'),
                    'second_last_month_event_count' => $m->get_data('second_last_month_event_count'),

                    'current_month_gallery_count' => $m->get_data('current_month_gallery_count'),
                    'last_month_gallery_count' => $m->get_data('last_month_gallery_count'),
                    'second_last_month_gallery_count' => $m->get_data('second_last_month_gallery_count'),

                    'current_month_penalty_count' => $m->get_data('current_month_penalty_count'),
                    'last_month_penalty_count' => $m->get_data('last_month_penalty_count'),
                    'second_last_month_penalty_count' => $m->get_data('second_last_month_penalty_count'),

                    'total_registered_vendors' => $m->get_data('total_registered_vendors'),

                    'current_month_sales_order_count' => $m->get_data('current_month_sales_order_count'),
                    'last_month_sales_order_count' => $m->get_data('last_month_sales_order_count'),
                    'second_last_month_sales_order_count' => $m->get_data('second_last_month_sales_order_count'),
                    'total_visitors' => $m->get_data('total_visitors'),
                    'fetch_date' => $m->get_data('fetch_date'),
                    'fetch_month_year' => $m->get_data('fetch_month_year'),
                    'created_at' => $cdate,
                );

                $qc = $d->select("society_whitelable_analytics_master", "society_id='$society_id' AND fetch_month_year = '$month_year'");
                if (mysqli_num_rows($qc) > 0) {
                    $q = $d->update("society_whitelable_analytics_master", $a1, "society_id='$society_id' AND fetch_month_year = '$month_year'");
                } else {
                    $q = $d->insert("society_whitelable_analytics_master", $a1);
                }
            } else {
                echo json_encode(['status' => 201]);
                exit();
            }
            echo json_encode(['status' => 200]);
            exit();
        } else if ($project_type == 1) {
            $target_url = $url . "chplApi/login_counts.php";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $target_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'key: CHPLAPIKEY',
                'Cookie: PHPSESSID=9jcvd72gldn9l7a53nsd5pc6hp'
            ));
            $result = curl_exec($ch);
            curl_close($ch);
            $result_data = json_decode($result, true);
            $result3 = $result_data["status"];
            if (isset($result_data) && $result3 == 200) {
                $societies = $result_data['society_data'];
                $whiteLabelExistingMap = array();
                $wlNames = array();
                foreach ($societies as $society) {
                    if (isset($society['society_name']) && $society['society_name'] !== '') {
                        $wlNames[] = "'" . addslashes($society['society_name']) . "'";
                    }
                }
                $wlNames = array_unique($wlNames);
                if (!empty($wlNames)) {
                    $wlNamesIn = implode(',', $wlNames);
                    $wlExistingQ = $d->select("white_label_analytics_master", "fetch_month_year = '$month_year' AND society_id = '$society_id' AND society_name IN ($wlNamesIn)");
                    while ($wlExistingRow = mysqli_fetch_assoc($wlExistingQ)) {
                        $whiteLabelExistingMap[$wlExistingRow['society_name']] = true;
                    }
                }
                foreach ($societies as $society) {
                    $m->set_data('society_id', test_input($society_id));
                    $m->set_data('master_company_id', test_input($master_company_id));
                    $m->set_data('society_name', $society['society_name']);
                    $m->set_data('city_name', $society['city_name']);
                    $m->set_data('total_user', test_input($society['total_user']));
                    $m->set_data('login_user', test_input($society['login_user']));
                    $m->set_data('android_user', test_input($society['android_user']));
                    $m->set_data('ios_user', test_input($society['ios_user']));
                    $m->set_data('plan_expire_date', test_input($society['plan_expire_date']));
                    $m->set_data('project_type', test_input($project_type));
                    $m->set_data('fetch_date', test_input($fetch_date));
                    $m->set_data('fetch_month_year', test_input($month_year));
                    $a = array(
                        'society_id' => $m->get_data('society_id'),
                        'master_company_id' => $m->get_data('master_company_id'),
                        'society_name' => $m->get_data('society_name'),
                        'city_name' => $m->get_data('city_name'),
                        'total_user' => $m->get_data('total_user'),
                        'login_user' => $m->get_data('login_user'),
                        'android_user' => $m->get_data('android_user'),
                        'ios_user' => $m->get_data('ios_user'),
                        'plan_expire_date' => $m->get_data('plan_expire_date'),
                        'project_type' => $m->get_data('project_type'),
                        'fetch_date' => $m->get_data('fetch_date'),
                        'fetch_month_year' => $m->get_data('fetch_month_year'),
                        'created_at' => $cdate,
                    );
                    if (isset($whiteLabelExistingMap[$society['society_name']])) {
                        $d->update("white_label_analytics_master", $a, "society_name = '$society[society_name]' AND fetch_month_year = '$month_year' AND society_id = '$society_id'");
                    } else {
                        $d->insert("white_label_analytics_master", $a);
                        $whiteLabelExistingMap[$society['society_name']] = true;
                    }
                }
            } else {
                echo json_encode(['status' => 201]);
                exit();
            }
            echo json_encode(['status' => 200]);
            exit();
        } else if ($project_type == 2) {
            $target_url = $url . "chplApi/login_counts.php";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $target_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ['getUserCount' => ' getUserCount']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'key: CHPLAPIKEY',
                'Cookie: PHPSESSID=m4coagsq3hcrnvfm6sv87qi104'
            ));
            $result = curl_exec($ch);
            curl_close($ch);
            $result_data = json_decode($result, true);
            $result3 = $result_data["status"];
            if (isset($result_data) && $result3 == 200) {
                $societies = $result_data['society_data'];
                $whiteLabelExistingMap = array();
                $wlNames = array();
                foreach ($societies as $society) {
                    if (isset($society['society_name']) && $society['society_name'] !== '') {
                        $wlNames[] = "'" . addslashes($society['society_name']) . "'";
                    }
                }
                $wlNames = array_unique($wlNames);
                if (!empty($wlNames)) {
                    $wlNamesIn = implode(',', $wlNames);
                    $wlExistingQ = $d->select("white_label_analytics_master", "fetch_month_year = '$month_year' AND society_id = '$society_id' AND society_name IN ($wlNamesIn)");
                    while ($wlExistingRow = mysqli_fetch_assoc($wlExistingQ)) {
                        $whiteLabelExistingMap[$wlExistingRow['society_name']] = true;
                    }
                }
                foreach ($societies as $society) {
                    $vendor_count = $society['vendor_count'];
                    $m->set_data('society_id', test_input($society_id));
                    $m->set_data('master_company_id', test_input($master_company_id));
                    $m->set_data('society_name', $society['society_name']);
                    $m->set_data('city_name', $society['city_name']);
                    $m->set_data('total_user', test_input($society['total_user']));
                    $m->set_data('login_user', test_input($society['login_user']));
                    $m->set_data('android_user', test_input($society['android_user']));
                    $m->set_data('ios_user', test_input($society['ios_user']));
                    $m->set_data('plan_expire_date', test_input($society['plan_expire_date']));
                    $m->set_data('project_type', test_input($project_type));
                    $m->set_data('fetch_date', test_input($fetch_date));
                    $m->set_data('fetch_month_year', test_input($month_year));
                    $m->set_data('vendor_count', test_input($vendor_count));

                    $a = array(
                        'society_id' => $m->get_data('society_id'),
                        'master_company_id' => $m->get_data('master_company_id'),
                        'society_name' => $m->get_data('society_name'),
                        'city_name' => $m->get_data('city_name'),
                        'total_user' => $m->get_data('total_user'),
                        'login_user' => $m->get_data('login_user'),
                        'android_user' => $m->get_data('android_user'),
                        'ios_user' => $m->get_data('ios_user'),
                        'plan_expire_date' => $m->get_data('plan_expire_date'),
                        'project_type' => $m->get_data('project_type'),
                        'fetch_date' => $m->get_data('fetch_date'),
                        'fetch_month_year' => $m->get_data('fetch_month_year'),
                        'vendor_count' => $m->get_data('vendor_count'),
                        'created_at' => $cdate,
                    );
                    if (isset($whiteLabelExistingMap[$society['society_name']])) {
                        $d->update("white_label_analytics_master", $a, "society_name = '$society[society_name]' AND fetch_month_year = '$month_year' AND society_id = '$society_id'");
                    } else {
                        $d->insert("white_label_analytics_master", $a);
                        $whiteLabelExistingMap[$society['society_name']] = true;
                    }
                }
            } else {
                echo json_encode(['status' => 201]);
                exit();
            }

            echo json_encode(['status' => 200]);
        } else {
            echo "Invalid project type";
            exit();
        }
    }
    if (isset($_POST['updateCRMPlan'])) {
        $crm_package_id = $_POST['crm_package_id'];
        $crm_plan_expiring_date = $_POST['crm_plan_expiring_date'];
        $crm_trial_days = $_POST['crm_trial_days'];
        $curl = curl_init();

        $mycoBackendUrl = $subDomain . 'crmApi';

        $qc = $d->selectRow("society_crm_master.*,society_master.sub_domain", "society_master,society_crm_master", "society_master.society_crm_id=society_crm_master.society_crm_id AND society_id='$companyId'");
        $cData = mysqli_fetch_array($qc);
        $sub_domain = $cData['sub_domain'];
        $frontendUrl = $sub_domain . 'crm';
        $society_crm_id = $cData['society_crm_id'];
        $society_crm_url = $cData['url'];
        $society_crm_token = $cData['token'];
        $crm_token = $cData['token'];
        $crm_url = $cData['url'] . 'api/v1/tenant-subscription/' . $companyId;
        $postDataArray =  array(
            'planExpiringDate' => $crm_plan_expiring_date,
            'frontendUrl' => $frontendUrl,
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $crm_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode($postDataArray),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $crm_token
            ),
        ));

        $response = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($code == '200') {
            $crmupdate = array(
                'crm_package_id' => $crm_package_id,
                'crm_trial_days' => $crm_trial_days,
                'crm_plan_expiring_date' => $crm_plan_expiring_date
            );

            $d->update("society_master", $crmupdate, "society_id='$companyId'");
            $_SESSION['msg'] = "CRM Plan Updated  Successfully";
            header("location:../crmPlanExpire");
            exit();
        } else {
            $logFile =  '../../img/crm_error_log.txt';
            $logMessage = "\n============================\n";
            $logMessage .= "Date: " . date('Y-m-d H:i:s') . "\n";
            $logMessage .= "Company ID: " . $companyId . "\n";
            $logMessage .= "HTTP Code: " . $code . "\n";
            $logMessage .= "CRM URL: " . $crm_url . "\n";
            $logMessage .= "Request Data: " . json_encode($postDataArray) . "\n";
            $logMessage .= "Response: " . $response . "\n";
            $logMessage .= "Curl Error: " . $curlError . "\n";
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            $_SESSION['msg1'] = "Failed to update CRM Plan";
            header("location:../crmPlanExpire");
            exit();
        }
    }

    if (isset($syncSlabData)) {
        $tax_slab_year = $year;
        $society_master_qry = $d->select("society_master", " society_id = '$society_id_post'");
        while ($society_master_data = mysqli_fetch_array($society_master_qry)) {
            $society_id = $society_master_data['society_id'];
            $target_url = $society_master_data['sub_domain'] . "residentApiNew/societyAnalytics.php";
        }
        $taxBenifitCategoryDataArray = array();
        $taxBenifitCategoryQry = $d->selectRow("tax_benefit_category.*", "tax_benefit_category", "tax_benefit_year='$tax_slab_year'");
        $tax_benefit_category_id_array = array();
        if (mysqli_num_rows($taxBenifitCategoryQry) > 0) {
            while ($taxBenifitCategoryData = mysqli_fetch_assoc($taxBenifitCategoryQry)) {
                array_push($tax_benefit_category_id_array, $taxBenifitCategoryData['tax_benefit_category_id']);
                $taxBenifitCategoryDataArray[] = $taxBenifitCategoryData;
            }
        }

        $ids = join("','", $tax_benefit_category_id_array);
        if ($ids != "") {
            $taxBenifitSubCategoryDataArray = array();
            $taxBenifitSubCategoryQry = $d->select("tax_benefit_sub_category", "tax_benefit_category_id IN ('$ids') ", "ORDER BY tax_benefit_sub_category_id ASC");
            if (mysqli_num_rows($taxBenifitSubCategoryQry) > 0) {
                while ($taxBenifitSubCategoryData = mysqli_fetch_assoc($taxBenifitSubCategoryQry)) {
                    $taxBenifitSubCategoryDataArray[] = $taxBenifitSubCategoryData;
                }
            }
        }
        $taxLimitDataArray = [];
        $taxLimitQry = $d->selectRow("tax_limit_master.*", "tax_limit_master", "tax_year='$tax_slab_year'");
        if (mysqli_num_rows($taxLimitQry) > 0) {
            while ($taxLimitData = mysqli_fetch_assoc($taxLimitQry)) {
                $taxLimitDataArray[] = $taxLimitData;
            }
        }
        $slabQry = $d->selectRow("tax_slab_master.*", "tax_slab_master", "tax_slab_year='$tax_slab_year'");
        $slabDataArray = [];
        if (mysqli_num_rows($slabQry)) {
            while ($slabData = mysqli_fetch_assoc($slabQry)) {
                $slabDataArray[] = $slabData;
            }
        }
        $jsonData = json_encode($slabDataArray);
        $jsonData1 = json_encode($taxLimitDataArray);
        $jsonData2 = json_encode($taxBenifitSubCategoryDataArray);
        $jsonData3 = json_encode($taxBenifitCategoryDataArray);
        $crulArr = array(
            "incomeTaxSlab" => "incomeTaxSlab",
            "syc_tax_slab_year" => "$tax_slab_year",
            "society_id" => "$society_id",
            "slabDataArray" => $jsonData,
            "taxLimitDataArray" => $jsonData1,
            "taxBenifitSubCategoryDataArray" => $jsonData2,
            "taxBenifitCategoryDataArray" => $jsonData3,
        );
        if (mysqli_num_rows($society_master_qry) > 0) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $target_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $crulArr,
                CURLOPT_HTTPHEADER => array(
                    'key:' . $keydb
                ),
            ));
            $response = curl_exec($curl);
            $code1 = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($response === false) {
                echo "cURL Error: " . $error;
                exit;
            }
            $json = json_decode($response, true);
        }
        if ($code1 == "200" && isset($json["message"], $json["status"])) {
            $societyData = array(
                "slab_synced" => "1",
            );
            $societyQry = $d->update("society_master", $societyData, "society_id='$society_id'");
            echo $json["message"] . ':' . $json["status"];
            exit;
        } else {
            echo " - No Response:201";
            exit;
        }
    }

    if (isset($deleteCRM)) {
        $logFile = "../../img/delete_crm_log.txt";
        function writeDeleteCRMLog($message, $logFile)
        {
            $date = date("Y-m-d H:i:s");
            file_put_contents($logFile, "[$date] " . $message . PHP_EOL, FILE_APPEND);
        }
        writeDeleteCRMLog(
            "========== DELETE CRM START ==========" . PHP_EOL .
                "Company ID: " . $companyId,
            $logFile
        );
        $sq = $d->selectRow("society_master.society_crm_id,society_master.sub_domain,society_master.api_key,society_master.crm_created,domain_master.domain_name,server_master.server_ip,domain_master.domain_name,society_crm_master.*", "society_master LEFT JOIN domain_master ON domain_master.domain_id = society_master.domain_id  JOIN server_master ON server_master.server_id = domain_master.server_id LEFT JOIN society_crm_master ON society_crm_master.society_crm_id = society_master.society_crm_id", "society_id='$companyId'");
        $socData = mysqli_fetch_array($sq);
        $server_url = $socData['sub_domain'];
        $crm_package_id = $socData['crm_package_id'];
        $crm_plan_expiring_date = $socData['crm_plan_expiring_date'];
        $crm_trial_days = $socData['crm_trial_days'];
        $mycoBackendUrl = $server_url . 'crmApi';
        $frontendUrl = $server_url . 'crm';
        $society_crm_id = $socData['society_crm_id'];
        $crm_created = $socData['crm_created'];
        $api_key = $socData['api_key'];
        $server_ip = $socData['server_ip'];
        $crm_server_ip = $server_ip;
        $new_domain_name = str_replace(["https://", "http://"], "", $server_url);
        $new_domain_name = rtrim($new_domain_name, '/');
        $company_url = $new_domain_name;
        $redirectUrl = "../viewCompanies?countryId=$country_id&sId=$state_id&cId=$city_id";

        // check crm
        $society_crm_url = $socData['url'];
        $crm_token = $socData['token'];
        $crm_url = $society_crm_url . 'api/v1/tenant-subscription/' . $companyId;
        writeDeleteCRMLog(
            "Fetched society data" . PHP_EOL .
                "CRM Created: $crm_created" . PHP_EOL .
                "Society CRM ID: $society_crm_id" . PHP_EOL .
                "Server URL: $server_url" . PHP_EOL .
                "Server IP: $server_ip" . PHP_EOL .
                "Company URL: $new_domain_name",
            $logFile
        );
        if ($crm_created != 1 && (empty($society_crm_id) || $society_crm_id == 0)) {
            writeDeleteCRMLog("CRM does not exist", $logFile);
            $_SESSION['msg1'] = "CRM does not exist for this company.";
            header("location:$redirectUrl");
            exit();
        }
        writeDeleteCRMLog("Starting CRM delete API call", $logFile);
        $postDataArray =  array(
            'planExpiringDate' => $crm_plan_expiring_date,
            'frontendUrl' => $frontendUrl,
        );
        $checkCrmExistCurl = curl_init();
        curl_setopt_array($checkCrmExistCurl, array(
            CURLOPT_URL => $crm_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode($postDataArray),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $crm_token
            ),
        ));
        writeDeleteCRMLog(
            "Starting CRM delete API call" . PHP_EOL .
                "CRM URL: " . $crm_url . PHP_EOL .
                "Request Payload: " . json_encode($postDataArray) . PHP_EOL .
                "Request Method: PUT",
            $logFile
        );

        $response = curl_exec($checkCrmExistCurl);

        $checkCrmExistCode = curl_getinfo(
            $checkCrmExistCurl,
            CURLINFO_HTTP_CODE
        );

        $crmCurlError = curl_error($checkCrmExistCurl);

        writeDeleteCRMLog(
            "CRM API Response Code: " . $checkCrmExistCode . PHP_EOL .
                "CRM API Response: " . $response . PHP_EOL .
                "CRM CURL Error: " . $crmCurlError,
            $logFile
        );

        curl_close($checkCrmExistCurl);

        // ONLY CONTINUE IF CRM API RETURNS 404
        if ($checkCrmExistCode != 404) {
            writeDeleteCRMLog(
                "CRM API did not return 404. Process stopped.",
                $logFile
            );

            $_SESSION['msg1'] =
                "CRM subscription still exists. Delete aborted.";

            writeDeleteCRMLog(
                "========== DELETE CRM END ==========",
                $logFile
            );

            header("location:$redirectUrl");
            exit();
        }

        // check crm
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $server_url . "residentApiNew/societyAnalytics.php",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                "deleteCRM" => "deleteCRM",
                "company_id" => $companyId
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'key: ' . $api_key
            ],
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        writeDeleteCRMLog(
            "HTTP Code: $httpCode" . PHP_EOL .
                "API Response: " . $response . PHP_EOL,
            $logFile
        );
        if (curl_errno($ch)) {
            $curlError = curl_error($ch);
            writeDeleteCRMLog("CURL Error: $curlError", $logFile);
            $_SESSION['msg1'] = "Connection failed: " . $curlError;
            curl_close($ch);
            header("location:$redirectUrl");
            exit();
        }
        curl_close($ch);
        if ($httpCode == 200) {
            $response_data = json_decode($response, true);
            writeDeleteCRMLog("Decoded Response: " . print_r($response_data, true), $logFile);

            if (isset($response_data['status']) && $response_data['status'] == "200") {
                writeDeleteCRMLog("CRM delete API successful", $logFile);
                $cmd = "sudo /var/ps/deletecrm.sh "
                    . escapeshellarg($crm_server_ip) . " "
                    . escapeshellarg($company_url) . " "
                    . escapeshellarg('f3N2n2IelsKZhHB')
                    . " 2>&1";
                writeDeleteCRMLog(
                    "Executing Shell Command:" . PHP_EOL . $cmd,
                    $logFile
                );
                // $shellResponse = shell_exec($cmd);
                $output = [];
                $return_var = 0;
                exec($cmd, $output, $return_var);
                $shellResponse = implode(PHP_EOL, $output);
                writeDeleteCRMLog(
                    "Shell Response: " . $shellResponse . PHP_EOL .
                        "Return Code: " . $return_var,
                    $logFile
                );

                if ($return_var == 0) {
                    $crm_update = array(
                        "crm_limit" => 0,
                        "crm_created" => 0,
                        "crm_created_by" => 0,
                        "crm_created_date" => "",
                        "crm_package_id" => "",
                        "crm_trial_days" => "",
                        "crm_plan_expiring_date" => "",
                        "society_crm_id" => "",
                    );
                    $d->update("society_master", $crm_update, "society_id='$companyId'");
                    writeDeleteCRMLog("CRM Updated: " . print_r($crm_update, true), $logFile);
                    $_SESSION['msg'] = "CRM Deleted Successfully";
                } else {
                    writeDeleteCRMLog(
                        "Shell Script Execution Failed",
                        $logFile
                    );

                    $_SESSION['msg1'] = "Shell script execution failed";
                }
            } else {
                writeDeleteCRMLog("CRM delete API failed", $logFile);
                $_SESSION['msg1'] = $response_data['message'] ?? "CRM deletion failed.";
            }
        } else {
            writeDeleteCRMLog("Invalid HTTP response code",  $logFile);
            $_SESSION['msg1'] = "Failed to delete CRM. HTTP Code: $httpCode";
        }
        writeDeleteCRMLog("========== DELETE CRM END ==========", $logFile);
        header("location:$redirectUrl");
        exit();
    }
} else {
    header('location:../login');
}
