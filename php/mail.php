<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../mailer/src/Exception.php';
require '../mailer/src/PHPMailer.php';
require '../mailer/src/SMTP.php';

include "../mail/model/model_compra.php";
function sendMail($notification)
{
    $model = getModel($notification);
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


    $mail->addCustomHeader("List-Unsubscribe", '<contato@jmacessoriosdeluxo.com.br>, <http://jmacessoriosdeluxo.com.br/Unsubscribe/?email=aaa>');
    $mail->Username = 'no-reply@jmacessoriosdeluxo.com.br';
    $mail->Password = 'Dev@159753';

    $mail->setFrom('no-reply@jmacessoriosdeluxo.com.br ', 'JM - Acessorios de Luxo');
    $mail->addReplyTo('contato@jmacessoriosdeluxo.com.br', 'Contato JM - Acessorios');

    $mail->addAddress($To, $Name);
    $mail->Subject = $subject;
    $mail->msgHTML($html);
    $mail->AltBody = $AltBody;
    if (!$mail->send()) {
       return((array("status" => "error", "message" => $mail->ErrorInfo)));
    } else {
        return((array("status" => "success", "message" => "Email enviado com sucesso!")));
    }

}
