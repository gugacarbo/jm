<?php


if (!empty($_POST['notificationCode'])) {
    include("db_connect.php");
    include("mail.php");

    $notificationCode =  $_POST['notificationCode'];

    //$credenciais = "email=guga_carbo@hotmail.com&token=2892d1c4-4188-475f-b840-975a99cb9b396e0c6960446fa00bc19a696db4163addcac8-51be-49aa-a7bf-2baed7e7e05b";

    $credenciais = "email=guga_carbo@hotmail.com&token=8BE1A0DF1DAD40D99949834093F21AB8";

    $url = "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/" . $notificationCode . "?" . $credenciais;
    $xml = simplexml_load_file($url);
    $json = json_encode($xml,  JSON_UNESCAPED_UNICODE);
    $notificationArray = json_decode($json, TRUE);
    //echo $notificationArray['status'];


    // * Print
    //print_r($notificationArray);
    // * ----

    $sql = "UPDATE internal_buy SET payload = ? WHERE reference = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $json, $notificationArray['reference']);

    if ($stmt->execute()) {

        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE reference = ?");
        $stmt->bind_param("s", $notificationArray['reference']);
        $stmt->execute();
        $result_ = $stmt->get_result();

        if ($result_->num_rows > 0) {

            //? Send Mail
            $curl_response = sendMail($notificationArray);

            $row = $result_->fetch_assoc();
            if ($notificationArray["status"] < 5) {
                $stmt = $mysqli->prepare("UPDATE vendas SET status = ?, internalStatus = ?, lastUpdate = ?, rawPayload = ? WHERE reference = ?");
                $stmt->bind_param("sssss", $notificationArray['status'], $notificationArray['status'], $notificationArray['lastEventDate'], $json, $notificationArray['reference']);
            } else {
                $stmt = $mysqli->prepare("UPDATE vendas SET status = ?, lastUpdate = ?, rawPayload = ? WHERE reference = ?");
                $stmt->bind_param("ssss", $notificationArray['status'], $notificationArray['lastEventDate'], $json, $notificationArray['reference']);
            }

            if ($stmt->execute()) {
                echo json_encode(array("status" => "success", "message" => "Notificação recebida com sucesso!"));
            }
        } else {

            $stmt = $mysqli->prepare("SELECT * FROM internal_buy WHERE reference = ?");
            $stmt->bind_param("s", $notificationArray['reference']);
            $stmt->execute();
            $result_ = $stmt->get_result();
            $row2 = mysqli_fetch_assoc($result_);

            $stmt = $mysqli->prepare("INSERT INTO vendas (status, internalStatus, clientId, reference, code, totalAmount, buyDate, lastUpdate, rawPayload) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $notificationArray['status'], $notificationArray['status'], $row2['clientId'], $notificationArray['reference'], $notificationArray['code'], $notificationArray['grossAmount'], $notificationArray['date'], $notificationArray['lastEventDate'], $json);
            $stmt->execute();
            if ($stmt->affected_rows) {

                echo json_encode(array("status" => "200", "message" => "Notificação recebida com sucesso9!"));
            } else {
                echo json_encode(array("status" => "400", "message" => "Erro ao inserir nova venda!"));
            }
        }
    } else {
        echo(json_encode(array("status" => "505", "message" => "Erro ao atualizar notificação!")));
    }
} else {
    echo(json_encode(array("status" => "405", "message" => "Bad Request")));
}

$stmt->close();
die();