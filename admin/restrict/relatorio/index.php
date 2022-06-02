<?php
//header('Content-Type: application/xls');
//header('Content-Disposition: attachment; filename=info.xls');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 4) {
    header("Location: ../index.php");
    die(json_encode(array('status' => 403, 'message' => 'Forbidden')));
}

include_once "../../api/config/db_connect.php";



class relatorio extends dbConnect
{
    private $data;

    public function __construct()
    {
    }
    public function json()
    {
        header('Content-Type: application/json; charset=utf-8');
        $this->getData();
        return (($this->data));
    }
    private function getData()
    {
        $mysqli = $this->connect();

        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE totalCost = 0");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $stmt = $mysqli->prepare("UPDATE vendas SET totalCost = 25 WHERE id = ?");
            $stmt->bind_param("i", $row['id']);
            $stmt->execute();
            $stmt->close();
        }


        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE internalStatus = 3");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $mysqli->close();
        $data = array();
        $lucroTotal = 0;
        $csvRow = array();
        while ($row = $result->fetch_assoc()) {

            $payload = json_decode($row['rawPayload'], true);
            $totalAmount = (float) $payload["grossAmount"];
            $taxaPagseguro = (float) $payload["creditorFees"]["intermediationFeeAmount"];
            $totalCost = (float) $row["totalCost"];
            $shippingCost = (float)$payload["shipping"]["cost"];

            $netAmount = (float) $payload["netAmount"] - $totalCost;
            $lucroTotal += (float) $netAmount;


            $csvRow[] = array(
                $row['id'],
                $payload['sender']['email'],
                $row['reference'],
                $row['buyDate'],
                $row['paymentDate'],
                (float)$payload['itemCount'],
                (float)$totalAmount,
                (float)$taxaPagseguro,
                (float)$shippingCost,
                (float)$payload['extraAmount'],
                (float)$totalCost,
                (float)$netAmount,
                $payload['escrowEndDate'],
            );
        }
        $this->data = $csvRow;
    }


    public function vendasCsv()
    {
        $this->getData();
        gc_collect_cycles();
        file_exists("arquivo.csv") ? unlink("arquivo.csv")  : null;
        $fp = fopen('arquivo.csv', 'w');
        foreach ($this->data as $fields) {
            fputcsv($fp, $fields, ';');
        }
        fclose($fp);
        return json_encode(array('status' => 200, 'message' => 'OK'));
    }
}


$relatorio = new relatorio();

if(isset($_GET['action']))
switch($_GET['action']){
    case 'json':
        die(json_encode($relatorio->json()));
        break;
    case 'vendasCsv':
        die($relatorio->vendasCsv());
        break;
}



