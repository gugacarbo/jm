<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
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
