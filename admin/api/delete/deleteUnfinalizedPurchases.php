<?php
header('Content-Type: application/json; charset=utf-8');

include "../config/db_connect.php";
include "../get/getNoFinalizedPurshases.php";

$list = json_decode(getPurchases());
foreach ($list as $value) {
    $products = ($value->products);
    $sucess = 0;
    $failed = 0;

    foreach ($products as $product) {
        $stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("s", $product->id);
        $stmt->execute();
        $result_ = $stmt->get_result();
        $row = $result_->fetch_assoc();
      
        
        $options = json_decode($row['options'], true);

        $options[$product->opt] = $options[$product->opt] + $product->qtd;

        ($options[$product->opt] < 0) ? die(json_encode(array('status' => 403))) : $newOptions = json_encode($options);
        
        $totalQuantity = 0;
        foreach ($options as $n => $op) {
            $totalQuantity += $options[$n];
        }

        $stmt = $mysqli->prepare("UPDATE products SET options = ?, totalQuantity = ? WHERE id = ?");
        $stmt->bind_param("sis", $newOptions, $totalQuantity, $product->id);
        $stmt->execute();

        if($stmt->affected_rows == 0){
            //die(json_encode(array('status' => 500)));
            $failed++;
        }else{
            $sucess++;
        }
        $stmt->close();
    }

    $stmt = $mysqli->prepare("DELETE FROM checkout_data WHERE reference = ?");
    $stmt->bind_param("s", $value->reference);
    //$stmt->execute();
    if($stmt->affected_rows == 0){
        //die(json_encode(array('status' => 500)));
    }
    $stmt->close();
}

die(json_encode(array('status' => 202, "sucess" => $sucess . " Produtos Atualizados", "failed" => $failed . " Produtos Não Encontrados")));


