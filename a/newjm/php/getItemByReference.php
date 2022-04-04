<?php
include("db_connect.php");


$ref = $_GET['ref'];

$ref = intval($ref);

if($ref == 0 || !(is_int($ref))){
    die();
}

$sql = "SELECT * FROM produtos WHERE referencia = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ref);
$stmt->execute();

$result = $stmt->get_result();
$result = $result->fetch_assoc();

    $row = $result;


    
        //$row['fotos'] = json_encode($row['fotos']);
        $row['preco'] = number_format($row['preco'], 2, ',', ' ');
        $row['preco_promo'] = number_format($row['preco_promo'], 2, ',', ' ');
        
        
        
        
        $material_id = $row['material'];
        $material_id = intval($material_id);

        $sql = "SELECT * FROM materiais WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $material_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $result = $result->fetch_assoc();

        $row['material'] = $result['nome'];
        



        $categoria_id = $row['categoria'];
        $categoria_id = intval($categoria_id);

        $sql = "SELECT * FROM categorias WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $categoria_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $result = $result->fetch_assoc();

        $row['categoria'] = $result['nome'];



        die(json_encode(($row)));
?>