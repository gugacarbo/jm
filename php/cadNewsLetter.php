<?php
//Cadastra novo usuário na newsletter
//Path: php\cadNewsLetter.php
include 'db_connect.php';

//verifica get name and email
if(isset($_GET['name']) && isset($_GET['email'])){
    $name = $_GET['name'];
    $email = $_GET['email'];
    
    //insert data to database
    $mysqli->query("INSERT INTO newsletter (name, email) VALUES ('$name', '$email')");
    
    //close connection
    $mysqli->close();
    die(json_encode(array('status' => 'success')));
}