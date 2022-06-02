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
  }
}
