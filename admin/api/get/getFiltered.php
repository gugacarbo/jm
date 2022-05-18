<?php
header('Content-Type: application/json; charset=utf-8');

error_reporting(1);
ini_set('display_errors', 1);


$params = array();
$labels = "";

$sql = "SELECT * FROM products WHERE price BETWEEN ? AND ?";

$min = 0;
if (isset($_GET['min']) && is_numeric($_GET['min'])) {
    $min = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['min']);
}

array_push($params, $min);
$labels .= "s";

$max = 3000;
if (is_numeric($_GET['max']) &&  isset($_GET['max'])) {
    $max = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['max']);
}
array_push($params, $max);
$labels .= "s";

if (is_numeric($_GET['promo']) &&  isset($_GET['promo']) && $_GET['promo'] == 1) {
    $sql .= " AND promo > 0";
}

if (isset($_GET['cat']) && is_numeric($_GET['cat']) && ($_GET['cat']) >= 0) {
    $cat = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['cat']);
    $sql .= " AND category = ?";
    array_push($params, $cat);
    $labels .= "s";
}

if (isset($_GET['text'])) {
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

$sql .= " AND totalQuantity > 0 ORDER BY $order";

include '../config/db_connect.php';
$stmt = $mysqli->prepare($sql);
$stmt->bind_param($labels, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();


if ($result->num_rows) {
    $products = array();
    while ($row = $result->fetch_assoc()) {
        unset($row['cost']);
        $products[] = $row;
    }
    die(json_encode($products,  JSON_UNESCAPED_UNICODE));
} else {
    die(json_encode(array()));
}
