<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');


if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM".$_SERVER['REMOTE_ADDR']));

    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 1) {
    die(json_encode(array('status' => 403)));
}



include_once '../../config/db_connect.php';

class orders extends dbConnect
{
    public function __construct()
    {
    }
    public function getFinances()
    {
        $mysqli = $this->connect();
        $ordersData = [];
        $ordersChart = array();
        
        $thisMonth = date("Y-m-1");
        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE buyDate >= '$thisMonth'");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        while ($row = $result->fetch_assoc()) {
            $ordersData['ordersList'][] = $row;
            isset($ordersChart[$row['status']]) ? $ordersChart[$row['status']] ++ : $ordersChart[$row['status']] = 1;
        }

        $ordersData['ordersChart'] = $ordersChart;

        die(json_encode($ordersData));
    }
}

$orders = new orders();

$orders->getFinances();
