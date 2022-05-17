<?php
header('Content-Type: application/json; charset=utf-8');


$params = array();
$labels = "";

$sql = "SELECT * FROM products WHERE 1 = 1 ";

$min = 0;
$max = 20000;

if (isset($_GET['min']) && is_numeric($_GET['min'])) {
    $min = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['min']);
}
if (is_numeric($_GET['max']) &&  isset($_GET['max'])) {
    $max = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['max']);
}

if (isset($_GET['cat']) && is_numeric($_GET['cat']) && ($_GET['cat']) > 0) {
    $cat = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['cat']);
    $sql .= " AND category = ?";
    array_push($params, $cat);
    $labels .= "s";
}

if (isset($_GET['text']) && strlen($_GET['text']) > 0 && strlen($_GET['text']) < 100 && $_GET["text"] != "") {
    $text = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['text']);
    $sql .= " AND name LIKE ?";
    array_push($params, "%$text%");
    $labels .= "s";
}

if (isset($_GET['order']) && $_GET['order'] == "price DESC") {
    $order = "price DESC";
} else {
    $order = "price ASC";
}
$sql .= " ORDER BY $order";

include 'db_connect.php';

$stmt = $mysqli->prepare($sql);
if ($labels != ""){
    $stmt->bind_param($labels, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$maxPrice = 0;
if ($result->num_rows) {
    $products = array();
    while ($row = $result->fetch_assoc()) {
        unset($row['cost']);
        if ($row['price'] > $maxPrice) {
            $maxPrice = $row['price'];
        }
        if($row['price'] > $min && $row['price'] < $max) {
            $products[] = $row;
        }
        
    }
    if(count($products) > 0) {
        $products[0]["maxPrice"] = $maxPrice;
    } else {
        $products["maxPrice"] = $maxPrice;
    }
    die(json_encode($products,  JSON_UNESCAPED_UNICODE));
} else {
    die(json_encode(array()));
}
