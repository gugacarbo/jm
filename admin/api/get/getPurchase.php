<?php
header('Content-Type: application/json; charset=utf-8');


if(isset($_GET['Bid'])){
    include '../config/db_connect.php';
    include 'getProdById.php';

    $id = $_GET['Bid'];
    $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $clientId = $row['clientId'];
    $purchase = ($row);
    $raw = (json_decode($row["rawPayload"]));   
    if($raw -> itemCount > 1){
        $products = ($raw->items->item);
    }else{
        $products = $raw->items;
    }

    $aProducts = array();
    
    foreach($products as $product){
        $p = getById($product->id);
        $p["amount"] = $product->amount;
        $p["quantity"] = $product->quantity;
        $p["description"] = $product->description;
        $p["description"] = $product->description;

        $aProducts[] = $p;
    }

 



    $stmt = $mysqli->prepare("SELECT * FROM client WHERE id = ?");
    $stmt->bind_param('i', $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $client = ($row);

    die(json_encode(array('status' => 200, 'purchase' => $purchase, 'client' => $client, 'products' => $aProducts)));
}else{
    die(json_encode(array("status" => 400, "message" => "Bad Request")));
}