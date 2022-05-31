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

class unfinalized extends dbConnect
{

    public function __construct()
    {
        $mysqli = $this->connect();

        $sql = "SELECT * FROM checkout_data WHERE payload = '{}' AND NOW() >= DATE_ADD(buy_date, INTERVAL 1 DAY)";
        $stmt = $mysqli->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row["buyer"] = json_decode($row["buyer"]);
                $row["payload"] = json_decode($row["payload"]);
                $row["products"] = json_decode($row["products"]);
                $data[] = $row;
            }
            return (($data));
        } else {
            return ((array()));
        }
    }
}

die(json_encode((new unfinalized())->__construct()));
