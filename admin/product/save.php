<?php

if (isset($_POST["product"])) {
    include "../db_connect.php";
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

    if ($_GET["id"] > 0) {
        $stmt = $mysqli->prepare("UPDATE `products` SET `name`= ? ,`price`= ? , `promo`= ? , `category`= ? , `totalQuantity`= ? , `material`= ? ,`weight`= ? ,`description`= ? ,`options`= ? ,`imgs`= ? ,`cost`= ?  WHERE `id` = ?");
        $stmt->bind_param("sssssssssssd", $name, $price, $promo, $category, $totalQuantity, $material, $weight, $description, $jsonOpt, $jsonImages, $cost, $_GET["id"]);
        if ($stmt->execute()) {
            $stmt->close();
            $mysqli->close();
            die(json_encode(array("status" => "success", "message" => "Produto atualizado com sucesso")));
        } else {
            $stmt->close();
            $mysqli->close();
            die(json_encode(array("status" => "error", "message" => "Erro ao atualizar produto")));
        }
    } else {
        $stmt = $mysqli->prepare("INSERT INTO `products` ( `name`, `price`, `promo`, `category`, `totalQuantity`, `material`, `weight`, `description`, `options`, `imgs`, `cost`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $name, $price, $promo, $category, $totalQuantity, $material, $weight, $description, $jsonOpt, $jsonImages, $cost);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            die(json_encode(array("status" => "success", "message" => "Produto cadastrado com sucesso")));
        } else {
            die(json_encode(array("status" => "error", "message" => "Erro ao cadastrar produto")));
        }
    }
} else {
    die(json_encode(array("error" => "no product"), JSON_UNESCAPED_UNICODE));
}
