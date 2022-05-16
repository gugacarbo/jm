<?php
//session start 
session_start();

if (isset($_GET['name']) && isset($_GET['email'])) {

    include 'db_connect.php';
    $name  = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['name']);
    $email  = preg_replace('/[|\,\;\\\:"]+/', '', $_GET['email']);
    //Remove caracteres for bd security
    $name = mysqli_real_escape_string($mysqli, $name);
    $email = mysqli_real_escape_string($mysqli, $email);
    //Verify if email already exists on db
    $stmt = $mysqli->prepare("SELECT * FROM newsletter WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result_ = $stmt->get_result();

    if ($result_->num_rows > 0) {

        die(json_encode(array('status' => 'success')));
    } else {
        if (isset($_SESSION['newsletterTry']) && $_SESSION['newsletterTry'] > 5){
            $lastTry = date($_SESSION['newsletterLastTry']);
            $interval = strtotime(date('Y-m-d H:i:s')) - strtotime($lastTry);
            if ($interval > 60) {
                $_SESSION['newsletterTry'] = isset($_SESSION['newsletterTry']) ? $_SESSION['newsletterTry'] + 1 : 1;
                $_SESSION['newsletterTry'] = 0;
            }

            die(json_encode(array('error' => 'Tente Novamente Mais Tarde')));
        } else {
            $_SESSION['newsletterTry'] = isset($_SESSION['newsletterTry']) ? $_SESSION['newsletterTry'] + 1 : 1;
            $_SESSION['newsletterLastTry'] = date("Y-m-d H:i:s");
            $stmt = $mysqli->prepare("INSERT INTO newsletter (name, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $email);
            if ($stmt->execute()) {
                die(json_encode(array('status' => 'success')));
            }
        }
    }
    //close connection
} else {
    $mysqli->close();
    die(json_encode(array('status' => 'error')));
}



function errHandle($errNo, $errStr, $errFile, $errLine)
{
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
        die(json_encode(array('status' => '403')));
    } 
}

set_error_handler('errHandle');
