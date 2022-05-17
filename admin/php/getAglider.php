<?php

if(isset($_GET["id"])){
    include "db_connect.php";
    $id = $_GET["id"];
    $stmt = "SELECT * FROM carousel WHERE category = ?";
    $stmt = $mysqli->prepare($stmt);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    die(json_encode($result->fetch_assoc()));
}