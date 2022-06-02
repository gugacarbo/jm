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

class freeShipping extends dbConnect
{
    public function __construct()
    {
        $mysqli = $this->connect();

        $stmt = $mysqli->prepare("SELECT value FROM generalconfig WHERE config = 'freteGratis'");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $result = $result->fetch_assoc();
        return(json_decode($result['value']));
    }
}

die(json_encode((new freeShipping())->__construct()));