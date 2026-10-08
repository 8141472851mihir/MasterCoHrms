<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
$url = $_SESSION['url'];
$activePage = basename($_SERVER['PHP_SELF'], ".php");
include_once '../lib/dao.php';
include '../lib/model.php';
include '../lib/sms_api.php';
include '../lib/jwt.php';
include_once '../fcm_file/admin_fcm.php';
include_once '../fcm_file/gaurd_fcm.php';
include_once '../fcm_file/resident_fcm.php';
$d = new dao();
$m = new model();
$sms_api = new sms_api();
$nAdmin = new firebase_admin();
$nResident = new firebase_resident();
$nGaurd = new firebase_gaurd();
$con = $d->dbCon();
$created_by = $admin_name ?? "";
$updated_by = $admin_name ?? "";
$society_id = $society_id ?? "";
extract(array_map("test_input", $_POST));
$base_url = $m->base_url();


if (isset($_REQUEST) && !empty($_REQUEST) && $_REQUEST["csrf"] != $_SESSION["token"] || $_REQUEST["csrf"] == '') {
	$_SESSION['msg1'] = "Token Mismatch";
	header('location:../');
	exit();
}

if (isset($_COOKIE['master_token'])) {
	$token = $_COOKIE['master_token'] ?? null;
	$key = $d->get_encrypt_key();
	try {
		$decoded = JWT::decode($token, $key, ['HS256']);
		if (time() < $decoded->exp) {
			header("location:./welcome");
			exit();
		}
	} catch (Exception $e) {
	}
}

