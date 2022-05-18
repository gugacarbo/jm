<?php
header('Content-Type: application/json; charset=utf-8');

if(isset($_POST["newCat"])){
    include "../config/db_connect.php";
    //verify if cat already exists in database
    //stmt prepare
    $stmt = $mysqli->prepare("SELECT * FROM material WHERE name = ?");
    $stmt->bind_param("s", $_POST["newCat"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if($result->num_rows > 0){
        die(json_encode(array("status" => 400, "message" => "Categoria já existe")));
    }else{
        $stmt = $mysqli->prepare("INSERT INTO material (name) VALUES (?)");
        $stmt->bind_param("s", $_POST["newCat"]);
        $stmt->execute();
        //get id from last insert
        $id = $mysqli->insert_id;
        if($stmt->affected_rows > 0){
            die(json_encode(array("status" => 202, "message" => "Categoria adicionada com sucesso", "id" => $id)));
        }else{
            die(json_encode(array("status" => 500, "message" => "Erro ao adicionar categoria")));
        }
    }
}else{
    die(json_encode(array("status" => 400, "message" => "Não foi enviado nenhuma categoria")));
}