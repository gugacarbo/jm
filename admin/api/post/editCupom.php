<?php

header('Content-Type: application/json; charset=utf-8');

if (isset($_POST["id"]) && isset($_POST["ticker"]) && isset($_POST["value"]) && isset($_POST["type"]) && isset($_POST["quantity"]) && isset($_POST["firstPurchase"])
    && $_POST["id"] != "" && $_POST["ticker"] != "" && $_POST["value"] != "" && $_POST["type"] != "" && $_POST["quantity"] != "" && $_POST["firstPurchase"] != "") {
    include "../config/db_connect.php";

    $stmt = $mysqli->prepare("SELECT * FROM cupom WHERE ticker = ? AND id = ?");
    $stmt->bind_param("ss", $_POST["ticker"], $_POST["id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0) {
        $stmt = $mysqli->prepare("UPDATE cupom SET value = ?, type = ?, quantity = ?, firstPurchase = ? WHERE ticker = ?");
        $stmt->bind_param("sssss", $_POST["value"], $_POST["type"], $_POST["quantity"], $_POST["firstPurchase"], $_POST["ticker"]);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            die(json_encode(array("status" => 200, "message" => "Cupom alterado com sucesso")));
        } else {
            $stmt->close();

            die(json_encode(array("status" => 500, "message" => "Erro ao alterar cupom")));
        }
    } else {
        $ticker = strtoupper($_POST["ticker"]);
        $stmt = $mysqli->prepare("INSERT INTO cupom (ticker, value, type, quantity, firstPurchase) VALUES ( ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $ticker, $_POST["value"], $_POST["type"], $_POST["quantity"], $_POST["firstPurchase"]);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            die(json_encode(array("status" => 200, "message" => "Categoria alterada com sucesso")));
        } else {
            die(json_encode(array("status" => 500, "message" => $stmt->error)));
        }
    }
} else {
    die(json_encode(array("status" => 400, "message" => "Bad Request")));
}
