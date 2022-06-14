<?php

define('TIMEZONE', 'America/Sao_Paulo');
date_default_timezone_set(TIMEZONE);


if (!function_exists('errHandledb')) {
  function errHandledb($errNo, $errStr, $errFile, $errLine)
  {
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
      die(json_encode(array('status' => 403, 'message' => "Forbidden! .")));
    }
  }
  //set_error_handler('errHandledb');
}



include("db_config.php");

abstract class dbConnect extends dbConfig
{
  public function Conectar()
  {
    try {
      $mysqli = new mysqli($this->JMserver, $this->JMuser, $this->JMpassword, $this->JMdbname);
      return $mysqli;
    } catch (Exception $e) {
      die(json_encode(array('status' => 500, 'message' => "Internal Server Error")));
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
