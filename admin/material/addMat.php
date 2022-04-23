<?php

if(isset($_GET["newMat"])){
    include "../db_connect.php";
    //verify if cat already exists in database
    //stmt prepare
    $stmt = $mysqli->prepare("SELECT * FROM material WHERE name = ?");
    $stmt->bind_param("s", $_GET["newMat"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if($result->num_rows > 0){
        die(json_encode(array("status" => "error", "message" => "Categoria já existe")));
    }else{
        $stmt = $mysqli->prepare("INSERT INTO material (name) VALUES (?)");
        $stmt->bind_param("s", $_GET["newMat"]);
        $stmt->execute();
        //get id from last insert
        $id = $mysqli->insert_id;
        if($stmt->affected_rows > 0){
            die(json_encode(array("status" => "success", "message" => "Material adicionado com sucesso", "id" => $id)));
        }else{
            die(json_encode(array("status" => "error", "message" => "Erro ao adicionar material")));
        }
    }
}