<?php
//logout 

if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM".$_SERVER['REMOTE_ADDR']));
    session_start();
}

$_SESSION['user'] = null;
$_SESSION['admin'] = null;
$_SESSION['username'] = null;

unset($_SESSION['user']);
unset($_SESSION['admin']);
unset($_SESSION['username']);
session_destroy();
session_name("sess");
header("Location: index.php");
exit;
