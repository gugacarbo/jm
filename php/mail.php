<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../mailer/src/Exception.php';
require '../mailer/src/PHPMailer.php';
require '../mailer/src/SMTP.php';

include "../mail/model/model_compra.php";

function sendMail($notification)
{
    include "db_connect.php";
    include "db_connect.php";
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
    //print_r($model);
    if ($model['status'] == "success") {
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


        $mail->addCustomHeader("List-Unsubscribe", $config["contactMail"] . ', <http://jmacessoriosdeluxo.com.br/unsubscribe/?email=aaa>');
        $mail->Username = $config["automaticMail"];
        $mail->Password = $config["automaticMailPass"];

        $mail->setFrom($config["automaticMail"], 'JM - Acessorios de Luxo');
        $mail->addReplyTo($config["contactMail"], 'Contato JM - Acessorios de Luxo');

        $mail->addAddress($To, $Name);
        $mail->Subject = $subject;
        $mail->msgHTML($html);
        $mail->AltBody = $AltBody;
        if (!$mail->send()) {
            return ((array("status" => "error", "message" => $mail->ErrorInfo)));
        } else {
            $mail->addAddress($config["adminMail"], $Name);
            if ($config['sendToAdminMail'] == "true") {
                if (!$mail->send()) {

                } else {
                }
            }
            return ((array("status" => "success", "message" => "Email enviado com sucesso!")));
        }
    } else {
        return ((array("status" => "error", "message" => $mail->ErrorInfo)));
    }
}
