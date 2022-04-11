<?php
error_reporting(0);
ini_set('display_errors', 0);
include 'db_connect.php';

//verificar se ha get min, max, cat, order e text com isset separadamente
$min = isset($_GET['min']) ? $_GET['min'] : 0;
$max = isset($_GET['max']) ? $_GET['max'] : 2000;
$cat = isset($_GET['cat']) ? $_GET['cat'] : 0;
$order = isset($_GET['order']) ? $_GET['order'] : 'price DESC';
$text = isset($_GET['text']) ? $_GET['text'] : '';

$sql = "SELECT id FROM products WHERE price BETWEEN $min AND $max";

if ($cat != 0) {
    $sql .= " AND category = '$cat'";
}

if ($text != '') {
    $sql .= " AND name LIKE '%$text%'";
}

$sql .= " ORDER BY $order";

$result = $mysqli->query($sql);
$mysqli->close();
if ($result->num_rows) {
    $products = array();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row["id"];
    }
    die(json_encode($products,  JSON_UNESCAPED_UNICODE));
} else {
    die(json_encode(array()));
}
