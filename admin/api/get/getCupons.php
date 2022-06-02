<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 1) {
    die(json_encode(array('status' => 403)));
}


include_once '../config/db_connect.php';

class cupons extends dbConnect
{
    public function __construct()
    {
        $mysqli = $this->connect();

        $stmt = $mysqli->prepare("SELECT * FROM cupom");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $row['clientIds'] = json_decode($row['clientIds']);
            $rows[] = $row;
        }
        
        return(array(
            'status' => 200,
            'cupons' => $rows
        ));
    }
}

die(json_encode((new cupons())->__construct()));