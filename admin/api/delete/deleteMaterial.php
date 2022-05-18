<?php
header('Content-Type: application/json; charset=utf-8');

if(isset($_POST["cat"])){
    include "../config/db_connect.php";
    //delete from db
    $stmt = $mysqli->prepare("DELETE FROM material WHERE name = ?");
    $stmt->bind_param("s", $_POST["cat"]);
    $stmt->execute();
    if($stmt->affected_rows > 0){
        die(json_encode(array("status" => 200, "message" => "Categoria removida com sucesso")));
    }else{
        die(json_encode(array("status" => 500, "message" => "Erro ao remover categoria")));
    }
}else{
    die(json_encode(array("status" => 400, "message" => "Bad Request")));
}