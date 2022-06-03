<?php
//logout 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['user'] = null;
$_SESSION['admin'] = null;
$_SESSION['hashCode'] = null;
unset($_SESSION['user']);
unset($_SESSION['admin']);
unset($_SESSION['hashCode']);
session_destroy();
header("Location: login.php");
exit;
