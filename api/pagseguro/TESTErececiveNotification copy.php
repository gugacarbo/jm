<?php
header('Content-Type: application/json;');
header('Access-Control-Allow-Methods: POST');

error_reporting(1);
ini_set('display_errors', 1);


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../mailer/src/Exception.php';
require '../mailer/src/PHPMailer.php';
require '../mailer/src/SMTP.php';

include "../mail/model/model_compra.php";

include_once "../config/db_connect.php";



$htmlJsonResponse = [];
class ReceiveNotification extends dbConnect
{

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

        //X Erro notification Code Inválido
        if ($this->Payload_ == []) { //=401
            $errorMessage = (array(
                'status' => 400,
                'errorCode' => 401,
                'code' => $this->notificationCode_,
                'payload' => json_encode($this->Payload_),
                "message" => "Erro ao receber notificação do PagSeguro",
            )
            );
            $this->error_log("error", $errorMessage);
            return ((array('status' => 400, "message" => "Erro ao receber notificação do PagSeguro, Código Inválido")));
        }



        $mysqli = $this->Conectar();
        $Payload = $this->Payload_;
        $jsonPayload = $this->jsonPayload_;

        //> Dados da compra do banco de vendas primário
        $sql = "SELECT products, clientId, totalCost FROM checkout_data WHERE reference = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $Payload['reference']);
        $stmt->execute();
        $result = $stmt->get_result();
        $resultD = $result->fetch_assoc();

        if ($result->num_rows > 0) {
            //*Venda Existe
            $products = json_decode($resultD['products']);
            $clientId = $resultD['clientId'];
            $totalCost = $resultD['totalCost'];
            $stmt->close();


            //| Atualiza dados da tabela primaria
            $sql = "UPDATE checkout_data SET payload = ? WHERE reference = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ss", $jsonPayload, $Payload['reference']);
            $stmt->execute();
            $stmt->close();

            //? Procura se a venda ja foi cadastrada na tabela secundária
            $stmt = $mysqli->prepare("SELECT internalStatus FROM vendas WHERE reference = ?");
            $stmt->bind_param("s", $Payload['reference']);
            $stmt->execute();
            $result = $stmt->get_result();
            $hasSale = $result->num_rows;
            $Purshcase_InternalStatus = $result->fetch_assoc()["internalStatus"];
            $stmt->close();


