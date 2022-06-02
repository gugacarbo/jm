<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../config/db_connect.php';

class Contact extends dbConnect
{
    private $name_, $message_, $phone_;

    public function __construct($name, $message, $phone)
    {
        $this->name_  = preg_replace('/[|\,\;\@\:"]+/', '', $name);
        $this->message_  = preg_replace('/[|\,\;\\\:"]+/', '', $message);
        $this->phone_  = preg_replace('/[|\,\;\\\:"]+/', '', $phone);
    }
    
    public function send()
    {
        $mysqli = $this->Conectar();

        $name = mysqli_real_escape_string($mysqli, $this->name_);
        $message = mysqli_real_escape_string($mysqli, $this->message_);
        $phone = mysqli_real_escape_string($mysqli, $this->phone_);

        //inserting data to database
        $mysqli->query("INSERT INTO contact (name, phone, message) VALUES ('$name', '$phone', '$message')");
        //close connection
        $mysqli->close();
        return(json_encode(array('status' => 201)));
    }
}


if (isset($_POST['name']) && isset($_POST['phone']) && isset($_POST['message'])) {

    if (isset($_SESSION['contactTry']) && $_SESSION['contactTry'] > 2) {
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

        $contact = new Contact($_POST['name'], $_POST['message'], $_POST['phone']);
        die($contact->send());
    }
} else {
    die(json_encode(array('status' => 400)));
}


