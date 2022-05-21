<?php
//header('Content-Type: application/json; charset=utf-8');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../mailer/src/Exception.php';
require '../mailer/src/PHPMailer.php';
require '../mailer/src/SMTP.php';

include "../model/model_tracking.php";

function sendMail($notification)
{
    include "../config/db_connect.php";
    $GconfigTake = ["contactMail", "automaticMail", "automaticMailPass", "adminMail"];
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
    if ($model['status'] >= 200 && $model['status'] < 300) {
        $html = $model["content"];
        $subject = $model["subject"];
        $AltBody = $model["AltBody"];
        //print_r($model);
        $To = $notification->sender->email;
        $Name = $notification->sender->name;


        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;

        $mail->Host = 'smtp.hostinger.com';
        $mail->Port = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;

        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $mail->addCustomHeader("List-Unsubscribe", $config["contactMail"] . ', <'.$actual_link.'/unsubscribe/?email=aaa>');
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
            return ((array("status" => 200, "message" => "Email enviado com sucesso")));
        }
    } else {
        return ((array("status" => 500, "message" => $model['status'])));
    }
}
