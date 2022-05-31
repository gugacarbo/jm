<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');

define('TIMEZONE', 'America/Sao_Paulo');
date_default_timezone_set(TIMEZONE);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//! Verificação de Excesso de Tentativas
unset($_SESSION['checkoutTry']);
if (isset($_SESSION["checkoutTry"])) {

    $checkoutTry = $_SESSION["checkoutTry"];

    if ($checkoutTry < 10) {
        $_SESSION["checkoutTry"] = $checkoutTry + 1;
    } else {
        $lastTry = date($_SESSION['checkoutLastTry']);
        $interval = strtotime(date('Y-m-d H:i:s')) - strtotime($lastTry);

        if ($interval > 60) {
            $_SESSION["checkoutTry"] = 0;
            $_SESSION["checkoutLastTry"] = date('Y-m-d H:i:s');
        }

        die(json_encode(array("status" => 403, "message" => "Tente novamente mais tarde")));
    }
} else {
    $_SESSION["checkoutTry"] = 1;
    $_SESSION["checkoutLastTry"] = date("Y-m-d H:i:s");
}


include "../get/frete.php";
include "../get/getProdById.php";
include_once  '../config/db_connect.php';

class checkout extends dbConnect
{
    private $buyer_, $ship_, $cart_;

    public function __construct($buyer, $ship, $cart)
    {
        if (count($cart) > 50) {
            die(json_encode(array('status' => 400)));
        }
        $this->buyer_ = $buyer;
        $this->ship_ = $ship;
        $this->cart_ = $cart;
    }

