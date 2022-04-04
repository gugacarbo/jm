<?php
$curl = curl_init();
//$notificationCode = "BC9377-8A7E467E4669-1CC43DDFA092-ADFEE7";
$notificationCode =  $_GET['notificationCode'];
$credenciais = "email=guga_carbo@hotmail.com&token=8BE1A0DF1DAD40D99949834093F21AB8";
curl_setopt_array($curl, [
    CURLOPT_URL => "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/".$notificationCode."?" . $credenciais,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",    
  ]);
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);

  $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
  $json = json_encode($xml);
  die($json);

?>
