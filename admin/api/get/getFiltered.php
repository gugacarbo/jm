<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    die(json_encode(array('status' => 403)));
}


include_once '../config/db_connect.php';

class filter extends dbConnect
{
    public function __construct()
    {
    }
    public function get()
    {
        $mysqli = $this->connect();

        $params = array();
        $labels = "";

        $sql = "SELECT * FROM products WHERE 1 = 1 ";


        if (isset($_GET['promo']) && is_numeric($_GET['promo']) &&   $_GET['promo'] == 1) {
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
            return ($products);
        } else {
            return (array());
        }
    }
}


$filter = new filter();
die(json_encode($filter->get()));
