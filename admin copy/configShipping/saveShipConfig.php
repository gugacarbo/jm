<?php

if (isset($_GET['cepOrigemFrete']) && isset($_GET['aditionalWeight']) && isset($_GET['alturaFrete']) && isset($_GET['larguraFrete']) && isset($_GET['comprimentoFrete']) && isset($_GET['freteGratis'])) {
    $config["cepOrigemFrete"] = $_GET['cepOrigemFrete'];
    $config["cepOrigemFrete"] = str_replace("-", "", $config["cepOrigemFrete"]);
    $config["cepOrigemFrete"] = str_replace(".", "", $config["cepOrigemFrete"]);
    $config["cepOrigemFrete"] = str_replace(" ", "", $config["cepOrigemFrete"]);
    if (!isset($_GET['cidades']) || ($_GET['cidades']) == "[]") {
        $config["freteGratis"]["cidades"] = [];
    } else {
        $cidades = $_GET['cidades'];

        foreach ($cidades as $key => $value) {
            $cidades_[] = $value;
        }
        $config["freteGratis"]["cidades"] = $cidades_;
    }
    if (!isset($_GET['estados']) || empty($_GET['estados'])) {
        $config["freteGratis"]["estados"] = [];
    } else {

        $estados = $_GET['estados'];

        foreach ($estados as $key => $value) {
            $estados_[] = $value;
        }
        $config["freteGratis"]["estados"] = $estados_;
    }
    $config["freteGratis"]["use"] = $_GET['freteGratis'];
    $config["aditionalWeight"] = $_GET['aditionalWeight'];
    $config["alturaFrete"] = $_GET['alturaFrete'];
    $config["larguraFrete"] = $_GET['larguraFrete'];
    $config["comprimentoFrete"] = $_GET['comprimentoFrete'];






    $config["freteGratis"] = json_encode($config["freteGratis"]);
    //print_r($config);



    include "../db_connect.php";

    foreach ($config as $key => $value) {
        $sql = "UPDATE generalConfig SET value = ? WHERE config = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $value, $key);
        if ($stmt->execute()) {
            $stmt->close();
        } else {
            $stmt->close();
            die(json_encode(array('status' => 'error')));
        }
    }
    die(json_encode(array('status' => 'success')));
} else {
    print_r($_GET);
    die(json_encode(array('status' => 'error')));
}
