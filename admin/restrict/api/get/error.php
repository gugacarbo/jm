<?php
//header('Content-Type: application/xls');
//header('Content-Disposition: attachment; filename=info.xls');

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 4) {
    header("Location: ../index.php");
    die(json_encode(array('status' => 403, 'message' => 'Forbidden')));
}

include_once "../../../api/config/db_connect.php";



class errorlog extends dbConnect
{

    public function __construct()
    {
        $mysqli = $this->connect();
        $data   = array();
        $stmt = $mysqli->prepare("SELECT * FROM error_log");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $row["message"] = json_decode($row["message"]);
            $row["message"]->server = json_decode($row["message"]->server);
            $data[] = $row;
        }
        die(json_encode($data));
    }
}


$errorlog = new errorlog();
