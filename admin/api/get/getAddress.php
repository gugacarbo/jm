<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 1) {
    die(json_encode(array('status' => 403)));
}


if (isset($_GET['cep'])) {
    $cep = $_GET['cep'];
    $cep = str_replace("-", "", $cep);
    $cep = str_replace(".", "", $cep);
    $cep = str_replace(" ", "", $cep);

    $url = "http://cep.republicavirtual.com.br/web_cep.php?cep=" . $cep . "&formato=xml";

    $xml = simplexml_load_file($url);
    $cidade = $xml->cidade[0];
    $uf = $xml->uf[0];

    die(json_encode(array('status' => 200, 'cidade' => $cidade, 'uf' => $uf)));
} else {
    die(json_encode(array("status" => 400, "message" => "Bad Request")));
}
