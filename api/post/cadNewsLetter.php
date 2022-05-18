<?php
//session start 
header('Content-Type: application/json; charset=utf-8');
session_start();

if (isset($_POST['name']) && isset($_POST['email'])) {

    if (isset($_SESSION['newsletterTry']) && $_SESSION['newsletterTry'] > 5) {
        $lastTry = date($_SESSION['newsletterLastTry']);
        $interval = strtotime(date('Y-m-d H:i:s')) - strtotime($lastTry);
        if ($interval > 60) {
            $_SESSION['newsletterTry'] = isset($_SESSION['newsletterTry']) ? $_SESSION['newsletterTry'] + 1 : 1;
            $_SESSION['newsletterTry'] = 0;
        }

        die(json_encode(array('status' => 403)));
    } else {

        $_SESSION['newsletterTry'] = isset($_SESSION['newsletterTry']) ? $_SESSION['newsletterTry'] + 1 : 1;
        $_SESSION['newsletterLastTry'] = date("Y-m-d H:i:s");

        include '../config/db_connect.php';

        $name  = preg_replace('/[|\,\;\@\:"]+/', '', $_POST['name']);
        $email  = preg_replace('/[|\,\;\\\:"]+/', '', $_POST['email']);

        $name = mysqli_real_escape_string($mysqli, $name);
        $email = mysqli_real_escape_string($mysqli, $email);

        $stmt = $mysqli->prepare("SELECT * FROM newsletter WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result_ = $stmt->get_result();
        $stmt->close();
        if ($result_->num_rows > 0) {
            die(json_encode(array('status' => 200)));
        } else {
            $stmt = $mysqli->prepare("INSERT INTO newsletter (name, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $email);
            if ($stmt->execute()) {
                die(json_encode(array('status' => 201)));
            }
        }
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
