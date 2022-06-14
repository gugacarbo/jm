<?php

header('Content-Type: application/json; charset=utf-8');


if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM".$_SERVER['REMOTE_ADDR']));
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 1) {
    die(json_encode(array('status' => 403)));
}


include_once '../config/db_connect.php';
include 'getProdById.php';

class Products extends dbConnect
{

    public function __construct()
    {
        $mysqli = $this->connect();

        $params = "";
        $labels = "";

        $sql = "SELECT * FROM products WHERE deleted = 0 ";


        $min = 0;
        $orderBy = "";

        if ($_GET["text"]) {
            $text = "%" . $_GET["text"] . "%";
            $sql .= " AND name LIKE ?";
            $labels = "s";
            $params = $text;
        } else {
            $sql .= " ";
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
                case "sold":
                    $sql .= " ORDER BY sold " . $orderBy;
                    break;
                case "id":
                    $sql .= " ORDER BY id " . $orderBy;
                    break;
            }
        }



        $stmt = $mysqli->prepare($sql);

        if ($labels != "") {
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
            return(json_encode($products,  JSON_UNESCAPED_UNICODE));
        } else {
            return(json_encode(array()));
        }
    }
}


die(((new Products())->__construct()));