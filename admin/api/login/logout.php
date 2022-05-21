<?php
//logout 
session_start();
$_SESSION['user'] = null;
$_SESSION['admin'] = null;
session_destroy();
header("Location: login.php");
exit;
