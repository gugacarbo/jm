<?php
header('Content-Type: application/json; charset=utf-8');

if (isset($_POST["id"])) {
    $id = $_POST["id"];
    $id = intval($id);
    include '../config/db_connect.php';

    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        die(json_encode(array("status" => 200)));
    } else {
        $stmt->close();
        die(json_encode(array("status" => 500, "message" => "Bad Request")));
    }
} else {
    die(json_encode(array("status" => 400, "message" => "Bad Request")));
}
