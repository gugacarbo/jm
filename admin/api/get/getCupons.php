<?php

header('Content-Type: application/json; charset=utf-8');

include "../config/db_connect.php";
$stmt = "SELECT * FROM cupom";
$stmt = $mysqli->prepare($stmt);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
while ($row = $result->fetch_assoc()) {
    $row["clientIds"] = json_decode($row["clientIds"]);
    $cupons[] = $row;
}
die(json_encode(array("status" => 200, "cupons" => $cupons)));
