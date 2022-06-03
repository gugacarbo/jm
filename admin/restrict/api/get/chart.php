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
    private $totalDS, $lucroMesDS, $futuroDS, $canceladasMesDS, $canceladas, $faturamentoMes, $custos, $lucroMes;

    public function __construct()
    {
        $this->totalDS = 0;
        $this->getMes();
        $this->getCanceladasMes();
        $this->getTotalCanceladas();

        $this->faturamentoMes = number_format($this->faturamentoMes, 2, '.', '');
        $this->custos = number_format($this->custos, 2, '.', '');
        $this->lucroMes = number_format($this->lucroMes, 2, '.', '');
        $this->lucroMesDS = number_format($this->lucroMesDS, 2, '.', '');
        $this->futuroDS = number_format($this->futuroDS, 2, '.', '');
        $this->canceladasMesDS = number_format($this->canceladasMesDS, 2, '.', '');
        $this->canceladas = number_format($this->canceladas, 2, '.', '');
        
        $this->faturamentoMes = floatval($this->faturamentoMes);
        $this->custos = floatval($this->custos);
        $this->lucroMes = floatval($this->lucroMes);
        $this->lucroMesDS = floatval($this->lucroMesDS);
        $this->futuroDS = floatval($this->futuroDS);
        $this->canceladasMesDS = floatval($this->canceladasMesDS);
        $this->canceladas = floatval($this->canceladas);

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

        while ($row = $result->fetch_assoc()) {
            $payload = json_decode($row['rawPayload'], true);

            $grossAmount = (float) $payload["grossAmount"]; // * Valor da compra

            $totalCost = (float) $row["totalCost"]; // > Custo de Produto e Frete

            //?Lucro Final =>    $payload["netAmount"] = Valor da compra descontado taxa pagseguro  
            $netAmount = (float) $payload["netAmount"] - $totalCost;


            $this->faturamentoMes += $grossAmount;  //- Faturamento
            $this->custos += $totalCost; //! Custos
            $this->lucroMes += (float) $netAmount; //* Lucro
            
            if (strtotime($payload['escrowEndDate']) > strtotime(date("Y-m-5"))) {
                $this->futuroDS += (float) $netAmount; //> Futuro DS
            } else {
                $this->lucroMesDS += (float) $netAmount; //> LucroMesDS
            }
            //echo "FuturoDS: " . $this->futuroDS . "\n";
            
        }
        $this->futuroDS = $this->futuroDS * 0.15; //> 15%
        $this->lucroMesDS = $this->lucroMesDS * 0.15; //> 15%
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
        $this->canceladasMesDS = $this->canceladasMesDS * 0.15; // >15%
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
        $this->canceladas = $this->canceladas * 0.15; // >15%
    }
}

$home = new home();
die(json_encode($home->__construct()));
