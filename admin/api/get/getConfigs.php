<?php

header('Content-Type: application/json; charset=utf-8');

include "../config/db_connect.php";

$GconfigTake = ["contactMail", "automaticMail",  "adminMail", "sendToAdminMail", "cepOrigemFrete", "aditionalWeight", "alturaFrete", "larguraFrete", "comprimentoFrete" ];

$config = array();

foreach ($GconfigTake as $key => $value) {

    $sql = "SELECT value FROM generalConfig WHERE config = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $stmt->bind_result($config[$value]);
    $stmt->fetch();
    $stmt->close();
}
$config["sendToAdminMail"] = json_decode($config["sendToAdminMail"]);
die(json_encode($config));