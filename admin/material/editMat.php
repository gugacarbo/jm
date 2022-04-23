<?php
if(isset($_GET["newMat"])){
    include "../db_connect.php";
    //prepare update where name = newCat
    $stmt = $mysqli->prepare("UPDATE material SET name = ? WHERE name = ?");
    $stmt->bind_param("ss", $_GET["newMat"], $_GET["oldMat"]); 
    $stmt->execute();
    if($stmt->affected_rows > 0){
        die(json_encode(array("status" => "success", "message" => "Material alterado com sucesso")));
    }else{
        die(json_encode(array("status" => "error", "message" => "Não foi possível alterar o material")));
    }
    
}