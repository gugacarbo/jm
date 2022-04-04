<?php
include("db_connect.php");
if(isset($_GET["email"]) && isset($_GET["nome"])){
    $email = $_GET["email"];
    $nome = $_GET["nome"];

    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        
        $sql = "SELECT * FROM newsletter WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $res = $result->fetch_assoc();
        if($result->num_rows > 0){
            
            die("email Já Cadastrado!");
        }else{
            $sql = "INSERT INTO newsletter( `email`, `nome`) VALUES (?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $email, $nome);
            $stmt->execute();
            die("email Cadastrado Com Sucesso!");
        }



    }else{
        die("ERROR");
    }
}else
    die("error");

?>