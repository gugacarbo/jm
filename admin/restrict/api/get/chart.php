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



class chart extends dbConnect
{

    public function __construct()
    {
    }

    public function getChart()
    {
        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT * FROM ds_relatory");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $chartDataInvoicing = array();
        $chartDataCanceled =  array();
        while ($row = $result->fetch_assoc()) {
            $chartDataInvoicing[date('Y', strtotime($row['date']))][date('m', strtotime($row['date']))][] = $row['netAmountDs'];
            $chartDataCanceled[date('Y', strtotime($row['date']))][date('m', strtotime($row['date']))][] = $row['canceled'];
        }


        $chartInv = [];
        $chartInvLabel = [];
        $chartCan = [];
        $chartCanLabel = [];
        

        foreach ($chartDataInvoicing as $Y => $Year) {
            foreach ($Year as $m => $month) {
                $chartInv[] = array_sum($month);
                $chartInvLabel[] = date("Y-m", strtotime($Y . "-" . $m));
            }
        }
        foreach ($chartDataCanceled as $Y => $Year) {
            foreach ($Year as $m => $month) {
                $chartCan[] = array_sum($month);
                $chartCanLabel[] = date("Y-m", strtotime($Y . "-" . $m));
            }
        }
        $invChart = array(
            'data' => $chartInv,
            'labels' => $chartInvLabel,
        );

        $canChart = array(
            'data' => $chartCan,
            'labels' => $chartCanLabel,
        );


        die(json_encode(array('status' => 200, 'message' => 'Success', 'data' => array('invoicing' => $invChart, 'canceled' => $canChart))));
    }
}

$chart = new chart();
$chart->getChart();
