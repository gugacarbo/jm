<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');

include_once '../config/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
class verifyCode extends dbConnect
{
    public function __construct()
    {
    }
    public function getCred($code_, $cpf_)
    {
        $code = str_replace(["", ".", " ", "|",  "/", "~", "^"], '', $code_);
        $cpf = str_replace(["-", ".", " ", "|", "/", "~", "^"],  '', $cpf_);


        $mysqli = $this->Conectar();

        $stmt = $mysqli->prepare("SELECT clientId FROM vendas WHERE code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result_ = $stmt->get_result();

        if ($result_->num_rows > 0) {
            $row = $result_->fetch_assoc();
            $stmt->close();

            $clientId = $row['clientId'];

            $stmt = $mysqli->prepare("SELECT id FROM client WHERE cpf =  ?  AND id = ?");
            $stmt->bind_param("ss", $cpf, $clientId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $_SESSION['verifyCodeTry'] = 0;

                return json_encode(array("status" => 200, "code" => $code, "cpf" => $cpf));
            } else {
                return json_encode(array("status" => 400, "message" => "Dados Incorretos!"));
            }
        } else {
            return json_encode(array("status" => 400, "message" => "Dados Incorretos!"));
        }
    }
}


if (isset($_SESSION['verifyCodeTry']) && $_SESSION['verifyCodeTry'] > 5) {
    $lastTry = date($_SESSION['verufyCodeLastTry']);
    $interval = strtotime(date('Y-m-d H:i:s')) - strtotime($lastTry);
    if ($interval > 60) {
        $_SESSION['verifyCodeTry'] = isset($_SESSION['verifyCodeTry']) ? $_SESSION['verifyCodeTry'] + 1 : 1;
        $_SESSION['verifyCodeTry'] = 0;
    }

    die(json_encode(array('status' => 403)));
} else {
    $_SESSION['verifyCodeTry'] = isset($_SESSION['verifyCodeTry']) ? $_SESSION['verifyCodeTry'] + 1 : 1;
    $_SESSION['verufyCodeLastTry'] = date("Y-m-d H:i:s");

    if (isset($_GET['cpf']) && isset($_GET['code']) && is_numeric($_GET['cpf']) && strlen($_GET['code']) == 36) {

        $cpf = $_GET['cpf'];
        $code = $_GET['code'];
        $purchase = new verifyCode();
        die($purchase->getCred($code, $cpf));
    } else {
        echo json_encode(array("status" => 400, "message" => "Bad Request!"));
    }
}
