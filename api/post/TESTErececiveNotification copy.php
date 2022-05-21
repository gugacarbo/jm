<?php
header('Content-Type: application/json; charset:utf-8');
//header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');


function errHandle($errNo, $errStr, $errFile, $errLine)
{
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
        die(json_encode(array('status' => 403)));
    }
}
//set_error_handler('errHandle');

$htmlJsonResponse = [];

if (empty($_POST['notificationCode'])) {

    include("mail.php");

    $notificationCode =  "6093BBD193A093A0B702244D3F86400A6E33"; // 3 id = 30
    $notificationCode =  "CC04E3DDBACABACAE9F8845F2FBA4D2049F4"; // 3 id = 30

    //$credenciais = "email=guga_carbo@hotmail.com&token=2892d1c4-4188-475f-b840-975a99cb9b396e0c6960446fa00bc19a696db4163addcac8-51be-49aa-a7bf-2baed7e7e05b";
    $credenciais = "email=guga_carbo@hotmail.com&token=8BE1A0DF1DAD40D99949834093F21AB8";
    $url = "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/" . $notificationCode . "?" . $credenciais;
    $xml = simplexml_load_file($url);
    $json = json_encode($xml,  JSON_UNESCAPED_UNICODE);
    $Payload = json_decode($json, TRUE);


    include("../config/db_connect.php");

    $sql = "SELECT products, clientId FROM checkout_data WHERE reference = ?";
    $stmt = $mysqli->prepare($sql);

    $stmt->bind_param("s", $Payload['reference']);
    $stmt->execute();
    $result = $stmt->get_result();
    $resultD = $result->fetch_assoc();
    $products = json_decode($resultD['products']);
    $clientId = $resultD['clientId'];
    //$htmlJsonResponse['reference'] = $Payload['reference'];
    //$htmlJsonResponse['code'] = $Payload['code'];
    //$htmlJsonResponse['Buystatus'] = $Payload['status'];


    // * * Code Exist && Sale Exist * *
    if ($result->num_rows > 0) {
        //array_push($htmlJsonResponse, ('status' => 200));
        $stmt->close();


        //> Upload checkout Data
        $sql = "UPDATE checkout_data SET payload = ? WHERE reference = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $json, $Payload['reference']);
        $stmt->execute();
        $stmt->close();
        //array_push($htmlJsonResponse, ('Update CheckoutData' => 200));

        //? Get the purchase data
        $stmt = $mysqli->prepare("SELECT internalStatus FROM vendas WHERE reference = ?");
        $stmt->bind_param("s", $Payload['reference']);
        $stmt->execute();
        $result = $stmt->get_result();
        $hasSale = $result->num_rows;
        $Purshcase_InternalStatus = $result->fetch_assoc()["internalStatus"];
        $stmt->close();


        //* * New purchase * *
        if ($hasSale <= 0) {
            $stmt = $mysqli->prepare("INSERT INTO vendas (status,  clientId, reference, code, totalAmount, buyDate, lastUpdate, rawPayload) VALUES (?, ?, ?,  ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $Payload['status'],  $clientId, $Payload['reference'], $Payload['code'], $Payload['grossAmount'], $Payload['date'], $Payload['lastEventDate'], $json);
            $stmt->execute();
            if ($stmt->affected_rows) {
                $stmt->close();

                $htmlJsonResponse['status'] = 202;
            } else {
                $stmt->close();
                $htmlJsonResponse['status'] = 500;
                die(json_encode($htmlJsonResponse));
            }
        } else {
            //> Update the purchase status on VENDAS
            $stmt = $mysqli->prepare("UPDATE vendas SET status = ?,  lastUpdate = ?, rawPayload = ? WHERE reference = ?");
            $stmt->bind_param("ssss", $Payload['status'], $Payload['lastEventDate'], $json, $Payload['reference']);

            if ($stmt->execute()) {
                $htmlJsonResponse['status'] = 200;
                ////array_push($htmlJsonResponse, ('All OK' => 200));
            } else {
                $stmt->close();
                $htmlJsonResponse['status'] = 500;
                die(json_encode($htmlJsonResponse));
            }
            $stmt->close();
        }

        //| Send Email 
        $sendedMail = sendMail($Payload);
        if ($sendedMail["status"] >= 200 && $sendedMail["status"] <= 200) {
            //array_push($htmlJsonResponse, ('Send Mail' => 200));
        } else {
            //array_push($htmlJsonResponse, ('Send Mail' => 403));
        }

        //> Pagamento Aprovado
        //> ["status" = 7, "Value" : "Cancelada"], ["internalStatus" = 8, "Review Products Of Canceled Purchase"]
        //> Atualiza quantidade Vendida dos Produtos
        if (intval($Payload['status']) == 3 && intval($Purshcase_InternalStatus) < 3) {
            $successP = 0;
            $errorP = 0;
            foreach ($products as $product) {
                $stmtP = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
                $stmtP->bind_param("s", $product->id);
                $stmtP->execute();
                $result = $stmtP->get_result();
                $rowP = $result->fetch_assoc();
               
                $stmtP = $mysqli->prepare("UPDATE products SET sold = ? WHERE id = ?");
                $sold = ($rowP['sold'] + $product->qtd);
                $stmtP->bind_param("is", $sold, $product->id);
                $stmtP->execute();
                if ($stmtP->affected_rows > 0) {
                    $successP++;
                } else {
                    $errorP++;
                }
                $stmtP->close();
            }
            $stmt = $mysqli->prepare("UPDATE vendas SET internalStatus = 3 WHERE reference = ?");
            $stmt->bind_param("s", $Payload['reference']);    //? Status 7 == Pagamento Aprovado, Produtos Vendidos
            $stmt->execute();
            $stmt->close();
            $htmlJsonResponse['status'] = 201;
        } //>Fim da Atualização de Produtos

        //? Verifica se a Compra foi cancelada
        //? Retorna os Produtos ao Estoque
        //? ["status" = 7, "Value" : "Cancelada"], ["internalStatus" = 8, "Review Products Of Canceled Purchase"
        if (intval($Payload['status']) == 7 && intval($Purshcase_InternalStatus < 8)) {
            $doneBack = 0;
            $failBack = 0;
            foreach ($products as $product) {
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
                if ($stmtP->affected_rows > 0) {
                    $doneBack++;
                } else {
                    $failBack++;
                }
                $stmtP->close();
            }
            $htmlJsonResponse['status'] = 201;
            $stmt = $mysqli->prepare("UPDATE vendas SET internalStatus = 8 WHERE reference = ?");
            $stmt->bind_param("s", $Payload['reference']);
            $stmt->execute();
            $stmt->close();
        } //? Fim Retorno de Produtos ao Estoque
        die(json_encode($htmlJsonResponse));
    } else {
        $stmt->close();
        $htmlJsonResponse['status'] = 500;
        die(json_encode($htmlJsonResponse));
    }
} else {
    $htmlJsonResponse['status'] = 400;
    die(json_encode($htmlJsonResponse));
}
