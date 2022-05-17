<?php
header('Content-Type: application/json; charset=utf-8');


if (isset($_GET['cpf']) && isset($_GET['code']) && is_numeric($_GET['cpf']) && strlen($_GET['code']) == 36) {

    include "db_connect.php";

    $cpf = $_GET['cpf'];
    $code = $_GET['code'];
    $code = preg_replace('/[|\,\;\\\:"]+/', '', $code);

    $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result_ = $stmt->get_result();

    if ($result_->num_rows > 0) {
        
        $product = $result_->fetch_assoc();
        $clientId = $product['clientId'];

        $stmt = $mysqli->prepare("SELECT * FROM client WHERE cpf = '$cpf'  AND id = ?");
        $stmt->bind_param("s", $clientId);
        $stmt->execute();
        $result2 = $stmt->get_result();

        if ($result2->num_rows > 0) {
            $row = $result2->fetch_assoc();
            
            $product["bornDate"] = $row["bornDate"];
            $product["name"] = $row["name"] . " " . $row["lastName"];
            $product["cpf"] = $cpf;
            echo json_encode($product);
        }else{
            echo json_encode(array("status" => "error", "message" => "Dados Inco2rretos!"));
        }
    }else{
        echo json_encode(array("status" => "error", "message" => "Dados Inc1orretos!"));
    }
}else{
    http_response_code(404);
}