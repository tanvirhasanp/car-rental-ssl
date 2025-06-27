<?php
session_start(); 

// Include OTP functions for cleanup
include('includes/config.php');
// Note: otp_functions.php is already included in config.php

// Clear any pending OTP sessions
clearCurrentOtpSession();

$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 60*60,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
unset($_SESSION['login']);
session_destroy(); // destroy session
header("location:index.php"); 
?>

