<?php

if(isset($_GET['id']) && isset($_GET['code'])){

    $id = $_GET['id'];
    $code = $_GET['code'];

    include '../db_connect.php';

    $stmt = $mysqli->prepare("UPDATE vendas SET trackingCode = ? WHERE id = ?");
    $stmt->bind_param('si', $code, $id);
    $stmt->execute();
    $stmt->close();

    die(json_encode(array('status' => '200')));
}