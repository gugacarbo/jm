<?php

include 'db_connect.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $res = $mysqli->query("SELECT * FROM products WHERE id = $id");
    $itens = $res->fetch_all(MYSQLI_ASSOC);
    $materialId = $itens[0]['material'];

    $mat = $mysqli->query("SELECT * FROM material WHERE id = $materialId");

    $material_R = $mat->fetch_all(MYSQLI_ASSOC);

    $ret["id"] = $itens[0]['id'];
    $ret["name"] = $itens[0]["name"];
    $ret["price"] = $itens[0]["price"];
    $ret["promo"] = $itens[0]["promo"];
    $ret["material"] = $material_R[0]["name"];
    $ret["weight"] = $itens[0]["weight"];
    $ret["description"] = $itens[0]["description"];
    $ret["imgs"] = ($itens[0]["imgs"]);
    $ret["options"] = ($itens[0]["options"]);

    echo json_encode($ret);
}
$mysqli->close();

?>