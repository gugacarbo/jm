<?php
error_reporting(0);
ini_set('display_errors', 0);


$params = array();
$labels = "";

$sql = "SELECT * FROM products WHERE price BETWEEN ? AND ?";

if(isset($_GET['min']) && is_numeric($_GET['min'])){
    $min = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['min']);
}else{
    $min = 0;
}
array_push($params, $min);
$labels .= "s";


if(is_numeric($_GET['max']) &&  isset($_GET['max'])){
    $max = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['max']);
}else{
    $max = 3000;
}
array_push($params, $max);
$labels .= "s";


if (isset($_GET['cat']) && is_numeric($_GET['cat']) && ($_GET['cat']) > 0) {
    $cat = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['cat']);
    $sql .= " AND category = ?";
    array_push($params, $cat);
    $labels .= "s";
}

if(isset($_GET['text'])){
    $text = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['text']);
    $sql .= " AND name LIKE ?";
    array_push($params, "%$text%");
    $labels .= "s";

}

if(isset($_GET['order']) && $_GET['order'] == "price DESC"){
    $order = "price DESC";
}else{
    $order = "price ASC";
}
$sql .= " ORDER BY $order";

include 'db_connect.php';

$stmt = $mysqli->prepare($sql);
$stmt->bind_param($labels, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();


if ($result->num_rows) {
    $products = array();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row["id"];
    }
    die(json_encode($products,  JSON_UNESCAPED_UNICODE));
} else {
    die(json_encode(array()));
}