if (isset($_POST) && !empty($_POST)) //it can be $_GET doesn't matter
{
	extract(array_map("test_input", $_POST));
	if (isset($_POST["adminMobile"]) && $_POST['SendOPT'] == "yes") {
		if (!$d->require_turnstile()) {
			echo "3";
			exit;
		}
		$adminMobile = mysqli_real_escape_string($con, $adminMobile);
		$adminMobileDec = $adminMobile;
		$adminMobile = $d->encryptDecrypt("encrypt", $adminMobile);
		$country_code = mysqli_real_escape_string($con, $country_code);
		$q = $d->select("bms_admin_master", "admin_mobile='$adminMobile' AND country_code='$country_code' and active_status='0'");
		if (mysqli_num_rows($q) > 0) {
			$bms_admin_master_data = mysqli_fetch_array($q);
			extract($bms_admin_master_data);
			$digits = 6;
			$otp_web = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
			$m->set_data('otp_web', $otp_web);
			$a = array(
				'otp_web' => $m->get_data('otp_web'),
			);
			$result = $d->update("bms_admin_master", $a, "admin_mobile='$adminMobile' and active_status='0'");
			if ($result) {
				$subject = "" . $d->app_name() . " Master Panel Login OTP";
				$admin_name = $bms_admin_master_data['admin_name'];
				$to = $d->encryptDecrypt("decrypt", $bms_admin_master_data['admin_email']);
				$msg = $otp_web;
				$dynamic_button_urls = [['url' => "https://www.whatsapp.com/otp/code/?otp_type=COPY_CODE&code_expiration_minutes=10&code=otp{{1}}",'variable' => "$otp_web"]];
				$encoded_urls = $dynamic_button_urls;
				if (method_exists($d, 'send_otp_on_mail') && $d->send_otp_on_mail()=='1'){
					$to_string = $country_code." ".$adminMobileDec ." on whatsapp.";
					$response = $sms_api->sendWhatsAppMessage($adminMobileDec, "55", "1472869617244077", ["$otp_web"], ['dynamic_button_urls' => $encoded_urls], $d, '91');
					echo $to_string;
				}else{
					$to_string = $to;
					include '../mail/adminPanelLoginOTP.php';
					include '../mail.php';
					echo $to_string;
				}
			} else {
				echo "2";
			}
		} else {
			echo "0";
		}
	}

	if (isset($_POST['checkLoginOTP']) && $_POST['checkLoginOTP'] == "checkLoginOTP") {
		$mobile = $d->encryptDecrypt("encrypt", $mobile);
		$q = $d->select("bms_admin_master", "(admin_mobile='$mobile' OR admin_email='$mobile') AND otp_web='$otp_web' and active_status='0'");
		$data = mysqli_num_rows($q);
		if ($data > 0) {
			echo 0;
		} else {
			echo 1;
		}
		exit;
	}
	if (isset($_POST["mobile"])) {
		$d->require_turnstile('../');
		$mobile = mysqli_real_escape_string($con, $mobile);
		$inputPass = mysqli_real_escape_string($con, $inputPass);
		$otp_web = (int)$otp_web;
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
					$_SESSION['msg1'] = "Invalid OTP";
					header("location:../");
					exit;
				}
			} else {
				$apeendOTPQueryMainAdmin = " AND admin_password='$inputPass'";
				$apeendOTPQuery = "  AND bms_admin_master.admin_password='$inputPass'";
				if (!password_verify($inputPass, $data['admin_password'])) {
					$_SESSION['msg1'] = "Wrong Credentials Details";
					header("location:../");
					exit;
				}
			}

			if ($data['active_status'] == 1) {
				$_SESSION['msg1'] = "Your account is deactivated, please contact " . $d->app_name() . "  support team";
				header("location:../");
				exit();
			}
			$admin_id = $data['admin_id'];
			$paylod = [
				'iat' => time(),
				'iss' => $base_url,
				'exp' => time() + (60 * 60 * 12), // 86400 * 5
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
			setcookie('master_token', $admin_token_data['master_token'], $paylod['exp'], "/", "", true, true);

			// setcookie('default_time_zone', $data['default_time_zone'], $paylod['exp'], "/", "",true,true);

			$_SESSION['msg'] = "Welcome $data[admin_name]";
			setcookie('country_code', $data['country_code'], time() + (86400 * 365), "/", true, true); // 86400 = 1 day
			setcookie('country_code', $data['country_code'], time() + (86400 * 365), "/", true, true); // 86400 = 1 day
			// Session Data insert
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
			setcookie('language_id', $language_id, time() + (86400 * 120), "/"); // 86400 = 1 day
			$_SESSION['msg'] = "Welcome " . $data['admin_name'];
			header("location:../welcome");
		} else {
			$_SESSION['msg1'] = "Wrong Credentials Details";
			header("location:../");
		}
	}


	// forgot Password 
	if (isset($_POST["forgot_email"])) {
		$d->require_turnstile('../forgot.php');
		extract(array_map("test_input", $_POST));
		$forgot_email = mysqli_real_escape_string($con, $forgot_email);
		$forgot_email = $d->encryptDecrypt("encrypt", $forgot_email);

		$q = $d->select("bms_admin_master", "admin_email='$forgot_email' OR admin_mobile='$forgot_email' AND country_code='$country_code'");
		if (mysqli_num_rows($q) > 0) {
			$data = mysqli_fetch_array($q);
			extract($data);
			if ($data['active_status'] == 1) {
				$_SESSION['msg1'] = "Your account is deactivated, please contact " . $d->app_name() . "  support team";
				header("location:../forgot.php");
				exit();
			}

			$admin_name = $data['admin_name'];
			$admin_mobile = $data['admin_mobile'];
			$admin_email = $data['admin_email'];
			$token = bin2hex(openssl_random_pseudo_bytes(16));
			$forgotTime = date("Y/m/d"); //Token Date
			$forgotLink = $base_url . "apAdmin/resetPassword.php?t=" . $token . "&f=" . $data['admin_id'];
			$m->set_data('token', $token);
			$m->set_data('token_date', $forgotTime);
			$a1 = array(
				'forgot_token' => $m->get_data('token'),
				'token_date' => $m->get_data('token_date')
			);

			$d->update('bms_admin_master', $a1, "admin_mobile='$admin_mobile'");

			$to = $d->encryptDecrypt("decrypt", $admin_email);
			$subject = "Forgot Password - Master " . $d->app_name() . " ";

			include '../mail/forgotPasswordMail.php';
			include '../mail.php';
			$_SESSION['msg'] = "Password reset link sent to your email";
			header("location:../forgot.php");
		} else {
			$_SESSION['msg1'] = "Wrong Email or Mobile";
			header("location:../forgot.php");
		}
	}


	//Reset Password
	if (isset($_POST["password2"])) {
		$resetRedirect = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php';
		$d->require_turnstile($resetRedirect);
		if ($password2 == $passwordNew) {
			$hashed_password = password_hash($passwordNew, PASSWORD_DEFAULT);
			$m->set_data('token', "");
			$m->set_data('token_date', "");
			$m->set_data('passwordNew', $hashed_password);
			$forgot = array(
				'admin_password' => $m->get_data('passwordNew'),
				'forgot_token' => $m->get_data('token'),
				'token_date' => $m->get_data('token_date'),
			);
			$qForgot = $d->update("bms_admin_master", $forgot, "admin_id= '$_SESSION[forgot_admin_id]'");
			if ($qForgot > 0) {
				$_SESSION['msg'] = "New Password set successfully.";
				header("location:../index.php");
			} else {
				$_SESSION['msg1'] = "Something Wrong...";
				header("location:../index.php");
			}
		} else {
			$_SESSION['msg1'] = "Confirm Password Not Match";
			header("location:../index.php");
		}
	}
} else {
	header("location:../forgot.php");
}
