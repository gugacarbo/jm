<?php

if(isset($_GET["mat"])){
    include "../db_connect.php";
    //delete from db
    $stmt = $mysqli->prepare("DELETE FROM material WHERE name = ?");
    $stmt->bind_param("s", $_GET["mat"]);
    $stmt->execute();
    if($stmt->affected_rows > 0){
        die(json_encode(array("status" => "success", "message" => "Material removido com sucesso")));
    }else{
        die(json_encode(array("status" => "error", "message" => "Erro ao remover material")));
    }
}