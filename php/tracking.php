<?php


//if isset get tracking code && tracking code.lenght == 13
if(isset($_GET['trackingCode']) && strlen($_GET['trackingCode']) == 13){
    $tracking_code = preg_replace('/[|\,\;\@\:"]+/', '', $_GET['trackingCode']);
    //open curt to  https://api.linketrack.com/track/json?user=guga_carbo@hotmail.com&token=bbf93b88111339583155e7a55b0f57f22f7cf316ca51090f7de12dd8f8e893e1&codigo=
    $curl = curl_init("https://api.linketrack.com/track/json?user=guga_carbo@hotmail.com&token=bbf93b88111339583155e7a55b0f57f22f7cf316ca51090f7de12dd8f8e893e1&codigo=" . $tracking_code);
    //set curl options
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //execute curl
    $result = curl_exec($curl);
    //close curl
    curl_close($curl);
    //decode json
    $result = json_decode($result, true);
    die (json_encode($result));
}