<?php

if (isset($_GET['cart'])) {
    include "db_connect.php";
    $cart = ($_GET['cart']);

    foreach ($cart as $n => $p) {
        $id = preg_replace('/[|\,\;\\\:"]+/', '', $p["id"]);
        $qtd = preg_replace('/[|\,\;\\\:"]+/', '', $p["qtd"]);
        $opt = preg_replace('/[|\,\;\\\:"]+/', '', $p["opt"]);


        $stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result_ = $stmt->get_result();
        $row = $result_->fetch_assoc();

        $options = json_decode($row['options'], true);

        $options[$opt] = $options[$opt] - $qtd;
        //sum all options
        ($options[$opt] < 0) ? die(json_encode(array('status' => 'error'))) : $newOptions = json_encode($options);
        
        $totalQuantity = 0;
        foreach ($options as $n => $op) {
            $totalQuantity += $options[$n];
        }

        $stmt = $mysqli->prepare("UPDATE products SET options = ?, totalQuantity = ? WHERE id = ?");
        $stmt->bind_param("sis", $newOptions, $totalQuantity, $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            
        } else {
            die (json_encode(array('status' => 'error')));
        }
    }
    echo json_encode(array('status' => 'success'));
    $mysqli->close();
    die();
}
