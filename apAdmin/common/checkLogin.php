<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// if(!isset($bms_admin_id))
// {
//   $_SESSION['msg1']= "Login First.";
//   header("location:index.php?LoginFirst");
// }
$pageName=basename($_SERVER['PHP_SELF']);
$token = $_COOKIE['master_token'] ?? null;

if (!$token) {
    $_SESSION['msg1'] = "Login First.";
    header("Location: index.php?LoginFirst");
    exit();
}
try {
    $key = $d->get_encrypt_key();
    $decoded = JWT::decode($token, $key, ['HS256']);
    if (time() > $decoded->exp) {
        $_SESSION['msg1'] = "Login First.";
        header("Location: index.php?LoginFirst");
        exit();
    }
    $adminId = $decoded->adminId; 
} catch (Exception $e) {
    $_SESSION['msg1'] = "Login First.";
    header("Location: index.php?LoginFirst");
    exit();
}
?>

