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

class glider extends dbConnect
{

    public function __construct()
    {
    }
    public function getAGlider($id)
    {
        $mysqli = $this->connect();

        $stmt = "SELECT * FROM carousel WHERE category = ?";
        $stmt = $mysqli->prepare($stmt);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $row = $result->fetch_assoc();
        if ($row) {
            $row["select"] = json_decode($row["select"]);
            return(($row));
        } else {
            return (array('status' => 404));
        }
    }
}


if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $glider = new glider();
    die(json_encode($glider->getAGlider($id)));
} else {
    die(json_encode(array("status" => 400, "message" => "Bad Request")));
}
