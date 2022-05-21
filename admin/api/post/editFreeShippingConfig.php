<?php
header('Content-Type: application/json; charset=utf-8');

if (isset($_POST['freteGratis'])) {
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
