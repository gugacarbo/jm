<?php

session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
  die(json_encode(array('status' => '403')));
} else {
  include("db_config.php");
  $mysqli = new mysqli($server, $user, $password, $dbname);
  if(!$mysqli){
    die("Falha na conexao: " . mysqli_connect_error());
  }  
}



?>