<?php
include("getItemById.php");
$itemsId = [1, 2, 3, 4, 5, 6, 7, 8];


/** CONST */
$data['currency'] = "BRL";
$data['shippingAddressCountry'] = "BRA";
$data['shippingAddressRequired'] = "TRUE";
//$data['receiverEmail'] = "gustavo@incubedev.com.br";
/** */
//$data['extraAmount'] = "Desconto";
//$data['redirectURL'] = "";


$data['shippingCost'] = "10.00";
$data['shippingAddressStreet'] = "rua do caralho";
$data['shippingAddressNumber'] = "666";
$data['shippingAddressComplement'] = "casa do capeta";
$data['shippingAddressDistrict'] = "Penha";
$data['shippingAddressCity'] = "Inferno";
$data['shippingAddressState'] = "RJ";
$data['shippingAddressPostalCode'] = "88504110";
$data['shippingType'] = "1";


$data['senderName'] = "capeta top";
$data['senderEmail'] = "gustavo@incubedev.com.br";
$data['senderAreaCode'] = "69";
$data['senderPhone'] = "966669666";
$data['senderCPF'] = "01189446901";
$data['reference'] = "999666999";

$data['itemWeight'] = 0;



foreach ($itemsId as $n => $id) {
  $item = getById($id);
  
  $data['itemId'.strval($n+1)] = $item['id'];
  $data['itemDescription'.strval($n+1)] = $item['description'];
  $data['itemQuantity'.strval($n+1)] = $item['quantity'];
  $data['itemAmount'.strval($n+1)] = $item['amount'];
  $data['itemWeight'] .= $item['weight'];
}

$curl = curl_init();
$query = http_build_query($data, '', '&');

//8BE1A0DF1DAD40D99949834093F21AB8
//"https://ws.pagseguro.uol.com.br/v2/checkout?email=guga_carbo@hotmail.com&token=2892d1c4-4188-475f-b840-975a99cb9b396e0c6960446fa00bc19a696db4163addcac8-51be-49aa-a7bf-2baed7e7e05b"
curl_setopt_array($curl, [
  CURLOPT_URL => "https://ws.sandbox.pagseguro.uol.com.br/v2/checkout?email=guga_carbo@hotmail.com&token=8BE1A0DF1DAD40D99949834093F21AB8",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $query, 
  
]);
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);


#print_r($response);

$xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
$array = json_decode($json,TRUE);

echo("https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=".$array['code']);
//echo($array['code']);
