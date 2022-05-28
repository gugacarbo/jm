<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');


function errHandle($errNo, $errStr, $errFile, $errLine)
{
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
        die(json_encode(array('status' => 403)));
    }
}

set_error_handler('errHandle');

//if isset get tracking code && tracking code.lenght == 13
if(isset($_GET['trackingCode']) && strlen($_GET['trackingCode']) == 13){
    $tracking_code = str_replace(["-","."," ", "|", "\\", "/", "~", "^"], '', $_GET['trackingCode']);
    $curl = curl_init("https://api.linketrack.com/track/json?user=guga_carbo@hotmail.com&token=bbf93b88111339583155e7a55b0f57f22f7cf316ca51090f7de12dd8f8e893e1&codigo=" . $tracking_code);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($result, true);
    die (json_encode($result));
}