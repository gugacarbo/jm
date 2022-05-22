<?php

header('Content-Type: application/json; charset=utf-8');

if (isset($_GET["id"])) {
    include "../config/db_connect.php";
    $stmt = "SELECT * FROM cupom WHERE id = ?";
    $stmt = $mysqli->prepare($stmt);
    $stmt->bind_param("i", $_GET["id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $cupom = $result->fetch_assoc();

    die(json_encode(array("status" => 200, "cupom" => $cupom)));
} else {
    die(json_encode(array("status" => 400)));
}
