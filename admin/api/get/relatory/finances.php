<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');

if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM".$_SERVER['REMOTE_ADDR']));

    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 1) {
    die(json_encode(array('status' => 403)));
}



include_once '../../config/db_connect.php';

class finances extends dbConnect
{
    public function __construct()
    {
    }
    public function getFinances()
    {
        $mysqli = $this->connect();

        $stmt = $mysqli->prepare("SELECT invoicing,canceled FROM ds_relatory");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $financesData = array();
        $financesData['totalInvoicing'] = 0;

        while ($row = $result->fetch_assoc()) {
            $financesData['totalInvoicing'] += ($row['invoicing'] );
        }


        $stmt = $mysqli->prepare("SELECT totalAmount FROM vendas WHERE internalStatus = 3");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        while ($row = $result->fetch_assoc()) {
            $financesData['totalInvoicing'] += ($row['totalAmount'] );
        }

        $stmt = $mysqli->prepare("SELECT * FROM ds_relatory ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $row = $result->fetch_assoc();
        $row['indicators'] = json_decode($row['indicators']);
        $financesData['av_ticket'] = $row['indicators']->av_ticket;
        $financesData['av_margin'] = $row['indicators']->av_margin;
        $financesData['av_cost'] = $row['indicators']->av_cost;
        $financesData['av_price'] = $row['indicators']->av_price;

        $thisMonth = date("Y-m-1");
        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE paymentDate >= '$thisMonth' AND (internalStatus = 3 OR internalStatus = 4)");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $montInvoicing = 0;
        $received = 0;
        $toReceive = 0;
        $listData = array();
        $monthNet = 0;

        while ($row = $result->fetch_assoc()) {

            $montInvoicing += $row['totalAmount'];
            $payload = json_decode($row['rawPayload']);
            $row['rawPayload'] = $payload;
            $listData[] = $row;

            $monthNet += $payload->netAmount - $row['totalCost'];

            if (strtotime($payload->escrowEndDate) < strtotime(date("Y-m-d"))) {
                $received += $payload->grossAmount;
            } else {
                $toReceive += $payload->grossAmount;
            }
        }
        $financesData['monthInvoicing'] = $montInvoicing;

        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE paymentDate <= '$thisMonth' AND internalStatus = 3");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $payload = json_decode($row['rawPayload']);
            if (!isset(($payload->escrowEndDate))) {
                echo $payload->reference;
            }

            if (strtotime($payload->escrowEndDate) < strtotime(date("Y-m-d"))) {
            } else {
                $toReceive += $payload->grossAmount;
            }
        }
        $financesData['monthNet'] = $monthNet;
        $financesData['received'] = $received;
        $financesData['toReceive'] = $toReceive;
        $financesData['listData'] = $listData;

        die(json_encode($financesData));
    }
}

$finances = new finances();

$finances->getFinances();
