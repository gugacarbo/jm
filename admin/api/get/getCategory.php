<?php
//include db and take category from db and returns as json and close connection
header('Content-Type: application/json; charset=utf-8');

include '../config/db_connect.php';
$stmt = $mysqli->prepare("SELECT * FROM categories");
$stmt->execute();
$result_ = $stmt->get_result();
if ($result_->num_rows > 0) {
    $data = array();
    while ($row = $result_->fetch_assoc()) {
        $stmt = $mysqli->prepare("SELECT * FROM products WHERE category = ?");
        $stmt->bind_param("i", $row['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $row['numProds'] = $result->num_rows;
        $data[] = $row;
    }
}
$mysqli->close();
die(json_encode($data, JSON_UNESCAPED_UNICODE));
