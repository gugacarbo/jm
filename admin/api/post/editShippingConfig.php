<?php
header('Content-Type: application/json; charset=utf-8');

if (isset($_POST['cepOrigemFrete']) && isset($_POST['aditionalWeight']) && isset($_POST['alturaFrete']) && isset($_POST['larguraFrete']) && isset($_POST['comprimentoFrete']) && isset($_POST['freteGratis'])) {
    $config["cepOrigemFrete"] = $_POST['cepOrigemFrete'];
    $config["cepOrigemFrete"] = str_replace("-", "", $config["cepOrigemFrete"]);
    $config["cepOrigemFrete"] = str_replace(".", "", $config["cepOrigemFrete"]);
    $config["cepOrigemFrete"] = str_replace(" ", "", $config["cepOrigemFrete"]);
    if (!isset($_POST['cidades']) || ($_POST['cidades']) == "[]") {
        $config["freteGratis"]["cidades"] = [];
    } else {
        $cidades = $_POST['cidades'];

        foreach ($cidades as $key => $value) {
            $cidades_[] = $value;
        }
        $config["freteGratis"]["cidades"] = $cidades_;
    }
    if (!isset($_POST['estados']) || empty($_POST['estados'])) {
        $config["freteGratis"]["estados"] = [];
    } else {

        $estados = $_POST['estados'];

        foreach ($estados as $key => $value) {
            $estados_[] = $value;
        }
        $config["freteGratis"]["estados"] = $estados_;
    }
    $config["freteGratis"]["use"] = $_POST['freteGratis'];
    $config["aditionalWeight"] = $_POST['aditionalWeight'];
    $config["alturaFrete"] = $_POST['alturaFrete'];
    $config["larguraFrete"] = $_POST['larguraFrete'];
    $config["comprimentoFrete"] = $_POST['comprimentoFrete'];






    $config["freteGratis"] = json_encode($config["freteGratis"]);
    //print_r($config);



    include "../config/db_connect.php";

    foreach ($config as $key => $value) {
        $sql = "UPDATE generalConfig SET value = ? WHERE config = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $value, $key);
        if ($stmt->execute()) {
            $stmt->close();
        } else {
            $stmt->close();
            die(json_encode(array('status' => 500)));
        }
    }
    die(json_encode(array('status' => 200)));
} else {
    print_r($_POST);
    die(json_encode(array('status' => 400)));
}
