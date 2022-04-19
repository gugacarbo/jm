<?php
//send contact form data to database
//connect to database
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