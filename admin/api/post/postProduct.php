<?php
header('Content-Type: application/json; charset=utf-8');

if (isset($_POST["product"])) {
    include "../config/db_connect.php";
    $product = $_POST["product"];

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

    if ($_POST["id"] > 0) {
        $stmt = $mysqli->prepare("UPDATE `products` SET `name`= ? ,`price`= ? , `promo`= ? , `category`= ? , `totalQuantity`= ? , `material`= ? ,`weight`= ? ,`description`= ? ,`options`= ? ,`imgs`= ? ,`cost`= ?  WHERE `id` = ?");
        $stmt->bind_param("sssssssssssd", $name, $price, $promo, $category, $totalQuantity, $material, $weight, $description, $jsonOpt, $jsonImages, $cost, $_POST["id"]);
        if ($stmt->execute()) {
            $stmt->close();
            $mysqli->close();
            die(json_encode(array("status" => 200, "message" => "Produto atualizado com sucesso", "id"=>$_POST["id"])));
        } else {
            $stmt->close();
            $mysqli->close();
            die(json_encode(array("status" => 500, "message" => "Erro ao atualizar produto")));
        }
    } else {
        $stmt = $mysqli->prepare("INSERT INTO `products` ( `name`, `price`, `promo`, `category`, `totalQuantity`, `material`, `weight`, `description`, `options`, `imgs`, `cost`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $name, $price, $promo, $category, $totalQuantity, $material, $weight, $description, $jsonOpt, $jsonImages, $cost);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $id = $stmt->insert_id;
            die(json_encode(array("status" => 200, "message" => "Produto cadastrado com sucesso", "id"=>$id)));
        } else {
            die(json_encode(array("status" => 500, "message" => "Erro ao cadastrar produto")));
        }
    }
} else {
    die(json_encode(array("status"=> 400, "message" => "no product"), JSON_UNESCAPED_UNICODE));
}
