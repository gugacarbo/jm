<?php
header('Content-Type: application/json; charset=utf-8');

//get banner on db by name
if(isset($_GET['name'])){
    include '../config/db_connect.php';
    $name  = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['name']);
    $name = $_GET['name'];
    $stmt = $mysqli->prepare("SELECT * FROM banners WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $itens = $result->fetch_assoc();
    $stmt->close();
    $ret["status"] = 200;

    $ret["id"] = $itens['id'];
    $ret["name"] = $itens["name"];
    $ret["images"] = json_decode($itens["images"]);

    die(json_encode($ret));
}
