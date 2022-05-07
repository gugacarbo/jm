<?php
include "getProdById.php";
include "db_connect.php";
$sender = ($_GET["buyer"]);
$shipping = ($_GET["ship"]);
$cart = ($_GET["cart"]);
$JsonSender = json_encode($sender,  JSON_UNESCAPED_UNICODE);

/** CONST */
$data['currency'] = "BRL";
$data['shippingAddressCountry'] = "BRA";
$data['shippingAddressRequired'] = "TRUE";

// ** **    ** **
//$data['extraAmount'] = "Desconto";
//$data['redirectURL'] = "";


//? ***************************************/
$data['reference'] = md5((str_replace(["-", "."], "", $sender['cpf'])) . date(DATE_RFC822));
//? ***************************************/

//verify if buyer already exists in database cleient by cpf and born date
$cpf = str_replace(["-", "."], "", $sender['cpf']);
$date = date('Y-m-d H:i:s', strtotime($sender["nascimento"]));

$stmt = $mysqli->prepare("SELECT * FROM client WHERE cpf = ? AND bornDate = ?");
$stmt->bind_param("ss", $cpf, $date);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $clientId = $row['id'];
} else {
    $stmt = $mysqli->prepare("INSERT INTO client (name, lastname, cpf, email, phone, bornDate) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $sender['nome'], $sender['sobrenome'], $cpf, $sender['email'], $sender['telefone'], $date);
    $stmt->execute();
    $clientId = $mysqli->insert_id;
}

$data['senderName'] = str_replace(" ", "_", $sender['nome'] ). " " . str_replace(" ", "_", $sender['sobrenome']);
$data['senderEmail'] = $sender['email'];
$data['senderAreaCode'] = $sender['telefone'][1] . $sender['telefone'][2];
$data['senderCPF'] = str_replace(["-", "."], "", $sender['cpf']);


$data['shippingType'] =  (($shipping["selected"] == "PAC") ? 1 : (($shipping["selected"] == "SEDEX") ? 2 : 3));
$data['shippingCost'] = /*"0.00";*/ number_format((str_replace(",", ".", $shipping["price"])), 2, ".", "");
$data['shippingAddressStreet'] = $sender["rua"];
$data['shippingAddressNumber'] = $sender["numero"];
$data['shippingAddressComplement'] = $sender["complemento"];
$data['shippingAddressDistrict'] = $sender["bairro"];
$data['shippingAddressCity'] = $sender["cidade"];
$data['shippingAddressState'] = $sender["UF"];
$data['shippingAddressPostalCode'] = str_replace(["-", "."], "", $sender["cep"]);


foreach ($cart as $n => $p) {
    $id_ = $p["id"];
    $qtd_ = $p["qtd"];
    $opt_ = $p["opt"];
    $item = getById($id_);
    $data['itemId' . strval($n + 1)] = $item['id'];
    $data['itemDescription' . strval($n + 1)] = $item['name'] . " - " . $opt_;
    $data['itemQuantity' . strval($n + 1)] = $qtd_;
    $data['itemAmount' . strval($n + 1)] = number_format((str_replace(",", ".", ($item['price']))), 2, '.', '');
    $data['itemWeight' . strval($n + 1)] = round($item['weight'] * 1000);
}


// curl post to https://ws.sandbox.pagseguro.uol.com.br/v2/checkout?email=guga_carbo@hotmail.com&token=8BE1A0DF1DAD40D99949834093F21AB8
$url = "https://ws.sandbox.pagseguro.uol.com.br/v2/checkout?email=guga_carbo@hotmail.com&token=8BE1A0DF1DAD40D99949834093F21AB8";
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$retorno = curl_exec($ch);
curl_close($ch);
//print_r($retorno);
$xml = simplexml_load_string($retorno, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml,  JSON_UNESCAPED_UNICODE);
$array = json_decode($json, TRUE);
//echo $json;
if($array["code"]){
    //save in the database in internal_buy, cart,buyer, shipping
    $JsonCart = json_encode($cart);


    $stmt = $mysqli->prepare("INSERT INTO internal_buy (products, clientId, buyer, reference) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $JsonCart, $clientId, $JsonSender, $data['reference']);
    $stmt->execute();
    if($stmt->affected_rows > 0){
        die(json_encode(array('status' => 'success', 'url' => "https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=" . $array['code'])));
    }else{
        die(json_encode(array('status' => 'error')));
    }
}else{
    //die json
    die(Array('status' => 'error'));
}
