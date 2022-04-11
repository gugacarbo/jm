<?php


if (isset($_GET['cpf']) && isset($_GET['code'])) {

    include "db_connect.php";

    $cpf = $_GET['cpf'];
    $code = $_GET['code'];

    $sql = "SELECT * FROM vendas WHERE code = '$code'";
    $result = $mysqli->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $clientId = $row['clientId'];
        
        $row = $result->fetch_assoc();
        $sql = "SELECT * FROM client WHERE cpf = '$cpf'  AND id = '$clientId'";
        //echo $sql;
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            echo json_encode(array("status" => "success", "code" => $code, "cpf" => $cpf));
        }else{
            echo($sql);
            echo json_encode(array("status" => "error", "message" => "Dados Inco2rretos!"));
        }
    }else{
        echo json_encode(array("status" => "error", "message" => "Dados Inc1orretos!"));
    }
}else{
    http_response_code(404);
}
