<?php
//header('Content-Type: application/csv');
//header('Content-Disposition: attachment; filename=arquivo.csv');


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
    private $mes = [], $canceladas = [], $indicators = [],
        $faturamento,
        $custoTotal,
        $lucro,
        $lucroDs,
        $totalCancel,
        $totalCancelNet,
        $totalCancelDs,
        $fileName;

    public function __construct()
    {
        $this->getMes();
        $this->getCanceladasMes();
        $this->indicators();


        //! Descontando Cancelamentos do Faturamento e do Lucro
        $this->faturamento = $this->faturamento - $this->totalCancel;
        $this->lucro = $this->lucro - $this->totalCancelNet;

        $this->custoTotal = $this->custoTotal - ($this->totalCancel - $this->totalCancelNet);

        $this->lucroDs = $this->lucroDs - $this->totalCancelDs;
    }



    public function generate()
    {

        if (count($this->mes) == 0 && count($this->canceladas) == 0) {
            die(json_encode(array('status' => 404, 'message' => 'Nenhum registro encontrado')));
        }


        $this->csv();
        $this->setTotal();
        $this->updateSales();

        die(json_encode(array('status' => 200, 'message' => 'Relatório gerado com sucesso')));
    }





    public function getMes()
    {

        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE internalStatus = 3");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();


        $fat = 0; //* Faturamento
        $luc = 0; //* Lucro Total

        $countSales = 0; // - Contagem De Vendas
        $TTProdCost = 0; // x Custo Total De Produtos
        $TTProdPrice = 0; //| Preço Total // Produtos
        $TTnumProds = 0; //? Quantidade De Produtos
        $totalMargin = 0; // > Margem Total
        $TTCost = 0; //! Custo Total // Taxa + Produtos

        while ($row = $result->fetch_assoc()) {
            $payload = json_decode($row['rawPayload'], true);
            $row['rawPayload'] = $payload;


            // * Pagamento Creditado
            if (isset($payload['escrowEndDate']) && strtotime($payload['escrowEndDate']) < strtotime(date("Y-m-d"))) {
                // - Contagem De Vendas
                $countSales++;
                $this->mes[] = $row;


                // * Valor da compra
                $grossAmount =  $payload["grossAmount"];


                // ? Custo // Taxa + Frete + Produtos 
                $totalCost =  $row["totalCost"] + floatval($payload['creditorFees']['intermediationRateAmount'])
                    + floatval($payload['creditorFees']['intermediationFeeAmount']);
                //
                //! Custo Total // Taxa + Produtos
                $TTCost += $totalCost;

                // x Custo Total De Produtos
                $TTProdCost += $row["totalCost"] - $payload['shipping']['cost'];

                // > Lucro Final
                $netAmount = $grossAmount - $totalCost;

                //* Lucro Total
                $luc +=  $netAmount;

                //- Faturamento - Fat
                $fat += $grossAmount;

                // > Margem Total
                $totalMargin += $row["margin"];

                $items = ($payload['itemCount'] == 1 ? [$payload['items']['item']] : $payload['items']['item']);
                foreach ($items as $item) {
                    //? Quantidade De Produtos
                    $TTnumProds += $item['quantity'];

                    //|Preço Total // Produtos
                    $TTProdPrice += $item['amount'] *  $item['quantity'];
                }
            }
        }


        $countSales == 0 ? $countSales = 1 : $countSales = $countSales;
        $TTnumProds == 0 ? $TTnumProds = 1 : $TTnumProds = $TTnumProds;
        //? Indicadores
        $this->indicators['av_ticket'] = $fat / $countSales; // * Ticket Médio = Faturamento / Quantidade de Vendas
        $this->indicators['av_price'] = $TTProdPrice / $TTnumProds; // > Preço Médio = Preço Total de Produtos / Quantidade de Produtos
        $this->indicators['av_numProds'] = $TTnumProds / $countSales; // - Quantidade Média de Produtos = Quantidade Total de Produtos / Quantidade de Vendas
        $this->indicators['av_margin'] = ($totalMargin / $countSales); // ? Margem Média = Margem Total / Quantidade de Vendas
        $this->indicators['av_cost'] = $TTProdCost / $countSales; // | Custo Médio = Custo Total da Venda / Quantidade de Vendas


        $this->faturamento = $fat; // * Faturamento
        $this->custoTotal = $TTCost; //! Custos Totais + Frete
        $this->lucro = $luc; // > Lucro
        $this->lucroDs = $luc * 0.15; // > Lucro DS (15%)
    }






    public function getCanceladasMes() // ! Para Devolver a jo internalStatus = 8
    {
        $mysqli = $this->connect();

        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE internalStatus = 8");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $totalCancel = 0;
        $totalCancelNet = 0;


        while ($row = $result->fetch_assoc()) {
            $payload = json_decode($row['rawPayload'], true);
            $row['rawPayload'] = $payload;

            $payload = ($row['rawPayload']);
            $row['rawPayload'] = $payload;

            // * Valor da compra            
            $grossAmount =  $payload["grossAmount"];

            // > Custo de Produto  Frete e taxas
            $totalCost =  $row["totalCost"] + floatval($payload['creditorFees']['intermediationRateAmount']) + floatval($payload['creditorFees']['intermediationFeeAmount']);

            //! Lucro
            $netAmount = $grossAmount - $totalCost;

            $totalCancel += $row['totalAmount'];
            $totalCancelNet +=  $netAmount;
            $this->canceladas[] = $row;
        }

        $this->totalCancel = $totalCancel;
        $this->totalCancelNet = $totalCancelNet;
        $this->totalCancelDs = $totalCancelNet * 0.15;
    }



    public function indicators()
    {



        //?newsletter
        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT * FROM newsletter WHERE 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $this->indicators['newsletter'] = $result->num_rows;


        //> Clientes
        $stmt = $mysqli->prepare("SELECT * FROM client WHERE 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $this->indicators['client'] = $result->num_rows;


        //- Visitantes
        $stmt = $mysqli->prepare("SELECT * FROM visitas WHERE 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $this->indicators['visitor'] = $result->num_rows;



        //| NPS
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM rating WHERE rate BETWEEN 0 AND 3");
        $stmt->execute();
        $stmt->bind_result($rateDetractor);
        $stmt->fetch();
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM rating WHERE rate = 5");
        $stmt->execute();
        $stmt->bind_result($ratePromoter);
        $stmt->fetch();
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM rating ");
        $stmt->execute();
        $stmt->bind_result($totalrates);
        $stmt->fetch();
        $stmt->close();

        $this->indicators['nps'] = intval((($ratePromoter / $totalrates) - ($rateDetractor / $totalrates)) * 100);
        //|

        //! Compras Canceladas No Mes (total - > )
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM vendas WHERE status = 6 AND internalStatus != 9");
        $stmt->execute();
        //!Contagem de compras Canceladas
        $stmt->bind_result($this->indicators['totalCanceled']);
        $stmt->close();
    }

    public function csv()
    {


        gc_collect_cycles();
        $d = date("Y-m-d-H-i");
        $this->fileName = "Relatório" . $d . ".csv";
        $fp = fopen("../../relatorios/" . $this->fileName, 'w');

        $csvRow = array(
            "Referencia",
            "Data do Pagamento",
            "Data de Recebimento",
            "Nome do Cliente",
            "Quantidade de Produtos",
            "Valor Total",
            "Custo Total",
            "Descontos",
            "Frete",
            "Lucro da Venda",
            "Lucro da Venda DS (15%)",
            "Status",
        );
        fputcsv($fp, $csvRow, ';');


        foreach ($this->mes as $compra) {

            
            $items = ($compra['rawPayload']['itemCount'] == 1 ? [$compra['rawPayload']['items']['item']] : $compra['rawPayload']['items']['item']);
            $totalItens = 0;
            foreach ($items as $item) {
                $totalItens = $totalItens + $item['quantity'];
            }

            $csvRow = array(
                $compra['reference'],
                $compra['paymentDate'],
                $compra['rawPayload']['escrowEndDate'],
                $compra['rawPayload']['sender']['name'],
                $totalItens,
                $compra['totalAmount'],
                $compra['totalCost'] + $compra['rawPayload']['creditorFees']['intermediationFeeAmount'] + $compra['rawPayload']['creditorFees']['intermediationRateAmount'],
                $compra['rawPayload']['discountAmount'],
                $compra['rawPayload']['shipping']['cost'],
                ($compra['rawPayload']['netAmount'] - $compra['totalCost']),
                (($compra['rawPayload']['netAmount'] - $compra['totalCost']) * 0.15),
                'Finalizada'
            );
            fputcsv($fp, $csvRow, ';');
        }


        foreach ($this->canceladas as $compra) {

            $csvRow = array(
                $compra['reference'],
                $compra['paymentDate'],
                $compra['rawPayload']['escrowEndDate'],
                $compra['rawPayload']['sender']['name'],
                $totalItens,
                $compra['totalAmount'],
                $compra['totalCost'] + $compra['rawPayload']['creditorFees']['intermediationFeeAmount'] + $compra['rawPayload']['creditorFees']['intermediationRateAmount'],
                $compra['rawPayload']['discountAmount'],
                $compra['rawPayload']['shipping']['cost'],
                ($compra['rawPayload']['netAmount'] - $compra['totalCost']),
                (($compra['rawPayload']['netAmount'] - $compra['totalCost']) * 0.15),
                'Cancelada'
            );
            fputcsv($fp, $csvRow, ';');
        }

        fclose($fp);
        $this->fileName = "relatorios/" . $this->fileName;
    }




    public function setTotal()
    {
        $indicators_ = json_encode($this->indicators);
        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("INSERT INTO ds_relatory (invoicing, cost, netAmount, netAmountDs,  canceled, canceledDs, indicators, fileName) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $this->faturamento, $this->custoTotal, $this->lucro, $this->lucroDs, $this->totalCancel, $this->totalCancelDs, $indicators_, $this->fileName);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            $mysqli->close();
            return true;
        } else {

            die(json_encode(array('status' => 500, "message" => $stmt->error)));
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

        $stmt = $mysqli->prepare("UPDATE vendas SET internalStatus = 9 WHERE status = 6 AND internalStatus = 6");
        $stmt->execute();
        $stmt->close();
    }
}

$getList = new getList();
$getList->generate();

//die(json_encode($getList->__construct()));
