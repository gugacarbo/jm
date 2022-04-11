<?php
//if receive a json "cart", for each get the quantity in db products and update the quantity of each product in the db
if (isset($_GET['cart'])) {
    include "db_connect.php";
    $cart = ($_GET['cart']);

    foreach ($cart as $n => $p) {
        $id = $p["id"];
        $qtd = $p["qtd"];
        $opt = $p["opt"];

        $sql = "SELECT * FROM products WHERE id = $id";
        $result = $mysqli->query($sql);
        $row = $result->fetch_assoc();
        $options = json_decode($row['options'], true);

        $options[$opt] = $options[$opt] - $qtd;
        //sum all options
        $totalQuantity = 0;
        foreach ($options as $n => $op) {
            $totalQuantity += $options[$n];
        }

        ($options[$opt] < 0) ? die(json_encode(array('status' => 'error'))) : $newOptions = json_encode($options);
        $sql = "UPDATE products SET options = '$newOptions', totalQuantity = $totalQuantity WHERE id = $id";
        $result = $mysqli->query($sql);
        //verify if update is ok
        if (!$result) {
            die(json_encode(array('status' => 'error')));
        }
    }
    $mysqli->close();
    die(json_encode(array('status' => 'sucess')));
}
