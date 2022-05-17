<?php

if(isset($_GET['get'])){
    die((getPurchases()));
}

function getPurchases()
{
    include "db_connect.php";
    $sql = "SELECT * from checkout_data WHERE payload = '{}'";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return (json_encode($data));
    }else{
        return (json_encode(array()));
    }
}
