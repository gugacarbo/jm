<?php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM".$_SERVER['REMOTE_ADDR']));

    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 1) {
    die(json_encode(array('status' => 403)));
}

include_once '../config/db_connect.php';

class GET_HomeInfo extends dbConnect
{
    private $info;
    public function __construct()
    {
        $mysqli = $this->connect();
        $this->emptyCartMove();

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM vendas WHERE status >= 3 AND status <= 4");
        $stmt->execute();
        $stmt->bind_result($totalAprovadas);
        $stmt->fetch();

        $stmt->close();
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM vendas WHERE status = 6 ");
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


        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM nofinalizedpurchases WHERE 1");
        $stmt->execute();
        $stmt->bind_result($totalNaoPagos);
        $stmt->fetch();
        $stmt->close();


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


        $nps = intval((($ratePromoter / $totalrates) - ($rateDetractor / $totalrates)) * 100);



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

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM client");
        $stmt->execute();
        $stmt->bind_result($clients);
        $stmt->fetch();
        $stmt->close();

        $thismonth = date('Y-m-1');

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM client WHERE date >= '$thismonth'");
        $stmt->execute();
        $stmt->bind_result($moreClients);
        $stmt->fetch();
        $stmt->close();


        $this->info = ((array(
            'status' => 200, 'Aprovadas' => $totalAprovadas,
            'Canceladas' => $totalCanceladas, 'AguardandoEnvio' => $totalAguardandoEnvio,
            'NaoPagos' => $totalNaoPagos, 'AguardandoPagamento' => $totalAguardandoPagamento,
            'Nps' => $nps,
            "canceling" => $totalCanceladas30,
            "visitas" => $visitas,
            "clients" => $clients,
            "moreClients" => $moreClients
        )));
    }
    public function get()
    {
        return $this->info;
    }


    private function emptyCartMove()
    {
        $mysqli = $this->connect();

        $sql = "SELECT * FROM checkout_data WHERE payload = '{}' AND NOW() >= DATE_ADD(buy_date, INTERVAL 3 DAY)";
        $stmt = $mysqli->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row["buyer"] = json_decode($row["buyer"]);
                $row["payload"] = json_decode($row["payload"]);
                $row["products"] = json_decode($row["products"]);
                $data[] = $row;
            }
            $list = $data;
        } else {
            return ((array(
                "status" => 100
            )));
        }
        //print_r($data);

        // > Todas as Vendas Não Finalizadas
        foreach ($list as $value) {
            $products = ($value['products']);
            $sucess = 0;
            $failed = 0;

            // * Rtornando Produtos para Estoque
            foreach ($products as $product) {
                $stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->bind_param("s", $product->id);
                $stmt->execute();
                $result_ = $stmt->get_result();
                if ($result_->num_rows <= 0) {
                    $failed++;
                } else {
                    $row = $result_->fetch_assoc();


                    $options = json_decode($row['options'], true);

                    $options[$product->opt] = $options[$product->opt] + $product->qtd;

                    $newOptions = json_encode($options);

                    $totalQuantity = 0;
                    foreach ($options as $n => $op) {
                        $totalQuantity += $options[$n];
                    }

                    $stmt = $mysqli->prepare("UPDATE products SET options = ?, totalQuantity = ? WHERE id = ?");
                    $stmt->bind_param("sis", $newOptions, $totalQuantity, $product->id);
                    $stmt->execute();

                    if ($stmt->affected_rows == 0) {
                        $failed++;
                    } else {
                        $sucess++;
                    }
                }
                $stmt->close();
            }
            // * Fim do Retorno de Produtos para Estoque


            // ? Movendoo Para Historico
            $jsonProducts = json_encode($value['products']);
            $stmt = $mysqli->prepare("INSERT INTO nofinalizedpurchases (clientId, products, totalAmount, reference, date) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $value['clientId'], $jsonProducts, $value['totalValue'], $value['reference'], $value['buy_date']);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $stmt->close();
            } else {
                $stmt->close();
                return array('status' => 500);
            }

            $stmt = $mysqli->prepare("DELETE FROM checkout_data WHERE id = ?");
            $stmt->bind_param("s", $value['id']);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $stmt->close();
            } else {
                $stmt->close();
                return array('status' => 500);
            }
        }
        return array(
            'status' => 200,
            'sucess' => $sucess,
            'failed' => $failed
        );
    }
}


if (isset($_SESSION['user']) && isset($_SESSION['admin'])) {
    $getHomeInfo = new GET_HomeInfo();
    die(json_encode($getHomeInfo->get()));
} else {
    die(json_encode(array('status' => 403)));
}
