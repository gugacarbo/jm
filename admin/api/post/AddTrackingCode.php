<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 1) {
    die(json_encode(array('status' => 403)));
}



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../mailer/src/Exception.php';
require '../mailer/src/PHPMailer.php';
require '../mailer/src/SMTP.php';

include "../model/model_tracking.php";

include_once '../config/db_connect.php';
//include "mailTracking.php";


class trackingCode extends dbConnect
{
    private $id_, $code_;
    public function __construct($id, $code)
    {
        $this->id_ = $id;
        $this->code_ = $code;
    }

    public function addCode()
    {

        $mysqli = $this->connect();

        $id = $this->id_;
        $code = $this->code_;

        $stmt = $mysqli->prepare("SELECT a.id, a.*, b.* FROM vendas as a INNER JOIN client as b ON b.id = a.clientId and a.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();
        $result = $res->fetch_assoc();

        $load = json_decode($result['rawPayload']);
        $load = json_encode($load);
        $load = json_decode($load);
        $load->trackingCode = $code;

        //print_r($load);


        $mail = $this->sendMail($load);

        $stmt = $mysqli->prepare("UPDATE vendas SET trackingCode = ? WHERE id = ?");
        $stmt->bind_param('si', $code, $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return array(
                'status' => 200,
                'message' => 'Tracking code added successfully',
                'mail' => json_encode($mail)
            );
        }else{
            $stmt->close();
            return array(
                'status' => 500,
                'message' => 'Tracking code not added'
            );
        }
    }

    private function sendMail($notification)
    {
        $mysqli = $this->connect();

        $GconfigTake = ["contactMail", "automaticMail", "automaticMailPass", "adminMail"];
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
                return ((array("status" => 200, "message" => "Email enviado com sucesso")));
            }
        } else {
            return ((array("status" => 500, "message" => $model['status'])));
        }
    }
}

if (isset($_POST['id']) && isset($_POST['code'])) {

    $id = $_POST['id'];
    $code = $_POST['code'];

    $tracking = new trackingCode($id, $code);
    die(json_encode($tracking->addCode()));
} else {

    die(json_encode(array('status' => 400)));
}
