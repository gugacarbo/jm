<?php
//verify if session login is valid
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
} else {
    $user = $_SESSION['user'];
}
?>
