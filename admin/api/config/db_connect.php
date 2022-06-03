<?php


define('TIMEZONE', 'America/Sao_Paulo');
date_default_timezone_set(TIMEZONE);


if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 1) {
  die(json_encode(array(
    'status' => 403,
    'message' => 'Forbidden'
  )));
} else {
  abstract class dbConnect
  {
    public function connect()
    {
      try {
        include("db_config.php");
        $mysqli = new mysqli($server, $user, $password, $dbname);
        return $mysqli;
      } catch (Exception $e) {
        die(json_encode(array(
          'status' => 500,
          'message' => 'Internal Server Error'
        )));
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
}
