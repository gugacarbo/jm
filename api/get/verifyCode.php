<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');

include_once '../config/db_connect.php';

class verifyCode extends dbConnect
{
    public function __construct()
    {

    }
    public function getCred($code_, $cpf_)
    {
        $code = str_replace(["","."," ", "|",  "/", "~", "^"], '', $code_);
        $cpf = str_replace(["-","."," ", "|", "/", "~", "^"],  '', $cpf_);
        

        $mysqli = $this->Conectar();

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
                return json_encode(array("status" => 200, "code" => $code, "cpf" => $cpf));
            } else {
                return json_encode(array("status" => 400, "message" => "Dados Incorretos!"));
            }
        } else {
            return json_encode(array("status" => 400, "message" => "Dados Incorretos!"));
        }
    }
}


if (isset($_GET['cpf']) && isset($_GET['code']) && is_numeric($_GET['cpf']) && strlen($_GET['code']) == 36) {

    $cpf = $_GET['cpf'];
    $code = $_GET['code'];
    $purchase = new verifyCode();
    die($purchase->getCred($code, $cpf));

} else {
    echo json_encode(array("status" => 400, "message" => "Bad Request!"));
}
