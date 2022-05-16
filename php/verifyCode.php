<?php


if (isset($_GET['cpf']) && isset($_GET['code']) && is_numeric($_GET['cpf']) && strlen($_GET['code']) == 36) {

    include "db_connect.php";

    $cpf = $_GET['cpf'];
    $code = $_GET['code'];
    $code = preg_replace('/[|\,\;\\\:"]+/', '', $code);
    
    $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result_ = $stmt->get_result();

    if ($result_->num_rows > 0) {
        $row = $result_->fetch_assoc();
        $stmt->close();

        $clientId = $row['clientId'];

        $stmt = $mysqli->prepare("SELECT * FROM client WHERE cpf =  ?  AND id = ?");
        $stmt->bind_param("ss", $cpf, $clientId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(array("status" => "success", "code" => $code, "cpf" => $cpf));
        }else{
            echo($sql);
            echo json_encode(array("status" => "error", "message" => "Dados Incorretos!"));
        }
    }else{
        echo json_encode(array("status" => "error", "message" => "Dados Incorretos!"));
    }
}else{
    http_response_code(404);
}



function errHandle($errNo, $errStr, $errFile, $errLine)
{
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
        die(json_encode(array('status' => '403')));
    } 
}

set_error_handler('errHandle');
