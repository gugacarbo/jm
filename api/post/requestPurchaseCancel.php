<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');

define('TIMEZONE', 'America/Sao_Paulo');
date_default_timezone_set(TIMEZONE);


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../mailer/src/Exception.php';
require '../mailer/src/PHPMailer.php';
require '../mailer/src/SMTP.php';

include "../mail/model/model_cancel.php";


include_once '../config/db_connect.php';

class CancelPurchase extends dbConnect
{

    public function __construct()
    {
    }
    public function cancel($code, $reason)
    {
        $mysqli = $this->Conectar();
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
                    $mailSended =  $this->sendCancelMail(json_decode($purchase["rawPayload"], true));
                    if ($mailSended["status"] >= 200 && $mailSended["status"] < 300) {
                        return (json_encode(array("status" => 200)));
                    } else {
                        return (json_encode(array("status" => 500, "message" => "Email Não Enviado")));
                    }
                } else {
                    return (json_encode(array("status" => 400, "error" => "Não foi possível gravar o cancelamento.")));
                }
            } else {
                return (json_encode(array("status" => 400, "error" => "Não foi possível Atualizar a compra.")));
            }
        } else {
            return (json_encode(array("status" => 403)));
        }
    }


    private function sendCancelMail($notification)
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


        $model = getModelCancel($notification["code"]);
        //print_r($model);
        if ($model['status'] >= 200 && $model['status'] < 300) {
            $html = $model["content"];
            $subject = $model["subject"];
            $AltBody = $model["AltBody"];
            //print_r($model);
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
                /*
            * Send to admin
            
            $statusCode = $model["purchaseStatusCode"];
            $listSend = json_decode($config['sendToAdminMail']);
            $statusCode == 6 ? $statusCode = 9 :  $statusCode = $statusCode;
            $statusCode > 7 ? $statusCode = 9 : $statusCode = $statusCode;

            if (in_array($statusCode, $listSend)) {
                $mail->addAddress($config["adminMail"], $Name);
                if (!$mail->send()) {}
            }
            */
                return ((array("status" => 200, "message" => "Email enviado com sucesso!")));
            }
        } else {
            return ((array("status" => 500, "message" => $model['status'])));
        }
    }
}



if (isset($_POST["code"]) && isset($_POST["reason"])) {
    $code = $_POST["code"];
    $reason = $_POST["reason"];
    die(((new CancelPurchase)->cancel($code, $reason)));
} else {
    die(json_encode(array("status" => 400, "message" => "Bad request.")));
}
