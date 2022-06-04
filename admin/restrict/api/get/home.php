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
    private $totalDS = 0, $lucroMesDS = 0, $futuroDS = 0, $canceladasMesDS  = 0, $canceladas = 0, $faturamentoMes = 0, $custos = 0, $lucroMes = 0;

    public function __construct()
    {
        $this->getMes();
        $this->getCanceladasMes();
        $this->getTotalCanceladas();
        $this->getTotals();
        $this->futuroDS = $this->futuroDS * 0.15; //> 15%
        $this->lucroMesDS = $this->lucroMesDS * 0.15; //> 15%
        $this->canceladas = $this->canceladas * 0.15; // >15%
        $this->canceladasMesDS = $this->canceladasMesDS * 0.15; // >15%
    }

    public function get()
    {
        return (array(
            'status' => 200,
            'totalDS' => $this->totalDS,
            "lucroMesDS" => $this->lucroMesDS,  // > LucroMesDS // >15%
            "futuroDS" => $this->futuroDS,  // > Futuro DS // >15%
            "canceladasMesDS" => $this->canceladasMesDS, // ! Canceladas Mes > 15%
            "canceladas" => $this->canceladas, // ! Total Canceladas > 15%
            'faturamentoMes' => $this->faturamentoMes, //- Faturamento
            "custos" => $this->custos, //! Custos
            "lucroMes" => $this->lucroMes, //* Lucro
        ));
    }
    public function getMes()
    {

        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE internalStatus = 3");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $lucDs = 0;
        $fut = 0;
        $luc = 0;
        $fat = 0;
        $cos = 0;

        while ($row = $result->fetch_assoc()) {
            $payload = json_decode($row['rawPayload'], true);
            $grossAmount = (float) $payload["grossAmount"]; // * Valor da compra
            $totalCost = (float) $row["totalCost"] + floatval($payload['creditorFees']['intermediationRateAmount']) + floatval($payload['creditorFees']['intermediationFeeAmount']); // > Custo de Produto e Frete
            $netAmount = $grossAmount - $totalCost;

            if (strtotime($payload['escrowEndDate']) > strtotime(date("Y-m-d"))) {
                $fut += (float) $netAmount; //> Futuro DS
            } else {
                $fat += $grossAmount;  //- Faturamento
                $cos += $totalCost; //! Custos
                $luc += (float) $netAmount; //* Lucro
                $lucDs += (float) $netAmount; //> LucroMesDS
            }
            //echo "FuturoDS: " . $this->futuroDS . "\n";

        }

        $this->faturamentoMes = $fat;  //- Faturamento
        $this->custos = $cos; //! Custos
        $this->lucroMes = $luc; //* Lucro
        $this->futuroDS = $fut; //> Futuro DS
        $this->lucroMesDS = $lucDs; //> LucroMesDS

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
            $totalCost = (float) $row["totalCost"];
            $netAmount = (float) $payload["netAmount"] - $totalCost;
            $this->canceladasMesDS += (float) $netAmount; // ! Canceladas Mes
        }
    }



    public function getTotalCanceladas()
    {
        $mysqli = $this->connect();

        $canceladas = 0;
        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE internalStatus = 9");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $payload = json_decode($row['rawPayload'], true);
            $totalCost = (float) $row["totalCost"];
            $netAmount = (float) $payload["netAmount"] - $totalCost;
            $this->canceladas += (float) $netAmount; // ! Total Canceladas
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
            //$row['date'] = date("d/m/Y", strtotime($row['date']));
            $this->totalDS +=$row['invoicing'];
        }
    }
}

$home = new home();
die(json_encode($home->get()));
