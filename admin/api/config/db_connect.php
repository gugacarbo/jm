<?php
define('TIMEZONE', 'America/Sao_Paulo');
date_default_timezone_set(TIMEZONE);


if (session_status() === PHP_SESSION_NONE) {
  session_name(md5("JM".$_SERVER['REMOTE_ADDR']));
  session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 1) {

  die(json_encode(array(
    'status' => 403,
    'message' => 'Forbidden'
  )));

} else {
  include("db_config.php");
  abstract class dbConnect extends dbConfig
  {
    public function connect()
    {
      try {


        $mysqli = new mysqli($this->JMserver, $this->JMuser, $this->JMpassword, $this->JMdbname);

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
      $mysqli = $this->connect();
      $stmt = $mysqli->prepare("INSERT INTO error_log (type, message) VALUES (?, ?)");
      $stmt->bind_param("ss", $type, $errorMessage);
      $stmt->execute();
    }
  }
}
