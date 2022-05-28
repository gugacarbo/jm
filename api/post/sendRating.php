<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function errHandle($errNo, $errStr, $errFile, $errLine)
{
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
        die(json_encode(array('status' => 403)));
    }
}

set_error_handler('errHandle');



if (isset($_POST['rate'])) {
    if (isset($_SESSION['rateTry']) && $_SESSION['rateTry'] > 2) {
        $lastTry = date($_SESSION['rateLastTry']);
        $interval = strtotime(date('Y-m-d H:i:s')) - strtotime($lastTry);
        if ($interval > 60) {
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
        include "../config/db_connect.php";
        $sql = "INSERT INTO rating (rate, message, code) VALUES (? , ? , ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iss", $rate, $message, $code);
        $rate = $_POST["rate"];
        $stmt->execute();
        $stmt->close();
        die(json_encode(array('status' => 200)));
    }
} else {
    die(json_encode(array('status' => 400)));
}

