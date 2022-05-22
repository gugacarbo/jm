<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$adminPass = "admin";
$adminHashCode = md5($adminPass);

if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || !isset($_SESSION['hashCode']) || $_SESSION['hashCode'] !== $adminHashCode) {
  die(json_encode(array('status' => 403)));
} else {
  include("db_config.php");
  $mysqli = new mysqli($server, $user, $password, $dbname);
  if(!$mysqli){
    die(json_encode(array('status' => 505)));
  }  
}



?>