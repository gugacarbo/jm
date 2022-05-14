<?php
if(isset($_GET["newCat"])){
    include "../db_connect.php";
    //prepare update where name = newCat
    $stmt = $mysqli->prepare("UPDATE material SET name = ? WHERE name = ?");
    $stmt->bind_param("ss", $_GET["newCat"], $_GET["oldCat"]); 
    $stmt->execute();
    if($stmt->affected_rows > 0){
        die(json_encode(array("status" => "success", "message" => "Categoria alterada com sucesso")));
    }else{
        die(json_encode(array("status" => "error", "message" => "Erro ao alterar categoria")));
    }
    
}