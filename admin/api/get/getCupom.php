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

class cupom extends dbConnect
{
    public function __construct()
    {
    }
    public function getCupom($id)
    {
        $mysqli =  $this->connect();
        $stmt = "SELECT * FROM cupom WHERE id = ?";
        $stmt = $mysqli->prepare($stmt);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $cupom = $result->fetch_assoc();
        return ((array("status" => 200, "cupom" => $cupom)));
    }
}

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $cupom = new cupom();
    die(json_encode($cupom->getCupom($id)));
} else {
    die(json_encode(array("status" => 400)));
}
