<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');


if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM".$_SERVER['REMOTE_ADDR']));
    session_start();
}



if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])  || ($_SESSION['admin']) < 1) {
    die(json_encode(array('status' => 403)));
}


include_once '../config/db_connect.php';

class clients extends dbConnect
{
    public function __construct()
    {
        $mysqli = $this->connect();

        $stmt = $mysqli->prepare("SELECT * FROM client");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return(($result->fetch_all(MYSQLI_ASSOC)));
    }
}

die(json_encode((new clients())->__construct()));