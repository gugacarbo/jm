<?php

if(isset($_GET["newCat"])){
    include "../db_connect.php";
    //verify if cat already exists in database
    //stmt prepare
    $stmt = $mysqli->prepare("SELECT * FROM categories WHERE name = ?");
    $stmt->bind_param("s", $_GET["newCat"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if($result->num_rows > 0){
        die(json_encode(array("status" => "error", "message" => "Categoria já existe")));
    }else{
        $stmt = $mysqli->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $_GET["newCat"]);
        $stmt->execute();
        //get id from last insert
        $id = $mysqli->insert_id;
        if($stmt->affected_rows > 0){
            die(json_encode(array("status" => "success", "message" => "Categoria adicionada com sucesso", "id" => $id)));
        }else{
            die(json_encode(array("status" => "error", "message" => "Erro ao adicionar categoria")));
        }
    }
}