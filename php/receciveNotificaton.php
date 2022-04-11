<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
if (empty($_POST['notificationCode'])) {
    include("db_connect.php");
    //$notificationCode =  $_POST['notificationCode'];


    //simple xmlLoadFile from http://pagseguro.incubedev.com.br/
    $notificationCode = file_get_contents("http://pagseguro.incubedev.com.br");

    echo $notificationCode;
    //$credenciais = "email=guga_carbo@hotmail.com&token=2892d1c4-4188-475f-b840-975a99cb9b396e0c6960446fa00bc19a696db4163addcac8-51be-49aa-a7bf-2baed7e7e05b";
    $credenciais = "email=guga_carbo@hotmail.com&token=8BE1A0DF1DAD40D99949834093F21AB8";

    // simple xml load file to https://ws.sandboox.pagseguro.uol.com.br/v3/transactions/notifications/
    $url = "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/" . $notificationCode . "?" . $credenciais;
    echo $url;
    $xml = simplexml_load_file($url);
    $json = json_encode($xml,  JSON_UNESCAPED_UNICODE);
    $array = json_decode($json, TRUE);

    // * Print
    print_r($array);
    // * ----

    $sql = "UPDATE internal_buy SET payload = '$json' WHERE reference = '$array[reference]'";
    $result = mysqli_query($mysqli, $sql);

    if ($result) {

        //if reference in table vendas, update row, else insert row
        $sql4 = "SELECT * FROM vendas WHERE reference = '$array[reference]'";
        $result4 = mysqli_query($mysqli, $sql4);
        $row4 = mysqli_fetch_assoc($result4);
        if ($row4) {
            if($array["status"] < 3){
                $sql2 = "UPDATE vendas SET status = '$array[status]', internalStatus = '$array[status]', lastUpdate = '$array[lastEventDate]', rawPayload = '$json' WHERE reference = '$array[reference]'";
            }else{
                $sql2 = "UPDATE vendas SET status = '$array[status]', lastUpdate = '$array[lastEventDate]', rawPayload = '$json' WHERE reference = '$array[reference]'";
            }
            $result2 = mysqli_query($mysqli, $sql2);

        } else {
            //select by reference
            $sql2 = "SELECT * FROM internal_buy WHERE reference = '$array[reference]'";
            $result2 = mysqli_query($mysqli, $sql2);
            $row2 = mysqli_fetch_assoc($result2);
            //insert into table vendas (status	clientId	reference	code	totalAmount	buyDate	lastUpdate	rawPayload)
            $sql3 = "INSERT INTO vendas (status, internalStatus, clientId, reference, code, totalAmount, buyDate, lastUpdate, rawPayload) VALUES ('$array[status]', '$array[status]', '$row2[clientId]', '$array[reference]', '$array[code]', '$array[grossAmount]', '$array[date]', '$array[lastEventDate]', '$json')";
            $result3 = mysqli_query($mysqli, $sql3);
            if ($result3) {
                echo "OK";
            } else {
                echo "Error updating record: " . mysqli_error($mysqli);
            }
        }
    } else {
        echo "Error updating record: " . mysqli_error($mysqli);
    }


}else{
    var_dump(http_response_code(505));
}

