<?php
//header('Content-Type: application/csv');
//header('Content-Disposition: attachment; filename=arquivo.csv');


header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 4) {
    header("Location: ../index.php");
    die(json_encode(array('status' => 403, 'message' => 'Forbidden')));
}

include_once "../../../api/config/db_connect.php";



class getList extends dbConnect
{
    private $mes = [], $canceladas = [], $faturamento, $totalCancel, $lucroDs ,$fileName;

    public function __construct()
    { 
        $this->getMes();
        $this->getCanceladasMes();
    }



    public function generate()
    {
        gc_collect_cycles();
        $d = date("Y-m-d");
        $this->fileName = "Relatório".$d.".csv";
        $fp = fopen($this->fileName, 'w');

        $csvRow = array(
            "Id",
            "Data do Pagamento",
            "Nome do Cliente",
            "Quantidade de Produtos",
            "Valor Total",
            "Custo Total",
            "Descontos",
            "Frete",
            "Lucro da Venda",
            "Lucro da Venda DS (15%)",
        );
        fputcsv($fp, $csvRow, ';');

        foreach ($this->mes as $compra) {

            $csvRow = array(
                $compra['id'],
                $compra['paymentDate'],
                $compra['rawPayload']['sender']['name'],
                $compra['rawPayload']['itemCount'],
                $compra['totalAmount'],
                $compra['totalCost'] + $compra['rawPayload']['creditorFees']['intermediationFeeAmount'] + $compra['rawPayload']['creditorFees']['intermediationRateAmount'],
                $compra['rawPayload']['discountAmount'],
                $compra['rawPayload']['shipping']['cost'],
                ($compra['rawPayload']['netAmount'] - $compra['totalCost']),
                (($compra['rawPayload']['netAmount'] - $compra['totalCost']) * 0.15)
            );
            fputcsv($fp, $csvRow, ';');
        }

        fputcsv($fp, [' ', ' ', ' ', ' ', ' ', ' ', ' '], ';');
        fputcsv($fp, ['Canceladas', ' ', ' ', ' ', ' ', ' ', ' '], ';');

        $csvRow = array(
            "Id",
            "Data do Pagamento",
            "Nome do Cliente",
            "Quantidade de Produtos",
            "Valor Total",
            "Custo Total",
            "Descontos",
            "Frete",
            "Lucro da Venda",
            "Lucro da Venda DS (15%)",
        );
        fputcsv($fp, $csvRow, ';');

        foreach ($this->canceladas as $compra) {

            $csvRow = array(
                $compra['id'],
                $compra['paymentDate'],
                $compra['rawPayload']['sender']['name'],
                $compra['rawPayload']['itemCount'],
                $compra['totalAmount'],
                $compra['totalCost'] + $compra['rawPayload']['creditorFees']['intermediationFeeAmount'] + $compra['rawPayload']['creditorFees']['intermediationRateAmount'],
                $compra['rawPayload']['discountAmount'],
                $compra['rawPayload']['shipping']['cost'],
                ($compra['rawPayload']['netAmount'] - $compra['totalCost']),
                (($compra['rawPayload']['netAmount'] - $compra['totalCost']) * 0.15)
            );
            fputcsv($fp, $csvRow, ';');
        }

        fclose($fp);
        $this->setTotal();
        //$this->updateSales();
        die(json_encode(array('status' => 200, 'message' => 'Arquivo gerado com sucesso')));
    }





    public function getMes()
    {

        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE internalStatus = 3");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $fat = 0;
        $luc = 0;
        $lucDs = 0;

        while ($row = $result->fetch_assoc()) {
            $payload = json_decode($row['rawPayload'], true);
            $row['rawPayload'] = $payload;

            $grossAmount = (float) $payload["grossAmount"]; // * Valor da compra
            $totalCost = (float) $row["totalCost"] + floatval($payload['creditorFees']['intermediationRateAmount']) + floatval($payload['creditorFees']['intermediationFeeAmount']); // > Custo de Produto e Frete
            $netAmount = $grossAmount - $totalCost;

            if (strtotime($payload['escrowEndDate']) > strtotime(date("Y-m-d"))) {
            } else {
                $fat += $grossAmount;  //- Faturamento
                $luc += (float) $netAmount; //* Lucro
                $lucDs += (float) $netAmount; //> LucroMesDS
                $this->mes[] = $row;
            }
        }
        $this->faturamento = $fat;
        $this->lucro = $luc;
        $this->lucroDS = $lucDs*0.15;

    }






    public function getCanceladasMes()
    {
        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE internalStatus = 8");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $totalCancel = 0;
        while ($row = $result->fetch_assoc()) {
            $payload = json_decode($row['rawPayload'], true);
            $row['rawPayload'] = $payload;

            $payload = ($row['rawPayload']);
            $row['rawPayload'] = $payload;

            $grossAmount = (float) $payload["grossAmount"]; // * Valor da compra
            $totalCost = (float) $row["totalCost"] + floatval($payload['creditorFees']['intermediationRateAmount']) + floatval($payload['creditorFees']['intermediationFeeAmount']); // > Custo de Produto e Frete
            $netAmount = $grossAmount - $totalCost;
            $totalCancel += (float) $netAmount;
            $this->canceladas[] = $row;
        }
        $this->totalCancel = $totalCancel * 0.15;
    }







    public function setTotal()
    {
        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("INSERT INTO ds_relatory (invoicing, canceled, netAmount, DS_netAmount, fileName) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $this->faturamento, $this->totalCancel, $this->lucro, $this->lucroDS, $this->fileName);
        $stmt->execute();
        if($stmt->affected_rows > 0){
            $stmt->close();
            $mysqli->close();
            return true;
        }else{
            die(json_encode(array('status' => 500, 'message' => $stmt->error)));
        }
        $stmt->close();

    }




    public function updateSales()
    {
        $mysqli = $this->connect();
        foreach ($this->mes as $compra) {
            $stmt = $mysqli->prepare("UPDATE vendas SET internalStatus = 4 WHERE id = ?");
            $stmt->bind_param("i", $compra['id']);
            $stmt->execute();
            $stmt->close();
        }
        foreach ($this->canceladas as $compra) {
            $stmt = $mysqli->prepare("UPDATE vendas SET internalStatus = 9 WHERE id = ?");
            $stmt->bind_param("i", $compra['id']);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$getList = new getList();
$getList->generate();

//die(json_encode($getList->__construct()));
