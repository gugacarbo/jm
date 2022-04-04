<?php
include 'db_connect.php';

//get banner on db by name
if(isset($_GET['name'])){
    $name = $_GET['name'];
    $res = $mysqli->query("SELECT * FROM banners WHERE name = '$name'");
    $itens = $res->fetch_all(MYSQLI_ASSOC);
    $ret["id"] = $itens[0]['id'];
    $ret["name"] = $itens[0]["name"];
    $ret["images"] = $itens[0]["images"];
    echo json_encode($ret);
}
