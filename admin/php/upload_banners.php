<?php

if (isset($_POST["banners"])) {
    include 'db_connect.php';

    $banners = $_POST["banners"];
    print_r($banners);
    foreach ($banners as $name => $imgs) {
        $imgsJ;
        foreach ($imgs as $key => $img) {
            $imgsJ[$key + 1] = $img;
        }
        //prepare sql
        $jsonImg = json_encode($imgsJ);
        $stmt = $mysqli->prepare("UPDATE `banners` SET `images`=? WHERE `name` = ?");
        $stmt->bind_param("ss", $jsonImg, $name);
        
        if ($stmt->execute()) {
            echo json_encode(array("status" => "success", "message" => "Banner atualizado com sucesso"));
        } else {
            $erro = $stmt->error;
            echo json_encode(array("status" => "error", "message" => "Erro ao atualizar banner", "error" => $erro));
        }
    }
}
