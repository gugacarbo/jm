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

class categories extends dbConnect
{

    public function __construct()
    {
        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT * FROM categories");
        $stmt->execute();
        $result_ = $stmt->get_result();
        if ($result_->num_rows > 0) {
            $data = array();
            while ($row = $result_->fetch_assoc()) {
                $stmt = $mysqli->prepare("SELECT * FROM products WHERE category = ?");
                $stmt->bind_param("i", $row['id']);
                $stmt->execute();
                $result = $stmt->get_result();

                $row['numProds'] = $result->num_rows;
                $data[] = $row;
            }
            return $data;
        } else {
            return (array('status' => 403));
        }
    }
}

die(json_encode((new categories())->__construct()));
