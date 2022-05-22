<?php
header('Content-Type: application/json; charset=utf-8');

if(isset($_POST["id"])){
    include "../config/db_connect.php";
    $id = $_POST["id"];
    $stmt = "DELETE FROM cupom WHERE id = ?";
    $stmt = $mysqli->prepare($stmt);
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        $stmt->close();
        die(json_encode(array("status" => 202)));
    }else{
        $stmt->close();
        die(json_encode(array("status" => 500)));
    }
}else{
    die(json_encode(array("status" => 400, "message" => "Bad Request")));
}