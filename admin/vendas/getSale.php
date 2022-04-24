<?php

if(isset($_GET["code"])){
    include "../db_connect.php";
    $code = $_GET["code"];
    $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $sale_ = $result->fetch_assoc();
    $sale = json_decode($sale_["rawPayload"]);
    $tk = $sale_["trackingCode"];
    $stmt = $mysqli->prepare("SELECT * FROM client WHERE id = ?");
    $stmt->bind_param("i", $sale_["clientId"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $client = $result->fetch_assoc();
    $sale->trackingCode = $tk;
    
    die(json_encode(array("status" => "sucess", "sale" => $sale, "client" => $client)));
}