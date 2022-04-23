<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../mailer/src/Exception.php';
require '../mailer/src/PHPMailer.php';
require '../mailer/src/SMTP.php';





$html = file_get_contents('../mail/model/model.html');





$mail = new PHPMailer();

$mail->isSMTP();

$mail->CharSet = 'UTF-8';

$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->Host = 'smtp.hostinger.com';
$mail->Port = 465;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->SMTPAuth = true;


$mail->Username = 'teste@jmacessoriosdeluxo.com.br';
$mail->Password = 'Dev@159753';

$mail->setFrom('teste@jmacessoriosdeluxo.com.br', 'Vendas JM - Acessórios de Luxo');
$mail->addReplyTo('teste@jmacessoriosdeluxo.com.br', 'Contato JM - Acessórios de Luxo');

$mail->addAddress('guga_carbo@hotmail.com', 'Gustavo Carbonera');

$mail->Subject = 'Compra Realizada!';
$mail->msgHTML($html);
$mail->AltBody = 'JM - Acessórios de Luxo';

if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {

}