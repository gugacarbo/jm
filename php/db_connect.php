<?php
include("db_config.php");
$mysqli = new mysqli($server, $user, $password, $dbname);
if(!$mysqli){
  die("Falha na conexao: " . mysqli_connect_error());
}
?>