    public function getCode()
    {
        $sender = $this->buyer_;
        $shipping  = $this->ship_;
        $cart = $this->cart_;

        $mysqli = $this->Conectar();


        $JsonSender = json_encode($sender,  JSON_UNESCAPED_UNICODE);

        //=CONST
        $data['currency'] = "BRL";
        $data['shippingAddressCountry'] = "BRA";
        $data['shippingAddressRequired'] = "TRUE";

        //? ************ Referencia ************

        $data['reference'] = md5((str_replace(["-", "."], "", $sender['cpf'])) . date(DATE_RFC822));

        //? ***************************************/

        //| Verifica e Insere ou atualiza cliente
        $cpf = str_replace(["-", ".", " ", "_", "-"], "", $sender['cpf']);
        $date = date('Y-m-d', strtotime($sender["nascimento"]));

        $stmt = $mysqli->prepare("SELECT * FROM client WHERE cpf = ?");
        $stmt->bind_param("s", $cpf);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row["bornDate"] != $date || $row["email"] != $sender["email"]) {
                $errorMessage = (array(
                    'status' => 401,
                    'errorCode' => 401,
                    'reference' => $data['reference'],
                    'sender' => $JsonSender,
                    'senderDate' => $date,
                    'senderEmail' => $sender["email"],
                    'dbDate' => $row["bornDate"],
                    'dbEmail' => $row["email"],

                    "message" => "Cliente Já Cadastrado, mas dados não conferem"
                )
                );
                $this->error_log("error", $errorMessage);
                //! Cpf Cadastrado, email ou data de nascimento incorretos
                return (array(
                    "status" => 401,
                    'errorCode' => 401,
                    "message" => "Os dados Informados não conferem com nosso banco de dados.
                     Verifique o email, cpf e data de nascimento caso voce nunca tenha feito 
                     uma compra na loja, entre em contato com o suporte.",
                ));
            } else {
                //? Atualiza referencia de compras do cliente;
                $stmt->close();
                $clientId = $row['id'];
                $actPurchases = json_decode($row['purchases']) ?? [];
                $actPurchases[] = $data['reference'];
                $jsonPurch = json_encode($actPurchases);
                $stmt = $mysqli->prepare("UPDATE client SET purchases = ? WHERE id = ?");
                $stmt->bind_param("si", $jsonPurch, $clientId);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            // * Novo CLiente
            $stmt->close();
            $clientPurchases[] = $data['reference'];
            $jsonPurch = json_encode($clientPurchases);
            $stmt = $mysqli->prepare("INSERT INTO client (gender, name, lastname, cpf, email, phone, bornDate, purchases) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $sender['gender'], $sender['nome'], $sender['sobrenome'], $cpf, $sender['email'], $sender['telefone'], $date, $jsonPurch);
            $stmt->execute();
            $clientId = $mysqli->insert_id;
            $stmt->close();
        }

        if ($clientId == 0) {
            $errorMessage = (array(
                'status' => 500,
                'errorCode' => 501,
                'reference' => $data['reference'],
                'sender' => $JsonSender,
                "message" => "Erro ao Atualizar inserir ou atualizar Cliente"
            )
            );
            $this->error_log("error", $errorMessage);
            return (json_encode(array(
                'status' => 500,
                'errorCode' => 501,
                'message' => 'Erro ao inserir ou atualizar o cliente'
            )));
        }



        //> Caso Desejado insere cliente na lista de newsletter
        if (isset($_POST['newsletter'])) {
            $stmt = $mysqli->prepare("SELECT id FROM newsletter WHERE email = ?");
            $stmt->bind_param("s", $sender['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $stmt->close();
            } else {
                $stmt->close();
                $stmt = $mysqli->prepare("INSERT INTO newsletter (name, email) VALUES (?,?)");
                $stmt->bind_param("ss", $sender['nome'], $sender['email']);
                $stmt->execute();
                if ($stmt->affected_rows == 0) {
                    $errorMessage = (array(
                        'status' => 500,
                        'errorCode' => 502,
                        'reference' => $data['reference'],
                        'email' => $sender['email'],
                        "message" => "Erro ao Inserir ou Atualizar Newsletter"
                    )
                    );
                    $this->error_log("error", $errorMessage);
                }
                $stmt->close();
            }
        }


        //> Insere dados do Comprador
        $data['senderName'] = str_replace(" ", "_", $sender['nome']) . " " . str_replace(" ", "_", $sender['sobrenome']);
        $data['senderEmail'] = $sender['email'];
        $data['senderAreaCode'] = $sender['telefone'][1] . $sender['telefone'][2];
        $data['senderCPF'] = str_replace(["-", "."], "", $sender['cpf']);


        //> Insere dados do Endereço de Entrega
        $data['shippingAddressStreet'] = $sender["rua"];
        $data['shippingAddressNumber'] = $sender["numero"];
        $data['shippingAddressComplement'] = $sender["complemento"];
        $data['shippingAddressDistrict'] = $sender["bairro"];
        $data['shippingAddressCity'] = $sender["cidade"];
        $data['shippingAddressState'] = $sender["UF"];
        $data['shippingAddressPostalCode'] = str_replace(["‑", ".", "-", " "], "", $sender["cep"]);

        $totalValue = 0;
        $totalWeight = 0;
        $totalCost = 0;


        //| Verifica a disponibilidade dos produtos no estoque
        $newProds = [];

        foreach ($cart as $n => $p) {
            $id = intval(preg_replace('/[|\,\;\\\:"]+/', '', $p["id"]));
            $qtd = intval(preg_replace('/[|\,\;\\\:"]+/', '', $p["qtd"]));
            $opt = preg_replace('/[|\,\;\\\:"]+/', '', $p["opt"]);

            $stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();

            $cart[$n]["prodCost"] = $item["cost"];

            if (!isset($newProds[$id])) {
                $options = json_decode($item['options']);
                $options = json_encode($options);
                $options = json_decode($options, true);
                $newProds[$id] = $options;
            }

            //> Adiciona informações do produto à query de compra
            $data['itemId' . strval($n + 1)] = $item['id'];
            $data['itemDescription' . strval($n + 1)] = tirarAcentos2($item['name']) . " - " . tirarAcentos2($opt);
            $data['itemQuantity' . strval($n + 1)] = $qtd;
            $data['itemAmount' . strval($n + 1)] = number_format((str_replace(",", ".", ($item['price']))), 2, '.', '');
            $data['itemWeight' . strval($n + 1)] = round($item['weight'] * 1000);
            $totalWeight += $item['weight'];
            $totalValue += $item['price'] * $qtd;
            $totalCost += $item['cost'] * $qtd;


            $newProds[$id][$opt] = $newProds[$id][$opt] - $qtd;
            //? Verifica se há a quantidade solicitada
            //!Remove do estoque


            if ($newProds[$id][$opt] < 0) {
                //x Produto não disponível
                $errorMessage = (array(
                    'status' => 500,
                    'errorCode' => 502,
                    'reference' => $data['reference'],
                    'clientId' => $clientId,
                    "message" => "Produto Indisponível"
                )
                );
                $this->error_log("error", $errorMessage);
                return ((array(
                    'status' => 500,
                    'errorCode' => 502,
                    "message" => "Itens Indisponíveis"
                )));
            } else {
                $newOptions = json_encode($options);
            }
        }

        //| Cupom Desconto
        if (isset($_POST['cupom'])) {
            $sql = "SELECT * FROM cupom WHERE ticker = ? AND quantity > 0"; // ? Verifica se o cupom existe
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $_POST['cupom']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) { //* Há o Cumpom Solicitado
                $cupom = $result->fetch_assoc();
                $stmt->close();
                $cupomDiscount = $cupom["type"] == "percent" ? ($totalValue *  $cupom["value"] / 100) : $cupom["value"];
                $cupomDiscount =  $cupomDiscount >= $totalValue - 1.5 ? $totalValue - 1.5 : $cupomDiscount;
                $cupomDiscount = "-" . number_format($cupomDiscount, 2, '.', '');
                $Rcupom["clientIds"] = json_decode($cupom["clientIds"]);

                //? Verifica se o cupom é de uso unico e se o cliente já utilizou o cupom
                if ($cupom["firstPurchase"] == 1) {
                    //* Cliente Não Usou, Aplica Cupom
                    if (!in_array($clientId, $Rcupom["clientIds"])) {
                        $data['extraAmount'] = $cupomDiscount;
                    } else {
                        $data['extraAmount'] = '0.00';
                    }
                } else {
                    //* Cupom não é de uso único, Aplica Cupom
                    $data['extraAmount'] = $cupomDiscount;
                }
            } else {
                $data['extraAmount'] = '0.00';
            }
        } else {
            $data['extraAmount'] = '0.00';
        }


        //> Calcula o valor total do Frete
        $frete = new frete($sender["cep"], $totalWeight);
        $newShip = $frete->getfrete();

        //x Erro ao Calcular Frete
        if ($newShip["erro"] > 0 || $newShip["erro2"] > 0) {
            $errorMessage = (array(
                'status' => 500,
                'errorCode' => 504,
                'reference' => $data['reference'],
                'clientId' => $clientId,
                'shipError' => json_encode($newShip),
                "message" => "Erro ao Atualizar Produtos"
            )
            );
            $this->error_log("error", $errorMessage);

            return json_encode(array(
                'status' => 500,
                'errorCode' => 503,
                "message" => "Erro ao calcular o frete. Tente novamente mais tarde."
            ));
        }

        //> Verifica o Tipo de Frete Solicitado
        if ($shipping["selected"] == "PAC") {
            $data['shippingType'] = 1;
            $sprice = str_replace(",", ".", $newShip["valorPac"][0]);
            $data['shippingCost'] = $sprice;
        } else if ($shipping["selected"] == "SEDEX") {
            $data['shippingType'] = 2;
            $sprice = str_replace(",", ".", $newShip["valorSedex"][0]);
            $data['shippingCost'] = $sprice;
        }

        //| Frete grátis
        if (isset($newShip["freteGratis"])) {
            $data['shippingCost'] =  '0.00';
        }

        $totalCost += floatval($data['shippingCost']);

        //* Solicita Link de Checkout no PagSeguro *//
        $url = "https://ws.sandbox.pagseguro.uol.com.br/v2/checkout?email=guga_carbo@hotmail.com&token=8BE1A0DF1DAD40D99949834093F21AB8";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $retorno = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($retorno, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml,  JSON_UNESCAPED_UNICODE);
        $array = json_decode($json, TRUE);

        if (isset($array["code"])) { //* Código de Cehckout Bem sucedido
            $JsonCart = json_encode($cart);

            //- Insere Pedido no Banco de Dados
            $stmt = $mysqli->prepare("INSERT INTO checkout_data (products, totalValue, clientId, buyer, reference, totalCost) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $JsonCart, $totalValue, $clientId, $JsonSender, $data['reference'], $totalCost);
            $stmt->execute();
            $stmt->close();

            // > Atualiza Quantidade dos Produtos

            foreach ($newProds as $n => $i) {
                $totalQuantity = array_sum($i);
                $jsonOptions = json_encode($i);
                $stmtU = $mysqli->prepare("UPDATE products SET options = ?, totalQuantity = ? WHERE id = ?");
                $stmtU->bind_param("sis", $jsonOptions, $totalQuantity, $n);
                $stmtU->execute();
                if ($stmtU->affected_rows > 0) {
                    $stmtU->close();
                } else {
                    $errorMessage = (array(
                        'status' => 500,
                        'errorCode' => 504,
                        'reference' => $data['reference'],
                        'clientId' => $clientId,
                        'products' => $JsonCart,
                        "message" => "Erro ao Atualizar Produtos"
                    )
                    );
                    $this->error_log("error", $errorMessage);
                    $stmtU->close();
                }
            }

            // > Atualiza Quantidade do Cupom
            if (isset($data['extraAmount']) && floatval($data['extraAmount']) < 0) {
                $sql = "UPDATE cupom SET quantity = quantity - 1, clientIds = ? WHERE ticker = ?";
                $stmt = $mysqli->prepare($sql);
                $newCupomIds = json_encode(array_merge($Rcupom["clientIds"], [$clientId]));
                $stmt->bind_param("ss", $newCupomIds, $cupom["ticker"]);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    $stmt->close();
                } else {
                    $errorMessage = (array(
                        'status' => 500,
                        'errorCode' => 505,
                        'reference' => $data['reference'],
                        'clientId' => $clientId,
                        'cupom' => $cupom["ticker"],
                        "message" => "Erro ao Atualizar Cupom", "error" => $stmt->error
                    )
                    );
                    $this->error_log("error", $errorMessage);
                    $stmt->close();
                }
            }

            return ((array('status' => 202, 'url' => "https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=" . $array['code'])));
        } else {
            $errorMessage = (array(
                'status' => 500,
                'errorCode' => 506,
                'reference' => $data['reference'],
                'pagseguro' => $array,
                "message" => "Erro ao Solicitar Checkout"
            )
            );
            $this->error_log("error", $errorMessage);
            return ((array('status' => 500, 'error' => $array)));
        }
    }


}


if (isset($_POST['buyer']) && isset($_POST['ship']) && isset($_POST['cart'])) {
    $sender = ($_POST["buyer"]);
    $shipping = ($_POST["ship"]);
    $cart = ($_POST["cart"]);
    $checkout = new checkout($sender, $shipping, $cart);
    die(json_encode($checkout->getCode()));
} else {
    die(json_encode(array('status' => 400)));
}


function tirarAcentos2($string)
{
    return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string);
}
