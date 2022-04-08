<?php
//if receive a json "cart", for each get the quantity in db products and update the quantity of each product in the db
include "db_connect.php";
if(isset($_GET['cart'])){
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

        ($options[$opt] < 0) ? die("ERROR") : $newOptions = json_encode($options);
        //Update products set $newOptions, totalQuantity $totalQuantity where id = $id
        $sql = "UPDATE products SET options = '$newOptions', totalQuantity = $totalQuantity WHERE id = $id";
        $result = $mysqli->query($sql);
        //close mysql connection
        echo("OK");
    }
}
$mysqli->close();