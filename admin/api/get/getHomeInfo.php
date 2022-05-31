<?php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    die(json_encode(array('status' => 403)));
}

include_once '../config/db_connect.php';

class GET_HomeInfo extends dbConnect
{
    private $info;
    public function __construct()
    {
        $mysqli = $this->connect();

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM vendas WHERE status >= 3 AND status <= 4");
        $stmt->execute();
        $stmt->bind_result($totalAprovadas);
        $stmt->fetch();

        $stmt->close();
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM vendas WHERE status = 6  || status = 8");
        $stmt->execute();
        $stmt->bind_result($totalCanceladas);
        $stmt->fetch();
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM vendas WHERE status >=3 AND status <= 4 AND trackingCode = ''");
        $stmt->execute();
        $stmt->bind_result($totalAguardandoEnvio);
        $stmt->fetch();
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM vendas WHERE status = 1 || status = 2");
        $stmt->execute();
        $stmt->bind_result($totalAguardandoPagamento);
        $stmt->fetch();
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM checkout_data WHERE payload = '{}' AND NOW() >= DATE_ADD(buy_date, INTERVAL 1 DAY)");
        $stmt->execute();
        $stmt->bind_result($totalNaoPagos);
        $stmt->fetch();
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM nofinalizedpurchases WHERE 1");
        $stmt->execute();
        $stmt->bind_result($totalNaoPagos2);
        $stmt->fetch();
        $stmt->close();

        $totalNaoPagos = $totalNaoPagos + $totalNaoPagos2;

        $stmt = $mysqli->prepare("SELECT rate FROM rating");
        $stmt->execute();
        $result = $stmt->get_result();
        $totalRows = $result->num_rows;
        $stmt->close();
        $totalRate = 0;
        foreach ($result->fetch_all(MYSQLI_ASSOC) as $rate) {
            $totalRate = $totalRate + $rate['rate'];
        }
        $rating = $totalRate / $totalRows;


        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM vendas WHERE status = 5 || status = 9");
        $stmt->execute();
        $stmt->bind_result($totalCanceladas30);
        $stmt->fetch();
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM visitas ");
        $stmt->execute();
        $stmt->bind_result($visitas);
        $stmt->fetch();
        $stmt->close();


            $this->info = ((array(
            'status' => 200, 'Aprovadas' => $totalAprovadas,
            'Canceladas' => $totalCanceladas, 'AguardandoEnvio' => $totalAguardandoEnvio,
            'NaoPagos' => $totalNaoPagos, 'AguardandoPagamento' => $totalAguardandoPagamento,
            'Nps' => $rating,
            "canceling" => $totalCanceladas30,
            "visitas" => $visitas
        )));
    }
    public function get(){
        return $this->info;
    }
}


if (isset($_SESSION['user']) && isset($_SESSION['admin'])) {
    $getHomeInfo = new GET_HomeInfo();
    die (json_encode($getHomeInfo->get()));
} else {
    die (json_encode(array('status' => 403)));
}