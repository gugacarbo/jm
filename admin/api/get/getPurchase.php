<?php
header('Content-Type: application/json; charset=utf-8');


define('TIMEZONE', 'America/Sao_Paulo');
date_default_timezone_set(TIMEZONE);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    die(json_encode(array('status' => 403)));
}

include_once '../config/db_connect.php';
include 'getProdById.php';

class Purchase extends dbConnect
{

    public function __construct()
    {
    }
    public function get($id)
    {
        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT a.*, b.* FROM vendas as a INNER JOIN client as b ON b.id = a.clientId WHERE a.id = ?");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $purchase = ($row);

        $raw = (json_decode($row["rawPayload"]));
        if ($raw->itemCount > 1) {
            $products = ($raw->items->item);
        } else {
            $products = $raw->items;
        }
        //print_r($purchase);

        $aProducts = array();
        $getProd = new Prods();
        foreach ($products as $product) {
            $p = $getProd->getById($product->id);
            $p["amount"] = $product->amount;
            $p["quantity"] = $product->quantity;
            $p["description"] = $product->description;
            $p["description"] = $product->description;

            $aProducts[] = $p;
        }
         return (array(
            "purchase" => $purchase,
            "products" => $aProducts
        ));
    }
}



if (isset($_GET['Bid']) && is_numeric($_GET['Bid'])) {
    $id = $_GET['Bid'];
    $purchase = new Purchase();
    die(json_encode($purchase->get($id)));
} else {
    die(json_encode(array('status' => 400)));
}
