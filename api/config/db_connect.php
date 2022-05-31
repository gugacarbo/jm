<?php

if (!function_exists('errHandledb')) {
  function errHandledb($errNo, $errStr, $errFile, $errLine)
  {
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
      die(json_encode(array('status' => 403)));
    }
  }
  //set_error_handler('errHandledb');
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
  public function error_log($type, $errorMessage)
  {

    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
    $errorMessage['url'] = $actual_link;
    $errorMessage["post"] = json_encode($_POST);
    $errorMessage["get"] =  json_encode($_GET);
    $errorMessage["session"] =  json_encode($_SESSION);
    $errorMessage["server"] =  json_encode($_SERVER);
    
    $errorMessage = json_encode($errorMessage);
    $mysqli = $this->Conectar();
    $stmt = $mysqli->prepare("INSERT INTO error_log (type, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $type, $errorMessage);
    $stmt->execute();
  }
}
