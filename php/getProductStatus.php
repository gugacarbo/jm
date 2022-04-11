<?php


if (isset($_GET['cpf']) && isset($_GET['code'])) {

    include "db_connect.php";

    $cpf = $_GET['cpf'];
    $code = $_GET['code'];

    $sql = "SELECT * FROM vendas WHERE code = '$code'";
    $result = $mysqli->query($sql);
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $clientId = $product['clientId'];
        
        $sql2 = "SELECT * FROM client WHERE cpf = '$cpf'  AND id = '$clientId'";

        //echo $sql;
        $result2 = $mysqli->query($sql2);
        if ($result2->num_rows > 0) {
            $row = $result2->fetch_assoc();
            $product["bornDate"] = $row["bornDate"];
            $product["cpf"] = $cpf;
            echo json_encode($product);
        }else{
            echo json_encode(array("status" => "error", "message" => "Dados Inco2rretos!"));
        }
    }else{
        echo json_encode(array("status" => "error", "message" => "Dados Inc1orretos!"));
    }
}else{
    http_response_code(404);
}


/*<?php
if (isset($_GET['cpf']) && isset($_GET['code'])) {

    include "db_connect.php";

    $cpf = $_GET['cpf'];
    $code = $_GET['code'];

    //select client by cpf on table client
    $sql = "SELECT * FROM client WHERE cpf = '$cpf'";
    $result = $mysqli->query($sql);
    
    //fetch bornDate
    //$bornDate = $row['bornDate'];

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $clientId = $row['id'];
        $bornDate = $row['bornDate'];
        //Select vendas by code and clientId on table vendas
        $sql = "SELECT * FROM vendas WHERE code = '$code' AND clientId = '$clientId'";
        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $row["bornDate"] = $bornDate;
            $row["cpf"] = $cpf;
            echo json_encode($row);
        } else {
            echo json_encode(array("status" => "error", "message" => "Dados Incorretos!"));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Dados Inc2orretos!"));
    }
} else {
    http_response_code(404);
}*/
