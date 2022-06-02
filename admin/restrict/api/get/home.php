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



class home extends dbConnect
{
    private $data;

    public function __construct()
    {
        $mysqli = $this->connect();


        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE internalStatus = 3");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $data = array();
        $lucroMes = 0;
        $csvRow = array();
        $futuro = 0;
        while ($row = $result->fetch_assoc()) {
            $payload = json_decode($row['rawPayload'], true);
            $totalCost = (float) $row["totalCost"];
            $netAmount = (float) $payload["netAmount"] - $totalCost;
            if (strtotime($payload['escrowEndDate']) > strtotime(date("Y-m-5"))) {
                $futuro += (float) $netAmount;
            }else{
                $lucroMes += (float) $netAmount;
            }
        }

        $lucroMes = $lucroMes * 0.15;
        $futuro = $futuro * 0.15;

        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE internalStatus = 9");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $data = array();
        $canceladas = 0;
        while ($row = $result->fetch_assoc()) {
            $payload = json_decode($row['rawPayload'], true);
            $totalCost = (float) $row["totalCost"];
            $netAmount = (float) $payload["netAmount"] - $totalCost;
            $lucroMes += (float) $netAmount;
            $canceladas += (float) $netAmount;
        }

        $canceladas = $canceladas * 0.15;



        return (array(
            'status' => 200,
            "lucroMes" => $lucroMes,
            "futuro" => $futuro,
            "canceladas" => $canceladas
        ));
    }
}

$home = new home();
die(json_encode($home->__construct()));
