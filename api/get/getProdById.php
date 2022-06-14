<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');



include_once  '../config/db_connect.php';
class Prods extends dbConnect
{

    public function __construct()
    {
        
    }
    public function getById($id)
    {
        $mysqli = $this->Conectar();

        $stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ? AND deleted = 0");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows <= 0) {
            $stmt->close();
            return (array('status' => 400, 'message' => 'Produto não encontrado'));
        }
        $itens = $result->fetch_assoc();
        $stmt->close();



        unset($itens['cost']);

        $materialId = $itens['material'];
        $categoryId = $itens['category'];


        $stmt = $mysqli->prepare("SELECT * FROM material WHERE id = ?");
        $stmt->bind_param("s", $materialId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows <= 0) {
            $material["name"] = "N/A";
        } else {
            $material = $result->fetch_assoc();
            $stmt->close();
        }

        $stmt = $mysqli->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->bind_param("s", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows <= 0) {
            $category["name"] = "N/A";
        } else {
            $category = $result->fetch_assoc();
            $stmt->close();
        }


        $ret["status"] = 200;
        $ret["id"] = $itens['id'];
        $ret["name"] = $itens["name"];
        $ret["price"] = $itens["price"];
        $ret["promo"] = $itens["promo"];
        $ret["category"] = $category["name"];
        $ret["material"] = $material["name"];
        $ret["weight"] = $itens["weight"];
        $ret["description"] = $itens["description"];
        $ret["imgs"] = (json_decode($itens["imgs"]));
        $ret["options"] = (json_decode($itens["options"]));
        $ret["totalQuantity"] = ($itens["totalQuantity"]);
        return ($ret);
    }
}


if (isset($_GET['id'])) {
    if (is_numeric($_GET['id'])) {
        $r = new Prods();
        die(json_encode($r->getById($_GET['id']), JSON_UNESCAPED_UNICODE));
    } else {
        die(json_encode(array('status' => 400)));
    }
}


