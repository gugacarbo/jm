<?php
header('Content-Type: application/json; charset=utf-8');

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
//! Fim da Verificação de Excesso de Tentativas


if (isset($_POST['buyer']) && isset($_POST['ship']) && isset($_POST['cart'])) {
    include "../get/getProdById.php";
    include "../get/frete.php";
    include "../config/db_connect.php";

    $sender = ($_POST["buyer"]);

    $shipping = ($_POST["ship"]);
    $cart = ($_POST["cart"]);

    if (count($cart) > 50) {
        die(json_encode(array('status' => 400)));
    }

    $JsonSender = json_encode($sender,  JSON_UNESCAPED_UNICODE);

    //=CONST
    $data['currency'] = "BRL";
    $data['shippingAddressCountry'] = "BRA";
    $data['shippingAddressRequired'] = "TRUE";

    //? ************ Referencia ************

    $data['reference'] = md5((str_replace(["-", "."], "", $sender['cpf'])) . date(DATE_RFC822));

    //? ***************************************/

    //* Verifica e Insere ou atualiza cliente
    $cpf = str_replace(["-", ".", " ", "_", "-"], "", $sender['cpf']);
    $date = date('Y-m-d H:i:s', strtotime($sender["nascimento"]));

    $stmt = $mysqli->prepare("SELECT * FROM client WHERE cpf = ? AND bornDate = ?");
    $stmt->bind_param("ss", $cpf, $date);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();
        $stmt->close();

        $clientId = $row['id'];
        $actPurchases = json_decode($row['purchases']) ?? [];

        $actPurchases[] = $data['reference'];
        $jsonPurch = json_encode($actPurchases);

        $stmt = $mysqli->prepare("UPDATE client SET purchases = ? WHERE id = ?");
        $stmt->bind_param("si", $jsonPurch, $clientId);
        $stmt->execute();
        $stmt->close();
    } else {

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
        die(json_encode(array('status' => 500, 'message' => 'Erro ao inserir ou atualizar o cliente')));
    }



    //? Caso Desejado insere cliente na lista de newsletter
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
            $stmt->close();
        }
    }


    $data['senderName'] = str_replace(" ", "_", $sender['nome']) . " " . str_replace(" ", "_", $sender['sobrenome']);
    $data['senderEmail'] = $sender['email'];
    $data['senderAreaCode'] = $sender['telefone'][1] . $sender['telefone'][2];
    $data['senderCPF'] = str_replace(["-", "."], "", $sender['cpf']);



    $data['shippingAddressStreet'] = $sender["rua"];
    $data['shippingAddressNumber'] = $sender["numero"];
    $data['shippingAddressComplement'] = $sender["complemento"];
    $data['shippingAddressDistrict'] = $sender["bairro"];
    $data['shippingAddressCity'] = $sender["cidade"];
    $data['shippingAddressState'] = $sender["UF"];
    $data['shippingAddressPostalCode'] = str_replace(["‑", ".", "-", " "], "", $sender["cep"]);

    $totalValue = 0;
    $totalWeight = 0;

    //? Verifica a disponibilidade dos produtos no estoque
    $newOptionsG = [];
    $newTotalQuantityG = [];
    $newIdG = [];

    foreach ($cart as $n => $p) {
        $id = preg_replace('/[|\,\;\\\:"]+/', '', $p["id"]);
        $qtd = preg_replace('/[|\,\;\\\:"]+/', '', $p["qtd"]);
        $opt = preg_replace('/[|\,\;\\\:"]+/', '', $p["opt"]);
        $item = getById($id);
        $data['itemId' . strval($n + 1)] = $item['id'];
        $data['itemDescription' . strval($n + 1)] = tirarAcentos2($item['name']) . " - " . tirarAcentos2($opt);
        $data['itemQuantity' . strval($n + 1)] = $qtd;
        $data['itemAmount' . strval($n + 1)] = number_format((str_replace(",", ".", ($item['price']))), 2, '.', '');
        $data['itemWeight' . strval($n + 1)] = round($item['weight'] * 1000);
        $totalWeight += $item['weight'];
        $totalValue += $item['price'] * $qtd;

        $options = json_encode($item['options']);
        $options = json_decode($options, true);

        $options[$opt] = $options[$opt] - $qtd;
        //! Verifica se há a quantidade solicitada
        ($options[$opt] < 0) ? die(json_encode(array('status' => 500, "message" => "Itens Indisponíveis"))) : $newOptions = json_encode($options);

        $totalQuantityOpt = 0;

        foreach ($options as $n => $op) {
            $totalQuantityOpt += $options[$n];
        }

        $newOptionsG[] = $newOptions;
        $newTotalQuantityG[] = $totalQuantityOpt;
        $newIdG[] = $id;
    }

    // ** ** Cupom Desconto  ** **
    if (isset($_POST['cupom'])) {
        $sql = "SELECT * FROM cupom WHERE ticker = ? AND quantity > 0"; // ? Verifica se o cupom existe
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $_POST['cupom']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) { //> Há o Cumpom Solicitado
            $cupom = $result->fetch_assoc();
            $stmt->close();
            $cupomDiscount = $cupom["type"] == "percent" ? ($totalValue *  $cupom["value"] / 100) : $cupom["value"];
            $cupomDiscount =  $cupomDiscount >= $totalValue - 1.5 ? $totalValue - 1.5 : $cupomDiscount;
            $cupomDiscount = "-" . number_format($cupomDiscount, 2, '.', '');
            //echo $cupomDiscount;
            $Rcupom["clientIds"] = json_decode($cupom["clientIds"]);

            //- Verifica se o cupom é de uso unico e se o cliente já utilizou o cupom
            if ($cupom["firstPurchase"] == true && !in_array($clientId, $Rcupom["clientIds"])) {
                $data['extraAmount'] = $cupomDiscount;
            } else { //= Cupom não é de uso único
                $data['extraAmount'] = $cupomDiscount;
            }
        }
    }
    // ** ** Fim Cupom  ** **



    //> Verifica o Tipo de Frete Solicitado
    $newShip = getfrete(str_replace(["‑", ".", "-", " "], "", $sender["cep"]), $totalWeight);
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

    if ($array["code"]) { //* Código de Cehckout Bem sucedido
        $JsonCart = json_encode($cart);

        //- Insere Pedido no Banco de Dados
        $stmt = $mysqli->prepare("INSERT INTO checkout_data (products, totalValue, clientId, buyer, reference) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $JsonCart, $totalValue, $clientId, $JsonSender, $data['reference']);
        $stmt->execute();
        $stmt->close();

        // > Atualiza Quantidade dos Produtos
        echo "\n";
        print_r($newTotalQuantityG);
        echo "\n";
        print_r($newOptionsG);
        echo "\n";
        print_r($newIdG);
        echo "\n";
        foreach ($newIdG as $n => $i) {

            $stmtU = $mysqli->prepare("UPDATE products SET options = ?, totalQuantity = ? WHERE id = ?");
            $stmtU->bind_param("sis", $newOptionsG[$i], $newTotalQuantityG[$i], $i);
            $stmtU->execute();
            if ($stmtU->affected_rows > 0) {
                $stmtU->close();
            } else {
                die(json_encode(array('status' => 500, "message" => "Erro ao Atualizar Produtos", "error" => $stmtU->error)));
                $stmtU->close();
            }
        }

        // > Atualiza Quantidade do Cupom
        if (isset($data['extraAmount']) && $data['extraAmount'] > 0) {
            $sql = "UPDATE cupom SET quantity = quantity - 1, clientIds = ? WHERE ticker = ?";
            $stmt = $mysqli->prepare($sql);
            $newCupomIds = json_encode(array_merge($Rcupom["clientIds"], [$clientId]));
            $stmt->bind_param("ss", $newCupomIds, $cupom["ticker"]);
            $stmt->execute();
            $stmt->close();
            if ($stmt->affected_rows > 0) {
                $stmt->close();
            } else {
                $stmt->close();
                //die(json_encode(array('status' => 500)));
            }
        }

        die(json_encode(array('status' => 202, 'url' => "https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=" . $array['code'])));
    } else {
        //die json
        die(json_encode(array('status' => 500)));
    }
} else {
    die(json_encode(array('status' => 400)));
}


function tirarAcentos2($string)
{
    return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string);
}
