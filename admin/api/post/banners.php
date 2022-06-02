<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 2) {
    die(json_encode(array('status' => 403,
    'message' => 'Acesso negado')));
}


include_once '../config/db_connect.php';


class Carousel extends dbConnect
{
    public function __construct()
    {
    }

    public function create($banners)
    {
        $mysqli = $this->connect();
        foreach ($banners as $name => $imgs) {
            foreach ($imgs as $key => $img) {
                $imgsJ[$key + 1] = $img;
            }
            $jsonImg = json_encode($imgsJ);
            $stmt = $mysqli->prepare("UPDATE `banners` SET `images`= ? WHERE `name` = ?");
            $stmt->bind_param("ss", $jsonImg, $name);

            if ($stmt->execute()) {
            } else {
                $erro = $stmt->error;
                return ((array("status" => 400, "message" => "Erro ao atualizar banner", "error" => $erro)));
            }
        }
        return (array("status" => 201, "message" => "Banner atualizado com sucesso"));
    }
}

if (isset($_POST["banners"])) {
    $banners = $_POST["banners"];
    $carousel = new Carousel();
    $response = $carousel->create($banners);
    die(json_encode($response));
}else{
    die(json_encode(array('status' => 400, 'message' => 'Nenhum banner foi enviado')));
}
