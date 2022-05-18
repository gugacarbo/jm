<?php
//send contact form data to database
//connect to database
header('Content-Type: application/json; charset=utf-8');
session_start();
if (isset($_POST['name']) && isset($_POST['phone']) && isset($_POST['message'])) {

    if (isset($_SESSION['contactTry']) && $_SESSION['contactTry'] > 5) {
        $lastTry = date($_SESSION['contactLastTry']);
        $interval = strtotime(date('Y-m-d H:i:s')) - strtotime($lastTry);
        if ($interval > 60) {
            $_SESSION['contactTry'] = isset($_SESSION['contactTry']) ? $_SESSION['contactTry'] + 1 : 1;
            $_SESSION['contactTry'] = 0;
        }

        die(json_encode(array('status' => 403)));
    } else {

        $_SESSION['contactTry'] = isset($_SESSION['contactTry']) ? $_SESSION['contactTry'] + 1 : 1;
        $_SESSION['contactLastTry'] = date("Y-m-d H:i:s");


        //verfiying if is get name, phone and message

        $name = preg_replace('/[|\,\;\@\:"]+/', '', $_POST['name']);
        $phone = preg_replace('/[|\,\;\@\:"]+/', '', $_POST['phone']);
        $message = $_POST['message'];

        include '../config/db_connect.php';

        //inserting data to database
        $mysqli->query("INSERT INTO contact (name, phone, message) VALUES ('$name', '$phone', '$message')");
        //close connection
        $mysqli->close();
        die(json_encode(array('status' => 201)));
    }
} else {
    die(json_encode(array('status' => 400)));
}
function errHandle($errNo, $errStr, $errFile, $errLine)
{
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
        die(json_encode(array('status' => 403)));
    }
}

set_error_handler('errHandle');
