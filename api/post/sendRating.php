<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');

define('TIMEZONE', 'America/Sao_Paulo');
date_default_timezone_set(TIMEZONE);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../config/db_connect.php';

class Nps extends dbConnect
{
    private $rate_, $message_, $code_;
    public function __construct($rate, $message, $code)
    {
        $this->rate_ = $rate;
        $this->message_ = $message;
        $this->code_ = $code;
    }
    public function sendRate()
    {
        $rate = $this->rate_;
        $message = $this->message_;
        $code = $this->code_;

        $mysqli = $this->Conectar();
        $sql = "INSERT INTO rating (rate, message, code) VALUES (? , ? , ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iss", $rate, $message, $code);
        if($stmt->execute()) {
            $stmt->close();
            return array("status" => 202, "message" => "Obrigado por avaliar!");
        } else {
            $stmt->close();
            return array("status" => 500, "message" => "Erro ao avaliar! >");
        }
    }
}


if (isset($_POST['rate'])) {
    if (isset($_SESSION['rateTry']) && $_SESSION['rateTry'] > 1) {
        $lastTry = date($_SESSION['rateLastTry']);
        $interval = strtotime(date('Y-m-d H:i:s')) - strtotime($lastTry);
        if ($interval > 360) {
            $_SESSION['rateTry'] = isset($_SESSION['rateTry']) ? $_SESSION['rateTry'] + 1 : 1;
            $_SESSION['rateTry'] = 0;
        }
        die(json_encode(array('status' => 403)));
    } else {

        $_SESSION['rateTry'] = isset($_SESSION['rateTry']) ? $_SESSION['rateTry'] + 1 : 1;
        $_SESSION['rateLastTry'] = date("Y-m-d H:i:s");

        if (isset($_POST["message"])) {
            $message = $_POST["message"];
        } else {
            $message = "";
        }
        if (isset($_POST["code"])) {
            $code = $_POST["code"];
        } else {
            $code = "";
        }
        $nps = new Nps($_POST["rate"], $message, $code);
        $result = $nps->sendRate();
        die(json_encode($result));
    }
} else {
    die(json_encode(array('status' => 400)));
}
