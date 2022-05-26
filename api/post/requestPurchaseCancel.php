<?php
header('Content-Type: application/json; charset=utf-8');

if (isset($_POST["code"]) && isset($_POST["reason"])) {
    $code = $_POST["code"];
    $reason = $_POST["reason"];

    include "../config/db_connect.php";
    include "mailCancel.php";

    $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE code = ? AND internalStatus < 5 AND status > 2  AND status < 4");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $purchase = $result->fetch_assoc();
    $stmt->close();
    if ($result->num_rows > 0) {
        $stmt = $mysqli->prepare("UPDATE vendas SET internalStatus = 5 WHERE code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
       
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            $stmt = $mysqli->prepare("INSERT INTO calcelrequest ( `code`, `reason`, `clientId`, `buyDate`) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $code, $reason,  $purchase["clientId"], $purchase["buyDate"]);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $stmt->close();
                $mysqli->close();

                // | Enviar email de cancelamento
                $mailSended =  sendCancelMail(json_decode($purchase["rawPayload"], true));
                if($mailSended["status"] >= 200 && $mailSended["status"] < 300){
                    die(json_encode(array("status" => 200)));
                }else{
                    die(json_encode(array("status" => 500, "message" => "Email Não Enviado")));
                }
            } else {
                die (json_encode(array("status" => 400, "error" => "Não foi possível gravar o cancelamento.")));
            }
        } else {
            die (json_encode(array("status" => 400, "error" => "Não foi possível Atualizar a compra.")));
        }
    }else{
        die(json_encode(array("status" => 403)));
    }
}else{
    die(json_encode(array("status" => 400, "message" => "Bad request.")));
}
