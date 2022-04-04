<?php
$servername = "localhost";
$username = "root";
$password = "";
$db = "esp";
$data;

$conn = new mysqli($servername, $username, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
}
if (isset($_GET["ip"]) && isset($_GET["mac"])) {
    $data["ip"] = $_GET["ip"];
    $ip =$_GET["ip"];
    $mac =$_GET["mac"];
    $data["mac"] = $_GET["mac"];
    if($conn->query("INSERT INTO register( `ip`, `mac`, `time`) VALUES ('$ip', '$mac', CURRENT_TIMESTAMP)")){
        $data["status"] = "200";
    }else{
        $data["status"] = "400";
    }
}else{
    $conn->query("INSERT INTO register( `ip`, `mac`) VALUES ('F', 'F')");
}
//$data["status"] = "400";
$data["name"] = "NewLed";
$data["id"] = "23";

die(json_encode($data));
