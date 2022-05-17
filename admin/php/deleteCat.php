<?php

if(isset($_GET["cat"])){
    include "db_connect.php";
    //delete from db
    $stmt = $mysqli->prepare("DELETE FROM categories WHERE name = ?");
    $stmt->bind_param("s", $_GET["cat"]);
    $stmt->execute();
    if($stmt->affected_rows > 0){
        die(json_encode(array("status" => "success", "message" => "Categoria removida com sucesso")));
    }else{
        die(json_encode(array("status" => "error", "message" => "Erro ao remover categoria")));
    }

}