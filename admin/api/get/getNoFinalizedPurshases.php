<?php
header('Content-Type: application/json; charset=utf-8');

if(isset($_GET['get'])){
    die((getNoFinalizedPurchases()));
}

function getNoFinalizedPurchases()
{
    include "../config/db_connect.php";
    $sql = "SELECT * FROM checkout_data WHERE payload = '{}' AND NOW() >= DATE_ADD(buy_date, INTERVAL 1 DAY)";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row["buyer"]= json_decode($row["buyer"]);
            $row["payload"]= json_decode($row["payload"]);
            $row["products"]= json_decode($row["products"]);
            $data[] = $row;
        }
        return (json_encode($data));
    }else{
        return (json_encode(array()));
    }
}
