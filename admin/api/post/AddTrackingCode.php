<?php
header('Content-Type: application/json; charset=utf-8');


if(isset($_POST['id']) && isset($_POST['code'])){

    $id = $_POST['id'];
    $code = $_POST['code'];

    include '../config/db_connect.php';

    $stmt = $mysqli->prepare("SELECT a.id, a.*, b.* FROM vendas as A INNER JOIN client as B ON b.id = a.clientId and a.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();
    $result = $res->fetch_assoc();

    include "mailTracking.php";
    
    $load = json_decode($result['rawPayload']);
    $load = json_encode($load);
    $load = json_decode($load);
    $load->trackingCode = $code;

    //print_r($load);

    sendMail($load);



    $stmt = $mysqli->prepare("UPDATE vendas SET trackingCode = ? WHERE id = ?");
    $stmt->bind_param('si', $code, $id);
    $stmt->execute();
    $stmt->close();

    die(json_encode(array('status' => 200)));
}