<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    die(json_encode(array('status' => 403)));
}


include_once '../config/db_connect.php';


class cupom extends dbConnect
{
    
    public function __construct()
    {
 
    }

    public function delete($id)
    {
        $mysqli = $this->connect();
        $id = $mysqli->real_escape_string($id);
        $sql = "DELETE FROM cupom WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $stmt->close();
            return ((array('status' => 200)));
        } else {
            $stmt->close();
            return ((array('status' => 500, 'error' => 'Erro ao deletar cupom')));
        }
    }

    public function create($ticker, $type, $value, $quantity, $singleUse)
    {
        $mysqli = $this->connect();
        $ticker = strtoupper($_POST["ticker"]);
        $stmt = $mysqli->prepare("INSERT INTO cupom (ticker, value, type, quantity, singleUse) VALUES ( ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $ticker, $value, $type, $quantity, $singleUse);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            return ((array("status" => 200, "message" => "Cupom adicionado com sucesso")));
        } else {
            return ((array("status" => 500, "message" => $stmt->error)));
        }
    }

    public function edit($value, $type, $quantity, $singleUse, $id)
    {

        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("UPDATE cupom SET value = ?, type = ?, quantity = ?, singleUse = ? WHERE id = ?");
        $stmt->bind_param("sssss", $value, $type, $quantity, $singleUse, $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return ((array("status" => 200, "message" => "Cupom alterado com sucesso")));
        } else {
            $stmt->close();

            return ((array("status" => 500, "message" => "Erro ao alterar cupom")));
        }
    }
}



if (//>Adiciona ou edita um cupom
    isset($_POST["value"]) && isset($_POST["type"]) && isset($_POST["quantity"]) && isset($_POST["singleUse"]) && $_POST["value"] != "" && $_POST["type"] != "" && $_POST["quantity"] != "" && $_POST["singleUse"] != ""
) {
    if (isset($_POST["id"]) && $_POST["id"] > 0) {
        //| Edita
        $cupom = new cupom();
        $return = $cupom->edit($_POST["value"], $_POST["type"], $_POST["quantity"], $_POST["singleUse"], $_POST["id"]);
        die(json_encode($return));
    } else if (isset($_POST["ticker"]) && $_POST["ticker"] != "") {
        //* Adiciona
        $cupom = new cupom();
        $return = $cupom->create($_POST["ticker"], $_POST["type"], $_POST["value"], $_POST["quantity"], $_POST["singleUse"]);
        die(json_encode($return));
    }
} else if (isset($_POST["delCupom"]) && $_POST["delCupom"] != "") {
    //x Deleta um cupom
    $cupom = new cupom();
    $return = $cupom->delete($_POST["delCupom"]);
    die(json_encode($return));
} else {
    die(json_encode(array("status" => 400, "message" => "Bad Request")));
}
