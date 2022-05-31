<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');

include_once '../config/db_connect.php';

class getBanner extends dbConnect
{
    public function __construct($name_)
    {
        $mysqli = $this->Conectar();

        $name  = preg_replace('/[|\,\;\@\:"]+/', '', $name_);
        $stmt = $mysqli->prepare("SELECT * FROM banners WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows <= 0) {
            $stmt->close();
            return (json_encode(array('status' => 400, 'message' => 'Banner não encontrado')));
        }
        $itens = $result->fetch_assoc();
        $stmt->close();
        $ret["status"] = 200;

        $ret["id"] = $itens['id'];
        $ret["name"] = $itens["name"];
        $ret["images"] = json_decode($itens["images"]);

        return (json_encode($ret));
    }
}

if (isset($_GET['name'])) {
    die((new getBanner($_GET["name"]))->__construct($_GET["name"]));
} else {
    die(json_encode(array('status' => 400, 'message' => 'Banner não encontrado')));
}
