<?php

include "../db_connect.php";

$stmt = $mysqli->prepare("SELECT * FROM vendas");
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$vendas = array();
while ($row = $result->fetch_assoc()) {
    $stmt = $mysqli->prepare("SELECT * FROM client WHERE id = ?");
    $stmt->bind_param("i", $row["clientId"]);
    $stmt->execute();
    $result2 = $stmt->get_result();
    $stmt->close();
    $cliente = $result2->fetch_assoc();
    $row["client"]["name"] = $cliente["name"] . " " . $cliente["lastName"];
    $row["client"]["email"] = $cliente["email"];
    $row["client"]["phone"] = $cliente["phone"];
    $row["client"]["cpf"] = $cliente["cpf"];

    $vendas[] = $row;
}
die(json_encode($vendas));
