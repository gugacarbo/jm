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



class home extends dbConnect
{
    private $totalDS = 0, $lucroMesDS = 0, $futuroDS = 0, $canceladasMes = 0,$canceladasMesDS  = 0, $canceladas = 0, $faturamentoMes = 0, $custos = 0, $lucroMes = 0;

    public function __construct()
    {
        $this->getMes();
        $this->getCanceladasMes();
        $this->getTotals();
        $this->futuroDS = $this->futuroDS * 0.15; //> 15%
        $this->lucroMesDS = $this->lucroMesDS * 0.15; //> 15%
        $this->canceladas = $this->canceladas; // >15%
        $this->canceladasMesDS = $this->canceladasMesDS * 0.15; // >15%
        $this->lucroMesDS = $this->lucroMesDS  - $this->canceladasMesDS; //> 15%
    }

    public function get()
    {
        return (array(
            'status' => 200,
            'totalDS' => $this->totalDS,
            "lucroMesDS" => $this->lucroMesDS,  // > LucroMesDS // >15%
            "futuroDS" => $this->futuroDS,  // > Futuro DS // >15%
            "canceladasMesDS" => $this->canceladasMesDS, // ! Canceladas Mes > 15%
            "canceladasMes" => $this->canceladasMes, // ! Canceladas Mes 
            "canceladas" => $this->canceladas, // ! Total Canceladas
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

        $fut = 0;
        $luc = 0;
        $fat = 0;
        $cos = 0;

        while ($row = $result->fetch_assoc()) {
            $payload = json_decode($row['rawPayload'], true);
            $grossAmount = (float) $payload["grossAmount"]; // * Valor da compra
            
            // > Custo de Produto Frete e Taxas
            $totalCost = (float) $row["totalCost"] + floatval($payload['creditorFees']['intermediationRateAmount']) + floatval($payload['creditorFees']['intermediationFeeAmount']);
            
                //* Lucro
            $netAmount = $grossAmount - $totalCost;

            // > Compra Já Creditada ?
            if (strtotime($payload['escrowEndDate']) > strtotime(date("Y-m-d"))) {
                $fut += (float) $netAmount; //> Futuro DS
            } else {
                $fat += $grossAmount;  //- Faturamento
                $cos += $totalCost ; //! Custos Produto  + Taxas + Frete
                $luc += (float) $netAmount; //* Lucro
            }
        }

        $this->custos = $cos; //! Custos
        $this->faturamentoMes = $fat;  //- Faturamento
        $this->lucroMes = $luc; //* Lucro Mes
        $this->lucroMesDS = $luc; //> Lucro Mes DS (100%)
        $this->futuroDS = $fut; //> Futuro DS

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
            $this->canceladasMes += (float) $row['totalAmount']; // ! Canceladas Mes
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
            $this->totalDS +=$row['netAmountDs'];
            $this->canceladas += (float) $row['canceledDs']; // ! Total Canceladas

        }
    }
}

$home = new home();
die(json_encode($home->get()));
