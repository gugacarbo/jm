<?php

error_reporting(1);
ini_set('display_errors', 1);


$params = "";
$labels = "";

$sql = "SELECT * FROM products WHERE";

$min = 0;

$orderBy = "";

if ($_GET["text"]) {
    $text = "%" . $_GET["text"] . "%";
    $sql .= " name LIKE ?";
    $labels = "s";
    $params = $text;
} else {
    $sql .= " 1=1 ";
}

if ($_GET["filter"]) {
    $filter = $_GET["filter"];

    if ($_GET["order"]) {
        $orderBy = $_GET["order"] == "true" ? "DESC" : "ASC";
    } else {
        $orderBy = "ASC";
    }


    switch ($filter) {
        case "name":
            $sql .= " ORDER BY SUBSTR( name, 1, 1 ) " . $orderBy;
            break;
        case "price":
            $sql .= " ORDER BY price " . $orderBy;
            break;
        case "promo":
            $sql .= " ORDER BY 1-price/promo " . $orderBy;
            break;
        case "cat":
            $sql .= " ORDER BY category " . $orderBy;
            break;
        case "qtd":
            $sql .= " ORDER BY totalQuantity " . $orderBy;
            break;
        case "id":
            $sql .= " ORDER BY id " . $orderBy;
            break;
    }
}



include 'db_connect.php';
$stmt = $mysqli->prepare($sql);

if($labels != ""){
    $stmt->bind_param($labels, $params);
}



$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
if ($result->num_rows) {
    $products = array();
    while ($row = $result->fetch_assoc()) {

        $stmt = $mysqli->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->bind_param("i", $row["category"]);
        $stmt->execute();
        $result2 = $stmt->get_result();
        $stmt->close();
        $category = $result2->fetch_assoc();
        $row["categoryId"] = $row["category"];
        $row["category"] = $category["name"];
        $products[] = $row;
    }
    die(json_encode($products,  JSON_UNESCAPED_UNICODE));
} else {
    die(json_encode(array()));
}
