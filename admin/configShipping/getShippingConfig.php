<?php
include "../db_connect.php";


$GconfigTake = ["cepOrigemFrete", "aditionalWeight", "alturaFrete", "larguraFrete", "comprimentoFrete", "freteGratis"];
$Gconfig = array();

foreach ($GconfigTake as $key => $value) {

    $sql = "SELECT value FROM generalConfig WHERE config = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $stmt->bind_result($Gconfig[$value]);
    $stmt->fetch();
    $stmt->close();
    $Gconfig[$value] = tirarAcentos($Gconfig[$value]);
}
die(json_encode(array('status' => 'success', 'data' => $Gconfig)));




function tirarAcentos($string)
{
    return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string);
}

