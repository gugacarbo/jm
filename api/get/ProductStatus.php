<?php
header('Content-Type: application/json; charset=utf-8');


if (isset($_GET['cpf']) && isset($_GET['code']) && is_numeric($_GET['cpf']) && strlen($_GET['code']) == 36) {

    include "../config/db_connect.php";

    $cpf = $_GET['cpf'];
    $code = $_GET['code'];
    $code = preg_replace('/[|\,\;\\\:"]+/', '', $code);

    $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result_ = $stmt->get_result();

    if ($result_->num_rows > 0) {
        $purchase = $result_->fetch_assoc();
        $purchase["status"] = 200;
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
} else {
    die(json_encode(array("status" => 400, "message" => "Bad Request")));
}
