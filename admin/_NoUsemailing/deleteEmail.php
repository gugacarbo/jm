<?php

if (isset($_GET["id"])) {
    include "../db_connect.php";

    $id = $_GET["id"];
    $stmt = $mysqli->prepare("DELETE FROM newsletter WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
