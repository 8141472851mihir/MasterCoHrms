<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Key");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}
include_once 'lib.php';
$compress = resolve_api_compress();
include '../apAdmin/lib/jwt.php';
// $_POST =  json_decode($d->manage_decryption("1", file_get_contents("php://input")), true);
// $_POST =  $d->manage_encryption("1", $_POST);
$is_encrypted = 0;
if (json_last_error() !== JSON_ERROR_NONE) {
    echo $d->manage_encryption($is_encrypted, [
        "status" => "201",
        "message" => 'Invalid Request'
    ], $compress);
    exit();
}
try {
    if (isset($_POST) && !empty($_POST)) {
        $response = array();
        extract($_POST);
        if ($key == $keydb) {
            if (isset($_POST['masterLogin']) && $_POST['masterLogin'] == 'masterLogin') {
                if (isset($_POST["mobile"]) && $_POST["mobile"] != '' && isset($_POST["country_code"]) && $_POST["country_code"] != '' && (isset($_POST["otp_web"]) && $_POST["otp_web"] != '' || isset($_POST["inputPass"]) && $_POST["inputPass"] != '')) {
                    $mobile = mysqli_real_escape_string($con, $mobile);
                    if (isset($inputPass)) {
                        $inputPass = mysqli_real_escape_string($con, $inputPass);
                    }
                    if (isset($otp_web)) {
                        $otp_web = (int)$otp_web;
                    } else {
                        $otp_web = 0;
                    }
                    if ($mobile == '9737564998') {
                        $mobile = $d->encryptDecrypt("encrypt", $mobile);
                        $q = $d->select("bms_admin_master", "admin_mobile='$mobile' AND role_id='1' AND country_code='$country_code'");
                    } else {
                        $mobile = $d->encryptDecrypt("encrypt", $mobile);
                        $q = $d->select("bms_admin_master,role_master", "bms_admin_master.role_id=role_master.role_id  AND bms_admin_master.admin_mobile='$mobile'  AND bms_admin_master.country_code='$country_code' OR bms_admin_master.role_id=role_master.role_id AND bms_admin_master.admin_email='$mobile' ");
                    }
                    if (mysqli_num_rows($q) > 0) {
                        $data = mysqli_fetch_array($q);
                        $login_success = false;
                        if (strlen($otp_web) == 6) {
                            $apeendOTPQueryMainAdmin = " AND otp_web='$otp_web'";
                            $apeendOTPQuery = "  AND bms_admin_master.otp_web='$otp_web'";
                            if ($otp_web != $data['otp_web']) {
                                $response["message"] = "Invalid OTP";
                                $response["status"] = "201";
                                echo $d->manage_encryption($is_encrypted, $response, $compress);
                                exit();
                            }
                        } else {
                            $apeendOTPQueryMainAdmin = " AND admin_password='$inputPass'";
                            $apeendOTPQuery = "  AND bms_admin_master.admin_password='$inputPass'";
                            if (!password_verify($inputPass, $data['admin_password'])) {
                                $response["message"] = "Wrong Credentials Details";
                                $response["status"] = "201";
                                echo $d->manage_encryption($is_encrypted, $response, $compress);
                                exit();
                            }
                        }

                        if ($data['active_status'] == 1) {
                            $response["message"] = "Your account is deactivated, please contact " . $d->app_name() . "  support team";
                            $response["status"] = "201";
                            echo $d->manage_encryption($is_encrypted, $response, $compress);
                            exit();
                        }
                        $admin_id = $data['admin_id'];
                        $admin_name = $data['admin_name'];
                        $paylod = [
                            'iat' => time(),
                            'iss' => $base_url,
                            'exp' => time() + (86400 * 5), // 86400 * 5
                            'adminId' => $d->encryptDecrypt("encrypt", "$admin_id")
                        ];
                        $key = $d->get_encrypt_key();
                        $token = JWT::encode($paylod, $key, 'HS256');

                        $m->set_data('master_token', $token);
                        $m->set_data('master_token_expired_on', date('Y-m-d H:i:s', $paylod['exp']));
                        $admin_token_data = array(
                            'master_token' => $m->get_data('master_token'),
                            'master_token_expired_on' => $m->get_data('master_token_expired_on'),
                        );
                        $insert1 = $d->update('bms_admin_master', $admin_token_data, "admin_id='$admin_id'");
                        $ip_address = $_SERVER['REMOTE_ADDR'];
                        $browser = $_SERVER['HTTP_USER_AGENT'];
                        $loginTime = date("Y-m-d H:i:s");
                        $m->set_data('admin_id', $admin_id);
                        $m->set_data('name', $admin_name);
                        $m->set_data('role_name', 'Admin');
                        $m->set_data('ip_address', $ip_address);
                        $m->set_data('browser', $browser);
                        $m->set_data('loginTime', $loginTime);
                        $a1 = array(
                            'admin_id' => $m->get_data('admin_id'),
                            'name' => $m->get_data('name'),
                            'role_name' => $m->get_data('role_name'),
                            'ip_address' => $m->get_data('ip_address'),
                            'browser' => $m->get_data('browser'),
                            'loginTime' => $m->get_data('loginTime'),
                        );
                        $insert = $d->insert('session_log', $a1);
                        $language_id = $data['primary_language_id'];
                        $response["message"] = "Welcome " . $data['admin_name'];
                        $response["token"] = $token;
                        $response["status"] = "200";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    } else {
                        $response["message"] = "Wrong Credentials Details";
                        $response["status"] = "201";
                        echo $d->manage_encryption($is_encrypted, $response, $compress);
                        exit();
                    }
                } else {
                    $response["message"] = "Mobile number is required";
                    $response["status"] = "201";
                    echo $d->manage_encryption($is_encrypted, $response, $compress);
                    exit();
                }
            } else {
                $response["message"] = "Wrong Tag   ";
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
