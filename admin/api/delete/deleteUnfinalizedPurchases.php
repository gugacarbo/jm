<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    die(json_encode(array('status' => 403)));
}


include_once '../config/db_connect.php';


class canceled extends dbConnect
{
    public function __construct()
    {
        $mysqli = $this->connect();

        $sql = "SELECT * FROM checkout_data WHERE payload = '{}' AND NOW() >= DATE_ADD(buy_date, INTERVAL 1 DAY)";
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
die(json_encode((new canceled())->__construct()));

