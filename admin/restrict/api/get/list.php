<?php
//header('Content-Type: application/xls');
//header('Content-Disposition: attachment; filename=info.xls');

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM".$_SERVER['REMOTE_ADDR']));
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 4) {
    //header("Location: ../index.php");
    die(json_encode(array('status' => 403, 'message' => 'Forbidden')));
}

include_once "../../../api/config/db_connect.php";



class getList extends dbConnect
{
    private $mes = [], $futuro = [], $canceladas = [], $totals = [];

    public function __construct()
    {
        $this->getMes();
        $this->getCanceladasMes();
        $this->getTotals();
        die(json_encode(array(
            "mes" => $this->mes,
            "futuro" => $this->futuro,
            "canceladas" => $this->canceladas,
            "totals" => $this->totals
        )));
    }

    public function getMes()
    {
        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE internalStatus = 3");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $payload = json_decode($row['rawPayload'], true);
            $row['rawPayload'] = $payload;
            if (strtotime($payload['escrowEndDate']) > strtotime(date("Y-m-d"))) {
                $this->futuro[] = $row;
            } else {
                $this->mes[] = $row;
            }
        }
    }

    public function getCanceladasMes()
    {
        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE internalStatus = 8");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $payload = json_decode($row['rawPayload'], true);
            $row['rawPayload'] = $payload;
            $this->canceladas[] = $row;
        }
    }

    
    public function getTotals()
    {
        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT * FROM ds_relatory");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $this->totals[] = $row;
        }
    }
}

$getList = new getList();

//die(json_encode($getList->__construct()));
