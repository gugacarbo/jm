<?php

if(isset($_GET["code"]) && isset($_GET["shippingCode"])){
    include "../db_connect.php";
    $code = $_GET["code"];
    $shippingCode = $_GET["shippingCode"];
    $stmt = $mysqli->prepare("UPDATE vendas SET trackingCode = ? WHERE code = ?");
    $stmt->bind_param("ss", $shippingCode, $code);
    if($stmt->execute()){
        $stmt->close();
        die(json_encode(array("status" => "sucess")));   
    }else{
        $stmt->close();
        die(json_encode(array("status" => "error")));
    }
}