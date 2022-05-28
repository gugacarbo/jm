<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');

include '../config/db_connect.php';


class verifyPurchase extends dbConnect
{
    private $cpf_, $code_;
    public function __construct($code, $cpf)
    {
        $this->code_ = str_replace(["."," ", "|", "\\", "/", "~", "^"], '', $code);
        $this->cpf_ = str_replace(["-","."," ", "|", "\\", "/", "~", "^"], '', $cpf);
    }
    public function getPurchase()
    {
        $cpf = $this->cpf_;
        $code = $this->code_;

        $mysqli = $this->Conectar();

        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result_ = $stmt->get_result();

        if ($result_->num_rows > 0) {
            $purchase = $result_->fetch_assoc();
            $purchase["rawPayload"] = json_decode($purchase["rawPayload"]);
            $clientId = $purchase['clientId'];

            $stmt = $mysqli->prepare("SELECT * FROM client WHERE cpf = '$cpf'  AND id = ?");
            $stmt->bind_param("s", $clientId);
            $stmt->execute();
            $result2 = $stmt->get_result();

            if ($result2->num_rows > 0) {
                $row = $result2->fetch_assoc();

                $purchase["bornDate"] = $row["bornDate"];
                $purchase["name"] = $row["name"] . " " . $row["lastName"];
                $purchase["cpf"] = $cpf;
                die(json_encode($purchase));
            } else {
                die(json_encode(array("status" => 400, "message" => "Bad Request")));
            }
        } else {
            die(json_encode(array("status" => 400)));
        }
    }
}

if (isset($_GET['cpf']) && isset($_GET['code']) && is_numeric($_GET['cpf']) && strlen($_GET['code']) == 36) {

    $code = $_GET['code'];
    $cpf = $_GET['cpf'];
    $purchase = new verifyPurchase($code, $cpf);
    die($purchase->getPurchase());
} else {
    die(json_encode(array("status" => 400, "message" => "Bad Request")));
}
