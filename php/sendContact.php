<?php
//send contact form data to database
//connect to database
header('Content-Type: application/json; charset=utf-8');

include 'db_connect.php';

//verfiying if is get name, phone and message
if(isset($_GET['name']) && isset($_GET['phone']) && isset($_GET['message'])){
    $name = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['name']);
    $phone = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['phone']);
    $message = $_GET['message'];
    
    //inserting data to database
    $mysqli->query("INSERT INTO contact (name, phone, message) VALUES ('$name', '$phone', '$message')");
    //close connection
    $mysqli->close();
    die(json_encode(array('status' => 'success')));
}

function errHandle($errNo, $errStr, $errFile, $errLine)
{
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
        die(json_encode(array('status' => '403')));
    } 
}

set_error_handler('errHandle');
