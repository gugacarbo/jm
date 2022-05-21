<?php

header('Content-Type: application/json; charset=utf-8');

if (isset($_POST["cepOrigemFrete"]) && isset($_POST["aditionalWeight"]) && isset($_POST["alturaFrete"]) && isset($_POST["larguraFrete"]) && isset($_POST["comprimentoFrete"])) {
    include "../config/db_connect.php";

    $configs = [
        "cepOrigemFrete" => $_POST["cepOrigemFrete"],
        "aditionalWeight" => $_POST["aditionalWeight"],
        "alturaFrete" => $_POST["alturaFrete"],
        "larguraFrete" => $_POST["larguraFrete"],
        "comprimentoFrete" => $_POST["comprimentoFrete"]
    ];
    foreach ($configs as $key => $value) {
        $sql = "UPDATE generalconfig SET value = ? WHERE config = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $value, $key);
        $stmt->execute();
        $stmt->close();
    }
    die(json_encode(array('status' => 200)));
} else {
    die(json_encode(array("status" => 400, "message" => "Bad Request")));
}
