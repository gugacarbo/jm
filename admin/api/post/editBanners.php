<?php
header('Content-Type: application/json; charset=utf-8');

if (isset($_POST["banners"])) {
    include '../config/db_connect.php';
    $banners = $_POST["banners"];
    foreach ($banners as $name => $imgs) {
        $imgsJ;
        foreach ($imgs as $key => $img) {
            $imgsJ[$key + 1] = $img;
        }
        $jsonImg = json_encode($imgsJ);
        $stmt = $mysqli->prepare("UPDATE `banners` SET `images`= ? WHERE `name` = ?");
        $stmt->bind_param("ss", $jsonImg, $name);

        if ($stmt->execute()) {
        } else {
            $erro = $stmt->error;
            die(json_encode(array("status" => 400, "message" => "Erro ao atualizar banner", "error" => $erro)));
        }
    }
    echo json_encode(array("status" => 201, "message" => "Banner atualizado com sucesso"));
}
die();
