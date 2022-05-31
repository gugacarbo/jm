<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');

define('TIMEZONE', 'America/Sao_Paulo');
date_default_timezone_set(TIMEZONE);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../config/db_connect.php';

class Unsubscribe extends dbConnect
{
    public function __construct($email)
    {
        $mysqli = $this->Conectar();
        $sql = "DELETE FROM newsletter WHERE email = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $stmt->close();
            return (json_encode(array("status" => 200, "message" => "You have been unsubscribed from the newsletter.")));
        } else {
            $stmt->close();
            return (json_encode(array("status" => 500, "message" => "Error unsubscribing from the newsletter.")));
        }
    }
}

if (isset($_GET['email'])) {

    if (isset($_SESSION['unsubTry']) && $_SESSION['unsubTry'] > 1) {
        $lastTry = date($_SESSION['unsubLastTry']);
        $interval = strtotime(date('Y-m-d H:i:s')) - strtotime($lastTry);
        if ($interval > 380) {
            $_SESSION['unsubTry'] = 0;
        }
        die(json_encode(array("status" => 403, 'error' => 'Tente Novamente Mais Tarde')));
    } else {
        $_SESSION['unsubTry'] = isset($_SESSION['unsubTry']) ? $_SESSION['unsubTry'] + 1 : 1;
        $_SESSION['unsubLastTry'] = date("Y-m-d H:i:s");

        die((new Unsubscribe($_GET["email"]))->__construct($_GET["email"]));
    }
}else{
    die(json_encode(array("status" => 403, 'error' => 'Tente Novamente Mais Tarde')));
}
