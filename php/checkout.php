<?php
include "getProdById.php";

$sender = ($_GET["buyer"]);
$shipping = ($_GET["ship"]);
$cart = ($_GET["cart"]);
/** CONST */
$data['currency'] = "BRL";
$data['shippingAddressCountry'] = "BRA";
$data['shippingAddressRequired'] = "TRUE";
/** */
//$data['extraAmount'] = "Desconto";
//$data['redirectURL'] = "";

$data['senderName'] = $sender['nome'] . " " . $sender['sobrenome'];
$data['senderEmail'] = $sender['email'];
$data['senderAreaCode'] = $sender['telefone'][1] . $sender['telefone'][2];
$data['senderCPF'] = str_replace(["-", "."], "",$sender['cpf']);


$data['shippingType'] =  (($shipping["selected"] == "PAC") ? 1 : (($shipping["selected"] == "SEDEX") ? 2 : 3));
$data['shippingCost'] = /*"0.00";*/number_format((str_replace(",", ".", $shipping["price"])), 2, ".", "");
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
    $data['itemId'.strval($n+1)] = $item['id'];
    $data['itemDescription'.strval($n+1)] = $item['name'] . " - " . $opt_;
    $data['itemQuantity'.strval($n+1)] = $qtd_;
    $data['itemAmount'.strval($n+1)] = number_format((str_replace(",", ".", ($item['price']))), 2, '.', '');
    $data['itemWeight'.strval($n+1)] = round($item['weight'] * 1000);

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
$json = json_encode($xml);
$array = json_decode($json,TRUE);

die("https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=".$array['code']);
