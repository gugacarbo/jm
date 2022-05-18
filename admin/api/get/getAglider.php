<?php

header('Content-Type: application/json; charset=utf-8');

if(isset($_GET["id"])){
    include "../config/db_connect.php";
    $id = $_GET["id"];
    $stmt = "SELECT * FROM carousel WHERE category = ?";
    $stmt = $mysqli->prepare($stmt);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $row = $result->fetch_assoc();
    $row["select"] = json_decode($row["select"]);
    die(json_encode($row));
}else{
    die(json_encode(array("status" => 400, "message" => "Bad Request")));
}
