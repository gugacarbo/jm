<?php
header('Content-Type: application/json; charset:utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

define('TIMEZONE', 'America/Sao_Paulo');
date_default_timezone_set(TIMEZONE);



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../mailer/src/Exception.php';
require '../mailer/src/PHPMailer.php';
require '../mailer/src/SMTP.php';

include "../mail/model/model_compra.php";

include "../config/db_connect.php";



$htmlJsonResponse = [];
class ReceiveNotification extends dbConnect
{

    //$credenciais = "email=guga_carbo@hotmail.com&token=2892d1c4-4188-475f-b840-975a99cb9b396e0c6960446fa00bc19a696db4163addcac8-51be-49aa-a7bf-2baed7e7e05b";
    private $notificationCode_, $jsonPayload_,
        $Payload_,
        $credenciais_ = "email=guga_carbo@hotmail.com&token=8BE1A0DF1DAD40D99949834093F21AB8";

    public function __construct($notificationCode)
    {
        $this->notificationCode_ = $notificationCode;

        $url = "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/" . $notificationCode . "?" . $this->credenciais_;
        $xml = simplexml_load_file($url);
        $jsonPayload = json_encode($xml,  JSON_UNESCAPED_UNICODE);
        $this->jsonPayload_ = $jsonPayload;
        $Payload = json_decode($jsonPayload, TRUE);
        $this->Payload_ = $Payload;
    }

    public function verify()
    {

        $mysqli = $this->Conectar();
        $Payload = $this->Payload_;
        $jsonPayload = $this->jsonPayload_;

        $sql = "SELECT products, clientId FROM checkout_data WHERE reference = ?";
        $stmt = $mysqli->prepare($sql);

        $stmt->bind_param("s", $Payload['reference']);
        $stmt->execute();
        $result = $stmt->get_result();
        $resultD = $result->fetch_assoc();

        if ($result->num_rows > 0) {
            $products = json_decode($resultD['products']);
            $clientId = $resultD['clientId'];

            //array_push($htmlJsonResponse, ('status' => 200));
            $stmt->close();


            //> Upload checkout Data
            $sql = "UPDATE checkout_data SET payload = ? WHERE reference = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ss", $jsonPayload, $Payload['reference']);
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
                $stmt->bind_param("ssssssss", $Payload['status'],  $clientId, $Payload['reference'], $Payload['code'], $Payload['grossAmount'], $Payload['date'], $Payload['lastEventDate'], $jsonPayload);
                $stmt->execute();
                if ($stmt->affected_rows) {
                    $stmt->close();

                    $htmlJsonResponse['status'] = 202;
                } else {
                    $stmt->close();
                    $htmlJsonResponse['status'] = 500;
                    $htmlJsonResponse['error'] = "Erro ao inserir nova venda";
                    return (json_encode($htmlJsonResponse));
                }
            } else {
                //> Update the purchase status on VENDAS
                $stmt = $mysqli->prepare("UPDATE vendas SET status = ?,  lastUpdate = ?, rawPayload = ? WHERE reference = ?");
                $stmt->bind_param("ssss", $Payload['status'], $Payload['lastEventDate'], $jsonPayload, $Payload['reference']);

                if ($stmt->execute()) {
                    $htmlJsonResponse['status'] = 200;
                    ////array_push($htmlJsonResponse, ('All OK' => 200));
                } else {

                    $htmlJsonResponse['status'] = 50;
                    $htmlJsonResponse['ref'] = $Payload['reference'];
                    $htmlJsonResponse['payload'] = $jsonPayload;
                    $htmlJsonResponse['error'] = "Erro ao atualizar venda" . $stmt->error;
                    $stmt->close();
                    return (json_encode($htmlJsonResponse));
                }
                $stmt->close();
            }

            //| Send Email 
            $sendedMail = $this->sendMail($Payload);
            if ($sendedMail["status"] >= 200 && $sendedMail["status"] <= 200) {
                //array_push($htmlJsonResponse, ('Send Mail' => 200));
            } else {
                array_push($htmlJsonResponse, array('Send Mail' => 403));
            }

            //> Pagamento Aprovado
            //> ["status" = 3, "Value" : "Pagamento Efetuado"], ["internalStatus" = 3, "Review Products Of Canceled Purchase"]
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
            return (json_encode($htmlJsonResponse));
        } else {
            $stmt->close();
            $htmlJsonResponse['status'] = 500;
            $htmlJsonResponse['error'] = "Venda Não Existe";
            //$htmlJsonResponse['reference'] = $Payload["reference"];
            return (json_encode($htmlJsonResponse));
        }
    }



    private function sendMail($notification)
    {
        $mysqli = $this->Conectar();
        $GconfigTake = ["contactMail", "automaticMail", "automaticMailPass", "adminMail", "sendToAdminMail"];
        $config = array();

        foreach ($GconfigTake as $key => $value) {
            $sql = "SELECT value FROM generalConfig WHERE config = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $value);
            $stmt->execute();
            $stmt->bind_result($config[$value]);
            $stmt->fetch();
            $stmt->close();
        }

        $model = getModel($notification);

        if ($model['status'] >= 200 && $model['status'] < 300) {
            $html = $model["content"];
            $subject = $model["subject"];
            $AltBody = $model["AltBody"];
            $To = $notification["sender"]["email"];
            $Name = $notification["sender"]["name"];

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->CharSet = 'UTF-8';
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;

            $mail->Host = 'smtp.hostinger.com';
            $mail->Port = 465;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPAuth = true;

            $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $mail->addCustomHeader("List-Unsubscribe", $config["contactMail"] . ', <' . $actual_link . '/unsubscribe/?email=aaa>');
            $mail->Username = $config["automaticMail"];
            $mail->Password = $config["automaticMailPass"];

            $mail->setFrom($config["automaticMail"], 'JM - Acessorios de Luxo');
            $mail->addReplyTo($config["contactMail"], 'Contato JM - Acessorios de Luxo');

            $mail->addAddress($To, $Name);
            $mail->Subject = $subject;
            $mail->msgHTML($html);
            $mail->AltBody = $AltBody;
            if (!$mail->send()) {
                return ((array("status" => 500, "message" => $mail->ErrorInfo)));
            } else {
                $mail->ClearAllRecipients();
                $mail->ClearAttachments();
                $mail->ClearAddresses();
                
                $statusCode = $model["purchaseStatusCode"];
                $listSend = json_decode($config['sendToAdminMail']);
                $statusCode == 6 ? $statusCode = 9 :  $statusCode = $statusCode;
                $statusCode > 7 ? $statusCode = 9 : $statusCode = $statusCode;

                if (in_array($statusCode, $listSend)) {
                    $mail->addAddress($config["adminMail"], $Name);
                    if ($mail->Send()) {
                        $mail->ClearAllRecipients();
                        $mail->ClearAttachments();
                        $mail->ClearAddresses();
                    }
                }
                return ((array("status" => 200, "message" => "Email enviado com sucesso!")));
            }
        } else {
            return ((array("status" => 500, "message" => $model['status'])));
        }
    }
}


// > > > > > 

if (empty($_POST['notificationCode'])) {

    $notificationCode =  "71C091970A9C0A9C8F24444D5F8B193A83C5"; // 3 id = 30

    $receive = new ReceiveNotification($notificationCode);
    echo $receive->verify();
} else {
    $htmlJsonResponse['status'] = 400;
    $htmlJsonResponse['error'] = "Bad Request";

    return (json_encode($htmlJsonResponse));
}
