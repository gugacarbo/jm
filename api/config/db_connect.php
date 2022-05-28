<?php

if(!function_exists('errHandle')){
  function errHandle($errNo, $errStr, $errFile, $errLine)
  {
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
      die(json_encode(array('status' => 403)));
    }
  }
  //set_error_handler('errHandle');
}



abstract class dbConnect
{
  public function Conectar()
  {
    try {
      include("db_config.php");
      $mysqli = new mysqli($server, $user, $password, $dbname);
      return $mysqli;
    } catch (Exception $e) {
      echo $Erro->getMessage();
    }
  }
}
