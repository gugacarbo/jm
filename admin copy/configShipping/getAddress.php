<?php

if(isset($_GET['cep'])){
    $cep = $_GET['cep'];
    $cep = str_replace("-", "", $cep);
    $cep = str_replace(".", "", $cep);
    $cep = str_replace(" ", "", $cep);
 
    $url = "http://cep.republicavirtual.com.br/web_cep.php?cep=" . $cep . "&formato=xml";
    
    $xml = simplexml_load_file($url);
    $cidade = $xml->cidade[0];
    $uf = $xml->uf[0];
    die(json_encode(array('status' => 'success', 'cidade' => $cidade, 'uf' => $uf)));
}