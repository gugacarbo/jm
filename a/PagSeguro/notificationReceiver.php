<?php
include("db_connect.php");

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

date_default_timezone_set('America/Sao_Paulo');

$curl = curl_init();
if (!empty($_POST['notificationCode'])) {
    $notificationCode =  $_POST['notificationCode'];
    $credenciais = "email=guga_carbo@hotmail.com&
    token=2892d1c4-4188-475f-b840-975a99cb9b396e0c6960446fa00bc19a696db4163addcac8-51be-49aa-a7bf-2baed7e7e05b";

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://ws.pagseguro.uol.com.br/v3/transactions/notifications/" . $notificationCode . "?" . $credenciais,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ]);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
    $json = json_encode($xml);

    // -3h UTC = 30000;
    $sql = "INSERT INTO `visitors` (`notificationCode`, `payload`, `date`) 
                    VALUES ('$notificationCode', '$json', (now() - 30000))";

    if ($conn->query($sql) === TRUE) {
        //echo ("Record updated successfully");
        var_dump(http_response_code(200));
    } else {
        die($conn->error);
    }
} else {
    $msg = "";
    $msg .= "POST: \n";
    foreach ($_POST as $key => $value) {
        $msg .= $key . ": " . $value . "\n";
    }
    $msg .= "GET: \n";
    foreach ($_GET as $key => $value) {
        $msg .= $key . ": " . $value . "\n";
    }
    $sql = "INSERT INTO `visitors` (`notificationCode`, `payload`, `date`) VALUES ('Visitor', '$msg', (now()-30000)) ";
    echo ($msg);
    if ($conn->query($sql) === TRUE) {
        //echo ("Record updated successfully");
    } else {
        echo ($conn->error);
    }
    die();
}
/*
$sql = "SELECT * from visitors";
$result = $conn->query($sql);
echo ($result->num_rows . "\n");
*/
?>