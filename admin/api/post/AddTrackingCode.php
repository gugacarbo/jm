<?php
header('Content-Type: application/json; charset=utf-8');


if(isset($_POST['id']) && isset($_POST['code'])){

    $id = $_POST['id'];
    $code = $_POST['code'];

    include '../config/db_connect.php';

    $stmt = $mysqli->prepare("UPDATE vendas SET trackingCode = ? WHERE id = ?");
    $stmt->bind_param('si', $code, $id);
    $stmt->execute();
    $stmt->close();

    die(json_encode(array('status' => 200)));
}