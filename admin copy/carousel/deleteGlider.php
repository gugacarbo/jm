<?php
if(isset($_GET["id"])){
    include "../db_connect.php";
    $id = $_GET["id"];
    $stmt = "DELETE FROM carousel WHERE id = ?";
    $stmt = $mysqli->prepare($stmt);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}