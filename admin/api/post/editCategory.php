<?php

header('Content-Type: application/json; charset=utf-8');

if(isset($_POST["newCat"])){
    include "../config/db_connect.php";
    //prepare update where name = newCat
    $stmt = $mysqli->prepare("UPDATE categories SET name = ? WHERE name = ?");
    $stmt->bind_param("ss", $_POST["newCat"], $_POST["oldCat"]); 
    $stmt->execute();
    if($stmt->affected_rows > 0){
        die(json_encode(array("status" => 200, "message" => "Categoria alterada com sucesso")));
    }else{
        die(json_encode(array("status" => 500, "message" => "Erro ao alterar categoria")));
    }
}else{
    die(json_encode(array("status" => 400, "message" => "Bad Request")));
}