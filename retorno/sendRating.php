<?php
include "../php/db_connect.php";

$rate = $_GET["rate"] ;

if(isset($_GET["message"])){
    $message = $_GET["message"];
}else{
    $message = "";
}
if(isset($_GET["code"])){
    $code = $_GET["code"];
}else{
    $code = "";
}

//insert into db rating
$sql = "INSERT INTO rating (rate, message, code) VALUES ('$rate', '$message', '$code')";
$result = $mysqli->query($sql);

//return 200
http_response_code(200);
