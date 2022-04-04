<?php
include("db_config.php");

$mysqli = mysqli_connect($server, $user, $password, $dbname);

if(!$mysqli){
  die("Falha na conexao: " . mysqli_connect_error());
}else{
}
?>