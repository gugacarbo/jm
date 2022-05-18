<?php
header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $r["status"] = 200;
    $r = getByID($id);
    echo json_encode($r, JSON_UNESCAPED_UNICODE);

    $mysqli_->close();
}


function getById($id_)
{   
    include '../config/db_connect.php';
    global $mysqli_;
    $mysqli_ = $mysqli;

    $id = preg_replace('/[a-z\|\,\;\@\:"]+/', '', $id_);
    $id = mysqli_real_escape_string($mysqli, $id);

    $stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $itens = $result->fetch_assoc();
    $stmt->close();

    $materialId = $itens['material'];
    $categoryId = $itens['category'];


    $stmt = $mysqli->prepare("SELECT * FROM material WHERE id = ?");
    $stmt->bind_param("s", $materialId);
    $stmt->execute();
    $result = $stmt->get_result();
    $material = $result->fetch_assoc();
    $stmt->close();

    $stmt = $mysqli->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("s", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
    
    $ret["id"] = $itens['id'];
    $ret["name"] = $itens["name"];
    $ret["price"] = $itens["price"];
    $ret["promo"] = $itens["promo"];
    $ret["category"] = $category["name"];
    $ret["categoryId"] = $categoryId;
    $ret["material"] = $material["name"];
    $ret["weight"] = $itens["weight"];
    $ret["description"] = $itens["description"];
    $ret["imgs"] = json_decode($itens["imgs"]);
    $ret['weight'] = $itens['weight'];
    $ret["options"] = json_decode($itens["options"]);
    $ret["cost"] = $itens["cost"];
    return ($ret);
    
}

