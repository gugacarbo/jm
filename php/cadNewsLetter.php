<?php
//Cadastra novo usuário na newsletter
//Path: php\cadNewsLetter.php
include 'db_connect.php';

//verifica get name and email
if(isset($_GET['name']) && isset($_GET['email'])){
    $name = $_GET['name'];
    $email = $_GET['email'];
    //Verify if email already exists on db
    $sql = "SELECT * FROM newsletter WHERE email = '$email'";
    $result = $mysqli->query($sql);
    if ($result->num_rows > 0) {
        $mysqli->close();
        die(json_encode(array('status' => 'success')));
    }else{
        $mysqli->query("INSERT INTO newsletter (name, email) VALUES ('$name', '$email')");   
        $mysqli->close();
        die(json_encode(array('status' => 'success')));
    }
    //close connection
}else{
    $mysqli->close();
    die(json_encode(array('status' => 'error')));
}