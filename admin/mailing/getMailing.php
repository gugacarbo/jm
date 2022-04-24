<?php

include "../db_connect.php";
$stmt = $mysqli->prepare("SELECT * FROM newsletter");
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
die(json_encode($result->fetch_all(MYSQLI_ASSOC)));