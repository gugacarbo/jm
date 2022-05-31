<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    die(json_encode(array('status' => 403)));
}


include_once '../config/db_connect.php';
//include "mailTracking.php";


class product extends dbConnect
{
    public function __construct()
    {
    }
    public function delete($id)
    {
        $mysqli = $this->connect();

        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return ((array("status" => 200)));
        } else {
            return ((array("status" => 500, "message" => "Bad Request")));
        }
    }
    public function create($product)
    {
        $mysqli = $this->connect();

        $name = $product["name"];
        $price = $product["price"];
        $promo = $product["promo"];
        $category = $product["category"];
        $totalQuantity = 0;
        $material = $product["material"];
        $weight = $product["weight"];
        $description = $product["description"];
        $opt = $product["options"];
        $images = $product["imgs"];
        $cost = $product["cost"];

        foreach ($opt as $o => $value) {
            $totalQuantity =  $totalQuantity + $value;
        }

        $jsonImages = json_encode($images);
        $jsonOpt = json_encode($opt);


        $stmt = $mysqli->prepare("INSERT INTO `products` ( `name`, `price`, `promo`, `category`, `totalQuantity`, `material`, `weight`, `description`, `options`, `imgs`, `cost`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $name, $price, $promo, $category, $totalQuantity, $material, $weight, $description, $jsonOpt, $jsonImages, $cost);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $id = $stmt->insert_id;
            return ((array("status" => 200, "message" => "Produto cadastrado com sucesso", "id" => $id)));
        } else {
            return ((array("status" => 500, "message" => "Erro ao cadastrar produto")));
        }
    }

    public function update($product, $id)
    {
        $mysqli = $this->connect();

        $name = $product["name"];
        $price = $product["price"];
        $promo = $product["promo"];
        $category = $product["category"];
        $totalQuantity = 0;
        $material = $product["material"];
        $weight = $product["weight"];
        $description = $product["description"];
        $opt = $product["options"];
        $images = $product["imgs"];
        $cost = $product["cost"];

        foreach ($opt as $o => $value) {
            $totalQuantity =  $totalQuantity + $value;
        }

        $jsonImages = json_encode($images);
        $jsonOpt = json_encode($opt);

        $stmt = $mysqli->prepare("UPDATE `products` SET `name`= ? ,`price`= ? , `promo`= ? , `category`= ? , `totalQuantity`= ? , `material`= ? ,`weight`= ? ,`description`= ? ,`options`= ? ,`imgs`= ? ,`cost`= ?  WHERE `id` = ?");
        $stmt->bind_param("sssssssssssd", $name, $price, $promo, $category, $totalQuantity, $material, $weight, $description, $jsonOpt, $jsonImages, $cost, $id);
        if ($stmt->execute()) {
            $stmt->close();
            $mysqli->close();
            return ((array("status" => 200, "message" => "Produto atualizado com sucesso", "id" => $id)));
        } else {
            $stmt->close();
            $mysqli->close();
            return ((array("status" => 500, "message" => "Erro ao atualizar produto")));
        }
    }
}
if (isset($_POST["product"])) {

    if (isset($_POST["id"]) && $_POST["id"] != "" && intval($_POST["id"] )> 0) {
        $id = intval($_POST["id"]);
        die(json_encode((new product())->update($_POST["product"], $id)));
    } else {
        die(json_encode((new product())->create($_POST["product"])));
    }
} else if (isset($_POST["deleteProduct"])) {
    die(json_encode((new product())->delete($_POST["deleteProduct"])));
} else {
    die(json_encode(array("status" => 400, "message" => "no product"), JSON_UNESCAPED_UNICODE));
}
