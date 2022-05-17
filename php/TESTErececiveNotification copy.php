<?php
function errHandle($errNo, $errStr, $errFile, $errLine)
{
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
        die(json_encode(array('status' => '403')));
    } 
}

set_error_handler('errHandle');


if (empty($_POST['notificationCode'])) {
    include("db_connect.php");
    include("mail.php");

    $notificationCode =  "1383B9A0043C043CD7633416FF80923D16AE";


    //$credenciais = "email=guga_carbo@hotmail.com&token=2892d1c4-4188-475f-b840-975a99cb9b396e0c6960446fa00bc19a696db4163addcac8-51be-49aa-a7bf-2baed7e7e05b";
    $credenciais = "email=guga_carbo@hotmail.com&token=8BE1A0DF1DAD40D99949834093F21AB8";

    $url = "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/" . $notificationCode . "?" . $credenciais;
    $xml = simplexml_load_file($url);
    $json = json_encode($xml,  JSON_UNESCAPED_UNICODE);
    $Payload = json_decode($json, TRUE);



    //* Upload checkout Data
    $sql = "UPDATE checkout_data SET payload = ? WHERE reference = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $json, $Payload['reference']);

    if ($stmt->execute()) { //? If have the purchase
        $stmt->close();

        
        //? Get the purchase data
        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE reference = ?");
        $stmt->bind_param("s", $Payload['reference']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) { //* Already have the purchase
            $sendedMail = sendMail($Payload);
            $Purshcase = $result->fetch_assoc();

            //? ["status" = 7, "Value" : "Cancelada"], ["internalStatus" = 8, "Review Products Of Canceled Purchase"]
            if ($Payload['status'] == 7 && $Purshcase['internalStatus'] < 8) {

                $stmtC = $mysqli->prepare("SELECT * FROM checkout_data WHERE reference = ?");
                $stmtC->bind_param("s", $Payload['reference']);
                $stmtC->execute();
                $resultC = $stmtC->get_result();
                $CheckoutData = $resultC->fetch_assoc();
                $stmtC->close();

                $ReviewProducts = json_decode($CheckoutData["products"]);

                foreach ($ReviewProducts as $product) {

                    $stmtP = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
                    $stmtP->bind_param("s", $product->id);
                    $stmtP->execute();
                    $result = $stmtP->get_result();

                    $rowP = $result->fetch_assoc();
                    $options = json_decode($rowP['options'], true);

                    $options[$product->opt] = $options[$product->opt] + $product->qtd;

                    $newOptions = json_encode($options);

                    $totalQuantity = 0;
                    foreach ($options as $n => $op) {
                        $totalQuantity += $options[$n];
                    }

                    $stmtP = $mysqli->prepare("UPDATE products SET options = ?, totalQuantity = ? WHERE id = ?");
                    $stmtP->bind_param("sis", $newOptions, $totalQuantity, $product->id);
                    $stmtP->execute();
                    //Could not find the product
                    $stmtP->close();
                }


                $stmt = $mysqli->prepare("UPDATE vendas SET internalStatus = 8 WHERE reference = ?");
                $stmt->bind_param("s", $Payload['reference']);    //* Status 8 == Cancelada, Produtos Voltaram ao Estoque Final
                $stmt->execute();
                $stmt->close();
            }
            //? End Review Canceled





            $stmt = $mysqli->prepare("UPDATE vendas SET status = ?,  lastUpdate = ?, rawPayload = ? WHERE reference = ?");
            $stmt->bind_param("ssss", $Payload['status'], $Payload['lastEventDate'], $json, $Payload['reference']);

            if ($stmt->execute()) {
                $stmt->close();
                echo json_encode(array("status" => "success", "message" => "Notificação recebida com sucesso!"));
            }
        } else { //* New purchase


            //? GET Client Id
            $stmt = $mysqli->prepare("SELECT * FROM checkout_data WHERE reference = ?");
            $stmt->bind_param("s", $Payload['reference']);
            $stmt->execute();
            $result_ = $stmt->get_result();
            $clientId = mysqli_fetch_assoc($result_);
            $stmt->close();


            $stmt = $mysqli->prepare("INSERT INTO vendas (status,  clientId, reference, code, totalAmount, buyDate, lastUpdate, rawPayload) VALUES (?, ?, ?,  ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $Payload['status'],  $clientId['clientId'], $Payload['reference'], $Payload['code'], $Payload['grossAmount'], $Payload['date'], $Payload['lastEventDate'], $json);
            $stmt->execute();
            if ($stmt->affected_rows) {
                $sendedMail = sendMail($Payload);
                $stmt->close();
                echo json_encode(array("status" => "success", "message" => "Recebido com sucesso!"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Erro ao inserir nova venda!"));
            }
        }
    } else {
        echo "Error updating recor22d: " . mysqli_error($mysqli);
    }
} else {
    var_dump(http_response_code(505));
}
die();



