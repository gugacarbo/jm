<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');

if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM".$_SERVER['REMOTE_ADDR']));
    session_start();
}




if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])  || ($_SESSION['admin']) < 1) {
    die(json_encode(array('status' => 403)));
}


include_once '../config/db_connect.php';

class config extends dbConnect
{
    public function __construct()
    {
        $GconfigTake = ["contactMail", "automaticMail",  "adminMail", "sendToAdminMail", "cepOrigemFrete", "aditionalWeight", "alturaFrete", "larguraFrete", "comprimentoFrete"];
        $config = array();

        $mysqli = $this->connect();
        foreach ($GconfigTake as $key => $value) {
            $sql = "SELECT value FROM generalconfig WHERE config = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $value);
            $stmt->execute();
            $stmt->bind_result($config[$value]);
            $stmt->fetch();
            $stmt->close();
        }
        $config["sendToAdminMail"] = json_decode($config["sendToAdminMail"]);
        return $config;
    }
}


die(json_encode((new config())->__construct()));
