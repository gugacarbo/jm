<?php
//Cadastra novo usuário na newsletter
//Path: php\cadNewsLetter.php

//verifica get name and email
if(isset($_GET['name']) && isset($_GET['email'])){
    
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
    }else{
        $stmt = $mysqli->prepare("INSERT INTO newsletter (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $email);
        if($stmt->execute()){
            die(json_encode(array('status' => 'success')));
        }
    }
    //close connection
}else{
    $mysqli->close();
    die(json_encode(array('status' => 'error')));
}