            if ($hasSale <= 0) {
            //* * Nova Compra * *
            
                $stmt = $mysqli->prepare("INSERT INTO vendas (status,  clientId, reference, code, totalAmount, buyDate, lastUpdate, rawPayload, totalCost) VALUES (?, ?, ?,  ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssss", $Payload['status'],  $clientId, $Payload['reference'], $Payload['code'], $Payload['grossAmount'], $Payload['date'], $Payload['lastEventDate'], $jsonPayload, $totalCost);
                $stmt->execute();
                if ($stmt->affected_rows) {
                    $stmt->close();
                    $htmlJsonResponse['status'] = 202;

                } else {

                    //X Erro ao criar nova venda
                    $htmlJsonResponse['status'] = 500;
                    $htmlJsonResponse['error'] = "Erro ao inserir nova venda";
                    $errorMessage = (array(//=501
                        'status' => 500,
                        'errorCode' => 501,
                        'code' => $this->notificationCode_,
                        'mysqlError' => $stmt->error,
                        "message" => "Erro ai criar nova venda",
                    )
                    );
                    $this->error_log("error", $errorMessage);

                    $stmt->close();
                    
                    if($Payload['status'] > 2){
                        $this->verify();
                    }
                    return (($htmlJsonResponse));
                }
            } else {

                //> Atualiza Tabela secundária
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
                    
                    $errorMessage = (array( //=502
                        'status' => 500,
                        'errorCode' => 502,
                        'code' => $this->notificationCode_,
                        'mysqlError' => $stmt->error,
                        "message" => "Erro ao atualizar venda",
                    )
                    );
                    $this->error_log("error", $errorMessage);
                    $stmt->close();
                    return (($htmlJsonResponse));
                }
                $stmt->close();
            }

            //| Send Email 
            $sendedMail = $this->sendMail($Payload);
            if ($sendedMail["status"] >= 200 && $sendedMail["status"] <= 200) {
                $htmlJsonResponse['Send Mail'] = "OK";
            } else {
                //X Erro ao enviar email
                $errorMessage = (array( //=402
                    'status' => 400,
                    'errorCode' => 402,
                    'code' => $this->notificationCode_,
                    "message" => "Enviar email",
                )
                );
                $this->error_log("error", $errorMessage);
                $htmlJsonResponse['Send Mail'] = "error";
            }


            //> Pagamento Aprovado
            //> ["status" = 3, "Value" : "Pagamento Efetuado"], ["internalStatus" = 3, "Sold Contabilized"]
            //> Atualiza quantidade Vendida dos Produtos
            if ((intval($Payload['status']) == 3 || intval($Payload['status']) == 4) && intval($Purshcase_InternalStatus) < 3) {
                $successP = 0;
                $errorP = 0;
                $errorPInfo = [];

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
                        //X Erro ao atualizar produto Vendido
                        $errorP++;
                        $errorPInfo[] = $stmtP->error;
                    }
                    $stmtP->close();
                }

                $stmt = $mysqli->prepare("UPDATE vendas SET internalStatus = 3, paymentDate = ? WHERE reference = ?");
                $stmt->bind_param("ss", $Payload['lastEventDate'], $Payload['reference']);//? Status 7 == Pagamento Aprovado, Produtos Vendidos
                $stmt->execute();
                $stmt->close();
                $htmlJsonResponse['status'] = 201;

                if ($errorP > 0) {
                    $errorMessage = (array(
                        'status' => 500,
                        'errorCode' => 500,
                        'code' => $this->notificationCode_,
                        'success' => $successP,
                        'Failed' => $errorP,
                        'FailedInfo' => json_encode($errorPInfo),
                        "message" => "Pedido Pago - Erro ao contabilizar Vendido",
                    )
                    );
                    $this->error_log("error", $errorMessage);
                }
            } //>Fim da Atualização de Produtos



            //? Verifica se a Compra foi cancelada
            //? Retorna os Produtos ao Estoque
            //? ["status" = 7, "Value" : "Cancelada"], ["internalStatus" = 7, "Return Products Of Canceled Purchase"
            if (intval($Payload['status']) == 7 && intval($Purshcase_InternalStatus < 7)) {
                $doneBack = 0;
                $failBack = 0;
                $failBackInfo = [];
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
                        //X Erro ao retornar produto ao Estoque 
                        $failBack++;
                        $failBackInfo[] = $stmtP->error;
                    }
                    $stmtP->close();
                }
                $htmlJsonResponse['status'] = 201;
                $stmt = $mysqli->prepare("UPDATE vendas SET internalStatus = 7 WHERE reference = ?");
                $stmt->bind_param("s", $Payload['reference']);
                $stmt->execute();
                $stmt->close();

                if ($failBack > 0) {
                    $errorMessage = (array(
                        'status' => 400,
                        'errorCode' => 400,
                        'code' => $this->notificationCode_,
                        'success' => $doneBack,
                        'Failed' => $failBack,
                        'FailedInfo' => json_encode($failBackInfo),
                        "message" => "Erro ao retornar produtos ao estoque",
                    )
                    );
                    $this->error_log("error", $errorMessage);
                }
            } //? Fim Retorno de Produtos ao Estoque


            return (($htmlJsonResponse));
        } else {
            //X Erro Venda nao existe
            $stmt->close();
            $htmlJsonResponse['status'] = 500;
            $htmlJsonResponse['error'] = "Venda Não Existe";

            $errorMessage = (array(
                'status' => 400,
                'errorCode' => 400,
                'code' => $this->notificationCode_,
                'reference' => $Payload['reference'],
                "message" => "Noticifação recebida, mas a venda não existe",
            )
            );
            $this->error_log("error", $errorMessage);
            
            //$htmlJsonResponse['reference'] = $Payload["reference"];
            return (($htmlJsonResponse));
        }
    }



    private function sendMail($notification)
    {
        $mysqli = $this->Conectar();
        $GconfigTake = ["contactMail", "automaticMail", "automaticMailPass", "adminMail", "sendToAdminMail"];
        $config = array();

        foreach ($GconfigTake as $key => $value) {
            $sql = "SELECT value FROM generalconfig WHERE config = ?";
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

    $notificationCode =  "C26201A45C945C943EA884B34FA38F3036C4"; // 3 id = 30

    $receive = new ReceiveNotification($notificationCode);
    die(json_encode($receive->verify()));
} else {
    $htmlJsonResponse['status'] = 400;
    $htmlJsonResponse['error'] = "Bad Request";

    return (json_encode($htmlJsonResponse));
}
