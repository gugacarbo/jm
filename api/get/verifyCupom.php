<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');

include_once '../config/db_connect.php';

class cupom extends dbConnect
{
    private $cupom_, $cpf_, $bornDate_;
    public function __construct($cupom_, $cpf, $bornDate)
    {
        $this->cupom_ =  str_replace(["-","."," ", "\\"], '', $cupom_);
        $this->cpf_ = str_replace(["-","."," ", "|", "\\", "/", "~", "^"], '', $cpf);
        $this->bornDate_ = $bornDate;
    }

    public function verifyCupom()
    {
        $mysqli = $this->Conectar();
        
        $cupom = $this->cupom_;
        $cpf = $this->cpf_;
        $bornDate = $this->bornDate_;


        $sql = "SELECT * FROM cupom WHERE ticker = ? AND quantity > 0";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $cupom);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $cupom = $result->fetch_assoc();
            $stmt->close();
    
            $Rcupom["ticker"] = $cupom["ticker"];
            $Rcupom["type"] = $cupom["type"];
            $Rcupom["value"] = $cupom["value"];
            $Rcupom["firstPurchase"] = $cupom["firstPurchase"];
            $Rcupom["clientIds"] = json_decode($cupom["clientIds"]);
    
            if($cupom["firstPurchase"]){

                $sql = "SELECT id FROM client WHERE cpf = ? AND bornDate = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param('ss', $cpf, $bornDate);
                $stmt->execute();
                $result = $stmt->get_result();
                $Cid = $result->fetch_assoc()["id"];
                if ($result->num_rows > 0) {
                    if (in_array($Cid, $Rcupom["clientIds"])) {
                        die(json_encode(array("status" => 403, "message" => "Cupom Já Utilizado.")));
                    } else {
                        die(json_encode(array('status' => 200, 'cupom' => $Rcupom)));
                    }
                } else {
                    $stmt->close();
                    die(json_encode(array('status' => 200, 'cupom' => $Rcupom)));
                }
            }else{
                die(json_encode(array('status' => 200, 'cupom' => $Rcupom)));
            }
    
    
        } else {
            die(json_encode(array("status" => 400, "message" => "Cupom inválido ou esgotado.")));
        }

    }
}

if (isset($_GET['cupom']) && isset($_GET['cpf']) &&  isset($_GET['bornDate'])) {
    $cupom = new cupom($_GET['cupom'], $_GET['cpf'], $_GET['bornDate']);
    die($cupom->verifyCupom());
}
